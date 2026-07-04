<?php
$title = isset($args['title']) ? $args['title'] : __('Products', 'fresh');
$limit = isset($args['limit']) ? absint($args['limit']) : 8;
$show_counter = ! empty($args['show_counter']);

$query_args = [
    'post_type'      => 'fresh_product',
    'post_status'    => 'publish',
    'posts_per_page' => $limit,
];

if (! empty($_GET['category'])) {
    $query_args['tax_query'] = [
        [
            'taxonomy' => 'fresh_product_category',
            'field'    => 'slug',
            'terms'    => sanitize_title(wp_unslash($_GET['category'])),
        ],
    ];
}

$products = new WP_Query($query_args);
?>

<section class="ltn__product-area ltn__product-gutter pt-115 pb-70">
    <div class="container">
        <?php if (isset($_GET['fresh_added'])) : ?>
            <div class="alert alert-success fresh-cart-added">
                <?php esc_html_e('Product added to cart.', 'fresh'); ?>
                <a href="<?php echo esc_url(fresh_page_url('cart')); ?>"><?php esc_html_e('View Cart', 'fresh'); ?></a>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-lg-12">
                <div class="section-title-area ltn__section-title-2 text-center">
                    <h1 class="section-title"><?php echo esc_html($title); ?></h1>
                </div>
            </div>
        </div>

        <div class="row ltn__tab-product-slider-one-active--- slick-arrow-1">
            <?php if ($products->have_posts()) : ?>
                <?php
                while ($products->have_posts()) :
                    $products->the_post();
                    $card_args = ['product' => get_post()];

                    if ($show_counter) {
                        $card_args['product_number'] = $products->current_post + 1;
                    }

                    get_template_part('template-parts/products/product-card', null, $card_args);
                endwhile;
                wp_reset_postdata();
                ?>
            <?php else : ?>
                <div class="col-12 fresh-product-empty">
                    <p><?php esc_html_e('No products added yet. Add products from Products in the WordPress admin.', 'fresh'); ?></p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
