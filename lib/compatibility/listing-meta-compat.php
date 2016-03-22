<?php
/**
 * EPL Listing Meta Fields Compatibility
 *
 * @package     EPL
 * @subpackage  Compatibility/Functions
 * @copyright   Copyright (c) 2014, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Function to retrieve property or rental custom fields.
 *
 * @since 1.0
 */

//Global
global $post;
$epl_settings = epl_settings();
// Store All Meta
$meta = get_post_custom();
$post_type = get_post_type();
$property_post_type = $post_type;
$d_bond = '';
if(!empty($epl_settings) && isset($epl_settings['display_bond'])) {
	$d_bond = $epl_settings['display_bond'];
}
// Property Heading
$property_heading = get_the_title();
	if(isset($meta['property_heading'])) {
		if(isset($meta['property_heading'][0])) {
			$property_heading = $meta['property_heading'][0];
			$the_property_heading = $property_heading;
		}
	}
// Process Status
$property_status = '';
	if(isset($meta['property_status'])) {
		if(isset($meta['property_status'][0])) {
			$property_status = $meta['property_status'][0];
		}
	}

// Process Property Features Taxonomy
if ( taxonomy_exists('tax_feature') ) {
	global $post;
	$property_feature_taxonomy = get_the_term_list($post->ID, 'tax_feature', '<li>', '</li><li>', '</li>' );
}
// Authority
if(isset($meta['property_authority'])) {
	if(isset($meta['property_authority'][0])) {
		$property_authority = $meta['property_authority'][0];
	}
}
$property_date_available = '';
if(isset($meta['property_date_available'])) {
	if(isset($meta['property_date_available'][0])) {
		$property_date_available = $meta['property_date_available'][0];
	}
}
// Property inspection Times
$property_inspection_times = '';
	if(isset($meta['property_inspection_times'])) {
		if(isset($meta['property_inspection_times'][0])) {
			$property_inspection_times = $meta['property_inspection_times'][0];
		}
	}
// Suburb Profile
$profiles = get_the_terms( $post->ID, 'location' );
if ( is_array ($profiles) ){
	global $post;
	foreach($profiles as $profile){
	$profile->slug; }
	$suburb_name = $profile->name;
}
// Sale Property Specifics
if ( 'rental' != $post_type ) {
	$property_price = '';
	if(isset($meta['property_price'])) {
		if(isset($meta['property_price'][0])) {
			$property_price_search = $meta['property_price'][0];
		}
	}
	$property_price_view = '';
	if(isset($meta['property_price_view'])) {
		if(isset($meta['property_price_view'][0])) {
			$property_price_view = $meta['property_price_view'][0];
		}
	}
	$property_under_offer = '';
	if(isset($meta['property_under_offer'])) {
		if(isset($meta['property_under_offer'][0])) {
			$property_under_offer = $meta['property_under_offer'][0];
		}
	}
	$property_price_display = '';
	if(isset($meta['property_price_display'])) {
		if(isset($meta['property_price_display'][0])) {
			$property_price_display = $meta['property_price_display'][0];
		}
	}
	$property_auction = '';
	if(isset($meta['property_auction'])) {
		if(isset($meta['property_auction'][0])) {
			$property_auction = $meta['property_auction'][0];
		}
	}
	// Property Price Display
	if ( $property_price_search == '') {
		$property_price = '';
	} elseif( $property_price_view != '' ) {
		$property_price = $meta['property_price_view'][0];
	} else {
		$property_price_search = $meta['property_price'][0];
		$property_price = epl_currency_formatted_amount( $property_price_search );
	}
}
// Rental / Leased Property Specifics
if ( 'rental' == $post_type ) {
	if(isset($meta['property_rent_display'])) {
		if(isset($meta['property_rent_display'][0])) {
			$property_rent_display = $meta['property_rent_display'][0];
		}
	}
	$property_date_available = '';
	if(isset($meta['property_date_available'])) {
		if(isset($meta['property_date_available'][0])) {
			$property_date_available = $meta['property_date_available'][0];
		}
	}

	$property_rent_period = '';
	if(isset($meta['property_rent_period'])) {
		if(isset($meta['property_rent_period'][0])) {
			$property_rent_period = $meta['property_rent_period'][0];
		}
	}

	// Rental Price XE Format
	$property_rent = $meta['property_rent'][0];
		if ( $property_rent !='' ) {
			$property_rent = epl_currency_formatted_amount($property_rent);
		}
	// Rental Bond
	$property_bond = $meta['property_bond'][0];
		if ( $property_bond !='' ) {
			$property_bond = epl_currency_formatted_amount($property_bond);
		}
}
if ( 'land' == $post_type || 'commercial_land' == $post_type ) {
	$property_land_category = '';
	if(isset($meta['property_land_category'])) {
		if(isset($meta['property_land_category'][0])) {
			$property_land_category_id = $meta['property_land_category'][0];
			$property_land_category = epl_listing_meta_land_category_value( $property_land_category_id );
		}
	}
}
if(isset($meta['property_unique_id'])) {
	if(isset($meta['property_unique_id'][0])) {
		$property_unique_id = $meta['property_unique_id'][0];
	}
}
//address
if(isset($meta['property_address_street_number'])) {
	if(isset($meta['property_address_street_number'][0])) {
		$property_address_street_number = $meta['property_address_street_number'][0];
	}
}
if(isset($meta['property_address_street'])) {
	if(isset($meta['property_address_street'][0])) {
		$property_address_street = $meta['property_address_street'][0];
	}
}
if(isset($meta['property_address_suburb'])) {
	if(isset($meta['property_address_suburb'][0])) {
		$property_address_suburb = $meta['property_address_suburb'][0];
	}
}
if(isset($meta['property_address_state'])) {
	if(isset($meta['property_address_state'][0])) {
		$property_address_state = $meta['property_address_state'][0];
	}
}
if(isset($meta['property_address_postal_code'])) {
	if(isset($meta['property_address_postal_code'][0])) {
		$property_address_postal_code = $meta['property_address_postal_code'][0];
	}
}
if(isset($meta['property_address_display'])) {
	if(isset($meta['property_address_display'][0])) {
		$property_address_display = $meta['property_address_display'][0];
	} else {
		$property_address_display = 0;
	}
} else {
		$property_address_display = 0;
	}

$coords = ',';
if(isset($meta['property_address_coordinates'])) {
	if(isset($meta['property_address_coordinates'][0])) {
		$property_address_coordinates = $meta['property_address_coordinates'][0];
	}
}
// Format Address
$property_address_street 	= $property_address_street_number . ' ' . $property_address_street;
$property_address 		= $property_address_street . ', ' . $property_address_suburb;
$price_sticker	 		= '';
$property_category 		= '';
$property_bedrooms 		= '';
$property_bathrooms 		= '';
$property_rooms 		= '';
$property_toilet 		= '';
$property_new_construction 	= '';
$property_garage 		= '';
$property_carport 		= '';
$property_parking 		= '';
$property_air_conditioning 	= '';
$property_pool 			= '';
$property_security_system 	= '';
if ( 'property' == $post_type || 'rental' == $post_type || 'rural' == $post_type ) {
	//house features
	if(isset($meta['property_category'])) {
		if(isset($meta['property_category'][0])) {
			$property_category_id = $meta['property_category'][0];
			$property_category = epl_listing_meta_property_category_value( $property_category_id );
		}
	}
	if(isset($meta['property_bedrooms'])) {
		if(isset($meta['property_bedrooms'][0])) {
			$property_bedrooms = $meta['property_bedrooms'][0];
		}
	}
	if(isset($meta['property_bathrooms'])) {
		if(isset($meta['property_bathrooms'][0])) {
			$property_bathrooms = $meta['property_bathrooms'][0];
		}
	}
	if(isset($meta['property_rooms'])) {
		if(isset($meta['property_rooms'][0])) {
			$property_rooms = $meta['property_rooms'][0];
		}
	}
	if(isset($meta['property_toilet'])) {
		if(isset($meta['property_toilet'][0])) {
			$property_toilet = $meta['property_toilet'][0];
		}
	}
	if(isset($meta['property_new_construction'])) {
		if(isset($meta['property_new_construction'][0])) {
			$property_new_construction = $meta['property_new_construction'][0];
		}
	}
	if(isset($meta['property_garage'])) {
		if(isset($meta['property_garage'][0])) {
			$property_garage = $meta['property_garage'][0];
		}
	}

	if(isset($meta['property_carport'])) {
		if(isset($meta['property_carport'][0])) {
			$property_carport = $meta['property_carport'][0];
		}
	}

	$property_parking = $property_garage + $property_carport;

	if(isset($meta['property_air_conditioning'])) {
		if(isset($meta['property_air_conditioning'][0])) {
			$property_air_conditioning = $meta['property_air_conditioning'][0];
		}
	}

	if(isset($meta['property_pool'])) {
		if(isset($meta['property_pool'][0])) {
			$property_pool = $meta['property_pool'][0];
		}
	}

	if(isset($meta['property_security_system'])) {
		if(isset($meta['property_security_system'][0])) {
			$property_security_system = $meta['property_security_system'][0];
		}
	}
}
// Land Area
$property_land_value = '';
if(isset($meta['property_land_area'])) {
	if(isset($meta['property_land_area'][0])) {
		$property_land_value = $meta['property_land_area'][0];
	}
}
$property_land = '';
if(isset($meta['property_land_area'])) {
	if(isset($meta['property_land_area'][0])) {
		$property_land = $meta['property_land_area'][0];
	}
}
$property_land_unit = '';
if(isset($meta['property_land_area_unit'])) {
	if(isset($meta['property_land_area_unit'][0])) {
		$property_land_unit = $meta['property_land_area_unit'][0];
	}
}
if ( $property_land_unit == 'squareMeter' ) {
	$property_land_unit = __('sqm' , 'easy-property-listings');
}
$property_land = $property_land . ' ' . $property_land_unit;
// Building Area
$building_area_value = '';
if(isset($meta['property_building_area'])) {
	if(isset($meta['property_building_area'][0])) {
		$building_area_value = $meta['property_building_area'][0];
	}
}
$building_area = '';
if(isset($meta['property_building_area'])) {
	if(isset($meta['property_building_area'][0])) {
		$building_area = $meta['property_building_area'][0];
	}
}
$building_area_unit = '';
if(isset($meta['property_building_area_unit'])) {
	if(isset($meta['property_building_area_unit'][0])) {
		$building_area_unit = $meta['property_building_area_unit'][0];
	}
}
if ( $building_area_unit == 'squareMeter') {
	$building_area_unit = 'm²';
}
$building_area = $building_area . ' ' . $building_area_unit;
// Property Other Details
$property_featured = '';
if(isset($meta['property_featured'])) {
	if(isset($meta['property_featured'][0])) {
		$property_featured = $meta['property_featured'][0];
	}
}
$property_video_url = '';
if(isset($meta['property_video_url'])) {
	if(isset($meta['property_video_url'][0])) {
		$property_video_url = $meta['property_video_url'][0];
	}
}
$home_open_label = '';
if(!empty($epl_settings) && isset($epl_settings['label_home_open'])) {
	$home_open_label = $epl_settings['label_home_open'];
}
// Pricing
if ( 'property' == $post_type || 'land' == $post_type || 'rural' == $post_type) {	// Property Pricing and Specialised Meta Fields
	// Property Meta
	if ( 'sold' == $property_status ) {
		$price_class = 'epl-price sold';
		$price_plain_value = __('Sold', 'easy-property-listings');
		$price = '<span class="page-price sold-status">'.__('Sold', 'easy-property-listings').'</span>';
		$price_sticker = '<span class="status-sticker sold">'.__('Sold', 'easy-property-listings').'</span>';
		$l_price = '<li class="page-price sold-status">'.__('Sold', 'easy-property-listings').'</li>';
	} elseif ( '' != $property_price && 'yes' == $property_price_display ) {	// Property
		$price_plain_value = $property_price;
		$price = '<span class="page-price">'. $property_price . '</span>';
		$l_price = '<li class="page-price">'. $property_price . '</li>';
		if ( $property_inspection_times != '' ){
			$price_sticker = '<span class="status-sticker open">'.$home_open_label.'</span>';
		}
	} else {
		$price_plain_value = '';
		if(!empty($epl_settings) && isset($epl_settings['label_poa'])) {
			$price_plain_value = $epl_settings['label_poa'];
		}

		$price = '<span class="page-price">' . $price_plain_value . '</span>';
		$l_price = '<li class="page-price">' . $price_plain_value . '</li>';
		if ( $property_inspection_times != '' ){
			$price_sticker = '<span class="status-sticker open">'.$home_open_label.'</span>';
		}
	}

	if ( 'yes' == $property_under_offer && 'sold' != $property_status) {	// Under Offer
		$price_plain_value = __('Under Offer', 'easy-property-listings');
		$price = '<span class="page-price under-offer-status">'.__('Under Offer', 'easy-property-listings').'</span>';
		$price_sticker = '<span class="status-sticker under-offer">'.__('Under Offer', 'easy-property-listings').'</span>';
		$l_price = '<li class="page-price under-offer-status">'.__('Under Offer', 'easy-property-listings').'</li>';
	}

	// Property Under Offer Tag
	if ($property_price != '' && 'yes' == $property_under_offer ) {
		$property_under_offer_tag = '<span class="status property-under-offer"><strong>'.__('Under Offer', 'easy-property-listings').'</strong></span>';
	}


} elseif ( 'rental' == $post_type ) {	// Rental Pricing
	// Rental Meta
	if( '' != $property_rent && 'yes' == $property_rent_display && 'leased' != $property_status ) {
		$price_plain_value = $property_rent . '/' . $property_rent_period;
		$price = '
			<span class="page-price-rent">
				<span class="page-price" style="margin-right:0;">'. $property_rent . '</span>
				<span class="rent-period">/' . $property_rent_period . '</span>
			</span>
		';

		$l_price = '
			<li class="page-price-rent">
				<span class="page-price" style="margin-right:0;">'. $property_rent . '</span>
				<span class="rent-period">/' . $property_rent_period . '</span>
			</li>
		';
		if ($property_bond != '' && $d_bond == 1) {
			$price_plain_value .= $property_bond . ' bond';
			$price .= '<span class="bond">' . $property_bond . ' bond</span>';
		}
		if ( $property_inspection_times != '' ){
			$price_sticker = '<span class="status-sticker open">'.$home_open_label.'</span>';
		}
	} elseif ( $property_status == 'leased') {
		$price_plain_value = __('Leased', 'easy-property-listings');
		$price = '<span class="page-price sold-status">'.__('Leased', 'easy-property-listings').'</span>';
		$price_sticker = '<span class="status-sticker leased">'.__('Leased', 'easy-property-listings').'</span>';
		$l_price = '<li class="page-price sold-status">'.__('Leased', 'easy-property-listings').'</li>';
	} else {
		$price_plain_value = __('TBA', 'easy-property-listings');
		$price = '<span class="page-price">'.__('TBA', 'easy-property-listings').'</span>';
		if ( $property_inspection_times != '' ){
			$price_sticker = '<span class="status-sticker open">'.$home_open_label.'</span>';
		}
	}
} elseif ( 'commercial' == $post_type || 'business' == $post_type || 'commercial_land' == $post_type) { // Commercial Pricing
	// Commercial Meta

	$property_com_listing_type = '';
	if(isset($meta['property_com_listing_type'])) {
		if(isset($meta['property_com_listing_type'][0])) {
			$property_com_listing_type = $meta['property_com_listing_type'][0];
		}
	}

	$property_com_display_suburb = '';
	if(isset($meta['property_com_display_suburb'])) {
		if(isset($meta['property_com_display_suburb'][0])) {
			$property_com_display_suburb = $meta['property_com_display_suburb'][0];
		}
	}

	$property_com_rent = '';
	if(isset($meta['property_com_rent'])) {
		if(isset($meta['property_com_rent'][0])) {
			$property_com_rent = $meta['property_com_rent'][0];
		}
	}
	$property_com_authority = '';
	if(isset($meta['property_com_authority'])) {
		if(isset($meta['property_com_authority'][0])) {
			$property_com_authority = $meta['property_com_authority'][0];
		}
	}

	$property_com_exclusivity = '';
	if(isset($meta['property_com_exclusivity'])) {
		if(isset($meta['property_com_exclusivity'][0])) {
			$property_com_exclusivity = $meta['property_com_exclusivity'][0];
		}
	}

	// Rental Price XE Format
	if ( $property_com_rent !='' ) {
		$property_com_rent = epl_currency_formatted_amount($property_com_rent);
	}

	// Commercial / Business Features
	$property_com_is_multiple = '';
	if(isset($meta['property_com_is_multiple'])) {
		if(isset($meta['property_com_is_multiple'][0])) {
			$property_com_is_multiple = $meta['property_com_is_multiple'][0];
		}
	}
	$property_com_rent_range_min = '';
	if(isset($meta['property_com_rent_range_min'])) {
		if(isset($meta['property_com_rent_range_min'][0])) {
			$property_com_rent_range_min = $meta['property_com_rent_range_min'][0];
		}
	}

	$property_com_rent_range_max = '';
	if(isset($meta['property_com_rent_range_max'])) {
		if(isset($meta['property_com_rent_range_max'][0])) {
			$property_com_rent_range_max = $meta['property_com_rent_range_max'][0];
		}
	}
	$property_com_outgoings = '';
	if(isset($meta['property_com_outgoings'])) {
		if(isset($meta['property_com_outgoings'][0])) {
			$property_com_outgoings = $meta['property_com_outgoings'][0];
		}
	}
	$property_com_plus_outgoings = '';
	if(isset($meta['property_com_plus_outgoings'])) {
		if(isset($meta['property_com_plus_outgoings'][0])) {
			$property_com_plus_outgoings = $meta['property_com_plus_outgoings'][0];
		}
	}
	$property_com_return = '';
	if(isset($meta['property_com_return'])) {
		if(isset($meta['property_com_return'][0])) {
			$property_com_return = $meta['property_com_return'][0];
		}
	}

	$property_com_lease_end_date = '';
	if(isset($meta['property_com_lease_end_date'])) {
		if(isset($meta['property_com_lease_end_date'][0])) {
			$property_com_lease_end_date = $meta['property_com_lease_end_date'][0];
		}
	}
	$property_com_tenancy = '';
	if(isset($meta['property_com_tenancy'])) {
		if(isset($meta['property_com_tenancy'][0])) {
			$property_com_tenancy = $meta['property_com_tenancy'][0];
		}
	}

	$property_com_further_options = '';
	if(isset($meta['property_com_further_options'])) {
		if(isset($meta['property_com_further_options'][0])) {
			$property_com_further_options = $meta['property_com_further_options'][0];
		}
	}
	$property_com_highlight_1 = '';
	if(isset($meta['property_com_highlight_1'])) {
		if(isset($meta['property_com_highlight_1'][0])) {
			$property_com_highlight_1 = $meta['property_com_highlight_1'][0];
		}
	}
	$property_com_highlight_2 = '';
	if(isset($meta['property_com_highlight_2'])) {
		if(isset($meta['property_com_highlight_2'][0])) {
			$property_com_highlight_2 = $meta['property_com_highlight_2'][0];
		}
	}
	$property_com_highlight_3 = '';
	if(isset($meta['property_com_highlight_3'])) {
		if(isset($meta['property_com_highlight_3'][0])) {
			$property_com_highlight_3 = $meta['property_com_highlight_3'][0];
		}
	}

	$property_com_car_spaces = '';
	if(isset($meta['property_com_car_spaces'])) {
		if(isset($meta['property_com_car_spaces'][0])) {
			$property_com_car_spaces = $meta['property_com_car_spaces'][0];
		}
	}
	$property_com_parking_comments = '';
	if(isset($meta['property_com_parking_comments'])) {
		if(isset($meta['property_com_parking_comments'][0])) {
			$property_com_parking_comments = $meta['property_com_parking_comments'][0];
		}
	}

	$property_com_zone = '';
	if(isset($meta['property_com_zone'])) {
		if(isset($meta['property_com_zone'][0])) {
			$property_com_zone = $meta['property_com_zone'][0];
		}
	}
	$property_commercial_category = '';
	if(isset($meta['property_commercial_category'])) {
		if(isset($meta['property_commercial_category'][0])) {
			$property_commercial_category_id = $meta['property_commercial_category'][0];
			$property_commercial_category = epl_listing_load_meta_commercial_category_value( $property_commercial_category_id );
		}
	}
	$the_property_commercial_category = '';
	if ( $property_commercial_category != '' ) {
		$the_property_commercial_category = '<div class="commercial-category">' . $property_commercial_category . '</div>';
		$l_com_cat = '<li class="commercial-category">' . $property_commercial_category . ' '.__('Commercial Category', 'easy-property-listings').'</li>';
	}

	if ($property_com_car_spaces != 0 ) {
		$l_car_spaces = '<li class="car-spaces">' . $property_com_car_spaces . ' '.__('Car Spaces', 'easy-property-listings').'</li>';
	}
	if ($property_com_parking_comments != '' ) {
		$l_car_comments = '<li class="car-comments">' . $property_com_parking_comments . '</li>';
	}

	$property_tax = '';
	if( isset($meta['property_tax']) ) {
		if( isset($meta['property_tax'][0]) ) {
			$property_tax = $meta['property_tax'][0];
		}
	}
	if ( $property_tax == 'exempt' ) {
		$property_tax = ' '.__('Inc. GST', 'easy-property-listings');
	} elseif ( $property_tax == 'inclusive' ) {
		$property_tax = ' '.__('Inc. GST', 'easy-property-listings');
	} elseif ( $property_tax == 'exclusive' ) {
		$property_tax = ' +  '.__('GST', 'easy-property-listings');
	} else {
		$property_tax = '';
	}

	if ( 'sold' == $property_status ) {	// Commercial Pricing
		$price_plain_value = __('Sold', 'easy-property-listings');
		$price_class = 'epl-price sold';
		$price = '<span class="page-price sold-status">'.__('Sold', 'easy-property-listings').'</span>';
		$price_sticker = '<span class="status-sticker sold">'.__('Sold', 'easy-property-listings').'</span>';
		$l_price = '<li class="status-sticker sold">'.__('Sold', 'easy-property-listings').'</li>';
	} elseif ( '' != $property_price && 'yes' == $property_price_display ) {	// Property
		$price_plain_value = __('For Sale', 'easy-property-listings').' ' . $property_price . $property_tax;
		$price = '<span class="page-price">'.__('For Sale', 'easy-property-listings').' '. $property_price . $property_tax . '</span>';
		$l_price = '<li class="page-price">'.__('For Sale', 'easy-property-listings').' '. $property_price . $property_tax . '</li>';
	} else {
		$price_plain_value = '';
		if(!empty($epl_settings) && isset($epl_settings['label_poa'])) {
			$price_plain_value = $epl_settings['label_poa'];
		}

		$price = '<span class="page-price">' . $price_plain_value . '</span>';
		$l_price = '<li class="page-price">' . $price_plain_value . '</li>';
	}

	// Under Offer
	if ( 'yes' == $property_under_offer && 'sold' != $property_status) {
		$price_plain_value = __('Under Offer', 'easy-property-listings');
		$price = '<div class="page-price under-offer-status">'.__('Under Offer', 'easy-property-listings').'</div>';
		$price_sticker = '<span class="status-sticker under-offer">'.__('Under Offer', 'easy-property-listings').'</span>';
		$l_price = '<li class="page-price under-offer-status">'.__('Under Offer', 'easy-property-listings').'</li>';
	}
	if( $property_com_rent != '' && $property_com_listing_type == 'both') {
		$price_plain_value .= '</br>'.__('For Lease', 'easy-property-listings').' ' . $property_com_rent . ' '.__('P.A.', 'easy-property-listings');
		$price .= '<div class="epl-clear"></div><span class="page-price">'.__('For Lease', 'easy-property-listings').' ' . $property_com_rent . ' '.__('P.A.', 'easy-property-listings').'</span>';
		$l_price .= '<li class="page-price">'.__('For Lease', 'easy-property-listings').' ' . $property_com_rent . ' '.__('P.A.', 'easy-property-listings').'</li>';
	} elseif( $property_com_rent != '' && $property_com_listing_type == 'lease') {
		$price_plain_value = ''.__('For Lease', 'easy-property-listings').' ' . $property_com_rent . ' '.__('P.A.', 'easy-property-listings');
		$price = '<span class="page-price">'.__('For Lease', 'easy-property-listings').' ' . $property_com_rent . ' '.__('P.A.', 'easy-property-listings').'</span>';
		$l_price = '<li class="page-price">'.__('For Lease', 'easy-property-listings').' ' . $property_com_rent . ' '.__('P.A.', 'easy-property-listings').'</li>';
	}

	// Property Under Offer Tag
	if ($property_price != '' && 'yes' == $property_under_offer ) {
		$property_under_offer_tag = '<span class="status property-under-offer"><strong>'.__('Under Offer', 'easy-property-listings').'</strong></span>';
	}

}
// END Property Pricing
// Link to the Suburb Taxonomy adding a $post_type filter
$locations = get_the_terms( $post->ID, 'location' );
if ( $locations != '' ) {
	foreach( $locations as $location ) {
		$location_link = get_bloginfo( 'url' ) . '/property-suburb/' . $location->slug . '?post_type=' . $post_type;
		$location_name = $location->name;
		$the_suburb_term = '<span class="suburb-link"><a href="' . $location_link . '">' .$location_name . '</a></span>';
	}
}
// DISPLAY Features
// Property Icons / Dynamic Description
if(isset($property_bedrooms) && $property_bedrooms != 0) {
	$i_bed = '<span title="'.__('Bedrooms', 'easy-property-listings').'" class="icon beds"><span class="icon-value">'. $property_bedrooms . '</span></span>';
	$d_bed = $property_bedrooms . ' '.__('bed', 'easy-property-listings').' ';
	$l_bed = '<li class="bedrooms">' . $property_bedrooms . ' '.__('bed', 'easy-property-listings').'</li>';
}
if(isset($property_bathrooms) && $property_bathrooms != 0) {
	$i_bath = '<span title="'.__('Bathrooms', 'easy-property-listings').'" class="icon bath"><span class="icon-value">' . $property_bathrooms  . '</span></span>';
	$d_bath = $property_bathrooms . ' '.__('bath', 'easy-property-listings').' ';
	$l_bath = '<li class="bathrooms">' . $property_bathrooms . ' '.__('bath', 'easy-property-listings').'</li>';
}
if(isset($property_rooms) && $property_rooms != 0) {
	$i_bath = '<span title="'.__('Rooms', 'easy-property-listings').'" class="icon rooms"><span class="icon-value">' . $property_rooms  . '</span></span>';
	$d_bath = $property_rooms . ' '.__('rooms', 'easy-property-listings').' ';
	$l_bath = '<li class="rooms">' . $property_rooms . ' '.__('rooms', 'easy-property-listings').'</li>';
}
if(isset($property_parking) && $property_parking != 0) {
	$i_park = '<span title="'.__('Parking Spaces', 'easy-property-listings').'" class="icon parking"><span class="icon-value">' . $property_parking . '</span></span>';
}
if( isset($property_air_conditioning) && ($property_air_conditioning == 1 || $property_air_conditioning == 'yes') ) {
	$i_air = '<span title="'.__('Air Conditioning', 'easy-property-listings').'" class="icon air"></span>';
	$l_air = '<li class="air">'.__('Air conditioning', 'easy-property-listings').'</li>';
}
if( isset($property_pool) && ($property_pool == 1 || $property_pool == 'yes') ) {
	$i_pool = '<span title="'.__('Pool', 'easy-property-listings').'" class="icon pool"></span>';
	$l_pool = '<li class="pool">'.__('Pool', 'easy-property-listings').'</li>';
}
if(isset($property_category) && $property_category != '') {
	$l_htype = '<li class="type">' . $property_category . '</li>';
}
if(isset($property_toilet) && $property_toilet != 0) {
	$l_toilet = '<li class="toilet">' . $property_toilet . ' '.__('Toilet', 'easy-property-listings').'</li>';
}
if( isset($property_new_construction) && ($property_new_construction == 1 || $property_new_construction == 'yes') ) {
	$l_new_construction = '<li class="new">'.__('New Construction', 'easy-property-listings').'</li>';
}
if( isset($property_security_system) && ($property_security_system == 1 || $property_security_system == 'yes') ) {
	$l_alarm = '<li class="alarm">'.__('Alarm system', 'easy-property-listings').'</li>';
}
if(isset($property_garage) && $property_garage != 0) {
	$l_garage = '<li class="garage">' . $property_garage . ' '.__('Garage', 'easy-property-listings').'</li>';
}
if(isset($property_carport) && $property_carport != 0) {
	$l_carport = '<li class="carport">' . $property_carport . ' '.__('Carport', 'easy-property-listings').'</li>';
} else {
	$l_carport = '';
}
if(isset($property_land_value) && $property_land_value != '') {
	$l_land = '<li class="land-size">'.__('Land is', 'easy-property-listings').' ' . $property_land . '</li>';
}
if(isset($building_area_value) && $building_area_value != '') {
	$l_area = '<li class="building-size">'.__('Floor Area is', 'easy-property-listings').' ' . $building_area . '</li>';
}
// Additional Features
if ( 'property' == $post_type || 'rental' == $post_type || 'rural' == $post_type) {
	// Remote Garage
	$property_remote_garage = '';
	if(isset($meta['property_remote_garage'])) {
		if(isset($meta['property_remote_garage'][0])) {
			$property_remote_garage = $meta['property_remote_garage'][0];
			if( isset($property_remote_garage) && ($property_remote_garage == 1 || $property_remote_garage == 'yes') ) {
				$l_r_garage = '<li class="remote_garage">'.__('Remote Garage', 'easy-property-listings').'</li>';
			}
		}
	}
	// Secure Parking
	$property_secure_parking = '';
	if(isset($meta['property_secure_parking'])) {
		if(isset($meta['property_secure_parking'][0])) {
			$property_secure_parking = $meta['property_secure_parking'][0];
			if( isset($property_secure_parking) && ($property_secure_parking == 1 || $property_secure_parking == 'yes') ) {
				$l_s_park = '<li class="secure_parking">'.__('Secure Parking', 'easy-property-listings').'</li>';
			}
		}
	}
	// Study
	$property_study = '';
	if(isset($meta['property_study'])) {
		if(isset($meta['property_study'][0])) {
			$property_study = $meta['property_study'][0];
			if( isset($property_study) && ($property_study == 1 || $property_study == 'yes') ) {
				$l_study = '<li class="study">'.__('Study', 'easy-property-listings').'</li>';
			}
		}
	}
	// Dishwasher
	$property_dishwasher = '';
	if(isset($meta['property_dishwasher'])) {
		if(isset($meta['property_dishwasher'][0])) {
			$property_dishwasher = $meta['property_dishwasher'][0];
			if( isset($property_dishwasher) && ($property_dishwasher == 1 || $property_dishwasher == 'yes') ) {
				$l_dish = '<li class="dishwasher">'.__('Dishwasher', 'easy-property-listings').'</li>';
			}
		}
	}
	// Built In Robes
	$property_built_in_robes = '';
	if(isset($meta['property_built_in_robes'])) {
		if(isset($meta['property_built_in_robes'][0])) {
			$property_built_in_robes = $meta['property_built_in_robes'][0];
			if( isset($property_built_in_robes) && ($property_built_in_robes == 1 || $property_built_in_robes == 'yes') ) {
				$l_b_i_r = '<li class="built_in_robes">'.__('Built In Robes', 'easy-property-listings').'</li>';
			}
		}
	}
	// Gym
	$property_gym = '';
	if(isset($meta['property_gym'])) {
		if(isset($meta['property_gym'][0])) {
			$property_gym = $meta['property_gym'][0];
			if( isset($property_gym) && ($property_gym == 1 || $property_gym == 'yes') ) {
				$l_gym = '<li class="gym">'.__('Gym', 'easy-property-listings').'</li>';
			}
		}
	}
	// Workshop
	$property_workshop = '';
	if(isset($meta['property_workshop'])) {
		if(isset($meta['property_workshop'][0])) {
			$property_workshop = $meta['property_workshop'][0];
			if( isset($property_workshop) && ($property_workshop == 1 || $property_workshop == 'yes') ) {
				$l_workshop = '<li class="workshop">'.__('Workshop', 'easy-property-listings').'</li>';
			}
		}
	}
	// Rumpus Room
	$property_rumpus_room = '';
	if(isset($meta['property_rumpus_room'])) {
		if(isset($meta['property_rumpus_room'][0])) {
			$property_rumpus_room = $meta['property_rumpus_room'][0];
			if( isset($property_rumpus_room) && ($property_rumpus_room == 1 || $property_rumpus_room == 'yes') ) {
				$l_r_room = '<li class="rumpus_room">'.__('Rumpus Room', 'easy-property-listings').'</li>';
			}
		}
	}
	// Floor Boards
	$property_floor_boards = '';
	if(isset($meta['property_floor_boards'])) {
		if(isset($meta['property_floor_boards'][0])) {
			$property_floor_boards = $meta['property_floor_boards'][0];
			if( isset($property_floor_boards) && ($property_floor_boards == 1 || $property_floor_boards == 'yes') ) {
				$l_f_board = '<li class="floor_boards">'.__('Floor Boards', 'easy-property-listings').'</li>';
			}
		}
	}
	// Broadband
	$property_broadband = '';
	if(isset($meta['property_broadband'])) {
		if(isset($meta['property_broadband'][0])) {
			$property_broadband = $meta['property_broadband'][0];
			if( isset($property_broadband) && ($property_broadband == 1 || $property_broadband == 'yes') ) {
				$l_b_band = '<li class="broadband">'.__('Broadband', 'easy-property-listings').'</li>';
			}
		}
	}
	// Pay TV
	$property_pay_tv = '';
	if(isset($meta['property_pay_tv'])) {
		if(isset($meta['property_pay_tv'][0])) {
			$property_pay_tv = $meta['property_pay_tv'][0];
			if( isset($property_pay_tv) && ($property_pay_tv == 1 || $property_pay_tv == 'yes') ) {
				$l_p_tv = '<li class="pay_tv">'.__('Pay TV', 'easy-property-listings').'</li>';
			}
		}
	}
	// Vacuum System
	$property_vacuum_system = '';
	if(isset($meta['property_vacuum_system'])) {
		if(isset($meta['property_vacuum_system'][0])) {
			$property_vacuum_system = $meta['property_vacuum_system'][0];
			if( isset($property_vacuum_system) && ($property_vacuum_system == 1 || $property_vacuum_system == 'yes') ) {
				$l_v_system = '<li class="vacuum_system">'.__('Vacuum System', 'easy-property-listings').'</li>';
			}
		}
	}
	// Intercom
	$property_intercom = '';
	if(isset($meta['property_intercom'])) {
		if(isset($meta['property_intercom'][0])) {
			$property_intercom = $meta['property_intercom'][0];
			if( isset($property_intercom) && ($property_intercom == 1 || $property_intercom == 'yes') ) {
				$l_i_com = '<li class="intercom">'.__('Intercom', 'easy-property-listings').'</li>';
			}
		}
	}
	// Spa
	$property_spa = '';
	if(isset($meta['property_spa'])) {
		if(isset($meta['property_spa'][0])) {
			$property_spa = $meta['property_spa'][0];
			if( isset($property_spa) && ($property_spa == 1 || $property_spa == 'yes') ) {
				$l_spa = '<li class="spa">'.__('Spa', 'easy-property-listings').'</li>';
			}
		}
	}
	// Tennis Court
	$property_tennis_court = '';
	if(isset($meta['property_tennis_court'])) {
		if(isset($meta['property_tennis_court'][0])) {
			$property_tennis_court = $meta['property_tennis_court'][0];
			if( isset($property_tennis_court) && ($property_tennis_court == 1 || $property_tennis_court == 'yes') ) {
				$l_t_court = '<li class="tennis_court">'.__('Tennis Court', 'easy-property-listings').'</li>';
			}
		}
	}
	// Balcony
	$property_balcony = '';
	if(isset($meta['property_balcony'])) {
		if(isset($meta['property_balcony'][0])) {
			$property_balcony = $meta['property_balcony'][0];
			if( isset($property_balcony) && ($property_balcony == 1 || $property_balcony == 'yes') ) {
				$l_balcony = '<li class="balcony">'.__('Balcony', 'easy-property-listings').'</li>';
			}
		}
	}
	// Deck
	$property_deck = '';
	if(isset($meta['property_deck'])) {
		if(isset($meta['property_deck'][0])) {
			$property_deck = $meta['property_deck'][0];
			if( isset($property_deck) && ($property_deck == 1 || $property_deck == 'yes') ) {
				$l_deck = '<li class="deck">'.__('Deck', 'easy-property-listings').'</li>';
			}
		}
	}
	// Courtyard
	$property_courtyard = '';
	if(isset($meta['property_courtyard'])) {
		if(isset($meta['property_courtyard'][0])) {
			$property_courtyard = $meta['property_courtyard'][0];
			if( isset($property_courtyard) && ($property_courtyard == 1 || $property_courtyard == 'yes') ) {
				$l_courtyard = '<li class="courtyard">'.__('Courtyard', 'easy-property-listings').'</li>';
			}
		}
	}
	// Outdoor Entertaining
	$property_outdoor_entertaining = '';
	if(isset($meta['property_outdoor_entertaining'])) {
		if(isset($meta['property_outdoor_entertaining'][0])) {
			$property_outdoor_entertaining = $meta['property_outdoor_entertaining'][0];
			if( isset($property_outdoor_entertaining) && ($property_outdoor_entertaining == 1 || $property_outdoor_entertaining == 'yes') ) {
				$l_o_ent = '<li class="outdoor_entertaining">'.__('Outdoor Entertaining', 'easy-property-listings').'</li>';
			}
		}
	}
	// Shed
	$property_shed = '';
	if(isset($meta['property_shed'])) {
		if(isset($meta['property_shed'][0])) {
			$property_shed = $meta['property_shed'][0];
			if( isset($property_shed) && ($property_shed == 1 || $property_shed == 'yes') ) {
				$l_shed = '<li class="shed">'.__('Shed', 'easy-property-listings').'</li>';
			}
		}
	}
	// Open Fire Place
	$property_open_fire_place = '';
	if(isset($meta['property_open_fire_place'])) {
		if(isset($meta['property_open_fire_place'][0])) {
			$property_open_fire_place = $meta['property_open_fire_place'][0];
			if( isset($property_open_fire_place) && ($property_open_fire_place == 1 || $property_open_fire_place == 'yes') ) {
				$l_o_fire = '<li class="open_fire_place">'.__('Open Fire Place', 'easy-property-listings').'</li>';
			}
		}
	}
	// Ducted Cooling
	$property_ducted_cooling = '';
	if(isset($meta['property_ducted_cooling'])) {
		if(isset($meta['property_ducted_cooling'][0])) {
			$property_ducted_cooling = $meta['property_ducted_cooling'][0];
			if( isset($property_ducted_cooling) && ($property_ducted_cooling == 1 || $property_ducted_cooling == 'yes') ) {
				$l_d_cool = '<li class="ducted_cooling">'.__('Ducted Cooling', 'easy-property-listings').'</li>';
			}
		}
	}
	// Split System Heating
	$property_split_system_heating = '';
	if(isset($meta['property_split_system_heating'])) {
		if(isset($meta['property_split_system_heating'][0])) {
			$property_split_system_heating = $meta['property_split_system_heating'][0];
			if( isset($property_split_system_heating) && ($property_split_system_heating == 1 || $property_split_system_heating == 'yes') ) {
				$l_s_heat = '<li class="split_system_heating">'.__('Split System Heating', 'easy-property-listings').'</li>';
			}
		}
	}
	// Hydronic Heating
	$property_hydronic_heating = '';
	if(isset($meta['property_hydronic_heating'])) {
		if(isset($meta['property_hydronic_heating'][0])) {
			$property_hydronic_heating = $meta['property_hydronic_heating'][0];
			if( isset($property_hydronic_heating) && ($property_hydronic_heating == 1 || $property_hydronic_heating == 'yes') ) {
				$l_h_heat = '<li class="hydronic_heating">'.__('Hydronic Heating', 'easy-property-listings').'</li>';
			}
		}
	}
	// Split System Aircon
	$property_split_system_aircon = '';
	if(isset($meta['property_split_system_aircon'])) {
		if(isset($meta['property_split_system_aircon'][0])) {
			$property_split_system_aircon = $meta['property_split_system_aircon'][0];
			if( isset($property_split_system_aircon) && ($property_split_system_aircon == 1 || $property_split_system_aircon == 'yes') ) {
				$l_s_system = '<li class="split_system_aircon">'.__('Split System Aircon', 'easy-property-listings').'</li>';
			}
		}
	}
	// Gas Heating
	$property_gas_heating = '';
	if(isset($meta['property_gas_heating'])) {
		if(isset($meta['property_gas_heating'][0])) {
			$property_gas_heating = $meta['property_gas_heating'][0];
			if( isset($property_gas_heating) && ($property_gas_heating == 1 || $property_gas_heating == 'yes') ) {
				$l_g_heat = '<li class="gas_heating">'.__('Gas Heating', 'easy-property-listings').'</li>';
			}
		}
	}
	// Reverse Cycle Aircon
	$property_reverse_cycle_aircon = '';
	if(isset($meta['property_reverse_cycle_aircon'])) {
		if(isset($meta['property_reverse_cycle_aircon'][0])) {
			$property_reverse_cycle_aircon = $meta['property_reverse_cycle_aircon'][0];
			if( isset($property_reverse_cycle_aircon) && ($property_reverse_cycle_aircon == 1 || $property_reverse_cycle_aircon == 'yes') ) {
				$l_rc_ac = '<li class="reverse_cycle_aircon">'.__('Reverse Cycle Aircon', 'easy-property-listings').'</li>';
			}
		}
	}
	// Evaporative Cooling
	$property_evaporative_cooling = '';
	if(isset($meta['property_evaporative_cooling'])) {
		if(isset($meta['property_evaporative_cooling'][0])) {
			$property_evaporative_cooling = $meta['property_evaporative_cooling'][0];
			if( isset($property_evaporative_cooling) && ($property_evaporative_cooling == 1 || $property_evaporative_cooling == 'yes') ) {
				$l_e_cool = '<li class="evaporative_cooling">'.__('Evaporative Cooling', 'easy-property-listings').'</li>';
			}
		}
	}
}
// Icons
$property_icons_full = '';
if(isset($i_bed)) {
	$property_icons_full .= $i_bed;
}
if(isset($i_bath)) {
	$property_icons_full .= $i_bath;
}
if(isset($i_room)) {
	$property_icons_full .= $i_room;
}
if(isset($i_park)) {
	$property_icons_full .= $i_park;
}
if(isset($i_air)) {
	$property_icons_full .= $i_air;
}
if(isset($i_pool)) {
	$property_icons_full .= $i_pool;
}
$property_icons_bb = '';
if(isset($i_bed)) {
	$property_icons_bb .= $i_bed;
}
if(isset($i_bath)) {
	$property_icons_bb .= $i_bath;
}
// Feature List
$the_property_feature_list = '';
if(isset($l_htype)) {
	$the_property_feature_list .= $l_htype;
}
if(isset($l_bed)) {
	$the_property_feature_list .= $l_bed;
}
if(isset($l_bath)) {
	$the_property_feature_list .= $l_bath;
}
if(isset($l_toilet)) {
	$the_property_feature_list .= $l_toilet;
}
if(isset($l_new_construction)) {
	$the_property_feature_list .= $l_new_construction;
}
if(isset($l_land)) {
	$the_property_feature_list .= $l_land;
}
if(isset($l_area)) {
	$the_property_feature_list .= $l_area;
}
if(isset($l_garage)) {
	$the_property_feature_list .= $l_garage;
}
if(isset($l_carport)) {
	$the_property_feature_list .= $l_carport;
}
if(isset($l_air)) {
	$the_property_feature_list .= $l_air;
}
if(isset($l_pool)) {
	$the_property_feature_list .= $l_pool;
}
if(isset($l_alarm)) {
	$the_property_feature_list .= $l_alarm;
}
// Additional Features List
if ( 'property' == $post_type || 'rental' == $post_type || 'rural' == $post_type) {
	if(isset($l_r_garage)) {
		$the_property_feature_list .= $l_r_garage;
	}
	if(isset($l_s_park)) {
		$the_property_feature_list .= $l_s_park;
	}
	if(isset($l_dish)) {
		$the_property_feature_list .= $l_dish;
	}
	if(isset($l_b_i_r)) {
		$the_property_feature_list .= $l_b_i_r;
	}
	if(isset($l_gym)) {
		$the_property_feature_list .= $l_gym;
	}
	if(isset($l_workshop)) {
		$the_property_feature_list .= $l_workshop;
	}
	if(isset($l_r_room)) {
		$the_property_feature_list .= $l_r_room;
	}
	if(isset($l_f_board)) {
		$the_property_feature_list .= $l_f_board;
	}
	if(isset($l_b_band)) {
		$the_property_feature_list .= $l_b_band;
	}
	if(isset($l_p_tv)) {
		$the_property_feature_list .= $l_p_tv;
	}
	if(isset($l_v_system)) {
		$the_property_feature_list .= $l_v_system;
	}
	if(isset($l_i_com)) {
		$the_property_feature_list .= $l_i_com;
	}
	if(isset($l_spa)) {
		$the_property_feature_list .= $l_spa;
	}
	if(isset($l_t_court)) {
		$the_property_feature_list .= $l_t_court;
	}
	if(isset($l_balcony)) {
		$the_property_feature_list .= $l_balcony;
	}
	if(isset($l_deck)) {
		$the_property_feature_list .= $l_deck;
	}
	if(isset($l_courtyard)) {
		$the_property_feature_list .= $l_courtyard;
	}
	if(isset($l_o_ent)) {
		$the_property_feature_list .= $l_o_ent;
	}
	if(isset($l_shed)) {
		$the_property_feature_list .= $l_shed;
	}
	if(isset($l_o_fire)) {
		$the_property_feature_list .= $l_o_fire;
	}
	if(isset($l_d_cool)) {
		$the_property_feature_list .= $l_d_cool;
	}
	if(isset($l_s_heat)) {
		$the_property_feature_list .= $l_s_heat;
	}
	if(isset($l_h_heat)) {
		$the_property_feature_list .= $l_h_heat;
	}
	if(isset($l_s_system)) {
		$the_property_feature_list .= $l_s_system;
	}
	if(isset($l_g_heat)) {
		$the_property_feature_list .= $l_g_heat;
	}
	if(isset($l_rc_ac)) {
		$the_property_feature_list .= $l_rc_ac;
	}
	if(isset($l_e_cool)) {
		$the_property_feature_list .= $l_e_cool;
	}
}
// Feature List : Commercial, Commercial Land, Business
if ( 'commercial' == $post_type || 'commercial_land' == $post_type || 'business' == $post_type ) {
	$the_property_feature_list = '';
	if(isset($l_com_cat)) {
		$the_property_feature_list .= $l_com_cat;
	}
	if(isset($l_htype)) {
		$the_property_feature_list .= $l_htype;
	}
	if(isset($l_bed)) {
		$the_property_feature_list .= $l_bed;
	}
	if(isset($l_bath)) {
		$the_property_feature_list .= $l_bath;
	}
	if(isset($l_room)) {
		$the_property_feature_list .= $l_room;
	}
	if(isset($l_land)) {
		$the_property_feature_list .= $l_land;
	}
	if(isset($l_area)) {
		$the_property_feature_list .= $l_area;
	}
	if(isset($l_garage)) {
		$the_property_feature_list .= $l_garage;
	}
	if(isset($l_carport)) {
		$the_property_feature_list .= $l_carport;
	}
	if(isset($l_car_spaces)) {
		$the_property_feature_list .= $l_car_spaces;
	}
	if(isset($l_air)) {
		$the_property_feature_list .= $l_air;
	}
	if(isset($l_pool)) {
		$the_property_feature_list .= $l_pool;
	}
	if(isset($l_alarm)) {
		$the_property_feature_list .= $l_alarm;
	}
	if(isset($l_car_comments)) {
		$the_property_feature_list .= $l_car_comments;
	}
}
if(isset($property_feature_taxonomy)) {
	$the_property_feature_list 	= $the_property_feature_list . $property_feature_taxonomy;
}
$map_mini_featured = '';
if(isset($d_bed)) {
	$map_mini_featured .= $d_bed;
}
if(isset($d_bath)) {
	$map_mini_featured .= $d_bath;
}
// RURAL specific custom post type meta
if ( 'rural' == $post_type ) {
	// Rural features Load Meta

	$property_rural_category 		= '';
	$property_rural_fencing 		= '';
	$property_rural_annual_rainfall 	= '';
	$property_rural_soil_types 		= '';
	$property_rural_improvements 		= '';
	$property_rural_council_rates 		= '';
	$property_rural_irrigation 		= '';
	$property_rural_carrying_capacity 	= '';



	if(isset($meta['property_rural_category'])) {
		if(isset($meta['property_rural_category'][0])) {
			$property_rural_category_id = $meta['property_rural_category'][0];
			$property_rural_category = epl_listing_load_meta_rural_category_value( $property_rural_category_id );
		}
	}
	if(isset($meta['property_rural_fencing'])) {
		if(isset($meta['property_rural_fencing'][0])) {
			$property_rural_fencing = $meta['property_rural_fencing'][0];
		}
	}
	if(isset($meta['property_rural_annual_rainfall'])) {
		if(isset($meta['property_rural_annual_rainfall'][0])) {
			$property_rural_annual_rainfall = $meta['property_rural_annual_rainfall'][0];
		}
	}
	if(isset($meta['property_rural_soil_types'])) {
		if(isset($meta['property_rural_soil_types'][0])) {
			$property_rural_soil_types = $meta['property_rural_soil_types'][0];
		}
	}
	if(isset($meta['property_rural_improvements'])) {
		if(isset($meta['property_rural_improvements'][0])) {
			$property_rural_improvements = $meta['property_rural_improvements'][0];
		}
	}
	if(isset($meta['property_rural_council_rates'])) {
		if(isset($meta['property_rural_council_rates'][0])) {
			$property_rural_council_rates = $meta['property_rural_council_rates'][0];
		}
	}

	if(isset($meta['property_rural_irrigation'])) {
		if(isset($meta['property_rural_irrigation'][0])) {
			$property_rural_irrigation = $meta['property_rural_irrigation'][0];
		}
	}
	if(isset($meta['property_rural_carrying_capacity'])) {
		if(isset($meta['property_rural_carrying_capacity'][0])) {
			$property_rural_carrying_capacity = $meta['property_rural_carrying_capacity'][0];
		}
	}
	if($property_rural_fencing != '') {
		$l_rural_fencing = '<div class="fencing"><h6>'.__('Fencing', 'easy-property-listings').'</h6><p>' .  $property_rural_fencing . '</p></div>';
	}
	if($property_rural_annual_rainfall != '') {
		$l_rural_annual_rainfall = '<div class="rainfall"><h6>'.__('Annual Rainfall', 'easy-property-listings').'</h6><p>' . $property_rural_annual_rainfall . '</p></div>';
	}
	if($property_rural_soil_types != '') {
		$l_rural_soil_types = '<div class="soil"><h6>'.__('Soil Types', 'easy-property-listings').'</h6><p>' . $property_rural_soil_types . '</p></div>';
	}
	if($property_rural_improvements != '') {
		$l_rural_improvements = '<div class="improvements"><h6>'.__('Improvements', 'easy-property-listings').'</h6><p>' . $property_rural_improvements . '</p></div>';
	}
	if($property_rural_council_rates != '') {
		$l_rural_council_rates = '<div class="council-rates"><h6>'.__('Council Rates', 'easy-property-listings').'</h6><p>' . $property_rural_council_rates . '</p></div>';
	}
	if($property_rural_irrigation != '') {
		$l_rural_irrigation = '<div class="irrigation"><h6>'.__('Irrigation', 'easy-property-listings').'</h6><p>' . $property_rural_irrigation . '</p></div>';
	}
	if($property_rural_carrying_capacity != '') {
		$l_rural_carrying_capacity = '<div class="carrying-capacity"><h6>'.__('Carrying Capacity', 'easy-property-listings').'</h6><p>' . $property_rural_carrying_capacity . '</p></div>';
	}

	// Append Rural Features to Features Tab
	$the_property_rural_feature_list = '';
	if(isset($l_rural_fencing)) {
		$the_property_rural_feature_list .= $l_rural_fencing;
	}
	if(isset($l_rural_annual_rainfall)) {
		$the_property_rural_feature_list .= $l_rural_annual_rainfall;
	}
	if(isset($l_rural_soil_types)) {
		$the_property_rural_feature_list .= $l_rural_soil_types;
	}
	if(isset($l_rural_improvements)) {
		$the_property_rural_feature_list .= $l_rural_improvements;
	}
	if(isset($l_rural_council_rates)) {
		$the_property_rural_feature_list .= $l_rural_council_rates;
	}
	if(isset($l_rural_improvements)) {
		$the_property_rural_feature_list .= $l_rural_improvements;
	}
	if(isset($l_rural_carrying_capacity)) {
		$the_property_rural_feature_list .= $l_rural_carrying_capacity;
	}
}
