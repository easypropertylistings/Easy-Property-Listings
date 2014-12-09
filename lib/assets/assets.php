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
	wp_enqueue_script( 'jquery-datetime-picke',$current_dir_path . '/js/jquery-datetime-picker.js', array('jquery') );
	wp_enqueue_style('jquery-ui-style',  $current_dir_path . '/css/jquery-ui.min.css');
	wp_enqueue_style( 'epl-admin-styles', $current_dir_path . '/css/style-admin.css' );
	wp_enqueue_script( 'epl-admin-scripts', $current_dir_path . '/js/jquery-admin-scripts.js', array('jquery') );
	
	if( $screen == 'post.php' || $screen == 'post-new.php' || $screen='toplevel_page_epl-general' ) {
		wp_enqueue_style( 'epl-jquery-validation-engine-style', $current_dir_path . '/css/validationEngine-jquery.css' );
		wp_enqueue_script( 'epl-jquery-validation-engine-lang-scripts', $current_dir_path . '/js/jquery-validationEngine-en.js', array('jquery') );
		wp_enqueue_script( 'epl-jquery-validation-engine-scripts', $current_dir_path . '/js/jquery-validationEngine.js', array('jquery') );
	}	
}
add_action( 'admin_enqueue_scripts', 'epl_admin_enqueue_scripts' );

/**
 * Load and enqueue front end scripts and stylesheets
 */
function epl_wp_enqueue_scripts() {
	global $epl_settings;
	$current_dir_path = plugins_url('', __FILE__ );
	$listings = array('property','land', 'commercial', 'business', 'commercial_land' , 'location_profile','rental','rural');
	if ( is_post_type_archive($listings) && (isset($epl_settings['listings_masonry']) &&  $epl_settings['listings_masonry'] == 1)) {
		wp_enqueue_script( 'jquery-masonry');
	}
	wp_enqueue_style( 'epl-front-styles', $current_dir_path . '/css/style-front.css' );
	wp_enqueue_script( 'epl-front-scripts', $current_dir_path . '/js/jquery-front-scripts.js', array('jquery') );
}
add_action( 'wp_enqueue_scripts', 'epl_wp_enqueue_scripts' );

function epl_inline_js_vars () { 
	global $epl_settings; ?>
	<script>
		var listingsMasonEnabled = <?php echo isset($epl_settings['listings_masonry'])?$epl_settings['listings_masonry']:0 ?>;
	</script>
<?php
}
add_action( 'wp_head', 'epl_inline_js_vars' );
