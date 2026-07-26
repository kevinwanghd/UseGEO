<?php

define('ABSPATH', __DIR__);

class WP_Error {
    private $code;
    private $message;
    public function __construct($code, $message) { $this->code = $code; $this->message = $message; }
    public function get_error_code() { return $this->code; }
    public function get_error_message() { return $this->message; }
}

function is_wp_error($value) { return $value instanceof WP_Error; }
function esc_url_raw($value) { return filter_var($value, FILTER_SANITIZE_URL); }
function wp_http_validate_url($value) { return filter_var($value, FILTER_VALIDATE_URL) !== false; }
function sanitize_key($value) { return preg_replace('/[^a-z0-9_\-]/', '', strtolower((string) $value)); }
function sanitize_text_field($value) { return trim(strip_tags((string) $value)); }
function sanitize_textarea_field($value) { return trim(strip_tags((string) $value)); }
function current_time() { return '2026-07-11 00:00:00'; }
function add_action() { return true; }

require_once dirname(__DIR__) . '/wordpress/theme/usegeo/inc/commerce.php';

function assert_true($condition, $message) {
    if (!$condition) {
        fwrite(STDERR, "FAIL: {$message}\n");
        exit(1);
    }
}

$invalid = usegeo_normalize_report_snapshot(array('url' => 'not-a-url'));
assert_true(is_wp_error($invalid) && $invalid->get_error_code() === 'invalid_snapshot', 'invalid snapshots are rejected');

$snapshot = usegeo_normalize_report_snapshot(array(
    'url' => 'https://example.com',
    'scores' => array(
        'total' => 62,
        'entity_clarity' => 120,
        'content_structure' => 70,
        'proof_signals' => -20,
        'comparison_readiness' => 30,
        'ai_extractability' => 80,
    ),
    'findings' => array(array(
        'severity' => 'HIGH<script>',
        'dimension' => 'proof_signals',
        'title' => '<b>Weak proof</b>',
        'evidence' => '<script>alert(1)</script>No case studies',
        'recommendation' => 'Add customer evidence',
    )),
    'extracted' => array('title' => 'Example', 'h1' => array('One'), 'schema_types' => array('Organization'), 'word_count' => 450),
));
assert_true(!is_wp_error($snapshot), 'valid snapshots normalize');
assert_true($snapshot['scores']['entity_clarity'] === 100, 'scores are capped at 100');
assert_true($snapshot['scores']['proof_signals'] === 0, 'scores are floored at zero');
assert_true($snapshot['findings'][0]['title'] === 'Weak proof', 'finding markup is removed');

$priorities = usegeo_report_priorities($snapshot);
assert_true(count($priorities) === 3, 'three priorities are returned');
assert_true($priorities[0]['key'] === 'proof_signals', 'lowest score is the first priority');
assert_true($priorities[1]['key'] === 'comparison_readiness', 'second-lowest score is second');

$payload = '{"id":"evt_test"}';
$secret = 'whsec_test';
$timestamp = time();
$signature = 't=' . $timestamp . ',v1=' . hash_hmac('sha256', $timestamp . '.' . $payload, $secret);
assert_true(usegeo_verify_stripe_signature($payload, $signature, $secret), 'valid Stripe signatures pass');
assert_true(!usegeo_verify_stripe_signature($payload . 'x', $signature, $secret), 'tampered Stripe payloads fail');
assert_true(!usegeo_verify_stripe_signature($payload, 't=' . ($timestamp - 600) . ',v1=bad', $secret), 'expired Stripe signatures fail');

$commerce_source = file_get_contents(dirname(__DIR__) . '/wordpress/theme/usegeo/inc/commerce.php');
assert_true(strpos($commerce_source, "'mode' => 'payment'") !== false, 'checkout uses one-time payment mode');
assert_true(strpos($commerce_source, "'metadata[report_id]'") !== false, 'checkout binds report metadata');
assert_true(strpos($commerce_source, "'metadata[report_token]'") === false, 'checkout metadata does not expose report access tokens');
assert_true(strpos($commerce_source, 'checkout.session.completed') !== false, 'webhook handles completed checkout sessions');
assert_true(strpos($commerce_source, "str_replace(array('%7BCHECKOUT_SESSION_ID%7D'") !== false, 'Stripe success URL preserves the session placeholder');

$scanner_template = file_get_contents(dirname(__DIR__) . '/wordpress/theme/usegeo/template-parts/scanner/scanner.php');
assert_true(strpos($scanner_template, 'Unlock full report · $29') !== false, 'scanner exposes the fixed-price report');
assert_true(strpos($scanner_template, 'name="company_size"') === false, 'first conversion step does not ask for company size');
assert_true(strpos($scanner_template, 'name="budget_range"') === false, 'first conversion step does not ask for budget');

echo "PASS: commerce unit tests\n";
