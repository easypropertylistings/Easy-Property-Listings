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

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get admin screens that EPL uses
 *
 * @return mixed|void
 *
 * @since 3.4
 */
function epl_get_admin_screens() {

	$screens = array(
		'easy-property-listings_page_epl-addons',
		'easy-property-listings_page_epl-tools',
		'toplevel_page_epl-general',
		'easy-property-listings_page_epl-settings',
		'easy-property-listings_page_epl-contacts',
		'easy-property-listings_page_epl-reports',
		'easy-property-listings_page_epl-extensions',
		'easy-property-listings_page_epl-licenses',
		'dashboard_page_epl-about',
		'dashboard_page_epl-getting-started',
		'edit.php',
		'index.php',
		'widgets.php'
	);

	return apply_filters( 'epl_admin_screens', $screens );
}

/**
 * Load and enqueue admin scripts and stylesheets.
 *
 * @since 1.0
 * @param string $screen Page hook.
 * @return void
 */
function epl_admin_enqueue_scripts( $screen ) {

	$mode   = epl_get_option( 'epl_plugin_mode', 'development' );
	$suffix = 'production' === $mode ? '.min' : '';

	$current_dir_path = plugins_url( '', __FILE__ );

	$js_vars = array(
		'default_map_address' => apply_filters( 'epl_default_map_address', epl_get_option( 'epl_default_country', 'Australia' ) ),

		'ajax_nonce'          => wp_create_nonce( 'epl_ajax_nonce' ),
	);

	wp_register_script( 'epl-admin-scripts', $current_dir_path . '/js/jquery-admin-scripts' . $suffix . '.js', array( 'jquery' ), EPL_PROPERTY_VER, false );

	wp_localize_script( 'epl-admin-scripts', 'epl_admin_vars', $js_vars );

	if ( 'edit.php' === $screen || 'post.php' === $screen || 'post-new.php' === $screen || 'easy-property-listings_page_epl-extensions' === $screen || 'easy-property-listings_page_epl-settings' === $screen || 'easy-property-listings_page_epl-extensions' === $screen ) {

		$googleapiurl       = 'https://maps.googleapis.com/maps/api/js?v=3.exp';
		$epl_google_api_key = epl_get_option( 'epl_google_api_key' );
		if ( ! empty( $epl_google_api_key ) ) {
			$googleapiurl = $googleapiurl . '&key=' . epl_get_option( 'epl_google_api_key' );
		}

		wp_enqueue_script( 'google-map-v-3', $googleapiurl, array(), EPL_PROPERTY_VER, false );

		wp_enqueue_style( 'epl-jquery-validation-engine-style', $current_dir_path . '/css/validationEngine-jquery' . $suffix . '.css', false, EPL_PROPERTY_VER );
		wp_enqueue_script( 'epl-jquery-validation-engine-lang-scripts', $current_dir_path . '/js/jquery-validationEngine-en' . $suffix . '.js', array( 'jquery' ), EPL_PROPERTY_VER, false );
		wp_enqueue_script( 'epl-jquery-validation-engine-scripts', $current_dir_path . '/js/jquery-validationEngine' . $suffix . '.js', array( 'jquery' ), EPL_PROPERTY_VER, false );
		wp_enqueue_script( 'jquery-datetime-picker', $current_dir_path . '/js/jquery-datetime-picker' . $suffix . '.js', array( 'jquery' ), EPL_PROPERTY_VER, false );
		wp_enqueue_style( 'jquery-ui-datetime-picker-style', $current_dir_path . '/css/jquery-ui' . $suffix . '.css', false, EPL_PROPERTY_VER );

		wp_enqueue_style( 'epl-admin-styles', $current_dir_path . '/css/style-admin' . $suffix . '.css', false, EPL_PROPERTY_VER );

	}

	// load admin style on help & documentation pages as well.
	if ( in_array( $screen, epl_get_admin_screens(), true ) ) {

		wp_enqueue_style( 'epl-admin-styles', $current_dir_path . '/css/style-admin' . $suffix . '.css', false, EPL_PROPERTY_VER );
	}

	wp_enqueue_script( 'epl-js-lib', $current_dir_path . '/js/epl' . $suffix . '.js', array( 'jquery' ), EPL_PROPERTY_VER, false );

	wp_enqueue_script( 'epl-admin-scripts' );
}
add_action( 'admin_enqueue_scripts', 'epl_admin_enqueue_scripts' );

/**
 * Load and enqueue front end scripts and stylesheets.
 *
 * @since 1.0
 */
function epl_wp_enqueue_scripts() {

	$mode                  = epl_get_option( 'epl_plugin_mode', 'development' );
	$suffix                = 'production' === $mode ? '.min' : '';
	$epl_default_view_type = epl_get_option( 'display_archive_view_type', 'list' );
	$current_dir_path      = plugins_url( '', __FILE__ );
	wp_register_script( 'epl-front-scripts', $current_dir_path . '/js/jquery-front-scripts' . $suffix . '.js', array( 'jquery' ), EPL_PROPERTY_VER, false );

	if ( is_epl_post() && shortcode_exists( 'listing_map' ) ) {

		$googleapiurl       = 'https://maps.googleapis.com/maps/api/js?v=3.exp';
		$epl_google_api_key = epl_get_option( 'epl_google_api_key' );
		if ( ! empty( $epl_google_api_key ) ) {
			$googleapiurl = $googleapiurl . '&key=' . epl_get_option( 'epl_google_api_key' );
		}

		if ( epl_get_option( 'epl_disable_google_api' ) !== 'on' ) {
			wp_enqueue_script( 'google-map-v-3', $googleapiurl, array(), EPL_PROPERTY_VER, false );
		}
	}

	// All CSS including Structual.
	if ( epl_get_option( 'epl_use_core_css', 'off' ) === 'on' ) {
		// Dont use css.
	} else {

		// Legacy CSS: on is to enable visual css, default off.
		if ( epl_get_option( 'epl_css_legacy', 'off' ) === 'on' ) {

			wp_enqueue_style( 'epl-front-styles', $current_dir_path . '/css/style-legacy' . $suffix . '.css', false, EPL_PROPERTY_VER );

		} else {
			// Structural CSS.
			wp_enqueue_style( 'epl-css-lib', $current_dir_path . '/css/style-structure' . $suffix . '.css', false, EPL_PROPERTY_VER );
			wp_enqueue_style( 'epl-style', $current_dir_path . '/css/style' . $suffix . '.css', false, EPL_PROPERTY_VER );
		}

		// Enhanced CSS: on is to enable visual css, default on for new installations.
		if ( epl_get_option( 'epl_css_enhanced', 'off' ) === 'on' ) {
			wp_enqueue_style( 'epl-style-enhanced', $current_dir_path . '/css/style-enhanced' . $suffix . '.css', false, EPL_PROPERTY_VER );
		}

		wp_enqueue_style( 'epl-field-sliders', $current_dir_path . '/css/field-slider' . $suffix . '.css', false, EPL_PROPERTY_VER );

		/**
		 * Load theme specific stylesheet for epl, if exists
		 * Loaded at the end to override any styles in plugin
		 */
		if ( file_exists( get_stylesheet_directory() . '/easypropertylistings/style.css' ) ) {
			wp_enqueue_style( 'epl-theme-style', get_stylesheet_directory_uri() . '/easypropertylistings/style.css', false, EPL_PROPERTY_VER );
		}

		if ( is_epl_post_archive() && file_exists( get_stylesheet_directory() . '/easypropertylistings/style-archive.css' ) ) {
			wp_enqueue_style( 'epl-theme-style-archive', get_stylesheet_directory_uri() . '/easypropertylistings/style-archive.css', false, EPL_PROPERTY_VER );
		}

		if ( is_epl_post_single() && file_exists( get_stylesheet_directory() . '/easypropertylistings/style-single.css' ) ) {
			wp_enqueue_style( 'epl-theme-style-single', get_stylesheet_directory_uri() . '/easypropertylistings/style-single.css', false, EPL_PROPERTY_VER );
		}
	}

	$js_vars = array(
		'site_url'              => esc_url( home_url( '/' ) ),
		'epl_default_view_type' => $epl_default_view_type,
		'ajaxurl'               => admin_url( 'admin-ajax.php' ),
		'image_base'            => EPL_PLUGIN_URL . 'lib/assets/images/',
		'field_sliders'         => epl_get_field_sliders(),
	);
	wp_enqueue_script( 'jquery-ui-slider' );
	wp_enqueue_script( 'jquery-touch-punch' );

	wp_enqueue_script( 'epl-js-lib', $current_dir_path . '/js/epl' . $suffix . '.js', array( 'jquery' ), EPL_PROPERTY_VER, false );

	wp_localize_script( 'epl-front-scripts', 'epl_frontend_vars', $js_vars );
	wp_enqueue_script( 'epl-front-scripts' );

}
add_action( 'wp_enqueue_scripts', 'epl_wp_enqueue_scripts' );

/**
 * Load and enqueue admin styles.
 *
 * @since 1.0
 */
function epl_admin_styles() {

	global $current_screen;

	if ( ! is_null( $current_screen ) && 'edit' !== $current_screen->base ) {
		return;
	}

	$active_size = epl_get_option( 'epl_admin_thumb_size', 'admin-list-thumb' );
	$sizes       = get_epl_image_sizes();
	$width       = '120px';

	foreach ( $sizes as $size ) {
		if ( $size['id'] === $active_size ) {
			$width = $size['width'] + 20 . 'px';  // Add 20 pixels for padding.
			break;
		}
	}

	?>
	<style>
		.column-property_thumb {
			width: <?php echo esc_html( $width ); ?>;
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
add_action( 'admin_head', 'epl_admin_styles' );
