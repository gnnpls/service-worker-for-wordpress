<?php
if (!defined('ABSPATH')) {
 exit;
}
/**
 *
 */

class WP_Service_Worker
{

 public function __construct()
 {
  require_once 'service_worker_functions.php';
  //add_action('wp_head', array($this, 'wp_sw_header'), PHP_INT_MAX);
  add_filter('awm_add_options_boxes_filter', array($this, 'wp_sw_options'));
  add_filter('admin_init', array($this, 'wp_sw_create_file'), PHP_INT_MAX);
 }

 public function wp_sw_notice()
 { ?>

  <div class="notice notice-success is-dismissible">
   <p><?php _e('Congratulations, you did it!', 'shapeSpace'); ?></p>
  </div>

  <?php

 }


 public function wp_sw_create_file()
 {
  $enabled = get_option('enable_wp_sw') ?: false;
  $version = get_option('wp_sw_file_version') ?: false;
  if ($enabled) {
   if ($version != wp_sw_version) {
    $WP_Filesystem_Direct = new WP_Filesystem_Direct(null);
    @$writer = $WP_Filesystem_Direct->copy(wp_sw_path . '/assets/js/service-worker.js', ABSPATH . 'service-worker.js', true, 0644);

    if (!$writer) {
     echo 'nikos';
    }
    die();
    $msg = $class = '';
    if ($writer) {
    }
    add_action('admin_notices', array($this, 'wp_sw_notice'));
   }
  }
 }

 /**
  * add the options page
  */
 public function wp_sw_options($boxes)
 {
  $boxes['wp_sw_options'] = array(
   'title' => __('WP Service Worker Settings', 'wp-service-worker'),
   'library' => wp_sw_library(),
   'parent' => '',
   'cap' => 'update_core',
   'explanation' => __('Set up the settings below in order to activate/deactivate amp', 'wp-service-worker'),
  );
  return $boxes;
 }



 /**
  * add the script to check for worker
  */
 public function wp_sw_header()
 {
  $enabled = get_option('enable_wp_sw') ?: false;
  $version = get_option('wp_sw_file_version') ?: false;

  if ($enabled && $version) {
  ?>
   <script>
    if ('serviceWorker' in navigator) {
     window.addEventListener('load', function() {
      if (!document.body.classList.contains('logged-in')) {
       navigator.serviceWorker.register('/service-worker.js');
      }
      /*navigator.serviceWorker.ready.then(function(reg) {
       var links = document.querySelectorAll('a:not([target="_blank"]):not([href=""]):not([href="#"])');
       var cached = [];
       if (links) {
        console.log(baseurl);
        links.forEach(function(link) {
         var href = link.getAttribute('href');

         if (href != '' && !href.match(/wp-admin/) && href.match(baseurl) && !href.match("wp-login") && !href.match("tatsu") && !href.match("wp-logout") && !cached.includes(href) && !href.match("mailto")) {
          var data = {
           action: "precache",
           url: href // if you had more than an url need to be cached, do a loop to collect all data
          };
          var jsonData = JSON.stringify(data);
          if (jsonData) {
           navigator.serviceWorker.controller.postMessage(jsonData);
           cached.push(href);
          }
         }
        });
       }
      });*/
     });
    }
   </script>
<?php
  }
 }
}

new WP_Service_Worker();
