<?php
/**
 * Admin Reports Page
 *
 * @package     EPL
 * @subpackage  Admin/Reports
 * @copyright   Copyright (c) 2016, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Reports Page
 *
 * Renders the reports page contents.
 *
 * @since 3.0
 * @return void
*/
function epl_reports_page() {
	$current_page = admin_url( 'edit.php?post_type=download&page=epl-reports' );
	$active_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'reports';
	?>
	<div class="wrap">
		<h1 class="nav-tab-wrapper">
			<a href="<?php echo add_query_arg( array( 'tab' => 'reports', 'settings-updated' => false ), $current_page ); ?>" class="nav-tab <?php echo $active_tab == 'reports' ? 'nav-tab-active' : ''; ?>"><?php _e( 'Reports', 'easy-property-listings'  ); ?></a>
			<?php do_action( 'epl_reports_tabs' ); ?>
		</h1>

		<?php
		do_action( 'epl_reports_page_top' );
		do_action( 'epl_reports_tab_' . $active_tab );
		do_action( 'epl_reports_page_bottom' );
		?>
	</div><!-- .wrap -->
	<?php
}

/**
 * Default Report Views
 *
 * @since 3.0
 * @return array $views Report Views
 */
function epl_reports_default_views() {

	$views = apply_filters( 'epl_report_views', epl_get_active_post_types() );

	return $views;
}

/**
 * Default Report Views
 *
 * Checks the $_GET['view'] parameter to ensure it exists within the default allowed views.
 *
 * @param string $default Default view to use.
 *
 * @since 3.0
 * @return string $view Report View
 *
 */
function epl_get_reporting_view( $default = 'property' ) {

	if ( ! isset( $_GET['view'] ) || ! in_array( $_GET['view'], array_keys( epl_reports_default_views() ) ) ) {
		$view = $default;
	} else {
		$view = $_GET['view'];
	}

	return apply_filters( 'epl_get_reporting_view', $view );
}

/**
 * Renders the Reports page
 *
 * @since 3.0
 * @return void
 */
function epl_reports_tab_reports() {

	if ( ! is_admin() || ! epl_reports_access() ) {
		wp_die( __( 'You do not have permission to access this report', 'easy-property-listings'  ), __( 'Error', 'easy-property-listings'  ), array( 'response' => 403 ) );
	}

	$current_view = 'property';
	$views        = epl_reports_default_views();

	if ( isset( $_GET['view'] ) && array_key_exists( $_GET['view'], $views ) )
		$current_view = $_GET['view'];

	do_action( 'epl_reports_view_' . $current_view );

}
add_action( 'epl_reports_tab_reports', 'epl_reports_tab_reports' );

/**
 * Renders the Reports Page Views Drop Downs
 *
 * @since 3.0
 * @return void
 */
function epl_report_views() {

	if ( ! is_admin() || ! epl_reports_access() ) {
		return;
	}

	$views        = epl_reports_default_views();
	$current_view = isset( $_GET['view'] ) ? $_GET['view'] : 'property';
	?>
	<form id="epl-reports-filter" method="get">
		<select id="epl-reports-view" name="view">
			<option value="-1"><?php _e( 'Report Type', 'easy-property-listings'  ); ?></option>
			<?php foreach ( $views as $view_id => $label ) : ?>
				<option value="<?php echo esc_attr( $view_id ); ?>" <?php selected( $view_id, $current_view ); ?>><?php echo $label; ?></option>
			<?php endforeach; ?>
		</select>

		<?php do_action( 'epl_report_view_actions' ); ?>

		<input type="hidden" name="page" value="epl-reports"/>
		<?php submit_button( __( 'Show', 'easy-property-listings'  ), 'secondary', 'submit', false ); ?>
	</form>
	<?php
	do_action( 'epl_report_view_actions_after' );
}

/**
 * Renders the Reports property Graphs
 *
 * @since 3.0
 * @return void
 */
function epl_reports_property() {

	if ( ! is_admin() || ! epl_reports_access() ) {
		return;
	}
	?>
	<div class="tablenav top">
		<div class="alignleft actions"><?php epl_report_views(); ?></div>
	</div>
	<?php
	epl_reports_graph('sold','current','#e50000','#a5df41');
}
add_action( 'epl_reports_view_property', 'epl_reports_property' );

/**
 * Renders the Reports rental Graphs
 *
 * @since 3.0
 * @return void
 */
function epl_reports_rental() {

	if ( ! is_admin() || ! epl_reports_access() ) {
		return;
	}
	?>
	<div class="tablenav top">
		<div class="alignleft actions"><?php epl_report_views(); ?></div>
	</div>
	<?php
	epl_reports_graph('leased','current','#e50000','#a5df41');
}
add_action( 'epl_reports_view_rental', 'epl_reports_rental' );

/**
 * Renders the Reports land Graphs
 *
 * @since 3.0
 * @return void
 */
function epl_reports_land() {

	if ( ! is_admin() || ! epl_reports_access() ) {
		return;
	}
	?>
	<div class="tablenav top">
		<div class="alignleft actions"><?php epl_report_views(); ?></div>
	</div>
	<?php
	epl_reports_graph('sold','current','#e50000','#a5df41');
}
add_action( 'epl_reports_view_land', 'epl_reports_land' );

/**
 * Renders the Reports land Graphs
 *
 * @since 3.0
 * @return void
 */
function epl_reports_commercial() {

	if ( ! is_admin() || ! epl_reports_access() ) {
		return;
	}
	?>
	<div class="tablenav top">
		<div class="alignleft actions"><?php epl_report_views(); ?></div>
	</div>
	<?php
	epl_reports_graph('sold','current','#e50000','#a5df41');
}
add_action( 'epl_reports_view_commercial', 'epl_reports_commercial' );

/**
 * Renders the Reports land Graphs
 *
 * @since 3.0
 * @return void
 */
function epl_reports_commercial_land() {

	if ( ! is_admin() || ! epl_reports_access() ) {
		return;
	}
	?>
	<div class="tablenav top">
		<div class="alignleft actions"><?php epl_report_views(); ?></div>
	</div>
	<?php
	epl_reports_graph('sold','current','#e50000','#a5df41');
}
add_action( 'epl_reports_view_commercial_land', 'epl_reports_commercial_land' );

/**
 * Renders the Reports land Graphs
 *
 * @since 3.0
 * @return void
 */
function epl_reports_business() {

	if ( ! is_admin() || ! epl_reports_access() ) {
		return;
	}
	?>
	<div class="tablenav top">
		<div class="alignleft actions"><?php epl_report_views(); ?></div>
	</div>
	<?php
	epl_reports_graph('sold','current','#e50000','#a5df41');
}
add_action( 'epl_reports_view_business', 'epl_reports_business' );

/**
 * Renders the Reports land Graphs
 *
 * @since 3.0
 * @return void
 */
function epl_reports_rural() {

	if ( ! is_admin() || ! epl_reports_access() ) {
		return;
	}
	?>
	<div class="tablenav top">
		<div class="alignleft actions"><?php epl_report_views(); ?></div>
	</div>
	<?php
	epl_reports_graph('sold','current','#e50000','#a5df41');
}
add_action( 'epl_reports_view_rural', 'epl_reports_rural' );

/**
 * Reports Access Roles
 *
 * @since  3.0
 */
function epl_reports_access() {
	$allowed = epl_get_option('min_reports_access');
	$allowed = empty($allowed) ? 'level_10' : $allowed;
	return current_user_can($allowed) ? true : false;
}