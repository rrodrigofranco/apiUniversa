<?php
// Settings menu creation
function check_sellers_admin_menu() {
    add_menu_page( 'Universa', 'Universa','manage_options', UNI_ROUTE . '/admin/config.php', '', 'dashicons-welcome-learn-more');
}
add_action( 'admin_menu', 'check_sellers_admin_menu' );

function create_token_table() {
    global $wpdb;

    $table_name = $wpdb->prefix . 'auth_tokens'; // Define the table name
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        email varchar(255) NOT NULL,
        token text NOT NULL,
        created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

add_action('after_switch_theme', 'create_token_table');
