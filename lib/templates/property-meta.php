<?php
/*
 * Function to retrieve property or rental custom fields.
 */

include('epl-settings.php');

// Store All Meta
$meta = get_post_custom();
$post_type = get_post_type();
$d_bond = $epl_bond_display;

// Property Heading
$property_heading = $meta['property_heading'][0];

if($property_heading != '') {
	$the_property_heading = $property_heading;
} else {
	$the_property_heading = get_the_title();	
}	

// Process Status 
$property_heading = $meta['property_heading'][0];
$property_status = $meta['property_status'][0];

// Process Property Features Taxonomy
if ( taxonomy_exists('epl_tax_feature') ) {
	$property_feature_taxonomy = get_the_term_list($post->ID, 'epl_tax_feature', '<li>', '</li><li>', '</li>' );
}

// Authority
$property_authority = $meta['property_authority'][0];

// Auction
$property_auction = $meta['property_auction'][0];

//address and inspection
$property_inspection_times = $meta['property_inspection_times'][0];

// Suburb Profile
$profiles = get_the_terms( $post->ID, 'epl_tax_suburb' );
if ( $profiles != '' ){
	global $post;
	foreach($profiles as $profile){
	$profile->slug; }
	$suburb_name = $profile->name;
}

// Price Fields
$property_price_search = $meta['property_price'][0];
$property_price_view = $meta['property_price_view'][0];
$property_under_offer = $meta['property_under_offer'][0];
if ( $property_under_offer == 'yes') {
	$property_under_offer = 1;
}

// Property Price Display
if ( $property_price_search == '') {
	
} elseif($property_price_view != '') {
	$property_price = $meta['property_price_view'][0]; 
} else {
	$property_price_search = $meta['property_price'][0];
	$price_format_number = number_format($property_price_search);
	$property_price = '$' . $price_format_number;
}

// Price / Rent Display converting yes to 1 
$property_price_display = $meta['property_price_display'][0];
if ( $property_price_display == 'yes') {
	$property_price_display = 1;
}

$property_rent_display = $meta['property_rent_display'][0];
if ( $property_rent_display == 'yes') {
	$property_rent_display = 1;
}

// Rental Price XE Format		
$property_rent = $meta['property_rent'][0];
if ( $property_rent !='' ) {
	$rent_format_number = number_format($property_rent);
	$property_rent = '$' . $rent_format_number;
}

// Rent Specifics
$property_date_available = $meta['property_date_available'][0];
$property_rent_period = $meta['property_rent_period'][0];
$property_bond = $meta['property_bond'][0];

//address
$property_unique_id = $meta['property_unique_id'][0];	
$property_address_street_number = $meta['property_address_street_number'][0];	
$property_address_street = $meta['property_address_street'][0];
$property_address_street = $property_address_street_number . ' ' . $property_address_street;
$property_address_suburb = $meta['property_address_suburb'][0];		
$property_address_state = $meta['property_address_state'][0];
$property_address_postal_code = $meta['property_address_postal_code'][0];
$property_address_display = $meta['property_address_display'][0];
$property_address = $property_address_street . ', ' . $property_address_suburb;
$coords = $meta['property_address_coordinates'][0];
if ( $coords == '' ) {
	$coords = ',';
}

//house features
$property_category = $meta['property_category'][0];
$property_house_type = $property_category;
$property_bedrooms = $meta['property_bedrooms'][0];
$property_bathrooms = $meta['property_bathrooms'][0];		
$property_garage = $meta['property_garage'][0];
$property_carport = $meta['property_carport'][0];
$property_parking = $property_garage + $property_carport;
$property_air_conditioning = $meta['property_air_conditioning'][0];
$property_pool = $meta['property_pool'][0];
$property_security_system = $meta['property_security_system'][0];


// Land Area
$property_land_value = $meta['property_land_area'][0];
$property_land = $meta['property_land_area'][0];
$property_land_unit = $meta['property_land_area_unit'][0];
if ( $property_land_unit == 'squareMeter') {
	$property_land_unit = 'sqm';
}
$property_land = $property_land . ' ' . $property_land_unit;

// Building Area
$building_area_value = $meta['property_building_area'][0];
$building_area = $meta['property_building_area'][0];
$building_area_unit = $meta['property_building_area_unit'][0];
if ( $building_area_unit == 'squareMeter') {
	$building_area_unit = 'm²';
}
$building_area = $building_area . ' ' . $building_area_unit;
$property_age = $meta['property_age'][0];

// Property Other Details
$property_featured = $meta['property_featured'][0];
$property_video_url = $meta['property_video_url'][0];
$property_post_type = $post_type;

// Pricing
$before_price = '<span class="page-price">';
if ( 'epl_property' == $post_type || 'epl_land' == $post_type || 'epl_rural' == $post_type) {	// Property Pricing and Specialised Meta Fields
	// Property Meta
	if ( 'sold' == $property_status ) {
		$price_class = 'epl-price sold';
		$price_plain_value = 'Sold';
		$price = '<span class="page-price sold-status">Sold</span>';
		$price_sticker = '<span class="status-sticker sold">Sold</span>';
		$l_price = '<li class="page-price sold-status">Sold</li>';
	} elseif ( $property_price != '' && $property_price_display == 1 ) {	// Property
		$price_plain_value = $property_price . $property_tax;
		$price = '<span class="page-price">'. $property_price . $property_tax . '</span>';
		$l_price = '<li class="page-price">'. $property_price . $property_tax . '</li>';
		if ( $property_inspection_times != '' ){
			$price_sticker = '<span class="status-sticker open">Open House</span>';
		}
	} else {
		$price_plain_value = 'POA';
		$price = '<span class="page-price">POA</span>';
		$l_price = '<li class="page-price">POA</li>';
		if ( $property_inspection_times != '' ){
			$price_sticker = '<span class="status-sticker open">Open House</span>';
		}
	}
	
	if ( $property_under_offer == 1 && 'sold' != $property_status) {	// Under Offer
		$price_plain_value = 'Under Offer';
		$price = '<span class="page-price under-offer-status">Under Offer</span>';
		$price_sticker = '<span class="status-sticker under-offer">Under Offer</span>';
		$l_price = '<li class="page-price under-offer-status">Under Offer</li>';
	}
} elseif ( 'epl_rental' == $post_type ) {	// Rental Pricing
	// Rental Meta
	if( $property_rent != '' && $property_rent_display == 1 && $property_status != 'leased') {	
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

		if ($property_bond != 0 && $d_bond == 1){ 
			$price_plain_value .= ' $' . $property_bond . ' bond';
			$price .= '<span class="bond">$' . $property_bond . ' bond</span>';
		}
	} elseif ( $property_status == 'leased') {
		$price_plain_value = 'Leased';
		$price = '<span class="page-price sold-status">Leased</span>';
		$price_sticker = '<span class="status-sticker leased">Leased</span>';
		$l_price = '<li class="page-price sold-status">Leased</li>';
	} else {
		$price_plain_value = 'TBA';
		$price = '<span class="page-price">TBA</span>';
		if ( $property_inspection_times != '' ){
			$price_sticker = '<span class="status-sticker open">Open House</span>';
		}
	}	
} elseif ( 'epl_commercial' == $post_type || 'epl_business' == $post_type ) { // Commercial Pricing
	// Commercial Meta
	$property_com_authority = $meta['property_com_authority'][0];
	$property_exclusivity = $meta['property_exclusivity'][0];
	$property_com_listing_type = $meta['property_com_listing_type'][0];
	$property_com_display_suburb = $meta['property_com_display_suburb'][0];
	
	$property_com_rent = $meta['property_com_rent'][0];
	$property_com_is_multiple = $meta['property_com_is_multiple'][0];
	$property_com_rent_range_min = $meta['property_com_rent_range_min'][0];
	$property_com_rent_range_max = $meta['property_com_rent_range_max'][0];
	$property_com_outgoings = $meta['property_com_outgoings'][0];
	$property_com_plus_outgoings = $meta['property_com_plus_outgoings'][0];
	$property_com_return = $meta['property_com_return'][0];
	$property_com_lease_end_date = $meta['property_com_lease_end_date'][0];
	$property_com_tenancy = $meta['property_com_tenancy'][0];
	
	$property_com_further_options = $meta['property_com_further_options'][0];
	$property_commercial_category = $meta['property_commercial_category'];
	if ( $property_commercial_category != '' ) {
		$the_property_commercial_category = implode(", ", $property_commercial_category);
	}
	$the_property_commercial_category = '<div class="commercial-category">' . $the_property_commercial_category . '</div>';

	$property_com_highlight_1 = $meta['property_com_highlight_1'][0];
	$property_com_highlight_2 = $meta['property_com_highlight_2'][0];
	$property_com_highlight_3 = $meta['property_com_highlight_3'][0];
	
	$property_com_car_spaces = $meta['property_com_car_spaces'][0];
	$property_com_parking_comments = $meta['property_com_parking_comments'][0];
	$property_com_zone = $meta['property_com_zone'][0];
	$property_com_external_link = $meta['property_com_external_link'][0];
	$property_com_mini_web = $meta['property_com_mini_web'][0];
	
	if ($property_com_car_spaces != 0 ) {
		$d_car_spaces = $property_com_car_spaces . ' Car Spaces, ';
		$l_car_spaces = '<li class="car-spaces">' . $property_com_car_spaces . ' Car Spaces</li>';
	}
	if ($property_com_parking_comments != '' ) {
		$l_car_comments = '<p class="car-comments">' . $property_com_parking_comments . '</p>';
	}	
	$property_tax = $meta['property_tax'][0];
	if ( $property_tax == 'exempt' ) {
		$property_tax = ' Inc. GST';
	} elseif ( $property_tax == 'inclusive' ) {
		$property_tax = ' Inc. GST';
	} elseif ( $property_tax == 'exclusive' ) {
		$property_tax = '+ GST';
	} else {
		$property_tax = '';
	}
	
	if ( 'sold' == $property_status ) {	// Commercial Pricing
		$price_plain_value = 'Sold';
		$price_class = 'epl-price sold';
		$price = '<span class="page-price sold-status">Sold</span>';
		$price_sticker = '<span class="status-sticker sold">Sold</span>';
		$l_price = '<li class="status-sticker sold">Sold</li>';
	} elseif ( $property_price != '' && $property_price_display == 1 ) {	// Property
		$price_plain_value = 'For Sale ' . $property_price . $property_tax;
		$price = '<span class="page-price">For Sale '. $property_price . $property_tax . '</span>';
		$l_price = '<li class="page-price">For Sale '. $property_price . $property_tax . '</li>';		
	} else {
		$price = '<span class="page-price">POA</span>';
		$price_plain_value = 'POA';
		$l_price = '<li class="page-price">POA</li>';
	}
	
	// Under Offer
	if ( $property_under_offer == 1 && 'sold' != $property_status) {
		$price_plain_value = 'Under Offer';
		$price = '<div class="page-price under-offer-status">Under Offer</div>';
		$price_sticker = '<span class="status-sticker under-offer">Under Offer</span>';
		$l_price = '<li class="page-price under-offer-status">Under Offer</li>';
	}

	if( $property_com_rent != 0 && $property_com_listing_type == 'both') {
		$price_com_rent = number_format($property_com_rent);
		$price_plain_value .= '</br>For Lease $' . $price_com_rent . ' P.A.';
		$price .= '<div class="epl-clear"></div><span class="page-price">For Lease $' . $price_com_rent . ' P.A.</span>';
		$l_price .= '<li class="page-price">For Lease $' . $price_com_rent . ' P.A.</li>';
	} elseif( $property_com_rent != '' && $property_com_listing_type == 'lease') {
		$price_com_rent = number_format($property_com_rent);
		$price_plain_value = 'For Lease $' . $price_com_rent . ' P.A.';
		$price = '<span class="page-price">For Lease $' . $price_com_rent . ' P.A.</span>';
		$l_price = '<li class="page-price">For Lease $' . $price_com_rent . ' P.A.</li>';
	}
}
// END Property Pricing

// Property Under Offer Tag
if ($property_price != ' ' && $property_under_offer == 1) {
	$property_under_offer_tag = '<span class="status property-under-offer"><strong>Under Offer</strong></span>';
} 

// Link to the Suburb Taxonomy adding a $post_type filter
$tax_suburbs = get_the_terms( $post->ID, 'property_suburb' );
if ( $tax_suburbs != '' ) {
	foreach( $tax_suburbs as $tax_suburb ) {
		$tax_suburb_link = get_bloginfo( 'url' ) . '/property-suburb/' . $tax_suburb->slug . '?post_type=' . $post_type;
		$tax_suburb_name = $tax_suburb->name;
		$the_suburb_term = '<span class="suburb-link"><a href="' . $tax_suburb_link . '">' .$tax_suburb_name . '</a></span>';
	}
}

// DISPLAY Features
// Property Icons / Dynamic Description
if($property_bedrooms != 0) {
	$i_bed = '<span title="Bathrooms" class="icon beds"><span class="icon-value">'. $property_bedrooms . '</span></span>'; 
	$d_bed = $property_bedrooms . ' bed ';
	$l_bed = '<li class="bedrooms">' . $property_bedrooms . ' bed</li>';
}

if($property_bathrooms != 0) { 
	$i_bath = '<span title="Bedrooms" class="icon bath"><span class="icon-value">' . $property_bathrooms  . '</span></span>'; 
	$d_bath = $property_bathrooms . ' bath ';
	$l_bath = '<li class="bathrooms">' . $property_bathrooms . ' bath</li>';
}

if($property_parking != 0) { 
	$i_park = '<span title="Parking Spaces" class="icon parking"><span class="icon-value">' . $property_parking . '</span></span>';
}

if($property_air_conditioning != 0) { 
	$i_air = '<span title="Air Conditioning" class="icon air"></span>'; 
	$d_air = 'an Air conditioning System ';
	$l_air = '<li class="air">Air conditioning</li>';
}

if($property_pool != 0) { 
	$i_pool = '<span title="Pool" class="icon pool"></span>'; 
	$d_pool = 'and a Pool. ';
	$l_pool = '<li class="pool">Pool</li>';
}

if($property_house_type != '') { 
	$d_htype = $property_house_type . '';
	$l_htype = '<li class="type">' . $property_house_type . '</li>';
}

if($property_security_system == 1) {
	$d_alarm = 'with Alarm system. ';
	$l_alarm = '<li class="alarm">Alarm system</li>';
}

if($property_bedrooms != 0 || $property_bathrooms != 0) {
	$d_also = '. that also has '; 
} else {
	$d_also = 'It has ';
}

if($property_garage != 0) {
	$d_garage =  $property_garage . ' Garage, ';
	$l_garage = '<li class="garage">' . $property_garage . ' Garage</li>';
}

if($property_carport != 0) {
	$d_carport = $property_carport . ' Carport, ';
	$l_carport = '<li class="carport">' . $property_carport . ' Carport</li>';
}

if($property_land_value != '') {
	$d_land = 'You will find it on ' . $property_land;
	$l_land = '<li class="land-size">Land is ' . $property_land . '</li>';
}

if($property_age != '') {
	$d_age = 'and was built in ' . $property_age . '.';
	$l_age = '<li class="age">Built in ; ' . $property_age . '</li>';
}

if($building_area_value != '') { 
	$l_area = '<li class="building-size">Floor Area is ' . $building_area . '</li>';
}

// Description
if ( $epl_dynamic_description == 1 ) {
	$property_dynamic_description = $d_bath . $d_bed . $d_htype .  $d_garage . $d_carport . $d_air . $d_pool . $d_land . $d_age;
}

// Icons
$property_icons_full = $i_bed . $i_bath . $i_park . $i_air . $i_pool;
$property_icons_bb = $i_bed . $i_bath;

// Feature List
$the_property_feature_list = $l_htype . $l_bed . $l_bath . $l_land . $l_area . $l_age . $l_garage . $l_carport . $l_car_spaces . $l_air . $l_pool . $l_alarm;
$the_property_feature_list = $the_property_feature_list . $property_feature_taxonomy;
$map_mini_featured = $d_bed . $d_bath;

// RURAL epl_rural specific custom post type meta
if ( 'epl_rural' == $post_type ) {

	// Rural features Load Meta
	$property_rural_category = $meta['property_rural_category'][0];
	$property_rural_fencing = $meta['property_rural_fencing'][0];
	$property_rural_annual_rainfall = $meta['property_rural_annual_rainfall'][0];
	$property_rural_soil_types = $meta['property_rural_soil_types'][0];
	$property_rural_improvements = $meta['property_rural_improvements'][0];
	$property_rural_council_rates = $meta['property_rural_council_rates'][0];
	$property_rural_irrigation = $meta['property_rural_irrigation'][0];
	$property_rural_carrying_capacity = $meta['property_rural_carrying_capacity'][0];

	
	if($property_rural_fencing != '') {
		$l_rural_fencing = '<li class="fencing">Fencing: ' . $property_rural_fencing . '</li>';
	}
	if($property_rural_annual_rainfall != '') {
		$l_rural_annual_rainfall = '<li class="rainfall">Annual Rainfall: ' . $property_rural_annual_rainfall . '</li>';
	}	
	if($property_rural_soil_types != '') {
		$l_rural_soil_types = '<li class="soil">Soil Types: ' . $property_rural_soil_types . '</li>';
	}	
	if($property_rural_improvements != '') {
		$l_rural_improvements = '<li class="improvements">Improvements: ' . $property_rural_improvements . '</li>';
	}	
	if($property_rural_council_rates != '') {
		$l_rural_council_rates = '<li class="council-rates">Council Rates: ' . $property_rural_council_rates . '</li>';
	}
	if($property_rural_irrigation != '') {
		$l_rural_irrigation = '<li class="irrigation">Irrigation: ' . $property_rural_irrigation . '</li>';
	}
	if($property_rural_carrying_capacity != '') {
		$l_rural_carrying_capacity = '<li class="carrying-capacity">Carrying Capacity: ' . $property_rural_carrying_capacity . '</li>';
	}
	
	// Append Rural Features to Features Tab
	$the_property_rural_feature_list = $l_rural_fencing . $l_rural_annual_rainfall . $l_rural_soil_types . $l_rural_improvements . $l_rural_council_rates . $l_rural_irrigation . $l_rural_carrying_capacity;
}

// Links External
$property_external_link_1 = $meta['property_external_link_1'][0];
$property_external_link_2 = $meta['property_external_link_2'][0];
$property_external_link_3 = $meta['property_external_link_3'][0];

// Microsite Links
// Floorplans
