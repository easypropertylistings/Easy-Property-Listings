<?php
/**
 * Menu Tools
 *
 * @package     EPL
 * @subpackage  Admin/Menu-Tools
 * @copyright   Copyright (c) 2019, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.3
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="wrap epl-wrap">
	<h2><?php esc_html_e( 'Tools', 'easy-property-listings' ); ?></h2>
	<p><?php esc_html_e( 'Visit the main settings page for Easy Property Listings Settings', 'easy-property-listings' ); ?></p>

	<div class="epl-content">

		<div class="epl-tabs">
			<?php
			$tabs    = epl_get_tools_tab(); // phpcs:ignore WordPress.WP.GlobalVariablesOverride
			$current = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : 'tools';// phpcs:ignore WordPress.Security.NonceVerification
			echo '<h1 class="nav-tab-wrapper">';
			foreach ( $tabs as $tab => $tab_options ) { // phpcs:ignore WordPress.WP.GlobalVariablesOverride
				$class = ( $tab === $current ) ? ' nav-tab-active' : '';
				$url   = admin_url( 'admin.php?page=epl-tools&tab=' . $tab );
				$url   = epl_show_reset_tab() ? add_query_arg( array( 'dev' => 'true' ), $url ) : $url;

				echo "<a class='nav-tab" . esc_attr( $class ) . "' href='" . esc_url( $url ) . "'>" . esc_attr( $tab_options['label'] ) . '</a>';
			}
			echo '</h1>';
			?>
		</div>

		<div class="epl-tool-msgs">
			<?php do_action( 'epl_import_status' ); ?>
		</div>

		<div class="epl-tabs-content">
			<form class="epl-tools-form" method="post" enctype="multipart/form-data">
				<?php wp_nonce_field( 'epl_nonce_tools_form', 'epl_nonce_tools_form' ); ?>
				<?php
				if ( isset( $tabs[ $current ] ) ) {
					call_user_func( $tabs[ $current ]['callback'] );
				}
				?>
			</form>
		</div>

	</div>

	<div class="epl-footer"></div>
</div>
