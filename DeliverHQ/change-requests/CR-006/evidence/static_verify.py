from pathlib import Path
import subprocess
ROOT = Path(__file__).resolve().parents[4]
subprocess.run(['node', '--check', 'wordpress/theme/usegeo/assets/js/scanner.js'], cwd=str(ROOT), check=True)
required = {
    'wordpress/theme/usegeo/template-parts/content-hub/seo-cluster.php': ['Generative Engine Optimization Checklist','AI Visibility Tools','Best GEO Agencies','/geo-checklist/','/ai-visibility-tools/','/best-geo-agencies/','/#scanner'],
    'wordpress/theme/usegeo/front-page.php': ["content-hub/seo-cluster"],
    'wordpress/theme/usegeo/assets/css/homepage.css': ['.usegeo-seo-cluster','.usegeo-seo-cluster-card'],
}
for rel, needles in required.items():
    text=(ROOT/rel).read_text(encoding='utf-8')
    for needle in needles:
        assert needle in text, f'{needle} missing in {rel}'
print('static verification passed')
