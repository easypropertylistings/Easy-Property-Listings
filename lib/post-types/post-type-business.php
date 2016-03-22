<?php
/**
 * Register post type :: Business
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
 * Registers and sets up the Business custom post type
 *
 * @since 1.0
 * @return void
 */
function epl_register_custom_post_type_business() {

	$archives = defined( 'EPL_BUSINESS_DISABLE_ARCHIVE' ) && EPL_BUSINESS_DISABLE_ARCHIVE ? false : true;
	$slug     = defined( 'EPL_BUSINESS_SLUG' ) ? EPL_BUSINESS_SLUG : 'business';
	$rewrite  = defined( 'EPL_BUSINESS_DISABLE_REWRITE' ) && EPL_BUSINESS_DISABLE_REWRITE ? false : array('slug' => $slug, 'with_front' => false);

	$labels = apply_filters( 'epl_business_labels', array(
		'name'			=>	__('Business Listings', 'easy-property-listings' ),
		'singular_name'		=>	__('Business Listings', 'easy-property-listings' ),
		'menu_name'		=>	__('Business', 'easy-property-listings' ),
		'add_new'		=>	__('Add New', 'easy-property-listings' ),
		'add_new_item'		=>	__('Add New Business Listing', 'easy-property-listings' ),
		'edit_item'		=>	__('Edit Business Listing', 'easy-property-listings' ),
		'new_item'		=>	__('New Business Listing', 'easy-property-listings' ),
		'update_item'		=>	__('Update Business Listing', 'easy-property-listings' ),
		'all_items'		=>	__('All Business Listings', 'easy-property-listings' ),
		'view_item'		=>	__('View Business Listing', 'easy-property-listings' ),
		'search_items'		=>	__('Search Business Listing', 'easy-property-listings' ),
		'not_found'		=>	__('Business Listing Not Found', 'easy-property-listings' ),
		'not_found_in_trash'	=>	__('Business Listing Not Found in Trash', 'easy-property-listings' ),
		'parent_item_colon'	=>	__('Parent Business Listing:', 'easy-property-listings' )
	) );

	$business_args = array(
		'labels'		=>	$labels,
		'public'		=>	true,
		'publicly_queryable'	=>	true,
		'show_ui'		=>	true,
		'show_in_menu'		=>	true,
		'query_var'		=>	true,
		'rewrite'		=>	$rewrite,
		'menu_icon'		=>	'dashicons-cart',
		'capability_type'	=>	'post',
		'has_archive'		=>	$archives,
		'hierarchical'		=>	false,
		'menu_position'		=>	'26.6',
		'taxonomies'		=>	array( 'location', 'tax_feature' ),
		'supports'		=>	apply_filters( 'epl_business_supports', array( 'title', 'editor', 'author', 'thumbnail', 'excerpt' , 'comments' ) ),
	);
	epl_register_post_type( 'business', 'Business', apply_filters( 'epl_business_post_type_args', $business_args ) );
}
add_action( 'init', 'epl_register_custom_post_type_business', 0 );


/**
 * Manage Admin Business Post Type Columns
 *
 * @since 1.0
 * @return void
 */
if ( is_admin() ) {
	/**
	 * Manage Admin Business Post Type Columns: Heading
	 *
	 * @since 1.0
	 * @return void
	 */
	function epl_manage_business_columns_heading( $columns ) {
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
	add_filter( 'manage_edit-business_columns', 'epl_manage_business_columns_heading' ) ;

	/**
	 * Manage Admin Business Post Type Columns: Row Contents
	 *
	 * @since 1.0
	 */
	function epl_manage_business_columns_value( $column, $post_id ) {
		global $post,$property,$epl_settings;
		switch( $column ) {

			/* If displaying the 'Featured' image column. */
			case 'property_thumb' :
				do_action('epl_manage_listing_column_property_thumb_before');
				do_action('epl_manage_listing_column_property_thumb');
				do_action('epl_manage_listing_column_property_thumb_after');

				break;

			case 'listing' :
				/* Get the post meta. */
				$property_address_suburb	= get_the_term_list( $post->ID, 'location', '', ', ', '' );
				$heading			= get_post_meta( $post_id, 'property_heading', true );

				$category			= get_post_meta( $post_id, 'property_commercial_category', true );
				$homeopen 			= get_post_meta( $post_id, 'property_inspection_times', true );

				$outgoings			= get_post_meta( $post_id, 'property_com_outgoings', true );
				$return				= get_post_meta( $post_id, 'property_com_return', true );

				$land				= get_post_meta( $post_id, 'property_land_area', true );
				$land_unit			= get_post_meta( $post_id, 'property_land_area_unit', true );

				if ( empty( $heading) ) {
					echo '<strong>'.__( 'Important! Set a Heading', 'easy-property-listings'  ).'</strong>';
				} else {
					echo '<div class="type_heading"><strong>' , $heading , '</strong></div>';
				}

				if ( !empty( $category ) ) {
					echo '<div class="epl_meta_category">Category: ' , $category , '</div>';
				}

				echo '<div class="type_suburb">' , $property_address_suburb , '</div>';

				if ( !empty( $outgoings ) ) {
					echo '<div class="epl_meta_outgoings">Outgoings: ' , epl_currency_formatted_amount ( $outgoings ) , '</div>';
				}

				if ( !empty( $return ) ) {
					echo '<div class="epl_meta_baths">Return: ' , $return , '%</div>';
				}

				if ( !empty( $land) ) {
					echo '<div class="epl_meta_land_details">';
					echo '<span class="epl_meta_land">Land: ' , $land , '</span>';
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
				$price 			= get_post_meta( $post_id, 'property_price', true );
				$view			= get_post_meta( $post_id, 'property_price_view', true );
				$property_under_offer 	= get_post_meta( $post_id, 'property_under_offer', true );
				$lease 			= get_post_meta( $post_id, 'property_com_rent', true );
				$lease_period		= get_post_meta( $post_id, 'property_com_rent_period', true );
				$lease_date 		= get_post_meta( $post_id, 'property_com_lease_end_date', true );

				$max_price = '2000000';
				if(isset($epl_settings['epl_max_graph_sales_price' ])) {
					$max_price = (int) $epl_settings['epl_max_graph_sales_price' ];
				}
				$property_status = ucfirst( get_post_meta( $post_id, 'property_status', true ) );
				$property_authority = get_post_meta( $post_id, 'property_authority', true );
				$sold_price = get_post_meta( $post_id, 'property_sold_price', true );

				if ( !empty( $property_under_offer) && 'yes' == $property_under_offer ) {
					$class = 'bar-under-offer';
				}elseif ( $property_status == 'Current' ) {
					$class = 'bar-home-open';
				}elseif($property_status == 'Sold' || $property_status == 'Leased'){
					$class = 'bar-home-sold';
				}else{
					$class = '';
				}
				if($sold_price != ''){
					$barwidth = $max_price == 0 ? 0 : $sold_price/$max_price * 100;
				} else {
					$barwidth = $max_price == 0 ? 0 : $price/$max_price * 100;
				}
				echo '
					<div class="epl-price-bar '.$class.'">
						<span style="width:'.$barwidth.'%"></span>
					</div>';

				if ( !empty( $property_under_offer) && 'yes' == $property_under_offer ) {
					echo '<div class="type_under_offer">' .$property->label_under_offer. '</div>';
				}

				if ( empty ( $view ) ) {
					echo '<div class="epl_meta_search_price">'.__('Sale','easy-property-listings' ).': ' , epl_currency_formatted_amount( $price ), '</div>';
				} else {
					echo '<div class="epl_meta_price">' , $view , '</div>';
				}

				if ( !empty ( $lease ) ) {
					if ( empty ( $lease_period ) ) {
						$lease_period = 'annual';
					}
					echo '<div class="epl_meta_lease_price">Lease: ' , epl_currency_formatted_amount( $lease ), ' ' ,epl_listing_load_meta_commercial_rent_period_value( $lease_period ) ,'</div>';
				}

				if ( !empty ( $lease_date ) ) {
					echo '<div class="epl_meta_lease_date">'.__('Lease End','easy-property-listings' ).': ' ,  $lease_date , '</div>';
				}
				if($property_authority == 'auction' ) {
					_e('Auction ','easy-property-listings' );

					echo '<br>'.$property->get_property_auction(true);
				}
				break;

			/* If displaying the 'Commercial Listing Type' column. */
			case 'listing_type' :
				/* Get the post meta. */
				$listing_type = get_post_meta( $post_id, 'property_com_listing_type', true );

				/* If no duration is found, output a default message. */
				if ( ! empty( $listing_type) )
					echo $listing_type;

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
	add_action( 'manage_business_posts_custom_column', 'epl_manage_business_columns_value', 10, 2 );

	/**
	 * Manage Business Columns Sorting
	 *
	 * @since 1.0
	 */
	function epl_manage_business_sortable_columns( $columns ) {
		$columns['property_status'] = 'property_status';
		return $columns;
	}
	add_filter( 'manage_edit-business_sortable_columns', 'epl_manage_business_sortable_columns' );
}
