# UseGEO Analytics And CRM Plan

## Scope

This document closes:

- `L7` GTM / GA4 minimal integration
- `L8` CRM / follow-up strategy confirmation

## L7. Analytics Integration

### Current State

The theme already emits a minimum event set through `window.dataLayer`.

Current events:

- `page_view`
- `scanner_focus`
- `scanner_submit`
- `scanner_complete`
- `roi_interaction`
- `cta_click`

### Theme Configuration

The theme now supports two configuration modes:

1. GTM mode
   - define `USEGEO_GTM_ID`
2. Direct GA4 mode
   - define `USEGEO_GA4_MEASUREMENT_ID`
   - only used when no GTM container ID is present

### Recommendation

Prefer GTM if the team expects to add more events, conversion mapping, or third-party tags later.

Use direct GA4 only if the stack needs the lightest possible setup.

### Verification Checklist

1. Open the homepage with tag assistant or browser devtools.
2. Confirm the GTM or GA4 script is present.
3. Confirm `window.dataLayer` exists.
4. Trigger and inspect these events:
   - page load -> `page_view`
   - focus scanner input -> `scanner_focus`
   - submit scanner -> `scanner_submit`
   - wait for scanner result -> `scanner_complete`
   - change ROI input -> `roi_interaction`
   - click CTA -> `cta_click`

### Acceptance

- script is loaded
- events are present in `dataLayer`
- at least the scanner and CTA path can be tracked

## L8. CRM And Follow-Up Strategy

### Recommended Phase-1 Strategy

Do not add a full external CRM before the first staging and beta validation.

Use this simple sequence first:

1. Store all leads in WordPress admin
2. Send admin notification mail
3. Review new leads once or twice daily
4. Manually move qualified leads into the team CRM if needed

### Why This Is The Right Phase-1 Choice

- lowest implementation cost
- lowest launch risk
- enough for early beta traffic
- preserves flexibility before lead volume is known

### Required Operating Rule

Every new lead must have an owner and a next action within 1 business day.

### Suggested Follow-Up Statuses

For `usegeo_lead` records, track follow-up operationally with this logic:

- new
- contacted
- qualified
- not now
- archived

If the team later wants system support, this can become either:

- custom lead statuses in WordPress
- sync into HubSpot / Pipedrive / another CRM

### Community Submission Workflow

For `usegeo_case_submission`, the backend review flow is now:

- review status
- processing lane
- editorial notes

This is enough for the first manual editorial workflow.
