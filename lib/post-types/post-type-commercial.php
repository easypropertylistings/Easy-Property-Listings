<?php
/**
 * Register post type :: Commercial
 *
 * @package     EPL
 * @subpackage  PostTypes/Commercial
 * @copyright   Copyright (c) 2020, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers and sets up the Commercial custom post type.
 *
 * @since 1.0
 * @since 3.5.1 View Commercial Listings added for plural.
 *
 * @return void
 */
function epl_register_custom_post_type_commercial() {

	$archives = defined( 'EPL_COMMERCIAL_DISABLE_ARCHIVE' ) && EPL_COMMERCIAL_DISABLE_ARCHIVE ? false : true;
	$slug     = defined( 'EPL_COMMERCIAL_SLUG' ) ? EPL_COMMERCIAL_SLUG : 'commercial';
	$rewrite  = defined( 'EPL_COMMERCIAL_DISABLE_REWRITE' ) && EPL_COMMERCIAL_DISABLE_REWRITE ? false : array(
		'slug'       => $slug,
		'with_front' => false,
	);
	$rest     = defined( 'EPL_COMMERCIAL_DISABLE_REST' ) && EPL_COMMERCIAL_DISABLE_REST ? false : true;

	$labels = apply_filters(
		'epl_commercial_labels',
		array(
			'name'               => __( 'Commercial Listings', 'easy-property-listings' ),
			'singular_name'      => __( 'Commercial Listing', 'easy-property-listings' ),
			'menu_name'          => __( 'Commercial', 'easy-property-listings' ),
			'add_new'            => __( 'Add New', 'easy-property-listings' ),
			'add_new_item'       => __( 'Add New Commercial Listing', 'easy-property-listings' ),
			'edit_item'          => __( 'Edit Commercial Listing', 'easy-property-listings' ),
			'new_item'           => __( 'New Commercial Listing', 'easy-property-listings' ),
			'update_item'        => __( 'Update Commercial Listing', 'easy-property-listings' ),
			'all_items'          => __( 'All Commercial Listings', 'easy-property-listings' ),
			'view_item'          => __( 'View Commercial Listing', 'easy-property-listings' ),
			'view_items'         => __( 'View Commercial Listings', 'easy-property-listings' ),
			'search_items'       => __( 'Search Commercial Listing', 'easy-property-listings' ),
			'not_found'          => __( 'Commercial Listing Not Found', 'easy-property-listings' ),
			'not_found_in_trash' => __( 'Commercial Listing Not Found in Trash', 'easy-property-listings' ),
			'parent_item_colon'  => __( 'Parent Commercial Listing:', 'easy-property-listings' ),
		)
	);

	$commercial_args = array(
		'labels'             => $labels,
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'rewrite'            => $rewrite,
		'menu_icon'          => 'dashicons-welcome-widgets-menus',
		'capability_type'    => 'post',
		'has_archive'        => $archives,
		'hierarchical'       => false,
		'menu_position'      => '26.7',
		'show_in_rest'       => $rest,
		'taxonomies'         => array( 'location', 'tax_feature' ),
		'supports'           => apply_filters( 'epl_commercial_supports', array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' ) ),
	);
	epl_register_post_type( 'commercial', 'Commercial', apply_filters( 'epl_commercial_post_type_args', $commercial_args ) );
}
add_action( 'init', 'epl_register_custom_post_type_commercial', 0 );

/**
 * Manage Admin Commercial Post Type Columns.
 *
 * @since 1.0
 * @return void
 */
if ( is_admin() ) {
	/**
	 * Manage Admin Business Post Type Columns: Heading.
	 *
	 * @param array $columns Column array.
	 *
	 * @return mixed|void $columns with epl_post_type_commercial_land_admin_columns filter.
	 * @since 1.0
	 */
	function epl_manage_commercial_heading( $columns ) {
		global $epl_settings;

		$columns = array(
			'cb'                => '<input type="checkbox" />',
			'property_featured' => '<span class="dashicons dashicons-star-half"></span><span class="epl-manage-featured">' . __( 'Featured', 'easy-property-listings' ) . '</span>',
			'property_thumb'    => __( 'Featured Image', 'easy-property-listings' ),
			'property_price'    => __( 'Price', 'easy-property-listings' ),
			'title'             => __( 'Address', 'easy-property-listings' ),
			'listing'           => __( 'Listing Details', 'easy-property-listings' ),
			'listing_id'        => __( 'Unique ID', 'easy-property-listings' ),
			'geo'               => __( 'Geo', 'easy-property-listings' ),
			'property_status'   => __( 'Status', 'easy-property-listings' ),
			'listing_type'      => __( 'Sale/Lease', 'easy-property-listings' ),
			'agent'             => __( 'Agent', 'easy-property-listings' ),
			'date'              => __( 'Date', 'easy-property-listings' ),
		) + $columns;

		// unset author columns as duplicate of agent column.
		unset( $columns['author'] );
		unset( $columns['comments'] );

		// Geocode Column.
		if ( 1 !== (int) epl_get_option( 'debug', 0 ) ) {
			unset( $columns['geo'] );
		}

		// Listing ID Column.
		if ( 1 !== (int) epl_get_option( 'admin_unique_id', 0 ) ) {
			unset( $columns['listing_id'] );
		}

		return apply_filters( 'epl_post_type_commercial_admin_columns', $columns );
	}
	add_filter( 'manage_edit-commercial_columns', 'epl_manage_commercial_heading' );

	/**
	 * Manage Admin Commercial Post Type Columns: Row Contents
	 *
	 * @since 1.0
	 * @param string $column column.
	 * @param int    $post_id post id.
	 */
	function epl_manage_commercial_columns_value( $column, $post_id ) {

		switch ( $column ) {

			// If displaying the 'Featured Star' column.
			case 'property_featured':
				do_action( 'epl_manage_listing_column_featured' );

				break;

			// If displaying the 'Featured' image column.
			case 'property_thumb':
				do_action( 'epl_manage_listing_column_property_thumb' );

				break;

			// If displaying the 'Listing Details' column.
			case 'listing':
				do_action( 'epl_manage_listing_column_listing' );

				break;

			// If displaying the 'Listing ID' column.
			case 'listing_id':
				do_action( 'epl_manage_listing_column_listing_id' );

				break;

			// If displaying the 'Geocoding' column.
			case 'geo':
				do_action( 'epl_manage_listing_column_geo' );

				break;

			// If displaying the 'Price' column.
			case 'property_price':
				do_action( 'epl_manage_listing_column_price' );

				break;

			// If displaying the 'Commercial Listing Type' column.
			case 'listing_type':
				do_action( 'epl_manage_listing_column_listing_type' );

				break;

			// If displaying the 'property_status' column.
			case 'property_status':
				do_action( 'epl_manage_listing_column_property_status' );

				break;

			// If displaying the 'Agent' column.
			case 'agent':
				do_action( 'epl_manage_listing_column_agent' );

				break;

			// Just break out of the switch statement for everything else.
			default:
				break;
		}
	}
	add_action( 'manage_commercial_posts_custom_column', 'epl_manage_commercial_columns_value', 10, 2 );
}
