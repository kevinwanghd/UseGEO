document.addEventListener("DOMContentLoaded", function () {
  window.dataLayer = window.dataLayer || [];

  const pushEvent = function (eventName, payload) {
    const data = Object.assign({ event: eventName }, payload || {});
    window.dataLayer.push(data);
    document.dispatchEvent(new CustomEvent(`usegeo:${eventName}`, { detail: data }));
  };

  pushEvent("page_view", {
    page_path: window.location.pathname,
    page_title: document.title,
  });

  document.querySelectorAll("[data-usegeo-scanner-form]").forEach(function (form) {
    const input = form.querySelector('input[name="query"]');

    if (input) {
      input.addEventListener("focus", function () {
        pushEvent("scanner_focus", { location: "homepage" });
      }, { once: true });
    }

    form.addEventListener("submit", function () {
      const query = input ? String(input.value || "").trim() : "";
      pushEvent("scanner_submit", {
        location: "homepage",
        query_length: query.length,
      });
    });
  });

  document.querySelectorAll("[data-usegeo-roi]").forEach(function (form) {
    let fired = false;
    form.addEventListener("input", function () {
      if (fired) {
        return;
      }
      fired = true;
      pushEvent("roi_interaction", { location: "homepage" });
    });
  });

  document.querySelectorAll(".usegeo-inline-cta, .usegeo-report-gate__button").forEach(function (node) {
    node.addEventListener("click", function () {
      pushEvent("cta_click", {
        label: String(node.textContent || "").trim(),
        location: window.location.pathname,
      });
    });
  });
});
