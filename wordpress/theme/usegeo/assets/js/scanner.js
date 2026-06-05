document.addEventListener("DOMContentLoaded", function () {
  const container = document.querySelector("[data-usegeo-scanner]");

  if (!container) {
    return;
  }

  const form = container.querySelector("[data-usegeo-scanner-form]");
  const status = container.querySelector("[data-usegeo-scanner-status]");
  const loading = container.querySelector("[data-usegeo-scanner-loading]");
  const loadingMessage = container.querySelector("[data-usegeo-loading-message]");
  const result = container.querySelector("[data-usegeo-scanner-result]");
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
  const scannerQueryInput = container.querySelector("[data-usegeo-scanner-query]");

  const loadingMessages = [
    "Checking the website structure and signal pattern...",
    "Estimating AI visibility and recommendation fit...",
    "Preparing your GEO readiness snapshot...",
  ];

  let loadingTimer = null;

  const setLoading = function () {
    let index = 0;
    if (loadingMessage) {
      loadingMessage.textContent = loadingMessages[index];
    }

    loadingTimer = window.setInterval(function () {
      index = (index + 1) % loadingMessages.length;
      if (loadingMessage) {
        loadingMessage.textContent = loadingMessages[index];
      }
    }, 650);
  };

  const clearLoading = function () {
    if (loadingTimer) {
      window.clearInterval(loadingTimer);
      loadingTimer = null;
    }
  };

    /**
   * MVP: GEO readiness score is simulated based on input URL length.
   * This is a placeholder for demonstration purposes.
   * Production version will call a real GEO assessment API.
   */
  const getResultPayload = function (query) {
    const normalized = String(query).trim();
    const length = Math.min(normalized.length, 24);
    const visibilityScore = Math.min(60 + length, 88);
    const gpt = Math.min(visibilityScore + 4, 92);
    const claude = Math.max(visibilityScore - 2, 55);
    const gemini = Math.min(visibilityScore + 1, 90);

    const radarContent = Math.min(visibilityScore + 2, 91);
    const radarMention = Math.max(visibilityScore - 5, 50);
    const radarCompetitive = Math.max(visibilityScore - 10, 45);
    const radarSemantic = Math.max(visibilityScore - 6, 48);
    const radarAuthority = Math.max(visibilityScore - 12, 42);

    let gradeText = "Emerging readiness";
    if (visibilityScore >= 80) {
      gradeText = "Strong readiness";
    } else if (visibilityScore < 70) {
      gradeText = "Early readiness";
    }

    return {
      visibilityScore,
      gpt,
      claude,
      gemini,
      radarContent,
      radarMention,
      radarCompetitive,
      radarSemantic,
      radarAuthority,
      gradeText,
      summaryText: `${normalized} shows ${gradeText.toLowerCase()} for GEO. The highest-leverage next steps are likely stronger solution clarity, comparison coverage, and more durable proof content.`,
      findingsHtml: [
        "Your site has some foundational signals, but not enough structured clarity to support stronger AI recommendation consistency.",
        "Solution, comparison, and proof content appear to be the most valuable gaps to close next.",
        "The next move should be focused execution, not simply publishing more undifferentiated content volume."
      ]
    };
  };

  const calculateRadarPoints = function (content, mention, competitive, semantic, authority) {
    const cx = 100, cy = 80;
    const maxRadius = 60;

    const toRad = (deg) => (deg * Math.PI) / 180;

    const r1 = (content / 100) * maxRadius;
    const r2 = (mention / 100) * maxRadius;
    const r3 = (competitive / 100) * maxRadius;
    const r4 = (semantic / 100) * maxRadius;
    const r5 = (authority / 100) * maxRadius;

    const p1 = { x: cx + r1 * Math.cos(toRad(-90)), y: cy + r1 * Math.sin(toRad(-90)) };
    const p2 = { x: cx + r2 * Math.cos(toRad(-18)), y: cy + r2 * Math.sin(toRad(-18)) };
    const p3 = { x: cx + r3 * Math.cos(toRad(54)), y: cy + r3 * Math.sin(toRad(54)) };
    const p4 = { x: cx + r4 * Math.cos(toRad(126)), y: cy + r4 * Math.sin(toRad(126)) };
    const p5 = { x: cx + r5 * Math.cos(toRad(198)), y: cy + r5 * Math.sin(toRad(198)) };

    return [
      Math.round(p1.x) + "," + Math.round(p1.y),
      Math.round(p2.x) + "," + Math.round(p2.y),
      Math.round(p3.x) + "," + Math.round(p3.y),
      Math.round(p4.x) + "," + Math.round(p4.y),
      Math.round(p5.x) + "," + Math.round(p5.y),
    ].join(" ");
  };

  form.addEventListener("submit", function (event) {
    event.preventDefault();

    const query = new FormData(form).get("query");
    if (!query || !String(query).trim()) {
      status.textContent = "Please enter your company website.";
      if (loading) {
        loading.hidden = true;
      }
      result.hidden = true;
      clearLoading();
      return;
    }

    status.textContent = "Preparing your GEO readiness snapshot...";
    if (loading) {
      loading.hidden = false;
    }
    result.hidden = true;
    setLoading();

    window.setTimeout(function () {
      const payload = getResultPayload(query);
      clearLoading();

      if (loading) {
        loading.hidden = true;
      }

      score.textContent = String(payload.visibilityScore);
      grade.textContent = payload.gradeText;
      modelGpt.textContent = String(payload.gpt);
      modelClaude.textContent = String(payload.claude);
      modelGemini.textContent = String(payload.gemini);

      if (radar) {
        const radarPoints = calculateRadarPoints(
          payload.radarContent,
          payload.radarMention,
          payload.radarCompetitive,
          payload.radarSemantic,
          payload.radarAuthority
        );
        radar.setAttribute("points", radarPoints);
      }

      if (radarVal1) radarVal1.textContent = String(payload.radarContent);
      if (radarVal2) radarVal2.textContent = String(payload.radarMention);
      if (radarVal3) radarVal3.textContent = String(payload.radarCompetitive);
      if (radarVal4) radarVal4.textContent = String(payload.radarSemantic);
      if (radarVal5) radarVal5.textContent = String(payload.radarAuthority);

      summary.textContent = payload.summaryText;
      if (scannerQueryInput) {
        scannerQueryInput.value = String(query).trim();
      }
      findings.innerHTML = payload.findingsHtml.map(function (item) {
        return `<li>${item}</li>`;
      }).join("");
      status.textContent = "Assessment complete.";
      result.hidden = false;
      if (window.dataLayer) {
        window.dataLayer.push({
          event: "scanner_complete",
          location: "homepage",
          score: payload.visibilityScore,
          query_length: String(query).trim().length,
        });
      }
    }, 2100);
  });
});
