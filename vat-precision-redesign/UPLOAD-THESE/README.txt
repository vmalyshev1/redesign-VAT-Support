# Files to Upload via FTP

## Location on your server:
`/wp-content/themes/vat-child/`

## Files in this folder:

1. **functions.php** - Replace your existing functions.php
   - Contains Google Fonts blocking code
   - All your existing functionality preserved

2. **precision-finance-override.css** - Upload to same folder
   - MINIMAL CSS - only sets system fonts
   - Does NOT override Bootstrap navbar or buttons
   - Won't break your menu

## What these files do:
- Block ALL Google Fonts requests (Baidu SEO compliant)
- Use system fonts that work in China (PingFang SC, Microsoft YaHei, etc.)
- Preserve ALL existing theme functionality

## After uploading:
Clear any cache (browser + WordPress cache plugin if you have one)
