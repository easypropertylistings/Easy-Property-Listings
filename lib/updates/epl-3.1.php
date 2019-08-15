<?php
/**
 * Settings updates for 3.1
 *
 * @package EPL
 * @subpackage updates
 * @since 3.1
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$epl_settings = epl_settings();

$new_fields_defaults = array(
	'epl_css_legacy' => 'on',
);

foreach ( $new_fields_defaults as $key    => $value ) {
	if ( ! isset( $epl_settings[ $key ] ) ) {
		// sure upgrade, fields are not set lets set them for very first time.
		$epl_settings[ $key ] = $value;
	}
}
update_option( 'epl_settings', $epl_settings );
