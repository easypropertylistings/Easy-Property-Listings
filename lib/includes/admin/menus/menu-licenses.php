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
	<h2><?php _e('Licenses Keys For Extensions', 'easy-property-listings' ); ?></h2>
	<p><?php _e('Activate extension updates by entering your license key below. Thanks for purchasing software for Easy Property Listings. You\'re awesome!', 'easy-property-listings' ); ?></p>
	<p><?php _e( 'Enter Your Activated Extension License Keys' , 'easy-property-listings' ); ?></p>

	<div id="epl-menu-licences" class="epl-content metabox-holder">
		<form action="" method="post" class="tba-epl-general-form">
			<div class="epl-fields epl-menu-page">
				<?php if(!empty($fields)) { ?>
					<?php foreach($fields as $field_group) {
						foreach($field_group['fields'] as $field) {

							//print_r($field)



						?>
							<div id="meta-sortables" class="meta-box-sortables tba-ui-sortable epl-menu-content">
								<div id="epl-<?php echo $field['label']; ?>" class="postbox epl-menu-section epl-menu-license epl-<?php $field['type']; ?>">

										<!--<div class="handlediv" title="Click to toggle"><br></div>-->
										<h3 class="hndle ui-sortable-handle epl-section-title"><?php _e($field['label'], 'easy-property-listings' ); ?></h3>

											<div class="inside epl-field">
												<div class="epl-full epl-license-label">
													<label for="<?php echo $field['name']; ?>"><?php _e( 'Enter your license key' , 'easy-property-listings' ); ?></label>
												</div>
												<div class="epl-full">
													<?php
														$val = get_option($field['name']);
														switch($field['type']) {
															case 'select':
																echo '<select name="'.$field['name'].'" id="'.$field['name'].'">';
																	if(!empty($field['default'])) {
																		echo '<option value="" selected="selected">'.__($field['default'], 'easy-property-listings' ).'</option>';
																	}

																	if(!empty($field['opts'])) {
																		foreach($field['opts'] as $k=>$v) {
																			$selected = '';
																			if($val == $k) {
																				$selected = 'selected="selected"';
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

															default:
																$val = '';
																if( !empty($epl_license) && isset($epl_license[$field['name']]) ) {
																	$val = $epl_license[$field['name']];
																}
																echo '<input type="text" class="epl-field-license" name="epl_license[epl_'.$field['name'].']" id="'.$field['name'].'" value="'.stripslashes($val).'" />';
																$status = get_option( 'epl_'.$field['name'].'_license_active' );

																if( empty($status) || is_null($status) || $status == false ) {
																	$status = 'invalid';
																} else {

																	if( is_object($status))
																		$status = $status->license;
																}

																if(!empty($status)) {
																	echo '<span class="epl-license-status epl-license-status-'.$status.'"></span>';

																	if( $status == 'valid' ) {
																		echo '<span class="epl-license-status-text epl-license-status-text-'.$status.'">';
																			echo '<span class="epl-license-status-text-label">' . __('Valid Key :', 'easy-property-listings' ) . '</span>';
																			echo '<span class="epl-license-status-text-value">' . __('Your license key has been successfully validated.', 'easy-property-listings' ) . '</span>';
																		echo '</span>';
																	} else  {
																		echo '<span class="epl-license-status-text epl-license-status-text-'.$status.'">';
																			echo '<span class="epl-license-status-text-label">' . __('Invalid or Expired Key :', 'easy-property-listings' ) . '</span>';
																			echo '<span class="epl-license-status-text-value">' . __('Please make sure you have entered the correct value and that your key is not expired.', 'easy-property-listings' ) . '</span>';
																		echo '</span>';
																	}
																}
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

								</div>
							</div>
						<?php }
					}
				} ?>
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

	$opts_pages = array( '' => __('Select Page', 'easy-property-listings' ) );
	$pages = get_pages();
	if(!empty($pages)) {
		foreach($pages as $page) {
			$opts_pages[$page->ID] = $page->post_title;
		}
	}

	$fields = apply_filters('epl_license_options_filter', array());
	return $fields;
}
