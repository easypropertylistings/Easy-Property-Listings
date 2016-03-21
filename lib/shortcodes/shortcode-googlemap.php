<?php
/**
 * SHORTCODE :: Simple Google Map
 *
 * @package     EPL
 * @subpackage  Shortcode/Map
 * @copyright   Copyright (c) 2014, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Listing Google Map [listing_map]
 *
 * @since       1.0
 */
function epl_shortcode_googlemap_callback($atts, $content = null) {
	global $property;
	extract( shortcode_atts( array(
		'width' 		=> '100%',
		'height' 		=> '350',
		'zoom' 			=> '17',
		'q' 			=> '',
		'cord'			=> '',
		'suburb_mode'		=> 0
	), $atts) );

	return '<div class="epl-tab-section">
				<div style="width:'.$width.'; height:'.$height.'px" data-suburb_mode="'.$suburb_mode.'" data-cord="'.$cord.'" data-zoom="'.$zoom.'" data-id="'.$property->post->ID.'" data-address="'.$q.'" id="epl-default-map">
			</div> </div>';
}
add_shortcode('listing_map', 'epl_shortcode_googlemap_callback');
