<section class="usegeo-card">
    <div class="usegeo-section-heading">
        <h2>Run a quick GEO assessment on your website</h2>
        <p>Start with a lightweight read on your current AI visibility. Then unlock a fuller assessment with more specific next-step guidance.</p>
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
                <input id="usegeo-scanner-input" type="text" name="query" placeholder="Enter your company website, for example https://yourcompany.com">
                <button type="submit">Run assessment</button>
            </div>
        </form>
        <div class="usegeo-tool__status" data-usegeo-scanner-status aria-live="polite"></div>

        <div class="usegeo-scanner-loading" data-usegeo-scanner-loading hidden>
            <div class="usegeo-scanner-loading__pulse">
                <span class="usegeo-scanner-loading__label">Preparing assessment</span>
                <strong data-usegeo-loading-message>Checking the website structure and GEO signal pattern...</strong>
            </div>
            <div class="usegeo-scanner-skeleton">
                <div class="usegeo-scanner-skeleton__card"></div>
                <div class="usegeo-scanner-skeleton__chart"></div>
                <div class="usegeo-scanner-skeleton__text"></div>
            </div>
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
                        <circle cx="100" cy="35" r="4" fill="#0f62fe"/>
                        <circle cx="154" cy="64" r="4" fill="#0f62fe"/>
                        <circle cx="138" cy="118" r="4" fill="#0f62fe"/>
                        <circle cx="62" cy="118" r="4" fill="#0f62fe"/>
                        <circle cx="46" cy="64" r="4" fill="#0f62fe"/>
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

            <div class="usegeo-report-gate">
                <div class="usegeo-report-gate__copy">
                    <h3>Unlock your full GEO assessment</h3>
                    <ul>
                        <li>See your top 3 priority GEO gaps</li>
                        <li>Get clearer next-step recommendations</li>
                        <li>Understand what to do next and what not to do yet</li>
                    </ul>
                </div>
                <form class="usegeo-report-gate__form" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                    <input type="hidden" name="action" value="usegeo_capture_lead">
                    <input type="hidden" name="redirect_to" value="<?php echo esc_url(home_url('/#scanner')); ?>">
                    <input type="hidden" name="scanner_query" value="" data-usegeo-scanner-query>
                    <?php wp_nonce_field('usegeo_capture_lead', 'usegeo_lead_nonce'); ?>
                    <label>
                        <span>Work email</span>
                        <input type="email" name="work_email" placeholder="name@company.com" required>
                    </label>
                    <label>
                        <span>Company type</span>
                        <select name="company_type" required>
                            <option value="">Select one</option>
                            <option value="b2b_saas">B2B SaaS</option>
                            <option value="agency_consultancy">Agency / Consultancy</option>
                            <option value="marketplace">Marketplace</option>
                            <option value="media_content">Media / Content Business</option>
                            <option value="ecommerce">Ecommerce</option>
                            <option value="other">Other</option>
                        </select>
                    </label>
                    <label>
                        <span>What kind of help are you looking for?</span>
                        <select name="help_intent" required>
                            <option value="">Select one</option>
                            <option value="exploring_geo">Just exploring GEO</option>
                            <option value="prioritized_next_steps">Want prioritized next steps</option>
                            <option value="open_to_specialist">Open to working with a GEO specialist</option>
                        </select>
                    </label>
                    <label>
                        <span>If you need outside help, what level of support would be realistic?</span>
                        <select name="support_level" required>
                            <option value="">Select one</option>
                            <option value="not_hiring">Not planning to hire anyone</option>
                            <option value="small_project">Open to a small project</option>
                            <option value="ongoing_support">Open to ongoing support</option>
                            <option value="not_sure">Not sure yet</option>
                        </select>
                    </label>
                    <label>
                        <span>When would you realistically act on this?</span>
                        <select name="timeline" required>
                            <option value="">Select one</option>
                            <option value="just_researching">Just researching</option>
                            <option value="next_quarter">Planning in the next quarter</option>
                            <option value="need_help_soon">Need help soon</option>
                            <option value="not_sure_yet">Not sure yet</option>
                        </select>
                    </label>
                    <button type="submit" class="usegeo-report-gate__button">Get my full assessment</button>
                    <p class="usegeo-report-gate__disclosure">
                        We use your submission to personalize the assessment and follow up if needed. If you ask for outside help or request a recommendation, we may use your details to help identify a relevant GEO specialist. See our
                        <a href="<?php echo esc_url(home_url('/privacy-policy')); ?>">Privacy Policy</a>.
                    </p>
                </form>
            </div>
        </div>
    </div>
</section>
