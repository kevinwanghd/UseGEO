<section class="usegeo-card">
    <div class="usegeo-section-heading">
        <h2>Start with the content that builds GEO judgment</h2>
        <p>These articles are designed to help B2B teams understand AI visibility, GEO structure, and which execution moves matter most.</p>
    </div>

    <div class="usegeo-content-intro">
        <span>Good for GEO beginners</span>
        <span>Useful for SEO and content teams adapting to AI search</span>
        <span>Built for growth leaders evaluating business impact</span>
    </div>

    <?php
    $featured_posts = new WP_Query(array(
        'post_type'           => 'post',
        'post_status'         => 'publish',
        'posts_per_page'      => 6,
        'ignore_sticky_posts' => true,
    ));
    ?>

    <?php if ($featured_posts->have_posts()) : ?>
        <div class="usegeo-content-grid">
            <?php
            while ($featured_posts->have_posts()) :
                $featured_posts->the_post();
                $categories = get_the_category();
                $tag_label = !empty($categories) ? $categories[0]->name : 'GEO Article';
                $reading_time = usegeo_get_estimated_reading_time(get_the_ID());
                ?>
                <article class="usegeo-content-card">
                    <div class="usegeo-content-card__meta">
                        <span class="usegeo-content-card__tag"><?php echo esc_html($tag_label); ?></span>
                        <span class="usegeo-content-card__time"><?php echo esc_html($reading_time); ?> min read</span>
                    </div>
                    <h3><?php the_title(); ?></h3>
                    <p><?php echo esc_html(usegeo_get_card_excerpt(get_the_ID(), 16)); ?></p>
                    <a href="<?php the_permalink(); ?>">Read article</a>
                </article>
            <?php endwhile; ?>
        </div>
        <?php wp_reset_postdata(); ?>
    <?php else : ?>
        <div class="usegeo-content-empty">
            <p>No published GEO articles yet. Add foundational content before using the homepage as a public acquisition entry point.</p>
        </div>
    <?php endif; ?>
</section>
