<?php
/**
 * IZIN Designs Theme functions.
 */

if (!defined('ABSPATH')) {
    exit;
}

function izin_designs_theme_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo');
    add_theme_support('html5', array('search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script'));

    register_nav_menus(array(
        'primary' => __('Primary Menu', 'izin-designs-theme'),
        'footer' => __('Footer Menu', 'izin-designs-theme'),
    ));
}
add_action('after_setup_theme', 'izin_designs_theme_setup');

function izin_designs_theme_assets() {
    $theme_version = wp_get_theme()->get('Version');

    wp_enqueue_style(
        'izin-designs-frontend',
        get_template_directory_uri() . '/frontend/styles.css',
        array(),
        $theme_version
    );

    wp_enqueue_script(
        'izin-designs-frontend',
        get_template_directory_uri() . '/frontend/script.js',
        array(),
        $theme_version,
        true
    );
}
add_action('wp_enqueue_scripts', 'izin_designs_theme_assets');
