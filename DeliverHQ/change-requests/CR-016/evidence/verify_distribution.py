#!/usr/bin/env python3
"""Verify CR-016 distribution launch assets."""
from __future__ import annotations
import argparse, json, re
from pathlib import Path
CR_DIR=Path(__file__).resolve().parents[1]
OUT=CR_DIR/'outputs'
EVIDENCE=CR_DIR/'evidence/distribution-result.json'
REQUIRED_FILES=['linkedin-template.md','x-twitter-template.md','community-template.md','partner-outreach-template.md','partner-prospect-list.json','linkedin-drafts.json','x-drafts.json','community-drafts.json']
SPAM_PATTERNS=[r'buy now',r'limited time',r'guarantee(?:d|s)?\s+(?:AI\s+)?ranking',r'click my link',r'spam']
TRACKING_FIELDS=['company','website','category','contact','fit','outreach_status']

def load_json(name): return json.loads((OUT/name).read_text(encoding='utf-8'))
def main():
    ap=argparse.ArgumentParser(); ap.add_argument('--mode',choices=['local'],default='local'); args=ap.parse_args()
    checks=[]
    for f in REQUIRED_FILES:
        checks.append({'name':f'{f}_exists','passed':(OUT/f).exists()})
    passed_files=all(c['passed'] for c in checks)
    if passed_files:
        linkedin=load_json('linkedin-drafts.json'); xs=load_json('x-drafts.json'); community=load_json('community-drafts.json'); prospects=load_json('partner-prospect-list.json')
        articles=sorted(set(x['article'] for x in linkedin) | set(x['article'] for x in xs) | set(x['article'] for x in community))
        for article in articles:
            l=[x for x in linkedin if x['article']==article]; types=sorted(set(x.get('type') for x in l))
            checks.append({'name':f'linkedin_{article}_count','passed':len(l)>=3,'count':len(l),'expected_min':3})
            checks.append({'name':f'linkedin_{article}_types','passed':all(t in types for t in ['before_after','checklist','pov']),'types':types})
            xposts=[x for x in xs if x['article']==article]
            checks.append({'name':f'x_{article}_count','passed':len(xposts)>=5,'count':len(xposts),'expected_min':5})
            cta=sum(1 for x in xposts if 'scanner' in x.get('draft','').lower() or '/#scanner' in x.get('draft',''))
            checks.append({'name':f'x_{article}_scanner_cta','passed':cta>=1,'count':cta,'expected_min':1})
            comm=[x for x in community if x['article']==article]
            checks.append({'name':f'community_{article}_count','passed':len(comm)>=1,'count':len(comm),'expected_min':1})
            for i,item in enumerate(comm):
                text=item.get('draft','')
                spam=[pat for pat in SPAM_PATTERNS if re.search(pat,text,re.I)]
                direct_links=len(re.findall(r'https?://',text))
                checks.append({'name':f'community_{article}_{i}_no_spam','passed':not spam and direct_links==0,'spam':spam,'direct_links':direct_links})
        checks.append({'name':'partner_prospect_count','passed':len(prospects)>=20,'count':len(prospects),'expected_min':20})
        missing=[]
        for idx,p in enumerate(prospects):
            for field in TRACKING_FIELDS:
                if field not in p: missing.append(f'{idx}:{field}')
        checks.append({'name':'partner_tracking_fields','passed':not missing,'missing':missing[:10]})
        out_t=(OUT/'partner-outreach-template.md').read_text(encoding='utf-8')
        checks.append({'name':'outreach_templates_count','passed':('Cold email A' in out_t and 'DM B' in out_t),'expected':'Cold email A and DM B'})
        checks.append({'name':'tracking_table_fields_defined','passed':all(f in out_t for f in TRACKING_FIELDS),'fields':TRACKING_FIELDS})
        for name in ['linkedin-template.md','x-twitter-template.md','community-template.md']:
            text=(OUT/name).read_text(encoding='utf-8')
            checks.append({'name':f'{name}_has_no_guaranteed_ranking','passed':not re.search(r'guarantee(?:d|s)?\s+(?:AI\s+)?ranking',text,re.I)})
    passed=all(c.get('passed') for c in checks)
    result={'mode':args.mode,'passed':passed,'checks':checks}
    EVIDENCE.write_text(json.dumps(result,indent=2,ensure_ascii=False),encoding='utf-8')
    print(f"CR-016 distribution verification ({args.mode}): {'PASS' if passed else 'FAIL'}")
    for c in checks:
        msg=c['name']
        if 'count' in c: msg += f" count={c['count']}"
        if c.get('missing'): msg += ' missing='+','.join(map(str,c['missing']))
        if c.get('spam'): msg += ' spam='+','.join(c['spam'])
        print(f"- {'PASS' if c.get('passed') else 'FAIL'}: {msg}")
    print(f'Evidence: {EVIDENCE}')
    return 0 if passed else 1
if __name__=='__main__': raise SystemExit(main())
