# 2026-04-27 Final Manual QA Checklist

## Scope

This checklist closes the local planning side of `L9`.

It should be executed again on staging before public beta.

## Homepage

- Hero headline reads naturally and does not look like placeholder copy.
- Homepage modules appear in the intended order.
- CTA labels are consistent in tone.
- No obvious Chinese / English mixing remains in visible primary UI.
- Cards are aligned and excerpts do not look uneven.

## Articles

- Open at least 3 article pages.
- Confirm article title, excerpt, and sidebar CTA look intentional.
- Confirm CTA returns to homepage scanner correctly.
- Confirm no obviously machine-looking placeholder copy remains.

## Funnel

- Homepage scanner works from input to result.
- Lead gate form submits successfully.
- Community case submission form submits successfully.
- Both backend post types receive records correctly.

## Mobile

- Test homepage on a narrow viewport.
- Test article page on a narrow viewport.
- Confirm no horizontal overflow.
- Confirm CTA buttons remain tappable and readable.

## Content Quality

- No placeholder labels such as `final`, `test`, `demo`, or `coming soon` appear in critical user-facing copy unless intentionally retained.
- No broken punctuation, garbled text, or malformed labels appear in key sections.
- Footer, privacy, and cookie links render correctly.

## Analytics

- `dataLayer` exists
- key events fire
- GTM or GA4 script is loaded in staging

## Release Decision

Staging is ready for beta only when:

- homepage and article paths are stable
- lead capture works
- mail delivery status is known
- analytics script is present
- the site no longer feels like a test artifact
