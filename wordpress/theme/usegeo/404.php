<?php

if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>
<main class="usegeo-shell usegeo-shell--narrow" style="text-align:center; padding:80px 0;">
    <div class="usegeo-card">
        <h1>Page not found</h1>
        <p>The page you are looking for does not exist or has been moved.</p>
        <div style="margin-top:24px;">
            <a class="usegeo-inline-cta usegeo-inline-cta--primary" href="<?php echo esc_url(home_url('/')); ?>">Return to homepage</a>
        </div>
        <div style="margin-top:16px;">
            <a href="<?php echo esc_url(home_url('/#scanner')); ?>">Run a GEO assessment</a>
            &nbsp;&middot;&nbsp;
            <a href="<?php echo esc_url(home_url('/articles/')); ?>">Read GEO articles</a>
        </div>
    </div>
</main>
<?php
get_footer();
