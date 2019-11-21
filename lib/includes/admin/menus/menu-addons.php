<?php
/**
 * Add-ons
 *
 * @package     EPL
 * @subpackage  Admin/Menus
 * @copyright   Copyright (c) 2019, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add-ons Page Init
 *
 * Hooks check feed to the page load action.
 * Code adapted from Easy Digital Downloads
 *
 * @since 1.0
 * @global $epl_add_ons_page
 * @return void
 */
function epl_add_ons_init() {
	global $epl_add_ons_page;
	add_action( 'load-' . $epl_add_ons_page, 'epl_add_ons_check_feed' );
}
add_action( 'admin_menu', 'epl_add_ons_init' );


/**
 * Add-ons Get Feed
 *
 * Gets the add-ons page feed.
 *
 * @return mixed|string $cache
 * @since 1.0
 */
function epl_add_ons_get_feed() {
	$cache = get_transient( 'easypropertylistings_add_ons_feed' );
	if ( false === $cache ) {
		$feed = wp_remote_get( 'https://easypropertylistings.com.au/?feed=addons', array( 'timeout' => 120 ) );
		if ( ! is_wp_error( $feed ) ) {
			if ( isset( $feed['body'] ) && strlen( $feed['body'] ) > 0 ) {
				$cache = wp_remote_retrieve_body( $feed );
				set_transient( 'easypropertylistings_add_ons_feed', $cache, 3600 );
			}
		} else {
			$cache = '<div class="error"><p>' . __( 'There was an error retrieving the extensions list from the server. Please try again later.', 'easy-property-listings' ) . '</div>';
		}
	}
	return $cache;
}

/**
 * Add-ons Page
 *
 * Renders the add-ons page content.
 *
 * @since 1.0
 * @return void
 */
function epl_add_ons_page() {
	ob_start(); ?>
	<div class="wrap" id="epl-add-ons">
		<h2>
			<?php esc_html_e( 'Add Ons for Easy Property Listings', 'easy-property-listings' ); ?>
			&nbsp;&mdash;&nbsp;<a href="https://easypropertylistings.com.au/extensions/?utm_source=plugin-addons-page&utm_medium=plugin&ytm_campaign=epl%20Addons%20Page&utm_content=All%20Extensions" class="button-primary" title="<?php esc_html_e( 'Browse All Extensions', 'easy-property-listings' ); ?>" target="_blank"><?php esc_html_e( 'Browse All Extensions', 'easy-property-listings' ); ?></a>
		</h2>
		<p><?php esc_html_e( 'These add-ons extend the functionality of Easy Property Listings.', 'easy-property-listings' ); ?></p>
		<?php echo wp_kses_post( epl_add_ons_get_feed() ); ?>
	</div>
	<?php
	echo wp_kses_post( ob_get_clean() );
}
echo wp_kses_post( epl_add_ons_page() );
