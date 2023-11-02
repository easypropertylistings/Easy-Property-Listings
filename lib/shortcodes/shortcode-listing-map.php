<?php
/**
 * SHORTCODE :: Simple Google Map
 *
 * @package     EPL
 * @subpackage  Shortcode/Map
 * @copyright   Copyright (c) 2020, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Listing Google Map [listing_map]
 *
 * @param array $atts Shortcode attributes.
 * @param array $content Content.
 *
 * @return string
 * @since 1.0.0
 * @since 3.4.18 Added wrapper class epl-section-map.
 * @since 3.5.0 Fixed warnings. Added width, height and zoop options, allowed usage on any page and renamed file.
 */
function epl_shortcode_googlemap_callback( $atts, $content = null ) {
	global $property;

	$id = 0;

	if ( ! is_null( $property ) ) {
		$id = $property->post->ID;
	}

		$defaults = array(
			'width'       => '100%',  // Pass the width.
			'height'      => '350px', // Pass the height.
			'zoom'        => '17',    // Map zoom level.
			'q'           => '',      // The Address to query.
			'cord'        => '',      // Lat/Long coordinates.
			'suburb_mode' => 0,       // Suburb mode.
		);

	$attributes = shortcode_atts(
		$defaults,
		$atts
	);

	if ( empty( $attributes['cord'] ) && empty( $attributes['q'] ) ) {
		return '';
	}

	if ( ! is_epl_post() ) {
		// Enqueue the Google Maps script if the shortcode is loaded on a page.
		epl_shortcode_googlemap_enqueue();
	}

	return '<div class="epl-tab-section epl-section-map epl-default-map-wrapper">
			<div style="width:' . esc_attr( $attributes['width'] ) . '; height:' . esc_attr( $attributes['height'] ) . '" data-suburb_mode="' . esc_attr( $attributes['suburb_mode'] ) . '" data-cord="' . esc_attr( $attributes['cord'] ) . '" data-zoom="' . esc_attr( $attributes['zoom'] ) . '" data-id="' . esc_attr( $id ) . '" data-address="' . esc_attr( $attributes['q'] ) . '" class="epl-map-shortcode" id="epl-default-map"></div>
		</div>';
}
add_shortcode( 'listing_map', 'epl_shortcode_googlemap_callback' );


/**
 * Loads google map script on non EPL pages if listing_map shortcode is called.
 *
 * @since 3.5.0
 */
function epl_shortcode_googlemap_enqueue() {

	$googleapiurl       = 'https://maps.googleapis.com/maps/api/js?v=3.exp&callback=Function.prototype';
	$epl_google_api_key = epl_get_option( 'epl_google_api_key' );
	if ( ! empty( $epl_google_api_key ) ) {
		$googleapiurl = $googleapiurl . '&key=' . epl_get_option( 'epl_google_api_key' );
	}

	if ( epl_get_option( 'epl_disable_google_api' ) !== 'on' ) {
		wp_enqueue_script( 'google-map-v-3', $googleapiurl, array(), EPL_PROPERTY_VER, false );
	}
}
