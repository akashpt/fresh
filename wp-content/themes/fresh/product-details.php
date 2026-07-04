<?php
/**
 * Template Name: Product Details
 * Template Post Type: page
 */

get_header();

$product_id = isset($_GET['product']) ? absint($_GET['product']) : 0;
$product    = $product_id ? get_post($product_id) : null;

fresh_breadcrumb_banner(
    $product && $product->post_type === 'fresh_product' ? get_the_title($product) : __('Product Details', 'fresh'),
    __('Shop details', 'fresh')
);

if ($product && $product->post_type === 'fresh_product' && $product->post_status === 'publish') {
    get_template_part('template-parts/products/product-detail', null, ['product' => $product]);
} else {
    ?>
    <main id="primary" class="site-main">
        <div class="container pt-60 pb-60">
            <h1><?php esc_html_e('Product not found', 'fresh'); ?></h1>
            <p><?php esc_html_e('Please choose a product from the shop.', 'fresh'); ?></p>
            <a class="theme-btn-1 btn btn-effect-1" href="<?php echo esc_url(fresh_page_url('shop')); ?>"><?php esc_html_e('Go to Shop', 'fresh'); ?></a>
        </div>
    </main>
    <?php
}

get_footer();
