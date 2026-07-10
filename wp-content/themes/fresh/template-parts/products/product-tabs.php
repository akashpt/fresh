<?php
$title = isset($args['title']) ? $args['title'] : __('Our Products', 'fresh');
$limit = isset($args['limit']) ? absint($args['limit']) : 8;

$terms = get_terms([
    'taxonomy'   => 'fresh_product_category',
    'hide_empty' => true,
    'orderby'    => 'name',
    'order'      => 'ASC',
]);

$tabs = [
    [
        'id'    => 'fresh_products_all',
        'label' => __('All', 'fresh'),
        'term'  => null,
    ],
];

if (! is_wp_error($terms)) {
    foreach ($terms as $term) {
        $tabs[] = [
            'id'    => 'fresh_products_' . $term->term_id,
            'label' => $term->name,
            'term'  => $term,
        ];
    }
}
?>

<div class="ltn__product-tab-area ltn__product-gutter fresh-product-section fresh-product-section-tabs pt-115 pb-0">
    <div class="container">
        <?php if (isset($_GET['fresh_added'])) : ?>
            <div class="alert alert-success fresh-cart-added">
                <?php esc_html_e('Product added to cart.', 'fresh'); ?>
                <a href="<?php echo esc_url(fresh_page_url('cart')); ?>"><?php esc_html_e('View Cart', 'fresh'); ?></a>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-lg-12">
                <div class="section-title-area ltn__section-title-2 text-center fresh-products-heading">
                    <span class="fresh-section-kicker"><?php esc_html_e('Shop by Category', 'fresh'); ?></span>
                    <h1 class="section-title"><?php echo esc_html($title); ?></h1>
                    <p><?php esc_html_e('Choose fresh daily essentials from our most-loved categories.', 'fresh'); ?></p>
                </div>
                <div class="ltn__tab-menu ltn__tab-menu-2 ltn__tab-menu-top-right-- text-center fresh-products-tabs">
                    <div class="nav">
                        <?php foreach ($tabs as $index => $tab) : ?>
                            <a class="<?php echo $index === 0 ? 'active show' : ''; ?>" data-bs-toggle="tab" href="#<?php echo esc_attr($tab['id']); ?>">
                                <?php echo esc_html($tab['label']); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="tab-content">
                    <?php foreach ($tabs as $index => $tab) : ?>
                        <?php
                        $query_args = [
                            'post_type'      => 'fresh_product',
                            'post_status'    => 'publish',
                            'posts_per_page' => $tab['term'] ? $limit : -1,
                        ];

                        if ($tab['term']) {
                            $query_args['tax_query'] = [
                                [
                                    'taxonomy' => 'fresh_product_category',
                                    'field'    => 'term_id',
                                    'terms'    => $tab['term']->term_id,
                                ],
                            ];
                        }

                        $products = new WP_Query($query_args);
                        ?>
                        <div class="tab-pane fade <?php echo $index === 0 ? 'active show' : ''; ?>" id="<?php echo esc_attr($tab['id']); ?>">
                            <div class="ltn__product-tab-content-inner">
                                <div class="row ltn__tab-product-slider-one-active slick-arrow-1">
                                    <?php if ($products->have_posts()) : ?>
                                        <?php
                                        while ($products->have_posts()) :
                                            $products->the_post();
                                            get_template_part('template-parts/products/product-card', null, [
                                                'product'      => get_post(),
                                                'column_class' => 'col-lg-12',
                                                'title_length' => 30,
                                            ]);
                                        endwhile;
                                        wp_reset_postdata();
                                        ?>
                                    <?php else : ?>
                                        <div class="col-lg-12 fresh-product-empty">
                                            <p><?php esc_html_e('No products found in this category.', 'fresh'); ?></p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>
