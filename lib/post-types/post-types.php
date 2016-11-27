<?php
/**
 * Custom Post Types Functions
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
 * Query filter for property_address_suburb custom field sortable in posts listing
 *
 * @since 1.0
 */
add_filter( 'request', 'epl_property_address_suburb_column_orderby' );
function epl_property_address_suburb_column_orderby( $vars ) {
	if ( isset( $vars['orderby'] ) && 'property_address_suburb' == $vars['orderby'] ) {
		$vars = array_merge( $vars, array(
			'meta_key' => 'property_address_suburb',
			'orderby' => 'meta_value'
		) );
	}

	return $vars;
}

// Add custom filters to post type posts listings
add_action( 'restrict_manage_posts', 'epl_custom_restrict_manage_posts' );
add_filter( 'parse_query', 'epl_admin_posts_filter' );

/**
 * Add custom filters to post type posts listings
 *
 * @since 1.0
 */
function epl_custom_restrict_manage_posts() {
	global $post_type;
	if($post_type == 'property' || $post_type == 'rental' || $post_type == 'land' || $post_type == 'commercial' || $post_type == 'rural' || $post_type == 'business' || $post_type == 'holiday_rental' || $post_type == 'commercial_land') {

		//Filter by property_status
		$fields = array(
			'current'	=>	__('Current', 'easy-property-listings' ),
			'withdrawn'	=>	__('Withdrawn', 'easy-property-listings' ),
			'offmarket'	=>	__('Off Market', 'easy-property-listings' )
		);

		if($post_type != 'rental' && $post_type != 'holiday_rental') {
			$fields['sold'] = apply_filters( 'epl_sold_label_status_filter' , __('Sold', 'easy-property-listings' ) );
		}

		if($post_type == 'rental' || $post_type == 'holiday_rental' || $post_type == 'commercial' || $post_type == 'business' || $post_type == 'commercial_land') {
			$fields['leased'] = apply_filters( 'epl_leased_label_status_filter' , __('Leased', 'easy-property-listings' ) );
		}

		if(!empty($fields)) {
			$_GET['property_status'] = isset($_GET['property_status'])?sanitize_text_field($_GET['property_status']):'';
			echo '<select name="property_status">';
				echo '<option value="">'.__('Filter By Type', 'easy-property-listings' ).'</option>';
				foreach($fields as $k=>$v) {
					$selected = ($_GET['property_status'] == $k ? 'selected="selected"' : '');
					echo '<option value="'.$k.'" '.$selected.'>'.__($v, 'easy-property-listings' ).'</option>';
				}
			echo '</select>';
		}

		$property_author =	isset($_GET['property_author']) ? intval($_GET['property_author']) : '';
		// filter by authors
		wp_dropdown_users(
			array(
				'name' 			=> 'property_author',
				'selected'		=> $property_author,
				'show_option_all'	=> __('All Users','easy-property-listings' )
			)
		);

		$custom_search_fields = array(
			'property_address_suburb'	=>	epl_labels('label_suburb'),
			'property_office_id'		=>	__('Office ID', 'easy-property-listings' ),
			'property_agent'		=>	__('Listing Agent', 'easy-property-listings' ),
			'property_second_agent'		=>	__('Second Listing Agent', 'easy-property-listings' ),
		);
		$custom_search_fields = apply_filters('epl_admin_search_fields',$custom_search_fields);

		if(!empty($custom_search_fields)) {
			$_GET['property_custom_fields'] = isset($_GET['property_custom_fields'])?sanitize_text_field($_GET['property_custom_fields']):'';
			echo '<select name="property_custom_fields">';
				echo '<option value="">'.__('Search For :', 'easy-property-listings' ).'</option>';
				foreach($custom_search_fields as $k=>$v) {
					$selected = ($_GET['property_custom_fields'] == $k ? 'selected="selected"' : '');
					echo '<option value="'.$k.'" '.$selected.'>'.__($v, 'easy-property-listings' ).'</option>';
				}
			echo '</select>';
		}

		//Filter by Suburb
		if(isset($_GET['property_custom_value'])) {
			$val = stripslashes($_GET['property_custom_value']);
		} else {
			$val = '';
		}
		echo '<input type="text" name="property_custom_value" placeholder="'.__('Search Value.', 'easy-property-listings' ).'" value="'.$val.'" />';
	}
}

/**
 * Admin Posts Filter
 *
 * @since 1.0
 */
function epl_admin_posts_filter( $query ) {
	global $pagenow;
	if( is_admin() && $pagenow == 'edit.php' ) {
		$meta_query = $query->get('meta_query');

		if(isset($_GET['property_status']) && $_GET['property_status'] != '') {
			$meta_query[] = array(
				'key'       => 'property_status',
				'value'     => $_GET['property_status']
			);
		}

		if(isset($_GET['property_author']) && $_GET['property_author'] != '') {
			$query->set( 'author', intval($_GET['property_author']) );
		}


		if( isset($_GET['property_custom_fields']) && trim($_GET['property_custom_fields']) != '' && isset($_GET['property_custom_value']) && trim($_GET['property_custom_value']) != '') {

			$meta_query[] = array(
				'key'       => sanitize_text_field( $_GET['property_custom_fields'] ),
				'value'     => sanitize_text_field( $_GET['property_custom_value'] ),
				'compare'   => 'LIKE',
			);

		}

		if(!empty($meta_query)) {
			$query->set('meta_query', $meta_query);
		}
	}
}

/**
 * Manage Property Columns Sorting
 *
 * @since 1.0
 */
function epl_manage_listings_sortable_columns( $columns ) {
	$columns['property_price']	= 'property_price';
	$columns['property_status'] 	= 'property_status';
	$columns['agent'] 		= 'agent';
	return $columns;
}

$epl_posts = array('property','land', 'commercial', 'business', 'commercial_land' , 'location_profile','rental','rural');

foreach($epl_posts  as $epl_post ) {
	add_filter( 'manage_edit-'.$epl_post.'_sortable_columns', 'epl_manage_listings_sortable_columns' );
}

/**
 * Manage Listing Columns Sorting
 *
 * @since 1.0
 */
function epl_custom_orderby( $query ) {
	if( ! is_admin() )
	return;

	$orderby = $query->get( 'orderby');

	if( 'property_price' == $orderby ) {
		$query->set('meta_key','property_price');
		$query->set('orderby','meta_value_num');
	}

}
// handle sorting of admin columns
add_filter( 'pre_get_posts', 'epl_custom_orderby' );

/**
 * Functions for post column contents
 *
 * @since 2.2
 */
function epl_manage_listing_column_property_thumb_callback() {
	global $epl_settings;

	if( function_exists('the_post_thumbnail') ) {
		$thumb_size = isset($epl_settings['epl_admin_thumb_size'])? $epl_settings['epl_admin_thumb_size'] : 'admin-list-thumb';
		the_post_thumbnail($thumb_size);
	}
}
add_action( 'epl_manage_listing_column_property_thumb' , 'epl_manage_listing_column_property_thumb_callback' );

/**
 * Posts Types Columns
 *
 * @since 1.0
 */
function epl_manage_listing_column_listing_callback() {
	global $post,$epl_settings,$property;

	$property_address_suburb	= get_the_term_list( $post->ID, 'location', '', ', ', '' );
	$heading 			= $property->get_property_meta('property_heading');
	$homeopen 			= $property->get_property_meta('property_inspection_times');
	$beds 				= $property->get_property_meta('property_bedrooms');
	$baths 				= $property->get_property_meta('property_bathrooms');
	$rooms 				= $property->get_property_meta('property_rooms' , false );
	$land 				= $property->get_property_meta('property_land_area' , false );
	$land_unit 			= $property->get_property_meta('property_land_area_unit');

	$category			= $property->get_property_meta('property_commercial_category');
	$outgoings			= $property->get_property_meta('property_com_outgoings');
	$return				= $property->get_property_meta('property_com_return');
	if ( is_array($category) ) {
		$category = implode(', ', $category);
	}
	if ( empty( $heading) ) {
		echo '<strong>'.__( 'Important! Set a Heading', 'easy-property-listings'  ).'</strong>';
	} else {
		echo '<div class="type_heading"><strong>' , $heading , '</strong></div>';
	}

	/* Commercial Listing Type */
	if ( !empty( $category ) ) {
		echo '<div class="epl_meta_category">Category: ' , $category , '</div>';
	}

	echo '<div class="type_suburb">' , $property_address_suburb , '</div>';

	/* Commercial Listing Type */
	if ( !empty( $outgoings ) ) {
		echo '<div class="epl_meta_outgoings">Outgoings: ' , epl_currency_formatted_amount ( $outgoings ) , '</div>';
	}

	/* Commercial Listing Type */
	if ( !empty( $return ) ) {
		echo '<div class="epl_meta_baths">Return: ' , $return , '%</div>';
	}

	if ( !empty( $beds ) || !empty( $baths ) ) {
		echo '<div class="epl_meta_beds_baths">';
			echo '<span class="epl_meta_beds">' , $beds , ' ' , __( 'Beds', 'easy-property-listings'  ) , ' | </span>';
			echo '<span class="epl_meta_baths">' , $baths , ' ' , __( 'Baths', 'easy-property-listings'  ) , '</span>';
		echo '</div>';
	}

	if ( !empty( $rooms ) ) {
		if ( $rooms == 1 ) {
			echo '<div class="epl_meta_rooms">' , $rooms , ' ' , __( 'Room', 'easy-property-listings'  ) , '</div>';
		} else {
			echo '<div class="epl_meta_rooms">' , $rooms , ' ' , __( 'Rooms', 'easy-property-listings'  ) , '</div>';
		}
	}

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
}
add_action( 'epl_manage_listing_column_listing' , 'epl_manage_listing_column_listing_callback' );

/**
 * Posts Types Column ID
 *
 * @since 1.0
 */
function epl_manage_listing_column_listing_id_callback() {
	global $post;

	$unique_id	= get_post_meta( $post->ID, 'property_unique_id', true );
	/* If no duration is found, output a default message. */
	if (  !empty( $unique_id ) )
		echo $unique_id;
}
add_action( 'epl_manage_listing_column_listing_id' , 'epl_manage_listing_column_listing_id_callback' );

/**
 * Posts Types Column Geocode
 *
 * @since 1.0
 */
function epl_manage_listing_column_geo_callback() {
	global $post;

	$property_address_coordinates = get_post_meta( $post->ID, 'property_address_coordinates', true );
	/* If no duration is found, output a default message. */
	if (  $property_address_coordinates == ',' || empty($property_address_coordinates ) )
		_e('No','easy-property-listings' ) ;
	/* If there is a duration, append 'minutes' to the text string. */
	else
		echo $property_address_coordinates;
}
add_action( 'epl_manage_listing_column_geo' , 'epl_manage_listing_column_geo_callback' );

/**
 * Posts Types Column Price
 *
 * @since 1.0
 */
function epl_manage_listing_column_price_callback() {
	global $post, $property, $epl_settings;

	$price 			= $property->get_property_meta('property_price');
	$view 			= $property->get_property_meta('property_price_view');
	$property_status	= ucfirst( $property->get_property_meta('property_status') );
	$property_authority	= $property->get_property_meta('property_authority');
	$sold_price		= $property->get_property_meta('property_sold_price');
	$property_under_offer	= $property->get_property_meta('property_under_offer');
	$lease 			= $property->get_property_meta('property_com_rent');
	$lease_period		= $property->get_property_meta('property_com_rent_period');
	$lease_date 		= $property->get_property_meta('property_com_lease_end_date');
	$d_bond 		= '';
	$bond 			= '';

	$max_price 		= '2000000';

	if(isset($epl_settings['epl_max_graph_sales_price' ])) {
		$max_price = (int) $epl_settings['epl_max_graph_sales_price' ];
	}

	/* Rental Listing Type Custom Values */
	if ( 'rental' == $post->post_type ) {

		$price 			= $property->get_property_meta('property_rent');
		$view 			= $property->get_property_meta('property_rent_view');

		$d_bond 		= $epl_settings['display_bond'];
		$bond 			= $property->get_property_meta('property_bond' , false );

		$max_price 		= '2000';
		if(isset($epl_settings['epl_max_graph_rent_price' ])) {
			$max_price = (int) $epl_settings['epl_max_graph_rent_price' ];
		}
	}

	/* Commercial Listing Lease Type Price */
	if ( 'commercial' == $post->post_type && $property->get_property_meta('property_com_listing_type') == 'lease' ) {

		// property_com_listing_type
		// property_com_rent
		// property_com_rent_period
		// property_com_rent_range_min
		// property_com_rent_range_max

		$price 			= $property->get_property_meta('property_com_rent');

		$max_price 		= '2000';
		if(isset($epl_settings['epl_max_graph_rent_price' ])) {
			$max_price = (int) $epl_settings['epl_max_graph_rent_price' ];
		}
	}

	if ( $property_status == 'Sold' ) {
		$class = 'bar-home-sold';
	}
	elseif ( $property_status == 'Leased' ) {
		$class = 'bar-home-sold';
	}
	elseif ( ! empty( $property_under_offer ) && 'yes' == $property_under_offer ) {
		$class = 'bar-under-offer';
	}
	elseif ( $property_status == 'Current' ) {
		$class = 'bar-home-open';
	}
	else {
		$class = '';
	}
	if ( ! empty ( $sold_price ) ){
		$barwidth = $max_price == 0 ? 0 : $sold_price/$max_price * 100;
	} else {
		$barwidth = $max_price == 0 ? 0 : $price/$max_price * 100;
	}
	echo '<div class="epl-price-bar '.$class.'">
			<span style="width:'.$barwidth.'%"></span>
		</div>';

	if ( !empty( $property_under_offer) && 'yes' == $property_under_offer ) {
		// echo '<div class="type_under_offer">' .$property->label_under_offer. '</div>';
	}

	// Display sold price
	if ( ! empty ( $view ) ) {
		echo '<div class="epl_meta_search_price">' . $property->get_price_plain_value() . ' ';
		echo $property_status == 'Sold' ? 	epl_currency_formatted_amount( $sold_price ) : '';
		echo '</div>';
	} else {
		echo '<div class="epl_meta_price">' . $property->get_price_plain_value() . '</div>';
	}

	/* Rental Listing Type */
	if ( ! empty ( $bond ) && $d_bond == 1 ) {
		echo '<div class="epl_meta_bond">' , epl_labels('label_bond') , ' ' , epl_currency_formatted_amount( $bond ) , '</div>';
	}


	/* Commercial Listing Type */
	if ( !empty ( $lease_date ) ) {
		echo '<div class="epl_meta_lease_date">Lease End: ' ,  $lease_date , '</div>';
	}

	/*
	if($property_authority == 'auction' ) {
		_e('Auction ','easy-property-listings' );
		echo '<br>'.$property->get_property_auction(true);
	}*/
}
add_action( 'epl_manage_listing_column_price' , 'epl_manage_listing_column_price_callback' );

/**
 * Posts Types Column Status
 *
 * @since 1.0
 */
function epl_manage_listing_column_property_status_callback() {
	global $post, $property;

	$property_status = get_post_meta( $post->ID, 'property_status', true );
	$labels_property_status = apply_filters (  'epl_labels_property_status_filter', array(
		'current' 	=> __('Current', 'easy-property-listings' ),
		'withdrawn' 	=> __('Withdrawn', 'easy-property-listings' ),
		'offmarket' 	=> __('Off Market', 'easy-property-listings' ),
		'sold'  	=> $property->label_sold,
		'leased'  	=> $property->label_leased
		)
	);
	if ( ! empty ( $property_status ) ) {
		echo '<span class="type_'.strtolower($property_status).'">'.$labels_property_status[$property_status].'</span>';
	}
}
add_action( 'epl_manage_listing_column_property_status' , 'epl_manage_listing_column_property_status_callback' );

/**
 * Posts Types Column Listing Type
 *
 * @since 1.0
 */
function epl_manage_listing_column_listing_type_callback() {
	global $post;

	/* Get the post meta. */
	$listing_type = get_post_meta( $post->ID, 'property_com_listing_type', true );
	/* If no duration is found, output a default message. */
	if ( ! empty( $listing_type) )
		echo $listing_type;
}
add_action( 'epl_manage_listing_column_listing_type' , 'epl_manage_listing_column_listing_type_callback' );

/**
 * Posts Types Agent/Author
 *
 * @since 1.0
 */
function epl_manage_listing_column_agent_callback() {
	global $post, $property;

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
}
add_action( 'epl_manage_listing_column_agent' , 'epl_manage_listing_column_agent_callback' );