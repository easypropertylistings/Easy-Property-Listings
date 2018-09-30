<?php
/**
 * SHORTCODE :: Listing Feature [listing_feature]
 *
 * @package     EPL
 * @subpackage  Shortcode/Listing Feature
 * @copyright   Copyright (c) 2014, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.1.2
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Listing Feature
 *
 * This shortcode allows for you to specify feature property type(s) using
 * [listing_feature post_type="property" status="current,sold,leased" feature_id="" feature="" template="default"] option. You can also
 * limit the number of entries that display. using  [listing_category limit="5"]
 *
 * @since       1.1.2
 *
 * @depricated since 3.2.3 use [listing] shortcode
 *
 * here for backward compatibility
 * 
 */
function epl_shortcode_listing_tax_feature_callback( $atts ) {
	
	$atts['class'] 		= 'epl-shortcode-listing-feature';
	$shortcode = new EPL_Shortcode_Listing($atts);
	return	$shortcode->render();
}
add_shortcode( 'listing_feature', 'epl_shortcode_listing_tax_feature_callback' );
