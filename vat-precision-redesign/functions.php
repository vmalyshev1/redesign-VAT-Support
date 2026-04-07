<?php
/**
 *            _               _
 *        ___|_|___ ___ _____|_|___ ___
 *       | . | |  _| . |     | |   | . |
 *       |  _|_|___|___|_|_|_|_|_|_|_  |
 *       |_|                       |___|
 *
 *       WELCOME TO THE STARTER THEME!
 *
 *       picostrap.com
 *       livecanvas.com
 */

//// LOAD CONFIGURATION FEATURES ////
$configuration_file = get_stylesheet_directory() . '/livecanvas/configuration.php';
if (file_exists($configuration_file))  require_once($configuration_file);

//// ENQUEUE CHILD THEME STYLES ////
add_action( 'wp_enqueue_scripts', function() {
    //Picostrap's main style, in its css-output folder
    wp_enqueue_style( 'picostrap-styles', get_template_directory_uri() . '/css-output/bundle.css', array(), filemtime(get_template_directory() . '/css-output/bundle.css'), 'all' );
    //child theme style: this very folder's style.css  (default)
    wp_enqueue_style( 'picostrap-child-styles', get_stylesheet_directory_uri() . '/style.css', array('picostrap-styles'), filemtime(get_stylesheet_directory() . '/style.css'), 'all' );
    //additional stylesheets enqueued here, as an example
    //wp_enqueue_style( 'my-extra-styles', get_stylesheet_directory_uri() . '/extra.css', array(), filemtime(get_stylesheet_directory() . '/extra.css'), 'all' );
    //LOAD the precision finance override CSS with high priority (loads after other styles)
    wp_enqueue_style( 'precision-finance-override', get_stylesheet_directory_uri() . '/precision-finance-override.css', array('picostrap-child-styles'), filemtime(get_stylesheet_directory() . '/precision-finance-override.css'), 'all' );
}, 100);

//DEQUEUE PICOSTRAP's BOOTSTRAP JS AND ENQUEUE CHILD'S VERSION with the "async" attribute
add_action( 'wp_enqueue_scripts', function() {
    wp_dequeue_script( 'bootstrap5' );
    wp_enqueue_script( 'bootstrap5', get_stylesheet_directory_uri() . '/js/bootstrap.bundle.min.js', array(), null,  array('strategy' => 'defer', 'in_footer' => true) );
    //optional: example of how to globally lazyload js files eg lottie player, using defer
    //wp_enqueue_script( 'lottie-player', 'https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js', array(), null, array('strategy' => 'defer', 'in_footer' => true) );
}, 101);

// HACK HERE: ENQUEUE YOUR CUSTOM JS FILES, IF NEEDED
add_action( 'wp_enqueue_scripts', function() {
    // Include custom.js for navbar scroll effect and other interactions
    wp_enqueue_script('custom', get_stylesheet_directory_uri() . '/js/custom.js', array(/* 'jquery' */), '2.0', array('strategy' => 'defer', 'in_footer' => true) );
    //UNCOMMENT next 3 rows to load the js file only on one page
    //if (is_page('mypageslug')) {
    //  wp_enqueue_script('custom', get_stylesheet_directory_uri() . '/js/custom.js', array(/* 'jquery' */), null, array('strategy' => 'defer', 'in_footer' => true) );
    //}
}, 102);

// OPTIONAL: ADD MORE NAV MENUS
//register_nav_menus( array( 'third' => __( 'Third Menu', 'picostrap' ), 'fourth' => __( 'Fourth Menu', 'picostrap' ), 'fifth' => __( 'Fifth Menu', 'picostrap' ), ) );
// THEN USE SHORTCODE: [lc_nav_menu theme_location="third" container_class="" container_id="" menu_class="navbar-nav"]

// OPTIONAL: FOR SECURITY: DISABLE APPLICATION PASSWORDS. Uncomment if needed
//add_filter( 'wp_is_application_passwords_available', '__return_false' );

// ADD YOUR CUSTOM PHP CODE DOWN BELOW /////////////////////////

// NO GOOGLE FONTS - Using system fonts for China/Baidu SEO compatibility
// Fonts are defined in precision-finance-override.css using system font stacks

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
    'primary'          => __('Primary Menu', 'picostrap'),
    'footer'           => __('Footer Menu', 'picostrap'),
    'footer_services'  => __('Footer Services', 'picostrap'),
    'footer_resources' => __('Footer Resources', 'picostrap'),
    'footer_company'   => __('Footer Company', 'picostrap'),
));

// Add custom body classes for styling
add_filter('body_class', function($classes) {
    $classes[] = 'vat-support-theme';
    if (is_front_page()) {
        $classes[] = 'homepage';
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
        $item_output .= '<a class="nav-link"' . $attributes . '>';
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
        'title'    => 'Get Started Today',
        'subtitle' => 'Contact us for a free consultation'
    ), $atts);
    ob_start();
    ?>
    <div class="vat-contact-form">
        <h3><?php echo esc_html($atts['title']); ?></h3>
        <p><?php echo esc_html($atts['subtitle']); ?></p>
        <form method="post" action="">
            <div class="form-group mb-3">
                <input type="text" name="name" class="form-control" placeholder="Your Name" required>
            </div>
            <div class="form-group mb-3">
                <input type="email" name="email" class="form-control" placeholder="Your Email" required>
            </div>
            <div class="form-group mb-3">
                <textarea name="message" class="form-control" rows="4" placeholder="Your Message" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Send Message</button>
        </form>
    </div>
    <?php
    return ob_get_clean();
});

// Register Testimonials Custom Post Type
add_action('init', function() {
    register_post_type('testimonial', array(
        'labels' => array(
            'name'          => 'Testimonials',
            'singular_name' => 'Testimonial',
        ),
        'public'       => true,
        'has_archive'  => true,
        'supports'     => array('title', 'editor', 'thumbnail'),
        'menu_icon'    => 'dashicons-format-quote',
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
