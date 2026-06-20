<section class="usegeo-case-preview-section">
  <div class="usegeo-section-heading">
    <h2>Featured GEO Case Studies</h2>
    <p class="usegeo-eyebrow">Real growth stories from the community</p>
  </div>

  <div class="usegeo-case-preview-grid">
    <?php
    $args = array(
      'post_type' => 'usegeo_case',
      'posts_per_page' => 3,
      'post_status' => 'publish',
      'meta_query' => array(
        array(
          'key' => 'review_status',
          'value' => 'approved',
          'compare' => '='
        )
      )
    );
    $cases_query = new WP_Query($args);
    if ($cases_query->have_posts()) :
      while ($cases_query->have_posts()) : $cases_query->the_post();
        $industry = get_post_meta(get_the_ID(), 'industry', true);
        $result = get_post_meta(get_the_ID(), 'results_seen', true);
        $is_anonymous = get_post_meta(get_the_ID(), 'allow_anonymous', true) === 'yes';
        $highlight_class = ($cases_query->current_post === 0) ? ' usegeo-case-preview-card--highlight' : '';
    ?>
    <article class="usegeo-case-preview-card<?php echo esc_attr($highlight_class); ?>"<?php if ($highlight_class): ?> data-highlight<?php endif; ?>>
      <span class="usegeo-case-preview-card__tag"><?php echo esc_html($industry ?: 'Cross-industry'); ?></span>
      <h3><?php the_title(); ?></h3>
      <p><?php echo esc_html(wp_trim_words(get_post_meta(get_the_ID(), 'core_problem', true), 25)); ?></p>
      <div class="usegeo-case-preview-card__meta">
        <span class="usegeo-case-preview-card__tag"><?php echo esc_html(wp_trim_words($result, 12)); ?></span>
        <span><?php echo esc_html($is_anonymous ? 'Anonymous contributor' : (get_post_meta(get_the_ID(), 'contributor_name', true) ?: 'Community member')); ?></span>
      </div>
    </article>
    <?php
      endwhile;
      wp_reset_postdata();
    else :
    ?>
    <div class="usegeo-content-empty">
      <p>The public case library is being built from reviewed submissions.</p>
      <p><a class="usegeo-inline-cta usegeo-inline-cta--ghost" href="<?php echo esc_url(home_url('/#community')); ?>">Submit a GEO case</a></p>
    </div>
    <?php endif; ?>
  </div>
</section>
