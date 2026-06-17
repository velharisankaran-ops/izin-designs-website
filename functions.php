<?php
/**
 * IZIN Designs Theme functions.
 */

if (!defined('ABSPATH')) {
    exit;
}

require_once get_template_directory() . '/includes/izin-leads.php';
require_once get_template_directory() . '/includes/izin-careers.php';

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

function izin_designs_rank_math_home_title($title) {
    if (is_front_page()) {
        return 'Interior Designers in Kochi | IZIN Designs Interior Studio';
    }

    return $title;
}
add_filter('rank_math/frontend/title', 'izin_designs_rank_math_home_title');

function izin_designs_rank_math_home_description($description) {
    if (is_front_page()) {
        return 'Custom home and commercial interior design in Kochi and Aluva, Kerala. Modular kitchens, bespoke furniture, turnkey execution and consultation.';
    }

    return $description;
}
add_filter('rank_math/frontend/description', 'izin_designs_rank_math_home_description');

function izin_designs_thin_page_slugs() {
    return array('draft-blog-page', 'elementor-page-580');
}

function izin_designs_is_thin_index_page() {
    if (!is_page()) {
        return false;
    }

    $post = get_queried_object();

    return $post instanceof WP_Post && in_array($post->post_name, izin_designs_thin_page_slugs(), true);
}

function izin_designs_wp_robots($robots) {
    if (izin_designs_is_thin_index_page()) {
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
    if (izin_designs_is_thin_index_page()) {
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

    return $data;
}
add_filter('rank_math/json_ld', 'izin_designs_filter_rank_math_schema', 20, 2);

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
