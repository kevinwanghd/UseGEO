# Context Summary: CR-020

## Current Phase
Spec / Dev preparation.

## Completed Phases
- Confirmed local HEAD equals SSH remote master after CR-019.
- Read CR-020 scope from handoff and SEO-GEO task list.
- Read PRD-PARTNER-LEADS and PRD-OPS-ADMIN.
- Inspected lead admin/export code in `inc/admin.php` and lead capture code in `inc/forms.php`.

## Key Decisions
- Use PRD-PARTNER-LEADS as anchor.
- Keep export consent=yes and hot/warm invariant.
- Store auto quality and override separately in post meta.
- Notes use append log; no overwrite.
- Production upload remains Kevin confirmation boundary.

## Loaded Context
- `DeliverHQ/docs/HANDOFF-2026-06-22-usegeo-growth-batch.md`
- `DeliverHQ/docs/SEO-GEO-GROWTH-TASK-LIST.md`
- `DeliverHQ/docs/PRD.md`
- `wordpress/theme/usegeo/inc/admin.php`
- `wordpress/theme/usegeo/inc/forms.php`
