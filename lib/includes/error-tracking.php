<?php
/**
 * Error Tracking
 *
 * @package     EPL
 * @subpackage  Functions/ErrorTracking
 * @copyright   Copyright (c) 2019, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Print Errors
 *
 * @since 3.0
 * @uses epl_get_errors()
 * @uses epl_clear_errors()
 * @return void
 */
function epl_print_errors() {
	$errors = epl_get_errors();
	if ( $errors ) {
		$classes = apply_filters(
			'epl_error_class',
			array(
				'epl_errors',
				'epl-alert',
				'epl-alert-error',
			)
		);
		echo '<div class="' . wp_kses_post( implode( ' ', $classes ) ) . '">';
			// Loop error codes and display errors.
		foreach ( $errors as $error_id => $error ) {
			echo '<p class="epl_error" id="epl_error_' . esc_attr( $error_id ) . '"><strong>' . esc_html__( 'Error', 'easy-property-listings' ) . '</strong>: ' . wp_kses_post( $error ) . '</p>';
		}
		echo '</div>';
		epl_clear_errors();
	}
}

/**
 * Get Errors
 *
 * If errors exist, they are returned.
 *
 * @since 3.0
 * @since 3.1.15 switched to EPL Session class for session handling.
 * @return mixed array if errors are present, false if none found
 */
function epl_get_errors() {
	$errors = EPL()->session->get( 'epl_errors' );
	$errors = apply_filters( 'epl_errors', $errors );
	return $errors;
}

/**
 * Set Error
 *
 * Stores an error in a session var.
 *
 * @since 3.0
 * @since 3.1.15 switched to EPL Session class for session handling.
 * @param int    $error_id ID of the error being set.
 * @param string $error_message Message to store with the error.
 * @return void
 */
function epl_set_error( $error_id, $error_message ) {
	$errors = epl_get_errors();
	if ( ! $errors ) {
		$errors = array();
	}
	$errors[ $error_id ]    = $error_message;
	EPL()->session->set( 'epl_errors', $errors );
}

/**
 * Clears all stored errors.
 *
 * @since 3.0
 * @since 3.1.15 switched to EPL Session class for session handling.
 * @return void
 */
function epl_clear_errors() {
	EPL()->session->set( 'epl_errors', null );
}

/**
 * Removes (unsets) a stored error
 *
 * @since 3.0
 * @since 3.1.15 switched to EPL Session class for session handling.
 * @param int $error_id ID of the error being set.
 */
function epl_unset_error( $error_id ) {
	$errors = epl_get_errors();
	if ( $errors ) {
		unset( $errors[ $error_id ] );
		EPL()->session->set( 'epl_errors', $errors );
	}
}

/**
 * Pretty var dump
 *
 * @param string $var ID of the variable.
 * @param bool   $die Die or not.
 * @since 3.0
 */
function epl_var_dump( $var, $die = false ) {
	echo '<pre class="epl_var_dump">';
	var_dump( $var ); //phpcs:ignore
	echo '</pre>';
	if ( $die ) {
		die();
	}
}

/**
 * Pretty print_r
 *
 * @param array $var Array to be printed.
 * @param bool  $die Die or not.
 * @since 3.0
 */
function epl_print_r( $var, $die = false ) {
	echo '<pre class="epl_print_r">';
	print_r( $var );//phpcs:ignore
	echo '</pre>';
	if ( $die ) {
		die();
	}
}
