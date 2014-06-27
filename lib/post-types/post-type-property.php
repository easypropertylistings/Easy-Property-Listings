<?php
/**
 * Register post type :: Property
 *
 * @package     EPL
 * @subpackage  Meta
 * @copyright   Copyright (c) 2014, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

function epl_register_custom_post_type_property() {
	$labels = array(
		'name'					=>	__('Properties', 'epl'),
		'singular_name'			=>	__('Property', 'epl'),
		'menu_name'				=>	__('Property', 'epl'),
		'add_new'				=>	__('Add New', 'epl'),
		'add_new_item'			=>	__('Add New Listing', 'epl'),
		'edit_item'				=>	__('Edit Listing', 'epl'),
		'new_item'				=>	__('New Listing', 'epl'),
		'update_item'			=>	__('Update Listing', 'epl'),
		'all_items'				=>	__('All Listings', 'epl'),
		'view_item'				=>	__('View Listing', 'epl'),
		'search_items'			=>	__('Search Listing', 'epl'),
		'not_found'				=>	__('Listing Not Found', 'epl'),
		'not_found_in_trash'	=>	__('Listing Not Found in Trash', 'epl'),
		'parent_item_colon'		=>	__('Parent Listing:', 'epl')
	);

	$args = array(
		'labels'				=>	$labels,
		'public'				=>	true,
		'publicly_queryable'	=>	true,
		'show_ui'				=>	true,
		'show_in_menu'			=>	true,
		'query_var'				=>	true,
		'rewrite'				=>	array( 'slug' => 'property' ),
		'menu_icon'				=>	'dashicons-admin-home',
		//'menu_icon'			=>	plugins_url( 'post-types/icons/home.png' , dirname(__FILE__) ),
		'capability_type'		=>	'post',
		'has_archive'			=>	true,
		'hierarchical'			=>	false,
		'menu_position'			=>	'26.2',
		'taxonomies'			=>	array( 'location', 'tax_feature' ),
		'supports'				=>	array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments', 'revisions' )
	);
	epl_register_post_type( 'property', 'Property (Residential)', $args );
}
add_action( 'init', 'epl_register_custom_post_type_property', 0 );

if ( is_admin() ) {
	// Manage Listing Columns
	function epl_manage_property_columns_heading( $columns ) {
		$columns = array(
			'cb' => '<input type="checkbox" />',
			'property_thumb' => __('Featured Image', 'epl'),
			'title' => __('Address', 'epl'),
			'listing' => __('Listing Details', 'epl'),
			'property_price' => __('Price', 'epl'),
			'geo' => __('Geo', 'epl'),
			'property_status' => __('Status', 'epl'),
			'author' => __('Agent', 'epl'),
			'date' => __('Date', 'epl')
		);
		
		$geo_debug = 0;
		global $epl_settings;
		if(!empty($epl_settings) && isset($epl_settings['debug'])) {
			$geo_debug = $epl_settings['debug'];
		}
		if ( $geo_debug != 1 ) {
			unset($columns['geo']);
		}
		return $columns;
	}
	add_filter( 'manage_edit-property_columns', 'epl_manage_property_columns_heading' ) ;

	function epl_manage_property_columns_value( $column, $post_id ) {
		global $post;
		switch( $column ) {	
			/* If displaying the 'Featured' image column. */
			case 'property_thumb' :
				/* Get the featured Image */
				if( function_exists('the_post_thumbnail') )
					echo the_post_thumbnail('admin-list-thumb');
				break;
				
			case 'listing' :
				/* Get the post meta. */
				$property_address_suburb = get_the_term_list( $post->ID, 'location', '', ', ', '' );
				$heading = get_post_meta( $post_id, 'property_heading', true );
				$homeopen = get_post_meta( $post_id, 'property_inspection_times', true );
			
				$beds = get_post_meta( $post_id, 'property_bedrooms', true );
				$baths = get_post_meta( $post_id, 'property_bathrooms', true );
				
				$land = get_post_meta( $post_id, 'property_land_area', true );
				$land_unit = get_post_meta( $post_id, 'property_land_area_unit', true );

				
				if ( empty( $heading) ) {
					echo '<strong>'.__( 'Important! Set a Heading', 'epl' ).'</strong>';
				} else {
					echo '<div class="type_heading"><strong>' , $heading , '</strong></div>';
				}		
				
				echo '<div class="type_suburb">' , $property_address_suburb , '</div>';

				echo '<div class="epl_meta_beds_baths">';
				echo '<span class="epl_meta_beds">' , $beds , ' Beds | </span>';
				echo '<span class="epl_meta_baths">' , $baths , ' Baths</span>';
				echo '</div>';
				
				if ( !empty( $land) ) {
					echo '<div class="epl_meta_land_details">';
					echo '<span class="epl_meta_land">' , $land , '</span>';
					echo '<span class="epl_meta_land_unit"> ' , $land_unit , '</span>';
					echo '</div>';
				}
				
				if ( !empty( $homeopen) ) {
					echo '<div class="epl_meta_home_open_label"><strong>Open: <span class="epl_meta_home_open">' , $homeopen , '</strong></span></div>';
				} 
			
				break;

			/* If displaying the 'Geocoding Debug' column. */
			case 'geo' :
				/* Get the post meta. */
				$property_address_coordinates = get_post_meta( $post_id, 'property_address_coordinates', true );

				/* If no duration is found, output a default message. */
				if (  $property_address_coordinates == ',' )
					echo 'NO' ;

				/* If there is a duration, append 'minutes' to the text string. */
				else
					// echo 'Yes';
					echo $property_address_coordinates;
				break;	
				
			/* If displaying the 'Price' column. */
			case 'property_price' :

				$price = get_post_meta( $post_id, 'property_price', true );
				$view = get_post_meta( $post_id, 'property_price_view', true );
				$property_under_offer = get_post_meta( $post_id, 'property_under_offer', true );
				
				if ( !empty( $property_under_offer) && 'yes' == $property_under_offer ) {
					echo '<div class="type_under_offer">Under Offer</div>';
				}

				if ( empty ( $view ) ) {
					echo '<div class="epl_meta_search_price">' , epl_currency_formatted_amount( $price ), '</div>';
				} else {
					echo '<div class="epl_meta_price">' , $view , '</div>'; 
				}
				break;

			/* If displaying the 'real-estate' column. */
			case 'property_status' :
				/* Get the genres for the post. */
				$property_status = ucfirst( get_post_meta( $post_id, 'property_status', true ) );
				echo '<span class="type_'.strtolower($property_status).'">'.$property_status.'</span>';
				break;

			/* Just break out of the switch statement for everything else. */
			default :
				break;
		}
	}
	add_action( 'manage_property_posts_custom_column', 'epl_manage_property_columns_value', 10, 2 );

	// Manage Columns Sorting
	function epl_manage_property_sortable_columns( $columns ) {
		//$columns['property_status'] = 'property_status';
		$columns['property_inspection_times'] = 'property_inspection_times';
		return $columns;
	}
	add_filter( 'manage_edit-property_sortable_columns', 'epl_manage_property_sortable_columns' );
}
