<?php
$terms = get_terms([
    'taxonomy'   => 'fresh_product_category',
    'hide_empty' => false,
    'orderby'    => 'name',
    'order'      => 'ASC',
]);

$icons = ['category-1.png', 'category-2.png', 'category-3.png', 'category-4.png', 'category-5.png'];
?>

<div class="ltn__category-area section-bg-1-- ltn__primary-bg before-bg-1 bg-image bg-overlay-theme-black-5--0 pt-115 pb-90" data-bg="<?php echo esc_url(get_template_directory_uri() . '/assets/img/bg/5.jpg'); ?>">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="section-title-area ltn__section-title-2 text-center">
                    <h1 class="section-title white-color"><?php esc_html_e('Top Categories', 'fresh'); ?></h1>
                </div>
            </div>
        </div>
        <div class="row ltn__category-slider-active slick-arrow-1">
            <div class="col-12">
                <div class="ltn__category-item ltn__category-item-3 text-center">
                    <div class="ltn__category-item-img">
                        <a href="<?php echo esc_url(fresh_page_url('shop')); ?>">
                            <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/icons/icon-img/category-1.png'); ?>" alt="">
                        </a>
                    </div>
                    <div class="ltn__category-item-name">
                        <h5><a href="<?php echo esc_url(fresh_page_url('shop')); ?>"><?php esc_html_e('Browse all', 'fresh'); ?></a></h5>
                        <h6>(<?php echo esc_html(wp_count_posts('fresh_product')->publish); ?> <?php esc_html_e('item', 'fresh'); ?>)</h6>
                    </div>
                </div>
            </div>
            <?php if (! is_wp_error($terms)) : ?>
                <?php foreach ($terms as $index => $term) : ?>
                    <div class="col-12">
                        <div class="ltn__category-item ltn__category-item-3 text-center">
                            <div class="ltn__category-item-img">
                                <a href="<?php echo esc_url(add_query_arg('category', $term->slug, fresh_page_url('shop'))); ?>">
                                    <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/icons/icon-img/' . $icons[$index % count($icons)]); ?>" alt="">
                                </a>
                            </div>
                            <div class="ltn__category-item-name">
                                <h5><a href="<?php echo esc_url(add_query_arg('category', $term->slug, fresh_page_url('shop'))); ?>"><?php echo esc_html($term->name); ?></a></h5>
                                <h6>(<?php echo esc_html($term->count); ?> <?php esc_html_e('item', 'fresh'); ?>)</h6>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
