<?php
/**
 * Outputs a property map onto the property template using a hook
 * This allows us to filter the hook and replace it with a different map
 *
 * @package     EPL
 * @subpackage  epl_property_map
 * @copyright   Copyright (c) 2014, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Pulls the address details so the map can be generated
 */
function epl_property_map_default_callback() {
	$address = '';
	
	$property_address_street_number = get_post_meta(get_the_ID(), 'property_address_street_number', true);
	if(!empty($property_address_street_number)) {
		$address .= $property_address_street_number . ' ';
	}
	
	$property_address_street = get_post_meta(get_the_ID(), 'property_address_street', true);
	if(!empty($property_address_street)) {
		$address .= $property_address_street . ' ';
	}
	
	$property_address_suburb = get_post_meta(get_the_ID(), 'property_address_suburb', true);
	if(!empty($property_address_suburb)) {
		$address .= $property_address_suburb . ',';
	}
	
	$property_address_state = get_post_meta(get_the_ID(), 'property_address_state', true);
	if(!empty($property_address_state)) {
		$address .= $property_address_state . ',';
	}
	
	$property_address_postal_code = get_post_meta(get_the_ID(), 'property_address_postal_code', true);
	if(!empty($property_address_postal_code)) {		
		$address .= $property_address_postal_code;
	}
	
	echo do_shortcode('[listing_map zoom=14 q="'.$address.'"]');
}
add_action('epl_property_map', 'epl_property_map_default_callback');
