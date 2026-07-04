<?php
/**
 * Main Index File
 */

get_header();
?>
<?php get_template_part('template-parts/layout/offcanvas'); ?>

<?php get_template_part('template-parts/home/slider'); ?>

<?php get_template_part('template-parts/home/features', null, ['variant' => 'top']); ?>

<?php get_template_part('template-parts/home/about'); ?>
    
<?php get_template_part('template-parts/home/categories'); ?>

<?php
get_template_part('template-parts/products/product-tabs', null, [
    'title' => __('Our Products', 'fresh'),
    'limit' => 8,
]);
?>

<?php get_template_part('template-parts/home/counter'); ?>

<?php
get_template_part('template-parts/products/product-grid', null, [
    'title'        => __('Featured Products', 'fresh'),
    'limit'        => 8,
    'show_counter' => true,
]);
?>

<?php get_template_part('template-parts/home/cta'); ?>

<?php get_template_part('template-parts/home/blog'); ?>

<?php get_template_part('template-parts/home/features', null, ['variant' => 'bottom']); ?>

<!-- Main Content End -->


<?php
get_footer();
?>
