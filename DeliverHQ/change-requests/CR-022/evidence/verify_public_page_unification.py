#!/usr/bin/env python3
"""Verify CR-022 public page visual unification."""
from __future__ import annotations

import argparse
import json
import re
import subprocess
import urllib.request
from pathlib import Path
from typing import Any

CR_DIR = Path(__file__).resolve().parents[1]
ROOT = Path(__file__).resolve().parents[4]
EVIDENCE = CR_DIR / 'evidence'
BASE_URL = 'https://usegeo.blog'
MONEY_PATHS = ['/geo-checklist/', '/ai-visibility-tools/', '/best-geo-agencies/']
GENERIC_PATHS = ['/about/', '/articles/', '/cases/']


def read(rel: str) -> str:
    return (ROOT / rel).read_text(encoding='utf-8')


def add(checks: list[dict[str, Any]], name: str, passed: bool, **extra: Any) -> None:
    item = {'name': name, 'passed': bool(passed)}
    item.update(extra)
    checks.append(item)


def fetch(url: str) -> tuple[int, str, str]:
    sep = '&' if '?' in url else '?'
    target = url + sep + 'cr022_verify=1'
    req = urllib.request.Request(target, headers={'User-Agent': 'Hermes-UseGEO-CR022-Verifier/1.0', 'Accept': 'text/html,*/*'})
    try:
        with urllib.request.urlopen(req, timeout=35) as resp:
            return int(resp.status), resp.read().decode('utf-8', errors='replace'), resp.geturl()
    except Exception:
        node_code = """
const url = process.argv[1];
const r = await fetch(url, {headers: {'user-agent':'Hermes-UseGEO-CR022-Verifier/1.0','cache-control':'no-cache','pragma':'no-cache'}});
const t = await r.text();
process.stdout.write(JSON.stringify({status:r.status, url:r.url, text:t}));
"""
        result = subprocess.run(['node', '-e', node_code, target], cwd=str(ROOT), stdout=subprocess.PIPE, stderr=subprocess.PIPE, text=True, timeout=45)
        if result.returncode != 0:
            raise RuntimeError(result.stderr or 'node fetch failed')
        payload = json.loads(result.stdout)
        return int(payload['status']), payload['text'], payload['url']


def count_h1(html: str) -> int:
    return len(re.findall(r'<h1\b', html, flags=re.I))


def local() -> dict[str, Any]:
    checks: list[dict[str, Any]] = []
    page = read('wordpress/theme/usegeo/page.php')
    base = read('wordpress/theme/usegeo/assets/css/base.css')
    front = read('wordpress/theme/usegeo/front-page.php')

    add(checks, 'page_duplicate_h1_strip_helper', 'usegeo_page_strip_first_h1' in page and 'preg_replace' in page)
    add(checks, 'public_page_classes_present', all(x in page for x in ['usegeo-public-page', 'usegeo-public-page-hero', 'usegeo-public-page-content', 'usegeo-public-page-action', 'usegeo-public-page-foundation']))
    add(checks, 'public_page_css_present', all(x in base for x in ['.usegeo-public-page', '.usegeo-public-page-hero', '.usegeo-public-page-content', '.usegeo-public-page-action', '.usegeo-public-page-foundation']))
    missing_money = [m for m in MONEY_PATHS if m not in page]
    add(checks, 'page_money_links_present', not missing_money, missing=missing_money)
    add(checks, 'page_scanner_cta_present', '/#scanner' in page and 'Run scanner' in page)
    add(checks, 'front_page_structure_unchanged', "get_template_part('template-parts/hero/hero')" in front and "get_template_part('template-parts/scanner/scanner')" in front)
    add(checks, 'no_fake_screenshot_or_images', 'fake screenshot' not in page.lower() and 'picsum' not in page.lower() and '<img' not in page.lower())
    add(checks, 'no_em_dash_visible_copy', '—' not in page and '–' not in page)

    # CR-021 article redesign must stay valid in the same final package.
    cr021 = ROOT / 'DeliverHQ/change-requests/CR-021/evidence/verify_article_redesign.py'
    if cr021.exists():
        result = subprocess.run(['C:/Users/kevin/AppData/Local/Programs/Python/Python312/python.exe', str(cr021), '--mode', 'local'], cwd=str(ROOT), stdout=subprocess.PIPE, stderr=subprocess.PIPE, text=True, timeout=120)
        add(checks, 'cr021_article_redesign_still_passes', result.returncode == 0, stdout=result.stdout[-500:], stderr=result.stderr[-500:])
    else:
        add(checks, 'cr021_article_redesign_still_passes', False, error='CR-021 verifier missing')

    syntax_errors = []
    for rel in ['wordpress/theme/usegeo/page.php', 'wordpress/theme/usegeo/assets/css/base.css']:
        text = read(rel)
        if rel.endswith('.php') and text.count('(') != text.count(')'):
            syntax_errors.append(f'{rel}:paren_count')
        if text.count('{') != text.count('}'):
            syntax_errors.append(f'{rel}:brace_count')
    add(checks, 'static_balanced_delimiters', not syntax_errors, errors=syntax_errors)

    out = {'mode': 'local', 'passed': all(c['passed'] for c in checks), 'checks': checks}
    (EVIDENCE / 'public-page-unification-local-result.json').write_text(json.dumps(out, indent=2, ensure_ascii=False), encoding='utf-8')
    return out


def production() -> dict[str, Any]:
    checks: list[dict[str, Any]] = []
    page_results = []
    for path in ['/', *MONEY_PATHS, *GENERIC_PATHS, '/geo-for-content-marketing-teams/']:
        status, html, final = fetch(BASE_URL + path)
        result = {
            'path': path,
            'status': status,
            'final_url': final,
            'h1_count': count_h1(html),
            'has_public_page': 'usegeo-public-page' in html,
            'has_article_page': 'usegeo-article-hero' in html,
            'has_scanner': '/#scanner' in html,
            'money_links': [m for m in MONEY_PATHS if m in html],
        }
        page_results.append(result)
    add(checks, 'all_sample_pages_200', all(r['status'] == 200 for r in page_results), pages=page_results)
    money = [r for r in page_results if r['path'] in MONEY_PATHS]
    generic = [r for r in page_results if r['path'] in GENERIC_PATHS]
    article = [r for r in page_results if r['path'] == '/geo-for-content-marketing-teams/'][0]
    home = [r for r in page_results if r['path'] == '/'][0]
    add(checks, 'money_pages_single_h1', all(r['h1_count'] == 1 for r in money), pages=money)
    add(checks, 'generic_pages_public_classes', all(r['has_public_page'] for r in generic), pages=generic)
    add(checks, 'money_pages_public_classes', all(r['has_public_page'] for r in money), pages=money)
    add(checks, 'money_pages_keep_links', all(len(r['money_links']) >= 3 and r['has_scanner'] for r in money), pages=money)
    add(checks, 'article_redesign_deployed', article['h1_count'] == 1 and article['has_article_page'], article=article)
    add(checks, 'homepage_smoke_200', home['status'] == 200 and home['h1_count'] >= 1, home=home)
    out = {'mode': 'production', 'passed': all(c['passed'] for c in checks), 'checks': checks, 'summary': {'pages': page_results}}
    (EVIDENCE / 'public-page-unification-production-result.json').write_text(json.dumps(out, indent=2, ensure_ascii=False), encoding='utf-8')
    return out


def main() -> int:
    ap = argparse.ArgumentParser()
    ap.add_argument('--mode', choices=['local', 'production'], required=True)
    ap.add_argument('--json', action='store_true')
    args = ap.parse_args()
    out = local() if args.mode == 'local' else production()
    if args.json:
        print(json.dumps(out, indent=2, ensure_ascii=False))
    else:
        print(f"CR-022 public page unification verification ({args.mode}): {'PASS' if out['passed'] else 'FAIL'}")
        for c in out['checks']:
            msg = c['name']
            if c.get('missing'): msg += f" missing={c['missing']}"
            if c.get('errors'): msg += f" errors={c['errors']}"
            print(f"- {'PASS' if c['passed'] else 'FAIL'}: {msg}")
        evidence = EVIDENCE / ('public-page-unification-local-result.json' if args.mode == 'local' else 'public-page-unification-production-result.json')
        print(f'Evidence: {evidence}')
    return 0 if out['passed'] else 1


if __name__ == '__main__':
    raise SystemExit(main())
