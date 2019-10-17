<?php
/**
 * Plugin Name:		   Owl Carousel WP
 * Plugin URI:		   https://www.themescode.com/items/owl-carousel-wp-pro/
 * Description:		   Owl carousel wp is a WordPress Carousel plugin based on owl carousel . You can add Image carousel in any WordPress Website. It is a responsive carousel plugin works perfectly in any Device Screen.Add Image through custom post type and have category to pull image in the carousel from any specific category.owl carousel wp is an easy plugin works using shortcode .
 * Version: 		     2.2.1
 * Author: 			     themescode
 * Author URI: 		   https://www.themescode.com/items/owl-carousel-wp-pro/
 * Text Domain:      owl-carousel-wp
 * License:          GPL-2.0+
 * License URI:      http://www.gnu.org/licenses/gpl-2.0.txt
 * License: GPL2
 */
// include files

/**
 * Protect direct access
 */

if( ! defined( 'TCOWLCAROUSEL_HACK_MSG' ) ) define( 'TCOWLCAROUSEL_HACK_MSG', __( 'Sorry ! You made a mistake !', 'owl-carousel-wp' ) );
if ( ! defined( 'ABSPATH' ) ) die( TCOWLCAROUSEL_HACK_MSG );

/**
 * Defining constants
*/

if( ! defined( 'TCOWLCAROUSEL_PLUGIN_DIR' ) ) define( 'TCOWLCAROUSEL_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
if( ! defined( 'TCOWLCAROUSEL_PLUGIN_URI' ) ) define( 'TCOWLCAROUSEL_PLUGIN_URI', plugins_url( '', __FILE__ ) );


 include(plugin_dir_path( __FILE__ ).'/lib/cpt.php');
 include(plugin_dir_path( __FILE__ ).'/public/view.php');
 // Scripts
   function themescode_ocw_carousel_enqueue_scripts() {
      // Vendors style & scripts
       wp_enqueue_style('owl.carousel', TCOWLCAROUSEL_PLUGIN_URI.'/vendors/owl-carousel/assets/owl.carousel.min.css');
       wp_enqueue_script('owl-carousel', TCOWLCAROUSEL_PLUGIN_URI.'/vendors/owl-carousel/owl.carousel.min.js', array('jquery'), 1.0, true);
      //Plugin Main CSS File
       wp_enqueue_style( 'tc-owmcarousel-style', TCOWLCAROUSEL_PLUGIN_URI.'/vendors/custom/style.custom.css');
    }

   add_action( 'wp_enqueue_scripts', 'themescode_ocw_carousel_enqueue_scripts' );

   // Adding Admin script

   add_action( 'admin_enqueue_scripts', 'tc_owlcarousel_admin_style' );

   function tc_owlcarousel_admin_style() {

    wp_enqueue_style( 'tc_owlcarousel_admin', TCOWLCAROUSEL_PLUGIN_URI.'/assets/css/tc-owl-carousel-admin.css');

   }

  //  Setting API
   require_once TCOWLCAROUSEL_PLUGIN_DIR  .'/lib/setting/tc-owlcarousel-settings-api.php';
   require_once TCOWLCAROUSEL_PLUGIN_DIR .'/lib/setting/tc-owlcarousel-settings.php';

   new TC_Owlcarousel_Settings_API_Test();
 // Scripts

  require_once TCOWLCAROUSEL_PLUGIN_DIR .'/lib/tc_exlink.php';

 if ( function_exists( 'add_theme_support' ) ) {
     add_theme_support( 'post-thumbnails' );
 }

 /* Move Featured Image Below Title */

 function move_featured_image_box() {
     remove_meta_box( 'postimagediv', 'ocw_carousel', 'side' );
     add_meta_box('postimagediv', __('Featured Image'), 'post_thumbnail_meta_box', 'ocw_carousel', 'normal', 'high');

 }
 add_action('do_meta_boxes', 'move_featured_image_box');


 // add submenu page

 add_action('admin_menu', 'tc_ocw_carousel_menu_init');



 function tc_ocw_carousel_menu_help(){
   include('lib/tc-ocw_carousel-help-upgrade.php');
 }

 function tc_ocw_carousel_menu_init()
   {

     add_submenu_page('edit.php?post_type=ocw_carousel', __('Help & Upgrade','owl-carousel-wp'), __('Help & Upgrade','owl-carousel-wp'), 'manage_options', 'tc_ocw_carousel_menu_help', 'tc_ocw_carousel_menu_help');

   }


include('lib/tc-owlcarousel-column.php');

// After Plugin Activation redirect

 if( !function_exists( 'tc_owlc_activation_redirect' ) ){
   function tc_owlc_activation_redirect( $plugin ) {
       if( $plugin == plugin_basename( __FILE__ ) ) {
           exit( wp_redirect( admin_url( 'edit.php?post_type=ocw_carousel&page=tc_ocw_carousel_menu_help' ) ) );
       }
   }
 }
 add_action( 'activated_plugin', 'tc_owlc_activation_redirect' );


// adding link
add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'tc_owl_plugin_action_links' );

function tc_owl_plugin_action_links( $links ) {
   $links[] = '<a href="https://www.themescode.com/items/owl-carousel-wp-pro/" target="_blank">Pro Version</a>';
   $links[] = '<a href="https://www.themescode.com/items/category/wordpress-plugins" target="_blank">TC Plugins</a>';
   return $links;
}
