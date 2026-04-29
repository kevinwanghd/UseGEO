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
        <nav class="usegeo-site-nav" aria-label="Primary navigation">
            <ul class="usegeo-menu usegeo-menu--primary">
                <li><a href="<?php echo esc_url(home_url('/#scanner')); ?>">Assessment</a></li>
                <li><a href="<?php echo esc_url(home_url('/#content')); ?>">Articles</a></li>
                <li><a href="<?php echo esc_url(home_url('/#community')); ?>">Cases</a></li>
                <li><a href="<?php echo esc_url(home_url('/about')); ?>">About</a></li>
            </ul>
        </nav>
    </div>
</header>
<div class="usegeo-site-main">
