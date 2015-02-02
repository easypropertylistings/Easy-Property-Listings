<?php
/**
 * Scripts & Styles
 *
 * @package     EPL
 * @subpackage  Scripts/Styles
 * @copyright   Copyright (c) 2014, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Load and enqueue admin scripts and stylesheets
 */
function epl_admin_enqueue_scripts($screen) {
	$current_dir_path = plugins_url('', __FILE__ );
	
	wp_enqueue_script( 'epl-admin-scripts', $current_dir_path . '/js/jquery-admin-scripts.js', array('jquery') );
	wp_enqueue_style( 'epl-admin-styles', $current_dir_path . '/css/style-admin.css','',EPL_PROPERTY_VER );
	
	if( $screen == 'post.php' || $screen == 'post-new.php' || $screen == 'toplevel_page_epl-general' ) {
		
		wp_enqueue_style(	'epl-jquery-validation-engine-style', $current_dir_path . '/css/validationEngine-jquery.css' );
		wp_enqueue_script(	'epl-jquery-validation-engine-lang-scripts', $current_dir_path . '/js/jquery-validationEngine-en.js', array('jquery') );
		wp_enqueue_script(	'epl-jquery-validation-engine-scripts', $current_dir_path . '/js/jquery-validationEngine.js', array('jquery') );
		
		wp_enqueue_script(	'jquery-datetime-picker',$current_dir_path . '/js/jquery-datetime-picker.js', array('jquery') );
		wp_enqueue_style(	'jquery-ui-datetime-picker-style',  $current_dir_path . '/css/jquery-ui.min.css');
	}	
}
add_action( 'admin_enqueue_scripts', 'epl_admin_enqueue_scripts' );

/**
 * Load and enqueue front end scripts and stylesheets
 */
function epl_wp_enqueue_scripts() {
	global $epl_settings;
	$epl_default_view_type = isset($epl_settings['display_archive_view_type'])?$epl_settings['display_archive_view_type'] : 'list';
	$current_dir_path = plugins_url('', __FILE__ );
	wp_register_script( 'epl-front-scripts', $current_dir_path . '/js/jquery-front-scripts.js', array('jquery') );
	wp_enqueue_style( 'epl-front-styles', $current_dir_path . '/css/style-front.css' ,'',EPL_PROPERTY_VER);
	$js_vars = array( 'epl_default_view_type' => $epl_default_view_type );
	wp_localize_script( 'epl-front-scripts', 'epl_frontend_vars', $js_vars);
	wp_enqueue_script( 'epl-front-scripts');
}
add_action( 'wp_enqueue_scripts', 'epl_wp_enqueue_scripts' );


