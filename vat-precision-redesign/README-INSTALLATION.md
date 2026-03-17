# VAT.SUPPORT - Precision Finance Redesign

## Installation Guide

This package transforms your current green-themed vat.support website into the modern "Precision Finance" teal aesthetic.

---

## Package Contents

```
vat-precision-redesign/
├── precision-finance-override.css   # Main stylesheet (THE KEY FILE)
├── functions-snippet.php            # PHP code to add to functions.php
├── html-templates/
│   ├── hero-section.html           # New hero section HTML
│   ├── trust-bar.html              # Trust badges bar
│   ├── services-grid.html          # Services section
│   ├── how-it-works.html           # Process steps section
│   └── testimonials.html           # Client testimonials
└── README-INSTALLATION.md          # This file
```

---

## Installation Steps

### Step 1: Upload the CSS File

1. Open **FileZilla** and connect to your server
2. Navigate to: `/web/vat.support/public_html/wp-content/themes/vat-child/`
3. Upload `precision-finance-override.css` to this folder

### Step 2: Update functions.php

1. In FileZilla, open the file:
   `/web/vat.support/public_html/wp-content/themes/vat-child/functions.php`

2. Add the following code at the **END** of the file (before the closing `?>` if present):

```php
/**
 * Enqueue Precision Finance Override Stylesheet
 * Priority 9999 ensures it loads LAST and overrides theme styles
 */
function vat_precision_finance_styles() {
    // Google Fonts - DM Sans & DM Serif Display
    wp_enqueue_style(
        'precision-finance-fonts',
        'https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&family=DM+Serif+Display:ital@0;1&display=swap',
        array(),
        null
    );
    
    // Main override stylesheet
    wp_enqueue_style(
        'precision-finance-override',
        get_stylesheet_directory_uri() . '/precision-finance-override.css',
        array(),
        '1.0.0'
    );
}
add_action('wp_enqueue_scripts', 'vat_precision_finance_styles', 9999);

/**
 * Add sticky navbar scroll effect
 */
function vat_navbar_scroll_script() {
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const navbar = document.querySelector('.navbar, .site-header, #main-menu');
        if (navbar) {
            window.addEventListener('scroll', function() {
                if (window.scrollY > 50) {
                    navbar.classList.add('scrolled');
                } else {
                    navbar.classList.remove('scrolled');
                }
            });
        }
    });
    </script>
    <?php
}
add_action('wp_footer', 'vat_navbar_scroll_script');
```

3. Save the file

### Step 3: Clear Cache & Refresh

1. In WordPress admin, go to any caching plugin and **clear all cache**
2. In your browser, do a **hard refresh**: `Cmd+Shift+R` (Mac) or `Ctrl+Shift+R` (Windows)

---

## Adding the New HTML Sections

The HTML templates in the `html-templates/` folder can be added to your pages using:

### Option A: Gutenberg Editor
1. Add a "Custom HTML" block
2. Paste the template code
3. Save the page

### Option B: Page Builder (Elementor, etc.)
1. Add an HTML widget
2. Paste the template code
3. Update the page

---

## What This Changes

| Element | Before | After |
|---------|--------|-------|
| **Primary Color** | Green (#3e8e41) | Teal (#0d9488) |
| **Typography** | System fonts | DM Sans + DM Serif Display |
| **Navbar** | Static | Sticky with glass-blur effect |
| **Hero** | Image background | Dark gradient with grid pattern |
| **Buttons** | Basic rounded | Modern with hover effects |
| **Cards** | Basic borders | Elevated with hover animations |

---

## Troubleshooting

### Styles not applying?
1. Check that the CSS file is in the correct folder
2. Verify the functions.php code was added correctly
3. Clear all caches (WordPress, browser, CDN)
4. Check browser console for 404 errors

### Old colors still showing?
The Picostrap5 theme stores settings in the database. The override stylesheet with priority 9999 should override these, but if not:
1. Go to **Appearance > Customize**
2. Find any color settings and update them to match the new teal palette
3. Click "Publish" to save

### Fonts not loading?
Check that the Google Fonts line is correctly added in functions.php

---

## Color Reference

```css
--teal-50:  #f0fdfa    /* Lightest - backgrounds */
--teal-100: #ccfbf1
--teal-200: #99f6e4
--teal-300: #5eead4    /* Accents */
--teal-400: #2dd4bf
--teal-500: #14b8a6    /* Primary buttons */
--teal-600: #0d9488    /* Main brand color */
--teal-700: #0f766e    /* Hover states */
--teal-800: #115e59
--teal-900: #134e4a
--teal-950: #042f2e    /* Darkest */
```

---

## Need Help?

If you encounter any issues, the key things to check:
1. CSS file location is correct
2. functions.php code is at the END of the file
3. No PHP syntax errors (check for missing semicolons)
4. All caches are cleared
