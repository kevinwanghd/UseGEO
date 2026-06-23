#!/usr/bin/env python3
from __future__ import annotations
import argparse,json,re,subprocess
from pathlib import Path
CR_DIR=Path(__file__).resolve().parents[1]
ROOT=Path(__file__).resolve().parents[4]
EVIDENCE=CR_DIR/'evidence'
BASE='https://usegeo.blog'
ROUTES=['/ai-visibility-audit/','/sample-report/','/partners/']
def read(rel): return (ROOT/rel).read_text(encoding='utf-8')
def add(ch,n,p,**e): d={'name':n,'passed':bool(p)}; d.update(e); ch.append(d)
def fetch(url):
    code="""const u=process.argv[1]; const r=await fetch(u+'?cr024_verify='+Date.now(),{headers:{'cache-control':'no-cache','pragma':'no-cache'}}); const t=await r.text(); process.stdout.write(JSON.stringify({status:r.status,url:r.url,text:t}));"""
    r=subprocess.run(['node','-e',code,url],cwd=str(ROOT),stdout=subprocess.PIPE,stderr=subprocess.PIPE,text=True,timeout=45)
    if r.returncode: raise RuntimeError(r.stderr)
    p=json.loads(r.stdout); return p['status'],p['text'],p['url']
def local():
    ch=[]; commercial=read('wordpress/theme/usegeo/inc/commercial-pages.php'); loop=read('wordpress/theme/usegeo/template-parts/conversion/commercial-loop.php'); base=read('wordpress/theme/usegeo/assets/css/base.css'); home=read('wordpress/theme/usegeo/assets/css/homepage.css')
    add(ch,'audit_input_present','usegeo-audit-input' in commercial and 'Website URL' in commercial and 'Run scanner' in commercial)
    add(ch,'audit_preview_present','usegeo-audit-preview' in commercial and 'Readiness preview' in commercial)
    add(ch,'report_mockup_present','usegeo-report-mockup' in commercial and 'Dimensions' in commercial and 'Evidence table' in commercial and 'Partner handoff' in commercial)
    add(ch,'partner_packages_present','usegeo-partner-packages' in commercial and 'Sponsor beta' in commercial and 'Lead referral beta' in commercial)
    add(ch,'lead_object_present','usegeo-lead-object' in commercial and 'scanner snapshot' in commercial and 'partner_consent = yes' in commercial)
    add(ch,'homepage_loop_upgraded','Scan' in loop and 'Report' in loop and 'Match' in loop and 'usegeo-commercial-loop__path' in loop)
    add(ch,'css_upgrade_present',all(x in base+home for x in ['usegeo-audit-input','usegeo-audit-preview','usegeo-report-mockup','usegeo-partner-packages','usegeo-lead-object','usegeo-commercial-loop__path']))
    add(ch,'no_bad_claims',not re.search(r'guarantee(?:d|s)?\s+(AI\s+)?ranking|4x growth|364%', commercial, re.I))
    add(ch,'no_external_side_effects',not any(x in commercial for x in ['wp_remote_post','wp_mail(','admin_post_','stripe','payment']))
    errs=[]
    for rel in ['wordpress/theme/usegeo/inc/commercial-pages.php','wordpress/theme/usegeo/template-parts/conversion/commercial-loop.php']:
      t=read(rel)
      if t.count('(')!=t.count(')'): errs.append(rel+':paren')
      if t.count('{')!=t.count('}'): errs.append(rel+':brace')
    add(ch,'static_balanced_delimiters',not errs,errors=errs)
    out={'mode':'local','passed':all(c['passed'] for c in ch),'checks':ch}; (EVIDENCE/'commercial-design-local-result.json').write_text(json.dumps(out,indent=2,ensure_ascii=False),encoding='utf-8'); return out
def production():
    ch=[]; pages=[]
    for path in ['/',*ROUTES]:
      s,h,u=fetch(BASE+path); pages.append({'path':path,'status':s,'url':u,'audit_input':'usegeo-audit-input' in h,'audit_preview':'usegeo-audit-preview' in h,'report':'usegeo-report-mockup' in h,'packages':'usegeo-partner-packages' in h,'lead_object':'usegeo-lead-object' in h,'loop':'usegeo-commercial-loop__path' in h,'no_bad':not re.search(r'guarantee(?:d|s)?\s+(AI\s+)?ranking|4x growth|364%',h,re.I)})
    audit=[p for p in pages if p['path']=='/ai-visibility-audit/'][0]; sample=[p for p in pages if p['path']=='/sample-report/'][0]; partners=[p for p in pages if p['path']=='/partners/'][0]; home=pages[0]
    add(ch,'routes_200',all(p['status']==200 for p in pages),pages=pages)
    add(ch,'audit_page_upgraded',audit['audit_input'] and audit['audit_preview'],audit=audit)
    add(ch,'sample_report_upgraded',sample['report'],sample=sample)
    add(ch,'partners_page_upgraded',partners['packages'] and partners['lead_object'],partners=partners)
    add(ch,'homepage_loop_upgraded',home['loop'],home=home)
    add(ch,'no_bad_claims_production',all(p['no_bad'] for p in pages),pages=pages)
    out={'mode':'production','passed':all(c['passed'] for c in ch),'checks':ch,'summary':{'pages':pages}}; (EVIDENCE/'commercial-design-production-result.json').write_text(json.dumps(out,indent=2,ensure_ascii=False),encoding='utf-8'); return out
def main():
    ap=argparse.ArgumentParser(); ap.add_argument('--mode',choices=['local','production'],required=True); ap.add_argument('--json',action='store_true'); a=ap.parse_args(); out=local() if a.mode=='local' else production()
    if a.json: print(json.dumps(out,indent=2,ensure_ascii=False))
    else:
      print(f"CR-024 commercial design verification ({a.mode}): {'PASS' if out['passed'] else 'FAIL'}")
      for c in out['checks']: print(f"- {'PASS' if c['passed'] else 'FAIL'}: {c['name']}")
      print('Evidence:',EVIDENCE/('commercial-design-local-result.json' if a.mode=='local' else 'commercial-design-production-result.json'))
    return 0 if out['passed'] else 1
if __name__=='__main__': raise SystemExit(main())
