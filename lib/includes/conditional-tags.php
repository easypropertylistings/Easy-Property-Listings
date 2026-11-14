<?php
/**
 * Conditional Tags
 *
 * @package     EPL
 * @subpackage  Functions/ConditionalTags
 * @copyright   Copyright (c) 2020, Merv Barrett
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
	return epl_listing_is_agent_available( 'property_second_agent' );
}

/**
 * Check if listing has third agent
 *
 * @since 3.4.38
 */
function epl_listing_has_third_agent() {
	return epl_listing_is_agent_available( 'property_third_agent' );
}

/**
 * Check if listing has fourth agent
 *
 * @since 3.4.38
 */
function epl_listing_has_fourth_agent() {
	return epl_listing_is_agent_available( 'property_fourth_agent' );
}

/**
 * Check if agent exists
 *
 * @param string $agent_key Agent Key.
 *
 * @return false|int $agent_author Return an array.
 *
 * @since 3.4.38
 */
function epl_listing_is_agent_available( $agent_key = 'property_second_agent' ) {
	$exists = false;
	$agent  = get_property_meta( $agent_key );
	if ( ! empty( $agent ) ) {
		$agent_author = get_user_by( 'login', sanitize_user( $agent ) );
		if ( false !== $agent_author ) {
			$exists = $agent_author->ID;
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

/**
 * Check if the current listing has a value in any of the meta keys
 *
 * A file field saved as an array counts when its image_url_or_path is set. With $respect_show,
 * a key whose epl_show_{$key} filter returns false is skipped, as the listing buttons do.
 *
 * @param array $keys         Meta keys.
 * @param bool  $respect_show Skip keys hidden with the epl_show_{$key} filter.
 *
 * @return bool
 *
 * @since 3.6
 */
function epl_listing_has_any_meta( $keys = array(), $respect_show = false ) {
	global $property;

	if ( ! $property instanceof EPL_Property_Meta ) {
		return false;
	}

	foreach ( (array) $keys as $key ) {
		if ( $respect_show && ! apply_filters( 'epl_show_' . $key, true ) ) {
			continue;
		}

		$value = $property->get_property_meta( $key );

		if ( is_array( $value ) ) {
			$value = isset( $value['image_url_or_path'] ) ? $value['image_url_or_path'] : '';
		}

		if ( is_string( $value ) ) {
			$value = trim( $value );
		}

		if ( ! empty( $value ) ) {
			return true;
		}
	}

	return false;
}

/**
 * Check if the listing has a floor plan
 *
 * Uses the same keys as the Floor Plan button (epl_floorplan_keys filter).
 *
 * @return bool
 *
 * @since 3.6
 */
function epl_listing_has_floor_plans() {
	$keys = apply_filters( 'epl_floorplan_keys', array( 'property_floorplan', 'property_floorplan_2' ) );

	return (bool) apply_filters( 'epl_listing_has_floor_plans', epl_listing_has_any_meta( $keys ) );
}

/**
 * Check if the listing has a video URL
 *
 * @return bool
 *
 * @since 3.6
 */
function epl_listing_has_video() {
	return (bool) apply_filters( 'epl_listing_has_video', epl_listing_has_any_meta( array( 'property_video_url' ) ) );
}

/**
 * Check if the listing has upcoming inspection times
 *
 * Past times and sold or leased listings don't count, as in get_property_inspection_times().
 *
 * @return bool
 *
 * @since 3.6
 */
function epl_listing_has_inspection_times() {
	global $property;

	$has = false;

	if ( $property instanceof EPL_Property_Meta ) {
		$times = $property->get_property_inspection_times();
		$has   = is_string( $times ) && '' !== trim( wp_strip_all_tags( $times ) );
	}

	return (bool) apply_filters( 'epl_listing_has_inspection_times', $has );
}

/**
 * Check if the listing has an auction date
 *
 * @return bool
 *
 * @since 3.6
 */
function epl_listing_has_auction_date() {
	global $property;

	$has = false;

	if ( $property instanceof EPL_Property_Meta ) {
		$auction = $property->get_property_auction();
		$has     = is_string( $auction ) && '' !== trim( $auction );
	}

	return (bool) apply_filters( 'epl_listing_has_auction_date', $has );
}

/**
 * Check if the listing has any button link
 *
 * External links, floor plans, mini web, energy certificate (the keys and epl_show_{$key}
 * filters the single listing buttons use) and the video URL. Use it to skip an empty
 * buttons wrapper in a template.
 *
 * @return bool
 *
 * @since 3.6
 */
function epl_listing_has_links() {
	$keys = array_merge(
		(array) apply_filters( 'epl_external_link_keys', array( 'property_external_link', 'property_external_link_2', 'property_external_link_3' ) ),
		(array) apply_filters( 'epl_floorplan_keys', array( 'property_floorplan', 'property_floorplan_2' ) ),
		(array) apply_filters( 'epl_mini_web_keys', array( 'property_com_mini_web', 'property_com_mini_web_2', 'property_com_mini_web_3' ) ),
		(array) apply_filters( 'epl_energy_certificate_keys', array( 'property_energy_certificate' ) ),
		array( 'property_video_url' )
	);

	return (bool) apply_filters( 'epl_listing_has_links', epl_listing_has_any_meta( $keys, true ) );
}
