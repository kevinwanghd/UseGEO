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
            <strong><?php esc_html_e('A directional diagnostic, not a promise of AI placement.', 'usegeo'); ?></strong>
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

function usegeo_render_audit_input_mockup() {
    ?>
    <section class="usegeo-audit-input" aria-label="Audit input preview">
        <div class="usegeo-audit-input__form" role="group" aria-label="Website scan input preview">
            <label for="usegeo-audit-url-preview"><?php esc_html_e('Website URL', 'usegeo'); ?></label>
            <div>
                <input id="usegeo-audit-url-preview" type="text" value="https://example-b2b-site.com" readonly>
                <a class="usegeo-inline-cta" href="<?php echo esc_url(home_url('/#scanner')); ?>">Run scanner</a>
            </div>
            <p><?php esc_html_e('Use the live scanner on the homepage. This field previews the flow without submitting data here.', 'usegeo'); ?></p>
        </div>
        <div class="usegeo-audit-preview" aria-label="Readiness preview">
            <span>Readiness preview</span>
            <strong>72</strong>
            <p>Emerging readiness with weak comparison context and limited proof signals.</p>
            <ul>
                <li>Entity clarity: good</li>
                <li>Proof signals: needs work</li>
                <li>Comparison readiness: weakest</li>
            </ul>
        </div>
    </section>
    <?php
}

function usegeo_render_report_mockup() {
    $dimensions = array(
        'Entity clarity' => 'Clear category and company language',
        'Content structure' => 'Readable sections, but weak prioritization',
        'Proof signals' => 'Few concrete examples or third-party signals',
        'Comparison readiness' => 'Missing buyer comparison context',
        'AI extractability' => 'Good headings, limited quotable passages',
    );
    ?>
    <section class="usegeo-report-mockup" aria-label="Sample report deliverable">
        <div class="usegeo-report-mockup__summary">
            <span>Sample report</span>
            <h2>What the scanner turns into</h2>
            <p>This is a fictional sample, designed to show the structure of the evidence and next-step recommendations.</p>
        </div>
        <div class="usegeo-report-mockup__score">
            <span>Sample score</span>
            <strong>72</strong>
            <p>Emerging readiness</p>
        </div>
        <div class="usegeo-report-mockup__dimensions">
            <h3>Dimensions</h3>
            <?php foreach ($dimensions as $name => $note) : ?>
                <div class="usegeo-report-row"><strong><?php echo esc_html($name); ?></strong><span><?php echo esc_html($note); ?></span></div>
            <?php endforeach; ?>
        </div>
        <div class="usegeo-report-mockup__evidence">
            <h3>Evidence table</h3>
            <dl>
                <dt>Page title</dt><dd>Clear topic, weak differentiation.</dd>
                <dt>Schema</dt><dd>Organization and Article schema present. FAQ schema should mirror visible FAQ content.</dd>
                <dt>Top gap</dt><dd>The page does not explain when a buyer should choose this company over alternatives.</dd>
                <dt>Recommended action</dt><dd>Add comparison context, proof examples, and a next-step CTA tied to buyer intent.</dd>
            </dl>
        </div>
        <div class="usegeo-report-mockup__handoff">
            <h3>Partner handoff</h3>
            <p>If the visitor gives explicit consent, a partner sees the score, weakest dimension, business context, and requested support level.</p>
        </div>
    </section>
    <?php
}

function usegeo_render_partner_offer_blocks() {
    ?>
    <section class="usegeo-lead-object" aria-label="Partner lead object">
        <div>
            <span>Lead object</span>
            <h2>A partner lead should arrive with context, not just contact data.</h2>
        </div>
        <ul class="usegeo-commercial-list">
            <li>partner_consent = yes</li>
            <li>lead_quality = hot or warm</li>
            <li>business email</li>
            <li>scanner snapshot</li>
            <li>budget, region, support intent, and timeline when provided</li>
        </ul>
    </section>
    <section class="usegeo-partner-packages" aria-label="Partner beta packages">
        <article>
            <span>Beta package</span>
            <h3>Sponsor beta</h3>
            <p>Test homepage, scanner result, or article sidebar inventory while traffic and reply data are still early.</p>
            <strong>Best for tools and agencies validating demand.</strong>
        </article>
        <article>
            <span>Beta package</span>
            <h3>Lead referral beta</h3>
            <p>Receive manually reviewed consented leads with scanner evidence and business context.</p>
            <strong>Best for GEO consultants and specialist agencies.</strong>
        </article>
    </section>
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
        <?php usegeo_render_audit_input_mockup(); ?>
        <section class="usegeo-commercial-grid" aria-label="Audit path">
            <?php
            usegeo_commercial_card('Scan', 'Check whether your page is clear enough for AI systems and human buyers to understand.', 'Step');
            usegeo_commercial_card('Report', 'Review a sample-style snapshot with evidence, gaps, and practical next steps.', 'Step');
            usegeo_commercial_card('Match', 'If the gap is worth acting on, give consent for relevant partner follow-up.', 'Step');
            ?>
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
        <?php usegeo_render_report_mockup(); ?>
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
        <?php usegeo_render_partner_offer_blocks(); ?>
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
