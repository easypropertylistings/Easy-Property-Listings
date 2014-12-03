<?php
/**
 * Display Menu Page
 *
 * @since 1.0
 * @return void
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

$fields = epl_get_admin_option_fields();
if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'epl_settings') {
	if(!empty($fields)) {
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

				$epl_settings = get_option('epl_settings');
				
				if( isset($field['default']) ) {
					if($_REQUEST[ $field['name'] ] == '') {
						$_REQUEST[ $field['name'] ] = $field['default'];
					}
				}

				$epl_settings[ $field['name'] ] = $_REQUEST[ $field['name'] ];
				update_option('epl_settings', $epl_settings);
			}
		}
	}
}

global $epl_settings;

//get the updated saved settings
$epl_settings = get_option('epl_settings');
?>

<div class="wrap">
	<h2><?php _e('Display and Template Options', 'epl'); ?></h2>
	<p><?php _e('Change the page layouts options for Easy Property Listings', 'epl'); ?></p>
	<div id="epl-menu-display" class="epl-content">
		<form action="" method="post">
			<div class="epl-fields epl-menu-page">
				<?php
					if(!empty($fields)) { ?>
						<div class="epl-field epl-menu-content">
							<?php foreach($fields as $field_group) { 
								$field_id 		= isset($field_group['id']) ? $field_group['id'] : 'extension';
								$field_class 	= isset($field_group['class']) ? $field_group['class'] : 'extension';
							?>
								<div id="epl-<?php echo $field_id; ?>" class="epl-field epl-menu-section epl-<?php echo $field_class; ?>">
										
									<h3 class="epl-section-title"><?php _e($field_group['label'], 'epl'); ?></h3>
									
									<div class="epl-section-content">
										<?php foreach($field_group['fields'] as $field ) { ?>
											<div class="epl-field">
												<div class="epl-half-left">
													<label for="<?php echo $field['name']; ?>"><?php _e($field['label'], 'epl'); ?></label>
												</div>
												<div class="epl-half-right">
													<?php
														$val = '';
														if(isset($epl_settings[ $field['name'] ])) {
															$val = $epl_settings[ $field['name'] ];
														}

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
															case 'number':
																	echo '<input class="validate[custom[onlyNumber]]" type="number" name="'.$field['name'].'" id="'.$field['name'].'" value="'.intval($val).'" />';
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
										<?php } ?>
									</div>
								</div>
							<?php } ?>
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

function epl_get_admin_option_fields() {
	$opts_epl_gallery_n = array();
	for($i=1; $i<=10; $i++) {
		$opts_epl_gallery_n[$i] = $i;
	}

	$opts_epl_features = array();
	for($i=1; $i<=5; $i++) {
		$opts_epl_features[$i] = $i;
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
			'label'		=>	__('General', 'epl'),
			'class'		=>	'core',
			'id'		=>	'general',
			'fields'	=>	array(

				array(
					'name'	=>	'display_bond',
					'label'	=>	__('Rental Bond/Deposit Amount Display (Rental Listing Type)', 'epl'),
					'type'	=>	'radio',
					'opts'	=>	array(
						1	=>	__('On', 'epl'),
						0	=>	__('Off', 'epl')
					),
					'help'	=>	__('Hide or show the bond/deposit on rental listings', 'epl')
				),

				array(
					'name'	=>	'display_single_gallery',
					'label'	=>	__('Automatically display gallery of attached images on the single property page?', 'epl'),
					'type'	=>	'radio',
					'opts'	=>	array(
						1	=>	__('Yes', 'epl'),
						0	=>	__('No', 'epl')
					),
					'help'	=>	__('Enable the Gallery on Single Property Pages', 'epl')
				),

				array(
					'name'	=>	'display_gallery_n',
					'label'	=>	__('Number of columns on the property image gallery', 'epl'),
					'type'	=>	'select',
					'opts'	=>	$opts_epl_gallery_n
				),

				array(
					'name'	=>	'display_feature_columns',
					'label'	=>	__('Number of columns in the property features', 'epl'),
					'type'	=>	'select',
					'opts'	=>	$opts_epl_features
				),

				array(
					'name'	=>	'display_excerpt_length',
					'label'	=>	__('Excerpt word length for individual listings on property archive pages unless a manual excerpt is entered.', 'epl'),
					'type'	=>	'select',
					'opts'	=>	$opts_epl_property_card_excerpt_length
				)
			)
		),
		array(
			'label'		=>	__('Labels', 'epl'),
			'class'		=>	'core',
			'id'		=>	'labels',
			'fields'	=>	array(

				array(
					'name'	=>	'label_bond',
					'label'	=>	__('Rental Bond or Deposit(default is: Bond)', 'epl'),
					'type'	=>	'text'
				),
				
				array(
					'name'	=>	'label_suburb',
					'label'	=>	__('Suburb/City (default is: Suburb)', 'epl'),
					'type'	=>	'text'
				),

				array(
					'name'	=>	'label_postcode',
					'label'	=>	__('Postcode Label (default is: Postcode)', 'epl'),
					'type'	=>	'text'
				),

				array(
					'name'	=>	'label_home_open',
					'label'	=>	__('Home Open Label (default is: Home Open)', 'epl'),
					'type'	=>	'text'
				),

				array(
					'name'	=>	'label_new',
					'label'	=>	__('New Home Label (default is NEW)', 'epl'),
					'type'	=>	'text',
					'default'	=>	'new'
				),
				
				array(
					'name'	=>	'label_poa',
					'label'	=>	__('No Price Label (default is POA)', 'epl'),
					'type'	=>	'text'
				)
			)
		),
		array(
			'label'		=>	__('Search Widget Tab Labels', 'epl'),
			'class'		=>	'core',
			'id'		=>	'labels',
			'fields'	=>	array(

				array(
					'name'	=>	'widget_label_property',
					'label'	=>	__('Property', 'epl'),
					'type'	=>	'text',
					'default'	=>	'Property',
					'help'	=>	__('Will be used by search widget for the tab headings of the listing type - property', 'epl')
				),
				array(
					'name'	=>	'widget_label_land',
					'label'	=>	__('Land', 'epl'),
					'type'	=>	'text',
					'default'	=>	'Land',
					'help'	=>	__('Will be used by search widget for the tab headings of the listing type - land', 'epl')
				),
				array(
					'name'	=>	'widget_label_rental',
					'label'	=>	__('Rental', 'epl'),
					'type'	=>	'text',
					'default'	=>	'Rental',
					'help'	=>	__('Will be used by search widget for the tab headings of the listing type - rental', 'epl')
				),
				array(
					'name'	=>	'widget_label_rural',
					'label'	=>	__('Rural', 'epl'),
					'type'	=>	'text',
					'default'	=>	'Rural',
					'help'	=>	__('Will be used by search widget for the tab headings of the listing type - rural', 'epl')
				),
				array(
					'name'	=>	'widget_label_commercial',
					'label'	=>	__('Commercial', 'epl'),
					'type'	=>	'text',
					'default'	=>	'Commercial',
					'help'	=>	__('Will be used by search widget for the tab headings of the listing type - commercial', 'epl')
				),
				array(
					'name'	=>	'widget_label_commercial_land',
					'label'	=>	__('Commercial Land', 'epl'),
					'type'	=>	'text',
					'default'	=>	'Commercial Land',
					'help'	=>	__('Will be used by search widget for the tab headings of the listing type - commercial land', 'epl')
				),
				array(
					'name'	=>	'widget_label_business',
					'label'	=>	__('Business', 'epl'),
					'type'	=>	'text',
					'default'	=>	'Business',
					'help'	=>	__('Will be used by search widget for the tab headings of the listing type - business', 'epl')
				)
			)
		)
	);

	$fields = apply_filters('epl_display_options_filter', $fields);
	return $fields;
}
