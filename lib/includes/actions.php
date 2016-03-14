<?php
/**
 * EPL Actions
 *
 * @package     EPL
 * @subpackage  Functions/Actions
 * @copyright   Copyright (c) 2016, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Hooks EPL front end actions, when present in the $_GET and $_POST superglobal. Every epl_action
 * present in the $_GET or $_POST is called using WordPress's do_action function. Functions
 * triggered on init hook.
 *
 * @since 3.0
 * @return void
 */
function epl_process_actions() {

	if ( isset( $_POST['epl_action'] ) ) {
		do_action( 'epl_' . $_POST['epl_action'], $_POST );
	}

	if ( isset( $_GET['epl_action'] ) ) {
		do_action( 'epl_' . $_GET['epl_action'], $_GET );
	}
}
add_action( 'init', 'epl_process_actions' );

/**
 * Hooks EPL admin actions, when present in the $_GET and $_POST superglobal. Every epl_action
 * present in the $_GET or $_POST is called using WordPress's do_action function. Functions
 * triggered on init hook.
 *
 * @since 3.0
 * @return void
 */
function epl_process_admin_actions() {

	if ( isset( $_POST['epl-action'] ) ) {
		do_action( 'epl_' . $_POST['epl-action'], $_POST );
	}

	if ( isset( $_GET['epl-action'] ) ) {
		do_action( 'epl_' . $_GET['epl-action'], $_GET );
	}
}
add_action( 'admin_init', 'epl_process_admin_actions' );


