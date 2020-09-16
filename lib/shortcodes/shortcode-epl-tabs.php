<?php
/**
 * SHORTCODE :: Listing [epl_tabs]
 *
 * @package     EPL
 * @subpackage  Shortcode/epl_tabs
 * @copyright   Copyright (c) 2020, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * EPL Tabs Shortcode [epl_tabs]
 *
 * This shortcode creates tabs with title and content
 * [epl_tabs]
 *
 * @param array $atts Shortcode attributes.
 *
 * @return false|string
 * @since       1.0
 */

function epl_shortcode_epl_tabs_callback( $atts=array(), $content ) {
	global $epl_shortcode_tabs;
	$epl_shortcode_tabs = new EPL_Tabs_Shortcode( $atts, $content );

	return $epl_shortcode_tabs->render();

}
add_shortcode( 'epl_tabs', 'epl_shortcode_epl_tabs_callback' );

function epl_shortcode_epl_tab_callback(  $atts=array(), $content ) {
	global $epl_shortcode_tabs;

	if( !is_a( $epl_shortcode_tabs, 'EPL_Tabs_Shortcode') ) {
		$epl_shortcode_tabs = new EPL_Tabs_Shortcode( null, null, null, true );
	}
	return $epl_shortcode_tabs->render_tab($atts, $content);
}
add_shortcode( 'epl_tab', 'epl_shortcode_epl_tab_callback' );