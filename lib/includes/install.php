<?php
/**
 * Install Function
 *
 * @package     EPL
 * @subpackage  Functions/Install
 * @copyright   Copyright (c) 2020, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get array of default settings
 *
 * @return array
 * @since 3.3.5
 */
function epl_get_default_settings() {

	return array(

		'currency'                             => 'AUD',
		'currency_position'                    => 'before',
		'currency_thousands_separator'         => ',',
		'currency_decimal_separator'           => '.',
		'admin_unique_id'                      => 0,
		'debug'                                => 0,
		'display_bond'                         => 0,
		'display_single_gallery'               => 1,
		'display_gallery_n'                    => 4,
		'display_feature_columns'              => 2,
		'display_excerpt_length'               => 10,
		'display_archive_view_type'            => 'list',
		'use_fancy_navigation'                 => 0,
		'label_bond'                           => __( 'Bond', 'easy-property-listings' ),
		'label_location'                       => __( 'Suburb', 'easy-property-listings' ),
		'label_suburb'                         => __( 'Suburb', 'easy-property-listings' ),
		'label_city'                           => __( 'City', 'easy-property-listings' ),
		'label_state'                          => __( 'State', 'easy-property-listings' ),
		'label_postcode'                       => __( 'Post Code', 'easy-property-listings' ),
		'label_home_open'                      => __( 'Home Open', 'easy-property-listings' ),
		'label_poa'                            => __( 'POA', 'easy-property-listings' ),
		'label_new'                            => __( 'New', 'easy-property-listings' ),
		'label_under_offer'                    => __( 'Under Offer', 'easy-property-listings' ),
		'label_leased'                         => __( 'Leased', 'easy-property-listings' ),
		'label_sold'                           => __( 'Sold', 'easy-property-listings' ),
		'widget_label_all'                     => __( 'All', 'easy-property-listings' ),
		'widget_label_property'                => __( 'Buy', 'easy-property-listings' ),
		'widget_label_land'                    => __( 'Land', 'easy-property-listings' ),
		'widget_label_rental'                  => __( 'Rent', 'easy-property-listings' ),
		'widget_label_rural'                   => __( 'Rural', 'easy-property-listings' ),
		'widget_label_business'                => __( 'Business', 'easy-property-listings' ),
		'widget_label_commercial'              => __( 'Commercial', 'easy-property-listings' ),
		'widget_label_commercial_land'         => __( 'Commercial Land', 'easy-property-listings' ),
		'epl_max_graph_sales_price'            => 2000000,
		'epl_max_graph_rent_price'             => 2000,
		'sticker_new_range'                    => 7,
		'epl_admin_thumb_size'                 => 'admin-list-thumb',
		'epl_enable_city_field'                => 'no',
		'epl_enable_country_field'             => 'no',
		'epl_feeling_lucky'                    => 'off',
		
		
		'theme_setup_css'                      => 'on',
		
		'theme_setup_archive_max_width'        => '100%',
		'theme_setup_archive_sidebar'          => 'off',

		
		'theme_setup_single_max_width'        => '100%',
		'theme_setup_single_sidebar'          => 'off',
		
		
		'epl_lucky_disable_single_thumb'       => 'off',
		'epl_lucky_disable_theme_single_thumb' => 'off',
		'epl_lucky_disable_archive_thumb'      => 'off',
		'epl_lucky_disable_epl_archive_thumb'  => 'off',
		'epl_use_core_css'                     => 'off', // Means Enable CSS.
		'epl_css_legacy'                       => 'off', // Legacy styles disabled by default.
		'epl_css_enhanced'                     => 'on', // Enhanced styles enabled for new installations by default.
		'epl_google_api_key'                   => '',
		'epl_disable_google_api'               => 'on',
		'uninstall_on_delete'                  => 0,
		'inspection_date_format'               => 'l, dS F',
		'inspection_time_format'               => 'h:i a',
		'custom_inspection_date_format'        => 'l, dS F',
		'custom_inspection_time_format'        => 'h:i a',
		'epl_video_width'                      => 600,
		'min_contact_access'                   => 'level_10',
		'min_reports_access'                   => 'level_10',
		'activate_post_types'                  => array( 'property' ),
		'epl_default_country'                  => 'Australia',
		'epl_icons_svg_listings'               => 'on',
		'epl_icons_svg_author'                 => 'on',
		'epl_plugin_mode'                      => 'production',
	);
}

/**
 * Install
 *
 * Runs on plugin install by setting up the current version,
 * flushing rewrite rules to initiate the new 'property' slug and also
 * creates the plugin and populates the settings fields for those plugin
 * pages. After successful install, the user is redirected to the EPL Welcome
 * screen.
 *
 * @global $wpdb
 * @global $epl_options
 * @global $wp_version
 * @return void
 *
 * @since 1.0
 * @since 3.4.20 Altered order of flush permalinks function.
 */
function epl_install() {
	global $wpdb, $epl_options, $wp_version;

	// Add default EPL Settings.
	$epl_settings = epl_settings();

	$new_fields_defaults = epl_get_default_settings();

	if ( ! empty( $epl_settings ) ) {

		// possible upgrade.
		foreach ( $new_fields_defaults as $key    => $value ) {
			if ( ! isset( $epl_settings[ $key ] ) ) {
				// sure upgrade, fields are not set lets set them for very first time.
				$epl_settings[ $key ] = $value;
			}
		}
	} else {
		// first time install . load with default values.
		$epl_settings = $new_fields_defaults;
	}
	update_option( 'epl_settings', $epl_settings );

	// Add Upgraded From Option.
	$current_version = get_option( 'epl_version' );
	if ( ! empty( $current_version ) ) {
		update_option( 'epl_version_upgraded_from', $current_version );

	} else {
		$epl_data = get_plugin_data( EPL_PLUGIN_PATH . '/easy-property-listings.php' );
		update_option( 'epl_version', $epl_data['Version'] );
	}

	$notice_display = get_transient( 'epl_admin_notices_display' );

	if ( ! $notice_display ) {
		set_transient( 'epl_admin_notices_display', true, 60 * 60 * 24 * 14 );
	}

	// Bail if activating from network, or bulk.
	if ( is_network_admin() || isset( $_GET['activate-multi'] ) ) { //phpcs:ignore
		return;
	}
	remove_role( 'epl_crm' );

	// Clear the permalinks.
	flush_rewrite_rules();

	// Add the transient to redirect.
	set_transient( '_epl_activation_redirect', true, 30 );
}
register_activation_hook( EPL_PLUGIN_FILE, 'epl_install' );

/**
 * Un-Install
 *
 * Runs on plugin un-install by setting up the current version, flushing rewrite rules
 *
 * @since 2.2
 */
function epl_uninstall() {
	update_option( 'epl_rewrite_rules', false );
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

	// Exit if not in admin or the transient doesn't exist.
	if ( false === $activation_pages ) {
		return;
	}

	// Delete the transient.
	delete_transient( '_epl_activation_pages' );

	do_action( 'epl_after_install', $activation_pages );
}
add_action( 'admin_init', 'epl_after_install' );

/**
 * Apply new settings on update
 *
 * Runs just after plugin installation
 *
 * @since 1.0
 * @return void
 */
function epl_plugin_updates() {
	$current_version = get_option( 'epl_version' );

	if ( version_compare( $current_version, '1.3', '<' ) ) {
		include EPL_PATH_UPDATES . 'epl-1.3.1.php';
		update_option( 'epl_version', '1.3' );
	}
	if ( version_compare( $current_version, '2.1', '<' ) ) {
		include EPL_PATH_UPDATES . 'epl-2.1.php';
		update_option( 'epl_version', '2.1' );
	}
	if ( version_compare( $current_version, '2.1.8', '<' ) ) {
		include EPL_PATH_UPDATES . 'epl-2.1.8.php';
		update_option( 'epl_version', '2.1.8' );
	}
	if ( version_compare( $current_version, '2.1.11', '<' ) ) {
		include EPL_PATH_UPDATES . 'epl-2.1.11.php';
		update_option( 'epl_version', '2.1.11' );
	}
	if ( version_compare( $current_version, '2.2', '<' ) ) {
		include EPL_PATH_UPDATES . 'epl-2.2.php';
		update_option( 'epl_version', '2.2' );
	}
	if ( version_compare( $current_version, '3.0', '<' ) ) {
		include EPL_PATH_UPDATES . 'epl-3.0.php';
		update_option( 'epl_version', '3.0' );
	}
	if ( version_compare( $current_version, '30', '==' ) ) { // Fix for 3.0 version number.
		include EPL_PATH_UPDATES . 'epl-3.1.php';
		update_option( 'epl_version', '3.1' );
	}
	if ( version_compare( $current_version, '3.1', '<' ) ) {
		include EPL_PATH_UPDATES . 'epl-3.1.php';
		update_option( 'epl_version', '3.1' );
	}
	if ( version_compare( $current_version, '3.1.16', '<' ) ) {
		include EPL_PATH_UPDATES . 'epl-3.1.16.php';
		update_option( 'epl_version', '3.1.16' );
	}
	if ( version_compare( $current_version, '3.2', '<' ) ) {
		include EPL_PATH_UPDATES . 'epl-3.2.php';
		update_option( 'epl_version', '3.2' );
	}
	if ( version_compare( $current_version, '3.2.2', '<' ) ) {
		flush_rewrite_rules();
		update_option( 'epl_version', '3.2.2' );
	}
	if ( version_compare( $current_version, '3.3', '<' ) ) {
		include EPL_PATH_UPDATES . 'epl-3.3.php';
		flush_rewrite_rules();
		update_option( 'epl_version', '3.3' );
	}
}
add_action( 'admin_init', 'epl_plugin_updates' );
