<?php
/**
 * Settings Menu
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

$fields = epl_get_admin_option_fields();
if ( isset( $_REQUEST['action'] ) && 'epl_settings' === $_REQUEST['action'] ) {

	if (
		! isset( $_POST['epl_nonce_settings_form'] ) ||
		! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['epl_nonce_settings_form'] ) ), 'epl_nonce_settings_form' )
	) {
		wp_die( esc_html__( 'Sorry, your nonce did not verify.', 'easy-property-listings' ) );
	}

	if ( ! empty( $fields ) ) {
		foreach ( $fields as &$field_group ) {
			$field_group['fields'] = array_filter( $field_group['fields'] );
			foreach ( $field_group['fields'] as $field ) {

				$field_value = isset( $_REQUEST[ $field['name'] ] ) ?
				wp_unslash( $_REQUEST[ $field['name'] ] ) : false; // phpcs:ignore WordPress.Security

				if ( 'help' === $field['type'] ) {
					continue;
				}

				if ( 'radio' === $field['type'] || 'checkbox' === $field['type'] || 'checkbox_single' === $field['type'] || 'checkbox_option' === $field['type'] ) {

					if ( ! isset( $_REQUEST[ $field['name'] ] ) ) {

						$field_value = '';

					} else {

						if ( is_array( $_REQUEST[ $field['name'] ] ) ) {

							$field_value = array_map( 'sanitize_text_field', $field_value );

						} else {

							$field_value = sanitize_text_field( $field_value );
						}
					}
				}

				if ( 'text' === $field['type'] ) {

					if ( isset( $_REQUEST[ $field['name'] ] ) ) {

						$field_value = sanitize_text_field( $field_value );
					}
				}

				$epl_settings = get_option( 'epl_settings' );

				if ( isset( $field['default'] ) && ! in_array( $field['type'], array( 'checkbox_single', 'checkbox_option', 'radio' ), true ) ) {

					if ( ! $field_value ) {

						$field_value = $field['default'];
					}
				}
				$epl_settings[ $field['name'] ] = $field_value;
				update_option( 'epl_settings', $epl_settings );
			}
		}
	}
	update_option( 'epl_rewrite_rules', false );
}

global $epl_settings;

// TODO: Get the latest saved settings. Needs re-write.
$epl_settings = get_option( 'epl_settings' );
?>

<div class="wrap">
	<h2><?php esc_html_e( 'General Settings', 'easy-property-listings' ); ?></h2>
	<p><?php esc_html_e( 'Configure Easy Property Listings. Visit ', 'easy-property-listings' ); ?><a href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'epl-getting-started' ), 'index.php' ) ) ); ?>"><?php esc_html_e( 'Getting Started', 'easy-property-listings' ); ?></a><?php esc_html_e( ' for help.', 'easy-property-listings' ); ?></p>

	<div id="epl-menu-general" class="epl-content epl-menu-content-wrapper">
		<form action="" method="post" class="tba-epl-general-form">
			<div class="epl-fields epl-menu-page  metabox-holder">
				<?php if ( ! empty( $fields ) ) { ?>
						<div id="meta-sortables" class="meta-box-sortables ui-sortable epl-menu-content">
							<?php
							foreach ( $fields as &$field_group ) {
								$field_id    = isset( $field_group['id'] ) ? $field_group['id'] : 'extension';
								$field_class = isset( $field_group['class'] ) ? $field_group['class'] : 'extension';
								?>

								<div id="epl-<?php echo esc_attr( $field_id ); ?>" class="postbox epl-menu-section epl-<?php echo esc_attr( $field_class ); ?>">
									<?php
									if ( ! empty( $field_group['label'] ) ) {
										?>
										<!--<div class="handlediv" title="Click to toggle"><br></div>-->
										<h3 class="hndle ui-sortable-handle epl-section-title"><?php echo esc_html( $field_group['label'] ); ?></h3>
										<?php
									}
									?>

									<div class="inside epl-section-content">
										<?php if ( ! empty( $field_group['label'] ) ) { ?>
											<p>
											<?php
											if ( isset( $field_group['help'] ) ) {
												echo wp_kses_post( $field_group['help'] );
											}
											?>
											</p>
											<?php
										}
										?>
										<?php
										$field_group['fields'] = array_filter( $field_group['fields'] );
										foreach ( $field_group['fields'] as $field ) {
											?>
											<div class="epl-field-wrapper epl-clearfix">
												<div class="epl-field">
													<?php if ( 'help' !== $field['type'] ) { ?>
													<div class="epl-half-left">
														<h4 id="epl-field-<?php echo esc_attr( $field['name'] ); ?>" class="epl-setting-heading"><?php echo esc_html( $field['label'] ); ?></h4>

													</div>
													<?php } ?>
													<div class="<?php echo 'help' === $field['type'] ? 'epl-full' : 'epl-half-right'; ?>">
														<?php
															$val = '';
														if ( isset( $epl_settings[ $field['name'] ] ) ) {
															$val = $epl_settings[ $field['name'] ];
														} else {
															if ( isset( $field['default'] ) ) {
																$val = $field['default'];
															}
														}
															epl_render_html_fields( $field, $val );
														?>
													</div>
												</div>
											</div>
										<?php } ?>
									</div>
								</div>
							<?php } ?>
						</div>
					<?php
				}
				?>
			</div>

			<div class="epl-content-footer submit">
				<?php wp_nonce_field( 'epl_nonce_settings_form', 'epl_nonce_settings_form' ); ?>
				<input type="hidden" name="action" value="epl_settings" />
				<p class="submit"><input type="submit" value="<?php esc_attr_e( 'Save Changes', 'easy-property-listings' ); ?>" class="button button-primary" id="submit" name="submit"></p>
			</div>
		</form>
	</div>

	<div class="epl-content-sidebar-wrapper">
		<div class="epl-content-sidebar ">
			<?php epl_admin_sidebar(); ?>
		</div>
	</div>
</div>
