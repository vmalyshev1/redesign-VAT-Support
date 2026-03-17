<?php
/**
 * VAT.SUPPORT REDESIGN - Functions to ADD to your existing functions.php
 * DO NOT REPLACE - ADD these to the END of your existing functions.php
 */

// =========================================
// 1. ENQUEUE GOOGLE FONTS
// =========================================
function vat_enqueue_fonts() {
    wp_enqueue_style('google-fonts', 
        'https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500;9..40,600&family=DM+Serif+Display:ital@0;1&display=swap', 
        [], null);
}
add_action('wp_enqueue_scripts', 'vat_enqueue_fonts');

// =========================================
// 2. NAVBAR SCROLL EFFECT
// =========================================
function vat_navbar_scroll_script() {
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const nav = document.querySelector('.navbar');
        if (nav) {
            window.addEventListener('scroll', function() {
                nav.classList.toggle('scrolled', window.scrollY > 10);
            }, { passive: true });
        }
    });
    </script>
    <?php
}
add_action('wp_footer', 'vat_navbar_scroll_script');
