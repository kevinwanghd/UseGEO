from pathlib import Path
import subprocess

ROOT = Path(__file__).resolve().parents[4]

subprocess.run(['node', '--check', 'wordpress/theme/usegeo/assets/js/scanner.js'], cwd=str(ROOT), check=True)

required = {
    'wordpress/theme/usegeo/template-parts/scanner/scanner.php': [
        'name="partner_consent"',
        'name="budget_range"',
        'name="region"',
        'Request expert follow-up',
    ],
    'wordpress/theme/usegeo/inc/forms.php': [
        'usegeo_calculate_lead_quality',
        "update_post_meta($post_id, 'partner_consent'",
        "update_post_meta($post_id, 'lead_quality'",
        'Partner Consent:',
        'Lead Quality:',
    ],
    'wordpress/theme/usegeo/inc/admin.php': [
        "partner_consent",
        "lead_quality",
        "budget_range",
        "Region",
    ],
}

for rel, needles in required.items():
    text = (ROOT / rel).read_text(encoding='utf-8')
    for needle in needles:
        assert needle in text, f'{needle} missing in {rel}'

print('static verification passed')
