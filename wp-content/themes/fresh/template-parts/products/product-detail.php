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

                        <div class="fresh-product-trust">
                            <span><?php esc_html_e('Freshly packed', 'fresh'); ?></span>
                            <span><?php esc_html_e('WhatsApp order support', 'fresh'); ?></span>
                            <span><?php esc_html_e('Quality checked', 'fresh'); ?></span>
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

                        <div class="fresh-product-info-panels">
                            <section>
                                <h2><?php esc_html_e('Why customers choose it', 'fresh'); ?></h2>
                                <ul>
                                    <li><?php esc_html_e('Selected for everyday freshness and value.', 'fresh'); ?></li>
                                    <li><?php esc_html_e('Easy quantity selection before adding to cart.', 'fresh'); ?></li>
                                    <li><?php esc_html_e('Order confirmation can be sent quickly through WhatsApp.', 'fresh'); ?></li>
                                </ul>
                            </section>
                            <section>
                                <h2><?php esc_html_e('Shipping and returns', 'fresh'); ?></h2>
                                <p><?php esc_html_e('Delivery details are confirmed after checkout. If an item arrives damaged or incorrect, contact support with your order details for quick help.', 'fresh'); ?></p>
                            </section>
                            <section>
                                <h2><?php esc_html_e('Product FAQs', 'fresh'); ?></h2>
                                <details>
                                    <summary><?php esc_html_e('How do I order this product?', 'fresh'); ?></summary>
                                    <p><?php esc_html_e('Choose the quantity, add it to cart, and complete checkout. Your order details can be sent on WhatsApp.', 'fresh'); ?></p>
                                </details>
                                <details>
                                    <summary><?php esc_html_e('How should I store it?', 'fresh'); ?></summary>
                                    <p><?php esc_html_e('Store in a cool, dry place away from direct sunlight. Follow any storage instruction mentioned on the product pack.', 'fresh'); ?></p>
                                </details>
                            </section>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
