<?php
/**
 * Home page dynamic content helpers.
 */

function fresh_home_default($key)
{
    $defaults = [
        'hero_1_subtitle' => '100% genuine Products',
        'hero_1_title'    => "Our Garden's Most Favorite Food",
        'hero_1_text'     => 'Fresh organic products for your daily needs.',
        'hero_1_image'    => get_template_directory_uri() . '/assets/img/slider/21.png',
        'hero_2_subtitle' => 'Fresh & organic',
        'hero_2_title'    => 'Tasty & Healthy Organic Food',
        'hero_2_text'     => 'Choose quality food from trusted local products.',
        'hero_2_image'    => get_template_directory_uri() . '/assets/img/slider/21.png',
        'about_subtitle'  => 'Know More About Shop',
        'about_title'     => 'Trusted Organic Food Store',
        'about_text'      => 'Add your store introduction from Appearance > Customize > Fresh Home.',
        'about_image'     => get_template_directory_uri() . '/assets/img/others/6.png',
        'cta_subtitle'    => 'Any question you have',
        'cta_title'       => '897-876-987-90',
        'counter_class'   => 'ltn__counterup-area bg-image bg-overlay-theme-black-80 pt-115 pb-70',
        'feature_top_1_title' => 'Free shipping',
        'feature_top_1_text'  => 'On all orders over ₹100',
        'feature_top_2_title' => '15 days returns',
        'feature_top_2_text'  => 'Money back guarantee',
        'feature_top_3_title' => 'Secure checkout',
        'feature_top_3_text'  => 'Protected by trusted checkout',
        'feature_top_4_title' => 'Offer & gift here',
        'feature_top_4_text'  => 'On selected products',
        'feature_bottom_1_title' => 'Curated Products',
        'feature_bottom_1_text'  => 'Provide curated products for all orders over ₹100',
        'feature_bottom_2_title' => 'Handmade',
        'feature_bottom_2_text'  => 'We ensure the product quality that is our main goal',
        'feature_bottom_3_title' => 'Natural Food',
        'feature_bottom_3_text'  => 'Return product within 3 days for any product you buy',
        'feature_bottom_4_title' => 'Free home delivery',
        'feature_bottom_4_text'  => 'We ensure the product quality that you can trust easily',
    ];

    return $defaults[$key] ?? '';
}

function fresh_home_option($key)
{
    if ($key === 'counter_class') {
        $admin_value = get_option('fresh_counter_class', '');

        if ($admin_value !== '') {
            return $admin_value;
        }
    }

    return get_theme_mod('fresh_' . $key, fresh_home_default($key));
}

function fresh_sanitize_counter_number($value)
{
    if ($value === '') {
        return '';
    }

    return absint($value);
}

function fresh_counter_number($key, $fallback)
{
    $admin_value = get_option('fresh_counter_' . $key, '');

    if ($admin_value !== '') {
        return absint($admin_value);
    }

    return max(absint($fallback), 0);
}

function fresh_register_home_customizer($wp_customize)
{
    $wp_customize->add_section('fresh_home', [
        'title'    => __('Fresh Home', 'fresh'),
        'priority' => 30,
    ]);

    $fields = [
        'hero_1_subtitle' => __('Hero 1 Subtitle', 'fresh'),
        'hero_1_title'    => __('Hero 1 Title', 'fresh'),
        'hero_1_text'     => __('Hero 1 Text', 'fresh'),
        'hero_1_image'    => __('Hero 1 Image URL', 'fresh'),
        'hero_2_subtitle' => __('Hero 2 Subtitle', 'fresh'),
        'hero_2_title'    => __('Hero 2 Title', 'fresh'),
        'hero_2_text'     => __('Hero 2 Text', 'fresh'),
        'hero_2_image'    => __('Hero 2 Image URL', 'fresh'),
        'about_subtitle'  => __('About Subtitle', 'fresh'),
        'about_title'     => __('About Title', 'fresh'),
        'about_text'      => __('About Text', 'fresh'),
        'about_image'     => __('About Image URL', 'fresh'),
        'cta_subtitle'    => __('CTA Subtitle', 'fresh'),
        'cta_title'       => __('CTA Title', 'fresh'),
        'counter_class'   => __('Counter Section Class', 'fresh'),
    ];

    for ($i = 1; $i <= 4; $i++) {
        $fields['feature_top_' . $i . '_title'] = sprintf(__('Top Feature %d Title', 'fresh'), $i);
        $fields['feature_top_' . $i . '_text'] = sprintf(__('Top Feature %d Text', 'fresh'), $i);
        $fields['feature_bottom_' . $i . '_title'] = sprintf(__('Bottom Feature %d Title', 'fresh'), $i);
        $fields['feature_bottom_' . $i . '_text'] = sprintf(__('Bottom Feature %d Text', 'fresh'), $i);
    }

    foreach ($fields as $key => $label) {
        $wp_customize->add_setting('fresh_' . $key, [
            'default'           => fresh_home_default($key),
            'sanitize_callback' => str_contains($key, 'image') ? 'esc_url_raw' : 'sanitize_text_field',
        ]);

        $wp_customize->add_control('fresh_' . $key, [
            'label'   => $label,
            'section' => 'fresh_home',
            'type'    => str_contains($key, 'text') ? 'textarea' : 'text',
        ]);
    }
}
add_action('customize_register', 'fresh_register_home_customizer');

function fresh_register_home_admin_page()
{
    add_theme_page(
        __('Fresh Home Settings', 'fresh'),
        __('Fresh Home', 'fresh'),
        'manage_options',
        'fresh-home-settings',
        'fresh_render_home_admin_page'
    );
}
add_action('admin_menu', 'fresh_register_home_admin_page');

function fresh_register_home_admin_settings()
{
    register_setting('fresh_home_settings', 'fresh_counter_class', [
        'type'              => 'string',
        'sanitize_callback' => 'sanitize_text_field',
        'default'           => fresh_home_default('counter_class'),
    ]);

    $counter_fields = [
        'products'   => __('Products Number', 'fresh'),
        'categories' => __('Categories Number', 'fresh'),
        'orders'     => __('Orders Number', 'fresh'),
        'blog_posts' => __('Blog Posts Number', 'fresh'),
    ];

    foreach ($counter_fields as $key => $label) {
        register_setting('fresh_home_settings', 'fresh_counter_' . $key, [
            'type'              => 'string',
            'sanitize_callback' => 'fresh_sanitize_counter_number',
            'default'           => '',
        ]);
    }

    add_settings_section(
        'fresh_home_counter_section',
        __('Counter Section', 'fresh'),
        '__return_false',
        'fresh-home-settings'
    );

    add_settings_field(
        'fresh_counter_class',
        __('Counter Section Class', 'fresh'),
        'fresh_render_counter_class_field',
        'fresh-home-settings',
        'fresh_home_counter_section'
    );

    foreach ($counter_fields as $key => $label) {
        add_settings_field(
            'fresh_counter_' . $key,
            $label,
            'fresh_render_counter_number_field',
            'fresh-home-settings',
            'fresh_home_counter_section',
            [
                'key'   => $key,
                'label' => $label,
            ]
        );
    }
}
add_action('admin_init', 'fresh_register_home_admin_settings');

function fresh_render_counter_class_field()
{
    $value = get_option('fresh_counter_class', fresh_home_default('counter_class'));
    ?>
    <input
        type="text"
        id="fresh_counter_class"
        name="fresh_counter_class"
        class="regular-text"
        value="<?php echo esc_attr($value); ?>"
    >
    <?php
}

function fresh_render_counter_number_field($args)
{
    $key = isset($args['key']) ? sanitize_key($args['key']) : '';
    $option_name = 'fresh_counter_' . $key;
    $value = get_option($option_name, '');
    ?>
    <input
        type="number"
        min="0"
        step="1"
        id="<?php echo esc_attr($option_name); ?>"
        name="<?php echo esc_attr($option_name); ?>"
        class="small-text"
        value="<?php echo esc_attr($value); ?>"
    >
    <p class="description"><?php esc_html_e('Leave blank to use the automatic count.', 'fresh'); ?></p>
    <?php
}

function fresh_render_home_admin_page()
{
    if (! current_user_can('manage_options')) {
        return;
    }
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('Fresh Home Settings', 'fresh'); ?></h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('fresh_home_settings');
            do_settings_sections('fresh-home-settings');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

function fresh_home_features($variant = 'top')
{
    if ($variant === 'bottom') {
        return [
            ['icon' => 'icons/icon-img/11.png', 'title' => fresh_home_option('feature_bottom_1_title'), 'text' => fresh_home_option('feature_bottom_1_text')],
            ['icon' => 'icons/icon-img/12.png', 'title' => fresh_home_option('feature_bottom_2_title'), 'text' => fresh_home_option('feature_bottom_2_text')],
            ['icon' => 'icons/icon-img/13.png', 'title' => fresh_home_option('feature_bottom_3_title'), 'text' => fresh_home_option('feature_bottom_3_text')],
            ['icon' => 'icons/icon-img/14.png', 'title' => fresh_home_option('feature_bottom_4_title'), 'text' => fresh_home_option('feature_bottom_4_text')],
        ];
    }

    return [
        ['icon' => 'icons/svg/8-trolley.svg', 'title' => fresh_home_option('feature_top_1_title'), 'text' => fresh_home_option('feature_top_1_text')],
        ['icon' => 'icons/svg/9-money.svg', 'title' => fresh_home_option('feature_top_2_title'), 'text' => fresh_home_option('feature_top_2_text')],
        ['icon' => 'icons/svg/10-credit-card.svg', 'title' => fresh_home_option('feature_top_3_title'), 'text' => fresh_home_option('feature_top_3_text')],
        ['icon' => 'icons/svg/11-gift-card.svg', 'title' => fresh_home_option('feature_top_4_title'), 'text' => fresh_home_option('feature_top_4_text')],
    ];
}
