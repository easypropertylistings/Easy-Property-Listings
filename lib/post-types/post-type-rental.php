<?php
/**
 * Register post type :: Rental
 *
 * @package     EPL
 * @subpackage  PostTypes/Rental
 * @copyright   Copyright (c) 2020, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers and sets up the Rental custom post type
 *
 * @return void
 *
 * @since 1.0
 * @since 3.5.1 View Listings added for plural.
 */
function epl_register_custom_post_type_rental() {

	$archives = defined( 'EPL_RENTAL_DISABLE_ARCHIVE' ) && EPL_RENTAL_DISABLE_ARCHIVE ? false : true;
	$slug     = defined( 'EPL_RENTAL_SLUG' ) ? EPL_RENTAL_SLUG : 'rental';
	$rewrite  = defined( 'EPL_RENTAL_DISABLE_REWRITE' ) && EPL_RENTAL_DISABLE_REWRITE ? false : array(
		'slug'       => $slug,
		'with_front' => false,
	);
	$rest     = defined( 'EPL_RENTAL_DISABLE_REST' ) && EPL_RENTAL_DISABLE_REST ? false : true;

	$labels      = apply_filters(
		'epl_rental_labels',
		array(
			'name'               => __( 'Rentals', 'easy-property-listings' ),
			'singular_name'      => __( 'Rental', 'easy-property-listings' ),
			'menu_name'          => __( 'Rentals', 'easy-property-listings' ),
			'add_new'            => __( 'Add New', 'easy-property-listings' ),
			'add_new_item'       => __( 'Add New Rental', 'easy-property-listings' ),
			'edit_item'          => __( 'Edit Rental', 'easy-property-listings' ),
			'new_item'           => __( 'New Rental', 'easy-property-listings' ),
			'update_item'        => __( 'Update Rental', 'easy-property-listings' ),
			'all_items'          => __( 'All Rentals', 'easy-property-listings' ),
			'view_item'          => __( 'View Rental', 'easy-property-listings' ),
			'view_items'         => __( 'View Rentals', 'easy-property-listings' ),
			'search_items'       => __( 'Search Rentals', 'easy-property-listings' ),
			'not_found'          => __( 'Rental Not Found', 'easy-property-listings' ),
			'not_found_in_trash' => __( 'Rental Not Found in Trash', 'easy-property-listings' ),
			'parent_item_colon'  => __( 'Parent Rental:', 'easy-property-listings' ),
		)
	);
	$rental_args = array(
		'labels'             => $labels,
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'rewrite'            => $rewrite,
		'menu_icon'          => 'dashicons-admin-home',
		'capability_type'    => 'post',
		'has_archive'        => $archives,
		'hierarchical'       => false,
		'menu_position'      => '26.5',
		'show_in_rest'       => $rest,
		'taxonomies'         => array( 'location', 'tax_feature' ),
		'supports'           => apply_filters( 'epl_rental_supports', array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' ) ),
	);
	epl_register_post_type( 'rental', 'Rental', apply_filters( 'epl_rental_post_type_args', $rental_args ) );
}
add_action( 'init', 'epl_register_custom_post_type_rental', 0 );

/**
 * Manage Admin Rental Post Type Columns.
 *
 * @since 1.0
 * @return void
 */
if ( is_admin() ) {
	/**
	 * Manage Admin Rental Post Type Columns: Heading.
	 *
	 * @param array $columns Column array.
	 *
	 * @return mixed|void $columns with epl_post_type_commercial_land_admin_columns filter.
	 * @since 1.0
	 */
	function epl_manage_rental_columns_heading( $columns ) {
		global $epl_settings;

		$columns = array(
			'cb'                => '<input type="checkbox" />',
			'property_featured' => '<span class="dashicons dashicons-star-half"></span><span class="epl-manage-featured">' . __( 'Featured', 'easy-property-listings' ) . '</span>',
			'property_thumb'    => __( 'Image', 'easy-property-listings' ),
			'property_rent'     => __( 'Rent', 'easy-property-listings' ),
			'title'             => __( 'Address', 'easy-property-listings' ),
			'listing'           => __( 'Listing Details', 'easy-property-listings' ),
			'listing_id'        => __( 'Unique ID', 'easy-property-listings' ),
			'geo'               => __( 'Geocoded', 'easy-property-listings' ),
			'property_status'   => __( 'Status', 'easy-property-listings' ),
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

		return apply_filters( 'epl_post_type_rental_admin_columns', $columns );
	}
	add_filter( 'manage_edit-rental_columns', 'epl_manage_rental_columns_heading' );

	/**
	 * Manage Admin Rental Post Type Columns: Row Contents.
	 *
	 * @since 1.0
	 * @param string $column column.
	 * @param int    $post_id post id.
	 */
	function epl_manage_rental_columns_value( $column, $post_id ) {

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

			// If displaying the 'property_rent' column.
			case 'property_rent':
				do_action( 'epl_manage_listing_column_price' );

				break;

			// If displaying the 'property_status' column.
			case 'property_status':
				do_action( 'epl_manage_listing_column_property_status' );

				break;

			// If displaying the 'agent' column.
			case 'agent':
				do_action( 'epl_manage_listing_column_agent' );

				break;

			// Just break out of the switch statement for everything else.
			default:
				break;
		}
	}
	add_action( 'manage_rental_posts_custom_column', 'epl_manage_rental_columns_value', 10, 2 );
}
