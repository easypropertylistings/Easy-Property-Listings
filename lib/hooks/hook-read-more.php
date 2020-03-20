<?php
/**
 * Hook for Read More Button useful to add to loop templates
 *
 * @package     EPL
 * @subpackage  Hooks/ReadMore
 * @copyright   Copyright (c) 2020, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Outputs a Read More button on the loop listing templates
 *
 * @param string $label Pass a custom label from the template hook.
 *
 * @since 3.4.23 Added epl_button_read_more hook to output button on templates.
 * @since 1.0.0
 */
function epl_button_read_more( $label ) {
	if ( empty( $label ) ) {
		$label = __( 'Read More ', 'easy-property-listings' );
	}
	$label = apply_filters( 'epl_button_label_read_more', $label );
	?><button type="button" class="epl-button epl-read-more" onclick="location.href='<?php the_permalink(); ?>'"><?php echo esc_html( $label ); ?></button>
	<?php
}
add_action( 'epl_button_read_more', 'epl_button_read_more', 10, 1 );
