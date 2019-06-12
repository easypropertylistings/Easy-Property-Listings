<?php
/**
 * Scripts & Styles
 *
 * @package     EPL
 * @subpackage  Scripts/Styles
 * @copyright   Copyright (c) 2019, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Load and enqueue admin scripts and stylesheets
 *
 * @since 1.0
 */
function epl_admin_enqueue_scripts($screen) {

	$mode = epl_get_option('epl_plugin_mode','development');
	$suffix = $mode == 'production' ? '.min' : '';

	$current_dir_path = plugins_url('', __FILE__ );

	if( $screen == 'post.php' || $screen == 'post-new.php' || $screen == 'easy-property-listings_page_epl-extensions' ||  $screen == 'easy-property-listings_page_epl-settings' ||  $screen= 'easy-property-listings_page_epl-extensions') {

		$googleapiurl = 'https://maps.googleapis.com/maps/api/js?v=3.exp';

		if( epl_get_option('epl_google_api_key') != '' ) {
			$googleapiurl = $googleapiurl.'&key='.epl_get_option('epl_google_api_key');
		}

		wp_enqueue_script('google-map-v-3',$googleapiurl);


		wp_enqueue_style(	'epl-jquery-validation-engine-style', 		$current_dir_path . '/css/validationEngine-jquery'.$suffix.'.css',	FALSE,		EPL_PROPERTY_VER );
		wp_enqueue_script(	'epl-jquery-validation-engine-lang-scripts', 	$current_dir_path . '/js/jquery-validationEngine-en'.$suffix.'.js',	array('jquery'),	EPL_PROPERTY_VER );
		wp_enqueue_script(	'epl-jquery-validation-engine-scripts', 		$current_dir_path . '/js/jquery-validationEngine'.$suffix.'.js', 	array('jquery'),	EPL_PROPERTY_VER );
		wp_enqueue_script(	'jquery-datetime-picker',				$current_dir_path . '/js/jquery-datetime-picker'.$suffix.'.js', 	array('jquery'),	EPL_PROPERTY_VER );
		wp_enqueue_style(	'jquery-ui-datetime-picker-style',  		$current_dir_path . '/css/jquery-ui.min.css',			FALSE,		EPL_PROPERTY_VER );

		$js_vars = array(
			'default_map_address' 	=> apply_filters('epl_default_map_address', epl_get_option('epl_default_country','Australia') ),
		);

		wp_register_script( 	'epl-admin-scripts', 				$current_dir_path . '/js/jquery-admin-scripts'.$suffix.'.js', 	array('jquery'),	EPL_PROPERTY_VER );

		wp_localize_script('epl-admin-scripts','epl_admin_vars',$js_vars);

		wp_enqueue_style( 	'epl-admin-styles', 				$current_dir_path . '/css/style-admin'.$suffix.'.css',		FALSE,		EPL_PROPERTY_VER );

	}

	// load admin style on help & documentation pages as well
	if($screen = 'edit.php' || $screen == 'toplevel_page_epl-general' || $screen == 'dashboard_page_epl-about' || $screen == 'dashboard_page_epl-getting-started' || $screen == 'toplevel_page_epl-tools' )	{
		wp_enqueue_style(	'epl-admin-styles', 				$current_dir_path . '/css/style-admin'.$suffix.'.css',		FALSE,		EPL_PROPERTY_VER );
	}

	wp_enqueue_script(		'epl-js-lib', 					$current_dir_path . '/js/epl'.$suffix.'.js', 			array('jquery'), EPL_PROPERTY_VER );

	wp_enqueue_script('epl-admin-scripts');
}
add_action( 'admin_enqueue_scripts', 'epl_admin_enqueue_scripts' );

/**
 * Load and enqueue front end scripts and stylesheets
 *
 * @since 1.0
 */
function epl_wp_enqueue_scripts() {
	global $epl_settings;

	$mode = epl_get_option('epl_plugin_mode','development');
	$suffix = $mode == 'production' ? '.min' : '';
	$epl_default_view_type = isset($epl_settings['display_archive_view_type']) ? $epl_settings['display_archive_view_type'] : 'list';
	$current_dir_path = plugins_url('', __FILE__ );
	wp_register_script( 		'epl-front-scripts', 				$current_dir_path . '/js/jquery-front-scripts'.$suffix.'.js', 	array('jquery'), EPL_PROPERTY_VER );

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

		// Legacy CSS: on is to enable visual css, default off
		if( isset($epl_settings['epl_css_legacy']) &&  $epl_settings['epl_css_legacy'] == 'on') {

			wp_enqueue_style(	'epl-front-styles', 	$current_dir_path . '/css/style-legacy'.$suffix.'.css',		FALSE,		EPL_PROPERTY_VER );

		} else {
			// Structural CSS
			wp_enqueue_style(	'epl-css-lib', 		$current_dir_path . '/css/style-structure'.$suffix.'.css',		FALSE,		EPL_PROPERTY_VER );
			wp_enqueue_style(	'epl-style', 		$current_dir_path . '/css/style'.$suffix.'.css',			FALSE,		EPL_PROPERTY_VER );
		}

		// Enhanced CSS: on is to enable visual css, default on for new installations
		if( isset($epl_settings['epl_css_enhanced']) &&  $epl_settings['epl_css_enhanced'] == 'on') {
			wp_enqueue_style(	'epl-style-enhanced', 	$current_dir_path . '/css/style-enhanced'.$suffix.'.css',		FALSE,		EPL_PROPERTY_VER );
		}

		wp_enqueue_style(	'epl-field-sliders', 	$current_dir_path . '/css/field-slider'.$suffix.'.css',		FALSE,		EPL_PROPERTY_VER );

		/**
		 * Load theme specific stylesheet for epl, if exists
		 * Loaded at the end to override any styles in plugin
		 */
		if( file_exists(get_stylesheet_directory().'/easypropertylistings/style.css' ) ) {
			wp_enqueue_style(	'epl-theme-style', 	get_stylesheet_directory_uri().'/easypropertylistings/style.css',	FALSE,		EPL_PROPERTY_VER );
		}

		if( is_epl_post_archive() && file_exists(get_stylesheet_directory().'/easypropertylistings/style-archive.css' ) ) {
			wp_enqueue_style(	'epl-theme-style', 	get_stylesheet_directory_uri().'/easypropertylistings/style-archive.css',	FALSE,		EPL_PROPERTY_VER );
		}

		if( is_epl_post_single() && file_exists(get_stylesheet_directory().'/easypropertylistings/style-single.css' ) ) {
			wp_enqueue_style(	'epl-theme-style', 	get_stylesheet_directory_uri().'/easypropertylistings/style-single.css',	FALSE,		EPL_PROPERTY_VER );
		}
	}

	$js_vars = array(
		'site_url'				=>	site_url(),
		'epl_default_view_type' => $epl_default_view_type,
		'ajaxurl'               => admin_url('admin-ajax.php'),
		'image_base'            => EPL_PLUGIN_URL.'lib/assets/images/',
		'field_sliders'         => epl_get_field_sliders()
	);

	wp_enqueue_script('jquery-ui-touch-punch', $current_dir_path .'/js/jquery.ui.touch-punch.min.js', array('jquery-ui-slider') );

	wp_enqueue_script( 	'epl-js-lib',	$current_dir_path . 	'/js/epl'.$suffix.'.js', 						array('jquery'), EPL_PROPERTY_VER );

	wp_localize_script( 	'epl-front-scripts', 'epl_frontend_vars', $js_vars);
	wp_enqueue_script( 	'epl-front-scripts');

}
add_action( 'wp_enqueue_scripts', 'epl_wp_enqueue_scripts' );

/**
 * Load and enqueue admin styles
 *
 * @since 1.0
 */
function epl_admin_styles() {

	global $current_screen,$epl_settings;

	if(!is_null($current_screen) && $current_screen->base != 'edit')
		return;

	$active_size = isset($epl_settings['epl_admin_thumb_size'])? $epl_settings['epl_admin_thumb_size'] : 'admin-list-thumb';
	$sizes = get_epl_image_sizes();
	$width = '120px';

	foreach( $sizes as $size) {
		if($size['id'] == $active_size) {
			$width = $size['width'] + 20 . 'px';  // Add 20 pixels for padding
			break;
		}
	}

	?>
	<style>
		.column-property_thumb {
			width:<?php echo $width; ?>;
		}
		.column-property_thumb img {
			max-width: 100%;
			height: auto;
		}
		.column-property_featured {
			width: 60px;
		}
		.column-property_featured .dashicons-star-filled {
			color: orange;
		}
		.column-property_featured .dashicons-star-empty {
			color: #aaaaaa;
		}
		.column-property_featured .epl-manage-featured {
			display: none;
		}
	</style>
<?php
}
add_action('admin_head','epl_admin_styles');
