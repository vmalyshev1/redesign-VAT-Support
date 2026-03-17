# VAT.SUPPORT REDESIGN - Installation Instructions

## FILES INCLUDED

```
vat-redesign-files/
├── _custom.scss                    ← Main SCSS file (replace existing)
├── functions-ADD-TO-EXISTING.php   ← PHP code to ADD to functions.php
└── html-templates/
    ├── hero-section.html           ← New hero HTML for page builder
    ├── trust-bar.html              ← Trust badges HTML
    ├── services-section.html       ← Services grid HTML
    └── footer.html                 ← Footer HTML
```

---

## INSTALLATION STEPS

### Step 1: Upload _custom.scss

1. Open FileZilla
2. Navigate to: `/wp-content/themes/vat-child/sass/`
3. Backup existing `_custom.scss` (rename to `_custom-backup.scss`)
4. Upload the new `_custom.scss`

### Step 2: Update functions.php

1. Navigate to: `/wp-content/themes/vat-child/`
2. Download `functions.php` to your computer
3. Open in a text editor
4. Add the code from `functions-ADD-TO-EXISTING.php` at the END
5. Save and upload back

### Step 3: Recompile SASS

1. Go to WordPress Admin
2. Click "Recompile SASS" in the top admin bar
3. Clear any caching plugins
4. Hard refresh browser (Cmd+Shift+R or Ctrl+Shift+R)

### Step 4: Update Page Content (Optional)

Use the HTML templates in your page builder (LiveCanvas) to update:
- Hero section
- Trust bar
- Services section
- Footer

---

## WHAT CHANGES

| Element | Before | After |
|---------|--------|-------|
| Primary Color | Green #4CAF50 | Teal #1d7d68 |
| Fonts | Inter + Montserrat | DM Sans + DM Serif Display |
| Navbar | Standard | Sticky with glass-blur |
| Buttons | Standard Bootstrap | Rounded 8px with shadows |

---

## TROUBLESHOOTING

**Styles not applying?**
- Clear browser cache
- Clear WordPress cache plugins
- Re-run SASS compile

**Site broken after changes?**
- Restore `_custom-backup.scss`
- Click "Recompile SASS"

---

## IMPORTANT NOTE

The Picostrap5 Customizer may override some SCSS variables. If colors don't change:

1. Go to Appearance → Customize → Colors
2. Set Primary color to: #1d7d68
3. Click Publish
