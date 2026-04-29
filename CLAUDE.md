# UseGEO Agent Rules

## Scope

These rules apply to all development agents working in this repository.

## Product Direction

- This project is built **WordPress-first**
- Use a custom lightweight theme
- Prefer WordPress native templates and assets
- Do not introduce a heavy frontend framework for MVP

## Runtime Code Placement

- Runtime code goes in `wordpress/theme/usegeo`
- Reusable page modules go in `wordpress/theme/usegeo/template-parts`
- Shared CSS and JS go in `wordpress/theme/usegeo/assets`
- Do not place production HTML/CSS/JS in the repository root

## Plugin Boundary

- Do not create standalone plugins for MVP unless explicitly requested
- Keep the GEO scanner and ROI calculator inside the theme for now
- Only consider plugin extraction if reuse or configuration complexity clearly justifies it

## Coding Rules

- Keep page templates thin
- Put reusable sections into `template-parts`
- Use native WordPress enqueue functions for assets
- Prefer simple, readable PHP, CSS, and vanilla JS
- Avoid unnecessary abstractions

## Git Rules

- Use clear branch names like `feature/<topic>`, `fix/<topic>`, `docs/<topic>`
- Use commit messages like `feat(scope): summary`
- Do not bundle unrelated changes into one commit

## Delivery Rules

- Prioritize homepage hero, GEO scanner, lead capture, and article CTA flow
- Desktop and mobile must both work before claiming completion
- Do not claim success without verifying the main conversion path
