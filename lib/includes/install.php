<?php
/**
 * Install Function
 *
 * @package     EPL
 * @subpackage  Functions/Install
 * @copyright   Copyright (c) 2014, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Install
 *
 * Runs on plugin install by setting up the current version,
 * flushing rewrite rules to initiate the new 'property' slug and also
 * creates the plugin and populates the settings fields for those plugin
 * pages. After successful install, the user is redirected to the EPL Welcome
 * screen.
 *
 * @since 1.0
 * @global $wpdb
 * @global $epl_options
 * @global $wp_version
 * @return void
 */
function epl_install() {
	global $wpdb, $epl_options, $wp_version;

	// Clear the permalinks
	flush_rewrite_rules();
	
	// Add default EPL Settings
	$epl_settings = epl_settings();
	if(empty($epl_settings)) {
		// first time install . load with default values.
		$epl_settings = array(
			'currency'						=> 'AUD',
			'currency_position'				=> 'before',
			'currency_thousands_separator'	=> ',',
			'currency_decimal_separator'	=> '.',
			'debug'							=> 0,
			'display_bond'					=> 0,
			'display_single_gallery'		=> 0,
			'display_gallery_n'				=> 4,
			'display_feature_columns'		=> 2,
			'display_excerpt_length'		=> 10,
			'label_bond'					=> __('Bond', 'epl'),
			'label_location'				=> __('Suburb', 'epl'),
			'label_suburb'					=> __('Suburb', 'epl'),
			'label_postcode'				=> __('Post Code', 'epl'),
			'label_home_open'				=> __('Home Open', 'epl'),
			'label_poa'						=> __('POA', 'epl'),
			'label_new'						=> __('New', 'epl'),
			'widget_label_property'			=> __('Buy', 'epl'),
			'widget_label_land'				=> __('Land', 'epl'),
			'widget_label_rental'			=> __('Rent', 'epl'),
			'widget_label_rural'			=> __('Rural', 'epl'),
			'widget_label_business'			=> __('Business', 'epl'),
			'widget_label_commercial'		=> __('Commercial', 'epl'),
			'widget_label_commercial_land'	=> __('Commercial Land', 'epl'),
			'epl_max_graph_sales_price'		=> 2000000,
			'epl_max_graph_rent_price'		=> 2000,
			'sticker_new_range'				=> 7,
		);
	} else {
	
		// possible upgrade
		$new_fields_defaults = array(
			'widget_label_property'			=> __('Property', 'epl'),
			'widget_label_land'				=> __('Land', 'epl'),
			'widget_label_rental'			=> __('Rental', 'epl'),
			'widget_label_rural'			=> __('Rural', 'epl'),
			'widget_label_business'			=> __('Business', 'epl'),
			'widget_label_commercial'		=> __('Commercial', 'epl'),
			'widget_label_commercial_land'	=> __('Commercial Land', 'epl'),
			'epl_max_graph_sales_price'		=> 2000000,
			'epl_max_graph_rent_price'		=> 2000,
			'sticker_new_range'				=> 7,
			'label_new'						=> __('New', 'epl')


		);
		
		
		foreach($new_fields_defaults as $key	=>	$value) {
			if(!isset($epl_settings[$key])) {
				// sure upgrade, fields are not set lets set them for very first time
				$epl_settings[$key] = $value;
			}
		}
	}
	update_option( 'epl_settings', $epl_settings );
	

	// Add Upgraded From Option
	$current_version = get_option( 'epl_version' );
	if ( $current_version ) {
		update_option( 'epl_version_upgraded_from', $current_version );
	} else {
		$epl_data = get_plugin_data(EPL_PLUGIN_PATH);
		update_option( 'epl_version', $epl_data['Version'] );
	}

	// Bail if activating from network, or bulk
	if ( is_network_admin() || isset( $_GET['activate-multi'] ) ) {
		return;
	}

	// Add the transient to redirect
	set_transient( '_epl_activation_redirect', true, 30 );
}
register_activation_hook( EPL_PLUGIN_FILE, 'epl_install' );

/**
 * Un-Install
 *
 * Runs on plugin un-install by setting up the current version,
 * flushing rewrite rules
 */
function epl_uninstall() {
	update_option('epl_rewrite_rules', false);
	flush_rewrite_rules();
}
register_deactivation_hook( EPL_PLUGIN_FILE, 'epl_uninstall' );

/**
 * Post-installation
 *
 * Runs just after plugin installation and exposes the
 * epl_after_install hook.
 *
 * @since 1.0
 * @return void
 */
function epl_after_install() {

	if ( ! is_admin() ) {
		return;
	}

	$activation_pages = get_transient( '_epl_activation_pages' );

	// Exit if not in admin or the transient doesn't exist
	if ( false === $activation_pages ) {
		return;
	}

	// Delete the transient
	delete_transient( '_epl_activation_pages' );

	do_action( 'epl_after_install', $activation_pages );
}
add_action( 'admin_init', 'epl_after_install' );

