<?php

if (!defined('ABSPATH')) {
    exit;
}

require_once get_template_directory() . '/inc/scanner-fetch.php';
require_once get_template_directory() . '/inc/scanner-parse.php';
require_once get_template_directory() . '/inc/scanner-score.php';
require_once get_template_directory() . '/inc/scanner-llm.php';
require_once get_template_directory() . '/inc/scanner-ratelimit.php';

function usegeo_is_real_scanner_enabled() {
    if (defined('USEGEO_REAL_SCANNER_ENABLED') && !USEGEO_REAL_SCANNER_ENABLED) {
        return false;
    }
    return (bool) apply_filters('usegeo_real_scanner_enabled', true);
}

function usegeo_scan_http_status_for_error($code) {
    $map = array(
        'invalid_url' => 400,
        'blocked_url' => 400,
        'fetch_timeout' => 504,
        'fetch_failed' => 502,
        'non_html_response' => 502,
        'parse_failed' => 422,
        'rate_limited' => 429,
        'internal_error' => 500,
    );
    return isset($map[$code]) ? $map[$code] : 500;
}

function usegeo_scan_public_error_message($code) {
    $messages = array(
        'invalid_url' => 'Please enter a valid website URL (e.g., https://yourcompany.com).',
        'blocked_url' => 'This URL cannot be scanned. Please enter a public website URL.',
        'fetch_timeout' => 'The scan timed out. Please try again in a moment.',
        'fetch_failed' => 'We could not reach this website. Please check the URL and try again.',
        'non_html_response' => 'This URL does not return a web page. Please enter a website URL.',
        'parse_failed' => 'We could not analyze this page. Please try a different URL.',
        'rate_limited' => 'You have run several scans recently. Please wait a minute and try again.',
        'internal_error' => 'Something went wrong on our end. Please try again later.',
    );
    return isset($messages[$code]) ? $messages[$code] : $messages['internal_error'];
}

function usegeo_scan_rest_error($code) {
    return new WP_REST_Response(array(
        'ok' => false,
        'error' => array(
            'error_code' => $code,
            'message' => usegeo_scan_public_error_message($code),
        ),
    ), usegeo_scan_http_status_for_error($code));
}

function usegeo_verify_scan_request_origin(WP_REST_Request $request) {
    $origin = $request->get_header('origin');
    if (!$origin) {
        $origin = $request->get_header('referer');
    }
    if (!$origin) {
        return false;
    }
    return usegeo_normalize_origin($origin) && usegeo_normalize_origin($origin) === usegeo_get_site_origin();
}

function usegeo_scan_endpoint(WP_REST_Request $request) {
    if (!usegeo_is_real_scanner_enabled()) {
        return usegeo_scan_rest_error('internal_error');
    }

    $nonce = $request->get_header('x_wp_nonce');
    $has_nonce = $nonce && wp_verify_nonce($nonce, 'wp_rest');
    if (!$has_nonce && !usegeo_verify_scan_request_origin($request)) {
        return usegeo_scan_rest_error('invalid_url');
    }

    $limited = usegeo_scan_check_rate_limit();
    if (is_wp_error($limited)) {
        error_log('UseGEO scanner rate limit: ' . usegeo_scan_get_client_ip());
        return usegeo_scan_rest_error('rate_limited');
    }

    $params = $request->get_json_params();
    $url = isset($params['url']) ? sanitize_text_field(wp_unslash($params['url'])) : '';
    $fetched = usegeo_scan_fetch_html($url);
    if (is_wp_error($fetched)) {
        error_log('UseGEO scanner fetch blocked/failed: ' . $fetched->get_error_code());
        return usegeo_scan_rest_error($fetched->get_error_code());
    }

    $extracted = usegeo_scan_parse_html($fetched['html'], $fetched['final_url']);
    if (is_wp_error($extracted)) {
        error_log('UseGEO scanner parse failed: ' . $extracted->get_error_code());
        return usegeo_scan_rest_error('parse_failed');
    }

    $scores = usegeo_scan_calculate_scores($extracted);
    $findings = usegeo_scan_generate_findings($extracted, $scores);
    $llm_summary = usegeo_scan_llm_summarize($fetched['final_url'], $extracted, $scores, $findings);

    unset($extracted['_body_excerpt'], $extracted['_robots'], $extracted['_final_url']);

    return new WP_REST_Response(array(
        'ok' => true,
        'url' => $fetched['url'],
        'final_url' => $fetched['final_url'],
        'scores' => $scores,
        'grade' => usegeo_scan_grade($scores['total']),
        'extracted' => $extracted,
        'findings' => $findings,
        'llm_summary' => $llm_summary,
    ), 200);
}

function usegeo_register_scan_route() {
    register_rest_route('usegeo/v1', '/scan', array(
        'methods' => 'POST',
        'callback' => 'usegeo_scan_endpoint',
        'permission_callback' => '__return_true',
    ));
}
add_action('rest_api_init', 'usegeo_register_scan_route');
