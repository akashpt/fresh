<?php
/**
 * The header for our theme.
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="shortcut icon" href="<?php echo get_template_directory_uri(); ?>/assets/img/favicon.png" type="image/x-icon" />
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>



    <div class="body-wrapper">
    <?php
    $fresh_marquee_messages = apply_filters('fresh_header_marquee_messages', [
        __('Fresh daily essentials delivered with care', 'fresh'),
        __('Free shipping on eligible orders', 'fresh'),
        __('Secure checkout and quick WhatsApp ordering', 'fresh'),
    ]);
    ?>
    <?php if (! empty($fresh_marquee_messages)) : ?>
        <div class="fresh-top-marquee" aria-label="<?php esc_attr_e('Store updates', 'fresh'); ?>">
            <marquee behavior="scroll" direction="left" scrollamount="5">
                <?php foreach ($fresh_marquee_messages as $fresh_marquee_message) : ?>
                    <span class="fresh-top-marquee-item"><?php echo esc_html($fresh_marquee_message); ?></span>
                <?php endforeach; ?>
            </marquee>
        </div>
    <?php endif; ?>
    <!-- HEADER AREA START (header-5) -->
    <header class="ltn__header-area ltn__header-5 ltn__header-transparent-- gradient-color-4---">
 
        <!-- ltn__header-top-area end -->

        <!-- ltn__header-middle-area start -->
        <div class="ltn__header-middle-area ltn__header-sticky ltn__sticky-bg-white ltn__logo-right-menu-option plr--9---">
            <div class="container">
                <div class="row">
                    <div class="col">
                        <div class="site-logo-wrap">
                           <div class="site-logo">
                                <?php fresh_site_logo(); ?>
                            </div>
                        </div>
                    </div>
                    <div class="col header-menu-column menu-color-white---">
                        <div class="header-menu d-none d-xl-block">
                            <nav>
                                <div class="ltn__main-menu">
                                    <?php
                                    wp_nav_menu([
                                        'theme_location' => 'primary',
                                        'container'      => false,
                                        'fallback_cb'    => 'fresh_primary_menu_fallback',
                                    ]);
                                    ?>
                                </div>
                            </nav>
                        </div>
                    </div>
                    <div class="ltn__header-options ltn__header-options-2 mb-sm-20">
                        <div class="header-cart-icon">
                            <a href="#ltn__utilize-cart-menu" class="ltn__utilize-toggle" title="<?php esc_attr_e('Cart', 'fresh'); ?>" aria-label="<?php esc_attr_e('Open cart sidebar', 'fresh'); ?>">
                                <i class="fas fa-shopping-cart" aria-hidden="true"></i>
                                <sup class="fresh-cart-count"><?php echo esc_html(fresh_cart_count()); ?></sup>
                            </a>
                        </div>
                        <div class="header-cart-icon">
                            <a href="<?php echo esc_url(fresh_page_url('wishlist')); ?>" title="<?php esc_attr_e('Wishlist', 'fresh'); ?>" aria-label="<?php esc_attr_e('Wishlist', 'fresh'); ?>">
                                <i class="far fa-heart" aria-hidden="true"></i>
                                <sup class="fresh-wishlist-count"><?php echo esc_html(fresh_wishlist_count()); ?></sup>
                            </a>
                        </div>
                      
                        <!-- Mobile Menu Button -->
                        <div class="mobile-menu-toggle d-xl-none">
                            <a href="#ltn__utilize-mobile-menu" class="ltn__utilize-toggle" aria-label="<?php esc_attr_e('Open mobile menu', 'fresh'); ?>">
                                <svg viewBox="0 0 800 600" aria-hidden="true" focusable="false">
                                    <path d="M300,220 C300,220 520,220 540,220 C740,220 640,540 520,420 C440,340 300,200 300,200" id="top"></path>
                                    <path d="M300,320 L540,320" id="middle"></path>
                                    <path d="M300,210 C300,210 520,210 540,210 C740,210 640,530 520,410 C440,330 300,190 300,190" id="bottom" transform="translate(480, 320) scale(1, -1) translate(-480, -318) "></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- ltn__header-middle-area end -->
    </header>

    <div class="fresh-mobile-store-bar" aria-label="<?php esc_attr_e('Store shortcuts', 'fresh'); ?>">
        <a href="#ltn__utilize-cart-menu" class="fresh-mobile-store-link fresh-mobile-store-cart ltn__utilize-toggle" aria-label="<?php esc_attr_e('Open cart sidebar', 'fresh'); ?>">
            <span class="fresh-mobile-store-icon">
                <i class="fas fa-shopping-cart" aria-hidden="true"></i>
                <span class="fresh-mobile-store-count fresh-cart-count"><?php echo esc_html(fresh_cart_count()); ?></span>
            </span>
            <span><?php esc_html_e('Cart', 'fresh'); ?></span>
        </a>
        <a href="<?php echo esc_url(fresh_page_url('wishlist')); ?>" class="fresh-mobile-store-link fresh-mobile-store-wishlist">
            <span class="fresh-mobile-store-icon">
                <i class="far fa-heart"></i>
                <span class="fresh-mobile-store-count fresh-wishlist-count"><?php echo esc_html(fresh_wishlist_count()); ?></span>
            </span>
            <span><?php esc_html_e('Wishlist', 'fresh'); ?></span>
        </a>
    </div>

    <?php get_template_part('template-parts/layout/offcanvas'); ?>
