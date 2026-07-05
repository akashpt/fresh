<div class="ltn__about-us-area pt-120 pb-120 d-none">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 align-self-center">
                <div class="about-us-img-wrap about-img-left">
                    <img src="<?php echo esc_url(fresh_home_option('about_image')); ?>" alt="<?php esc_attr_e('About', 'fresh'); ?>">
                </div>
            </div>
            <div class="col-lg-6 align-self-center">
                <div class="about-us-info-wrap">
                    <div class="section-title-area ltn__section-title-2">
                        <h6 class="section-subtitle ltn__secondary-color"><?php echo esc_html(fresh_home_option('about_subtitle')); ?></h6>
                        <h1 class="section-title"><?php echo esc_html(fresh_home_option('about_title')); ?></h1>
                        <p><?php echo esc_html(fresh_home_option('about_text')); ?></p>
                    </div>
                    <div class="btn-wrapper">
                        <a href="<?php echo esc_url(home_url('/about/')); ?>" class="theme-btn-1 btn btn-effect-1"><?php esc_html_e('Read More', 'fresh'); ?></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
