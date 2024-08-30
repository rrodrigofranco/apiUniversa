<?php
// Settings menu creation
function check_sellers_admin_menu() {
    add_menu_page( 'Universa', 'Universa','manage_options', UNI_ROUTE . '/admin/config.php', '', 'dashicons-welcome-learn-more');
}
add_action( 'admin_menu', 'check_sellers_admin_menu' );