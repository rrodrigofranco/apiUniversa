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

function verifyToken($email, $password){
    $url = API_UNIVERSA_BASE ."/v1/auth/login";
    
    $data = [
        'email' => $email,
        'password' => $password
    ];
  
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data, true));
  
    $response = curl_exec($ch);
     
    if (curl_errno($ch)) {
        echo 'cURL error: ' . curl_error($ch);
    } else {
      $response_data = json_decode($response, true);
      if (isset($response_data["token"])) {
          $token = $response_data["token"];
          
          update_option('universa_email', $email);
          update_option('universa_password', $password);
          update_option('universa_auth_token', $token);
          return $token;
      }
  
    }
      curl_close($ch);
  
      return false;
}

function api_universa_admin_page() {
    include(API_UNIVERSA_DIRECTORY_PATH . 'admin/config.php');
}

function schedule_token_verification() {
    $email    = get_option('universa_email');
    $password = get_option('universa_password');

    verifyToken($email, $password);
 }
 add_action( 'envento_verificar_token','schedule_token_verification' );
 
 wp_schedule_single_event( time() + 30, 'envento_verificar_token' );