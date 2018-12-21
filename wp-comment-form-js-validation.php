<?php
/**
 * Plugin Name: WP Comment Form Js Validation
 * Plugin URI: 
 * Text Domain: wp-comment-form-js-validation
 * Domain Path: /languages/
 * Description: This plugin use for wordpress comments js validation to the comment form.
 * Version:     1.0
 * Author:      Urmil Patel
 * Author URI:  https://profiles.wordpress.org/urmilwp
 * License:     
 * License URI: 
 * Version: 1.0
 * 
 * @package WordPress
 * @author Urmil Patel
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Basic plugin definitions
 * 
 * @package WP Comment Form Js Validation
 * @since 1.0.0
 */
if( !defined( 'WPCJS_VERSION' ) ) {
	define( 'WPCJS_VERSION', '1.0.0' ); // Version of plugin
}
register_activation_hook( __FILE__, 'wpcjs_install' );
function wpcjs_install() {
	if( is_plugin_active('wp-comment-form-js-validation/wp-comment-form-js-validation.php') ) {
		add_action('update_option_active_plugins', 'wpcjs_deactivate_version');
	}
}

function wpcjs_deactivate_version() {
   deactivate_plugins('wp-comment-form-js-validation/wp-comment-form-js-validation.php',true);
}

function wpcjs_style_css_script() {
	if(is_single() && comments_open() ) {
		wp_enqueue_style( 'csscomment',  plugin_dir_url( __FILE__ ). 'assets/css/stylecomment.css', array(), WPCJS_VERSION );
		wp_enqueue_script( 'up-jquery-validate', plugin_dir_url( __FILE__ ) . 'assets/js/jquery-comment-validate.min.js', array( 'jquery' ), WPCJS_VERSION);
		wp_enqueue_script( 'up-comment-public', plugin_dir_url( __FILE__ ) . 'assets/js/up-comment-public.js', array( 'jquery' ), WPCJS_VERSION);
	}
}
add_action( 'wp_enqueue_scripts', 'wpcjs_style_css_script' );
