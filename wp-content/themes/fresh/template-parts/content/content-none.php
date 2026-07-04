<section class="no-results not-found">
    <header class="page-header">
        <h1 class="page-title"><?php esc_html_e('Nothing found', 'fresh'); ?></h1>
    </header>

    <div class="page-content">
        <?php if (is_search()) : ?>
            <p><?php esc_html_e('Try another search term.', 'fresh'); ?></p>
            <?php get_search_form(); ?>
        <?php else : ?>
            <p><?php esc_html_e('Add content in WordPress admin to show it here.', 'fresh'); ?></p>
        <?php endif; ?>
    </div>
</section>
