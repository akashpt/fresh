<?php
/**
 * Template Name: Shop
 * Template Post Type: page
 */

get_header();

fresh_breadcrumb_banner(__('Shop', 'fresh'), __('Fresh products', 'fresh'));

get_template_part('template-parts/products/product-grid', null, [
    'title'        => __('Shop Fresh Products', 'fresh'),
    'limit'        => 24,
    'show_filters' => true,
]);

get_footer();
