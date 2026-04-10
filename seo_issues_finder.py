#!/usr/bin/env python3
"""
SEO Issue Finder - Google Fonts & Missing Meta Descriptions
"""

import requests
from bs4 import BeautifulSoup
import re
import time

SITEMAP_URL = "https://vat.support/page-sitemap.xml"
HEADERS = {"User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"}

def fetch_url(url, timeout=20):
    try:
        resp = requests.get(url, headers=HEADERS, timeout=timeout)
        return resp
    except:
        return None

def get_all_pages():
    """Get all page URLs from sitemap"""
    resp = fetch_url(SITEMAP_URL)
    if not resp:
        return []
    soup = BeautifulSoup(resp.text, "xml")
    urls = []
    for loc in soup.find_all("loc"):
        url = loc.text.strip()
        # Filter out images
        if not any(ext in url.lower() for ext in ['.png', '.jpg', '.jpeg', '.gif', '.webp', '.pdf', '.svg']):
            urls.append(url)
    return urls

def check_page(url):
    """Check a page for Google Fonts and Meta Description"""
    resp = fetch_url(url)
    if not resp:
        return None
    
    soup = BeautifulSoup(resp.text, "html.parser")
    html_text = resp.text
    
    # Check for Google Fonts
    google_fonts_links = soup.find_all("link", href=re.compile(r"fonts\.googleapis\.com"))
    google_fonts_in_css = re.findall(r"fonts\.googleapis\.com[^\"'\s]*", html_text)
    has_google_fonts = len(google_fonts_links) > 0 or len(google_fonts_in_css) > 0
    google_fonts_refs = list(set([l.get("href", "") for l in google_fonts_links] + google_fonts_in_css))
    
    # Check Meta Description
    meta_desc = soup.find("meta", attrs={"name": "description"})
    meta_desc_content = ""
    if meta_desc and meta_desc.get("content"):
        meta_desc_content = meta_desc["content"].strip()
    
    has_meta_desc = len(meta_desc_content) >= 50  # At least 50 chars to be meaningful
    
    # Get title
    title = soup.find("title")
    title_text = title.get_text(strip=True) if title else "N/A"
    
    return {
        "url": url,
        "title": title_text[:60],
        "has_google_fonts": has_google_fonts,
        "google_fonts_refs": google_fonts_refs[:3],  # First 3 refs
        "has_meta_desc": has_meta_desc,
        "meta_desc_length": len(meta_desc_content),
        "meta_desc_preview": meta_desc_content[:80] if meta_desc_content else ""
    }

def main():
    print("Fetching all pages from sitemap...")
    all_urls = get_all_pages()
    print(f"Found {len(all_urls)} pages\n")
    
    google_fonts_pages = []
    missing_meta_desc_pages = []
    
    for i, url in enumerate(all_urls, 1):
        print(f"[{i}/{len(all_urls)}] Checking: {url[:60]}...", end=" ")
        
        result = check_page(url)
        if not result:
            print("FAILED")
            continue
        
        issues = []
        if result["has_google_fonts"]:
            google_fonts_pages.append(result)
            issues.append("FONTS")
        if not result["has_meta_desc"]:
            missing_meta_desc_pages.append(result)
            issues.append("NO-META")
        
        if issues:
            print(f"⚠️  {', '.join(issues)}")
        else:
            print("✓")
        
        time.sleep(0.2)
    
    # Generate Report
    print("\n" + "="*80)
    print("SEO ISSUES REPORT")
    print("="*80)
    
    # Google Fonts Report
    print("\n## 🔴 GOOGLE FONTS DETECTED (Baidu Blocker)")
    print(f"Total pages with Google Fonts: {len(google_fonts_pages)}")
    print("-"*80)
    if google_fonts_pages:
        print("\n| # | Page URL | Title |")
        print("|---|----------|-------|")
        for i, page in enumerate(google_fonts_pages, 1):
            short_url = page["url"].replace("https://vat.support", "")
            print(f"| {i} | `{short_url}` | {page['title'][:40]} |")
        
        print("\n### Google Fonts References Found:")
        all_refs = set()
        for page in google_fonts_pages:
            all_refs.update(page["google_fonts_refs"])
        for ref in list(all_refs)[:10]:
            print(f"  - {ref[:80]}")
    else:
        print("✅ No Google Fonts found!")
    
    # Missing Meta Description Report
    print("\n\n## 🔴 MISSING META DESCRIPTIONS")
    print(f"Total pages without proper meta description: {len(missing_meta_desc_pages)}")
    print("-"*80)
    
    if missing_meta_desc_pages:
        print("\n| # | Page URL | Title | Current Meta Length |")
        print("|---|----------|-------|---------------------|")
        for i, page in enumerate(missing_meta_desc_pages, 1):
            short_url = page["url"].replace("https://vat.support", "")[:50]
            meta_status = f"{page['meta_desc_length']} chars" if page['meta_desc_length'] > 0 else "EMPTY"
            print(f"| {i} | `{short_url}` | {page['title'][:30]} | {meta_status} |")
    
    # Summary
    print("\n\n## SUMMARY")
    print(f"- Pages with Google Fonts: {len(google_fonts_pages)}")
    print(f"- Pages missing meta descriptions: {len(missing_meta_desc_pages)}")
    print(f"- Total pages scanned: {len(all_urls)}")

if __name__ == "__main__":
    main()
