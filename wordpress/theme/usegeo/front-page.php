<?php

if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>
<main class="usegeo-shell">
    <?php get_template_part('template-parts/hero/hero'); ?>
    <div id="scanner" class="usegeo-home-scanner">
        <?php get_template_part('template-parts/scanner/scanner'); ?>
    </div>
    <?php if (function_exists('usegeo_render_sponsor_slot')) { usegeo_render_sponsor_slot('homepage'); } ?>
    <?php get_template_part('template-parts/conversion/services'); ?>
    <?php get_template_part('template-parts/conversion/methodology'); ?>
    <?php get_template_part('template-parts/conversion/sample-report'); ?>
    <?php get_template_part('template-parts/value/value-proposition'); ?>
    <?php get_template_part('template-parts/learning/learning-path'); ?>
    <?php get_template_part('template-parts/content-hub/seo-cluster'); ?>
    <div id="content">
        <?php get_template_part('template-parts/content-hub/content-hub'); ?>
    </div>
    <div id="community">
        <?php get_template_part('template-parts/community/community-intro'); ?>
    </div>
    <div id="cases">
        <?php get_template_part('template-parts/cases/case-preview'); ?>
    </div>
    <?php get_template_part('template-parts/roi/roi'); ?>
</main>
<?php
get_footer();
