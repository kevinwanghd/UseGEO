<?php

if (!defined('ABSPATH')) {
    exit;
}
?>
<section class="usegeo-community-section usegeo-card">
    <div class="usegeo-section-heading">
        <h2>Contribute GEO execution cases for the future library</h2>
        <p>
            The current priority is diagnostic and consultation conversion. Case submissions are still useful because they create the proof base
            for future playbooks, anonymized examples, and paid case-library products.
        </p>
    </div>

    <?php
    $case_status = isset($_GET['case_submission_status']) ? sanitize_key(wp_unslash($_GET['case_submission_status'])) : '';
    if ($case_status === 'success') {
        usegeo_render_flash_notice('success', 'Your case submission has been received for editorial review.');
    } elseif ($case_status === 'invalid') {
        usegeo_render_flash_notice('error', 'Please complete the required case fields before submitting.');
    } elseif ($case_status === 'failed') {
        usegeo_render_flash_notice('error', 'Case submission failed. Please try again.');
    }
    ?>

    <div class="usegeo-community-layout">
        <div class="usegeo-community-story">
            <div class="usegeo-community-story__card">
                <span class="usegeo-community-story__eyebrow">Community Direction</span>
                <h3>This is the intake layer for future proof assets, not a public forum.</h3>
                <p>
                    The first version of the community is designed to collect real GEO execution stories,
                    review them, structure them, and turn them into case assets worth learning from.
                </p>
            </div>

            <div class="usegeo-community-points">
                <article class="usegeo-community-point">
                    <strong>Submit a real execution context</strong>
                    <p>Share the company stage, business context, GEO actions taken, and the changes you observed.</p>
                </article>
                <article class="usegeo-community-point">
                    <strong>Reviewed before it is surfaced</strong>
                    <p>Raw submissions are not published directly. They are screened, structured, and anonymized where needed.</p>
                </article>
                <article class="usegeo-community-point">
                    <strong>Help later teams move faster</strong>
                    <p>The goal is to turn one-off execution experience into reusable decision support for other B2B teams.</p>
                </article>
            </div>
        </div>

        <aside class="usegeo-community-cta" aria-label="Case contribution form">
            <span class="usegeo-community-cta__label">Case Submission</span>
            <h3>Share your GEO execution case</h3>
            <p>Submit a structured GEO case for future editorial review and possible inclusion in the featured case library.</p>
            <form class="usegeo-community-form" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <input type="hidden" name="action" value="usegeo_submit_case">
                <input type="hidden" name="redirect_to" value="<?php echo esc_url(home_url('/#community')); ?>">
                <?php wp_nonce_field('usegeo_submit_case', 'usegeo_case_nonce'); ?>
                <label>
                    <span>Name or team name</span>
                    <input type="text" name="contributor_name" placeholder="Optional">
                </label>
                <label>
                    <span>Work email</span>
                    <input type="email" name="contact_email" placeholder="name@company.com" required>
                </label>
                <label>
                    <span>Company type</span>
                    <input type="text" name="company_type" placeholder="B2B SaaS / Agency / Media / Marketplace" required>
                </label>
                <label>
                    <span>Industry and business stage</span>
                    <div class="usegeo-community-form__split">
                        <input type="text" name="industry" placeholder="Industry">
                        <input type="text" name="business_stage" placeholder="Seed / Growth / Mature">
                    </div>
                </label>
                <label>
                    <span>Main GEO problem</span>
                    <textarea name="core_problem" rows="3" placeholder="What were you trying to improve?" required></textarea>
                </label>
                <label>
                    <span>What GEO actions did you take?</span>
                    <textarea name="geo_actions" rows="3" placeholder="What pages, content, or structures did you change?" required></textarea>
                </label>
                <label>
                    <span>What did you update?</span>
                    <textarea name="pages_updated" rows="2" placeholder="Pages, sections, or content types touched"></textarea>
                </label>
                <label>
                    <span>What results did you see?</span>
                    <textarea name="results_seen" rows="3" placeholder="Observed outcomes, signals, or changes" required></textarea>
                </label>
                <label>
                    <span>Most reusable lesson</span>
                    <textarea name="lessons_learned" rows="2" placeholder="Optional"></textarea>
                </label>
                <label class="usegeo-community-form__checkbox">
                    <input type="checkbox" name="allow_anonymous" value="yes">
                    <span>You may anonymize this case if it is turned into a public example.</span>
                </label>
                <button type="submit" class="usegeo-report-gate__button">Submit case</button>
            </form>
        </aside>
    </div>
</section>
