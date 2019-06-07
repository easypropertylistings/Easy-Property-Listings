<?php
/**
 * SHORTCODE :: Listing [listing]
 *
 * @package     EPL
 * @subpackage  Shortcode/Listing
 * @copyright   Copyright (c) 2019, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.3
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Listing Shortcode [listing]
 *
 * This shortcode allows for you to specify the property type(s) using
 * [listing_advanced post_type="property,rental" status="current,sold,leased" template="default"] option. You can also
 * limit the number of entries that display. using  [listing limit="5"]
 *
 * @since       1.0
 */
function epl_shortcode_listing_advanced_callback( $atts ) {

	$shortcode = new EPL_Advanced_Shortcode_Listing($atts);

	return	$shortcode->render();

}
add_shortcode( 'listing_advanced', 'epl_shortcode_listing_advanced_callback' );