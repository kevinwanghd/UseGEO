# Test Plan: CR-010 Expand Money Pages

## Local content validation
Run:

```bash
/c/Users/kevin/AppData/Local/Programs/Python/Python312/python.exe DeliverHQ/change-requests/CR-010/evidence/verify_money_pages.py --mode local
```

Checks:
- 3 required output HTML files exist.
- Each page has 2500–3500 English words.
- Each page has at least 5 FAQ items.
- Each page has at least 2 Scanner CTAs.
- Each page links to the other two money pages.
- Forbidden claims are absent.

## Production validation
Run:

```bash
/c/Users/kevin/AppData/Local/Programs/Python/Python312/python.exe DeliverHQ/change-requests/CR-010/evidence/verify_money_pages.py --mode production
```

Checks:
- Production URLs return HTTP 200.
- Production HTML passes the same content checks.
