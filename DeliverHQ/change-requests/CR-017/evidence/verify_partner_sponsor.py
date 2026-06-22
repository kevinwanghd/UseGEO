#!/usr/bin/env python3
"""Verify CR-017 partner and sponsor prospecting assets."""
from __future__ import annotations
import argparse, json, re
from pathlib import Path
CR_DIR=Path(__file__).resolve().parents[1]
OUT=CR_DIR/'outputs'
EVIDENCE=CR_DIR/'evidence/partner-sponsor-result.json'
REQUIRED_FILES=['partner-prospect-list.json','partner-prospect-list.csv','first-20-outreach-queue.json','sponsor-offer.md','lead-referral-offer.md','outreach-templates.md']
CATEGORIES=['SEO agency','content strategy agency','GEO consultant','AI visibility tool']
FIELDS=['company','website','contact','category','fit','outreach_status']
SPONSOR_PHRASES=['Homepage sponsor','Scanner result sponsor','Article sidebar sponsor','Beta price draft','No traffic guarantee','Audience']
LEAD_PHRASES=['partner_consent = yes','lead_quality = hot or warm','business email','scanner snapshot','Beta pack A','Beta pack B','Referral rules','Compliance notes']
OUTREACH_PHRASES=['Template A','Template B','Tracking fields','not_started','interested','not_fit']

def load(name): return json.loads((OUT/name).read_text(encoding='utf-8'))
def contains_all(text, phrases): return [p for p in phrases if p.lower() not in text.lower()]
def main():
    ap=argparse.ArgumentParser(); ap.add_argument('--mode',choices=['local'],default='local'); args=ap.parse_args()
    checks=[]
    for f in REQUIRED_FILES: checks.append({'name':f'{f}_exists','passed':(OUT/f).exists()})
    if all(c['passed'] for c in checks):
        prospects=load('partner-prospect-list.json'); queue=load('first-20-outreach-queue.json')
        checks.append({'name':'prospect_count','passed':len(prospects)>=50,'count':len(prospects),'expected_min':50})
        names=[p.get('company','').strip().lower() for p in prospects]
        dup=len(names)-len(set(names)); checks.append({'name':'unique_companies','passed':dup==0,'duplicate_count':dup})
        high=sum(1 for p in prospects if p.get('fit')=='high'); checks.append({'name':'high_fit_count','passed':high>=20,'count':high,'expected_min':20})
        cats={p.get('category') for p in prospects}; missing_cat=[c for c in CATEGORIES if c not in cats]; checks.append({'name':'category_coverage','passed':not missing_cat,'missing':missing_cat})
        missing_fields=[]
        for idx,p in enumerate(prospects):
            for f in FIELDS:
                if f not in p or p.get(f) in [None,'']:
                    missing_fields.append(f'{idx}:{f}')
        checks.append({'name':'prospect_required_fields','passed':not missing_fields,'missing':missing_fields[:20]})
        # Contact can be TBD, but should be explicitly marked, not blank.
        contact_blank=[p['company'] for p in prospects if not p.get('contact')]
        checks.append({'name':'contacts_not_blank','passed':not contact_blank,'missing':contact_blank[:10]})
        checks.append({'name':'queue_count','passed':len(queue)==20,'count':len(queue),'expected':20})
        sent=[q for q in queue if q.get('send_status')!='ready_for_kevin_review_not_sent']
        checks.append({'name':'queue_not_sent','passed':not sent,'bad_count':len(sent)})
        sponsor=(OUT/'sponsor-offer.md').read_text(encoding='utf-8')
        miss=contains_all(sponsor, SPONSOR_PHRASES); checks.append({'name':'sponsor_offer_complete','passed':not miss,'missing':miss})
        lead=(OUT/'lead-referral-offer.md').read_text(encoding='utf-8')
        miss=contains_all(lead, LEAD_PHRASES); checks.append({'name':'lead_referral_offer_complete','passed':not miss,'missing':miss})
        outreach=(OUT/'outreach-templates.md').read_text(encoding='utf-8')
        miss=contains_all(outreach, OUTREACH_PHRASES); checks.append({'name':'outreach_templates_complete','passed':not miss,'missing':miss})
        # No external sent state
        sent_words=sum(1 for p in prospects if str(p.get('outreach_status','')).lower()=='sent')
        checks.append({'name':'no_external_send_state','passed':sent_words==0,'sent_count':sent_words})
        # No fake guarantee
        combined=sponsor+'\n'+lead+'\n'+outreach
        bad=re.findall(r'guarantee(?:d|s)?\s+(?:traffic|leads|AI\s+ranking)', combined, flags=re.I)
        checks.append({'name':'no_guarantee_claims','passed':not bad,'hits':bad})
    passed=all(c.get('passed') for c in checks)
    out={'mode':args.mode,'passed':passed,'checks':checks}
    EVIDENCE.write_text(json.dumps(out,indent=2,ensure_ascii=False),encoding='utf-8')
    print(f"CR-017 partner/sponsor verification ({args.mode}): {'PASS' if passed else 'FAIL'}")
    for c in checks:
        msg=c['name']
        if 'count' in c: msg+=f" count={c['count']}"
        if 'expected_min' in c: msg+=f" expected_min={c['expected_min']}"
        if 'missing' in c and c['missing']: msg+=' missing='+','.join(map(str,c['missing']))
        if 'bad_count' in c: msg+=f" bad_count={c['bad_count']}"
        if 'sent_count' in c: msg+=f" sent_count={c['sent_count']}"
        print(f"- {'PASS' if c.get('passed') else 'FAIL'}: {msg}")
    print(f'Evidence: {EVIDENCE}')
    return 0 if passed else 1
if __name__=='__main__': raise SystemExit(main())
