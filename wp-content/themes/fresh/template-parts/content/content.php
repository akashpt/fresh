<article id="post-<?php the_ID(); ?>" <?php post_class('fresh-entry'); ?>>
    <header class="entry-header">
        <?php
        if (is_singular()) {
            the_title('<h1 class="entry-title">', '</h1>');
        } else {
            the_title('<h2 class="entry-title"><a href="' . esc_url(get_permalink()) . '" rel="bookmark">', '</a></h2>');
        }
        ?>
    </header>

    <?php if (has_post_thumbnail()) : ?>
        <div class="entry-thumbnail">
            <a href="<?php the_permalink(); ?>">
                <?php the_post_thumbnail('large'); ?>
            </a>
        </div>
    <?php endif; ?>

    <div class="entry-content">
        <?php
        if (is_singular()) {
            the_content();
            wp_link_pages();
        } else {
            the_excerpt();
        }
        ?>
    </div>
</article>
