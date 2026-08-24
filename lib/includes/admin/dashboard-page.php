<?php
/**
 * EPL Dashboard Admin Page
 *
 * Registers the dashboard admin page and enqueues React app.
 *
 * @package     EPL
 * @subpackage  Admin/Dashboard
 * @since       4.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register Dashboard Submenu Page
 *
 * @since 4.0.0
 */
function epl_register_dashboard_page() {
	add_submenu_page(
		'edit.php?post_type=property',
		__( 'Dashboard', 'easy-property-listings' ),
		__( 'Dashboard', 'easy-property-listings' ),
		'manage_options',
		'epl-dashboard',
		'epl_render_dashboard_page'
	);
}
add_action( 'admin_menu', 'epl_register_dashboard_page', 5 );

/**
 * Render Dashboard Page Container
 *
 * @since 4.0.0
 */
function epl_render_dashboard_page() {
	?>
	<div class="wrap epl-dashboard-wrap">
		<div id="epl-dashboard-root"></div>
	</div>
	<?php
}

/**
 * Enqueue Dashboard Scripts and Styles
 *
 * @since 4.0.0
 */
function epl_enqueue_dashboard_scripts() {
	$screen = get_current_screen();

	if ( ! $screen || 'property_page_epl-dashboard' !== $screen->id ) {
		return;
	}

	$asset_file = EPL_PATH_LIB . 'admin/epl-dashboard/build/index.asset.php';
	$assets     = file_exists( $asset_file ) ? require $asset_file : array(
		'dependencies' => array( 'wp-element', 'wp-components', 'wp-i18n', 'wp-api-fetch' ),
		'version'      => EPL_PROPERTY_VER,
	);

	wp_enqueue_script(
		'epl-dashboard',
		plugins_url( 'lib/admin/epl-dashboard/build/index.js', EPL_PLUGIN_FILE ),
		$assets['dependencies'],
		$assets['version'],
		true
	);

	wp_enqueue_style(
		'epl-dashboard',
		plugins_url( 'lib/admin/epl-dashboard/build/index.css', EPL_PLUGIN_FILE ),
		array( 'wp-components' ),
		$assets['version']
	);

	// Pass configuration to JavaScript.
	wp_localize_script(
		'epl-dashboard',
		'eplDashboard',
		array(
			'restUrl'   => rest_url( 'epl/v1/admin/' ),
			'nonce'     => wp_create_nonce( 'wp_rest' ),
			'adminUrl'  => admin_url(),
			'listingsPageUrl' => admin_url( 'admin.php?page=epl-listings' ),
		)
	);
}
add_action( 'admin_enqueue_scripts', 'epl_enqueue_dashboard_scripts' );
