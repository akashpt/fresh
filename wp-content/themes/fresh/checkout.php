<?php
/**
 * Template Name: Checkout
 * Template Post Type: page
 */

$checkout_result = fresh_handle_checkout();

get_header();

$items = fresh_cart_items();
$cart_subtotal = fresh_cart_subtotal();
$cart_discount = fresh_cart_discount();
$cart_items_total = fresh_cart_total();
$cart_delivery_charge = fresh_cart_delivery_charge($cart_items_total);
$cart_total = fresh_cart_payable_total();
$applied_coupon = fresh_get_applied_coupon_code();
$posted_name = isset($_POST['customer_name']) ? sanitize_text_field(wp_unslash($_POST['customer_name'])) : '';
$posted_email = isset($_POST['customer_email']) ? sanitize_email(wp_unslash($_POST['customer_email'])) : '';
$posted_phone = isset($_POST['customer_phone']) ? sanitize_text_field(wp_unslash($_POST['customer_phone'])) : '';
$posted_address = isset($_POST['customer_address']) ? sanitize_textarea_field(wp_unslash($_POST['customer_address'])) : '';
$posted_note = isset($_POST['customer_note']) ? sanitize_textarea_field(wp_unslash($_POST['customer_note'])) : '';

fresh_breadcrumb_banner(__('Checkout', 'fresh'), __('Complete your order', 'fresh'));
?>

<main id="primary" class="site-main">
    <div class="ltn__checkout-area pt-60 pb-85">
        <div class="container">
            <h1><?php esc_html_e('Checkout', 'fresh'); ?></h1>

            <?php if (is_wp_error($checkout_result)) : ?>
                <div class="alert alert-danger"><?php echo esc_html($checkout_result->get_error_message()); ?></div>
            <?php elseif ($checkout_result) : ?>
                <?php $whatsapp_url = fresh_order_whatsapp_url($checkout_result); ?>
                <div class="alert alert-success">
                    <?php
                    printf(
                        esc_html__('Thank you. Your order #%d has been received.', 'fresh'),
                        absint($checkout_result)
                    );
                    ?>
                </div>
                <?php if ($whatsapp_url) : ?>
                    <p><?php esc_html_e('Your order details are ready to send on WhatsApp.', 'fresh'); ?></p>
                    <div class="btn-wrapper">
                        <a class="theme-btn-1 btn btn-effect-1" href="<?php echo esc_url($whatsapp_url); ?>" target="_blank" rel="noopener"><?php esc_html_e('Send Order on WhatsApp', 'fresh'); ?></a>
                        <a class="theme-btn-2 btn btn-effect-2" href="<?php echo esc_url(fresh_page_url('shop')); ?>"><?php esc_html_e('Continue Shopping', 'fresh'); ?></a>
                    </div>
                    <script>
                        window.setTimeout(function () {
                            window.location.href = <?php echo wp_json_encode($whatsapp_url); ?>;
                        }, 800);
                    </script>
                <?php else : ?>
                    <div class="alert alert-warning">
                        <?php esc_html_e('WhatsApp number is not set. Go to Settings > Fresh WhatsApp and add your number.', 'fresh'); ?>
                    </div>
                    <a class="theme-btn-1 btn btn-effect-1" href="<?php echo esc_url(fresh_page_url('shop')); ?>"><?php esc_html_e('Continue Shopping', 'fresh'); ?></a>
                <?php endif; ?>
            <?php elseif ($items) : ?>
                <div class="row">
                    <div class="col-lg-7">
                        <form method="post" class="ltn__checkout-single-content-info">
                            <?php wp_nonce_field('fresh_place_order'); ?>
                            <div class="alert alert-info fresh-checkout-note">
                                <?php esc_html_e('After placing the order, customer and cart details will open in WhatsApp. Do not enter payment card numbers here.', 'fresh'); ?>
                            </div>
                            <p>
                                <label><?php esc_html_e('Name', 'fresh'); ?></label>
                                <input type="text" name="customer_name" value="<?php echo esc_attr($posted_name); ?>" autocomplete="name" required>
                            </p>
                            <p>
                                <label><?php esc_html_e('Email', 'fresh'); ?></label>
                                <input type="email" name="customer_email" value="<?php echo esc_attr($posted_email); ?>" autocomplete="email" required>
                            </p>
                            <p>
                                <label><?php esc_html_e('Phone', 'fresh'); ?></label>
                                <input type="text" name="customer_phone" value="<?php echo esc_attr($posted_phone); ?>" autocomplete="tel" required>
                            </p>
                            <p>
                                <label><?php esc_html_e('Address', 'fresh'); ?></label>
                                <textarea name="customer_address" rows="5" autocomplete="street-address" required><?php echo esc_textarea($posted_address); ?></textarea>
                            </p>
                            <div class="fresh-checkout-preferences">
                                <p>
                                    <label><?php esc_html_e('Order Note', 'fresh'); ?></label>
                                    <textarea name="customer_note" rows="3" placeholder="<?php esc_attr_e('Example: call before delivery, leave at reception, extra packing needed.', 'fresh'); ?>"><?php echo esc_textarea($posted_note); ?></textarea>
                                </p>
                            </div>
                            <button type="submit" name="fresh_place_order" value="1" class="theme-btn-1 btn btn-effect-1"><?php esc_html_e('Place Order', 'fresh'); ?></button>
                        </form>
                    </div>
                    <div class="col-lg-5">
                        <div class="shoping-cart-total">
                            <h4><?php esc_html_e('Order Summary', 'fresh'); ?></h4>
                            <ul>
                                <?php foreach ($items as $item) : ?>
                                    <li>
                                        <?php echo esc_html(get_the_title($item['product'])); ?>
                                        x <?php echo esc_html($item['quantity']); ?>
                                        <span><?php echo esc_html(fresh_format_price($item['subtotal'])); ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                            <ul>
                                <li>
                                    <?php esc_html_e('Subtotal', 'fresh'); ?>
                                    <span><?php echo esc_html(fresh_format_price($cart_subtotal)); ?></span>
                                </li>
                                <?php if ($cart_discount > 0) : ?>
                                    <li>
                                        <?php esc_html_e('Discount', 'fresh'); ?>
                                        <?php if ($applied_coupon) : ?>
                                            (<?php echo esc_html($applied_coupon); ?>)
                                        <?php endif; ?>
                                        <span>-<?php echo esc_html(fresh_format_price($cart_discount)); ?></span>
                                    </li>
                                <?php endif; ?>
                                <li>
                                    <?php esc_html_e('Delivery', 'fresh'); ?>
                                    <span><?php echo esc_html($cart_delivery_charge > 0 ? fresh_format_price($cart_delivery_charge) : __('Free', 'fresh')); ?></span>
                                </li>
                            </ul>
                            <h5><?php esc_html_e('Total:', 'fresh'); ?> <?php echo esc_html(fresh_format_price($cart_total)); ?></h5>
                        </div>
                    </div>
                </div>
            <?php else : ?>
                <p><?php esc_html_e('Your cart is empty.', 'fresh'); ?></p>
                <a class="theme-btn-1 btn btn-effect-1" href="<?php echo esc_url(fresh_page_url('shop')); ?>"><?php esc_html_e('Go to Shop', 'fresh'); ?></a>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php
get_footer();
