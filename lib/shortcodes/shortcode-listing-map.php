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
 * @since 3.5.0 Fixed warnings.
 */
function epl_shortcode_googlemap_callback( $atts, $content = null ) {
	global $property;

	$id = 0;

	if ( ! is_null( $property ) ) {
		$id = $property->post->ID;
	}

	$atts = shortcode_atts(
		array(
			'width'       => '100%',
			'height'      => '350',
			'zoom'        => '17',
			'q'           => '',
			'cord'        => '',
			'suburb_mode' => 0,
		),
		$atts
	);

	if ( empty( $atts['cord'] ) && empty( $atts['q'] ) ) {
		return '';
	}

	if ( ! is_epl_post() ) {
		epl_shortcode_googlemap_enqueue();
	}

	return '<div class="epl-tab-section epl-section-map">
			<div style="width:' . esc_attr( $atts['width'] ) . '; height:' . esc_attr( $atts['height'] ) . 'px" data-suburb_mode="' . esc_attr( $atts['suburb_mode'] ) . '" data-cord="' . esc_attr( $atts['cord'] ) . '" data-zoom="' . esc_attr( $atts['zoom'] ) . '" data-id="' . esc_attr( $id ) . '" data-address="' . esc_attr( $atts['q'] ) . '" id="epl-default-map"></div>
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
