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

function fresh_slug_needs_english($slug)
{
    return strpos((string) $slug, '%') !== false || (bool) preg_match('/[^\x20-\x7E]/', (string) $slug);
}

function fresh_english_slug_from_text($text, $fallback = 'item')
{
    $text = rawurldecode((string) $text);
    $map = [
        'அ' => 'a', 'ஆ' => 'aa', 'இ' => 'i', 'ஈ' => 'ee', 'உ' => 'u', 'ஊ' => 'oo',
        'எ' => 'e', 'ஏ' => 'e', 'ஐ' => 'ai', 'ஒ' => 'o', 'ஓ' => 'o', 'ஔ' => 'au',
        'க' => 'ka', 'ங' => 'nga', 'ச' => 'sa', 'ஜ' => 'ja', 'ஞ' => 'nya',
        'ட' => 'ta', 'ண' => 'na', 'த' => 'tha', 'ந' => 'na', 'ன' => 'na',
        'ப' => 'pa', 'ம' => 'ma', 'ய' => 'ya', 'ர' => 'ra', 'ற' => 'ra',
        'ல' => 'la', 'ள' => 'la', 'ழ' => 'zha', 'வ' => 'va', 'ஷ' => 'sha',
        'ஸ' => 'sa', 'ஹ' => 'ha',
        'ா' => 'a', 'ி' => 'i', 'ீ' => 'ee', 'ு' => 'u', 'ூ' => 'oo',
        'ெ' => 'e', 'ே' => 'e', 'ை' => 'ai', 'ொ' => 'o', 'ோ' => 'o', 'ௌ' => 'au',
        '்' => '', 'ஃ' => '',
        '௦' => '0', '௧' => '1', '௨' => '2', '௩' => '3', '௪' => '4',
        '௫' => '5', '௬' => '6', '௭' => '7', '௮' => '8', '௯' => '9',
    ];

    $text = strtr($text, $map);
    $text = remove_accents($text);
    $text = strtolower($text);
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    $text = trim((string) $text, '-');

    return $text ?: sanitize_key($fallback);
}

function fresh_sanitize_title_to_english($title, $raw_title, $context)
{
    if (! apply_filters('fresh_enable_english_slug_generation', false)) {
        return $title;
    }

    if ($context !== 'save' || ! fresh_slug_needs_english($raw_title . $title)) {
        return $title;
    }

    return fresh_english_slug_from_text($raw_title ?: $title, $title ?: 'item');
}
add_filter('sanitize_title', 'fresh_sanitize_title_to_english', 9, 3);

function fresh_update_existing_non_english_slugs()
{
    if (! apply_filters('fresh_enable_existing_slug_migration', false)) {
        return;
    }

    $version = '2026-08-english-slugs-v1';

    if (get_option('fresh_english_slugs_version') === $version || wp_installing()) {
        return;
    }

    $posts = get_posts([
        'post_type'      => ['post', 'page', 'fresh_product'],
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'no_found_rows'  => true,
    ]);

    foreach ($posts as $post) {
        if (! fresh_slug_needs_english($post->post_name)) {
            continue;
        }

        $base_slug = fresh_english_slug_from_text($post->post_title, $post->post_type . '-' . $post->ID);
        $unique_slug = wp_unique_post_slug($base_slug, $post->ID, $post->post_status, $post->post_type, $post->post_parent);

        wp_update_post([
            'ID'        => $post->ID,
            'post_name' => $unique_slug,
        ]);
    }

    foreach (['category', 'fresh_product_category'] as $taxonomy) {
        $terms = get_terms([
            'taxonomy'   => $taxonomy,
            'hide_empty' => false,
        ]);

        if (is_wp_error($terms)) {
            continue;
        }

        foreach ($terms as $term) {
            if (! fresh_slug_needs_english($term->slug)) {
                continue;
            }

            $base_slug = fresh_english_slug_from_text($term->name, $taxonomy . '-' . $term->term_id);
            $term->slug = $base_slug;

            wp_update_term($term->term_id, $taxonomy, [
                'slug' => wp_unique_term_slug($base_slug, $term),
            ]);
        }
    }

    update_option('fresh_english_slugs_version', $version, false);
}
add_action('init', 'fresh_update_existing_non_english_slugs', 30);

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

    $attachment_id = attachment_url_to_postid($url);

    if ($attachment_id) {
        $srcset = wp_get_attachment_image_srcset($attachment_id, 'full');
        $sizes = wp_get_attachment_image_sizes($attachment_id, 'full');

        if ($srcset) {
            $attrs['srcset'] = esc_attr($srcset);
        }

        if ($sizes) {
            $attrs['sizes'] = esc_attr($sizes);
        }
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

function fresh_pagination_resource_links()
{
    if (is_admin() || ! (is_home() || is_archive() || is_search())) {
        return;
    }

    $current_page = max(1, (int) get_query_var('paged'));
    $max_pages = isset($GLOBALS['wp_query']->max_num_pages) ? (int) $GLOBALS['wp_query']->max_num_pages : 0;

    if ($current_page > 1) {
        printf('<link rel="prev" href="%s">' . "\n", esc_url(get_pagenum_link($current_page - 1)));
    }

    if ($max_pages && $current_page < $max_pages) {
        printf('<link rel="next" href="%s">' . "\n", esc_url(get_pagenum_link($current_page + 1)));
    }
}
add_action('wp_head', 'fresh_pagination_resource_links', 3);

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
        '<a href="%s" class="fresh-site-title" rel="home">%s</a>',
        esc_url(home_url('/')),
        esc_html(get_bloginfo('name'))
    );
}

function fresh_document_title_separator($separator)
{
    return '|';
}
add_filter('document_title_separator', 'fresh_document_title_separator');

function fresh_document_title_product()
{
    if (is_singular('fresh_product')) {
        $product = get_post();

        return $product instanceof WP_Post && $product->post_status === 'publish' ? $product : null;
    }

    if (empty($_GET['product'])) {
        return null;
    }

    $product_id = absint(wp_unslash($_GET['product']));
    $product = $product_id ? get_post($product_id) : null;

    if (! $product || $product->post_type !== 'fresh_product' || $product->post_status !== 'publish') {
        return null;
    }

    return $product;
}

function fresh_document_title_parts($parts)
{
    if (is_admin()) {
        return $parts;
    }

    $site_title = get_bloginfo('name', 'display');
    $page_title = isset($parts['title']) ? $parts['title'] : '';
    $product = fresh_document_title_product();

    if ($product) {
        $seo_title = get_post_meta($product->ID, '_fresh_product_seo_title', true);
        $page_title = $seo_title ?: get_the_title($product);
    }

    if (is_front_page()) {
        return [
            'site' => $site_title,
        ];
    }

    $ordered_parts = [
        'site' => $site_title,
    ];

    if ($page_title && strcasecmp($page_title, $site_title) !== 0) {
        $ordered_parts['title'] = $page_title;
    }

    if (! empty($parts['page'])) {
        $ordered_parts['page'] = $parts['page'];
    }

    return $ordered_parts;
}
add_filter('document_title_parts', 'fresh_document_title_parts');
remove_action('wp_head', 'rel_canonical');

function fresh_seo_trim_text($text, $length = 155)
{
    $text = trim(preg_replace('/\s+/', ' ', wp_strip_all_tags((string) $text)));

    if ($text === '') {
        return '';
    }

    if (function_exists('mb_strlen') && mb_strlen($text) > $length) {
        return rtrim(mb_substr($text, 0, $length - 1)) . '...';
    }

    if (strlen($text) > $length) {
        return rtrim(substr($text, 0, $length - 1)) . '...';
    }

    return $text;
}

function fresh_seo_description()
{
    $site_name = get_bloginfo('name', 'display');
    $tagline = get_bloginfo('description', 'display');
    $product = fresh_document_title_product();

    if ($product) {
        $description = get_post_meta($product->ID, '_fresh_product_seo_description', true);
        $description = $description ?: ($product->post_excerpt ?: $product->post_content);

        if ($description) {
            return fresh_seo_trim_text($description);
        }

        return fresh_seo_trim_text(sprintf(
            __('Order %1$s from %2$s. pureauranaturals products, easy cart ordering, and quick checkout.', 'fresh'),
            get_the_title($product),
            $site_name
        ));
    }

    if (is_singular()) {
        $post = get_post();
        $description = has_excerpt($post) ? get_the_excerpt($post) : $post->post_content;

        if ($description) {
            return fresh_seo_trim_text($description);
        }
    }

    if (is_tax() || is_category() || is_tag()) {
        $term_description = term_description();

        if ($term_description) {
            return fresh_seo_trim_text($term_description);
        }
    }

    if (is_search()) {
        return fresh_seo_trim_text(sprintf(
            __('Search pureauranaturals products and everyday essentials at %s.', 'fresh'),
            $site_name
        ));
    }

    if (is_front_page()) {
        return fresh_seo_trim_text($tagline ?: sprintf(
            __('Shop pureauranaturals products, natural essentials, and daily favorites from %s.', 'fresh'),
            $site_name
        ));
    }

    return fresh_seo_trim_text(sprintf(
        __('Browse pureauranaturals products, compare prices, add items to cart, and place orders with %s.', 'fresh'),
        $site_name
    ));
}

function fresh_seo_current_route_slug()
{
    $request_path = isset($_SERVER['REQUEST_URI']) ? wp_parse_url(sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI'])), PHP_URL_PATH) : '';
    $home_path = wp_parse_url(home_url('/'), PHP_URL_PATH);

    if ($home_path && strpos($request_path, $home_path) === 0) {
        $request_path = substr($request_path, strlen($home_path));
    }

    return trim((string) $request_path, '/');
}

function fresh_seo_is_low_value_page()
{
    if (is_search() || is_404()) {
        return true;
    }

    $route = fresh_seo_current_route_slug();

    return in_array($route, ['cart', 'checkout', 'wishlist', 'product-details'], true);
}

function fresh_seo_canonical_url()
{
    $product = fresh_document_title_product();

    if ($product && function_exists('fresh_product_detail_url')) {
        return fresh_product_detail_url($product->ID);
    }

    if (is_singular('fresh_product') && function_exists('fresh_product_detail_url')) {
        return fresh_product_detail_url(get_the_ID());
    }

    if (is_singular()) {
        return get_permalink();
    }

    if (is_tax() || is_category() || is_tag()) {
        $term = get_queried_object();
        $term_link = $term ? get_term_link($term) : '';

        return is_wp_error($term_link) ? home_url('/') : $term_link;
    }

    if (is_front_page()) {
        return home_url('/');
    }

    return get_pagenum_link(max(1, get_query_var('paged')));
}

function fresh_seo_image_url()
{
    $product = fresh_document_title_product();

    if ($product && function_exists('fresh_product_image_url')) {
        return fresh_product_image_url($product->ID, 'large');
    }

    if (is_singular() && has_post_thumbnail()) {
        return get_the_post_thumbnail_url(null, 'large');
    }

    return fresh_default_logo_url();
}

function fresh_seo_output_meta()
{
    if (is_admin()) {
        return;
    }

    $description = fresh_seo_description();
    $canonical = fresh_seo_canonical_url();
    $title = wp_get_document_title();
    $image = fresh_seo_image_url();
    $site_name = get_bloginfo('name', 'display');
    $type = fresh_document_title_product() || is_singular('fresh_product') ? 'product' : 'website';
    $noindex = fresh_seo_is_low_value_page() || isset($_GET['product_search']) || isset($_GET['sort']);

    if ($noindex) {
        echo '<meta name="robots" content="noindex,follow">' . "\n";
    }

    if ($description) {
        printf('<meta name="description" content="%s">' . "\n", esc_attr($description));
    }

    printf('<link rel="canonical" href="%s">' . "\n", esc_url($canonical));
    printf('<meta property="og:site_name" content="%s">' . "\n", esc_attr($site_name));
    printf('<meta property="og:title" content="%s">' . "\n", esc_attr($title));
    printf('<meta property="og:description" content="%s">' . "\n", esc_attr($description));
    printf('<meta property="og:url" content="%s">' . "\n", esc_url($canonical));
    printf('<meta property="og:type" content="%s">' . "\n", esc_attr($type));
    printf('<meta property="og:image" content="%s">' . "\n", esc_url($image));
    echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
    printf('<meta name="twitter:title" content="%s">' . "\n", esc_attr($title));
    printf('<meta name="twitter:description" content="%s">' . "\n", esc_attr($description));
    printf('<meta name="twitter:image" content="%s">' . "\n", esc_url($image));
}
add_action('wp_head', 'fresh_seo_output_meta', 2);

function fresh_send_robots_header()
{
    if (is_admin()) {
        return;
    }

    if (fresh_seo_is_low_value_page() || isset($_GET['product_search']) || isset($_GET['sort'])) {
        header('X-Robots-Tag: noindex, follow', true);
    }
}
add_action('send_headers', 'fresh_send_robots_header');

function fresh_seo_breadcrumb_schema($current_title = '')
{
    $items = [
        [
            '@type'    => 'ListItem',
            'position' => 1,
            'name'     => __('Home', 'fresh'),
            'item'     => home_url('/'),
        ],
    ];

    $product = fresh_document_title_product();

    if ($product) {
        $items[] = [
            '@type'    => 'ListItem',
            'position' => 2,
            'name'     => __('Shop', 'fresh'),
            'item'     => fresh_page_url('shop'),
        ];
        $items[] = [
            '@type'    => 'ListItem',
            'position' => 3,
            'name'     => get_the_title($product),
            'item'     => fresh_product_detail_url($product->ID),
        ];
    } elseif (is_singular()) {
        $items[] = [
            '@type'    => 'ListItem',
            'position' => 2,
            'name'     => get_the_title(),
            'item'     => get_permalink(),
        ];
    } elseif (is_tax() || is_category() || is_tag()) {
        $term = get_queried_object();
        $term_link = $term ? get_term_link($term) : '';
        $items[] = [
            '@type'    => 'ListItem',
            'position' => 2,
            'name'     => $term ? $term->name : $current_title,
            'item'     => is_wp_error($term_link) ? home_url('/') : $term_link,
        ];
    } elseif ($current_title) {
        $items[] = [
            '@type'    => 'ListItem',
            'position' => 2,
            'name'     => $current_title,
            'item'     => fresh_seo_canonical_url(),
        ];
    }

    return [
        '@context'        => 'https://schema.org',
        '@type'           => 'BreadcrumbList',
        'itemListElement' => $items,
    ];
}

function fresh_seo_local_business_schema()
{
    return [
        '@context' => 'https://schema.org',
        '@type'    => 'LocalBusiness',
        'name'     => get_bloginfo('name', 'display'),
        'url'      => home_url('/'),
        'image'    => fresh_seo_image_url(),
        'sameAs'   => [],
        'areaServed' => [
            '@type' => 'Country',
            'name'  => 'India',
        ],
    ];
}

function fresh_seo_webpage_schema()
{
    $type = is_tax() || is_category() || is_post_type_archive() ? 'CollectionPage' : 'WebPage';
    $product = fresh_document_title_product();

    if ($product) {
        $type = 'ItemPage';
    }

    return [
        '@context'    => 'https://schema.org',
        '@type'       => $type,
        'name'        => wp_get_document_title(),
        'description' => fresh_seo_description(),
        'url'         => fresh_seo_canonical_url(),
        'isPartOf'    => [
            '@type' => 'WebSite',
            'name'  => get_bloginfo('name', 'display'),
            'url'   => home_url('/'),
        ],
    ];
}

function fresh_seo_organization_schema()
{
    return [
        '@context' => 'https://schema.org',
        '@type'    => 'Organization',
        'name'     => get_bloginfo('name', 'display'),
        'url'      => home_url('/'),
        'logo'     => fresh_default_logo_url(),
    ];
}

function fresh_seo_output_schema()
{
    if (is_admin()) {
        return;
    }

    $product = fresh_document_title_product();
    $search_target = add_query_arg('product_search', 'FRESH_SEARCH_TERM', function_exists('fresh_page_url') ? fresh_page_url('shop') : home_url('/shop/'));
    $search_target = str_replace('FRESH_SEARCH_TERM', '{search_term_string}', $search_target);
    $schema = [
        '@context' => 'https://schema.org',
        '@type'    => 'WebSite',
        'name'     => get_bloginfo('name', 'display'),
        'url'      => home_url('/'),
        'potentialAction' => [
            '@type'       => 'SearchAction',
            'target'      => $search_target,
            'query-input' => 'required name=search_term_string',
        ],
    ];

    if ($product && function_exists('fresh_product_price') && function_exists('fresh_product_image_url')) {
        $price = fresh_product_price($product->ID);
        $sku = get_post_meta($product->ID, '_fresh_product_sku', true);
        $categories = get_the_terms($product->ID, 'fresh_product_category');
        $ingredients = get_post_meta($product->ID, '_fresh_product_ingredients', true);
        $schema = [
            '@context'    => 'https://schema.org',
            '@type'       => 'Product',
            'name'        => get_the_title($product),
            'description' => fresh_seo_description(),
            'image'       => fresh_product_image_url($product->ID, 'large'),
            'url'         => function_exists('fresh_product_detail_url') ? fresh_product_detail_url($product->ID) : get_permalink($product),
            'offers'      => [
                '@type'         => 'Offer',
                'priceCurrency' => 'INR',
                'price'         => number_format((float) $price, 2, '.', ''),
                'availability'  => 'https://schema.org/InStock',
                'itemCondition' => 'https://schema.org/NewCondition',
                'url'           => function_exists('fresh_product_detail_url') ? fresh_product_detail_url($product->ID) : get_permalink($product),
            ],
            'brand'       => [
                '@type' => 'Brand',
                'name'  => get_bloginfo('name', 'display'),
            ],
        ];

        if ($sku) {
            $schema['sku'] = $sku;
        }

        if (! is_wp_error($categories) && ! empty($categories)) {
            $schema['category'] = implode(', ', wp_list_pluck($categories, 'name'));
        }

        if ($ingredients) {
            $schema['material'] = $ingredients;
        }
    }

    $schemas = [$schema, fresh_seo_webpage_schema(), fresh_seo_breadcrumb_schema(), fresh_seo_organization_schema()];

    if ($product) {
        $faq_items = function_exists('fresh_product_faqs') ? fresh_product_faqs($product->ID) : [];
        $main_entity = [];

        foreach ($faq_items as $faq) {
            $main_entity[] = [
                '@type'          => 'Question',
                'name'           => $faq['question'],
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text'  => $faq['answer'],
                ],
            ];
        }

        if ($main_entity) {
            $schemas[] = [
                '@context'   => 'https://schema.org',
                '@type'      => 'FAQPage',
                'mainEntity' => $main_entity,
            ];
        }
    }

    $graph = array_map(function ($item) {
        unset($item['@context']);
        return $item;
    }, $schemas);

    printf(
        '<script type="application/ld+json">%s</script>' . "\n",
        wp_json_encode([
            '@context' => 'https://schema.org',
            '@graph'   => $graph,
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
    );
}
add_action('wp_head', 'fresh_seo_output_schema', 20);

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
        __('pureauranaturals Logo', 'fresh'),
        __('pureauranaturals Logo', 'fresh'),
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
        <h1><?php esc_html_e('pureauranaturals Logo', 'fresh'); ?></h1>
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

function fresh_redirect_legacy_product_detail_url()
{
    if (
        fresh_seo_current_route_slug() !== 'product-details' ||
        empty($_GET['product']) ||
        ! function_exists('fresh_product_detail_url')
    ) {
        return;
    }

    $product_id = absint(wp_unslash($_GET['product']));
    $product = $product_id ? get_post($product_id) : null;

    if (! $product || $product->post_type !== 'fresh_product' || $product->post_status !== 'publish') {
        return;
    }

    wp_safe_redirect(fresh_product_detail_url($product->ID), 301);
    exit;
}
add_action('template_redirect', 'fresh_redirect_legacy_product_detail_url', 1);

function fresh_sitemap_url_entry($url, $modified = '', $changefreq = 'weekly', $priority = '0.6')
{
    static $seen_urls = [];

    $url_key = untrailingslashit($url);

    if (isset($seen_urls[$url_key])) {
        return;
    }

    $seen_urls[$url_key] = true;
    $modified = $modified ?: gmdate('Y-m-d H:i:s');
    $timestamp = strtotime($modified . ' UTC');

    printf(
        "\t<url>\n\t\t<loc>%s</loc>\n\t\t<lastmod>%s</lastmod>\n\t\t<changefreq>%s</changefreq>\n\t\t<priority>%s</priority>\n\t</url>\n",
        esc_url($url),
        esc_html($timestamp ? gmdate('c', $timestamp) : gmdate('c')),
        esc_html($changefreq),
        esc_html($priority)
    );
}

function fresh_sitemap_posts($post_type, $changefreq = 'weekly', $priority = '0.6')
{
    $excluded_page_slugs = ['cart', 'checkout', 'wishlist', 'product-details'];
    $posts = get_posts([
        'post_type'      => $post_type,
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'orderby'        => 'modified',
        'order'          => 'DESC',
        'no_found_rows'  => true,
    ]);

    foreach ($posts as $post) {
        if ($post_type === 'page' && in_array($post->post_name, $excluded_page_slugs, true)) {
            continue;
        }

        fresh_sitemap_url_entry(get_permalink($post), $post->post_modified_gmt, $changefreq, $priority);
    }
}

function fresh_sitemap_static_routes()
{
    $routes = [
        'shop'    => ['monthly', '0.9'],
        'contact' => ['monthly', '0.7'],
    ];

    foreach ($routes as $slug => $meta) {
        fresh_sitemap_url_entry(home_url('/' . $slug . '/'), get_lastpostmodified('GMT'), $meta[0], $meta[1]);
    }
}

function fresh_sitemap_product_detail_urls()
{
    if (! function_exists('fresh_product_detail_url')) {
        return;
    }

    $products = get_posts([
        'post_type'      => 'fresh_product',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'orderby'        => 'modified',
        'order'          => 'DESC',
        'no_found_rows'  => true,
    ]);

    foreach ($products as $product) {
        fresh_sitemap_url_entry(fresh_product_detail_url($product->ID), $product->post_modified_gmt, 'weekly', '0.8');
    }
}

function fresh_render_merchant_feed_xml()
{
    if (! isset($_SERVER['REQUEST_URI']) || ! function_exists('fresh_product_price') || ! function_exists('fresh_product_image_url')) {
        return;
    }

    $request_path = wp_parse_url(sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI'])), PHP_URL_PATH);
    $home_path = wp_parse_url(home_url('/'), PHP_URL_PATH);

    if ($home_path && strpos($request_path, $home_path) === 0) {
        $request_path = substr($request_path, strlen($home_path));
    }

    if (trim((string) $request_path, '/') !== 'google-merchant-feed.xml') {
        return;
    }

    $products = get_posts([
        'post_type'      => 'fresh_product',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'orderby'        => 'modified',
        'order'          => 'DESC',
        'no_found_rows'  => true,
    ]);

    status_header(200);
    nocache_headers();
    header('Content-Type: application/xml; charset=' . get_bloginfo('charset'));

    echo '<?xml version="1.0" encoding="' . esc_attr(get_bloginfo('charset')) . '"?>' . "\n";
    echo '<rss version="2.0" xmlns:g="http://base.google.com/ns/1.0">' . "\n";
    echo '<channel>' . "\n";
    echo '<title>' . esc_html(get_bloginfo('name', 'display')) . '</title>' . "\n";
    echo '<link>' . esc_url(home_url('/')) . '</link>' . "\n";
    echo '<description>' . esc_html(fresh_seo_trim_text(get_bloginfo('description', 'display') ?: get_bloginfo('name', 'display'))) . '</description>' . "\n";

    foreach ($products as $product) {
        $price = fresh_product_price($product->ID);
        $description = get_post_meta($product->ID, '_fresh_product_seo_description', true);
        $description = $description ?: ($product->post_excerpt ?: $product->post_content);
        $sku = get_post_meta($product->ID, '_fresh_product_sku', true);
        $categories = get_the_terms($product->ID, 'fresh_product_category');
        $category_names = ! is_wp_error($categories) && $categories ? implode(' > ', wp_list_pluck($categories, 'name')) : '';

        echo '<item>' . "\n";
        echo '<g:id>' . esc_html($sku ?: $product->ID) . '</g:id>' . "\n";
        echo '<g:title>' . esc_html(get_the_title($product)) . '</g:title>' . "\n";
        echo '<g:description>' . esc_html(fresh_seo_trim_text($description, 500)) . '</g:description>' . "\n";
        echo '<g:link>' . esc_url(fresh_product_detail_url($product->ID)) . '</g:link>' . "\n";
        echo '<g:image_link>' . esc_url(fresh_product_image_url($product->ID, 'large')) . '</g:image_link>' . "\n";
        echo '<g:availability>in_stock</g:availability>' . "\n";
        echo '<g:price>' . esc_html(number_format((float) $price, 2, '.', '') . ' INR') . '</g:price>' . "\n";
        echo '<g:brand>' . esc_html(get_bloginfo('name', 'display')) . '</g:brand>' . "\n";
        echo '<g:condition>new</g:condition>' . "\n";

        if ($category_names) {
            echo '<g:product_type>' . esc_html($category_names) . '</g:product_type>' . "\n";
        }

        echo '</item>' . "\n";
    }

    echo '</channel>' . "\n";
    echo '</rss>';
    exit;
}
add_action('template_redirect', 'fresh_render_merchant_feed_xml', -11);

function fresh_sitemap_terms($taxonomy, $changefreq = 'weekly', $priority = '0.5')
{
    $terms = get_terms([
        'taxonomy'   => $taxonomy,
        'hide_empty' => false,
    ]);

    if (is_wp_error($terms)) {
        return;
    }

    foreach ($terms as $term) {
        $term_link = get_term_link($term);

        if (is_wp_error($term_link)) {
            continue;
        }

        fresh_sitemap_url_entry($term_link, fresh_sitemap_term_lastmod($term), $changefreq, $priority);
    }
}

function fresh_sitemap_term_lastmod($term)
{
    $post_types = $term->taxonomy === 'fresh_product_category' ? ['fresh_product'] : ['post'];
    $latest = get_posts([
        'post_type'      => $post_types,
        'post_status'    => 'publish',
        'posts_per_page' => 1,
        'orderby'        => 'modified',
        'order'          => 'DESC',
        'tax_query'      => [
            [
                'taxonomy' => $term->taxonomy,
                'field'    => 'term_id',
                'terms'    => $term->term_id,
            ],
        ],
        'no_found_rows'  => true,
    ]);

    return $latest ? $latest[0]->post_modified_gmt : get_lastpostmodified('GMT');
}

function fresh_render_sitemap_xml()
{
    if (! isset($_SERVER['REQUEST_URI'])) {
        return;
    }

    $request_path = wp_parse_url(sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI'])), PHP_URL_PATH);
    $home_path = wp_parse_url(home_url('/'), PHP_URL_PATH);

    if ($home_path && strpos($request_path, $home_path) === 0) {
        $request_path = substr($request_path, strlen($home_path));
    }

    if (trim((string) $request_path, '/') !== 'sitemap.xml') {
        return;
    }

    status_header(200);
    nocache_headers();
    header('Content-Type: application/xml; charset=' . get_bloginfo('charset'));

    echo '<?xml version="1.0" encoding="' . esc_attr(get_bloginfo('charset')) . '"?>' . "\n";
    echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

    fresh_sitemap_url_entry(home_url('/'), get_lastpostmodified('GMT'), 'daily', '1.0');
    fresh_sitemap_static_routes();
    fresh_sitemap_posts('page', 'monthly', '0.8');
    fresh_sitemap_posts('post', 'weekly', '0.7');
    fresh_sitemap_product_detail_urls();
    fresh_sitemap_terms('fresh_product_category', 'weekly', '0.6');
    fresh_sitemap_terms('category', 'weekly', '0.5');

    echo '</urlset>';
    exit;
}
add_action('template_redirect', 'fresh_render_sitemap_xml', -10);

function fresh_filter_core_sitemap_product_entry($sitemap_entry, $post)
{
    if (
        ! $post instanceof WP_Post ||
        $post->post_type !== 'fresh_product' ||
        ! function_exists('fresh_product_detail_url')
    ) {
        return $sitemap_entry;
    }

    $sitemap_entry['loc'] = fresh_product_detail_url($post->ID);

    return $sitemap_entry;
}
add_filter('wp_sitemaps_posts_entry', 'fresh_filter_core_sitemap_product_entry', 10, 2);

function fresh_filter_core_sitemap_posts_query_args($args, $post_type)
{
    if ($post_type === 'page') {
        $excluded_pages = get_posts([
            'post_type'      => 'page',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'fields'         => 'ids',
            'post_name__in'  => ['cart', 'checkout', 'wishlist', 'product-details'],
            'no_found_rows'  => true,
        ]);

        if ($excluded_pages) {
            $args['post__not_in'] = array_values(array_unique(array_merge(
                isset($args['post__not_in']) ? (array) $args['post__not_in'] : [],
                $excluded_pages
            )));
        }
    }

    return $args;
}
add_filter('wp_sitemaps_posts_query_args', 'fresh_filter_core_sitemap_posts_query_args', 10, 2);

function fresh_filter_core_sitemap_taxonomies_query_args($args, $taxonomy)
{
    if (in_array($taxonomy, ['category', 'fresh_product_category'], true)) {
        $args['hide_empty'] = false;
    }

    return $args;
}
add_filter('wp_sitemaps_taxonomies_query_args', 'fresh_filter_core_sitemap_taxonomies_query_args', 10, 2);

function fresh_add_sitemap_to_robots($output, $public)
{
    if ($public) {
        $output .= "\nAllow: /wp-admin/admin-ajax.php\n";
        $output .= "Disallow: /wp-admin/\n";
        $output .= "Disallow: /cart/\n";
        $output .= "Disallow: /checkout/\n";
        $output .= "Disallow: /wishlist/\n";
        $output .= "Disallow: /*?product_search=\n";
        $output .= "Disallow: /*?sort=\n";
        $output .= "Disallow: /*?fresh_add_to_cart=\n";
        $output .= "Disallow: /*?fresh_add_to_wishlist=\n";
        $output .= "Sitemap: " . home_url('/google-merchant-feed.xml') . "\n";
        $output .= "Sitemap: " . home_url('/sitemap.xml') . "\n";
    }

    return $output;
}
add_filter('robots_txt', 'fresh_add_sitemap_to_robots', 10, 2);
