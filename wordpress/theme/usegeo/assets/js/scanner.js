document.addEventListener("DOMContentLoaded", function () {
  const container = document.querySelector("[data-usegeo-scanner]");
  if (!container) return;

  const config = window.usegeoScannerConfig || {};
  const realScannerEnabled = config.realScannerEnabled !== false;
  const apiUrl = config.apiUrl || "/wp-json/usegeo/v1/scan";
  const nonce = config.nonce || "";

  const form = container.querySelector("[data-usegeo-scanner-form]");
  const status = container.querySelector("[data-usegeo-scanner-status]");
  const emptyState = container.querySelector("[data-usegeo-scanner-empty]");
  const stages = Array.from(container.querySelectorAll("[data-usegeo-stage]"));
  const loading = container.querySelector("[data-usegeo-scanner-loading]");
  const loadingMessage = container.querySelector("[data-usegeo-loading-message]");
  const result = container.querySelector("[data-usegeo-scanner-result]");
  const errorBox = container.querySelector("[data-usegeo-scanner-error]");
  const errorMessage = container.querySelector("[data-usegeo-scanner-error-message]");
  const score = container.querySelector("[data-usegeo-score]");
  const grade = container.querySelector("[data-usegeo-grade]");
  const modelGpt = container.querySelector("[data-usegeo-model-gpt]");
  const modelClaude = container.querySelector("[data-usegeo-model-claude]");
  const modelGemini = container.querySelector("[data-usegeo-model-gemini]");
  const radar = container.querySelector("[data-usegeo-radar]");
  const radarVal1 = container.querySelector("[data-usegeo-radar-val-1]");
  const radarVal2 = container.querySelector("[data-usegeo-radar-val-2]");
  const radarVal3 = container.querySelector("[data-usegeo-radar-val-3]");
  const radarVal4 = container.querySelector("[data-usegeo-radar-val-4]");
  const radarVal5 = container.querySelector("[data-usegeo-radar-val-5]");
  const summary = container.querySelector("[data-usegeo-summary]");
  const findings = container.querySelector("[data-usegeo-findings]");
  const evidence = container.querySelector("[data-usegeo-evidence]");
  const scannerQueryInput = container.querySelector("[data-usegeo-scanner-query]");
  const hidden = {
    total: container.querySelector("[data-usegeo-scan-total-score]"),
    entity: container.querySelector("[data-usegeo-scan-entity-clarity]"),
    structure: container.querySelector("[data-usegeo-scan-content-structure]"),
    proof: container.querySelector("[data-usegeo-scan-proof-signals]"),
    comparison: container.querySelector("[data-usegeo-scan-comparison-readiness]"),
    extractability: container.querySelector("[data-usegeo-scan-ai-extractability]"),
    completedAt: container.querySelector("[data-usegeo-scan-completed-at]"),
    findingsSummary: container.querySelector("[data-usegeo-scan-findings-summary]"),
    llmSummary: container.querySelector("[data-usegeo-scan-llm-summary]")
  };

  const loadingMessages = [
    { text: "Fetching the homepage and checking indexable signals...", stage: "structure" },
    { text: "Mapping entity, content, proof, comparison, and extractability signals...", stage: "signals" },
    { text: "Preparing your directional GEO readiness snapshot...", stage: "roadmap" }
  ];

  const errorMessages = {
    invalid_url: "Please enter a valid website URL (e.g., https://yourcompany.com).",
    blocked_url: "This URL cannot be scanned. Please enter a public website URL.",
    fetch_timeout: "The scan timed out. Please try again in a moment.",
    fetch_failed: "We could not reach this website. Please check the URL and try again.",
    non_html_response: "This URL does not return a web page. Please enter a website URL.",
    parse_failed: "We could not analyze this page. Please try a different URL.",
    rate_limited: "You have run several scans recently. Please wait a minute and try again.",
    internal_error: "Something went wrong on our end. Please try again later."
  };

  let loadingTimer = null;
  const escapeHtml = (value) => String(value || "").replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");

  function setStage(stageName) {
    stages.forEach(function (stage) {
      const isCurrent = stage.getAttribute("data-usegeo-stage") === stageName;
      stage.classList.toggle("is-active", isCurrent);
      if (isCurrent) stage.classList.add("is-complete");
    });
  }

  function setLoading() {
    let index = 0;
    if (loadingMessage) loadingMessage.textContent = loadingMessages[index].text;
    setStage(loadingMessages[index].stage);
    loadingTimer = window.setInterval(function () {
      index = (index + 1) % loadingMessages.length;
      if (loadingMessage) loadingMessage.textContent = loadingMessages[index].text;
      setStage(loadingMessages[index].stage);
    }, 900);
  }

  function clearLoading() {
    if (loadingTimer) window.clearInterval(loadingTimer);
    loadingTimer = null;
  }

  function demoPayload(query) {
    const normalized = String(query).trim();
    const base = Math.min(60 + Math.min(normalized.length, 24), 88);
    const scores = {
      total: base,
      entity_clarity: Math.max(base - 5, 50),
      content_structure: Math.min(base + 2, 91),
      proof_signals: Math.max(base - 12, 42),
      comparison_readiness: Math.max(base - 10, 45),
      ai_extractability: Math.max(base - 6, 48)
    };
    const gradeText = base >= 80 ? "Strong readiness" : (base < 70 ? "Early readiness" : "Emerging readiness");
    return {
      ok: true,
      url: normalized,
      final_url: normalized,
      scores,
      grade: gradeText,
      extracted: {
        title: "Demo mode: homepage title unavailable",
        meta_description: "Demo mode uses a directional placeholder until Real Scanner is enabled.",
        h1: ["Demo scanner result"],
        h2: [],
        schema_types: [],
        word_count: 0,
        detected_signals: {}
      },
      findings: [
        { severity: "medium", dimension: "content_structure", title: "Demo mode result", evidence: "Real Scanner is disabled, so this score is directional demo output.", recommendation: "Enable Real Scanner to fetch and analyze the real homepage." },
        { severity: "medium", dimension: "comparison_readiness", title: "Comparison coverage should be verified", evidence: "Demo mode cannot inspect comparison terms on the page.", recommendation: "Use Real Scanner before making content decisions." },
        { severity: "low", dimension: "proof_signals", title: "Proof signals need real evidence", evidence: "Demo mode cannot detect customer, case, or result terms.", recommendation: "Run the real scanner to inspect proof signals." }
      ],
      llm_summary: { available: false, summary: `${normalized} shows ${gradeText.toLowerCase()} in demo mode. Enable Real Scanner for evidence-based results.`, top_recommendations: ["Enable Real Scanner for evidence-based findings."], audit_recommendation: { recommended: false, reason: "Demo mode" } },
      demo_mode: true
    };
  }

  function radarPoints(content, entity, comparison, extractability, proof) {
    const cx = 100, cy = 80, maxRadius = 60;
    const angles = [-90, -18, 54, 126, 198];
    return [content, entity, comparison, extractability, proof].map(function (value, index) {
      const radius = (Number(value || 0) / 100) * maxRadius;
      const rad = angles[index] * Math.PI / 180;
      return Math.round(cx + radius * Math.cos(rad)) + "," + Math.round(cy + radius * Math.sin(rad));
    }).join(" ");
  }

  function setSnapshot(query, payload) {
    const scores = payload.scores || {};
    const set = (field, value) => { if (field) field.value = value || ""; };
    if (scannerQueryInput) scannerQueryInput.value = String(query).trim();
    set(hidden.total, scores.total);
    set(hidden.entity, scores.entity_clarity);
    set(hidden.structure, scores.content_structure);
    set(hidden.proof, scores.proof_signals);
    set(hidden.comparison, scores.comparison_readiness);
    set(hidden.extractability, scores.ai_extractability);
    set(hidden.completedAt, new Date().toISOString());
    set(hidden.findingsSummary, (payload.findings || []).map(item => `${item.title}: ${item.recommendation}`).join("\n"));
    set(hidden.llmSummary, payload.llm_summary && payload.llm_summary.summary ? payload.llm_summary.summary : "");
  }

  function clearSnapshot(query) {
    if (scannerQueryInput) scannerQueryInput.value = String(query || "").trim();
    Object.keys(hidden).forEach(key => { if (hidden[key]) hidden[key].value = ""; });
  }

  function renderEvidence(payload) {
    if (!evidence) return;
    const extracted = payload.extracted || {};
    const signals = extracted.detected_signals || {};
    const proofSignals = [];
    if (signals.has_case_study_terms) proofSignals.push("case study/results");
    if (signals.has_customer_terms) proofSignals.push("customer/testimonial");
    if (signals.has_pricing_terms) proofSignals.push("pricing/ROI");
    const comparisonSignals = [];
    if (signals.has_comparison_terms) comparisonSignals.push("comparison/alternative");
    if (signals.has_use_case_terms) comparisonSignals.push("use case");
    evidence.innerHTML = `
      <span class="usegeo-scanner-evidence__label">What we found on your site</span>
      <dl>
        <dt>Page title</dt><dd>${escapeHtml(extracted.title || "Not detected")}</dd>
        <dt>H1</dt><dd>${escapeHtml((extracted.h1 || [])[0] || "Not detected")}</dd>
        <dt>Schema detected</dt><dd>${escapeHtml((extracted.schema_types || []).join(", ") || "Not detected")}</dd>
        <dt>Word count</dt><dd>${escapeHtml(extracted.word_count || 0)}</dd>
        <dt>Proof signals</dt><dd>${escapeHtml(proofSignals.join(", ") || "Not detected")}</dd>
        <dt>Comparison signals</dt><dd>${escapeHtml(comparisonSignals.join(", ") || "Not detected")}</dd>
      </dl>`;
    evidence.hidden = false;
  }

  function renderPayload(query, payload) {
    const scores = payload.scores || {};
    const total = Number(scores.total || 0);
    const gradeText = payload.grade || (total >= 80 ? "Strong readiness" : (total >= 65 ? "Emerging readiness" : "Early readiness"));
    if (score) score.textContent = String(total);
    if (grade) grade.textContent = gradeText;
    if (modelGpt) modelGpt.textContent = String(Math.round(((scores.entity_clarity || 0) + (scores.ai_extractability || 0)) / 2));
    if (modelClaude) modelClaude.textContent = String(Math.round(((scores.content_structure || 0) + (scores.proof_signals || 0)) / 2));
    if (modelGemini) modelGemini.textContent = String(Math.round(((scores.comparison_readiness || 0) + (scores.ai_extractability || 0)) / 2));
    if (radar) radar.setAttribute("points", radarPoints(scores.content_structure, scores.entity_clarity, scores.comparison_readiness, scores.ai_extractability, scores.proof_signals));
    if (radarVal1) radarVal1.textContent = String(scores.content_structure || 0);
    if (radarVal2) radarVal2.textContent = String(scores.entity_clarity || 0);
    if (radarVal3) radarVal3.textContent = String(scores.comparison_readiness || 0);
    if (radarVal4) radarVal4.textContent = String(scores.ai_extractability || 0);
    if (radarVal5) radarVal5.textContent = String(scores.proof_signals || 0);
    if (summary) summary.textContent = payload.llm_summary && payload.llm_summary.summary ? payload.llm_summary.summary : "This directional scan uses homepage signals to estimate GEO readiness.";
    if (findings) findings.innerHTML = (payload.findings || []).slice(0, 5).map(item => `<li><strong>${escapeHtml(item.title)}</strong><br><span>${escapeHtml(item.evidence)}</span><br><em>${escapeHtml(item.recommendation)}</em></li>`).join("");
    renderEvidence(payload);
    if (payload.demo_mode) {
      clearSnapshot(query);
      if (scannerQueryInput) scannerQueryInput.value = String(query).trim();
    } else {
      setSnapshot(query, payload);
    }
    if (status) status.textContent = "Assessment complete. This is a directional homepage signal scan.";
    if (emptyState) emptyState.hidden = true;
    if (result) result.hidden = false;
    if (errorBox) errorBox.hidden = true;
    if (window.dataLayer) window.dataLayer.push({ event: "scanner_complete", location: "homepage", score: total, llm_available: Boolean(payload.llm_summary && payload.llm_summary.available) });
  }

  function showError(query, code, message) {
    clearSnapshot(query);
    if (status) status.textContent = "Assessment could not be completed.";
    if (emptyState) emptyState.hidden = false;
    if (result) result.hidden = true;
    if (errorBox) errorBox.hidden = false;
    if (errorMessage) errorMessage.textContent = errorMessages[code] || message || errorMessages.internal_error;
  }

  function runRealScanner(query) {
    return window.fetch(apiUrl, {
      method: "POST",
      headers: { "Content-Type": "application/json", "X-WP-Nonce": nonce },
      credentials: "same-origin",
      body: JSON.stringify({ url: String(query).trim() })
    }).then(response => response.json().then(body => {
      if (!response.ok || !body.ok) {
        const error = body.error || {};
        throw { code: error.error_code || "internal_error", message: error.message || "" };
      }
      return body;
    }));
  }

  form.addEventListener("submit", function (event) {
    event.preventDefault();
    const query = new FormData(form).get("query");
    if (!query || !String(query).trim()) {
      if (status) status.textContent = "Please enter your company website.";
      if (loading) loading.hidden = true;
      if (result) result.hidden = true;
      clearLoading();
      return;
    }
    if (status) status.textContent = "Preparing your directional GEO readiness snapshot...";
    if (emptyState) emptyState.hidden = true;
    if (loading) loading.hidden = false;
    if (result) result.hidden = true;
    if (errorBox) errorBox.hidden = true;
    setLoading();
    const runner = realScannerEnabled ? runRealScanner(query) : new Promise(resolve => window.setTimeout(() => resolve(demoPayload(query)), 1200));
    runner.then(payload => {
      clearLoading();
      if (loading) loading.hidden = true;
      renderPayload(query, payload);
    }).catch(error => {
      clearLoading();
      if (loading) loading.hidden = true;
      showError(query, error.code || "internal_error", error.message || "");
    });
  });
});
