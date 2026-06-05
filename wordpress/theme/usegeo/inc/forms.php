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


function usegeo_send_lead_notification($lead_id, $work_email, $company_type, $help_intent, $support_level, $scanner_query) {
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
        'Help intent: ' . ($help_intent ?: '(empty)'),
        'Support level: ' . ($support_level ?: '(empty)'),
        'Website input: ' . ($scanner_query ?: '(empty)'),
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
    $help_intent = isset($_POST['help_intent']) ? sanitize_key(wp_unslash($_POST['help_intent'])) : '';
    $support_level = isset($_POST['support_level']) ? sanitize_key(wp_unslash($_POST['support_level'])) : '';
    $timeline = isset($_POST['timeline']) ? sanitize_key(wp_unslash($_POST['timeline'])) : '';
    $scanner_query = isset($_POST['scanner_query']) ? esc_url_raw(wp_unslash($_POST['scanner_query'])) : '';
    $redirect_to = isset($_POST['redirect_to']) ? esc_url_raw(wp_unslash($_POST['redirect_to'])) : home_url('/#scanner');
    $company_type_options = usegeo_get_lead_company_type_options();
    $timeline_labels = usegeo_get_timeline_labels();

    if (
        !$work_email ||
        !is_email($work_email) ||
        !$company_type ||
        !isset($company_type_options[$company_type]) ||
        !$help_intent ||
        !$support_level ||
        !$timeline ||
        !isset($timeline_labels[$timeline]) ||
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
    update_post_meta($post_id, 'help_intent', $help_intent);
    update_post_meta($post_id, 'support_level', $support_level);
    update_post_meta($post_id, 'timeline', $timeline);
    update_post_meta($post_id, 'scanner_query', $scanner_query);
    update_post_meta($post_id, 'submitted_at', current_time('mysql'));
    update_post_meta($post_id, 'source', 'full_assessment_gate');

    $mail_result = usegeo_send_lead_notification($post_id, $work_email, $company_type, $help_intent, $support_level, $scanner_query);
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

