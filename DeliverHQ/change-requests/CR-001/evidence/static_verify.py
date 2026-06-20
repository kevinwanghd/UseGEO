from pathlib import Path
import re
import subprocess
import sys
from collections import defaultdict

ROOT = Path(__file__).resolve().parents[4]


def run(cmd):
    result = subprocess.run(cmd, cwd=str(ROOT), text=True, stdout=subprocess.PIPE, stderr=subprocess.PIPE)
    if result.returncode != 0:
        print(result.stdout)
        print(result.stderr, file=sys.stderr)
        raise SystemExit(result.returncode)


def check_balanced_php():
    files = list((ROOT / 'wordpress/theme/usegeo/inc').glob('scanner-*.php')) + [
        ROOT / 'wordpress/theme/usegeo/inc/forms.php',
        ROOT / 'wordpress/theme/usegeo/inc/admin.php',
        ROOT / 'wordpress/theme/usegeo/inc/assets.php',
        ROOT / 'wordpress/theme/usegeo/functions.php',
        ROOT / 'wordpress/theme/usegeo/template-parts/scanner/scanner.php',
    ]
    for path in files:
        text = path.read_text(encoding='utf-8')
        balance = {'(': 0, '{': 0, '[': 0}
        for ch in text:
            if ch in balance:
                balance[ch] += 1
            elif ch == ')':
                balance['('] -= 1
            elif ch == '}':
                balance['{'] -= 1
            elif ch == ']':
                balance['['] -= 1
        bad = {k: v for k, v in balance.items() if v != 0}
        if bad:
            raise AssertionError(f'unbalanced tokens in {path}: {bad}')


def check_duplicate_functions():
    by_name = defaultdict(list)
    for path in (ROOT / 'wordpress/theme/usegeo/inc').glob('*.php'):
        text = path.read_text(encoding='utf-8')
        for match in re.finditer(r'function\s+([a-zA-Z0-9_]+)\s*\(', text):
            by_name[match.group(1)].append(str(path.relative_to(ROOT)))
    duplicates = {name: paths for name, paths in by_name.items() if len(paths) > 1}
    if duplicates:
        raise AssertionError(f'duplicate functions: {duplicates}')


def contains(path, needle):
    return needle in (ROOT / path).read_text(encoding='utf-8')


def check_contracts():
    checks = {
        'rest_route': contains('wordpress/theme/usegeo/inc/scanner-api.php', "register_rest_route('usegeo/v1', '/scan'"),
        'manual_redirect_ssrf': contains('wordpress/theme/usegeo/inc/scanner-fetch.php', "'redirection' => 0") and contains('wordpress/theme/usegeo/inc/scanner-fetch.php', 'usegeo_scan_absolute_url'),
        'private_ranges': all(contains('wordpress/theme/usegeo/inc/scanner-fetch.php', x) for x in ['10.0.0.0', '172.16.0.0', '192.168.0.0', '::1']),
        'five_scores': all(contains('wordpress/theme/usegeo/assets/js/scanner.js', x) for x in ['entity_clarity', 'content_structure', 'proof_signals', 'comparison_readiness', 'ai_extractability']),
        'hidden_fields': contains('wordpress/theme/usegeo/template-parts/scanner/scanner.php', 'data-usegeo-scan-total-score') and contains('wordpress/theme/usegeo/template-parts/scanner/scanner.php', 'data-usegeo-scan-findings-summary'),
        'evidence_ui': contains('wordpress/theme/usegeo/template-parts/scanner/scanner.php', 'data-usegeo-evidence') and contains('wordpress/theme/usegeo/assets/css/homepage.css', '.usegeo-scanner-evidence'),
        'lead_snapshot': contains('wordpress/theme/usegeo/inc/forms.php', 'usegeo_save_scan_snapshot_meta') and contains('wordpress/theme/usegeo/inc/forms.php', 'GEO Readiness Score'),
        'admin_columns': contains('wordpress/theme/usegeo/inc/admin.php', 'scan_total_score') and contains('wordpress/theme/usegeo/inc/admin.php', 'weakest_dimension'),
        'feature_flag': contains('wordpress/theme/usegeo/inc/scanner-api.php', 'USEGEO_REAL_SCANNER_ENABLED') and contains('wordpress/theme/usegeo/assets/js/scanner.js', 'realScannerEnabled'),
    }
    failed = [name for name, ok in checks.items() if not ok]
    if failed:
        raise AssertionError(f'contract checks failed: {failed}')


run(['node', '--check', 'wordpress/theme/usegeo/assets/js/scanner.js'])
check_balanced_php()
check_duplicate_functions()
check_contracts()
print('static verification passed')
