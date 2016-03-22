<?php
/**
 * Error Tracking
 *
 * @package     EPL
 * @subpackage  Functions/ErrorTracking
 * @copyright   Copyright (c) 2016, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

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
		$classes = apply_filters( 'epl_error_class', array(
			'epl_errors', 'epl-alert', 'epl-alert-error'
		) );
		echo '<div class="' . implode( ' ', $classes ) . '">';
		    // Loop error codes and display errors
		   foreach ( $errors as $error_id => $error ) {
		        echo '<p class="epl_error" id="epl_error_' . $error_id . '"><strong>' . __( 'Error', 'easy-property-listings' ) . '</strong>: ' . $error . '</p>';
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
 * @return mixed array if errors are present, false if none found
 */
function epl_get_errors() {
	if( session_id() && isset($_SESSION['epl_errors']) ) {
		return $_SESSION['epl_errors'];
	}

}

/**
 * Set Error
 *
 * Stores an error in a session var.
 *
 * @since 3.0
 * @param int $error_id ID of the error being set
 * @param string $error_message Message to store with the error
 * @return void
 */
function epl_set_error( $error_id, $error_message ) {
	$errors = epl_get_errors();
	if ( ! $errors ) {
		$errors = array();
	}
	$errors[ $error_id ] = $error_message;
	$_SESSION['epl_errors'] = $errors;
}

/**
 * Clears all stored errors.
 *
 * @since 3.0
 * @return void
 */
function epl_clear_errors() {
	$_SESSION['epl_errors'] = null;
}

/**
 * Removes (unsets) a stored error
 *
 * @since 3.0
 * @param int $error_id ID of the error being set
 * @return string
 */
function epl_unset_error( $error_id ) {
	$errors = epl_get_errors();
	if ( $errors ) {
		unset( $errors[ $error_id ] );
		$_SESSION['epl_errors'] = $errors;
	}
}

/**
 * pretty var dump
 *
 * @since 3.0
 */
function epl_var_dump($var,$die=false) {
	echo "<pre class=\"epl_var_dump\">";
	var_dump($var);
	echo "</pre>";
	if($die) {
		die();
	}
}

/**
 * pretty print_r
 *
 * @since 3.0
 */
function epl_print_r($var,$die=false) {
	echo "<pre class=\"epl_print_r\">";
	print_r($var);
	echo "</pre>";
	if($die) {
		die();
	}
}
