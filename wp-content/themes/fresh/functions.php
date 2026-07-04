<?php
/**
 * Fresh theme functions.
 *
 * Keep this file as a small loader. Theme setup, assets, and helpers live in
 * the inc directory so the custom theme stays easy to maintain.
 */

if (! defined('FRESH_THEME_VERSION')) {
    $theme = wp_get_theme();
    define('FRESH_THEME_VERSION', $theme->get('Version') ?: '1.0.0');
}

require get_template_directory() . '/inc/setup.php';
require get_template_directory() . '/inc/enqueue.php';
require get_template_directory() . '/inc/template-functions.php';
require get_template_directory() . '/inc/products.php';
require get_template_directory() . '/inc/home.php';
