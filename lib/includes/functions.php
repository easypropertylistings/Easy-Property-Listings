<?php
/**
 * Front End Functions
 *
 * @package     EPL
 * @subpackage  Functions/FrontEnd
 * @copyright   Copyright (c) 2014, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Get an option
 *
 * Looks to see if the specified setting exists, returns default if not
 *
 * @since 2.2
 * @return mixed
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
	if(function_exists('et_divi_fonts_url')) {
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
	if(function_exists('it_classes_load')) {
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
	if(function_exists('genesis_constants')) {
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
	foreach( get_intermediate_image_sizes() as $s ) {
		$sizes[ $s ] = array( 0, 0 );
		if( in_array( $s, array( 'thumbnail', 'medium', 'large' ) ) ) {
			$sizes[ $s ][0] = get_option( $s . '_size_w' );
			$sizes[ $s ][1] = get_option( $s . '_size_h' );
		} else {
			if( isset( $_wp_additional_image_sizes ) && isset( $_wp_additional_image_sizes[ $s ] ) ) {
				$sizes[ $s ] = array( $_wp_additional_image_sizes[ $s ]['width'], $_wp_additional_image_sizes[ $s ]['height'], );
			}
		}
	}
	return $sizes;
}

/**
 * Remote get function
 *
 * @since 1.0
 */
function epl_remote_url_get($url) {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$response = curl_exec($ch);
	$response = json_decode($response);
	if(!empty($response)) {
		if($response->status == 'OK') {
			return $response->results;
		}
	}
}

/**
 * Register post type to EPL and wordpress
 *
 * @since 1.0
 */
function epl_register_post_type($post_type='', $post_type_label, $args=array()) {
	if(empty($post_type)) {
		return;
	}

	global $epl_settings;
	if(!empty($epl_settings) && isset($epl_settings['activate_post_types'])) {
		$epl_activate_post_types = $epl_settings['activate_post_types'];
		if(!empty($epl_activate_post_types)) {
			if( in_array($post_type, $epl_activate_post_types) ) {
				global $epl_active_post_types;
				if(!isset($epl_active_post_types)) {
					$epl_active_post_types = array();
				}
				$epl_active_post_types[ $post_type ] = $post_type_label;
				register_post_type( $post_type, $args );
			}
		}
	}

	if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'epl_settings') {
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
	if(is_null($epl_active_post_types))
		$epl_active_post_types = array();
	return $epl_active_post_types;
}

/**
 * Return all the post types doesn't matter if selected as active or not by admin
 *
 * @since 1.0
 */
function epl_get_post_types() {
	$epl_post_types = array(
		'property'		=>	__('Property (Residential)', 'easy-property-listings' ),
		'land'			=>	__('Land', 'easy-property-listings' ),
		'rental'		=>	__('Rental', 'easy-property-listings' ),
		'rural'			=>	__('Rural', 'easy-property-listings' ),
		'commercial'		=>	__('Commercial', 'easy-property-listings' ),
		'commercial_land'	=>	__('Commercial Land', 'easy-property-listings' ),
		'business'		=>	__('Business', 'easy-property-listings' ),
	);
	// allow 3rd party extensions to add custom posts as a part of epl
	return apply_filters('epl_post_types',$epl_post_types);
}

/**
 * Get Currencies
 *
 * @since 1.0
 * @return array $currencies A list of the available currencies
 */
function epl_get_currencies() {
	$currencies = array(
		'AUD'  => __( 'Australian Dollars (&#36;)', 'easy-property-listings'  ),
		'USD'  => __( 'US Dollars (&#36;)', 'easy-property-listings'  ),
		'EUR'  => __( 'Euros (&euro;)', 'easy-property-listings'  ),
		'GBP'  => __( 'Pounds Sterling (&pound;)', 'easy-property-listings'  ),
		'BRL'  => __( 'Brazilian Real (R&#36;)', 'easy-property-listings'  ),
		'CAD'  => __( 'Canadian Dollars (&#36;)', 'easy-property-listings'  ),
		'CZK'  => __( 'Czech Koruna', 'easy-property-listings'  ),
		'DKK'  => __( 'Danish Krone', 'easy-property-listings'  ),
		'HKD'  => __( 'Hong Kong Dollar (&#36;)', 'easy-property-listings'  ),
		'HUF'  => __( 'Hungarian Forint', 'easy-property-listings'  ),
		'INR'  => __( 'Indian Rupee (&#8377;)', 'easy-property-listings'  ),
		'RIAL' => __( 'Iranian Rial (&#65020;)', 'easy-property-listings'  ),
		'ILS'  => __( 'Israeli Shekel (&#8362;)', 'easy-property-listings'  ),
		'JPY'  => __( 'Japanese Yen (&yen;)', 'easy-property-listings'  ),
		'MYR'  => __( 'Malaysian Ringgits', 'easy-property-listings'  ),
		'MXN'  => __( 'Mexican Peso (&#36;)', 'easy-property-listings'  ),
		'NZD'  => __( 'New Zealand Dollar (&#36;)', 'easy-property-listings'  ),
		'NOK'  => __( 'Norwegian Krone', 'easy-property-listings'  ),
		'PHP'  => __( 'Philippine Pesos', 'easy-property-listings'  ),
		'PLN'  => __( 'Polish Zloty', 'easy-property-listings'  ),
		'QAR'  => __( 'Qatar Riyal (QAR)', 'easy-property-listings'  ),
		'SGD'  => __( 'Singapore Dollar (&#36;)', 'easy-property-listings'  ),
		'ZAR'  => __( 'South African Rand (R)', 'easy-property-listings'  ),
		'SEK'  => __( 'Swedish Krona', 'easy-property-listings'  ),
		'CHF'  => __( 'Swiss Franc', 'easy-property-listings'  ),
		'TWD'  => __( 'Taiwan New Dollars', 'easy-property-listings'  ),
		'THB'  => __( 'Thai Baht (&#3647;)', 'easy-property-listings'  ),
		'TRY'  => __( 'Turkish Lira (&#8378;)', 'easy-property-listings'  ),
		'RUB'  => __( 'Russian Rubles', 'easy-property-listings'  ),
		'AED'  => __( 'United Arab Emirates, Dirham (AED)', 'easy-property-listings'  ),
		'UAH'  => __( 'Ukrainian Hryvnia (&#8372;)', 'easy-property-listings'  ),
		'VND'  => __( 'Vietnamese đồng (&#8363;)', 'easy-property-listings'  )
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
	if(!empty($epl_settings) && isset($epl_settings['currency'])) {
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
	if(!empty($epl_settings) && isset($epl_settings['currency_position'])) {
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
	if(!empty($epl_settings) && isset($epl_settings['currency_thousands_separator'])) {
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
	if(!empty($epl_settings) && isset($epl_settings['currency_decimal_separator'])) {
		$epl_decimal_separator = $epl_settings['currency_decimal_separator'];
	}
	return apply_filters( 'epl_decimal_separator', $epl_decimal_separator );
}

/**
 * Get the currency formatted amount
 *
 * @since 1.0
 * @param integer $price
 * @return string Currency Formatted price
 */
function epl_currency_formatted_amount( $price ) {
	$price_format 			= apply_filters('epl_price_number_format','number');
	$price_format_com_lease 	= apply_filters('epl_price_number_format_commercial_lease','number');

	if($price_format == 'decimal' || $price_format_com_lease == 'decimal' )
		return epl_currency_filter( epl_format_amount( $price , true ) );
	else
		return epl_currency_filter( epl_format_amount( $price , false ) );

}

/**
 * Get the currency formatted amount
 *
 * @since 2.2
 * @param integer $key Settings meta key
 * @return string
 */
function epl_labels( $key ) {
	global $epl_settings;
	$field_groups = epl_get_admin_option_fields();
	$epl_labels = array();
	foreach($field_groups as $field_group) {
		if($field_group['id']	==	'labels' || $field_group['id']	==	'address') {
			$epl_labels = array_merge ( $epl_labels, array_filter($field_group['fields']) );

		}
	}
	foreach($epl_labels as $label_key	=>	$label) {

		if( isset($label['default']) && $key == $label['name'] ) {

			$label =  isset($epl_settings[$key]) ? $epl_settings[$key] : $label['default'];

			return apply_filters( 'epl_display_'.$key, $label );
		}
	}
}

/**
 * @depricated since 2.2. use epl_labels instead
 */
function epl_display_label_suburb( ) {
	$epl_display_label_suburb = '';

	global $epl_settings;
	if(!empty($epl_settings) && isset($epl_settings['label_suburb'])) {
		$epl_display_label_suburb = $epl_settings['label_suburb'];
	}
	return apply_filters( 'epl_display_label_suburb', $epl_display_label_suburb );
}

/**
 * @depricated since 2.2. use epl_labels instead
 */
function epl_display_label_bond( ) {
	$epl_display_label_bond = '';

	global $epl_settings;
	if(!empty($epl_settings) && isset($epl_settings['label_bond'])) {
		$epl_display_label_bond = $epl_settings['label_bond'];
	}
	return apply_filters( 'epl_display_label_bond', $epl_display_label_bond );
}

/**
 * @depricated since 2.2. use epl_labels instead
 */
function epl_display_label_postcode() {
	$epl_display_label_postcode = '';

	global $epl_settings;
	if(!empty($epl_settings) && isset($epl_settings['label_postcode'])) {
		$epl_display_label_postcode = $epl_settings['label_postcode'];
	}
	return apply_filters( 'epl_display_label_postcode', $epl_display_label_postcode );
}

/**
 * Print EPL property address
 *
 * @since 1.0
 * @param integer $post_ID
 * @return the string for address
 */
function epl_the_property_address( $post_ID = '' ) {
	$address = epl_get_property_address($post_ID);
	echo apply_filters('epl_the_property_address_filter', $address);
}

/**
 * Get EPL property meta data based on post id
 *
 * @since 1.0
 * @param integer $post_ID
 * @param string $meta_key
 * @return the string/list for values
 */
function epl_get_property_meta( $post_ID = '' , $meta_key = '' ) {
	if($post_ID == '') {
		$post_ID = get_the_ID();
	}

	if(!empty($meta_key)) {
		$meta_value = trim(get_post_meta($post_ID, $meta_key, true));
	} else {
		$meta_value = get_post_meta($post_ID, $meta_key);
	}
	return apply_filters('epl_get_property_meta_filter', $meta_value);
}

/**
 * Print EPL property meta data
 *
 * @since 1.0
 * @param integer $post_ID
 * @param string $meta_key
 * @return the string/list for values
 */
function epl_the_property_meta( $post_ID = '' , $meta_key ) {
	$meta_value = epl_get_property_meta($post_ID, $meta_key);
	echo apply_filters('epl_the_property_meta_filter', $meta_value);
}

/**
 * Taxonomy Location Label
 *
 * @since 1.0
 * @return all the settings in array
 */
function epl_tax_location_label() {
	$label_location = '';
	global $epl_settings;
	if(!empty($epl_settings) && isset($epl_settings['label_location'])) {
		$label_location = trim($epl_settings['label_location']);
	}
	if(empty($label_location)) {
		$label_location = 'City';
	}
	return $label_location;
}

/**
 * Location Label
 *
 * @since 1.0
 * @return all the settings in array
 */
function epl_meta_location_label() {
	$label_location = '';
	global $epl_settings;
	if(!empty($epl_settings) && isset($epl_settings['label_suburb'])) {
		$label_location = trim($epl_settings['label_suburb']);
	}
	if(empty($label_location)) {
		$label_location = __('City' , 'easy-property-listings' );
	}
	return $label_location;
}

/**
 * Under Offer Label
 *
 * @since 2.1
 * @return label
 */
function epl_meta_under_offer_label() {
	$under_offer = '';
	global $epl_settings;
	if(!empty($epl_settings) && isset($epl_settings['label_under_offer'])) {
		$under_offer = trim($epl_settings['label_under_offer']);
	}
	if(empty($under_offer)) {
		$under_offer = __('Under Offer' , 'easy-property-listings' );
	}
	return $under_offer;
}

/**
 * House Categories Options
 *
 * @since 1.1
 * @return all the categories in array
 */
function epl_listing_load_meta_property_category() {
	$defaults = array(
		'House'				=>	__('House', 'easy-property-listings' ),
		'Unit'				=>	__('Unit', 'easy-property-listings' ),
		'Townhouse'			=>	__('Townhouse', 'easy-property-listings' ),
		'Villa'				=>	__('Villa', 'easy-property-listings' ),
		'Apartment'			=>	__('Apartment', 'easy-property-listings' ),
		'Flat'				=>	__('Flat', 'easy-property-listings' ),
		'Studio'			=>	__('Studio', 'easy-property-listings' ),
		'Warehouse'			=>	__('Warehouse', 'easy-property-listings' ),
		'DuplexSemi-detached'		=>	__('Duplex Semi-detached', 'easy-property-listings' ),
		'Alpine'			=>	__('Alpine', 'easy-property-listings' ),
		'AcreageSemi-rural'		=>	__('Acreage Semi-rural', 'easy-property-listings' ),
		'Retirement'			=>	__('Retirement', 'easy-property-listings' ),
		'BlockOfUnits'			=>	__('Block Of Units', 'easy-property-listings' ),
		'Terrace'			=>	__('Terrace', 'easy-property-listings' ),
		'ServicedApartment'		=>	__('Serviced Apartment', 'easy-property-listings' ),
		'Other'				=>	__('Other', 'easy-property-listings' )
	);
	return apply_filters( 'epl_listing_meta_property_category', $defaults );
}

/**
 * Custom Meta: Return Value of House Category
 *
 * @since 1.1
 * @param string $key
 * @return all the categories in array
 */
function epl_listing_meta_property_category_value( $key ) {
	$array = epl_listing_load_meta_property_category();
	$value = array_key_exists( $key , $array ) && !empty( $array[$key] )  ? $array[$key] : '';

	return $value;
}

/**
 * Custom Meta: Land Categories
 *
 * @since 1.1
 * @return all the categories in array
 */
function epl_listing_load_meta_land_category() {
	$defaults = array(
		'Commercial'			=>	__('Commercial', 'easy-property-listings' ),
		'Residential'			=>	__('Residential', 'easy-property-listings' )
	);
	return apply_filters( 'epl_listing_meta_land_category', $defaults );
}

/**
 * Custom Meta: Return Value of Land Category
 *
 * @since 1.1
 * @param string $key
 * @return all the categories in array
 */
function epl_listing_meta_land_category_value( $key ) {
	$array = epl_listing_load_meta_land_category();
	$value = array_key_exists( $key , $array ) && !empty( $array[$key] )  ? $array[$key] : '';

	return $value;
}

/**
 * Custom Meta: Commercial Categories
 *
 * @since 1.1
 * @return all the categories in array
 */
function epl_listing_load_meta_commercial_category() {
	$defaults = array(
		'Commercial Farming'		=>	__('Commercial Farming', 'easy-property-listings' ),
		'Land/Development'		=>	__('Land/Development', 'easy-property-listings' ),
		'Hotel/Leisure'			=>	__('Hotel/Leisure', 'easy-property-listings' ),
		'Industrial/Warehouse'		=>	__('Industrial/Warehouse', 'easy-property-listings' ),
		'Medical/Consulting'		=>	__('Medical/Consulting', 'easy-property-listings' ),
		'Offices'			=>	__('Offices', 'easy-property-listings' ),
		'Retail'			=>	__('Retail', 'easy-property-listings' ),
		'Showrooms/Bulky Goods'		=>	__('Showrooms/Bulky Goods', 'easy-property-listings' ),
		'Other'				=>	__('Other', 'easy-property-listings' )
	);
	return apply_filters( 'epl_listing_meta_commercial_category', $defaults );
}

/**
 * Custom Meta: Return Value of Commercial Category
 *
 * @since 1.1
 * @param string $key
 * @return all the categories in array
 */
function epl_listing_load_meta_commercial_category_value( $key ) {
	$array = epl_listing_load_meta_commercial_category();

	if( is_array($key) ) {
		$values = array();
		foreach($key as $k) {
			$values[] = array_key_exists( $k , $array ) && !empty( $array[$k] )  ? $array[$k] : ucfirst($k);
		}
		$value = implode(', ',$values);
	} else {
		$value = array_key_exists( $key , $array ) && !empty( $array[$key] )  ? $array[$key] : ucfirst($key);
	}


	return apply_filters('epl_meta_commercial_category_value',$value,$key,$array);
}

/**
 * Custom Meta: Commercial Rental Period
 *
 * @since 2.1
 * @return all the categories in array
 */
function epl_listing_load_meta_commercial_rent_period() {
	$defaults = array(
		'annual'		=>	__('P.A.', 'easy-property-listings' ),
		'nnn'			=>	__('NNN', 'easy-property-listings' ),
		'full-service'		=>	__('Full Service', 'easy-property-listings' ),
		'gross-lease-rates'	=>	__('Gross Lease Rates', 'easy-property-listings' )
	);
	return apply_filters( 'epl_listing_meta_commercial_rent_period', $defaults );
}

/**
 * Custom Meta: Return Value of Commercial Rental Period
 *
 * @since 2.1
 * @param string $key
 * @return all the categories in array
 */
function epl_listing_load_meta_commercial_rent_period_value( $key ) {
	$array = epl_listing_load_meta_commercial_rent_period();
	$value = array_key_exists( $key , $array ) && !empty( $array[$key] )  ? $array[$key] : '';

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
		'Cropping'			=>	__('Cropping', 'easy-property-listings' ),
		'Dairy'				=>	__('Dairy', 'easy-property-listings' ),
		'Farmlet'			=>	__('Farmlet', 'easy-property-listings' ),
		'Horticulture'			=>	__('Horticulture', 'easy-property-listings' ),
		'Livestock'			=>	__('Livestock', 'easy-property-listings' ),
		'Viticulture'			=>	__('Viticulture', 'easy-property-listings' ),
		'MixedFarming'			=>	__('Mixed Farming', 'easy-property-listings' ),
		'Lifestyle'			=>	__('Lifestyle', 'easy-property-listings' ),
		'Other'				=>	__('Other', 'easy-property-listings' )
	);
	return apply_filters( 'epl_listing_meta_rural_category', $defaults );
}

/**
 * Custom Meta: Return Value of Rural Category
 *
 * @since 1.1
 * @param string $key
 * @return all the categories in array
 */
function epl_listing_load_meta_rural_category_value( $key ) {
	$array = epl_listing_load_meta_rural_category();
	$value = array_key_exists( $key , $array ) && !empty( $array[$key] )  ? $array[$key] : '';

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
 * @param string $date
 * @return formatted date
 */
function epl_feedsync_format_date( $date ) {
    $date_example = '2014-07-22-16:45:56';

    $tempdate = explode('-',$date);
	$date = $tempdate[0].'-'.$tempdate[1].'-'.$tempdate[2].' '.$tempdate[3];
    return  date("Y-m-d H:i:s",strtotime($date));
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
 * @param string $sub_value
 * @return formatted sub number/
 */
function epl_feedsync_format_sub_number( $sub_value ) {
	if ( $sub_value ) {
		$sub_value = $sub_value . '/';
		return $sub_value;
	}
	return;
}

/**
 * REAXML convert currency to integer
 *
 * Processing Function for WP All Import and FeedSync
 *
 * @since 3.0
 * @return integer
 */
function epl_feedsync_format_strip_currency( $value ) {
	if ( $value ) {
		$value = preg_replace( apply_filters( 'epl_feedsync_format_strip_currency_symbol' , '/[\$,]/' ) , '', $value);
		$value = floatval($value);
		return $value;
	}
	return;
}


/**
 * Offers presented on settings page, removed if extension is present and activated
 *
 * @since 2.0
 */
function epl_admin_sidebar () {

	if ( has_filter( 'epl_extensions_options_filter_new' ) )
		return;

	$service_banners = array(
		array(
			'url' => 'http://easypropertylistings.com.au/extensions/developer-license/',
			'img' => 'bannertwo.png',
			'alt' => __('Developer bundle Prospector for Easy Property Listings', 'easy-property-listings' )
		),
		/*
		* array(
		*	'url' => 'http://easypropertylistings.com.au/extensions/prospector-license/',
		*	'img' => 'bannerone.png',
		*	'alt' => __('Prospector pack for Easy Property Listings', 'easy-property-listings' )
		*),
		*/
	);
	$i = 0;
	foreach ( $service_banners as $banner ) {
		echo '<a target="_blank" href="' . esc_url( $banner['url'] ) . '"><img width="261" src="' .plugins_url( 'lib/assets/images/' . $banner['img'], EPL_PLUGIN_FILE ) .'" alt="' . esc_attr( $banner['alt'] ) . '"/></a><br/><br/>';
		$i ++;
	}
}

/**
 * Renders field array to html
 *
 * @since 2.1
 * @param array $field
 * @param string $val
 */
function epl_render_html_fields ( $field = array() , $val = '' ) {
 	global $post;
 	switch($field['type']) {
		case 'select':
			$dependency = 'false';
			if(isset($field['opt_args']) && !empty($field['opt_args'])) {
				if( isset($field['opt_args']['type']) ) {
					switch($field['opt_args']['type']) {
						case 'taxonomy':
							$terms = get_terms(
								$field['opt_args']['slug'],
								array(
									'hide_empty'	=>	0,
									'parent'	=>	0
								)
							);

							if(!isset($field['opt_args']['parent']) || $field['opt_args']['parent'] == '') {
								$var = sanitize_title( $field['opt_args']['slug'] );
								$var = 'var_'.str_replace("-", "_", $var);

								if(!isset($$var)) {
									$$var = array();
									if ( !empty($terms) && !is_wp_error($terms) ) {
										$arr = array('' => '');
										foreach ( $terms as $term ) {
											$arr[$term->term_id] = $term->name;
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
			if($dependency == 'true') {
				$field_atts = 'data-dependency="true" data-type="taxonomy" data-type-name="'.$field['opt_args']['slug'].'" data-parent="'.$field['opt_args']['parent'].'" data-default="'.$val.'"';
			}

			echo '<select name="'.$field['name'].'" id="'.$field['name'].'" '.$field_atts.' class="dependency-'.$dependency.'">';
				if(!empty($field['default'])) {
					//echo '<option value="" selected="selected">'.__($field['default'], 'easy-property-listings' ).'</option>';
				}

				if(isset($field['opts']) && !empty($field['opts'])) {
					foreach($field['opts'] as $k=>$v) {
						$selected = '';
						if($val == $k) {
							$selected = 'selected="selected"';
						}

						if(is_array($v)) {
							if(isset($v['exclude']) && !empty($v['exclude'])) {
								if( in_array($post->post_type, $v['exclude']) ) {
									continue;
								}
							}

							if(isset($v['include']) && !empty($v['include'])) {
								if( !in_array($post->post_type, $v['include']) ) {
									continue;
								}
							}
							$v = $v['label'];
						}

						echo '<option value="'.$k.'" '.$selected.'>'.__($v, 'easy-property-listings' ).'</option>';
					}
				} else {
					echo '<option value=""> </option>';
				}
			echo '</select>';
			break;
		case 'select_multiple':

			$val = (array) $val;
			echo '<select multiple name="'.$field['name'].'[]" id="'.$field['name'].'" >';

				if(isset($field['opts']) && !empty($field['opts'])) {
					foreach($field['opts'] as $k=>$v) {
						$selected = '';
						if( in_array($k,$val)) {
							$selected = 'selected="selected"';
						}

						if(is_array($v)) {
							if(isset($v['exclude']) && !empty($v['exclude'])) {
								if( in_array($post->post_type, $v['exclude']) ) {
									continue;
								}
							}

							if(isset($v['include']) && !empty($v['include'])) {
								if( !in_array($post->post_type, $v['include']) ) {
									continue;
								}
							}
							$v = $v['label'];
						}

						echo '<option value="'.$k.'" '.$selected.'>'.__($v, 'easy-property-listings' ).'</option>';
					}
				} else {
					echo '<option value=""> </option>';
				}
			echo '</select>';
		break;

		case 'checkbox':
			if(!empty($field['opts'])) {
				foreach($field['opts'] as $k=>$v) {
					$checked = '';
					if(!empty($val)) {

						$val = (array) $val;
						if( in_array($k, $val) ) {
							$checked = 'checked="checked"';
						}
					}
					echo '<span class="epl-field-row"><input type="checkbox" name="'.$field['name'].'[]" id="'.$field['name'].'_'.$k.'" value="'.$k.'" '.$checked.' /> <label for="'.$field['name'].'_'.$k.'">'.__($v, 'easy-property-listings' ).'</label></span>';
				}
			}
			break;

		case 'checkbox_single':

			if(!empty($field['opts'])) {
				foreach($field['opts'] as $k=>$v) {
					$checked = '';
					if(!empty($val)) {
						$checkbox_single_options = apply_filters('epl_checkbox_single_check_options', array(1,'yes','on','true'));
						if( $k == $val || in_array($val,$checkbox_single_options) ) {
							$checked = 'checked="checked"';
						}
					}
					if(count($field['opts']) == 1)
						$v = $field['label'];
					echo '<span class="epl-field-row"><input type="checkbox" name="'.$field['name'].'" id="'.$field['name'].'_'.$k.'" value="'.$k.'" '.$checked.' /> <label for="'.$field['name'].'_'.$k.'">'.__($v, 'easy-property-listings' ).'</label></span>';
				}
			}
			break;

		case 'checkbox_option':
			if(!empty($field['opts'])) {
				foreach($field['opts'] as $k=>$v) {
					$checked = '';
					if(!empty($val)) {
						if( $k == $val ) {
							$checked = 'checked="checked"';
						}
					}
					echo '<span class="epl-field-row"><input type="checkbox" name="'.$field['name'].'" id="'.$field['name'].'_'.$k.'" value="'.$k.'" '.$checked.' /> <label for="'.$field['name'].'_'.$k.'">'.__($v, 'epl').'</label></span>';
				}
			}
			break;

		case 'radio':
			if(!empty($field['opts'])) {
				foreach($field['opts'] as $k=>$v) {
					$checked = '';
					if($val == $k) {
						$checked = 'checked="checked"';
					}
					echo '<span class="epl-field-row"><input type="radio" name="'.$field['name'].'" id="'.$field['name'].'_'.$k.'" value="'.$k.'" '.$checked.' /> <label for="'.$field['name'].'_'.$k.'">'.__($v, 'easy-property-listings' ).'</label></span>';
				}
			}
			break;

		case 'image':
		case 'file':
			if($val != '') {
				$img = $val;
			} else {
				$img = plugin_dir_url( __DIR__ ).'assets/images/no_image.png';
			}
			echo '
				<div class="epl-media-row">
					<input type="text" name="'.$field['name'].'" id="'.$field['name'].'" value="'.stripslashes($val).'" />
					&nbsp;&nbsp;<input type="button" name="epl_upload_button" class="button" value="'.__('Add File', 'easy-property-listings' ).'" />';

					if( in_array( pathinfo($img, PATHINFO_EXTENSION), array('jpg','jpeg','png','gif') ) ) {
						echo '&nbsp;&nbsp;<img src="'.$img.'" alt="" />';
					}
			echo
					'<div class="epl-clear"></div>
				</div>
			';
			break;

		case 'editor':
			wp_editor(stripslashes($val), $field['name'], $settings = array('textarea_rows'=>5));
			break;

		case 'textarea':
			$atts = '';
			if(isset($field['maxlength'] ) && $field['maxlength'] > 0) {
				$atts = ' maxlength="'.$field['maxlength'].'"';
			}
			echo '<textarea name="'.$field['name'].'" id="'.$field['name'].'" '.$atts.'>'.stripslashes($val).'</textarea>';
			break;

		case'decimal':
			$atts = '';
			if($field['maxlength'] > 0) {
				$atts = ' maxlength="'.$field['maxlength'].'"';
			}
			echo '<input type="text" name="'.$field['name'].'" id="'.$field['name'].'" value="'.stripslashes($val).'" class="validate[custom[onlyNumberWithDecimal]]" '.$atts.' />';
			break;

		case 'number':
			$atts = '';
			if(isset($field['maxlength']) && $field['maxlength'] > 0) {
				$atts = ' maxlength="'.$field['maxlength'].'"';
			}
			echo '<input type="number" name="'.$field['name'].'" id="'.$field['name'].'" value="'.stripslashes($val).'" class="validate[custom[onlyNumber]]" '.$atts.' />';
			break;

		case 'date':
			$atts = '';
			echo '<input type="text" class="epldatepicker" name="'.$field['name'].'" id="'.$field['name'].'" value="'.stripslashes($val).'" '.$atts.' />';
			break;


		case 'auction-date':
			$atts = '';
			echo '<input type="text" name="'.$field['name'].'" id="'.$field['name'].'" value="'.stripslashes($val).'" '.$atts.' />';
			break;

		case 'sold-date':
			$atts = '';
			echo '<input type="text" name="'.$field['name'].'" id="'.$field['name'].'" value="'.stripslashes($val).'" '.$atts.' />';
			break;

		case 'email':
			echo '<input type="text" name="'.$field['name'].'" id="'.$field['name'].'" value="'.stripslashes($val).'" class="validate[custom[email]]" />';
			break;

		case 'url':
			echo '<input type="text" name="'.$field['name'].'" id="'.$field['name'].'" value="'.stripslashes($val).'" class="validate[custom[url]]" />';
			break;
		case 'button':
			$classes = isset($field['class']) ? $field['class'] : '';
			echo '<input type="button" name="'.$field['name'].'" id="'.$field['name'].'" value="'.$field['value'].'" class="'.$classes.'" />';
			break;
		case 'locked':
			$atts = '';
			echo '<span>'.stripslashes($val).'</span>';
			break;

		case 'help':
			echo '<div class="epl-help-container" id="'.isset($field['name']) ? $field['name'] : ''.'">
					'.isset($field['content']) ? $field['content'] : ''.'
				</div>';
			break;

		default:
			$atts = '';
			if(isset($field['maxlength']) &&  $field['maxlength'] > 0) {
				$atts .= ' maxlength="'.$field['maxlength'].'"';
			}
            $classes = isset($field['class']) ? $field['class'] : '';
			foreach($field as $temp_key	=>	$temp_value) {
				if (0 === strpos($temp_key, 'data-')) {
				  $atts .= ''.$temp_key.'="'.$temp_value.'"';
				}
			}
            echo '<input type="'.$field['type'].'" name="'.$field['name'].'" id="'.$field['name'].'" class="'.$classes.'"  value="'.stripslashes($val).'" '.$atts.' />';
	}

	if( isset($field['geocoder']) ) {
		if( $field['geocoder'] == 'true' ) {
			echo '<span class="epl-geocoder-button"></span>';
		}

		do_action('epl_admin_listing_map',stripslashes($val));
	}

	if(isset($field['help'])) {
		$field['help'] = trim($field['help']);
		if(!empty($field['help'])) {
			echo '<span class="epl-help-text">'.__($field['help'], 'easy-property-listings' ).'</span>';
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
	for($i=1; $i<=10; $i++) {
		$opts_epl_gallery_n[$i] = $i;
	}

	$opts_epl_property_card_excerpt_length = array();
	for($i=10; $i<=55; $i++) {
		$opts_epl_property_card_excerpt_length[$i] = $i;
	}

	$opts_epl_features = array();
	for($i=1; $i<=5; $i++) {
		$opts_epl_features[$i] = $i;
	}

	$opts_pages = array( '' => __('Select Page', 'easy-property-listings' ) );
	$pages = get_pages();

	if(!empty($pages)) {
		foreach($pages as $page) {
			$opts_pages[$page->ID] = $page->post_title;
		}
	}

	$get_google_maps_api_key_uri = '<a target="_blank" href="https://developers.google.com/maps/documentation/javascript/get-api-key">' . __( 'Google Maps Api Key' , 'easy-property-listings') . '</a>';


	$epl_currency_positions = array(
			'before'	=> __('Before - $10', 'easy-property-listings' ),
			'after'		=> __('After - 10$', 'easy-property-listings' )
			);
	$epl_currency_types = epl_get_currencies();
	$epl_post_types = epl_get_post_types();
	 if ( !function_exists('get_editable_roles') ) {
		 require_once( ABSPATH . '/wp-admin/includes/user.php' );
	 }
	$roles = array(
		'level_10'  =>  __('Administrator'),
		'level_7'  =>  __('Editor'),
		'level_2'  =>  __('Author'),
		'level_1'  =>  __('Contributor'),
		'level_0'  =>  __('Subscriber')
	);
	$fields = array(
		array(
			'label'		=>	__('Listing Types and Location Taxonomy' , 'easy-property-listings' ),
			'class'		=>	'core',
			'id'		=>	'general',
			'help'		=>	__('Select the listing types you want to enable and press Save Changes. Refresh the page to see your new activated listing types.' , 'easy-property-listings' ) . '<hr/>',
			'fields'	=>	array(
				array(
					'name'		=>	'activate_post_types',
					'label'		=>	__('Listing Types to Enable', 'easy-property-listings' ),
					'type'		=>	'checkbox',
					'opts'		=>	$epl_post_types,
					'help'		=>	__('Note: If they are not visible on the front end visit Dashboard > Settings > Permalinks and press Save Changes.' , 'easy-property-listings' )
				),

				array(
					'name'		=>	'label_location',
					'label'		=>	__('Location Taxonomy', 'easy-property-listings' ),
					'type'		=>	'text',
					'help'		=>	__('After changing this setting visit Dashboard > Settings > Permalinks to save the settings.', 'easy-property-listings' ),
					'default'	=>	__('Location' , 'easy-property-listings' )
				)
			)
		),

		array(
			'label'		=>	__('Address', 'easy-property-listings' ),
			'class'		=>	'core',
			'id'		=>	'address',
			'fields'	=>	array(

				array(
					'name'		=>	'label_suburb',
					'label'		=>	__('Suburb/Town/City Label', 'easy-property-listings' ),
					'type'		=>	'text',
					'default'	=>	__('Suburb', 'easy-property-listings' )
				),

				array(
					'name'		=>	'epl_enable_city_field',
					'label'		=>	__('Additional Address Field', 'easy-property-listings' ),
					'type'		=>	'radio',
					'opts'		=>	array(
						'yes'	=>	__('Enable', 'easy-property-listings' ),
						'no'	=>	__('Disable', 'easy-property-listings' ),
					),
					'default'	=>	'no',
					'help'		=>	__('Use when you need an additional Municipality/Town/City/Region.' , 'easy-property-listings' )
				),

				array(
					'name'		=>	'label_city',
					'label'		=>	__('Additional Address Field Label', 'easy-property-listings' ),
					'type'		=>	'text',
					'default'	=>	__('City', 'easy-property-listings' )
				),

				array(
					'name'		=>	'label_state',
					'label'		=>	__('State/Province/Region Label', 'easy-property-listings' ),
					'type'		=>	'text',
					'default'	=>	__('State', 'easy-property-listings' )
				),

				array(
					'name'		=>	'label_postcode',
					'label'		=>	__('Postcode/ZIP Label', 'easy-property-listings' ),
					'type'		=>	'text',
					'default'	=>	__('Postcode', 'easy-property-listings' )
				),

				array(
					'name'		=>	'epl_enable_country_field',
					'label'		=>	__('Display Country', 'easy-property-listings' ),
					'type'		=>	'radio',
					'opts'		=>	array(
						'yes'	=>	__('Enable', 'easy-property-listings' ),
						'no'	=>	__('Disable', 'easy-property-listings' ),
					),
					'default'	=>	'no',
					'help'		=>	__('Display country with listing address.' , 'easy-property-listings' )
				)
			)
		),

		array(
			'label'		=>	__('Labels', 'easy-property-listings' ),
			'class'		=>	'core',
			'id'		=>	'labels',
			'fields'	=>	array(

				array(
					'name'		=>	'sticker_new_range',
					'label'		=>	__('Keep Listings flagged "New" for', 'easy-property-listings' ),
					'type'		=>	'number',
					'default'	=>	'7',
					'help'		=>	__('Listings will have a "NEW" Sticker for the defined number of days.', 'easy-property-listings' )
				),

				array(
					'name'		=>	'label_new',
					'label'		=>	__('New/Just Listed Label', 'easy-property-listings' ),
					'type'		=>	'text',
					'default'	=>	__('New' , 'easy-property-listings'  )
				),

				array(
					'name'		=>	'label_home_open',
					'label'		=>	__('Home Open Label', 'easy-property-listings' ),
					'type'		=>	'text',
					'default'	=>	__('Home Open', 'easy-property-listings' )
				),

				array(
					'name'		=>	'label_poa',
					'label'		=>	__('No Price Label', 'easy-property-listings' ),
					'type'		=>	'text',
					'default'	=>	__('POA', 'easy-property-listings' )
				),

				array(
					'name'		=>	'label_under_offer',
					'label'		=>	__('Under Offer Label', 'easy-property-listings' ),
					'type'		=>	'text',
					'default'	=>	__('Under Offer', 'easy-property-listings' )
				),

				array(
					'name'		=>	'label_sold',
					'label'		=>	__('Sold Label', 'easy-property-listings' ),
					'type'		=>	'text',
					'default'	=>	__('Sold', 'easy-property-listings' )
				),

				array(
					'name'		=>	'label_leased',
					'label'		=>	__('Leased Label', 'easy-property-listings' ),
					'type'		=>	'text',
					'default'	=>	__('Leased', 'easy-property-listings' )
				),

				array(
					'name'		=>	'display_bond',
					'label'		=>	__('Rental Bond/Deposit Display', 'easy-property-listings' ),
					'type'		=>	'radio',
					'opts'		=>	array(
							1	=>	__('Enable', 'easy-property-listings' ),
							0	=>	__('Disable', 'easy-property-listings' )
					),
					'help'		=>	__('Display the Bond/Deposit on rental listings.', 'easy-property-listings' )
				),

				array(
					'name'		=>	'label_bond',
					'label'		=>	__('Rental Bond/Deposit Label', 'easy-property-listings' ),
					'type'		=>	'text',
					'default'	=>	__('Bond', 'easy-property-listings' )
				),
			)
		),

		array(
			'label'		=>	__('Listing Single View', 'easy-property-listings' ),
			'class'		=>	'core',
			'id'		=>	'general',
			'help'		=>	__('Configure the default options when viewing a single listing.', 'easy-property-listings' ),
			'fields'	=>	array(
				array(
					'name'		=>	'display_single_gallery',
					'label'		=>	__('Automatically display image gallery?', 'easy-property-listings' ),
					'type'		=>	'radio',
					'opts'		=>	array(
						1	=>	__('Enable', 'easy-property-listings' ),
						0	=>	__('Disable', 'easy-property-listings' )
					),
					'default'	=>	0,
					'help'		=>	__('Images uploaded and attached to a listing will automatically display on the single listing page.', 'easy-property-listings' )
				),

				array(
					'name'		=>	'display_gallery_n',
					'label'		=>	__('Gallery columns?', 'easy-property-listings' ),
					'type'		=>	'select',
					'opts'		=>	$opts_epl_gallery_n,
					'default'	=>	4
				),

				array(
					'name'		=>	'display_feature_columns',
					'label'		=>	__('Feature list columns?', 'easy-property-listings' ),
					'type'		=>	'select',
					'opts'		=>	$opts_epl_features,
					'default'	=>	2
				),

				array(
					'name'		=>	'epl_video_width',
					'label'		=>	__('Video width on single listings', 'easy-property-listings' ),
					'type'		=>	'number',
					'help'		=>	__('Width should be in pixels','easy-property-listings' )
				)
			)
		),

		array(
			'label'		=>	__('Listing Archive View', 'easy-property-listings' ),
			'class'		=>	'core',
			'id'		=>	'general',
			'help'		=>	__('Configure the default options for when viewing the archive listing pages.', 'easy-property-listings' ),
			'fields'	=>	array(
				array(
					'name'		=>	'display_excerpt_length',
					'label'		=>	__('Excerpt words', 'easy-property-listings' ),
					'type'		=>	'select',
					'opts'		=>	$opts_epl_property_card_excerpt_length,
					'default'	=>	10,
					'help'		=>	__('This is ignored when using manual excerpts.', 'easy-property-listings' )
				),
				array(
					'name'		=>	'display_archive_view_type',
					'label'		=>	__('Listing view type', 'easy-property-listings' ),
					'type'		=>	'radio',
					'opts'		=>	array(
						'list'	=>	__('List', 'easy-property-listings' ),
						'grid'	=>	__('Grid', 'easy-property-listings' )
					),
					'default'	=>	'list'
				),

				array(
					'name'		=>	'use_fancy_navigation',
					'label'		=>	__('Fancy pagination', 'easy-property-listings' ),
					'type'		=>	'select',
					'opts'		=>	array(
						0		=>	__('No, use WordPress default pagination', 'easy-property-listings' ),
						1		=>	__('Yes, use fancy navigation', 'easy-property-listings' )
					),
					'default'	=>	0
				)
			)
		),

		array(
			'label'		=>	__('Search Widget: Tab Labels', 'easy-property-listings' ),
			'class'		=>	'core',
			'id'		=>	'labels',
			'help'		=>	__('Customise the tab labels of the EPL - Search Widget.', 'easy-property-listings' ),
			'fields'	=>	array(

				array(
					'name'		=>	'widget_label_property',
					'label'		=>	__('Property', 'easy-property-listings' ),
					'type'		=>	'text',
					'default'	=>	__('Property', 'easy-property-listings' )
				),
				array(
					'name'		=>	'widget_label_land',
					'label'		=>	__('Land', 'easy-property-listings' ),
					'type'		=>	'text',
					'default'	=>	__('Land', 'easy-property-listings' )
				),
				array(
					'name'		=>	'widget_label_rental',
					'label'		=>	__('Rental', 'easy-property-listings' ),
					'type'		=>	'text',
					'default'	=>	__('Rental', 'easy-property-listings' )
				),
				array(
					'name'		=>	'widget_label_rural',
					'label'		=>	__('Rural', 'easy-property-listings' ),
					'type'		=>	'text',
					'default'	=>	__('Rural', 'easy-property-listings' )
				),
				array(
					'name'		=>	'widget_label_commercial',
					'label'		=>	__('Commercial', 'easy-property-listings' ),
					'type'		=>	'text',
					'default'	=>	__('Commercial', 'easy-property-listings' )
				),
				array(
					'name'		=>	'widget_label_commercial_land',
					'label'		=>	__('Commercial Land', 'easy-property-listings' ),
					'type'		=>	'text',
					'default'	=>	__('Commercial Land', 'easy-property-listings' )
				),
				array(
					'name'		=>	'widget_label_business',
					'label'		=>	__('Business', 'easy-property-listings' ),
					'type'		=>	'text',
					'default'	=>	__('Business', 'easy-property-listings' )
				)
			)
		),

		array(
			'label'		=>	__('Dashboard Listing Columns' , 'easy-property-listings' ),
			'class'		=>	'core',
			'id'		=>	'admin_general',
			'fields'	=>	array(

				array(
					'name'		=>	'epl_max_graph_sales_price',
					'label'		=>	__('Graph Max', 'easy-property-listings' ),
					'type'		=>	'number',
					'default'	=>	'2000000',
					'help'		=>	__('Used for bar chart display on listings for sale.' , 'easy-property-listings' )
				),

				array(
					'name'		=>	'epl_max_graph_rent_price',
					'label'		=>	__('Graph Rental Max', 'easy-property-listings' ),
					'type'		=>	'number',
					'default'	=>	'2000',
					'help'		=>	__('Rental range.' , 'easy-property-listings' )
				),

				array(
					'name'		=>	'epl_admin_thumb_size',
					'label'		=>	__('Image size', 'easy-property-listings' ),
					'type'		=>	'radio',
					'opts'		=>	array(
						'admin-list-thumb'	=>	__('100 X 100', 'easy-property-listings' ),
						'epl-image-medium-crop'	=>	__('300 X 200', 'easy-property-listings' ),
					),
					'default'	=>	'admin-list-thumb',
					'help'		=>	__('Size of the image shown in listing columns in admin area' , 'easy-property-listings' )
				),

				array(
					'name'		=>	'admin_unique_id',
					'label'		=>	__('Unique Listing ID column', 'easy-property-listings' ),
					'type'		=>	'radio',
					'opts'		=>	array(
						1	=>	__('Enable', 'easy-property-listings' ),
						0	=>	__('Disable', 'easy-property-listings' )
					),
					'default'	=>	0
				),

				array(
					'name'		=>	'debug',
					'label'		=>	__('Geocode Lat/Long results column', 'easy-property-listings' ),
					'type'		=>	'radio',
					'opts'		=>	array(
						1	=>	__('Enable', 'easy-property-listings' ),
						0	=>	__('Disable', 'easy-property-listings' )
					),
					'default'	=>	0
				),
			),
		),

		array(
			'label'		=>	__('Theme Setup' , 'easy-property-listings' ),
			'class'		=>	'core',
			'id'		=>	'theme_setup',
			'help'		=>	__('The following settings will use your theme templates to generate your listing pages. If your listings appear too wide or your sidebar is in the wrong place enable theme compatibility. When this is enabled you can use the included shortcodes like [listing post_type="property" tools_top="on"] to display your listings with sorting and grid options.', 'easy-property-listings' ) . '<hr/>',
			'fields'	=>	array(

				array(
					'name'		=>	'epl_feeling_lucky',
					'label'		=>	__('Theme Compatibility', 'easy-property-listings' ),
					'type'		=>	'radio',
					'opts'		=>	array(
						'on'	=>	__('Enable', 'easy-property-listings' ),
						'off'	=>	__('Disable', 'easy-property-listings' )
					),
					'default'	=>	'off',
					'help'		=>	__('When using iThemes, Genesis frameworks or your listings look good, leave this disabled.' , 'easy-property-listings' )
				)
			)
		),

		array(
			'label'		=>	__('Theme Setup: Featured Images' , 'easy-property-listings' ),
			'class'		=>	'core',
			'id'		=>	'theme_setup_featured_images',
			'help'		=>	__('Some WordPress themes automatically display featured images on posts and pages which may cause you to see double on your listings. Use the following settings to adjust the featured image behaviour.', 'easy-property-listings' ) . '<hr/>',
			'fields'	=>	array(

				array(
					'name'		=>	'help_lucky_theme_featured_image',
					'type'		=>	'help',
					'content'	=>	__('Theme Featured Image Settings' , 'easy-property-listings' )
				),

				array(
					'name'		=>	'epl_lucky_disable_theme_single_thumb',
					'label'		=>	__('Single Listing', 'easy-property-listings' ),
					'type'		=>	'checkbox_option',
					'opts'		=>	array(
						'on'	=>	__('Disable', 'easy-property-listings' ),
					),
					'default'	=>	'off'
				),

				array(
					'name'		=>	'epl_lucky_disable_archive_thumb',
					'label'		=>	__('Archive Listing', 'easy-property-listings' ),
					'type'		=>	'checkbox_option',
					'opts'		=>	array(
						'on'	=>	__('Disable', 'easy-property-listings' ),
					),
					'default'	=>	'off'
				),

				array(
					'name'		=>	'help_lucky_epl_featured_image',
					'type'		=>	'help',
					'content'	=>	'<hr/>' . __('Easy Property Listings Featured Image Settings' , 'easy-property-listings' )
				),

				array(
					'name'		=>	'epl_lucky_disable_single_thumb',
					'label'		=>	__('Single Listing', 'easy-property-listings' ),
					'type'		=>	'checkbox_option',
					'opts'		=>	array(
						'on'	=>	__('Disable', 'easy-property-listings' ),
					),
					'default'	=>	'off'
				),

				array(
					'name'		=>	'epl_lucky_disable_epl_archive_thumb',
					'label'		=>	__('Archive Listing', 'easy-property-listings' ),
					'type'		=>	'checkbox_option',
					'opts'		=>	array(
						'on'	=>	__('Disable', 'easy-property-listings' ),
					),
					'default'	=>	'off'
				)
			)
		),


		array(
			'label'		=>	__('Currency' , 'easy-property-listings' ),
			'class'		=>	'core',
			'id'		=>	'currency',
			'fields'	=>	array(
				array(
					'name'	=>	'currency',
					'label'	=>	__('Currency Type', 'easy-property-listings' ),
					'type'	=>	'select',
					'opts'	=>	$epl_currency_types
				),

				array(
					'name'	=>	'currency_position',
					'label'	=>	__('Symbol Position', 'easy-property-listings' ),
					'type'	=>	'select',
					'opts'	=>	$epl_currency_positions
				),

				array(
					'name'	=>	'currency_thousands_separator',
					'label'	=>	__('Thousands Separator', 'easy-property-listings' ),
					'type'	=>	'text'
				),

				array(
					'name'	=>	'currency_decimal_separator',
					'label'	=>	__('Decimal Separator', 'easy-property-listings' ),
					'type'	=>	'text'
				)
			)
		),

		array(
			'label'		=>	__('Inspection Date & Time Format' , 'easy-property-listings' ),
			'class'		=>	'core',
			'id'		=>	'inspection_date_time',
			'fields'	=>	array(
				array(
					'name'	=>	'inspection_date_format',
					'label'	=>	__('Date Format', 'easy-property-listings' ),
					'type'	=>	'radio',
					'opts'	=>	array(

						'd-M-Y'				=>	date('d-M-Y',time() ),
						'l, dS F'			=>	date('l, dS F',time() ),
						'D d M'				=>	date('D d M',time() ),
						'custom_inspection_date_format'	=>	__('Custom','easy-property-listings' )

					),
				),
				array(
					'name'	=>	'custom_inspection_date_format',
					'label'	=>	__('Custom Date Format', 'easy-property-listings' ),
					'type'	=>	'text'
				),
				array(
					'name'	=>	'inspection_time_format',
					'label'	=>	__('Time Format', 'easy-property-listings' ),
					'type'	=>	'radio',
					'opts'	=>	array(

						'h:i A'				=>	date('h:i A',time() ),
						'h:i a'				=>	date('h:i a',time() ),
						'H:i'				=>	date('h:i',time() ) . __(' ( 24 Hours Format ) ' , 'easy-property-listings' ),
						'custom_inspection_time_format'	=>	__('Custom','easy-property-listings' )

					)
				),
				array(
					'name'	=>	'custom_inspection_time_format',
					'label'	=>	__('Custom Time Format', 'easy-property-listings' ),
					'type'	=>	'text'
				),
			)
		),

		array(
			'label'		=>	__('Contact Settings' , 'easy-property-listings' ),
			'class'		=>	'core',
			'id'		=>	'contact',
			'fields'	=>	array(
				array(
					'name'	=>	'min_contact_access',
					'label'	=>	__('Contact Access Level', 'easy-property-listings' ),
					'type'	=>	'radio',
					'opts'	=>	$roles,
				),

			),
			'default'	=>	'administrator',
		),

		array(
			'label'		=>	__('Reports Settings' , 'easy-property-listings' ),
			'class'		=>	'core',
			'id'		=>	'reports',
			'fields'	=>	array(
				array(
					'name'	=>	'min_reports_access',
					'label'	=>	__('Reports Access Level', 'easy-property-listings' ),
					'type'	=>	'radio',
					'opts'	=>	$roles,
				),

			),
			'default'	=>	'administrator',
		),

		array(
			'label'		=>	__('Advanced Settings' , 'easy-property-listings' ),
			'class'		=>	'core',
			'id'		=>	'advanced',
			'fields'	=>	array(
				array(
					'name'		=>	'epl_use_core_css',
					'label'		=>	__('Disable Styles', 'easy-property-listings' ),
					'type'		=>	'checkbox_option',
					'opts'		=>	array(
						'on'	=>	__('Yes', 'easy-property-listings' ),
					),
					'default'	=>	'off',
					'help'		=>	__('Check this to disable all elements.' , 'easy-property-listings' )
				),

				array(
					'name'		=>	'epl_css_legacy',
					'label'		=>	__('Enable Legacy Styles', 'easy-property-listings' ),
					'type'		=>	'checkbox_option',
					'opts'		=>	array(
						'on'	=>	__('Enable', 'easy-property-listings' ),
					),
					'default'	=>	'off',
					'help'		=>	__('Check this to enable legacy css styles.' , 'easy-property-listings' )
				),


				array(
					'name'		=>	'epl_disable_google_api',
					'label'		=>	__('Google Maps API', 'easy-property-listings' ),
					'type'		=>	'radio',
					'opts'		=>	array(
						''	=>	__('Enable', 'easy-property-listings' ),
						'on'	=>	__('Disable', 'easy-property-listings' )
					),
					'default'	=>	'',
					'help'		=>	__('Set to disabled if Google Maps API has already been loaded in your theme or other plugin.' , 'easy-property-listings' )

				),
				array(
					'name'		=>	'epl_google_api_key',
					'label'		=>	__('Google Maps API Key', 'easy-property-listings' ),
					'type'		=>	'text',
					'help'		=>	__("Register for a $get_google_maps_api_key_uri here." , 'easy-property-listings' )
				),
				array(
					'name'		=>	'uninstall_on_delete',
					'label'		=>	__('Remove Data on Uninstall?', 'easy-property-listings' ),
					'type'		=>	'radio',
					'opts'		=>	array(
						1	=>	__('Enable', 'easy-property-listings' ),
						0	=>	__('Disable', 'easy-property-listings' )
					),
					'help'		=>	__('Check this box if you would like EPL to completely remove all of its data when the plugin is deleted.', 'easy-property-listings' ),
					'default'	=>	0
				)
			)
		),

	);

	$fields = apply_filters('epl_display_options_filter', $fields);
	return $fields;
}

/**
 * Sold status filter
 *
 * @since 2.1.11
 */
function epl_sold_label_status_filter_callback() {
	global $epl_settings;
	$epl_settings['label_sold'] = !isset($epl_settings['label_sold']) ? '' : $epl_settings['label_sold'];
	$sold_label	= $epl_settings['label_sold'] != 'Sold' || $epl_settings['label_sold'] != '' ? $epl_settings['label_sold'] : __('Sold' , 'easy-property-listings' );
	return $sold_label;
}
add_filter('epl_sold_label_status_filter', 'epl_sold_label_status_filter_callback' );

/**
 * Under Offer status filter
 *
 * @since 2.1.11
 */
function epl_under_offer_label_status_filter_callback() {
	global $epl_settings;
	$epl_settings['label_under_offer'] = !isset($epl_settings['label_under_offer']) ? '' : $epl_settings['label_under_offer'];
	$under_offer_label	= $epl_settings['label_under_offer'] != 'Under Offer' || $epl_settings['label_under_offer'] != '' ? $epl_settings['label_under_offer'] : __('Under Offer' , 'easy-property-listings' );
	return $under_offer_label;
}
add_filter('epl_under_offer_label_status_filter', 'epl_under_offer_label_status_filter_callback' );

/**
 * Leased status filter
 *
 * @since 2.1.11
 */
function epl_leased_label_status_filter_callback() {
	global $epl_settings;
	$epl_settings['label_leased'] = !isset($epl_settings['label_leased']) ? '' : $epl_settings['label_leased'];
	$leased_label	= $epl_settings['label_leased'] != 'Leased' || $epl_settings['label_leased'] != '' ? $epl_settings['label_leased'] : __('Leased' , 'easy-property-listings' );
	return $leased_label;
}
add_filter('epl_leased_label_status_filter', 'epl_leased_label_status_filter_callback' );

/**
* Description: Getting all the values associated with a specific custom post meta key, across all posts
* Author: Chinmoy Paul
* Author URL: http://pwdtechnology.com
*
* @since 2.1.11
* @param string $key Post Meta Key.
* @param string $type Post Type. Default is post. You can pass custom post type here.
* @param string $status Post Status like Publish, draft, future etc. default is publish
* @return array
*/
function epl_get_unique_post_meta_values( $key = '', $type = 'post', $status = 'publish' ) {

    global $wpdb;

    if( empty( $key ) )
        return;

    $res = $wpdb->get_col( $wpdb->prepare( "
SELECT DISTINCT pm.meta_value FROM {$wpdb->postmeta} pm
LEFT JOIN {$wpdb->posts} p ON p.ID = pm.post_id
WHERE pm.meta_key = '%s'
AND p.post_status = '%s'
AND p.post_type = '%s'
", $key, $status, $type ) );

	$res = array_filter($res);
	foreach($res as $key =>	&$elem) {
		$elem 	= maybe_unserialize($elem);
		if(!empty($elem) && is_array($elem) ) {
			foreach($elem as $el) {
				$res[] 	= $el;
			}
			unset($res[$key]);
		}

	}
	$res = array_filter($res);
	if(!empty($res))
    	return array_combine(array_filter($res), array_map('ucwords',array_filter($res)) );
}

/**
 * Session Start
 *
 * @since 3.0
 */
function epl_session_start() {
    if(!session_id()) {
        session_start();
    }
}
add_action('init', 'epl_session_start', 1);

/**
 * Session End
 *
 * @since 3.0
 */
function epl_session_end() {
    session_destroy ();
}
add_action('wp_logout', 'epl_session_end');
add_action('wp_login', 'epl_session_end');

/**
 * Get Sales Count By Date
 *
 * @since 3.0
 * @param int $day Day number
 * @param int $month_num Month number
 * @param int $year Year
 * @param int $hour Hour
 * @return int $count Sales
 */
function epl_get_sales_by_date( $day = null, $month_num = null, $year = null, $hour = null, $status=null,$day_by_day=true ) {
	$post_type = isset($_GET['view']) ? $_GET['view'] : 'property';
	$args = array(
		'post_type'      => $post_type,
		'nopaging'       => true,
		'year'           => $year,
		'fields'         => 'ids',
		'post_status'    => array( 'publish' ),
		'update_post_meta_cache' => false,
		'update_post_term_cache' => false
	);

	if( in_array($status, array('sold','leased') ) ) {

			$month_num  = is_null($month_num) ? 00 : $month_num;
			$day 		= is_null($day) ? 00 : $day;
			$year 		= is_null($year) ? 0000 : $year;
			$range		= isset($_GET['range'])?$_GET['range']:'other';

			$args['meta_query'] = array(
				array(
					'key' 		=> 'property_status',
					'value' 	=> (array) $status,
					'compare' => 'IN',
				),
			);

			if( in_array($range, array('other','last_year','this_year','last_quarter','this_quarter') ) ) {

				$sold_key = $status == 'leased' ? 'property_date_available':'property_sold_date';

				$sold_date_end  	= date('Y-m-d',strtotime($year.'-'.$month_num.'-'.$day));
				$sold_date_start  	= $day_by_day == true ? $sold_date_end : date('Y-m-01',strtotime($year.'-'.$month_num.'-'.$day));

				$args['meta_query'][] = array(
					'key' 		=> $sold_key,
					'value' 	=> array($sold_date_start,$sold_date_end),
					'type'		=>	'DATE',
					'compare'	=>	'BETWEEN'
				);

			} else {

				$sold_key = $status == 'leased' ? 'property_date_available':'property_sold_date';
				$sold_date  = date('Y-m-d',strtotime($year.'-'.$month_num.'-'.$day));

				$args['meta_query'][] = array(
					'key' 		=> $sold_key,
					'value' 	=> $sold_date,
					'type'		=>	'DATE',
				);
			}

	} else {

			if ( ! empty( $month_num ) )
				$args['monthnum'] = $month_num;

			if ( ! empty( $day ) )
				$args['day'] = $day;

			if ( ! empty( $hour ) )
				$args['hour'] = $hour;
	}

	$args = apply_filters( 'epl_get_sales_by_date_args', $args  );

	$key   = 'epl_stats_' . substr( md5( serialize( $args ) ), 0, 15 );
	$count = get_transient( $key );
	if( false === $count ) {
		$sales = new WP_Query( $args );
		$count = (int) $sales->post_count;
		// Cache the results for one hour
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
 * @param integer $n
 * @return string Short month name
 */
function epl_month_num_to_name( $n ) {
	$timestamp = mktime( 0, 0, 0, $n, 1, 2005 );

	return date_i18n( "M", $timestamp );
}

/**
 * Retrieve contacts from the database
 *
 * @access  public
 * @since   3.0
 */
function epl_get_contacts( $args = array() ) {

	global $wpdb;

	$defaults = array(
		'post_type'		=> 'epl_contact',
		'posts_per_page'	=> 20,
		'offset'		=> 0,
		'orderby'		=> 'ID',
		'order'			=> 'DESC'
	);

	$args  = wp_parse_args( $args, $defaults );

	if( $args['posts_per_page'] < 1 ) {
		$args['number'] = -1;
	}

	$where = ' WHERE 1=1 ';

	// specific contacts
	if( ! empty( $args['ID'] ) ) {
		$args['post__in']  = $args['ID'];


	}

	//specific contacts by email
	if( ! empty( $args['email'] ) ) {

		$email_query =  array(
			'key'		=> 'contact_email',
			'value'		=> $args['email'],
		);

		if( is_array( $args['email'] ) ) {
			$email_query['comparison'] = 'IN';

		}
		$args['meta_query'][] = $email_query;
	}

	// specific contacts by name
	if( ! empty( $args['name'] ) ) {
		$args['post_title'] = $args['name'];
	}

	$cache_key = md5( 'epl_contacts_' . serialize( $args ) );

	$contacts = wp_cache_get( $cache_key, 'contacts' );

	$args['orderby'] = esc_sql( $args['orderby'] );
	$args['order']   = esc_sql( $args['order'] );

	if( $contacts === false ) {
		$contacts = get_posts($args);
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
		's'			=> sanitize_text_field($_POST['s']),
		'showposts'   		=> 6,
		'post_type' 		=> epl_get_core_post_types(),
		'post_status' 		=> 'publish',
	);
	$query = http_build_query($search_array);
	$listings = get_posts(  $query );
	if( !empty($listings) ) {
		echo '<ul class="epl-popup-box epl-property-suggestion epl-striped">';
		foreach( $listings as  $listing) {
			$status = get_post_meta($listing->ID,'property_status',true);
			echo '<li data-id="'.$listing->ID.'"><span class="epl-listing-type">'.$listing->post_type.'</span>'.$listing->post_title.'<span class="epl-listing-status type_'.$status.'">'.$status.'</span></li>';
		}
		echo '</ul>';
	}
	wp_die();
}
add_action('wp_ajax_nopriv_epl_search_listing','epl_search_listing');
add_action('wp_ajax_epl_search_listing','epl_search_listing');

/**
 * Get Contact Categories
 *
 * @access  public
 * @since   3.0
 */
function epl_get_contact_categories() {
	return apply_filters('epl_contact_categories',array(
		'appraisal'	=>  __('Appraisal','easy-property-listings' ),
		'buyer'		=>  __('Buyer','easy-property-listings' ),
		'contact'	=>  __('Contact','easy-property-listings' ),
		'lead'		=>  __('Lead','easy-property-listings' ),
		'landlord'	=>  __('Landlord','easy-property-listings' ),
		'new'		=>  __('New','easy-property-listings' ),
		'past_customer'	=>  __('Past Customer','easy-property-listings' ),
		'seller'	=>  __('Seller','easy-property-listings' ),
		'tenant'	=>  __('Tenant','easy-property-listings' ),
		'widget'	=>  __('Widget Lead','easy-property-listings' ),
		'contract'	=>  __('Under Contract','easy-property-listings' ),
	));
}

/**
 * Get Contact Labels
 *
 * @access  public
 * @since   3.0
 */
function get_category_label($category) {
	foreach(epl_get_contact_categories() as $key    =>  $cat) {
		if($key == $category) {
			return $cat;
			break;
		}
	}
	return $category;
}

function epl_starts_with($haystack, $needle) {
    // search backwards starting from haystack length characters from the end
    return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== false;
}

function epl_ends_with($haystack, $needle) {
    // search forward starting from end minus needle length characters
    return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== false);
}

/**
 * Parse EPL shortcodes for meta queries
 * @param  [type] $atts [description]
 * @return [type]       [description]
 */
function epl_parse_atts($atts) {

	$query = array();

	if( empty($atts) )
		return $atts;

	foreach($atts as $key 	=>	&$value) {

		$this_query = array(
			'compare'	=>	'=',
			'value'		=>	$value
		);

		// check for meta
		if( epl_starts_with($key,'epl_meta_') ) {
			$key = preg_replace('/^epl_meta_/', '', $key);

			if( epl_ends_with($key,'_min') || epl_ends_with($key,'_max') ) {

				if(epl_ends_with($key,'_min')) {
					$key = preg_replace('/_min$/', '', $key);
					$this_query['compare'] = '>=';
				} else {
					$key = preg_replace('/_max$/', '', $key);
					$this_query['compare'] = '<=';
				}
			}

			$this_query['key'] = $key;
			$query['meta_query'][] = $this_query;
		}

	}
	return isset($query['meta_query'])?$query['meta_query'] : false;

}