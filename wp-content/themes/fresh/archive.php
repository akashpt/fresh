<?php
/**
 * Archive template.
 */

get_header();
?>

<main id="primary" class="site-main">
    <div class="container">
        <?php if (have_posts()) : ?>
            <header class="page-header">
                <?php the_archive_title('<h1 class="page-title">', '</h1>'); ?>
                <?php the_archive_description('<div class="archive-description">', '</div>'); ?>
            </header>

            <?php
            while (have_posts()) :
                the_post();
                get_template_part('template-parts/content/content');
            endwhile;

            the_posts_navigation();
            ?>
        <?php else : ?>
            <?php get_template_part('template-parts/content/content', 'none'); ?>
        <?php endif; ?>
    </div>
</main>

<?php
get_footer();
