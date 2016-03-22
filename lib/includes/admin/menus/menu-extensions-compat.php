<?php
/**
 * Extensions Settings Compatibility
 *
 * @package     EPL
 * @subpackage  Admin/Menus
 * @copyright   Copyright (c) 2014, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

$fields = epl_get_admin_option_fields_extensions_compat();
if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'epl_settings') {
	if(!empty($fields)) {
		$epl_settings = get_option('epl_settings');
		foreach($fields as $field_group) {
			foreach($field_group['fields'] as $field) {
				if( $field['type'] == 'radio' || $field['type'] == 'checkbox' ) {
					if(!isset($_REQUEST[ $field['name'] ])) {
						$_REQUEST[ $field['name'] ] = '';
					}
				}

				if($field['type'] == 'text') {
					$_REQUEST[ $field['name'] ] = sanitize_text_field($_REQUEST[ $field['name'] ]);
				}

				$epl_settings[ $field['name'] ] = $_REQUEST[ $field['name'] ];
			}
		}
		update_option('epl_settings', $epl_settings);
	}
}

global $epl_settings;

//get the updated saved settings
$epl_settings = get_option('epl_settings');
?>

<div class="wrap">
	<h2><?php _e('Update your extensions for new features', 'easy-property-listings' ); ?></h2>
	<p><?php _e('Every extension has been updated to use the code base of 2.1 please update. Adjust non supported extension settings on this page.', 'easy-property-listings' ); ?></p>
	<div class="epl-content">
		<form action="" method="post">
			<div class="epl-fields">
				<?php
					if(!empty($fields)) {
						foreach($fields as $field_group) {
							if( !empty($field_group['label']) ) { ?>
								<div class="epl-field">
									<h3><?php _e($field_group['label'], 'easy-property-listings' ); ?></h3>
								</div>
								<?php
							}

							foreach($field_group['fields'] as $field) { ?>
								<div class="epl-field">
									<div class="epl-half-left">
										<label for="<?php echo $field['name']; ?>"><?php _e($field['label'], 'easy-property-listings' ); ?></label>
									</div>
									<div class="epl-half-right">
										<?php
											$val = '';
											if(isset($epl_settings[ $field['name'] ])) {
												$val = $epl_settings[ $field['name'] ];
											}

											switch($field['type']) {
												case 'select':
													$multiple = '';
													$field['id'] = $field['name'];
													if( isset($field['multiple']) && $field['multiple'] ) {
														$multiple = $field['multiple'];
														$field['name'] = $field['name'].'[]';
													}

													echo '<select name="'.$field['name'].'" id="'.$field['id'].'" '.((isset($field['multiple']) && $field['multiple']) ? 'multiple' : false).'>';
														if(!empty($field['default'])) {
															echo '<option value="" selected="selected">'.__($field['default'], 'easy-property-listings' ).'</option>';
														}

														if(!empty($field['opts'])) {
															foreach($field['opts'] as $k=>$v) {
																$selected = '';
																if(is_array($val)) {
																	if(!empty($val)) {
																		if(in_array($k, $val)) {
																			$selected = 'selected="selected"';
																		}
																	}
																} else {
																	if($val == $k) {
																		$selected = 'selected="selected"';
																	}
																}
																echo '<option value="'.$k.'" '.$selected.'>'.__($v, 'easy-property-listings' ).'</option>';
															}
														}
													echo '</select>';
													break;

												case 'checkbox':
													if(!empty($field['opts'])) {
														foreach($field['opts'] as $k=>$v) {
															$checked = '';
															if(!empty($val)) {
																if( in_array($k, $val) ) {
																	$checked = 'checked="checked"';
																}
															}
															echo '<span class="epl-field-row"><input type="checkbox" name="'.$field['name'].'[]" id="'.$field['name'].'_'.$k.'" value="'.$k.'" '.$checked.' /> <label for="'.$field['name'].'_'.$k.'">'.__($v, 'easy-property-listings' ).'</label></span>';
														}
													}
													break;

												case 'radio':
													if(!empty($field['opts'])) {
														foreach($field['opts'] as $k=>$v) {
															$checked = '';
															if($val == $k) {
																$checked = 'checked="checked"';
															}
															echo '<span class="epl-field-row"><input type="radio" name="'.$field['name'].'" id="'.$field['name'].'_'.$k.'" value="'.$k.'" '.$checked.' /> <label for="'.$field['name'].'_'.$k.'">'.__($v, 'easy-property-listings' ).'</label></span>';
														}
													}
													break;

												case 'editor':
													echo '<span class="epl-field-row">';
														wp_editor(stripslashes($val), $field['name'], array('wpautop'=>true, 'textarea_rows'=>5));
													echo '</span>';
													break;

												case 'textarea':
													echo '<span class="epl-field-row">';
														echo '<textarea name="'.$field['name'].'" id="'.$field['name'].'">'.stripslashes($val).'</textarea>';
													echo '</span>';
													break;

												default:
													echo '<input class="wide" type="text" name="'.$field['name'].'" id="'.$field['name'].'" value="'.stripslashes($val).'" />';
											}

											if(isset($field['help'])) {
												$field['help'] = trim($field['help']);
												if(!empty($field['help'])) {
													echo '<span class="epl-help-text">'.__($field['help'], 'easy-property-listings' ).'</span>';
												}
											}
										?>
									</div>
								</div>
							<?php }
						}
					}
				?>
			</div>
			<div class="epl-clear"></div>

			<div class="epl-content-footer">
				<input type="hidden" name="action" value="epl_settings" />
				<p class="submit"><input type="submit" value="<?php _e('Save Changes', 'easy-property-listings' ); ?>" class="button button-primary" id="submit" name="submit"></p>
			</div>
		</form>
	</div>
</div><?php

/**
 * Extensions Compatibility Load Settings
 *
 * @since 1.0
 */
function epl_get_admin_option_fields_extensions_compat() {
	$fields = array( );
	$fields = apply_filters('epl_extensions_options_filter', $fields);
	return $fields;
}
