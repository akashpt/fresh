<?php
$published_products = (int) wp_count_posts('fresh_product')->publish;
$categories = get_terms([
    'taxonomy'   => 'fresh_product_category',
    'hide_empty' => false,
    'fields'     => 'ids',
]);
$category_count = is_wp_error($categories) ? 0 : count($categories);
$published_orders = (int) wp_count_posts('fresh_order')->publish;
$published_posts = (int) wp_count_posts('post')->publish;

$counters = [
    ['icon' => 'icons/icon-img/2.png', 'number' => fresh_counter_number('products', $published_products), 'suffix' => '+', 'label' => __('Products', 'fresh')],
    ['icon' => 'icons/icon-img/3.png', 'number' => fresh_counter_number('categories', $category_count), 'suffix' => '+', 'label' => __('Categories', 'fresh')],
    ['icon' => 'icons/icon-img/4.png', 'number' => fresh_counter_number('orders', $published_orders), 'suffix' => '+', 'label' => __('Orders', 'fresh')],
    ['icon' => 'icons/icon-img/5.png', 'number' => fresh_counter_number('blog_posts', $published_posts), 'suffix' => '+', 'label' => __('Blog Posts', 'fresh')],
];

$counter_class = fresh_home_option('counter_class');
?>

<div class="<?php echo esc_attr($counter_class); ?>" data-bg="<?php echo esc_url(get_template_directory_uri() . '/assets/img/bg/5.jpg'); ?>">
    <div class="container">
        <div class="row">
            <?php foreach ($counters as $counter) : ?>
                <div class="col-md-3 col-sm-6 align-self-center">
                    <div class="ltn__counterup-item-3 text-color-white text-center">
                        <div class="counter-icon">
                            <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/' . $counter['icon']); ?>" alt="">
                        </div>
                        <h1><span class="counter"><?php echo esc_html($counter['number']); ?></span><span class="counterUp-icon"><?php echo esc_html($counter['suffix']); ?></span></h1>
                        <h6><?php echo esc_html($counter['label']); ?></h6>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
