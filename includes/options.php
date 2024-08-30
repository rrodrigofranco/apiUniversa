<?php
defined('ABSPATH') or die('Not Authorized!');

function api_universa_add_admin_menu() {
    add_menu_page(
        'Universa',
        'Universa',
        'manage_options',
        'api-universa-admin',
        'api_universa_admin_page',
        'dashicons-welcome-learn-more'
    );
}
add_action('admin_menu', 'api_universa_add_admin_menu');

function api_universa_admin_enqueue_styles($hook) {
    if ($hook != 'toplevel_page_api-universa-admin') {
        return;
    }

    wp_enqueue_style(
        'api-universa-admin-style',
        API_UNIVERSA_DIRECTORY_URL . '/assets/css/style.css',
        array(),
        '1.0',
        'all'
    );
}
add_action('admin_enqueue_scripts', 'api_universa_admin_enqueue_styles');

function api_universa_admin_page() {
    include(API_UNIVERSA_DIRECTORY_PATH . 'admin/config.php');
}
