<?php
/**
 * Hook for Mini Website Links used with commercial and business listings
 *
 * @package     EPL
 * @subpackage  Hooks/WebLink
 * @copyright   Copyright (c) 2020, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Outputs any commercial or business mini web links
 *
 * When the hook epl_buttons_single_property is used and the commercial/business
 * property as a mini web links they will be output on the template
 *
 * @since 1.0.0
 * @since 3.4.24 Refactored, added epl_button_label_{$key} filter for labels.
 */
function epl_button_mini_web() {

	$keys = array( 'property_com_mini_web', 'property_com_mini_web_2', 'property_com_mini_web_3' );

	foreach ( $keys as $key ) {

		$link       	= get_post_meta( get_the_ID(), $key, true );
		$count      	= 'property_com_mini_web' === $key ? '' : substr( $key, -1 );
		$meta_label    	= __( 'Mini Web ', 'easy-property-listings' ) . $count;

		// For class.
		$count = empty( $count ) ? '1' : $count;

		if ( ! empty( $link ) ) { ?>
			<button type="button" class="epl-button epl-mini-web-link <?php echo 'epl-mini-web-link-'.$count; ?>" onclick="window.open('<?php echo esc_url( $link ); ?>')">
				<?php

				if ( has_filter( 'epl_button_label_' . $key ) ) {
					$label = apply_filters( 'epl_button_label_' . $key, $meta_label );
				} else {
					$label = apply_filters( 'epl_button_label_mini_web', $meta_label );
				}
				?>
				<?php echo esc_attr( $label ); ?>
			</button>
			<?php
		}
	}
}
add_action( 'epl_buttons_single_property', 'epl_button_mini_web' );