<?php
/**
 * Hook for Read More Button useful to add to loop templates
 *
 * @package     EPL
 * @subpackage  epl_buttons_loop_property
 * @copyright   Copyright (c) 2014, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Outputs a Read More button on the loop listing templates
 *
 */
function epl_button_read_more() {
	?><button type="button" class="epl-button epl-read-more" onclick="location.href='<?php the_permalink(); ?>'"><?php echo __('Read More ', 'epl'); ?></button><?php

}
