<?php
/**
 * Admin Plugins
 *
 * @package     EPL
 * @subpackage  Admin/Plugins
 * @copyright   Copyright (c) 2014, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Plugins row action links adapted from Easy Digital Downloads
 *
 * @author Michael Cannon <mc@aihr.us>
 * @since 1.8
 * @param array $links already defined action links
 * @param string $file plugin file path and name being processed
 * @return array $links
 */
function epl_plugin_action_links( $links, $file ) {
	$settings_link = '<a href="' . admin_url( 'admin.php?page=epl-settings' ) . '">' . esc_html__( 'General Settings', 'easy-property-listings'  ) . '</a>';
	if ( $file == 'easy-property-listings/easy-property-listings.php' )
		array_unshift( $links, $settings_link );

	return $links;
}
add_filter( 'plugin_action_links', 'epl_plugin_action_links', 10, 2 );

/**
 * Plugin row meta links adapted from Easy Digital Downloads
 *
 * @author Michael Cannon <mc@aihr.us>
 * @since 1.8
 * @param array $input already defined meta links
 * @param string $file plugin file path and name being processed
 * @return array $input
 */
function epl_plugin_row_meta( $input, $file ) {
	if ( $file != 'easy-property-listings/easy-property-listings.php' )
		return $input;

	$links = array(
		'<a href="' . admin_url( 'index.php?page=epl-getting-started' ) . '">' . esc_html__( 'Getting Started', 'easy-property-listings'  ) . '</a>',
		'<a href="https://www.easypropertylistings.com.au/extensions/?utm_source=plugins-page&utm_medium=plugin-row&utm_campaign=admin">' . esc_html__( 'Add Ons', 'easy-property-listings'  ) . '</a>',
	);

	$input = array_merge( $input, $links );

	return $input;
}
add_filter( 'plugin_row_meta', 'epl_plugin_row_meta', 10, 2 );
