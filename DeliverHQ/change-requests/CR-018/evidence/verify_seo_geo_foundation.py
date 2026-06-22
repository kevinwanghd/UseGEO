#!/usr/bin/env python3
"""Verify CR-018 on-site SEO/GEO technical foundation."""
from __future__ import annotations

import argparse
import json
import re
import subprocess
import sys
import urllib.request
from html import unescape
from pathlib import Path
from typing import Any

CR_DIR = Path(__file__).resolve().parents[1]
ROOT = Path(__file__).resolve().parents[4]
THEME = ROOT / "wordpress/theme/usegeo"
EVIDENCE = CR_DIR / "evidence"
BASE_URL = "https://usegeo.blog"
MONEY_PATHS = ["/geo-checklist/", "/ai-visibility-tools/", "/best-geo-agencies/"]
SAMPLE_PAGES = ["/", *MONEY_PATHS, "/what-is-geo-generative-engine-optimization/"]
FORBIDDEN_SCHEMA_TERMS = ["AggregateRating", "Review", "ratingValue", "reviewCount", "bestRating", "worstRating"]


def read(rel: str) -> str:
    return (ROOT / rel).read_text(encoding="utf-8")


def add_check(checks: list[dict[str, Any]], name: str, passed: bool, **extra: Any) -> None:
    item = {"name": name, "passed": bool(passed)}
    item.update(extra)
    checks.append(item)


def fetch(url: str) -> tuple[int, str, str]:
    sep = "&" if "?" in url else "?"
    req = urllib.request.Request(
        url + sep + "cr018_verify=1",
        headers={
            "User-Agent": "Hermes-UseGEO-CR018-Verifier/1.0",
            "Accept": "text/html,application/xhtml+xml,application/xml,text/plain,*/*",
        },
    )
    with urllib.request.urlopen(req, timeout=35) as resp:
        raw = resp.read().decode("utf-8", errors="replace")
        return int(resp.status), raw, resp.geturl()


def strip_tags(text: str) -> str:
    text = re.sub(r"<script\b.*?</script>", " ", text, flags=re.I | re.S)
    text = re.sub(r"<style\b.*?</style>", " ", text, flags=re.I | re.S)
    text = re.sub(r"<[^>]+>", " ", text)
    return unescape(re.sub(r"\s+", " ", text)).strip()


def check_local() -> dict[str, Any]:
    checks: list[dict[str, Any]] = []
    seo_rel = "wordpress/theme/usegeo/inc/seo.php"
    seo_path = ROOT / seo_rel
    seo = seo_path.read_text(encoding="utf-8") if seo_path.exists() else ""
    functions = read("wordpress/theme/usegeo/functions.php")
    single = read("wordpress/theme/usegeo/single.php")
    single_css = read("wordpress/theme/usegeo/assets/css/single.css")
    header = read("wordpress/theme/usegeo/header.php")

    add_check(checks, "seo_php_exists", seo_path.exists(), file=seo_rel)
    add_check(checks, "functions_requires_seo", "inc/seo.php" in functions)
    add_check(checks, "wp_head_schema_hook", "add_action('wp_head', 'usegeo_output_json_ld'" in seo or 'add_action("wp_head", "usegeo_output_json_ld"' in seo)

    required_functions = [
        "usegeo_get_schema_site_url",
        "usegeo_get_schema_description",
        "usegeo_extract_visible_faq_items",
        "usegeo_build_organization_schema",
        "usegeo_build_website_schema",
        "usegeo_build_webpage_schema",
        "usegeo_build_article_schema",
        "usegeo_build_faq_schema",
        "usegeo_output_json_ld",
    ]
    missing_functions = [name for name in required_functions if f"function {name}" not in seo]
    add_check(checks, "schema_builder_functions_present", not missing_functions, missing=missing_functions)

    schema_terms = ["Organization", "WebSite", "WebPage", "Article", "FAQPage", "Question", "Answer"]
    missing_terms = [term for term in schema_terms if term not in seo]
    add_check(checks, "schema_types_present", not missing_terms, missing=missing_terms)

    forbidden_hits = [term for term in FORBIDDEN_SCHEMA_TERMS if term in seo]
    add_check(checks, "no_fake_schema_terms", not forbidden_hits, hits=forbidden_hits)

    add_check(checks, "faq_visible_content_parser", "the_content" in seo and "preg_match_all" in seo and "FAQ" in seo)
    add_check(checks, "json_encode_safety_flags", "JSON_UNESCAPED_SLASHES" in seo and "JSON_UNESCAPED_UNICODE" in seo)
    add_check(checks, "header_meta_description_existing", '<meta name="description"' in header)

    missing_single_links = [path for path in MONEY_PATHS if path not in single]
    add_check(checks, "single_template_money_links", not missing_single_links, missing=missing_single_links, expected_count=3)
    add_check(checks, "single_foundation_section_present", "usegeo-single-foundation-links" in single)
    add_check(checks, "single_foundation_css_present", "usegeo-single-foundation-links" in single_css)

    no_external_fetch = not any(token in seo for token in ["wp_remote_get", "curl_", "file_get_contents("])
    add_check(checks, "schema_no_external_fetch", no_external_fetch)

    syntax_like_errors = []
    for rel in [seo_rel, "wordpress/theme/usegeo/functions.php", "wordpress/theme/usegeo/single.php"]:
        text = read(rel)
        if text.count("(") != text.count(")"):
            syntax_like_errors.append(f"{rel}:paren_count")
        if text.count("{") != text.count("}"):
            syntax_like_errors.append(f"{rel}:brace_count")
    add_check(checks, "php_static_balanced_delimiters", not syntax_like_errors, errors=syntax_like_errors)

    passed = all(c["passed"] for c in checks)
    out = {"mode": "local", "passed": passed, "checks": checks}
    (EVIDENCE / "seo-geo-local-result.json").write_text(json.dumps(out, indent=2, ensure_ascii=False), encoding="utf-8")
    return out


def check_production_audit() -> dict[str, Any]:
    checks: list[dict[str, Any]] = []
    summary: dict[str, Any] = {}

    sitemap_status, sitemap_index, sitemap_final = fetch(BASE_URL + "/sitemap.xml")
    robots_status, robots, robots_final = fetch(BASE_URL + "/robots.txt")
    post_status, post_xml, post_final = fetch(BASE_URL + "/wp-sitemap-posts-post-1.xml")
    page_status, page_xml, page_final = fetch(BASE_URL + "/wp-sitemap-posts-page-1.xml")

    post_urls = re.findall(r"<loc>([^<]+)</loc>", post_xml)
    page_urls = re.findall(r"<loc>([^<]+)</loc>", page_xml)
    indexed_money = [path for path in MONEY_PATHS if BASE_URL + path in page_urls]

    add_check(checks, "sitemap_status_200", sitemap_status == 200, status=sitemap_status, final_url=sitemap_final)
    add_check(checks, "robots_status_200", robots_status == 200, status=robots_status, final_url=robots_final)
    add_check(checks, "robots_has_sitemap", "Sitemap:" in robots, robots=robots[:200])
    add_check(checks, "post_sitemap_count", len(post_urls) >= 20, count=len(post_urls), expected_min=20)
    add_check(checks, "page_sitemap_money_pages", len(indexed_money) == 3, found=indexed_money, expected=MONEY_PATHS)

    page_results = []
    title_values = []
    desc_values = []
    for path in SAMPLE_PAGES:
        status, html, final = fetch(BASE_URL + path)
        jsonld_count = len(re.findall(r"<script[^>]+application/ld\+json", html, flags=re.I))
        title = (re.search(r"<title[^>]*>([\s\S]*?)</title>", html, flags=re.I) or [None, ""])[1]
        title = strip_tags(title)
        desc_match = re.search(r"<meta\s+name=[\"']description[\"'][^>]*content=[\"']([^\"']*)", html, flags=re.I)
        desc = unescape(desc_match.group(1)).strip() if desc_match else ""
        hits = [m for m in MONEY_PATHS if m in html]
        title_values.append((path, title))
        if path in MONEY_PATHS:
            desc_values.append((path, desc))
        page_results.append({"path": path, "status": status, "final_url": final, "jsonld_count": jsonld_count, "title": title, "description": desc, "money_link_hits": hits})

    money_titles = [title for path, title in title_values if path in MONEY_PATHS]
    money_descs = [desc for path, desc in desc_values]
    homepage = next((r for r in page_results if r["path"] == "/"), {})
    money_results = [r for r in page_results if r["path"] in MONEY_PATHS]
    article_results = []
    for url in post_urls:
        status, html, final = fetch(url)
        hits = [m for m in MONEY_PATHS if m in html]
        article_results.append({"url": url, "status": status, "money_link_count": len(hits), "money_link_hits": hits})

    add_check(checks, "sample_pages_status_200", all(r["status"] == 200 for r in page_results), pages=page_results)
    add_check(checks, "current_production_jsonld_present", all(r["jsonld_count"] > 0 for r in page_results), note="Expected to fail before CR-018 theme deployment", pages=[{"path": r["path"], "jsonld_count": r["jsonld_count"]} for r in page_results])
    add_check(checks, "homepage_links_three_money_pages", set(homepage.get("money_link_hits", [])) == set(MONEY_PATHS), hits=homepage.get("money_link_hits", []))
    add_check(checks, "money_pages_mutual_links", all(len(set(r["money_link_hits"]) - {r["path"]}) >= 2 for r in money_results), pages=money_results)
    add_check(checks, "money_page_unique_titles", len(set(money_titles)) == 3 and all(money_titles), titles=money_titles)
    add_check(checks, "money_page_unique_descriptions", len(set(money_descs)) == 3 and all(money_descs), descriptions=money_descs)
    missing_article_money = [r for r in article_results if r["money_link_count"] == 0]
    add_check(checks, "published_articles_have_money_links", not missing_article_money, missing_count=len(missing_article_money), missing=missing_article_money[:10], total=len(article_results))

    keyword_stuffing = []
    for path, title in title_values:
        words = re.findall(r"[A-Za-z]{3,}", title.lower())
        for word in set(words):
            if words.count(word) > 2 and word not in {"usegeo", "blog"}:
                keyword_stuffing.append({"path": path, "word": word, "title": title})
    add_check(checks, "title_keyword_stuffing_absent", not keyword_stuffing, hits=keyword_stuffing)

    summary.update({
        "sitemap_final_url": sitemap_final,
        "post_count": len(post_urls),
        "page_count": len(page_urls),
        "indexed_money_pages": indexed_money,
        "sample_pages": page_results,
        "article_money_link_missing_count": len(missing_article_money),
    })
    passed = all(c["passed"] for c in checks if c["name"] != "current_production_jsonld_present")
    out = {"mode": "production-audit", "passed": passed, "checks": checks, "summary": summary}
    (EVIDENCE / "seo-geo-production-audit.json").write_text(json.dumps(out, indent=2, ensure_ascii=False), encoding="utf-8")
    return out


def main() -> int:
    ap = argparse.ArgumentParser()
    ap.add_argument("--mode", choices=["local", "production-audit"], required=True)
    ap.add_argument("--json", action="store_true")
    args = ap.parse_args()

    if args.mode == "local":
        out = check_local()
    else:
        out = check_production_audit()

    if args.json:
        print(json.dumps(out, indent=2, ensure_ascii=False))
    else:
        print(f"CR-018 SEO/GEO foundation verification ({args.mode}): {'PASS' if out['passed'] else 'FAIL'}")
        for c in out["checks"]:
            msg = c["name"]
            if "count" in c:
                msg += f" count={c['count']}"
            if "missing_count" in c:
                msg += f" missing_count={c['missing_count']}"
            if "status" in c:
                msg += f" status={c['status']}"
            if c.get("missing"):
                msg += f" missing={c['missing']}"
            if c.get("hits"):
                msg += f" hits={c['hits']}"
            if c.get("note"):
                msg += f" note={c['note']}"
            print(f"- {'PASS' if c['passed'] else 'FAIL'}: {msg}")
        evidence_name = "seo-geo-local-result.json" if args.mode == "local" else "seo-geo-production-audit.json"
        print(f"Evidence: {EVIDENCE / evidence_name}")
    return 0 if out["passed"] else 1


if __name__ == "__main__":
    raise SystemExit(main())
