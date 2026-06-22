# Context Summary: CR-018

## Current Phase
Dev / verification.

## Completed Phases
- Read handoff and confirmed Git/SSH remote state.
- Read SEO-GEO growth task list lines 563-596.
- Read PRD, CONTENT-TEMPLATE, existing theme files.
- Production initial audit found sitemap OK, money pages included, posts count 24, but JSON-LD count 0 on sampled pages.

## Key Decisions
- Use WordPress core sitemap; do not add plugin.
- Add minimal theme JSON-LD because production currently has no JSON-LD.
- FAQ schema must mirror visible FAQ only.
- Article internal links are enforced at template level with 3 fixed money page links.
- Production upload/cache purge remains manual confirmation boundary.

## Loaded Context
- `DeliverHQ/docs/HANDOFF-2026-06-22-usegeo-growth-batch.md`
- `DeliverHQ/docs/SEO-GEO-GROWTH-TASK-LIST.md`
- `DeliverHQ/docs/PRD.md`
- `DeliverHQ/docs/CONTENT-TEMPLATE.md`
- `wordpress/theme/usegeo/header.php`
- `wordpress/theme/usegeo/functions.php`
- `wordpress/theme/usegeo/single.php`
- `wordpress/theme/usegeo/front-page.php`
- `wordpress/theme/usegeo/inc/assets.php`
