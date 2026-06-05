<?php

if (!defined('ABSPATH')) {
    exit;
}
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
    <?php
    $usegeo_description = get_bloginfo('description', 'display');
    if (is_singular()) {
        $usegeo_description = wp_trim_words(get_the_excerpt(), 30);
    }
    if ($usegeo_description) :
    ?>
    <meta name="description" content="<?php echo esc_attr($usegeo_description); ?>">
    <?php endif; ?>
    <?php if (!has_site_icon()) : ?>
    <link rel="icon" href="<?php echo esc_url(get_template_directory_uri()); ?>/assets/images/favicon.svg" type="image/svg+xml">
    <?php endif; ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<header class="usegeo-site-header">
    <div class="usegeo-shell usegeo-site-header__inner">
        <div class="usegeo-site-brand">
            <?php if (function_exists('the_custom_logo') && has_custom_logo()) : ?>
                <div class="usegeo-site-logo usegeo-site-logo--image">
                    <?php the_custom_logo(); ?>
                </div>
            <?php else : ?>
                <a class="usegeo-site-logo" href="<?php echo esc_url(home_url('/')); ?>">
                    <?php esc_html_e('usegeo.blog', 'usegeo'); ?>
                </a>
            <?php endif; ?>
        </div>
        <button class="usegeo-menu-toggle" aria-label="Toggle menu" aria-expanded="false">
            <span class="usegeo-menu-toggle__bar"></span>
            <span class="usegeo-menu-toggle__bar"></span>
            <span class="usegeo-menu-toggle__bar"></span>
        </button>
        <nav class="usegeo-site-nav" aria-label="Primary navigation">
            <ul class="usegeo-menu usegeo-menu--primary">
                <li><a href="<?php echo esc_url(home_url('/#scanner')); ?>">Assessment</a></li>
                <li><a href="<?php echo esc_url(home_url('/articles/')); ?>">Articles</a></li>
                <li><a href="<?php echo esc_url(home_url('/cases/')); ?>">Cases</a></li>
                <li><a href="<?php echo esc_url(home_url('/about')); ?>">About</a></li>
            </ul>
        </nav>
    </div>
</header>
<div class="usegeo-site-main">
