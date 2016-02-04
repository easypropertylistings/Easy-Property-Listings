<?php
/**
 * EPL Actions
 *
 * @package     EPL
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
*
*	Processes all EPL actions sent via POST and GET by looking for the 'epl_action' . Used to trigger action on init hook
**/
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
*
*	Processes all EPL actions sent via POST and GET by looking for the 'epl-action' . Used to trigger action on admin_init hook
**/
function epl_process_admin_actions() {

	if ( isset( $_POST['epl-action'] ) ) {
		do_action( 'epl_' . $_POST['epl-action'], $_POST );
	}

	if ( isset( $_GET['epl-action'] ) ) {
		do_action( 'epl_' . $_GET['epl-action'], $_GET );
	}
}
add_action( 'admin_init', 'epl_process_admin_actions' );


