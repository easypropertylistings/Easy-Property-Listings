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

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

$fields = epl_get_admin_option_licence_fields();
if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'epl_settings') {
	if(!empty($fields)) {
		$epl_license = array();
		foreach($fields as $field_group) {
			foreach($field_group['fields'] as $field) {
				if( $field['type'] == 'radio' || $field['type'] == 'checkbox' ) {
					if(!isset($_REQUEST[ $field['name'] ])) {
						$_REQUEST[ $field['name'] ] = '';
					}
				}

				if($field['type'] == 'text') {
					$_REQUEST['epl_license']['epl_'.$field['name']] = sanitize_text_field($_REQUEST['epl_license']['epl_'.$field['name']]);
					$epl_license[ $field['name'] ] = $_REQUEST['epl_license']['epl_'.$field['name']];
				} else {
					update_option($field['name'], $_REQUEST[ $field['name'] ]);
				}
			}
		}
		update_option('epl_license', $epl_license);
	}
}
$epl_license = get_option('epl_license');
?>

<div class="wrap">
	<h2><?php _e('Licenses Keys For Extensions', 'epl'); ?></h2>
	<p><?php _e('Activate extension updates by entering your license key below. Thanks for purchasing software for Easy Property Listings. You\'re awesome!', 'epl'); ?></p>

	<div id="epl-menu-licences" class="epl-content metabox-holder">
		<form action="" method="post" class="tba-epl-general-form">
			<div class="epl-fields epl-menu-page">
				<?php if(!empty($fields)) { ?>
					<div id="meta-sortables" class="meta-box-sortables tba-ui-sortable epl-menu-content">
						<div id="epl-<?php echo $field_id; ?>" class="postbox epl-menu-section epl-<?php echo $field_class; ?>">

						<!--<div class="handlediv" title="Click to toggle"><br></div>-->
						<h3 class="hndle ui-sortable-handle epl-section-title"><?php _e( 'Enter Your Activated Extension License Keys' , 'epl'); ?></h3>

							<?php foreach($fields as $field_group) { ?>
								<?php foreach($field_group['fields'] as $field) { ?>
									<div class="inside epl-field">
										<div class="epl-half-left">
											<label for="<?php echo $field['name']; ?>"><?php _e($field['label'], 'epl'); ?></label>
										</div>
										<div class="epl-half-right">
											<?php
												$val = get_option($field['name']);
												switch($field['type']) {
													case 'select':
														echo '<select name="'.$field['name'].'" id="'.$field['name'].'">';
															if(!empty($field['default'])) {
																echo '<option value="" selected="selected">'.__($field['default'], 'epl').'</option>';
															}

															if(!empty($field['opts'])) {
																foreach($field['opts'] as $k=>$v) {
																	$selected = '';
																	if($val == $k) {
																		$selected = 'selected="selected"';
																	}
																	echo '<option value="'.$k.'" '.$selected.'>'.__($v, 'epl').'</option>';
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
																echo '<span class="epl-field-row"><input type="checkbox" name="'.$field['name'].'[]" id="'.$field['name'].'_'.$k.'" value="'.$k.'" '.$checked.' /> <label for="'.$field['name'].'_'.$k.'">'.__($v, 'epl').'</label></span>';
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
																echo '<span class="epl-field-row"><input type="radio" name="'.$field['name'].'" id="'.$field['name'].'_'.$k.'" value="'.$k.'" '.$checked.' /> <label for="'.$field['name'].'_'.$k.'">'.__($v, 'epl').'</label></span>';
															}
														}
														break;

													default:
														$val = '';
														if( !empty($epl_license) && isset($epl_license[$field['name']]) ) {
															$val = $epl_license[$field['name']];
														}
														echo '<input type="text" class="epl-field-license" name="epl_license[epl_'.$field['name'].']" id="'.$field['name'].'" value="'.stripslashes($val).'" />';
														$status = get_option( 'epl_'.$field['name'].'_license_active' );
														if(stripslashes($val) != '') {
															if(!empty($status)) {
																echo '<span class="license-status license-status-'.$status.'"></span>';

																if( $status == 'invalid' ) {
																	echo '<span class="license-status-text license-status-text-'.$status.'">';
																		echo '<span class="license-status-text-label">' . __('Invalid or Expired Key :', 'epl') . '</span>';
																		echo '<span class="license-status-text-value">' . __('Please make sure you have entered the correct value and that your key is not expired.', 'epl') . '</span>';
																	echo '</span>';
																} else if( $status == 'valid' ) {
																	echo '<span class="license-status-text license-status-text-'.$status.'">';
																		echo '<span class="license-status-text-label">' . __('Valid Key :', 'epl') . '</span>';
																		echo '<span class="license-status-text-value">' . __('Your license key has been successfully validated.', 'epl') . '</span>';
																	echo '</span>';
																}
															}
														}
												}

												if(isset($field['help'])) {
													$field['help'] = trim($field['help']);
													if(!empty($field['help'])) {
														echo '<span class="epl-help-text">'.__($field['help'], 'epl').'</span>';
													}
												}
											?>
										</div>
									</div>
								<?php }
							} ?>
						</div>
					</div>
					<?php }
				?>
			</div>
			<div class="epl-clear"></div>

			<div class="epl-content-footer">
				<input type="hidden" name="action" value="epl_settings" />
				<p class="submit"><input type="submit" value="<?php _e('Save Changes', 'epl'); ?>" class="button button-primary" id="submit" name="submit"></p>
			</div>
		</form>
	</div>
</div><?php

/**
 * License Fields
 *
 * @since 1.0
 */
function epl_get_admin_option_licence_fields() {
	$opts_epl_gallery_n = array();
	for($i=1; $i<=10; $i++) {
		$opts_epl_gallery_n[$i] = $i;
	}

	$opts_epl_property_card_excerpt_length = array();
	for($i=10; $i<=55; $i++) {
		$opts_epl_property_card_excerpt_length[$i] = $i;
	}

	$opts_pages = array( '' => __('Select Page', 'epl') );
	$pages = get_pages();
	if(!empty($pages)) {
		foreach($pages as $page) {
			$opts_pages[$page->ID] = $page->post_title;
		}
	}

	$fields = apply_filters('epl_license_options_filter', array());
	return $fields;
}
