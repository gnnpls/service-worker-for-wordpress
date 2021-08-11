<?php
/*
Plugin Name: WP Service Worker
Plugin URI: https://github.com/gnnpls/service-worker-for-wordpress
Description: Service Worker Plugin for wordpress
Version: 1
Author: Giannopoulos Nikolaos
Author URI: https://motivar.io
Text Domain: wp-service-worker
 */

if (!defined('WPINC')) {
 die;
}

define('wp_sw_url', plugin_dir_url(__FILE__));
define('wp_sw_path', plugin_dir_path(__FILE__));
define('wp_sw_version', 1);

require_once 'includes/init.php';
