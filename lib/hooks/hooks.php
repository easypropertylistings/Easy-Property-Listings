<?php
/**
 * Wrapper div's for the epl_buttons_single_property hook
 *
 * @package     EPL
 * @subpackage  epl_buttons_single_property
 * @copyright   Copyright (c) 2014, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Outputs a wrapper div before the first button
 */
function epl_buttons_wrapper_before() {
	echo '<div class="epl-button-wrapper epl-clearfix">';
}
/**
 * Outputs a wrapper div after the last button
 */
function epl_buttons_wrapper_after() {
	echo '</div>';
}

add_action('epl_buttons_single_property', 'epl_buttons_wrapper_before' , 1);
add_action('epl_buttons_single_property', 'epl_buttons_wrapper_after' , 99);

/**
 * Flush Rewrite Rules
 */
function epl_flush_rewrite_rules() {
	$epl_rewrite_rules = get_option('epl_rewrite_rules');
	if(!$epl_rewrite_rules) {
		flush_rewrite_rules();
		update_option('epl_rewrite_rules', true);
	}
}
add_action('admin_init', 'epl_flush_rewrite_rules'); 
