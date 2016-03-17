<?php
/**
 * Outputs a property map onto the property template using a hook
 * This allows us to filter the hook and replace it with a different map
 *
 * @package     EPL
 * @subpackage  Hooks/Map
 * @copyright   Copyright (c) 2014, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Pulls the address details so the map can be generated
 *
 * @since 1.0
 */
function epl_property_map_default_callback() {
	global $property;

	// only show map if address display is set to true
	if ( $property->get_property_meta('property_address_display') == 'yes' ) {

		$address = epl_property_get_the_full_address();
		$address = apply_filters('epl_map_address',$address);

		// use coordinates if they are already present
		$coordinates = $property->get_property_meta('property_address_coordinates');

		echo do_shortcode('[listing_map zoom=14 cord="'.$coordinates.'" q="'.$address.'"]');
	} else {

		$address  = $property->get_property_meta('property_address_suburb').', ';
		$address .= $property->get_property_meta('property_address_state').', ';
		$address .= $property->get_property_meta('property_address_postal_code');
		$address  = apply_filters('epl_map_address',$address);
		echo do_shortcode('[listing_map zoom=14 suburb_mode=1 q="'.$address.'"]');
	}



}
add_action('epl_property_map', 'epl_property_map_default_callback');
