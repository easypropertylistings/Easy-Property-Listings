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

/**
 * Google Maps API Key Notification
 *
 * @since 3.1.5
 * @return admin notice text
 */
function epl_admin_google_maps_api_notification() {

	$screen = get_current_screen();

	if ( $screen->id == 'easy-property-listings_page_epl-settings' ) {

		if( current_user_can( 'manage_options' ) && epl_get_option('epl_disable_google_api') != 'on' && epl_get_option('epl_google_api_key') == '' ) { ?>
			<div class="notice notice-error is-dismissible">
				<h3 class="epl-text-red" style="color:#dd3d36"><?php _e( 'Easy Property Listings requires a Google API key for mapping' , 'easy-property-listings' ); ?></h3>

				<p><strong><?php _e( 'As of June 22, 2016 Google has made API keys required for the Javascript Maps API.' , 'easy-property-listings' ); ?></strong><br>

					<?php $link = '<strong><a href="https://developers.google.com/maps/documentation/javascript/get-api-key" target="_blank">' . __( 'Google Maps API Key' , 'easy-property-listings'  ) . '</a></strong>'; ?>
					<?php printf( __( 'To allow maps to function correctly please create a %s and enable <strong>Google Maps API</strong>.', 'easy-property-listings'  ), $link ); ?><br>

					<?php $link = '<strong><a href="#epl-advanced">' . __( 'Advanced Settings' , 'easy-property-listings'  ) . '</a></strong>'; ?>
					<?php printf( __( 'Once complete enter your API key below under %s > Google Maps API Key. When set, refresh page to dismiss notice.', 'easy-property-listings'  ), $link ); ?>
				</p>
			</div>
		<?php
		}
	}
}
add_action( 'admin_notices', 'epl_admin_google_maps_api_notification',5 );