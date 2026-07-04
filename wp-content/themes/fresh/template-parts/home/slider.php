<div class="ltn__slider-area ltn__slider-3 section-bg-1">
    <div class="ltn__slide-one-active slick-slide-arrow-1 slick-slide-dots-1">
        <?php for ($i = 1; $i <= 2; $i++) : ?>
            <div class="ltn__slide-item ltn__slide-item-2 ltn__slide-item-3 ltn__slide-item-3-normal">
                <div class="ltn__slide-item-inner <?php echo $i === 2 ? 'text-right text-end' : ''; ?>">
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-12 align-self-center">
                                <div class="slide-item-info">
                                    <div class="slide-item-info-inner ltn__slide-animation">
                                        <h6 class="slide-sub-title animated <?php echo $i === 2 ? 'ltn__secondary-color' : ''; ?>">
                                            <?php if ($i === 1) : ?>
                                                <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/icons/icon-img/1.png'); ?>" alt="">
                                            <?php endif; ?>
                                            <?php echo esc_html(fresh_home_option('hero_' . $i . '_subtitle')); ?>
                                        </h6>
                                        <h1 class="slide-title animated"><?php echo wp_kses_post(nl2br(esc_html(fresh_home_option('hero_' . $i . '_title')))); ?></h1>
                                        <div class="slide-brief animated">
                                            <p><?php echo esc_html(fresh_home_option('hero_' . $i . '_text')); ?></p>
                                        </div>
                                        <div class="btn-wrapper animated">
                                            <a href="<?php echo esc_url(fresh_page_url('shop')); ?>" class="theme-btn-1 btn btn-effect-1 text-uppercase"><?php esc_html_e('Explore Products', 'fresh'); ?></a>
                                            <?php if ($i === 2) : ?>
                                                <a href="<?php echo esc_url(home_url('/about/')); ?>" class="btn btn-transparent btn-effect-3"><?php esc_html_e('Learn More', 'fresh'); ?></a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="slide-item-img <?php echo $i === 2 ? 'slide-img-left' : ''; ?>">
                                    <img src="<?php echo esc_url(fresh_home_option('hero_' . $i . '_image')); ?>" alt="">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endfor; ?>
    </div>
</div>
