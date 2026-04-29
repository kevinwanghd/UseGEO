<?php

if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>
<main class="usegeo-shell usegeo-article-layout">
    <div class="usegeo-article-main usegeo-card">
        <?php
        if (have_posts()) {
            while (have_posts()) {
                the_post();
                ?>
                <article <?php post_class('usegeo-article'); ?>>
                    <header class="usegeo-article__header">
                        <div class="usegeo-article__meta">
                            <span><?php echo esc_html(get_the_date()); ?></span>
                            <span><?php echo esc_html(get_the_author()); ?></span>
                            <span><?php echo esc_html(usegeo_get_estimated_reading_time(get_the_ID())); ?> min read</span>
                            <?php if (get_the_category()) : ?>
                                <span><?php echo esc_html(get_the_category()[0]->name); ?></span>
                            <?php endif; ?>
                        </div>
                        <h1><?php the_title(); ?></h1>
                        <?php if (has_excerpt()) : ?>
                            <p class="usegeo-article__excerpt"><?php echo esc_html(get_the_excerpt()); ?></p>
                        <?php endif; ?>
                    </header>
                    <div class="usegeo-article__content">
                        <?php the_content(); ?>
                    </div>
                </article>
                <?php
            }
        }
        ?>
    </div>

    <aside class="usegeo-article-sidebar">
        <?php get_template_part('template-parts/cta/sidebar-cta'); ?>
    </aside>
</main>
<?php
get_footer();
