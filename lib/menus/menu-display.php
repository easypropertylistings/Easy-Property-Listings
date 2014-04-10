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
	<h2><?php _e('Display', 'epl'); ?></h2>
	<p><?php _e('Change the display options of Property Connected', 'epl'); ?></p>
	<div class="epl-content">
		<form action="" method="post">
			<div class="epl-fields">
				<?php
					if(!empty($fields)) {
						foreach($fields as $field_group) { ?>
							<div class="epl-field">
								<strong><u><?php echo $field_group['label']; ?>:</u></strong>
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
			'label'		=>	'Single Property Page Display Options',
			'fields'	=>	array(
				array(
					'name'	=>	'epl_display_single_property',
					'label'	=>	'Property Single Display Style',
					'type'	=>	'select',
					'opts'	=>	array(
						0	=>	'Expanded',
						1	=>	'Condensed'
					),
				),

				array(
					'name'	=>	'epl_display_single_gallery',
					'label'	=>	'Property Single Display Gallery',
					'type'	=>	'radio',
					'opts'	=>	array(
						1	=>	'Yes',
						0	=>	'No'
					),
					'help'	=>	'Enable the Gallerty on Single Property Pages'
				),

				array(
					'name'	=>	'epl_display_single_map_position',
					'label'	=>	'Property Single Display Map Location',
					'type'	=>	'select',
					'opts'	=>	array(
						0	=>	'Separate Map',
						1	=>	'Maps with Featured Image'
					),
				),
			),
		),
		
		array(
			'label'		=>	'Card Display Options',
			'fields'	=>	array(
				array(
					'name'	=>	'epl_property_card_excerpt_length',
					'label'	=>	'Excerpt length on property archive pages',
					'type'	=>	'select',
					'opts'	=>	$opts_epl_property_card_excerpt_length
				),
				
				array(
					'name'	=>	'epl_property_card_style',
					'label'	=>	'Property Card Style',
					'type'	=>	'select',
					'opts'	=>	array( 
						0	=>	'(Default) Details Right',
						1	=>	'Address Top',
						2	=>	'Slim List',
						3	=>	'Suburb Top'
					),
				),
			),
		),
		
		array(
			'label'		=>	'Graph Comparison Options',
			'fields'	=>	array(
				array(
					'name'	=>	'epl_gallery_n',
					'label'	=>	'Number of Gallery Images',
					'type'	=>	'select',
					'opts'	=>	$opts_epl_gallery_n
				),				
		
				array(
					'name'	=>	'epl_staff_link_to',
					'label'	=>	'Staff Directory',
					'type'	=>	'radio',
					'opts'	=>	array(
						0	=>	'Link to Author Profile',
						1	=>	'Link to Static Directory Page'
					),
				),

				array(
					'name'	=>	'epl_staff_image_type',
					'label'	=>	'Staff Image Type',
					'type'	=>	'radio',
					'opts'	=>	array(
						0	=>	'Use Gravatar Image',
						1	=>	'Use Staff Directory Image'
					),
				),
		
				array(
					'name'	=>	'epl_staff_excerpt',
					'label'	=>	'Excerpt on Directory Archive Page',
					'type'	=>	'radio',
					'opts'	=>	array(
						0	=>	'No Excerpt',
						1	=>	'Display Excerpt'
					),
				),
		
				array(
					'name'	=>	'epl_dynamic_description',
					'label'	=>	'Dynamic Property Description *beta',
					'type'	=>	'radio',
					'opts'	=>	array(			
						1	=>	'On',
						0	=>	'Off'
					),
					'help'	=>	'Enable Dynamic Description'
				),
		
				array(
					'name'	=>	'epl_bond_display',
					'label'	=>	'Bond Display',
					'type'	=>	'radio',
					'opts'	=>	array(			
						1	=>	'On',
						0	=>	'Off'
					),
					'help'	=>	'Enable Bond Display'
				),
		
				array(
					'name'	=>	'epl_graph_on_off',
					'label'	=>	'Graph Comparison',
					'type'	=>	'radio',
					'opts'	=>	array(
						1	=>	'On',
						0	=>	'Off'
					),
					'help'	=>	'The graph system offers  a visual graph to allow the visitor to see comparable properties'
				),

				array(
					'name'	=>	'epl_max_graph_property_price',
					'label'	=>	'Max Graph Property Price',
					'type'	=>	'text'
				),

				array(
					'name'	=>	'epl_max_graph_rental_price',
					'label'	=>	'Max Graph Rental Price',
					'type'	=>	'text'
				),
			),
		),
	);
	return $fields;
}
