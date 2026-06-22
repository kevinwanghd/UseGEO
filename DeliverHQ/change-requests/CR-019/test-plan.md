# Test Plan: CR-019

## Local
Run:
`C:/Users/kevin/AppData/Local/Programs/Python/Python312/python.exe DeliverHQ/change-requests/CR-019/evidence/verify_sponsor_reporting.py --mode local`

Expected:
- sponsor click event present with placement + sponsor_url.
- sponsor impression event present with placement only.
- sponsor slot admin supports start_date/end_date.
- render helper respects active window.
- sponsor link rel includes noopener sponsored nofollow.

## Production after upload
Run:
`C:/Users/kevin/AppData/Local/Programs/Python/Python312/python.exe DeliverHQ/change-requests/CR-019/evidence/verify_sponsor_reporting.py --mode production`
