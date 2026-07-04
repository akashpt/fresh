<?php
/**
 * 404 template.
 */

get_header();
?>

<main id="primary" class="site-main">
    <div class="container">
        <section class="error-404 not-found">
            <header class="page-header">
                <h1 class="page-title"><?php esc_html_e('Page not found', 'fresh'); ?></h1>
            </header>

            <div class="page-content">
                <p><?php esc_html_e('The page you are looking for could not be found.', 'fresh'); ?></p>
                <?php get_search_form(); ?>
            </div>
        </section>
    </div>
</main>

<?php
get_footer();
