<?php
$cart_items = fresh_cart_items();
$cart_total = fresh_cart_total();
?>

<div id="ltn__utilize-cart-menu" class="ltn__utilize ltn__utilize-cart-menu">
    <div class="ltn__utilize-menu-inner ltn__scrollbar">
        <div class="ltn__utilize-menu-head">
            <span class="ltn__utilize-menu-title"><?php esc_html_e('Cart', 'fresh'); ?></span>
            <button class="ltn__utilize-close">x</button>
        </div>
        <div class="mini-cart-product-area ltn__scrollbar">
            <?php if ($cart_items) : ?>
                <?php foreach ($cart_items as $item) : ?>
                    <?php $product = $item['product']; ?>
                    <div class="mini-cart-item clearfix">
                        <div class="mini-cart-img">
                            <a href="<?php echo esc_url(fresh_product_detail_url($product->ID)); ?>">
                                <img src="<?php echo esc_url(fresh_product_image_url($product->ID)); ?>" alt="<?php echo esc_attr(get_the_title($product)); ?>">
                            </a>
                            <a class="mini-cart-item-delete" href="<?php echo esc_url(fresh_remove_from_cart_url($product->ID)); ?>"><i class="icon-cancel"></i></a>
                        </div>
                        <div class="mini-cart-info">
                            <h6><a href="<?php echo esc_url(fresh_product_detail_url($product->ID)); ?>"><?php echo esc_html(get_the_title($product)); ?></a></h6>
                            <span class="mini-cart-quantity"><?php echo esc_html($item['quantity']); ?> x <?php echo esc_html(fresh_format_price($item['price'])); ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else : ?>
                <p><?php esc_html_e('Your cart is empty.', 'fresh'); ?></p>
            <?php endif; ?>
        </div>
        <div class="mini-cart-footer">
            <div class="mini-cart-sub-total">
                <h5><?php esc_html_e('Subtotal:', 'fresh'); ?> <span><?php echo esc_html(fresh_format_price($cart_total)); ?></span></h5>
            </div>
            <div class="btn-wrapper">
                <a href="<?php echo esc_url(fresh_page_url('cart')); ?>" class="theme-btn-1 btn btn-effect-1"><?php esc_html_e('View Cart', 'fresh'); ?></a>
                <a href="<?php echo esc_url(fresh_page_url('checkout')); ?>" class="theme-btn-2 btn btn-effect-2"><?php esc_html_e('Checkout', 'fresh'); ?></a>
            </div>
            <p><?php esc_html_e('Free Shipping on All Orders Over $100!', 'fresh'); ?></p>
        </div>
    </div>
</div>

<div id="ltn__utilize-mobile-menu" class="ltn__utilize ltn__utilize-mobile-menu">
    <div class="ltn__utilize-menu-inner ltn__scrollbar">
        <div class="ltn__utilize-menu-head">
            <div class="site-logo">
                <?php fresh_site_logo(); ?>
            </div>
            <button class="ltn__utilize-close">x</button>
        </div>
        <!-- <div class="ltn__utilize-menu-search-form">
            <?php //get_search_form(); ?>
        </div> -->
        <div class="ltn__utilize-menu">
            <?php
            wp_nav_menu([
                'theme_location' => 'primary',
                'container'      => false,
                'fallback_cb'    => 'fresh_primary_menu_fallback',
            ]);
            ?>
        </div>
        <div class="ltn__utilize-buttons ltn__utilize-buttons-2">
            <ul>
                <li>
                    <a href="<?php echo esc_url(fresh_page_url('cart')); ?>" title="<?php esc_attr_e('Shopping Cart', 'fresh'); ?>">
                        <span class="utilize-btn-icon">
                            <i class="fas fa-shopping-cart"></i>
                            <sup class="fresh-cart-count"><?php echo esc_html(fresh_cart_count()); ?></sup>
                        </span>
                        <?php esc_html_e('Shopping Cart', 'fresh'); ?>
                    </a>
                </li>
                <li>
                    <a href="<?php echo esc_url(fresh_page_url('wishlist')); ?>" title="<?php esc_attr_e('Wishlist', 'fresh'); ?>">
                        <span class="utilize-btn-icon">
                            <i class="far fa-heart"></i>
                            <sup class="fresh-wishlist-count"><?php echo esc_html(fresh_wishlist_count()); ?></sup>
                        </span>
                        <?php esc_html_e('Wishlist', 'fresh'); ?>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>

<div class="ltn__utilize-overlay"></div>
