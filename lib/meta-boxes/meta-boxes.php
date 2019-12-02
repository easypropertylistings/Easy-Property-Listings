<?php
/**
 * Register custom meta fields for property post types
 *
 * @package     EPL
 * @subpackage  Meta
 * @copyright   Copyright (c) 2019, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


add_action( 'init', 'epl_meta_box_init' );

/**
 * Init rendering of the metabox
 *
 * @since 1.0
 */
function epl_meta_box_init() {

	global $epl_settings;
	global $epl_meta_boxes;

	$opts_property_status              = epl_get_property_status_opts();
	$opts_property_authority           = epl_get_property_authority_opts();
	$opts_property_exclusivity         = epl_get_property_exclusivity_opts();
	$opts_property_com_authority       = epl_get_property_com_authority_opts();
	$opts_area_unit                    = epl_get_property_area_unit_opts();
	$opts_rent_period                  = epl_get_property_rent_period_opts();
	$opts_property_com_listing_type    = epl_get_property_com_listing_type_opts();
	$opts_property_com_tenancy         = epl_get_property_com_tenancy_opts();
	$opts_property_com_property_extent = epl_get_property_com_property_extent_opts();

	$epl_meta_boxes = epl_get_meta_boxes();
}


/**
 * Add meta boxes to the post-edit page
 *
 * @since 1.0
 */
function epl_add_meta_boxes() {

	global $epl_meta_boxes;
	if ( ! empty( $epl_meta_boxes ) ) {
		foreach ( $epl_meta_boxes as $epl_meta_box ) {
			if ( is_array( $epl_meta_box['post_type'] ) ) {
				foreach ( $epl_meta_box['post_type'] as $post_type ) {
					add_meta_box( $epl_meta_box['id'], $epl_meta_box['label'], 'epl_meta_box_inner_custom_box', $post_type, $epl_meta_box['context'], $epl_meta_box['priority'], $epl_meta_box );
				}
			} else {
				add_meta_box( $epl_meta_box['id'], $epl_meta_box['label'], 'epl_meta_box_inner_custom_box', $epl_meta_box['post_type'], $epl_meta_box['context'], $epl_meta_box['priority'], $epl_meta_box );
			}
		}
	}
}

add_action( 'add_meta_boxes', 'epl_add_meta_boxes' );

/**
 * Add sub meta boxes to the post-edit page
 *
 * @param  [type] $post post variable.
 * @param  [type] $args meta box array.
 * @return void       [description]
 * @since 1.0 [<description>]
 */
function epl_meta_box_inner_custom_box( $post, $args ) {

	$groups = $args['args']['groups'];
	$groups = array_filter( $groups );
	if ( ! empty( $groups ) ) {
		wp_nonce_field( 'epl_inner_custom_box', 'epl_inner_custom_box_nonce' );
		foreach ( $groups as $group ) { ?>
			<div class="epl-inner-div col-<?php echo esc_attr( $group['columns'] ); ?> table-<?php echo esc_attr( $args['args']['context'] ); ?>">
				<?php
					$group['label'] = trim( $group['label'] );
				if ( ! empty( $group['label'] ) ) {
					echo '<h3>' . esc_attr( $group['label'] ) . '</h3>';
				}
				?>
				<ul class="form-table epl-form-table">
					<?php
					$fields         = $group['fields'];
					$gp_field_width = isset( $group['width'] ) ? $group['width'] : '1';
					$fields         = array_filter( $fields );
					if ( ! empty( $fields ) ) {
						foreach ( $fields as $field ) {
							if ( isset( $field['exclude'] ) && ! empty( $field['exclude'] ) ) {
								if ( in_array( $post->post_type, $field['exclude'], true ) ) {
									continue;
								}
							}

							if ( isset( $field['include'] ) && ! empty( $field['include'] ) ) {
								if ( ! in_array( $post->post_type, $field['include'], true ) ) {
									continue;
								}
							}
							$val = get_post_meta( $post->ID, $field['name'], true );
							if ( has_action( 'epl_before_meta_field_' . $field['name'] ) ) {
								do_action( 'epl_before_meta_field_' . $field['name'], $post, $val );
							}

							$field_width = isset( $field['width'] ) ? $field['width'] : $gp_field_width;
							?>
							<li id="epl_<?php echo esc_attr( $field['name'] ); ?>_wrap" class="epl-form-field-wrap epl-grid-<?php echo esc_attr( $field_width ); ?> epl_<?php echo esc_attr( $field['name'] ); ?>_wrap epl-field-type-<?php echo esc_attr( $field['type'] ); ?>">


								<?php if ( 'checkbox_single' !== $field['type'] || ( isset( $field['opts'] ) && 1 !== count( $field['opts'] ) ) ) : ?>
								<div class="form-field epl-form-field-label">
									<span valign="top" scope="row">
										<label for="<?php echo esc_attr( $field['name'] ); ?>"><?php echo esc_attr( $field['label'] ); ?></label>
									</span>
								</div>
								<?php endif; ?>


								<div id="epl_<?php echo esc_attr( $field['name'] ); ?>" class="form-field epl-form-field-value epl_<?php echo esc_attr( $field['name'] ); ?>">
									<?php
										epl_render_html_fields( $field, $val );
									?>
								</div>

							</li>

							<?php
							if ( has_action( 'epl_after_meta_field_' . $field['name'] ) ) {
								do_action( 'epl_after_meta_field_' . $field['name'], $post, $val );
							}
							?>
							<?php
						}
					}
					?>
				</ul>
			</div>
			<?php
		}
		?>
		<input type="hidden" name="epl_meta_box_ids[]" value="<?php echo esc_attr( $args['id'] ); ?>" />
		<div class="epl-clear"></div>
		<?php
	}
}

/**
 * Save and update meta box values to the post-edit page
 *
 * @param  [type] $post_ID [description].
 *
 * @return mixed
 * @since 1.0
 * @since 3.4.17	Fixed issue : empty values not getting saved for decimals & numbers
 */
function epl_save_meta_boxes( $post_ID ) {

	if ( ! isset( $_POST['epl_inner_custom_box_nonce'] ) ) {
		return $post_ID;
	}
	if ( ! wp_verify_nonce( sanitize_key( $_POST['epl_inner_custom_box_nonce'] ), 'epl_inner_custom_box' ) ) {
		return $post_ID;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return $post_ID;
	}
	if ( isset( $_POST['post_type'] ) && 'page' === $_POST['post_type'] ) {
		if ( ! current_user_can( 'edit_page', $post_ID ) ) {
			return $post_ID;
		}
	} else {
		if ( ! current_user_can( 'edit_post', $post_ID ) ) {
			return $post_ID;
		}
	}

	$epl_meta_box_ids = '';
	if ( isset( $_POST['epl_meta_box_ids'] ) ) {
		$epl_meta_box_ids = array_map( 'sanitize_text_field', wp_unslash( $_POST['epl_meta_box_ids'] ) );
	}

	if ( ! empty( $epl_meta_box_ids ) ) {
		global $epl_meta_boxes;
		if ( ! empty( $epl_meta_boxes ) ) {
			$epl_enable_import_geocode = get_option( 'epl_enable_import_geocode' );
			foreach ( $epl_meta_box_ids as $epl_meta_box_id ) {
				foreach ( $epl_meta_boxes as $epl_meta_box ) {
					if ( $epl_meta_box['id'] === $epl_meta_box_id ) {
						if ( ! empty( $epl_meta_box['groups'] ) ) {
							foreach ( $epl_meta_box['groups'] as $group ) {

								$fields = $group['fields'];
								if ( ! empty( $fields ) ) {
									foreach ( $fields as $field ) {

										if ( isset( $field['exclude'] ) && ! empty( $field['exclude'] ) ) {
											if ( in_array( $_POST['post_type'], $field['exclude'], true ) ) {
												continue;
											}
										}

										if ( isset( $field['include'] ) && ! empty( $field['include'] ) ) {
											if ( ! in_array( $_POST['post_type'], $field['include'], true ) ) {
												continue;
											}
										}

										switch ( $field['type'] ) {

											case 'radio':
												if ( ! isset( $_POST[ $field['name'] ] ) ) {
													continue 2;
												}

												$meta_value = sanitize_text_field( wp_unslash( $_POST[ $field['name'] ] ) );

												break;

											case 'checkbox_single':
												if ( ! isset( $_POST[ $field['name'] ] ) ) {
													$meta_value = '';
												} else {
													$meta_value = sanitize_text_field( wp_unslash( $_POST[ $field['name'] ] ) );
												}

												break;

											case 'number':
											case 'decimal':
												/** Validate numeric data */
												if ( ! is_numeric( $_POST[ $field['name'] ] ) && ! empty( $_POST[ $field['name'] ] ) ) {
													continue 2;
												}

												$meta_value = sanitize_text_field( wp_unslash( $_POST[ $field['name'] ] ) );

												break;

											case 'textarea':
												$meta_value = sanitize_textarea_field( wp_unslash( $_POST[ $field['name'] ] ) );

												break;

											case 'url':
											case 'file':
												/** Sanitize URLs */
												$meta_value = esc_url_raw( wp_unslash( $_POST[ $field['name'] ] ) );

												break;

											case 'auction-date':
												$meta_value = '';

												if ( isset( $_POST[ $field['name'] ] ) ) {

													$meta_value = sanitize_text_field( wp_unslash( $_POST[ $field['name'] ] ) );

													if ( false !== strpos( $meta_value, 'T' ) ) {
														$meta_value = date( 'Y-m-d\TH:i', strtotime( $meta_value ) );
													} else {
														$meta_value = DateTime::createFromFormat( 'Y-m-d-H:i:s', $meta_value );

														if ( $meta_value ) {
															$meta_value = $meta_value->format( 'Y-m-d\TH:i' );
														}
													}
												}

												break;

											case 'sold-date':
												$meta_value = '';

												if ( isset( $_POST[ $field['name'] ] ) ) {

													$meta_value = sanitize_text_field( wp_unslash( $_POST[ $field['name'] ] ) );

													if ( false !== strpos( $meta_value, 'T' ) ) {
														$meta_value = date( 'Y-m-d\TH:i', strtotime( $meta_value ) );
													} else {
														$meta_value = DateTime::createFromFormat( 'Y-m-d', $meta_value );

														if ( $meta_value ) {
															$meta_value = $meta_value->format( 'Y-m-d' );
														}
													}
												}

												break;

											case 'editor':
												$meta_value = wp_unslash( $_POST[ $field['name'] ] ) ; //phpcs:ignore

												break;

											default:
												$meta_value = sanitize_text_field( wp_unslash( $_POST[ $field['name'] ] ) );

												break;
										}
										$meta_value = apply_filters( 'epl_field_save_meta_' . $field['name'], $meta_value );
										update_post_meta( $post_ID, $field['name'], $meta_value );
									}
								}
							}
						}
					}
				}
			}
		}
	}
}
add_action( 'save_post', 'epl_save_meta_boxes' );
