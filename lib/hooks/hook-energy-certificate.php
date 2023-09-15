<?php
/**
 * Hook for Energy certificate on Property Templates
 *
 * @package     EPL
 * @subpackage  Hooks/EnergyCertificate
 * @copyright   Copyright (c) 2020, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.2.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Outputs energy certificate button on the property templates
 *
 * When the hook epl_buttons_single_property is used and the property
 * has an energy certificate they will be output on the template
 *
 * @since 3.2.0
 * @since 3.4.25 filter epl_show_{key} e.g. epl_show_property_energy_certificate to disable button rendering.
 * @since 3.4.38 Added filter epl_energy_certificate_keys to support additional energy certificates.
 * @since 3.4.49 Fix for label to use esc_html instead of esc_attr.
 */
function epl_button_energy_certificate() {

	$keys = apply_filters( 'epl_energy_certificate_keys', array( 'property_energy_certificate' ) );

	foreach ( $keys as $key ) {
		$link       = get_post_meta( get_the_ID(), $key, true );
		$count      = 'property_energy_certificate' === $key ? '' : substr( $key, - 1 );
		$default    = __( 'Energy Certificate ', 'easy-property-listings' ) . $count;
		$meta_label = get_post_meta( get_the_ID(), $key . '_label', true );
		$meta_label = empty( $meta_label ) ? $default : $meta_label;

		if ( is_array( $link ) ) { // Fallback if metadata is saved as an array.

			if ( ! empty( $link['image_url_or_path'] ) ) {
				$link = $link['image_url_or_path'];
			} else {
				$link = '';
			}
		}

		if ( ! empty( $link ) && apply_filters( 'epl_show_' . $key, true ) ) { ?>
			<button type="button" class="epl-button epl-energy-certificate"
					onclick="window.open('<?php echo esc_url( $link ); ?>')">
				<?php
				if ( has_filter( 'epl_button_label_' . $key ) ) {
					$label = apply_filters( 'epl_button_label_' . $key, $meta_label );
				} else {
					$label = apply_filters( 'epl_button_label_energy_certificate', $meta_label );
				}
				?>
				<?php echo esc_html( $label ); ?>
			</button>
			<?php

		}
	}
}

add_action( 'epl_buttons_single_property', 'epl_button_energy_certificate' );
