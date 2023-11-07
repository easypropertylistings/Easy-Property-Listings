<?php
/**
 * Outputs a property map onto the property template using a hook
 * This allows us to filter the hook and replace it with a different map
 *
 * @package     EPL
 * @subpackage  Hooks/Map
 * @copyright   Copyright (c) 2020, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Pulls the address details so the map can be generated
 *
 * @param array $args Shortcode options.
 *
 * @since 1.0.0
 * @since 3.5.0 Tweak: [listing_map] Shortcode Added width, height and zoom options and allowed usage on any page. Filter: epl_map_display_defaults added.
 */
function epl_property_map_default_callback( $args ) {

	$defaults = array(
		'width'  => '100%',
		'height' => '350px',
		'zoom'   => '17',
	);

	$args = wp_parse_args( $args, apply_filters( 'epl_map_display_defaults', $defaults ) );

	global $property;

	$api_key      = epl_get_option( 'epl_google_api_key' );
	$show_warning = apply_filters( 'epl_show_map_key_warning', true );

	if ( empty( $api_key ) ) {
		if ( $show_warning && is_user_logged_in() && current_user_can( 'administrator' ) ) {
			epl_map_api_key_warning();
		}
		return;
	}

	// Only show map if address display is set to true.
	if ( 'yes' === $property->get_property_meta( 'property_address_display' ) ) {

		$address = epl_property_get_the_full_address();
		$address = apply_filters( 'epl_map_address', $address );

		// Use coordinates if they are already present.
		$coordinates = $property->get_property_meta( 'property_address_coordinates' );

		echo do_shortcode( '[listing_map zoom=14 width="' . $args['width'] . '" height="' . $args['height'] . '" zoom="' . $args['zoom'] . '" cord="' . $coordinates . '" q="' . $address . '"]' );
	} else {

		$address  = $property->get_property_meta( 'property_address_suburb' ) . ', ';
		$address .= $property->get_property_meta( 'property_address_state' ) . ', ';
		$address .= $property->get_property_meta( 'property_address_postal_code' );
		$address  = apply_filters( 'epl_map_address', $address );
		echo do_shortcode( '[listing_map zoom=14 suburb_mode=1 q="' . $address . '"]' );
	}
}
add_action( 'epl_property_map', 'epl_property_map_default_callback' );

/**
 * Missing map key warning message
 *
 * @since 3.3.0
 */
function epl_map_api_key_warning() { ?>

	<div class="epl-danger epl-warning-map-key">
		<p><?php esc_html_e( 'Ensure you have set a Google Maps API Key from Dashboard > Easy Property Listings > Settings.', 'easy-property-listings' ); ?>
			<em><?php esc_html_e( 'Note: This message is only displayed to logged in administrators.', 'easy-property-listings' ); ?></em>
		</p>
	</div>
	<?php
}
