<?php

if (!defined('ABSPATH')) {
    exit;
}

function usegeo_scan_rate_limit_key($scope, $value) {
    return 'usegeo_scan_rl_' . $scope . '_' . md5((string) $value);
}

function usegeo_scan_get_client_ip() {
    $ip = isset($_SERVER['REMOTE_ADDR']) ? sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR'])) : 'unknown';
    return $ip ?: 'unknown';
}

function usegeo_scan_get_session_id() {
    if (isset($_COOKIE['usegeo_scan_session'])) {
        return sanitize_text_field(wp_unslash($_COOKIE['usegeo_scan_session']));
    }
    return usegeo_scan_get_client_ip();
}

function usegeo_scan_increment_bucket($key, $ttl) {
    $count = (int) get_transient($key);
    $count++;
    set_transient($key, $count, $ttl);
    return $count;
}

function usegeo_scan_check_rate_limit() {
    $ip_limit = (int) apply_filters('usegeo_scan_rate_limit_per_ip', 6);
    $session_limit = (int) apply_filters('usegeo_scan_rate_limit_per_session', 12);

    $ip_count = usegeo_scan_increment_bucket(usegeo_scan_rate_limit_key('ip', usegeo_scan_get_client_ip()), MINUTE_IN_SECONDS);
    $session_count = usegeo_scan_increment_bucket(usegeo_scan_rate_limit_key('session', usegeo_scan_get_session_id()), 5 * MINUTE_IN_SECONDS);

    if ($ip_count > $ip_limit || $session_count > $session_limit) {
        return new WP_Error('rate_limited', 'You have run several scans recently. Please wait a minute and try again.');
    }

    return true;
}
