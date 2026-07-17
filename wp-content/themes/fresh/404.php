<?php
/**
 * 404 template.
 */

get_header();

fresh_breadcrumb_banner(__('Page Not Found', 'fresh'));
?>

<main id="primary" class="site-main">
    <section class="fresh-not-found" aria-labelledby="fresh-error-title">
        <div class="container">
            <div class="fresh-not-found-card">
                <span class="fresh-not-found-code" aria-hidden="true"><?php esc_html_e('404', 'fresh'); ?></span>
                <h1 id="fresh-error-title"><?php esc_html_e('This page is not available', 'fresh'); ?></h1>
                <p><?php esc_html_e('The link may be old, moved, or typed incorrectly. You can continue shopping fresh products from here.', 'fresh'); ?></p>

                <div class="fresh-not-found-actions">
                    <a class="theme-btn-1 btn btn-effect-1" href="<?php echo esc_url(home_url('/')); ?>">
                        <i class="fas fa-home" aria-hidden="true"></i>
                        <?php esc_html_e('Go Home', 'fresh'); ?>
                    </a>
                    <a class="theme-btn-2 btn btn-effect-2" href="<?php echo esc_url(fresh_page_url('shop')); ?>">
                        <i class="fas fa-shopping-basket" aria-hidden="true"></i>
                        <?php esc_html_e('Shop Products', 'fresh'); ?>
                    </a>
                    <a class="fresh-not-found-link" href="<?php echo esc_url(fresh_page_url('contact')); ?>">
                        <?php esc_html_e('Need help?', 'fresh'); ?>
                    </a>
                </div>
            </div>
        </div>
    </section>
</main>

<?php
get_footer();
