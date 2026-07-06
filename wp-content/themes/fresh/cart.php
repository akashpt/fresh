<?php
/**
 * Template Name: Cart
 * Template Post Type: page
 */

get_header();

$items = fresh_cart_items();
$cart_subtotal = fresh_cart_subtotal();
$cart_discount = fresh_cart_discount();
$cart_items_total = fresh_cart_total();
$cart_delivery_charge = fresh_cart_delivery_charge($cart_items_total);
$cart_total = fresh_cart_payable_total();
$cart_count = fresh_cart_count();
$applied_coupon = fresh_get_applied_coupon_code();
$applied_coupon_data = $applied_coupon ? fresh_find_coupon($applied_coupon) : null;
$cart_product_discount = 0;

foreach ($items as $item) {
    $product_id = $item['product']->ID;
    $regular_price = (float) get_post_meta($product_id, '_fresh_product_price', true);
    $sale_price = (float) get_post_meta($product_id, '_fresh_product_sale_price', true);
    $quantity = absint($item['quantity']);

    if ($regular_price > 0 && $sale_price > 0 && $sale_price < $regular_price) {
        $cart_product_discount += ($regular_price - $sale_price) * $quantity;
    }
}

$cart_total_discount = $cart_product_discount + $cart_discount;

fresh_breadcrumb_banner(__('Cart', 'fresh'), __('Shopping cart', 'fresh'));
?>

<main id="primary" class="site-main">
    <div class="liton__shoping-cart-area fresh-cart-page mb-120">
        <div class="container">
            <?php if ($items) : ?>
                <?php if (! empty($_GET['fresh_coupon_error'])) : ?>
                    <div class="alert alert-danger"><?php echo esc_html(sanitize_text_field(wp_unslash($_GET['fresh_coupon_error']))); ?></div>
                <?php elseif (! empty($_GET['fresh_coupon_applied'])) : ?>
                    <div class="alert alert-success"><?php esc_html_e('Coupon applied successfully.', 'fresh'); ?></div>
                <?php endif; ?>

                <form method="post" class="fresh-cart-form" data-coupon-type="<?php echo esc_attr($applied_coupon_data['type'] ?? ''); ?>" data-coupon-amount="<?php echo esc_attr($applied_coupon_data['amount'] ?? 0); ?>" data-delivery-threshold="<?php echo esc_attr(fresh_delivery_free_threshold()); ?>" data-delivery-charge="<?php echo esc_attr(fresh_delivery_charge_amount()); ?>">
                    <?php wp_nonce_field('fresh_update_cart'); ?>

                    <div class="fresh-cart-layout">
                        <div class="fresh-cart-items-panel">
                            <div class="fresh-cart-shipping-note">
                                <i class="fas fa-truck"></i>
                                <?php echo esc_html(sprintf(__('Free delivery above %s.', 'fresh'), fresh_format_price(fresh_delivery_free_threshold()))); ?>
                            </div>

                            <div class="fresh-cart-items-head">
                                <span><?php echo esc_html(sprintf(_n('Your Order (%d item)', 'Your Order (%d items)', $cart_count, 'fresh'), $cart_count)); ?></span>
                                <span><?php esc_html_e('Subtotal', 'fresh'); ?></span>
                            </div>

                            <div class="fresh-cart-items">
                                <?php foreach ($items as $item) : ?>
                                    <?php
                                    $product = $item['product'];
                                    $unit = get_post_meta($product->ID, '_fresh_product_unit', true);
                                    $sku = get_post_meta($product->ID, '_fresh_product_sku', true);
                                    $regular_price = (float) get_post_meta($product->ID, '_fresh_product_price', true);
                                    $sale_price = (float) get_post_meta($product->ID, '_fresh_product_sale_price', true);
                                    $has_product_discount = $regular_price > 0 && $sale_price > 0 && $sale_price < $regular_price;
                                    $line_product_discount = $has_product_discount ? ($regular_price - $sale_price) * absint($item['quantity']) : 0;
                                    ?>
                                    <div class="fresh-cart-item">
                                        <a class="fresh-cart-item-image" href="<?php echo esc_url(fresh_product_detail_url($product->ID)); ?>">
                                            <img src="<?php echo esc_url(fresh_product_image_url($product->ID)); ?>" alt="<?php echo esc_attr(get_the_title($product)); ?>">
                                        </a>

                                        <div class="fresh-cart-item-info">
                                            <h4>
                                                <a href="<?php echo esc_url(fresh_product_detail_url($product->ID)); ?>">
                                                    <?php echo esc_html(get_the_title($product)); ?>
                                                </a>
                                            </h4>
                                            <?php if ($sku) : ?>
                                                <p><?php esc_html_e('SKU:', 'fresh'); ?> <?php echo esc_html($sku); ?></p>
                                            <?php endif; ?>
                                            <?php if ($unit) : ?>
                                                <p><?php esc_html_e('Unit:', 'fresh'); ?> <?php echo esc_html($unit); ?></p>
                                            <?php endif; ?>

                                            <div class="fresh-cart-quantity">
                                                <span><?php esc_html_e('Quantity:', 'fresh'); ?></span>
                                                <div class="cart-plus-minus">
                                                    <input type="number" min="0" name="quantity[<?php echo esc_attr($product->ID); ?>]" value="<?php echo esc_attr($item['quantity']); ?>" class="cart-plus-minus-box fresh-cart-qty">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="fresh-cart-item-total">
                                            <a class="fresh-cart-remove" href="<?php echo esc_url(fresh_remove_from_cart_url($product->ID)); ?>" data-product-id="<?php echo esc_attr($product->ID); ?>" aria-label="<?php esc_attr_e('Remove item', 'fresh'); ?>">
                                                <i class="fas fa-trash-alt"></i>
                                            </a>
                                            <strong class="fresh-cart-line-subtotal" data-price="<?php echo esc_attr($item['price']); ?>" data-regular-price="<?php echo esc_attr($has_product_discount ? $regular_price : $item['price']); ?>"><?php echo esc_html(fresh_format_price($item['subtotal'])); ?></strong>
                                            <?php if ($has_product_discount) : ?>
                                                <del class="fresh-cart-line-regular"><?php echo esc_html(fresh_format_price($regular_price * absint($item['quantity']))); ?></del>
                                                <em class="fresh-cart-line-save"><?php echo esc_html(sprintf(__('Save %s', 'fresh'), fresh_format_price($line_product_discount))); ?></em>
                                            <?php endif; ?>
                                            <small><?php echo esc_html($item['quantity']); ?> x <?php echo esc_html(fresh_format_price($item['price'])); ?></small>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <aside class="fresh-cart-summary">
                            <h4><?php esc_html_e('Order Summary', 'fresh'); ?></h4>

                            <div class="fresh-summary-lines">
                                <div>
                                    <span><?php echo esc_html(sprintf(_n('Subtotal (%d item)', 'Subtotal (%d items)', $cart_count, 'fresh'), $cart_count)); ?></span>
                                    <strong class="fresh-cart-subtotal"><?php echo esc_html(fresh_format_price($cart_subtotal)); ?></strong>
                                </div>
                                <div>
                                    <span><?php esc_html_e('Product Discount', 'fresh'); ?></span>
                                    <strong class="fresh-summary-success fresh-cart-product-discount">
                                        <?php echo esc_html($cart_product_discount > 0 ? '-' . fresh_format_price($cart_product_discount) : fresh_format_price(0)); ?>
                                    </strong>
                                </div>
                                <div>
                                    <span><?php esc_html_e('Coupon Discount', 'fresh'); ?></span>
                                    <strong class="fresh-summary-success fresh-cart-coupon-discount">
                                        <?php echo esc_html($cart_discount > 0 ? '-' . fresh_format_price($cart_discount) : fresh_format_price(0)); ?>
                                    </strong>
                                </div>
                                <div class="fresh-summary-discount-total">
                                    <span><?php esc_html_e('Total Discount', 'fresh'); ?></span>
                                    <strong class="fresh-summary-success fresh-cart-discount">
                                        <?php echo esc_html($cart_total_discount > 0 ? '-' . fresh_format_price($cart_total_discount) : fresh_format_price(0)); ?>
                                    </strong>
                                </div>
                                <div>
                                    <span><?php esc_html_e('Delivery Charges', 'fresh'); ?></span>
                                    <strong class="fresh-summary-success fresh-cart-delivery">
                                        <?php echo esc_html($cart_delivery_charge > 0 ? fresh_format_price($cart_delivery_charge) : __('Free', 'fresh')); ?>
                                    </strong>
                                </div>
                            </div>

                            <div class="fresh-cart-coupon">
                                <input type="text" name="cart_coupon" value="<?php echo esc_attr($applied_coupon); ?>" placeholder="<?php esc_attr_e('Coupon Code', 'fresh'); ?>">
                                <?php if ($applied_coupon && $cart_discount > 0) : ?>
                                    <button type="submit" name="fresh_remove_coupon" value="1"><?php esc_html_e('Remove', 'fresh'); ?></button>
                                <?php else : ?>
                                    <button type="submit" name="fresh_apply_coupon" value="1"><?php esc_html_e('Apply', 'fresh'); ?></button>
                                <?php endif; ?>
                            </div>

                            <div class="fresh-summary-total">
                                <span><?php esc_html_e('Total Payment', 'fresh'); ?></span>
                                <strong class="fresh-cart-total"><?php echo esc_html(fresh_format_price($cart_total)); ?></strong>
                            </div>

                            <button type="submit" name="fresh_update_cart" value="1" class="fresh-update-cart-btn">
                                <?php esc_html_e('Update Cart', 'fresh'); ?>
                            </button>

                            <button type="submit" name="fresh_update_cart_checkout" value="1" class="fresh-checkout-btn">
                                <?php esc_html_e('Proceed to checkout', 'fresh'); ?>
                                <i class="fas fa-arrow-right"></i>
                            </button>
                        </aside>
                    </div>
                </form>
            <?php else : ?>
                <div class="text-center pt-60 pb-60">
                    <h2><?php esc_html_e('Your cart is empty.', 'fresh'); ?></h2>
                    <div class="btn-wrapper mt-30">
                        <a class="theme-btn-1 btn btn-effect-1" href="<?php echo esc_url(fresh_page_url('shop')); ?>"><?php esc_html_e('Go to Shop', 'fresh'); ?></a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php
get_footer();
