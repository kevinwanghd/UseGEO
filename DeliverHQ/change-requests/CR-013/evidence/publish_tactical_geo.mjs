import fs from 'node:fs';
import path from 'node:path';
const crDir = path.resolve('D:/Code/UseGEO/DeliverHQ/change-requests/CR-013');
const articles = JSON.parse(fs.readFileSync(path.join(crDir, 'outputs/articles.json'), 'utf8'));
const categories = { 'geo-fundamentals': 8, 'geo-vs-seo': 9, measurement: 10, 'content-strategy': 11, strategy: 12 };
const base = 'https://usegeo.blog/wp-json/wp/v2';
const user = process.env.WP_USER;
const password = process.env.WP_APP_PASSWORD;
if (!user || !password) { console.error('WP_USER/WP_APP_PASSWORD missing'); process.exit(2); }
const auth = Buffer.from(`${user}:${password}`).toString('base64');
async function fetchJson(url, options = {}) {
  const res = await fetch(url, {
    ...options,
    headers: {
      'User-Agent': 'Hermes-UseGEO-CR013-Publisher/1.0',
      ...(options.body ? {'Content-Type': 'application/json'} : {}),
      ...(options.auth ? {'Authorization': `Basic ${auth}`} : {}),
      ...(options.headers || {}),
    },
  });
  const text = await res.text();
  let json;
  try { json = text ? JSON.parse(text) : null; } catch { json = {raw: text.slice(0,500)}; }
  if (!res.ok) throw new Error(`HTTP ${res.status}: ${text.slice(0,500)}`);
  return {status: res.status, json};
}
const results=[];
for (const [key,d] of Object.entries(articles)) {
  const slug=d.wp_slug;
  try {
    const existing=(await fetchJson(`${base}/posts?${new URLSearchParams({slug, per_page:'1', _fields:'id,slug,status,link,title'}).toString()}`)).json;
    const content=fs.readFileSync(path.join(crDir,'outputs',`${key}.html`),'utf8');
    const payload=JSON.stringify({title:d.title, slug, content, status:'publish', categories:[categories[d.category]||11]});
    let action, status, body;
    if (Array.isArray(existing) && existing.length) {
      const r=await fetchJson(`${base}/posts/${existing[0].id}`, {method:'POST', body:payload, auth:true});
      action='updated'; status=r.status; body=r.json;
    } else {
      const r=await fetchJson(`${base}/posts`, {method:'POST', body:payload, auth:true});
      action='created'; status=r.status; body=r.json;
    }
    results.push({key, slug, action, status, id:body.id, link:body.link, modified_gmt:body.modified_gmt});
  } catch(e) { results.push({key, slug, action:'error', error:String(e.message||e).slice(0,500)}); }
}
fs.writeFileSync(path.join(crDir,'evidence/wp-publish-result.json'), JSON.stringify({results},null,2));
console.log(JSON.stringify(results,null,2));
if (!results.every(r=>['updated','created'].includes(r.action)&&r.id)) process.exit(1);
