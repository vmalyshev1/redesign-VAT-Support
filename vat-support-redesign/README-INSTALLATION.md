# VAT.SUPPORT Redesign - Installation Guide

## 📦 Package Contents

| File | Purpose | Install Location |
|------|---------|------------------|
| `sass/_custom.scss` | Main styles (colors, components, responsive) | `/wp-content/themes/vat-child/sass/_custom.scss` |
| `functions-additions.php` | New PHP functions (fonts, shortcodes, scroll effect) | Add to existing `functions.php` |
| `templates/hero-section.html` | New hero HTML for LiveCanvas | Copy into LiveCanvas |
| `templates/services-section.html` | Services grid HTML | Copy into LiveCanvas |
| `templates/footer-template.html` | New footer HTML | Copy into LiveCanvas or footer.php |

---

## 🚀 Installation Steps

### Step 1: Backup Your Theme
Before making changes, backup your current `vat-child` theme folder via FTP.

### Step 2: Upload SCSS File
1. Connect via FileZilla to: `/web/vat.support/public_html/wp-content/themes/vat-child/sass/`
2. **Replace** `_custom.scss` with the new file
3. Alternatively, **append** the contents to your existing `_custom.scss`

### Step 3: Update functions.php
1. Open `/wp-content/themes/vat-child/functions.php`
2. **Add** (don't replace) the contents of `functions-additions.php` at the end
3. Save the file

### Step 4: Recompile SASS
1. Log into WordPress Admin
2. Go to **Appearance** → look for "**Recompile SASS**" button/link
3. Click to recompile
4. Clear any cache plugins (WP Rocket, LiteSpeed, W3TC)

### Step 5: Update LiveCanvas Pages
1. Edit your homepage in LiveCanvas
2. Replace the current hero section with `hero-section.html` contents
3. Update the services section with `services-section.html`
4. Update footer with `footer-template.html`

---

## 🎨 Color Reference

| Variable | Hex | Usage |
|----------|-----|-------|
| `$primary` | `#1d7d68` | Main teal - buttons, links |
| `$primary-dark` | `#0f5144` | Hover states |
| `$primary-darker` | `#0a3d33` | Hero backgrounds |
| `$primary-light` | `#5dd4b8` | Accents, highlights |
| `$primary-lighter` | `#cff5ed` | Light backgrounds |
| `$slate-900` | `#0f1923` | Footer, dark text |
| `$slate-600` | `#4a5e72` | Body text |
| `$slate-100` | `#e6edf3` | Borders, dividers |

---

## 📱 Responsive Breakpoints

- **Desktop**: 992px+
- **Tablet**: 768px - 991px  
- **Mobile**: < 768px

All components are mobile-responsive by default.

---

## 🔧 Shortcodes Available

After adding the functions, you can use these shortcodes:

```
[vat_trust_bar]
- Displays the accreditation badges bar

[vat_hero_stats countries="50+" filing_rate="99.8%" recovered="£2.4M" businesses="2,400+"]
- Displays the stats bar with customizable numbers

[vat_service_card icon="📋" title="IOSS" description="Import One-Stop Shop..." tag="Popular" link="/ioss"]
- Creates a service card
```

---

## ✅ Post-Installation Checklist

- [ ] SASS recompiled successfully
- [ ] Fonts loading correctly (DM Sans, DM Serif Display)
- [ ] Navbar sticky effect working on scroll
- [ ] Hero section displays with teal gradient
- [ ] Trust bar shows below hero
- [ ] Services grid displays correctly
- [ ] Footer updated with new design
- [ ] Mobile view tested
- [ ] Cache cleared

---

## 🆘 Troubleshooting

**Styles not applying?**
- Clear browser cache (Ctrl+Shift+R)
- Clear WordPress cache plugins
- Re-run SASS compile

**Fonts not loading?**
- Check if `vat_enqueue_redesign_fonts` function was added
- Verify no caching is blocking Google Fonts

**Navbar scroll effect not working?**
- Ensure `vat_navbar_scroll_script` function is in functions.php
- Check browser console for JS errors

---

## 📞 Need Help?

If you encounter any issues during installation, share:
1. Screenshot of the error
2. Which step failed
3. Browser console errors (F12 → Console tab)

I can help debug and provide fixes!
