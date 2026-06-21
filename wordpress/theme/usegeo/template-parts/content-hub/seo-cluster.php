<?php
if (!defined('ABSPATH')) {
    exit;
}
$cards = array(
    array(
        'label' => 'GEO checklist',
        'title' => 'Generative Engine Optimization Checklist',
        'description' => 'A practical checklist for evaluating homepage clarity, proof signals, comparison readiness, and AI extractability.',
        'url' => home_url('/geo-checklist/'),
        'cta' => 'Open the checklist',
    ),
    array(
        'label' => 'Tools',
        'title' => 'AI Visibility Tools',
        'description' => 'A curated entry point for scanner workflows, AI visibility tools, and software that helps teams measure GEO readiness.',
        'url' => home_url('/ai-visibility-tools/'),
        'cta' => 'Explore tools',
    ),
    array(
        'label' => 'Partner directory',
        'title' => 'Best GEO Agencies',
        'description' => 'A future directory for vetted GEO, SEO, and AI visibility partners that can help teams act on scanner findings.',
        'url' => home_url('/best-geo-agencies/'),
        'cta' => 'View agency guide',
    ),
);
?>
<section class="usegeo-card usegeo-seo-cluster" aria-label="GEO learning paths">
    <div class="usegeo-section-heading usegeo-section-heading--center">
        <span class="usegeo-section-kicker">GEO learning paths</span>
        <h2>Start with the learning path that matches your next decision</h2>
        <p>UseGEO is built as a learning and discovery hub. These entry points connect educational content, scanner evidence, and partner discovery without promising that UseGEO will deliver the full service itself.</p>
    </div>
    <div class="usegeo-seo-cluster__grid">
        <?php foreach ($cards as $card) : ?>
            <article class="usegeo-seo-cluster-card">
                <span><?php echo esc_html($card['label']); ?></span>
                <h3><?php echo esc_html($card['title']); ?></h3>
                <p><?php echo esc_html($card['description']); ?></p>
                <a href="<?php echo esc_url($card['url']); ?>"><?php echo esc_html($card['cta']); ?></a>
            </article>
        <?php endforeach; ?>
    </div>
    <div class="usegeo-seo-cluster__cta">
        <p>Not sure where to start? Run the free scanner first and use the result to choose a learning path.</p>
        <a class="usegeo-inline-cta" href="<?php echo esc_url(home_url('/#scanner')); ?>">Run the free scanner</a>
    </div>
</section>
