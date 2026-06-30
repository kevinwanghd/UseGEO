// UseGEO ROI Calculator
document.addEventListener('DOMContentLoaded', function() {
  var container = document.querySelector('[data-usegeo-roi]');
  if (!container) return;

  var investmentInput = container.querySelector('input[name="investment"]');
  var articlesInput = container.querySelector('input[name="articles"]');
  var trafficInput = container.querySelector('input[name="traffic"]');
  var conversionRateInput = container.querySelector('input[name="conversion_rate"]');
  var calculateBtn = container.querySelector('#usegeo-roi-calculate');
  var revenueDisplay = container.querySelector('[data-usegeo-roi-revenue]');
  var percentDisplay = container.querySelector('[data-usegeo-roi-percent]');
  var paybackDisplay = container.querySelector('[data-usegeo-roi-payback]');

  if (!investmentInput || !calculateBtn) return;

  calculateBtn.addEventListener('click', function(e) {
    e.preventDefault();
    var investment = parseFloat(investmentInput.value) || 0;
    var articles = parseFloat(articlesInput.value) || 4;
    var traffic = parseFloat(trafficInput.value) || 10000;
    var conversionRate = parseFloat(conversionRateInput.value) || 2;

    if (investment <= 0) {
      investmentInput.focus();
      return;
    }

    // GEO industry averages
    var trafficIncrease = 0.30;
    var conversionLift = 0.15;
    var avgOrderValue = 500;  // B2B average order value estimate
    var contentLiftFactor = Math.min(articles / 4, 3);  // Scale factor: 4 articles = 1x, more = higher lift

    var additionalVisitors = traffic * trafficIncrease * contentLiftFactor;
    var newConversionRate = conversionRate * (1 + conversionLift);
    var additionalConversions = additionalVisitors * (newConversionRate / 100);
    var monthlyRevenue = additionalConversions * avgOrderValue;
    var yearlyRevenue = monthlyRevenue * 12;
    var roi = ((yearlyRevenue - investment) / investment) * 100;

    if (revenueDisplay) {
      revenueDisplay.textContent = '$' + Math.round(yearlyRevenue).toLocaleString();
    }
    if (percentDisplay) {
      percentDisplay.textContent = (roi > 0 ? '+' : '') + Math.round(roi) + '%';
    }
    var percentMetric = percentDisplay ? percentDisplay.closest('.usegeo-metric') : null;
    if (percentMetric) {
      percentMetric.classList.toggle('usegeo-metric--featured', roi > 0);
    }
    if (paybackDisplay) {
      paybackDisplay.textContent = roi > 0 ? Math.ceil(investment / (monthlyRevenue || 1)) + ' months' : 'N/A';
    }

    // Track event
    if (typeof dataLayer !== 'undefined') {
      dataLayer.push({
        event: 'roi_calculated',
        event_category: 'engagement',
        investment: investment,
        articles: articles,
        roi: Math.round(roi)
      });
    }
  });

  // Enter key support
  container.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
      calculateBtn.click();
    }
  });
});
