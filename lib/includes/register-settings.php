<?php
/**
 * Register Settings
 *
 * @package     EPL
 * @subpackage  Settings
 * @copyright   Copyright (c) 2019, Merv Barrett
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
 * @return array All the settings in array
 * @since 1.0
 */
function epl_settings( $meta_key = '' ) {
	global $epl_settings;
	if ( ! empty( $epl_settings ) ) {
		return $epl_settings;
	}

	$epl_settings = get_option( 'epl_settings' );
	if ( ! empty( $meta_key ) ) {
		return $epl_settings[ $meta_key ];
	} else {
		return $epl_settings;
	}
}
