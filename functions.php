<?php
/**
 * IZIN Designs Theme functions.
 */

if (!defined('ABSPATH')) {
    exit;
}

require_once get_template_directory() . '/includes/izin-leads.php';
require_once get_template_directory() . '/includes/izin-careers.php';
require_once get_template_directory() . '/includes/izin-projects.php';
require_once get_template_directory() . '/includes/izin-video-section.php';

function izin_designs_theme_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo');
    add_theme_support('automatic-feed-links');
    add_theme_support('html5', array('search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script'));
    add_image_size('izin-featured-large', 1200, 675, true);
    add_image_size('izin-card', 600, 400, true);

    register_nav_menus(array(
        'primary' => __('Primary Menu', 'izin-designs-theme'),
        'footer' => __('Footer Menu', 'izin-designs-theme'),
    ));
}
add_action('after_setup_theme', 'izin_designs_theme_setup');

function izin_designs_theme_assets() {
    $theme_version = wp_get_theme()->get('Version');
    $styles_path = get_template_directory() . '/frontend/styles.css';
    $script_path = get_template_directory() . '/frontend/script.js';
    $styles_version = file_exists($styles_path) ? (string) filemtime($styles_path) : $theme_version;
    $script_version = file_exists($script_path) ? (string) filemtime($script_path) : $theme_version;

    wp_enqueue_style(
        'izin-designs-frontend',
        get_template_directory_uri() . '/frontend/styles.css',
        array(),
        $styles_version
    );

    wp_enqueue_script(
        'izin-designs-frontend',
        get_template_directory_uri() . '/frontend/script.js',
        array(),
        $script_version,
        true
    );
}
add_action('wp_enqueue_scripts', 'izin_designs_theme_assets');

function izin_designs_theme_activate() {
    if (function_exists('izin_leads_install')) {
        izin_leads_install();
    }

    if (function_exists('izin_careers_install')) {
        izin_careers_install();
    }

    if (function_exists('izin_projects_install')) {
        izin_projects_install();
    }
}
add_action('after_switch_theme', 'izin_designs_theme_activate');

function izin_designs_ensure_career_page() {
    if (get_page_by_path('career')) {
        return;
    }

    wp_insert_post(array(
        'post_title' => 'Career',
        'post_name' => 'career',
        'post_status' => 'publish',
        'post_type' => 'page',
    ));
}
add_action('init', 'izin_designs_ensure_career_page');

function izin_designs_bid_project_page_slug() {
    return 'bid-project';
}

function izin_designs_ensure_bid_project_page() {
    if (get_page_by_path(izin_designs_bid_project_page_slug())) {
        return;
    }

    wp_insert_post(array(
        'post_title'   => 'Bid Project',
        'post_name'    => izin_designs_bid_project_page_slug(),
        'post_status'  => 'publish',
        'post_type'    => 'page',
        'post_content' => '',
    ));
}
add_action('init', 'izin_designs_ensure_bid_project_page');

function izin_designs_project_status_page_slug() {
    return 'project-status';
}

function izin_designs_ensure_project_status_page() {
    if (get_page_by_path(izin_designs_project_status_page_slug())) {
        return;
    }

    wp_insert_post(array(
        'post_title'   => 'Project Status',
        'post_name'    => izin_designs_project_status_page_slug(),
        'post_status'  => 'publish',
        'post_type'    => 'page',
        'post_content' => '',
    ));
}
add_action('init', 'izin_designs_ensure_project_status_page');

function izin_designs_package_page_slug() {
    return '3bhk-interior-package-kochi-aluva';
}

function izin_designs_ensure_package_page() {
    if (get_page_by_path(izin_designs_package_page_slug())) {
        return;
    }

    wp_insert_post(array(
        'post_title'   => '3BHK Interior Package',
        'post_name'    => izin_designs_package_page_slug(),
        'post_status'  => 'publish',
        'post_type'    => 'page',
        'post_content' => '',
    ));
}
add_action('init', 'izin_designs_ensure_package_page');

function izin_designs_body_classes($classes) {
    if (is_single()) {
        $classes[] = 'izin-single-post';
    }

    if (is_archive() || is_home()) {
        $classes[] = 'izin-archive-view';
    }

    if (is_search()) {
        $classes[] = 'izin-search-view';
    }

    if (is_home() || is_category()) {
        $classes[] = 'izin-insights-view';
    }

    return $classes;
}
add_filter('body_class', 'izin_designs_body_classes');

function izin_designs_is_package_page() {
    return is_page(izin_designs_package_page_slug());
}

function izin_designs_is_project_status_page() {
    return is_page(izin_designs_project_status_page_slug());
}

function izin_designs_package_seo_title() {
    return '3BHK Interior Package in Kochi & Aluva | ₹4,99,999 | Izin Designs';
}

function izin_designs_package_meta_description() {
    return 'Get a complete 3BHK interior package in Kochi and Aluva for ₹4,99,999. Includes WPC kitchen, wardrobes, beds, sofa, TV unit and centre table by Izin Designs Interior Studio.';
}

function izin_designs_document_title_parts($title_parts) {
    if (izin_designs_is_package_page()) {
        $title_parts['title'] = izin_designs_package_seo_title();
    }

    return $title_parts;
}
add_filter('document_title_parts', 'izin_designs_document_title_parts');

function izin_designs_rank_math_title($title) {
    if (is_front_page()) {
        return 'Interior Designers in Kochi | IZIN Designs Interior Studio';
    }

    if (izin_designs_is_package_page()) {
        return izin_designs_package_seo_title();
    }

    return $title;
}
add_filter('rank_math/frontend/title', 'izin_designs_rank_math_title');

function izin_designs_rank_math_description($description) {
    if (is_front_page()) {
        return 'Custom home and commercial interior design in Kochi and Aluva, Kerala. Modular kitchens, bespoke furniture, turnkey execution and consultation.';
    }

    if (izin_designs_is_package_page()) {
        return izin_designs_package_meta_description();
    }

    return $description;
}
add_filter('rank_math/frontend/description', 'izin_designs_rank_math_description');

function izin_designs_package_head_meta() {
    if (!izin_designs_is_package_page()) {
        return;
    }

    $title = izin_designs_package_seo_title();
    $description = izin_designs_package_meta_description();
    $url = get_permalink();
    ?>
    <?php if (!defined('RANK_MATH_VERSION')) : ?>
    <meta name="description" content="<?php echo esc_attr($description); ?>">
    <link rel="canonical" href="<?php echo esc_url($url); ?>">
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?php echo esc_attr($title); ?>">
    <meta property="og:description" content="<?php echo esc_attr($description); ?>">
    <meta property="og:url" content="<?php echo esc_url($url); ?>">
    <meta property="og:image" content="https://izindesigns.com/wp-content/uploads/2026/06/Cover-Page.png">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo esc_attr($title); ?>">
    <meta name="twitter:description" content="<?php echo esc_attr($description); ?>">
    <meta name="twitter:image" content="https://izindesigns.com/wp-content/uploads/2026/06/Cover-Page.png">
    <?php endif; ?>
    <script type="application/ld+json">
    <?php
    echo wp_json_encode(array(
        '@context'   => 'https://schema.org',
        '@type'      => 'FAQPage',
        'mainEntity' => array(
            array(
                '@type'          => 'Question',
                'name'           => 'What is the price of the 3BHK interior package?',
                'acceptedAnswer' => array(
                    '@type' => 'Answer',
                    'text'  => 'The 3BHK interior package price is ₹4,99,999.',
                ),
            ),
        ),
    ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    ?>
    </script>
    <?php
}
add_action('wp_head', 'izin_designs_package_head_meta', 5);

function izin_designs_thin_page_slugs() {
    return array('draft-blog-page', 'elementor-page-580', izin_designs_project_status_page_slug());
}

function izin_designs_is_thin_index_page() {
    if (!is_page()) {
        return false;
    }

    $post = get_queried_object();

    return $post instanceof WP_Post && in_array($post->post_name, izin_designs_thin_page_slugs(), true);
}

function izin_designs_wp_robots($robots) {
    if (izin_designs_is_thin_index_page() || izin_designs_is_project_status_page()) {
        $robots['index'] = false;
        $robots['follow'] = false;
        $robots['noimageindex'] = true;
    } elseif (!is_admin() && !is_404()) {
        $robots['max-image-preview'] = 'large';
    }

    return $robots;
}
add_filter('wp_robots', 'izin_designs_wp_robots');

function izin_designs_rank_math_robots($robots) {
    if (izin_designs_is_thin_index_page() || izin_designs_is_project_status_page()) {
        return array('noindex', 'nofollow', 'noimageindex');
    }

    if (is_admin() || is_404()) {
        return $robots;
    }

    if (!is_array($robots)) {
        $robots = array();
    }

    if (!in_array('max-image-preview:large', $robots, true)) {
        $robots[] = 'max-image-preview:large';
    }

    return $robots;
}
add_filter('rank_math/frontend/robots', 'izin_designs_rank_math_robots');

function izin_designs_rank_math_sitemap_entry($url, $type, $object) {
    if ($type !== 'post' || !($object instanceof WP_Post)) {
        return $url;
    }

    if ($object->post_type === 'page' && in_array($object->post_name, izin_designs_thin_page_slugs(), true)) {
        return false;
    }

    return $url;
}
add_filter('rank_math/sitemap/entry', 'izin_designs_rank_math_sitemap_entry', 10, 3);

function izin_designs_filter_rank_math_schema($data, $jsonld) {
    if (!is_front_page() || !is_array($data)) {
        return $data;
    }

    foreach ($data as $key => $entity) {
        if (!is_array($entity) || empty($entity['@type'])) {
            continue;
        }

        $type = $entity['@type'];

        if ((is_string($type) && $type === 'Article') || (is_array($type) && in_array('Article', $type, true))) {
            unset($data[$key]);
        }
    }

    if (function_exists('izin_designs_homepage_videos')) {
        foreach (izin_designs_homepage_videos() as $index => $video) {
            $entry = array(
                '@type'            => 'VideoObject',
                '@id'              => home_url('/#' . $video['slug']),
                'name'             => $video['title'],
                'description'      => $video['description'],
                'thumbnailUrl'     => array($video['thumbnail_url']),
                'isFamilyFriendly' => true,
                'publisher'        => array(
                    '@type' => 'Organization',
                    'name'  => 'IZIN Designs Interior Studio',
                    'url'   => home_url('/'),
                    'logo'  => array(
                        '@type' => 'ImageObject',
                        'url'   => 'https://izindesigns.com/wp-content/uploads/2026/05/cropped-Izin-Design-Interior-Studio-1-63x64.png',
                    ),
                ),
            );

            if (!empty($video['watch_url'])) {
                $entry['url'] = $video['watch_url'];
                $entry['mainEntityOfPage'] = $video['watch_url'];
            } else {
                $entry['url'] = home_url('/#' . $video['slug']);
                $entry['mainEntityOfPage'] = home_url('/');
            }

            if (!empty($video['embed_url'])) {
                $entry['embedUrl'] = $video['embed_url'];
            }

            if (!empty($video['content_url'])) {
                $entry['contentUrl'] = $video['content_url'];
            }

            $data['izin_home_video_' . $index] = $entry;
        }
    }

    return $data;
}
add_filter('rank_math/json_ld', 'izin_designs_filter_rank_math_schema', 20, 2);

function izin_designs_homepage_video_schema() {
    if (!is_front_page() || !function_exists('izin_designs_homepage_videos')) {
        return;
    }

    $graph = array();

    foreach (izin_designs_homepage_videos() as $video) {
        $entry = array(
            '@type'        => 'VideoObject',
            'name'         => $video['title'],
            'description'  => $video['description'],
            'thumbnailUrl' => array($video['thumbnail_url']),
            'url'          => !empty($video['watch_url']) ? $video['watch_url'] : home_url('/#' . $video['slug']),
        );

        if (!empty($video['embed_url'])) {
            $entry['embedUrl'] = $video['embed_url'];
        }

        if (!empty($video['content_url'])) {
            $entry['contentUrl'] = $video['content_url'];
        }

        $graph[] = $entry;
    }
    ?>
    <script type="application/ld+json">
    <?php
    echo wp_json_encode(
        array(
            '@context' => 'https://schema.org',
            '@graph'   => $graph,
        ),
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
    );
    ?>
    </script>
    <?php
}
add_action('wp_head', 'izin_designs_homepage_video_schema', 30);

function izin_designs_get_primary_term_name($post_id = 0) {
    $post_id = $post_id ?: get_the_ID();
    $terms = get_the_category($post_id);

    if (!$terms || is_wp_error($terms)) {
        return '';
    }

    return $terms[0]->name;
}

function izin_designs_get_post_image_alt($attachment_id, $fallback = '') {
    $alt = trim((string) get_post_meta($attachment_id, '_wp_attachment_image_alt', true));

    if ($alt !== '') {
        return $alt;
    }

    return $fallback;
}

function izin_designs_render_breadcrumbs() {
    if (function_exists('rank_math_the_breadcrumbs')) {
        rank_math_the_breadcrumbs();
        return;
    }

    get_template_part('template-parts/breadcrumbs');
}
