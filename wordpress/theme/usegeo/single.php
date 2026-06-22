<?php

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('usegeo_single_enhance_content_and_toc')) {
    function usegeo_single_enhance_content_and_toc($content) {
        $toc_items = array();
        $seen_ids = array();
        $index = 0;

        $content = preg_replace_callback('/<h([2-3])([^>]*)>(.*?)<\/h\1>/is', function ($matches) use (&$toc_items, &$seen_ids, &$index) {
            $level = (int) $matches[1];
            $attrs = $matches[2];
            $inner = $matches[3];
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

            $toc_items[] = array(
                'id' => $id,
                'level' => $level,
                'title' => $text,
            );

            return '<h' . $level . $attrs . '>' . $inner . '</h' . $level . '>';
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
            list($enhanced_content, $toc_items) = usegeo_single_enhance_content_and_toc($rendered_content);
            $excerpt = has_excerpt() ? get_the_excerpt() : wp_trim_words(wp_strip_all_tags($rendered_content), 28);
            ?>
            <article <?php post_class('usegeo-single'); ?>>
                <header class="usegeo-single-hero">
                    <div class="usegeo-single-hero__eyebrow">
                        <?php if ($primary_category) : ?>
                            <a href="<?php echo esc_url(get_category_link($primary_category)); ?>"><?php echo esc_html($primary_category->name); ?></a>
                        <?php else : ?>
                            <span>GEO Strategy</span>
                        <?php endif; ?>
                        <span><?php echo esc_html($reading_time); ?> min read</span>
                        <time datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo esc_html(get_the_date('M j, Y')); ?></time>
                    </div>

                    <div class="usegeo-single-hero__grid">
                        <div class="usegeo-single-hero__copy">
                            <p class="usegeo-single-hero__kicker">GEO Learning Library</p>
                            <h1><?php the_title(); ?></h1>
                            <?php if ($excerpt) : ?>
                                <p class="usegeo-single-hero__excerpt"><?php echo esc_html($excerpt); ?></p>
                            <?php endif; ?>
                            <div class="usegeo-single-hero__actions">
                                <a class="usegeo-single-button" data-cta="article_hero_scanner" href="<?php echo esc_url(home_url('/#scanner')); ?>">Run the free assessment</a>
                                <a class="usegeo-single-button usegeo-single-button--ghost" data-cta="article_hero_manual_audit" href="<?php echo esc_url(home_url('/#services')); ?>">See audit options</a>
                            </div>
                        </div>

                        <aside class="usegeo-single-hero__panel" aria-label="Article summary">
                            <span>Recommended next step</span>
                            <strong>Check whether your site has the signals AI systems need to cite you.</strong>
                            <ul>
                                <li>Assess baseline AI visibility</li>
                                <li>Find missing content signals</li>
                                <li>Decide whether a manual audit is worth it</li>
                            </ul>
                        </aside>
                    </div>
                </header>

                <div class="usegeo-single-layout">
                    <aside class="usegeo-single-sidebar" aria-label="Article tools">
                        <?php if (!empty($toc_items)) : ?>
                            <nav class="usegeo-single-toc" aria-label="Table of contents">
                                <span>In this article</span>
                                <ol>
                                    <?php foreach ($toc_items as $toc_item) : ?>
                                        <li class="usegeo-single-toc__item usegeo-single-toc__item--h<?php echo esc_attr($toc_item['level']); ?>">
                                            <a href="#<?php echo esc_attr($toc_item['id']); ?>"><?php echo esc_html($toc_item['title']); ?></a>
                                        </li>
                                    <?php endforeach; ?>
                                </ol>
                            </nav>
                        <?php endif; ?>

                        <section class="usegeo-single-side-cta">
                            <span>Conversion checkpoint</span>
                            <h2>Apply this article to your own site</h2>
                            <p>Run the scanner first. If your baseline shows commercial opportunity, request the full diagnostic and use it as a manual audit brief.</p>
                            <a class="usegeo-single-button" data-cta="article_sidebar_scanner" href="<?php echo esc_url(home_url('/#scanner')); ?>">Start assessment</a>
                            <a class="usegeo-single-text-link" data-cta="article_sidebar_sample_report" href="<?php echo esc_url(home_url('/#sample-report')); ?>">Preview the diagnostic →</a>
                        </section>
                        <?php if (function_exists('usegeo_render_sponsor_slot')) { usegeo_render_sponsor_slot('article_sidebar'); } ?>
                    </aside>

                    <div class="usegeo-single-content-card">
                        <div class="usegeo-single-content">
                            <?php echo $enhanced_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                        </div>
                    </div>
                </div>

                <section class="usegeo-single-mid-cta">
                    <span>Before you keep reading</span>
                    <h2>Want to know if this problem exists on your site?</h2>
                    <p>Use the scanner to get a baseline, then unlock the full diagnostic if you want prioritized GEO fixes.</p>
                    <a class="usegeo-single-button" data-cta="article_mid_scanner" href="<?php echo esc_url(home_url('/#scanner')); ?>">Run the scanner</a>
                </section>

                <section class="usegeo-single-bottom-cta">
                    <div>
                        <span>Ready to apply this?</span>
                        <h2>Turn GEO reading into an execution priority.</h2>
                        <p>Run the free assessment to see where your website is clear, where AI systems may struggle, and which gaps are worth closing first.</p>
                    </div>
                    <a class="usegeo-single-button" data-cta="article_bottom_scanner" href="<?php echo esc_url(home_url('/#scanner')); ?>">Get your readiness score</a>
                </section>

                <nav class="usegeo-single-pagination" aria-label="Article navigation">
                    <div class="usegeo-single-pagination__item">
                        <span>Previous</span>
                        <?php previous_post_link('%link', '%title'); ?>
                    </div>
                    <div class="usegeo-single-pagination__item usegeo-single-pagination__item--next">
                        <span>Next</span>
                        <?php next_post_link('%link', '%title'); ?>
                    </div>
                </nav>

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

                <section class="usegeo-single-foundation-links" aria-label="GEO foundation guides">
                    <div class="usegeo-single-section-heading">
                        <span>GEO foundation</span>
                        <h2>Use these guides to turn this article into a practical next step</h2>
                    </div>
                    <div class="usegeo-single-foundation-links__grid">
                        <a href="<?php echo esc_url(home_url('/geo-checklist/')); ?>">
                            <span>Checklist</span>
                            <strong>Generative Engine Optimization Checklist</strong>
                            <em>Inspect entity clarity, proof, comparison readiness, and extractability.</em>
                        </a>
                        <a href="<?php echo esc_url(home_url('/ai-visibility-tools/')); ?>">
                            <span>Tools</span>
                            <strong>AI Visibility Tools</strong>
                            <em>Compare scanners, monitoring tools, and measurement limits before buying.</em>
                        </a>
                        <a href="<?php echo esc_url(home_url('/best-geo-agencies/')); ?>">
                            <span>Partner help</span>
                            <strong>Best GEO Agencies</strong>
                            <em>Evaluate expert support without falling for fake ranking promises.</em>
                        </a>
                    </div>
                </section>

                <?php if ($related_posts->have_posts()) : ?>
                    <section class="usegeo-single-related" aria-label="Recommended reading">
                        <div class="usegeo-single-section-heading">
                            <span>Recommended reading</span>
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
