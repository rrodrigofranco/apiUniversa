<?php

/**
 * Plugin Name: Api Universa
 * Plugin URI: https://versatecnologia.com.br
 * Description: Este plugin do WordPress permite realizar autenticação e consultas à API da Universa.
 * Version: 1.0.0
 * Author: Gleidson
 * Author URI: https://versatecnologia.com.br
 * Text Domain: api-universa
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

// Block direct access to file
defined('ABSPATH') or die('Not Authorized!');

// Plugin Defines
define('API_UNIVERSA_FILE', __FILE__);
define('API_UNIVERSA_DIRECTORY', dirname(__FILE__));
define('API_UNIVERSA_BASE', 'https://universa-api.universaeducacional.com.br/centec');
define('API_UNIVERSA_TEXT_DOMAIN', 'api-universa');
define('API_UNIVERSA_DIRECTORY_BASENAME', plugin_basename(API_UNIVERSA_FILE));
define('API_UNIVERSA_DIRECTORY_PATH', plugin_dir_path(API_UNIVERSA_FILE));
define('API_UNIVERSA_DIRECTORY_URL', plugins_url('', API_UNIVERSA_FILE));

require_once(API_UNIVERSA_DIRECTORY_PATH . 'includes/options.php');

register_activation_hook(API_UNIVERSA_FILE, 'api_universa_plugin_activation');
register_deactivation_hook(API_UNIVERSA_FILE, 'api_universa_plugin_deactivation');

function api_universa_plugin_activation() {
    flush_rewrite_rules();
}

function api_universa_plugin_deactivation() {
    flush_rewrite_rules();
}