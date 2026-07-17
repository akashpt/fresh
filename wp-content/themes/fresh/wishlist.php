<?php
/**
 * Template Name: Wishlist
 * Template Post Type: page
 */

get_header();

$items = fresh_wishlist_items();

fresh_breadcrumb_banner(__('Wishlist', 'fresh'), __('Saved products', 'fresh'));
?>

<main id="primary" class="site-main">
    <div class="liton__shoping-cart-area mb-120">
        <div class="container">
            <?php if ($items) : ?>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="shoping-cart-inner">
                            <div class="shoping-cart-table table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th class="cart-product-remove"><?php esc_html_e('Remove', 'fresh'); ?></th>
                                            <th class="cart-product-image"><?php esc_html_e('Image', 'fresh'); ?></th>
                                            <th class="cart-product-info"><?php esc_html_e('Product', 'fresh'); ?></th>
                                            <th class="cart-product-price"><?php esc_html_e('Price', 'fresh'); ?></th>
                                            <th class="cart-product-add-cart"><?php esc_html_e('Cart', 'fresh'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($items as $product) : ?>
                                            <?php $product_image_url = fresh_product_image_url($product->ID); ?>
                                            <tr class="fresh-wishlist-item">
                                                <td class="cart-product-remove">
                                                    <a class="fresh-remove-from-wishlist" href="<?php echo esc_url(fresh_remove_from_wishlist_url($product->ID)); ?>" data-product-id="<?php echo esc_attr($product->ID); ?>" aria-label="<?php esc_attr_e('Remove item', 'fresh'); ?>">x</a>
                                                </td>
                                                <td class="cart-product-image">
                                                    <a href="<?php echo esc_url(fresh_product_detail_url($product->ID)); ?>">
                                                        <img <?php echo fresh_image_attrs($product_image_url, get_the_title($product), ['fallback_width' => 300, 'fallback_height' => 300]); ?>>
                                                    </a>
                                                </td>
                                                <td class="cart-product-info">
                                                    <h4>
                                                        <a href="<?php echo esc_url(fresh_product_detail_url($product->ID)); ?>">
                                                            <?php echo esc_html(get_the_title($product)); ?>
                                                        </a>
                                                    </h4>
                                                </td>
                                                <td class="cart-product-price">
                                                    <?php echo esc_html(fresh_format_price(fresh_product_price($product->ID))); ?>
                                                </td>
                                                <td class="cart-product-add-cart">
                                                    <a class="theme-btn-1 btn btn-effect-1 fresh-add-to-cart" href="<?php echo esc_url(fresh_add_to_cart_url($product->ID)); ?>" data-product-id="<?php echo esc_attr($product->ID); ?>">
                                                        <i class="fas fa-shopping-cart"></i>
                                                        <?php esc_html_e('Add to Cart', 'fresh'); ?>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else : ?>
                <div class="text-center pt-60 pb-60">
                    <h2><?php esc_html_e('Your wishlist is empty.', 'fresh'); ?></h2>
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
