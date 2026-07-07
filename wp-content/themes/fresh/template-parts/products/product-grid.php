<?php
$title = isset($args['title']) ? $args['title'] : __('Products', 'fresh');
$limit = isset($args['limit']) ? absint($args['limit']) : 8;
$show_counter = ! empty($args['show_counter']);
$show_filters = ! empty($args['show_filters']);
$selected_category = ! empty($_GET['category']) ? sanitize_title(wp_unslash($_GET['category'])) : '';
$selected_sort = ! empty($_GET['sort']) ? sanitize_key(wp_unslash($_GET['sort'])) : 'latest';
$shop_url = fresh_page_url('shop');

$query_args = [
    'post_type'      => 'fresh_product',
    'post_status'    => 'publish',
    'posts_per_page' => $limit,
];

if ($selected_category) {
    $query_args['tax_query'] = [
        [
            'taxonomy' => 'fresh_product_category',
            'field'    => 'slug',
            'terms'    => $selected_category,
        ],
    ];
}

if ($selected_sort === 'price-low') {
    $query_args['meta_key'] = '_fresh_product_price';
    $query_args['orderby']  = 'meta_value_num';
    $query_args['order']    = 'ASC';
} elseif ($selected_sort === 'price-high') {
    $query_args['meta_key'] = '_fresh_product_price';
    $query_args['orderby']  = 'meta_value_num';
    $query_args['order']    = 'DESC';
} elseif ($selected_sort === 'name') {
    $query_args['orderby'] = 'title';
    $query_args['order']   = 'ASC';
} else {
    $selected_sort = 'latest';
    $query_args['orderby'] = 'date';
    $query_args['order']   = 'DESC';
}

$products = new WP_Query($query_args);
$categories = get_terms([
    'taxonomy'   => 'fresh_product_category',
    'hide_empty' => true,
]);
$product_counts = wp_count_posts('fresh_product');
$all_products_count = isset($product_counts->publish) ? (int) $product_counts->publish : 0;
$selected_category_label = __('All Products', 'fresh');
if ($selected_category && ! is_wp_error($categories)) {
    foreach ($categories as $category) {
        if ($selected_category === $category->slug) {
            $selected_category_label = $category->name;
            break;
        }
    }
}
$sort_options = [
    'latest'     => __('Newest First', 'fresh'),
    'price-low'  => __('Price: Low to High', 'fresh'),
    'price-high' => __('Price: High to Low', 'fresh'),
    'name'       => __('Name: A to Z', 'fresh'),
];
?>

<section class="ltn__product-area ltn__product-gutter fresh-product-section fresh-product-section-featured <?php echo $show_filters ? 'fresh-shop-page' : ''; ?> pt-50 pb-70">
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
                    <span class="fresh-section-kicker"><?php esc_html_e('Customer Favorites', 'fresh'); ?></span>
                    <h1 class="section-title"><?php echo esc_html($title); ?></h1>
                    <p><?php esc_html_e('Popular picks selected for freshness, value, and everyday use.', 'fresh'); ?></p>
                </div>
            </div>
        </div>

        <div class="<?php echo $show_filters ? 'fresh-shop-layout' : ''; ?>">
            <?php if ($show_filters) : ?>
                <aside class="fresh-shop-options" aria-label="<?php esc_attr_e('Shop filters', 'fresh'); ?>">
                    <div class="fresh-shop-options-head">
                        <span><?php esc_html_e('Filters', 'fresh'); ?></span>
                        <?php if ($selected_category || $selected_sort !== 'latest') : ?>
                            <a href="<?php echo esc_url($shop_url); ?>"><?php esc_html_e('Clear', 'fresh'); ?></a>
                        <?php endif; ?>
                    </div>

                    <details class="fresh-shop-category-panel">
                        <summary>
                            <span>
                                <i class="fas fa-layer-group" aria-hidden="true"></i>
                                <?php esc_html_e('Categories', 'fresh'); ?>
                            </span>
                           
                        </summary>
                        <div class="fresh-shop-category-list">
                            <a class="<?php echo $selected_category ? '' : 'is-active'; ?>" href="<?php echo esc_url(add_query_arg('sort', $selected_sort, $shop_url)); ?>">
                                <span><?php esc_html_e('All Products', 'fresh'); ?></span>
                                <small><?php echo esc_html($all_products_count); ?></small>
                            </a>
                            <?php if (! is_wp_error($categories)) : ?>
                                <?php foreach ($categories as $category) : ?>
                                    <?php
                                    $category_url = add_query_arg([
                                        'category' => $category->slug,
                                        'sort'     => $selected_sort,
                                    ], $shop_url);
                                    ?>
                                    <a class="<?php echo $selected_category === $category->slug ? 'is-active' : ''; ?>" href="<?php echo esc_url($category_url); ?>">
                                        <span><?php echo esc_html($category->name); ?></span>
                                        <small><?php echo esc_html($category->count); ?></small>
                                    </a>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </details>

                    <form class="fresh-shop-sort-form" action="<?php echo esc_url($shop_url); ?>" method="get">
                        <?php if ($selected_category) : ?>
                            <input type="hidden" name="category" value="<?php echo esc_attr($selected_category); ?>">
                        <?php endif; ?>
                        <label for="fresh-shop-sort"><?php esc_html_e('Sort By', 'fresh'); ?></label>
                        <div class="fresh-shop-sort-row">
                            <select id="fresh-shop-sort" name="sort">
                                <?php foreach ($sort_options as $value => $label) : ?>
                                    <option value="<?php echo esc_attr($value); ?>" <?php selected($selected_sort, $value); ?>>
                                        <?php echo esc_html($label); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit"><?php esc_html_e('Apply', 'fresh'); ?></button>
                        </div>
                    </form>
                </aside>
            <?php endif; ?>

            <div class="<?php echo $show_filters ? 'fresh-shop-results' : ''; ?>">
                <?php if ($show_filters) : ?>
                    <div class="fresh-shop-toolbar">
                        <div>
                            <strong><?php echo esc_html($products->found_posts); ?></strong>
                            <span><?php esc_html_e('items found', 'fresh'); ?></span>
                        </div>
                        <span><?php echo esc_html($sort_options[$selected_sort]); ?></span>
                    </div>
                <?php endif; ?>

                <div class="row ltn__tab-product-slider-one-active--- slick-arrow-1 fresh-shop-product-grid">
                    <?php if ($products->have_posts()) : ?>
                        <?php
                        while ($products->have_posts()) :
                            $products->the_post();
                            $card_args = ['product' => get_post()];

                            if ($show_filters) {
                                $card_args['column_class'] = 'col-xl-4 col-lg-4 col-md-6 col-6';
                            }

                            if ($show_counter) {
                                $card_args['product_number'] = $products->current_post + 1;
                            }

                            get_template_part('template-parts/products/product-card', null, $card_args);
                        endwhile;
                        wp_reset_postdata();
                        ?>
                    <?php else : ?>
                        <div class="col-12 fresh-product-empty">
                            <p><?php esc_html_e('No products found. Try another category or sorting option.', 'fresh'); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>
