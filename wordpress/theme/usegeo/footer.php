<?php

if (!defined('ABSPATH')) {
    exit;
}
?>
<footer class="usegeo-site-footer">
    <div class="usegeo-shell">
        <div class="usegeo-site-footer__grid">
            <div class="usegeo-site-footer__brand">
                <strong><?php esc_html_e('usegeo.blog', 'usegeo'); ?></strong>
                <p><?php esc_html_e('A GEO learning, assessment, and case-driven insight site for B2B teams.', 'usegeo'); ?></p>
            </div>
            <nav class="usegeo-site-footer__nav" aria-label="Footer navigation">
                <ul class="usegeo-menu usegeo-menu--footer">
                    <li><a href="<?php echo esc_url(home_url('/about')); ?>">About</a></li>
                    <li><a href="<?php echo esc_url(home_url('/privacy-policy')); ?>">Privacy Policy</a></li>
                    <li><a href="<?php echo esc_url(home_url('/cookie-policy')); ?>">Cookie Policy</a></li>
                </ul>
            </nav>
        </div>
    </div>
</footer>
</div>
<?php wp_footer(); ?>
</body>
</html>
