<?php
/**
 * Search results template.
 */

get_header();
?>

<main id="primary" class="site-main">
    <div class="container">
        <header class="page-header">
            <h1 class="page-title">
                <?php
                printf(
                    esc_html__('Search results for: %s', 'fresh'),
                    '<span>' . esc_html(get_search_query()) . '</span>'
                );
                ?>
            </h1>
        </header>

        <?php
        if (have_posts()) :
            while (have_posts()) :
                the_post();
                get_template_part('template-parts/content/content');
            endwhile;

            the_posts_navigation();
        else :
            get_template_part('template-parts/content/content', 'none');
        endif;
        ?>
    </div>
</main>

<?php
get_footer();
