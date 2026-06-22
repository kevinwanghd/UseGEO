# Implementation Plan: CR-019

1. Extend sponsor slot option schema with `start_date` and `end_date`.
2. Add admin inputs for start/end dates.
3. Add date-window helper and hide expired/not-yet-started sponsor slots.
4. Add sponsor card data attributes for placement/url/date window.
5. Update rel to `noopener sponsored nofollow`.
6. Add `sponsor_click` and `sponsor_impression` events in `tracking.js`.
7. Add verifier for local and production checks.
8. Run gates, package ZIP, then wait for Kevin production upload confirmation.
