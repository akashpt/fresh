<?php
/**
 * Template Name: Contact
 * Template Post Type: page
 */

get_header();

fresh_breadcrumb_banner(__('Contact', 'fresh'));
?>

<main id="primary" class="site-main">
    <section class="fresh-contact-page">
        <div class="container">
            <div class="fresh-contact-intro text-center">
                <span><?php esc_html_e('Get in touch', 'fresh'); ?></span>
                <h1><?php esc_html_e('We are here to help with your order', 'fresh'); ?></h1>
                <p><?php esc_html_e('Have a question about products, delivery, bulk orders, or your cart? Send us a message and our team will respond soon.', 'fresh'); ?></p>
            </div>

            <div class="row">
                <div class="col-lg-4 col-sm-6">
                    <div class="ltn__contact-address-item ltn__contact-address-item-3 fresh-contact-card">
                        <div class="ltn__contact-address-icon fresh-contact-icon">
                            <i class="icon-placeholder"></i>
                        </div>
                        <h3><?php esc_html_e('Address', 'fresh'); ?></h3>
                        <p><?php esc_html_e('Peelamedu, Coimbatore - 641 004, Tamil Nadu, India', 'fresh'); ?></p>
                    </div>
                </div>
                <div class="col-lg-4 col-sm-6">
                    <div class="ltn__contact-address-item ltn__contact-address-item-3 fresh-contact-card">
                        <div class="ltn__contact-address-icon fresh-contact-icon">
                            <i class="icon-call"></i>
                        </div>
                        <h3><?php esc_html_e('Phone', 'fresh'); ?></h3>
                        <p><a href="tel:+917867879000"><?php esc_html_e('+91 78678 79000', 'fresh'); ?></a></p>
                    </div>
                </div>
                <div class="col-lg-4 col-sm-6">
                    <div class="ltn__contact-address-item ltn__contact-address-item-3 fresh-contact-card">
                        <div class="ltn__contact-address-icon fresh-contact-icon">
                            <i class="icon-mail"></i>
                        </div>
                        <h3><?php esc_html_e('Email', 'fresh'); ?></h3>
                        <p><a href="mailto:contact@pureauranaturals.in">contact@pureauranaturals.in</a></p>
                    </div>
                </div>
            </div>

            <div class="fresh-contact-panel">
                <div class="fresh-contact-help">
                    <span><?php esc_html_e('Order support', 'fresh'); ?></span>
                    <h2><?php esc_html_e('Need help choosing products?', 'fresh'); ?></h2>
                    <p><?php esc_html_e('Tell us what you are looking for and we will help you find the right fresh products, quantity, and delivery option.', 'fresh'); ?></p>
                    <ul>
                        <li><i class="fas fa-check"></i><?php esc_html_e('Product and availability questions', 'fresh'); ?></li>
                        <li><i class="fas fa-check"></i><?php esc_html_e('Delivery and order support', 'fresh'); ?></li>
                        <li><i class="fas fa-check"></i><?php esc_html_e('Bulk or regular purchase enquiries', 'fresh'); ?></li>
                    </ul>
                    <a href="<?php echo esc_url(fresh_page_url('shop')); ?>" class="fresh-contact-shop-link">
                        <?php esc_html_e('Browse Products', 'fresh'); ?>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>

                <div class="contact-form-box fresh-contact-form">
                    <h4 class="title-2"><?php esc_html_e('Send us a message', 'fresh'); ?></h4>
                    <form action="mailto:contact@pureauranaturals.in" method="post" enctype="text/plain">
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
    </section>
</main>

<?php
get_footer();
