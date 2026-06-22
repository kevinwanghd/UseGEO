<?php

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('usegeo_page_strip_first_h1')) {
    function usegeo_page_strip_first_h1($content, $page_title) {
        unset($page_title);

        return preg_replace('/^\s*<h1[^>]*>.*?<\/h1>/is', '', $content, 1);
    }
}

get_header();
?>
<main class="usegeo-shell usegeo-public-page">
    <?php
    if (have_posts()) {
        while (have_posts()) {
            the_post();

            $page_id = get_the_ID();
            $rendered_content = apply_filters('the_content', get_the_content());
            $content = usegeo_page_strip_first_h1($rendered_content, get_the_title());
            $excerpt = has_excerpt() ? get_the_excerpt() : wp_trim_words(wp_strip_all_tags($rendered_content), 26);
            ?>
            <article <?php post_class('usegeo-public-page__article'); ?>>
                <header class="usegeo-public-page-hero">
                    <div class="usegeo-public-page-hero__copy">
                        <div class="usegeo-public-page-meta" aria-label="Page details">
                            <span><?php esc_html_e('UseGEO guide', 'usegeo'); ?></span>
                            <span><?php echo esc_html(get_the_modified_date('M j, Y', $page_id)); ?></span>
                        </div>
                        <h1><?php the_title(); ?></h1>
                        <?php if ($excerpt) : ?>
                            <p><?php echo esc_html($excerpt); ?></p>
                        <?php endif; ?>
                        <div class="usegeo-public-page-hero__actions">
                            <a class="usegeo-inline-cta" data-cta="page_hero_scanner" href="<?php echo esc_url(home_url('/#scanner')); ?>">Run scanner</a>
                            <a class="usegeo-public-page-text-link" data-cta="page_hero_articles" href="<?php echo esc_url(home_url('/articles/')); ?>">Browse articles</a>
                        </div>
                    </div>
                    <aside class="usegeo-public-page-hero__panel" aria-label="UseGEO positioning">
                        <span><?php esc_html_e('Learning first', 'usegeo'); ?></span>
                        <strong><?php esc_html_e('Use the guide, then test your own site.', 'usegeo'); ?></strong>
                        <p><?php esc_html_e('UseGEO helps you learn the signals, run a lightweight scanner, and decide whether expert follow-up is worth it.', 'usegeo'); ?></p>
                    </aside>
                </header>

                <div class="usegeo-public-page-content">
                    <?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                </div>

                <section class="usegeo-public-page-action" aria-label="Page action">
                    <div>
                        <span><?php esc_html_e('Next step', 'usegeo'); ?></span>
                        <h2><?php esc_html_e('Turn this page into a quick website check.', 'usegeo'); ?></h2>
                        <p><?php esc_html_e('Run the scanner to see whether your own pages have the clarity, proof, and comparison signals this guide describes.', 'usegeo'); ?></p>
                    </div>
                    <a class="usegeo-inline-cta" data-cta="page_final_scanner" href="<?php echo esc_url(home_url('/#scanner')); ?>">Run scanner</a>
                </section>

                <section class="usegeo-public-page-foundation" aria-label="Core UseGEO guides">
                    <div class="usegeo-section-heading">
                        <h2><?php esc_html_e('Core guides', 'usegeo'); ?></h2>
                        <p><?php esc_html_e('Use these pages as the stable path through GEO basics, tooling, and partner evaluation.', 'usegeo'); ?></p>
                    </div>
                    <div class="usegeo-public-page-foundation__grid">
                        <a href="<?php echo esc_url(home_url('/geo-checklist/')); ?>">
                            <span><?php esc_html_e('Checklist', 'usegeo'); ?></span>
                            <strong><?php esc_html_e('Generative Engine Optimization Checklist', 'usegeo'); ?></strong>
                            <em><?php esc_html_e('Audit clarity, proof, comparison context, and extractability.', 'usegeo'); ?></em>
                        </a>
                        <a href="<?php echo esc_url(home_url('/ai-visibility-tools/')); ?>">
                            <span><?php esc_html_e('Tools', 'usegeo'); ?></span>
                            <strong><?php esc_html_e('AI Visibility Tools', 'usegeo'); ?></strong>
                            <em><?php esc_html_e('Compare scanners, monitoring tools, and measurement limits.', 'usegeo'); ?></em>
                        </a>
                        <a href="<?php echo esc_url(home_url('/best-geo-agencies/')); ?>">
                            <span><?php esc_html_e('Expert help', 'usegeo'); ?></span>
                            <strong><?php esc_html_e('Best GEO Agencies', 'usegeo'); ?></strong>
                            <em><?php esc_html_e('Evaluate outside support without buying fake ranking promises.', 'usegeo'); ?></em>
                        </a>
                    </div>
                </section>
            </article>
            <?php
        }
    }
    ?>
</main>
<?php
get_footer();
