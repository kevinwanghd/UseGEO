#!/usr/bin/env python3
"""Verify CR-011 GEO Basics Cluster locally or in production."""
from __future__ import annotations
import argparse, html, json, re, subprocess, urllib.request
from pathlib import Path

CR_DIR=Path(__file__).resolve().parents[1]
OUTPUTS=CR_DIR/'outputs'
ARTICLES=json.loads((OUTPUTS/'articles.json').read_text(encoding='utf-8'))
BASE='https://usegeo.blog'
TAG_RE=re.compile(r'<[^>]+>')
WORD_RE=re.compile(r"\b[A-Za-z][A-Za-z0-9'\-]*\b")
FORBIDDEN=[
    r'guarantee(?:d|s)?\s+(?:AI\s+)?(?:ranking|rankings|recommendation|recommendations)',
    r'precisely\s+track\s+all\s+AI\s+recommendations',
    r'control\s+AI\s+(?:answers|rankings|recommendations)',
    r'SEO\s+is\s+dead',
]
SECRET_PATTERNS=[
    r'USEGEO_LLM_API_KEY\s*[,=]',
    r'Authorization:\s*Bearer\s+[A-Za-z0-9_\-.]+',
    r'Basic\s+[A-Za-z0-9+/=]{20,}',
]

def strip_tags(s:str)->str:
    s=re.sub(r'<script\b.*?</script>',' ',s,flags=re.I|re.S)
    s=re.sub(r'<style\b.*?</style>',' ',s,flags=re.I|re.S)
    return html.unescape(re.sub(r'\s+',' ',TAG_RE.sub(' ',s))).strip()

def fetch(url:str)->tuple[int,str]:
    full_url = url + ('?cr011_verify=1' if '?' not in url else '&cr011_verify=1')
    js = """
const url = process.argv[1];
fetch(url, {headers:{'User-Agent':'Hermes-UseGEO-CR011-Verifier/1.0','Accept':'text/html'}})
  .then(async r => { console.log(JSON.stringify({status:r.status, body: await r.text()})); })
  .catch(e => { console.error(e && (e.stack || e.message || e)); process.exit(1); });
"""
    proc=subprocess.run(['node','-e',js,full_url], stdout=subprocess.PIPE, stderr=subprocess.PIPE, text=True, encoding='utf-8', errors='replace', timeout=45)
    if proc.returncode==0:
        data=json.loads(proc.stdout)
        return int(data['status']), data['body']
    req=urllib.request.Request(full_url, headers={'User-Agent':'Hermes-UseGEO-CR011-Verifier/1.0','Accept':'text/html'})
    with urllib.request.urlopen(req,timeout=30) as resp:
        return resp.status, resp.read().decode('utf-8', errors='replace')

def check(slug, source, mode, status=None):
    d=ARTICLES[slug]
    text=strip_tags(source)
    words=len(WORD_RE.findall(text))
    faq_count=len(re.findall(r'<h[23][^>]*>\s*(?:Q[:.]\s*)?.*?\?</h[23]>',source,flags=re.I|re.S))
    scanner_cta=len(re.findall(r'/#scanner|https://usegeo\.blog/#scanner', source, flags=re.I))
    lower=source.lower()
    quick='quick answer' in lower
    common=('common mistakes' in lower) or ('practical checklist' in lower)
    missing_links=[x for x in d['links'] if x not in source]
    forbidden=[pat for pat in FORBIDDEN if re.search(pat, source, re.I)]
    secret=[pat for pat in SECRET_PATTERNS if re.search(pat, source, re.I)]
    errors=[]
    if mode=='production' and status!=200: errors.append(f'HTTP status {status}, expected 200')
    if not (1200<=words<=1800): errors.append(f'word_count {words}, expected 1200-1800')
    if faq_count<4: errors.append(f'faq_count {faq_count}, expected >=4')
    if scanner_cta<1: errors.append(f'scanner_cta_count {scanner_cta}, expected >=1')
    if len([x for x in d['links'] if x in source])<2: errors.append('internal_links <2; missing '+','.join(missing_links))
    if not quick: errors.append('missing Quick answer')
    if not common: errors.append('missing common mistakes or practical checklist')
    if forbidden: errors.append('forbidden claim patterns: '+','.join(forbidden))
    if secret: errors.append('secret-like patterns: '+','.join(secret))
    return {'slug':slug,'wp_slug':d['wp_slug'],'mode':mode,'status':status,'word_count':words,'faq_count':faq_count,'scanner_cta_count':scanner_cta,'passed':not errors,'errors':errors}

def main():
    ap=argparse.ArgumentParser(); ap.add_argument('--mode',choices=['local','production'],required=True); ap.add_argument('--json',action='store_true'); args=ap.parse_args()
    results=[]
    for slug,d in ARTICLES.items():
        if args.mode=='local':
            path=OUTPUTS/f'{slug}.html'
            if not path.exists(): results.append({'slug':slug,'passed':False,'errors':[f'missing {path}']}); continue
            results.append(check(slug,path.read_text(encoding='utf-8'),args.mode))
        else:
            try:
                status,src=fetch(f"{BASE}/{d['wp_slug']}/")
                results.append(check(slug,src,args.mode,status))
            except Exception as e:
                results.append({'slug':slug,'wp_slug':d['wp_slug'],'mode':args.mode,'passed':False,'errors':[f'fetch failed: {e}']})
    passed=all(r.get('passed') for r in results)
    out={'passed':passed,'results':results}
    evidence=CR_DIR/'evidence'/('geo-basics-local-result.json' if args.mode=='local' else 'geo-basics-production-result.json')
    evidence.write_text(json.dumps(out,indent=2,ensure_ascii=False),encoding='utf-8')
    if args.json: print(json.dumps(out,indent=2,ensure_ascii=False))
    else:
        print(f'CR-011 GEO basics verification ({args.mode}): '+('PASS' if passed else 'FAIL'))
        for r in results:
            print(f"- {r['slug']}: {'PASS' if r.get('passed') else 'FAIL'} | words={r.get('word_count')} faq={r.get('faq_count')} cta={r.get('scanner_cta_count')}")
            for err in r.get('errors',[]): print('  * '+err)
        print(f'Evidence: {evidence}')
    return 0 if passed else 1
if __name__=='__main__': raise SystemExit(main())
