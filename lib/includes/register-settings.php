<?php
/**
 * Register Settings
 *
 * @package     EPL
 * @subpackage  Functions/Settings
 * @copyright   Copyright (c) 2020, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * EPL Get Settings
 *
 * @since 1.0
 * @return array $epl_settings The settings in array
 */
function epl_get_settings() {
	global $epl_settings;
	if ( ! empty( $epl_settings ) ) {
		return $epl_settings;
	}

	$epl_settings = get_option( 'epl_settings' );

	return $epl_settings;
}

/**
 * EPL Settings Function for returning all or by settings meta_key
 *
 * @param string $meta_key Meta key name.
 * @return mixed All the settings in array, or the value of $meta_key ('' when not set).
 * @since 1.0
 * @since 3.6 Returns the $meta_key value once the settings are loaded, not the whole array. No warning for a missing key.
 */
function epl_settings( $meta_key = '' ) {
	$epl_settings = epl_get_settings();

	if ( empty( $meta_key ) ) {
		return $epl_settings;
	}

	return ( is_array( $epl_settings ) && isset( $epl_settings[ $meta_key ] ) ) ? $epl_settings[ $meta_key ] : '';
}
