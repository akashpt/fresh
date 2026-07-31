<?php
/**
 * Theme setup.
 */

function fresh_theme_setup()
{
    load_theme_textdomain('fresh', get_template_directory() . '/languages');

    add_theme_support('automatic-feed-links');
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('responsive-embeds');
    add_theme_support('wp-block-styles');
    add_theme_support('align-wide');

    add_theme_support('html5', [
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'script',
        'style',
        'search-form',
    ]);

    add_theme_support('custom-logo', [
        'flex-height' => true,
        'flex-width'  => true,
    ]);

    register_nav_menus([
        'primary' => __('Primary Menu', 'fresh'),
        'footer'  => __('Footer Menu', 'fresh'),
    ]);
}
add_action('after_setup_theme', 'fresh_theme_setup');

function fresh_set_default_brand_name()
{
    $site_name = trim((string) get_option('blogname'));

    if ($site_name === '' || strcasecmp($site_name, 'fresh') === 0) {
        update_option('blogname', 'pureauranaturals');
    }
}
add_action('after_setup_theme', 'fresh_set_default_brand_name');
