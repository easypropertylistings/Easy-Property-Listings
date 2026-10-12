<?php
/**
 * EPL Actions
 *
 * @package     EPL
 * @subpackage  Functions/Actions
 * @copyright   Copyright (c) 2019, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// phpcs:disable WordPress.Security.NonceVerification

/**
 * Actions that may be triggered by the epl_action / epl-action request parameter.
 *
 * Only these actions are dispatched. Every other epl_* hook is internal and must never be
 * reachable from a URL or form, as it would be called with the raw request as its arguments.
 * Actions are only dispatched for logged in users. Each allowed handler is still responsible
 * for its own capability and nonce checks.
 *
 * @since 3.5.26
 * @return array
 */
function epl_get_allowed_request_actions() {

	$actions = array(
		'new-contact',
		'meta-contact',
		'delete-contact',
		'add-contact-note',
		'add-contact-note-note-tab',
		'filter_reports',
	);

	return (array) apply_filters( 'epl_allowed_request_actions', $actions );
}

/**
 * Dispatch an allowed request action.
 *
 * @since 3.5.26
 * @param string $action Unslashed action name from the request.
 * @param array  $args   Request data passed to the action.
 * @return void
 */
function epl_do_request_action( $action, $args ) {

	if ( ! is_user_logged_in() ) {
		return;
	}

	$action = sanitize_text_field( $action );

	if ( in_array( $action, epl_get_allowed_request_actions(), true ) ) {
		do_action( 'epl_' . $action, $args );
	}
}

/**
 * Hooks EPL front end actions, when present in the $_GET and $_POST superglobal. Each allowed
 * epl_action present in the $_GET or $_POST is called using WordPress's do_action function.
 * Functions triggered on init hook.
 *
 * @since 3.0
 * @since 3.5.26 Only allowed actions are dispatched.
 * @return void
 */
function epl_process_actions() {

	if ( isset( $_POST['epl_action'] ) ) {
		epl_do_request_action( wp_unslash( $_POST['epl_action'] ), $_POST ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	}

	if ( isset( $_GET['epl_action'] ) ) {
		epl_do_request_action( wp_unslash( $_GET['epl_action'] ), $_GET ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	}
}
add_action( 'init', 'epl_process_actions' );

/**
 * Hooks EPL admin actions, when present in the $_GET and $_POST superglobal. Each allowed
 * epl-action present in the $_GET or $_POST is called using WordPress's do_action function.
 * Functions triggered on admin_init hook.
 *
 * @since 3.0
 * @since 3.5.26 Only allowed actions are dispatched.
 * @return void
 */
function epl_process_admin_actions() {

	if ( isset( $_POST['epl-action'] ) ) {
		epl_do_request_action( wp_unslash( $_POST['epl-action'] ), $_POST ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	}

	if ( isset( $_GET['epl-action'] ) ) {
		epl_do_request_action( wp_unslash( $_GET['epl-action'] ), $_GET ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	}
}
add_action( 'admin_init', 'epl_process_admin_actions' );
