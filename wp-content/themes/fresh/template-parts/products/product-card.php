<?php
$product = isset($args['product']) ? $args['product'] : get_post();

if (! $product instanceof WP_Post) {
    return;
}

$price      = fresh_product_price($product->ID);
$unit       = get_post_meta($product->ID, '_fresh_product_unit', true);
$sale_price = get_post_meta($product->ID, '_fresh_product_sale_price', true);
$regular    = get_post_meta($product->ID, '_fresh_product_price', true);
$detail_url = fresh_product_detail_url($product->ID);
$column_class = isset($args['column_class']) ? $args['column_class'] : 'col-lg-3 col-md-4 col-sm-6 col-6';
$title_length = isset($args['title_length']) ? absint($args['title_length']) : 35;
$product_title = get_the_title($product);
$display_title = fresh_trim_product_title($product, $title_length);
$product_number = isset($args['product_number']) ? absint($args['product_number']) : 0;
$cart = fresh_get_cart();
$cart_quantity = isset($cart[$product->ID]) ? absint($cart[$product->ID]) : 0;
$card_quantity = $cart_quantity > 0 ? $cart_quantity : 1;
$product_image_url = fresh_product_image_url($product->ID);
?>

<div class="<?php echo esc_attr($column_class); ?>">
    <div class="ltn__product-item ltn__product-item-3 text-left">
        <div class="product-img">
             
            <a href="<?php echo esc_url($detail_url); ?>">
                <img <?php echo fresh_image_attrs($product_image_url, $product_title, ['fallback_width' => 300, 'fallback_height' => 300]); ?>>
            </a>
            <div class="product-badge">
                <ul>
                    <li><a class="fresh-add-to-wishlist" href="<?php echo esc_url(fresh_add_to_wishlist_url($product->ID)); ?>" data-product-id="<?php echo esc_attr($product->ID); ?>" title="<?php esc_attr_e('Wishlist', 'fresh'); ?>" aria-label="<?php esc_attr_e('Add to Wishlist', 'fresh'); ?>">
                            <i class="far fa-heart" aria-hidden="true"></i>
                    </a></li>
                </ul>
            </div>
 
        </div>
        <div class="product-info">
            <div class="product-card-bottom">
                <div>
                    <div class="product-ratting">
                        <ul>
                            <li><a href="<?php echo esc_url($detail_url); ?>" aria-label="<?php echo esc_attr(sprintf(__('View %s details', 'fresh'), $product_title)); ?>"><i class="fas fa-star" aria-hidden="true"></i></a></li>
                            <li><a href="<?php echo esc_url($detail_url); ?>" aria-label="<?php echo esc_attr(sprintf(__('View %s details', 'fresh'), $product_title)); ?>"><i class="fas fa-star" aria-hidden="true"></i></a></li>
                            <li><a href="<?php echo esc_url($detail_url); ?>" aria-label="<?php echo esc_attr(sprintf(__('View %s details', 'fresh'), $product_title)); ?>"><i class="fas fa-star" aria-hidden="true"></i></a></li>
                            <li><a href="<?php echo esc_url($detail_url); ?>" aria-label="<?php echo esc_attr(sprintf(__('View %s details', 'fresh'), $product_title)); ?>"><i class="fas fa-star-half-alt" aria-hidden="true"></i></a></li>
                            <li><a href="<?php echo esc_url($detail_url); ?>" aria-label="<?php echo esc_attr(sprintf(__('View %s details', 'fresh'), $product_title)); ?>"><i class="far fa-star" aria-hidden="true"></i></a></li>
                        </ul>
                    </div>
                    <h2 class="product-title">
                        <a href="<?php echo esc_url($detail_url); ?>" title="<?php echo esc_attr($product_title); ?>">
                            <?php echo esc_html($display_title); ?>
                        </a>
                    </h2>
                </div>
                <div class="fresh-card-cart-control">
                    <div class="fresh-card-qty-control">
                        <button class="fresh-card-qty-btn fresh-card-qty-minus" type="button" aria-label="<?php esc_attr_e('Decrease quantity', 'fresh'); ?>">-</button>
                        <input class="fresh-card-qty" type="number" min="0" step="1" value="<?php echo esc_attr($card_quantity); ?>" aria-label="<?php esc_attr_e('Quantity', 'fresh'); ?>">
                        <button class="fresh-card-qty-btn fresh-card-qty-plus" type="button" aria-label="<?php esc_attr_e('Increase quantity', 'fresh'); ?>">+</button>
                    </div>
                    <a class="product-cart-btn fresh-add-to-cart" href="<?php echo esc_url(fresh_add_to_cart_url($product->ID)); ?>" data-product-id="<?php echo esc_attr($product->ID); ?>" data-cart-mode="set" title="<?php esc_attr_e('Update Cart', 'fresh'); ?>" aria-label="<?php esc_attr_e('Update Cart', 'fresh'); ?>">
                        <i class="fas fa-shopping-cart" aria-hidden="true"></i>
                        <span><?php echo esc_html($cart_quantity > 0 ? __('Update', 'fresh') : __('Add', 'fresh')); ?></span>
                    </a>
                </div>
            </div>
            <div class="product-card-bottom">
                <div class="product-price">
                    <span><?php echo esc_html(fresh_format_price($price)); ?></span>
                    <?php if ($sale_price !== '' && $regular !== '' && (float) $sale_price < (float) $regular) : ?>
                        <del><?php echo esc_html(fresh_format_price($regular)); ?></del>
                    <?php endif; ?>
                    <?php if ($unit) : ?>
                        <small>/ <?php echo esc_html($unit); ?></small>
                    <?php endif; ?>
                </div>
                
            </div>
        </div>
    </div>
</div>
