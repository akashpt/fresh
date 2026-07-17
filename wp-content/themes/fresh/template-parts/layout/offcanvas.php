<div id="ltn__utilize-cart-menu" class="ltn__utilize ltn__utilize-cart-menu">
    <div class="ltn__utilize-menu-inner ltn__scrollbar">
        <div class="ltn__utilize-menu-head">
            <span class="ltn__utilize-menu-title"><?php esc_html_e('Cart', 'fresh'); ?></span>
            <button class="ltn__utilize-close" aria-label="<?php esc_attr_e('Close cart sidebar', 'fresh'); ?>">&times;</button>
        </div>
        <div class="fresh-mini-cart-content">
            <?php echo fresh_mini_cart_html(); ?>
        </div>
    </div>
</div>

<div id="ltn__utilize-mobile-menu" class="ltn__utilize ltn__utilize-mobile-menu">
    <div class="ltn__utilize-menu-inner ltn__scrollbar">
        <div class="ltn__utilize-menu-head">
            <div class="site-logo">
                <?php fresh_site_logo(); ?>
            </div>
            <button class="ltn__utilize-close" aria-label="<?php esc_attr_e('Close mobile menu', 'fresh'); ?>">&times;</button>
        </div>
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
                    <a href="#ltn__utilize-cart-menu" class="ltn__utilize-toggle" title="<?php esc_attr_e('Shopping Cart', 'fresh'); ?>" aria-label="<?php esc_attr_e('Open cart sidebar', 'fresh'); ?>">
                        <span class="utilize-btn-icon">
                            <i class="fas fa-shopping-cart" aria-hidden="true"></i>
                            <sup class="fresh-cart-count"><?php echo esc_html(fresh_cart_count()); ?></sup>
                        </span>
                        <?php esc_html_e('Shopping Cart', 'fresh'); ?>
                    </a>
                </li>
                <li>
                    <a href="<?php echo esc_url(fresh_page_url('wishlist')); ?>" title="<?php esc_attr_e('Wishlist', 'fresh'); ?>">
                        <span class="utilize-btn-icon">
                            <i class="far fa-heart" aria-hidden="true"></i>
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
