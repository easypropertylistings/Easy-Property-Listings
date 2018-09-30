<?php
/**
 * SHORTCODE :: Listing Location Taxonomy [listing_location]
 *
 * @package     EPL
 * @subpackage  Shortcode/ListingLocation
 * @copyright   Copyright (c) 2014, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.1.2
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Listing Shortcode [listing_location]
 *
 * This shortcode allows for you to specify feature property type(s) using
 * [listing_location post_type="property" location="sorrento" location_id="6" status="current,sold,leased" template="default"] option. You can also
 * limit the number of entries that display. using  [listing_location limit="5"]
 *
 * @since       1.1.2
 * 
 * @depricated since 3.2.3 use [listing] shortcode
 *
 * here for backward compatibility

 */
function epl_shortcode_listing_tax_location_callback( $atts ) {

	$atts['class'] 		= 'epl-shortcode-listing-location';
	$shortcode = new EPL_Shortcode_Listing($atts);
	return	$shortcode->render();
}

add_shortcode( 'listing_location', 'epl_shortcode_listing_tax_location_callback' );
