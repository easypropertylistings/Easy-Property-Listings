<?php
/**
 * WordPress session managment.
 *
 * Standardizes WordPress session data and uses either database transients or in-memory caching
 * for storing user session information.
 *
 * @package WordPress
 * @since   3.6.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'PHP_SESSION_ACTIVE' ) ) {
	define( 'PHP_SESSION_ACTIVE', 2 );
}
if ( ! defined( 'PHP_SESSION_NONE' ) ) {
	define( 'PHP_SESSION_NONE', 1 );
}

/**
 * Return the current cache expire setting.
 *
 * @return int
 */
function wp_session_cache_expire() {
	$wp_session = WP_Session::get_instance();

	return $wp_session->cache_expiration();
}

/**
 * Alias of wp_session_write_close()
 */
function wp_session_commit() {
	wp_session_write_close();
}

/**
 * Load a JSON-encoded string into the current session.
 *
 * @param string $data JSON-encoded string to load.
 */
function wp_session_decode( $data ) {
	$wp_session = WP_Session::get_instance();

	return $wp_session->json_in( $data );
}

/**
 * Encode the current session's data as a JSON string.
 *
 * @return string
 */
function wp_session_encode() {
	$wp_session = WP_Session::get_instance();

	return $wp_session->json_out();
}

/**
 * Regenerate the session ID.
 *
 * @param bool $delete_old_session Whether to delete the old session.
 *
 * @return bool
 */
function wp_session_regenerate_id( $delete_old_session = false ) {
	$wp_session = WP_Session::get_instance();

	$wp_session->regenerate_id( $delete_old_session );

	return true;
}

/**
 * Start new or resume existing session.
 *
 * Resumes an existing session based on a value sent by the _wp_session cookie.
 *
 * @return bool
 */
function wp_session_start() {
	$wp_session = WP_Session::get_instance();

	$wp_session = WP_Session::get_instance();
	do_action( 'wp_session_start' );

	return $wp_session->session_started();
}
add_action( 'plugins_loaded', 'wp_session_start' );

/**
 * Return the current session status.
 *
 * @return int
 */
function wp_session_status() {
	$wp_session = WP_Session::get_instance();

	if ( $wp_session->session_started() ) {
		return defined( 'PHP_SESSION_ACTIVE' ) ? PHP_SESSION_ACTIVE : 2; // phpcs:ignore PHPCompatibility.Constants.NewConstants.php_session_activeFound
	}

	return defined( 'PHP_SESSION_NONE' ) ? PHP_SESSION_NONE : 1; // phpcs:ignore PHPCompatibility.Constants.NewConstants.php_session_noneFound
}

/**
 * Unset all session variables.
 */
function wp_session_unset() {
	$wp_session = WP_Session::get_instance();

	$wp_session->reset();
}

/**
 * Write session data and end session
 */
function wp_session_write_close() {
	$wp_session = WP_Session::get_instance();

	$wp_session->write_data();
	do_action( 'wp_session_commit' );
}
add_action( 'shutdown', 'wp_session_write_close' );
