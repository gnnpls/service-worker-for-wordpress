<?php
if (!defined('ABSPATH')) {
 exit;
}

if (!function_exists('wp_sw_library')) {
 function wp_sw_library()
 {
  return array(
   'enable_wp_sw' => array(
    'case' => 'input',
    'type' => 'checkbox',
    'label' => __('Enable service worker', 'wp-service-worker')
   )
  );
 }
}
