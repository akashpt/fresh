<?php
/**
 * Template Name: Return Policy
 * Template Post Type: page
 */

get_header();

fresh_breadcrumb_banner(__('Return Policy', 'fresh'));
?>

<main id="primary" class="site-main">
    <section class="fresh-policy-page">
        <div class="container">
            <div class="fresh-policy-layout">
                <aside class="fresh-policy-summary" aria-label="<?php esc_attr_e('Return policy summary', 'fresh'); ?>">
                    <span><?php esc_html_e('Customer care', 'fresh'); ?></span>
                    <h2><?php esc_html_e('Return Policy', 'fresh'); ?></h2>
                    <p><?php esc_html_e('Please check your order as soon as it arrives. If something is damaged, incorrect, or missing, contact us quickly so we can help.', 'fresh'); ?></p>
                    <div class="fresh-policy-contact">
                        <a href="mailto:contact@pureauranaturals.in">
                            <i class="icon-mail"></i>
                            contact@pureauranaturals.in
                        </a>
                        <a href="tel:+917867879000">
                            <i class="icon-call"></i>
                            <?php esc_html_e('+91 78678 79000', 'fresh'); ?>
                        </a>
                    </div>
                </aside>

                <div class="fresh-policy-content">
                    <div class="fresh-policy-intro">
                        <span><?php esc_html_e('Last updated: August 4, 2026', 'fresh'); ?></span>
                        <h1><?php esc_html_e('Returns, replacements, and refunds', 'fresh'); ?></h1>
                        <p><?php esc_html_e('We take care to pack every order safely. Because our products are personal-use and natural goods, returns are accepted only in the situations listed below.', 'fresh'); ?></p>
                    </div>

                    <div class="fresh-policy-section">
                        <h3><?php esc_html_e('When a return or replacement is accepted', 'fresh'); ?></h3>
                        <ul>
                            <li><?php esc_html_e('The product was damaged during delivery.', 'fresh'); ?></li>
                            <li><?php esc_html_e('You received the wrong product or wrong quantity.', 'fresh'); ?></li>
                            <li><?php esc_html_e('An item from your confirmed order is missing.', 'fresh'); ?></li>
                            <li><?php esc_html_e('The product is expired at the time of delivery.', 'fresh'); ?></li>
                        </ul>
                    </div>

                    <div class="fresh-policy-section">
                        <h3><?php esc_html_e('Return request window', 'fresh'); ?></h3>
                        <p><?php esc_html_e('Please contact us within 48 hours of delivery with your order details, photos of the product, outer package, invoice, and a short note explaining the issue. Requests raised after 48 hours may not be eligible for return, replacement, or refund.', 'fresh'); ?></p>
                    </div>

                    <div class="fresh-policy-section">
                        <h3><?php esc_html_e('Items not eligible for return', 'fresh'); ?></h3>
                        <ul>
                            <li><?php esc_html_e('Opened, used, altered, or partially consumed products.', 'fresh'); ?></li>
                            <li><?php esc_html_e('Products damaged after delivery because of misuse, storage, or handling.', 'fresh'); ?></li>
                            <li><?php esc_html_e('Products returned without original packaging, invoice, or proof of purchase.', 'fresh'); ?></li>
                            <li><?php esc_html_e('Change-of-mind returns after the order has been delivered.', 'fresh'); ?></li>
                        </ul>
                    </div>

                    <div class="fresh-policy-section">
                        <h3><?php esc_html_e('Refunds', 'fresh'); ?></h3>
                        <p><?php esc_html_e('Once your request is reviewed and approved, we will arrange a replacement where possible. If a replacement is not available, the eligible refund will be processed to the original payment method or another agreed method. Refund timelines may vary based on your bank or payment provider.', 'fresh'); ?></p>
                    </div>

                    <div class="fresh-policy-section">
                        <h3><?php esc_html_e('Cancellations', 'fresh'); ?></h3>
                        <p><?php esc_html_e('Orders can be cancelled before dispatch. Once an order has been shipped, cancellation may not be available, but you can contact us and we will guide you based on the order status.', 'fresh'); ?></p>
                    </div>

                    <div class="fresh-policy-note">
                        <i class="fas fa-info-circle"></i>
                        <p><?php esc_html_e('For fast support, include your order number, registered phone number, and clear photos when you contact us.', 'fresh'); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php
get_footer();
