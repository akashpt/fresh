<?php
/**
 * Template Name: Contact
 * Template Post Type: page
 */

get_header();

fresh_breadcrumb_banner(__('Contact', 'fresh'));
?>

<main id="primary" class="site-main">
    <div class="ltn__contact-address-area mb-90">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-sm-6">
                    <div class="ltn__contact-address-item ltn__contact-address-item-3">
                        <div class="ltn__contact-address-icon">
                            <i class="icon-placeholder"></i>
                        </div>
                        <h3><?php esc_html_e('Address', 'fresh'); ?></h3>
                        <p><?php esc_html_e('Brooklyn, New York, United States', 'fresh'); ?></p>
                    </div>
                </div>
                <div class="col-lg-4 col-sm-6">
                    <div class="ltn__contact-address-item ltn__contact-address-item-3">
                        <div class="ltn__contact-address-icon">
                            <i class="icon-call"></i>
                        </div>
                        <h3><?php esc_html_e('Phone', 'fresh'); ?></h3>
                        <p><a href="tel:+0123456789"><?php esc_html_e('+0123-456789', 'fresh'); ?></a></p>
                    </div>
                </div>
                <div class="col-lg-4 col-sm-6">
                    <div class="ltn__contact-address-item ltn__contact-address-item-3">
                        <div class="ltn__contact-address-icon">
                            <i class="icon-mail"></i>
                        </div>
                        <h3><?php esc_html_e('Email', 'fresh'); ?></h3>
                        <p><a href="mailto:example@example.com">example@example.com</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="ltn__contact-message-area mb-120">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 offset-lg-2">
                    <div class="contact-form-box box-shadow white-bg">
                        <h4 class="title-2"><?php esc_html_e('Send us a message', 'fresh'); ?></h4>
                        <form action="mailto:example@example.com" method="post" enctype="text/plain">
                            <div class="row">
                                <div class="col-md-6">
                                    <input type="text" name="name" placeholder="<?php esc_attr_e('Name', 'fresh'); ?>">
                                </div>
                                <div class="col-md-6">
                                    <input type="email" name="email" placeholder="<?php esc_attr_e('Email', 'fresh'); ?>">
                                </div>
                                <div class="col-md-12">
                                    <input type="text" name="subject" placeholder="<?php esc_attr_e('Subject', 'fresh'); ?>">
                                </div>
                                <div class="col-md-12">
                                    <textarea name="message" placeholder="<?php esc_attr_e('Message', 'fresh'); ?>"></textarea>
                                </div>
                            </div>
                            <button class="btn theme-btn-1 btn-effect-1" type="submit"><?php esc_html_e('Send Message', 'fresh'); ?></button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
get_footer();
