from pathlib import Path
import subprocess

ROOT = Path(__file__).resolve().parents[4]
subprocess.run(['node', '--check', 'wordpress/theme/usegeo/assets/js/scanner.js'], cwd=str(ROOT), check=True)
required = {
    'wordpress/theme/usegeo/inc/admin.php': ['usegeo_render_sponsor_slot','usegeo_register_sponsor_admin_page','usegeo_sponsor_slots','rel="noopener sponsored"'],
    'wordpress/theme/usegeo/front-page.php': ["usegeo_render_sponsor_slot('homepage')"],
    'wordpress/theme/usegeo/template-parts/scanner/scanner.php': ["usegeo_render_sponsor_slot('scanner_result')"],
    'wordpress/theme/usegeo/single.php': ["usegeo_render_sponsor_slot('article_sidebar')"],
    'wordpress/theme/usegeo/assets/css/homepage.css': ['.usegeo-sponsor-card'],
}
for rel, needles in required.items():
    text = (ROOT / rel).read_text(encoding='utf-8')
    for needle in needles:
        assert needle in text, f'{needle} missing in {rel}'
print('static verification passed')
