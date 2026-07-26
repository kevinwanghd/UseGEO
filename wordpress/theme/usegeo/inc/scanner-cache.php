<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Scan Result Caching
 *
 * Implements URL-based caching for scan results to avoid redundant API calls
 * and reduce LLM API costs.
 */

function usegeo_scan_get_cache_ttl() {
    $ttl = (int) apply_filters('usegeo_scan_cache_ttl', 12 * HOUR_IN_SECONDS);
    return max(1 * HOUR_IN_SECONDS, min(24 * HOUR_IN_SECONDS, $ttl));
}

function usegeo_scan_get_cache_enabled() {
    if (defined('USEGEO_SCAN_CACHE_DISABLED') && USEGEO_SCAN_CACHE_DISABLED) {
        return false;
    }
    return (bool) apply_filters('usegeo_scan_cache_enabled', true);
}

function usegeo_scan_cache_key($url) {
    $hash = md5(strtolower(trim((string) $url)));
    return 'usegeo_scan_' . $hash;
}

function usegeo_scan_get_cached_result($url) {
    if (!usegeo_scan_get_cache_enabled()) {
        return null;
    }

    $key = usegeo_scan_cache_key($url);
    $cached = get_transient($key);

    if ($cached === false) {
        return null;
    }

    if (!is_array($cached)) {
        return null;
    }

    $created_at = isset($cached['created_at']) ? (int) $cached['created_at'] : 0;
    $ttl = usegeo_scan_get_cache_ttl();

    if ($created_at > 0 && (time() - $created_at) > $ttl) {
        delete_transient($key);
        return null;
    }

    return $cached;
}

function usegeo_scan_set_cached_result($url, $result) {
    if (!usegeo_scan_get_cache_enabled()) {
        return false;
    }

    if (!is_array($result)) {
        return false;
    }

    $url = trim((string) $url);
    if ($url === '') {
        return false;
    }

    $key = usegeo_scan_cache_key($url);

    $cached = array(
        'created_at' => time(),
        'result' => $result,
    );

    $ttl = usegeo_scan_get_cache_ttl();

    return set_transient($key, $cached, $ttl);
}

function usegeo_scan_invalidate_cache($url) {
    $url = trim((string) $url);
    if ($url === '') {
        return false;
    }

    $key = usegeo_scan_cache_key($url);
    return delete_transient($key);
}

function usegeo_scan_get_cache_stats() {
    global $wpdb;

    $prefix = '_transient_usegeo_scan_';
    $pattern = $wpdb->esc_like($prefix) . '%';

    $count = (int) $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM {$wpdb->options} WHERE option_name LIKE %s",
        $pattern
    ));

    return array(
        'enabled' => usegeo_scan_get_cache_enabled(),
        'ttl_hours' => usegeo_scan_get_cache_ttl() / HOUR_IN_SECONDS,
        'cached_scans' => $count,
    );
}

function usegeo_scan_clear_all_cache() {
    global $wpdb;

    $prefix = '_transient_usegeo_scan_';
    $pattern = $wpdb->esc_like($prefix) . '%';

    $wpdb->query($wpdb->prepare(
        "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s",
        $pattern
    ));

    return $wpdb->rows_affected;
}

function usegeo_scan_log_cache_hit($url, $ttl_remaining) {
    if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log(sprintf(
            'UseGEO scanner cache HIT for %s (TTL remaining: %d seconds)',
            esc_url($url),
            (int) $ttl_remaining
        ));
    }
}

function usegeo_scan_log_cache_miss($url) {
    if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log(sprintf(
            'UseGEO scanner cache MISS for %s',
            esc_url($url)
        ));
    }
}

function usegeo_scan_log_cache_set($url, $ttl) {
    if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log(sprintf(
            'UseGEO scanner cache SET for %s (TTL: %d seconds)',
            esc_url($url),
            (int) $ttl
        ));
    }
}
