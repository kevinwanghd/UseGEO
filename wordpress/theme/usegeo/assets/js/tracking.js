// UseGEO Event Tracking Helper
// GTM/GA4 base tags are injected via PHP in wp_head / wp_body_open.
// This script provides convenience helpers for tracking custom events.
(function() {
  'use strict';

  // Push event to dataLayer (works with both GTM and GA4)
  window.usegeoTrackEvent = function(eventName, eventParams) {
    if (typeof dataLayer !== 'undefined') {
      dataLayer.push(Object.assign({ event: eventName }, eventParams || {}));
    }
  };

  // Scanner start
  document.addEventListener('usegeo_scanner_start', function(e) {
    window.usegeoTrackEvent('scanner_start', {
      event_category: 'engagement',
      event_label: e.detail && e.detail.domain || ''
    });
  });

  // Scanner complete
  document.addEventListener('usegeo_scanner_complete', function(e) {
    window.usegeoTrackEvent('scanner_complete', {
      event_category: 'engagement',
      event_label: e.detail && e.detail.domain || '',
      value: e.detail && e.detail.score || 0
    });
  });

  // Lead form submitted
  document.addEventListener('usegeo_lead_submitted', function(e) {
    window.usegeoTrackEvent('generate_lead', {
      event_category: 'conversion',
      event_label: e.detail && e.detail.form_type || 'lead'
    });
  });

  // ROI calculated
  document.addEventListener('usegeo_roi_calculated', function(e) {
    window.usegeoTrackEvent('roi_calculated', {
      event_category: 'engagement',
      event_label: 'roi_calculator',
      value: e.detail && e.detail.roi || 0
    });
  });

  // Case submission
  document.addEventListener('usegeo_case_submitted', function(e) {
    window.usegeoTrackEvent('case_submitted', {
      event_category: 'engagement',
      event_label: e.detail && e.detail.industry || ''
    });
  });

  // CTA click tracking
  document.addEventListener('click', function(e) {
    var target = e.target.closest('a[href], button');
    if (!target) return;
    var ctaLabel = target.getAttribute('data-cta') || target.textContent.trim().slice(0, 50);
    if (target.classList.contains('usegeo-cta') || target.closest('.usegeo-cta')) {
      window.usegeoTrackEvent('cta_click', {
        event_category: 'engagement',
        event_label: ctaLabel
      });
    }
  });
})();
