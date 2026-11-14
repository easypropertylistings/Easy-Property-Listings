<?php
/**
 * SHORTCODE :: Listing [listing]
 *
 * @package     EPL
 * @subpackage  Shortcode/Listing
 * @copyright   Copyright (c) 2020, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 * @since       3.4 support for third and fourth agents.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// phpcs:disable WordPress.DB.SlowDBQuery

/**
 * Listing Shortcode [listing]
 *
 * This shortcode allows for you to specify the property type(s) using
 * [listing post_type="property,rental" status="current,sold,leased" template="default"] option. You can also
 * limit the number of entries that display. using  [listing limit="5"]
 *
 * Since 3.6 [listing] and [listing_advanced] are the same shortcode, so [listing] also takes
 * feature="pool", location_id="12", open_house="true", auction="true", post__in="1,2",
 * orderby_clause and the other [listing_advanced] options.
 *
 * @param array $atts Shortcode attributes.
 *
 * @return false|string
 * @since       1.0
 * @since       3.5 make sure meta_query in arg is defined as array, prior usage.
 * @since       3.6 Runs on the [listing_advanced] engine (EPL_Shortcode_Listing), so it takes every [listing_advanced] option. Existing [listing] options give the same query and output as before.
 */
function epl_shortcode_listing_callback( $atts ) {
	$shortcode = new EPL_Shortcode_Listing( $atts );

	return $shortcode->render();
}
add_shortcode( 'listing', 'epl_shortcode_listing_callback' );

/**
 * Listing Shortcode Sorting
 *
 * @param array $sorters Array of sorters.
 *
 * @return array
 * @since 1.0
 */
function epl_sorting_options_callback( $sorters ) {
	foreach ( $sorters as $key => &$sorter ) {
		if ( 'status_asc' === $sorter['id'] || 'status_desc' === $sorter['id'] ) {
			unset( $sorters[ $key ] );
		}
	}
	return $sorters;
}
