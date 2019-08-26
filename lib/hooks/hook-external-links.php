<?php
/**
 * Hook for External Link Buttons on Property Templates
 *
 * @package     EPL
 * @subpackage  Hooks/ExternalLinks
 * @copyright   Copyright (c) 2019, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Outputs any external links for virtual tours on the property templates
 *
 * When the hook epl_buttons_single_property is used and the property
 * has external links they will be output on the template
 *
 * @since 1.0
 */
function epl_button_external_link() {

	$keys = array( 'property_external_link', 'property_external_link_2', 'property_external_link_3' );

	foreach ( $keys as $key ) {
		$link       = get_post_meta( get_the_ID(), $key, true );
		$count      = 'property_external_link' === $key ? '' : substr( $key, -1 );
		$default    = __( 'Tour ', 'easy-property-listings' ) . $count;
		$meta_label = get_post_meta( get_the_ID(), $key . '_label', true );
		$meta_label = empty( $meta_label ) ? $default : $meta_label;

		if ( is_array( $link ) ) { // Fallback if meta data is saved as an array.

			if ( ! empty( $link['image_url_or_path'] ) ) {
				$link = $link['image_url_or_path'];
			} else {
				$link = '';
			}
		}

		if ( ! empty( $link ) ) { ?>
			<button type="button" class="epl-button epl-external-link" onclick="window.open('<?php echo esc_url( $link ); ?>')">
				<?php

				if ( has_filter( 'epl_button_label_' . $key ) ) {
					$label = apply_filters( 'epl_button_label_' . $key, $meta_label );
				} else {
					$label = apply_filters( 'epl_button_label_tour', $meta_label );
				}
				?>
				<?php echo esc_attr( $label ); ?>
			</button> 
			<?php

		}
	}

}
add_action( 'epl_buttons_single_property', 'epl_button_external_link' );
