<?php

if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>
<main class="usegeo-shell usegeo-shell--narrow">
    <section class="usegeo-card">
        <h1><?php esc_html_e('UseGEO', 'usegeo'); ?></h1>
        <p><?php esc_html_e('Theme fallback template. Use front-page.php for the landing page and single.php for article detail pages.', 'usegeo'); ?></p>
    </section>
</main>
<?php
get_footer();
