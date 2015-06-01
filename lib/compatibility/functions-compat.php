<?php
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
