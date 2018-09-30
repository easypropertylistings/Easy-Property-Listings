<?php
/**
 * SHORTCODE :: Open For Inspection [listing_open]
 *
 * @package     EPL
 * @subpackage  Shortcode/ListingOpen
 * @copyright   Copyright (c) 2014, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Listing Open Shortcode
 *
 * This shortcode allows for you to specify the property type(s) using
 * [listing_open post_type="property,rental"] option. You can also
 * limit the number of entries that display. using  [epl-property-open limit="5"]
 *
 * @since       1.0
 *
 * @depricated since 3.2.3 use [listing] shortcode
 *
 * here for backward compatibility
 */
function epl_shortcode_property_open_callback( $atts ) {

	$atts['open_house'] = true;
	$atts['class'] 		= 'epl-shortcode-listing-open-house';
	$shortcode 			= new EPL_Shortcode_Listing($atts);
	return	$shortcode->render();
}
add_shortcode( 'home_open_list', 'epl_shortcode_property_open_callback' );
add_shortcode( 'listing_open', 'epl_shortcode_property_open_callback' );
