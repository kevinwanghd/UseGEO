# Test Plan: CR-018

## Local verification
Run:
`C:/Users/kevin/AppData/Local/Programs/Python/Python312/python.exe DeliverHQ/change-requests/CR-018/evidence/verify_seo_geo_foundation.py --mode local`

Expected:
- JSON-LD hook and schema builders present.
- Forbidden fake schema keywords absent.
- `single.php` contains all 3 money page links.
- `functions.php` requires `inc/seo.php`.

## Production audit
Run:
`C:/Users/kevin/AppData/Local/Programs/Python/Python312/python.exe DeliverHQ/change-requests/CR-018/evidence/verify_seo_geo_foundation.py --mode production-audit`

Expected before deployment:
- sitemap/robots checks pass.
- current production schema checks may report JSON-LD gap.

Expected after deployment:
- sampled home/money/article pages include JSON-LD.
