from pathlib import Path
import subprocess

ROOT = Path(__file__).resolve().parents[4]

subprocess.run(['node', '--check', 'wordpress/theme/usegeo/assets/js/scanner.js'], cwd=str(ROOT), check=True)

required = {
    'wordpress/theme/usegeo/inc/admin.php': [
        'usegeo_get_partner_status_labels',
        'usegeo_filter_partner_leads',
        'usegeo_render_partner_lead_filters',
        'usegeo_export_partner_leads',
        'partner_status',
        'partner_notes',
        'usegeo_export_partner_leads',
    ],
}

for rel, needles in required.items():
    text = (ROOT / rel).read_text(encoding='utf-8')
    for needle in needles:
        assert needle in text, f'{needle} missing in {rel}'

print('static verification passed')
