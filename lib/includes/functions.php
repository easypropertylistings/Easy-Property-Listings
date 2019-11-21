<?php
/**
 * Front End Functions
 *
 * @package     EPL
 * @subpackage  Functions/Global
 * @copyright   Copyright (c) 2019, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get an option
 *
 * Looks to see if the specified setting exists, returns default if not
 *
 * @param string $key Meta key.
 * @param bool   $default Returns default if not set.
 * @return mixed
 * @since 2.2
 */
function epl_get_option( $key = '', $default = false ) {
	global $epl_settings;
	$value = ! empty( $epl_settings[ $key ] ) ? $epl_settings[ $key ] : $default;
	$value = apply_filters( 'epl_get_option', $value, $key, $default );
	return apply_filters( 'epl_get_option_' . $key, $value, $key, $default );
}

/**
 * Determine if Divi framework is loaded
 *
 * @since 3.1
 */
function epl_is_divi_framework_theme() {
	if ( function_exists( 'et_divi_fonts_url' ) ) {
		return true;
	}
	return false;
}

/**
 * Determine if iThemes Builder framework is loaded
 *
 * @since 1.0
 */
function epl_is_builder_framework_theme() {
	if ( function_exists( 'it_classes_load' ) ) {
		return true;
	}
	return false;
}

/**
 * Determine if Genesis framework is loaded
 *
 * @since 1.0
 */
function epl_is_genesis_framework_theme() {
	if ( function_exists( 'genesis_constants' ) ) {
		return true;
	}
	return false;
}

/**
 * Determine if TwentyTwenty is loaded
 *
 * @since 3.4.14
 */
function epl_is_twenty_twenty_theme() {
	if ( function_exists( 'twentytwenty_theme_support' ) ) {
		return true;
	}
	return false;
}

/**
 * Used in the widgets by appending the registered image sizes
 *
 * @since 1.0
 */
function epl_get_thumbnail_sizes() {
	global $_wp_additional_image_sizes;
	$sizes = array();
	foreach ( get_intermediate_image_sizes() as $s ) {
		$sizes[ $s ] = array( 0, 0 );
		if ( in_array( $s, array( 'thumbnail', 'medium', 'large' ), true ) ) {
			$sizes[ $s ][0] = get_option( $s . '_size_w' );
			$sizes[ $s ][1] = get_option( $s . '_size_h' );
		} else {
			if ( isset( $_wp_additional_image_sizes ) && isset( $_wp_additional_image_sizes[ $s ] ) ) {
				$sizes[ $s ] = array( $_wp_additional_image_sizes[ $s ]['width'], $_wp_additional_image_sizes[ $s ]['height'] );
			}
		}
	}
	return $sizes;
}

/**
 * Remote get function
 *
 * @param string $url Url.
 *
 * @return string
 * @since 3.3.5 Removed curl.
 * @since 1.0
 */
function epl_remote_url_get( $url ) {

	$response = wp_remote_get( $url );
	return wp_remote_retrieve_body( $response );
}

/**
 * Register post type to EPL and WordPress
 *
 * @param string $post_type Post type name.
 * @param string $post_type_label Post type label.
 * @param array  $args Arguments.
 * @since 1.0
 */
function epl_register_post_type( $post_type = '', $post_type_label, $args = array() ) {
	if ( empty( $post_type ) ) {
		return;
	}

	global $epl_settings;
	if ( ! empty( $epl_settings ) && isset( $epl_settings['activate_post_types'] ) ) {
		$epl_activate_post_types = $epl_settings['activate_post_types'];
		if ( ! empty( $epl_activate_post_types ) ) {
			if ( in_array( $post_type, $epl_activate_post_types, true ) ) {
				global $epl_active_post_types;
				if ( ! isset( $epl_active_post_types ) ) {
					$epl_active_post_types = array();
				}
				$epl_active_post_types[ $post_type ] = $post_type_label;
				register_post_type( $post_type, $args );
			}
		}
	}

	if ( isset( $_REQUEST['action'] ) && 'epl_settings' === $_REQUEST['action'] ) { //phpcs:ignore
		$_SESSION['epl_actions']['epl_flush_rewrite_rules'] = true;
	}
}

/**
 * Return all the post types that are selected as active by admin
 *
 * @since 1.0
 */
function epl_get_active_post_types() {
	global $epl_active_post_types;
	if ( is_null( $epl_active_post_types ) ) {
		$epl_active_post_types = array();
	}
	return $epl_active_post_types;
}

/**
 * Return all the post types doesn't matter if selected as active or not by admin
 *
 * @since 1.0
 */
function epl_get_post_types() {
	$epl_post_types = array(
		'property'        => __( 'Property (Residential)', 'easy-property-listings' ),
		'land'            => __( 'Land', 'easy-property-listings' ),
		'rental'          => __( 'Rental', 'easy-property-listings' ),
		'rural'           => __( 'Rural', 'easy-property-listings' ),
		'commercial'      => __( 'Commercial', 'easy-property-listings' ),
		'commercial_land' => __( 'Commercial Land', 'easy-property-listings' ),
		'business'        => __( 'Business', 'easy-property-listings' ),
	);
	// allow 3rd party extensions to add custom posts as a part of epl.
	return apply_filters( 'epl_post_types', $epl_post_types );
}

/**
 * Get Currencies
 *
 * @since 1.0
 * @return array $currencies A list of the available currencies
 */
function epl_get_currencies() {
	$currencies = array(
		'AUD'  => __( 'Australian Dollars (&#36;)', 'easy-property-listings' ),
		'USD'  => __( 'US Dollars (&#36;)', 'easy-property-listings' ),
		'EUR'  => __( 'Euros (&euro;)', 'easy-property-listings' ),
		'GBP'  => __( 'Pounds Sterling (&pound;)', 'easy-property-listings' ),
		'BRL'  => __( 'Brazilian Real (R&#36;)', 'easy-property-listings' ),
		'CAD'  => __( 'Canadian Dollars (&#36;)', 'easy-property-listings' ),
		'CZK'  => __( 'Czech Koruna', 'easy-property-listings' ),
		'DKK'  => __( 'Danish Krone', 'easy-property-listings' ),
		'HKD'  => __( 'Hong Kong Dollar (&#36;)', 'easy-property-listings' ),
		'HUF'  => __( 'Hungarian Forint', 'easy-property-listings' ),
		'INR'  => __( 'Indian Rupee (&#8377;)', 'easy-property-listings' ),
		'RIAL' => __( 'Iranian Rial (&#65020;)', 'easy-property-listings' ),
		'ILS'  => __( 'Israeli Shekel (&#8362;)', 'easy-property-listings' ),
		'JPY'  => __( 'Japanese Yen (&yen;)', 'easy-property-listings' ),
		'MYR'  => __( 'Malaysian Ringgits', 'easy-property-listings' ),
		'MXN'  => __( 'Mexican Peso (&#36;)', 'easy-property-listings' ),
		'NZD'  => __( 'New Zealand Dollar (&#36;)', 'easy-property-listings' ),
		'NOK'  => __( 'Norwegian Krone', 'easy-property-listings' ),
		'PHP'  => __( 'Philippine Pesos', 'easy-property-listings' ),
		'PLN'  => __( 'Polish Zloty', 'easy-property-listings' ),
		'PKR'  => __( 'Pakistani Rupee', 'easy-property-listings' ),
		'QAR'  => __( 'Qatar Riyal (QAR)', 'easy-property-listings' ),
		'SGD'  => __( 'Singapore Dollar (&#36;)', 'easy-property-listings' ),
		'ZAR'  => __( 'South African Rand (R)', 'easy-property-listings' ),
		'SEK'  => __( 'Swedish Krona', 'easy-property-listings' ),
		'CHF'  => __( 'Swiss Franc', 'easy-property-listings' ),
		'TWD'  => __( 'Taiwan New Dollars', 'easy-property-listings' ),
		'THB'  => __( 'Thai Baht (&#3647;)', 'easy-property-listings' ),
		'TRY'  => __( 'Turkish Lira (&#8378;)', 'easy-property-listings' ),
		'RUB'  => __( 'Russian Rubles', 'easy-property-listings' ),
		'AED'  => __( 'United Arab Emirates, Dirham (AED)', 'easy-property-listings' ),
		'UAH'  => __( 'Ukrainian Hryvnia (&#8372;)', 'easy-property-listings' ),
		'VND'  => __( 'Vietnamese đồng (&#8363;)', 'easy-property-listings' ),
	);
	return apply_filters( 'epl_get_currencies', $currencies );
}

/**
 * Get the store's set currency
 *
 * @since 1.0
 * @return string The currency code
 */
function epl_get_currency() {
	$epl_currency = '';

	global $epl_settings;
	if ( ! empty( $epl_settings ) && isset( $epl_settings['currency'] ) ) {
		$epl_currency = $epl_settings['currency'];
	}
	return apply_filters( 'epl_currency', $epl_currency );
}

/**
 * Get the currency position
 *
 * @since 1.0
 * @return string The currency symbol position
 */
function epl_get_currency_position() {
	$epl_currency_position = '';

	global $epl_settings;
	if ( ! empty( $epl_settings ) && isset( $epl_settings['currency_position'] ) ) {
		$epl_currency_position = $epl_settings['currency_position'];
	}
	return apply_filters( 'epl_currency_position', $epl_currency_position );
}

/**
 * Get the currency thousands separator
 *
 * @since 1.0
 * @return string The currency thousands separator
 */
function epl_get_thousands_separator() {
	$epl_thousands_separator = '';

	global $epl_settings;
	if ( ! empty( $epl_settings ) && isset( $epl_settings['currency_thousands_separator'] ) ) {
		$epl_thousands_separator = $epl_settings['currency_thousands_separator'];
	}
	return apply_filters( 'epl_thousands_separator', $epl_thousands_separator );
}

/**
 * Get the currency decimal separator
 *
 * @since 1.0
 * @return string The currency decimal separator
 */
function epl_get_decimal_separator() {
	$epl_decimal_separator = '';

	global $epl_settings;
	if ( ! empty( $epl_settings ) && isset( $epl_settings['currency_decimal_separator'] ) ) {
		$epl_decimal_separator = $epl_settings['currency_decimal_separator'];
	}
	return apply_filters( 'epl_decimal_separator', $epl_decimal_separator );
}

/**
 * Get the currency formatted amount
 *
 * @param integer $price The price.
 *
 * @return string Currency Formatted price
 * @since 1.0
 */
function epl_currency_formatted_amount( $price ) {
	$price_format           = apply_filters( 'epl_price_number_format', 'number' );
	$price_format_com_lease = apply_filters( 'epl_price_number_format_commercial_lease', 'number' );

	if ( 'decimal' === $price_format || 'decimal' === $price_format_com_lease ) {
		return epl_currency_filter( epl_format_amount( $price, true ) );
	} else {
		return epl_currency_filter( epl_format_amount( $price, false ) );
	}
}

/**
 * Get labels
 *
 * @since 2.2
 * @param integer $key Settings meta key.
 * @return string
 */
function epl_labels( $key ) {
	global $epl_settings;
	$field_groups = epl_get_admin_option_fields();
	$epl_labels   = array();
	foreach ( $field_groups as $field_group ) {
		if ( 'labels' === $field_group['id'] || 'address' === $field_group['id'] ) {
			$epl_labels = array_merge( $epl_labels, array_filter( $field_group['fields'] ) );
		}
	}
	foreach ( $epl_labels as $label_key   => $label ) {

		if ( isset( $label['default'] ) && $key === $label['name'] ) {

			$label = isset( $epl_settings[ $key ] ) ? $epl_settings[ $key ] : $label['default'];

			return apply_filters( 'epl_display_' . $key, $label );
		}
	}
}

/**
 * Display or retrieve the current listing address based on user display selection with optional markup.
 *
 * @since 3.3
 *
 * @param string $before   Optional. Markup to prepend to the title. Default empty.
 * @param string $after    Optional. Markup to append to the title. Default empty.
 * @param bool   $country  Optional. Whether to echo or return country. Default false for country.
 * @param bool   $echo     Optional. Whether to echo or return the title. Default true for echo.
 * @return string|void Current post title if $echo is false.
 */
function epl_the_address( $before = '', $after = '', $country = false, $echo = true ) {
	$address = epl_get_the_address();

	if ( strlen( $address ) === 0 ) {
		return;
	}

		$address = $before . $address . $after;

	if ( $echo ) {
		echo wp_kses_post( $address );
	} else {
		return $address;
	}
}

/**
 * Retrieve address based on user display selection.
 *
 * @param  array $address_args address components.
 * @param  array $sep override default separators for each address components here.
 * @param  bool  $country  Return country with true, default false.
 * @return string
 * @since 3.3
 */
function epl_get_the_address( $address_args = array(), $sep = array(), $country = false ) {

	$address = '';

	$address_defaults = array(
		'sub_number'    => '/',
		'lot_number'    => ' ',
		'street_number' => ' ',
		'street'        => ', ',
		'suburb'        => ' ',
		'city'          => ' ',
		'state'         => ' ',
		'postal_code'   => ' ',
		'country'       => ' ',
	);

	// override default separators for address components.
	$seps = array_merge( $address_defaults, $sep );

	// Output the full address based on user selection.
	if ( empty( $address_args ) ) {
		$address_args = array_keys( $address_defaults );
	}

	foreach ( $address_args as $arg ) {

		if ( isset( $address_defaults[ $arg ] ) ) {

			if ( 'yes' !== get_property_meta( 'property_address_display' ) && in_array( $arg, array( 'sub_number', 'lot_number', 'street_number', 'street' ), true ) ) {
				continue;
			}

			// Country hidden by default.
			if ( true !== $country && in_array( $arg, array( 'country' ), true ) ) {
				continue;
			}

			$value = get_property_meta( 'property_address_' . $arg );

			if ( ! empty( $value ) ) {
				$address .= get_property_meta( 'property_address_' . $arg ) . $seps[ $arg ];
			}
		}
	}

	/**
	 * Filters the post title.
	 *
	 * @since 3.3
	 *
	 * @param string $title The listing address.
	 * @param int    $id    The post ID.
	 */
	return apply_filters( 'epl_the_address', $address );
}

/**
 * Display or retrieve the listing status label with optional markup.
 *
 * @since 3.3
 *
 * @param string $before Output string before.
 * @param string $after Output string after.
 * @param bool   $echo Echo the result.
 * @return string/list for values
 */
function epl_the_status( $before = '', $after = '', $echo = true ) {
	$status = epl_get_the_status();

	$status_opts = epl_get_property_status_opts();

	if ( strlen( $status ) === 0 ) {
			return;
	}

		$status = $before . $status_opts[ $status ] . $after;

	if ( $echo ) {
		echo wp_kses_post( $status );
	} else {
		return $status;
	}
}

/**
 * Retrieve listing status.
 *
 * @since 3.3
 *
 * @param int|WP_Post $post Optional. Post ID or WP_Post object. Default is global $post.
 * @return string
 */
function epl_get_the_status( $post = 0 ) {

	$post = get_post( $post );

	$status = get_property_meta( 'property_status' );
	$status = isset( $status ) ? $status : '';
	$id     = isset( $post->ID ) ? $post->ID : 0;

	/**
	 * Filters the status.
	 *
	 * @since 3.3
	 *
	 * @param string $title The listing status.
	 * @param int    $id    The post ID.
	 */
	return apply_filters( 'epl_get_the_status', $status, $id );
}

/**
 * Get EPL property meta data based on post id
 *
 * @since 1.0
 * @param string $post_ID The post ID.
 * @param string $meta_key Meta key name.
 * @return string Value.
 */
function epl_get_property_meta( $post_ID = '', $meta_key = '' ) {

	if ( empty( $post_ID ) ) {
		$post_ID = get_the_ID();
	}

	if ( ! empty( $meta_key ) ) {
		$meta_value = trim( get_post_meta( $post_ID, $meta_key, true ) );
	} else {
		$meta_value = get_post_meta( $post_ID, $meta_key );
	}
	return apply_filters( 'epl_get_property_meta_filter', $meta_value );
}

/**
 * Print EPL property meta data
 *
 * @since 1.0
 * @param string $post_ID The post ID.
 * @param string $meta_key Meta key name.
 */
function epl_the_property_meta( $post_ID = '', $meta_key ) {
	$meta_value = epl_get_property_meta( $post_ID, $meta_key );
	echo wp_kses_post( apply_filters( 'epl_the_property_meta_filter', $meta_value ) );
}

/**
 * Taxonomy Location Label
 *
 * @since 1.0
 * @return string Location label.
 */
function epl_tax_location_label() {
	$label_location = '';
	global $epl_settings;
	if ( ! empty( $epl_settings ) && isset( $epl_settings['label_location'] ) ) {
		$label_location = trim( $epl_settings['label_location'] );
	}
	if ( empty( $label_location ) ) {
		$label_location = __( 'City', 'easy-property-listings' );
	}
	return $label_location;
}

/**
 * Location Label
 *
 * @since 1.0
 * @return string Location label.
 */
function epl_meta_location_label() {
	$label_location = '';
	global $epl_settings;
	if ( ! empty( $epl_settings ) && isset( $epl_settings['label_suburb'] ) ) {
		$label_location = trim( $epl_settings['label_suburb'] );
	}
	if ( empty( $label_location ) ) {
		$label_location = __( 'City', 'easy-property-listings' );
	}
	return $label_location;
}

/**
 * Display or retrieve the under offer label with optional markup.
 *
 * @since 3.3
 *
 * @param string $before Optional. Markup to prepend to the formatted Under Offer label. Default empty.
 * @param string $after  Optional. Markup to append to the formatted Under Offer label. Default empty.
 * @param bool   $echo   Optional. Whether to echo or return the formatted Under Offer label. Default true for echo.
 * @return string|void Current post title if $echo is false.
 */
function epl_the_under_offer( $before = '', $after = '', $echo = true ) {
	$under_offer = epl_get_the_under_offer();

	$under_offer_label = epl_meta_under_offer_label();

	if ( strlen( $under_offer ) === 0 ) {
		return;
	}

	if ( 'yes' !== strtolower( $under_offer ) && 'sold' !== epl_get_the_status() ) {
		return;
	}

	$under_offer = $before . $under_offer_label . $after;

	if ( $echo ) {
		echo wp_kses_post( $under_offer );
	} else {
		return $under_offer;
	}
}

/**
 * Retrieve listing under offer value.
 *
 * @since 3.3
 *
 * @param int|WP_Post $post Optional. Post ID or WP_Post object. Default is global $post.
 * @return string
 */
function epl_get_the_under_offer( $post = 0 ) {

	$post = get_post( $post );

	$under_offer = get_property_meta( 'property_under_offer' );
	$under_offer = 'yes' === get_property_meta( 'property_under_offer' ) && 'sold' !== epl_get_the_status() ? $under_offer : '';
	$id          = isset( $post->ID ) ? $post->ID : 0;

	/**
	 * Filters the status.
	 *
	 * @since 3.3
	 *
	 * @param string $under_offer The Under Offer status.
	 * @param int    $id    The post ID.
	 */
	return apply_filters( 'epl_get_the_under_offer', $under_offer, $id );
}

/**
 * Under Offer Label
 *
 * @since 2.1
 * @return string
 */
function epl_meta_under_offer_label() {
	$under_offer = '';
	global $epl_settings;
	if ( ! empty( $epl_settings ) && isset( $epl_settings['label_under_offer'] ) ) {
		$under_offer = trim( $epl_settings['label_under_offer'] );
	}
	if ( empty( $under_offer ) ) {
		$under_offer = __( 'Under Offer', 'easy-property-listings' );
	}
	return $under_offer;
}

/**
 * House Categories Options
 *
 * @since 1.1
 * @return array Categories in array.
 */
function epl_listing_load_meta_property_category() {
	$defaults = array(
		'House'               => __( 'House', 'easy-property-listings' ),
		'Unit'                => __( 'Unit', 'easy-property-listings' ),
		'Townhouse'           => __( 'Townhouse', 'easy-property-listings' ),
		'Villa'               => __( 'Villa', 'easy-property-listings' ),
		'Apartment'           => __( 'Apartment', 'easy-property-listings' ),
		'Flat'                => __( 'Flat', 'easy-property-listings' ),
		'Studio'              => __( 'Studio', 'easy-property-listings' ),
		'Warehouse'           => __( 'Warehouse', 'easy-property-listings' ),
		'DuplexSemi-detached' => __( 'Duplex Semi-detached', 'easy-property-listings' ),
		'Alpine'              => __( 'Alpine', 'easy-property-listings' ),
		'AcreageSemi-rural'   => __( 'Acreage Semi-rural', 'easy-property-listings' ),
		'Retirement'          => __( 'Retirement', 'easy-property-listings' ),
		'BlockOfUnits'        => __( 'Block Of Units', 'easy-property-listings' ),
		'Terrace'             => __( 'Terrace', 'easy-property-listings' ),
		'ServicedApartment'   => __( 'Serviced Apartment', 'easy-property-listings' ),
		'Other'               => __( 'Other', 'easy-property-listings' ),
	);
	return apply_filters( 'epl_listing_meta_property_category', $defaults );
}

/**
 * Custom Meta: Return Value of House Category
 *
 * @since 1.1
 * @param string $key Meta key.
 * @return string the categories in array
 */
function epl_listing_meta_property_category_value( $key ) {
	$array  = epl_listing_load_meta_property_category();
	$key    = (array) $key;
	$values = array();
	foreach ( $key as $single ) {
		$values[] = array_key_exists( $single, $array ) && ! empty( $array[ $single ] ) ? $array[ $single ] : $single;
	}

	$value = implode( ', ', $values );

	return apply_filters( 'epl_property_category_value', $value );
}

/**
 * Custom Meta: Land Categories
 *
 * @since 1.1
 * @return array the categories in array
 */
function epl_listing_load_meta_land_category() {
	$defaults = array(
		'Commercial'  => __( 'Commercial', 'easy-property-listings' ),
		'Residential' => __( 'Residential', 'easy-property-listings' ),
	);
	return apply_filters( 'epl_listing_meta_land_category', $defaults );
}

/**
 * Custom Meta: Return Value of Land Category
 *
 * @since 1.1
 * @param string $key Meta key.
 * @return array the categories in array
 */
function epl_listing_meta_land_category_value( $key ) {
	$array = epl_listing_load_meta_land_category();
	$value = array_key_exists( $key, $array ) && ! empty( $array[ $key ] ) ? $array[ $key ] : '';

	return $value;
}

/**
 * Custom Meta: Commercial Categories
 *
 * @since 1.1
 * @return array the categories in array
 */
function epl_listing_load_meta_commercial_category() {
	$defaults = array(
		'Commercial Farming'    => __( 'Commercial Farming', 'easy-property-listings' ),
		'Land/Development'      => __( 'Land/Development', 'easy-property-listings' ),
		'Hotel/Leisure'         => __( 'Hotel/Leisure', 'easy-property-listings' ),
		'Industrial/Warehouse'  => __( 'Industrial/Warehouse', 'easy-property-listings' ),
		'Medical/Consulting'    => __( 'Medical/Consulting', 'easy-property-listings' ),
		'Offices'               => __( 'Offices', 'easy-property-listings' ),
		'Retail'                => __( 'Retail', 'easy-property-listings' ),
		'Showrooms/Bulky Goods' => __( 'Showrooms/Bulky Goods', 'easy-property-listings' ),
		'Other'                 => __( 'Other', 'easy-property-listings' ),
	);
	return apply_filters( 'epl_listing_meta_commercial_category', $defaults );
}

/**
 * Custom Meta: Return Value of Commercial Category
 *
 * @since 1.1
 * @param string $key Meta key.
 * @return array the categories in array
 */
function epl_listing_load_meta_commercial_category_value( $key ) {
	$array = epl_listing_load_meta_commercial_category();

	if ( is_array( $key ) ) {
		$values = array();
		foreach ( $key as $k ) {
			$values[] = array_key_exists( $k, $array ) && ! empty( $array[ $k ] ) ? $array[ $k ] : ucfirst( $k );
		}
		$value = implode( ', ', $values );
	} else {
		$value = array_key_exists( $key, $array ) && ! empty( $array[ $key ] ) ? $array[ $key ] : ucfirst( $key );
	}

	return apply_filters( 'epl_meta_commercial_category_value', $value, $key, $array );
}

/**
 * Custom Meta: Commercial Rental Period
 *
 * @since 2.1
 * @return array the categories in array
 */
function epl_listing_load_meta_commercial_rent_period() {
	$defaults = array(
		'annual'            => __( 'P.A.', 'easy-property-listings' ),
		'nnn'               => __( 'NNN', 'easy-property-listings' ),
		'full-service'      => __( 'Full Service', 'easy-property-listings' ),
		'gross-lease-rates' => __( 'Gross Lease Rates', 'easy-property-listings' ),
	);
	return apply_filters( 'epl_listing_meta_commercial_rent_period', $defaults );
}

/**
 * Custom Meta: Return Value of Commercial Rental Period
 *
 * @since 2.1
 * @param string $key Meta key.
 * @return array the categories in array
 */
function epl_listing_load_meta_commercial_rent_period_value( $key ) {
	$array = epl_listing_load_meta_commercial_rent_period();
	$value = array_key_exists( $key, $array ) && ! empty( $array[ $key ] ) ? $array[ $key ] : '';

	return $value;
}

/**
 * Custom Meta: Rural Categories
 *
 * @since 1.1
 * @return all the categories in array
 */
function epl_listing_load_meta_rural_category() {
	$defaults = array(
		'Cropping'     => __( 'Cropping', 'easy-property-listings' ),
		'Dairy'        => __( 'Dairy', 'easy-property-listings' ),
		'Farmlet'      => __( 'Farmlet', 'easy-property-listings' ),
		'Horticulture' => __( 'Horticulture', 'easy-property-listings' ),
		'Livestock'    => __( 'Livestock', 'easy-property-listings' ),
		'Viticulture'  => __( 'Viticulture', 'easy-property-listings' ),
		'MixedFarming' => __( 'Mixed Farming', 'easy-property-listings' ),
		'Lifestyle'    => __( 'Lifestyle', 'easy-property-listings' ),
		'Other'        => __( 'Other', 'easy-property-listings' ),
	);
	return apply_filters( 'epl_listing_meta_rural_category', $defaults );
}

/**
 * Custom Meta: Return Value of Rural Category
 *
 * @since 1.1
 * @param string $key Meta key.
 * @return all the categories in array
 */
function epl_listing_load_meta_rural_category_value( $key ) {
	$array = epl_listing_load_meta_rural_category();
	$value = array_key_exists( $key, $array ) && ! empty( $array[ $key ] ) ? $array[ $key ] : '';

	return $value;
}

/**
 * REAXML Date Processing Function for WP All Import and FeedSync
 *
 * Some imports set the current date instead of the date from the REAXML file.
 * Usage in WP All Import Post Date field is:
 * [epl_feedsync_format_date({./@modTime})]
 *
 * @since 1.2
 * @param string $date Date.
 * @return string formatted date
 * @revised 3.3
 */
function epl_feedsync_format_date( $date ) {
	$date_example = '2014-07-22-16:45:56';

	$tempdate = explode( '-', $date );
	$date     = $tempdate[0];
	if ( isset( $tempdate[1] ) ) {
		$date .= '-' . $tempdate[1];
	}
	if ( isset( $tempdate[2] ) ) {
		$date .= '-' . $tempdate[2];
	}
	if ( isset( $tempdate[3] ) ) {
		$date .= ' ' . $tempdate[3];
	}

	return date( 'Y-m-d H:i:s', strtotime( $date ) );
}

/**
 * REAXML Auction Date Processing Function for WP All Import and FeedSync
 *
 * Some imports set the current date instead of the date from the REAXML file.
 * Usage in WP All Import Post Date field is:
 * [epl_feedsync_format_date_auction({AUCDATE[1]},{AUC_TIME[1]})]
 *
 * @since 3.1.7
 * @param string $date Date.
 * @param string $time Time.
 * @return string formatted date
 */
function epl_feedsync_format_date_auction( $date, $time ) {

	$date = str_replace( '/', '-', $date ); // Convert to european date format for strtotime function.
	return date( 'Y-M-d H:i:s', strtotime( $date . ' ' . $time ) );
}

/**
 * REAXML Address Sub Number field for title import
 * processing Function for WP All Import and FeedSync
 *
 * This function adds a / after the $value else returns false.
 * Usage in WP All Import Post Date field is:
 * [epl_feedsync_filter_sub_number({address[1]/subNumber[1]})]
 *
 * @since 1.3
 * @param string $sub_value Address sub value.
 * @return string formatted sub number/
 */
function epl_feedsync_format_sub_number( $sub_value ) {
	if ( $sub_value ) {
		$sub_value = $sub_value . '/';
		return $sub_value;
	}
}

/**
 * REAXML convert currency to integer
 *
 * Processing Function for WP All Import and FeedSync
 *
 * @param string $value Value.
 * @return integer
 * @since 3.0
 */
function epl_feedsync_format_strip_currency( $value ) {
	if ( $value ) {
		$value = preg_replace( apply_filters( 'epl_feedsync_format_strip_currency_symbol', '/[\$,]/' ), '', $value );
		$value = floatval( $value );
		return $value;
	}
}

/**
 * REAXML convert date/time to adjust for timezone
 *
 * Processing Function for WP All Import and FeedSync
 * [epl_feedsync_switch_date_time({firstDate[1]},"Australia/Perth","Australia/Sydney")]
 *
 * @param bool   $date_time Swtich date time.
 * @param string $old_time_zone Old Timezone.
 * @param string $new_timezone New timezone.
 * @param string $format Date format.
 *
 * @return integer
 * @throws exception Exception.
 * @since 3.0
 */
function epl_feedsync_switch_date_time( $date_time = false, $old_time_zone = 'Australia/Perth', $new_timezone = 'Australia/Sydney', $format = 'Y-m-d H:i:s' ) {

	if ( ! $date_time ) {
		$date_time = date( 'Y-m-d H:i:s', time() );
	}
	if ( ! $old_time_zone ) {
		$old_time_zone = 'Australia/Perth';
	}
	if ( ! $new_timezone ) {
		$new_timezone = 'Australia/Sydney';
	}

	$schedule_date = new DateTime( $date_time, new DateTimeZone( $new_timezone ) );
	$schedule_date->setTimeZone( new DateTimeZone( $old_time_zone ) );
	return $schedule_date->format( $format );

}

/**
 * Renders field array to html
 *
 * @since 2.1
 * @param array  $field Field array.
 * @param string $val Value.
 * @revised 3.3
 */
function epl_render_html_fields( $field = array(), $val = '' ) {
	global $post;
	switch ( $field['type'] ) {
		case 'select':
			$dependency = 'false';
			if ( isset( $field['opt_args'] ) && ! empty( $field['opt_args'] ) ) {
				if ( isset( $field['opt_args']['type'] ) ) {
					switch ( $field['opt_args']['type'] ) {
						case 'taxonomy':
							$terms = get_terms(
								$field['opt_args']['slug'],
								array(
									'hide_empty' => 0,
									'parent'     => 0,
								)
							);

							if ( empty( $field['opt_args']['parent'] ) ) {
								$var = sanitize_title( $field['opt_args']['slug'] );
								$var = 'var_' . str_replace( '-', '_', $var );

								if ( ! isset( $$var ) ) {
									$$var = array();
									if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
										$arr = array( '' => '' );
										foreach ( $terms as $term ) {
											$arr[ $term->term_id ] = $term->name;
										}
									}
									$$var = $arr;
								}
								$field['opts'] = $$var;
							} else {
								$dependency = 'true';
							}
							break;
					}
				}
			}

			$field_atts = '';
			if ( 'true' === $dependency ) {
				$field_atts = 'data-dependency="true" data-type="taxonomy" data-type-name="' . $field['opt_args']['slug'] . '" data-parent="' . $field['opt_args']['parent'] . '" data-default="' . $val . '"';
			}

			echo '<select name="' . esc_attr( $field['name'] ) . '" id="' . esc_attr( $field['name'] ) . '" ' . $field_atts . ' class="dependency-' . esc_attr( $dependency ) . '">'; //phpcs:ignore

			if ( isset( $field['opts'] ) && ! empty( $field['opts'] ) ) {
				foreach ( $field['opts'] as $k => $v ) {
					$selected = '';
					if ( $val == $k ) { //phpcs:ignore
						$selected = 'selected';
					}

					if ( is_array( $v ) ) {
						if ( isset( $v['exclude'] ) && ! empty( $v['exclude'] ) ) {
							if ( in_array( $post->post_type, $v['exclude'], true ) ) {
								continue;
							}
						}

						if ( isset( $v['include'] ) && ! empty( $v['include'] ) ) {
							if ( ! in_array( $post->post_type, $v['include'], true ) ) {
								continue;
							}
						}
						$v = $v['label'];
					}

					echo '<option value="' . esc_attr( $k ) . '" ' . esc_attr( $selected ) . '>' . esc_html( $v ) . '</option>';
				}
			} else {
				echo '<option value=""> </option>';
			}
			echo '</select>';
			break;
		case 'select_multiple':
			$val = (array) $val;
			echo '<select multiple name="' . esc_attr( $field['name'] ) . '[]" id="' . esc_attr( $field['name'] ) . '" >';

			if ( isset( $field['opts'] ) && ! empty( $field['opts'] ) ) {
				foreach ( $field['opts'] as $k => $v ) {
					$selected = '';
					if ( in_array( $k, $val ) ) { //phpcs:ignore
						$selected = 'selected';
					}

					if ( is_array( $v ) ) {
						if ( isset( $v['exclude'] ) && ! empty( $v['exclude'] ) ) {
							if ( in_array( $post->post_type, $v['exclude'], true ) ) {
								continue;
							}
						}

						if ( isset( $v['include'] ) && ! empty( $v['include'] ) ) {
							if ( ! in_array( $post->post_type, $v['include'], true ) ) {
								continue;
							}
						}
						$v = $v['label'];
					}

					echo '<option value="' . esc_attr( $k ) . '" ' . esc_attr( $selected ) . '>' . esc_html( $v ) . '</option>';
				}
			} else {
				echo '<option value=""> </option>';
			}
			echo '</select>';
			break;

		case 'checkbox':
			if ( ! empty( $field['opts'] ) ) {
				foreach ( $field['opts'] as $k => $v ) {
					$checked = '';
					if ( ! empty( $val ) ) {

						$val = (array) $val;
						if ( in_array( $k, $val ) ) { //phpcs:ignore
							$checked = 'checked';
						}
					}
					echo '<span class="epl-field-row"><input type="checkbox" name="' . esc_attr( $field['name'] ) . '[]" id="' . esc_attr( $field['name'] ) . '_' . esc_attr( $k ) . '" value="' . esc_attr( $k ) . '" ' . esc_attr( $checked ) . ' /> <label for="' . esc_html( $field['name'] ) . '_' . esc_attr( $k ) . '">' . esc_html( $v ) . '</label></span>';
				}
			}
			break;

		case 'checkbox_single':
			if ( ! empty( $field['opts'] ) ) {
				foreach ( $field['opts'] as $k => $v ) {
					$checked = '';
					if ( ! empty( $val ) ) {
						$checkbox_single_options = apply_filters( 'epl_checkbox_single_check_options', array( 1, 'yes', 'on', 'true', '1' ) );
						if ( $k == $val || in_array( $val, $checkbox_single_options, true ) ) { //phpcs:ignore
							$checked = 'checked';
						}
					}
					if ( 1 === count( $field['opts'] ) ) {
						$v = $field['label'];
					}
					echo '<span class="epl-field-row"><input type="checkbox" name="' . esc_attr( $field['name'] ) . '" id="' . esc_attr( $field['name'] ) . '_' . esc_attr( $k ) . '" value="' . esc_attr( $k ) . '" ' . esc_attr( $checked ) . ' /> <label for="' . esc_html( $field['name'] ) . '_' . esc_attr( $k ) . '">' . esc_html( $v ) . '</label></span>';
				}
			}
			break;

		case 'checkbox_option':
			if ( ! empty( $field['opts'] ) ) {
				foreach ( $field['opts'] as $k => $v ) {
					$checked = '';
					if ( ! empty( $val ) ) {
						if ( $k == $val ) { //phpcs:ignore
							$checked = 'checked';
						}
					}
					echo '<span class="epl-field-row"><input type="checkbox" name="' . esc_attr( $field['name'] ) . '" id="' . esc_attr( $field['name'] ) . '_' . esc_attr( $k ) . '" value="' . esc_attr( $k ) . '" ' . esc_attr( $checked ) . ' /> <label for="' . esc_html( $field['name'] ) . '_' . esc_attr( $k ) . '">' . esc_html( $v ) . '</label></span>';
				}
			}
			break;

		case 'radio':
			if ( ! empty( $field['opts'] ) ) {
				foreach ( $field['opts'] as $k => $v ) {
					$checked = '';
					if ( $val == $k ) { //phpcs:ignore
						$checked = 'checked';
					}
					echo '<span class="epl-field-row"><input type="radio" name="' . esc_attr( $field['name'] ) . '" id="' . esc_attr( $field['name'] ) . '_' . esc_attr( $k ) . '" value="' . esc_attr( $k ) . '" ' . esc_attr( $checked ) . ' /> <label for="' . esc_html( $field['name'] ) . '_' . esc_attr( $k ) . '">' . esc_html( $v ) . '</label></span>';
				}
			}
			break;

		case 'image':
		case 'file':
			if ( is_array( $val ) ) {
				$val = isset( $val['image_url_or_path'] ) ? $val['image_url_or_path'] : '';
			}

			if ( ! empty( $val ) ) {
				$img = esc_attr( stripslashes( $val ) );
			} else {
				$img = plugin_dir_url( __DIR__ ) . 'assets/images/no_image.png'; //phpcs:ignore
			}

			echo '
				<div class="epl-media-row">
					<input type="text" name="' . esc_attr( $field['name'] ) . '" id="' . esc_attr( $field['name'] ) . '" value="' . esc_attr( stripslashes( $val ) ) . '" />
					&nbsp;&nbsp;<input type="button" name="epl_upload_button" class="button" value="' . esc_html__( 'Add File', 'easy-property-listings' ) . '" />';

			if ( in_array( pathinfo( $img, PATHINFO_EXTENSION ), array( 'jpg', 'jpeg', 'png', 'gif' ), true ) ) {
				echo '&nbsp;&nbsp;<img src="' . esc_url( $img ) . '" alt="" />';
			}
			echo '<div class="epl-clear"></div>
				</div>
			';
			break;

		case 'editor':
			wp_editor( stripslashes( $val ), esc_attr( $field['name'] ), $settings = array( 'textarea_rows' => 5 ) );
			break;

		case 'textarea':
			$atts = '';
			if ( isset( $field['maxlength'] ) && $field['maxlength'] > 0 ) {
				$atts = ' maxlength="' . absint( $field['maxlength'] ) . '"';
			}
			echo '<textarea name="' . esc_attr( $field['name'] ) . '" id="' . esc_attr( $field['name'] ) . '" ' . wp_kses_post( $atts ) . '>' . wp_kses_post( stripslashes( $val ) ) . '</textarea>';
			break;

		case 'decimal':
			$atts = '';
			if ( $field['maxlength'] > 0 ) {
				$atts .= ' maxlength="' . absint( $field['maxlength'] ) . '"';
			}

			if ( ! empty( $field['autocomplete'] ) ) {
				$atts .= ' autocomplete="' . esc_attr( $field['autocomplete'] ) . '"';
			}
			echo '<input type="text" name="' . esc_attr( $field['name'] ) . '" id="' . esc_attr( $field['name'] ) . '" value="' . esc_attr( $val ) . '" class="validate[custom[onlyNumberWithDecimal]]" ' . wp_kses_post( $atts ) . ' />';
			break;

		case 'number':
			$atts = '';
			if ( isset( $field['maxlength'] ) && $field['maxlength'] > 0 ) {
				$atts .= ' maxlength="' . absint( $field['maxlength'] ) . '"';
			}
			if ( ! empty( $field['autocomplete'] ) ) {
				$atts .= ' autocomplete="' . esc_attr( $field['autocomplete'] ) . '"';
			}
			echo '<input type="number" name="' . esc_attr( $field['name'] ) . '" id="' . esc_attr( $field['name'] ) . '" value="' . esc_attr( stripslashes( $val ) ) . '" class="validate[custom[onlyNumber]]" ' . wp_kses_post( $atts ) . ' />';
			break;

		case 'date':
			$atts = '';
			if ( ! empty( $field['autocomplete'] ) ) {
				$atts .= ' autocomplete="' . esc_attr( $field['autocomplete'] ) . '"';
			}
			$format     = isset( $field['format'] ) ? $field['format'] : 'Y-m-d';
			$timepicker = isset( $field['timepicker'] ) ? $field['timepicker'] : false;
			echo '<input type="text" class="epldatepicker" data-format="' . esc_attr( $format ) . '"  data-timepicker="' . esc_attr( $timepicker ) . '" name="' . esc_attr( $field['name'] ) . '" id="' . esc_attr( $field['name'] ) . '" value="' . esc_attr( stripslashes( $val ) ) . '" ' . wp_kses_post( $atts ) . ' />';
			break;

		case 'auction-date':
			$atts = '';
			if ( ! empty( $field['autocomplete'] ) ) {
				$atts .= ' autocomplete="' . esc_attr( $field['autocomplete'] ) . '"';
			}
			echo '<input type="text" name="' . esc_attr( $field['name'] ) . '" id="' . esc_attr( $field['name'] ) . '" value="' . esc_attr( stripslashes( $val ) ) . '" ' . wp_kses_post( $atts ) . ' />';
			break;

		case 'sold-date':
			$atts = '';
			if ( ! empty( $field['autocomplete'] ) ) {
				$atts .= ' autocomplete="' . esc_attr( $field['autocomplete'] ) . '"';
			}
			echo '<input type="text" name="' . esc_attr( $field['name'] ) . '" id="' . esc_attr( $field['name'] ) . '" value="' . esc_attr( stripslashes( $val ) ) . '" ' . wp_kses_post( $atts ) . ' />';
			break;

		case 'email':
			$atts = '';
			if ( ! empty( $field['autocomplete'] ) ) {
				$atts .= ' autocomplete="' . esc_attr( $field['autocomplete'] ) . '"';
			}
			if ( ! empty( $field['autocomplete'] ) ) {
				$atts .= ' autocomplete="' . esc_attr( $field['autocomplete'] ) . '"';
			}
			echo '<input type="text" name="' . esc_attr( $field['name'] ) . '" id="' . esc_attr( $field['name'] ) . '" value="' . esc_attr( stripslashes( $val ) ) . '" class="validate[custom[email]]" ' . wp_kses_post( $atts ) . ' />';
			break;

		case 'url':
			$atts = '';
			if ( ! empty( $field['autocomplete'] ) ) {
				$atts .= ' autocomplete="' . esc_attr( $field['autocomplete'] ) . '"';
			}
			echo '<input type="text" name="' . esc_attr( $field['name'] ) . '" id="' . esc_attr( $field['name'] ) . '" value="' . esc_attr( stripslashes( $val ) ) . '" class="validate[custom[url]]" ' . wp_kses_post( $atts ) . ' />';
			break;
		case 'button':
			$classes = isset( $field['class'] ) ? $field['class'] : '';
			echo '<input type="button" name="' . esc_attr( $field['name'] ) . '" id="' . esc_attr( $field['name'] ) . '" value="' . esc_attr( stripslashes( $field['value'] ) ) . '" class="' . esc_attr( $classes ) . '" />';
			break;
		case 'locked':
			$atts = '';
			echo '<span>' . esc_attr( stripslashes( $val ) ) . '</span>';
			break;

		case 'help':
			$content = isset( $field['content'] ) ? $field['content'] : '';
			$help_id = isset( $field['name'] ) ? sanitize_key( $field['name'] ) : '';
			//phpcs:ignore
			echo '<div class="epl-help-container" id="'.$help_id.'">
					' . wp_kses_post( $content ) . '
				</div>';
			break;

		default:
			$atts = '';
			if ( isset( $field['maxlength'] ) && $field['maxlength'] > 0 ) {
				$atts .= ' maxlength="' . $field['maxlength'] . '"';
			}
			if ( ! empty( $field['autocomplete'] ) ) {
				$atts .= ' autocomplete="' . esc_attr( $field['autocomplete'] ) . '"';
			}
			$classes = isset( $field['class'] ) ? $field['class'] : '';
			foreach ( $field as $temp_key => $temp_value ) {
				if ( 0 === strpos( $temp_key, 'data-' ) ) {
					$atts .= '' . $temp_key . '="' . $temp_value . '"';
				}
			}
			echo '<input type="' . esc_attr( $field['type'] ) . '" name="' . esc_attr( $field['name'] ) . '" id="' . esc_attr( $field['name'] ) . '" class="' . esc_attr( $classes ) . '"  value="' . esc_attr( stripslashes( $val ) ) . '" ' . wp_kses_post( $atts ) . ' />';
	}

	if ( isset( $field['geocoder'] ) ) {
		if ( 'true' == $field['geocoder'] ) { //phpcs:ignore
			echo '<span class="epl-geocoder-button"></span>';
		}

		do_action( 'epl_admin_listing_map', esc_attr( stripslashes( $val ) ) );
	}

	if ( isset( $field['help'] ) ) {
		$field['help'] = trim( $field['help'] );
		if ( ! empty( $field['help'] ) ) {
			echo '<span class="epl-help-text">' . wp_kses_post( $field['help'] ) . '</span>';
		}
	}
}

/**
 * Admin options settings
 *
 * @since 2.1
 */
function epl_get_admin_option_fields() {
	global $epl_settings;
	$opts_epl_gallery_n = array();
	for ( $i = 1; $i <= 10; $i++ ) {
		$opts_epl_gallery_n[ $i ] = $i;
	}

	$opts_epl_property_card_excerpt_length = array();
	for ( $i = 10; $i <= 55; $i++ ) {
		$opts_epl_property_card_excerpt_length[ $i ] = $i;
	}

	$opts_epl_features = array();
	for ( $i = 1; $i <= 5; $i++ ) {
		$opts_epl_features[ $i ] = $i;
	}

	$opts_pages = array( '' => __( 'Select Page', 'easy-property-listings' ) );
	$pages      = get_pages();

	if ( ! empty( $pages ) ) {
		foreach ( $pages as $page ) {
			$opts_pages[ $page->ID ] = $page->post_title;
		}
	}

	$get_google_maps_api_key_uri = '<a target="_blank" href="https://developers.google.com/maps/documentation/javascript/get-api-key">' . __( 'Google Maps API Key', 'easy-property-listings' ) . '</a>';

	$epl_currency_positions = array(
		'before' => __( 'Before - $10', 'easy-property-listings' ),
		'after'  => __( 'After - 10$', 'easy-property-listings' ),
	);
	$epl_currency_types     = epl_get_currencies();
	$epl_post_types         = epl_get_post_types();
	if ( ! function_exists( 'get_editable_roles' ) ) {
		require_once ABSPATH . '/wp-admin/includes/user.php';
	}
	$roles  = array(
		'level_10' => __( 'Administrator', 'easy-property-listings' ),
		'level_7'  => __( 'Editor', 'easy-property-listings' ),
		'level_2'  => __( 'Author', 'easy-property-listings' ),
		'level_1'  => __( 'Contributor', 'easy-property-listings' ),
		'level_0'  => __( 'Subscriber', 'easy-property-listings' ),
	);
	$fields = array(
		array(
			'label'  => __( 'Listing Types and Location Taxonomy', 'easy-property-listings' ),
			'class'  => 'core',
			'id'     => 'general',
			'help'   => __( 'Select the listing types you want to enable and press Save Changes. Refresh the page to see your new activated listing types.', 'easy-property-listings' ) . '<hr/>',
			'fields' => array(
				array(
					'name'  => 'activate_post_types',
					'label' => __( 'Listing Types to Enable', 'easy-property-listings' ),
					'type'  => 'checkbox',
					'opts'  => $epl_post_types,
					'help'  => __( 'Note: If they are not visible on the front end visit Dashboard > Settings > Permalinks and press Save Changes.', 'easy-property-listings' ),
				),

				array(
					'name'    => 'label_location',
					'label'   => __( 'Location Taxonomy', 'easy-property-listings' ),
					'type'    => 'text',
					'help'    => __( 'After changing this setting visit Dashboard > Settings > Permalinks to save the settings.', 'easy-property-listings' ),
					'default' => __( 'Location', 'easy-property-listings' ),
				),
			),
		),

		array(
			'label'  => __( 'Address', 'easy-property-listings' ),
			'class'  => 'core',
			'id'     => 'address',
			'help'   => __( 'Use these settings to adjust the address labels to suite your location and address format for your listings.', 'easy-property-listings' ) . '<hr/>',
			'fields' => array(

				array(
					'name'    => 'label_suburb',
					'label'   => __( 'Suburb/Town/City Label', 'easy-property-listings' ),
					'type'    => 'text',
					'default' => __( 'Suburb', 'easy-property-listings' ),
				),

				array(
					'name'    => 'epl_enable_city_field',
					'label'   => __( 'Additional Address Field', 'easy-property-listings' ),
					'type'    => 'radio',
					'opts'    => array(
						'yes' => __( 'Enable', 'easy-property-listings' ),
						'no'  => __( 'Disable', 'easy-property-listings' ),
					),
					'default' => 'no',
					'help'    => __( 'Use when you need an additional Municipality/Town/City/Region.', 'easy-property-listings' ),
				),

				array(
					'name'    => 'label_city',
					'label'   => __( 'Additional Address Field Label', 'easy-property-listings' ),
					'type'    => 'text',
					'default' => __( 'City', 'easy-property-listings' ),
				),

				array(
					'name'    => 'label_state',
					'label'   => __( 'State/Province/Region Label', 'easy-property-listings' ),
					'type'    => 'text',
					'default' => __( 'State', 'easy-property-listings' ),
				),

				array(
					'name'    => 'label_postcode',
					'label'   => __( 'Postcode/ZIP Label', 'easy-property-listings' ),
					'type'    => 'text',
					'default' => __( 'Postcode', 'easy-property-listings' ),
				),

				array(
					'name'    => 'epl_enable_country_field',
					'label'   => __( 'Display Country', 'easy-property-listings' ),
					'type'    => 'radio',
					'opts'    => array(
						'yes' => __( 'Enable', 'easy-property-listings' ),
						'no'  => __( 'Disable', 'easy-property-listings' ),
					),
					'default' => 'no',
					'help'    => __( 'Display country with listing address.', 'easy-property-listings' ),
				),

				array(
					'name'    => 'epl_default_country',
					'label'   => __( 'Default Country', 'easy-property-listings' ),
					'type'    => 'select',
					'opts'    => epl_get_countries_list(),
					'default' => 'Australia',
					'help'    => __( 'This is used for map when listing has no address details', 'easy-property-listings' ),
				),

			),
		),

		array(
			'label'  => __( 'Labels', 'easy-property-listings' ),
			'class'  => 'core',
			'id'     => 'labels',
			'help'   => __( 'Customise the labels that are used to show the status and information on the archive and individual listing pages.', 'easy-property-listings' ) . '<hr/>',
			'fields' => array(

				array(
					'name'    => 'label_new',
					'label'   => __( 'New/Just Listed Label', 'easy-property-listings' ),
					'type'    => 'text',
					'default' => __( 'New', 'easy-property-listings' ),
				),

				array(
					'name'    => 'sticker_new_range',
					'label'   => __( 'Keep Listings flagged "New" for', 'easy-property-listings' ),
					'type'    => 'number',
					'default' => '7',
					'help'    => __( 'Listings will have a "NEW" Sticker for the defined number of days.', 'easy-property-listings' ),
				),

				array(
					'name'    => 'label_home_open',
					'label'   => __( 'Home Open Label', 'easy-property-listings' ),
					'type'    => 'text',
					'default' => __( 'Home Open', 'easy-property-listings' ),
				),

				array(
					'name'    => 'label_poa',
					'label'   => __( 'No Price Label', 'easy-property-listings' ),
					'type'    => 'text',
					'default' => __( 'POA', 'easy-property-listings' ),
				),

				array(
					'name'    => 'label_under_offer',
					'label'   => __( 'Under Offer Label', 'easy-property-listings' ),
					'type'    => 'text',
					'default' => __( 'Under Offer', 'easy-property-listings' ),
				),

				array(
					'name'    => 'label_sold',
					'label'   => __( 'Sold Label', 'easy-property-listings' ),
					'type'    => 'text',
					'default' => __( 'Sold', 'easy-property-listings' ),
				),

				array(
					'name'    => 'label_leased',
					'label'   => __( 'Leased Label', 'easy-property-listings' ),
					'type'    => 'text',
					'default' => __( 'Leased', 'easy-property-listings' ),
				),

				array(
					'name'    => 'label_bond',
					'label'   => __( 'Rental Bond/Deposit Label', 'easy-property-listings' ),
					'type'    => 'text',
					'default' => __( 'Bond', 'easy-property-listings' ),
				),

				array(
					'name'  => 'display_bond',
					'label' => __( 'Rental Bond/Deposit Display', 'easy-property-listings' ),
					'type'  => 'radio',
					'opts'  => array(
						1 => __( 'Enable', 'easy-property-listings' ),
						0 => __( 'Disable', 'easy-property-listings' ),
					),
					'help'  => __( 'Display the Bond/Deposit on rental listings.', 'easy-property-listings' ),
				),
			),
		),

		array(
			'label'  => __( 'Listing Single View', 'easy-property-listings' ),
			'class'  => 'core',
			'id'     => 'general',
			'help'   => __( 'Configure the default options when viewing a single listing.', 'easy-property-listings' ) . '<hr/>',
			'fields' => array(
				array(
					'name'    => 'display_single_gallery',
					'label'   => __( 'Automatically display image gallery?', 'easy-property-listings' ),
					'type'    => 'radio',
					'opts'    => array(
						1 => __( 'Enable', 'easy-property-listings' ),
						0 => __( 'Disable', 'easy-property-listings' ),
					),
					'default' => 1,
					'help'    => __( 'Images uploaded and attached to a listing will automatically display on the single listing page.', 'easy-property-listings' ),
				),

				array(
					'name'    => 'display_gallery_n',
					'label'   => __( 'Gallery columns?', 'easy-property-listings' ),
					'type'    => 'select',
					'opts'    => $opts_epl_gallery_n,
					'default' => 4,
				),

				array(
					'name'    => 'display_feature_columns',
					'label'   => __( 'Feature list columns?', 'easy-property-listings' ),
					'type'    => 'select',
					'opts'    => $opts_epl_features,
					'default' => 2,
				),

				array(
					'name'  => 'epl_video_width',
					'label' => __( 'Video width on single listings', 'easy-property-listings' ),
					'type'  => 'number',
					'help'  => __( 'Width should be in pixels', 'easy-property-listings' ),
				),
			),
		),

		array(
			'label'  => __( 'Listing Archive View', 'easy-property-listings' ),
			'class'  => 'core',
			'id'     => 'general',
			'help'   => __( 'Configure the default options for when viewing the archive listing pages.', 'easy-property-listings' ) . '<hr/>',
			'fields' => array(
				array(
					'name'    => 'display_excerpt_length',
					'label'   => __( 'Excerpt words', 'easy-property-listings' ),
					'type'    => 'select',
					'opts'    => $opts_epl_property_card_excerpt_length,
					'default' => 10,
					'help'    => __( 'This is ignored when using manual excerpts.', 'easy-property-listings' ),
				),
				array(
					'name'    => 'display_archive_view_type',
					'label'   => __( 'Listing view type', 'easy-property-listings' ),
					'type'    => 'radio',
					'opts'    => array(
						'list' => __( 'List', 'easy-property-listings' ),
						'grid' => __( 'Grid', 'easy-property-listings' ),
					),
					'default' => 'list',
				),

				array(
					'name'    => 'use_fancy_navigation',
					'label'   => __( 'Fancy pagination', 'easy-property-listings' ),
					'type'    => 'select',
					'opts'    => array(
						0 => __( 'No, use WordPress default pagination', 'easy-property-listings' ),
						1 => __( 'Yes, use fancy navigation', 'easy-property-listings' ),
					),
					'default' => 0,
				),
			),
		),

		array(
			'label'  => __( 'Search Widget: Tab Labels', 'easy-property-listings' ),
			'class'  => 'core',
			'id'     => 'labels',
			'help'   => __( 'Customise the tab labels of the EPL - Search Widget or [listing_search] shortcode.', 'easy-property-listings' ) . '<hr/>',
			'fields' => array(
				array(
					'name'    => 'widget_label_all',
					'label'   => __( 'All', 'easy-property-listings' ),
					'type'    => 'text',
					'default' => __( 'All', 'easy-property-listings' ),
				),
				array(
					'name'    => 'widget_label_property',
					'label'   => __( 'Property', 'easy-property-listings' ),
					'type'    => 'text',
					'default' => __( 'Property', 'easy-property-listings' ),
				),
				array(
					'name'    => 'widget_label_land',
					'label'   => __( 'Land', 'easy-property-listings' ),
					'type'    => 'text',
					'default' => __( 'Land', 'easy-property-listings' ),
				),
				array(
					'name'    => 'widget_label_rental',
					'label'   => __( 'Rental', 'easy-property-listings' ),
					'type'    => 'text',
					'default' => __( 'Rental', 'easy-property-listings' ),
				),
				array(
					'name'    => 'widget_label_rural',
					'label'   => __( 'Rural', 'easy-property-listings' ),
					'type'    => 'text',
					'default' => __( 'Rural', 'easy-property-listings' ),
				),
				array(
					'name'    => 'widget_label_commercial',
					'label'   => __( 'Commercial', 'easy-property-listings' ),
					'type'    => 'text',
					'default' => __( 'Commercial', 'easy-property-listings' ),
				),
				array(
					'name'    => 'widget_label_commercial_land',
					'label'   => __( 'Commercial Land', 'easy-property-listings' ),
					'type'    => 'text',
					'default' => __( 'Commercial Land', 'easy-property-listings' ),
				),
				array(
					'name'    => 'widget_label_business',
					'label'   => __( 'Business', 'easy-property-listings' ),
					'type'    => 'text',
					'default' => __( 'Business', 'easy-property-listings' ),
				),
			),
		),

		array(
			'label'  => __( 'Dashboard Listing Columns', 'easy-property-listings' ),
			'class'  => 'core',
			'id'     => 'admin_general',
			'help'   => __( 'Use the following settings to change how the Dashboard > Property or any listing of the types you have enabled display.', 'easy-property-listings' ) . '<hr/>',
			'fields' => array(

				array(
					'name'    => 'epl_max_graph_sales_price',
					'label'   => __( 'Graph Max', 'easy-property-listings' ),
					'type'    => 'number',
					'default' => '2000000',
					'help'    => __( 'Used for bar chart display on listings for sale.', 'easy-property-listings' ),
				),

				array(
					'name'    => 'epl_max_graph_rent_price',
					'label'   => __( 'Graph Rental Max', 'easy-property-listings' ),
					'type'    => 'number',
					'default' => '2000',
					'help'    => __( 'Rental range.', 'easy-property-listings' ),
				),

				array(
					'name'    => 'epl_admin_thumb_size',
					'label'   => __( 'Image size', 'easy-property-listings' ),
					'type'    => 'radio',
					'opts'    => array(
						'admin-list-thumb'      => __( '100 X 100', 'easy-property-listings' ),
						'epl-image-medium-crop' => __( '300 X 200', 'easy-property-listings' ),
					),
					'default' => 'admin-list-thumb',
					'help'    => __( 'Size of the image shown in listing columns in admin area', 'easy-property-listings' ),
				),

				array(
					'name'    => 'admin_unique_id',
					'label'   => __( 'Unique Listing ID column', 'easy-property-listings' ),
					'type'    => 'radio',
					'opts'    => array(
						1 => __( 'Enable', 'easy-property-listings' ),
						0 => __( 'Disable', 'easy-property-listings' ),
					),
					'default' => 0,
				),

				array(
					'name'    => 'debug',
					'label'   => __( 'Geocode Lat/Long results column', 'easy-property-listings' ),
					'type'    => 'radio',
					'opts'    => array(
						1 => __( 'Enable', 'easy-property-listings' ),
						0 => __( 'Disable', 'easy-property-listings' ),
					),
					'default' => 0,
				),
			),
		),

		array(
			'label'  => __( 'Theme Setup', 'easy-property-listings' ),
			'class'  => 'core',
			'id'     => 'theme_setup',
			'help'   => __( 'The following settings will use your theme templates to generate your listing pages. If your listings appear too wide or your sidebar is in the wrong place enable theme compatibility. When this is enabled you can use the included shortcodes like [listing post_type="property" tools_top="on"] to display your listings with sorting and grid options.', 'easy-property-listings' ) . '<hr/>',
			'fields' => array(

				array(
					'name'    => 'epl_feeling_lucky',
					'label'   => __( 'Theme Compatibility', 'easy-property-listings' ),
					'type'    => 'radio',
					'opts'    => array(
						'on'  => __( 'Enable', 'easy-property-listings' ),
						'off' => __( 'Disable', 'easy-property-listings' ),
					),
					'default' => 'off',
					'help'    => __( 'When using iThemes, Genesis frameworks or your listings look good, leave this disabled.', 'easy-property-listings' ),
				),
			),
		),

		array(
			'label'  => __( 'Theme Setup: Featured Images', 'easy-property-listings' ),
			'class'  => 'core',
			'id'     => 'theme_setup_featured_images',
			'help'   => __( 'Some WordPress themes automatically display featured images on posts and pages which may cause you to see double on your listings. Use the following settings to adjust the featured image behaviour.', 'easy-property-listings' ) . '<hr/>',
			'fields' => array(

				array(
					'name'    => 'help_lucky_theme_featured_image',
					'type'    => 'help',
					'content' => __( 'Theme Featured Image Settings', 'easy-property-listings' ),
				),

				array(
					'name'    => 'epl_lucky_disable_theme_single_thumb',
					'label'   => __( 'Single Listing', 'easy-property-listings' ),
					'type'    => 'checkbox_option',
					'opts'    => array(
						'on' => __( 'Disable', 'easy-property-listings' ),
					),
					'default' => 'off',
				),

				array(
					'name'    => 'epl_lucky_disable_archive_thumb',
					'label'   => __( 'Archive Listing', 'easy-property-listings' ),
					'type'    => 'checkbox_option',
					'opts'    => array(
						'on' => __( 'Disable', 'easy-property-listings' ),
					),
					'default' => 'off',
				),

				array(
					'name'    => 'help_lucky_epl_featured_image',
					'type'    => 'help',
					'content' => '<hr/>' . __( 'Easy Property Listings Featured Image Settings', 'easy-property-listings' ),
				),

				array(
					'name'    => 'epl_lucky_disable_single_thumb',
					'label'   => __( 'Single Listing', 'easy-property-listings' ),
					'type'    => 'checkbox_option',
					'opts'    => array(
						'on' => __( 'Disable', 'easy-property-listings' ),
					),
					'default' => 'off',
				),

				array(
					'name'    => 'epl_lucky_disable_epl_archive_thumb',
					'label'   => __( 'Archive Listing', 'easy-property-listings' ),
					'type'    => 'checkbox_option',
					'opts'    => array(
						'on' => __( 'Disable', 'easy-property-listings' ),
					),
					'default' => 'off',
				),
			),
		),

		array(
			'label'  => __( 'Currency', 'easy-property-listings' ),
			'class'  => 'core',
			'id'     => 'currency',
			// translators: currency artice link.
			'help'   => sprintf( __( 'Select your default currency. If you can not find the currency you are looking for, you can add additional currencies with a filter, <a href="%s" target="_blank">visit the codex to see how</a>.', 'easy-property-listings' ), esc_url( 'https://codex.easypropertylistings.com.au/article/153-eplgetcurrencies' ) ) . '<hr/>',
			'fields' => array(
				array(
					'name'  => 'currency',
					'label' => __( 'Currency Type', 'easy-property-listings' ),
					'type'  => 'select',
					'opts'  => $epl_currency_types,
				),

				array(
					'name'  => 'currency_position',
					'label' => __( 'Symbol Position', 'easy-property-listings' ),
					'type'  => 'select',
					'opts'  => $epl_currency_positions,
				),

				array(
					'name'  => 'currency_thousands_separator',
					'label' => __( 'Thousands Separator', 'easy-property-listings' ),
					'type'  => 'text',
				),

				array(
					'name'  => 'currency_decimal_separator',
					'label' => __( 'Decimal Separator', 'easy-property-listings' ),
					'type'  => 'text',
				),
			),
		),

		array(
			'label'  => __( 'Inspection Date & Time Format', 'easy-property-listings' ),
			'class'  => 'core',
			'id'     => 'inspection_date_time',
			'help'   => __( 'These settings allow you to control how your inspection times display by default.', 'easy-property-listings' ) . '<hr/>',
			'fields' => array(
				array(
					'name'  => 'inspection_date_format',
					'label' => __( 'Date Format', 'easy-property-listings' ),
					'type'  => 'radio',
					'opts'  => array(

						'd-M-Y'                         => date( 'd-M-Y', time() ),
						'l, dS F'                       => date( 'l, dS F', time() ),
						'D d M'                         => date( 'D d M', time() ),
						'custom_inspection_date_format' => __( 'Custom', 'easy-property-listings' ),

					),
				),
				array(
					'name'  => 'custom_inspection_date_format',
					'label' => __( 'Custom Date Format', 'easy-property-listings' ),
					'type'  => 'text',
				),
				array(
					'name'  => 'inspection_time_format',
					'label' => __( 'Time Format', 'easy-property-listings' ),
					'type'  => 'radio',
					'opts'  => array(

						'h:i A'                         => date( 'h:i A', time() ),
						'h:i a'                         => date( 'h:i a', time() ),
						'H:i'                           => date( 'h:i', time() ) . __( ' ( 24 Hours Format ) ', 'easy-property-listings' ),
						'custom_inspection_time_format' => __( 'Custom', 'easy-property-listings' ),

					),
				),
				array(
					'name'  => 'custom_inspection_time_format',
					'label' => __( 'Custom Time Format', 'easy-property-listings' ),
					'type'  => 'text',
				),
			),
		),

		array(
			'label'   => __( 'Contact Settings', 'easy-property-listings' ),
			'class'   => 'core',
			'id'      => 'contact',
			'help'    => __( 'You can allow access to the Contacts system for logged in users.', 'easy-property-listings' ) . '<hr/>',
			'fields'  => array(
				array(
					'name'  => 'min_contact_access',
					'label' => __( 'Contact Access Level', 'easy-property-listings' ),
					'type'  => 'radio',
					'opts'  => $roles,
				),

			),
			'default' => 'administrator',
		),

		array(
			'label'   => __( 'Reports Settings', 'easy-property-listings' ),
			'class'   => 'core',
			'id'      => 'reports',
			'help'    => __( 'You can allow access to the Reports system for logged in users.', 'easy-property-listings' ) . '<hr/>',

			'fields'  => array(
				array(
					'name'  => 'min_reports_access',
					'label' => __( 'Reports Access Level', 'easy-property-listings' ),
					'type'  => 'radio',
					'opts'  => $roles,
				),

			),
			'default' => 'administrator',
		),

		array(
			'label'  => __( 'Advanced Settings', 'easy-property-listings' ),
			'class'  => 'core',
			'id'     => 'advanced',
			// translators: EPL site link.
			'help'   => sprintf( __( 'Additional settings to control how Easy Property Listings works. For even more advanced filters and hooks, <a href="%s" target="_blank">visit the codex</a>.', 'easy-property-listings' ), esc_url( 'https://codex.easypropertylistings.com.au/' ) ) . '<hr/>',
			'fields' => array(
				array(
					'name'    => 'epl_use_core_css',
					'label'   => __( 'Disable Styles', 'easy-property-listings' ),
					'type'    => 'checkbox_option',
					'opts'    => array(
						'on' => __( 'Yes', 'easy-property-listings' ),
					),
					'default' => 'off',
					'help'    => __( 'Check this to disable all elements.', 'easy-property-listings' ),
				),

				array(
					'name'    => 'epl_css_legacy',
					'label'   => __( 'Legacy Styles', 'easy-property-listings' ),
					'type'    => 'checkbox_option',
					'opts'    => array(
						'on' => __( 'Enable', 'easy-property-listings' ),
					),
					'default' => 'off',
					'help'    => __( 'Check this to enable legacy css styles.', 'easy-property-listings' ),
				),

				array(
					'name'    => 'epl_css_enhanced',
					'label'   => __( 'Enhanced Styles', 'easy-property-listings' ),
					'type'    => 'checkbox_option',
					'opts'    => array(
						'on' => __( 'Enable', 'easy-property-listings' ),
					),
					'default' => 'off',
					'help'    => __( 'Check this to enable enhanced structural css styles. This implements inline-block mode for a better grid format on your listings.', 'easy-property-listings' ),
				),

				array(
					'name'    => 'epl_icons_svg_listings',
					'label'   => __( 'SVG Icons for Listings', 'easy-property-listings' ),
					'type'    => 'checkbox_option',
					'opts'    => array(
						'on' => __( 'Enable', 'easy-property-listings' ),
					),
					'default' => 'off',
					'help'    => __( 'Check this to enable SVG icons on listings.', 'easy-property-listings' ),
				),

				array(
					'name'    => 'epl_icons_svg_author',
					'label'   => __( 'SVG Icons for Author Social Links', 'easy-property-listings' ),
					'type'    => 'checkbox_option',
					'opts'    => array(
						'on' => __( 'Enable', 'easy-property-listings' ),
					),
					'default' => 'off',
					'help'    => __( 'Check this to enable SVG icons on author social links.', 'easy-property-listings' ),
				),

				array(
					'name'    => 'epl_disable_google_api',
					'label'   => __( 'Google Maps API', 'easy-property-listings' ),
					'type'    => 'radio',
					'opts'    => array(
						''   => __( 'Enable', 'easy-property-listings' ),
						'on' => __( 'Disable', 'easy-property-listings' ),
					),
					'default' => '',
					'help'    => __( 'Set to disabled if Google Maps API has already been loaded in your theme or other plugin.', 'easy-property-listings' ),

				),

				array(
					'name'  => 'epl_google_api_key',
					'label' => __( 'Google Maps API Key', 'easy-property-listings' ),
					'type'  => 'text',
					// translators: google map register key link.
					'help'  => sprintf( __( 'Register for a %s here.', 'easy-property-listings' ), $get_google_maps_api_key_uri ),
				),

				array(
					'name'    => 'epl_plugin_mode',
					'label'   => __( 'Mode', 'easy-property-listings' ),
					'type'    => 'radio',
					'opts'    => array(
						'development' => __( 'Development', 'easy-property-listings' ),
						'production'  => __( 'Production', 'easy-property-listings' ),
					),
					'help'    => __( 'Production mode enqueue minified js & css for faster page loads.', 'easy-property-listings' ),
					'default' => 'production',
				),

				array(
					'name'    => 'uninstall_on_delete',
					'label'   => __( 'Remove Data on Uninstall?', 'easy-property-listings' ),
					'type'    => 'radio',
					'opts'    => array(
						1 => __( 'Enable', 'easy-property-listings' ),
						0 => __( 'Disable', 'easy-property-listings' ),
					),
					'help'    => __( 'Select Enable if you would like EPL to completely remove all of its data when the plugin is deleted.', 'easy-property-listings' ),
					'default' => 0,
				),
			),
		),

	);

	if ( defined( 'EPL_BETA_VERSIONS' ) && true === EPL_BETA_VERSIONS ) {
		$fields[] = array(
			'label'  => __( 'Beta Versions', 'easy-property-listings' ),
			'class'  => 'core',
			'id'     => 'beta-versions',
			'fields' => array(
				array(
					'name'  => 'enabled_betas',
					'label' => __( 'Enable Beta Versions', 'easy-property-listings' ),
					'type'  => 'checkbox',
					'opts'  => epl_get_beta_enabled_extensions(),
					'help'  => __( 'Checking any of the checkboxes will opt you in to receive pre-release update notifications. You can opt-out at any time. Pre-release updates do not install automatically, you will still have the opportunity to ignore update notifications.', 'easy-property-listings' ),
				),

			),
		);
	}

	$fields = apply_filters( 'epl_display_options_filter', $fields );
	return $fields;
}

/**
 * Sold status filter
 *
 * @since 2.1.11
 */
function epl_sold_label_status_filter_callback() {
	global $epl_settings;
	$epl_settings['label_sold'] = ! isset( $epl_settings['label_sold'] ) ? '' : $epl_settings['label_sold'];
	$sold_label                 = 'Sold' !== $epl_settings['label_sold'] || ! empty( $epl_settings['label_sold'] ) ? $epl_settings['label_sold'] : __( 'Sold', 'easy-property-listings' );
	return $sold_label;
}
add_filter( 'epl_sold_label_status_filter', 'epl_sold_label_status_filter_callback' );

/**
 * Under Offer status filter
 *
 * @since 2.1.11
 */
function epl_under_offer_label_status_filter_callback() {
	global $epl_settings;
	$epl_settings['label_under_offer'] = ! isset( $epl_settings['label_under_offer'] ) ? '' : $epl_settings['label_under_offer'];
	$under_offer_label                 = 'Under Offer' !== $epl_settings['label_under_offer'] || ! empty( $epl_settings['label_under_offer'] ) ? $epl_settings['label_under_offer'] : __( 'Under Offer', 'easy-property-listings' );
	return $under_offer_label;
}
add_filter( 'epl_under_offer_label_status_filter', 'epl_under_offer_label_status_filter_callback' );

/**
 * Leased status filter
 *
 * @since 2.1.11
 */
function epl_leased_label_status_filter_callback() {
	global $epl_settings;
	$epl_settings['label_leased'] = ! isset( $epl_settings['label_leased'] ) ? '' : $epl_settings['label_leased'];
	$leased_label                 = 'Leased' !== $epl_settings['label_leased'] || ! empty( $epl_settings['label_leased'] ) ? $epl_settings['label_leased'] : __( 'Leased', 'easy-property-listings' );
	return $leased_label;
}
add_filter( 'epl_leased_label_status_filter', 'epl_leased_label_status_filter_callback' );

/**
 * Description: Getting all the values associated with a specific custom post meta key, across all posts
 * Author: Chinmoy Paul
 * Author URL: http://pwdtechnology.com
 *
 * @param string $key Post Meta Key.
 * @param string $type Post Type. Default is post. You can pass custom post type here.
 * @param string $status Post Status like Publish, draft, future etc. default is publish.
 * @param string $property_status Listing status.
 * @return array
 * @since 2.1.11
 */
function epl_get_unique_post_meta_values( $key = '', $type = '', $status = 'publish', $property_status = '' ) {

	global $wpdb;

	if ( empty( $key ) ) {
		return;
	}

	if ( empty( $type ) ) {
		$type = epl_get_core_post_types();
	}

	$type     = (array) $type;
	$type     = array_map( 'sanitize_text_field', $type );
	$type_str = " ( '" . implode( "','", $type ) . "' ) ";

	$query = "
SELECT DISTINCT pm.meta_value FROM {$wpdb->postmeta} pm
LEFT JOIN {$wpdb->posts} p ON p.ID = pm.post_id
LEFT JOIN {$wpdb->postmeta} pm2 ON pm.post_id = pm2.post_id
WHERE pm.meta_key = '%s'
AND p.post_status = '%s'
AND p.post_type IN $type_str
";

	if ( ! empty( $property_status ) ) {
		$property_status = array_map( 'trim', explode( ',', $property_status ) );
		if ( count( $property_status ) ) {
			$query .= "
			AND pm2.meta_key 		= 'property_status'
			AND pm2.meta_value 		IN ('" . implode( "','", $property_status ) . "')";
		}
	}

	$res = $wpdb->get_col( $wpdb->prepare( $query, $key, $status ) ); // phpcs:ignore

	$res = array_filter( $res );

	foreach ( $res as $key => &$elem ) {
		$elem = maybe_unserialize( $elem );
		if ( ! empty( $elem ) && is_array( $elem ) ) {
			foreach ( $elem as $el ) {
				$res[] = $el;
			}
			unset( $res[ $key ] );
		}
	}

	$res = array_filter( $res );

	$results = array();

	foreach ( $res as $s_res ) {
		$results[ $s_res ] = ucwords( $s_res );
	}

	return apply_filters( 'epl_get_unique_post_meta_values', $results, $key, $type );
}

/**
 * Get Sales Count By Date
 *
 * @param int  $day Day number.
 * @param int  $month_num Month number.
 * @param int  $year Year.
 * @param int  $hour Hour.
 * @param int  $status Listing status.
 * @param bool $day_by_day Sales by day.
 * @return int $count Sales
 * @since 3.0
 */
function epl_get_sales_by_date( $day = null, $month_num = null, $year = null, $hour = null, $status = null, $day_by_day = true ) {
	// phpcs:disable WordPress.Security.NonceVerification
	$post_type = isset( $_GET['view'] ) ? sanitize_text_field( wp_unslash( $_GET['view'] ) ) : 'property';
	$args      = array(
		'post_type'              => $post_type,
		'nopaging'               => true,
		'year'                   => $year,
		'fields'                 => 'ids',
		'post_status'            => array( 'publish' ),
		'update_post_meta_cache' => false,
		'update_post_term_cache' => false,
	);

	if ( in_array( $status, array( 'sold', 'leased' ), true ) ) {

			$month_num = is_null( $month_num ) ? 00 : $month_num;
			$day       = is_null( $day ) ? 00 : $day;
			$year      = is_null( $year ) ? 0000 : $year;
			$range     = isset( $_GET['range'] ) ? sanitize_text_field( wp_unslash( $_GET['range'] ) ) : 'other';

			$args['meta_query'] = array( //phpcs:ignore
				array(
					'key'     => 'property_status',
					'value'   => (array) $status,
					'compare' => 'IN',
				),
			);

			if ( in_array( $range, array( 'other', 'last_year', 'this_year', 'last_quarter', 'this_quarter' ), true ) ) {

				$sold_key = 'leased' === $status ? 'property_date_available' : 'property_sold_date';

				$sold_date_end   = date( 'Y-m-d', strtotime( $year . '-' . $month_num . '-' . $day ) );
				$sold_date_start = true === $day_by_day ? $sold_date_end : date( 'Y-m-01', strtotime( $year . '-' . $month_num . '-' . $day ) );

				$args['meta_query'][] = array(
					'key'     => $sold_key,
					'value'   => array( $sold_date_start, $sold_date_end ),
					'type'    => 'DATE',
					'compare' => 'BETWEEN',
				);

			} else {

				$sold_key  = 'leased' === $status ? 'property_date_available' : 'property_sold_date';
				$sold_date = date( 'Y-m-d', strtotime( $year . '-' . $month_num . '-' . $day ) );

				$args['meta_query'][] = array(
					'key'   => $sold_key,
					'value' => $sold_date,
					'type'  => 'DATE',
				);
			}
	} else {

		if ( ! empty( $month_num ) ) {
			$args['monthnum'] = $month_num;
		}

		if ( ! empty( $day ) ) {
			$args['day'] = $day;
		}

		if ( ! empty( $hour ) ) {
			$args['hour'] = $hour;
		}
	}

	$args = apply_filters( 'epl_get_sales_by_date_args', $args );

	$key   = 'epl_stats_' . substr( md5( serialize( $args ) ), 0, 15 ); //phpcs:ignore
	$count = get_transient( $key );
	if ( false === $count ) {
		$sales = new WP_Query( $args );
		$count = (int) $sales->post_count;
		// Cache the results for one hour.
		set_transient( $key, $count, HOUR_IN_SECONDS );
	}

	return $count;
}

/**
 * Month Num To Name
 *
 * Takes a month number and returns the name three letter name of it.
 *
 * @since 3.0
 *
 * @param integer $n Month number.
 * @return string Short month name
 */
function epl_month_num_to_name( $n ) {
	$timestamp = mktime( 0, 0, 0, $n, 1, 2005 );

	return date_i18n( 'M', $timestamp );
}

/**
 * Retrieve contacts from the database
 *
 * @access  public
 *
 * @param array $args Arguments.
 *
 * @return bool|int[]|mixed|WP_Post[]
 * @since   3.0
 */
function epl_get_contacts( $args = array() ) {

	global $wpdb;

	$defaults = array(
		'post_type'      => 'epl_contact',
		'posts_per_page' => 20,
		'offset'         => 0,
		'orderby'        => 'ID',
		'order'          => 'DESC',
	);

	$args = wp_parse_args( $args, $defaults );

	if ( $args['posts_per_page'] < 1 ) {
		$args['number'] = -1;
	}

	$where = ' WHERE 1=1 ';

	// specific contacts.
	if ( ! empty( $args['ID'] ) ) {
		$args['post__in'] = $args['ID'];

	}

	// specific contacts by email.
	if ( ! empty( $args['email'] ) ) {

		$email_query = array(
			'key'   => 'contact_email',
			'value' => $args['email'],
		);

		if ( is_array( $args['email'] ) ) {
			$email_query['comparison'] = 'IN';

		}
		$args['meta_query'][] = $email_query;
	}

	// specific contacts by name.
	if ( ! empty( $args['name'] ) ) {
		$args['post_title'] = $args['name'];
	}

	$cache_key = md5( 'epl_contacts_' . serialize( $args ) ); //phpcs:ignore

	$contacts = wp_cache_get( $cache_key, 'contacts' );

	$args['orderby'] = esc_sql( $args['orderby'] );
	$args['order']   = esc_sql( $args['order'] );

	if ( false === $contacts ) {
		$contacts = get_posts( $args );
		wp_cache_set( $cache_key, $contacts, 'contacts', 3600 );
	}
	return $contacts;
}

/**
 * Search Listing Ajax
 *
 * @access  public
 * @since   3.0
 */
function epl_search_listing() {
	$search_array = array(
		's'           => isset( $_POST['s'] ) ? sanitize_text_field( wp_unslash( $_POST['s'] ) ) : '',
		'showposts'   => 6,
		'post_type'   => epl_get_core_post_types(),
		'post_status' => 'publish',
	);
	$query        = http_build_query( $search_array );
	$listings     = get_posts( $query );
	if ( ! empty( $listings ) ) {
		echo '<ul class="epl-popup-box epl-property-suggestion epl-striped">';
		foreach ( $listings as  $listing ) {
			$status = get_post_meta( $listing->ID, 'property_status', true );
			echo '<li data-id="' . esc_attr( $listing->ID ) . '"><span class="epl-listing-type">' . esc_attr( $listing->post_type ) . '</span>' . esc_attr( $listing->post_title ) . '<span class="epl-listing-status type_' . esc_attr( $status ) . '">' . esc_attr( $status ) . '</span></li>';
		}
		echo '</ul>';
	}
	wp_die();
}
add_action( 'wp_ajax_nopriv_epl_search_listing', 'epl_search_listing' );
add_action( 'wp_ajax_epl_search_listing', 'epl_search_listing' );

/**
 * Get Contact Categories
 *
 * @access  public
 * @since   3.0
 */
function epl_get_contact_categories() {
	return apply_filters(
		'epl_contact_categories',
		array(
			'appraisal'     => __( 'Appraisal', 'easy-property-listings' ),
			'buyer'         => __( 'Buyer', 'easy-property-listings' ),
			'contact'       => __( 'Contact', 'easy-property-listings' ),
			'lead'          => __( 'Lead', 'easy-property-listings' ),
			'landlord'      => __( 'Landlord', 'easy-property-listings' ),
			'new'           => __( 'New', 'easy-property-listings' ),
			'past_customer' => __( 'Past Customer', 'easy-property-listings' ),
			'seller'        => __( 'Seller', 'easy-property-listings' ),
			'tenant'        => __( 'Tenant', 'easy-property-listings' ),
			'widget'        => __( 'Widget Lead', 'easy-property-listings' ),
			'contract'      => __( 'Under Contract', 'easy-property-listings' ),
		)
	);
}

/**
 * Get Contact Labels
 *
 * @param string $category Category label.
 *
 * @access  public
 * @return string
 * @since   3.0
 */
function get_category_label( $category ) {
	foreach ( epl_get_contact_categories() as $key    => $cat ) {
		if ( $key === $category ) {
			return $cat;
		}
	}
	return $category;
}

/**
 * Helper function starts with
 *
 * @param string $haystack String.
 * @param string $needle String.
 *
 * @return bool
 * @since 3.3
 */
function epl_starts_with( $haystack, $needle ) {
	// search backwards starting from haystack length characters from the end.
	return empty( $needle ) || strrpos( $haystack, $needle, -strlen( $haystack ) ) !== false;
}

/**
 * Helper function ends with
 *
 * @param string $haystack String.
 * @param string $needle String.
 *
 * @return bool
 * @since 3.3
 */
function epl_ends_with( $haystack, $needle ) {
	// search forward starting from end minus needle length characters.
	$temp = strlen( $haystack ) - strlen( $needle );
	return empty( $needle ) || ( $temp >= 0 && strpos( $haystack, $needle, $temp ) !== false );
}

/**
 * Parse EPL shortcodes for meta queries
 *
 * @param  array $atts Attributes.
 * @return array
 */
function epl_parse_atts( $atts ) {

	$query = array();

	if ( empty( $atts ) ) {
		return $atts;
	}

	$compare_operators = array(
		'_min'         => '>=',
		'_max'         => '<=',
		'_not_equal'   => '!=',
		'_like'        => 'LIKE',
		'_not_like'    => 'NOT LIKE',
		'_exists'      => 'EXISTS',
		'_not_exists'  => 'NOT EXISTS',
		'_in'          => 'IN',
		'_not_in'      => 'NOT IN',
		'_between'     => 'BETWEEN',
		'_not_between' => 'NOT BETWEEN',
	);

	foreach ( $atts as $key   => &$value ) {

		$this_query = array(
			'compare' => '=',
			'value'   => $value,
		);

		// check for meta.
		if ( epl_starts_with( $key, '_' ) ) {

			$key = preg_replace( '/^_/', '', $key );

			foreach ( $compare_operators as $look_for => $compare_operator ) {

				if ( epl_ends_with( $key, $look_for ) ) {

					$key                   = preg_replace( '/' . $look_for . '$/', '', $key );
					$this_query['compare'] = $compare_operator;

					if ( in_array( $look_for, array( '_in', '_not_in', '_between', '_not_between' ), true ) ) {

						$this_query['value'] =
						array_map( 'trim', explode( ',', $this_query['value'] ) );

						if ( in_array( $look_for, array( '_between', '_not_between' ), true ) ) {

							if( is_numeric( $this_query['value'][0] ) ) {
								$this_query['type'] = 'numeric';
							}
						}
					}

					if ( in_array( $look_for, array( '_exists', '_not_exists' ), true ) ) {
						unset( $this_query['value'] );
					}
				}
			}
			$this_query['key']                       = $key;
			$query['meta_query'][ $key . '_clause' ] = $this_query;
		}
	}
	return isset( $query['meta_query'] ) ? $query['meta_query'] : false;
}

/**
 * Return an array of all extensions with beta support
 *
 * Extensions should be added as 'extension-slug' => 'Extension Name'
 *
 * @return array $extensions The array of extensions
 */
function epl_get_beta_enabled_extensions() {
	return apply_filters( 'epl_beta_enabled_extensions', array() );
}

/**
 * Returns List of countries
 *
 * @since 3.2
 */
function epl_get_countries_list() {

	$countries = array( 'Australia', 'Afghanistan', 'Albania', 'Algeria', 'American Samoa', 'Andorra', 'Angola', 'Anguilla', 'Antarctica', 'Antigua and Barbuda', 'Argentina', 'Armenia', 'Aruba', 'Austria', 'Azerbaijan', 'Bahamas', 'Bahrain', 'Bangladesh', 'Barbados', 'Belarus', 'Belgium', 'Belize', 'Benin', 'Bermuda', 'Bhutan', 'Bolivia', 'Bosnia and Herzegowina', 'Botswana', 'Bouvet Island', 'Brazil', 'British Indian Ocean Territory', 'Brunei Darussalam', 'Bulgaria', 'Burkina Faso', 'Burundi', 'Cambodia', 'Cameroon', 'Canada', 'Cape Verde', 'Cayman Islands', 'Central African Republic', 'Chad', 'Chile', 'China', 'Christmas Island', 'Cocos (Keeling) Islands', 'Colombia', 'Comoros', 'Congo', 'Congo, the Democratic Republic of the', 'Cook Islands', 'Costa Rica', "Cote d'Ivoire", 'Croatia (Hrvatska)', 'Cuba', 'Cyprus', 'Czech Republic', 'Denmark', 'Djibouti', 'Dominica', 'Dominican Republic', 'East Timor', 'Ecuador', 'Egypt', 'El Salvador', 'Equatorial Guinea', 'Eritrea', 'Estonia', 'Ethiopia', 'Falkland Islands (Malvinas)', 'Faroe Islands', 'Fiji', 'Finland', 'France', 'France Metropolitan', 'French Guiana', 'French Polynesia', 'French Southern Territories', 'Gabon', 'Gambia', 'Georgia', 'Germany', 'Ghana', 'Gibraltar', 'Greece', 'Greenland', 'Grenada', 'Guadeloupe', 'Guam', 'Guatemala', 'Guinea', 'Guinea-Bissau', 'Guyana', 'Haiti', 'Heard and Mc Donald Islands', 'Holy See (Vatican City State)', 'Honduras', 'Hong Kong', 'Hungary', 'Iceland', 'India', 'Indonesia', 'Iran (Islamic Republic of)', 'Iraq', 'Ireland', 'Israel', 'Italy', 'Jamaica', 'Japan', 'Jordan', 'Kazakhstan', 'Kenya', 'Kiribati', "Korea, Democratic People's Republic of", 'Korea, Republic of', 'Kuwait', 'Kyrgyzstan', "Lao, People's Democratic Republic", 'Latvia', 'Lebanon', 'Lesotho', 'Liberia', 'Libyan Arab Jamahiriya', 'Liechtenstein', 'Lithuania', 'Luxembourg', 'Macau', 'Macedonia, The Former Yugoslav Republic of', 'Madagascar', 'Malawi', 'Malaysia', 'Maldives', 'Mali', 'Malta', 'Marshall Islands', 'Martinique', 'Mauritania', 'Mauritius', 'Mayotte', 'Mexico', 'Micronesia, Federated States of', 'Moldova, Republic of', 'Monaco', 'Mongolia', 'Montserrat', 'Morocco', 'Mozambique', 'Myanmar', 'Namibia', 'Nauru', 'Nepal', 'Netherlands', 'Netherlands Antilles', 'New Caledonia', 'New Zealand', 'Nicaragua', 'Niger', 'Nigeria', 'Niue', 'Norfolk Island', 'Northern Mariana Islands', 'Norway', 'Oman', 'Pakistan', 'Palau', 'Panama', 'Papua New Guinea', 'Paraguay', 'Peru', 'Philippines', 'Pitcairn', 'Poland', 'Portugal', 'Puerto Rico', 'Qatar', 'Reunion', 'Romania', 'Russian Federation', 'Rwanda', 'Saint Kitts and Nevis', 'Saint Lucia', 'Saint Vincent and the Grenadines', 'Samoa', 'San Marino', 'Sao Tome and Principe', 'Saudi Arabia', 'Senegal', 'Seychelles', 'Sierra Leone', 'Singapore', 'Slovakia (Slovak Republic)', 'Slovenia', 'Solomon Islands', 'Somalia', 'South Africa', 'South Georgia and the South Sandwich Islands', 'Spain', 'Sri Lanka', 'St. Helena', 'St. Pierre and Miquelon', 'Sudan', 'Suriname', 'Svalbard and Jan Mayen Islands', 'Swaziland', 'Sweden', 'Switzerland', 'Syrian Arab Republic', 'Taiwan, Province of China', 'Tajikistan', 'Tanzania, United Republic of', 'Thailand', 'Togo', 'Tokelau', 'Tonga', 'Trinidad and Tobago', 'Tunisia', 'Turkey', 'Turkmenistan', 'Turks and Caicos Islands', 'Tuvalu', 'Uganda', 'Ukraine', 'United Arab Emirates', 'United Kingdom', 'United States', 'United States Minor Outlying Islands', 'Uruguay', 'Uzbekistan', 'Vanuatu', 'Venezuela', 'Vietnam', 'Virgin Islands (British)', 'Virgin Islands (U.S.)', 'Wallis and Futuna Islands', 'Western Sahara', 'Yemen', 'Yugoslavia', 'Zambia', 'Zimbabwe' );

	return apply_filters( 'epl_get_countries_list', array_combine( $countries, $countries ) );
}

/**
 * Returns array containing Property Status
 *
 * @since 3.2
 */
function epl_get_property_status_opts() {

	return apply_filters(
		'epl_opts_property_status_filter',
		array(
			'current'   => __( 'Current', 'easy-property-listings' ),
			'withdrawn' => __( 'Withdrawn', 'easy-property-listings' ),
			'offmarket' => __( 'Off Market', 'easy-property-listings' ),
			'sold'      => array(
				'label'   => apply_filters( 'epl_sold_label_status_filter', __( 'Sold', 'easy-property-listings' ) ),
				'exclude' => array( 'rental' ),
			),
			'leased'    => array(
				'label'   => apply_filters( 'epl_leased_label_status_filter', __( 'Leased', 'easy-property-listings' ) ),
				'include' => array( 'rental', 'commercial', 'commercial_land', 'business' ),
			),
		)
	);
}

/**
 * Returns array containing Property authority
 *
 * @since 3.2
 */
function epl_get_property_authority_opts() {

	return apply_filters(
		'epl_property_authority_filter',
		array(
			'exclusive'     => __( 'Exclusive', 'easy-property-listings' ),
			'auction'       => __( 'Auction', 'easy-property-listings' ),
			'multilist'     => __( 'Multilist', 'easy-property-listings' ),
			'conjunctional' => __( 'Conjunctional', 'easy-property-listings' ),
			'open'          => __( 'Open', 'easy-property-listings' ),
			'sale'          => __( 'Sale', 'easy-property-listings' ),
			'setsale'       => __( 'Set Sale', 'easy-property-listings' ),
		)
	);
}

/**
 * Returns array containing Property exclusivity
 *
 * @since 3.2
 */
function epl_get_property_exclusivity_opts() {

	return apply_filters(
		'epl_opts_property_exclusivity_filter',
		array(
			'exclusive' => __( 'Exclusive', 'easy-property-listings' ),
			'open'      => __( 'Open', 'easy-property-listings' ),
		)
	);
}

/**
 * Returns array containing Commercial Authority
 *
 * @since 3.2
 */
function epl_get_property_com_authority_opts() {

	return apply_filters(
		'epl_opts_property_com_authority_filter',
		array(
			'Forsale' => __( 'For Sale', 'easy-property-listings' ),
			'auction' => __( 'Auction', 'easy-property-listings' ),
			'tender'  => __( 'Tender', 'easy-property-listings' ),
			'eoi'     => __( 'EOI', 'easy-property-listings' ),
			'Sale'    => __( 'Sale', 'easy-property-listings' ),
			'offers'  => __( 'Offers', 'easy-property-listings' ),
		)
	);
}

/**
 * Returns array containing area unit options
 *
 * @since 3.2
 */
function epl_get_property_area_unit_opts() {

	return apply_filters(
		'epl_opts_area_unit_filter',
		array(
			'square'      => __( 'Square', 'easy-property-listings' ),
			'squareMeter' => __( 'Square Meter', 'easy-property-listings' ),
			'acre'        => __( 'Acre', 'easy-property-listings' ),
			'hectare'     => __( 'Hectare', 'easy-property-listings' ),
			'sqft'        => __( 'Square Feet', 'easy-property-listings' ),
		)
	);
}

/**
 * Returns array containing Rental period
 *
 * @since 3.2
 */
function epl_get_property_rent_period_opts() {

	return apply_filters(
		'epl_opts_rent_period_filter',
		array(
			'day'     => __( 'Day', 'easy-property-listings' ),
			'daily'   => __( 'Daily', 'easy-property-listings' ),
			'week'    => __( 'Week', 'easy-property-listings' ),
			'weekly'  => __( 'Weekly', 'easy-property-listings' ),
			'month'   => __( 'Month', 'easy-property-listings' ),
			'monthly' => __( 'Monthly', 'easy-property-listings' ),
		)
	);
}

/**
 * Returns array containing Commercial Leasing Types
 *
 * @since 3.2
 */
function epl_get_property_com_listing_type_opts() {

	return apply_filters(
		'epl_opts_property_com_listing_type_filter',
		array(
			'sale'  => __( 'Sale', 'easy-property-listings' ),
			'lease' => __( 'Lease', 'easy-property-listings' ),
			'both'  => __( 'Both', 'easy-property-listings' ),
		)
	);
}

/**
 * Returns array containing Commercial Tenancy
 *
 * @since 3.2
 */
function epl_get_property_com_tenancy_opts() {

	return apply_filters(
		'epl_opts_property_com_tenancy_filter',
		array(
			'unknown'  => __( 'Unknown', 'easy-property-listings' ),
			'vacant'   => __( 'Vacant', 'easy-property-listings' ),
			'tenanted' => __( 'Tenanted', 'easy-property-listings' ),
		)
	);
}

/**
 * Returns array containing Commercial Land Extent
 *
 * @since 3.2
 */
function epl_get_property_com_property_extent_opts() {

	return apply_filters(
		'epl_opts_property_com_property_extent_filter',
		array(
			'whole' => __( 'Whole', 'easy-property-listings' ),
			'part'  => __( 'Part', 'easy-property-listings' ),
		)
	);
}

/**
 * Get author id from name
 *
 * @param string $author Author name.
 *
 * @return int
 * @since 3.1.1
 */
function epl_get_author_id_from_name( $author ) {
	if ( is_numeric( $author ) ) {
		return absint( $author );
	} else {
		$user = get_user_by( 'login', $author );
		return $user->ID;
	}
}

/**
 * WP All Import Pro filter that processes property_price_global after updating or saving a post
 *
 * @param string $id Post ID.
 * @since 3.3
 */
function epl_pmxi_import_post_saved( $id ) {

	$post = get_post( $id );

	if( is_null( $post ) ){
		return;
	}

	if ( is_epl_post( $post->post_type ) ) {

		if ( 'rental' === $post->post_type ) {
			$price = get_post_meta( $id, 'property_rent', true );
			update_post_meta( $id, 'property_price_global', $price );
		} elseif ( 'commercial' === $post->post_type ) {
			$price = get_post_meta( $id, 'property_price', true );
			if ( empty( $price ) ) {
				$price = get_post_meta( $id, 'property_com_rent', true );
			}
			update_post_meta( $id, 'property_price_global', $price );
		} else {
			$price = get_post_meta( $id, 'property_price', true );
			update_post_meta( $id, 'property_price_global', $price );
		}
	}
}
add_action( 'pmxi_saved_post', 'epl_pmxi_import_post_saved', 10, 1 );

/**
 * Load functions from child theme folder if present
 *
 * @since 3.3
 */
function epl_single_and_archive_functions() {

	if ( is_epl_post_single() && file_exists( get_stylesheet_directory() . '/easypropertylistings/functions-single.php' ) ) {
		include_once get_stylesheet_directory() . '/easypropertylistings/functions-single.php';
	}

	if ( is_epl_post_archive() && file_exists( get_stylesheet_directory() . '/easypropertylistings/functions-archive.php' ) ) {
		include_once get_stylesheet_directory() . '/easypropertylistings/functions-archive.php';
	}
}
add_action( 'wp', 'epl_single_and_archive_functions', 99 );

/**
 * Recursive array map for multi dimensional array
 *
 * @param string $callback Callback.
 * @param array  $array Array.
 *
 * @return array
 * @since 3.3.5
 */
function epl_array_map_recursive( $callback, $array ) {
	$func = function ( $item ) use ( &$func, &$callback ) { //phpcs:ignore
		return is_array( $item ) ? array_map( $func, $item ) : call_user_func( $callback, $item );
	};
	return array_map( $func, $array );
}

