<?php
/**
 * Register post type :: Rental
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
 * Registers and sets up the Rental custom post type
 *
 * @since 1.0
 * @return void
 */
function epl_register_custom_post_type_rental() {

	$archives = defined( 'EPL_RENTAL_DISABLE_ARCHIVE' ) && EPL_RENTAL_DISABLE_ARCHIVE ? false : true;
	$slug     = defined( 'EPL_RENTAL_SLUG' ) ? EPL_RENTAL_SLUG : 'rental';
	$rewrite  = defined( 'EPL_RENTAL_DISABLE_REWRITE' ) && EPL_RENTAL_DISABLE_REWRITE ? false : array('slug' => $slug, 'with_front' => false);

	$labels = apply_filters( 'epl_rental_labels', array(
		'name'			=>	__('Rentals', 'epl'),
		'singular_name'		=>	__('Rental', 'epl'),
		'menu_name'		=>	__('Rentals', 'epl'),
		'add_new'		=>	__('Add New', 'epl'),
		'add_new_item'		=>	__('Add New Rental', 'epl'),
		'edit_item'		=>	__('Edit Rental', 'epl'),
		'new_item'		=>	__('New Rental', 'epl'),
		'update_item'		=>	__('Update Rental', 'epl'),
		'all_items'		=>	__('All Rentals', 'epl'),
		'view_item'		=>	__('View Rental', 'epl'),
		'search_items'		=>	__('Search Rentals', 'epl'),
		'not_found'		=>	__('Rental Not Found', 'epl'),
		'not_found_in_trash'	=>	__('Rental Not Found in Trash', 'epl'),
		'parent_item_colon'	=>	__('Parent Rental:', 'epl')
	) );
	$rental_args = array(
		'labels'		=>	$labels,
		'public'		=>	true,
		'publicly_queryable'	=>	true,
		'show_ui'		=>	true,
		'show_in_menu'		=>	true,
		'query_var'		=>	true,
		'rewrite'		=>	$rewrite,
		'menu_icon'		=>	'dashicons-admin-home',
		'capability_type'	=>	'post',
		'has_archive'		=>	$archives,
		'hierarchical'		=>	false,
		'menu_position'		=>	'26.5',
		'taxonomies'		=>	array( 'location', 'tax_feature' ),
		'supports'		=>	apply_filters( 'epl_rental_supports', array( 'title', 'editor', 'author', 'thumbnail', 'excerpt' , 'comments' ) ),
	);
	epl_register_post_type( 'rental', 'Rental', apply_filters( 'epl_rental_post_type_args', $rental_args ) );
}
add_action( 'init', 'epl_register_custom_post_type_rental', 0 );

/**
 * Manage Admin Rental Post Type Columns
 *
 * @since 1.0
 * @return void
 */
if ( is_admin() ) {
	/**
	 * Manage Admin Rental Post Type Columns: Heading
	 *
	 * @since 1.0
	 * @return void
	 */
	function epl_manage_rental_columns_heading( $columns ) {
		global $epl_settings;

		$columns = array(
			'cb' 			=> '<input type="checkbox" />',
			'property_thumb'	=> __('Image', 'epl'),
			'property_price'	=> __('Rent', 'epl'),
			'title'			=> __('Address', 'epl'),
			'listing'		=> __('Listing Details', 'epl'),
			'listing_id'		=> __('Unique ID' , 'epl'),
			'geo'			=> __('Geocoded', 'epl'),
			'property_status'	=> __('Status', 'epl'),
			'agent'			=> __('Agent', 'epl'),
			'date'			=> __('Date', 'epl')
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
	add_filter( 'manage_edit-rental_columns', 'epl_manage_rental_columns_heading' ) ;

	/**
	 * Manage Admin Rental Post Type Columns: Row Contents
	 *
	 * @since 1.0
	 */
	function epl_manage_rental_columns_value( $column, $post_id ) {
		global $post,$property;
		global $epl_settings;
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

			/* If displaying the 'property_price' column. */
			case 'property_price' :
				do_action('epl_manage_listing_column_price_before');
				do_action('epl_manage_listing_column_price');
				do_action('epl_manage_listing_column_price_after');

				break;
			/* If displaying the 'property_status' column. */
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
	add_action( 'manage_rental_posts_custom_column', 'epl_manage_rental_columns_value', 10, 2 );
}
