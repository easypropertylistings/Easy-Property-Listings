<?php
/**
 * Extensions License Menu page
 *
 * @package     EPL
 * @subpackage  Admin/Menus
 * @copyright   Copyright (c) 2014, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// phpcs:disable WordPress.WP.GlobalVariablesOverride

$epl_license = (array) get_option( 'epl_license' );

$fields = epl_get_admin_option_licence_fields();

if ( isset( $_REQUEST['action'] ) && 'epl_settings' === $_REQUEST['action'] ) {

	if (
		! isset( $_POST['epl_nonce_license_form'] ) ||
		! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['epl_nonce_license_form'] ) ), 'epl_nonce_license_form' )
	) {
		wp_die( esc_html__( 'Sorry, your nonce did not verify.', 'easy-property-listings' ) );
	}

	if ( ! empty( $fields ) ) {

		foreach ( $fields as $field_group ) {
			foreach ( $field_group['fields'] as $field ) {

				$field_value = isset( $_REQUEST['epl_license'][ 'epl_' . $field['name'] ] ) ?
				wp_unslash( $_REQUEST['epl_license'][ 'epl_' . $field['name'] ] ) : ''; // phpcs:ignore WordPress.Security

				if ( 'text' === $field['type'] ) {

					$field_value = sanitize_text_field( $field_value );

					$epl_license[ $field['name'] ] = $field_value;
				}
			}
		}

		update_option( 'epl_license', $epl_license );
	}
}


?>

<div class="wrap">
	<h2><?php esc_html_e( 'Licenses Keys For Extensions', 'easy-property-listings' ); ?></h2>
	<p><?php esc_html_e( 'Activate extension updates by entering your license key below. Thanks for purchasing software for Easy Property Listings. You\'re awesome!', 'easy-property-listings' ); ?></p>
	<p><?php esc_html_e( 'Enter Your Activated Extension License Keys', 'easy-property-listings' ); ?></p>

	<div id="epl-menu-licences" class="epl-content metabox-holder">
		<form action="" method="post" class="tba-epl-general-form">
			<div class="epl-fields epl-menu-page">
				<?php if ( ! empty( $fields ) ) { ?>
					<?php
					foreach ( $fields as $field_group ) {
						foreach ( $field_group['fields'] as $field ) {
							?>
							<div id="meta-sortables" class="meta-box-sortables tba-ui-sortable epl-menu-content">
								<div id="epl-<?php echo esc_attr( $field['label'] ); ?>" class="postbox epl-menu-section epl-menu-license epl-<?php esc_attr( $field['type'] ); ?>">

										<!--<div class="handlediv" title="Click to toggle"><br></div>-->
										<h3 class="hndle ui-sortable-handle epl-section-title"><?php echo esc_attr( $field['label'] ); ?></h3>

											<div class="inside epl-field">
												<div class="epl-full epl-license-label">
													<label for="<?php echo esc_attr( $field['name'] ); ?>"><?php esc_html_e( 'Enter your license key', 'easy-property-listings' ); ?></label>
												</div>
												<div class="epl-full">
													<?php
														$val = get_option( $field['name'] );
													switch ( $field['type'] ) {
														case 'select':
															echo '<select name="' . esc_attr( $field['name'] ) . '" id="' . esc_attr( $field['name'] ) . '">';
															if ( ! empty( $field['default'] ) ) {
																echo '<option value="" selected="selected">' . esc_attr( $field['default'] ) . '</option>';
															}

															if ( ! empty( $field['opts'] ) ) {
																foreach ( $field['opts'] as $k => $v ) {
																	$selected = '';
																	if ( $val === $k ) {
																		$selected = 'selected="selected"';
																	}
																	echo '<option value="' . esc_attr( $k ) . '" ' . $selected . '>' . esc_attr( $v ) . '</option>'; // phpcs:ignore 
																}
															}
																echo '</select>';
															break;

														case 'checkbox':
															if ( ! empty( $field['opts'] ) ) {
																foreach ( $field['opts'] as $k => $v ) {
																	$checked = '';
																	if ( ! empty( $val ) ) {
																		if ( in_array( $k, $val, true ) ) {
																			$checked = 'checked="checked"';
																		}
																	}
																	echo '<span class="epl-field-row"><input type="checkbox" name="' . esc_attr( $field['name'] ) . '[]" id="' . esc_attr( $field['name'] ) . '_' . esc_attr( $k ) . '" value="' . esc_attr( $k ) . '" ' . $checked . ' /> <label for="' . esc_attr( $field['name'] ) . '_' . esc_attr( $k ) . '">' . esc_attr( $v ) . '</label></span>'; // phpcs:ignore 
																}
															}
															break;

														case 'radio':
															if ( ! empty( $field['opts'] ) ) {
																foreach ( $field['opts'] as $k => $v ) {
																	$checked = '';
																	if ( $val === $k ) {
																		$checked = 'checked="checked"';
																	}
																	echo '<span class="epl-field-row"><input type="radio" name="' . esc_attr( $field['name'] ) . '" id="' . esc_attr( $field['name'] ) . '_' . esc_attr( $k ) . '" value="' . esc_attr( $k ) . '" ' . $checked . ' /> <label for="' . esc_attr( $field['name'] ) . '_' . esc_attr( $k ) . '">' . esc_attr( $v ) . '</label></span>'; // phpcs:ignore 
																}
															}
															break;

														default:
															$val = '';
															if ( ! empty( $epl_license ) && isset( $epl_license[ $field['name'] ] ) ) {
																$val = $epl_license[ $field['name'] ];
															}
															echo '<input type="text" class="epl-field-license" name="epl_license[epl_' . esc_attr( $field['name'] ) . ']" id="' . esc_attr( $field['name'] ) . '" value="' . esc_attr( $val ) . '" />';
															$status = get_option( 'epl_' . $field['name'] . '_license_active' );

															if ( empty( $status ) || is_null( $status ) || false === $status ) {
																$status = 'invalid';
															} else {

																if ( is_object( $status ) ) {
																	$status = $status->license;
																}
															}

															if ( ! empty( $status ) ) {
																echo '<span class="epl-license-status epl-license-status-' . esc_attr( $status ) . '"></span>';

																if ( 'valid' === $status ) {
																	echo '<span class="epl-license-status-text epl-license-status-text-' . esc_attr( $status ) . '">';
																		echo '<span class="epl-license-status-text-label">' . esc_html__( 'Valid Key :', 'easy-property-listings' ) . '</span>';
																		echo '<span class="epl-license-status-text-value">' . esc_html__( 'Your license key has been successfully validated.', 'easy-property-listings' ) . '</span>';
																	echo '</span>';
																} else {
																	echo '<span class="epl-license-status-text epl-license-status-text-' . esc_attr( $status ) . '">';
																		echo '<span class="epl-license-status-text-label">' . esc_html__( 'Invalid or Expired Key :', 'easy-property-listings' ) . '</span>';
																		echo '<span class="epl-license-status-text-value">' . esc_html__( 'Please make sure you have entered the correct value and that your key is not expired.', 'easy-property-listings' ) . '</span>';
																	echo '</span>';
																}
															}
													}

													if ( isset( $field['help'] ) ) {
														$field['help'] = trim( $field['help'] );
														if ( ! empty( $field['help'] ) ) {
															echo '<span class="epl-help-text">' . wp_kses_post( $field['help'] ) . '</span>';
														}
													}
													?>
												</div>
											</div>

								</div>
							</div>
							<?php
						}
					}
				}
				?>
			</div>
			<div class="epl-clear"></div>

			<div class="epl-content-footer">
				<input type="hidden" name="action" value="epl_settings" />
				<?php wp_nonce_field( 'epl_nonce_license_form', 'epl_nonce_license_form' ); ?>
				<p class="submit"><input type="submit" value="<?php esc_html_e( 'Save Changes', 'easy-property-listings' ); ?>" class="button button-primary" id="submit" name="submit"></p>
			</div>
		</form>
	</div>
</div>
<?php

/**
 * License Fields
 *
 * @since 1.0
 */
function epl_get_admin_option_licence_fields() {
	$opts_epl_gallery_n = array();
	for ( $i = 1; $i <= 10; $i++ ) {
		$opts_epl_gallery_n[ $i ] = $i;
	}

	$opts_epl_property_card_excerpt_length = array();
	for ( $i = 10; $i <= 55; $i++ ) {
		$opts_epl_property_card_excerpt_length[ $i ] = $i;
	}

	$opts_pages = array( '' => __( 'Select Page', 'easy-property-listings' ) );
	$pages      = get_pages();
	if ( ! empty( $pages ) ) {
		foreach ( $pages as $page ) {
			$opts_pages[ $page->ID ] = $page->post_title;
		}
	}

	$fields = apply_filters( 'epl_license_options_filter', array() );
	return $fields;
}
