# Timeline field design for UseGEO full assessment

## Goal
Add one required `timeline` field to the full assessment lead form so UseGEO can distinguish low-intent educational leads from leads that are more referral-ready.

## Scope
Only update these files:
- `wordpress/theme/usegeo/template-parts/scanner/scanner.php`
- `wordpress/theme/usegeo/functions.php`

## Form design
Add a required select field after `support_level` and before the submit button.

- Field name: `timeline`
- Label: `When would you realistically act on this?`
- Placeholder option: `Select one`
- Options:
  - `just_researching` → `Just researching`
  - `next_quarter` → `Planning in the next quarter`
  - `need_help_soon` → `Need help soon`
  - `not_sure_yet` → `Not sure yet`

## Backend behavior
Extend `usegeo_handle_lead_capture()` to:
- Read `timeline` from `POST`
- Sanitize it with `sanitize_key()`
- Validate it against a dedicated whitelist function
- Treat invalid or missing values the same as other required-field failures
- Save the accepted value to post meta with key `timeline`

## Label mapping
Add a dedicated label mapping function in `functions.php` for timeline values so admin UI renders readable labels instead of raw keys.

Expected mapping:
- `just_researching` → `Just researching`
- `next_quarter` → `Planning in the next quarter`
- `need_help_soon` → `Need help soon`
- `not_sure_yet` → `Not sure yet`

## Admin list view
Add a `Timeline` column to the UseGEO Leads admin list.

Display rules:
- Show readable labels, not raw keys
- `need_help_soon` uses a success/high-priority badge
- `next_quarter` uses an info/medium-priority badge
- `not_sure_yet` uses a warn badge
- `just_researching` renders as plain text

## Lead detail view
Add a `Timeline` row to the lead detail meta box.

Display rules:
- Show readable label, not raw key
- Keep visual treatment consistent with the existing `help_intent` and `support_level` display style

## Validation intent
Whitelist validation exists to prevent forged or malformed request values from being stored in lead data. Frontend selects limit normal input, but backend validation remains the source of truth.

## Error handling
No new status type is needed.
Invalid or missing timeline values should continue using the existing `lead_status=invalid` path and existing user-facing error message.

## Testing target
Manual verification is enough for this change:
1. Submit a valid form and confirm redirect success notice still appears
2. Confirm the new lead stores `timeline`
3. Confirm the admin list shows the readable timeline label
4. Confirm the lead detail page shows the readable timeline label
5. Confirm an invalid forged timeline value is rejected
