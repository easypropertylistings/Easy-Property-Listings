<?php
/**
 * Add-ons Page Init
 *
 * Hooks check feed to the page load action.
 * Code adapted from Easy Digital Downloads
 *
 * @since 1.0
 * @global $epl_add_ons_page EPL Add-ons Pages
 * @return void
 */
 
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// Only load in admin
if( !is_admin() ) {
	return;
}

function epl_add_ons_init() {
	global $epl_add_ons_page;
	add_action( 'load-' . $epl_add_ons_page, 'epl_add_ons_check_feed' );
}
add_action( 'admin_menu', 'epl_add_ons_init');

 
/**
 * Add-ons Get Feed
 *
 * Gets the add-ons page feed.
 *
 * @since 1.0
 * @return void
 */
function epl_add_ons_get_feed() {
	if ( false === ( $cache = get_transient( 'easypropertylistings_add_ons_feed' ) ) ) {
		$feed = wp_remote_get( 'http://easypropertylistings.com.au/?feed=addons', array( 'timeout' => 120 ) );
		if ( ! is_wp_error( $feed ) ) {
			if ( isset( $feed['body'] ) && strlen( $feed['body'] ) > 0 ) {
				$cache = wp_remote_retrieve_body( $feed );
				set_transient( 'easypropertylistings_add_ons_feed', $cache, 3600 );
			}
		} else {
			$cache = '<div class="error"><p>' . __( 'There was an error retrieving the extensions list from the server. Please try again later.', 'epl' ) . '</div>';
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
			<?php _e( 'Add Ons for Easy Property Listings', 'epl' ); ?>
			&nbsp;&mdash;&nbsp;<a href="http://easypropertylistings.com.au/extensions/?utm_source=plugin-addons-page&utm_medium=plugin&ytm_campaign=epl%20Addons%20Page&utm_content=All%20Extensions" class="button-primary" title="<?php _e( 'Browse All Extensions', 'epl' ); ?>" target="_blank"><?php _e( 'Browse All Extensions', 'epl' ); ?></a>
		</h2>
		<p><?php _e( 'These add-ons extend the functionality of Easy Property Listings.', 'epl' ); ?></p>
		<?php echo epl_add_ons_get_feed(); ?>
	</div>
	<?php
	echo ob_get_clean();
}
echo epl_add_ons_page();


/*
* End EDD Adapted Code
*/
