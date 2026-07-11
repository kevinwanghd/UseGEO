<section class="usegeo-card usegeo-scanner-card">
    <div class="usegeo-section-heading usegeo-scanner-heading">
        <div>
            <span class="usegeo-section-kicker">Free GEO scanner</span>
            <h2>Find out whether AI systems can understand and recommend your brand.</h2>
            <p>Enter your website to get a quick AI visibility snapshot. You will see a directional score, model-readiness breakdown, and the most likely signal gaps before requesting a full diagnostic.</p>
        </div>
        <div class="usegeo-scanner-heading__proof" aria-label="Scanner proof points">
            <span>No login required</span>
            <span>2-minute snapshot</span>
            <span>Built for B2B sites</span>
        </div>
    </div>

    <?php
    $lead_status = isset($_GET['lead_status']) ? sanitize_key(wp_unslash($_GET['lead_status'])) : '';
    if ($lead_status === 'success') {
        usegeo_render_flash_notice('success', 'Your full assessment request has been received.');
    } elseif ($lead_status === 'success_no_mail') {
        usegeo_render_flash_notice('success', 'Your request has been saved, but the email notification did not send successfully.');
    } elseif ($lead_status === 'invalid') {
        usegeo_render_flash_notice('error', 'Please complete all required fields before unlocking the full assessment.');
    } elseif ($lead_status === 'failed') {
        usegeo_render_flash_notice('error', 'Submission failed. Please try again.');
    }
    ?>

    <div class="usegeo-tool" data-usegeo-scanner>
        <label class="screen-reader-text" for="usegeo-scanner-input">Company website</label>
        <form class="usegeo-tool__form" data-usegeo-scanner-form>
            <div class="usegeo-tool__row">
                <input id="usegeo-scanner-input" type="text" inputmode="url" autocomplete="url" name="query" pattern="https?://[\w.-]+.*|[\w.-]+\.[\w]{2,}" placeholder="Enter your company website, for example https://yourcompany.com" required>
                <button type="submit" data-cta="scanner_run_assessment">Run assessment</button>
            </div>
        </form>
        <div class="usegeo-tool__status" data-usegeo-scanner-status aria-live="polite">Enter a website to generate your free GEO readiness snapshot.</div>

        <div class="usegeo-scanner-empty" data-usegeo-scanner-empty>
            <div class="usegeo-scanner-empty__panel">
                <span>What you will get</span>
                <h3>A practical snapshot, not a vanity score.</h3>
                <p>The scanner estimates whether your site has the content, entity, comparison, and proof signals AI systems need before they can mention or recommend you.</p>
            </div>
            <div class="usegeo-scanner-empty__grid" aria-label="Scanner preview">
                <article>
                    <strong>01</strong>
                    <h4>AI visibility baseline</h4>
                    <p>Directional readiness score across ChatGPT, Claude, and Gemini-style answers.</p>
                </article>
                <article>
                    <strong>02</strong>
                    <h4>Signal gap map</h4>
                    <p>Content, authority, semantic, comparison, and proof signals ranked by likely impact.</p>
                </article>
                <article>
                    <strong>03</strong>
                    <h4>Next-step recommendation</h4>
                    <p>Whether to fix pages yourself, request a manual audit, or wait until GEO is more relevant.</p>
                </article>
            </div>
        </div>

        <div class="usegeo-scanner-loading" data-usegeo-scanner-loading hidden>
            <div class="usegeo-scanner-loading__pulse">
                <span class="usegeo-scanner-loading__label">Preparing assessment</span>
                <strong data-usegeo-loading-message>Checking homepage clarity and brand signal density...</strong>
            </div>
            <ol class="usegeo-scanner-steps" aria-label="Assessment progress">
                <li data-usegeo-stage="structure" class="is-active"><span>1</span><strong>Read site structure</strong><small>Homepage, category, and entity clarity</small></li>
                <li data-usegeo-stage="signals"><span>2</span><strong>Map AI signals</strong><small>Comparison, semantic, authority, and proof coverage</small></li>
                <li data-usegeo-stage="roadmap"><span>3</span><strong>Prepare next steps</strong><small>Priority gaps and diagnostic recommendation</small></li>
            </ol>
            <div class="usegeo-scanner-skeleton">
                <div class="usegeo-scanner-skeleton__card"></div>
                <div class="usegeo-scanner-skeleton__chart"></div>
                <div class="usegeo-scanner-skeleton__text"></div>
            </div>
        </div>

        <div class="usegeo-tool__error usegeo-scanner-error" data-usegeo-scanner-error hidden>
            <strong>We could not complete this scan.</strong>
            <p data-usegeo-scanner-error-message>Please try again in a moment.</p>
            <p class="usegeo-scanner-error__hint">You can still request a full diagnostic below if you want us to review the site manually.</p>
        </div>

        <div class="usegeo-tool__result usegeo-scanner-result" data-usegeo-scanner-result hidden>
            <div class="usegeo-scanner-result__header">
                <div class="usegeo-metric usegeo-metric--featured">
                    <span class="usegeo-metric__label">Current GEO readiness</span>
                    <strong data-usegeo-score>72</strong>
                    <small data-usegeo-grade>Emerging readiness</small>
                </div>

                <div class="usegeo-scanner-models" aria-label="Model overview">
                    <div class="usegeo-scanner-model">
                        <span>ChatGPT</span>
                        <strong data-usegeo-model-gpt>78</strong>
                    </div>
                    <div class="usegeo-scanner-model">
                        <span>Claude</span>
                        <strong data-usegeo-model-claude>69</strong>
                    </div>
                    <div class="usegeo-scanner-model">
                        <span>Gemini</span>
                        <strong data-usegeo-model-gemini>73</strong>
                    </div>
                </div>
            </div>

            <div class="usegeo-scanner-chart">
                <div class="usegeo-scanner-chart__inner">
                    <svg class="usegeo-radar" viewBox="0 0 200 200" aria-label="AI visibility radar chart">
                        <polygon class="usegeo-radar__bg" points="100,20 168,60 148,130 52,130 32,60" fill="none" stroke="#e5e7eb" stroke-width="1"/>
                        <polygon class="usegeo-radar__bg" points="100,40 148,68 136,115 64,115 52,68" fill="none" stroke="#e5e7eb" stroke-width="1"/>
                        <polygon class="usegeo-radar__bg" points="100,60 128,76 120,100 80,100 72,76" fill="none" stroke="#e5e7eb" stroke-width="1"/>
                        <line x1="100" y1="20" x2="100" y2="140" stroke="#e5e7eb" stroke-width="1"/>
                        <line x1="32" y1="60" x2="168" y2="60" stroke="#e5e7eb" stroke-width="1"/>
                        <line x1="52" y1="130" x2="148" y2="130" stroke="#e5e7eb" stroke-width="1"/>
                        <polygon class="usegeo-radar__data" data-usegeo-radar points="100,35 154,64 138,118 62,118 46,64" fill="rgba(15,98,254,0.15)" stroke="#0f62fe" stroke-width="2"/>
                    </svg>
                    <div class="usegeo-radar-labels">
                        <span class="usegeo-radar-label usegeo-radar-label--top"><strong data-usegeo-radar-val-1>72</strong> Content signals</span>
                        <span class="usegeo-radar-label usegeo-radar-label--top-right"><strong data-usegeo-radar-val-2>68</strong> Brand mention fit</span>
                        <span class="usegeo-radar-label usegeo-radar-label--bottom-right"><strong data-usegeo-radar-val-3>61</strong> Competitive clarity</span>
                        <span class="usegeo-radar-label usegeo-radar-label--bottom-left"><strong data-usegeo-radar-val-4>64</strong> Semantic coverage</span>
                        <span class="usegeo-radar-label usegeo-radar-label--top-left"><strong data-usegeo-radar-val-5>57</strong> Authority depth</span>
                    </div>
                </div>
            </div>

            <div class="usegeo-scanner-insights">
                <p class="usegeo-scanner-summary" data-usegeo-summary>
                    Your website shows emerging GEO readiness, but stronger solution clarity, comparison coverage, and authority signals would likely improve AI visibility.
                </p>
                <ul class="usegeo-scanner-findings" data-usegeo-findings>
                    <li>Your site has some foundational discoverability, but not yet enough structured signal for strong recommendation consistency.</li>
                    <li>Comparison, solution, and proof content appear to be the highest-leverage gaps to close next.</li>
                    <li>The best next step is to prioritize execution moves instead of adding more unfocused content volume.</li>
                </ul>
            </div>

            <div class="usegeo-scanner-evidence" data-usegeo-evidence hidden>
                <span class="usegeo-scanner-evidence__label">What we found on your site</span>
                <dl>
                    <dt>Page title</dt><dd>Not scanned yet</dd>
                    <dt>H1</dt><dd>Not scanned yet</dd>
                    <dt>Schema detected</dt><dd>Not scanned yet</dd>
                    <dt>Word count</dt><dd>0</dd>
                    <dt>Proof signals</dt><dd>Not scanned yet</dd>
                    <dt>Comparison signals</dt><dd>Not scanned yet</dd>
                </dl>
            </div>

            <?php if (function_exists('usegeo_render_sponsor_slot')) { usegeo_render_sponsor_slot('scanner_result'); } ?>

            <div class="usegeo-report-gate" data-usegeo-report-gate>
                <div class="usegeo-report-gate__copy">
                    <span class="usegeo-report-gate__kicker">Your next move</span>
                    <h3>Turn this snapshot into a focused 30-day action plan.</h3>
                    <p class="usegeo-report-gate__intro">Get the free result by email, or unlock the full implementation report instantly. No sales call and no subscription.</p>
                    <ul>
                        <li>Every evidence-backed finding and recommendation</li>
                        <li>Your three lowest-scoring priorities in order</li>
                        <li>A printable report your team can execute</li>
                    </ul>
                </div>
                <form class="usegeo-report-gate__form" data-usegeo-report-form>
                    <div class="usegeo-report-gate__form-head">
                        <span>Homepage GEO Action Report</span>
                        <strong>$29</strong>
                        <small>One website · instant delivery</small>
                    </div>
                    <label>
                        <span>Where should we send your result?</span>
                        <input type="email" name="email" autocomplete="email" placeholder="name@company.com" required>
                    </label>
                    <div class="usegeo-report-gate__actions">
                        <button type="submit" name="report_action" value="email" class="usegeo-report-gate__secondary" data-cta="email_free_snapshot">Email free snapshot</button>
                        <button type="submit" name="report_action" value="checkout" class="usegeo-report-gate__button" data-cta="unlock_report">Unlock full report · $29</button>
                    </div>
                    <p class="usegeo-report-gate__status" data-usegeo-report-status aria-live="polite"></p>
                    <p class="usegeo-report-gate__disclosure">Secure checkout by Stripe. Your information is used only to deliver this report. See our <a href="<?php echo esc_url(home_url('/privacy-policy')); ?>">Privacy Policy</a>.</p>
                </form>
            </div>
        </div>
    </div>
</section>
