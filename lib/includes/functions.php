<?php
/**
 * Functions
 *
 * @package     EPL
 * @subpackage  Front/Display
 * @copyright   Copyright (c) 2014, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */
 
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Fetch and format property price
 *
 * @since 1.0
 */
function epl_get_property_price($post) {
	global $epl_settings;
	
	$d_bond = '';
	if(!empty($epl_settings) && isset($epl_settings['display_bond'])) {
		$d_bond = $epl_settings['display_bond'];
	}
	
	$property_tax = '';
	$price_class = '';
	$price_sticker = '';
	$l_price = '';
	
	$property_meta = epl_get_property_meta($post->id);
	
	$property_price = '';
	if(isset($property_meta['property_price']) && !empty($property_meta['property_price'])) {
		$property_price = $property_meta['property_price'][0];
	}
	
	$property_status = '';
	if(isset($property_meta['property_status']) && !empty($property_meta['property_status'])) {
		$property_status = $property_meta['property_status'][0];
	}
	
	$property_price_search = '';
	if(isset($property_meta['property_price']) && !empty($property_meta['property_price'])) {
		$property_price_search = $property_meta['property_price'][0];
	}
	
	$property_price_view = '';
	if(isset($property_meta['property_price_view']) && !empty($property_meta['property_price_view'])) {
		$property_price_view = $property_meta['property_price_view'][0];
	}
	
	$property_inspection_times = '';
	if(isset($property_meta['property_inspection_times']) && !empty($property_meta['property_inspection_times'])) {
		$property_inspection_times = $property_meta['property_inspection_times'][0];
	}
	
	$property_price_display = '';
	if(isset($property_meta['property_price_display']) && !empty($property_meta['property_price_display'])) {
		$property_price_display = $property_meta['property_price_display'][0];
		if( $property_price_display == 'yes') {
			$property_price_display = 1;
		}
	}
	
	$property_under_offer = '';
	if(isset($property_meta['property_under_offer']) && !empty($property_meta['property_under_offer'])) {
		$property_under_offer = $property_meta['property_under_offer'][0];
		if( $property_under_offer == 'yes') {
			$property_under_offer = 1;
		}
	}
	
	$property_rent_display = '';
	if(isset($property_meta['property_rent_display']) && !empty($property_meta['property_rent_display'])) {
		$property_rent_display = $property_meta['property_rent_display'][0];
		if( $property_rent_display == 'yes') {
			$property_rent_display = 1;
		}
	}
	
	$property_rent = '';
	if(isset($property_meta['property_rent']) && !empty($property_meta['property_rent'])) {
		$property_rent = $property_meta['property_rent'][0];
		if ( $property_rent !='' ) {
			$property_rent = epl_currency_formatted_amount($property_rent);
		}
	}
	
	$property_rent_period = '';
	if(isset($property_meta['property_rent_period']) && !empty($property_meta['property_rent_period'])) {
		$property_rent_period = $property_meta['property_rent_period'][0];
	}
	
	$property_bond = '';
	if(isset($property_meta['property_bond']) && !empty($property_meta['property_bond'])) {
		$property_bond = $property_meta['property_bond'][0];
	}
	
	// Property Price Display
	if ( $property_price_search == '') {
	
	} elseif($property_price_view != '') {
		$property_price = $property_price_view; 
	} else  {
		$property_price = epl_currency_formatted_amount($property_price_search);
	}
	if ( $post->post_type == 'property' || $post->post_type == 'land') {
		if ( 'sold' == $property_status ) {
			$price_class = 'epl-price sold';
			$price_plain_value = __('Sold', 'epl');
			$price = '<span class="page-price sold-status">'.__('Sold', 'epl').'</span>';
			$price_sticker = '<span class="status-sticker sold">'.__('Sold', 'epl').'</span>';
			$l_price = '<li class="page-price sold-status">'.__('Sold', 'epl').'</li>';
		} elseif ( $property_price != '' && $property_price_display == 1 ) {
			$price_plain_value = $property_price . $property_tax;
			$price = '<span class="page-price">'. $property_price . $property_tax . '</span>';
			$l_price = '<li class="page-price">'. $property_price . $property_tax . '</li>';
			if ( $property_inspection_times != '' ){
				$price_sticker = '<span class="status-sticker open">'.__('Open House', 'epl').'</span>';
			}
		} else {
			$price_plain_value = 'POA';
			$price = '<span class="page-price">POA</span>';
			$l_price = '<li class="page-price">POA</li>';
			if ( $property_inspection_times != '' ){
				$price_sticker = '<span class="status-sticker open">'.__('Open House', 'epl').'</span>';
			}
		}
		if ( $property_under_offer == 1 && 'sold' != $property_status) {
			$price_plain_value = __('Under Offer', 'epl');
			$price = '<span class="page-price under-offer-status">'.__('Under Offer', 'epl').'</span>';
			$price_sticker = '<span class="status-sticker under-offer">'.__('Under Offer', 'epl').'</span>';
			$l_price = '<li class="page-price under-offer-status">'.__('Under Offer', 'epl').'</li>';
		}
	} elseif ( $post->post_type == 'rental' ) {
		if( $property_rent != '' && $property_rent_display == 1 && $property_status != 'leased') {		
			$price_plain_value = $property_rent . '/' . $property_rent_period;
			$price = '<span class="page-price-rent">';
			$price .= '<span class="page-price" style="margin-right:0;">'. $property_rent . '</span>';
			$price .= '<span class="rent-period">/' . $property_rent_period . '</span>';
			$price .= '</span>';
			$l_price = '<li class="page-price-rent">';
			$l_price .= '<span class="page-price" style="margin-right:0;">'. $property_rent . '</span>';
			$l_price .= '<span class="rent-period">/' . $property_rent_period . '</span>';
			$l_price .= '</li>';
			if ($property_bond != 0 && $d_bond == 1) {
				$price_plain_value .= ' $' . $property_bond . ' '.__('bond', 'epl');
				$price .= '<span class="bond">$' . $property_bond . ' '.__('bond', 'epl').'</span>';
			}		
		} elseif ( $property_status == 'leased') {
			$price_plain_value = __('Leased', 'epl');
			$price = '<span class="page-price sold-status">'.__('Leased', 'epl').'</span>';
			$price_sticker = '<span class="status-sticker leased">'.__('Leased', 'epl').'</span>';
			$l_price = '<li class="page-price sold-status">'.__('Leased', 'epl').'</li>';
		} else {
			$price_plain_value = __('TBA', 'epl');
			$price = '<span class="page-price">'.__('TBA', 'epl').'</span>';
			if ( $property_inspection_times != '' ){
				$price_sticker = '<span class="status-sticker open">'.__('Open House', 'epl').'</span>';
			}
		}
	} elseif ( $post->post_type == 'commercial' ) {
		$property_com_authority = '';
		if(isset($property_meta['property_com_authority']) && !empty($property_meta['property_com_authority'])) {
			$property_com_authority = $property_meta['property_com_authority'][0];
		}
		
		$property_exclusivity = '';
		if(isset($property_meta['property_exclusivity']) && !empty($property_meta['property_exclusivity'])) {
			$property_exclusivity = $property_meta['property_exclusivity'][0];
		}
		
		$property_com_listing_types = '';
		if(isset($property_meta['property_com_listing_types']) && !empty($property_meta['property_com_listing_types'])) {
			$property_com_listing_types = $property_meta['property_com_listing_types'][0];
		}
		
		$property_com_rent = '';
		if(isset($property_meta['property_com_rent']) && !empty($property_meta['property_com_rent'])) {
			$property_com_rent = $property_meta['property_com_rent'][0];
		}
		
		$property_com_is_multiple = '';
		if(isset($property_meta['property_com_is_multiple']) && !empty($property_meta['property_com_is_multiple'])) {
			$property_com_is_multiple = $property_meta['property_com_is_multiple'][0];
		}
		
		$property_com_rent_range_min = '';
		if(isset($property_meta['property_com_rent_range_min']) && !empty($property_meta['property_com_rent_range_min'])) {
			$property_com_rent_range_min = $property_meta['property_com_rent_range_min'][0];
		}
		
		$property_com_rent_range_max = '';
		if(isset($property_meta['property_com_rent_range_max']) && !empty($property_meta['property_com_rent_range_max'])) {
			$property_com_rent_range_max = $property_meta['property_com_rent_range_max'][0];
		}
		
		$property_com_outgoings = '';
		if(isset($property_meta['property_com_outgoings']) && !empty($property_meta['property_com_outgoings'])) {
			$property_com_outgoings = $property_meta['property_com_outgoings'][0];
		}
		
		$property_com_plus_outgoings = '';
		if(isset($property_meta['property_com_plus_outgoings']) && !empty($property_meta['property_com_plus_outgoings'])) {
			$property_com_plus_outgoings = $property_meta['property_com_plus_outgoings'][0];
		}
		
		$property_com_return = '';
		if(isset($property_meta['property_com_return']) && !empty($property_meta['property_com_return'])) {
			$property_com_return = $property_meta['property_com_return'][0];
		}
		
		$property_com_lease_end_date = '';
		if(isset($property_meta['property_com_lease_end_date']) && !empty($property_meta['property_com_lease_end_date'])) {
			$property_com_lease_end_date = $property_meta['property_com_lease_end_date'][0];
		}
		
		$property_com_tenancy = '';
		if(isset($property_meta['property_com_tenancy']) && !empty($property_meta['property_com_tenancy'])) {
			$property_com_tenancy = $property_meta['property_com_tenancy'][0];
		}
		
		$property_com_further_options = '';
		if(isset($property_meta['property_com_further_options']) && !empty($property_meta['property_com_further_options'])) {
			$property_com_further_options = $property_meta['property_com_further_options'][0];
		}
		
		$property_commercial_category = '';
		$the_property_commercial_category = '';
		if(isset($property_meta['property_commercial_category']) && !empty($property_meta['property_commercial_category'])) {
			$property_commercial_category = $property_meta['property_commercial_category'][0];
			if ( $property_commercial_category != '' ) {
				//$the_property_commercial_category = implode(", ", $property_commercial_category);
				$the_property_commercial_category = '<div class="commercial-category">' . $the_property_commercial_category . '</div>';
			}
		}
		
		$property_com_car_spaces = '';
		if(isset($property_meta['property_com_car_spaces']) && !empty($property_meta['property_com_car_spaces'])) {
			$property_com_car_spaces = $property_meta['property_com_car_spaces'][0];
		}
		
		$property_com_parking_comments = '';
		if(isset($property_meta['property_com_parking_comments']) && !empty($property_meta['property_com_parking_comments'])) {
			$property_com_parking_comments = $property_meta['property_com_parking_comments'][0];
		}
		
		$property_com_zone = '';
		if(isset($property_meta['property_com_zone']) && !empty($property_meta['property_com_zone'])) {
			$property_com_zone = $property_meta['property_com_zone'][0];
		}
		
		$property_com_external_link = '';
		if(isset($property_meta['property_com_external_link']) && !empty($property_meta['property_com_external_link'])) {
			$property_com_external_link = $property_meta['property_com_external_link'][0];
		}
		
		$property_com_mini_web = '';
		if(isset($property_meta['property_com_mini_web']) && !empty($property_meta['property_com_mini_web'])) {
			$property_com_mini_web = $property_meta['property_com_mini_web'][0];
		}
		
		$d_car_spaces = ''; $l_car_spaces = '';
		if ( $property_com_car_spaces != '' && $property_com_car_spaces != 0 ) {
			$d_car_spaces = $property_com_car_spaces . ' '.__('Car Spaces', 'epl').', ';
			$l_car_spaces = '<li class="car-spaces">' . $property_com_car_spaces . ' '.__('Car Spaces', 'epl').'</li>';
		}
		$l_car_comments = '';
		if ($property_com_parking_comments != '' ) {
			$l_car_comments = '<p class="car-comments">' . $property_com_parking_comments . '</p>';
		}
		
		$property_tax = '';
		if(isset($property_meta['property_tax']) && !empty($property_meta['property_tax'])) {
			$property_tax = $property_meta['property_tax'][0];
		}
		
		if ( $property_tax == 'exempt' ) {
			$property_tax = ' '.__('Inc. GST', 'epl');
		} elseif ( $property_tax == 'inclusive' ) {
			$property_tax = ' '.__('Inc. GST', 'epl');
		} elseif ( $property_tax == 'exclusive' ) {
			$property_tax = '+ '.__('GST', 'epl');
		} else {
			$property_tax = '';
		}
		
		if ( 'sold' == $property_status ) {
			$price_plain_value = __('Sold', 'epl');
			$price_class = 'epl-price sold';
			$price = '<span class="page-price sold-status">'.__('Sold', 'epl').'</span>';
			$price_sticker = '<span class="status-sticker sold">'.__('Sold', 'epl').'</span>';
			$l_price = '<li class="status-sticker sold">'.__('Sold', 'epl').'</li>';
		} elseif ( $property_price != '' && $property_price_display == 1 ) {
			$price_plain_value = __('For Sale', 'epl').' ' . $property_price . $property_tax;
			$price = '<span class="page-price">'.__('For Sale', 'epl').' '. $property_price . $property_tax . '</span>';
			$l_price = '<li class="page-price">'.__('For Sale', 'epl').' '. $property_price . $property_tax . '</li>';		
		} else {
			$price = '<span class="page-price">'.__('POA', 'epl').'</span>';
			$price_plain_value = __('POA', 'epl');
			$l_price = '<li class="page-price">'.__('POA', 'epl').'</li>';
		}
		if ( $property_under_offer == 1 && 'sold' != $property_status) {
			$price_plain_value = __('Under Offer', 'epl');
			$price = '<div class="page-price under-offer-status">'.__('Under Offer', 'epl').'</div>';
			$price_sticker = '<span class="status-sticker under-offer">'.__('Under Offer', 'epl').'</span>';
			$l_price = '<li class="page-price under-offer-status">'.__('Under Offer', 'epl').'</li>';
		}
		if( $property_com_rent != 0 && $property_com_listing_types == 'both') {
			$price_com_rent = number_format($property_com_rent);
			$price_plain_value .= '</br>'.__('For Lease', 'epl').' $' . $price_com_rent . ' '.__('P.A.', 'epl');
			$price .= '<div class="epl-clear"></div><span class="page-price">'.__('For Lease', 'epl').' $' . $price_com_rent . ' '.__('P.A.', 'epl').'</span>';
			$l_price .= '<li class="page-price">'.__('For Lease', 'epl').' $' . $price_com_rent . ' '.__('P.A.', 'epl').'</li>';
		} elseif( $property_com_rent != 0 && $property_com_listing_types == 'lease') {
			$price_com_rent = number_format($property_com_rent);
			$price_plain_value = __('For Lease', 'epl').' $' . $price_com_rent . ' '.__('P.A.', 'epl');
			$price = '<span class="page-price">'.__('For Lease', 'epl').' $' . $price_com_rent . ' '.__('P.A.', 'epl').'</span>';
			$l_price = '<li class="page-price">'.__('For Lease', 'epl').' $' . $price_com_rent . ' '.__('P.A.', 'epl').'</li>';
		}
	}
	
	if(!isset($price_class)) {
		$price_class = '';
	}
	
	if(!isset($price_plain_value)) {
		$price_plain_value = '';
	}
	
	if(!isset($price)) {
		$price = '';
	}
	
	if(!isset($price_sticker)) {
		$price_sticker = '';
	}
	
	if(!isset($l_price)) {
		$l_price = '';
	}
	
	$return = array(
		'price_class'		=>	$price_class,
		'price_plain_value'	=>	$price_plain_value,
		'price'				=>	$price,
		'price_sticker'		=>	$price_sticker,
		'l_price'			=>	$l_price
	);
	return $return;
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
	return $epl_active_post_types;
}
/**
 * Return all the post types doesn't matter if selected as active or not by admin
 *
 * @since 1.0
 */
function epl_get_post_types() {
	$epl_post_types = array(
		'property'		=>	__('Property (Residential)', 'epl'),
		'land'			=>	__('Land', 'epl'),
		'rental'		=>	__('Rental', 'epl'),
		'rural'			=>	__('Rural', 'epl'),
		'commercial'	=>	__('Commercial', 'epl'),
		'commercial_land'=>	__('Commercial Land', 'epl'),
		'business'		=>	__('Business', 'epl'),
	);
	return $epl_post_types;
}
/**
 * Get Currencies
 *
 * @since 1.0
 * @return array $currencies A list of the available currencies
 */
function epl_get_currencies() {
	$currencies = array(
		'AUD'  => __( 'Australian Dollars (&#36;)', 'epl' ),
		'USD'  => __( 'US Dollars (&#36;)', 'epl' ),
		'EUR'  => __( 'Euros (&euro;)', 'epl' ),
		'GBP'  => __( 'Pounds Sterling (&pound;)', 'epl' ),
		'BRL'  => __( 'Brazilian Real (R&#36;)', 'epl' ),
		'CAD'  => __( 'Canadian Dollars (&#36;)', 'epl' ),
		'CZK'  => __( 'Czech Koruna', 'epl' ),
		'DKK'  => __( 'Danish Krone', 'epl' ),
		'HKD'  => __( 'Hong Kong Dollar (&#36;)', 'epl' ),
		'HUF'  => __( 'Hungarian Forint', 'epl' ),
		'ILS'  => __( 'Israeli Shekel (&#8362;)', 'epl' ),
		'JPY'  => __( 'Japanese Yen (&yen;)', 'epl' ),
		'MYR'  => __( 'Malaysian Ringgits', 'epl' ),
		'MXN'  => __( 'Mexican Peso (&#36;)', 'epl' ),
		'NZD'  => __( 'New Zealand Dollar (&#36;)', 'epl' ),
		'NOK'  => __( 'Norwegian Krone', 'epl' ),
		'PHP'  => __( 'Philippine Pesos', 'epl' ),
		'PLN'  => __( 'Polish Zloty', 'epl' ),
		'SGD'  => __( 'Singapore Dollar (&#36;)', 'epl' ),
		'SEK'  => __( 'Swedish Krona', 'epl' ),
		'CHF'  => __( 'Swiss Franc', 'epl' ),
		'TWD'  => __( 'Taiwan New Dollars', 'epl' ),
		'THB'  => __( 'Thai Baht (&#3647;)', 'epl' ),
		'INR'  => __( 'Indian Rupee (&#8377;)', 'epl' ),
		'TRY'  => __( 'Turkish Lira (&#8378;)', 'epl' ),
		'RIAL' => __( 'Iranian Rial (&#65020;)', 'epl' ),
		'RUB'  => __( 'Russian Rubles', 'epl' )
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
function epl_get_currency_position() {
	$epl_currency_position = '';
	
	global $epl_settings;
	if(!empty($epl_settings) && isset($epl_settings['currency_position'])) {
		$epl_currency_position = $epl_settings['currency_position'];
	}
	return apply_filters( 'epl_currency_position', $epl_currency_position );
}
function epl_get_thousands_separator() {
	$epl_thousands_separator = '';
	
	global $epl_settings;
	if(!empty($epl_settings) && isset($epl_settings['currency_thousands_separator'])) {
		$epl_thousands_separator = $epl_settings['currency_thousands_separator'];
	}
	return apply_filters( 'epl_thousands_separator', $epl_thousands_separator );
}
function epl_get_decimal_separator() {
	$epl_decimal_separator = '';
	
	global $epl_settings;
	if(!empty($epl_settings) && isset($epl_settings['currency_decimal_separator'])) {
		$epl_decimal_separator = $epl_settings['currency_decimal_separator'];
	}
	return apply_filters( 'epl_decimal_separator', $epl_decimal_separator );
}
function epl_currency_formatted_amount($price) {
	return epl_currency_filter( epl_format_amount( $price ) );
}
function epl_display_label_suburb( ) {
	$epl_display_label_suburb = '';
	
	global $epl_settings;
	if(!empty($epl_settings) && isset($epl_settings['label_suburb'])) {
		$epl_display_label_suburb = $epl_settings['label_suburb'];
	}
	return apply_filters( 'epl_display_label_suburb', $epl_display_label_suburb );
}
function epl_display_label_postcode() {
	$epl_display_label_postcode = '';
	
	global $epl_settings;
	if(!empty($epl_settings) && isset($epl_settings['label_postcode'])) {
		$epl_display_label_postcode = $epl_settings['label_postcode'];
	}
	return apply_filters( 'epl_display_label_postcode', $epl_display_label_postcode );
}
/**
 * Get EPL author meta
 *
 * @since 1.0
 * @return the list of author meta variables
 */
function epl_get_author_meta() {
	global $epl_author_meta_sent;
	if($epl_author_meta_sent) {
		return;
	}
	
	require_once EPL_PATH_LIB . 'templates/content/author-meta.php';
	$epl_author_meta_sent = true;
}
/**
 * Get EPL property address
 *
 * @since 1.0
 * @return the string for address
 */
function epl_get_property_address($post_ID='') {
	if($post_ID == '') {
		$post_ID = get_the_ID();
	}	
	$property_meta = epl_get_property_meta($post_ID);
	
	$address = '';
	
	if(isset($property_meta['property_address_street_number']) && !empty($property_meta['property_address_street_number'])) {
		$property_address_street_number = $property_meta['property_address_street_number'][0];
		if( $property_address_street_number != '' ) {
			$address .= $property_address_street_number . ", ";
		}
	}
	
	if(isset($property_meta['property_address_street']) && !empty($property_meta['property_address_street'])) {
		$property_address_street = $property_meta['property_address_street'][0];
		if( $property_address_street != '' ) {
			$address .= $property_address_street . ", ";
		}
	}
	
	if(isset($property_meta['property_address_suburb']) && !empty($property_meta['property_address_suburb'])) {
		$property_address_suburb = $property_meta['property_address_suburb'][0];
		if( $property_address_suburb != '' ) {
			$address .= $property_address_suburb . ", ";
		}
	}
	
	if(isset($property_meta['property_address_state']) && !empty($property_meta['property_address_state'])) {
		$property_address_state = $property_meta['property_address_state'][0];
		if( $property_address_state != '' ) {
			$address .= $property_address_state . ", ";
		}
	}
	
	if(isset($property_meta['property_address_postal_code']) && !empty($property_meta['property_address_postal_code'])) {
		$property_address_postal_code = $property_meta['property_address_postal_code'][0];
		if( $property_address_postal_code != '' ) {
			$address .= $property_address_postal_code . ", ";
		}
	}
	
	$address = trim($address); $address = trim($address, ","); $address = trim($address);
	return apply_filters('epl_get_property_address_filter', $address);
}
/**
 * Print EPL property address
 *
 * @since 1.0
 * @return the string for address
 */
function epl_the_property_address($post_ID='') {
	$address = epl_get_property_address($post_ID);
	echo apply_filters('epl_the_property_address_filter', $address);
}
/**
 * Get EPL property meta data
 *
 * @since 1.0
 * @return the string/list for values
 */
function epl_get_property_meta($post_ID='', $meta_key='') {
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
 * @return the string/list for values
 */
function epl_the_property_meta($post_ID='', $meta_key) {
	$meta_value = epl_get_property_meta($post_ID, $meta_key);
	echo apply_filters('epl_the_property_meta_filter', $meta_value);
}
/**
 * Taxonomy: Location Label
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
 * Custom Meta: House Categories
 *
 * @since 1.1
 * @return all the categories in array
 */
function epl_listing_load_meta_property_category() {
	$defaults = array(
		'House'					=>	__('House', 'epl'),
		'Unit'					=>	__('Unit', 'epl'),
		'Townhouse'				=>	__('Townhouse', 'epl'),
		'Villa'					=>	__('Villa', 'epl'),
		'Apartment'				=>	__('Apartment', 'epl'),
		'Flat'					=>	__('Flat', 'epl'),
		'Studio'				=>	__('Studio', 'epl'),
		'Warehouse'				=>	__('Warehouse', 'epl'),
		'DuplexSemi-detached'	=>	__('Duplex Semi-detached', 'epl'),
		'Alpine'				=>	__('Alpine', 'epl'),
		'AcreageSemi-rural'		=>	__('Acreage Semi-rural', 'epl'),
		'Retirement'			=>	__('Retirement', 'epl'),
		'BlockOfUnits'			=>	__('Block Of Units', 'epl'),
		'Terrace'				=>	__('Terrace', 'epl'),
		'ServicedApartment'		=>	__('Serviced Apartment', 'epl'),
		'Other'					=>	__('Other', 'epl')
	);
	return apply_filters( 'epl_listing_meta_property_category', $defaults );
}

/**
 * Custom Meta: Return Value of House Category
 *
 * @since 1.1
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
		'Commercial'	=>	__('Commercial', 'epl'),
		'Residential'	=>	__('Residential', 'epl')
	);
	return apply_filters( 'epl_listing_meta_land_category', $defaults );
}

/**
 * Custom Meta: Return Value of Land Category
 *
 * @since 1.1
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
		'Commercial Farming'	=>	__('Commercial Farming', 'epl'),
		'Land/Development'		=>	__('Land/Development', 'epl'),
		'Hotel/Leisure'			=>	__('Hotel/Leisure', 'epl'),
		'Industrial/Warehouse'	=>	__('Industrial/Warehouse', 'epl'),
		'Medical/Consulting'	=>	__('Medical/Consulting', 'epl'),
		'Offices'				=>	__('Offices', 'epl'),
		'Retail'				=>	__('Retail', 'epl'),
		'Showrooms/Bulky Goods'	=>	__('Showrooms/Bulky Goods', 'epl'),
		'Other'					=>	__('Other', 'epl')
	);
	return apply_filters( 'epl_listing_meta_commercial_category', $defaults );
}
 
/**
 * Custom Meta: Return Value of Commercial Category
 *
 * @since 1.1
 * @return all the categories in array
 */
function epl_listing_load_meta_commercial_category_value( $key ) {
	$array = epl_listing_load_meta_commercial_category();
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
		'Cropping'		=>	__('Cropping', 'epl'),
		'Dairy'			=>	__('Dairy', 'epl'),
		'Farmlet'		=>	__('Farmlet', 'epl'),
		'Horticulture'	=>	__('Horticulture', 'epl'),
		'Livestock'		=>	__('Livestock', 'epl'),
		'Viticulture'	=>	__('Viticulture', 'epl'),
		'MixedFarming'	=>	__('Mixed Farming', 'epl'),
		'Lifestyle'		=>	__('Lifestyle', 'epl'),
		'Other'			=>	__('Other', 'epl')
	);
	return apply_filters( 'epl_listing_meta_rural_category', $defaults );
}
 
  
/**
 * Custom Meta: Return Value of Rural Category
 *
 * @since 1.1
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
 * @since 1.2
 * @return formatted date
 */
function epl_feedsync_format_date( $date ) {
    $date_example = '2014-07-22-16:45:56';
     
    $format = '%Y-%m-%d-%H:%M:%S';
    $d = strptime($date, $format);
    $time = mktime($d['tm_hour'],$d['tm_min'],$d['tm_sec'],1 + $d['tm_mon'],$d['tm_mday'], 1900 + $d['tm_year']);
     
    $result = date("Y-m-d H:i:s" , $time );
     
    return $result;
}
