<?php
/**
 * Conditional Tags
 *
 * @package     EPL
 * @subpackage  Functions/ConditionalTags
 * @copyright   Copyright (c) 2019, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.2
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get list of core epl posts
 *
 * @since 2.3
 */
function epl_get_core_post_types() {
	return apply_filters( 'epl_core_post_types', array( 'rural', 'property', 'rental', 'land', 'commercial', 'commercial_land', 'business' ) );
}

/**
 * Get list of sales epl posts
 *
 * @since 2.3
 */
function epl_get_core_sales_post_types() {
	return apply_filters( 'epl_core_sales_post_types', array( 'rural', 'property', 'land', 'commercial', 'commercial_land', 'business' ) );
}

/**
 * Get list of rental epl posts
 *
 * @since 2.3
 */
function epl_get_core_rental_post_types() {
	return apply_filters( 'epl_core_rental_post_types', array( 'rental' ) );
}

/**
 * List of all epl custom post types
 *
 * @since 2.3
 */
function epl_all_post_types() {
	$epl_posts = epl_get_active_post_types();
	$epl_posts = array_keys( $epl_posts );
	return apply_filters( 'epl_additional_post_types', $epl_posts );
}

/**
 * Check if post is core epl post
 *
 * @param string $type EPL post type.
 *
 * @return bool
 * @since 2.3
 */
function is_epl_core_post( $type = null ) {
	$type = is_null( $type ) ? get_post_type() : $type;
	return in_array( $type, epl_get_core_post_types(), true );
}

/**
 * Check if post is sales post
 *
 * @param string $type EPL post type.
 *
 * @return bool
 * @since 2.3
 */
function is_epl_sales_post( $type = null ) {
	$type = is_null( $type ) ? get_post_type() : $type;
	return in_array( $type, epl_get_core_sales_post_types(), true );
}

/**
 * Check if post is rental post
 *
 * @param string $type EPL post type.
 *
 * @return bool
 * @since 2.3
 */
function is_epl_rental_post( $type = null ) {
	$type = is_null( $type ) ? get_post_type() : $type;
	return in_array( $type, epl_get_core_rental_post_types(), true );
}

/**
 * Check if current post is of epl
 *
 * @param string $type EPL post type.
 *
 * @return bool
 * @since 2.2
 */
function is_epl_post( $type = null ) {
	$all_types = epl_all_post_types();
	if ( empty( $all_types ) ) {
		return false;
	}

	$type = null === $type ? get_post_type() : $type;
	return in_array( $type, epl_all_post_types(), true );
}

/**
 * Check if viewing a single post of epl
 *
 * @since 2.2
 */
function is_epl_post_single() {

	$all_types = epl_all_post_types();
	if ( empty( $all_types ) ) {
		return false;
	}

	return is_singular( epl_all_post_types() );
}

/**
 * Check if cpt is from epl
 *
 * @param string $type EPL post type.
 *
 * @return bool
 * @since 2.2
 */
function is_epl_post_type( $type ) {

	$all_types = epl_all_post_types();
	if ( empty( $all_types ) ) {
		return false;
	}

	return ( in_array( $type, epl_all_post_types(), true ) && get_post_type() === $type );
}

/**
 * Check if current post is of epl
 *
 * @since 2.2
 */
function is_epl_post_archive() {

	$all_types = epl_all_post_types();
	if ( empty( $all_types ) ) {
		return false;
	}

	return is_post_type_archive( epl_all_post_types() );
}

/**
 * Same as epl_listing_has_secondary_agent, kept for backward compatibility
 *
 * @return bool|int [type] [description]
 */
function epl_listing_has_secondary_author() {
	return epl_listing_has_secondary_agent();
}

/**
 * Check if listing has secondary agent
 *
 * @since 3.2
 */
function epl_listing_has_secondary_agent() {
	$exists                = false;
	$property_second_agent = get_property_meta( 'property_second_agent' );
	if ( ! empty( $property_second_agent ) ) {
		$second_author = get_user_by( 'login', sanitize_user( $property_second_agent ) );
		if ( false !== $second_author ) {
			$exists = $second_author->ID;
		}
	}
	return $exists;
}

/**
 * Check if listing has primary agent
 *
 * @since 3.2
 */
function epl_listing_has_primary_agent() {
	$exists         = false;
	$property_agent = get_property_meta( 'property_agent' );
	if ( ! empty( $property_agent ) ) {
		$author = get_user_by( 'login', sanitize_user( $property_agent ) );
		if ( false !== $author ) {
			$exists = $author->ID;
		}
	}
	return $exists;
}
