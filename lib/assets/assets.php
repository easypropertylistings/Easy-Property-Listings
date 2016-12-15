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

	if( $screen == 'post.php' || $screen == 'post-new.php' || $screen == 'easy-property-listings_page_epl-extensions' ||  $screen == 'easy-property-listings_page_epl-settings' ||  $screen= 'easy-property-listings_page_epl-extensions') {

		$googleapiurl = 'https://maps.googleapis.com/maps/api/js?v=3.exp';

		if( epl_get_option('epl_google_api_key') != '' ) {
			$googleapiurl = $googleapiurl.'&key='.epl_get_option('epl_google_api_key');
		}

		wp_enqueue_script('google-map-v-3',$googleapiurl);


		wp_enqueue_style(	'epl-jquery-validation-engine-style', 		$current_dir_path . '/css/validationEngine-jquery.css',	FALSE,			EPL_PROPERTY_VER );
		wp_enqueue_script(	'epl-jquery-validation-engine-lang-scripts', 	$current_dir_path . '/js/jquery-validationEngine-en.js',array('jquery'),	EPL_PROPERTY_VER );
		wp_enqueue_script(	'epl-jquery-validation-engine-scripts', 	$current_dir_path . '/js/jquery-validationEngine.js', 	array('jquery'),	EPL_PROPERTY_VER );
		wp_enqueue_script(	'jquery-datetime-picker',			$current_dir_path . '/js/jquery-datetime-picker.js', 	array('jquery'),	EPL_PROPERTY_VER );
		wp_enqueue_style(	'jquery-ui-datetime-picker-style',  		$current_dir_path . '/css/jquery-ui.min.css',		FALSE,			EPL_PROPERTY_VER );
		wp_enqueue_script( 	'epl-admin-scripts', 				$current_dir_path . '/js/jquery-admin-scripts.js', 	array('jquery'),	EPL_PROPERTY_VER );
		wp_enqueue_style( 	'epl-admin-styles', 				$current_dir_path . '/css/style-admin.css',		FALSE,			EPL_PROPERTY_VER );

	}

	// load admin style on help & documentation pages as well
	if($screen = 'edit.php' || $screen == 'toplevel_page_epl-general' || $screen == 'dashboard_page_epl-about' || $screen == 'dashboard_page_epl-getting-started')	{
		wp_enqueue_style(	'epl-admin-styles', 				$current_dir_path . '/css/style-admin.css',		FALSE,			EPL_PROPERTY_VER );
	}

	wp_enqueue_script(		'epl-js-lib', 					$current_dir_path . '/js/epl.js', 			array('jquery') , 	EPL_PROPERTY_VER );
}
add_action( 'admin_enqueue_scripts', 'epl_admin_enqueue_scripts' );

/**
 * Load and enqueue front end scripts and stylesheets
 */
function epl_wp_enqueue_scripts() {
	global $epl_settings;
	$epl_default_view_type = isset($epl_settings['display_archive_view_type']) ? $epl_settings['display_archive_view_type'] : 'list';
	$current_dir_path = plugins_url('', __FILE__ );
	wp_register_script( 		'epl-front-scripts', 				$current_dir_path . '/js/jquery-front-scripts.js', 	array('jquery') , 	EPL_PROPERTY_VER );

	if( is_epl_post() && shortcode_exists('listing_map') ) {

		$googleapiurl = 'https://maps.googleapis.com/maps/api/js?v=3.exp';

		if( epl_get_option('epl_google_api_key') != '' ) {
			$googleapiurl = $googleapiurl.'&key='.epl_get_option('epl_google_api_key');
		}

		if(epl_get_option('epl_disable_google_api') != 'on')
			wp_enqueue_script('google-map-v-3',$googleapiurl);

	}

	// All CSS includting Structual
	if( isset($epl_settings['epl_use_core_css']) &&  $epl_settings['epl_use_core_css'] == 'on') {

	} else {

		// Legacy CSS: on is to disable visual css, default off
		if( isset($epl_settings['epl_css_legacy']) &&  $epl_settings['epl_css_legacy'] == 'on') {

			wp_enqueue_style(	'epl-front-styles', 	$current_dir_path . '/css/style-legacy.css',			FALSE,			EPL_PROPERTY_VER );

		} else {
			// Structural CSS
			wp_enqueue_style(	'epl-css-lib', 		$current_dir_path . '/css/style-structure.css',		FALSE,			EPL_PROPERTY_VER );
			wp_enqueue_style(	'epl-style', 		$current_dir_path . '/css/style.css',			FALSE,			EPL_PROPERTY_VER );
		}

		/**
		 * Load theme specific stylesheet for epl, if exists
		 * Loaded at the end to override any styles in plugin
		 */
		if( file_exists(get_stylesheet_directory().'/easypropertylistings/style.css' ) ) {
			wp_enqueue_style(	'epl-theme-style', 		get_stylesheet_directory_uri().'/easypropertylistings/style.css',			FALSE,			EPL_PROPERTY_VER );
		}
	}

	$js_vars = array(
		'epl_default_view_type' => $epl_default_view_type,
		'ajaxurl'		=> admin_url('admin-ajax.php')
	);
	wp_enqueue_script( 	'epl-js-lib', 						$current_dir_path . '/js/epl.js', 			array('jquery'), 	EPL_PROPERTY_VER );

	wp_localize_script( 	'epl-front-scripts', 'epl_frontend_vars', $js_vars);
	wp_enqueue_script( 	'epl-front-scripts');

}
add_action( 'wp_enqueue_scripts', 'epl_wp_enqueue_scripts' );

function epl_admin_styles() {

	global $current_screen,$epl_settings;

	if(!is_null($current_screen) && $current_screen->base != 'edit')
		return;

	$active_size = isset($epl_settings['epl_admin_thumb_size'])? $epl_settings['epl_admin_thumb_size'] : 'admin-list-thumb';
	$sizes = array(
				'admin-list-thumb'		=>	'100 X 100',
				'epl-image-medium-crop'		=>	'300 X 200',
			);

	$width = current(explode(' X ',$sizes[$active_size])).'px';
	?>
	<style>
		.column-property_thumb {
			width:<?php echo $width; ?>;
		}
	</style>
<?php
}
add_action('admin_head','epl_admin_styles');
