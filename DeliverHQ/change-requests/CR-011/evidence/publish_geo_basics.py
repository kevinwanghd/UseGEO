#!/usr/bin/env python3
"""Publish/update CR-011 GEO Basics posts via WordPress REST.
Reads WP_USER and WP_APP_PASSWORD from process environment. Does not write secrets.
"""
from __future__ import annotations
import base64, json, os, subprocess, sys, urllib.parse
from pathlib import Path

CR_DIR=Path(__file__).resolve().parents[1]
ROOT=CR_DIR.parents[2]
ARTICLES=json.loads((CR_DIR/'outputs/articles.json').read_text(encoding='utf-8'))
CATEGORIES={'geo-fundamentals':8,'geo-vs-seo':9,'measurement':10,'content-strategy':11,'strategy':12}
BASE='https://usegeo.blog/wp-json/wp/v2'

def run_curl(args, payload=None, auth=False):
    cmd=['curl','--http1.1','--ssl-no-revoke','--retry','5','--retry-all-errors','--retry-delay','3','--connect-timeout','30','--max-time','120','-sS']
    if auth:
        user=os.environ['WP_USER']; pw=os.environ['WP_APP_PASSWORD']
        token=base64.b64encode(f'{user}:{pw}'.encode()).decode()
        cmd += ['-H','Authorization: Basic '+token]
    if payload is not None:
        cmd += ['-H','Content-Type: application/json','--data-binary','@-']
    cmd += args
    proc=subprocess.run(cmd, input=payload, text=True, encoding='utf-8', errors='replace', stdout=subprocess.PIPE, stderr=subprocess.PIPE)
    if proc.returncode != 0:
        raise RuntimeError(f'curl exit {proc.returncode}: {proc.stderr[:500]}')
    return proc.stdout

def get_existing(slug):
    qs=urllib.parse.urlencode({'slug':slug,'per_page':1,'_fields':'id,slug,status,link,title'})
    raw=run_curl([f'{BASE}/posts?{qs}'])
    return json.loads(raw)

def main():
    if not os.environ.get('WP_USER') or not os.environ.get('WP_APP_PASSWORD'):
        print('WP_USER/WP_APP_PASSWORD missing', file=sys.stderr)
        return 2
    results=[]
    for key,d in ARTICLES.items():
        slug=d['wp_slug']
        existing=get_existing(slug)
        content=(CR_DIR/'outputs'/f'{key}.html').read_text(encoding='utf-8')
        payload=json.dumps({'title':d['title'],'slug':slug,'content':content,'status':'publish','categories':[CATEGORIES.get(d.get('category'),8)]}, ensure_ascii=False)
        try:
            if existing:
                post_id=existing[0]['id']
                raw=run_curl(['-X','POST',f'{BASE}/posts/{post_id}'], payload=payload, auth=True)
                action='updated'
            else:
                raw=run_curl(['-X','POST',f'{BASE}/posts'], payload=payload, auth=True)
                action='created'
            body=json.loads(raw)
            if 'code' in body and 'message' in body:
                results.append({'key':key,'slug':slug,'action':'error','error_code':body.get('code'),'message':body.get('message')})
            else:
                results.append({'key':key,'slug':slug,'action':action,'status':'publish','id':body.get('id'),'link':body.get('link'),'modified_gmt':body.get('modified_gmt')})
        except Exception as exc:
            results.append({'key':key,'slug':slug,'action':'exception','error':str(exc)[:500]})
    out={'results':results}
    (CR_DIR/'evidence/wp-publish-result.json').write_text(json.dumps(out, indent=2, ensure_ascii=False), encoding='utf-8')
    print(json.dumps(results, indent=2, ensure_ascii=False))
    ok=all(r.get('action') in {'updated','created'} and r.get('id') for r in results)
    return 0 if ok else 1
if __name__=='__main__':
    raise SystemExit(main())
