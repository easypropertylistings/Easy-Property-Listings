<?php
$fields = get_admin_option_fields();
if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'epl_settings') {
	if(!empty($fields)) {
		foreach($fields as $field_group) {
			foreach($field_group['fields'] as $field) {
				if($field['type'] == 'text') {
					$_REQUEST[ $field['name'] ] = sanitize_text_field($_REQUEST[ $field['name'] ]);
				}
				update_option($field['name'], $_REQUEST[ $field['name'] ]);
			}
		}
	}
} ?>

<div class="wrap">
	<h2><?php _e('Integrations', 'epl'); ?></h2>
	<p><?php _e('Enable your integrations with the settings below', 'epl'); ?></p>
	<div class="epl-content">
		<form action="" method="post">
			<div class="epl-fields">
				<?php
					if(!empty($fields)) {
						foreach($fields as $field_group) { ?>
							<div class="epl-field">
								<strong><h3><?php echo $field_group['label']; ?>:</h3></strong>
							</div>							
							<?php foreach($field_group['fields'] as $field) { ?>
								<div class="epl-field">
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
													echo '<input type="text" name="'.$field['name'].'" id="'.$field['name'].'" value="'.stripslashes($val).'" />';
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
						}
					}
				?>
			</div>
			<div class="epl-clear"></div>
		
			<div class="epl-content-footer">
				<input type="hidden" name="action" value="epl_settings" />
				<p class="submit"><input type="submit" value="Save Changes" class="button button-primary" id="submit" name="submit"></p>
			</div>
		</form>
	</div>
</div><?php

function get_admin_option_fields() {
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
	
	$fields = array(
		array(
			'label'		=>	'1Form Integration Options',
			'fields'	=>	array(
				array(
					'name'	=>	'epl_1form',
					'label'	=>	'Integration - 1Form',
					'type'	=>	'radio',
					'opts'	=>	array(
						1	=>	'On',
						0	=>	'Off'
					),
					'help'	=>	'ON if you have 1Form account for tenants to book inspections and make rental applications'
				),

				array(
					'name'	=>	'epl_1form_code',
					'label'	=>	'Integration - 1Form Integration Code',
					'type'	=>	'text',
					'help'	=>	'Paste your 1Form integration ID'
				),

				array(
					'name'	=>	'epl_1form_button',
					'label'	=>	'Integration - 1Form Button Style',
					'type'	=>	'radio',
					'opts'	=>	array(
						1	=>	'On',
						0	=>	'Off'
					),
				),
			),
		),
		
		array(
			'label'		=>	'Inspect Real Estate',
			'fields'	=>	array(
				array(
					'name'	=>	'epl_book_inspection',
					'label'	=>	'Integration - Inspect Real Estate',
					'type'	=>	'radio',
					'opts'	=>	array(
						1	=>	'On',
						0	=>	'Off'
					),
					'help'	=>	'Add a Book Inspection button to Rentals'
				),

				array(
					'name'	=>	'epl_inspect_re_account',
					'label'	=>	'Integration - Inspect Real Estate Account Name',
					'type'	=>	'text',
					'help'	=>	'Your Inspect Real Estate account name'
				),
			),
		),
	);
	return $fields;
}
