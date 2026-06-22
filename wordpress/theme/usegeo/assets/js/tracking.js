// UseGEO Event Tracking Helper
// GTM/GA4 base tags are injected via PHP in wp_head / wp_body_open.
// This script provides convenience helpers for tracking custom events.
(function() {
  'use strict';

  // Push event to dataLayer (works with both GTM and GA4)
  window.usegeoTrackEvent = function(eventName, eventParams) {
    window.dataLayer = window.dataLayer || [];
    window.dataLayer.push(Object.assign({ event: eventName }, eventParams || {}));
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

  // Full diagnostic requested after scanner result
  document.addEventListener('usegeo_full_diagnostic_requested', function(e) {
    window.usegeoTrackEvent('generate_lead', {
      event_category: 'conversion',
      event_label: 'full_diagnostic_request',
      scanner_domain: e.detail && e.detail.domain || '',
      help_intent: e.detail && e.detail.help_intent || '',
      support_level: e.detail && e.detail.support_level || '',
      timeline: e.detail && e.detail.timeline || ''
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

  document.addEventListener('submit', function(e) {
    var form = e.target;
    if (!form || !form.classList) return;
    if (form.classList.contains('usegeo-report-gate__form')) {
      var formData = new FormData(form);
      window.usegeoTrackEvent('lead_form_submit_attempt', {
        event_category: 'conversion',
        event_label: 'full_assessment_gate',
        scanner_domain: formData.get('scanner_query') || '',
        help_intent: formData.get('help_intent') || '',
        support_level: formData.get('support_level') || '',
        timeline: formData.get('timeline') || ''
      });
      document.dispatchEvent(new CustomEvent('usegeo_full_diagnostic_requested', {
        detail: {
          domain: formData.get('scanner_query') || '',
          help_intent: formData.get('help_intent') || '',
          support_level: formData.get('support_level') || '',
          timeline: formData.get('timeline') || ''
        }
      }));
    }
    if (form.classList.contains('usegeo-community-form')) {
      window.usegeoTrackEvent('case_form_submit_attempt', {
        event_category: 'engagement',
        event_label: 'case_submission'
      });
    }
  });


  // Sponsor click and impression tracking
  document.addEventListener('click', function(e) {
    var sponsorLink = e.target.closest('.usegeo-sponsor-card a[href]');
    if (!sponsorLink) return;
    var sponsorCard = sponsorLink.closest('.usegeo-sponsor-card');
    var placement = sponsorCard && sponsorCard.getAttribute('data-usegeo-sponsor-slot') || '';
    var sponsorUrl = sponsorCard && sponsorCard.getAttribute('data-usegeo-sponsor-url') || sponsorLink.href || '';
    window.usegeoTrackEvent('sponsor_click', {
      event_category: 'sponsor',
      placement: placement,
      sponsor_url: sponsorUrl
    });
  });

  function trackSponsorImpression(card) {
    if (!card || card.dataset.usegeoSponsorImpressionTracked === 'yes') return;
    card.dataset.usegeoSponsorImpressionTracked = 'yes';
    window.usegeoTrackEvent('sponsor_impression', {
      event_category: 'sponsor',
      placement: card.getAttribute('data-usegeo-sponsor-slot') || ''
    });
  }

  var sponsorCards = Array.prototype.slice.call(document.querySelectorAll('.usegeo-sponsor-card[data-usegeo-sponsor-slot]'));
  if ('IntersectionObserver' in window) {
    var sponsorObserver = new IntersectionObserver(function(entries) {
      entries.forEach(function(entry) {
        if (entry.isIntersecting) {
          trackSponsorImpression(entry.target);
          sponsorObserver.unobserve(entry.target);
        }
      });
    }, { threshold: 0.5 });
    sponsorCards.forEach(function(card) { sponsorObserver.observe(card); });
  }

  // CTA click tracking
  document.addEventListener('click', function(e) {
    var target = e.target.closest('a[href], button');
    if (!target) return;
    var ctaLabel = target.getAttribute('data-cta') || target.textContent.trim().slice(0, 50);
    if (target.classList.contains('usegeo-cta') || target.classList.contains('usegeo-inline-cta') || target.classList.contains('usegeo-single-button') || target.classList.contains('usegeo-report-gate__button') || target.closest('.usegeo-cta')) {
      window.usegeoTrackEvent('cta_click', {
        event_category: 'engagement',
        event_label: ctaLabel
      });
    }
  });
})();
