<?php

if (!defined('ABSPATH')) {
    exit;
}

function usegeo_get_case_review_status_options() {
    return array(
        'pending_review' => __('Pending Review', 'usegeo'),
        'reviewing'      => __('Reviewing', 'usegeo'),
        'approved'       => __('Approved', 'usegeo'),
        'needs_followup' => __('Needs Follow-up', 'usegeo'),
        'archived'       => __('Archived', 'usegeo'),
    );
}


function usegeo_get_case_processing_lane_options() {
    return array(
        'unassigned'      => __('Unassigned', 'usegeo'),
        'featured_pool'   => __('Featured Pool', 'usegeo'),
        'article_pipeline'=> __('Article Pipeline', 'usegeo'),
        'followup_pool'   => __('Follow-up Pool', 'usegeo'),
        'archive_only'    => __('Archive Only', 'usegeo'),
    );
}


function usegeo_render_primary_menu() {
    if (has_nav_menu('primary')) {
        wp_nav_menu(array(
            'theme_location' => 'primary',
            'container'      => false,
            'menu_class'     => 'usegeo-menu usegeo-menu--primary',
            'fallback_cb'    => false,
        ));
        return;
    }
    ?>
    <ul class="usegeo-menu usegeo-menu--primary">
        <li><a href="<?php echo esc_url(home_url('/#scanner')); ?>"><?php esc_html_e('Assessment', 'usegeo'); ?></a></li>
        <li><a href="<?php echo esc_url(home_url('/articles/')); ?>"><?php esc_html_e('Articles', 'usegeo'); ?></a></li>
        <li><a href="<?php echo esc_url(home_url('/about')); ?>"><?php esc_html_e('About', 'usegeo'); ?></a></li>
    </ul>
    <?php
}


function usegeo_render_footer_menu() {
    if (has_nav_menu('footer')) {
        wp_nav_menu(array(
            'theme_location' => 'footer',
            'container'      => false,
            'menu_class'     => 'usegeo-menu usegeo-menu--footer',
            'fallback_cb'    => false,
        ));
        return;
    }
    ?>
    <ul class="usegeo-menu usegeo-menu--footer">
        <li><a href="<?php echo esc_url(home_url('/about')); ?>"><?php esc_html_e('About', 'usegeo'); ?></a></li>
        <li><a href="<?php echo esc_url(home_url('/privacy-policy')); ?>"><?php esc_html_e('Privacy Policy', 'usegeo'); ?></a></li>
        <li><a href="<?php echo esc_url(home_url('/cookie-policy')); ?>"><?php esc_html_e('Cookie Policy', 'usegeo'); ?></a></li>
    </ul>
    <?php
}


function usegeo_get_estimated_reading_time($post_id = 0) {
    $post = get_post($post_id ?: get_the_ID());

    if (!$post) {
        return 1;
    }

    $word_count = str_word_count(wp_strip_all_tags($post->post_content));
    $minutes = (int) ceil(max($word_count, 80) / 220);

    return max(1, $minutes);
}


function usegeo_get_card_excerpt($post_id = 0, $word_limit = 18) {
    $post = get_post($post_id ?: get_the_ID());

    if (!$post) {
        return '';
    }

    $source = $post->post_excerpt ? $post->post_excerpt : wp_strip_all_tags($post->post_content);
    return wp_trim_words($source, $word_limit);
}


function usegeo_case_submission_admin_columns($columns) {
    $date = isset($columns['date']) ? $columns['date'] : '';

    unset($columns['date']);

    $columns['contact_email'] = __('Contact Email', 'usegeo');
    $columns['company_type'] = __('Company Type', 'usegeo');
    $columns['industry'] = __('Industry', 'usegeo');
    $columns['review_status'] = __('Review Status', 'usegeo');
    $columns['processing_lane'] = __('Processing Lane', 'usegeo');
    $columns['allow_anonymous'] = __('Anonymous', 'usegeo');

    if ($date) {
        $columns['date'] = $date;
    }

    return $columns;
}
add_filter('manage_usegeo_case_posts_columns', 'usegeo_case_submission_admin_columns');
add_filter('manage_edit-usegeo_case_columns', 'usegeo_case_submission_admin_columns');


function usegeo_render_case_submission_admin_column($column, $post_id) {
    switch ($column) {
        case 'contact_email':
            echo esc_html(get_post_meta($post_id, 'contact_email', true) ?: '-');
            break;

        case 'company_type':
            echo esc_html(get_post_meta($post_id, 'company_type', true) ?: '-');
            break;

        case 'industry':
            echo esc_html(get_post_meta($post_id, 'industry', true) ?: '-');
            break;

        case 'review_status':
            $status = get_post_meta($post_id, 'review_status', true) ?: 'pending_review';
            $options = usegeo_get_case_review_status_options();
            echo esc_html(isset($options[$status]) ? $options[$status] : $status);
            break;

        case 'processing_lane':
            $lane = get_post_meta($post_id, 'processing_lane', true) ?: 'unassigned';
            $lane_options = usegeo_get_case_processing_lane_options();
            echo esc_html(isset($lane_options[$lane]) ? $lane_options[$lane] : $lane);
            break;

        case 'allow_anonymous':
            echo esc_html(get_post_meta($post_id, 'allow_anonymous', true) === 'yes' ? 'yes' : 'no');
            break;
    }
}
add_action('manage_usegeo_case_posts_custom_column', 'usegeo_render_case_submission_admin_column', 10, 2);


function usegeo_register_case_submission_meta_box() {
    add_meta_box(
        'usegeo-case-submission-details',
        __('Case Submission Details', 'usegeo'),
        'usegeo_render_case_submission_meta_box',
        'usegeo_case',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'usegeo_register_case_submission_meta_box');


function usegeo_render_case_submission_meta_box($post) {
    wp_nonce_field('usegeo_save_case_review', 'usegeo_case_review_nonce');

    $review_status = get_post_meta($post->ID, 'review_status', true) ?: 'pending_review';
    $processing_lane = get_post_meta($post->ID, 'processing_lane', true) ?: 'unassigned';
    $editorial_notes = get_post_meta($post->ID, 'editorial_notes', true);
    $fields = array(
        'contributor_name' => __('Contributor Name', 'usegeo'),
        'contact_email'    => __('Contact Email', 'usegeo'),
        'company_type'     => __('Company Type', 'usegeo'),
        'industry'         => __('Industry', 'usegeo'),
        'business_stage'   => __('Business Stage', 'usegeo'),
        'core_problem'     => __('Core Problem', 'usegeo'),
        'geo_actions'      => __('GEO Actions', 'usegeo'),
        'pages_updated'    => __('Pages Updated', 'usegeo'),
        'results_seen'     => __('Results Seen', 'usegeo'),
        'lessons_learned'  => __('Lessons Learned', 'usegeo'),
        'allow_anonymous'  => __('Allow Anonymous', 'usegeo'),
        'submitted_at'     => __('Submitted At', 'usegeo'),
        'source'           => __('Source', 'usegeo'),
    );
    ?>
    <table class="form-table" role="presentation">
        <tbody>
            <tr>
                <th scope="row"><label for="usegeo-review-status"><?php esc_html_e('Review Status', 'usegeo'); ?></label></th>
                <td>
                    <select id="usegeo-review-status" name="usegeo_review_status">
                        <?php foreach (usegeo_get_case_review_status_options() as $value => $label) : ?>
                            <option value="<?php echo esc_attr($value); ?>" <?php selected($review_status, $value); ?>><?php echo esc_html($label); ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="usegeo-processing-lane"><?php esc_html_e('Processing Lane', 'usegeo'); ?></label></th>
                <td>
                    <select id="usegeo-processing-lane" name="usegeo_processing_lane">
                        <?php foreach (usegeo_get_case_processing_lane_options() as $value => $label) : ?>
                            <option value="<?php echo esc_attr($value); ?>" <?php selected($processing_lane, $value); ?>><?php echo esc_html($label); ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="usegeo-editorial-notes"><?php esc_html_e('Editorial Notes', 'usegeo'); ?></label></th>
                <td>
                    <textarea id="usegeo-editorial-notes" name="usegeo_editorial_notes" rows="5" style="width:100%;"><?php echo esc_textarea($editorial_notes); ?></textarea>
                    <p class="description"><?php esc_html_e('Use this field to capture anonymization needs, follow-up questions, and whether the case should become a featured example or article source.', 'usegeo'); ?></p>
                </td>
            </tr>
            <?php foreach ($fields as $key => $label) : ?>
                <tr>
                    <th scope="row"><?php echo esc_html($label); ?></th>
                    <td>
                        <?php
                        $value = get_post_meta($post->ID, $key, true);
                        if (in_array($key, array('core_problem', 'geo_actions', 'pages_updated', 'results_seen', 'lessons_learned'), true)) {
                            echo '<div style="white-space:pre-wrap;">' . esc_html($value ?: '-') . '</div>';
                        } else {
                            echo esc_html($value ?: '-');
                        }
                        ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php
}


function usegeo_save_case_submission_review_meta($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!isset($_POST['usegeo_case_review_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['usegeo_case_review_nonce'])), 'usegeo_save_case_review')) {
        return;
    }

    if (get_post_type($post_id) !== 'usegeo_case') {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    $review_options = usegeo_get_case_review_status_options();
    $lane_options = usegeo_get_case_processing_lane_options();

    $review_status = isset($_POST['usegeo_review_status']) ? sanitize_key(wp_unslash($_POST['usegeo_review_status'])) : 'pending_review';
    $processing_lane = isset($_POST['usegeo_processing_lane']) ? sanitize_key(wp_unslash($_POST['usegeo_processing_lane'])) : 'unassigned';
    $editorial_notes = isset($_POST['usegeo_editorial_notes']) ? sanitize_textarea_field(wp_unslash($_POST['usegeo_editorial_notes'])) : '';

    if (!isset($review_options[$review_status])) {
        $review_status = 'pending_review';
    }

    if (!isset($lane_options[$processing_lane])) {
        $processing_lane = 'unassigned';
    }

    update_post_meta($post_id, 'review_status', $review_status);
    update_post_meta($post_id, 'processing_lane', $processing_lane);
    update_post_meta($post_id, 'editorial_notes', $editorial_notes);
}
add_action('save_post_usegeo_case', 'usegeo_save_case_submission_review_meta');


function usegeo_get_help_intent_labels() {
    return array(
        'exploring_geo'          => __('Just exploring GEO', 'usegeo'),
        'prioritized_next_steps' => __('Want prioritized next steps', 'usegeo'),
        'open_to_specialist'     => __('Open to working with a GEO specialist', 'usegeo'),
    );
}


function usegeo_get_support_level_labels() {
    return array(
        'not_hiring'      => __('Not planning to hire anyone', 'usegeo'),
        'small_project'   => __('Open to a small project', 'usegeo'),
        'ongoing_support' => __('Open to ongoing support', 'usegeo'),
        'not_sure'        => __('Not sure yet', 'usegeo'),
    );
}


function usegeo_get_timeline_labels() {
    return array(
        'just_researching' => __('Just researching', 'usegeo'),
        'next_quarter'     => __('Planning in the next quarter', 'usegeo'),
        'need_help_soon'   => __('Need help soon', 'usegeo'),
        'not_sure_yet'     => __('Not sure yet', 'usegeo'),
    );
}


function usegeo_get_lead_company_type_options() {
    return array(
        'b2b_saas'           => __('B2B SaaS', 'usegeo'),
        'agency_consultancy' => __('Agency / Consultancy', 'usegeo'),
        'marketplace'        => __('Marketplace', 'usegeo'),
        'media_content'      => __('Media / Content Business', 'usegeo'),
        'ecommerce'          => __('Ecommerce', 'usegeo'),
        'other'              => __('Other', 'usegeo'),
    );
}


function usegeo_get_lead_company_size_options() {
    return array(
        '1_10'      => __('1–10 people', 'usegeo'),
        '11_50'     => __('11–50 people', 'usegeo'),
        '51_200'    => __('51–200 people', 'usegeo'),
        '201_1000'  => __('201–1,000 people', 'usegeo'),
        '1000_plus' => __('1,000+ people', 'usegeo'),
    );
}



function usegeo_get_partner_status_labels() {
    return array(
        'new' => __('New', 'usegeo'),
        'reviewing' => __('Reviewing', 'usegeo'),
        'contacted' => __('Contacted', 'usegeo'),
        'sold' => __('Sold', 'usegeo'),
        'rejected' => __('Rejected', 'usegeo'),
    );
}

function usegeo_render_lead_priority_badge($label, $tone = 'neutral') {
    $tones = array(
        'neutral' => 'background:#e5e7eb;color:#334155;',
        'info'    => 'background:#dbeafe;color:#1d4ed8;',
        'success' => 'background:#dcfce7;color:#166534;',
        'warn'    => 'background:#fef3c7;color:#92400e;',
    );

    $style = isset($tones[$tone]) ? $tones[$tone] : $tones['neutral'];

    return '<span style="display:inline-flex;align-items:center;padding:4px 10px;border-radius:999px;font-size:12px;font-weight:700;' . esc_attr($style) . '">' . esc_html($label) . '</span>';
}


function usegeo_lead_admin_columns($columns) {
    $date = isset($columns['date']) ? $columns['date'] : '';

    unset($columns['date']);

    $columns['work_email'] = __('Work Email', 'usegeo');
    $columns['company_type'] = __('Company Type', 'usegeo');
    $columns['company_size'] = __('Company Size', 'usegeo');
    $columns['help_intent'] = __('Help Intent', 'usegeo');
    $columns['support_level'] = __('Support Level', 'usegeo');
    $columns['timeline'] = __('Timeline', 'usegeo');
    $columns['scanner_query'] = __('Website Input', 'usegeo');
    $columns['scan_total_score'] = __('GEO Score', 'usegeo');
    $columns['weakest_dimension'] = __('Weakest Dimension', 'usegeo');
    $columns['partner_consent'] = __('Partner Consent', 'usegeo');
    $columns['lead_quality'] = __('Lead Quality', 'usegeo');
    $columns['budget_range'] = __('Budget', 'usegeo');
    $columns['region'] = __('Region', 'usegeo');
    $columns['partner_status'] = __('Partner Status', 'usegeo');
    $columns['notification_status'] = __('Notification', 'usegeo');
    $columns['notification_recipient'] = __('Recipient', 'usegeo');

    if ($date) {
        $columns['date'] = $date;
    }

    return $columns;
}
add_filter('manage_usegeo_lead_posts_columns', 'usegeo_lead_admin_columns');
add_filter('manage_edit-usegeo_lead_columns', 'usegeo_lead_admin_columns');


function usegeo_render_lead_admin_column($column, $post_id) {
    if ($column === 'company_type') {
        $company_type = get_post_meta($post_id, 'company_type', true);
        $company_type_options = usegeo_get_lead_company_type_options();
        echo esc_html(isset($company_type_options[$company_type]) ? $company_type_options[$company_type] : ($company_type ?: '-'));
        return;
    }

    if ($column === 'company_size') {
        $company_size = get_post_meta($post_id, 'company_size', true);
        $company_size_options = usegeo_get_lead_company_size_options();
        echo esc_html(isset($company_size_options[$company_size]) ? $company_size_options[$company_size] : ($company_size ?: '-'));
        return;
    }

    if ($column === 'help_intent') {
        $help_intent = get_post_meta($post_id, 'help_intent', true);
        $help_intent_labels = usegeo_get_help_intent_labels();
        $help_intent_label = isset($help_intent_labels[$help_intent]) ? $help_intent_labels[$help_intent] : $help_intent;

        if ($help_intent === 'open_to_specialist') {
            echo usegeo_render_lead_priority_badge($help_intent_label, 'success');
        } elseif ($help_intent === 'prioritized_next_steps') {
            echo usegeo_render_lead_priority_badge($help_intent_label, 'info');
        } else {
            echo esc_html($help_intent_label ?: '-');
        }
        return;
    }

    if ($column === 'support_level') {
        $support_level = get_post_meta($post_id, 'support_level', true);
        $support_level_labels = usegeo_get_support_level_labels();
        $support_level_label = isset($support_level_labels[$support_level]) ? $support_level_labels[$support_level] : $support_level;

        if ($support_level === 'ongoing_support') {
            echo usegeo_render_lead_priority_badge($support_level_label, 'success');
        } elseif ($support_level === 'small_project') {
            echo usegeo_render_lead_priority_badge($support_level_label, 'info');
        } elseif ($support_level === 'not_sure') {
            echo usegeo_render_lead_priority_badge($support_level_label, 'warn');
        } else {
            echo esc_html($support_level_label ?: '-');
        }
        return;
    }

    if ($column === 'timeline') {
        $timeline = get_post_meta($post_id, 'timeline', true);
        $timeline_labels = usegeo_get_timeline_labels();
        $timeline_label = isset($timeline_labels[$timeline]) ? $timeline_labels[$timeline] : $timeline;

        if ($timeline === 'need_help_soon') {
            echo usegeo_render_lead_priority_badge($timeline_label, 'success');
        } elseif ($timeline === 'next_quarter') {
            echo usegeo_render_lead_priority_badge($timeline_label, 'info');
        } elseif ($timeline === 'not_sure_yet') {
            echo usegeo_render_lead_priority_badge($timeline_label, 'warn');
        } else {
            echo esc_html($timeline_label ?: '-');
        }
        return;
    }


    if ($column === 'scan_total_score') {
        $score = get_post_meta($post_id, 'scan_total_score', true);
        echo esc_html($score !== '' ? $score : '—');
        return;
    }

    if ($column === 'weakest_dimension') {
        $weakest = function_exists('usegeo_get_scan_weakest_dimension') ? usegeo_get_scan_weakest_dimension($post_id) : '';
        echo esc_html($weakest ?: '—');
        return;
    }


    if ($column === 'partner_consent') {
        echo esc_html(get_post_meta($post_id, 'partner_consent', true) ?: 'no');
        return;
    }

    if ($column === 'lead_quality') {
        echo esc_html(get_post_meta($post_id, 'lead_quality', true) ?: 'cold');
        return;
    }

    if ($column === 'budget_range') {
        $value = get_post_meta($post_id, 'budget_range', true);
        $labels = function_exists('usegeo_get_budget_range_labels') ? usegeo_get_budget_range_labels() : array();
        echo esc_html(isset($labels[$value]) ? $labels[$value] : ($value ?: '—'));
        return;
    }

    if ($column === 'region') {
        $value = get_post_meta($post_id, 'region', true);
        $labels = function_exists('usegeo_get_region_labels') ? usegeo_get_region_labels() : array();
        echo esc_html(isset($labels[$value]) ? $labels[$value] : ($value ?: '—'));
        return;
    }

    if ($column === 'partner_status') {
        $value = get_post_meta($post_id, 'partner_status', true) ?: 'new';
        $labels = usegeo_get_partner_status_labels();
        echo esc_html(isset($labels[$value]) ? $labels[$value] : $value);
        return;
    }

    switch ($column) {
        case 'work_email':
            echo esc_html(get_post_meta($post_id, 'work_email', true) ?: '—');
            break;

        case 'scanner_query':
            echo esc_html(get_post_meta($post_id, 'scanner_query', true) ?: '—');
            break;

        case 'notification_status':
            $sent = get_post_meta($post_id, 'notification_sent', true);
            $error = get_post_meta($post_id, 'notification_error', true);

            if ($sent === 'yes') {
                echo '<span style="color:#15803d;font-weight:700;">sent</span>';
            } elseif ($error) {
                echo '<span style="color:#b91c1c;font-weight:700;">failed</span><br>';
                echo '<span style="color:#6b7280;">' . esc_html($error) . '</span>';
            } else {
                echo '<span style="color:#92400e;font-weight:700;">not sent</span>';
            }
            break;

        case 'notification_recipient':
            echo esc_html(get_post_meta($post_id, 'notification_recipient', true) ?: '—');
            break;
    }
}
add_action('manage_usegeo_lead_posts_custom_column', 'usegeo_render_lead_admin_column', 10, 2);


function usegeo_register_lead_meta_box() {
    add_meta_box(
        'usegeo-lead-details',
        __('Lead Details', 'usegeo'),
        'usegeo_render_lead_meta_box',
        'usegeo_lead',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'usegeo_register_lead_meta_box');


function usegeo_render_lead_meta_box($post) {
    $work_email = get_post_meta($post->ID, 'work_email', true);
    $company_type = get_post_meta($post->ID, 'company_type', true);
    $company_size = get_post_meta($post->ID, 'company_size', true);
    $help_intent = get_post_meta($post->ID, 'help_intent', true);
    $support_level = get_post_meta($post->ID, 'support_level', true);
    $timeline = get_post_meta($post->ID, 'timeline', true);
    $scanner_query = get_post_meta($post->ID, 'scanner_query', true);
    $submitted_at = get_post_meta($post->ID, 'submitted_at', true);
    $source = get_post_meta($post->ID, 'source', true);
    $notification_sent = get_post_meta($post->ID, 'notification_sent', true);
    $notification_recipient = get_post_meta($post->ID, 'notification_recipient', true);
    $notification_error = get_post_meta($post->ID, 'notification_error', true);
    $company_type_options = usegeo_get_lead_company_type_options();
    $help_intent_labels = usegeo_get_help_intent_labels();
    $support_level_labels = usegeo_get_support_level_labels();
    $timeline_labels = usegeo_get_timeline_labels();
    $company_type_label = isset($company_type_options[$company_type]) ? $company_type_options[$company_type] : $company_type;
    $company_size_options = usegeo_get_lead_company_size_options();
    $company_size_label = isset($company_size_options[$company_size]) ? $company_size_options[$company_size] : $company_size;
    $help_intent_label = isset($help_intent_labels[$help_intent]) ? $help_intent_labels[$help_intent] : $help_intent;
    $support_level_label = isset($support_level_labels[$support_level]) ? $support_level_labels[$support_level] : $support_level;
    $timeline_label = isset($timeline_labels[$timeline]) ? $timeline_labels[$timeline] : $timeline;
    ?>
    <table class="form-table" role="presentation">
        <tbody>
            <tr>
                <th scope="row"><?php esc_html_e('Work Email', 'usegeo'); ?></th>
                <td><?php echo esc_html($work_email ?: '-'); ?></td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e('Company Type', 'usegeo'); ?></th>
                <td><?php echo esc_html($company_type_label ?: '-'); ?></td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e('Company Size', 'usegeo'); ?></th>
                <td><?php echo esc_html($company_size_label ?: '-'); ?></td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e('Help Intent', 'usegeo'); ?></th>
                <td>
                    <?php
                    if ($help_intent === 'open_to_specialist') {
                        echo usegeo_render_lead_priority_badge($help_intent_label, 'success');
                    } elseif ($help_intent === 'prioritized_next_steps') {
                        echo usegeo_render_lead_priority_badge($help_intent_label, 'info');
                    } else {
                        echo esc_html($help_intent_label ?: '-');
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e('Support Level', 'usegeo'); ?></th>
                <td>
                    <?php
                    if ($support_level === 'ongoing_support') {
                        echo usegeo_render_lead_priority_badge($support_level_label, 'success');
                    } elseif ($support_level === 'small_project') {
                        echo usegeo_render_lead_priority_badge($support_level_label, 'info');
                    } elseif ($support_level === 'not_sure') {
                        echo usegeo_render_lead_priority_badge($support_level_label, 'warn');
                    } else {
                        echo esc_html($support_level_label ?: '-');
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e('Timeline', 'usegeo'); ?></th>
                <td>
                    <?php
                    if ($timeline === 'need_help_soon') {
                        echo usegeo_render_lead_priority_badge($timeline_label, 'success');
                    } elseif ($timeline === 'next_quarter') {
                        echo usegeo_render_lead_priority_badge($timeline_label, 'info');
                    } elseif ($timeline === 'not_sure_yet') {
                        echo usegeo_render_lead_priority_badge($timeline_label, 'warn');
                    } else {
                        echo esc_html($timeline_label ?: '-');
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e('Website Input', 'usegeo'); ?></th>
                <td><?php echo esc_html($scanner_query ?: '-'); ?></td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e('GEO Score', 'usegeo'); ?></th>
                <td><?php echo esc_html(get_post_meta($post->ID, 'scan_total_score', true) ?: '-'); ?></td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e('Weakest Dimension', 'usegeo'); ?></th>
                <td><?php echo esc_html(function_exists('usegeo_get_scan_weakest_dimension') ? (usegeo_get_scan_weakest_dimension($post->ID) ?: '-') : '-'); ?></td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e('Scan Findings', 'usegeo'); ?></th>
                <td><div style="white-space:pre-wrap;"><?php echo esc_html(get_post_meta($post->ID, 'scan_findings_summary', true) ?: '-'); ?></div></td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e('Submitted At', 'usegeo'); ?></th>
                <td><?php echo esc_html($submitted_at ?: '-'); ?></td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e('Source', 'usegeo'); ?></th>
                <td><?php echo esc_html($source ?: '-'); ?></td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e('Partner Consent', 'usegeo'); ?></th>
                <td><?php echo esc_html(get_post_meta($post->ID, 'partner_consent', true) ?: 'no'); ?></td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e('Lead Quality', 'usegeo'); ?></th>
                <td><?php echo esc_html(get_post_meta($post->ID, 'lead_quality', true) ?: 'cold'); ?></td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e('Budget', 'usegeo'); ?></th>
                <td><?php $budget = get_post_meta($post->ID, 'budget_range', true); $budget_labels = function_exists('usegeo_get_budget_range_labels') ? usegeo_get_budget_range_labels() : array(); echo esc_html(isset($budget_labels[$budget]) ? $budget_labels[$budget] : ($budget ?: '-')); ?></td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e('Region', 'usegeo'); ?></th>
                <td><?php $region = get_post_meta($post->ID, 'region', true); $region_labels = function_exists('usegeo_get_region_labels') ? usegeo_get_region_labels() : array(); echo esc_html(isset($region_labels[$region]) ? $region_labels[$region] : ($region ?: '-')); ?></td>
            </tr>
            <tr>
                <th scope="row"><label for="usegeo-partner-status"><?php esc_html_e('Partner Status', 'usegeo'); ?></label></th>
                <td>
                    <select id="usegeo-partner-status" name="usegeo_partner_status">
                        <?php $partner_status = get_post_meta($post->ID, 'partner_status', true) ?: 'new'; foreach (usegeo_get_partner_status_labels() as $value => $label) : ?>
                            <option value="<?php echo esc_attr($value); ?>" <?php selected($partner_status, $value); ?>><?php echo esc_html($label); ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="usegeo-partner-notes"><?php esc_html_e('Partner Notes', 'usegeo'); ?></label></th>
                <td><textarea id="usegeo-partner-notes" name="usegeo_partner_notes" rows="4" style="width:100%;"><?php echo esc_textarea(get_post_meta($post->ID, 'partner_notes', true)); ?></textarea></td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e('Notification Status', 'usegeo'); ?></th>
                <td>
                    <?php
                    if ($notification_sent === 'yes') {
                        echo '<strong style="color:#15803d;">sent</strong>';
                    } elseif ($notification_error) {
                        echo '<strong style="color:#b91c1c;">failed</strong>';
                    } else {
                        echo '<strong style="color:#92400e;">not sent</strong>';
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e('Notification Recipient', 'usegeo'); ?></th>
                <td><?php echo esc_html($notification_recipient ?: '-'); ?></td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e('Notification Error', 'usegeo'); ?></th>
                <td><?php echo esc_html($notification_error ?: '-'); ?></td>
            </tr>
        </tbody>
    </table>
    <?php
}




function usegeo_save_lead_partner_pipeline_meta($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (get_post_type($post_id) !== 'usegeo_lead') return;
    if (!current_user_can('edit_post', $post_id)) return;
    $labels = usegeo_get_partner_status_labels();
    $status = isset($_POST['usegeo_partner_status']) ? sanitize_key(wp_unslash($_POST['usegeo_partner_status'])) : 'new';
    if (!isset($labels[$status])) $status = 'new';
    $notes = isset($_POST['usegeo_partner_notes']) ? sanitize_textarea_field(wp_unslash($_POST['usegeo_partner_notes'])) : '';
    update_post_meta($post_id, 'partner_status', $status);
    update_post_meta($post_id, 'partner_notes', $notes);
}
add_action('save_post_usegeo_lead', 'usegeo_save_lead_partner_pipeline_meta');


function usegeo_filter_partner_leads($query) {
    global $pagenow;
    if (!is_admin() || $pagenow !== 'edit.php' || empty($_GET['post_type']) || $_GET['post_type'] !== 'usegeo_lead' || !$query->is_main_query()) return;
    $meta_query = array();
    foreach (array('partner_consent', 'lead_quality', 'partner_status') as $key) {
        if (!empty($_GET[$key])) {
            $meta_query[] = array('key' => $key, 'value' => sanitize_key(wp_unslash($_GET[$key])), 'compare' => '=');
        }
    }
    if ($meta_query) $query->set('meta_query', $meta_query);
}
add_action('pre_get_posts', 'usegeo_filter_partner_leads');

function usegeo_render_partner_lead_filters() {
    global $typenow;
    if ($typenow !== 'usegeo_lead') return;
    $fields = array(
        'partner_consent' => array('yes' => 'Partner consent: yes', 'no' => 'Partner consent: no'),
        'lead_quality' => array('hot' => 'Lead quality: hot', 'warm' => 'Lead quality: warm', 'cold' => 'Lead quality: cold'),
        'partner_status' => usegeo_get_partner_status_labels(),
    );
    foreach ($fields as $name => $options) {
        $current = isset($_GET[$name]) ? sanitize_key(wp_unslash($_GET[$name])) : '';
        echo '<select name="' . esc_attr($name) . '"><option value="">' . esc_html(ucwords(str_replace('_', ' ', $name))) . '</option>';
        foreach ($options as $value => $label) echo '<option value="' . esc_attr($value) . '" ' . selected($current, $value, false) . '>' . esc_html($label) . '</option>';
        echo '</select>';
    }
    $export_url = wp_nonce_url(admin_url('admin-post.php?action=usegeo_export_partner_leads'), 'usegeo_export_partner_leads');
    echo '<a class="button" style="margin-left:8px;" href="' . esc_url($export_url) . '">' . esc_html__('Export Partner Leads CSV', 'usegeo') . '</a>';
}
add_action('restrict_manage_posts', 'usegeo_render_partner_lead_filters');

function usegeo_export_partner_leads() {
    if (!current_user_can('manage_options')) wp_die('Forbidden', 403);
    check_admin_referer('usegeo_export_partner_leads');
    $query = new WP_Query(array('post_type'=>'usegeo_lead','post_status'=>'publish','posts_per_page'=>500,'meta_query'=>array(array('key'=>'partner_consent','value'=>'yes','compare'=>'='),array('key'=>'lead_quality','value'=>array('hot','warm'),'compare'=>'IN'))));
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="usegeo-partner-leads.csv"');
    $out=fopen('php://output','w');
    fputcsv($out,array('lead_id','work_email','company_type','company_size','website_input','geo_score','weakest_dimension','partner_consent','lead_quality','budget_range','region','help_intent','support_level','timeline','partner_status','submitted_at'));
    foreach ($query->posts as $post) {
        fputcsv($out,array($post->ID,get_post_meta($post->ID,'work_email',true),get_post_meta($post->ID,'company_type',true),get_post_meta($post->ID,'company_size',true),get_post_meta($post->ID,'scanner_query',true),get_post_meta($post->ID,'scan_total_score',true),function_exists('usegeo_get_scan_weakest_dimension') ? usegeo_get_scan_weakest_dimension($post->ID) : '',get_post_meta($post->ID,'partner_consent',true),get_post_meta($post->ID,'lead_quality',true),get_post_meta($post->ID,'budget_range',true),get_post_meta($post->ID,'region',true),get_post_meta($post->ID,'help_intent',true),get_post_meta($post->ID,'support_level',true),get_post_meta($post->ID,'timeline',true),get_post_meta($post->ID,'partner_status',true) ?: 'new',get_post_meta($post->ID,'submitted_at',true)));
    }
    fclose($out); exit;
}
add_action('admin_post_usegeo_export_partner_leads', 'usegeo_export_partner_leads');


function usegeo_get_sponsor_placements() {
    return array(
        'homepage' => __('Homepage', 'usegeo'),
        'scanner_result' => __('Scanner Result', 'usegeo'),
        'article_sidebar' => __('Article Sidebar', 'usegeo'),
    );
}

function usegeo_default_sponsor_slots() {
    $defaults = array();
    foreach (usegeo_get_sponsor_placements() as $placement => $label) {
        $defaults[$placement] = array(
            'active' => 'no',
            'title' => '',
            'description' => '',
            'cta_label' => '',
            'url' => '',
        );
    }
    return $defaults;
}

function usegeo_get_sponsor_slots() {
    $saved = get_option('usegeo_sponsor_slots', array());
    return array_replace_recursive(usegeo_default_sponsor_slots(), is_array($saved) ? $saved : array());
}

function usegeo_register_sponsor_admin_page() {
    add_theme_page(__('UseGEO Sponsors', 'usegeo'), __('UseGEO Sponsors', 'usegeo'), 'manage_options', 'usegeo-sponsors', 'usegeo_render_sponsor_admin_page');
}
add_action('admin_menu', 'usegeo_register_sponsor_admin_page');

function usegeo_render_sponsor_admin_page() {
    if (!current_user_can('manage_options')) return;
    if (isset($_POST['usegeo_sponsor_nonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['usegeo_sponsor_nonce'])), 'usegeo_save_sponsors')) {
        $next = usegeo_default_sponsor_slots();
        foreach (usegeo_get_sponsor_placements() as $placement => $label) {
            $row = isset($_POST['sponsors'][$placement]) && is_array($_POST['sponsors'][$placement]) ? wp_unslash($_POST['sponsors'][$placement]) : array();
            $next[$placement] = array(
                'active' => !empty($row['active']) ? 'yes' : 'no',
                'title' => isset($row['title']) ? sanitize_text_field($row['title']) : '',
                'description' => isset($row['description']) ? sanitize_textarea_field($row['description']) : '',
                'cta_label' => isset($row['cta_label']) ? sanitize_text_field($row['cta_label']) : '',
                'url' => isset($row['url']) ? esc_url_raw($row['url']) : '',
            );
        }
        update_option('usegeo_sponsor_slots', $next, false);
        echo '<div class="notice notice-success"><p>' . esc_html__('Sponsor slots saved.', 'usegeo') . '</p></div>';
    }
    $slots = usegeo_get_sponsor_slots();
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('UseGEO Sponsors', 'usegeo'); ?></h1>
        <form method="post">
            <?php wp_nonce_field('usegeo_save_sponsors', 'usegeo_sponsor_nonce'); ?>
            <?php foreach (usegeo_get_sponsor_placements() as $placement => $label) : $slot = $slots[$placement]; ?>
                <h2><?php echo esc_html($label); ?></h2>
                <table class="form-table" role="presentation"><tbody>
                    <tr><th><?php esc_html_e('Active', 'usegeo'); ?></th><td><label><input type="checkbox" name="sponsors[<?php echo esc_attr($placement); ?>][active]" value="yes" <?php checked($slot['active'], 'yes'); ?>> <?php esc_html_e('Show this sponsor slot', 'usegeo'); ?></label></td></tr>
                    <tr><th><?php esc_html_e('Title', 'usegeo'); ?></th><td><input class="regular-text" name="sponsors[<?php echo esc_attr($placement); ?>][title]" value="<?php echo esc_attr($slot['title']); ?>"></td></tr>
                    <tr><th><?php esc_html_e('Description', 'usegeo'); ?></th><td><textarea class="large-text" rows="3" name="sponsors[<?php echo esc_attr($placement); ?>][description]"><?php echo esc_textarea($slot['description']); ?></textarea></td></tr>
                    <tr><th><?php esc_html_e('CTA Label', 'usegeo'); ?></th><td><input class="regular-text" name="sponsors[<?php echo esc_attr($placement); ?>][cta_label]" value="<?php echo esc_attr($slot['cta_label']); ?>"></td></tr>
                    <tr><th><?php esc_html_e('URL', 'usegeo'); ?></th><td><input class="regular-text" type="url" name="sponsors[<?php echo esc_attr($placement); ?>][url]" value="<?php echo esc_attr($slot['url']); ?>"></td></tr>
                </tbody></table>
            <?php endforeach; ?>
            <?php submit_button(__('Save Sponsor Slots', 'usegeo')); ?>
        </form>
    </div>
    <?php
}

function usegeo_render_sponsor_slot($placement) {
    $slots = usegeo_get_sponsor_slots();
    if (empty($slots[$placement]) || $slots[$placement]['active'] !== 'yes' || !$slots[$placement]['title'] || !$slots[$placement]['url']) return;
    $slot = $slots[$placement];
    ?>
    <aside class="usegeo-sponsor-card usegeo-card" data-usegeo-sponsor-slot="<?php echo esc_attr($placement); ?>">
        <span class="usegeo-sponsor-card__label"><?php esc_html_e('Sponsored', 'usegeo'); ?></span>
        <h3><?php echo esc_html($slot['title']); ?></h3>
        <?php if ($slot['description']) : ?><p><?php echo esc_html($slot['description']); ?></p><?php endif; ?>
        <a class="usegeo-inline-cta usegeo-inline-cta--ghost" href="<?php echo esc_url($slot['url']); ?>" target="_blank" rel="noopener sponsored"><?php echo esc_html($slot['cta_label'] ?: __('Learn more', 'usegeo')); ?></a>
    </aside>
    <?php
}
