<?php

if (!defined('ABSPATH')) {
    exit;
}

function usegeo_scan_get_llm_api_key() {
    if (defined('USEGEO_LLM_API_KEY') && USEGEO_LLM_API_KEY) {
        return (string) USEGEO_LLM_API_KEY;
    }
    return (string) apply_filters('usegeo_llm_api_key', '');
}

function usegeo_scan_get_llm_model() {
    return (string) apply_filters('usegeo_llm_model', defined('USEGEO_LLM_MODEL') ? USEGEO_LLM_MODEL : 'gpt-5.4-mini');
}

function usegeo_scan_get_llm_base_url() {
    $base_url = defined('USEGEO_LLM_BASE_URL') && USEGEO_LLM_BASE_URL
        ? (string) USEGEO_LLM_BASE_URL
        : 'https://api.openai.com/v1';
    $base_url = (string) apply_filters('usegeo_llm_base_url', $base_url);
    $base_url = rtrim($base_url, '/');

    if (substr($base_url, -17) === '/chat/completions') {
        return $base_url;
    }

    $parts = wp_parse_url($base_url);
    $path = isset($parts['path']) ? trim($parts['path'], '/') : '';
    if ($path === '') {
        return $base_url . '/v1/chat/completions';
    }

    return $base_url . '/chat/completions';
}

function usegeo_scan_get_llm_timeout() {
    return max(3, min(8, (int) apply_filters('usegeo_llm_timeout', 8)));
}

function usegeo_scan_fallback_llm_summary($scores, $findings) {
    $top = !empty($findings[0]['title']) ? $findings[0]['title'] : 'Review the weakest GEO signal first';
    return array(
        'available' => false,
        'summary' => sprintf('This directional scan found a GEO readiness score of %d. Start with the highest-priority signal gap before adding more content volume.', (int) $scores['total']),
        'top_recommendations' => array(
            !empty($findings[0]['recommendation']) ? $findings[0]['recommendation'] : 'Improve the weakest scoring dimension first.',
            !empty($findings[1]['recommendation']) ? $findings[1]['recommendation'] : 'Add clearer proof and comparison signals.',
            !empty($findings[2]['recommendation']) ? $findings[2]['recommendation'] : 'Keep the assessment directional until a manual audit verifies the details.',
        ),
        'audit_recommendation' => array(
            'recommended' => $scores['total'] < 80,
            'reason' => $top,
        ),
    );
}

function usegeo_scan_llm_summarize($url, $extracted, $scores, $findings) {
    $api_key = usegeo_scan_get_llm_api_key();
    if (!$api_key) {
        return usegeo_scan_fallback_llm_summary($scores, $findings);
    }

    $payload = array(
        'url' => esc_url_raw($url),
        'scores' => $scores,
        'extracted' => array(
            'title' => isset($extracted['title']) ? $extracted['title'] : '',
            'meta_description' => isset($extracted['meta_description']) ? $extracted['meta_description'] : '',
            'h1' => isset($extracted['h1']) ? $extracted['h1'] : array(),
            'h2' => isset($extracted['h2']) ? array_slice($extracted['h2'], 0, 8) : array(),
            'schema_types' => isset($extracted['schema_types']) ? $extracted['schema_types'] : array(),
            'word_count' => isset($extracted['word_count']) ? (int) $extracted['word_count'] : 0,
            'detected_signals' => isset($extracted['detected_signals']) ? $extracted['detected_signals'] : array(),
        ),
        'findings' => $findings,
    );

    $messages = array(
        array(
            'role' => 'system',
            'content' => 'You write concise GEO readiness diagnostics. Use only the provided extracted page signals. Do not invent customers, case studies, competitors, or AI ranking results. Return strict JSON with summary, top_recommendations array, and audit_recommendation object.',
        ),
        array(
            'role' => 'user',
            'content' => wp_json_encode($payload),
        ),
    );

    try {
        $request_body = apply_filters('usegeo_llm_request_body', array(
            'model' => usegeo_scan_get_llm_model(),
            'messages' => $messages,
            'temperature' => 0.2,
        ));

        $response = wp_remote_post(usegeo_scan_get_llm_base_url(), array(
            'timeout' => usegeo_scan_get_llm_timeout(),
            'headers' => array(
                'Authorization' => 'Bearer ' . $api_key,
                'Content-Type' => 'application/json',
            ),
            'body' => wp_json_encode($request_body),
        ));
    } catch (Throwable $exc) {
        error_log('UseGEO scanner LLM exception: ' . $exc->getMessage());
        return usegeo_scan_fallback_llm_summary($scores, $findings);
    }

    if (is_wp_error($response)) {
        error_log('UseGEO scanner LLM failed: ' . $response->get_error_message());
        return usegeo_scan_fallback_llm_summary($scores, $findings);
    }

    $code = (int) wp_remote_retrieve_response_code($response);
    $body = json_decode(wp_remote_retrieve_body($response), true);
    $content = isset($body['choices'][0]['message']['content']) ? $body['choices'][0]['message']['content'] : '';
    $decoded = $content ? json_decode($content, true) : null;

    if ($code < 200 || $code >= 300 || !is_array($decoded) || empty($decoded['summary']) || empty($decoded['top_recommendations'])) {
        error_log('UseGEO scanner LLM returned invalid response.');
        return usegeo_scan_fallback_llm_summary($scores, $findings);
    }

    return array(
        'available' => true,
        'summary' => sanitize_textarea_field($decoded['summary']),
        'top_recommendations' => array_values(array_map('sanitize_text_field', array_slice((array) $decoded['top_recommendations'], 0, 5))),
        'audit_recommendation' => array(
            'recommended' => !empty($decoded['audit_recommendation']['recommended']),
            'reason' => isset($decoded['audit_recommendation']['reason']) ? sanitize_text_field($decoded['audit_recommendation']['reason']) : '',
        ),
    );
}
