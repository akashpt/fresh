<?php
/**
 * Single post template.
 */

get_header();
?>

<main id="primary" class="site-main">
    <?php fresh_breadcrumb_banner(get_the_title(), __('Latest News', 'fresh')); ?>

    <div class="ltn__page-details-area ltn__blog-details-area mb-120">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <?php
                    while (have_posts()) :
                        the_post();
                        ?>
                        <article id="post-<?php the_ID(); ?>" <?php post_class('ltn__blog-details-wrap'); ?>>
                            <div class="ltn__page-details-inner ltn__blog-details-inner">
                                <?php if (has_post_thumbnail()) : ?>
                                    <div class="ltn__blog-img">
                                        <?php the_post_thumbnail('large'); ?>
                                    </div>
                                <?php endif; ?>

                                <div class="ltn__blog-meta">
                                    <ul>
                                        <li class="ltn__blog-category">
                                            <?php the_category(' '); ?>
                                        </li>
                                        <li class="ltn__blog-author">
                                            <a href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))); ?>">
                                                <i class="far fa-user"></i><?php the_author(); ?>
                                            </a>
                                        </li>
                                        <li class="ltn__blog-date">
                                            <i class="far fa-calendar-alt"></i><?php echo esc_html(get_the_date()); ?>
                                        </li>
                                        <li>
                                            <a href="#comments"><i class="far fa-comments"></i><?php comments_number(__('No Comments', 'fresh'), __('1 Comment', 'fresh'), __('% Comments', 'fresh')); ?></a>
                                        </li>
                                    </ul>
                                </div>

                                <h1 class="ltn__blog-title"><?php the_title(); ?></h1>

                                <div class="fresh-single-content">
                                    <?php
                                    the_content();

                                    wp_link_pages([
                                        'before' => '<div class="page-links">' . esc_html__('Pages:', 'fresh'),
                                        'after'  => '</div>',
                                    ]);
                                    ?>
                                </div>
                            </div>

                            <?php if (has_tag()) : ?>
                                <div class="ltn__blog-tags-social-media mt-80 row">
                                    <div class="ltn__tagcloud-widget col-lg-8">
                                        <h4><?php esc_html_e('Tags', 'fresh'); ?></h4>
                                        <?php the_tags('<ul><li>', '</li><li>', '</li></ul>'); ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </article>

                        <div class="ltn__prev-next-btn row mb-50">
                            <div class="blog-prev col-lg-6">
                                <?php previous_post_link('%link', '<span><i class="fas fa-arrow-left"></i> ' . esc_html__('Previous Post', 'fresh') . '</span><h6 class="ltn__blog-title">%title</h6>'); ?>
                            </div>
                            <div class="blog-next text-end col-lg-6">
                                <?php next_post_link('%link', '<span>' . esc_html__('Next Post', 'fresh') . ' <i class="fas fa-arrow-right"></i></span><h6 class="ltn__blog-title">%title</h6>'); ?>
                            </div>
                        </div>

                        <?php
                        if (comments_open() || get_comments_number()) {
                            comments_template();
                        }
                    endwhile;
                    ?>
                </div>

                <div class="col-lg-4">
                    <aside class="sidebar-area blog-sidebar ltn__right-sidebar">
                        <div class="widget ltn__search-widget">
                            <h4 class="ltn__widget-title ltn__widget-title-border"><?php esc_html_e('Search News', 'fresh'); ?></h4>
                            <?php get_search_form(); ?>
                        </div>

                        <div class="widget ltn__popular-post-widget">
                            <h4 class="ltn__widget-title ltn__widget-title-border"><?php esc_html_e('Recent Posts', 'fresh'); ?></h4>
                            <ul>
                                <?php
                                $recent_posts = new WP_Query([
                                    'post_type'           => 'post',
                                    'post_status'         => 'publish',
                                    'posts_per_page'      => 4,
                                    'ignore_sticky_posts' => true,
                                    'post__not_in'        => [get_the_ID()],
                                ]);

                                if ($recent_posts->have_posts()) :
                                    while ($recent_posts->have_posts()) :
                                        $recent_posts->the_post();
                                        ?>
                                        <li>
                                            <div class="popular-post-widget-item clearfix">
                                                <div class="popular-post-widget-img">
                                                    <a href="<?php the_permalink(); ?>">
                                                        <?php if (has_post_thumbnail()) : ?>
                                                            <?php the_post_thumbnail('thumbnail'); ?>
                                                        <?php else : ?>
                                                            <img <?php echo fresh_image_attrs(get_template_directory_uri() . '/assets/img/blog/1.jpg', get_the_title(), ['fallback_width' => 150, 'fallback_height' => 150]); ?>>
                                                        <?php endif; ?>
                                                    </a>
                                                </div>
                                                <div class="popular-post-widget-brief">
                                                    <h6><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h6>
                                                    <div class="ltn__blog-meta">
                                                        <ul>
                                                            <li class="ltn__blog-date">
                                                                <a href="<?php the_permalink(); ?>"><i class="far fa-calendar-alt"></i><?php echo esc_html(get_the_date()); ?></a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                        <?php
                                    endwhile;
                                    wp_reset_postdata();
                                else :
                                    ?>
                                    <li><?php esc_html_e('Add more posts to show recent news.', 'fresh'); ?></li>
                                <?php endif; ?>
                            </ul>
                        </div>

                        <?php
                        $categories = get_categories([
                            'hide_empty' => true,
                            'orderby'    => 'name',
                        ]);
                        ?>
                        <?php if ($categories) : ?>
                            <div class="widget ltn__menu-widget ltn__menu-widget-2 ltn__menu-widget-2-color-2">
                                <h4 class="ltn__widget-title ltn__widget-title-border"><?php esc_html_e('Categories', 'fresh'); ?></h4>
                                <ul>
                                    <?php foreach ($categories as $category) : ?>
                                        <li>
                                            <a href="<?php echo esc_url(get_category_link($category->term_id)); ?>">
                                                <?php echo esc_html($category->name); ?> <span><?php echo esc_html($category->count); ?></span>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <?php
                        $tags = get_tags([
                            'hide_empty' => true,
                            'number'     => 12,
                        ]);
                        ?>
                        <?php if ($tags) : ?>
                            <div class="widget ltn__tagcloud-widget">
                                <h4 class="ltn__widget-title ltn__widget-title-border"><?php esc_html_e('Popular Tags', 'fresh'); ?></h4>
                                <ul>
                                    <?php foreach ($tags as $tag) : ?>
                                        <li><a href="<?php echo esc_url(get_tag_link($tag)); ?>"><?php echo esc_html($tag->name); ?></a></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <div class="widget ltn__banner-widget">
                            <a href="<?php echo esc_url(fresh_page_url('shop')); ?>">
                                <img <?php echo fresh_image_attrs(get_template_directory_uri() . '/assets/img/banner/banner-4.jpg', __('Shop Fresh Products', 'fresh'), ['fallback_width' => 370, 'fallback_height' => 460]); ?>>
                            </a>
                        </div>
                    </aside>
                </div>
            </div>
        </div>
    </div>

    <?php get_template_part('template-parts/home/features', null, ['variant' => 'bottom']); ?>
</main>

<?php
get_footer();
