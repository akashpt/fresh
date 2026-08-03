<?php
/**
 * Product category archive template.
 */

get_header();

$term = get_queried_object();
$title = $term instanceof WP_Term ? $term->name : __('Product Category', 'fresh');
$description = $term instanceof WP_Term ? term_description($term) : '';

fresh_breadcrumb_banner($title, __('Shop by category', 'fresh'));
?>

<main id="primary" class="site-main">
    <?php if ($description) : ?>
        <section class="fresh-category-intro pt-50">
            <div class="container">
                <div class="archive-description">
                    <?php echo wp_kses_post($description); ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <?php
    get_template_part('template-parts/products/product-grid', null, [
        'title'         => $title,
        'limit'         => -1,
        'show_filters'  => true,
        'category'      => $term instanceof WP_Term ? $term->slug : '',
    ]);
    ?>
</main>

<?php
get_footer();
