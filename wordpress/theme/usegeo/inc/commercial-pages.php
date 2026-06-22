<?php

if (!defined('ABSPATH')) {
    exit;
}

function usegeo_get_commercial_route() {
    $path = isset($_SERVER['REQUEST_URI']) ? wp_parse_url(sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI'])), PHP_URL_PATH) : '';
    $path = trim((string) $path, '/');
    $routes = array('ai-visibility-audit', 'sample-report', 'partners');

    return in_array($path, $routes, true) ? $path : '';
}

function usegeo_commercial_page_header($label, $title, $description, $primary_label = 'Run scanner', $secondary_label = 'View sample report', $secondary_url = '/sample-report/') {
    ?>
    <section class="usegeo-commercial-hero">
        <div class="usegeo-commercial-hero__copy">
            <div class="usegeo-commercial-kicker"><?php echo esc_html($label); ?></div>
            <h1><?php echo esc_html($title); ?></h1>
            <p><?php echo esc_html($description); ?></p>
            <div class="usegeo-commercial-actions">
                <a class="usegeo-inline-cta" href="<?php echo esc_url(home_url('/#scanner')); ?>" data-cta="commercial_scan"><?php echo esc_html($primary_label); ?></a>
                <a class="usegeo-commercial-text-link" href="<?php echo esc_url(home_url($secondary_url)); ?>" data-cta="commercial_secondary"><?php echo esc_html($secondary_label); ?></a>
            </div>
        </div>
        <aside class="usegeo-commercial-proof-card">
            <span><?php esc_html_e('No AI ranking promises', 'usegeo'); ?></span>
            <strong><?php esc_html_e('The scan is a directional diagnostic, not a promise of AI placement.', 'usegeo'); ?></strong>
            <p><?php esc_html_e('Partner follow-up only happens with explicit consent. You can use the report yourself first.', 'usegeo'); ?></p>
        </aside>
    </section>
    <?php
}

function usegeo_commercial_card($title, $body, $label = '') {
    ?>
    <article class="usegeo-commercial-card">
        <?php if ($label) : ?><span><?php echo esc_html($label); ?></span><?php endif; ?>
        <h3><?php echo esc_html($title); ?></h3>
        <p><?php echo esc_html($body); ?></p>
    </article>
    <?php
}

function usegeo_render_ai_visibility_audit_page() {
    get_header();
    ?>
    <main class="usegeo-shell usegeo-commercial-page usegeo-commercial-page--audit">
        <?php usegeo_commercial_page_header(
            'Free AI visibility audit',
            'Run a free GEO scan before you buy tools or hire help.',
            'UseGEO turns your public homepage into a practical readiness snapshot, then explains whether you should fix it yourself, compare tools, or consent to partner follow-up.'
        ); ?>

        <section class="usegeo-commercial-grid" aria-label="Audit path">
            <?php
            usegeo_commercial_card('Learn the gaps', 'Understand whether your page has clear entities, extractable content, proof signals, comparison context, and structured data.', '01');
            usegeo_commercial_card('Run the scanner', 'Get a directional score from your public page. The scanner records evidence that can become a manual brief.', '02');
            usegeo_commercial_card('Choose the next step', 'Use the sample report to decide whether to self-fix, evaluate tools, or request consent-based partner follow-up.', '03');
            ?>
        </section>

        <section class="usegeo-commercial-panel" aria-label="What the audit checks">
            <div>
                <span>What we check</span>
                <h2>Five dimensions that explain why AI systems may struggle with your site.</h2>
            </div>
            <ul class="usegeo-commercial-list">
                <li>Entity clarity</li>
                <li>Content structure</li>
                <li>Proof signals</li>
                <li>Comparison readiness</li>
                <li>AI extractability</li>
            </ul>
        </section>

        <section class="usegeo-commercial-panel usegeo-commercial-panel--dark" aria-label="Consent explanation">
            <div>
                <span>Consent protected</span>
                <h2>You control whether a partner sees your information.</h2>
                <p>UseGEO can help match high-intent leads with relevant GEO tools, consultants, or agencies. We only share contact details after explicit consent.</p>
            </div>
            <a class="usegeo-inline-cta" href="<?php echo esc_url(home_url('/sample-report/')); ?>">View sample report</a>
        </section>
    </main>
    <?php
    get_footer();
}

function usegeo_render_sample_report_page() {
    get_header();
    ?>
    <main class="usegeo-shell usegeo-commercial-page usegeo-commercial-page--sample">
        <?php usegeo_commercial_page_header(
            'Sample report',
            'See what a UseGEO scan can turn into.',
            'This sample report uses fictional data. It shows the type of evidence a reader, founder, or partner can review before deciding what to fix next.',
            'Run scanner',
            'Apply this to your site',
            '/ai-visibility-audit/'
        ); ?>

        <section class="usegeo-sample-report" aria-label="Sample report preview">
            <div class="usegeo-sample-report__score">
                <span>Sample score</span>
                <strong>72</strong>
                <p>Emerging readiness. Clear enough to start, but missing stronger proof and comparison signals.</p>
            </div>
            <div class="usegeo-sample-report__details">
                <h2>Example evidence</h2>
                <dl>
                    <dt>Page title</dt><dd>Clear category language, but weak differentiation.</dd>
                    <dt>H1</dt><dd>Readable, but does not state the buyer problem directly.</dd>
                    <dt>Schema</dt><dd>Organization and Article schema present. FAQ schema should mirror visible FAQ content.</dd>
                    <dt>Weakest dimension</dt><dd>Comparison readiness.</dd>
                </dl>
            </div>
        </section>

        <section class="usegeo-commercial-grid" aria-label="Sample recommendations">
            <?php
            usegeo_commercial_card('Top gap', 'The page does not explain when a buyer should choose you instead of a better-known alternative.', 'Evidence');
            usegeo_commercial_card('Fix first', 'Add a comparison section, proof examples, and a next-step CTA that maps to the buyer stage.', 'Action');
            usegeo_commercial_card('Partner handoff', 'If you consent, a partner can see the score, weakest dimension, business context, and your stated support intent.', 'Consent');
            ?>
        </section>

        <section class="usegeo-commercial-panel usegeo-commercial-panel--dark">
            <div>
                <span>No fake certainty</span>
                <h2>The report does not claim to track every AI answer.</h2>
                <p>It gives a practical starting point from public page signals and clearly separates evidence from assumptions.</p>
            </div>
            <a class="usegeo-inline-cta" href="<?php echo esc_url(home_url('/#scanner')); ?>">Run scanner</a>
        </section>
    </main>
    <?php
    get_footer();
}

function usegeo_render_partners_page() {
    get_header();
    ?>
    <main class="usegeo-shell usegeo-commercial-page usegeo-commercial-page--partners">
        <?php usegeo_commercial_page_header(
            'Partner and sponsor program',
            'Reach consented GEO leads with scanner evidence.',
            'UseGEO is building a learning and diagnostic surface for teams exploring AI visibility. Partners can help users act on the gaps, and sponsors can reach readers in relevant contexts.',
            'Apply as partner',
            'See sample report',
            '/sample-report/'
        ); ?>

        <section class="usegeo-commercial-panel" aria-label="Partner lead definition">
            <div>
                <span>Partner lead definition</span>
                <h2>A qualified partner lead is not just an email address.</h2>
            </div>
            <ul class="usegeo-commercial-list">
                <li>partner_consent = yes</li>
                <li>lead_quality = hot or warm</li>
                <li>business email</li>
                <li>scanner snapshot</li>
                <li>budget, region, support intent, and timeline when provided</li>
            </ul>
        </section>

        <section class="usegeo-commercial-grid" aria-label="Sponsor inventory">
            <?php
            usegeo_commercial_card('Homepage sponsor', 'Reach visitors before they run the scanner or choose a learning path.', 'Inventory');
            usegeo_commercial_card('Scanner result sponsor', 'Appear near the moment when a user is evaluating what to do next.', 'Inventory');
            usegeo_commercial_card('Article sidebar sponsor', 'Reach readers while they are learning a specific GEO tactic or use case.', 'Inventory');
            ?>
        </section>

        <section class="usegeo-commercial-panel usegeo-commercial-panel--dark">
            <div>
                <span>Beta terms</span>
                <h2>No traffic guarantee. No hidden reselling. No cold-list dump.</h2>
                <p>Early sponsor and lead referral packages should be manually reviewed, consent protected, and priced as beta inventory until traffic and reply data are real.</p>
            </div>
            <a class="usegeo-inline-cta" href="mailto:kevin.wang.hd@gmail.com?subject=UseGEO%20partner%20program">Apply as partner</a>
        </section>
    </main>
    <?php
    get_footer();
}

function usegeo_render_commercial_route() {
    $route = usegeo_get_commercial_route();

    if (!$route) {
        return;
    }

    status_header(200);
    nocache_headers();

    if ($route === 'ai-visibility-audit') {
        usegeo_render_ai_visibility_audit_page();
    } elseif ($route === 'sample-report') {
        usegeo_render_sample_report_page();
    } elseif ($route === 'partners') {
        usegeo_render_partners_page();
    }

    exit;
}
add_action('template_redirect', 'usegeo_render_commercial_route', 0);
