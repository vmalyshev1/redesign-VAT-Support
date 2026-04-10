<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<?php
/**
 * Helper function to get language-aware "Schedule a Call" URL
 * Supports: English (default), Russian (ru), Chinese (zh)
 */
function vat_get_schedule_call_url() {
    if ( function_exists('pll_current_language') ) {
        $lang = pll_current_language();
        if ( $lang === 'ru' ) {
            return '/ru/%d0%bf%d0%be%d0%b4%d0%b4%d0%b5%d1%80%d0%b6%d0%ba%d0%b0-%d0%bd%d0%b4%d1%81-%d0%b0%d0%b4%d0%b0%d0%bf%d1%82%d0%b8%d1%80%d0%be%d0%b2%d0%b0%d0%bd%d0%bd%d0%b0%d1%8f-%d0%ba-%d0%b2%d0%b0%d1%88%d0%b5%d0%bc/';
        } elseif ( $lang === 'zh' ) {
            return '/zh/%e9%87%8f%e8%ba%ab%e5%ae%9a%e5%88%b6%e7%9a%84%e5%a2%9e%e5%80%bc%e7%a8%8e%e6%94%af%e6%8c%81/';
        }
    }
    return '/schedule-a-call/';
}

/**
 * Helper function to get translated "Get Started" button text
 */
function vat_get_started_text() {
    if ( function_exists('pll_current_language') ) {
        $lang = pll_current_language();
        if ( $lang === 'ru' ) {
            return 'Начать';
        } elseif ( $lang === 'zh' ) {
            return '开始使用';
        }
    }
    return 'Get Started';
}

/**
 * Helper function to get translated "Log in" button text
 */
function vat_get_login_text() {
    if ( function_exists('pll_current_language') ) {
        $lang = pll_current_language();
        if ( $lang === 'ru' ) {
            return 'Войти';
        } elseif ( $lang === 'zh' ) {
            return '登录';
        }
    }
    return 'Log in';
}

/**
 * Helper function to get language-aware home URL
 */
function vat_get_home_url() {
    if ( function_exists('pll_home_url') ) {
        return pll_home_url();
    }
    return home_url('/');
}
?>

<div id="page" class="site">
    <header id="masthead" class="site-header">
        <nav class="navbar navbar-expand-lg fixed-top">
            <div class="container">
                <!-- Logo/Brand -->
                <?php if (!has_custom_logo()) { ?>
                    <?php if (is_front_page() && is_home()): ?>
                        <div class="navbar-brand mb-0">
                            <a rel="home" href="<?php echo esc_url(vat_get_home_url()); ?>" title="<?php echo esc_attr(get_bloginfo('name', 'display')); ?>" itemprop="url">
                                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: var(--teal-600);">
                                    <path d="M12 2L2 7l10 5 10-5-10-5z"></path>
                                    <path d="M2 17l10 5 10-5"></path>
                                    <path d="M2 12l10 5 10-5"></path>
                                </svg>
                                <?php bloginfo('name'); ?>
                            </a>
                        </div>
                    <?php else: ?>
                        <a class="navbar-brand mb-0" rel="home" href="<?php echo esc_url(vat_get_home_url()); ?>" title="<?php echo esc_attr(get_bloginfo('name', 'display')); ?>" itemprop="url">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: var(--teal-600);">
                                <path d="M12 2L2 7l10 5 10-5-10-5z"></path>
                                <path d="M2 17l10 5 10-5"></path>
                                <path d="M2 12l10 5 10-5"></path>
                            </svg>
                            <?php bloginfo('name'); ?>
                        </a>
                    <?php endif; ?>
                <?php } else {
                    the_custom_logo();
                } ?>

                <!-- Mobile Toggle Button -->
                <button class="navbar-toggler d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <!-- Desktop Navigation - Centered -->
                <div class="collapse navbar-collapse justify-content-center" id="navbarNavDropdown">
                    <?php
                    wp_nav_menu(
                        array(
                            'theme_location' => 'primary',
                            'container' => false,
                            'menu_class' => '',
                            'fallback_cb' => '__return_false',
                            'items_wrap' => '<ul id="%1$s" class="navbar-nav %2$s">%3$s</ul>',
                            'walker' => new bootstrap_5_wp_nav_menu_walker()
                        )
                    );
                    ?>
                </div>
                
                <!-- Desktop CTA Buttons -->
                <div class="d-none d-lg-flex gap-2">
                    <a href="https://portal.vat.support" class="btn btn-outline-primary"><?php echo esc_html(vat_get_login_text()); ?></a>
                    <a href="<?php echo esc_url(vat_get_schedule_call_url()); ?>" class="btn btn-primary"><?php echo esc_html(vat_get_started_text()); ?></a>
                </div>
            </div>
        </nav>

        <!-- Offcanvas Menu - Beautiful Mobile Menu -->
        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="offcanvasNavbarLabel">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: var(--teal-600); vertical-align: middle; margin-right: 8px;">
                        <path d="M12 2L2 7l10 5 10-5-10-5z"></path>
                        <path d="M2 17l10 5 10-5"></path>
                        <path d="M2 12l10 5 10-5"></path>
                    </svg>
                    <?php bloginfo('name'); ?>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <nav class="offcanvas-nav">
                    <?php
                    wp_nav_menu(
                        array(
                            'theme_location' => 'primary',
                            'container' => false,
                            'menu_class' => 'offcanvas-menu-list',
                            'fallback_cb' => '__return_false',
                            'items_wrap' => '<ul id="%1$s-mobile" class="%2$s">%3$s</ul>',
                            'depth' => 2,
                            'walker' => new WP_Bootstrap_Navwalker_Simple()
                        )
                    );
                    ?>
                </nav>

                <!-- Mobile CTA Buttons -->
                <div class="offcanvas-cta">
                    <a href="https://portal.vat.support" class="btn btn-outline-primary w-100 mb-2"><?php echo esc_html(vat_get_login_text()); ?></a>
                    <a href="<?php echo esc_url(vat_get_schedule_call_url()); ?>" class="btn btn-primary w-100"><?php echo esc_html(vat_get_started_text()); ?></a>
                </div>
            </div>
        </div>
    </header>

    <div id="content" class="site-content">
