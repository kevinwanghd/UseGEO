# UseGEO Staging Deployment Runbook

## Scope

This runbook covers the environment-dependent launch tasks:

- `L3` staging / production WordPress environment preparation
- `L4` theme upload and online verification
- `L5` live lead submission and backend record verification
- `L6` live email notification verification

This document assumes the local theme implementation is already complete.

## Pre-Deployment Package

Before touching staging, confirm the local package includes:

- `wordpress/theme/usegeo`
- homepage templates
- scanner / ROI scripts
- lead capture flow
- community case submission flow
- backend custom post types:
  - `usegeo_lead`
  - `usegeo_case`

## L3. Prepare Staging WordPress

### Goal

Create a public-but-safe environment for final verification before production.

### Required Conditions

- a staging domain or subdomain is available
- WordPress is installed
- admin login is available
- permalink settings can be changed
- admin email can be updated

### Checklist

1. Create or access the staging WordPress site.
2. Confirm `wp-admin` access works.
3. Set permalink structure away from the default plain query mode.
4. Confirm you can upload and activate a custom theme.
5. Create or verify required pages:
   - Home
   - About
   - Privacy Policy
   - Cookie Policy
6. Confirm the site is not blocked by maintenance mode or broken SSL.

### Acceptance

- admin is accessible
- theme upload works
- pages and posts can be created

## L4. Upload Theme And Verify Rendering

### Goal

Deploy the current `usegeo` theme to staging and confirm the site renders the correct templates.

### Checklist

1. Upload the latest `usegeo` theme folder or zip.
2. Activate the theme.
3. Set the homepage to the intended static page.
4. Confirm header and footer render correctly.
5. Confirm homepage modules render in this order:
   - Hero
   - Value proposition
   - Learning path
   - Content hub
   - Community section
   - Case preview section
   - Scanner
   - ROI
6. Open at least 2 article pages and confirm sidebar CTA still works.
7. Confirm mobile rendering on a narrow viewport.

### Acceptance

- homepage is driven by the `UseGEO` theme
- there is no fallback to a default WordPress theme template
- content and tool sections render without layout breakage

## L5. Verify Lead Submission Flow In Staging

### Goal

Re-confirm the commercial funnel path works in a real environment.

### Test Path A

`Homepage -> Scanner -> Lead form`

### Test Path B

`Article page -> CTA -> Homepage scanner -> Lead form`

### Checklist

1. Run the scanner from the homepage.
2. Submit a test lead with a valid work email and job title.
3. Confirm redirect returns with a visible success or partial-success notice.
4. Open `UseGEO Leads` in WordPress admin.
5. Confirm a new record exists with:
   - work email
   - job title
   - scanner query
   - submitted timestamp
   - notification fields
6. Repeat from an article CTA path.

### Acceptance

- both funnel paths complete
- a new backend lead record is created
- no fatal redirect or form errors occur

## L6. Verify Email Notification In Real Environment

### Goal

Confirm whether admin notification mail is actually deliverable in staging or production.

### Checklist

1. Confirm `admin_email` is set to the real receiving mailbox.
2. Submit one test lead.
3. Check whether the email is received.
4. If mail does not arrive, inspect the backend lead record:
   - `notification_sent`
   - `notification_recipient`
   - `notification_error`
5. If delivery fails, decide whether to:
   - keep backend-only alerts temporarily
   - configure SMTP before public launch

### Acceptance

- delivery status is known
- backend lead persistence still works even if mail fails

## Analytics Configuration

This theme now supports either:

- `USEGEO_GTM_ID`
- `USEGEO_GA4_MEASUREMENT_ID`

Add one of these in the environment before staging verification.

Prefer GTM if a tag management workflow already exists.

## Suggested Launch Sequence

1. Prepare staging WordPress
2. Upload and activate theme
3. Verify homepage and articles
4. Verify lead submission
5. Verify mail delivery
6. Verify analytics events
7. Run final manual QA
