<?php
/**
 * Template Name: Shop
 * Template Post Type: page
 */

get_header();

fresh_breadcrumb_banner(__('Shop', 'fresh'), __('pureauranaturals products', 'fresh'));

get_template_part('template-parts/products/product-grid', null, [
    'title'        => __('Shop pureauranaturals Products', 'fresh'),
    'limit'        => -1,
    'show_filters' => true,
]);

get_footer();
