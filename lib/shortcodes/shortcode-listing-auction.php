<?php
/**
 * SHORTCODE :: Listing Auction [listing_auction]
 *
 * @package     EPL
 * @subpackage  Shortcode/Listing Auction
 * @copyright   Copyright (c) 2016, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Listing Auction Shortcode
 *
 * This shortcode allows for you to specify the property type(s) using
 * [listing_auction post_type="property,rental" status="current,sold,leased" template="default"] option. You can also
 * limit the number of entries that display. using  [listing_auction limit="5"]
 *
 * @since       1.0
 *
 * @depricated since 3.2.3 use [listing] shortcode
 *
 * here for backward compatibility
 */
function epl_shortcode_listing_auction_callback( $atts ) {

	$atts['auction'] 	= true;
	$shortcode 			= new EPL_Shortcode_Listing($atts);
	return	$shortcode->render();
}
add_shortcode( 'listing_auction', 'epl_shortcode_listing_auction_callback' );
