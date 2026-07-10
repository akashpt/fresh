<?php
$title = isset($args['title']) ? $args['title'] : __('Our Products', 'fresh');
$limit = isset($args['limit']) ? absint($args['limit']) : 8;
$featured_only = ! empty($args['featured_only']);
$show_featured_only = $featured_only;

if ($featured_only) {
    $featured_check = get_posts([
        'post_type'      => 'fresh_product',
        'post_status'    => 'publish',
        'posts_per_page' => 1,
        'fields'         => 'ids',
        'meta_query'     => [
            [
                'key'   => '_fresh_product_featured_front',
                'value' => '1',
            ],
        ],
    ]);

    $show_featured_only = ! empty($featured_check);
}

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

<div class="ltn__product-tab-area ltn__product-gutter fresh-product-section fresh-product-section-tabs pt-50 pb-0">
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
                    <span class="fresh-section-kicker"><?php esc_html_e('Fresh Picks', 'fresh'); ?></span>
                    <h1 class="section-title"><?php echo esc_html($title); ?></h1>
                    <p><?php esc_html_e('Simple everyday favorites, organized by category and ready for quick ordering.', 'fresh'); ?></p>
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
                        $base_query_args = [
                            'post_type'      => 'fresh_product',
                            'post_status'    => 'publish',
                            'fields'         => 'ids',
                            'posts_per_page' => $tab['term'] ? $limit : -1,
                        ];

                        if ($tab['term']) {
                            $base_query_args['tax_query'] = [
                                [
                                    'taxonomy' => 'fresh_product_category',
                                    'field'    => 'term_id',
                                    'terms'    => $tab['term']->term_id,
                                ],
                            ];
                        }

                        if ($show_featured_only) {
                            $featured_query_args = array_merge($base_query_args, [
                                'posts_per_page' => -1,
                                'meta_key'       => '_fresh_product_front_order',
                                'orderby'        => [
                                    'meta_value_num' => 'ASC',
                                    'title'          => 'ASC',
                                ],
                                'meta_query'     => [
                                    [
                                        'key'   => '_fresh_product_featured_front',
                                        'value' => '1',
                                    ],
                                ],
                            ]);

                            $featured_product_ids = get_posts($featured_query_args);
                            $remaining_limit = $tab['term'] ? max(0, $limit - count($featured_product_ids)) : -1;
                            $regular_product_ids = [];

                            if ($remaining_limit !== 0) {
                                $regular_query_args = array_merge($base_query_args, [
                                    'posts_per_page' => $remaining_limit,
                                    'post__not_in'   => $featured_product_ids,
                                    'orderby'        => 'title',
                                    'order'          => 'ASC',
                                ]);

                                $regular_product_ids = get_posts($regular_query_args);
                            }

                            $product_ids = array_merge($featured_product_ids, $regular_product_ids);
                        } else {
                            $product_ids = get_posts(array_merge($base_query_args, [
                                'orderby' => 'title',
                                'order'   => 'ASC',
                            ]));
                        }
                        ?>
                        <div class="tab-pane fade <?php echo $index === 0 ? 'active show' : ''; ?>" id="<?php echo esc_attr($tab['id']); ?>">
                            <div class="ltn__product-tab-content-inner">
                                <div class="row fresh-featured-product-grid">
                                    <?php if (! empty($product_ids)) : ?>
                                        <?php foreach ($product_ids as $product_id) : ?>
                                            <?php
                                            get_template_part('template-parts/products/product-card', null, [
                                                'product'      => get_post($product_id),
                                                'column_class' => 'col-xl-3 col-lg-4 col-md-4 col-sm-6 col-6',
                                                'title_length' => 30,
                                            ]);
                                            ?>
                                        <?php endforeach; ?>
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
