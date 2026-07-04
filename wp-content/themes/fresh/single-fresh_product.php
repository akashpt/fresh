<?php
/**
 * Single product fallback.
 */

get_header();

get_template_part('template-parts/products/product-detail', null, ['product' => get_post()]);

get_footer();
