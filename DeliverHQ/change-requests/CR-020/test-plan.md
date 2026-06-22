# Test Plan: CR-020

Run local verifier:
`C:/Users/kevin/AppData/Local/Programs/Python/Python312/python.exe DeliverHQ/change-requests/CR-020/evidence/verify_lead_ops.py --mode local`

Expected: lead_quality auto/override split, partner_status bulk action, CSV date filters, partner notes append log.

Production after upload:
`C:/Users/kevin/AppData/Local/Programs/Python/Python312/python.exe DeliverHQ/change-requests/CR-020/evidence/verify_lead_ops.py --mode production`
