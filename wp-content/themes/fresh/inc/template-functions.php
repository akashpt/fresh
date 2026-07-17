<?php
/**
 * Small template helpers.
 */

function fresh_asset_uri($path = '')
{
    return esc_url(get_template_directory_uri() . '/assets/' . ltrim($path, '/'));
}

function fresh_posted_on()
{
    printf(
        '<span class="posted-on">%s</span>',
        esc_html(get_the_date())
    );
}

function fresh_primary_menu_fallback()
{
    ?>
    <ul>
        <li><a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Home', 'fresh'); ?></a></li>
        <li><a href="<?php echo esc_url(home_url('/shop/')); ?>"><?php esc_html_e('Shop', 'fresh'); ?></a></li>
        <li><a href="<?php echo esc_url(home_url('/contact/')); ?>"><?php esc_html_e('Contact', 'fresh'); ?></a></li>
     </ul>
    <?php
}

function fresh_remove_about_from_primary_menu($items, $args)
{
    if (empty($args->theme_location) || $args->theme_location !== 'primary') {
        return $items;
    }

    $about_page = get_page_by_path('about');
    $about_id   = $about_page ? (int) $about_page->ID : 0;

    return array_values(array_filter($items, function ($item) use ($about_id) {
        $url_path = trim((string) wp_parse_url($item->url, PHP_URL_PATH), '/');

        if ($about_id && (int) $item->object_id === $about_id) {
            return false;
        }

        return $url_path !== 'about' && substr($url_path, -6) !== '/about';
    }));
}
add_filter('wp_nav_menu_objects', 'fresh_remove_about_from_primary_menu', 10, 2);

function fresh_logo_attachment_id()
{
    return absint(get_option('fresh_header_logo_id', 0));
}

function fresh_default_logo_url()
{
    return get_template_directory_uri() . '/assets/img/logo.png';
}

function fresh_local_file_path_from_url($url)
{
    $url_path = wp_parse_url($url, PHP_URL_PATH);

    if (! $url_path) {
        return '';
    }

    $theme_base_url = wp_parse_url(get_template_directory_uri(), PHP_URL_PATH);

    if ($theme_base_url && strpos($url_path, $theme_base_url) === 0) {
        $relative_path = ltrim(substr($url_path, strlen($theme_base_url)), '/\\');
        return trailingslashit(get_template_directory()) . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relative_path);
    }

    $uploads = wp_get_upload_dir();
    $uploads_base_url = ! empty($uploads['baseurl']) ? wp_parse_url($uploads['baseurl'], PHP_URL_PATH) : '';

    if ($uploads_base_url && strpos($url_path, $uploads_base_url) === 0) {
        $relative_path = ltrim(substr($url_path, strlen($uploads_base_url)), '/\\');
        return trailingslashit($uploads['basedir']) . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relative_path);
    }

    return '';
}

function fresh_image_dimensions($url, $fallback_width = 800, $fallback_height = 800)
{
    $file_path = fresh_local_file_path_from_url($url);

    if ($file_path && file_exists($file_path)) {
        $size = getimagesize($file_path);

        if (! empty($size[0]) && ! empty($size[1])) {
            return [
                'width'  => absint($size[0]),
                'height' => absint($size[1]),
            ];
        }
    }

    return [
        'width'  => absint($fallback_width),
        'height' => absint($fallback_height),
    ];
}

function fresh_image_attrs($url, $alt = '', $args = [])
{
    $args = wp_parse_args($args, [
        'class'           => '',
        'fallback_width'  => 800,
        'fallback_height' => 800,
        'loading'         => 'lazy',
        'decoding'        => 'async',
        'fetchpriority'   => '',
    ]);
    $dimensions = fresh_image_dimensions($url, $args['fallback_width'], $args['fallback_height']);
    $attrs = [
        'src'      => esc_url($url),
        'alt'      => esc_attr($alt),
        'width'    => $dimensions['width'],
        'height'   => $dimensions['height'],
        'loading'  => $args['loading'],
        'decoding' => $args['decoding'],
    ];

    if ($args['class']) {
        $attrs['class'] = $args['class'];
    }

    if ($args['fetchpriority']) {
        $attrs['fetchpriority'] = $args['fetchpriority'];
    }

    return implode(' ', array_map(
        function ($name, $value) {
            return sprintf('%s="%s"', esc_attr($name), esc_attr($value));
        },
        array_keys($attrs),
        $attrs
    ));
}

function fresh_preload_critical_assets()
{
    $hero_image = fresh_home_option('hero_1_image');

    if ($hero_image) {
        printf(
            '<link rel="preload" as="image" href="%s" fetchpriority="high">' . "\n",
            esc_url($hero_image)
        );
    }

    printf(
        '<link rel="preload" as="font" type="font/woff2" href="%s" crossorigin>' . "\n",
        esc_url(get_template_directory_uri() . '/assets/webfonts/fa-solid-900.woff2')
    );
}
add_action('wp_head', 'fresh_preload_critical_assets', 1);

function fresh_site_logo()
{
    $logo_id = fresh_logo_attachment_id();

    if ($logo_id) {
        $logo = wp_get_attachment_image($logo_id, 'full', false, [
            'class' => 'custom-logo fresh-theme-logo',
            'alt'   => get_bloginfo('name'),
        ]);

        if ($logo) {
            printf(
                '<a href="%s" class="custom-logo-link" rel="home">%s</a>',
                esc_url(home_url('/')),
                $logo
            );
            return;
        }
    }

    if (has_custom_logo()) {
        the_custom_logo();
        return;
    }

    printf(
        '<a href="%s"><img %s></a>',
        esc_url(home_url('/')),
        fresh_image_attrs(fresh_default_logo_url(), get_bloginfo('name'), [
            'class'           => 'custom-logo fresh-theme-logo',
            'fallback_width'  => 220,
            'fallback_height' => 80,
        ])
    );
}

function fresh_register_logo_setting()
{
    register_setting('fresh_logo_settings', 'fresh_header_logo_id', [
        'type'              => 'integer',
        'sanitize_callback' => 'absint',
        'default'           => 0,
    ]);
}
add_action('admin_init', 'fresh_register_logo_setting');

function fresh_logo_admin_menu()
{
    add_theme_page(
        __('Fresh Logo', 'fresh'),
        __('Fresh Logo', 'fresh'),
        'manage_options',
        'fresh-logo',
        'fresh_render_logo_admin_page'
    );
}
add_action('admin_menu', 'fresh_logo_admin_menu');

function fresh_logo_admin_assets($hook)
{
    if ($hook !== 'appearance_page_fresh-logo') {
        return;
    }

    wp_enqueue_media();
}
add_action('admin_enqueue_scripts', 'fresh_logo_admin_assets');

function fresh_render_logo_admin_page()
{
    $logo_id  = fresh_logo_attachment_id();
    $logo_url = $logo_id ? wp_get_attachment_image_url($logo_id, 'medium') : '';
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('Fresh Logo', 'fresh'); ?></h1>
        <p><?php esc_html_e('Upload or select your header logo here. This uploader does not crop the image.', 'fresh'); ?></p>
        <form method="post" action="options.php">
            <?php settings_fields('fresh_logo_settings'); ?>
            <input type="hidden" id="fresh_header_logo_id" name="fresh_header_logo_id" value="<?php echo esc_attr($logo_id); ?>">

            <div id="fresh-logo-preview" style="margin: 20px 0;">
                <?php if ($logo_url) : ?>
                    <img src="<?php echo esc_url($logo_url); ?>" alt="<?php esc_attr_e('Selected logo', 'fresh'); ?>" style="max-width: 320px; height: auto; background: #fff; border: 1px solid #ccd0d4; padding: 12px;">
                <?php endif; ?>
            </div>

            <p>
                <button type="button" class="button" id="fresh-select-logo"><?php esc_html_e('Choose Logo', 'fresh'); ?></button>
                <button type="button" class="button" id="fresh-remove-logo"><?php esc_html_e('Remove Logo', 'fresh'); ?></button>
            </p>

            <?php submit_button(); ?>
        </form>
    </div>
    <script>
        (function () {
            var selectButton = document.getElementById('fresh-select-logo');
            var removeButton = document.getElementById('fresh-remove-logo');
            var input = document.getElementById('fresh_header_logo_id');
            var preview = document.getElementById('fresh-logo-preview');
            var frame;

            if (!selectButton || !removeButton || !input || !preview) {
                return;
            }

            selectButton.addEventListener('click', function () {
                if (frame) {
                    frame.open();
                    return;
                }

                frame = wp.media({
                    title: <?php echo wp_json_encode(__('Choose Logo', 'fresh')); ?>,
                    button: { text: <?php echo wp_json_encode(__('Use this logo', 'fresh')); ?> },
                    multiple: false
                });

                frame.on('select', function () {
                    var attachment = frame.state().get('selection').first().toJSON();
                    var url = attachment.sizes && attachment.sizes.medium ? attachment.sizes.medium.url : attachment.url;
                    input.value = attachment.id;
                    preview.innerHTML = '<img src="' + url + '" alt="" style="max-width: 320px; height: auto; background: #fff; border: 1px solid #ccd0d4; padding: 12px;">';
                });

                frame.open();
            });

            removeButton.addEventListener('click', function () {
                input.value = '';
                preview.innerHTML = '';
            });
        }());
    </script>
    <?php
}

function fresh_breadcrumb_banner($title, $subtitle = '')
{
    ?>
    <div class="ltn__breadcrumb-area fresh-breadcrumb-simple">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="fresh-breadcrumb-simple-inner">
                        <h1 class="section-title"><?php echo esc_html($title); ?></h1>
                        <div class="ltn__breadcrumb-list">
                            <ul>
                                <li><a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Home', 'fresh'); ?></a></li>
                                <li><?php echo esc_html($title); ?></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
}

function fresh_static_route_template()
{
    $request_path = isset($_SERVER['REQUEST_URI']) ? wp_parse_url(sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI'])), PHP_URL_PATH) : '';
    $home_path = wp_parse_url(home_url('/'), PHP_URL_PATH);

    if ($home_path && strpos($request_path, $home_path) === 0) {
        $request_path = substr($request_path, strlen($home_path));
    }

    $slug = trim((string) $request_path, '/');

    if (in_array($slug, ['404', 'error-page'], true)) {
        $template = locate_template('404.php');

        if (! $template) {
            return;
        }

        global $wp_query;
        $wp_query->is_404 = true;

        status_header(404);
        include $template;
        exit;
    }

    if (! is_404()) {
        return;
    }

    $templates = [
        'cart'     => 'cart.php',
        'checkout' => 'checkout.php',
        'shop'     => 'shop.php',
        'wishlist' => 'wishlist.php',
        'contact'  => 'contact.php',
    ];

    if (empty($templates[$slug])) {
        return;
    }

    $template = locate_template($templates[$slug]);
    if (! $template) {
        return;
    }

    global $wp_query;
    $wp_query->is_404 = false;

    status_header(200);
    include $template;
    exit;
}
add_action('template_redirect', 'fresh_static_route_template', 0);
