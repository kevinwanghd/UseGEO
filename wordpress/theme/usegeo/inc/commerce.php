<?php

if (!defined('ABSPATH')) {
    exit;
}

function usegeo_report_price_cents() {
    return 2900;
}

function usegeo_report_currency() {
    return 'usd';
}

function usegeo_get_stripe_secret_key() {
    return defined('USEGEO_STRIPE_SECRET_KEY') ? trim((string) USEGEO_STRIPE_SECRET_KEY) : '';
}

function usegeo_get_stripe_webhook_secret() {
    return defined('USEGEO_STRIPE_WEBHOOK_SECRET') ? trim((string) USEGEO_STRIPE_WEBHOOK_SECRET) : '';
}

function usegeo_is_checkout_enabled() {
    return usegeo_get_stripe_secret_key() !== '';
}

function usegeo_report_score_keys() {
    return array('total', 'entity_clarity', 'content_structure', 'proof_signals', 'comparison_readiness', 'ai_extractability');
}

function usegeo_normalize_report_snapshot($payload) {
    if (!is_array($payload)) {
        return new WP_Error('invalid_snapshot', 'The scan snapshot is invalid.');
    }

    $url = isset($payload['final_url']) ? esc_url_raw($payload['final_url']) : '';
    if (!$url) {
        $url = isset($payload['url']) ? esc_url_raw($payload['url']) : '';
    }
    if (!$url || !wp_http_validate_url($url)) {
        return new WP_Error('invalid_snapshot', 'A public website URL is required.');
    }

    $scores = array();
    $incoming_scores = isset($payload['scores']) && is_array($payload['scores']) ? $payload['scores'] : array();
    foreach (usegeo_report_score_keys() as $key) {
        $value = isset($incoming_scores[$key]) ? (int) $incoming_scores[$key] : 0;
        $scores[$key] = max(0, min(100, $value));
    }

    $findings = array();
    foreach (array_slice(isset($payload['findings']) && is_array($payload['findings']) ? $payload['findings'] : array(), 0, 8) as $finding) {
        if (!is_array($finding)) {
            continue;
        }
        $findings[] = array(
            'severity' => sanitize_key(isset($finding['severity']) ? $finding['severity'] : 'medium'),
            'dimension' => sanitize_key(isset($finding['dimension']) ? $finding['dimension'] : ''),
            'title' => sanitize_text_field(isset($finding['title']) ? $finding['title'] : ''),
            'evidence' => sanitize_textarea_field(isset($finding['evidence']) ? $finding['evidence'] : ''),
            'recommendation' => sanitize_textarea_field(isset($finding['recommendation']) ? $finding['recommendation'] : ''),
        );
    }

    $extracted = isset($payload['extracted']) && is_array($payload['extracted']) ? $payload['extracted'] : array();
    return array(
        'url' => $url,
        'grade' => sanitize_text_field(isset($payload['grade']) ? $payload['grade'] : ''),
        'scores' => $scores,
        'findings' => $findings,
        'summary' => sanitize_textarea_field(isset($payload['llm_summary']['summary']) ? $payload['llm_summary']['summary'] : ''),
        'extracted' => array(
            'title' => sanitize_text_field(isset($extracted['title']) ? $extracted['title'] : ''),
            'meta_description' => sanitize_textarea_field(isset($extracted['meta_description']) ? $extracted['meta_description'] : ''),
            'h1' => array_values(array_map('sanitize_text_field', array_slice(isset($extracted['h1']) && is_array($extracted['h1']) ? $extracted['h1'] : array(), 0, 3))),
            'schema_types' => array_values(array_map('sanitize_text_field', array_slice(isset($extracted['schema_types']) && is_array($extracted['schema_types']) ? $extracted['schema_types'] : array(), 0, 12))),
            'word_count' => max(0, (int) (isset($extracted['word_count']) ? $extracted['word_count'] : 0)),
        ),
        'created_at' => current_time('mysql', true),
    );
}

function usegeo_report_dimension_labels() {
    return array(
        'entity_clarity' => 'Entity clarity',
        'content_structure' => 'Content structure',
        'proof_signals' => 'Proof signals',
        'comparison_readiness' => 'Comparison readiness',
        'ai_extractability' => 'AI extractability',
    );
}

function usegeo_report_priorities($snapshot) {
    $scores = isset($snapshot['scores']) ? $snapshot['scores'] : array();
    $labels = usegeo_report_dimension_labels();
    $ranked = array();
    foreach ($labels as $key => $label) {
        $ranked[$key] = isset($scores[$key]) ? (int) $scores[$key] : 0;
    }
    asort($ranked, SORT_NUMERIC);

    $actions = array(
        'entity_clarity' => 'Rewrite the homepage opening so it names the category, ideal customer, core problem, and outcome in plain language.',
        'content_structure' => 'Create a clean H1/H2 hierarchy and turn long narrative sections into answer-ready blocks with descriptive headings.',
        'proof_signals' => 'Add concrete customer evidence, named use cases, measurable outcomes, and links to supporting case studies.',
        'comparison_readiness' => 'Publish a buyer-focused comparison or alternatives page that explains fit, tradeoffs, and selection criteria.',
        'ai_extractability' => 'Align title, description, H1, canonical, and Organization schema around one consistent entity description.',
    );

    $priorities = array();
    foreach (array_slice(array_keys($ranked), 0, 3) as $key) {
        $priorities[] = array('key' => $key, 'label' => $labels[$key], 'score' => $ranked[$key], 'action' => $actions[$key]);
    }
    return $priorities;
}

function usegeo_create_report($email, $payload) {
    $email = sanitize_email($email);
    if (!$email || !is_email($email)) {
        return new WP_Error('invalid_email', 'Enter a valid email address.');
    }
    $snapshot = usegeo_normalize_report_snapshot($payload);
    if (is_wp_error($snapshot)) {
        return $snapshot;
    }

    $host = wp_parse_url($snapshot['url'], PHP_URL_HOST);
    $post_id = wp_insert_post(array(
        'post_type' => 'usegeo_report',
        'post_status' => 'publish',
        'post_title' => sprintf('%s - %s', $host ? $host : $snapshot['url'], current_time('mysql')),
    ), true);
    if (is_wp_error($post_id)) {
        return $post_id;
    }

    $token = wp_generate_password(40, false, false);
    update_post_meta($post_id, 'report_email', $email);
    update_post_meta($post_id, 'report_access_token', $token);
    update_post_meta($post_id, 'report_token_hash', hash('sha256', $token));
    update_post_meta($post_id, 'report_snapshot', wp_json_encode($snapshot));
    update_post_meta($post_id, 'payment_status', 'unpaid');
    update_post_meta($post_id, 'report_price_cents', usegeo_report_price_cents());

    return array('id' => $post_id, 'token' => $token, 'snapshot' => $snapshot);
}

function usegeo_find_report($report_id, $token) {
    $report_id = (int) $report_id;
    $expected = get_post_meta($report_id, 'report_token_hash', true);
    if (!$report_id || !$expected || !hash_equals($expected, hash('sha256', (string) $token))) {
        return null;
    }
    return get_post($report_id);
}

function usegeo_report_url($report_id, $token) {
    return add_query_arg(array('usegeo_report' => (int) $report_id, 'access_token' => rawurlencode($token)), home_url('/'));
}

function usegeo_email_free_snapshot($report_id, $token, $snapshot) {
    $email = get_post_meta($report_id, 'report_email', true);
    $priorities = usegeo_report_priorities($snapshot);
    $lines = array(
        'Your UseGEO snapshot is ready.', '',
        'Website: ' . $snapshot['url'],
        'GEO readiness score: ' . $snapshot['scores']['total'] . '/100', '',
        'Top priority: ' . $priorities[0]['label'],
        $priorities[0]['action'], '',
        'View your snapshot: ' . usegeo_report_url($report_id, $token), '',
        'The scan is directional and does not promise placement in AI answers.',
    );
    return wp_mail($email, 'Your UseGEO readiness snapshot', implode("\n", $lines), array('Content-Type: text/plain; charset=UTF-8'));
}

function usegeo_stripe_request($method, $path, $body = array()) {
    $args = array(
        'method' => $method,
        'timeout' => 20,
        'headers' => array('Authorization' => 'Bearer ' . usegeo_get_stripe_secret_key()),
    );
    if ($body) {
        $args['body'] = $body;
    }
    $response = wp_remote_request('https://api.stripe.com/v1/' . ltrim($path, '/'), $args);
    if (is_wp_error($response)) {
        return $response;
    }
    $decoded = json_decode(wp_remote_retrieve_body($response), true);
    if (wp_remote_retrieve_response_code($response) >= 300 || !is_array($decoded)) {
        return new WP_Error('stripe_error', isset($decoded['error']['message']) ? $decoded['error']['message'] : 'Stripe could not create the checkout session.');
    }
    return $decoded;
}

function usegeo_create_checkout_session($report_id, $token, $email) {
    if (!usegeo_is_checkout_enabled()) {
        return new WP_Error('checkout_disabled', 'Online checkout is not configured yet.');
    }
    $success_url = add_query_arg(array(
        'usegeo_report' => $report_id,
        'access_token' => rawurlencode($token),
        'session_id' => '{CHECKOUT_SESSION_ID}',
    ), home_url('/'));
    $success_url = str_replace(array('%7BCHECKOUT_SESSION_ID%7D', '%7bCHECKOUT_SESSION_ID%7d'), '{CHECKOUT_SESSION_ID}', $success_url);
    return usegeo_stripe_request('POST', 'checkout/sessions', array(
        'mode' => 'payment',
        'customer_email' => $email,
        'success_url' => $success_url,
        'cancel_url' => usegeo_report_url($report_id, $token),
        'line_items[0][price_data][currency]' => usegeo_report_currency(),
        'line_items[0][price_data][unit_amount]' => usegeo_report_price_cents(),
        'line_items[0][price_data][product_data][name]' => 'UseGEO Homepage Action Report',
        'line_items[0][price_data][product_data][description]' => 'Evidence-backed GEO priorities and a 30-day website action plan.',
        'line_items[0][quantity]' => 1,
        'metadata[report_id]' => $report_id,
    ));
}

function usegeo_report_request_endpoint(WP_REST_Request $request) {
    $origin = $request->get_header('origin') ?: $request->get_header('referer');
    if (!$origin || usegeo_normalize_origin($origin) !== usegeo_get_site_origin()) {
        return new WP_REST_Response(array('ok' => false, 'message' => 'Invalid request origin.'), 403);
    }
    $params = $request->get_json_params();
    $report = usegeo_create_report(isset($params['email']) ? $params['email'] : '', isset($params['snapshot']) ? $params['snapshot'] : array());
    if (is_wp_error($report)) {
        return new WP_REST_Response(array('ok' => false, 'message' => $report->get_error_message()), 400);
    }

    $email_sent = usegeo_email_free_snapshot($report['id'], $report['token'], $report['snapshot']);
    $response = array(
        'ok' => true,
        'report_url' => usegeo_report_url($report['id'], $report['token']),
        'checkout_enabled' => usegeo_is_checkout_enabled(),
        'email_sent' => (bool) $email_sent,
    );
    if (!empty($params['checkout'])) {
        $session = usegeo_create_checkout_session($report['id'], $report['token'], sanitize_email($params['email']));
        if (is_wp_error($session)) {
            return new WP_REST_Response(array('ok' => false, 'message' => $session->get_error_message(), 'report_url' => $response['report_url']), 503);
        }
        $response['checkout_url'] = esc_url_raw($session['url']);
        update_post_meta($report['id'], 'stripe_session_id', sanitize_text_field($session['id']));
    }
    return new WP_REST_Response($response, 200);
}

function usegeo_verify_stripe_signature($payload, $signature, $secret, $tolerance = 300) {
    $parts = array();
    foreach (explode(',', (string) $signature) as $part) {
        $pair = array_map('trim', explode('=', $part, 2));
        if (count($pair) === 2) {
            $parts[$pair[0]][] = $pair[1];
        }
    }
    if (empty($parts['t'][0]) || empty($parts['v1'])) {
        return false;
    }
    $timestamp = (int) $parts['t'][0];
    if (abs(time() - $timestamp) > $tolerance) {
        return false;
    }
    $expected = hash_hmac('sha256', $timestamp . '.' . $payload, $secret);
    foreach ($parts['v1'] as $candidate) {
        if (hash_equals($expected, $candidate)) {
            return true;
        }
    }
    return false;
}

function usegeo_mark_report_paid($report_id, $session_id, $token = '') {
    $report_id = (int) $report_id;
    if (!$token) {
        $token = (string) get_post_meta($report_id, 'report_access_token', true);
    }
    if ($token && !usegeo_find_report($report_id, $token)) {
        return false;
    }
    $already_paid = get_post_meta($report_id, 'payment_status', true) === 'paid';
    update_post_meta($report_id, 'payment_status', 'paid');
    update_post_meta($report_id, 'stripe_session_id', sanitize_text_field($session_id));
    update_post_meta($report_id, 'paid_at', current_time('mysql', true));
    if (!$already_paid && $token) {
        $email = get_post_meta($report_id, 'report_email', true);
        wp_mail($email, 'Your full UseGEO action report is unlocked', implode("\n", array(
            'Payment received. Your full GEO action report is ready.', '',
            usegeo_report_url($report_id, $token), '',
            'You can print or save the report as a PDF from the report page.',
        )), array('Content-Type: text/plain; charset=UTF-8'));
    }
    return true;
}

function usegeo_stripe_webhook_endpoint(WP_REST_Request $request) {
    $payload = $request->get_body();
    $secret = usegeo_get_stripe_webhook_secret();
    if (!$secret || !usegeo_verify_stripe_signature($payload, $request->get_header('stripe_signature'), $secret)) {
        return new WP_REST_Response(array('ok' => false), 400);
    }
    $event = json_decode($payload, true);
    if (isset($event['type']) && $event['type'] === 'checkout.session.completed') {
        $session = isset($event['data']['object']) ? $event['data']['object'] : array();
        $metadata = isset($session['metadata']) ? $session['metadata'] : array();
        if (isset($metadata['report_id']) && isset($session['payment_status']) && $session['payment_status'] === 'paid') {
            usegeo_mark_report_paid($metadata['report_id'], isset($session['id']) ? $session['id'] : '');
        }
    }
    return new WP_REST_Response(array('received' => true), 200);
}

function usegeo_register_commerce_routes() {
    register_rest_route('usegeo/v1', '/report', array('methods' => 'POST', 'callback' => 'usegeo_report_request_endpoint', 'permission_callback' => '__return_true'));
    register_rest_route('usegeo/v1', '/stripe-webhook', array('methods' => 'POST', 'callback' => 'usegeo_stripe_webhook_endpoint', 'permission_callback' => '__return_true'));
}
add_action('rest_api_init', 'usegeo_register_commerce_routes');

function usegeo_verify_checkout_return($report_id, $token, $session_id) {
    if (!$session_id || !usegeo_is_checkout_enabled() || !usegeo_find_report($report_id, $token)) {
        return false;
    }
    $session = usegeo_stripe_request('GET', 'checkout/sessions/' . rawurlencode($session_id));
    if (is_wp_error($session) || !isset($session['payment_status']) || $session['payment_status'] !== 'paid') {
        return false;
    }
    if (!isset($session['metadata']['report_id']) || (int) $session['metadata']['report_id'] !== (int) $report_id) {
        return false;
    }
    return usegeo_mark_report_paid($report_id, $session_id, $token);
}

function usegeo_render_report_page() {
    if (!isset($_GET['usegeo_report'], $_GET['access_token'])) {
        return;
    }
    $report_id = absint($_GET['usegeo_report']);
    $token = sanitize_text_field(wp_unslash($_GET['access_token']));
    $report = usegeo_find_report($report_id, $token);
    if (!$report) {
        status_header(404);
        wp_die('This report link is invalid or expired.', 'Report not found', array('response' => 404));
    }
    if (!empty($_GET['session_id'])) {
        usegeo_verify_checkout_return($report_id, $token, sanitize_text_field(wp_unslash($_GET['session_id'])));
    }
    $snapshot = json_decode(get_post_meta($report_id, 'report_snapshot', true), true);
    $paid = get_post_meta($report_id, 'payment_status', true) === 'paid';
    $priorities = usegeo_report_priorities($snapshot);
    get_header();
    ?>
    <main class="usegeo-shell usegeo-report-page">
        <header class="usegeo-report-page__hero">
            <div><span class="usegeo-report-page__kicker"><?php echo $paid ? 'Full action report' : 'Free readiness snapshot'; ?></span><h1><?php echo esc_html(wp_parse_url($snapshot['url'], PHP_URL_HOST)); ?></h1><p><?php echo esc_html($snapshot['summary'] ?: 'A directional assessment of the homepage signals that help AI systems understand and reuse your brand information.'); ?></p></div>
            <div class="usegeo-report-page__score"><strong><?php echo esc_html($snapshot['scores']['total']); ?></strong><span>/100 GEO readiness</span></div>
        </header>
        <section class="usegeo-report-page__dimensions" aria-label="GEO readiness dimensions">
            <?php foreach (usegeo_report_dimension_labels() as $key => $label) : ?><article><span><?php echo esc_html($label); ?></span><strong><?php echo esc_html($snapshot['scores'][$key]); ?></strong><div><i style="width:<?php echo esc_attr($snapshot['scores'][$key]); ?>%"></i></div></article><?php endforeach; ?>
        </section>
        <section class="usegeo-report-page__section"><span class="usegeo-report-page__kicker">Highest-leverage move</span><h2><?php echo esc_html($priorities[0]['label']); ?></h2><p><?php echo esc_html($priorities[0]['action']); ?></p></section>
        <?php if ($paid) : ?>
            <section class="usegeo-report-page__section"><span class="usegeo-report-page__kicker">Evidence-backed findings</span><h2>What to fix and why</h2><div class="usegeo-report-page__findings"><?php foreach ($snapshot['findings'] as $finding) : ?><article><span><?php echo esc_html($finding['severity']); ?></span><h3><?php echo esc_html($finding['title']); ?></h3><p><?php echo esc_html($finding['evidence']); ?></p><strong><?php echo esc_html($finding['recommendation']); ?></strong></article><?php endforeach; ?></div></section>
            <section class="usegeo-report-page__section"><span class="usegeo-report-page__kicker">30-day plan</span><h2>Three focused moves</h2><ol class="usegeo-report-page__plan"><?php foreach ($priorities as $index => $priority) : ?><li><span>Week <?php echo esc_html($index === 0 ? '1' : ($index === 1 ? '2' : '3-4')); ?></span><div><strong><?php echo esc_html($priority['label']); ?> · <?php echo esc_html($priority['score']); ?>/100</strong><p><?php echo esc_html($priority['action']); ?></p></div></li><?php endforeach; ?></ol><button class="usegeo-report-page__print" type="button" onclick="window.print()">Print or save as PDF</button></section>
        <?php else : ?>
            <section class="usegeo-report-page__paywall"><div><span class="usegeo-report-page__kicker">One-time purchase</span><h2>Turn the score into a 30-day action plan.</h2><p>Unlock every finding, evidence, three prioritized fixes, and a printable implementation report. No subscription and no sales call.</p></div><div><strong>$29</strong><span>one website · instant delivery</span><a class="usegeo-inline-cta" href="<?php echo esc_url(home_url('/#scanner')); ?>">Unlock from the scanner</a></div></section>
        <?php endif; ?>
        <p class="usegeo-report-page__disclaimer">Directional homepage signal scan. UseGEO does not promise placement or ranking in AI-generated answers.</p>
    </main>
    <?php
    get_footer();
    exit;
}
add_action('template_redirect', 'usegeo_render_report_page', 1);
