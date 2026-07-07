<?php
$steps = [
    [
        'icon'  => 'fas fa-search',
        'title' => __('Browse products', 'fresh'),
        'text'  => __('Find fresh essentials by category or featured picks.', 'fresh'),
    ],
    [
        'icon'  => 'fas fa-cart-plus',
        'title' => __('Add quantity', 'fresh'),
        'text'  => __('Choose how many you need and add them to cart.', 'fresh'),
    ],
    [
        'icon'  => 'fas fa-credit-card',
        'title' => __('Checkout easily', 'fresh'),
        'text'  => __('Review your cart and place the order in minutes.', 'fresh'),
    ],
];
?>

<section class="fresh-order-flow" aria-label="<?php esc_attr_e('How to order', 'fresh'); ?>">
    <div class="container">
        <div class="fresh-order-flow-inner">
            <div class="fresh-order-flow-heading">
                <span><?php esc_html_e('Easy ordering', 'fresh'); ?></span>
                <h2><?php esc_html_e('Order purchase in 3 simple steps', 'fresh'); ?></h2>
            </div>
            <div class="fresh-order-steps">
                <?php foreach ($steps as $index => $step) : ?>
                    <div class="fresh-order-step">
                        <div class="fresh-order-step-icon">
                            <i class="<?php echo esc_attr($step['icon']); ?>"></i>
                        </div>
                        <div>
                            <strong><?php echo esc_html(sprintf('%02d. %s', $index + 1, $step['title'])); ?></strong>
                            <p><?php echo esc_html($step['text']); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <a class="fresh-order-flow-cart" href="<?php echo esc_url(fresh_page_url('cart')); ?>">
                <i class="fas fa-shopping-cart"></i>
                <?php esc_html_e('View Cart', 'fresh'); ?>
            </a>
        </div>
    </div>
</section>
