<?php
/**
 * Settings updates for 2.1.8
 *
 * @package EPL
 * @subpackage updates
 * @since 2.1.8
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$epl_settings = epl_settings();

$new_fields_defaults = array(
	'epl_use_core_css' => 'off',
);


foreach ( $new_fields_defaults as $key    => $value ) {
	if ( ! isset( $epl_settings[ $key ] ) ) {
		// sure upgrade, fields are not set lets set them for very first time.
		$epl_settings[ $key ] = $value;
	}
}
update_option( 'epl_settings', $epl_settings );
