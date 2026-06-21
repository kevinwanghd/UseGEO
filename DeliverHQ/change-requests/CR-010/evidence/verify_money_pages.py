#!/usr/bin/env python3
"""Verify CR-010 UseGEO money pages locally or in production."""
from __future__ import annotations

import argparse
import html
import json
import re
import sys
import urllib.request
from pathlib import Path

CR_DIR = Path(__file__).resolve().parents[1]
OUTPUTS = CR_DIR / "outputs"
PAGES = {
    "geo-checklist": {
        "file": OUTPUTS / "geo-checklist-expanded.html",
        "url": "https://usegeo.blog/geo-checklist/",
        "required_links": ["/ai-visibility-tools/", "/best-geo-agencies/"],
        "markers": ["Generative Engine Optimization Checklist", "Entity clarity", "Comparison readiness"],
    },
    "ai-visibility-tools": {
        "file": OUTPUTS / "ai-visibility-tools-expanded.html",
        "url": "https://usegeo.blog/ai-visibility-tools/",
        "required_links": ["/geo-checklist/", "/best-geo-agencies/"],
        "markers": ["AI Visibility Tools", "Prompt monitoring", "readiness scanner"],
    },
    "best-geo-agencies": {
        "file": OUTPUTS / "best-geo-agencies-expanded.html",
        "url": "https://usegeo.blog/best-geo-agencies/",
        "required_links": ["/geo-checklist/", "/ai-visibility-tools/"],
        "markers": ["Best GEO Agencies", "Red flags", "partner matching"],
    },
}
FORBIDDEN = [
    r"guarantee(?:d|s)?\s+(?:AI\s+)?(?:ranking|rankings|recommendation|recommendations)",
    r"guarantee(?:d|s)?\s+(?:to\s+)?(?:appear|show up)\s+in\s+(?:ChatGPT|Claude|Gemini|Perplexity)",
    r"UseGEO\s+(?:will|does)\s+(?:deliver|provide)\s+(?:a\s+)?full\s+GEO\s+(?:service|audit|consulting|fix sprint)",
    r"we\s+control\s+AI\s+(?:answers|rankings|recommendations)",
]
SECRET_PATTERNS = [
    r"USEGEO_LLM_API_KEY\s*[,=]",
    r"Authorization:\s*Bearer\s+[A-Za-z0-9_\-.]+",
    r"Basic\s+[A-Za-z0-9+/=]{20,}",
    r"[A-Za-z0-9_\-.]+:[A-Za-z0-9_\-.]{16,}@usegeo\.blog",
]

TAG_RE = re.compile(r"<[^>]+>")
WORD_RE = re.compile(r"\b[A-Za-z][A-Za-z0-9'\-]*\b")


def strip_tags(text: str) -> str:
    text = re.sub(r"<script\b.*?</script>", " ", text, flags=re.I | re.S)
    text = re.sub(r"<style\b.*?</style>", " ", text, flags=re.I | re.S)
    text = TAG_RE.sub(" ", text)
    return html.unescape(re.sub(r"\s+", " ", text)).strip()


def fetch(url: str) -> tuple[int, str]:
    req = urllib.request.Request(url + ("?cr010_verify=1" if "?" not in url else "&cr010_verify=1"), headers={
        "User-Agent": "Hermes-UseGEO-CR010-Verifier/1.0",
        "Accept": "text/html,application/xhtml+xml",
    })
    with urllib.request.urlopen(req, timeout=30) as resp:
        raw = resp.read().decode("utf-8", errors="replace")
        return resp.status, raw


def check_page(slug: str, source: str, mode: str, status: int | None = None) -> dict:
    cfg = PAGES[slug]
    text = strip_tags(source)
    words = len(WORD_RE.findall(text))
    faq_count = len(re.findall(r"<h[23][^>]*>\s*(?:Q[:.]\s*)?.*?\?</h[23]>", source, flags=re.I | re.S))
    scanner_cta_count = len(re.findall(r"/#scanner|https://usegeo\.blog/#scanner", source, flags=re.I))
    missing_links = [link for link in cfg["required_links"] if link not in source]
    missing_markers = [marker for marker in cfg["markers"] if marker.lower() not in source.lower()]
    forbidden_hits = []
    for pat in FORBIDDEN:
        if re.search(pat, source, flags=re.I):
            forbidden_hits.append(pat)
    secret_hits = []
    for pat in SECRET_PATTERNS:
        if re.search(pat, source, flags=re.I):
            secret_hits.append(pat)
    errors = []
    if mode == "production" and status != 200:
        errors.append(f"HTTP status {status}, expected 200")
    if not (2500 <= words <= 3500):
        errors.append(f"word_count {words}, expected 2500-3500")
    if faq_count < 5:
        errors.append(f"faq_count {faq_count}, expected >=5")
    if scanner_cta_count < 2:
        errors.append(f"scanner_cta_count {scanner_cta_count}, expected >=2")
    if missing_links:
        errors.append("missing links: " + ", ".join(missing_links))
    if missing_markers:
        errors.append("missing markers: " + ", ".join(missing_markers))
    if forbidden_hits:
        errors.append("forbidden claim patterns: " + ", ".join(forbidden_hits))
    if secret_hits:
        errors.append("secret-like patterns: " + ", ".join(secret_hits))
    return {
        "slug": slug,
        "mode": mode,
        "status": status,
        "word_count": words,
        "faq_count": faq_count,
        "scanner_cta_count": scanner_cta_count,
        "missing_links": missing_links,
        "missing_markers": missing_markers,
        "forbidden_hits": forbidden_hits,
        "secret_hits": secret_hits,
        "passed": not errors,
        "errors": errors,
    }


def main() -> int:
    ap = argparse.ArgumentParser()
    ap.add_argument("--mode", choices=["local", "production"], required=True)
    ap.add_argument("--json", action="store_true")
    args = ap.parse_args()
    results = []
    for slug, cfg in PAGES.items():
        if args.mode == "local":
            if not cfg["file"].exists():
                results.append({"slug": slug, "passed": False, "errors": [f"missing file {cfg['file']}"]})
                continue
            source = cfg["file"].read_text(encoding="utf-8")
            results.append(check_page(slug, source, args.mode))
        else:
            try:
                status, source = fetch(cfg["url"])
                results.append(check_page(slug, source, args.mode, status=status))
            except Exception as exc:
                results.append({"slug": slug, "mode": args.mode, "passed": False, "errors": [f"fetch failed: {exc}"]})
    passed = all(r.get("passed") for r in results)
    output = {"passed": passed, "results": results}
    evidence = CR_DIR / "evidence" / ("money-pages-local-result.json" if args.mode == "local" else "money-pages-production-result.json")
    evidence.write_text(json.dumps(output, indent=2, ensure_ascii=False), encoding="utf-8")
    if args.json:
        print(json.dumps(output, indent=2, ensure_ascii=False))
    else:
        print(f"CR-010 money page verification ({args.mode}): {'PASS' if passed else 'FAIL'}")
        for r in results:
            print(f"- {r['slug']}: {'PASS' if r.get('passed') else 'FAIL'} | words={r.get('word_count')} faq={r.get('faq_count')} cta={r.get('scanner_cta_count')}")
            for err in r.get("errors", []):
                print(f"  * {err}")
        print(f"Evidence: {evidence}")
    return 0 if passed else 1

if __name__ == "__main__":
    raise SystemExit(main())
