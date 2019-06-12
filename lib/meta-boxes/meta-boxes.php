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

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Variables List required for meta boxes
 *
 * @since 1.0
 */
add_action('init', 'epl_meta_box_init');

function epl_meta_box_init() {

	global $epl_settings;
	global $epl_meta_boxes;

	$opts_property_status 			= epl_get_property_status_opts();
	$opts_property_authority 		= epl_get_property_authority_opts();
	$opts_property_exclusivity 		= epl_get_property_exclusivity_opts();
	$opts_property_com_authority 		= epl_get_property_com_authority_opts();
	$opts_area_unit 			= epl_get_property_area_unit_opts();
	$opts_rent_period 			= epl_get_property_rent_period_opts();
	$opts_property_com_listing_type 	= epl_get_property_com_listing_type_opts();
	$opts_property_com_tenancy 		= epl_get_property_com_tenancy_opts();
	$opts_property_com_property_extent 	= epl_get_property_com_property_extent_opts();

	$epl_meta_boxes 			= epl_get_meta_boxes();
}


/**
 * Add meta boxes to the post-edit page
 *
 * @since 1.0
 */
function epl_add_meta_boxes() {

	global $epl_meta_boxes;
	if(!empty($epl_meta_boxes)) {
		foreach($epl_meta_boxes as $epl_meta_box) {
			if( is_array($epl_meta_box['post_type']) ) {
				foreach($epl_meta_box['post_type'] as $post_type) {
					add_meta_box($epl_meta_box['id'], __( $epl_meta_box['label'], 'easy-property-listings'  ), 'epl_meta_box_inner_custom_box', $post_type, $epl_meta_box['context'], $epl_meta_box['priority'], $epl_meta_box);
				}
			} else {
				add_meta_box($epl_meta_box['id'], __( $epl_meta_box['label'], 'easy-property-listings'  ), 'epl_meta_box_inner_custom_box', $epl_meta_box['post_type'], $epl_meta_box['context'], $epl_meta_box['priority'], $epl_meta_box);
			}
		}
	}
}

add_action( 'add_meta_boxes', 'epl_add_meta_boxes' );

/**
 * Add sub meta boxes to the post-edit page
 *
 * @since 1.0
 */
function epl_meta_box_inner_custom_box($post, $args) {

	$groups = $args['args']['groups'];
	$groups = array_filter($groups);
	if(!empty($groups)) {
		wp_nonce_field( 'epl_inner_custom_box', 'epl_inner_custom_box_nonce' );
		foreach($groups as $group) { ?>
			<div class="epl-inner-div col-<?php echo $group['columns']; ?> table-<?php echo $args['args']['context']; ?>">
				<?php
					$group['label'] = trim($group['label']);
					if(!empty($group['label'])) {
						echo '<h3>'.__($group['label'], 'easy-property-listings' ).'</h3>';
					}
				?>
				<ul class="form-table epl-form-table">
					<?php
					$fields = $group['fields'];
					$gp_field_width = isset( $group['width'] ) ? $group['width'] : '1';
					$fields = array_filter($fields);
					if(!empty($fields)) {
						foreach($fields as $field) {
							if(isset($field['exclude']) && !empty($field['exclude'])) {
								if( in_array($post->post_type, $field['exclude']) ) {
									continue;
								}
							}

							if(isset($field['include']) && !empty($field['include'])) {
								if( !in_array($post->post_type, $field['include']) ) {
									continue;
								}
							}
							$val = get_post_meta($post->ID, $field['name'], true);
							if( has_action('epl_before_meta_field_'.$field['name']) ) {
								do_action('epl_before_meta_field_'.$field['name'],$post,$val);
							}

							$field_width = isset( $field['width'] ) ? $field['width'] : $gp_field_width;
							?>
							<li id="epl_<?php echo $field['name']; ?>_wrap" class="epl-form-field-wrap epl-grid-<?php echo $field_width; ?> epl_<?php echo $field['name']; ?>_wrap epl-field-type-<?php echo $field['type'] ?>">


								<?php if($field['type'] != 'checkbox_single' || ( isset($field['opts']) && count($field['opts']) != 1 )  ): ?>
								<div class="form-field epl-form-field-label">
									<span valign="top" scope="row">
										<label for="<?php echo $field['name']; ?>"><?php _e($field['label'], 'easy-property-listings' ); ?></label>
									</span>
								</div>
								<?php endif; ?>


								<div id="epl_<?php echo $field['name']; ?>" class="form-field epl-form-field-value epl_<?php echo $field['name']; ?>">
									<?php
										epl_render_html_fields ($field,$val);
									?>
								</div>

							</li>

							<?php
								if( has_action('epl_after_meta_field_'.$field['name']) ) {
									do_action('epl_after_meta_field_'.$field['name'],$post,$val);
								}
							?>
						<?php }
					}
					?>
				</ul>
			</div>
			<?php
		} ?>
		<input type="hidden" name="epl_meta_box_ids[]" value="<?php echo $args['id']; ?>" />
		<div class="epl-clear"></div>
		<?php
	}
}

/**
 * Save and update meta box values to the post-edit page
 *
 * @since 1.0
 */
function epl_save_meta_boxes( $post_ID ) {
	if ( ! isset( $_POST['epl_inner_custom_box_nonce'] ) )
		return $post_ID;
	$nonce = $_POST['epl_inner_custom_box_nonce'];
	if ( ! wp_verify_nonce( $nonce, 'epl_inner_custom_box' ) )
		return $post_ID;
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
		return $post_ID;
	if ( 'page' == $_POST['post_type'] ) {
		if ( ! current_user_can( 'edit_page', $post_ID ) )
			return $post_ID;
	} else {
		if ( ! current_user_can( 'edit_post', $post_ID ) )
		return $post_ID;
	}

	$epl_meta_box_ids = '';
	if(isset($_POST['epl_meta_box_ids'])) {
		$epl_meta_box_ids = $_POST['epl_meta_box_ids'];
	}

	if(!empty($epl_meta_box_ids)) {
		global $epl_meta_boxes;
		if(!empty($epl_meta_boxes)) {
			$epl_enable_import_geocode = get_option('epl_enable_import_geocode');
			foreach($epl_meta_box_ids as $epl_meta_box_id) {
				foreach($epl_meta_boxes as $epl_meta_box) {
					if($epl_meta_box['id'] == $epl_meta_box_id) {
						if(!empty($epl_meta_box['groups'])) {
							foreach($epl_meta_box['groups'] as $group) {

								$fields = $group['fields'];
								if(!empty($fields)) {
									foreach($fields as $field) {
										if(isset($field['exclude']) && !empty($field['exclude'])) {
											if( in_array($_POST['post_type'], $field['exclude']) ) {
												continue;
											}
										}

										if(isset($field['include']) && !empty($field['include'])) {
											if( !in_array($_POST['post_type'], $field['include']) ) {
												continue;
											}
										}

										if( $field['type'] == 'radio' ) {
											if(!isset($_POST[ $field['name'] ])) {
												continue;
											}
										} else if( $field['type'] == 'checkbox_single') {
											if(!isset($_POST[ $field['name'] ])) {
												$_POST[ $field['name'] ] = '';
											}
										} else if( $field['type'] == 'auction-date' && $_POST[ $field['name'] ] != '') {
											$epl_date = $_POST[ $field['name'] ];
											if(strpos($epl_date, 'T') !== FALSE){
												$epl_date = date("Y-m-d\TH:i",strtotime($epl_date));
											} else {
												$epl_date = DateTime::createFromFormat('Y-m-d-H:i:s', $epl_date);

												if($epl_date)
													$epl_date = $epl_date->format('Y-m-d\TH:i');
											}
											$_POST[ $field['name'] ] = $epl_date;
										} else if( $field['type'] == 'sold-date' && $_POST[ $field['name'] ] != '') {
											$epl_date = $_POST[ $field['name'] ];
											if(strpos($epl_date, 'T') !== FALSE){
												$epl_date = date("Y-m-d\TH:i",strtotime($epl_date));
											} else {
												$epl_date = DateTime::createFromFormat('Y-m-d', $epl_date);

												if($epl_date)
													$epl_date = $epl_date->format('Y-m-d');
											}
											$_POST[ $field['name'] ] = $epl_date;
										}

										update_post_meta( $post_ID, $field['name'], $_POST[ $field['name'] ] );
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

/**
 * Returns a dropdown list for terms
 *
 * @since 1.0
 */
function epl_get_terms_drop_list() {
	$_POST = array_map('trim', $_POST);
	extract($_POST);

	$parent_id = (int) $parent_id;
	$terms = get_terms(
		$type_name,
		array(
			'hide_empty'	=>	0,
			'parent'		=>	$parent_id
		)
	);
	if ( !empty($terms) && !is_wp_error($terms) ) {
		$arr = array('' => '');
		foreach ( $terms as $term ) {
			$arr[$term->term_id] = $term->name;
		}

		if(!empty($arr)) {
			foreach($arr as $k=>$v) {
				$selected = '';
				if($default_value == $k) {
					$selected = 'selected="selected"';
				}
				echo '<option value="'.$k.'" '.$selected.'>'.__($v, 'easy-property-listings' ).'</option>';
			}
		}
	}
}
add_action( 'wp_ajax_epl_get_terms_drop_list', 'epl_get_terms_drop_list' );