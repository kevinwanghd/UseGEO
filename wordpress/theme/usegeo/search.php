<?php

if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>
<main class="usegeo-shell usegeo-shell--narrow">
    <div class="usegeo-card">
        <h1><?php printf(esc_html__('Search results for: %s', 'usegeo'), get_search_query()); ?></h1>

        <?php if (have_posts()) : ?>
            <div class="usegeo-content-grid" style="margin-top:24px;">
                <?php while (have_posts()) : the_post(); ?>
                    <article class="usegeo-content-card">
                        <div class="usegeo-content-card__meta">
                            <span class="usegeo-content-card__tag"><?php echo esc_html(get_post_type()); ?></span>
                            <span class="usegeo-content-card__time"><?php echo esc_html(get_the_date()); ?></span>
                        </div>
                        <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                        <p><?php echo esc_html(usegeo_get_card_excerpt(get_the_ID(), 20)); ?></p>
                    </article>
                <?php endwhile; ?>
            </div>

            <div style="margin-top:24px; text-align:center;">
                <?php the_posts_pagination(array('mid_size' => 2)); ?>
            </div>
        <?php else : ?>
            <p style="margin-top:24px; color:var(--usegeo-color-muted);">
                No results found. Try a different search term or
                <a href="<?php echo esc_url(home_url('/')); ?>">return to the homepage</a>.
            </p>
            <?php get_search_form(); ?>
        <?php endif; ?>
    </div>
</main>
<?php
get_footer();
