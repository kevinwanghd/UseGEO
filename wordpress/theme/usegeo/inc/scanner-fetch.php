<?php

if (!defined('ABSPATH')) {
    exit;
}

function usegeo_scan_get_fetch_timeout() {
    $timeout = (int) apply_filters('usegeo_scan_fetch_timeout', 10);
    return max(5, min(15, $timeout));
}

function usegeo_scan_get_max_bytes() {
    $bytes = (int) apply_filters('usegeo_scan_max_bytes', 2 * 1024 * 1024);
    return max(256 * 1024, min(5 * 1024 * 1024, $bytes));
}

function usegeo_scan_get_user_agent() {
    $user_agent = (string) apply_filters('usegeo_scan_user_agent', 'UseGEO/1.0 (+https://usegeo.blog)');
    return $user_agent ?: 'UseGEO/1.0';
}

function usegeo_scan_error($code, $message, $http_status = 400) {
    return array(
        'ok' => false,
        'http_status' => $http_status,
        'error' => array(
            'error_code' => $code,
            'message' => $message,
        ),
    );
}

function usegeo_scan_is_private_ip($ip) {
    if (!filter_var($ip, FILTER_VALIDATE_IP)) {
        return true;
    }

    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
        $long = ip2long($ip);
        $ranges = array(
            array('0.0.0.0', '0.255.255.255'),
            array('10.0.0.0', '10.255.255.255'),
            array('127.0.0.0', '127.255.255.255'),
            array('169.254.0.0', '169.254.255.255'),
            array('172.16.0.0', '172.31.255.255'),
            array('192.168.0.0', '192.168.255.255'),
        );
        foreach ($ranges as $range) {
            if ($long >= ip2long($range[0]) && $long <= ip2long($range[1])) {
                return true;
            }
        }
        return false;
    }

    $normalized = strtolower($ip);
    return $normalized === '::1'
        || strpos($normalized, 'fc') === 0
        || strpos($normalized, 'fd') === 0
        || strpos($normalized, 'fe80:') === 0;
}

function usegeo_scan_absolute_url($location, $base_url) {
    $location = trim((string) $location);
    if ($location === '') {
        return '';
    }
    if (preg_match('#^https?://#i', $location)) {
        return $location;
    }

    $base = wp_parse_url($base_url);
    if (!is_array($base) || empty($base['scheme']) || empty($base['host'])) {
        return '';
    }

    $scheme = $base['scheme'];
    $host = $base['host'];
    $port = isset($base['port']) ? ':' . $base['port'] : '';

    if (strpos($location, '//') === 0) {
        return $scheme . ':' . $location;
    }
    if (strpos($location, '/') === 0) {
        return $scheme . '://' . $host . $port . $location;
    }

    $path = isset($base['path']) ? $base['path'] : '/';
    $dir = preg_replace('#/[^/]*$#', '/', $path);
    return $scheme . '://' . $host . $port . $dir . $location;
}

function usegeo_scan_validate_public_url($url) {
    $url = trim((string) $url);
    if ($url && !preg_match('#^https?://#i', $url) && preg_match('/^[\w.-]+\.[a-z]{2,}/i', $url)) {
        $url = 'https://' . $url;
    }

    if (!$url || strlen($url) > 2048 || !wp_http_validate_url($url)) {
        return new WP_Error('invalid_url', 'Please enter a valid public website URL.');
    }

    $parts = wp_parse_url($url);
    if (!is_array($parts) || empty($parts['scheme']) || empty($parts['host'])) {
        return new WP_Error('invalid_url', 'Please enter a valid public website URL.');
    }

    $scheme = strtolower($parts['scheme']);
    if (!in_array($scheme, array('http', 'https'), true)) {
        return new WP_Error('blocked_url', 'This URL cannot be scanned. Please enter a public website URL.');
    }

    $host = strtolower($parts['host']);
    if ($host === 'localhost' || $host === '0.0.0.0') {
        return new WP_Error('blocked_url', 'This URL cannot be scanned. Please enter a public website URL.');
    }

    $ips = filter_var($host, FILTER_VALIDATE_IP) ? array($host) : gethostbynamel($host);
    if (!$ips) {
        return new WP_Error('fetch_failed', 'We could not resolve this website. Please check the URL and try again.');
    }

    foreach ((array) $ips as $ip) {
        if (usegeo_scan_is_private_ip($ip)) {
            return new WP_Error('blocked_url', 'This URL cannot be scanned. Please enter a public website URL.');
        }
    }

    return esc_url_raw($url);
}

function usegeo_scan_fetch_html($url) {
    $valid_url = usegeo_scan_validate_public_url($url);
    if (is_wp_error($valid_url)) {
        return $valid_url;
    }

    $current_url = $valid_url;
    $response = null;

    for ($redirects = 0; $redirects <= 3; $redirects++) {
        $current_check = usegeo_scan_validate_public_url($current_url);
        if (is_wp_error($current_check)) {
            return new WP_Error('blocked_url', 'This URL cannot be scanned. Please enter a public website URL.');
        }
        $current_url = $current_check;

        $response = wp_remote_get($current_url, array(
            'timeout' => usegeo_scan_get_fetch_timeout(),
            'redirection' => 0,
            'limit_response_size' => usegeo_scan_get_max_bytes(),
            'user-agent' => usegeo_scan_get_user_agent(),
            'headers' => array(
                'Accept' => 'text/html,application/xhtml+xml;q=0.9,*/*;q=0.1',
            ),
        ));

        if (is_wp_error($response)) {
            $code = $response->get_error_code() === 'http_request_failed' ? 'fetch_failed' : $response->get_error_code();
            if (strpos($response->get_error_message(), 'timed out') !== false) {
                return new WP_Error('fetch_timeout', 'The scan timed out. Please try again in a moment.');
            }
            return new WP_Error($code, 'We could not reach this website. Please check the URL and try again.');
        }

        $status = (int) wp_remote_retrieve_response_code($response);
        if (in_array($status, array(301, 302, 303, 307, 308), true)) {
            $location = wp_remote_retrieve_header($response, 'location');
            if (!$location) {
                return new WP_Error('fetch_failed', 'We could not reach this website. Please check the URL and try again.');
            }
            $next_url = usegeo_scan_absolute_url($location, $current_url);
            if (!$next_url) {
                return new WP_Error('blocked_url', 'This URL cannot be scanned. Please enter a public website URL.');
            }
            $current_url = $next_url;
            continue;
        }

        break;
    }

    if (!$response) {
        return new WP_Error('fetch_failed', 'We could not reach this website. Please check the URL and try again.');
    }

    $final_check = usegeo_scan_validate_public_url($current_url);
    if (is_wp_error($final_check)) {
        return new WP_Error('blocked_url', 'This URL cannot be scanned. Please enter a public website URL.');
    }
    $final_url = $final_check;

    $status = (int) wp_remote_retrieve_response_code($response);
    if ($status < 200 || $status >= 400) {
        return new WP_Error('fetch_failed', 'We could not reach this website. Please check the URL and try again.');
    }

    $content_type = strtolower((string) wp_remote_retrieve_header($response, 'content-type'));
    if ($content_type && strpos($content_type, 'text/html') === false) {
        return new WP_Error('non_html_response', 'This URL does not return a web page. Please enter a website URL.');
    }

    $body = (string) wp_remote_retrieve_body($response);
    if ($body === '') {
        return new WP_Error('parse_failed', 'We could not analyze this page. Please try a different URL.');
    }

    if (strlen($body) > usegeo_scan_get_max_bytes()) {
        return new WP_Error('fetch_failed', 'This page is too large to scan. Please try a smaller public web page.');
    }

    return array(
        'url' => $valid_url,
        'final_url' => $final_url,
        'html' => $body,
    );
}
