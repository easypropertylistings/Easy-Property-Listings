<?php
/**
 * SHORTCODE :: Simple Google Map
 *
 * @package     EPL
 * @subpackage  Shortcode/Map
 * @copyright   Copyright (c) 2019, Merv Barrett
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
 */
function epl_shortcode_googlemap_callback( $atts, $content = null ) {
	global $property;
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

	return '<div class="epl-tab-section epl-section-map">
				<div style="width:' . esc_attr( $atts['width'] ) . '; height:' . esc_attr( $atts['height'] ) . 'px" data-suburb_mode="' . esc_attr( $atts['suburb_mode'] ) . '" data-cord="' . esc_attr( $atts['cord'] ) . '" data-zoom="' . esc_attr( $atts['zoom'] ) . '" data-id="' . esc_attr( $property->post->ID ) . '" data-address="' . esc_attr( $atts['q'] ) . '" id="epl-default-map">
			</div> </div>';
}
add_shortcode( 'listing_map', 'epl_shortcode_googlemap_callback' );
