#!/usr/bin/env python3
"""
Efficient Cross-Language Link Audit for vat.support
Scans only ZH and RU pages for cross-language linking issues.
"""

import requests
from bs4 import BeautifulSoup
from urllib.parse import urlparse, unquote
import time

SITEMAP_URL = "https://vat.support/page-sitemap.xml"
BASE_DOMAIN = "vat.support"

# Known English-only pages 
ENGLISH_ONLY_PATHS = [
    "vat-quiz",
    "client-terms-conditions",
    "terms-conditions", 
    "privacy-policy",
]

HEADERS = {"User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"}

def fetch_url(url, retries=2):
    for attempt in range(retries):
        try:
            resp = requests.get(url, headers=HEADERS, timeout=20)
            resp.raise_for_status()
            return resp.text
        except Exception as e:
            if attempt == retries - 1:
                return None
            time.sleep(0.5)
    return None

def get_sitemap_urls():
    content = fetch_url(SITEMAP_URL)
    if not content:
        return []
    soup = BeautifulSoup(content, "xml")
    urls = []
    for loc in soup.find_all("loc"):
        url = loc.text.strip()
        # Filter out images and media files
        if not any(ext in url for ext in ['.png', '.jpg', '.jpeg', '.gif', '.webp', '.pdf', '.svg']):
            urls.append(url)
    return urls

def get_page_lang(url):
    path = urlparse(url).path
    if '/zh/' in path or path.startswith('/zh'):
        return 'zh'
    elif '/ru/' in path or path.startswith('/ru') or '%d0%' in url.lower():
        return 'ru'
    return 'en'

def is_internal_link(href):
    if not href:
        return False
    if href.startswith('#') or href.startswith('mailto:') or href.startswith('tel:') or href.startswith('javascript:'):
        return False
    if href.startswith('/'):
        return True
    parsed = urlparse(href)
    return parsed.netloc == '' or BASE_DOMAIN in parsed.netloc

def get_link_lang(href):
    href_lower = href.lower()
    if '/zh/' in href_lower:
        return 'zh'
    elif '/ru/' in href_lower or '%d0%' in href_lower:
        return 'ru'
    return 'en'

def is_english_only(href):
    href_lower = href.lower()
    for pattern in ENGLISH_ONLY_PATHS:
        if pattern in href_lower:
            return True
    return False

def audit_page(url, page_lang):
    issues = []
    content = fetch_url(url)
    if not content:
        return [{"error": f"Could not fetch: {url}"}]
    
    soup = BeautifulSoup(content, "html.parser")
    
    for a in soup.find_all("a", href=True):
        href = a.get("href", "").strip()
        if not is_internal_link(href):
            continue
            
        link_lang = get_link_lang(href)
        link_text = a.get_text(strip=True)[:40] or "[no text]"
        
        # Case 1: Translated page links to English (not to its own language)
        if page_lang != 'en' and link_lang == 'en':
            if is_english_only(href):
                issues.append({
                    "type": "ENGLISH_ONLY",
                    "href": href,
                    "text": link_text,
                    "issue": f"Links to English-only page (no {page_lang.upper()} version)",
                    "fix": f"Create {page_lang.upper()} version OR remove link"
                })
            else:
                # Should link to translated version
                suggested = f"/{page_lang}{href}" if href.startswith("/") and not href.startswith(f"/{page_lang}") else href.replace(href, f"/{page_lang}" + href.split("/", 1)[-1] if "/" in href else href)
                issues.append({
                    "type": "WRONG_LANG",
                    "href": href,
                    "text": link_text,
                    "issue": f"{page_lang.upper()} page links to English",
                    "fix": f"Change to {page_lang.upper()} version"
                })
        
        # Case 2: ZH linking to RU or RU linking to ZH
        elif page_lang == 'zh' and link_lang == 'ru':
            issues.append({
                "type": "CROSS_LANG",
                "href": href,
                "text": link_text,
                "issue": "ZH page links to RU version",
                "fix": "Change /ru/ to /zh/"
            })
        elif page_lang == 'ru' and link_lang == 'zh':
            issues.append({
                "type": "CROSS_LANG",
                "href": href,
                "text": link_text,
                "issue": "RU page links to ZH version",
                "fix": "Change /zh/ to /ru/"
            })
    
    return issues

def main():
    print("Fetching sitemap...")
    all_urls = get_sitemap_urls()
    print(f"Found {len(all_urls)} page URLs (excluding images)")
    
    # Filter to only ZH and RU pages (EN pages can link anywhere)
    translated_pages = [(url, get_page_lang(url)) for url in all_urls if get_page_lang(url) != 'en']
    print(f"Found {len(translated_pages)} translated (ZH/RU) pages to audit")
    print()
    
    all_issues = {}
    
    for i, (url, lang) in enumerate(translated_pages, 1):
        short_path = unquote(urlparse(url).path)[:60]
        print(f"[{i}/{len(translated_pages)}] Scanning {lang.upper()}: {short_path}...")
        
        issues = audit_page(url, lang)
        if issues:
            all_issues[url] = {"lang": lang, "issues": issues}
            print(f"   -> {len(issues)} issue(s) found")
        
        time.sleep(0.2)  # Be nice to server
    
    # Generate Report
    print("\n" + "="*80)
    print("GENERATING REPORT...")
    print("="*80 + "\n")
    
    report = []
    report.append("# Cross-Language Link Audit Report")
    report.append(f"**Site:** vat.support")
    report.append(f"**Generated:** {time.strftime('%Y-%m-%d %H:%M:%S')}")
    report.append("")
    
    # Summary
    total_issues = sum(len(d["issues"]) for d in all_issues.values())
    report.append("## Summary")
    report.append(f"- **Total translated pages scanned:** {len(translated_pages)}")
    report.append(f"- **Pages with issues:** {len(all_issues)}")
    report.append(f"- **Total issues found:** {total_issues}")
    report.append("")
    
    # Categorize
    wrong_lang = []
    english_only = []
    cross_lang = []
    
    for url, data in all_issues.items():
        for issue in data["issues"]:
            issue["source"] = url
            issue["source_lang"] = data["lang"]
            if issue.get("type") == "WRONG_LANG":
                wrong_lang.append(issue)
            elif issue.get("type") == "ENGLISH_ONLY":
                english_only.append(issue)
            elif issue.get("type") == "CROSS_LANG":
                cross_lang.append(issue)
    
    # Section 1: Wrong Language
    if wrong_lang:
        report.append("---")
        report.append("## 🔴 CRITICAL: Translated Pages Linking to English Versions")
        report.append("")
        report.append("These links on translated pages point to the English version instead of the matching translated version.")
        report.append("")
        report.append("| # | Source Page (Lang) | Link Text | Current Link | Fix |")
        report.append("|---|-------------------|-----------|--------------|-----|")
        for i, item in enumerate(wrong_lang, 1):
            src_path = unquote(urlparse(item["source"]).path)[:50]
            report.append(f"| {i} | `{src_path}` ({item['source_lang'].upper()}) | {item['text'][:25]} | `{item['href'][:40]}` | {item['fix']} |")
        report.append("")
    
    # Section 2: English-Only
    if english_only:
        report.append("---")
        report.append("## 🟡 WARNING: Links to English-Only Pages")
        report.append("")
        report.append("These translated pages link to pages that only exist in English (VAT Quiz, Terms, etc).")
        report.append("")
        report.append("| # | Source Page (Lang) | Link Text | English-Only Link | Action |")
        report.append("|---|-------------------|-----------|-------------------|--------|")
        for i, item in enumerate(english_only, 1):
            src_path = unquote(urlparse(item["source"]).path)[:50]
            report.append(f"| {i} | `{src_path}` ({item['source_lang'].upper()}) | {item['text'][:25]} | `{item['href'][:40]}` | {item['fix']} |")
        report.append("")
    
    # Section 3: Cross-Language
    if cross_lang:
        report.append("---")
        report.append("## 🟠 Cross-Language Errors (ZH↔RU)")
        report.append("")
        report.append("| # | Source Page (Lang) | Link Text | Wrong Link | Fix |")
        report.append("|---|-------------------|-----------|------------|-----|")
        for i, item in enumerate(cross_lang, 1):
            src_path = unquote(urlparse(item["source"]).path)[:50]
            report.append(f"| {i} | `{src_path}` ({item['source_lang'].upper()}) | {item['text'][:25]} | `{item['href'][:40]}` | {item['fix']} |")
        report.append("")
    
    # Detailed by page
    report.append("---")
    report.append("## Detailed Breakdown by Page")
    report.append("")
    
    for url, data in sorted(all_issues.items()):
        path = unquote(urlparse(url).path)
        report.append(f"### `{path}` ({data['lang'].upper()})")
        report.append(f"**Full URL:** {url}")
        report.append(f"**Issues: {len(data['issues'])}**")
        report.append("")
        for i, issue in enumerate(data["issues"], 1):
            report.append(f"{i}. **Link:** `{issue['href']}`")
            report.append(f"   - Text: \"{issue['text']}\"")
            report.append(f"   - Problem: {issue['issue']}")
            report.append(f"   - Fix: {issue['fix']}")
            report.append("")
    
    report_text = "\n".join(report)
    
    # Save
    with open("/app/cross-language-audit-report.md", "w", encoding="utf-8") as f:
        f.write(report_text)
    
    print(report_text)
    print("\n" + "="*80)
    print("Report saved to: /app/cross-language-audit-report.md")
    print("="*80)

if __name__ == "__main__":
    main()
