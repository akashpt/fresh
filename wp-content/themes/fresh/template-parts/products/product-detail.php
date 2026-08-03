<?php
$product = isset($args['product']) ? $args['product'] : get_post();

if (! $product instanceof WP_Post) {
    return;
}

$price = fresh_product_price($product->ID);
$unit  = get_post_meta($product->ID, '_fresh_product_unit', true);
$sku   = get_post_meta($product->ID, '_fresh_product_sku', true);
$product_image_url = fresh_product_image_url($product->ID, 'large');
$benefits = function_exists('fresh_product_meta_lines') ? fresh_product_meta_lines($product->ID, '_fresh_product_benefits') : [];
$ingredients = function_exists('fresh_product_meta_text') ? fresh_product_meta_text($product->ID, '_fresh_product_ingredients') : '';
$how_to_use = function_exists('fresh_product_meta_text') ? fresh_product_meta_text($product->ID, '_fresh_product_how_to_use') : '';
$storage = function_exists('fresh_product_meta_text') ? fresh_product_meta_text($product->ID, '_fresh_product_storage') : '';
$shipping = function_exists('fresh_product_meta_text') ? fresh_product_meta_text($product->ID, '_fresh_product_shipping') : '';
$returns = function_exists('fresh_product_meta_text') ? fresh_product_meta_text($product->ID, '_fresh_product_returns') : '';
$faqs = function_exists('fresh_product_faqs') ? fresh_product_faqs($product->ID) : [];
$terms = get_the_terms($product->ID, 'fresh_product_category');
$term_ids = ! is_wp_error($terms) && $terms ? wp_list_pluck($terms, 'term_id') : [];
$related_args = [
    'post_type'      => 'fresh_product',
    'post_status'    => 'publish',
    'posts_per_page' => 4,
    'post__not_in'   => [$product->ID],
    'orderby'        => [
        'menu_order' => 'ASC',
        'title'      => 'ASC',
    ],
];

if ($term_ids) {
    $related_args['tax_query'] = [
        [
            'taxonomy' => 'fresh_product_category',
            'field'    => 'term_id',
            'terms'    => $term_ids,
        ],
    ];
}

$related_products = get_posts($related_args);
?>

<main id="primary" class="site-main">
    <div class="ltn__shop-details-area pb-85 pt-60">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <div class="ltn__shop-details-img-gallery">
                        <img <?php echo fresh_image_attrs($product_image_url, get_the_title($product), ['fallback_width' => 800, 'fallback_height' => 800, 'loading' => 'eager', 'fetchpriority' => 'high']); ?>>
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
                            <span><?php esc_html_e('pureauranaturals packed', 'fresh'); ?></span>
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
                                <h2><?php esc_html_e('Benefits', 'fresh'); ?></h2>
                                <?php if ($benefits) : ?>
                                    <ul>
                                        <?php foreach ($benefits as $benefit) : ?>
                                            <li><?php echo esc_html($benefit); ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php else : ?>
                                    <ul>
                                        <li><?php esc_html_e('Selected for everyday freshness and value.', 'fresh'); ?></li>
                                        <li><?php esc_html_e('Easy quantity selection before adding to cart.', 'fresh'); ?></li>
                                        <li><?php esc_html_e('Order confirmation can be sent quickly through WhatsApp.', 'fresh'); ?></li>
                                    </ul>
                                <?php endif; ?>
                            </section>
                            <section>
                                <h2><?php esc_html_e('Features', 'fresh'); ?></h2>
                                <ul>
                                    <li><?php esc_html_e('Easy online cart ordering with quantity control.', 'fresh'); ?></li>
                                    <li><?php esc_html_e('Clear product details before checkout.', 'fresh'); ?></li>
                                    <li><?php esc_html_e('Support available for order questions.', 'fresh'); ?></li>
                                </ul>
                            </section>
                            <section>
                                <h2><?php esc_html_e('Ingredients', 'fresh'); ?></h2>
                                <p><?php echo esc_html($ingredients ?: __('Ingredient details will be confirmed on the product label or by support before purchase.', 'fresh')); ?></p>
                            </section>
                            <section>
                                <h2><?php esc_html_e('How to Use', 'fresh'); ?></h2>
                                <p><?php echo esc_html($how_to_use ?: __('Use as suitable for your daily cooking, serving, or household routine.', 'fresh')); ?></p>
                            </section>
                            <section>
                                <h2><?php esc_html_e('Specifications', 'fresh'); ?></h2>
                                <dl class="fresh-product-specifications">
                                    <?php if ($sku) : ?>
                                        <dt><?php esc_html_e('SKU', 'fresh'); ?></dt>
                                        <dd><?php echo esc_html($sku); ?></dd>
                                    <?php endif; ?>
                                    <?php if ($unit) : ?>
                                        <dt><?php esc_html_e('Unit', 'fresh'); ?></dt>
                                        <dd><?php echo esc_html($unit); ?></dd>
                                    <?php endif; ?>
                                    <dt><?php esc_html_e('Price', 'fresh'); ?></dt>
                                    <dd><?php echo esc_html(fresh_format_price($price)); ?></dd>
                                    <?php if (! is_wp_error($terms) && $terms) : ?>
                                        <dt><?php esc_html_e('Category', 'fresh'); ?></dt>
                                        <dd><?php echo esc_html(implode(', ', wp_list_pluck($terms, 'name'))); ?></dd>
                                    <?php endif; ?>
                                </dl>
                            </section>
                            <section>
                                <h2><?php esc_html_e('Storage Instructions', 'fresh'); ?></h2>
                                <p><?php echo esc_html($storage ?: __('Store in a cool, dry place away from direct sunlight. Keep the pack closed after opening.', 'fresh')); ?></p>
                            </section>
                            <section>
                                <h2><?php esc_html_e('Shipping Information', 'fresh'); ?></h2>
                                <p><?php echo esc_html($shipping ?: __('Delivery details are confirmed after checkout.', 'fresh')); ?></p>
                                <p><?php echo esc_html($returns ?: __('If an item arrives damaged or incorrect, contact support with your order details for quick help.', 'fresh')); ?></p>
                            </section>
                            <section>
                                <h2><?php esc_html_e('FAQs', 'fresh'); ?></h2>
                                <?php foreach ($faqs as $faq) : ?>
                                    <details>
                                        <summary><?php echo esc_html($faq['question']); ?></summary>
                                        <p><?php echo esc_html($faq['answer']); ?></p>
                                    </details>
                                <?php endforeach; ?>
                            </section>
                            <section>
                                <h2><?php esc_html_e('Customer Reviews', 'fresh'); ?></h2>
                                <p><?php esc_html_e('Customer reviews are collected after orders and will appear here when available.', 'fresh'); ?></p>
                            </section>
                        </div>
                    </div>
                </div>
            </div>
            <?php if ($related_products) : ?>
                <div class="row">
                    <div class="col-12">
                        <section class="fresh-related-products-section pt-50">
                            <div class="section-title-area ltn__section-title-2 text-center">
                                <h2 class="section-title"><?php esc_html_e('Related Products', 'fresh'); ?></h2>
                            </div>
                            <div class="row fresh-related-products">
                                <?php foreach ($related_products as $related_product) : ?>
                                    <?php
                                    get_template_part('template-parts/products/product-card', null, [
                                        'product'      => $related_product,
                                        'column_class' => 'col-xl-3 col-lg-3 col-md-6 col-6',
                                    ]);
                                    ?>
                                <?php endforeach; ?>
                            </div>
                        </section>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>
