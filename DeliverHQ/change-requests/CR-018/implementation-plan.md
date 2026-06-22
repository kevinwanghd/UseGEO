# Implementation Plan: CR-018

## Current approach
1. Capture production audit for sitemap/robots/schema/internal links/title/meta.
2. Add `wordpress/theme/usegeo/inc/seo.php` for minimal safe JSON-LD.
3. Require `inc/seo.php` from `functions.php`.
4. Add article-template foundation links in `single.php` to guarantee links to all 3 money pages.
5. Add minimal styles in `assets/css/single.css`.
6. Add deterministic verifier under CR-018 evidence.
7. Run SpecGate, PreDevGate, local verifier, ReviewGate, QualityGate, package verification.

## Non-goals
- No fake review/rating schema.
- No SEO plugin dependency.
- No production upload without Kevin confirmation.
- No lead/sponsor/scanner behavior change.
