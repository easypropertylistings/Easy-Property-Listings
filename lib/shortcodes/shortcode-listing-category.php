<?php
/**
 * SHORTCODE :: Listing Category [listing_category]
 *
 * @package     EPL
 * @subpackage  Shortcode/ListingCategory
 * @copyright   Copyright (c) 2014, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.1.1
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Listing Category
 *
 * This shortcode allows for you to specify the property type(s) using
 * [listing_category post_type="property" status="current,sold,leased" category_key="property_rural_category" category_key="farm"] option. You can also
 * limit the number of entries that display. using  [listing_category limit="5"]
 * Added Commercial Category Support
 *
 * @depricated since 3.2.3 use [listing] shortcode
 *
 * here for backward compatibility
 * 
 * @since       1.1.1
 */
function epl_shortcode_listing_category_callback( $atts ) {
	
	if(!empty($atts['category_key']) && !empty($atts['category_value'])) {

		$atts['category_compare'] = !empty( $atts['category_compare'] ) ? 
		$atts['category_compare'] : '='; 

		$compare_operators = array(
			'_min'			=>	'>=',
			'_max'			=>	'<=',
			'_not_equal'	=>	'!=',
			'_like'			=>	'LIKE', 
			'_not_like'		=>	'NOT LIKE', 
			'_exists'		=>	'EXISTS', 
			'_not_exists'	=>	'NOT EXISTS', 
			'_in'			=>	'IN',
			'_not_in'		=>	'NOT IN',
			'_between'		=>	'BETWEEN',
			'_not_between'	=>	'NOT BETWEEN'
		);

		$suffix = '';

		if( false !== $suffix = array_search($atts['category_compare'], $compare_operators) ){
			
		}

		$atts['epl_meta_'.$atts['category_key'].$suffix] = $atts['category_value'];

	}

	$atts['class'] 		= 'epl-shortcode-listing-category';
	$shortcode = new EPL_Shortcode_Listing($atts);
	return	$shortcode->render();

}
add_shortcode( 'listing_category', 'epl_shortcode_listing_category_callback' );
