document.addEventListener("DOMContentLoaded", function () {
  const container = document.querySelector("[data-usegeo-scanner]");

  if (!container) {
    return;
  }

  const form = container.querySelector("[data-usegeo-scanner-form]");
  const status = container.querySelector("[data-usegeo-scanner-status]");
  const emptyState = container.querySelector("[data-usegeo-scanner-empty]");
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
  const stages = Array.from(container.querySelectorAll("[data-usegeo-stage]"));

  if (!form || !status || !result) {
    return;
  }

  const loadingMessages = [
    {
      text: "Checking homepage clarity, entity definitions, and business category signals...",
      stage: "structure",
    },
    {
      text: "Mapping comparison coverage, proof depth, and semantic reuse signals...",
      stage: "signals",
    },
    {
      text: "Preparing your GEO readiness score and recommended next step...",
      stage: "roadmap",
    },
  ];

  let loadingTimer = null;

  const setStage = function (stageName) {
    stages.forEach(function (stage) {
      const isCurrent = stage.getAttribute("data-usegeo-stage") === stageName;
      stage.classList.toggle("is-active", isCurrent);
      if (isCurrent) {
        stage.classList.add("is-complete");
      }
    });
  };

  const setLoading = function () {
    let index = 0;
    if (loadingMessage) {
      loadingMessage.textContent = loadingMessages[index].text;
    }
    setStage(loadingMessages[index].stage);

    loadingTimer = window.setInterval(function () {
      index = (index + 1) % loadingMessages.length;
      if (loadingMessage) {
        loadingMessage.textContent = loadingMessages[index].text;
      }
      setStage(loadingMessages[index].stage);
    }, 700);
  };

  const clearLoading = function () {
    if (loadingTimer) {
      window.clearInterval(loadingTimer);
      loadingTimer = null;
    }
  };

  /**
   * MVP: GEO readiness score is simulated from input traits.
   * Production version can replace this function with a real assessment API.
   */
  const getResultPayload = function (query) {
    const normalized = String(query).trim();
    const clean = normalized.replace(/^https?:\/\//i, "").replace(/\/$/, "");
    const length = Math.min(clean.length, 28);
    const hasPath = clean.indexOf("/") !== -1;
    const hasSubdomain = clean.split(".").length > 2;
    const visibilityScore = Math.min(58 + length + (hasPath ? 3 : 0) + (hasSubdomain ? 2 : 0), 89);
    const gpt = Math.min(visibilityScore + 4, 93);
    const claude = Math.max(visibilityScore - 3, 52);
    const gemini = Math.min(visibilityScore + 1, 91);

    const radarContent = Math.min(visibilityScore + 2, 91);
    const radarMention = Math.max(visibilityScore - 5, 50);
    const radarCompetitive = Math.max(visibilityScore - 12, 43);
    const radarSemantic = Math.max(visibilityScore - 6, 48);
    const radarAuthority = Math.max(visibilityScore - 14, 40);

    let gradeText = "Emerging readiness";
    let actionText = "A Manual GEO Audit would help convert this baseline into a prioritized execution plan.";
    if (visibilityScore >= 80) {
      gradeText = "Strong readiness";
      actionText = "You likely have enough signal to justify deeper prompt testing and a fix sprint.";
    } else if (visibilityScore < 70) {
      gradeText = "Early readiness";
      actionText = "Start with the highest-impact page fixes before investing in ongoing monitoring.";
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
      summaryText: `${clean} shows ${gradeText.toLowerCase()} for GEO. The highest-leverage next steps are likely stronger solution clarity, comparison coverage, and more durable proof content. ${actionText}`,
      findingsHtml: [
        "AI systems need clearer entity, solution, and proof signals before they can confidently reuse your site in recommendations.",
        "The most valuable gaps to inspect next are comparison coverage, use-case clarity, and outcome-backed evidence.",
        "The next step should be a prioritized diagnostic, not more unfocused content production."
      ]
    };
  };

  const calculateRadarPoints = function (content, mention, competitive, semantic, authority) {
    const cx = 100, cy = 80;
    const maxRadius = 60;

    const toRad = (deg) => (deg * Math.PI) / 180;

    const values = [content, mention, competitive, semantic, authority];
    const degrees = [-90, -18, 54, 126, 198];

    return values.map(function (value, index) {
      const radius = (value / 100) * maxRadius;
      const x = cx + radius * Math.cos(toRad(degrees[index]));
      const y = cy + radius * Math.sin(toRad(degrees[index]));
      return Math.round(x) + "," + Math.round(y);
    }).join(" ");
  };

  form.addEventListener("submit", function (event) {
    event.preventDefault();

    const query = new FormData(form).get("query");
    if (!query || !String(query).trim()) {
      status.textContent = "Please enter your company website.";
      if (loading) {
        loading.hidden = true;
      }
      if (emptyState) {
        emptyState.hidden = false;
      }
      result.hidden = true;
      clearLoading();
      return;
    }

    const domain = String(query).trim();
    status.textContent = "Preparing your GEO diagnostic snapshot...";
    document.dispatchEvent(new CustomEvent("usegeo_scanner_start", {
      detail: { domain }
    }));
    if (emptyState) {
      emptyState.hidden = true;
    }
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
        scannerQueryInput.value = domain;
      }
      findings.innerHTML = payload.findingsHtml.map(function (item) {
        return `<li>${item}</li>`;
      }).join("");
      status.textContent = "Assessment complete. Review the snapshot below and request the full diagnostic if you want prioritized next steps.";
      result.hidden = false;
      document.dispatchEvent(new CustomEvent("usegeo_scanner_complete", {
        detail: {
          domain,
          score: payload.visibilityScore,
          grade: payload.gradeText
        }
      }));
    }, 2300);
  });
});
