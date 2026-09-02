<?php
/**
 * Integrations/Gravity-Form
 *
 * @package     EPL
 * @subpackage  Functions/ConditionalTags
 * @copyright   Copyright (c) 2020, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.7.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Gravity Forms Filter for populating contact form with author email address
 *
 * Create a gravity from and add a hidden field and enable "Allow field to be populated dynamically"
 * option on the advanced tab. Use author_email as the Parameter Name and when a form is submitted
 * the email address of the author of that property/page/post will be added to the hidden field.
 * You can then setup the forwarding and notifications directly to the author as their email
 * will be part of the form.
 *
 * @param string $value Form pre populating field.
 *
 * @return string
 * @since 3.7.0
 */
function epl_populate_secondary_author_email( $value ) {
	global $post, $epl_author;

	$secondary = get_property_meta( 'property_second_agent' );
	$name      = get_the_author_meta( 'user_login', $epl_author->author_id );

	$author_email = '';
	
	if ( $secondary ) {
		$id           = epl_listing_has_secondary_agent();
		$author_email = get_the_author_meta( 'user_email', $id );
	}
	
	return $author_email;
}
add_filter( 'gform_field_value_author_email_secondary', 'epl_populate_secondary_author_email' );

/**
 * Gravity Forms Filter for populating contact form with property unique id
 *
 * Create a gravity from and add a hidden field and enable "Allow field to be populated dynamically"
 * option on the advanced tab. Use author_email as the Parameter Name and when a form is submitted
 * the email address of the author of that property/page/post will be added to the hidden field.
 * You can then setup the forwarding and notifications directly to the author as their email
 * will be part of the form.
 *
 * @param string $value Form pre populating field.
 *
 * @return string
 * @since 3.7.0
 */

if( !function_exists( 'epl_gravityforms_populate_post_property_unique_id' ) ) :
        function epl_gravityforms_populate_post_property_unique_id( $value ) {
                global $property;
                $unique_id = $property->get_property_meta( 'property_unique_id' );
                return $unique_id;
        }
endif;

add_filter( 'gform_field_value_property_unique_id', 'epl_gravityforms_populate_post_property_unique_id' );

/**
 * Gravity Forms Filter for populating contact form with office id
 *
 * Create a gravity from and add a hidden field and enable "Allow field to be populated dynamically"
 * option on the advanced tab. Use author_email as the Parameter Name and when a form is submitted
 * the email address of the author of that property/page/post will be added to the hidden field.
 * You can then setup the forwarding and notifications directly to the author as their email
 * will be part of the form.
 *
 * @param string $value Form pre populating field.
 *
 * @return string
 * @since 3.7.0
 */

if( !function_exists( 'epl_gravityforms_populate_post_property_office_id' ) ) :
        function epl_gravityforms_populate_post_property_office_id( $value ) {
                global $property;
                $unique_id = $property->get_property_meta( 'property_office_id' );
                return $unique_id;
        }
endif;

add_filter( 'gform_field_value_property_office_id', 'epl_gravityforms_populate_post_property_office_id' );

/**
 * Auction post class
 *
 * @since 3.7.0
 */
function epl_auction_post_class_callback( $classes ) {
 
	if ( is_epl_post() ) {
		
		$property_authority   = get_property_meta( 'property_authority' );
		$class_prefix         = 'epl-status-';
 
		if ( 'auction' === $property_authority ) {
			$classes[] = $class_prefix . 'auction';
		}
	}
 
	return $classes;
}
add_filter( 'post_class', 'epl_auction_post_class_callback' );

/**
 * Gravity Forms Filter for populating contact form with staff directory email address
 *
 * This is for adding support for Staff Directory extension
 *
 * Create a gravity from and add a hidden field and enable "Allow field to be populated dynamically"
 * option on the advanced tab. Use author_email as the Parameter Name and when a form is submitted
 * the email address of the author of that property/page/post will be added to the hidden field.
 * You can then setup the forwarding and notifications directly to the author as their email
 * will be part of the form.
 *
 * @param string $value Form pre populating field.
 *
 * @return string
 * @since 3.7.0
 */
function epl_populate_primary_agent_email( $value ) {
	global $post, $epl_author;
	
	$author_email = '';
	
	if ( ! empty( $epl_author ) ) {
		$author_email = $epl_author->email;
	}
	return $author_email;
}
add_filter( 'gform_field_value_primary_agent_email', 'epl_populate_primary_agent_email' );

/**
 * Gravity Forms Filter for populating contact form with staff directory email address
 *
 * This is for adding support for Staff Directory extension
 *
 * Create a gravity from and add a hidden field and enable "Allow field to be populated dynamically"
 * option on the advanced tab. Use author_email as the Parameter Name and when a form is submitted
 * the email address of the author of that property/page/post will be added to the hidden field.
 * You can then setup the forwarding and notifications directly to the author as their email
 * will be part of the form.
 *
 * @param string $value Form pre populating field.
 *
 * @return string
 * @since 3.7.0
 */
function epl_populate_primary_agent_name( $value ) {
	global $post, $epl_author;
	
	$author_name = '';
	
	if ( ! empty( $epl_author ) ) {
		$author_name = $epl_author->name;
	}
	return $author_name;
}
add_filter( 'gform_field_value_primary_agent_name', 'epl_populate_primary_agent_name' );