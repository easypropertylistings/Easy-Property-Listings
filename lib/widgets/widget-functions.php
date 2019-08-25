<?php
/**
 * Search Widget Fields
 *
 * @package     EPL
 * @subpackage  Widget/Search
 * @copyright   Copyright (c) 2019, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// phpcs:disable WordPress.DB.SlowDBQuery

/**
 * Search widget form fields for search widget
 *
 * @since 2.0
 */
function epl_search_widget_fields() {

	$post_type_array = array_merge( array( '' => __( 'All', 'easy-property-listings' ) ) + epl_get_active_post_types() );
	$fields          = apply_filters(
		'epl_search_widget_fields',
		array(

			array(
				'key'     => 'title',
				'label'   => __( 'Title', 'easy-property-listings' ),
				'type'    => 'text',
				'default' => '',
				'order'   => 10,
			),
			array(
				'key'      => 'post_type',
				'label'    => __( 'Post Type', 'easy-property-listings' ),
				'default'  => array( 'property' ),
				'type'     => 'select',
				'multiple' => true,
				'options'  => $post_type_array,
				'order'    => 20,
			),
			array(
				'key'     => 'style',
				'label'   => __( 'Style', 'easy-property-listings' ),
				'default' => 'default',
				'type'    => 'select',
				'options' => array(
					'default' => __( 'Default', 'easy-property-listings' ),
					'wide'    => __( 'Wide', 'easy-property-listings' ),
					'slim'    => __( 'Slim', 'easy-property-listings' ),
					'fixed'   => __( 'Fixed Width', 'easy-property-listings' ),
				),
				'order'   => 30,
			),
			array(
				'key'     => 'show_property_status_frontend',
				'label'   => __( 'Status', 'easy-property-listings' ),
				'default' => 'off',
				'type'    => 'checkbox',
				'order'   => 40,
			),
			array(
				'key'     => 'property_status',
				'label'   => __( 'Status', 'easy-property-listings' ),
				'default' => '',
				'type'    => 'select',
				'options' => array(
					''        => __( 'Any', 'easy-property-listings' ),
					'current' => __( 'Current', 'easy-property-listings' ),
					'sold'    => apply_filters( 'epl_sold_label_status_filter', __( 'Sold', 'easy-property-listings' ) ),
					'leased'  => apply_filters( 'epl_leased_label_status_filter', __( 'Leased', 'easy-property-listings' ) ),
				),
				'order'   => 50,
			),
			array(
				'key'     => 'search_id',
				'label'   => __( 'Property ID', 'easy-property-listings' ),
				'default' => 'off',
				'type'    => 'checkbox',
				'order'   => 60,
			),
			array(
				'key'     => 'search_address',
				'label'   => __( 'Property Address', 'easy-property-listings' ),
				'default' => 'off',
				'type'    => 'checkbox',
				'order'   => 70,
			),
			array(
				'key'     => 'search_location',
				'label'   => epl_tax_location_label(),
				'default' => 'on',
				'type'    => 'checkbox',
				'order'   => 80,
			),
			array(
				'key'     => 'search_city',
				'label'   => epl_labels( 'label_city' ),
				'default' => 'off',
				'type'    => 'checkbox',
				'order'   => 90,
			),
			array(
				'key'     => 'search_state',
				'label'   => epl_labels( 'label_state' ),
				'default' => 'off',
				'type'    => 'checkbox',
				'order'   => 100,
			),
			array(
				'key'     => 'search_postcode',
				'label'   => epl_labels( 'label_postcode' ),
				'default' => 'off',
				'type'    => 'checkbox',
				'order'   => 110,
			),
			array(
				'key'     => 'search_country',
				'label'   => __( 'Country', 'easy-property-listings' ),
				'default' => 'off',
				'type'    => 'checkbox',
				'order'   => 120,
			),
			array(
				'key'     => 'search_house_category',
				'label'   => __( 'Category', 'easy-property-listings' ),
				'default' => 'on',
				'type'    => 'checkbox',
				'order'   => 130,
			),
			array(
				'key'     => 'house_category_multiple',
				'label'   => __( 'Categories: Multi select', 'easy-property-listings' ),
				'default' => 'off',
				'type'    => 'checkbox',
				'order'   => 140,
			),
			array(
				'key'     => 'search_price_global',
				'label'   => __( 'Price Slider', 'easy-property-listings' ),
				'default' => 'off',
				'type'    => 'checkbox',
				'order'   => 150,
			),
			array(
				'key'     => 'search_price',
				'label'   => __( 'Price', 'easy-property-listings' ),
				'default' => 'on',
				'type'    => 'checkbox',
				'order'   => 160,
			),

			array(
				'key'     => 'search_bed',
				'label'   => __( 'Bed', 'easy-property-listings' ),
				'default' => 'on',
				'type'    => 'checkbox',
				'order'   => 170,
			),
			array(
				'key'     => 'search_bath',
				'label'   => __( 'Bath', 'easy-property-listings' ),
				'default' => 'on',
				'type'    => 'checkbox',
				'order'   => 180,
			),
			array(
				'key'     => 'search_rooms',
				'label'   => __( 'Rooms', 'easy-property-listings' ),
				'default' => 'off',
				'type'    => 'checkbox',
				'order'   => 190,
			),
			array(
				'key'     => 'search_car',
				'label'   => __( 'Car', 'easy-property-listings' ),
				'default' => 'off',
				'type'    => 'checkbox',
				'order'   => 200,
			),
			array(
				'key'     => 'search_land_area',
				'label'   => __( 'Land Area', 'easy-property-listings' ),
				'default' => 'off',
				'type'    => 'checkbox',
				'order'   => 210,
			),
			array(
				'key'     => 'search_building_area',
				'label'   => __( 'Building Area', 'easy-property-listings' ),
				'default' => 'off',
				'type'    => 'checkbox',
				'order'   => 220,
			),
			array(
				'key'     => 'search_features',
				'label'   => __( 'Features', 'easy-property-listings' ),
				'default' => 'off',
				'type'    => 'checkbox',
				'order'   => 230,
			),

			array(
				'key'     => 'search_other',
				'label'   => __( 'Other Search Options', 'easy-property-listings' ),
				'default' => 'on',
				'type'    => 'checkbox',
				'order'   => 240,
			),
			array(
				'key'     => 'submit_label',
				'label'   => __( 'Submit Label', 'easy-property-listings' ),
				'type'    => 'text',
				'default' => __( 'Search', 'easy-property-listings' ),
				'order'   => 300,
			),
		)
	);

	$order = array();
	foreach ( $fields as $field_key => $field_value ) {

		$fields[ $field_value['key'] ] = $field_value;
		$order[ $field_value['key'] ]  = isset( $field_value['order'] ) ? $field_value['order'] : 245;
		unset( $fields[ $field_key ] );

	}

	array_multisort( $order, SORT_ASC, $fields );

	return $fields;
}

/**
 * Number Suffix Callback
 *
 * @param string $v Value.
 * @param string $suffix Suffix.
 *
 * @return string
 * @since 2.0
 */
function epl_number_suffix_callback( $v, $suffix = ' +' ) {
	return $v . '' . $suffix;
}

/**
 * Get the price slider default ranges
 *
 * @param string $post_type Post type.
 * @param string $transaction Transaction param may come in handy in commercial search where we have both sale & lease commercial properties.
 *
 * @return array|false|mixed|void
 * @since 3.3
 */
function epl_get_price_slider_array( $post_type = 'property', $transaction = 'default' ) {

	if ( empty( $post_type ) ) {
		$range       = range( 0, 10000000, 50000 );
		$price_array = array_combine( $range, array_map( 'epl_currency_formatted_amount', $range ) );
		$price_array = apply_filters( 'epl_listing_search_price_slider_global', $price_array );
	} elseif ( is_epl_rental_post( $post_type ) ) {
		$range       = range( 50, 5000, 50 );
		$price_array = array_combine( $range, array_map( 'epl_currency_formatted_amount', $range ) );

		// the additional $post_type param passed to apply_filters will enable us to change price range for each post type.
		$price_array = apply_filters( 'epl_listing_search_price_slider_rental', $price_array, $post_type, $transaction );
	} else {
		$range       = range( 50000, 10000000, 50000 );
		$price_array = array_combine( $range, array_map( 'epl_currency_formatted_amount', $range ) );
		$price_array = apply_filters( 'epl_listing_search_price_slider_sale', $price_array, $post_type, $transaction );
	}

	return $price_array;
}

/**
 * Get the price array for the price slider
 *
 * @param string $post_type Post type.
 * @param string $transaction Transaction param may come in handy in commercial search where we have both sale & lease commercial properties.
 *
 * @return array|false|mixed|void
 * @since 3.1
 */
function epl_get_price_array( $post_type = 'property', $transaction = 'default' ) {

	if ( empty( $post_type ) ) {

		$range = array_merge( range( 0, 1000, 50 ), range( 1100, 2000, 100 ), range( 2500, 5000, 500 ), range( 50000, 1000000, 50000 ), range( 1250000, 3000000, 250000 ), array( 4000000, 5000000, 10000000 ) );

		$price_array = array_combine( $range, array_map( 'epl_currency_formatted_amount', $range ) );

		$price_array = apply_filters( 'epl_listing_search_price_global', $price_array );
	} elseif ( is_epl_rental_post( $post_type ) ) {
		$range       = array_merge( range( 50, 1000, 50 ), range( 1100, 2000, 100 ), range( 2500, 5000, 500 ) );
		$price_array = array_combine( $range, array_map( 'epl_currency_formatted_amount', $range ) );

		// the additional $post_type param passed to apply_filters will enable us to change price range for each post type.
		$price_array = apply_filters( 'epl_listing_search_price_rental', $price_array, $post_type, $transaction );
	} else {
		$range       = array_merge( range( 50000, 1000000, 50000 ), range( 1250000, 3000000, 250000 ), array( 4000000, 5000000, 10000000 ) );
		$price_array = array_combine( $range, array_map( 'epl_currency_formatted_amount', $range ) );
		$price_array = apply_filters( 'epl_listing_search_price_sale', $price_array, $post_type, $transaction );
	}

	return $price_array;

}

/**
 * Get the price meta key
 *
 * @param string $post_type Post type.
 * @param string $transaction Transaction param may come in handy in commercial search where we have both sale & lease commercial properties.
 *
 * @return mixed|void
 * @since 3.1
 */
function epl_get_price_meta_key( $post_type = 'property', $transaction = 'default' ) {

	// move from specific to general.
	if ( 'commercial' === $post_type ) {

		$price_meta_key = 'lease' === $transaction ? 'property_com_rent' : 'property_price';

	} elseif ( is_epl_rental_post( $post_type ) ) {

		$price_meta_key = 'property_rent';

	} else {

		$price_meta_key = 'property_price';
	}

	// use this filter to change property price meta key on the basis of post type & transaction.
	return apply_filters( 'epl_price_meta_key', $price_meta_key, $post_type, $transaction );

}

/**
 * Search widget form fields for search widget - frontend
 *
 * @param string $post_type Post type.
 * @param string $property_status Listing status.
 * @param string $transaction_type Transaction param may come in handy in commercial search where we have both sale & lease commercial properties.
 *
 * @return mixed|void
 * @since 2.2
 */
function epl_search_widget_fields_frontend( $post_type = '', $property_status = '', $transaction_type = 'default' ) {

	$price_array        = epl_get_price_array( $post_type, $transaction_type );
	$price_slider_array = epl_get_price_slider_array( $post_type, $transaction_type );

	$price_meta_key = epl_get_price_meta_key( $post_type, $transaction_type );

	$fields = apply_filters(
		'epl_search_widget_fields_frontend',
		array(

			array(
				'key'      => 'post_type',
				'meta_key' => 'post_type',
				'type'     => 'hidden',
				'order'    => 10,
			),
			array(
				'key'           => 'property_status',
				'meta_key'      => 'property_status',
				'label'         => __( 'Status', 'easy-property-listings' ),
				'type'          => 'hidden',
				'option_filter' => 'property_status',
				'options'       => epl_get_unique_post_meta_values( 'property_status', $post_type ),
				'query'         => array(
					'query'   => 'meta',
					'compare' => 'IN',
				),
				'order'         => 20,
			),
			array(
				'key'         => 'search_id',
				'meta_key'    => 'property_id',
				'label'       => __( 'Search by Property ID', 'easy-property-listings' ),
				'placeholder' => __( 'Search ID', 'easy-property-listings' ),
				'type'        => 'text',
				'class'       => 'epl-search-row-full',
				'query'       => array(
					'query' => 'meta',
					'key'   => 'property_unique_id',
				),
				'order'       => 25,
			),
			array(
				'key'         => 'search_address',
				'meta_key'    => 'property_address',
				'label'       => __( 'Search by Address', 'easy-property-listings' ),
				'placeholder' => __( 'Search Address', 'easy-property-listings' ),
				'type'        => 'text',
				'class'       => 'epl-search-row-full',
				'query'       => array( 'query' => 'post' ),
				'order'       => 30,
			),
			array(
				'key'           => 'search_location',
				'meta_key'      => 'property_location',
				'label'         => epl_tax_location_label(),
				'type'          => 'select',
				'option_filter' => 'location',
				'options'       => epl_get_available_locations( $post_type, $property_status ),
				'query'         => array( 'query' => 'tax' ),
				'class'         => 'epl-search-row-full',
				'order'         => 40,
			),
			array(
				'key'           => 'search_city',
				'meta_key'      => 'property_address_city',
				'label'         => epl_labels( 'label_city' ),
				'type'          => 'select',
				'option_filter' => 'city',
				'options'       => epl_get_unique_post_meta_values( 'property_address_city', $post_type, 'publish', $property_status ),
				'query'         => array( 'query' => 'meta' ),
				'class'         => 'epl-search-row-half',
				'order'         => 50,
			),
			array(
				'key'           => 'search_state',
				'meta_key'      => 'property_address_state',
				'label'         => epl_labels( 'label_state' ),
				'type'          => 'select',
				'option_filter' => 'state',
				'options'       => epl_get_unique_post_meta_values( 'property_address_state', $post_type, 'publish', $property_status ),
				'query'         => array( 'query' => 'meta' ),
				'class'         => 'epl-search-row-half',
				'order'         => 60,
			),
			array(
				'key'           => 'search_postcode',
				'meta_key'      => 'property_address_postal_code',
				'label'         => epl_labels( 'label_postcode' ),
				'type'          => 'select',
				'option_filter' => 'postcode',
				'options'       => epl_get_unique_post_meta_values( 'property_address_postal_code', $post_type, 'publish', $property_status ),
				'query'         => array( 'query' => 'meta' ),
				'class'         => 'epl-search-row-half',
				'order'         => 70,
			),
			array(
				'key'           => 'search_country',
				'meta_key'      => 'property_address_country',
				'label'         => __( 'Country', 'easy-property-listings' ),
				'type'          => 'select',
				'option_filter' => 'country',
				'options'       => epl_get_unique_post_meta_values( 'property_address_country', $post_type, 'publish', $property_status ),
				'query'         => array( 'query' => 'meta' ),
				'class'         => 'epl-search-row-half',
				'order'         => 80,
			),
			array(
				'key'           => 'search_house_category',
				'meta_key'      => 'property_category',
				'label'         => __( 'House Category', 'easy-property-listings' ),
				'option_filter' => 'category',
				'options'       => epl_get_meta_values( 'property_category', $post_type ),
				'type'          => 'select',
				'query'         => array( 'query' => 'meta' ),
				'class'         => 'epl-search-row-full',
				'exclude'       => array( 'rural', 'land', 'commercial', 'commercial_land', 'business' ),
				'order'         => 90,
			),
			array(
				'key'           => 'search_house_category',
				'meta_key'      => 'property_rural_category',
				'label'         => __( 'Rural Category', 'easy-property-listings' ),
				'option_filter' => 'category',
				'options'       => epl_get_meta_values( 'property_rural_category', $post_type ),
				'type'          => 'select',
				'query'         => array( 'query' => 'meta' ),
				'class'         => 'epl-search-row-full',
				'exclude'       => array( 'property', 'rental', 'land', 'commercial', 'commercial_land', 'business' ),
				'order'         => 100,
			),
			array(
				'key'           => 'search_house_category',
				'meta_key'      => 'property_land_category',
				'label'         => __( 'Land Category', 'easy-property-listings' ),
				'option_filter' => 'category',
				'options'       => epl_get_meta_values( 'property_land_category', $post_type ),
				'type'          => 'select',
				'query'         => array( 'query' => 'meta' ),
				'class'         => 'epl-search-row-full',
				'exclude'       => array( 'property', 'rental', 'rural', 'commercial', 'commercial_land', 'business' ),
				'order'         => 110,
			),
			array(
				'key'           => 'search_house_category',
				'meta_key'      => 'property_commercial_category',
				'label'         => __( 'Commercial Category', 'easy-property-listings' ),
				'option_filter' => 'category',
				'options'       => epl_get_meta_values( 'property_commercial_category', $post_type ),
				'type'          => 'select',
				'query'         => array( 'query' => 'meta' ),
				'class'         => 'epl-search-row-full',
				'exclude'       => array( 'property', 'rental', 'land', 'rural', 'business' ),
				'order'         => 120,
			),
			array(
				'key'           => 'search_house_category',
				'meta_key'      => 'property_tax_business_listing',
				'label'         => __( 'Business Category', 'easy-property-listings' ),
				'type'          => 'select',
				'option_filter' => 'tax_business_listing',
				'options'       => epl_get_available_terms( 'tax_business_listing', 'business', $property_status ),
				'query'         => array( 'query' => 'tax' ),
				'class'         => 'epl-search-row-full',
				'exclude'       => array( 'property', 'rental', 'land', 'rural', 'commercial', 'commercial_land' ),
				'order'         => 130,
			),
			array(
				'key'           => 'search_price_global',
				'meta_key'      => 'property_price_global_from',
				'label'         => __( 'Search Price From', 'easy-property-listings' ),
				'option_filter' => 'global_price_from',
				'options'       => $price_slider_array,
				'type'          => 'select',
				'query'         => array(
					'query'   => 'meta',
					'key'     => 'property_price_global',
					'type'    => 'numeric',
					'compare' => '>=',
				),
				'class'         => 'epl-search-row-half',
				'order'         => 134,
			),
			array(
				'key'           => 'search_price_global',
				'meta_key'      => 'property_price_global_to',
				'label'         => __( 'Search Price To', 'easy-property-listings' ),
				'option_filter' => 'global_price_to',
				'options'       => $price_slider_array,
				'type'          => 'select',
				'query'         => array(
					'query'   => 'meta',
					'key'     => 'property_price_global',
					'type'    => 'numeric',
					'compare' => '<=',
				),
				'class'         => 'epl-search-row-half',
				'order'         => 135,
			),
			array(
				'key'           => 'search_price',
				'meta_key'      => 'property_price_from',
				'label'         => __( 'Price From', 'easy-property-listings' ),
				'option_filter' => 'price_from',
				'options'       => $price_array,
				'type'          => 'select',
				'query'         => array(
					'query'   => 'meta',
					'key'     => $price_meta_key,
					'type'    => 'numeric',
					'compare' => '>=',
				),
				'class'         => 'epl-search-row-half',
				'order'         => 140,
			),
			array(
				'key'           => 'search_price',
				'meta_key'      => 'property_price_to',
				'label'         => __( 'Price To', 'easy-property-listings' ),
				'option_filter' => 'price_to',
				'options'       => $price_array,
				'type'          => 'select',
				'query'         => array(
					'query'   => 'meta',
					'key'     => $price_meta_key,
					'type'    => 'numeric',
					'compare' => '<=',
				),
				'class'         => 'epl-search-row-half',
				'order'         => 150,
			),
			array(
				'key'           => 'search_bed',
				'meta_key'      => 'property_bedrooms_min',
				'label'         => __( 'Bedrooms Min', 'easy-property-listings' ),
				'option_filter' => 'bedrooms_min',
				'options'       => apply_filters(
					'epl_listing_search_bed_select_min',
					array_combine( range( 1, 10 ), array_map( 'epl_number_suffix_callback', range( 1, 10 ) ) )
				),
				'type'          => 'select',
				'exclude'       => array( 'land', 'commercial', 'commercial_land', 'business' ),
				'query'         => array(
					'query'   => 'meta',
					'key'     => 'property_bedrooms',
					'type'    => 'numeric',
					'compare' => '>=',
				),
				'class'         => 'epl-search-row-half',
				'order'         => 160,
			),
			array(
				'key'           => 'search_bed',
				'meta_key'      => 'property_bedrooms_max',
				'label'         => __( 'Bedrooms Max', 'easy-property-listings' ),
				'option_filter' => 'bedrooms_max',
				'options'       => apply_filters(
					'epl_listing_search_bed_select_max',
					array_combine( range( 1, 10 ), array_map( 'epl_number_suffix_callback', range( 1, 10 ) ) )
				),
				'type'          => 'select',
				'exclude'       => array( 'land', 'commercial', 'commercial_land', 'business' ),
				'query'         => array(
					'query'   => 'meta',
					'key'     => 'property_bedrooms',
					'type'    => 'numeric',
					'compare' => '<=',
				),
				'class'         => 'epl-search-row-half',
				'order'         => 170,
			),
			array(
				'key'           => 'search_bath',
				'meta_key'      => 'property_bathrooms',
				'label'         => __( 'Bathrooms', 'easy-property-listings' ),
				'option_filter' => 'bathrooms',
				'options'       => apply_filters(
					'epl_listing_search_bath_select',
					array_combine( range( 1, 3 ), array_map( 'epl_number_suffix_callback', range( 1, 3 ) ) )
				),
				'type'          => 'select',
				'exclude'       => array( 'land', 'commercial', 'commercial_land', 'business' ),
				'query'         => array(
					'query'   => 'meta',
					'type'    => 'numeric',
					'compare' => '>=',
				),
				'class'         => 'epl-search-row-half',
				'order'         => 180,
			),
			array(
				'key'           => 'search_rooms',
				'meta_key'      => 'property_rooms',
				'label'         => __( 'Rooms', 'easy-property-listings' ),
				'option_filter' => 'rooms',
				'options'       => apply_filters(
					'epl_listing_search_room_select',
					array_combine( range( 1, 3 ), array_map( 'epl_number_suffix_callback', range( 1, 3 ) ) )
				),
				'type'          => 'select',
				'exclude'       => array( 'land', 'commercial', 'commercial_land', 'business' ),
				'query'         => array(
					'query'   => 'meta',
					'type'    => 'numeric',
					'compare' => '>=',
				),
				'class'         => 'epl-search-row-half',
				'order'         => 190,
			),
			array(
				'key'           => 'search_car',
				'meta_key'      => 'property_carport',
				'label'         => __( 'Car Spaces', 'easy-property-listings' ),
				'option_filter' => 'carport',
				'options'       => apply_filters(
					'epl_listing_search_parking_select',
					array_combine( range( 1, 3 ), array_map( 'epl_number_suffix_callback', range( 1, 3 ) ) )
				),
				'type'          => 'select',
				'class'         => 'epl-search-row-half',
				'exclude'       => array( 'land', 'commercial', 'commercial_land', 'business' ),
				'query'         => array(
					'multiple'    => true,
					'query'       => 'meta',
					'relation'    => 'OR',
					'sub_queries' => array(
						array(
							'key'     => 'property_carport',
							'type'    => 'numeric',
							'compare' => '>=',
						),
						array(
							'key'     => 'property_garage',
							'type'    => 'numeric',
							'compare' => '>=',
						),
					),
				),
				'order'         => 200,
			),
			array(
				'key'           => 'search_features',
				'meta_key'      => 'property_tax_feature',
				'label'         => __( 'Features', 'easy-property-listings' ),
				'type'          => 'select',
				'option_filter' => 'tax_feature',
				'options'       => epl_get_available_terms( 'tax_feature', $post_type, $property_status ),
				'query'         => array( 'query' => 'tax' ),
				'class'         => 'epl-search-row-half',
				'order'         => 205,
			),
			array(
				'key'         => 'search_land_area',
				'meta_key'    => 'property_land_area_min',
				'label'       => __( 'Land Min', 'easy-property-listings' ),
				'type'        => has_filter( 'epl_property_land_area_min' ) ? apply_filters( 'epl_property_land_area_min', '' ) : 'number',
				'query'       => array(
					'query'   => 'meta',
					'type'    => 'numeric',
					'compare' => '>=',
					'key'     => 'property_land_area',
				),
				'class'       => 'epl-search-row-third',
				'placeholder' => __( 'Min', 'easy-property-listings' ),
				'wrap_start'  => 'epl-search-row epl-search-land-area',
				'order'       => 210,
			),
			array(
				'key'         => 'search_land_area',
				'meta_key'    => 'property_land_area_max',
				'label'       => __( 'Land Max', 'easy-property-listings' ),
				'class'       => 'epl-search-row-third',
				'placeholder' => __( 'Max', 'easy-property-listings' ),
				'type'        => has_filter( 'epl_property_land_area_max' ) ? apply_filters( 'epl_property_land_area_max', '' ) : 'number',
				'query'       => array(
					'query'   => 'meta',
					'type'    => 'numeric',
					'compare' => '<=',
					'key'     => 'property_land_area',
				),
				'order'       => 220,
			),
			array(
				'key'           => 'search_land_area',
				'meta_key'      => 'property_land_area_unit',
				'label'         => __( 'Area Unit', 'easy-property-listings' ),
				'class'         => 'epl-search-row-third',
				'type'          => 'select',
				'option_filter' => 'land_area_unit',
				'options'       => apply_filters(
					'epl_listing_search_land_unit_label',
					array(
						'square'      => __( 'Square', 'easy-property-listings' ),
						'squareMeter' => __( 'Square Meter', 'easy-property-listings' ),
						'acre'        => __( 'Acre', 'easy-property-listings' ),
						'hectare'     => __( 'Hectare', 'easy-property-listings' ),
						'sqft'        => __( 'Square Feet', 'easy-property-listings' ),
					)
				),
				'query'         => array( 'query' => 'meta' ),
				'wrap_end'      => true,
				'order'         => 230,

			),
			array(
				'key'         => 'search_building_area',
				'meta_key'    => 'property_building_area_min',
				'label'       => __( 'Building Min', 'easy-property-listings' ),
				'placeholder' => __( 'Min', 'easy-property-listings' ),
				'class'       => 'epl-search-row-third',
				'type'        => has_filter( 'epl_property_building_area_min' ) ? apply_filters( 'epl_property_building_area_min', '' ) : 'number',
				'exclude'     => array( 'land' ),
				'query'       => array(
					'query'   => 'meta',
					'type'    => 'numeric',
					'compare' => '>=',
					'key'     => 'property_building_area',
				),
				'wrap_start'  => 'epl-search-row epl-search-building-area',
				'order'       => 240,
			),
			array(
				'key'         => 'search_building_area',
				'meta_key'    => 'property_building_area_max',
				'label'       => __( 'Building Max', 'easy-property-listings' ),
				'class'       => 'epl-search-row-third',
				'placeholder' => __( 'Max', 'easy-property-listings' ),
				'type'        => has_filter( 'epl_property_building_area_max' ) ? apply_filters( 'epl_property_building_area_max', '' ) : 'number',
				'exclude'     => array( 'land' ),
				'query'       => array(
					'query'   => 'meta',
					'type'    => 'numeric',
					'compare' => '<=',
					'key'     => 'property_building_area',
				),
				'order'       => 250,
			),
			array(
				'key'           => 'search_building_area',
				'meta_key'      => 'property_building_area_unit',
				'label'         => __( 'Area Unit', 'easy-property-listings' ),
				'class'         => 'epl-search-row-third',
				'type'          => 'select',
				'option_filter' => 'building_area_unit',
				'options'       => apply_filters(
					'epl_listing_search_building_unit_label',
					array(
						'square'      => __( 'Square', 'easy-property-listings' ),
						'squareMeter' => __( 'Square Meter', 'easy-property-listings' ),
						'acre'        => __( 'Acre', 'easy-property-listings' ),
						'hectare'     => __( 'Hectare', 'easy-property-listings' ),
						'sqft'        => __( 'Square Feet', 'easy-property-listings' ),
					)
				),
				'exclude'       => array( 'land' ),
				'query'         => array( 'query' => 'meta' ),
				'wrap_end'      => true,
				'order'         => 260,
			),
			array(
				'key'        => 'search_other',
				'meta_key'   => 'property_air_conditioning',
				'label'      => __( 'Air Conditioning', 'easy-property-listings' ),
				'type'       => 'checkbox',
				'exclude'    => array( 'land', 'commercial', 'commercial_land', 'business' ),
				'query'      => array(
					'query'   => 'meta',
					'compare' => 'IN',
					'value'   => array( 'yes', '1' ),
				),
				'class'      => 'epl-search-row-half',
				'wrap_start' => 'epl-search-row epl-search-other',
				'order'      => 270,
			),
			array(
				'key'      => 'search_other',
				'meta_key' => 'property_pool',
				'label'    => __( 'Pool', 'easy-property-listings' ),
				'type'     => 'checkbox',
				'exclude'  => array( 'land', 'commercial', 'commercial_land', 'business' ),
				'query'    => array(
					'query'   => 'meta',
					'compare' => 'IN',
					'value'   => array( 'yes', '1' ),
				),
				'class'    => 'epl-search-row-half',
				'order'    => 280,
			),
			array(
				'key'      => 'search_other',
				'meta_key' => 'property_security_system',
				'label'    => __( 'Security', 'easy-property-listings' ),
				'type'     => 'checkbox',
				'exclude'  => array( 'land', 'commercial', 'commercial_land', 'business' ),
				'query'    => array(
					'query'   => 'meta',
					'compare' => 'IN',
					'value'   => array( 'yes', '1' ),
				),
				'class'    => 'epl-search-row-half',
				'wrap_end' => true,
				'order'    => 290,
			),

		),
		$post_type,
		$property_status
	);

	$order = array();
	foreach ( $fields as $field_key => $field_value ) {
		$fields[ $field_value['meta_key'] ] = $field_value;
		$order[ $field_value['meta_key'] ]  = isset( $field_value['order'] ) ? $field_value['order'] : 20;
		unset( $fields[ $field_key ] );
	}
	array_multisort( $order, SORT_ASC, $fields );
		return $fields;
}

/**
 * Search widget form fields defaults
 *
 * @since 2.2
 */
function epl_search_get_defaults() {

	$defaults = array();
	$fields   = epl_search_widget_fields();

	foreach ( $fields as $field ) {
		$defaults[ $field['key'] ] = $field['default'];
	}
	return $defaults;

}

/**
 * Render widget field blocks -- for backend form
 *
 * @since 2.2
 * @param array  $field Array of field type for the switch.
 * @param array  $object Array of object.
 * @param string $value Value.
 */
function epl_widget_render_backend_field( $field, $object, $value = '' ) {

	switch ( $field['type'] ) {

		// Checkbox.
		case 'checkbox':
			?>
			<p>
				<input
					id="<?php echo esc_attr( $object->get_field_id( $field['key'] ) ); ?>"
					name="<?php echo esc_attr( $object->get_field_name( $field['key'] ) ); ?>"
					type="checkbox"
						<?php
						if ( isset( $value ) && esc_attr( $value ) === 'on' ) {
							echo 'checked="checked"';
						}
						?>
				/>
				<label for="<?php echo esc_attr( $object->get_field_id( $field['key'] ) ); ?>">
					<?php echo esc_attr( $field['label'] ); ?>
				</label>
			</p>
			<?php

			break;

		// Text.
		case 'text':
			?>
			<p>
				<label for="<?php echo esc_attr( $object->get_field_id( $field['key'] ) ); ?>">
					<?php echo esc_attr( $field['label'] ); ?>
				</label>
				<input
					class="widefat"
					id="<?php echo esc_attr( $object->get_field_id( $field['key'] ) ); ?>"
					name="<?php echo esc_attr( $object->get_field_name( $field['key'] ) ); ?>"
					type="text"
					value="<?php echo esc_attr( $value ); ?>"
				/>
			</p>
			<?php

			break;

		// Textarea.
		case 'textarea':
			?>
			<p>
				<label for="<?php echo esc_attr( $object->get_field_id( $field['key'] ) ); ?>">
					<?php echo esc_attr( $field['label'] ); ?>
				</label>
				<textarea
					class="widefat"
					rows="10"
					cols="20"
					id="<?php echo esc_attr( $object->get_field_id( $field['key'] ) ); ?>"
					name="<?php echo esc_attr( $object->get_field_name( $field['key'] ) ); ?>"
					><?php echo esc_attr( $value ); ?></textarea>
			</p>
			<?php

			break;

		// Select.
		case 'select':
			?>
			<p>
				<label for="<?php echo esc_attr( $object->get_field_id( $field['key'] ) ); ?>">
					<?php echo esc_attr( $field['label'] ); ?>
				</label>

				<select

					<?php
					// Autoformatting breaks select boxes in widgets.
					echo isset( $field['multiple'] ) ? ' multiple ' : ' '; ?>
					class="widefat"
					id="<?php echo esc_attr( $object->get_field_id( $field['key'] ) ); ?>"
					name="<?php echo esc_attr( $object->get_field_name( $field['key'] ) ); echo isset( $field['multiple'] ) ? '[]' : ''; ?>">

					<?php

					foreach ( $field['options'] as $k => $v ) {
						$selected = '';
						if ( isset( $field['multiple'] ) ) {

							if ( in_array( $k, (array) $value, true ) ) {
								$selected = ' selected ';
							}
						} else {

							if ( isset( $value ) && $k == $value ) { // phpcs:ignore
								$selected = ' selected ';
							}
						}
						echo '<option value="' . esc_attr( $k ) . '" ' . esc_attr( $selected ) . '>' . esc_attr( $v ) . '</option>';
					}
					?>

				</select>
			</p>
			<?php

			break;
	}
}

/**
 * Render widget field blocks -- for front-end form
 *
 * @since 2.2
 * @param array  $field Array of options.
 * @param string $config Config option.
 * @param string $value Value.
 * @param string $post_type Post type name.
 * @param string $property_status Listing status.
 */
function epl_widget_render_frontend_fields( $field, $config = '', $value = '', $post_type = '', $property_status = '' ) {

	if ( 'hidden' !== $field['type'] && 'on' !== $config ) {
		if ( 'property_status' !== $field['key'] ) {
			return;
		}
	}

	if ( ! empty( $field['exclude'] ) && in_array( $post_type, $field['exclude'], true ) ) {
		return;
	}

	do_action( 'epl_frontend_search_field_' . $field['type'], $field, $config, $value, $post_type, $property_status );
}

/**
 * Listings search.
 *
 * @param WP_Query $query WordPress Query object.
 * @param array    $data That contains epl search key value pairs and if it's empty it will replace by $_REQUEST.
 * @param boolean  $get_posts If set to true get_posts of WP_Query will execute on query and returns posts.
 *
 * @return array posts if query is set.
 * @since  2.3.1
 */
function epl_search( WP_Query &$query, array $data = array(), $get_posts = false ) {
	// phpcs:disable WordPress.Security.NonceVerification
	if ( empty( $data ) ) {
		$data = $_REQUEST;
	}

	$epl_search = new EPL_SEARCH( $query, $data );

	if ( $get_posts ) {
		return $epl_search->get_posts();
	}

}

/**
 * Property Search Query
 *
 * @since  2.3.1
 * @param  WP_Query $query WordPress Query object.
 */
function epl_search_pre_get_posts( $query ) {
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}
	if ( epl_is_search() ) {
		epl_search( $query );
	}
}
add_action( 'pre_get_posts', 'epl_search_pre_get_posts' );

/**
 * Is Property Search
 *
 * @since  2.3.1
 */
function epl_is_search() {
	if ( isset( $_REQUEST['action'] ) && 'epl_search' === $_REQUEST['action'] ) {
		return true;
	}
	return false;
}

/**
 * Get Meta Values
 *
 * @param string $key Meta key name.
 * @param string $type Post type name.
 * @param string $status Status type.
 *
 * @return mixed|void
 * @since  2.3.1
 */
function epl_get_meta_values( $key = '', $type = 'post', $status = 'publish' ) {

	// phpcs:disable WordPress.DB

	if ( empty( $key ) ) {
		return;
	}

	if ( empty( $type ) ) {
		$type = epl_get_core_post_types();
	}

	$type     = (array) $type;
	$type     = array_map( 'sanitize_text_field', $type );
	$type_str = " ( '" . implode( "','", $type ) . "' ) ";

	global $wpdb;

	$query = "SELECT distinct(pm.`meta_value`)
		FROM {$wpdb->postmeta} pm
		LEFT JOIN {$wpdb->posts} p
		ON p.`ID` = pm.`post_id`
		WHERE pm.`meta_key` = %s
		AND p.`post_status` = %s
		AND p.`post_type`
		IN $type_str
		AND pm.`meta_value` != ''";

	$results = $wpdb->get_results( $wpdb->prepare( $query, $key, $status ) );

	if ( ! empty( $results ) ) {

		$return = array();
		if ( 'property_category' === $key ) {
			$defaults = epl_listing_load_meta_property_category();
		}
		foreach ( $results as $result ) {
			if ( isset( $defaults ) && ! empty( $defaults ) ) {
				if ( isset( $defaults[ $result->meta_value ] ) ) {
					$return[ $result->meta_value ] = $defaults[ $result->meta_value ];
				} else {
					$return[ $result->meta_value ] = $result->meta_value;
				}
			} else {
				$return[] = $result->meta_value;
			}
		}
		foreach ( $return as $key => &$elem ) {
			$elem = maybe_unserialize( $elem );
			if ( ! empty( $elem ) && is_array( $elem ) ) {
				foreach ( $elem as $el ) {
					$return[] = $el;
				}
				unset( $return[ $key ] );
			}
		}

		$return = array_filter( $return );

		if ( isset( $defaults ) ) {

			$values = $return;

		} else {

			$values = array();

			foreach ( $return as $s_res ) {
				$values[ $s_res ] = esc_html( ucwords( $s_res ) );
			}
		}

		return apply_filters( 'epl_get_unique_post_meta_values', $values, $key, $type );
	}
}

/**
 * Esc Values
 *
 * @param string $text String output.
 *
 * @return string
 * @since  2.3.1
 */
function epl_esc_like( $text ) {
	return addcslashes( $text, '_%\\' );
}

/**
 * Search Where
 *
 * @param string $where Return where.
 * @param array  $wp_query WordPress query object.
 *
 * @return string
 * @since  2.3.1
 */
function epl_listings_where( $where, $wp_query ) {
	global $wpdb;

	$epl_post_title = $wp_query->get( 'epl_post_title' );

	if ( $epl_post_title ) {
		$where .= ' AND ' . $wpdb->posts . '.post_title LIKE \'%' . esc_sql( epl_esc_like( $epl_post_title ) ) . '%\'';
	}
	return $where;
}
add_filter( 'posts_where', 'epl_listings_where', 10, 2 );

/**
 * Get available terms based on post type & property status
 *
 * @param  string $tax Taxonomy name.
 * @param  string $post_type Post type name.
 * @param  string $property_status Listing status.
 * @return mixed terms which is filterable with epl_get_available_terms.
 */
function epl_get_available_terms( $tax = 'location', $post_type = '', $property_status = '' ) {
	global $wpdb;
	$available_loc_query = "
	SELECT DISTINCT (
		tt.term_id
	)
	FROM {$wpdb->prefix}posts p
	LEFT JOIN {$wpdb->prefix}postmeta pm ON ( p.ID = pm.post_id )
	LEFT JOIN {$wpdb->prefix}term_relationships tr ON ( p.ID = tr.object_id )
	LEFT JOIN {$wpdb->prefix}term_taxonomy tt ON ( tr.term_taxonomy_id = tt.term_taxonomy_id ) WHERE
	tt.taxonomy 			= '{$tax}'
	AND p.post_status 		= 'publish'";

	if ( is_array( $post_type ) ) {
		$available_loc_query .= " AND p.post_type IN ('" . implode( "','", $post_type ) . "')";
	} else {
		$available_loc_query .= " AND p.post_type 		= '{$post_type}'";
	}

	if ( ! empty( $property_status ) ) {
		$property_status = array_map( 'trim', explode( ',', $property_status ) );
		if ( count( $property_status ) ) {
			$available_loc_query .= "
			AND pm.meta_key 		= 'property_status'
			AND pm.meta_value 		IN ('" . implode( "','", $property_status ) . "')";
		}
	}
	$available_locs = $wpdb->get_col( $available_loc_query );
	$terms          = get_terms(
		$tax,
		array(
			'hide_empty' => true,
			'include'    => $available_locs,
		)
	);
	$arr            = array();
	foreach ( $terms as $term ) {
		$arr[ $term->term_id ] = $term->name;
	}
	return apply_filters( 'epl_get_available_terms', $arr, $tax, $post_type, $property_status );
}

/**
 * Search Get Locations
 *
 * @param string $post_type Post type name.
 * @param string $property_status Listing status.
 *
 * @return string
 * @since  2.3.1
 */
function epl_get_available_locations( $post_type = '', $property_status = '' ) {
	return epl_get_available_terms( 'location', $post_type, $property_status );

}

/**
 * Pre Process Search Meta
 *
 * @param array $meta_query Array of meta query.
 * @param array $form_fields Form fields.
 *
 * @return mixed|void
 * @since  2.3.1
 */
function epl_preprocess_search_meta_query( $meta_query, $form_fields ) {
	$range_sep  = apply_filters( 'epl_search_field_range_separator', '-' );
	$option_sep = apply_filters( 'epl_search_field_option_separator', ',' );
	foreach ( $meta_query as $key => &$query ) {

		if ( isset( $query['compare'] ) && isset( $query['value'] )
			&& in_array( strtoupper( $query['compare'] ), array( 'IN', 'NOT IN', 'BETWEEN', 'NOT BETWEEN' ), true )
			&& ! is_array( $query['value'] ) ) {
			$query['value'] = array_map( 'trim', explode( $option_sep, $query['value'] ) );

			if ( isset( $form_fields[ $query['key'] ]['option_type'] ) && 'range' === $form_fields[ $query['key'] ]['option_type'] ) {
				$query['value'] = array(
					current( explode( $range_sep, current( $query['value'] ) ) ),
					next( explode( $range_sep, end( $query['value'] ) ) ),
				);
			}
		}
	}
	return apply_filters( 'epl_preprocess_search_meta_query', $meta_query );
}

/**
 * Contacts widget form functions
 *
 * @param array $atts Array of attributes.
 *
 * @return mixed|void
 * @since  3.0
 */
function epl_contact_capture_get_widget_fields( $atts ) {
	$property_id = 0;
	if ( is_epl_post_single() ) {
		global $property;
		$property_id = $property->post->ID;
	}
	$fields = array(
		array(
			'label'        => __( 'First Name', 'easy-property-listings' ),
			'name'         => 'epl_contact_first_name',
			'id'           => 'epl_contact_first_name',
			'type'         => 'text',
			'data-default' => 'on',
		),
		array(
			'label'        => __( 'Last Name', 'easy-property-listings' ),
			'name'         => 'epl_contact_last_name',
			'id'           => 'epl_contact_last_name',
			'type'         => 'text',
			'data-default' => 'on',
		),
		array(
			'label'        => __( 'Email *', 'easy-property-listings' ),
			'name'         => 'epl_contact_email',
			'id'           => 'epl_contact_email',
			'type'         => 'email',
			'data-default' => 'on',
		),
		array(
			'label'        => __( 'Phone', 'easy-property-listings' ),
			'name'         => 'epl_contact_phone',
			'id'           => 'epl_contact_phone',
			'type'         => 'text',
			'data-default' => 'off',
		),
		array(
			'label'        => __( 'Subject', 'easy-property-listings' ),
			'name'         => 'epl_contact_title',
			'id'           => 'epl_contact_title',
			'type'         => 'text',
			'data-default' => 'on',
		),
		array(
			'label'        => __( 'Message', 'easy-property-listings' ),
			'name'         => 'epl_contact_note',
			'id'           => 'epl_contact_note',
			'type'         => 'textarea',
			'data-default' => 'on',
		),
		array(
			'label'        => '',
			'name'         => 'epl_contact_anti_spam',
			'id'           => 'epl_contact_note',
			'type'         => 'hidden',
			'data-default' => 'on',
		),
		array(
			'name'         => 'epl_contact_listing_id',
			'id'           => 'epl_contact_listing_id',
			'type'         => 'hidden',
			'value'        => $property_id,
			'data-default' => 'on',
		),
		array(
			'name'         => 'epl_contact_submit',
			'id'           => 'epl_contact_submit',
			'type'         => 'submit',
			'value'        => __( 'Submit', 'easy-property-listings' ),
			'data-default' => 'on',
		),
	);
	// Allow additional fields.
	$all_fields = apply_filters( 'epl_contact_capture_get_widget_fields', $fields );
	// Filter fields based on attributes.
	foreach ( $all_fields as $field_key => &$field ) {

		if ( isset( $atts[ $field['name'] ] ) && 'on' !== $atts[ $field['name'] ] ) {
			unset( $all_fields[ $field_key ] );
		} else {
			$field['data-default'] = 'on';
		}
	}
	return $all_fields;
}

/**
 * Contacts widget form get defaults
 *
 * @since  3.0
 */
function epl_contact_capture_get_widget_defaults() {
	$fields   = epl_contact_capture_widget_form_fields();
	$defaults = array();
	foreach ( $fields as $field ) {
		if ( 'checkbox' === $field['type'] ) {
			$defaults[ $field['key'] ] = ( isset( $field['default'] ) && 'off' === $field['default'] ) ? 'off' : 'on';
		} else {
			$defaults[ $field['key'] ] = isset( $field['default'] ) ? $field['default'] : '';
		}
	}
	return apply_filters( 'epl_contact_capture_get_widget_defaults', $defaults );
}

/**
 * Contacts widget form get fields
 *
 * @since  3.0
 */
function epl_contact_capture_widget_form_fields() {
	$fields = array(
		array(
			'key'     => 'title',
			'label'   => __( 'Title', 'easy-property-listings' ),
			'type'    => 'text',
			'default' => __( 'Contact Form', 'easy-property-listings' ),
		),
		array(
			'key'     => 'description',
			'label'   => __( 'Description', 'easy-property-listings' ),
			'type'    => 'textarea',
			'default' => '',
		),
		array(
			'label'   => __( 'First Name', 'easy-property-listings' ),
			'key'     => 'epl_contact_first_name',
			'type'    => 'checkbox',
			'default' => 'on',
		),
		array(
			'label'   => __( 'Last Name', 'easy-property-listings' ),
			'key'     => 'epl_contact_last_name',
			'type'    => 'checkbox',
			'default' => 'on',
		),
		array(
			'label'   => __( 'Email', 'easy-property-listings' ),
			'key'     => 'epl_contact_email',
			'type'    => 'checkbox',
			'default' => 'on',
		),
		array(
			'label'   => __( 'Phone', 'easy-property-listings' ),
			'key'     => 'epl_contact_phone',
			'id'      => 'epl_contact_phone',
			'type'    => 'checkbox',
			'default' => 'off',
		),
		array(
			'label'   => __( 'Subject', 'easy-property-listings' ),
			'key'     => 'epl_contact_title',
			'type'    => 'checkbox',
			'default' => 'on',
		),
		array(
			'label'   => __( 'Message', 'easy-property-listings' ),
			'key'     => 'epl_contact_note',
			'type'    => 'checkbox',
			'default' => 'on',
		),
		array(
			'label'   => __( 'Anti Spam', 'easy-property-listings' ),
			'key'     => 'epl_contact_anti_spam',
			'type'    => 'checkbox',
			'default' => 'on',
		),
		array(
			'key'     => 'submit',
			'label'   => __( 'Submit Label', 'easy-property-listings' ),
			'type'    => 'text',
			'default' => __( 'Submit', 'easy-property-listings' ),
		),
	);
	return apply_filters( 'epl_contact_capture_widget_form_fields', $fields );
}

/**
 * Get listing owner
 *
 * @since 3.3
 */
function epl_get_owners() {
	$c_array     = array();
	$contact_ids = (array) epl_get_meta_values( 'property_owner', epl_get_core_post_types() );
	foreach ( $contact_ids as $contact ) {
		$contact_object      = new EPL_Contact( $contact );
		$c_array[ $contact ] = $contact_object->name;
	}
	return $c_array;

}

/**
 * Get price slider
 *
 * @since 3.3
 * @since  3.4.1 Currency support for search field sliders
 */
function epl_get_field_sliders() {

	$currency          = epl_currency_filter( '' );
	$currency_position = epl_get_currency_position();

	$position = ( isset( $currency_position ) && ! empty( $currency_position ) ) ? $currency_position : 'before';

	if ( 'before' === $position ) {
		$prefix = $currency;
		$suffix = '';
	} else {
		$suffix = $currency;
		$prefix = '';
	}

	$sliders = array(
		'epl_field_slider_property_price_global' => array(
			'els'       => array( 'property_price_global_from', 'property_price_global_to' ),
			'label'     => __( 'Price Search', 'easy-property-listings' ),
			'prefix'    => $prefix,
			'suffix'    => $suffix,
			'separator' => ' - ',
		), /**
		'epl_field_slider_property_price'   =>  array(
			'els'       =>  array('property_price_from','property_price_to'),
			'label'     => __('Price','easy-property-listings'),
			'prefix'    => '$',
			'suffix'    => '',
			'separator' => ' - '
		),
		'epl_field_slider_property_bedrooms'    =>  array(
			'els'       =>  array('property_bedrooms_min','property_bedrooms_max'),
			'label'     => __('Bedrooms','easy-property-listings'),
			'prefix'    => '',
			'suffix'    => '',
			'separator' => ' - '
		),
		'epl_field_slider_property_land_area'   =>  array(
			'els'       =>  array('property_land_area_min','property_land_area_max'),
			'label'     => __('Land Area','easy-property-listings'),
			'prefix'    => '',
			'suffix'    => 'm<sup>2</sup>',
			'separator' => ' - ',
			'min'       => 0,
			'max'       => 2000,
			'step'      => 10
		),
		'epl_field_slider_property_building_area'   =>  array(
			'els'       =>  array('property_building_area_min','property_building_area_max'),
			'label'     => __('Building Area','easy-property-listings'),
			'prefix'    => '',
			'suffix'    => 'sq',
			'separator' => ' - ',
			'min'       => 0,
			'max'       => 2000,
			'step'      => 10
		)
		*/
	);

	return apply_filters( 'epl_search_slider_fields', $sliders );
}
