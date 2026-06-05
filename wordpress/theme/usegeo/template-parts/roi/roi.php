<section class="usegeo-card">
  <div class="usegeo-section-heading">
    <h2>GEO ROI Calculator</h2>
    <p class="usegeo-eyebrow">Estimate how much growth a GEO strategy could bring to your business</p>
  </div>

  <div class="usegeo-roi" data-usegeo-roi>
    <div class="usegeo-roi__grid">
      <label>
        <span>Estimated GEO investment (USD)</span>
        <input type="number" name="investment" placeholder="e.g. 5000" min="0" step="100">
      </label>
      <label>
        <span>Current monthly traffic</span>
        <input type="number" name="traffic" value="10000" min="0" step="100">
      </label>
      <label>
        <span>Current conversion rate (%)</span>
        <input type="number" name="conversion_rate" value="2" min="0" max="100" step="0.1">
      </label>
    </div>

    <div class="usegeo-roi__results">
      <div class="usegeo-metric">
        <span class="usegeo-metric__label">Projected annual revenue increase</span>
        <strong data-usegeo-roi-revenue>$0</strong>
      </div>
      <div class="usegeo-metric">
        <span class="usegeo-metric__label">Return on investment</span>
        <strong data-usegeo-roi-percent>0%</strong>
      </div>
      <div class="usegeo-metric">
        <span class="usegeo-metric__label">Payback period</span>
        <strong data-usegeo-roi-payback>-</strong>
      </div>
    </div>

    <div class="usegeo-roi__cta">
      <button class="usegeo-report-gate__button" type="button" id="usegeo-roi-calculate">Calculate ROI</button>
      <p>Based on GEO industry averages: 30% organic traffic lift, 15% conversion rate improvement</p>
    </div>
  </div>
</section>
