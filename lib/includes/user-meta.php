<?php
/**
 * User Meta Sanitization
 *
 * Input side hardening for the author profile fields. These values are rendered
 * in the author box on the front end, so they are sanitized at the meta layer
 * where every write path passes through, admin or not.
 *
 * @package     EPL
 * @subpackage  Includes/UserMeta
 * @copyright   Copyright (c) 2020, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.5.25
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * User meta keys that hold a profile URL or a social handle.
 *
 * @return array
 * @since 3.5.25
 */
function epl_get_user_link_meta_keys() {
	return apply_filters(
		'epl_user_link_meta_keys',
		array( 'facebook', 'twitter', 'linkedin', 'google', 'instagram', 'pinterest', 'youtube', 'skype', 'video' )
	);
}

/**
 * User meta keys that hold a plain text value.
 *
 * @return array
 * @since 3.5.25
 */
function epl_get_user_text_meta_keys() {
	return apply_filters(
		'epl_user_text_meta_keys',
		array( 'mobile', 'office_phone', 'position', 'slogan', 'contact-form' )
	);
}

/**
 * Sanitize a profile URL or social handle before it is stored.
 *
 * These values end up inside href attributes in the author box. Output is
 * escaped there as well, this is the matching input side guard so a hostile
 * value never reaches the database in the first place. Handles entered without
 * a scheme (for example a bare Twitter username) are preserved, since the
 * author class turns those into absolute URLs at render time.
 *
 * @param mixed $value Meta value being saved.
 *
 * @return mixed
 * @since 3.5.25
 */
function epl_sanitize_user_link_meta( $value ) {

	if ( ! is_string( $value ) || '' === $value ) {
		return $value;
	}

	$value = sanitize_text_field( $value );

	if ( preg_match( '#^[a-z][a-z0-9+.\-]*:#i', $value ) ) {
		// Looks like an absolute URL, so drop anything with a disallowed scheme.
		return esc_url_raw( $value, epl_get_profile_link_protocols() );
	}

	// A bare handle: strip the characters that could break out of an attribute.
	return str_replace( array( '"', "'", '<', '>', '`', '\\' ), '', $value );
}

/**
 * Sanitize a plain text profile value before it is stored.
 *
 * @param mixed $value Meta value being saved.
 *
 * @return mixed
 * @since 3.5.25
 */
function epl_sanitize_user_text_meta( $value ) {
	return is_string( $value ) ? sanitize_text_field( $value ) : $value;
}

/**
 * Protocols permitted in an author profile link.
 *
 * Skype is added to the WordPress list because the author box renders
 * skype: URIs for the Skype contact method.
 *
 * @return array
 * @since 3.5.25
 */
function epl_get_profile_link_protocols() {
	return apply_filters(
		'epl_profile_link_protocols',
		array_values( array_unique( array_merge( wp_allowed_protocols(), array( 'skype' ) ) ) )
	);
}

/**
 * Register the sanitize_user_meta_{$key} callbacks.
 *
 * Hooking the meta layer rather than the profile form covers every write path,
 * including the REST API, wp_update_user() and direct update_user_meta() calls.
 *
 * @since 3.5.25
 */
function epl_register_user_meta_sanitizers() {

	foreach ( epl_get_user_link_meta_keys() as $meta_key ) {
		add_filter( "sanitize_user_meta_{$meta_key}", 'epl_sanitize_user_link_meta', 10, 1 );
	}

	foreach ( epl_get_user_text_meta_keys() as $meta_key ) {
		add_filter( "sanitize_user_meta_{$meta_key}", 'epl_sanitize_user_text_meta', 10, 1 );
	}
}
// Registered immediately so the filters are in place for any early meta write,
// and again on init so late loading plugins can still extend the key lists.
epl_register_user_meta_sanitizers();
add_action( 'init', 'epl_register_user_meta_sanitizers', 1 );
