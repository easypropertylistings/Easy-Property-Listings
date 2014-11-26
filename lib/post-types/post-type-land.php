<?php
/**
 * Register post type :: Land
 *
 * @package     EPL
 * @subpackage  Meta
 * @copyright   Copyright (c) 2014, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */
 
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
 
/**
 * Registers and sets up the Land custom post type
 *
 * @since 1.0
 * @return void
 */
function epl_register_custom_post_type_land() {

	$archives = defined( 'EPL_LAND_DISABLE_ARCHIVE' ) && EPL_LAND_DISABLE_ARCHIVE ? false : true;
	$slug     = defined( 'EPL_LAND_SLUG' ) ? EPL_LAND_SLUG : 'land';
	$rewrite  = defined( 'EPL_LAND_DISABLE_REWRITE' ) && EPL_LAND_DISABLE_REWRITE ? false : array('slug' => $slug, 'with_front' => false);

	$labels = apply_filters( 'epl_land_labels', array(
		'name'					=>	__('Land', 'epl'),
		'singular_name'			=>	__('Land', 'epl'),
		'menu_name'				=>	__('Land', 'epl'),
		'add_new'				=>	__('Add New', 'epl'),
		'add_new_item'			=>	__('Add New Land Listing', 'epl'),
		'edit_item'				=>	__('Edit Land Listing', 'epl'),
		'new_item'				=>	__('New Land Listing', 'epl'),
		'update_item'			=>	__('Update Land Listing', 'epl'),
		'all_items'				=>	__('All Land Listings', 'epl'),
		'view_item'				=>	__('View Land Listing', 'epl'),
		'search_items'			=>	__('Search Land Listing', 'epl'),
		'not_found'				=>	__('Land Listing Not Found', 'epl'),
		'not_found_in_trash'	=>	__('Land Listing Not Found in Trash', 'epl'),
		'parent_item_colon'		=>	__('Parent Land Listing:', 'epl')
	) );
	
	$land_args = array(
		'labels'				=>	$labels,
		'public'				=>	true,
		'publicly_queryable'	=>	true,
		'show_ui'				=>	true,
		'show_in_menu'			=>	true,
		'query_var'				=>	true,
		'rewrite'				=>	$rewrite,
		'menu_icon'				=>	'dashicons-image-crop',
		//'menu_icon'			=>	plugins_url( 'post-types/icons/land.png' , dirname(__FILE__) ),
		'capability_type'		=>	'post',
		'has_archive'			=>	$archives,
		'hierarchical'			=>	false,
		'menu_position'			=>	'26.3',
		'taxonomies'			=>	array( 'location', 'tax_feature' ),
		'supports'				=>	apply_filters( 'epl_land_supports', array( 'title', 'editor', 'author', 'thumbnail', 'excerpt' , 'comments' ) ),
	);
	epl_register_post_type( 'land', 'Land', apply_filters( 'epl_land_post_type_args', $land_args ) );
}
add_action( 'init', 'epl_register_custom_post_type_land', 0 );
 
/**
 * Manage Admin Land Post Type Columns
 *
 * @since 1.0
 * @return void
 */
if ( is_admin() ) {
	/**
	 * Manage Admin Land Post Type Columns: Heading
	 *
	 * @since 1.0
	 * @return void
	 */
	function epl_manage_land_columns_heading( $columns ) {
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
	add_filter( 'manage_edit-land_columns', 'epl_manage_land_columns_heading' ) ;

	/**
	 * Manage Admin Land Post Type Columns: Row Contents
	 *
	 * @since 1.0
	 */
	function epl_manage_land_columns_value( $column, $post_id ) {
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
				
				$land = get_post_meta( $post_id, 'property_land_area', true );
				$land_unit = get_post_meta( $post_id, 'property_land_area_unit', true );
				
				if ( empty( $heading) ) {
					echo '<strong>'.__( 'Important! Set a Heading', 'epl' ).'</strong>';
				} else {
					echo '<div class="type_heading"><strong>' , $heading , '</strong></div>';
				}		
				
				echo '<div class="type_suburb">' , $property_address_suburb , '</div>';
				
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
	add_action( 'manage_land_posts_custom_column', 'epl_manage_land_columns_value', 10, 2 );

	/**
	 * Manage Land Columns Sorting
	 *
	 * @since 1.0
	 */
	function epl_manage_land_sortable_columns( $columns ) {
		$columns['property_status'] = 'property_status';
		$columns['property_address_suburb'] = 'property_address_suburb';
		return $columns;
	}
	add_filter( 'manage_edit-land_sortable_columns', 'epl_manage_land_sortable_columns' );
}
