<?php
/**
 * EPL Admin Functions
 *
 * @package     EPL
 * @subpackage  Admin/Functions
 * @copyright   Copyright (c) 2014, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Flush Rewrite Rules
 *
 * @since 1.0
 */
function epl_flush_rewrite_rules() {
	$epl_rewrite_rules = get_option('epl_rewrite_rules');
	if(!$epl_rewrite_rules) {
		flush_rewrite_rules();
		update_option('epl_rewrite_rules', true);
	}
}
add_action('admin_init', 'epl_flush_rewrite_rules');
add_action('init', 'epl_flush_rewrite_rules');
