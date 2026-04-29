document.addEventListener("DOMContentLoaded", function () {
  const form = document.querySelector("[data-usegeo-roi]");

  if (!form) {
    return;
  }

  const savingsNode = form.querySelector("[data-usegeo-roi-savings]");
  const roiNode = form.querySelector("[data-usegeo-roi-value]");

  const recalculate = function () {
    const data = new FormData(form);
    const posts = Number(data.get("posts")) || 0;
    const cac = Number(data.get("cac")) || 0;
    const acv = Number(data.get("acv")) || 0;

    const traditionalCost = posts * cac;
    const automatedCost = traditionalCost * 0.6;
    const savings = traditionalCost > 0 ? ((traditionalCost - automatedCost) / traditionalCost) * 100 : 0;
    const roi = automatedCost > 0 ? ((posts * acv - automatedCost) / automatedCost) * 100 : 0;

    savingsNode.textContent = `${Math.round(savings)}%`;
    roiNode.textContent = `${Math.max(0, Math.round(roi))}%`;
  };

  form.addEventListener("input", recalculate);
  recalculate();
});
