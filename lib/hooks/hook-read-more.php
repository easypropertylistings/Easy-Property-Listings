<?php
/**
 * Hook for Read More Button useful to add to loop templates
 *
 * @package     EPL
 * @subpackage  Hooks/Read_More
 * @copyright   Copyright (c) 2014, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Outputs a Read More button on the loop listing templates
 *
 * @since 1.0
 */
function epl_button_read_more() {
	?><button type="button" class="epl-button epl-read-more" onclick="location.href='<?php the_permalink(); ?>'"><?php echo apply_filters( 'epl_button_label_read_more' , __('Read More ', 'easy-property-listings') ); ?></button><?php
}
