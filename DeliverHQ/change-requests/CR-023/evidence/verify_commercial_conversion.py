#!/usr/bin/env python3
from __future__ import annotations
import argparse,json,re,subprocess,urllib.request
from pathlib import Path
from typing import Any
CR_DIR=Path(__file__).resolve().parents[1]
ROOT=Path(__file__).resolve().parents[4]
EVIDENCE=CR_DIR/'evidence'
BASE='https://usegeo.blog'
ROUTES=['/ai-visibility-audit/','/sample-report/','/partners/']

def read(rel): return (ROOT/rel).read_text(encoding='utf-8')
def add(ch,name,passed,**extra):
    d={'name':name,'passed':bool(passed)}; d.update(extra); ch.append(d)
def fetch(url):
    target=url+('&' if '?' in url else '?')+'cr023_verify=1'
    code="""const u=process.argv[1]; const r=await fetch(u,{headers:{'cache-control':'no-cache','pragma':'no-cache','user-agent':'Hermes-UseGEO-CR023-Verifier/1.0'}}); const t=await r.text(); process.stdout.write(JSON.stringify({status:r.status,url:r.url,text:t}));"""
    r=subprocess.run(['node','-e',code,target],cwd=str(ROOT),stdout=subprocess.PIPE,stderr=subprocess.PIPE,text=True,timeout=45)
    if r.returncode: raise RuntimeError(r.stderr)
    p=json.loads(r.stdout); return int(p['status']),p['text'],p['url']

def local():
    ch=[]
    files={
      'functions': read('wordpress/theme/usegeo/functions.php'),
      'commercial': read('wordpress/theme/usegeo/inc/commercial-pages.php') if (ROOT/'wordpress/theme/usegeo/inc/commercial-pages.php').exists() else '',
      'front': read('wordpress/theme/usegeo/front-page.php'),
      'loop': read('wordpress/theme/usegeo/template-parts/conversion/commercial-loop.php') if (ROOT/'wordpress/theme/usegeo/template-parts/conversion/commercial-loop.php').exists() else '',
      'base': read('wordpress/theme/usegeo/assets/css/base.css'),
      'homecss': read('wordpress/theme/usegeo/assets/css/homepage.css'),
    }
    add(ch,'commercial_module_required', "inc/commercial-pages.php" in files['functions'])
    add(ch,'routes_registered', all(x.strip('/') in files['commercial'] for x in ROUTES) and 'template_redirect' in files['commercial'])
    add(ch,'route_renderers_present', all(x in files['commercial'] for x in ['usegeo_render_ai_visibility_audit_page','usegeo_render_sample_report_page','usegeo_render_partners_page']))
    add(ch,'commercial_copy_markers', all(x in files['commercial'] for x in ['explicit consent','sample report','partner lead','Sponsor inventory','No AI ranking promises']))
    add(ch,'no_bad_claims', not re.search(r'guarantee(?:d|s)?\s+(AI\s+)?ranking|we deliver full GEO', files['commercial'], re.I))
    add(ch,'homepage_module_added', "template-parts/conversion/commercial-loop" in files['front'] and 'usegeo-commercial-loop' in files['loop'])
    add(ch,'css_present', 'usegeo-commercial-page' in files['base'] and 'usegeo-commercial-loop' in files['homecss'])
    add(ch,'scanner_ctas_present', files['commercial'].count('/#scanner')>=2 and ('/#scanner' in files['loop'] or '/ai-visibility-audit/' in files['loop']))
    add(ch,'no_external_side_effects', not any(x in files['commercial'] for x in ['wp_remote_post','wp_mail(','admin_post_','curl_']))
    errs=[]
    for rel in ['wordpress/theme/usegeo/functions.php','wordpress/theme/usegeo/inc/commercial-pages.php','wordpress/theme/usegeo/front-page.php','wordpress/theme/usegeo/template-parts/conversion/commercial-loop.php']:
        p=ROOT/rel
        if p.exists():
            t=p.read_text(encoding='utf-8')
            if t.count('(')!=t.count(')'): errs.append(rel+':paren')
            if t.count('{')!=t.count('}'): errs.append(rel+':brace')
    add(ch,'static_balanced_delimiters',not errs,errors=errs)
    out={'mode':'local','passed':all(c['passed'] for c in ch),'checks':ch}
    (EVIDENCE/'commercial-conversion-local-result.json').write_text(json.dumps(out,indent=2,ensure_ascii=False),encoding='utf-8')
    return out

def production():
    ch=[]; pages=[]
    for path in ['/',*ROUTES]:
        s,h,u=fetch(BASE+path)
        pages.append({'path':path,'status':s,'final_url':u,'has_commercial':'usegeo-commercial' in h,'has_scanner':'/#scanner' in h,'has_consent':'consent' in h.lower(),'has_no_ranking':'No AI ranking promises' in h,'h1_count':len(re.findall(r'<h1\b',h,re.I))})
    route_pages=[p for p in pages if p['path'] in ROUTES]; home=pages[0]
    add(ch,'routes_200', all(p['status']==200 for p in route_pages), pages=route_pages)
    add(ch,'routes_have_commercial_classes', all(p['has_commercial'] for p in route_pages), pages=route_pages)
    add(ch,'routes_have_scanner_and_consent', all(p['has_scanner'] and p['has_consent'] for p in route_pages), pages=route_pages)
    add(ch,'routes_have_no_ranking_copy', all(p['has_no_ranking'] for p in route_pages), pages=route_pages)
    add(ch,'homepage_commercial_module', home['status']==200 and home['has_commercial'] and home['has_scanner'], home=home)
    out={'mode':'production','passed':all(c['passed'] for c in ch),'checks':ch,'summary':{'pages':pages}}
    (EVIDENCE/'commercial-conversion-production-result.json').write_text(json.dumps(out,indent=2,ensure_ascii=False),encoding='utf-8')
    return out

def main():
    ap=argparse.ArgumentParser(); ap.add_argument('--mode',choices=['local','production'],required=True); ap.add_argument('--json',action='store_true'); a=ap.parse_args()
    out=local() if a.mode=='local' else production()
    if a.json: print(json.dumps(out,indent=2,ensure_ascii=False))
    else:
        print(f"CR-023 commercial conversion verification ({a.mode}): {'PASS' if out['passed'] else 'FAIL'}")
        for c in out['checks']: print(f"- {'PASS' if c['passed'] else 'FAIL'}: {c['name']}")
        print('Evidence:', EVIDENCE/('commercial-conversion-local-result.json' if a.mode=='local' else 'commercial-conversion-production-result.json'))
    return 0 if out['passed'] else 1
if __name__=='__main__': raise SystemExit(main())
