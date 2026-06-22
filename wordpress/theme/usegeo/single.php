<?php

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('usegeo_single_strip_duplicate_h1')) {
    function usegeo_single_strip_duplicate_h1($content, $post_title) {
        $trimmed_title = trim(wp_strip_all_tags($post_title));

        return preg_replace_callback('/^\s*<h1([^>]*)>(.*?)<\/h1>/is', function ($matches) use ($trimmed_title) {
            $heading_text = trim(wp_strip_all_tags($matches[2]));

            if ($heading_text && strcasecmp($heading_text, $trimmed_title) === 0) {
                return '';
            }

            return $matches[0];
        }, $content, 1);
    }
}

if (!function_exists('usegeo_single_enhance_content_and_toc')) {
    function usegeo_single_enhance_content_and_toc($content, $post_title) {
        $content = usegeo_single_strip_duplicate_h1($content, $post_title);
        $toc_items = array();
        $seen_ids = array();
        $index = 0;

        $content = preg_replace_callback('/<h2([^>]*)>(.*?)<\/h2>/is', function ($matches) use (&$toc_items, &$seen_ids, &$index) {
            $attrs = $matches[1];
            $inner = $matches[2];
            $text = trim(wp_strip_all_tags($inner));

            if ($text === '') {
                return $matches[0];
            }

            if (preg_match('/\sid=["\']([^"\']+)["\']/', $attrs, $id_match)) {
                $id = sanitize_title($id_match[1]);
            } else {
                $base_id = sanitize_title($text);
                $id = $base_id ? $base_id : 'section-' . ($index + 1);
                $suffix = 2;

                while (isset($seen_ids[$id])) {
                    $id = $base_id . '-' . $suffix;
                    $suffix++;
                }

                $attrs .= ' id="' . esc_attr($id) . '"';
            }

            $seen_ids[$id] = true;
            $index++;

            if (mb_strlen($text) <= 90) {
                $toc_items[] = array(
                    'id' => $id,
                    'title' => $text,
                );
            }

            return '<h2' . $attrs . '>' . $inner . '</h2>';
        }, $content);

        return array($content, $toc_items);
    }
}

get_header();
?>
<main class="usegeo-single-shell">
    <?php
    if (have_posts()) {
        while (have_posts()) {
            the_post();

            $post_id = get_the_ID();
            $categories = get_the_category($post_id);
            $primary_category = !empty($categories) ? $categories[0] : null;
            $reading_time = usegeo_get_estimated_reading_time($post_id);
            $rendered_content = apply_filters('the_content', get_the_content());
            list($enhanced_content, $toc_items) = usegeo_single_enhance_content_and_toc($rendered_content, get_the_title());
            $excerpt = has_excerpt() ? get_the_excerpt() : wp_trim_words(wp_strip_all_tags($rendered_content), 24);
            ?>
            <article <?php post_class('usegeo-single'); ?>>
                <header class="usegeo-article-hero">
                    <div class="usegeo-article-hero__main">
                        <div class="usegeo-article-meta" aria-label="Article details">
                            <?php if ($primary_category) : ?>
                                <a href="<?php echo esc_url(get_category_link($primary_category)); ?>"><?php echo esc_html($primary_category->name); ?></a>
                            <?php else : ?>
                                <span>GEO strategy</span>
                            <?php endif; ?>
                            <span><?php echo esc_html($reading_time); ?> min read</span>
                            <time datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo esc_html(get_the_date('M j, Y')); ?></time>
                        </div>

                        <h1><?php the_title(); ?></h1>
                        <?php if ($excerpt) : ?>
                            <p class="usegeo-article-hero__excerpt"><?php echo esc_html($excerpt); ?></p>
                        <?php endif; ?>

                        <div class="usegeo-article-hero__actions">
                            <a class="usegeo-single-button" data-cta="article_hero_scanner" href="<?php echo esc_url(home_url('/#scanner')); ?>">Run scanner</a>
                            <a class="usegeo-single-text-link" data-cta="article_hero_checklist" href="<?php echo esc_url(home_url('/geo-checklist/')); ?>">Use the checklist</a>
                        </div>
                    </div>

                    <aside class="usegeo-article-hero__card" aria-label="Article next step">
                        <span>Best next step</span>
                        <strong>Read the playbook, then test your own site.</strong>
                        <p>The scanner turns the article into a short readiness snapshot for your homepage.</p>
                    </aside>
                </header>

                <div class="usegeo-article-frame">
                    <aside class="usegeo-reader-rail" aria-label="Reader tools">
                        <?php if (!empty($toc_items)) : ?>
                            <nav class="usegeo-reader-toc" aria-label="In this article">
                                <span>In this article</span>
                                <ol>
                                    <?php foreach ($toc_items as $toc_item) : ?>
                                        <li><a href="#<?php echo esc_attr($toc_item['id']); ?>"><?php echo esc_html($toc_item['title']); ?></a></li>
                                    <?php endforeach; ?>
                                </ol>
                            </nav>
                        <?php endif; ?>

                        <section class="usegeo-reader-cta">
                            <span>Apply it</span>
                            <h2>Check your page before you rewrite it</h2>
                            <p>Use the scanner to find the signal gaps this article talks about.</p>
                            <a class="usegeo-single-button" data-cta="article_sidebar_scanner" href="<?php echo esc_url(home_url('/#scanner')); ?>">Run scanner</a>
                        </section>
                        <?php if (function_exists('usegeo_render_sponsor_slot')) { usegeo_render_sponsor_slot('article_sidebar'); } ?>
                    </aside>

                    <div class="usegeo-article-content-card">
                        <div class="usegeo-article-content">
                            <?php echo $enhanced_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                        </div>
                    </div>
                </div>

                <section class="usegeo-article-action" aria-label="Article action">
                    <div>
                        <span>Turn reading into a decision</span>
                        <h2>See whether your own site has the same GEO gaps.</h2>
                        <p>Run a quick scan, then use the checklist and tool guide to decide what to fix first.</p>
                    </div>
                    <a class="usegeo-single-button" data-cta="article_final_scanner" href="<?php echo esc_url(home_url('/#scanner')); ?>">Run scanner</a>
                </section>

                <?php
                $related_args = array(
                    'post_type' => 'post',
                    'posts_per_page' => 3,
                    'post__not_in' => array($post_id),
                    'ignore_sticky_posts' => true,
                );

                if ($primary_category) {
                    $related_args['cat'] = $primary_category->term_id;
                }

                $related_posts = new WP_Query($related_args);

                if (!$related_posts->have_posts() && $primary_category) {
                    wp_reset_postdata();
                    unset($related_args['cat']);
                    $related_posts = new WP_Query($related_args);
                }
                ?>

                <section class="usegeo-foundation-strip" aria-label="GEO foundation guides">
                    <div class="usegeo-single-section-heading">
                        <h2>Use the core guides next</h2>
                        <p>Pick one practical path instead of opening ten more tabs.</p>
                    </div>
                    <div class="usegeo-foundation-strip__grid">
                        <a href="<?php echo esc_url(home_url('/geo-checklist/')); ?>">
                            <span>Checklist</span>
                            <strong>Generative Engine Optimization Checklist</strong>
                            <em>Audit clarity, proof, comparison context, and extractability.</em>
                        </a>
                        <a href="<?php echo esc_url(home_url('/ai-visibility-tools/')); ?>">
                            <span>Tools</span>
                            <strong>AI Visibility Tools</strong>
                            <em>Compare scanners, monitoring tools, and measurement limits.</em>
                        </a>
                        <a href="<?php echo esc_url(home_url('/best-geo-agencies/')); ?>">
                            <span>Expert help</span>
                            <strong>Best GEO Agencies</strong>
                            <em>Evaluate outside support without buying fake ranking promises.</em>
                        </a>
                    </div>
                </section>

                <?php if ($related_posts->have_posts()) : ?>
                    <section class="usegeo-single-related" aria-label="Recommended reading">
                        <div class="usegeo-single-section-heading">
                            <h2>Keep building GEO judgment</h2>
                        </div>
                        <div class="usegeo-single-related__grid">
                            <?php while ($related_posts->have_posts()) : $related_posts->the_post(); ?>
                                <article class="usegeo-single-related-card">
                                    <span><?php echo esc_html(usegeo_get_estimated_reading_time(get_the_ID())); ?> min read</span>
                                    <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                                    <p><?php echo esc_html(wp_trim_words(get_the_excerpt(), 18)); ?></p>
                                </article>
                            <?php endwhile; ?>
                        </div>
                    </section>
                    <?php wp_reset_postdata(); ?>
                <?php endif; ?>
            </article>
            <?php
        }
    }
    ?>
</main>
<?php
get_footer();
