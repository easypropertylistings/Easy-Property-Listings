<?php
/**
 * EPL Extensions Compatibility
 *
 * @package     EPL
 * @subpackage  Compatibility/Extensions
 * @copyright   Copyright (c) 2014, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/*
 * Staff Directory Compatibility
 *
 * @since 1.0
 */
function epl_sd_author_box_compat() {
	if(has_action( 'epl_single_author' , 'epl_sd_advanced_author_box' ) || has_action( 'epl_single_author' , 'epl_sd_advanced_author_box_tabs' )) {
		remove_action( 'epl_single_author','epl_property_author_box' );
	}
}
add_action( 'init' , 'epl_sd_author_box_compat' );