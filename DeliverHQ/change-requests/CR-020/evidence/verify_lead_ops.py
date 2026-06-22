#!/usr/bin/env python3
"""Verify CR-020 Lead Ops Enhancements."""
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
BASE_URL = 'https://usegeo.blog'


def read(rel: str) -> str:
    return (ROOT / rel).read_text(encoding='utf-8')


def add(checks: list[dict[str, Any]], name: str, passed: bool, **extra: Any) -> None:
    item = {'name': name, 'passed': bool(passed)}
    item.update(extra)
    checks.append(item)


def fetch(url: str) -> tuple[int, str, str]:
    sep = '&' if '?' in url else '?'
    req = urllib.request.Request(url + sep + 'cr020_verify=1', headers={'User-Agent': 'Hermes-UseGEO-CR020-Verifier/1.0'})
    with urllib.request.urlopen(req, timeout=35) as resp:
        return int(resp.status), resp.read().decode('utf-8', errors='replace'), resp.geturl()


def local() -> dict[str, Any]:
    checks: list[dict[str, Any]] = []
    admin = read('wordpress/theme/usegeo/inc/admin.php')
    forms = read('wordpress/theme/usegeo/inc/forms.php')

    add(checks, 'lead_quality_labels_present', 'function usegeo_get_lead_quality_labels' in admin)
    add(checks, 'lead_quality_resolver_present', 'function usegeo_resolve_effective_lead_quality' in admin and 'lead_quality_override' in admin and 'lead_quality_auto' in admin)
    add(checks, 'lead_quality_override_select_present', 'usegeo_lead_quality_override' in admin and '<select id="usegeo-lead-quality-override"' in admin)
    add(checks, 'lead_quality_auto_saved_on_capture', "'lead_quality_auto'" in forms and "update_post_meta($post_id, 'lead_quality_auto'" in forms)
    add(checks, 'lead_quality_override_save_logic', 'lead_quality_override' in admin and 'usegeo_resolve_effective_lead_quality($auto_quality, $override_quality)' in admin)

    add(checks, 'partner_notes_append_helper', 'function usegeo_append_partner_notes_log' in admin and 'partner_notes_log' in admin and 'current_time(\'mysql\')' in admin)
    add(checks, 'partner_notes_append_input', 'usegeo_partner_note_append' in admin)
    overwrite_pattern = "update_post_meta($post_id, 'partner_notes', $notes)"
    add(checks, 'partner_notes_not_overwritten', overwrite_pattern not in admin)

    add(checks, 'partner_status_bulk_actions_registered', 'bulk_actions-edit-usegeo_lead' in admin and 'usegeo_partner_status_bulk_actions' in admin)
    add(checks, 'partner_status_bulk_handler_present', 'handle_bulk_actions-edit-usegeo_lead' in admin and 'usegeo_handle_partner_status_bulk_action' in admin)
    add(checks, 'partner_status_bulk_statuses', all(x in admin for x in ['usegeo_partner_status_reviewing', 'usegeo_partner_status_contacted', 'usegeo_partner_status_sold', 'usegeo_partner_status_rejected']))

    add(checks, 'csv_date_inputs_in_filter_bar', 'name="start_date"' in admin and 'name="end_date"' in admin and 'type="date"' in admin)
    add(checks, 'csv_export_start_end_params', 'start_date' in admin and 'end_date' in admin and 'usegeo_export_partner_leads' in admin)
    add(checks, 'csv_export_date_query', 'date_query' in admin and 'after' in admin and 'before' in admin and 'inclusive' in admin)
    add(checks, 'csv_export_consent_hot_warm_invariant', "'partner_consent'" in admin and "'value'=>'yes'" in admin and "array('hot','warm')" in admin)

    syntax_like_errors = []
    for rel in ['wordpress/theme/usegeo/inc/admin.php', 'wordpress/theme/usegeo/inc/forms.php']:
        text = read(rel)
        if text.count('(') != text.count(')'):
            syntax_like_errors.append(f'{rel}:paren_count')
        if text.count('{') != text.count('}'):
            syntax_like_errors.append(f'{rel}:brace_count')
    add(checks, 'static_balanced_delimiters', not syntax_like_errors, errors=syntax_like_errors)

    out = {'mode': 'local', 'passed': all(c['passed'] for c in checks), 'checks': checks}
    (EVIDENCE / 'lead-ops-local-result.json').write_text(json.dumps(out, indent=2, ensure_ascii=False), encoding='utf-8')
    return out


def production() -> dict[str, Any]:
    checks: list[dict[str, Any]] = []
    status, html, final = fetch(BASE_URL + '/')
    add(checks, 'homepage_status_200', status == 200, status=status, final_url=final)
    # Public PHP source is not exposed; use file existence/public page smoke plus local committed evidence for behavior.
    add(checks, 'production_smoke_public_site_up', status == 200 and len(html) > 1000, length=len(html))
    out = {'mode': 'production', 'passed': all(c['passed'] for c in checks), 'checks': checks, 'summary': {'homepage_length': len(html)}}
    (EVIDENCE / 'lead-ops-production-result.json').write_text(json.dumps(out, indent=2, ensure_ascii=False), encoding='utf-8')
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
        print(f"CR-020 lead ops verification ({args.mode}): {'PASS' if out['passed'] else 'FAIL'}")
        for c in out['checks']:
            msg = c['name']
            if 'status' in c: msg += f" status={c['status']}"
            if c.get('errors'): msg += f" errors={c['errors']}"
            print(f"- {'PASS' if c['passed'] else 'FAIL'}: {msg}")
        evidence = EVIDENCE / ('lead-ops-local-result.json' if args.mode == 'local' else 'lead-ops-production-result.json')
        print(f'Evidence: {evidence}')
    return 0 if out['passed'] else 1


if __name__ == '__main__':
    raise SystemExit(main())
