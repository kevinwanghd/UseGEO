#!/usr/bin/env python3
"""Verify CR-021 article page conversion redesign."""
from __future__ import annotations

import argparse
import json
import re
import urllib.request
from pathlib import Path
from typing import Any

CR_DIR = Path(__file__).resolve().parents[1]
ROOT = Path(__file__).resolve().parents[4]
EVIDENCE = CR_DIR / 'evidence'
SAMPLE_URL = 'https://usegeo.blog/geo-for-content-marketing-teams/'
MONEY_LINKS = ['/geo-checklist/', '/ai-visibility-tools/', '/best-geo-agencies/']


def read(rel: str) -> str:
    return (ROOT / rel).read_text(encoding='utf-8')


def add(checks: list[dict[str, Any]], name: str, passed: bool, **extra: Any) -> None:
    item = {'name': name, 'passed': bool(passed)}
    item.update(extra)
    checks.append(item)


def fetch(url: str) -> tuple[int, str, str]:
    sep = '&' if '?' in url else '?'
    req = urllib.request.Request(url + sep + 'cr021_verify=1', headers={'User-Agent': 'Hermes-UseGEO-CR021-Verifier/1.0', 'Accept': 'text/html,*/*'})
    with urllib.request.urlopen(req, timeout=35) as resp:
        return int(resp.status), resp.read().decode('utf-8', errors='replace'), resp.geturl()


def count_h1(html: str) -> int:
    return len(re.findall(r'<h1\b', html, flags=re.I))


def local() -> dict[str, Any]:
    checks: list[dict[str, Any]] = []
    single = read('wordpress/theme/usegeo/single.php')
    css = read('wordpress/theme/usegeo/assets/css/single.css')

    add(checks, 'duplicate_h1_strip_helper_present', 'usegeo_single_strip_duplicate_h1' in single and 'preg_replace' in single)
    add(checks, 'toc_h2_only', "'/<h2" in single and 'toc_items[]' in single and "<h([2-3])" not in single)
    add(checks, 'sponsor_slot_retained', "usegeo_render_sponsor_slot('article_sidebar')" in single)
    missing_money = [link for link in MONEY_LINKS if link not in single]
    add(checks, 'three_money_links_retained', not missing_money, missing=missing_money)
    add(checks, 'scanner_cta_retained', '/#scanner' in single and 'Run scanner' in single)
    add(checks, 'mid_cta_removed', 'usegeo-single-mid-cta' not in single and 'usegeo-single-bottom-cta' not in single)
    add(checks, 'new_article_shell_classes', all(x in single for x in ['usegeo-article-hero', 'usegeo-reader-rail', 'usegeo-article-action', 'usegeo-foundation-strip']))
    add(checks, 'css_rewritten_classes_present', all(x in css for x in ['.usegeo-article-hero', '.usegeo-reader-rail', '.usegeo-article-action', '.usegeo-foundation-strip']))
    add(checks, 'old_heavy_hero_removed', 'font-size: clamp(2.55rem, 7vw, 5.9rem)' not in css and 'box-shadow: 0 28px 80px' not in css)
    add(checks, 'no_em_dash_visible_copy', '—' not in single and '–' not in single)
    add(checks, 'no_fake_screenshot', 'fake screenshot' not in single.lower() and 'div-based' not in single.lower())
    syntax_errors = []
    for rel in ['wordpress/theme/usegeo/single.php', 'wordpress/theme/usegeo/assets/css/single.css']:
        text = read(rel)
        if rel.endswith('.php') and text.count('(') != text.count(')'):
            syntax_errors.append(f'{rel}:paren_count')
        if text.count('{') != text.count('}'):
            syntax_errors.append(f'{rel}:brace_count')
    add(checks, 'static_balanced_delimiters', not syntax_errors, errors=syntax_errors)

    out = {'mode': 'local', 'passed': all(c['passed'] for c in checks), 'checks': checks}
    (EVIDENCE / 'article-redesign-local-result.json').write_text(json.dumps(out, indent=2, ensure_ascii=False), encoding='utf-8')
    return out


def production() -> dict[str, Any]:
    checks: list[dict[str, Any]] = []
    status, html, final = fetch(SAMPLE_URL)
    headings = re.findall(r'<h([1-4])[^>]*>([\s\S]*?)</h\1>', html, flags=re.I)
    h1_count = count_h1(html)
    toc_has_long_faq = bool(re.search(r'usegeo-reader-toc[\s\S]{0,5000}What is the main GEO priority', html, flags=re.I))
    add(checks, 'sample_status_200', status == 200, status=status, final_url=final)
    add(checks, 'production_single_h1', h1_count == 1, h1_count=h1_count)
    add(checks, 'production_toc_not_polluted_by_faq', not toc_has_long_faq)
    add(checks, 'production_scanner_cta_present', '/#scanner' in html and 'Run scanner' in html)
    missing_money = [link for link in MONEY_LINKS if link not in html]
    add(checks, 'production_money_links_present', not missing_money, missing=missing_money)
    add(checks, 'production_new_classes_present', all(x in html for x in ['usegeo-article-hero', 'usegeo-reader-rail', 'usegeo-article-action', 'usegeo-foundation-strip']))
    add(checks, 'production_sponsor_slot_possible', 'data-usegeo-sponsor-slot' in html or 'usegeo-reader-rail' in html, note='Sponsor card appears only when a sponsor slot is active.')
    out = {'mode': 'production', 'passed': all(c['passed'] for c in checks), 'checks': checks, 'summary': {'heading_count': len(headings), 'h1_count': h1_count}}
    (EVIDENCE / 'article-redesign-production-result.json').write_text(json.dumps(out, indent=2, ensure_ascii=False), encoding='utf-8')
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
        print(f"CR-021 article redesign verification ({args.mode}): {'PASS' if out['passed'] else 'FAIL'}")
        for c in out['checks']:
            msg = c['name']
            if 'status' in c: msg += f" status={c['status']}"
            if 'h1_count' in c: msg += f" h1_count={c['h1_count']}"
            if c.get('missing'): msg += f" missing={c['missing']}"
            if c.get('errors'): msg += f" errors={c['errors']}"
            print(f"- {'PASS' if c['passed'] else 'FAIL'}: {msg}")
        evidence = EVIDENCE / ('article-redesign-local-result.json' if args.mode == 'local' else 'article-redesign-production-result.json')
        print(f'Evidence: {evidence}')
    return 0 if out['passed'] else 1


if __name__ == '__main__':
    raise SystemExit(main())
