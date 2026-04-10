#!/usr/bin/env python3
"""
SEO Audit Script for vat.support
Checks: Meta tags, headings, images, hreflang, schema, performance indicators
"""

import requests
from bs4 import BeautifulSoup
from urllib.parse import urlparse, urljoin
import json
import time
import re

SITE_URL = "https://vat.support"
HEADERS = {"User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"}

def fetch_url(url, timeout=20):
    try:
        resp = requests.get(url, headers=HEADERS, timeout=timeout)
        return resp
    except Exception as e:
        return None

def check_robots_txt():
    """Check robots.txt"""
    resp = fetch_url(f"{SITE_URL}/robots.txt")
    if resp and resp.status_code == 200:
        return {"status": "OK", "content": resp.text[:500]}
    return {"status": "MISSING", "content": None}

def check_sitemap():
    """Check sitemap"""
    sitemaps = [
        f"{SITE_URL}/sitemap.xml",
        f"{SITE_URL}/sitemap_index.xml",
        f"{SITE_URL}/page-sitemap.xml"
    ]
    found = []
    for url in sitemaps:
        resp = fetch_url(url)
        if resp and resp.status_code == 200:
            found.append(url)
    return {"status": "OK" if found else "MISSING", "sitemaps": found}

def audit_page(url):
    """Comprehensive SEO audit of a single page"""
    issues = []
    warnings = []
    good = []
    
    resp = fetch_url(url)
    if not resp:
        return {"error": f"Could not fetch {url}"}
    
    soup = BeautifulSoup(resp.text, "html.parser")
    
    # 1. Title Tag
    title = soup.find("title")
    if title:
        title_text = title.get_text(strip=True)
        title_len = len(title_text)
        if title_len < 30:
            warnings.append(f"Title too short ({title_len} chars): '{title_text[:50]}'")
        elif title_len > 60:
            warnings.append(f"Title too long ({title_len} chars): '{title_text[:60]}...'")
        else:
            good.append(f"Title OK ({title_len} chars)")
    else:
        issues.append("MISSING: Title tag")
    
    # 2. Meta Description
    meta_desc = soup.find("meta", attrs={"name": "description"})
    if meta_desc and meta_desc.get("content"):
        desc_len = len(meta_desc["content"])
        if desc_len < 120:
            warnings.append(f"Meta description too short ({desc_len} chars)")
        elif desc_len > 160:
            warnings.append(f"Meta description too long ({desc_len} chars)")
        else:
            good.append(f"Meta description OK ({desc_len} chars)")
    else:
        issues.append("MISSING: Meta description")
    
    # 3. H1 Tag
    h1_tags = soup.find_all("h1")
    if len(h1_tags) == 0:
        issues.append("MISSING: H1 tag")
    elif len(h1_tags) > 1:
        warnings.append(f"Multiple H1 tags ({len(h1_tags)})")
    else:
        good.append("H1 tag OK (single)")
    
    # 4. Heading Hierarchy
    h2_tags = soup.find_all("h2")
    h3_tags = soup.find_all("h3")
    if len(h2_tags) == 0:
        warnings.append("No H2 tags found")
    else:
        good.append(f"H2 tags: {len(h2_tags)}")
    
    # 5. Images without Alt
    images = soup.find_all("img")
    images_no_alt = [img for img in images if not img.get("alt")]
    if images_no_alt:
        issues.append(f"Images without alt text: {len(images_no_alt)}/{len(images)}")
    elif images:
        good.append(f"All {len(images)} images have alt text")
    
    # 6. Canonical Tag
    canonical = soup.find("link", attrs={"rel": "canonical"})
    if canonical and canonical.get("href"):
        good.append(f"Canonical: {canonical['href'][:50]}")
    else:
        warnings.append("MISSING: Canonical tag")
    
    # 7. Hreflang Tags (critical for multilingual)
    hreflangs = soup.find_all("link", attrs={"rel": "alternate", "hreflang": True})
    if hreflangs:
        langs = [h.get("hreflang") for h in hreflangs]
        good.append(f"Hreflang tags: {', '.join(langs)}")
    else:
        issues.append("MISSING: Hreflang tags (critical for multilingual SEO)")
    
    # 8. Open Graph Tags
    og_title = soup.find("meta", attrs={"property": "og:title"})
    og_desc = soup.find("meta", attrs={"property": "og:description"})
    og_image = soup.find("meta", attrs={"property": "og:image"})
    og_missing = []
    if not og_title: og_missing.append("og:title")
    if not og_desc: og_missing.append("og:description")
    if not og_image: og_missing.append("og:image")
    if og_missing:
        warnings.append(f"Missing Open Graph: {', '.join(og_missing)}")
    else:
        good.append("Open Graph tags complete")
    
    # 9. Twitter Cards
    twitter_card = soup.find("meta", attrs={"name": "twitter:card"})
    if not twitter_card:
        warnings.append("MISSING: Twitter Card meta tags")
    
    # 10. Schema.org Markup
    schema_scripts = soup.find_all("script", attrs={"type": "application/ld+json"})
    if schema_scripts:
        good.append(f"Schema.org markup found ({len(schema_scripts)} scripts)")
    else:
        warnings.append("No Schema.org structured data found")
    
    # 11. Check for Google Fonts (Baidu SEO issue!)
    google_fonts = soup.find_all("link", href=re.compile(r"fonts\.googleapis\.com"))
    google_fonts_css = re.findall(r"fonts\.googleapis\.com", resp.text)
    if google_fonts or google_fonts_css:
        issues.append(f"BAIDU BLOCKER: Google Fonts detected ({len(google_fonts) + len(google_fonts_css)} references)")
    else:
        good.append("No Google Fonts (Baidu-friendly)")
    
    # 12. Check viewport meta
    viewport = soup.find("meta", attrs={"name": "viewport"})
    if viewport:
        good.append("Viewport meta tag present (mobile-friendly)")
    else:
        issues.append("MISSING: Viewport meta tag")
    
    # 13. Check for lazy loading
    lazy_images = soup.find_all("img", attrs={"loading": "lazy"})
    if lazy_images:
        good.append(f"Lazy loading enabled ({len(lazy_images)} images)")
    
    # 14. Internal links count
    internal_links = [a for a in soup.find_all("a", href=True) if "vat.support" in a.get("href", "") or a.get("href", "").startswith("/")]
    external_links = [a for a in soup.find_all("a", href=True) if a.get("href", "").startswith("http") and "vat.support" not in a.get("href", "")]
    good.append(f"Internal links: {len(internal_links)}, External: {len(external_links)}")
    
    # 15. Page size
    page_size_kb = len(resp.content) / 1024
    if page_size_kb > 500:
        warnings.append(f"Large page size: {page_size_kb:.0f}KB")
    else:
        good.append(f"Page size: {page_size_kb:.0f}KB")
    
    return {
        "url": url,
        "issues": issues,
        "warnings": warnings,
        "good": good,
        "title": title.get_text(strip=True)[:60] if title else "N/A"
    }

def main():
    print("="*80)
    print("SEO AUDIT REPORT - vat.support")
    print("="*80)
    print()
    
    # Check robots.txt
    print("## 1. Robots.txt")
    robots = check_robots_txt()
    print(f"Status: {robots['status']}")
    if robots['content']:
        print(f"Content preview:\n{robots['content'][:300]}")
    print()
    
    # Check sitemap
    print("## 2. Sitemap")
    sitemap = check_sitemap()
    print(f"Status: {sitemap['status']}")
    for s in sitemap['sitemaps']:
        print(f"  - {s}")
    print()
    
    # Audit key pages
    pages_to_audit = [
        f"{SITE_URL}/",  # English home
        f"{SITE_URL}/ru/",  # Russian home
        f"{SITE_URL}/zh/",  # Chinese home
        f"{SITE_URL}/schedule-a-call/",
        f"{SITE_URL}/ru/%d0%bf%d0%be%d0%b4%d0%b4%d0%b5%d1%80%d0%b6%d0%ba%d0%b0-%d0%bd%d0%b4%d1%81-%d0%b0%d0%b4%d0%b0%d0%bf%d1%82%d0%b8%d1%80%d0%be%d0%b2%d0%b0%d0%bd%d0%bd%d0%b0%d1%8f-%d0%ba-%d0%b2%d0%b0%d1%88%d0%b5%d0%bc/",
        f"{SITE_URL}/ioss/",
        f"{SITE_URL}/eu-vat/",
    ]
    
    all_issues = []
    all_warnings = []
    
    print("## 3. Page-by-Page Audit")
    print("-"*80)
    
    for url in pages_to_audit:
        print(f"\n### Auditing: {url[:70]}...")
        result = audit_page(url)
        
        if "error" in result:
            print(f"  ERROR: {result['error']}")
            continue
        
        print(f"  Title: {result['title']}")
        
        if result['issues']:
            print(f"  🔴 ISSUES ({len(result['issues'])}):")
            for issue in result['issues']:
                print(f"     - {issue}")
                all_issues.append({"page": url, "issue": issue})
        
        if result['warnings']:
            print(f"  🟡 WARNINGS ({len(result['warnings'])}):")
            for warning in result['warnings']:
                print(f"     - {warning}")
                all_warnings.append({"page": url, "warning": warning})
        
        if result['good']:
            print(f"  🟢 GOOD ({len(result['good'])}):")
            for g in result['good'][:5]:  # Show first 5
                print(f"     - {g}")
        
        time.sleep(0.3)
    
    # Summary
    print("\n" + "="*80)
    print("## SUMMARY")
    print("="*80)
    print(f"Total Critical Issues: {len(all_issues)}")
    print(f"Total Warnings: {len(all_warnings)}")
    
    # Group issues by type
    print("\n### Critical Issues by Type:")
    issue_types = {}
    for item in all_issues:
        issue = item['issue'].split(":")[0]
        if issue not in issue_types:
            issue_types[issue] = 0
        issue_types[issue] += 1
    
    for issue_type, count in sorted(issue_types.items(), key=lambda x: x[1], reverse=True):
        print(f"  - {issue_type}: {count} pages")
    
    print("\n### Top Recommendations:")
    print("1. Add hreflang tags to ALL pages (critical for multilingual SEO)")
    print("2. Ensure no Google Fonts for Baidu compatibility")
    print("3. Add Schema.org structured data")
    print("4. Optimize meta descriptions (120-160 chars)")
    print("5. Add missing alt text to images")

if __name__ == "__main__":
    main()
