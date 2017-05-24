<?php
/**
 * EPL Admin Functions
 *
 * @package     EPL
 * @subpackage  Admin/Functions
 * @copyright   Copyright (c) 2014, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Flush Rewrite Rules
 *
 * @since 1.0
 */
function epl_flush_rewrite_rules() {
	$epl_rewrite_rules = get_option('epl_rewrite_rules');
	if(!$epl_rewrite_rules) {
		flush_rewrite_rules();
		update_option('epl_rewrite_rules', true);
	}
}
add_action('admin_init', 'epl_flush_rewrite_rules');
add_action('init', 'epl_flush_rewrite_rules');

/**
 * Notice on Dashboard Widget
 *
 * @since 3.1.6
 */
function epl_dashboard_widget_offer_rough() {

	if ( has_filter( 'epl_extensions_options_filter_new' ) || get_transient( 'epl_admin_notices_display' ) == '1' )
		return;


	echo '<div class="epl-internal-ad">';
		echo '<div>' . __('Remove these ads?' , 'easy-property-listings') . '</div>';

		echo '<div><a href="https://easypropertylistings.com.au/extensions/?utm_source=offers&utm_medium=description_tab&utm_content=settings_link&utm_campaign=epl_extension_store">' . __('Purchase an extension remove me' , 'easy-property-listings') . '</a></div>';
	echo '</div>';
}

/**
 * Notice presented on settings page, removed if extension is present and activated
 *
 * @since 3.1.6
 */
function epl_admin_sidebar () {

	if ( has_filter( 'epl_extensions_options_filter_new' ) || get_transient( 'epl_admin_notices_display' ) == '1' )
		return;

	$service_banners = array(
		array(
			'url' => 'https://easypropertylistings.com.au/extensions/core-bundle/?utm_source=offers&utm_medium=description_tab&utm_content=settings_link&utm_campaign=epl_extension_core',
			'img' => 'banner-dash-wide.png',
			'alt' => __('Core bundle for Easy Property Listings', 'easy-property-listings' ),
			'width'	=> 261,
		),
		array(
			'url' => 'https://easypropertylistings.com.au/extensions/advanced-mapping/?utm_source=offers&utm_medium=description_tab&utm_content=settings_link&utm_campaign=epl_extension_advanced_map',
			'img' => 'offer-advanced-mapping.png',
			'alt' => __('Advanced Mapping', 'easy-property-listings' ),
			'width'	=> 261,
		),
		array(
			'url' => 'https://easypropertylistings.com.au/extensions/sliders/?utm_source=offers&utm_medium=description_tab&utm_content=settings_link&utm_campaign=epl_extension_sliders',
			'img' => 'offer-sliders.png',
			'alt' => __('Sliders extension', 'easy-property-listings' ),
			'width'	=> 261,
		),
		array(
			'url' => 'https://easypropertylistings.com.au/extensions/staff-directory/?utm_source=offers&utm_medium=description_tab&utm_content=settings_link&utm_campaign=epl_extension_staff_directory',
			'img' => 'offer-staff-directory.png',
			'alt' => __('Staff Directory', 'easy-property-listings' ),
			'width'	=> 261,
		),
		array(
			'url' => 'https://easypropertylistings.com.au/extensions/listing-templates/?utm_source=offers&utm_medium=description_tab&utm_content=settings_link&utm_campaign=epl_extension_listing_templates',
			'img' => 'offer-templates.png',
			'alt' => __('Listing Templates', 'easy-property-listings' ),
			'width'	=> 261,
		)
	);
	$i = 0;
	foreach ( $service_banners as $banner ) {
		echo '<a target="_blank" href="' . esc_url( $banner['url'] ) . '"><img width="' . $banner['width'] . '" src="' .plugins_url( 'lib/assets/images/' . $banner['img'], EPL_PLUGIN_FILE ) .'" alt="' . esc_attr( $banner['alt'] ) . '"/></a><br/><br/>';
		$i ++;
	}
	?>
	<div class="epl-admin-offer" style="margin-bottom: 1em;">
		<div><span><?php _e('Remove these ads?', 'easy-property-listings' ); ?><span></div>
		<div><a href="http://easypropertylistings.com.au/extensions/?utm_source=offers&utm_medium=description_tab&utm_content=settings_link&utm_campaign=epl_extension_store"><?php _e('Purchase and activate any extension', 'easy-property-listings' ); ?> &#x27F6;</a></div>
	</div>
<?php
}

/**
 * Notice presented on dashboard widget, removed if extension is present and activated
 *
 * @since 3.1.6
 */
function epl_dashboard_widget_offer () {
	if ( has_filter( 'epl_extensions_options_filter_new' ) || get_transient( 'epl_admin_notices_display' ) == '1' )
		return;

	$service_banners = array(
		array(
			'url' 	=> 'https://easypropertylistings.com.au/extensions/core-bundle/?utm_source=offers&utm_medium=description_tab&utm_content=extensions_link&utm_campaign=epl_extension_core',
			'img' 	=> 'banner-dash-wide.png',
			'alt' 	=> __('Check out the premium extensions', 'easy-property-listings' ),
			'width'	=> 261,
		),
		array(
			'url' => 'https://easypropertylistings.com.au/extensions/?utm_source=offers&utm_medium=description_tab&utm_content=extensions_link&utm_campaign=epl_extension_store',
			'img' => 'banner-dash-wide-store.png',
			'alt' => __('Browse the extension store', 'easy-property-listings' ),
			'width'	=> 125,
		)
	);
	$i = 0;
	foreach ( $service_banners as $banner ) {
		echo '<a target="_blank" href="' . esc_url( $banner['url'] ) . '">
			<img style="margin-right: 0.5em" width="' . $banner['width'] . '" src="' .plugins_url( 'lib/assets/images/' . $banner['img'], EPL_PLUGIN_FILE ) .'" alt="' . esc_attr( $banner['alt'] ) . '"/>
		      </a>';
		$i ++;
	}
	?>

	<div class="epl-admin-offer" style="margin-bottom: 1em;">
		<div><span><?php _e('Remove these ads?', 'easy-property-listings' ); ?><span></div>
		<div><a href="http://easypropertylistings.com.au/extensions/"><?php _e('Purchase and activate any extension', 'easy-property-listings' ); ?> &#x27F6;</a></div>
	</div>
<?php
}

/**
 * Notice presented on post types, removed if extension is present and activated
 *
 * @since 3.1.6
 */
function epl_dashboard_widget_offer_post_types () {
	if ( has_filter( 'epl_extensions_options_filter_new' ) || get_transient( 'epl_admin_notices_display' ) == '1' )
		return;

	$service_banners = array(
		array(
			'url' => 'https://easypropertylistings.com.au/extensions/?utm_source=offers&utm_medium=description_tab&utm_content=post_link&utm_campaign=epl_extensions',
			'img' => 'banner-dash-wide-store.png',
			'alt' => __('Browse the extension store', 'easy-property-listings' ),
			'width'	=> 125,
		),
		array(
			'url' => 'https://easypropertylistings.com.au/extensions/advanced-mapping/?utm_source=offers&utm_medium=description_tab&utm_content=post_link&utm_campaign=epl_extension_advanced_map',
			'img' => 'offer-advanced-mapping.png',
			'alt' => __('Advanced Mapping', 'easy-property-listings' ),
			'width'	=> 175,
		),
		array(
			'url' => 'https://easypropertylistings.com.au/extensions/sliders/?utm_source=offers&utm_medium=description_tab&utm_content=post_link&utm_campaign=epl_extension_sliders',
			'img' => 'offer-sliders.png',
			'alt' => __('Sliders extension', 'easy-property-listings' ),
			'width'	=> 175,
		),
		array(
			'url' => 'https://easypropertylistings.com.au/extensions/staff-directory/?utm_source=offers&utm_medium=description_tab&utm_content=post_link&utm_campaign=epl_extension_staff_directory',
			'img' => 'offer-staff-directory.png',
			'alt' => __('Staff Directory', 'easy-property-listings' ),
			'width'	=> 175,
		),
		array(
			'url' => 'https://easypropertylistings.com.au/extensions/listing-templates/?utm_source=offers&utm_medium=description_tab&utm_content=post_link&utm_campaign=epl_extension_listing_templates',
			'img' => 'offer-templates.png',
			'alt' => __('Listing Templates', 'easy-property-listings' ),
			'width'	=> 175,
		)
	);
	$i = 0;
	foreach ( $service_banners as $banner ) {
		echo '<a target="_blank" href="' . esc_url( $banner['url'] ) . '">
			<img style="display: block; float: left; margin: 0.5em 0.5em 0.5em 0" width="' . $banner['width'] . '" src="' .plugins_url( 'lib/assets/images/' . $banner['img'], EPL_PLUGIN_FILE ) .'" alt="' . esc_attr( $banner['alt'] ) . '"/>
		      </a>';
		$i ++;
	}
	?>
<?php
}

/**
 * Notice presented on post types, removed if extension is present and activated
 *
 * @since 3.1.6
 */
function epl_dashboard_widget_offer_post_types_last () {
	if ( has_filter( 'epl_extensions_options_filter_new' ) || get_transient( 'epl_admin_notices_display' ) == '1' )
		return;

	echo '<a target="_blank" href="https://easypropertylistings.com.au/extensions/?utm_source=offers&utm_medium=description_tab&utm_content=extensions_link&utm_campaign=epl_extension_store">
			<img style="display: block; float: right; margin: 0.5em 0 0.5em 0.5em" width="125" src="' . plugins_url( 'lib/assets/images/' . 'banner-dash-wide-store-reverse.png' , EPL_PLUGIN_FILE ) .'" alt="' . __('Purchase and activate any extension to remove these ads', 'easy-property-listings' ) . '"/>
		</a>';
	?>
<?php
}

/**
 * Admin notices
 *
 * @since 3.1.6
*/
function epl_extension_notice_offer() {

	if ( has_filter( 'epl_extensions_options_filter_new' ) || get_transient( 'epl_admin_notices_display' ) == '1' )
		return;

	$screen = get_current_screen();

	if( is_epl_core_post() == true || $screen->id == 'easy-property-listings_page_epl-reports' || $screen->id == 'easy-property-listings_page_epl-contacts' || $screen->id == 'toplevel_page_epl-general' ) {

		echo '<div class="notice notice-success" style="max-height: 130px; overflow: hidden;">';
			echo '<div class="epl-offers epl-offers-left" style="box-sizing: border-box; width: 85%; float: left; height: 125px">';
				epl_dashboard_widget_offer_post_types();
			echo '</div>';

			echo '<div class="epl-offers epl-offers-right" style="box-sizing: border-box; width: 15%; float: left;  height: 125px">';
				epl_dashboard_widget_offer_post_types_last();
			echo '</div>';
		echo '</div>';
	}
}
add_action( 'admin_notices', 'epl_extension_notice_offer' );