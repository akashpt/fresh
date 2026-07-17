<?php
$product = isset($args['product']) ? $args['product'] : get_post();

if (! $product instanceof WP_Post) {
    return;
}

$price = fresh_product_price($product->ID);
$unit  = get_post_meta($product->ID, '_fresh_product_unit', true);
$sku   = get_post_meta($product->ID, '_fresh_product_sku', true);
$product_image_url = fresh_product_image_url($product->ID, 'large');
?>

<main id="primary" class="site-main">
    <div class="ltn__shop-details-area pb-85 pt-60">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <div class="ltn__shop-details-img-gallery">
                        <img <?php echo fresh_image_attrs($product_image_url, get_the_title($product), ['fallback_width' => 800, 'fallback_height' => 800]); ?>>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="modal-product-info shop-details-info pl-0">
                        <h1><?php echo esc_html(get_the_title($product)); ?></h1>
                        <div class="product-price">
                            <span><?php echo esc_html(fresh_format_price($price)); ?></span>
                            <?php if ($unit) : ?>
                                <small>/ <?php echo esc_html($unit); ?></small>
                            <?php endif; ?>
                        </div>
                        <?php if ($sku) : ?>
                            <p><strong><?php esc_html_e('SKU:', 'fresh'); ?></strong> <?php echo esc_html($sku); ?></p>
                        <?php endif; ?>
                        <div class="product-short-description">
                            <?php echo wp_kses_post(wpautop($product->post_excerpt)); ?>
                        </div>

                        <form method="get" action="<?php echo esc_url(home_url('/')); ?>" class="cart">
                            <input type="hidden" name="fresh_add_to_cart" value="<?php echo esc_attr($product->ID); ?>">
                            <input type="hidden" name="redirect_to" value="<?php echo esc_url(fresh_page_url('cart')); ?>">
                            <?php wp_nonce_field('fresh_add_to_cart_' . $product->ID); ?>
                            <div class="cart-plus-minus">
                                <input type="number" value="1" name="quantity" min="1" class="cart-plus-minus-box">
                            </div>
                            <div class="btn-wrapper mt-3">
                                <button type="submit" class="theme-btn-1 btn btn-effect-1"><?php esc_html_e('Add to Cart', 'fresh'); ?></button>
                                <a class="theme-btn-2 btn btn-effect-2" href="<?php echo esc_url(fresh_page_url('cart')); ?>"><?php esc_html_e('View Cart', 'fresh'); ?></a>
                            </div>
                        </form>

                        <div class="product-details-content mt-4">
                            <?php echo apply_filters('the_content', $product->post_content); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
