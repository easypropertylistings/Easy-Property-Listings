<?php
/**
 * EPL Admin Actions
 *
 * @package     EPL
 * @subpackage  Admin/Actions
 * @copyright   Copyright (c) 2016, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Renders map in address meta box
 *
 * @since 3.0
 * @return void
 */
function epl_admin_listing_map( $address ) { ?>
	<div id='epl_admin_map_canvas' data-address="<?php echo $address; ?>"></div><?php
}
add_action('epl_admin_listing_map','epl_admin_listing_map');

/**
 * Add a class or many to the body in the dashboard
 *
 * @since 3.0
 * @return void
 */
function epl_add_admin_body_class( $classes ) {

	if ( is_epl_post_archive() ) {
		return "$classes epl-admin-page epl-admin-page-archive";
	}

    return "$classes epl-admin-page";
}
add_filter( 'admin_body_class', 'epl_add_admin_body_class' );