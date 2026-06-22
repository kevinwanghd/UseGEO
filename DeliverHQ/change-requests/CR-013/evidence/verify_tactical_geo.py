#!/usr/bin/env python3
"""Verify CR-013 Tactical GEO Cluster locally or in production."""
from __future__ import annotations
import argparse, html, json, re, subprocess, urllib.request
from pathlib import Path
CR_DIR=Path(__file__).resolve().parents[1]
OUTPUTS=CR_DIR/'outputs'
ARTICLES=json.loads((OUTPUTS/'articles.json').read_text(encoding='utf-8'))
BASE='https://usegeo.blog'
TAG_RE=re.compile(r'<[^>]+>')
WORD_RE=re.compile(r"\b[A-Za-z][A-Za-z0-9'\-]*\b")
FORBIDDEN=[r'guarantee(?:d|s)?\s+(?:AI\s+)?(?:ranking|rankings|recommendation|recommendations)',r'precisely\s+track\s+all\s+AI\s+recommendations',r'SEO\s+is\s+dead',r'schema\s+(?:will|can)\s+(?:directly\s+)?(?:make|cause)\s+AI\s+(?:recommend|mention)']
SECRET_PATTERNS=[r'USEGEO_LLM_API_KEY\s*[,=]',r'Authorization:\s*Bearer\s+[A-Za-z0-9_\-.]+',r'Basic\s+[A-Za-z0-9+/=]{20,}']
MARKERS={
 'how-to-improve-entity-clarity-for-ai-search':['Entity clarity checklist','Homepage before and after'],
 'how-to-build-comparison-pages-for-geo':['Comparison page outline','What not to do'],
 'how-proof-signals-help-ai-recommend-your-brand':['Proof signal examples','testimonials'],
 'schema-markup-for-ai-visibility':['Organization schema','FAQ schema','Article schema'],
 'how-to-make-your-homepage-easier-for-llms-to-understand':['Fix the title, H1, and meta description','meta','examples'],
}
def strip_tags(s):
    s=re.sub(r'<script\b.*?</script>',' ',s,flags=re.I|re.S); s=re.sub(r'<style\b.*?</style>',' ',s,flags=re.I|re.S)
    return html.unescape(re.sub(r'\s+',' ',TAG_RE.sub(' ',s))).strip()
def fetch(url):
    full=url+('?cr013_verify=1' if '?' not in url else '&cr013_verify=1')
    js="""const url=process.argv[1];fetch(url,{headers:{'User-Agent':'Hermes-UseGEO-CR013-Verifier/1.0','Accept':'text/html'}}).then(async r=>console.log(JSON.stringify({status:r.status,body:await r.text()}))).catch(e=>{console.error(e&&e.stack||e);process.exit(1)});"""
    proc=subprocess.run(['node','-e',js,full],stdout=subprocess.PIPE,stderr=subprocess.PIPE,text=True,encoding='utf-8',errors='replace',timeout=45)
    if proc.returncode==0:
        d=json.loads(proc.stdout); return int(d['status']), d['body']
    req=urllib.request.Request(full,headers={'User-Agent':'Hermes-UseGEO-CR013-Verifier/1.0','Accept':'text/html'})
    with urllib.request.urlopen(req,timeout=30) as resp: return resp.status, resp.read().decode('utf-8',errors='replace')
def check(slug, source, mode, status=None):
    d=ARTICLES[slug]; text=strip_tags(source); words=len(WORD_RE.findall(text))
    faq=len(re.findall(r'<h[23][^>]*>\s*(?:Q[:.]\s*)?.*?\?</h[23]>',source,flags=re.I|re.S)); cta=len(re.findall(r'/#scanner|https://usegeo\.blog/#scanner',source,re.I))
    links=sum(1 for x in d['links'] if x in source); missing_markers=[m for m in MARKERS[slug] if m.lower() not in source.lower()]
    forbidden=[pat for pat in FORBIDDEN if re.search(pat,source,re.I)]; secret=[pat for pat in SECRET_PATTERNS if re.search(pat,source,re.I)]
    errs=[]
    if mode=='production' and status!=200: errs.append(f'HTTP status {status}, expected 200')
    if not 1200<=words<=1800: errs.append(f'word_count {words}, expected 1200-1800')
    if faq<4: errs.append(f'faq_count {faq}, expected >=4')
    if cta<1: errs.append(f'scanner_cta_count {cta}, expected >=1')
    if links<3: errs.append(f'internal_links {links}, expected >=3')
    if 'quick answer' not in source.lower(): errs.append('missing Quick answer')
    if 'common mistakes' not in source.lower(): errs.append('missing Common mistakes')
    if missing_markers: errs.append('missing markers: '+', '.join(missing_markers))
    if forbidden: errs.append('forbidden claim patterns: '+', '.join(forbidden))
    if secret: errs.append('secret-like patterns: '+', '.join(secret))
    return {'slug':slug,'wp_slug':d['wp_slug'],'mode':mode,'status':status,'word_count':words,'faq_count':faq,'scanner_cta_count':cta,'internal_link_count':links,'passed':not errs,'errors':errs}
def main():
    ap=argparse.ArgumentParser(); ap.add_argument('--mode',choices=['local','production'],required=True); ap.add_argument('--json',action='store_true'); args=ap.parse_args(); results=[]
    for slug,d in ARTICLES.items():
        if args.mode=='local':
            p=OUTPUTS/f'{slug}.html'; results.append(check(slug,p.read_text(encoding='utf-8'),args.mode) if p.exists() else {'slug':slug,'passed':False,'errors':[f'missing {p}']})
        else:
            try: status,src=fetch(f"{BASE}/{d['wp_slug']}/"); results.append(check(slug,src,args.mode,status))
            except Exception as e: results.append({'slug':slug,'wp_slug':d['wp_slug'],'mode':args.mode,'passed':False,'errors':[f'fetch failed: {e}']})
    passed=all(r.get('passed') for r in results); out={'passed':passed,'results':results}; ev=CR_DIR/'evidence'/('tactical-geo-local-result.json' if args.mode=='local' else 'tactical-geo-production-result.json'); ev.write_text(json.dumps(out,indent=2,ensure_ascii=False),encoding='utf-8')
    if args.json: print(json.dumps(out,indent=2,ensure_ascii=False))
    else:
        print(f'CR-013 tactical GEO verification ({args.mode}): '+('PASS' if passed else 'FAIL'))
        for r in results:
            print(f"- {r['slug']}: {'PASS' if r.get('passed') else 'FAIL'} | words={r.get('word_count')} faq={r.get('faq_count')} cta={r.get('scanner_cta_count')} links={r.get('internal_link_count')}")
            for err in r.get('errors',[]): print('  * '+err)
        print(f'Evidence: {ev}')
    return 0 if passed else 1
if __name__=='__main__': raise SystemExit(main())
