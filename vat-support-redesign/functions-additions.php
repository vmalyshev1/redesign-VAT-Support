<?php
/**
 * VAT.SUPPORT - Additional Functions for Redesign
 * Add this code to your existing functions.php file
 * DO NOT REPLACE - ADD TO EXISTING FILE
 */

// =========================================
// 1. ENQUEUE NEW GOOGLE FONTS
// =========================================
function vat_enqueue_redesign_fonts() {
    // Remove old font enqueue if exists
    wp_dequeue_style('google-fonts');
    
    // Add new DM Sans & DM Serif Display fonts
    wp_enqueue_style(
        'vat-google-fonts', 
        'https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500;9..40,600;9..40,700&family=DM+Serif+Display:ital@0;1&display=swap', 
        [], 
        null
    );
}
add_action('wp_enqueue_scripts', 'vat_enqueue_redesign_fonts', 20);


// =========================================
// 2. NAVBAR SCROLL EFFECT - Add JavaScript
// =========================================
function vat_navbar_scroll_script() {
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const nav = document.querySelector('.navbar');
        if (nav) {
            window.addEventListener('scroll', function() {
                if (window.scrollY > 10) {
                    nav.classList.add('scrolled');
                } else {
                    nav.classList.remove('scrolled');
                }
            }, { passive: true });
        }
    });
    </script>
    <?php
}
add_action('wp_footer', 'vat_navbar_scroll_script');


// =========================================
// 3. ADD NAV BUTTONS TO NAVBAR (Optional)
// =========================================
function vat_add_nav_buttons($items, $args) {
    if ($args->theme_location == 'primary') {
        $items .= '<li class="nav-item ms-lg-3 d-none d-lg-block">';
        $items .= '<a href="/contact" class="btn-nav-ghost me-2">Sign in</a>';
        $items .= '</li>';
        $items .= '<li class="nav-item d-none d-lg-block">';
        $items .= '<a href="/contact" class="btn-nav-primary">Get started free</a>';
        $items .= '</li>';
    }
    return $items;
}
// Uncomment the line below to enable nav buttons:
// add_filter('wp_nav_menu_items', 'vat_add_nav_buttons', 10, 2);


// =========================================
// 4. TRUST BADGES SHORTCODE
// =========================================
function vat_trust_bar_shortcode() {
    ob_start();
    ?>
    <div class="trust-bar">
        <span class="trust-bar-label">Accredited &amp; certified</span>
        <div class="trust-badges">
            <div class="trust-badge">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                </svg>
                HMRC Approved Agent
            </div>
            <div class="trust-badge">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="20 6 9 17 4 12"/>
                </svg>
                ISO 27001 Certified
            </div>
            <div class="trust-badge">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
                GDPR Compliant
            </div>
            <div class="trust-badge">SOC 2 Type II</div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('vat_trust_bar', 'vat_trust_bar_shortcode');


// =========================================
// 5. HERO STATS SHORTCODE
// =========================================
function vat_hero_stats_shortcode($atts) {
    $atts = shortcode_atts([
        'countries' => '50+',
        'filing_rate' => '99.8%',
        'recovered' => '£2.4M',
        'businesses' => '2,400+'
    ], $atts);
    
    ob_start();
    ?>
    <div class="hero-stats-bar">
        <div class="hero-stat">
            <span class="hero-stat-num"><?php echo esc_html($atts['countries']); ?></span>
            <span class="hero-stat-label">Countries covered</span>
        </div>
        <div class="hero-stat">
            <span class="hero-stat-num"><?php echo esc_html($atts['filing_rate']); ?></span>
            <span class="hero-stat-label">On-time filing rate</span>
        </div>
        <div class="hero-stat">
            <span class="hero-stat-num"><?php echo esc_html($atts['recovered']); ?></span>
            <span class="hero-stat-label">VAT recovered</span>
        </div>
        <div class="hero-stat">
            <span class="hero-stat-num"><?php echo esc_html($atts['businesses']); ?></span>
            <span class="hero-stat-label">Businesses worldwide</span>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('vat_hero_stats', 'vat_hero_stats_shortcode');


// =========================================
// 6. SERVICE CARD SHORTCODE
// =========================================
function vat_service_card_shortcode($atts) {
    $atts = shortcode_atts([
        'icon' => '📋',
        'title' => 'Service Title',
        'description' => 'Service description goes here.',
        'tag' => '',
        'link' => '#'
    ], $atts);
    
    ob_start();
    ?>
    <a href="<?php echo esc_url($atts['link']); ?>" class="service-card">
        <div class="service-icon"><?php echo $atts['icon']; ?></div>
        <div class="service-name">
            <?php echo esc_html($atts['title']); ?>
            <span class="service-arrow">→</span>
        </div>
        <div class="service-desc"><?php echo esc_html($atts['description']); ?></div>
        <?php if ($atts['tag']): ?>
            <span class="service-tag"><?php echo esc_html($atts['tag']); ?></span>
        <?php endif; ?>
    </a>
    <?php
    return ob_get_clean();
}
add_shortcode('vat_service_card', 'vat_service_card_shortcode');
