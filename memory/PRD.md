# VAT.support Website Redesign - PRD

## Original Problem Statement
Redesign the WordPress website `vat.support` (Picostrap5 child theme) to a modern "Precision Finance" aesthetic while strictly adhering to Chinese SEO compatibility (Baidu) by removing all Google Fonts. Standardize all service pages to a uniform full-width layout, remove mobile horizontal scrolling, completely rewrite the Contact page to include an interactive map and office details, and generate a thorough site-wide audit report identifying incorrect cross-language internal links.

## User Personas
- International e-commerce businesses seeking VAT compliance services
- Russian-speaking businesses (RU localization)  
- Chinese-speaking businesses (ZH localization for Baidu SEO)
- Accountancy firms with international clients

## Core Requirements
1. **Baidu SEO Compliance** - No Google Fonts API calls; use system fonts only
2. **Cross-Language Link Consistency** - All translated pages must link to matching language versions
3. **Contact Page Redesign** - Interactive map with office locations (UK, CZ, Beijing)
4. **Full-width responsive layouts** - No horizontal scrolling on mobile

---

## Completed Work

### 2026-04-10: Cross-Language Link Audit (COMPLETED)
- Scanned 124 translated (ZH/RU) pages from sitemap
- **Found 1,058 cross-language linking errors**
- Generated comprehensive report: `/app/cross-language-audit-report.md`
- Key findings:
  - Navigation menus on ALL translated pages link to English versions
  - "Log in", "Get Started", "English" links are most common offenders
  - Some ZH pages incorrectly link to RU versions and vice versa

### Contact Page Code (COMPLETED - Awaiting User Verification)
- Created HTML/CSS block with equirectangular world map
- Added office markers for UK, Czech Republic, and Beijing
- Used system fonts only (Baidu-compliant)
- CTA links to existing `/schedule-a-call/` page
- Saved to: `/app/contact-page-code.html`

---

## Prioritized Backlog

### P0 - Critical (User to Action)
- **Fix cross-language links** - Use audit report to update links in WordPress admin
- **Verify Contact Page** - Paste code from `/app/contact-page-code.html` and confirm map accuracy

### P1 - High Priority (Blocked)
- **Logo Optimization** - Waiting for user to provide high-res PNG or SVG file

### P2 - Future Tasks
- Implement Baidu SEO files for `bakode.cn`
- Standardize all service pages to uniform full-width layout
- Remove mobile horizontal scrolling issues

---

## Technical Notes
- **Do NOT edit functions.php** - Breaks navigation menu (missing navwalker)
- All HTML blocks use inline CSS for WordPress Custom HTML blocks
- System font stack: `-apple-system, BlinkMacSystemFont, "Segoe UI", "PingFang SC", "Hiragino Sans GB", "Microsoft YaHei", "Helvetica Neue", Arial, sans-serif`

---

## Files Reference
- `/app/cross-language-audit-report.md` - Full 6,878-line audit report
- `/app/contact-page-code.html` - Contact page HTML block
- `/app/audit_links_v2.py` - Python script used for audit

---

## Key Insights from Audit
The majority of issues come from:
1. **Global header/navigation** - Links to English pages on all translated pages
2. **Footer links** - Not localized
3. **Language switcher** - "English" link appears on translated pages
4. **CTA buttons** - "Get Started", "Log in", "Schedule a Call" link to English

**Recommendation**: Fix the header/footer templates in WordPress theme to use language-aware URLs. This will resolve ~80% of issues automatically.
