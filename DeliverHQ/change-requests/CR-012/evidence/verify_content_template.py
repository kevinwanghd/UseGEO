#!/usr/bin/env python3
"""Verify CR-012 UseGEO content template artifacts."""
from __future__ import annotations

import argparse
import json
import re
from pathlib import Path

CR_DIR = Path(__file__).resolve().parents[1]
ROOT = CR_DIR.parents[2]
TEMPLATE_DOC = ROOT / 'DeliverHQ/docs/CONTENT-TEMPLATE.md'
OUTPUTS = CR_DIR / 'outputs'
EVIDENCE = CR_DIR / 'evidence/content-template-result.json'

REQUIRED_BRIEF_FIELDS = [
    'primary_keyword',
    'secondary_keywords',
    'search_intent',
    'target_reader',
    'reader_problem',
    'related_money_page',
    'scanner_cta',
    'outline',
    'faq_items',
    'internal_links',
    'partner_angle',
]
REQUIRED_TEMPLATE_PHRASES = [
    'H1',
    'Opening 100 words',
    'Quick answer',
    'Main sections',
    'Checklist / criteria',
    'Example',
    'Common mistakes',
    'FAQ',
    'Next-step CTA',
]
REQUIRED_SKELETON_PHRASES = [
    '{{H1}}',
    'Quick answer',
    'Main question',
    'Checklist or criteria',
    'Example or before/after scenario',
    'Common mistakes',
    'Related reading and next steps',
    'FAQ',
    '/#scanner',
]
REQUIRED_CHECKLIST_PHRASES = [
    'Article brief completed',
    'Opening 100 words',
    'Quick answer',
    'At least 3 internal links',
    'At least 1 Scanner CTA',
    'FAQ has 4-7 visible items',
    'Forbidden claims absent',
    'Local verifier passes',
    'Production verifier passes',
]
REQUIRED_SCHEMA_PHRASES = [
    'visible in the article body',
    'Do not add fake author credentials',
    'Do not add ratings/reviews',
    'browser-rendered page or Rich Results Test',
]
FORBIDDEN = [
    r'guarantee(?:d|s)?\s+(?:AI\s+)?(?:ranking|rankings|recommendation|recommendations)',
    r'precisely\s+track\s+all\s+AI\s+recommendations',
    r'SEO\s+is\s+dead',
]


def read(path: Path) -> str:
    return path.read_text(encoding='utf-8')


def check_contains(name: str, text: str, phrases: list[str]) -> dict:
    missing = [p for p in phrases if p.lower() not in text.lower()]
    return {'name': name, 'passed': not missing, 'missing': missing}


def main() -> int:
    ap = argparse.ArgumentParser()
    ap.add_argument('--mode', choices=['local'], default='local')
    args = ap.parse_args()

    checks = []
    files = {
        'template_doc': TEMPLATE_DOC,
        'brief_template': OUTPUTS / 'article-brief-template.yml',
        'html_skeleton': OUTPUTS / 'article-html-skeleton.html',
        'publishing_checklist': OUTPUTS / 'publishing-checklist.md',
        'schema_plan': OUTPUTS / 'faq-jsonld-plan.md',
    }
    for name, path in files.items():
        checks.append({'name': f'{name}_exists', 'passed': path.exists(), 'path': str(path)})

    if TEMPLATE_DOC.exists():
        template = read(TEMPLATE_DOC)
        checks.append(check_contains('template_required_brief_fields', template, REQUIRED_BRIEF_FIELDS))
        checks.append(check_contains('template_required_structure', template, REQUIRED_TEMPLATE_PHRASES))
        checks.append(check_contains('template_link_cta_rules', template, ['at least 3 internal links', 'Scanner CTA', '/#scanner', 'No promised AI ranking', 'visible FAQ content']))
        forbidden_hits = [pat for pat in FORBIDDEN if re.search(pat, template, flags=re.I)]
        # Allow forbidden phrases when they appear as explicit prohibitions.
        unsafe_hits = []
        for pat in forbidden_hits:
            for match in re.finditer(pat, template, flags=re.I):
                ctx = template[max(0, match.start()-80):match.end()+80].lower()
                if not any(marker in ctx for marker in ['no ', 'do not', 'forbidden', '不得', '不允许']):
                    unsafe_hits.append(pat)
                    break
        checks.append({'name': 'template_forbidden_claims_safe', 'passed': not unsafe_hits, 'unsafe_hits': unsafe_hits})

    if files['brief_template'].exists():
        brief = read(files['brief_template'])
        checks.append(check_contains('brief_fields', brief, REQUIRED_BRIEF_FIELDS))
        link_count = len(re.findall(r'^\s*-\s+"?/', brief, flags=re.M))
        checks.append({'name': 'brief_internal_links_count', 'passed': link_count >= 3, 'count': link_count, 'expected_min': 3})

    if files['html_skeleton'].exists():
        skeleton = read(files['html_skeleton'])
        checks.append(check_contains('html_skeleton_structure', skeleton, REQUIRED_SKELETON_PHRASES))
        faq_items = len(re.findall(r'usegeo-faq-item', skeleton))
        checks.append({'name': 'html_skeleton_faq_items', 'passed': faq_items >= 4, 'count': faq_items, 'expected_min': 4})

    if files['publishing_checklist'].exists():
        checklist = read(files['publishing_checklist'])
        checks.append(check_contains('publishing_checklist_items', checklist, REQUIRED_CHECKLIST_PHRASES))

    if files['schema_plan'].exists():
        schema = read(files['schema_plan'])
        checks.append(check_contains('schema_plan_safety', schema, REQUIRED_SCHEMA_PHRASES))
        checks.append({'name': 'schema_plan_visible_faq_only', 'passed': 'Only emit FAQ schema for FAQ content visible' in schema, 'expected': 'visible FAQ only'})

    passed = all(c.get('passed') for c in checks)
    output = {'mode': args.mode, 'passed': passed, 'checks': checks}
    EVIDENCE.write_text(json.dumps(output, indent=2, ensure_ascii=False), encoding='utf-8')
    print(f"CR-012 content template verification ({args.mode}): {'PASS' if passed else 'FAIL'}")
    for c in checks:
        status = 'PASS' if c.get('passed') else 'FAIL'
        msg = c['name']
        if c.get('missing'):
            msg += ' missing=' + ', '.join(c['missing'])
        if 'count' in c:
            msg += f" count={c['count']}"
        print(f'- {status}: {msg}')
    print(f'Evidence: {EVIDENCE}')
    return 0 if passed else 1

if __name__ == '__main__':
    raise SystemExit(main())
