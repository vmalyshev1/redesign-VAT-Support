<?php
/**
 * VAT.SUPPORT - Precision Finance Redesign
 * =========================================
 * Add this code to your child theme's functions.php file
 * Location: /wp-content/themes/vat-child/functions.php
 * 
 * NO GOOGLE FONTS VERSION - For Chinese SEO (Baidu compatibility)
 * 
 * IMPORTANT: Add this AFTER any existing code in functions.php
 */

/**
 * Enqueue Precision Finance Override Stylesheet
 * Priority 9999 ensures it loads LAST and overrides theme styles
 * NO Google Fonts - uses system fonts only
 */
function vat_precision_finance_styles() {
    // Main override stylesheet (no external font dependencies)
    wp_enqueue_style(
        'precision-finance-override',
        get_stylesheet_directory_uri() . '/precision-finance-override.css',
        array(), // No dependencies
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
