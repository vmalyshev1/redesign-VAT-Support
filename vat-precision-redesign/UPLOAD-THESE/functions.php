<?php
/*
        _               _                  _____        _     _ _     _   _   _                         
       (_)             | |                | ____|      | |   (_) |   | | | | | |                        
  _ __  _  ___ ___  ___| |_ _ __ __ _ _ __| |__     ___| |__  _| | __| | | |_| |__   ___ _ __ ___   ___ 
 | '_ \| |/ __/ _ \/ __| __| '__/ _` | '_ \___ \   / __| '_ \| | |/ _` | | __| '_ \ / _ \ '_ ` _ \ / _ \
 | |_) | | (_| (_) \__ \ |_| | | (_| | |_) |__) | | (__| | | | | | (_| | | |_| | | |  __/ | | | | |  __/
 | .__/|_|\___\___/|___/\__|_|  \__,_| .__/____/   \___|_| |_|_|_|\__,_|  \__|_| |_|\___|_| |_| |_|\___|
 | |                                 | |                                                                
 |_|                                 |_|                                                                

                                                       
*************************************** WELCOME TO PICOSTRAP ***************************************

********************* THE BEST WAY TO EXPERIENCE SASS, BOOTSTRAP AND WORDPRESS *********************

    PLEASE WATCH THE VIDEOS FOR BEST RESULTS:
    https://www.youtube.com/playlist?list=PLtyHhWhkgYU8i11wu-5KJDBfA9C-D4Bfl

*/

//LOAD LC CONFIG TO DEFINE FRAMEWORK
require_once ("livecanvas/configuration.php");

// ENQUEUE PRECISION FINANCE OVERRIDE CSS (loads LAST to override everything)
add_action('wp_enqueue_scripts', function() {
    wp_enqueue_style(
        'precision-finance-override',
        get_stylesheet_directory_uri() . '/precision-finance-override.css',
        array(), // no dependencies, loads after all other styles
        filemtime(get_stylesheet_directory() . '/precision-finance-override.css'),
        'all'
    );
}, 9999); // Very high priority to load LAST

// BLOCK ALL GOOGLE FONTS - China/Baidu SEO Compatibility
add_action('wp_enqueue_scripts', function() {
    // Dequeue any Google Fonts that might be enqueued
    global $wp_styles;
    if (isset($wp_styles->registered)) {
        foreach ($wp_styles->registered as $handle => $style) {
            if (isset($style->src) && (
                strpos($style->src, 'fonts.googleapis.com') !== false ||
                strpos($style->src, 'fonts.gstatic.com') !== false
            )) {
                wp_dequeue_style($handle);
                wp_deregister_style($handle);
            }
        }
    }
}, 9998);

// Block Google Fonts at the source level
add_filter('style_loader_src', function($src) {
    if (strpos($src, 'fonts.googleapis.com') !== false || strpos($src, 'fonts.gstatic.com') !== false) {
        return false;
    }
    return $src;
}, 9999);

// DE-ENQUEUE PARENT THEME BOOTSTRAP JS BUNDLE
add_action( 'wp_print_scripts', function(){
    wp_dequeue_script( 'bootstrap5' );
    //wp_dequeue_script( 'dark-mode-switch' );  //optionally
}, 100 );

// ENQUEUE THE BOOTSTRAP JS BUNDLE (AND EVENTUALLY MORE LIBS) FROM THE CHILD THEME DIRECTORY
add_action( 'wp_enqueue_scripts', function() {
    //enqueue js in footer, defer
    wp_enqueue_script( 'bootstrap5-childtheme', get_stylesheet_directory_uri() . "/js/bootstrap.bundle.min.js", array(), null, array('strategy' => 'defer', 'in_footer' => true)  );
    
    //optional: example of how to globally lazyload js files eg lottie player, using defer
    //wp_enqueue_script( 'lottie-player', 'https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js', array(), null, array('strategy' => 'defer', 'in_footer' => true)  );
}, 101);

// HACK HERE: ENQUEUE YOUR CUSTOM JS FILES, IF NEEDED
add_action( 'wp_enqueue_scripts', function() {	   
    
    // Include custom.js for navbar scroll effect and other interactions
    wp_enqueue_script('custom', get_stylesheet_directory_uri() . '/js/custom.js', array(/* 'jquery' */), '2.0', array('strategy' => 'defer', 'in_footer' => true) ); 

    //UNCOMMENT next 3 rows to load the js file only on one page
    //if (is_page('mypageslug')) {
    //    wp_enqueue_script('custom', get_stylesheet_directory_uri() . '/js/custom.js', array(/* 'jquery' */), null, array('strategy' => 'defer', 'in_footer' => true) ); 
    //}  

}, 102);

// VAT Landing template: base CSS (no third-party font CDNs)
add_action('wp_enqueue_scripts', function () {
    if (!is_page() || !is_page_template('page-templates/page-vat-landing.php')) {
        return;
    }
    wp_enqueue_style(
        'vat-landing',
        get_stylesheet_directory_uri() . '/css/vat-landing.css',
        array(),
        '1.0.0'
    );
}, 103);

// OPTIONAL: ADD MORE NAV MENUS
//register_nav_menus( array( 'third' => __( 'Third Menu', 'picostrap' ), 'fourth' => __( 'Fourth Menu', 'picostrap' ), 'fifth' => __( 'Fifth Menu', 'picostrap' ), ) );
// THEN USE SHORTCODE:  [lc_nav_menu theme_location="third" container_class="" container_id="" menu_class="navbar-nav"]

// OPTIONAL: FOR SECURITY: DISABLE APPLICATION PASSWORDS. Uncomment if needed
//add_filter( 'wp_is_application_passwords_available', '__return_false' );

// ADD YOUR CUSTOM PHP CODE DOWN BELOW /////////////////////////

// No Google Fonts — system font stacks only (SEO / performance / China-friendly).
// Add @font-face in sass/_custom.scss if you self-host a font.

// Add theme support for various WordPress features
add_action('after_setup_theme', function() {
    // Add support for custom logo
    add_theme_support('custom-logo', array(
        'height'      => 50,
        'width'       => 200,
        'flex-height' => true,
        'flex-width'  => true,
    ));
    
    // Add support for post thumbnails
    add_theme_support('post-thumbnails');
    
    // Add support for title tag
    add_theme_support('title-tag');
    
    // Add support for HTML5 markup
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
    ));
});

// Register navigation menus
register_nav_menus(array(
    'primary'           => __('Primary Menu',          'picostrap'),
    'footer'            => __('Footer Menu',            'picostrap'),
    'footer_services'   => __('Footer Services',        'picostrap'),
    'footer_resources'  => __('Footer Resources',       'picostrap'),
    'footer_company'    => __('Footer Company',         'picostrap'),
));

// Add custom body classes for styling
add_filter('body_class', function($classes) {
    $classes[] = 'vat-support-theme';
    
    if (is_front_page()) {
        $classes[] = 'homepage';
    }

    // Single template: "VAT Landing (full width)" — category via body classes
    if (is_page() && is_page_template('page-templates/page-vat-landing.php')) {
        $classes[] = 'vat-landing';
        $post = get_queried_object();
        if ($post instanceof WP_Post) {
            $classes[] = 'vat-lp--' . sanitize_html_class($post->post_name, 'page');
            // Optional Page Attribute or custom field: add extra class e.g. campaign / country / blog
            $variant = get_post_meta($post->ID, '_vat_lp_variant', true);
            if (is_string($variant) && $variant !== '') {
                $classes[] = 'vat-lp-variant--' . sanitize_html_class($variant, 'variant');
            }
        }
        if (function_exists('pll_current_language')) {
            $lang = pll_current_language();
            if (is_string($lang) && $lang !== '') {
                $classes[] = 'pll--' . sanitize_html_class($lang, 'lang');
            }
        }
    }
    
    return $classes;
});

// Simple Nav Walker for Offcanvas (no dropdowns)
class WP_Bootstrap_Navwalker_Simple extends Walker_Nav_Menu {
    function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
        $classes = empty($item->classes) ? array() : (array) $item->classes;
        $class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item, $args, $depth));
        $class_names = $class_names ? ' class="' . esc_attr($class_names) . '"' : '';
        
        $output .= '<li' . $class_names . '>';
        
        $atts = array();
        $atts['href'] = !empty($item->url) ? $item->url : '';
        $atts = apply_filters('nav_menu_link_attributes', $atts, $item, $args, $depth);
        
        $attributes = '';
        foreach ($atts as $attr => $value) {
            if (!empty($value)) {
                $value = ('href' === $attr) ? esc_url($value) : esc_attr($value);
                $attributes .= ' ' . $attr . '="' . $value . '"';
            }
        }
        
        $title = apply_filters('the_title', $item->title, $item->ID);
        
        $item_output = $args->before;
        $item_output .= '<a' . $attributes . '>';
        $item_output .= $args->link_before . $title . $args->link_after;
        $item_output .= '</a>';
        $item_output .= $args->after;
        
        $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
    }
}

// Customize excerpt length
add_filter('excerpt_length', function($length) {
    return 25;
});

// Add custom excerpt more text
add_filter('excerpt_more', function($more) {
    return '...';
});

// Remove WordPress version from head for security
remove_action('wp_head', 'wp_generator');

// Add custom admin styles (optional)
add_action('admin_enqueue_scripts', function() {
    wp_enqueue_style('vat-admin-styles', get_stylesheet_directory_uri() . '/css/admin.css');
});

function special_nav_class($classes, $item, $args) {
    if (isset($args->theme_location) && $args->theme_location === 'footer') {
        $classes[] = 'list-inline-item';
    }
    return $classes;
}
add_filter('nav_menu_css_class', 'special_nav_class', 10, 3);

function add_additional_class_on_a($atts, $item, $args) {
    if (isset($args->add_a_class)) {
        $atts['class'] = $args->add_a_class;
    }
    return $atts;
}
add_filter('nav_menu_link_attributes', 'add_additional_class_on_a', 10, 3);

// Add custom contact form shortcode (basic example)
add_shortcode('vat_contact_form', function($atts) {
    $atts = shortcode_atts(array(
        'title' => 'Get Started Today',
        'subtitle' => 'Contact us for a free consultation'
    ), $atts);
    
    ob_start();
    ?>
    <div class="vat-contact-form bg-light p-5 rounded">
        <h3><?php echo esc_html($atts['title']); ?></h3>
        <p class="text-muted"><?php echo esc_html($atts['subtitle']); ?></p>
        <form class="row g-3">
            <div class="col-md-6">
                <input type="text" class="form-control" placeholder="Your Name" required>
            </div>
            <div class="col-md-6">
                <input type="email" class="form-control" placeholder="Your Email" required>
            </div>
            <div class="col-12">
                <input type="text" class="form-control" placeholder="Company Name">
            </div>
            <div class="col-12">
                <textarea class="form-control" rows="4" placeholder="Tell us about your VAT needs..."></textarea>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary">Send Message</button>
            </div>
        </form>
    </div>
    <?php
    return ob_get_clean();
});

// Add custom post types for testimonials (optional)
add_action('init', function() {
    register_post_type('testimonial', array(
        'labels' => array(
            'name' => 'Testimonials',
            'singular_name' => 'Testimonial',
        ),
        'public' => true,
        'has_archive' => true,
        'supports' => array('title', 'editor', 'thumbnail'),
        'menu_icon' => 'dashicons-format-quote',
    ));
});

// ChatGPT Simple Interface Integration
// Load the simplified ChatGPT feature (no modal, no Bootstrap)
$chatgpt_simple_file = get_stylesheet_directory() . '/chatgpt/chatgpt-simple.php';
if (file_exists($chatgpt_simple_file)) {
    require_once $chatgpt_simple_file;
    
    // Initialize the ChatGPT Simple feature
    add_action('init', function() {
        ChatGPT_Simple_Integration::init();
    });
    
    // Add admin notice if API key is not configured
    add_action('admin_notices', function() {
        if (current_user_can('manage_options') && !get_option('chatgpt_api_key')) {
            $config_url = admin_url('options-general.php?page=chatgpt-simple-settings');
            echo '<div class="notice notice-info is-dismissible">';
            echo '<p><strong>ChatGPT Simple:</strong> To enable AI-powered content rewriting, please configure your OpenAI API key. ';
            echo '<a href="' . esc_url($config_url) . '">Configure settings</a> | ';
            echo '<a href="https://platform.openai.com/api-keys" target="_blank">Get your API key here</a>.</p>';
            echo '</div>';
        }
    });
}

// Polylang Auto-Translator Integration
// Load the Polylang auto-translation feature
if (is_admin()) {
    $translator_admin_file = get_stylesheet_directory() . '/chatgpt/admin-translator.php';
    if (file_exists($translator_admin_file)) {
        require_once $translator_admin_file;
    }
}

// Load WP-CLI commands if available
if (defined('WP_CLI') && WP_CLI) {
    $translator_cli_file = get_stylesheet_directory() . '/chatgpt/cli-translator.php';
    if (file_exists($translator_cli_file)) {
        require_once $translator_cli_file;
    }
}

// Menu Duplicator for Polylang
// Load the admin menu duplicator tool
if (is_admin()) {
    $menu_duplicator_file = get_stylesheet_directory() . '/admin-menu-duplicator.php';
    if (file_exists($menu_duplicator_file)) {
        require_once $menu_duplicator_file;
    }
}
remove_filter( 'the_content', 'wpautop' );
remove_filter( 'the_excerpt', 'wpautop' );



// ============================================================
// Fix: Force Polylang language filter on Post Grid (lazy load AJAX)
// Post Grid uses lazy_load:yes which fires an AJAX request to
// wp_ajax_nopriv_post_grid_paginate_ajax_free. That handler uses
// apply_filters('post_grid_ajax_query_args', ...) to build its query.
// We map each grid ID to a language and inject it into the tax_query.
// Grid IDs: 2341=RU, 2384=ZH, 2383=EN (EN works fine via Polylang).
// ============================================================
add_filter( 'post_grid_ajax_query_args', function( $args, $grid_id ) {
    // Map grid ID => Polylang language slug
    $grid_lang_map = array(
        '2341' => 'ru',
        2341   => 'ru',
        '2384' => 'zh',
        2384   => 'zh',
    );
    if ( ! isset( $grid_lang_map[ $grid_id ] ) ) {
        return $args;
    }
    $lang = $grid_lang_map[ $grid_id ];
    // Use Polylang's tax_query approach: filter by language term
    if ( function_exists( 'pll_get_post_language' ) && function_exists( 'get_terms' ) ) {
        $lang_terms = get_terms( array(
            'taxonomy'   => 'language',
            'slug'       => $lang,
            'hide_empty' => false,
        ) );
        if ( ! empty( $lang_terms ) && ! is_wp_error( $lang_terms ) ) {
            $lang_term = reset( $lang_terms );
            $existing_tax_query = isset( $args['tax_query'] ) ? $args['tax_query'] : array();
            // Remove the relation entry if present to rebuild
            $filtered = array_filter( $existing_tax_query, function($item) {
                return is_array($item) && isset($item['taxonomy']);
            });
            $filtered[] = array(
                'taxonomy' => 'language',
                'field'    => 'term_id',
                'terms'    => array( $lang_term->term_id ),
                'operator' => 'IN',
            );
            $relation = isset( $existing_tax_query['relation'] ) ? $existing_tax_query['relation'] : 'AND';
            $args['tax_query'] = array_merge( array( 'relation' => $relation ), array_values($filtered) );
        }
    }
    return $args;
}, 10, 2 );
