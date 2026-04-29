# 2026-04-27 Community MVP Local Progress

## Summary

Local implementation for the UseGEO community MVP has moved beyond homepage messaging and now includes a working submission flow.

This round covered:

- `C1` homepage community entry and contribution narrative
- `C2` case contribution field structure
- `C3` native WordPress form as the submission method
- `C4` backend record structure for case submissions
- `C5` minimal review workflow in WordPress admin
- `C6` homepage featured-case placeholder section

## What Was Added

### Homepage

- Added a dedicated community section after the content hub and before the scanner.
- Added a featured case placeholder block to reserve future showcase space.
- Replaced the previous "coming next" community CTA with a real submission form.

### Submission Flow

- Added a native WordPress form submission endpoint: `usegeo_submit_case`
- Added a new custom post type: `usegeo_case_submission`
- Added field capture for:
  - contributor name
  - contact email
  - company type
  - industry
  - business stage
  - core problem
  - GEO actions
  - pages updated
  - results seen
  - lessons learned
  - anonymous display permission

### Review Workflow

- Added review status tracking:
  - `pending_review`
  - `reviewing`
  - `approved`
  - `needs_followup`
  - `archived`
- Added processing lane tracking:
  - `unassigned`
  - `featured_pool`
  - `article_pipeline`
  - `followup_pool`
  - `archive_only`
- Added editable editorial notes in the case submission admin meta box.

## Current Acceptance Status

### Implemented locally

- Homepage communicates that UseGEO is accepting structured case contributions.
- Case submissions can be captured through a WordPress-native form.
- Each submission is stored in the WordPress admin as a structured backend record.
- Submissions do not auto-publish to the public site.
- Admin can mark review status and downstream handling lane.

### Not yet verified

- Real browser submission test in the current local environment
- PHP linting from CLI
  - blocked because `php` is not available in the current shell PATH
- Email notification for case submissions
  - not implemented in this round
- Any public rendering of approved cases
  - still intentionally out of scope

## Recommended Next Step

The strongest next local task is `L2`: final content and homepage card polish.

After that, work should shift to environment-dependent items:

- `L3` staging / production WordPress preparation
- `L4` theme upload and online verification
- `L5` live lead and case submission verification
- `L6` mail notification verification
