<?php
/**
 * SHORTCODE :: Listing [listing]
 *
 * @package     EPL
 * @subpackage  Shortcode/Listing
 * @copyright   Copyright (c) 2014, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Listing Shortcode [listing]
 *
 * This shortcode allows for you to specify the property type(s) using
 * [listing post_type="property,rental" status="current,sold,leased" template="default"] option. You can also
 * limit the number of entries that display. using  [listing limit="5"]
 *
 * @since       1.0
 */
function epl_shortcode_listing_callback( $atts ) {


	$property_types = epl_get_active_post_types();
	if ( ! empty($property_types ) ) {
		$property_types = array_keys( $property_types );
	}

	$attributes = shortcode_atts( array(
		'post_type'	=> $property_types, //Post Type
		'status'	=> array( 'current', 'sold', 'leased' ),
		'limit'		=> '10', // Number of maximum posts to show
		'offset'	=> '', // Offset posts. When used, pagination is disabled
		'author'	=> '',	// Author of listings.
		'featured'	=> 0,	// Featured listings.
		'template'	=> false, // Template can be set to "slim" for home open style template
		'location'	=> '', // Location slug. Should be a name like sorrento
		'tools_top'	=> 'off', // Tools before the loop like Sorter and Grid on or off
		'tools_bottom'	=> 'off', // Tools after the loop like pagination on or off
		'sortby'	=> '', // Options: price, date : Default date
		'sort_order'	=> 'DESC', // Sort by ASC or DESC
		'query_object'	=> '', // only for internal use . if provided use it instead of custom query
		'pagination'	=> 'on' // Enable or disable pagination
	), $atts );

	if ( is_string( $attributes['post_type'] ) && $attributes['post_type'] == 'rental' ) {
		$meta_key_price = 'property_rent';
	} else {
		$meta_key_price = 'property_price';
	}

	$sort_options = array(
		'price'			=>	$meta_key_price,
		'date'			=>	'post_date',
	);
	if ( ! is_array( $attributes['post_type'] ) ) {
		$attributes['post_type'] = array_map( 'trim', explode( ',',$attributes['post_type'] ) );
	}

	$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
	$args = array(
		'post_type'      =>	$attributes['post_type'],
		'posts_per_page' =>	$attributes['limit'],
		'paged'          =>	absint( $paged ),
	);

	// Offset query does not work with pagination
	if ( ! empty ( $attributes['offset'] ) ) {
		$args['offset'] 		= $attributes['offset'];
		$attributes['pagination'] 	= 'off'; // Disable pagination when offset is used
	}

	$args['meta_query'] = epl_parse_atts($atts);

	// Listings of specified author.
	if ( ! empty( $attributes['author'] ) ) {
		$attributes['author'] = (array) $attributes['author'];
		if ( is_array( $attributes['author'] ) ) {
			$author_ids = array_map( 'epl_get_author_id_from_name', $attributes['author'] );
			$attributes['author'] = implode( ',', $author_ids );
		}
		$args['author'] = trim( $attributes['author'] );
	}

	// Featured listings.
	if ( $attributes['featured'] ) {
		$args['meta_query'][] = array(
			'key'   => 'property_featured',
			'value' => 'yes',
		);
	}

	if ( ! empty( $attributes['location'] ) ) {
		if ( ! is_array( $attributes['location'] ) ) {
			$attributes['location'] = array_map( 'trim', explode( ',', $attributes['location'] ) );

			$args['tax_query'][] = array(
				'taxonomy'	=> 'location',
				'field'		=> 'slug',
				'terms' 	=> $attributes['location'],
			);
		}
	}

	if ( ! empty( $attributes['status'] ) ) {
		if ( ! is_array( $attributes['status'] ) ) {
			$attributes['status'] = array_map( 'trim', explode( ',', $attributes['status'] ) );

			$args['meta_query'][] = array(
				'key'		=> 'property_status',
				'value'		=> $attributes['status'],
				'compare'	=> 'IN',
			);

			add_filter( 'epl_sorting_options', 'epl_sorting_options_callback' );
		}
	}

	if ( ! empty ( $attributes['sortby'] ) ) {
		if ( $attributes['sortby'] == 'price' ) {
			$args['orderby']  = 'meta_value_num';
			$args['meta_key'] =	$meta_key_price;
		} else {
			$args['orderby']  = 'post_date';
			$args['order']    = 'DESC';
		}
		$args['order']        = $attributes['sort_order'];
	}

	// add sortby arguments to query, if listings sorted by $_GET['sortby'];
	$args = epl_add_orderby_args($args);
	$query_open = new WP_Query( $args );

	if ( is_object( $attributes['query_object'] ) ) {
		$query_open = $attributes['query_object'];
	}

	ob_start();
	epl_get_template_part(
		'shortcode-listing.php',
		array(
			'attributes' => $attributes,
			'query_open' => $query_open,
		)
	);
	return ob_get_clean();
}
add_shortcode( 'listing', 'epl_shortcode_listing_callback' );

/**
 * Listing Shortcode Sorting
 *
 * @since       1.0
 */
function epl_sorting_options_callback( $sorters ) {
	foreach ( $sorters as $key => &$sorter ) {
		if ( $sorter['id'] == 'status_asc' || $sorter['id'] == 'status_desc' ) {
			unset( $sorters[ $key ] );
		}
	}
	return $sorters;
}

/**
 * Get author id from name
 *
 * @since       3.1.1
 */
function epl_get_author_id_from_name($author) {
	if( is_numeric($author) ) {
		return absint($author);
	} else {
		$user = get_user_by( 'login', $author );
		return $user->ID;
	}
}
