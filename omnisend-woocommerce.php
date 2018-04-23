<?php
/**
 * Plugin Name: Omnisend for Woocommerce
 * Plugin URI: https://omnisend.com
 * Description: Omnisend integration extension for Woocommerce 
 * Version: 1.0.0
 * Author: Omnisend
 * Author URI: https://omnisend.com
 * Developer: Omnisend
 * Developer URI: https://omnisend.com
 * Text Domain: omnisend-woocommerce
 *
 * WC requires at least: 2.2
 * WC tested up to: 3.3.5
 *
 * Copyright: © 2018 Omnisend
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/*Include OmnisendHelper class*/
require_once 'Manager/OmnisendHelper.php';
/*Include OmnisendManager class*/
require_once 'Manager/OmnisendManager.php';
/*Include Model classes*/
require_once 'Model/OmnisendProduct.php';
require_once 'Model/OmnisendContact.php';
require_once 'Model/OmnisendCart.php';
require_once 'Model/OmnisendOrder.php';
/*Include Ajax functions*/
require_once 'omnisend-ajax.php';
/*Include settings page display function*/
require_once 'omnisend-settings-page.php';
/*Include Woocommerce hooks*/
require_once 'omnisend-woocommerce-hooks.php';

/*Declare plugin's settings page*/
add_action( 'admin_menu', 'omnisend_woocommerce_menu' );
function omnisend_woocommerce_menu(){

	$page_title = 'Omnisend for Woocommerce';
	$menu_title = 'Omnisend';
	$capability = 'manage_options';
	$menu_slug  = 'omnisend-woocommerce';
	$function   = 'omnisend_settings_page';	
	$icon_url   = plugin_dir_url ( __FILE__ ) . 'assets/img/omnisend-icon.png';
	
	$position   = 2;

	add_menu_page( $page_title,
	                 $menu_title,
	                 $capability,
	                 $menu_slug,
	                 $function,
	                 $icon_url,
	                 $position );

}

/*Include scripts and styles for settings page*/
add_action('admin_enqueue_scripts', 'omnisend_admin_scripts_and_styles');
function omnisend_admin_scripts_and_styles() {
	if($_GET['page'] == 'omnisend-woocommerce')
    {
    	wp_enqueue_script('omnisend-admin-script.js', plugin_dir_url ( __FILE__ ) . 'assets/js/omnisend-admin-script.js?' . time(), array(), '1.0.0', true );
    	wp_enqueue_style('omnisend-admin-style.css', plugin_dir_url ( __FILE__ )  . 'assets/css/omnisend-admin-style.css?' . time() );
    }
}

/*Include scripts and styles for frontend*/
add_action( 'wp_enqueue_scripts', 'omnisend_scripts_and_styles' );
function omnisend_scripts_and_styles(){
	wp_enqueue_script( 'omnisend-frontend-script.js', plugin_dir_url ( __FILE__ ) . 'assets/js/omnisend-frontend-script.js?' . time(), array(), '1.0.0', true );
}

/*After plugin activation - go to settings page*/
add_action( 'activated_plugin', 'omnisend_activation_redirect' );
function omnisend_activation_redirect( $plugin ) {
    if( $plugin == plugin_basename( __FILE__ ) ) {
        exit( wp_redirect( admin_url( 'admin.php?page=omnisend-woocommerce' ) ) );
    }
}