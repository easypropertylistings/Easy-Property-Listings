<?php
function epl_get_property_price($post) {
	$d_bond = $epl_bond_display;
	$property_status = get_post_meta($post->id, 'property_status', true);
	$property_price_search = get_post_meta($post->id, 'property_price', true);
	$property_price_view = get_post_meta($post->id, 'property_price_view', true);	
	$property_inspection_times = get_post_meta($post->id, 'property_inspection_times', true);
	
	$property_price_display = get_post_meta($post->id, 'property_price_display', true);
	if( $property_price_display == 'yes') {
		$property_price_display = 1;
	}
	
	$property_under_offer = get_post_meta($post->id, 'property_under_offer', true);
	if( $property_under_offer == 'yes') {
		$property_under_offer = 1;
	}
	
	$property_rent_display = get_post_meta($post->id, 'property_rent_display', true);
	if( $property_rent_display == 'yes') {
		$property_rent_display = 1;
	}

 	$property_rent = get_post_meta($post->id, 'property_rent', true);
	if ( $property_rent !='' ) {
		$rent_format_number = number_format($property_rent);
		$property_rent = '$' . $rent_format_number;
	}
	
	$property_rent_period = get_post_meta($post->id, 'property_rent_period', true);
	$property_bond = get_post_meta($post->id, 'property_bond', true);
	
	// Property Price Display
	if ( $property_price_search == '') {
	
	}
	elseif($property_price_view != '') {
		$property_price = $property_price_view; 
	} 
	else  {
		$price_format_number = number_format($property_price_search);
		$property_price = '$' . $price_format_number;
	}
	
	if ( $post->post_type == 'epl_property' || $post->post_type == 'epl_land') {
		if ( 'sold' == $property_status ) {
			$price_class = 'epl-price sold';
			$price_plain_value = 'Sold';
			$price = '<span class="page-price sold-status">Sold</span>';
			$price_sticker = '<span class="status-sticker sold">Sold</span>';
			$l_price = '<li class="page-price sold-status">Sold</li>';
		} elseif ( $property_price != '' && $property_price_display == 1 ) {
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
		
		if ( $property_under_offer == 1 && 'sold' != $property_status) {
			$price_plain_value = 'Under Offer';
			$price = '<span class="page-price under-offer-status">Under Offer</span>';
			$price_sticker = '<span class="status-sticker under-offer">Under Offer</span>';
			$l_price = '<li class="page-price under-offer-status">Under Offer</li>';
		}
	} elseif ( $post->post_type == 'epl_rental' ) {
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
	} elseif ( $post->post_type == 'epl_commercial' ) {
		$property_com_authority = get_post_meta($post->id, 'property_com_authority', true);
		$property_exclusivity = get_post_meta($post->id, 'property_exclusivity', true);
		$property_com_listing_types = get_the_terms( $post->id, 'com_listing_type' );
		if ($property_com_listing_types != '') {
			global $post;
			foreach($property_com_listing_types as $type) {
				$property_com_listing_types = $type->name;
			}
		}
		$property_com_rent = get_post_meta($post->id, 'property_com_rent', true);
		$property_com_is_multiple = get_post_meta($post->id, 'property_com_is_multiple', true);
		$property_com_rent_range_min = get_post_meta($post->id, 'property_com_rent_range_min', true);
		$property_com_rent_range_max = get_post_meta($post->id, 'property_com_rent_range_max', true);
		$property_com_outgoings = get_post_meta($post->id, 'property_com_outgoings', true);
		$property_com_plus_outgoings = get_post_meta($post->id, 'property_com_plus_outgoings', true);
		$property_com_return = get_post_meta($post->id, 'property_com_return', true);
		$property_com_lease_end_date = get_post_meta($post->id, 'property_com_lease_end_date', true);
		$property_com_tenancy = get_post_meta($post->id, 'property_com_tenancy', true);
		$property_com_further_options = get_post_meta($post->id, 'property_com_further_options', true);
		$property_commercial_category = get_post_meta($post->id, 'property_commercial_category', true);
		if ( $property_commercial_category != '' ) {
			$the_property_commercial_category = implode(", ", $property_commercial_category);
		}
		$the_property_commercial_category = '<div class="commercial-category">' . $the_property_commercial_category . '</div>';
		
		$property_com_car_spaces = get_post_meta($post->id, 'property_com_car_spaces', true);
		$property_com_parking_comments = get_post_meta($post->id, 'property_com_parking_comments', true);
		$property_com_zone = get_post_meta($post->id, 'property_com_zone', true);
		$property_com_external_link = get_post_meta($post->id, 'property_com_external_link', true);
		$property_com_mini_web = get_post_meta($post->id, 'property_com_mini_web', true);
		
		if ($property_com_car_spaces != 0 ) {
			$d_car_spaces = $property_com_car_spaces . ' Car Spaces, ';
			$l_car_spaces = '<li class="car-spaces">' . $property_com_car_spaces . ' Car Spaces</li>';
		}
		if ($property_com_parking_comments != '' ) {
			$l_car_comments = '<p class="car-comments">' . $property_com_parking_comments . '</p>';
		}
		
		$property_tax = get_post_meta($post->id, 'property_tax', true);
		if ( $property_tax == 'exempt' ) {
			$property_tax = ' Inc. GST';
		} elseif ( $property_tax == 'inclusive' ) {
			$property_tax = ' Inc. GST';
		} elseif ( $property_tax == 'exclusive' ) {
			$property_tax = '+ GST';
		} else {
			$property_tax = '';
		}
		
		if ( 'sold' == $property_status ) {
			$price_plain_value = 'Sold';
			$price_class = 'epl-price sold';
			$price = '<span class="page-price sold-status">Sold</span>';
			$price_sticker = '<span class="status-sticker sold">Sold</span>';
			$l_price = '<li class="status-sticker sold">Sold</li>';
		} elseif ( $property_price != '' && $property_price_display == 1 ) {
			$price_plain_value = 'For Sale ' . $property_price . $property_tax;
			$price = '<span class="page-price">For Sale '. $property_price . $property_tax . '</span>';
			$l_price = '<li class="page-price">For Sale '. $property_price . $property_tax . '</li>';		
		} else {
			$price = '<span class="page-price">POA</span>';
			$price_plain_value = 'POA';
			$l_price = '<li class="page-price">POA</li>';
		}
		
		if ( $property_under_offer == 1 && 'sold' != $property_status) {
			$price_plain_value = 'Under Offer';
			$price = '<div class="page-price under-offer-status">Under Offer</div>';
			$price_sticker = '<span class="status-sticker under-offer">Under Offer</span>';
			$l_price = '<li class="page-price under-offer-status">Under Offer</li>';
		}

		if( $property_com_rent != 0 && $property_com_listing_types == 'both') {
			$price_com_rent = number_format($property_com_rent);
			$price_plain_value .= '</br>For Lease $' . $price_com_rent . ' P.A.';
			$price .= '<div class="epl-clear"></div><span class="page-price">For Lease $' . $price_com_rent . ' P.A.</span>';
			$l_price .= '<li class="page-price">For Lease $' . $price_com_rent . ' P.A.</li>';
		} elseif( $property_com_rent != 0 && $property_com_listing_types == 'lease') {
			$price_com_rent = number_format($property_com_rent);
			$price_plain_value = 'For Lease $' . $price_com_rent . ' P.A.';
			$price = '<span class="page-price">For Lease $' . $price_com_rent . ' P.A.</span>';
			$l_price = '<li class="page-price">For Lease $' . $price_com_rent . ' P.A.</li>';
		}
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

function epl_is_builder_framework_theme() {
	if(function_exists('it_classes_load')) {
		return true;
	}
	return false;
}

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
