<?php
/**
 * Main template file.
 */

get_header();
?>

<main id="primary" class="site-main">
    <div class="container">
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
