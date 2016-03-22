<?php
/**
 * Register post type :: Commercial Land
 *
 * @package     EPL
 * @subpackage  Functions/CPT
 * @copyright   Copyright (c) 2014, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Registers and sets up the Commercial Land custom post type
 *
 * @since 1.0
 * @return void
 */
function epl_register_custom_post_type_commercial_land() {

	$archives = defined( 'EPL_COMMERCIAL_LAND_DISABLE_ARCHIVE' ) && EPL_COMMERCIAL_LAND_DISABLE_ARCHIVE ? false : true;
	$slug     = defined( 'EPL_COMMERCIAL_LAND_SLUG' ) ? EPL_COMMERCIAL_LAND_SLUG : 'commercial-land';
	$rewrite  = defined( 'EPL_COMMERCIAL_LAND_DISABLE_REWRITE' ) && EPL_COMMERCIAL_LAND_DISABLE_REWRITE ? false : array('slug' => $slug, 'with_front' => false);

	$labels = apply_filters( 'epl_commercial_land_labels', array(
		'name'			=>	__('Commercial Land Listings', 'easy-property-listings' ),
		'singular_name'		=>	__('Commercial Land Listing', 'easy-property-listings' ),
		'menu_name'		=>	__('Commercial Land', 'easy-property-listings' ),
		'add_new'		=>	__('Add New', 'easy-property-listings' ),
		'add_new_item'		=>	__('Add New Commercial Land Listing', 'easy-property-listings' ),
		'edit_item'		=>	__('Edit Commercial Land Listing', 'easy-property-listings' ),
		'new_item'		=>	__('New Commercial Land Listing', 'easy-property-listings' ),
		'update_item'		=>	__('Update Commercial Land Listing', 'easy-property-listings' ),
		'all_items'		=>	__('All Commercial Land Listings', 'easy-property-listings' ),
		'view_item'		=>	__('View Commercial Land Listing', 'easy-property-listings' ),
		'search_items'		=>	__('Search Commercial Land Listing', 'easy-property-listings' ),
		'not_found'		=>	__('Commercial Land Listing Not Found', 'easy-property-listings' ),
		'not_found_in_trash'	=>	__('Commercial Land Listing Not Found in Trash', 'easy-property-listings' ),
		'parent_item_colon'	=>	__('Parent Commercial Land Listing:', 'easy-property-listings' )
	) );

	$commercial_land_args = array(
		'labels'		=>	$labels,
		'public'		=>	true,
		'publicly_queryable'	=>	true,
		'show_ui'		=>	true,
		'show_in_menu'		=>	true,
		'query_var'		=>	true,
		'rewrite'		=>	$rewrite,
		'menu_icon'		=>	'dashicons-image-crop',
		'capability_type'	=>	'post',
		'has_archive'		=>	$archives,
		'hierarchical'		=>	false,
		'menu_position'		=>	'26.8',
		'taxonomies'		=>	array( 'location', 'tax_feature' ),
		'supports'		=>	apply_filters( 'epl_commercial_land_supports', array( 'title', 'editor', 'author', 'thumbnail', 'excerpt' , 'comments' ) ),
	);
	epl_register_post_type( 'commercial_land', 'Commercial Land', apply_filters( 'epl_commercial_land_post_type_args', $commercial_land_args ) );
}
add_action( 'init', 'epl_register_custom_post_type_commercial_land', 0 );

/**
 * Manage Admin Commercial Land Post Type Columns
 *
 * @since 1.0
 * @return void
 */
if ( is_admin() ) {
	/**
	 * Manage Admin Commercial Land Post Type Columns: Heading
	 *
	 * @since 1.0
	 * @return void
	 */
	function epl_manage_commercial_land_columns_heading( $columns ) {
		global $epl_settings;

		$columns = array(
			'cb'			=> '<input type="checkbox" />',
			'property_thumb'	=> __('Image', 'easy-property-listings' ),
			'property_price'	=> __('Price', 'easy-property-listings' ),
			'title'			=> __('Address', 'easy-property-listings' ),
			'listing'		=> __('Listing Details', 'easy-property-listings' ),
			'listing_id'		=> __('Unique ID' , 'easy-property-listings' ),
			'geo'			=> __('Geo', 'easy-property-listings' ),
			'property_status'	=> __('Status', 'easy-property-listings' ),
			'listing_type'		=> __('Sale/Lease', 'easy-property-listings' ),
			'agent'			=> __('Agent', 'easy-property-listings' ),
			'date'			=> __('Date', 'easy-property-listings' )
		);

		// Geocode Column
		$geo_debug = !empty($epl_settings) && isset($epl_settings['debug']) ? $epl_settings['debug'] : 0;
		if ( $geo_debug != 1 ) {
			unset($columns['geo']);
		}

		// Listing ID Column
		$admin_unique_id = !empty($epl_settings) && isset($epl_settings['admin_unique_id']) ? $epl_settings['admin_unique_id'] : 0;
		if ( $admin_unique_id != 1 ) {
			unset($columns['listing_id']);
		}

		return $columns;
	}
	add_filter( 'manage_edit-commercial_land_columns', 'epl_manage_commercial_land_columns_heading' ) ;

	/**
	 * Manage Admin Commercial Land Post Type Columns: Row Contents
	 *
	 * @since 1.0
	 */
	function epl_manage_commercial_land_columns_value( $column, $post_id ) {
		switch( $column ) {

			/* If displaying the 'Featured' image column. */
			case 'property_thumb' :
				do_action('epl_manage_listing_column_property_thumb_before');
				do_action('epl_manage_listing_column_property_thumb');
				do_action('epl_manage_listing_column_property_thumb_after');

				break;

			case 'listing' :
				do_action('epl_manage_listing_column_listing_before');
				do_action('epl_manage_listing_column_listing');
				do_action('epl_manage_listing_column_listing_after');

				break;

			/* If displaying the 'Listing ID' column. */
			case 'listing_id' :
				do_action('epl_manage_listing_column_listing_id_before');
				do_action('epl_manage_listing_column_listing_id');
				do_action('epl_manage_listing_column_listing_id_after');

				break;

			/* If displaying the 'Geocoding' column. */
			case 'geo' :
				do_action('epl_manage_listing_column_geo_before');
				do_action('epl_manage_listing_column_geo');
				do_action('epl_manage_listing_column_geo_after');

				break;

			/* If displaying the 'Price' column. */
			case 'property_price' :
				do_action('epl_manage_listing_column_price_before');
				do_action('epl_manage_listing_column_price');
				do_action('epl_manage_listing_column_price_after');

				break;

			/* If displaying the 'Commercial Land Listing Type' column. */
			case 'listing_type' :
				do_action('epl_manage_listing_column_listing_type_before');
				do_action('epl_manage_listing_column_listing_type');
				do_action('epl_manage_listing_column_listing_type_after');

				break;

			/* If displaying the 'real-estate' column. */
			case 'property_status' :
				do_action('epl_manage_listing_column_property_status_before');
				do_action('epl_manage_listing_column_property_status');
				do_action('epl_manage_listing_column_property_status_after');

				break;

			case 'agent':
				do_action('epl_manage_listing_column_agent_before');
				do_action('epl_manage_listing_column_agent');
				do_action('epl_manage_listing_column_agent_after');

				break;

			/* Just break out of the switch statement for everything else. */
			default :
				break;
		}
	}
	add_action( 'manage_commercial_land_posts_custom_column', 'epl_manage_commercial_land_columns_value', 10, 2 );
}
