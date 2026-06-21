<?php

if (!defined('ABSPATH')) {
    exit;
}

function usegeo_render_flash_notice($type, $message) {
    if (!$message) {
        return;
    }
    ?>
    <div class="usegeo-flash usegeo-flash--<?php echo esc_attr($type); ?>" role="status">
        <p><?php echo esc_html($message); ?></p>
    </div>
    <?php
}


function usegeo_get_lead_notice() {
    $status = isset($_GET['lead_status']) ? sanitize_key(wp_unslash($_GET['lead_status'])) : '';

    if ($status === 'success') {
        return array(
            'type'    => 'success',
            'message' => __('Your full assessment request has been received. We will reach out to your business email shortly.', 'usegeo'),
        );
    }

    if ($status === 'success_no_mail') {
        return array(
            'type'    => 'success',
            'message' => __('Your request has been saved, but the email notification did not send successfully.', 'usegeo'),
        );
    }

    if ($status === 'invalid') {
        return array(
            'type'    => 'error',
            'message' => __('Please provide a valid business email and job title before submitting.', 'usegeo'),
        );
    }

    if ($status === 'failed') {
        return array(
            'type'    => 'error',
            'message' => __('Submission failed. Please try again.', 'usegeo'),
        );
    }

    return null;
}


function usegeo_get_case_submission_notice() {
    $status = isset($_GET['case_submission_status']) ? sanitize_key(wp_unslash($_GET['case_submission_status'])) : '';

    if ($status === 'success') {
        return array(
            'type'    => 'success',
            'message' => __('Your case submission has been received for editorial review.', 'usegeo'),
        );
    }

    if ($status === 'invalid') {
        return array(
            'type'    => 'error',
            'message' => __('Please complete the required case fields before submitting.', 'usegeo'),
        );
    }

    if ($status === 'failed') {
        return array(
            'type'    => 'error',
            'message' => __('Case submission failed. Please try again.', 'usegeo'),
        );
    }

    return null;
}



function usegeo_sanitize_scan_score($key) {
    if (!isset($_POST[$key]) || $_POST[$key] === '') {
        return '';
    }
    $value = (int) sanitize_text_field(wp_unslash($_POST[$key]));
    return (string) max(0, min(100, $value));
}

function usegeo_collect_scan_snapshot_from_post() {
    $snapshot = array(
        'scan_total_score' => usegeo_sanitize_scan_score('scan_total_score'),
        'scan_entity_clarity' => usegeo_sanitize_scan_score('scan_entity_clarity'),
        'scan_content_structure' => usegeo_sanitize_scan_score('scan_content_structure'),
        'scan_proof_signals' => usegeo_sanitize_scan_score('scan_proof_signals'),
        'scan_comparison_readiness' => usegeo_sanitize_scan_score('scan_comparison_readiness'),
        'scan_ai_extractability' => usegeo_sanitize_scan_score('scan_ai_extractability'),
        'scan_completed_at' => isset($_POST['scan_completed_at']) ? sanitize_text_field(wp_unslash($_POST['scan_completed_at'])) : '',
        'scan_findings_summary' => isset($_POST['scan_findings_summary']) ? sanitize_textarea_field(wp_unslash($_POST['scan_findings_summary'])) : '',
        'scan_llm_summary' => isset($_POST['scan_llm_summary']) ? sanitize_textarea_field(wp_unslash($_POST['scan_llm_summary'])) : '',
    );
    if (!$snapshot['scan_total_score'] || !$snapshot['scan_completed_at'] || !$snapshot['scan_findings_summary']) {
        $snapshot['scan_status'] = 'missing';
        return $snapshot;
    }
    $snapshot['scan_status'] = 'available';
    return $snapshot;
}

function usegeo_save_scan_snapshot_meta($post_id, $snapshot) {
    foreach ($snapshot as $key => $value) {
        update_post_meta($post_id, $key, $value);
    }
}

function usegeo_get_scan_weakest_dimension($snapshot_or_post_id) {
    $scores = array();
    $keys = array(
        'entity_clarity' => 'scan_entity_clarity',
        'content_structure' => 'scan_content_structure',
        'proof_signals' => 'scan_proof_signals',
        'comparison_readiness' => 'scan_comparison_readiness',
        'ai_extractability' => 'scan_ai_extractability',
    );
    foreach ($keys as $dimension => $meta_key) {
        $value = is_array($snapshot_or_post_id) ? (isset($snapshot_or_post_id[$meta_key]) ? $snapshot_or_post_id[$meta_key] : '') : get_post_meta($snapshot_or_post_id, $meta_key, true);
        if ($value !== '') {
            $scores[$dimension] = (int) $value;
        }
    }
    if (!$scores) {
        return '';
    }
    asort($scores, SORT_NUMERIC);
    $keys = array_keys($scores);
    return (string) $keys[0];
}

function usegeo_get_scan_top_finding($snapshot) {
    if (empty($snapshot['scan_findings_summary'])) {
        return '';
    }
    $lines = preg_split('/
?
/', $snapshot['scan_findings_summary']);
    return trim($lines[0]);
}

function usegeo_get_scan_recommended_next_step($snapshot) {
    if (!empty($snapshot['scan_llm_summary'])) {
        return trim(wp_trim_words($snapshot['scan_llm_summary'], 24));
    }
    $top = usegeo_get_scan_top_finding($snapshot);
    return $top ? $top : '';
}


function usegeo_get_budget_range_labels() {
    return array(
        'under_500' => __('Under $500', 'usegeo'),
        '500_1500' => __('$500–$1,500', 'usegeo'),
        '1500_5000' => __('$1,500–$5,000', 'usegeo'),
        '5000_plus' => __('$5,000+', 'usegeo'),
        'not_sure' => __('Not sure yet', 'usegeo'),
    );
}

function usegeo_get_region_labels() {
    return array(
        'north_america' => __('North America', 'usegeo'),
        'europe' => __('Europe', 'usegeo'),
        'asia_pacific' => __('Asia-Pacific', 'usegeo'),
        'global_remote' => __('Global / remote', 'usegeo'),
        'other' => __('Other', 'usegeo'),
    );
}

function usegeo_calculate_lead_quality($partner_consent, $help_intent, $support_level, $timeline, $scan_total_score = '') {
    if ($partner_consent !== 'yes') {
        return 'cold';
    }
    $high_help = in_array($help_intent, array('prioritized_next_steps', 'open_to_specialist'), true);
    $high_support = in_array($support_level, array('small_project', 'ongoing_support'), true);
    $high_timeline = in_array($timeline, array('next_quarter', 'need_help_soon'), true);
    if ($high_help && $high_support && $high_timeline) {
        return 'hot';
    }
    if ($help_intent === 'exploring_geo' && $support_level === 'not_hiring') {
        return 'cold';
    }
    if ($scan_total_score !== '' && (int) $scan_total_score < 70) {
        return 'warm';
    }
    return 'warm';
}

function usegeo_send_lead_notification($lead_id, $work_email, $company_type, $company_size, $help_intent, $support_level, $scanner_query, $timeline = '', $scan_snapshot = array(), $partner_context = array()) {
    $admin_email = get_option('admin_email');

    if (!$admin_email || !is_email($admin_email)) {
        return array(
            'sent'      => false,
            'recipient' => $admin_email,
            'error'     => 'invalid_admin_email',
        );
    }

    $subject = sprintf('[UseGEO] New full assessment lead from %s', $work_email);

    $message_lines = array(
        'A new UseGEO full assessment lead has been submitted.',
        '',
        'Lead ID: ' . $lead_id,
        'Work email: ' . $work_email,
        'Company type: ' . ($company_type ?: '(empty)'),
        'Company size: ' . ($company_size ?: '(empty)'),
        'Help intent: ' . ($help_intent ?: '(empty)'),
        'Support level: ' . ($support_level ?: '(empty)'),
        'Timeline: ' . ($timeline ?: '(empty)'),
        'Website input: ' . ($scanner_query ?: '(empty)'),
        'GEO Readiness Score: ' . (!empty($scan_snapshot['scan_total_score']) ? $scan_snapshot['scan_total_score'] : '(scan unavailable)'),
        'Weakest Dimension: ' . (usegeo_get_scan_weakest_dimension($scan_snapshot) ?: '(scan unavailable)'),
        'Top Finding: ' . (usegeo_get_scan_top_finding($scan_snapshot) ?: '(scan unavailable)'),
        'Recommended Next Step: ' . (usegeo_get_scan_recommended_next_step($scan_snapshot) ?: '(scan unavailable)'),
        'Partner Consent: ' . (!empty($partner_context['partner_consent']) ? $partner_context['partner_consent'] : 'no'),
        'Lead Quality: ' . (!empty($partner_context['lead_quality']) ? $partner_context['lead_quality'] : 'cold'),
        'Budget: ' . (!empty($partner_context['budget_range_label']) ? $partner_context['budget_range_label'] : '(empty)'),
        'Region: ' . (!empty($partner_context['region_label']) ? $partner_context['region_label'] : '(empty)'),
        'Submitted at: ' . current_time('mysql'),
        '',
        'View in WordPress admin:',
        admin_url('post.php?post=' . $lead_id . '&action=edit'),
    );

    $headers = array('Content-Type: text/plain; charset=UTF-8');

    delete_transient('usegeo_last_mail_error');
    $sent = wp_mail($admin_email, $subject, implode("\n", $message_lines), $headers);
    $error = get_transient('usegeo_last_mail_error');

    return array(
        'sent'      => (bool) $sent,
        'recipient' => $admin_email,
        'error'     => $error ? $error : '',
    );
}


function usegeo_capture_mail_failure($wp_error) {
    if (!$wp_error instanceof WP_Error) {
        return;
    }

    set_transient('usegeo_last_mail_error', $wp_error->get_error_message(), MINUTE_IN_SECONDS * 10);
}
add_action('wp_mail_failed', 'usegeo_capture_mail_failure');


function usegeo_normalize_origin($url) {
    $parts = wp_parse_url($url);

    if (!is_array($parts) || empty($parts['host'])) {
        return '';
    }

    $scheme = isset($parts['scheme']) ? $parts['scheme'] : 'https';
    $host = strtolower($parts['host']);
    $port = isset($parts['port']) ? ':' . $parts['port'] : '';

    return $scheme . '://' . $host . $port;
}


function usegeo_get_request_origin() {
    $origin = '';

    if (!empty($_SERVER['HTTP_ORIGIN'])) {
        $origin = sanitize_text_field(wp_unslash($_SERVER['HTTP_ORIGIN']));
    } elseif (!empty($_SERVER['HTTP_REFERER'])) {
        $origin = sanitize_text_field(wp_unslash($_SERVER['HTTP_REFERER']));
    }

    return usegeo_normalize_origin($origin);
}


function usegeo_get_site_origin() {
    return usegeo_normalize_origin(home_url('/'));
}


function usegeo_verify_public_form_nonce($action, $field_name) {
    if (is_user_logged_in()) {
        if (!isset($_POST[$field_name])) {
            return false;
        }

        $nonce = sanitize_text_field(wp_unslash($_POST[$field_name]));

        return (bool) wp_verify_nonce($nonce, $action);
    }

    return usegeo_get_request_origin() && usegeo_get_request_origin() === usegeo_get_site_origin();
}


function usegeo_handle_lead_capture() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        wp_safe_redirect(home_url('/'));
        exit;
    }

    if (!usegeo_verify_public_form_nonce('usegeo_capture_lead', 'usegeo_lead_nonce')) {
        wp_safe_redirect(add_query_arg('lead_status', 'invalid', home_url('/#scanner')));
        exit;
    }

    $work_email = isset($_POST['work_email']) ? sanitize_email(wp_unslash($_POST['work_email'])) : '';
    $company_type = isset($_POST['company_type']) ? sanitize_key(wp_unslash($_POST['company_type'])) : '';
    $company_size = isset($_POST['company_size']) ? sanitize_key(wp_unslash($_POST['company_size'])) : '';
    $help_intent = isset($_POST['help_intent']) ? sanitize_key(wp_unslash($_POST['help_intent'])) : '';
    $support_level = isset($_POST['support_level']) ? sanitize_key(wp_unslash($_POST['support_level'])) : '';
    $timeline = isset($_POST['timeline']) ? sanitize_key(wp_unslash($_POST['timeline'])) : '';
    $scanner_query = isset($_POST['scanner_query']) ? esc_url_raw(wp_unslash($_POST['scanner_query'])) : '';
    $budget_range = isset($_POST['budget_range']) ? sanitize_key(wp_unslash($_POST['budget_range'])) : '';
    $region = isset($_POST['region']) ? sanitize_key(wp_unslash($_POST['region'])) : '';
    $partner_consent = isset($_POST['partner_consent']) && wp_unslash($_POST['partner_consent']) === 'yes' ? 'yes' : 'no';
    $redirect_to = isset($_POST['redirect_to']) ? esc_url_raw(wp_unslash($_POST['redirect_to'])) : home_url('/#scanner');
    $scan_snapshot = usegeo_collect_scan_snapshot_from_post();
    $company_type_options = usegeo_get_lead_company_type_options();
    $company_size_options = usegeo_get_lead_company_size_options();
    $timeline_labels = usegeo_get_timeline_labels();
    $budget_labels = usegeo_get_budget_range_labels();
    $region_labels = usegeo_get_region_labels();

    if (
        !$work_email ||
        !is_email($work_email) ||
        !$company_type ||
        !isset($company_type_options[$company_type]) ||
        !$company_size ||
        !isset($company_size_options[$company_size]) ||
        !$help_intent ||
        !$support_level ||
        !$timeline ||
        !isset($timeline_labels[$timeline]) ||
        ($budget_range && !isset($budget_labels[$budget_range])) ||
        ($region && !isset($region_labels[$region])) ||
        !$scanner_query
    ) {
        wp_safe_redirect(add_query_arg('lead_status', 'invalid', $redirect_to));
        exit;
    }

    $post_id = wp_insert_post(array(
        'post_type'   => 'usegeo_lead',
        'post_status' => 'publish',
        'post_title'  => sprintf('%s - %s', $work_email, current_time('mysql')),
    ), true);

    if (is_wp_error($post_id)) {
        wp_safe_redirect(add_query_arg('lead_status', 'failed', $redirect_to));
        exit;
    }

    update_post_meta($post_id, 'work_email', $work_email);
    update_post_meta($post_id, 'company_type', $company_type);
    update_post_meta($post_id, 'company_size', $company_size);
    update_post_meta($post_id, 'help_intent', $help_intent);
    update_post_meta($post_id, 'support_level', $support_level);
    update_post_meta($post_id, 'timeline', $timeline);
    update_post_meta($post_id, 'scanner_query', $scanner_query);
    update_post_meta($post_id, 'submitted_at', current_time('mysql'));
    update_post_meta($post_id, 'source', 'full_assessment_gate');
    $lead_quality = usegeo_calculate_lead_quality($partner_consent, $help_intent, $support_level, $timeline, isset($scan_snapshot['scan_total_score']) ? $scan_snapshot['scan_total_score'] : '');
    update_post_meta($post_id, 'partner_consent', $partner_consent);
    update_post_meta($post_id, 'budget_range', $budget_range);
    update_post_meta($post_id, 'region', $region);
    update_post_meta($post_id, 'lead_quality', $lead_quality);
    usegeo_save_scan_snapshot_meta($post_id, $scan_snapshot);

    $partner_context = array(
        'partner_consent' => $partner_consent,
        'lead_quality' => $lead_quality,
        'budget_range_label' => $budget_range && isset($budget_labels[$budget_range]) ? $budget_labels[$budget_range] : '',
        'region_label' => $region && isset($region_labels[$region]) ? $region_labels[$region] : '',
    );
    $mail_result = usegeo_send_lead_notification($post_id, $work_email, $company_type, $company_size, $help_intent, $support_level, $scanner_query, $timeline, $scan_snapshot, $partner_context);
    $mail_sent = !empty($mail_result['sent']);
    update_post_meta($post_id, 'notification_sent', $mail_sent ? 'yes' : 'no');
    update_post_meta($post_id, 'notification_recipient', isset($mail_result['recipient']) ? $mail_result['recipient'] : '');
    update_post_meta($post_id, 'notification_error', isset($mail_result['error']) ? $mail_result['error'] : '');

    $redirect_status = $mail_sent ? 'success' : 'success_no_mail';
    wp_safe_redirect(add_query_arg('lead_status', $redirect_status, $redirect_to));
    exit;
}
add_action('admin_post_nopriv_usegeo_capture_lead', 'usegeo_handle_lead_capture');
add_action('admin_post_usegeo_capture_lead', 'usegeo_handle_lead_capture');


function usegeo_handle_case_submission() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        wp_safe_redirect(home_url('/#community'));
        exit;
    }

    if (!usegeo_verify_public_form_nonce('usegeo_submit_case', 'usegeo_case_nonce')) {
        wp_safe_redirect(add_query_arg('case_submission_status', 'invalid', home_url('/#community')));
        exit;
    }

    $redirect_to = isset($_POST['redirect_to']) ? esc_url_raw(wp_unslash($_POST['redirect_to'])) : home_url('/#community');
    $contributor_name = isset($_POST['contributor_name']) ? sanitize_text_field(wp_unslash($_POST['contributor_name'])) : '';
    $contact_email = isset($_POST['contact_email']) ? sanitize_email(wp_unslash($_POST['contact_email'])) : '';
    $company_type = isset($_POST['company_type']) ? sanitize_text_field(wp_unslash($_POST['company_type'])) : '';
    $industry = isset($_POST['industry']) ? sanitize_text_field(wp_unslash($_POST['industry'])) : '';
    $business_stage = isset($_POST['business_stage']) ? sanitize_text_field(wp_unslash($_POST['business_stage'])) : '';
    $core_problem = isset($_POST['core_problem']) ? sanitize_textarea_field(wp_unslash($_POST['core_problem'])) : '';
    $geo_actions = isset($_POST['geo_actions']) ? sanitize_textarea_field(wp_unslash($_POST['geo_actions'])) : '';
    $pages_updated = isset($_POST['pages_updated']) ? sanitize_textarea_field(wp_unslash($_POST['pages_updated'])) : '';
    $results_seen = isset($_POST['results_seen']) ? sanitize_textarea_field(wp_unslash($_POST['results_seen'])) : '';
    $lessons_learned = isset($_POST['lessons_learned']) ? sanitize_textarea_field(wp_unslash($_POST['lessons_learned'])) : '';
    $allow_anonymous = isset($_POST['allow_anonymous']) ? 'yes' : 'no';

    if (!$contact_email || !is_email($contact_email) || !$company_type || !$core_problem || !$geo_actions || !$results_seen) {
        wp_safe_redirect(add_query_arg('case_submission_status', 'invalid', $redirect_to));
        exit;
    }

    $title_parts = array_filter(array(
        $contributor_name ? $contributor_name : 'Anonymous contributor',
        $company_type,
    ));

    $post_id = wp_insert_post(array(
        'post_type'   => 'usegeo_case',
        'post_status' => 'publish',
        'post_title'  => implode(' - ', $title_parts) . ' - ' . current_time('mysql'),
    ), true);

    if (is_wp_error($post_id)) {
        wp_safe_redirect(add_query_arg('case_submission_status', 'failed', $redirect_to));
        exit;
    }

    update_post_meta($post_id, 'contributor_name', $contributor_name);
    update_post_meta($post_id, 'contact_email', $contact_email);
    update_post_meta($post_id, 'company_type', $company_type);
    update_post_meta($post_id, 'industry', $industry);
    update_post_meta($post_id, 'business_stage', $business_stage);
    update_post_meta($post_id, 'core_problem', $core_problem);
    update_post_meta($post_id, 'geo_actions', $geo_actions);
    update_post_meta($post_id, 'pages_updated', $pages_updated);
    update_post_meta($post_id, 'results_seen', $results_seen);
    update_post_meta($post_id, 'lessons_learned', $lessons_learned);
    update_post_meta($post_id, 'allow_anonymous', $allow_anonymous);
    update_post_meta($post_id, 'review_status', 'pending_review');
    update_post_meta($post_id, 'processing_lane', 'unassigned');
    update_post_meta($post_id, 'editorial_notes', '');
    update_post_meta($post_id, 'submitted_at', current_time('mysql'));
    update_post_meta($post_id, 'source', 'community_case_form');

    wp_safe_redirect(add_query_arg('case_submission_status', 'success', $redirect_to));
    exit;
}
add_action('admin_post_nopriv_usegeo_submit_case', 'usegeo_handle_case_submission');
add_action('admin_post_usegeo_submit_case', 'usegeo_handle_case_submission');

