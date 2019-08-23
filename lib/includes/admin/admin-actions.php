<?php
/**
 * EPL Admin Actions
 *
 * @package     EPL
 * @subpackage  Admin/Actions
 * @copyright   Copyright (c) 2019, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Renders map in address meta box
 *
 * @since 3.0.0
 * @param string $address The listing address.
 * @return void
 */
function epl_admin_listing_map( $address ) { ?>
	<div id='epl_admin_map_canvas' data-address="<?php echo esc_attr( $address ); ?>"></div>
	<?php
}
add_action( 'epl_admin_listing_map', 'epl_admin_listing_map' );

/**
 * Add a class or many to the body in the dashboard
 *
 * @since 3.0.0
 * @param string $classes Classes.
 * @return string of classes.
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
 * @return void
 */
function epl_admin_google_maps_api_notification() {

	$screen             = get_current_screen();
	$opt_google_disable = epl_get_option( 'epl_disable_google_api' );
	$opt_google_key     = epl_get_option( 'epl_google_api_key' );

	if ( 'easy-property-listings_page_epl-settings' === $screen->id && current_user_can( 'manage_options' ) ) {

		if ( 'on' === $opt_google_disable || empty( $opt_google_key ) ) {
			?>
			<div class="notice notice-error is-dismissible">
				<h3 class="epl-text-red" style="color:#dd3d36"><?php esc_html_e( 'Easy Property Listings requires a Google API key for mapping', 'easy-property-listings' ); ?></h3>

				<p><strong><?php esc_html_e( 'As of June 22, 2016 Google has made API keys required for the Javascript Maps API.', 'easy-property-listings' ); ?></strong><br>

					<?php $link = '<strong><a href="https://developers.google.com/maps/documentation/javascript/get-api-key" target="_blank">' . __( 'Google Maps API Key', 'easy-property-listings' ) . '</a></strong>'; ?>
					<?php
						/* Translators: %s is a link. */
						printf( wp_kses_post( __( 'To allow maps to function correctly please create a %s and enable <strong>Google Maps API</strong>.', 'easy-property-listings' ) ), $link ); // phpcs:ignore
					?>
					<br>

					<?php $link = '<strong><a href="#epl-advanced">' . __( 'Advanced Settings', 'easy-property-listings' ) . '</a></strong>'; ?>
					<?php
						/* Translators: %s is a link to advanced settings. */
						printf( esc_html__( 'Once complete enter your API key below under %s > Google Maps API Key. When set, refresh page to dismiss notice.', 'easy-property-listings' ), $link ); // phpcs:ignore
					?>
				</p>
			</div>
			<?php
		}
	}
}
add_action( 'admin_notices', 'epl_admin_google_maps_api_notification', 5 );
