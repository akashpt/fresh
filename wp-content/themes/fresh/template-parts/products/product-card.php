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
$product_number = isset($args['product_number']) ? absint($args['product_number']) : 0;
?>

<div class="<?php echo esc_attr($column_class); ?>">
    <div class="ltn__product-item ltn__product-item-3 text-left">
        <div class="product-img">
             
            <a href="<?php echo esc_url($detail_url); ?>">
                <img src="<?php echo esc_url(fresh_product_image_url($product->ID)); ?>" alt="<?php echo esc_attr(get_the_title($product)); ?>">
            </a>
            <div class="product-badge">
                <ul>
                    <a class="fresh-add-to-wishlist" href="<?php echo esc_url(fresh_add_to_wishlist_url($product->ID)); ?>" data-product-id="<?php echo esc_attr($product->ID); ?>" title="<?php esc_attr_e('Wishlist', 'fresh'); ?>" aria-label="<?php esc_attr_e('Add to Wishlist', 'fresh'); ?>">
                            <i class="far fa-heart"></i>
                    </a>
                </ul>
            </div>
 
        </div>
        <div class="product-info">
            <div class="product-card-bottom">
                <div>
                    <div class="product-ratting">
                        <ul>
                            <li><a href="<?php echo esc_url($detail_url); ?>"><i class="fas fa-star"></i></a></li>
                            <li><a href="<?php echo esc_url($detail_url); ?>"><i class="fas fa-star"></i></a></li>
                            <li><a href="<?php echo esc_url($detail_url); ?>"><i class="fas fa-star"></i></a></li>
                            <li><a href="<?php echo esc_url($detail_url); ?>"><i class="fas fa-star-half-alt"></i></a></li>
                            <li><a href="<?php echo esc_url($detail_url); ?>"><i class="far fa-star"></i></a></li>
                        </ul>
                    </div>
                    <h2 class="product-title">
                        <a href="<?php echo esc_url($detail_url); ?>">
                            <?php echo esc_html(get_the_title($product)); ?>
                        </a>
                    </h2>
                </div>
                <a class="product-cart-btn fresh-add-to-cart" href="<?php echo esc_url(fresh_add_to_cart_url($product->ID)); ?>" data-product-id="<?php echo esc_attr($product->ID); ?>" title="<?php esc_attr_e('Add to Cart', 'fresh'); ?>" aria-label="<?php esc_attr_e('Add to Cart', 'fresh'); ?>">
                    <i class="fas fa-shopping-cart"></i>
                </a>
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
