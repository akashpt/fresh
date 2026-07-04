<?php
$posts = new WP_Query([
    'post_type'      => 'post',
    'post_status'    => 'publish',
    'posts_per_page' => 5,
]);
?>

<div class="ltn__blog-area pt-115 pb-70 d-none">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="section-title-area ltn__section-title-2 text-center">
                    <h1 class="section-title white-color---"><?php esc_html_e('Latest Blog', 'fresh'); ?></h1>
                </div>
            </div>
        </div>
        <div class="row ltn__blog-slider-one-active slick-arrow-1 ltn__blog-item-3-normal">
            <?php if ($posts->have_posts()) : ?>
                <?php while ($posts->have_posts()) : $posts->the_post(); ?>
                    <div class="col-lg-12">
                        <div class="ltn__blog-item ltn__blog-item-3">
                            <div class="ltn__blog-img">
                                <a href="<?php the_permalink(); ?>">
                                    <?php if (has_post_thumbnail()) : ?>
                                        <?php the_post_thumbnail('medium_large'); ?>
                                    <?php else : ?>
                                        <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/blog/1.jpg'); ?>" alt="">
                                    <?php endif; ?>
                                </a>
                            </div>
                            <div class="ltn__blog-brief">
                                <div class="ltn__blog-meta">
                                    <ul>
                                        <li class="ltn__blog-author"><a href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))); ?>"><i class="far fa-user"></i><?php the_author(); ?></a></li>
                                        <li class="ltn__blog-tags"><i class="fas fa-tags"></i><?php echo esc_html(wp_strip_all_tags(get_the_category_list(', '))); ?></li>
                                    </ul>
                                </div>
                                <h3 class="ltn__blog-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                                <div class="ltn__blog-meta-btn">
                                    <div class="ltn__blog-meta">
                                        <ul><li class="ltn__blog-date"><i class="far fa-calendar-alt"></i><?php echo esc_html(get_the_date()); ?></li></ul>
                                    </div>
                                    <div class="ltn__blog-btn"><a href="<?php the_permalink(); ?>"><?php esc_html_e('Read more', 'fresh'); ?></a></div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; wp_reset_postdata(); ?>
            <?php else : ?>
                <?php for ($i = 1; $i <= 3; $i++) : ?>
                    <div class="col-lg-12">
                        <div class="ltn__blog-item ltn__blog-item-3">
                            <div class="ltn__blog-img"><img src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/blog/' . $i . '.jpg'); ?>" alt=""></div>
                            <div class="ltn__blog-brief">
                                <h3 class="ltn__blog-title"><?php esc_html_e('Add posts to show latest blog content', 'fresh'); ?></h3>
                            </div>
                        </div>
                    </div>
                <?php endfor; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
