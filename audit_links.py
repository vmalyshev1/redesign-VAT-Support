#!/usr/bin/env python3
"""
Cross-Language Link Audit Script for vat.support
Scans all pages in the sitemap and flags internal links that point to wrong language versions.
"""

import requests
from bs4 import BeautifulSoup
from urllib.parse import urlparse, urljoin
import re
import time

SITEMAP_URL = "https://vat.support/page-sitemap.xml"
BASE_DOMAIN = "vat.support"

# Known English-only pages that should NEVER be linked from translated pages
ENGLISH_ONLY_PATTERNS = [
    "vat-quiz",
    "client-terms-conditions",
    "terms-conditions",
    "privacy-policy",
]

def fetch_url(url, retries=3):
    """Fetch URL content with retries."""
    headers = {
        "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
    }
    for attempt in range(retries):
        try:
            resp = requests.get(url, headers=headers, timeout=30)
            resp.raise_for_status()
            return resp.text
        except Exception as e:
            if attempt == retries - 1:
                print(f"Failed to fetch {url}: {e}")
                return None
            time.sleep(1)
    return None

def get_sitemap_urls(sitemap_url):
    """Parse sitemap XML and extract all page URLs."""
    content = fetch_url(sitemap_url)
    if not content:
        return []
    
    soup = BeautifulSoup(content, "xml")
    urls = []
    for loc in soup.find_all("loc"):
        urls.append(loc.text.strip())
    return urls

def get_page_language(url):
    """Determine the language of a page based on URL path."""
    parsed = urlparse(url)
    path = parsed.path
    
    if path.startswith("/zh/") or "/zh/" in path:
        return "zh"
    elif path.startswith("/ru/") or "/ru/" in path:
        return "ru"
    else:
        return "en"

def is_internal_link(href, base_domain):
    """Check if a link is internal to the site."""
    if not href:
        return False
    if href.startswith("#") or href.startswith("mailto:") or href.startswith("tel:"):
        return False
    if href.startswith("/"):
        return True
    parsed = urlparse(href)
    return parsed.netloc == "" or base_domain in parsed.netloc

def is_english_only_page(href):
    """Check if link points to a known English-only page."""
    href_lower = href.lower()
    for pattern in ENGLISH_ONLY_PATTERNS:
        if pattern in href_lower:
            return True
    return False

def get_link_language(href):
    """Determine language of a link target."""
    if "/zh/" in href:
        return "zh"
    elif "/ru/" in href:
        return "ru"
    else:
        return "en"

def audit_page(url):
    """Audit a single page for cross-language link issues."""
    issues = []
    page_lang = get_page_language(url)
    
    # Skip English pages - they can link anywhere
    if page_lang == "en":
        return issues
    
    content = fetch_url(url)
    if not content:
        return [{"type": "error", "message": "Could not fetch page"}]
    
    soup = BeautifulSoup(content, "html.parser")
    
    # Find all links
    for a_tag in soup.find_all("a", href=True):
        href = a_tag.get("href", "").strip()
        
        if not is_internal_link(href, BASE_DOMAIN):
            continue
        
        # Normalize the href
        if href.startswith("/"):
            full_href = f"https://{BASE_DOMAIN}{href}"
        else:
            full_href = href
        
        link_lang = get_link_language(href)
        link_text = a_tag.get_text(strip=True)[:50] if a_tag.get_text(strip=True) else "[no text]"
        
        # Issue 1: Translated page linking to English version instead of translated version
        if page_lang != "en" and link_lang == "en":
            # Check if it's a known English-only page
            if is_english_only_page(href):
                issues.append({
                    "type": "english_only_page",
                    "href": href,
                    "link_text": link_text,
                    "page_lang": page_lang,
                    "message": f"Links to English-only page (no {page_lang.upper()} version exists)",
                    "suggestion": f"Remove link or create {page_lang.upper()} version of this page"
                })
            else:
                # This should link to the translated version
                issues.append({
                    "type": "wrong_language",
                    "href": href,
                    "link_text": link_text,
                    "page_lang": page_lang,
                    "link_lang": link_lang,
                    "message": f"{page_lang.upper()} page links to EN version",
                    "suggestion": f"Change to /{page_lang}{href}" if href.startswith("/") else f"Add /{page_lang}/ prefix"
                })
        
        # Issue 2: ZH page linking to RU or vice versa
        elif page_lang == "zh" and link_lang == "ru":
            issues.append({
                "type": "cross_language",
                "href": href,
                "link_text": link_text,
                "page_lang": page_lang,
                "link_lang": link_lang,
                "message": "ZH page links to RU version",
                "suggestion": f"Change /ru/ to /zh/ in the URL"
            })
        elif page_lang == "ru" and link_lang == "zh":
            issues.append({
                "type": "cross_language",
                "href": href,
                "link_text": link_text,
                "page_lang": page_lang,
                "link_lang": link_lang,
                "message": "RU page links to ZH version",
                "suggestion": f"Change /zh/ to /ru/ in the URL"
            })
    
    return issues

def generate_report(all_issues):
    """Generate a comprehensive Markdown report."""
    report = []
    report.append("# Cross-Language Link Audit Report")
    report.append(f"## Site: vat.support")
    report.append(f"## Generated: {time.strftime('%Y-%m-%d %H:%M:%S')}")
    report.append("")
    
    # Summary
    total_pages_with_issues = len([p for p in all_issues if all_issues[p]])
    total_issues = sum(len(issues) for issues in all_issues.values())
    
    report.append("## Summary")
    report.append(f"- **Total pages scanned:** {len(all_issues)}")
    report.append(f"- **Pages with issues:** {total_pages_with_issues}")
    report.append(f"- **Total issues found:** {total_issues}")
    report.append("")
    
    # Categorize issues
    wrong_lang_issues = []
    english_only_issues = []
    cross_lang_issues = []
    
    for page_url, issues in all_issues.items():
        for issue in issues:
            issue["source_page"] = page_url
            if issue.get("type") == "wrong_language":
                wrong_lang_issues.append(issue)
            elif issue.get("type") == "english_only_page":
                english_only_issues.append(issue)
            elif issue.get("type") == "cross_language":
                cross_lang_issues.append(issue)
    
    # Section 1: Wrong Language Links (most critical)
    if wrong_lang_issues:
        report.append("---")
        report.append("## 1. Translated Pages Linking to English Versions (CRITICAL)")
        report.append("These links should point to the translated version of the page.")
        report.append("")
        report.append("| Source Page | Link Text | Current Link | Suggested Fix |")
        report.append("|-------------|-----------|--------------|---------------|")
        for issue in wrong_lang_issues:
            source = issue["source_page"].replace("https://vat.support", "")
            report.append(f"| `{source}` | {issue['link_text'][:30]} | `{issue['href']}` | `{issue['suggestion']}` |")
        report.append("")
    
    # Section 2: Links to English-Only Pages
    if english_only_issues:
        report.append("---")
        report.append("## 2. Links to English-Only Pages (Needs Review)")
        report.append("These translated pages link to pages that only exist in English (e.g., VAT Quiz, Terms & Conditions).")
        report.append("")
        report.append("| Source Page | Link Text | English-Only Link | Action Needed |")
        report.append("|-------------|-----------|-------------------|---------------|")
        for issue in english_only_issues:
            source = issue["source_page"].replace("https://vat.support", "")
            report.append(f"| `{source}` | {issue['link_text'][:30]} | `{issue['href']}` | {issue['suggestion']} |")
        report.append("")
    
    # Section 3: Cross-Language Issues (ZH<->RU)
    if cross_lang_issues:
        report.append("---")
        report.append("## 3. Cross-Language Linking Errors")
        report.append("Pages linking between wrong non-English languages.")
        report.append("")
        report.append("| Source Page | Link Text | Wrong Link | Suggested Fix |")
        report.append("|-------------|-----------|------------|---------------|")
        for issue in cross_lang_issues:
            source = issue["source_page"].replace("https://vat.support", "")
            report.append(f"| `{source}` | {issue['link_text'][:30]} | `{issue['href']}` | `{issue['suggestion']}` |")
        report.append("")
    
    # Detailed breakdown by page
    report.append("---")
    report.append("## Detailed Breakdown by Page")
    report.append("")
    
    for page_url, issues in sorted(all_issues.items()):
        if not issues:
            continue
        
        page_path = page_url.replace("https://vat.support", "")
        page_lang = get_page_language(page_url)
        
        report.append(f"### `{page_path}` ({page_lang.upper()} page)")
        report.append(f"**Issues found: {len(issues)}**")
        report.append("")
        
        for i, issue in enumerate(issues, 1):
            report.append(f"{i}. **Link:** `{issue['href']}`")
            report.append(f"   - Text: \"{issue['link_text']}\"")
            report.append(f"   - Problem: {issue['message']}")
            report.append(f"   - Fix: {issue['suggestion']}")
            report.append("")
    
    # Pages with no issues
    clean_pages = [p for p in all_issues if not all_issues[p]]
    if clean_pages:
        report.append("---")
        report.append("## Pages with No Issues")
        for page in clean_pages:
            page_path = page.replace("https://vat.support", "")
            report.append(f"- `{page_path}`")
    
    return "\n".join(report)

def main():
    print("Fetching sitemap...")
    urls = get_sitemap_urls(SITEMAP_URL)
    print(f"Found {len(urls)} pages in sitemap")
    
    all_issues = {}
    
    for i, url in enumerate(urls, 1):
        page_lang = get_page_language(url)
        print(f"[{i}/{len(urls)}] Auditing: {url} ({page_lang.upper()})")
        
        issues = audit_page(url)
        all_issues[url] = issues
        
        if issues:
            print(f"  -> Found {len(issues)} issue(s)")
        
        # Small delay to be respectful to the server
        time.sleep(0.3)
    
    print("\nGenerating report...")
    report = generate_report(all_issues)
    
    # Save report
    with open("/app/cross-language-audit-report.md", "w", encoding="utf-8") as f:
        f.write(report)
    
    print("\n" + "="*60)
    print("AUDIT COMPLETE!")
    print("="*60)
    print(f"Report saved to: /app/cross-language-audit-report.md")
    print("\n")
    print(report)

if __name__ == "__main__":
    main()
