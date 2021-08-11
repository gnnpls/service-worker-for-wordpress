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
  add_action('wp_head', array($this, 'wp_sw_header'), PHP_INT_MAX);
 }

 /**
  * add the script to check for worker
  */
 public function wp_sw_header()
 {
?>
  <script>
   if ('serviceWorker' in navigator) {
    window.addEventListener('load', function() {
     if (!document.body.classList.contains('logged-in')) {
      navigator.serviceWorker.register('/service-worker.js');
     }

     // ensure service worker is ready, you can also put this into DOM 'ready' or 'load' event
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

new WP_Service_Worker();
