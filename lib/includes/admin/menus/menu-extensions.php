<?php
/**
 * Extension Settings
 *
 * @package     EPL
 * @subpackage  Admin/Menus
 * @copyright   Copyright (c) 2019, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$epl_extensions = epl_get_new_admin_option_fields();
$active_tab     = isset( $_GET['tab'] ) ? sanitize_title( wp_unslash( $_GET['tab'] ) ) : current( array_keys( $epl_extensions ) );
$active_sub_tab = isset( $_GET['sub_tab'] ) ? sanitize_title( wp_unslash( $_GET['sub_tab'] ) ) : '';

if ( isset( $_REQUEST['action'] ) && 'epl_settings' === $_REQUEST['action'] ) {

	if (
		! isset( $_POST['epl_nonce_extension_form'] ) ||
		! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['epl_nonce_extension_form'] ) ), 'epl_nonce_extension_form' )
	) {
		wp_die( esc_html__( 'Sorry, your nonce did not verify.', 'easy-property-listings' ) );
	}

	if ( ! empty( $epl_extensions ) ) {
		if ( array_key_exists( $active_tab, $epl_extensions ) ) {
			$epl_settings     = get_option( 'epl_settings' );
			$ext_field_groups = $epl_extensions[ $active_tab ];
			foreach ( $ext_field_groups['fields'] as $ext_field_group ) {
				foreach ( $ext_field_group['fields'] as $field ) {
					if ( 'radio' === $field['type'] || 'checkbox' === $field['type'] ) {
						if ( ! isset( $_REQUEST[ $field['name'] ] ) ) {
							$_REQUEST[ $field['name'] ] = '';
						} else {
							if ( is_array( $_REQUEST[ $field['name'] ] ) ) {
								$_REQUEST[ $field['name'] ] = array_map( 'sanitize_text_field', wp_unslash( $_REQUEST[ $field['name'] ] ) );
							} else {
								$_REQUEST[ $field['name'] ] = sanitize_text_field( wp_unslash( $_REQUEST[ $field['name'] ] ) );
							}
						}
					}

					if ( 'text' === $field['type'] ) {
						if ( isset( $_REQUEST[ $field['name'] ] ) && is_array( $_REQUEST[ $field['name'] ] ) ) {
							array_walk_recursive( wp_unslash( $_REQUEST[ $field['name'] ] ), 'sanitize_text_field' ); //phpcs:ignore
						}

						if ( isset( $_REQUEST[ $field['name'] ] ) && is_string( $_REQUEST[ $field['name'] ] ) ) {
							$_REQUEST[ $field['name'] ] = sanitize_text_field( wp_unslash( $_REQUEST[ $field['name'] ] ) );
						}
					}

					if ( isset( $_REQUEST[ $field['name'] ] ) ) {
						$epl_settings[ $field['name'] ] = wp_unslash( $_REQUEST[ $field['name'] ] ); //phpcs:ignore

						// Remove fields after adding them to epl_settings.
						unset( $_REQUEST[ $field['name'] ] );
					}
				}
			}

			// Adding some way for dynamic fields to be added to epl_settings array.

			// Settings page params.
			$page_params = apply_filters( 'epl_settings_page_params', array( 'tab', 'page', 'sub_tab', 'action', 'submit' ) );

			// Remove all settings page params from $_REQUEST.
			foreach ( $page_params as $page_param ) {
				if ( isset( $_REQUEST[ $page_param ] ) ) {
					unset( $_REQUEST[ $page_param ] );
				}
			}

			// Remaining fields are probably dynamic fields added via javascript/jquery save them as well if they are not already in $epl_settings.
			foreach ( $_REQUEST as $dynamic_key  => $dynamic_value ) {
				if ( ! empty( $dynamic_value ) ) {
					$epl_settings[ $dynamic_key ] = $dynamic_value;
				}
			}

			update_option( 'epl_settings', $epl_settings );
		}
	}
}

global $epl_settings;
$epl_settings = get_option( 'epl_settings' );

?>
<div class="wrap">
	<h2><?php esc_html_e( 'Extensions Settings', 'easy-property-listings' ); ?></h2>
	<p><?php esc_html_e( 'Configure your extension settings here. Visit the main settings page for more extension settings.', 'easy-property-listings' ); ?></p>
	<h2 class="nav-tab-wrapper epl-nav-tab-wrapper">
		<?php
		foreach ( $epl_extensions as $ext_key => $epl_extension ) {
			$nav_active = $active_tab === $ext_key ? 'nav-tab-active' : '';
			?>
			<a class="nav-tab <?php echo esc_attr( $nav_active ); ?>"
				href="<?php echo esc_url( admin_url( 'admin.php?page=epl-extensions&tab=' . sanitize_title( $ext_key ) ) ); ?>">
				<?php echo esc_attr( $epl_extension['label'] ); ?>
			</a>
			<?php
		}
		?>
	</h2>
	<?php
	// Get fields so that we can get updated fields if added via hook.
	$epl_extensions = epl_get_new_admin_option_fields();
	if ( array_key_exists( $active_tab, $epl_extensions ) ) :
		$ext_field_groups = $epl_extensions[ $active_tab ];
		?>
		<div class="epl-content">
			<?php
			echo '<div class="epl-fields-help-content">';
			foreach ( $ext_field_groups['fields'] as $ext_field_group ) {

				if ( isset( $ext_field_group['intro'] ) && ! empty( $ext_field_group['intro'] ) ) {
					echo '<div class="epl-field epl-field-intro" data-help="tab-menu-' . esc_attr( sanitize_title( $ext_field_group['label'] ) ) . '">';
					echo wp_kses_post( $ext_field_group['intro'] );
					echo '</div>';
				}
			}
			echo '</div>';
			?>

			<form action="" method="post" id="epl_extension_settings_form">
				<div class="epl-fields">
				<?php
				$counter = 1;
				echo '
					<div class="epl-fields-tab-menu">
						<ul>';
				foreach ( $ext_field_groups['fields'] as $ext_field_group ) {

					$current_class = 'tab-menu-' . esc_attr( sanitize_title( $ext_field_group['label'] ) ) === $active_sub_tab ? 'epl-fields-menu-current' : '';

					if ( empty( $active_sub_tab ) && 1 === $counter ) {
						$current_class = 'epl-fields-menu-current';
					}
					if ( ! empty( $ext_field_group['label'] ) ) {
						?>
						<li class="<?php echo esc_attr( $current_class ); ?>" data-tab="<?php echo 'tab-menu-' . esc_attr( sanitize_title( $ext_field_group['label'] ) ); ?>">
							<?php echo esc_attr( $ext_field_group['label'] ); ?>
						</li>
						<?php
					}
					$counter++;
				}
							echo '
						</ul>
					</div>
				';

				if ( ! empty( $ext_field_groups['fields'] ) ) {

					echo '<div class="epl-fields-tab-content">';
					$counter = 1;
					foreach ( $ext_field_groups['fields'] as $field_group ) {
						$current_class = 'tab-menu-' . sanitize_title( $field_group['label'] ) === $active_sub_tab ? 'epl-fields-field-current' : '';
						if ( empty( $active_sub_tab ) && 1 === $counter ) {
							$current_class = 'epl-fields-field-current';
						}
						?>

						<div class="<?php echo esc_attr( $current_class ); ?> epl-fields-single-menu" id="<?php echo 'tab-menu-' . esc_attr( sanitize_title( $field_group['label'] ) ); ?>">
							<?php foreach ( $field_group['fields'] as $field ) { ?>
							<div class="epl-field">
								<div class="epl-help-entry-header">
									<div class="epl_help_entry_content"></div>
								</div>
								<?php if ( 'help' !== $field['type'] ) { ?>
									<div class="epl-half-left">
										<label for="<?php echo esc_attr( $field['name'] ); ?>"><?php echo esc_attr( $field['label'] ); ?></label>
									</div>
								<?php } ?>
								<div class="<?php echo 'help' === $field['type'] ? 'epl-full' : 'epl-half-right'; ?>">
									<?php
									$val = isset( $field['default'] ) ? $field['default'] : '';
									if ( isset( $epl_settings[ $field['name'] ] ) ) {
										$val = $epl_settings[ $field['name'] ];
									}
									epl_render_html_fields( $field, $val );
									?>
								</div>
							</div>
							<?php } ?>
						</div>
						<?php $counter++; ?>
					<?php } ?>
					</div>
				<?php } ?>
				<div class="epl-clear"></div>
				<div class="epl-content-footer">
					<input type="hidden" name="action" value="epl_settings" />
					<?php wp_nonce_field( 'epl_nonce_extension_form', 'epl_nonce_extension_form' ); ?>
					<input type="hidden" name="sub_tab" id="sub_tab" value="" />
					<p class="submit">
						<input type="submit" value="<?php esc_html_e( 'Save Changes', 'easy-property-listings' ); ?>" class="button button-primary" id="submit" name="submit">
					</p>
				</div>
			</form>
		</div>
	<?php endif; ?>
</div>
<?php

/**
 * Get Extension Settings
 *
 * @since 2.0
 */
function epl_get_new_admin_option_fields() {
	$fields = array();
	$fields = apply_filters( 'epl_extensions_options_filter_new', $fields );
	return $fields;
}
