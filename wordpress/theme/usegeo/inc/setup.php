<?php

if (!defined('ABSPATH')) {
    exit;
}

function usegeo_theme_setup() {
    load_theme_textdomain('usegeo', get_template_directory() . '/languages');

    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo', array(
        'height'      => 48,
        'width'       => 240,
        'flex-height' => true,
        'flex-width'  => true,
    ));
    add_theme_support('custom-line-height');
    add_theme_support('custom-spacing');
    add_theme_support('align-wide');
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script',
    ));

    register_nav_menus(array(
        'primary' => __('Primary Menu', 'usegeo'),
        'footer'  => __('Footer Menu', 'usegeo'),
    ));
}
add_action('after_setup_theme', 'usegeo_theme_setup');


function usegeo_force_local_admin_email($email) {
    $site_url = home_url('/');

    if (strpos($site_url, 'usegeo-local.local') === false) {
        return $email;
    }

    return 'kevin_wang_hd@163.com';
}
add_filter('pre_option_admin_email', 'usegeo_force_local_admin_email');


function usegeo_sync_local_admin_email() {
    $site_url = home_url('/');

    if (strpos($site_url, 'usegeo-local.local') === false) {
        return;
    }

    $target_email = 'kevin_wang_hd@163.com';
    $current_email = get_option('admin_email');

    if ($current_email !== $target_email) {
        update_option('admin_email', $target_email);
    }

    // Clear WordPress pending admin email confirmation on local environments.
    delete_option('new_admin_email');
}
add_action('init', 'usegeo_sync_local_admin_email', 5);


function usegeo_body_classes($classes) {
    $classes[] = 'usegeo-theme';

    if (is_front_page()) {
        $classes[] = 'usegeo-page-front';
    }

    if (is_single()) {
        $classes[] = 'usegeo-page-single';
    }

    if (is_page()) {
        $classes[] = 'usegeo-page-standard';
    }

    return $classes;
}
add_filter('body_class', 'usegeo_body_classes');

