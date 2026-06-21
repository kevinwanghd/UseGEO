# Context Summary: CR-010 Expand Money Pages

## Current Phase
spec/design/pre-dev preparation. CR-010 expands three existing UseGEO WordPress money pages into long-form SEO/GEO commercial guides.

## Completed Phases
- Request drafted from `DeliverHQ/docs/SEO-GEO-GROWTH-TASK-LIST.md` and handoff context.
- Acceptance spec linked to `PRD-CONTENT-LIBRARY`.
- Design decision: content-only update; no theme UI change.

## Key Decisions
- Update existing WordPress pages for `/geo-checklist/`, `/ai-visibility-tools/`, and `/best-geo-agencies/`; do not create new slugs.
- Avoid fake Top 10 rankings for tools or agencies.
- Keep UseGEO positioned as learning content + Scanner + partner referral / sponsor / affiliate / lead resale.
- Do not commit or print production secrets.

## Loaded Context
- `DeliverHQ/docs/HANDOFF-2026-06-21-usegeo-growth.md`
- `DeliverHQ/docs/SEO-GEO-GROWTH-TASK-LIST.md`
- `DeliverHQ/docs/PRD.md` anchor `PRD-CONTENT-LIBRARY`
- Existing CR-007/CR-008/CR-009 page publishing outputs

## Verification Plan
- Local verifier checks word count, FAQ count, Scanner CTA count, internal links, and forbidden claims for generated HTML outputs.
- Production verifier fetches the three public URLs and runs the same checks on production HTML.
