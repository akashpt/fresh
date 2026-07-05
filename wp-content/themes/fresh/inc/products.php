<?php
/**
 * Lightweight product, cart, and checkout support.
 */

function fresh_register_product_post_types()
{
    register_post_type('fresh_product', [
        'labels' => [
            'name'          => __('Products', 'fresh'),
            'singular_name' => __('Product', 'fresh'),
            'add_new_item'  => __('Add New Product', 'fresh'),
            'edit_item'     => __('Edit Product', 'fresh'),
        ],
        'public'       => true,
        'has_archive'  => true,
        'menu_icon'    => 'dashicons-products',
        'rewrite'      => ['slug' => 'products'],
        'show_in_rest' => true,
        'supports'     => ['title', 'editor', 'excerpt', 'thumbnail'],
    ]);

    register_post_type('fresh_order', [
        'labels' => [
            'name'          => __('Orders', 'fresh'),
            'singular_name' => __('Order', 'fresh'),
        ],
        'public'      => false,
        'show_ui'     => true,
        'menu_icon'   => 'dashicons-clipboard',
        'supports'    => ['title'],
    ]);

    register_taxonomy('fresh_product_category', ['fresh_product'], [
        'labels' => [
            'name'          => __('Product Categories', 'fresh'),
            'singular_name' => __('Product Category', 'fresh'),
            'add_new_item'  => __('Add New Product Category', 'fresh'),
            'edit_item'     => __('Edit Product Category', 'fresh'),
        ],
        'hierarchical'      => true,
        'public'            => true,
        'show_admin_column' => true,
        'show_in_rest'      => true,
        'rewrite'           => ['slug' => 'product-category'],
    ]);
}
add_action('init', 'fresh_register_product_post_types');

function fresh_register_order_statuses()
{
    register_post_status('fresh_completed', [
        'label'                     => _x('Completed', 'order status', 'fresh'),
        'public'                    => false,
        'internal'                  => false,
        'protected'                 => true,
        'exclude_from_search'       => true,
        'show_in_admin_all_list'    => false,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop(
            'Completed <span class="count">(%s)</span>',
            'Completed <span class="count">(%s)</span>',
            'fresh'
        ),
    ]);
}
add_action('init', 'fresh_register_order_statuses');

function fresh_completed_orders_menu()
{
    add_submenu_page(
        'edit.php?post_type=fresh_order',
        __('Completed Orders', 'fresh'),
        __('Completed Orders', 'fresh'),
        'edit_posts',
        'edit.php?post_type=fresh_order&post_status=fresh_completed'
    );
}
add_action('admin_menu', 'fresh_completed_orders_menu');

function fresh_coupon_settings_menu()
{
    add_submenu_page(
        'edit.php?post_type=fresh_order',
        __('Coupon Settings', 'fresh'),
        __('Coupon Settings', 'fresh'),
        'manage_options',
        'fresh-coupon-settings',
        'fresh_render_coupon_settings_page'
    );
}
add_action('admin_menu', 'fresh_coupon_settings_menu');

function fresh_filter_order_admin_list($query)
{
    global $pagenow;

    if (! is_admin() || $pagenow !== 'edit.php' || ! $query->is_main_query()) {
        return;
    }

    if ($query->get('post_type') !== 'fresh_order') {
        return;
    }

    $status = $query->get('post_status');

    if (is_array($status)) {
        if (count($status) === 1 && in_array('fresh_completed', $status, true)) {
            return;
        }

        $query->set('post_status', 'publish');
        return;
    }

    if ($status === 'fresh_completed') {
        return;
    }

    if (empty($status) || $status === 'all') {
        $query->set('post_status', 'publish');
    }
}
add_action('pre_get_posts', 'fresh_filter_order_admin_list');

function fresh_register_whatsapp_settings()
{
    register_setting('fresh_whatsapp_settings', 'fresh_whatsapp_number', [
        'type'              => 'string',
        'sanitize_callback' => 'fresh_sanitize_whatsapp_number',
        'default'           => '',
    ]);
}
add_action('admin_init', 'fresh_register_whatsapp_settings');

function fresh_whatsapp_settings_page()
{
    add_options_page(
        __('Fresh WhatsApp', 'fresh'),
        __('Fresh WhatsApp', 'fresh'),
        'manage_options',
        'fresh-whatsapp',
        'fresh_render_whatsapp_settings_page'
    );
}
add_action('admin_menu', 'fresh_whatsapp_settings_page');

function fresh_sanitize_whatsapp_number($number)
{
    return preg_replace('/\D+/', '', (string) $number);
}

function fresh_render_whatsapp_settings_page()
{
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('Fresh WhatsApp Settings', 'fresh'); ?></h1>
        <form method="post" action="options.php">
            <?php settings_fields('fresh_whatsapp_settings'); ?>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row">
                        <label for="fresh_whatsapp_number"><?php esc_html_e('WhatsApp Number', 'fresh'); ?></label>
                    </th>
                    <td>
                        <input type="text" id="fresh_whatsapp_number" name="fresh_whatsapp_number" value="<?php echo esc_attr(get_option('fresh_whatsapp_number', '')); ?>" class="regular-text" placeholder="919876543210">
                        <p class="description"><?php esc_html_e('Use country code without +, spaces, or hyphens. Example: 919876543210', 'fresh'); ?></p>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

function fresh_whatsapp_number()
{
    return fresh_sanitize_whatsapp_number(get_option('fresh_whatsapp_number', ''));
}

function fresh_product_meta_box()
{
    add_meta_box(
        'fresh_product_details',
        __('Product Details', 'fresh'),
        'fresh_render_product_meta_box',
        'fresh_product',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'fresh_product_meta_box');

function fresh_render_product_meta_box($post)
{
    wp_nonce_field('fresh_save_product_meta', 'fresh_product_meta_nonce');

    $price      = get_post_meta($post->ID, '_fresh_product_price', true);
    $sale_price = get_post_meta($post->ID, '_fresh_product_sale_price', true);
    $sku        = get_post_meta($post->ID, '_fresh_product_sku', true);
    $unit       = get_post_meta($post->ID, '_fresh_product_unit', true);
    ?>
    <p>
        <label for="fresh_product_price"><strong><?php esc_html_e('Price', 'fresh'); ?></strong></label><br>
        <input type="number" step="0.01" min="0" id="fresh_product_price" name="fresh_product_price" value="<?php echo esc_attr($price); ?>" style="width: 100%;">
    </p>
    <p>
        <label for="fresh_product_sale_price"><strong><?php esc_html_e('Sale Price', 'fresh'); ?></strong></label><br>
        <input type="number" step="0.01" min="0" id="fresh_product_sale_price" name="fresh_product_sale_price" value="<?php echo esc_attr($sale_price); ?>" style="width: 100%;">
    </p>
    <p>
        <label for="fresh_product_sku"><strong><?php esc_html_e('SKU', 'fresh'); ?></strong></label><br>
        <input type="text" id="fresh_product_sku" name="fresh_product_sku" value="<?php echo esc_attr($sku); ?>" style="width: 100%;">
    </p>
    <p>
        <label for="fresh_product_unit"><strong><?php esc_html_e('Unit', 'fresh'); ?></strong></label><br>
        <input type="text" id="fresh_product_unit" name="fresh_product_unit" value="<?php echo esc_attr($unit); ?>" placeholder="kg, box, piece" style="width: 100%;">
    </p>
    <?php
}

function fresh_save_product_meta($post_id)
{
    if (
        ! isset($_POST['fresh_product_meta_nonce']) ||
        ! wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['fresh_product_meta_nonce'])), 'fresh_save_product_meta')
    ) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (! current_user_can('edit_post', $post_id)) {
        return;
    }

    $fields = [
        '_fresh_product_price'      => 'fresh_product_price',
        '_fresh_product_sale_price' => 'fresh_product_sale_price',
        '_fresh_product_sku'        => 'fresh_product_sku',
        '_fresh_product_unit'       => 'fresh_product_unit',
    ];

    foreach ($fields as $meta_key => $field_name) {
        $value = isset($_POST[$field_name]) ? sanitize_text_field(wp_unslash($_POST[$field_name])) : '';
        update_post_meta($post_id, $meta_key, $value);
    }
}
add_action('save_post_fresh_product', 'fresh_save_product_meta');

function fresh_order_meta_boxes()
{
    add_meta_box(
        'fresh_order_details',
        __('Order Details', 'fresh'),
        'fresh_render_order_details_meta_box',
        'fresh_order',
        'normal',
        'high'
    );

    add_meta_box(
        'fresh_order_customer',
        __('Customer Details', 'fresh'),
        'fresh_render_order_customer_meta_box',
        'fresh_order',
        'side',
        'high'
    );
}
add_action('add_meta_boxes_fresh_order', 'fresh_order_meta_boxes');

function fresh_order_customer($order_id)
{
    $customer = get_post_meta($order_id, '_fresh_order_customer', true);

    return is_array($customer) ? $customer : [];
}

function fresh_order_items($order_id)
{
    $items = get_post_meta($order_id, '_fresh_order_items', true);

    return is_array($items) ? $items : [];
}

function fresh_order_item_product_id($item)
{
    if (! is_array($item) || empty($item['product'])) {
        return 0;
    }

    if ($item['product'] instanceof WP_Post) {
        return absint($item['product']->ID);
    }

    if (is_numeric($item['product'])) {
        return absint($item['product']);
    }

    return 0;
}

function fresh_render_order_customer_meta_box($post)
{
    $customer = fresh_order_customer($post->ID);
    $is_completed = $post->post_status === 'fresh_completed';
    $fields = [
        __('Name', 'fresh')    => $customer['name'] ?? '',
        __('Email', 'fresh')   => $customer['email'] ?? '',
        __('Phone', 'fresh')   => $customer['phone'] ?? '',
        __('Address', 'fresh') => $customer['address'] ?? '',
    ];
    ?>
    <div class="fresh-order-customer">
        <?php foreach ($fields as $label => $value) : ?>
            <p>
                <strong><?php echo esc_html($label); ?></strong><br>
                <?php echo nl2br(esc_html($value)); ?>
            </p>
        <?php endforeach; ?>

        <?php $whatsapp_url = fresh_order_whatsapp_url($post->ID); ?>
        <?php if ($whatsapp_url) : ?>
            <p>
                <a class="button button-primary" href="<?php echo esc_url($whatsapp_url); ?>" target="_blank" rel="noopener">
                    <?php esc_html_e('Send on WhatsApp', 'fresh'); ?>
                </a>
            </p>
        <?php endif; ?>

        <hr>

        <?php if ($is_completed) : ?>
            <p><strong><?php esc_html_e('Order Status', 'fresh'); ?></strong><br><?php esc_html_e('Completed', 'fresh'); ?></p>
        <?php else : ?>
            <?php
            $complete_url = wp_nonce_url(
                add_query_arg(
                    [
                        'action'   => 'fresh_complete_order',
                        'order_id' => $post->ID,
                    ],
                    admin_url('admin-post.php')
                ),
                'fresh_complete_order_' . $post->ID
            );
            ?>
            <p>
                <a class="button button-primary" href="<?php echo esc_url($complete_url); ?>">
                    <?php esc_html_e('Mark as Completed', 'fresh'); ?>
                </a>
            </p>
        <?php endif; ?>
    </div>
    <?php
}

function fresh_render_order_details_meta_box($post)
{
    $items    = fresh_order_items($post->ID);
    $subtotal = get_post_meta($post->ID, '_fresh_order_subtotal', true);
    $discount = get_post_meta($post->ID, '_fresh_order_discount', true);
    $coupon   = get_post_meta($post->ID, '_fresh_order_coupon', true);
    $total    = get_post_meta($post->ID, '_fresh_order_total', true);

    if ($subtotal === '') {
        $subtotal = array_sum(wp_list_pluck($items, 'subtotal'));
    }
    ?>
    <table class="widefat striped">
        <thead>
            <tr>
                <th><?php esc_html_e('Product', 'fresh'); ?></th>
                <th><?php esc_html_e('SKU', 'fresh'); ?></th>
                <th><?php esc_html_e('Unit', 'fresh'); ?></th>
                <th><?php esc_html_e('Price', 'fresh'); ?></th>
                <th><?php esc_html_e('Quantity', 'fresh'); ?></th>
                <th><?php esc_html_e('Subtotal', 'fresh'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if ($items) : ?>
                <?php foreach ($items as $item) : ?>
                    <?php
                    $product_id = fresh_order_item_product_id($item);
                    $product    = $product_id ? get_post($product_id) : null;
                    $stored_product = is_array($item) && ! empty($item['product']) && $item['product'] instanceof WP_Post ? $item['product'] : null;
                    $title      = $product ? get_the_title($product) : ($stored_product ? $stored_product->post_title : __('Product unavailable', 'fresh'));
                    $edit_link  = $product ? get_edit_post_link($product_id) : '';
                    $sku        = $product_id ? get_post_meta($product_id, '_fresh_product_sku', true) : '';
                    $unit       = $product_id ? get_post_meta($product_id, '_fresh_product_unit', true) : '';
                    $price      = isset($item['price']) ? (float) $item['price'] : 0;
                    $quantity   = isset($item['quantity']) ? absint($item['quantity']) : 0;
                    $subtotal   = isset($item['subtotal']) ? (float) $item['subtotal'] : $price * $quantity;
                    ?>
                    <tr>
                        <td>
                            <?php if ($edit_link) : ?>
                                <a href="<?php echo esc_url($edit_link); ?>"><?php echo esc_html($title); ?></a>
                            <?php else : ?>
                                <?php echo esc_html($title); ?>
                            <?php endif; ?>
                        </td>
                        <td><?php echo esc_html($sku ?: '-'); ?></td>
                        <td><?php echo esc_html($unit ?: '-'); ?></td>
                        <td><?php echo esc_html(fresh_format_price($price)); ?></td>
                        <td><?php echo esc_html($quantity); ?></td>
                        <td><?php echo esc_html(fresh_format_price($subtotal)); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="6"><?php esc_html_e('No order items found.', 'fresh'); ?></td>
                </tr>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="5" style="text-align: right;"><?php esc_html_e('Subtotal', 'fresh'); ?></th>
                <th><?php echo esc_html(fresh_format_price($subtotal)); ?></th>
            </tr>
            <?php if ((float) $discount > 0) : ?>
                <tr>
                    <th colspan="5" style="text-align: right;">
                        <?php esc_html_e('Coupon Discount', 'fresh'); ?>
                        <?php if ($coupon) : ?>
                            (<?php echo esc_html($coupon); ?>)
                        <?php endif; ?>
                    </th>
                    <th>-<?php echo esc_html(fresh_format_price($discount)); ?></th>
                </tr>
            <?php endif; ?>
            <tr>
                <th colspan="5" style="text-align: right;"><?php esc_html_e('Order Total', 'fresh'); ?></th>
                <th><?php echo esc_html(fresh_format_price($total)); ?></th>
            </tr>
        </tfoot>
    </table>
    <?php
}

function fresh_order_admin_columns($columns)
{
    return [
        'cb'             => $columns['cb'],
        'title'          => __('Order', 'fresh'),
        'fresh_customer' => __('Customer', 'fresh'),
        'fresh_phone'    => __('Phone', 'fresh'),
        'fresh_total'    => __('Total', 'fresh'),
        'fresh_status'   => __('Status', 'fresh'),
        'date'           => $columns['date'],
    ];
}
add_filter('manage_fresh_order_posts_columns', 'fresh_order_admin_columns');

function fresh_order_admin_column_content($column, $post_id)
{
    $customer = fresh_order_customer($post_id);

    if ($column === 'fresh_customer') {
        echo esc_html($customer['name'] ?? '-');
        if (! empty($customer['email'])) {
            echo '<br><a href="mailto:' . esc_attr($customer['email']) . '">' . esc_html($customer['email']) . '</a>';
        }
    }

    if ($column === 'fresh_phone') {
        echo esc_html($customer['phone'] ?? '-');
    }

    if ($column === 'fresh_total') {
        echo esc_html(fresh_format_price(get_post_meta($post_id, '_fresh_order_total', true)));
    }

    if ($column === 'fresh_status') {
        $status = get_post_status($post_id);
        echo esc_html($status === 'fresh_completed' ? __('Completed', 'fresh') : __('New', 'fresh'));
    }
}
add_action('manage_fresh_order_posts_custom_column', 'fresh_order_admin_column_content', 10, 2);

function fresh_complete_order()
{
    $order_id = isset($_REQUEST['order_id']) ? absint($_REQUEST['order_id']) : 0;

    if (! $order_id || get_post_type($order_id) !== 'fresh_order') {
        wp_die(esc_html__('Invalid order.', 'fresh'));
    }

    check_admin_referer('fresh_complete_order_' . $order_id);

    if (! current_user_can('edit_post', $order_id)) {
        wp_die(esc_html__('You are not allowed to update this order.', 'fresh'));
    }

    wp_update_post([
        'ID'          => $order_id,
        'post_status' => 'fresh_completed',
    ]);

    wp_safe_redirect(admin_url('edit.php?post_type=fresh_order&post_status=fresh_completed&fresh_order_completed=1'));
    exit;
}
add_action('admin_post_fresh_complete_order', 'fresh_complete_order');

function fresh_completed_order_notice()
{
    if (empty($_GET['fresh_order_completed'])) {
        return;
    }

    ?>
    <div class="notice notice-success is-dismissible">
        <p><?php esc_html_e('Order moved to Completed Orders.', 'fresh'); ?></p>
    </div>
    <?php
}
add_action('admin_notices', 'fresh_completed_order_notice');

function fresh_register_coupon_settings()
{
    register_setting('fresh_coupon_settings', 'fresh_coupons', [
        'type'              => 'array',
        'sanitize_callback' => 'fresh_sanitize_coupons',
        'default'           => [],
    ]);
}
add_action('admin_init', 'fresh_register_coupon_settings');

function fresh_sanitize_checkbox($value)
{
    return empty($value) ? 0 : 1;
}

function fresh_sanitize_coupon_code($code)
{
    return strtoupper(preg_replace('/[^A-Z0-9_-]+/i', '', (string) $code));
}

function fresh_sanitize_coupon_type($type)
{
    return in_array($type, ['percent', 'fixed'], true) ? $type : 'percent';
}

function fresh_sanitize_coupon_amount($amount)
{
    return max(0, (float) $amount);
}

function fresh_sanitize_coupons($coupons)
{
    if (! is_array($coupons)) {
        return [];
    }

    $clean = [];

    foreach ($coupons as $coupon) {
        if (! is_array($coupon)) {
            continue;
        }

        if (! empty($coupon['delete'])) {
            continue;
        }

        $code   = fresh_sanitize_coupon_code($coupon['code'] ?? '');
        $amount = fresh_sanitize_coupon_amount($coupon['amount'] ?? 0);

        if ($code === '' || $amount <= 0) {
            continue;
        }

        $clean[$code] = [
            'enabled' => fresh_sanitize_checkbox($coupon['enabled'] ?? 0),
            'code'    => $code,
            'type'    => fresh_sanitize_coupon_type($coupon['type'] ?? 'percent'),
            'amount'  => $amount,
        ];
    }

    return array_values($clean);
}

function fresh_coupons()
{
    $coupons = get_option('fresh_coupons', false);

    if ($coupons !== false) {
        return fresh_sanitize_coupons($coupons);
    }

    $legacy_code = fresh_sanitize_coupon_code(get_option('fresh_coupon_code', ''));
    $legacy_amount = fresh_sanitize_coupon_amount(get_option('fresh_coupon_amount', 0));

    if ($legacy_code === '' || $legacy_amount <= 0) {
        return [];
    }

    return [[
        'enabled' => fresh_sanitize_checkbox(get_option('fresh_coupon_enabled', 0)),
        'code'    => $legacy_code,
        'type'    => fresh_sanitize_coupon_type(get_option('fresh_coupon_type', 'percent')),
        'amount'  => $legacy_amount,
    ]];
}

function fresh_render_coupon_settings_page()
{
    $coupons = fresh_coupons();
    $rows    = array_merge($coupons, array_fill(0, 2, [
        'enabled' => 1,
        'code'    => '',
        'type'    => 'percent',
        'amount'  => '',
    ]));
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('Coupon Settings', 'fresh'); ?></h1>
        <form method="post" action="options.php">
            <?php settings_fields('fresh_coupon_settings'); ?>
            <table class="widefat striped" id="fresh-coupons-table">
                <thead>
                    <tr>
                        <th><?php esc_html_e('Active', 'fresh'); ?></th>
                        <th><?php esc_html_e('Coupon Code', 'fresh'); ?></th>
                        <th><?php esc_html_e('Discount Type', 'fresh'); ?></th>
                        <th><?php esc_html_e('Amount', 'fresh'); ?></th>
                        <th><?php esc_html_e('Delete', 'fresh'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rows as $index => $coupon) : ?>
                        <?php $type = fresh_sanitize_coupon_type($coupon['type'] ?? 'percent'); ?>
                        <tr>
                            <td>
                                <input type="hidden" name="fresh_coupons[<?php echo esc_attr($index); ?>][enabled]" value="0">
                                <input type="checkbox" name="fresh_coupons[<?php echo esc_attr($index); ?>][enabled]" value="1" <?php checked(! empty($coupon['enabled'])); ?>>
                            </td>
                            <td>
                                <input type="text" name="fresh_coupons[<?php echo esc_attr($index); ?>][code]" value="<?php echo esc_attr($coupon['code'] ?? ''); ?>" class="regular-text" placeholder="SAVE10">
                            </td>
                            <td>
                                <select name="fresh_coupons[<?php echo esc_attr($index); ?>][type]">
                                    <option value="percent" <?php selected($type, 'percent'); ?>><?php esc_html_e('Percentage', 'fresh'); ?></option>
                                    <option value="fixed" <?php selected($type, 'fixed'); ?>><?php esc_html_e('Fixed Amount', 'fresh'); ?></option>
                                </select>
                            </td>
                            <td>
                                <input type="number" step="0.01" min="0" name="fresh_coupons[<?php echo esc_attr($index); ?>][amount]" value="<?php echo esc_attr($coupon['amount'] ?? ''); ?>" class="small-text">
                            </td>
                            <td>
                                <?php if (! empty($coupon['code'])) : ?>
                                    <label>
                                        <input type="hidden" name="fresh_coupons[<?php echo esc_attr($index); ?>][delete]" value="0">
                                        <input type="checkbox" name="fresh_coupons[<?php echo esc_attr($index); ?>][delete]" value="1">
                                        <?php esc_html_e('Delete', 'fresh'); ?>
                                    </label>
                                <?php else : ?>
                                    <button type="button" class="button fresh-remove-coupon-row"><?php esc_html_e('Remove', 'fresh'); ?></button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <p class="description"><?php esc_html_e('Tick Delete and save changes to remove an existing coupon. Leave the code blank to ignore a row.', 'fresh'); ?></p>
            <p>
                <button type="button" class="button" id="fresh-add-coupon-row"><?php esc_html_e('Add Coupon Row', 'fresh'); ?></button>
            </p>
            <?php submit_button(); ?>
        </form>
    </div>
    <script>
        (function () {
            var button = document.getElementById('fresh-add-coupon-row');
            var table = document.getElementById('fresh-coupons-table');
            if (!button || !table) {
                return;
            }

            button.addEventListener('click', function () {
                var tbody = table.querySelector('tbody');
                var index = tbody.querySelectorAll('tr').length;
                var row = document.createElement('tr');
                row.innerHTML =
                    '<td><input type="hidden" name="fresh_coupons[' + index + '][enabled]" value="0"><input type="checkbox" name="fresh_coupons[' + index + '][enabled]" value="1" checked></td>' +
                    '<td><input type="text" name="fresh_coupons[' + index + '][code]" class="regular-text" placeholder="SAVE10"></td>' +
                    '<td><select name="fresh_coupons[' + index + '][type]"><option value="percent"><?php echo esc_js(__('Percentage', 'fresh')); ?></option><option value="fixed"><?php echo esc_js(__('Fixed Amount', 'fresh')); ?></option></select></td>' +
                    '<td><input type="number" step="0.01" min="0" name="fresh_coupons[' + index + '][amount]" class="small-text"></td>' +
                    '<td><button type="button" class="button fresh-remove-coupon-row"><?php echo esc_js(__('Remove', 'fresh')); ?></button></td>';
                tbody.appendChild(row);
            });

            table.addEventListener('click', function (event) {
                if (!event.target.classList.contains('fresh-remove-coupon-row')) {
                    return;
                }

                event.target.closest('tr').remove();
            });
        }());
    </script>
    <?php
}

function fresh_product_price($product_id)
{
    $sale_price = get_post_meta($product_id, '_fresh_product_sale_price', true);
    $price      = get_post_meta($product_id, '_fresh_product_price', true);

    return $sale_price !== '' ? (float) $sale_price : (float) $price;
}

function fresh_format_price($price)
{
    return '$' . number_format((float) $price, 2);
}

function fresh_product_detail_url($product_id)
{
    return add_query_arg('product', absint($product_id), fresh_page_url('product-details'));
}

function fresh_product_image_url($product_id)
{
    if (has_post_thumbnail($product_id)) {
        return get_the_post_thumbnail_url($product_id, 'medium');
    }

    $fallbacks = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
    $image_id = $fallbacks[absint($product_id) % count($fallbacks)];

    return get_template_directory_uri() . '/assets/img/product/' . $image_id . '.png';
}

function fresh_page_url($slug)
{
    $page = get_page_by_path($slug);

    return $page ? get_permalink($page) : home_url('/' . trim($slug, '/') . '/');
}

function fresh_cart_cookie_name()
{
    return 'fresh_cart';
}

function fresh_coupon_cookie_name()
{
    return 'fresh_coupon';
}

function fresh_set_storefront_cookie($name, $value, $expires)
{
    $path   = fresh_storefront_cookie_path();
    $domain = COOKIE_DOMAIN ?: '';

    if (PHP_VERSION_ID >= 70300) {
        $options = [
            'expires'  => $expires,
            'path'     => $path,
            'secure'   => is_ssl(),
            'httponly' => false,
            'samesite' => 'Lax',
        ];

        if ($domain !== '') {
            $options['domain'] = $domain;
        }

        setcookie($name, $value, $options);
        return;
    }

    setcookie($name, $value, $expires, $path, $domain);
}

function fresh_storefront_cookie_path()
{
    $script_name = isset($_SERVER['SCRIPT_NAME']) ? wp_unslash($_SERVER['SCRIPT_NAME']) : '';
    $script_name = str_replace('\\', '/', $script_name);

    if (strpos($script_name, '/wp-admin/') !== false) {
        $path = strtok($script_name, '?');
        $path = substr($path, 0, strpos($path, '/wp-admin/') + 1);

        return $path ?: '/';
    }

    $path = wp_parse_url(home_url('/'), PHP_URL_PATH);

    return $path ?: '/';
}

function fresh_get_cart()
{
    if (empty($_COOKIE[fresh_cart_cookie_name()])) {
        return [];
    }

    $cart = json_decode(stripslashes($_COOKIE[fresh_cart_cookie_name()]), true);
    if (! is_array($cart)) {
        return [];
    }

    return fresh_sanitize_cart($cart);
}

function fresh_set_cart($cart)
{
    $cart = fresh_sanitize_cart($cart);
    fresh_set_storefront_cookie(fresh_cart_cookie_name(), wp_json_encode($cart), time() + MONTH_IN_SECONDS);
    $_COOKIE[fresh_cart_cookie_name()] = wp_json_encode($cart);
}

function fresh_sanitize_cart($cart)
{
    $clean = [];

    foreach ((array) $cart as $product_id => $quantity) {
        $product_id = absint($product_id);
        $quantity   = absint($quantity);

        if ($product_id > 0 && $quantity > 0) {
            $clean[$product_id] = $quantity;
        }
    }

    return $clean;
}

function fresh_get_applied_coupon_code()
{
    if (empty($_COOKIE[fresh_coupon_cookie_name()])) {
        return '';
    }

    return fresh_sanitize_coupon_code(wp_unslash($_COOKIE[fresh_coupon_cookie_name()]));
}

function fresh_set_applied_coupon_code($code)
{
    $code = fresh_sanitize_coupon_code($code);

    if ($code === '') {
        fresh_set_storefront_cookie(fresh_coupon_cookie_name(), '', time() - HOUR_IN_SECONDS);
        unset($_COOKIE[fresh_coupon_cookie_name()]);
        return;
    }

    fresh_set_storefront_cookie(fresh_coupon_cookie_name(), $code, time() + MONTH_IN_SECONDS);
    $_COOKIE[fresh_coupon_cookie_name()] = $code;
}

function fresh_find_coupon($code)
{
    $code = fresh_sanitize_coupon_code($code);

    if ($code === '') {
        return null;
    }

    foreach (fresh_coupons() as $coupon) {
        if (empty($coupon['enabled']) || $coupon['code'] !== $code) {
            continue;
        }

        return $coupon;
    }

    return null;
}

function fresh_validate_coupon_code($code)
{
    $code   = fresh_sanitize_coupon_code($code);
    $coupon = fresh_find_coupon($code);

    if (! $coupon) {
        return new WP_Error('invalid_coupon', __('Please enter a valid coupon code.', 'fresh'));
    }

    if (! fresh_cart_items()) {
        return new WP_Error('empty_cart', __('Your cart is empty.', 'fresh'));
    }

    return $coupon;
}

function fresh_cart_items()
{
    $items = [];

    foreach (fresh_get_cart() as $product_id => $quantity) {
        $product = get_post((int) $product_id);
        if (! $product || $product->post_type !== 'fresh_product' || $product->post_status !== 'publish') {
            continue;
        }

        $price   = fresh_product_price($product->ID);
        $items[] = [
            'product'  => $product,
            'quantity' => max(1, (int) $quantity),
            'price'    => $price,
            'subtotal' => $price * max(1, (int) $quantity),
        ];
    }

    return $items;
}

function fresh_cart_subtotal()
{
    return array_sum(wp_list_pluck(fresh_cart_items(), 'subtotal'));
}

function fresh_cart_discount()
{
    $coupon = fresh_find_coupon(fresh_get_applied_coupon_code());

    if (! $coupon) {
        return 0;
    }

    $subtotal = fresh_cart_subtotal();
    if ($subtotal <= 0) {
        return 0;
    }

    $discount = $coupon['type'] === 'percent' ? $subtotal * min(100, $coupon['amount']) / 100 : $coupon['amount'];

    return min($subtotal, max(0, $discount));
}

function fresh_cart_total()
{
    return max(0, fresh_cart_subtotal() - fresh_cart_discount());
}

function fresh_cart_count()
{
    return array_sum(fresh_get_cart());
}

function fresh_wishlist_cookie_name()
{
    return 'fresh_wishlist';
}

function fresh_get_wishlist()
{
    if (empty($_COOKIE[fresh_wishlist_cookie_name()])) {
        return [];
    }

    $wishlist = json_decode(stripslashes($_COOKIE[fresh_wishlist_cookie_name()]), true);
    if (! is_array($wishlist)) {
        return [];
    }

    return array_values(array_unique(array_filter(array_map('absint', $wishlist))));
}

function fresh_set_wishlist($wishlist)
{
    $wishlist = array_values(array_unique(array_filter(array_map('absint', $wishlist))));
    fresh_set_storefront_cookie(fresh_wishlist_cookie_name(), wp_json_encode($wishlist), time() + MONTH_IN_SECONDS);
    $_COOKIE[fresh_wishlist_cookie_name()] = wp_json_encode($wishlist);
}

function fresh_wishlist_items()
{
    $items = [];

    foreach (fresh_get_wishlist() as $product_id) {
        $product = get_post((int) $product_id);
        if (! $product || $product->post_type !== 'fresh_product' || $product->post_status !== 'publish') {
            continue;
        }

        $items[] = $product;
    }

    return $items;
}

function fresh_wishlist_count()
{
    return count(fresh_wishlist_items());
}

function fresh_current_url()
{
    $scheme = is_ssl() ? 'https://' : 'http://';
    $host   = isset($_SERVER['HTTP_HOST']) ? sanitize_text_field(wp_unslash($_SERVER['HTTP_HOST'])) : wp_parse_url(home_url(), PHP_URL_HOST);
    $uri    = isset($_SERVER['REQUEST_URI']) ? sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI'])) : '/';

    return esc_url_raw($scheme . $host . $uri);
}

function fresh_handle_cart_actions()
{
    if (isset($_GET['fresh_add_to_cart'])) {
        $product_id = absint($_GET['fresh_add_to_cart']);
        if (! isset($_GET['_wpnonce']) || ! wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['_wpnonce'])), 'fresh_add_to_cart_' . $product_id)) {
            return;
        }

        $quantity = isset($_GET['quantity']) ? max(1, absint($_GET['quantity'])) : 1;
        $cart = fresh_get_cart();
        $cart[$product_id] = isset($cart[$product_id]) ? $cart[$product_id] + $quantity : $quantity;
        fresh_set_cart($cart);

        $redirect_url = isset($_GET['redirect_to']) ? esc_url_raw(wp_unslash($_GET['redirect_to'])) : '';
        $redirect_url = $redirect_url ? wp_validate_redirect($redirect_url, fresh_page_url('shop')) : (wp_get_referer() ?: fresh_page_url('shop'));
        $redirect_url = remove_query_arg(['fresh_add_to_cart', 'quantity', '_wpnonce', 'fresh_added'], $redirect_url);
        $redirect_url = add_query_arg([
            'fresh_added' => $product_id,
        ], $redirect_url);

        wp_safe_redirect($redirect_url);
        exit;
    }

    if (isset($_GET['fresh_remove_from_cart'])) {
        $product_id = absint($_GET['fresh_remove_from_cart']);
        if (! isset($_GET['_wpnonce']) || ! wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['_wpnonce'])), 'fresh_remove_from_cart_' . $product_id)) {
            return;
        }

        $cart = fresh_get_cart();
        unset($cart[$product_id]);
        fresh_set_cart($cart);

        if (! $cart) {
            fresh_set_applied_coupon_code('');
        }

        wp_safe_redirect(fresh_page_url('cart'));
        exit;
    }

    if (isset($_GET['fresh_add_to_wishlist'])) {
        $product_id = absint($_GET['fresh_add_to_wishlist']);
        if (! isset($_GET['_wpnonce']) || ! wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['_wpnonce'])), 'fresh_add_to_wishlist_' . $product_id)) {
            return;
        }

        $wishlist = fresh_get_wishlist();
        if (! in_array($product_id, $wishlist, true)) {
            $wishlist[] = $product_id;
            fresh_set_wishlist($wishlist);
        }

        $redirect_url = isset($_GET['redirect_to']) ? esc_url_raw(wp_unslash($_GET['redirect_to'])) : '';
        $redirect_url = $redirect_url ? wp_validate_redirect($redirect_url, fresh_page_url('shop')) : (wp_get_referer() ?: fresh_page_url('shop'));
        $redirect_url = remove_query_arg(['fresh_add_to_wishlist', '_wpnonce', 'fresh_wishlist_added'], $redirect_url);
        $redirect_url = add_query_arg([
            'fresh_wishlist_added' => $product_id,
        ], $redirect_url);

        wp_safe_redirect($redirect_url);
        exit;
    }

    if (isset($_GET['fresh_remove_from_wishlist'])) {
        $product_id = absint($_GET['fresh_remove_from_wishlist']);
        if (! isset($_GET['_wpnonce']) || ! wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['_wpnonce'])), 'fresh_remove_from_wishlist_' . $product_id)) {
            return;
        }

        $wishlist = array_diff(fresh_get_wishlist(), [$product_id]);
        fresh_set_wishlist($wishlist);

        wp_safe_redirect(fresh_page_url('wishlist'));
        exit;
    }

    if (isset($_POST['fresh_update_cart']) || isset($_POST['fresh_update_cart_checkout'])) {
        check_admin_referer('fresh_update_cart');
        $cart = [];
        $quantities = isset($_POST['quantity']) && is_array($_POST['quantity']) ? wp_unslash($_POST['quantity']) : [];

        foreach ($quantities as $product_id => $quantity) {
            $quantity = absint($quantity);
            if ($quantity > 0) {
                $cart[absint($product_id)] = $quantity;
            }
        }

        fresh_set_cart($cart);
        if (! $cart) {
            fresh_set_applied_coupon_code('');
        }
        wp_safe_redirect(isset($_POST['fresh_update_cart_checkout']) ? fresh_page_url('checkout') : fresh_page_url('cart'));
        exit;
    }

    if (isset($_POST['fresh_apply_coupon'])) {
        check_admin_referer('fresh_update_cart');

        $code   = isset($_POST['cart_coupon']) ? sanitize_text_field(wp_unslash($_POST['cart_coupon'])) : '';
        $result = fresh_validate_coupon_code($code);

        if (is_wp_error($result)) {
            fresh_set_applied_coupon_code('');
            $redirect_url = add_query_arg('fresh_coupon_error', $result->get_error_message(), fresh_page_url('cart'));
        } else {
            fresh_set_applied_coupon_code($result['code']);
            $redirect_url = add_query_arg('fresh_coupon_applied', $result['code'], fresh_page_url('cart'));
        }

        wp_safe_redirect($redirect_url);
        exit;
    }

    if (isset($_POST['fresh_remove_coupon'])) {
        check_admin_referer('fresh_update_cart');
        fresh_set_applied_coupon_code('');
        wp_safe_redirect(fresh_page_url('cart'));
        exit;
    }
}
add_action('template_redirect', 'fresh_handle_cart_actions', -5);

function fresh_add_to_cart_url($product_id, $quantity = 1)
{
    $url = add_query_arg([
        'fresh_add_to_cart' => absint($product_id),
        'quantity'          => max(1, absint($quantity)),
        'redirect_to'       => fresh_current_url(),
    ], home_url('/'));

    return wp_nonce_url($url, 'fresh_add_to_cart_' . absint($product_id));
}

function fresh_add_to_wishlist_url($product_id)
{
    $url = add_query_arg([
        'fresh_add_to_wishlist' => absint($product_id),
        'redirect_to'           => fresh_current_url(),
    ], home_url('/'));

    return wp_nonce_url($url, 'fresh_add_to_wishlist_' . absint($product_id));
}

function fresh_remove_from_cart_url($product_id)
{
    $url = add_query_arg('fresh_remove_from_cart', absint($product_id), fresh_page_url('cart'));

    return wp_nonce_url($url, 'fresh_remove_from_cart_' . absint($product_id));
}

function fresh_remove_from_wishlist_url($product_id)
{
    $url = add_query_arg('fresh_remove_from_wishlist', absint($product_id), fresh_page_url('wishlist'));

    return wp_nonce_url($url, 'fresh_remove_from_wishlist_' . absint($product_id));
}

function fresh_ajax_add_to_cart()
{
    check_ajax_referer('fresh_storefront', 'nonce');

    $product_id = isset($_POST['product_id']) ? absint($_POST['product_id']) : 0;
    $quantity   = isset($_POST['quantity']) ? max(1, absint($_POST['quantity'])) : 1;
    $product    = $product_id ? get_post($product_id) : null;

    if (! $product || $product->post_type !== 'fresh_product' || $product->post_status !== 'publish') {
        wp_send_json_error(['message' => __('Product not found.', 'fresh')], 404);
    }

    $cart = fresh_get_cart();
    $cart[$product_id] = isset($cart[$product_id]) ? $cart[$product_id] + $quantity : $quantity;
    fresh_set_cart($cart);

    wp_send_json_success([
        'message'   => __('Product added to cart.', 'fresh'),
        'cartCount' => fresh_cart_count(),
    ]);
}
add_action('wp_ajax_fresh_add_to_cart', 'fresh_ajax_add_to_cart');
add_action('wp_ajax_nopriv_fresh_add_to_cart', 'fresh_ajax_add_to_cart');

function fresh_ajax_add_to_wishlist()
{
    check_ajax_referer('fresh_storefront', 'nonce');

    $product_id = isset($_POST['product_id']) ? absint($_POST['product_id']) : 0;
    $product    = $product_id ? get_post($product_id) : null;

    if (! $product || $product->post_type !== 'fresh_product' || $product->post_status !== 'publish') {
        wp_send_json_error(['message' => __('Product not found.', 'fresh')], 404);
    }

    $wishlist = fresh_get_wishlist();
    if (! in_array($product_id, $wishlist, true)) {
        $wishlist[] = $product_id;
        fresh_set_wishlist($wishlist);
    }

    wp_send_json_success([
        'message'       => __('Product added to wishlist.', 'fresh'),
        'wishlistCount' => fresh_wishlist_count(),
    ]);
}
add_action('wp_ajax_fresh_add_to_wishlist', 'fresh_ajax_add_to_wishlist');
add_action('wp_ajax_nopriv_fresh_add_to_wishlist', 'fresh_ajax_add_to_wishlist');

function fresh_ajax_remove_from_wishlist()
{
    check_ajax_referer('fresh_storefront', 'nonce');

    $product_id = isset($_POST['product_id']) ? absint($_POST['product_id']) : 0;

    if (! $product_id) {
        wp_send_json_error(['message' => __('Product not found.', 'fresh')], 404);
    }

    $wishlist = array_values(array_diff(fresh_get_wishlist(), [$product_id]));
    fresh_set_wishlist($wishlist);

    wp_send_json_success([
        'message'       => __('Product removed from wishlist.', 'fresh'),
        'wishlistCount' => fresh_wishlist_count(),
        'isEmpty'       => fresh_wishlist_count() === 0,
    ]);
}
add_action('wp_ajax_fresh_remove_from_wishlist', 'fresh_ajax_remove_from_wishlist');
add_action('wp_ajax_nopriv_fresh_remove_from_wishlist', 'fresh_ajax_remove_from_wishlist');

function fresh_handle_checkout()
{
    if (! isset($_POST['fresh_place_order'])) {
        return null;
    }

    check_admin_referer('fresh_place_order');

    $items = fresh_cart_items();
    if (! $items) {
        return new WP_Error('empty_cart', __('Your cart is empty.', 'fresh'));
    }

    $name    = isset($_POST['customer_name']) ? sanitize_text_field(wp_unslash($_POST['customer_name'])) : '';
    $email   = isset($_POST['customer_email']) ? sanitize_email(wp_unslash($_POST['customer_email'])) : '';
    $phone   = isset($_POST['customer_phone']) ? sanitize_text_field(wp_unslash($_POST['customer_phone'])) : '';
    $address = isset($_POST['customer_address']) ? sanitize_textarea_field(wp_unslash($_POST['customer_address'])) : '';

    if ($name === '' || $email === '' || $phone === '' || $address === '') {
        return new WP_Error('missing_fields', __('Please fill all checkout fields.', 'fresh'));
    }

    $order_id = wp_insert_post([
        'post_type'   => 'fresh_order',
        'post_status' => 'publish',
        'post_title'  => sprintf(__('Order - %s', 'fresh'), $name),
    ]);

    if (is_wp_error($order_id) || ! $order_id) {
        return new WP_Error('order_failed', __('Could not create order.', 'fresh'));
    }

    update_post_meta($order_id, '_fresh_order_customer', [
        'name'    => $name,
        'email'   => $email,
        'phone'   => $phone,
        'address' => $address,
    ]);
    update_post_meta($order_id, '_fresh_order_items', $items);
    update_post_meta($order_id, '_fresh_order_subtotal', fresh_cart_subtotal());
    update_post_meta($order_id, '_fresh_order_discount', fresh_cart_discount());
    update_post_meta($order_id, '_fresh_order_coupon', fresh_get_applied_coupon_code());
    update_post_meta($order_id, '_fresh_order_total', fresh_cart_total());

    fresh_set_cart([]);
    fresh_set_applied_coupon_code('');

    return $order_id;
}

function fresh_order_whatsapp_message($order_id)
{
    $customer = get_post_meta($order_id, '_fresh_order_customer', true);
    $items    = get_post_meta($order_id, '_fresh_order_items', true);
    $subtotal = get_post_meta($order_id, '_fresh_order_subtotal', true);
    $discount = get_post_meta($order_id, '_fresh_order_discount', true);
    $coupon   = get_post_meta($order_id, '_fresh_order_coupon', true);
    $total    = get_post_meta($order_id, '_fresh_order_total', true);

    if (! is_array($customer)) {
        $customer = [];
    }

    if (! is_array($items)) {
        $items = [];
    }

    $lines = [
        sprintf(__('New Order #%d', 'fresh'), $order_id),
        '',
        __('Customer Details', 'fresh'),
        __('Name: ', 'fresh') . ($customer['name'] ?? ''),
        __('Email: ', 'fresh') . ($customer['email'] ?? ''),
        __('Phone: ', 'fresh') . ($customer['phone'] ?? ''),
        __('Address: ', 'fresh') . ($customer['address'] ?? ''),
        '',
        __('Cart Details', 'fresh'),
    ];

    foreach ($items as $item) {
        if (empty($item['product']) || ! $item['product'] instanceof WP_Post) {
            continue;
        }

        $lines[] = sprintf(
            '%s x %d - %s',
            get_the_title($item['product']),
            absint($item['quantity']),
            fresh_format_price($item['subtotal'])
        );
    }

    $lines[] = '';
    if ($subtotal !== '') {
        $lines[] = __('Subtotal: ', 'fresh') . fresh_format_price($subtotal);
    }

    if ((float) $discount > 0) {
        $coupon_text = $coupon ? ' (' . $coupon . ')' : '';
        $lines[] = __('Discount: ', 'fresh') . '-' . fresh_format_price($discount) . $coupon_text;
    }

    $lines[] = __('Total: ', 'fresh') . fresh_format_price($total);

    return implode("\n", $lines);
}

function fresh_order_whatsapp_url($order_id)
{
    $number = fresh_whatsapp_number();

    if (! $number) {
        return '';
    }

    return 'https://wa.me/' . rawurlencode($number) . '?text=' . rawurlencode(fresh_order_whatsapp_message($order_id));
}
