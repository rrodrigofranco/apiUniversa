<?php
defined('ABSPATH') or die('Not Authorized!');

include_once API_UNIVERSA_DIRECTORY_PATH . 'includes/Traits/ApiRequestTrait.php';
include_once API_UNIVERSA_DIRECTORY_PATH . 'includes/Services/ApiClientService.php';

use includes\Services\ApiClientService;

/**
 * Returns the main Service of ApiUniversa.
 *
 * @since  2.1
 * @return ApiClientService
 */

function ApiUniversa() {
	return new ApiClientService();
}

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
    include API_UNIVERSA_DIRECTORY_PATH . 'admin/config.php';
}

function schedule_token_verification() {
    $client = ApiUniversa();
    $email    = get_option('universa_email');
    $password = get_option('universa_password');
    $data = [
        'email' => $email,
        'password' => $password
    ];
    
    $data_token = $client->createData('/v1/auth/login', $data);

    if (isset($data_token["token"])) {
        $token = $data_token["token"];
        update_option('universa_email', $email);
        update_option('universa_password', $password);
        update_option('universa_auth_token', $token);
        return $token;
    }

    return false;
 }
 add_action( 'envento_verificar_token','schedule_token_verification' );
 
 wp_schedule_single_event( time() + 3600, 'envento_verificar_token' );