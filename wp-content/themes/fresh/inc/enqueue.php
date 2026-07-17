<?php
/**
 * Theme assets.
 */

function fresh_theme_enqueue_assets()
{
    $theme_uri = get_template_directory_uri();
    $theme_dir = get_template_directory();
    $custom_css_version = file_exists($theme_dir . '/assets/css/custom.css') ? filemtime($theme_dir . '/assets/css/custom.css') : FRESH_THEME_VERSION;
    $main_js_version = file_exists($theme_dir . '/assets/js/main.js') ? filemtime($theme_dir . '/assets/js/main.js') : FRESH_THEME_VERSION;
    $performance_js_version = file_exists($theme_dir . '/assets/js/performance.js') ? filemtime($theme_dir . '/assets/js/performance.js') : FRESH_THEME_VERSION;

    wp_enqueue_style('fresh-font-icons', $theme_uri . '/assets/css/font-icons.css', [], FRESH_THEME_VERSION);
    wp_enqueue_style('fresh-plugins', $theme_uri . '/assets/css/plugins.css', [], FRESH_THEME_VERSION);
    wp_enqueue_style('fresh-template', $theme_uri . '/assets/css/style.css', ['fresh-font-icons', 'fresh-plugins'], FRESH_THEME_VERSION);
    wp_enqueue_style('fresh-responsive', $theme_uri . '/assets/css/responsive.css', ['fresh-template'], FRESH_THEME_VERSION);
    wp_enqueue_style('fresh-custom', $theme_uri . '/assets/css/custom.css', ['fresh-responsive'], $custom_css_version);
    wp_enqueue_style('fresh-style', get_stylesheet_uri(), ['fresh-custom'], FRESH_THEME_VERSION);

    wp_enqueue_script('fresh-plugins', $theme_uri . '/assets/js/plugins.js', ['jquery'], FRESH_THEME_VERSION, true);
    wp_enqueue_script('fresh-performance', $theme_uri . '/assets/js/performance.js', ['fresh-plugins'], $performance_js_version, true);
    wp_enqueue_script('fresh-main', $theme_uri . '/assets/js/main.js', ['fresh-performance'], $main_js_version, true);
    wp_enqueue_script('fresh-shop', $theme_uri . '/assets/js/fresh-shop.js', ['jquery'], FRESH_THEME_VERSION, true);
    wp_localize_script('fresh-shop', 'freshShop', [
        'ajaxUrl'       => admin_url('admin-ajax.php'),
        'nonce'         => wp_create_nonce('fresh_storefront'),
        'addedToCart'   => __('Product added to cart.', 'fresh'),
        'addedWishlist' => __('Product added to wishlist.', 'fresh'),
        'errorMessage'  => __('Something went wrong. Please try again.', 'fresh'),
        'shopUrl'       => fresh_page_url('shop'),
    ]);

    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }
}
add_action('wp_enqueue_scripts', 'fresh_theme_enqueue_assets');

function fresh_remove_jquery_migrate($scripts)
{
    if (is_admin() || empty($scripts->registered['jquery'])) {
        return;
    }

    $jquery = $scripts->registered['jquery'];

    if (! empty($jquery->deps)) {
        $jquery->deps = array_diff($jquery->deps, ['jquery-migrate']);
    }
}
add_action('wp_default_scripts', 'fresh_remove_jquery_migrate');
