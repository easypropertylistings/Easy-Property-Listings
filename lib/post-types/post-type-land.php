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
		'name'			=>	__('Land', 'epl'),
		'singular_name'		=>	__('Land', 'epl'),
		'menu_name'		=>	__('Land', 'epl'),
		'add_new'		=>	__('Add New', 'epl'),
		'add_new_item'		=>	__('Add New Land Listing', 'epl'),
		'edit_item'		=>	__('Edit Land Listing', 'epl'),
		'new_item'		=>	__('New Land Listing', 'epl'),
		'update_item'		=>	__('Update Land Listing', 'epl'),
		'all_items'		=>	__('All Land Listings', 'epl'),
		'view_item'		=>	__('View Land Listing', 'epl'),
		'search_items'		=>	__('Search Land Listing', 'epl'),
		'not_found'		=>	__('Land Listing Not Found', 'epl'),
		'not_found_in_trash'	=>	__('Land Listing Not Found in Trash', 'epl'),
		'parent_item_colon'	=>	__('Parent Land Listing:', 'epl')
	) );
	
	$land_args = array(
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
		'menu_position'		=>	'26.3',
		'taxonomies'		=>	array( 'location', 'tax_feature' ),
		'supports'		=>	apply_filters( 'epl_land_supports', array( 'title', 'editor', 'author', 'thumbnail', 'excerpt' , 'comments' ) ),
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
		global $epl_settings;
		
		$columns = array(
			'cb'			=> '<input type="checkbox" />',
			'property_thumb'	=> __('Image', 'epl'),
			'property_price'	=> __('Price', 'epl'),
			'title'			=> __('Address', 'epl'),
			'listing'		=> __('Listing Details', 'epl'),
			'listing_id'		=> __('Unique ID' , 'epl'),
			'geo'			=> __('Geo', 'epl'),
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
	add_filter( 'manage_edit-land_columns', 'epl_manage_land_columns_heading' ) ;

	/**
	 * Manage Admin Land Post Type Columns: Row Contents
	 *
	 * @since 1.0
	 */
	function epl_manage_land_columns_value( $column, $post_id ) {
		global $post,$property,$epl_settings;
		switch( $column ) {
		
			/* If displaying the 'Featured' image column. */
			case 'property_thumb' :
				/* Get the featured Image */
				if( function_exists('the_post_thumbnail') ) {
					$thumb_size = isset($epl_settings['epl_admin_thumb_size'])? $epl_settings['epl_admin_thumb_size'] : 'admin-list-thumb';
					the_post_thumbnail($thumb_size);
				}
				break;
				
			case 'listing' :
				/* Get the post meta. */
				$property_address_suburb	= get_the_term_list( $post->ID, 'location', '', ', ', '' );
				$heading			= get_post_meta( $post_id, 'property_heading', true );
				$land				= get_post_meta( $post_id, 'property_land_area', true );
				$land_unit			= get_post_meta( $post_id, 'property_land_area_unit', true );
				$homeopen 			= get_post_meta( $post_id, 'property_inspection_times', true );
				
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
					$homeopen = array_filter(explode( "\n", $homeopen ));
						$homeopen_list =  '<ul class="epl_meta_home_open">';
						foreach ( $homeopen as $num => $item ) {
						  $homeopen_list .= '<li>' . htmlspecialchars( $item ) . '</li>';
						}
						$homeopen_list .= '</ul>';
					echo '<div class="epl_meta_home_open_label"><span class="home-open"><strong>'.$epl_settings['label_home_open'].'</strong></span>' , $homeopen_list , '</div>';
				}
			
				break;
			
			/* If displaying the 'Listing ID' column. */
			case 'listing_id' :
				/* Get the post meta. */
				$unique_id	= get_post_meta( $post_id, 'property_unique_id', true );
				/* If no duration is found, output a default message. */
				if (  !empty( $unique_id ) )
					echo $unique_id;
				break;
				
			/* If displaying the 'Geocoding' column. */
			case 'geo' :
				/* Get the post meta. */
				$property_address_coordinates = get_post_meta( $post_id, 'property_address_coordinates', true );
				/* If no duration is found, output a default message. */
				if (  $property_address_coordinates == ',' || empty($property_address_coordinates ) )
					_e('No','epl') ;
				/* If there is a duration, append 'minutes' to the text string. */
				else
					echo $property_address_coordinates;
				break;
				
			/* If displaying the 'Price' column. */
			case 'property_price' :
				$price = get_post_meta( $post_id, 'property_price', true );
				$view = get_post_meta( $post_id, 'property_price_view', true );
				$property_under_offer = get_post_meta( $post_id, 'property_under_offer', true );
				$property_authority = get_post_meta( $post_id, 'property_authority', true );
				
				$max_price = '2000000';
				if(isset($epl_settings['epl_max_graph_sales_price' ])) {
					$max_price = (int) $epl_settings['epl_max_graph_sales_price' ];
				}

				$property_status = ucfirst( get_post_meta( $post_id, 'property_status', true ) );
				$sold_price = get_post_meta( $post_id, 'property_sold_price', true );
				
				if ( !empty( $property_under_offer) && 'yes' == $property_under_offer ) {
					$class = 'bar-under-offer';
				}elseif ( $property_status == 'Current' ) {
					$class = 'bar-home-open';
				}elseif($property_status == 'Sold'){
					$class = 'bar-home-sold';
				}else{
					$class = '';
				}
				if($sold_price != ''){
					$barwidth = $max_price == 0 ? 0: $sold_price/$max_price * 100;
				} else {
					$barwidth = $max_price == 0 ? 0: $price/$max_price * 100;
				}
				echo '
					<div class="epl-price-bar '.$class.'">
						<span style="width:'.$barwidth.'%"></span>
					</div>';


				if ( !empty( $property_under_offer) && 'yes' == $property_under_offer ) {
					echo '<div class="type_under_offer">' . $property->label_under_offer . '</div>';
				}
				if ( empty ( $view ) ) {
					echo '<div class="epl_meta_search_price">' , epl_currency_formatted_amount( $price ), '</div>';
				} else {
					echo '<div class="epl_meta_price">' , $view , '</div>'; 
				}
				if($property_authority == 'auction' ) {
					_e('Auction ','epl');
					echo '<br>'.$property->get_property_auction(true);
				}
				break;
			/* If displaying the 'real-estate' column. */
			case 'property_status' :
				/* Get the genres for the post. */
				$property_status = get_post_meta( $post_id, 'property_status', true );
				$labels_property_status = apply_filters (  'epl_labels_property_status_filter', array(
					'current' 	=> __('Current', 'epl'),
					'withdrawn' 	=> __('Withdrawn', 'epl'),
					'offmarket' 	=> __('Off Market', 'epl'),
					'sold'  	=> $property->label_sold,
					'leased'  	=> $property->label_leased
					)
				);
				if ( ! empty ( $property_status ) ) {
					echo '<span class="type_'.strtolower($property_status).'">'.$labels_property_status[$property_status].'</span>';
				}
				break;
				
				case 'agent':
				printf( '<a href="%s">%s</a>',
					esc_url( add_query_arg( array( 'post_type' => $post->post_type, 'author' => get_the_author_meta( 'ID' ) ), 'edit.php' )),
					get_the_author()
				);
				
				$property_second_agent = $property->get_property_meta('property_second_agent');
				if ( '' != $property_second_agent ) {
					$second_author = get_user_by( 'login' , $property_second_agent );
					if($second_author !== false){
						printf( '<br><a href="%s">%s</a>',
							esc_url( add_query_arg( array( 'post_type' => $post->post_type, 'author' => $second_author->ID ), 'edit.php' )),
							get_the_author_meta('display_name', $second_author->ID) 
						);

					}
					epl_reset_post_author();
				}
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
