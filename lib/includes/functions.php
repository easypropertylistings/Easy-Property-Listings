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
		'property'			=>	__('Property (Residential)', 'epl'),
		'land'				=>	__('Land', 'epl'),
		'rental'			=>	__('Rental', 'epl'),
		'rural'				=>	__('Rural', 'epl'),
		'commercial'		=>	__('Commercial', 'epl'),
		'commercial_land'	=>	__('Commercial Land', 'epl'),
		'business'			=>	__('Business', 'epl'),
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
		'ZAR'  => __( 'South African Rand (R)', 'epl' ),
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
	return epl_currency_filter( epl_format_amount( $price , true ) );
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
 * Custom Meta: Location Label
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
		'House'				=>	__('House', 'epl'),
		'Unit'				=>	__('Unit', 'epl'),
		'Townhouse'			=>	__('Townhouse', 'epl'),
		'Villa'				=>	__('Villa', 'epl'),
		'Apartment'			=>	__('Apartment', 'epl'),
		'Flat'				=>	__('Flat', 'epl'),
		'Studio'			=>	__('Studio', 'epl'),
		'Warehouse'			=>	__('Warehouse', 'epl'),
		'DuplexSemi-detached'		=>	__('Duplex Semi-detached', 'epl'),
		'Alpine'			=>	__('Alpine', 'epl'),
		'AcreageSemi-rural'		=>	__('Acreage Semi-rural', 'epl'),
		'Retirement'			=>	__('Retirement', 'epl'),
		'BlockOfUnits'			=>	__('Block Of Units', 'epl'),
		'Terrace'			=>	__('Terrace', 'epl'),
		'ServicedApartment'		=>	__('Serviced Apartment', 'epl'),
		'Other'				=>	__('Other', 'epl')
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
		'Commercial'			=>	__('Commercial', 'epl'),
		'Residential'			=>	__('Residential', 'epl')
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
		'Commercial Farming'		=>	__('Commercial Farming', 'epl'),
		'Land/Development'		=>	__('Land/Development', 'epl'),
		'Hotel/Leisure'			=>	__('Hotel/Leisure', 'epl'),
		'Industrial/Warehouse'		=>	__('Industrial/Warehouse', 'epl'),
		'Medical/Consulting'		=>	__('Medical/Consulting', 'epl'),
		'Offices'			=>	__('Offices', 'epl'),
		'Retail'			=>	__('Retail', 'epl'),
		'Showrooms/Bulky Goods'		=>	__('Showrooms/Bulky Goods', 'epl'),
		'Other'				=>	__('Other', 'epl')
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
		'Cropping'			=>	__('Cropping', 'epl'),
		'Dairy'				=>	__('Dairy', 'epl'),
		'Farmlet'			=>	__('Farmlet', 'epl'),
		'Horticulture'			=>	__('Horticulture', 'epl'),
		'Livestock'			=>	__('Livestock', 'epl'),
		'Viticulture'			=>	__('Viticulture', 'epl'),
		'MixedFarming'			=>	__('Mixed Farming', 'epl'),
		'Lifestyle'			=>	__('Lifestyle', 'epl'),
		'Other'				=>	__('Other', 'epl')
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
 * Some imports set the current date instead of the date from the REAXML file. 
 * Usage in WP All Import Post Date field is: 
 * [epl_feedsync_format_date({./@modTime})]
 * 
 * @since 1.2
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
 * Offers presented on settings page
 *
 * @since 2.0
 */
function epl_admin_sidebar () {
	$service_banners = array(
		array(
			'url' => 'http://easypropertylistings.com.au/extensions/developer-license/',
			'img' => 'bannertwo.png',
			'alt' => __('Developer bundle Prospector for Easy Property Listings', 'epl')
		),
		array(
			'url' => 'http://easypropertylistings.com.au/extensions/prospector-license/',
			'img' => 'bannerone.png',
			'alt' => __('Prospector pack for Easy Property Listings', 'epl')
		),
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
 */
 function epl_render_html_fields ($field=array(),$val='') {
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
									'parent'		=>	0
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
					echo '<option value="" selected="selected">'.__($field['default'], 'epl').'</option>';
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
					
						echo '<option value="'.$k.'" '.$selected.'>'.__($v, 'epl').'</option>';
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
						if( in_array($k, $val) ) {
							$checked = 'checked="checked"';
						}
					}
					echo '<span class="epl-field-row"><input type="checkbox" name="'.$field['name'].'[]" id="'.$field['name'].'_'.$k.'" value="'.$k.'" '.$checked.' /> <label for="'.$field['name'].'_'.$k.'">'.__($v, 'epl').'</label></span>';
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
					echo '<span class="epl-field-row"><input type="radio" name="'.$field['name'].'" id="'.$field['name'].'_'.$k.'" value="'.$k.'" '.$checked.' /> <label for="'.$field['name'].'_'.$k.'">'.__($v, 'epl').'</label></span>';
				}
			}
			break;

		case 'image':
			if($val != '') {
				$img = $val;
			} else {
				$img = plugin_dir_url( __FILE__ ).'images/no_image.jpg';
			}
			echo '
				<div class="epl-media-row">
					<input type="text" name="'.$field['name'].'" id="'.$field['name'].'" value="'.stripslashes($val).'" />
					&nbsp;&nbsp;<input type="button" name="epl_upload_button" class="button" value="'.__('Add File', 'epl').'" />
					&nbsp;&nbsp;<img src="'.$img.'" alt="" />
					<div class="epl-clear"></div>
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
		
		case 'url':
			echo '<input type="text" name="'.$field['name'].'" id="'.$field['name'].'" value="'.stripslashes($val).'" class="validate[custom[url]]" />';
			break;
		
		default:
			$atts = '';
			if(isset($field['maxlength']) &&  $field['maxlength'] > 0) {
				$atts = ' maxlength="'.$field['maxlength'].'"';
			}
			echo '<input type="'.$field['type'].'" name="'.$field['name'].'" id="'.$field['name'].'" value="'.stripslashes($val).'" '.$atts.' />';
	}

	if( isset($field['geocoder']) ) {
		if( $field['geocoder'] == 'true' ) {
			echo '<span class="epl-geocoder-button"></span>';
		}
		
		if( !empty($val) ) {
			echo '<iframe width="100%" height="200" frameborder="0" scrolling="no" src="//maps.google.com/?q='.stripslashes($val).'&output=embed&z=14" style="margin:5px 0 0 0;"></iframe>';
		}
	}
	
	if(isset($field['help'])) {
		$field['help'] = trim($field['help']);
		if(!empty($field['help'])) {
			echo '<span class="epl-help-text">'.__($field['help'], 'epl').'</span>';
		}
	}
 	
 }
