<?php

if (!defined('ABSPATH')) {
    exit;
}
?>
<section id="sample-report" class="usegeo-card usegeo-sample-report-section">
    <div class="usegeo-section-heading usegeo-section-heading--center">
        <span class="usegeo-section-kicker">Sample report preview</span>
        <h2>What a full diagnostic turns into</h2>
        <p>The paid audit is not a generic SEO checklist. It turns your scanner result into a short, business-prioritized GEO action plan.</p>
    </div>

    <div class="usegeo-sample-report">
        <div class="usegeo-sample-report__score">
            <span>Example readiness score</span>
            <strong>68</strong>
            <p>Promising category fit, but weak comparison and proof signals.</p>
        </div>
        <div class="usegeo-sample-report__sections">
            <article>
                <span>Gap 1</span>
                <h3>Category and entity definitions are too vague</h3>
                <p>Homepage copy explains outcomes, but not enough explicit category, buyer, and use-case language.</p>
            </article>
            <article>
                <span>Gap 2</span>
                <h3>No comparison assets for AI buying questions</h3>
                <p>Models have little first-party context for “best alternative”, “for B2B teams”, or “vs competitor” prompts.</p>
            </article>
            <article>
                <span>Gap 3</span>
                <h3>Proof exists, but is not structured for reuse</h3>
                <p>Customer outcomes need clearer metrics, before/after context, and summary snippets.</p>
            </article>
        </div>
    </div>

    <div class="usegeo-sample-report__cta">
        <p><strong>Want this for your site?</strong> Run the free scanner first, then request the full diagnostic if the opportunity looks real.</p>
        <a class="usegeo-inline-cta" data-cta="sample_report_to_scanner" href="<?php echo esc_url(home_url('/#scanner')); ?>">Run free scanner</a>
    </div>
</section>
