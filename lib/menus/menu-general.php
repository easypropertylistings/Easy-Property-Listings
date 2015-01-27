<?php
/**
 * General Menu Page options
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
					if($_REQUEST[ $field['name'] ] == '' || $_REQUEST[ $field['name'] ] == 0) {
						$_REQUEST[ $field['name'] ] = $field['default'];
					}
				}
				$epl_settings[ $field['name'] ] = $_REQUEST[ $field['name'] ];
				
				update_option('epl_settings', $epl_settings);
			}
		}
	}
	update_option('epl_rewrite_rules', false);
}

global $epl_settings;

//get the latest saved settings
$epl_settings = get_option('epl_settings');
?>

<div class="wrap">
	<h2><?php _e('General Settings', 'epl'); ?></h2>
	<p><?php _e('Configure Easy Property Listings. Visit ', 'epl'); ?><a href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'epl-getting-started' ), 'index.php' ) ) ); ?>"><?php _e( 'Getting Started', 'epl' ); ?></a><?php _e(' for help.', 'epl');?></p>
	
	<div id="epl-menu-general" class="epl-content epl-menu-content-wrapper">
		<form action="" method="post" class="tba-epl-general-form">
			<div class="epl-fields epl-menu-page  metabox-holder">
				<?php if(!empty($fields)) { ?>
						<div id="meta-sortables" class="meta-box-sortables ui-sortable epl-menu-content">
							<?php foreach($fields as $field_group) {
								$field_id 	= isset($field_group['id']) ? $field_group['id'] : 'extension';
								$field_class 	= isset($field_group['class']) ? $field_group['class'] : 'extension';
								?>
								
								<div id="epl-<?php echo $field_id; ?>" class="postbox epl-menu-section epl-<?php echo $field_class; ?>">
									<?php
									if( !empty($field_group['label']) ) { ?>
										<!--<div class="handlediv" title="Click to toggle"><br></div>-->
										<h3 class="hndle ui-sortable-handle epl-section-title"><?php _e($field_group['label'], 'epl'); ?></h3>
									<?php
									} ?>
									
									<div class="inside epl-section-content">
										<?php if( !empty($field_group['label']) ) { ?>
											<p><?php if(isset($field_group['help'])) _e($field_group['help'], 'epl'); ?></p>
										<?php
										} ?>
										<?php foreach($field_group['fields'] as $field) { ?>
											<div class="epl-field-wrapper epl-clearfix">
												<div class="epl-field">
													<div class="epl-half-left">
														<h4 id="epl-field-<?php echo $field['name']; ?>" class="epl-setting-heading"><?php _e($field['label'], 'epl'); ?></h4>
														
														<?php if(isset($field['help'])) {
															$field['help'] = trim($field['help']);
															if(!empty($field['help'])) {
																echo '<span class="epl-help-text">'.__($field['help'], 'epl').'</span>';
															}
														} ?>
															
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
																	echo '<input class="epl-input-normal" type="text" name="'.$field['name'].'" id="'.$field['name'].'" value="'.stripslashes($val).'" />';
															}
														?>
													</div>
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
			
			<div class="epl-content-footer submit">
				<input type="hidden" name="action" value="epl_settings" />
				<p class="submit"><input type="submit" value="<?php _e('Save Changes', 'epl'); ?>" class="button button-primary" id="submit" name="submit"></p>
			</div>
		</form>
	</div>
	
	<div class="epl-content-sidebar-wrapper">
		<div class="epl-content-sidebar ">
			<?php epl_admin_sidebar (); ?>
		</div>
	</div>
</div>



<?php
function epl_get_admin_option_fields() {
	$opts_epl_gallery_n = array();
	for($i=1; $i<=10; $i++) {
		$opts_epl_gallery_n[$i] = $i;
	}

	$opts_epl_property_card_excerpt_length = array();
	for($i=10; $i<=55; $i++) {
		$opts_epl_property_card_excerpt_length[$i] = $i;
	}

	$opts_epl_features = array();
	for($i=1; $i<=5; $i++) {
		$opts_epl_features[$i] = $i;
	}

	$opts_pages = array( '' => __('Select Page', 'epl') );
	$pages = get_pages();
	
	if(!empty($pages)) {
		foreach($pages as $page) {
			$opts_pages[$page->ID] = $page->post_title;
		}
	}

	$epl_currency_positions = array(
			'before'	=> __('Before - $10', 'epl'), 
			'after'		=> __('After - 10$', 'epl')
			);
	$epl_currency_types = epl_get_currencies();
	$epl_post_types = epl_get_post_types();

	$fields = array(
		array(
			'label'		=>	__('Listing Types and Location Settings' , 'epl'),
			'class'		=>	'core',
			'id'		=>	'general',
			'help'		=>	__('Select the listing types you want to enable and press Save Changes. Refresh the page to see your new activated listing types.' , 'epl'),
			'fields'	=>	array(
				array(
					'name'	=>	'activate_post_types',
					'label'	=>	__('Listing Types to Enable', 'epl'),
					'type'	=>	'checkbox',
					'opts'	=>	$epl_post_types,
					'help'	=>	__('Note: If they are not visible on the front end visit Dashboard > Settings > Permalinks and press Save Changes.' , 'epl')
				),
				
				array(
					'name'	=>	'label_location',
					'label'	=>	__('Location Taxonomy', 'epl'),
					'type'	=>	'text'
				),
				
				array(
					'name'	=>	'sticker_new_range',
					'label'	=>	__('Keep Listings flagged "New" for', 'epl'),
					'type'	=>	'number',
					'default'	=>	'7',
					'help'	=>	__('Listings will have a "NEW" Sticker for the defined number of days.', 'epl')
				),
				
				array(
					'name'	=>	'display_bond',
					'label'	=>	__('Rental Bond/Deposit?', 'epl'),
					'type'	=>	'radio',
					'opts'	=>	array(
						1	=>	__('Enable', 'epl'),
						0	=>	__('Disable', 'epl')
					),
					'help'	=>	__('Display the bond/deposit on rental listings.', 'epl')
				),

				array(
					'name'	=>	'epl_max_graph_sales_price',
					'label'	=>	__('Graph Max', 'epl'),
					'type'	=>	'number',
					'default'	=>	'2000000',
					'help'		=>	__('Used for bar chart display on listings for sale.' , 'epl')
				),
				
				array(
					'name'	=>	'epl_max_graph_rent_price',
					'label'	=>	__('Graph Rental Max', 'epl'),
					'type'	=>	'number',
					'default'	=>	'2000',
					'help'		=>	__('Rental range.' , 'epl')
				)
			)
		),
		
		array(
			'label'		=>	__('Currency' , 'epl'),
			'class'		=>	'core',
			'id'		=>	'general',
			'fields'	=>	array(
				array(
					'name'	=>	'currency',
					'label'	=>	__('Currency Type', 'epl'),
					'type'	=>	'select',
					'opts'	=>	$epl_currency_types
				),

				array(
					'name'	=>	'currency_position',
					'label'	=>	__('Symbol Position', 'epl'),
					'type'	=>	'select',
					'opts'	=>	$epl_currency_positions
				),

				array(
					'name'	=>	'currency_thousands_separator',
					'label'	=>	__('Thousands Separator', 'epl'),
					'type'	=>	'text'
				),

				array(
					'name'	=>	'currency_decimal_separator',
					'label'	=>	__('Decimal Separator', 'epl'),
					'type'	=>	'text'
				)
			)
		),
		
		array(
			'label'		=>	__('Listing Single View', 'epl'),
			'class'		=>	'core',
			'id'		=>	'general',
			'help'		=>	__('Configure the default options for the single listing and archive view.', 'epl'),
			'fields'	=>	array(
				array(
					'name'	=>	'display_single_gallery',
					'label'	=>	__('Single Listing: Automatically display image gallery?', 'epl'),
					'type'	=>	'radio',
					'opts'	=>	array(
						1	=>	__('Enable', 'epl'),
						0	=>	__('Disable', 'epl')
					),
					'help'	=>	__('Images uploaded and attached to a listing will automatically display on the single listing page.', 'epl')
				),

				array(
					'name'	=>	'display_gallery_n',
					'label'	=>	__('Single Listing: Gallery columns?', 'epl'),
					'type'	=>	'select',
					'opts'	=>	$opts_epl_gallery_n
				),

				array(
					'name'	=>	'display_feature_columns',
					'label'	=>	__('Single Listing: Feature list columns?', 'epl'),
					'type'	=>	'select',
					'opts'	=>	$opts_epl_features
				)
			)
		),
		
		array(
			'label'		=>	__('Listing Archive View', 'epl'),
			'class'		=>	'core',
			'id'		=>	'general',
			'help'		=>	__('Configure the default options for the single listing and archive view.', 'epl'),
			'fields'	=>	array(
				array(
					'name'	=>	'display_excerpt_length',
					'label'	=>	__('Archive View: Excerpt word count?', 'epl'),
					'type'	=>	'select',
					'opts'	=>	$opts_epl_property_card_excerpt_length,
					'help'	=>	__('This is ignored when using manual excerpts.', 'epl')
				),
				array(
					'name'	=>	'display_archive_view_type',
					'label'	=>	__('Archive View: listing view type', 'epl'),
					'type'	=>	'radio',
					'opts'	=>	array(
						'list'	=>	__('List', 'epl'),
						'grid'	=>	__('Grid', 'epl')
					)
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
					'label'	=>	__('Rental Bond/Deposit (default: Bond)', 'epl'),
					'type'	=>	'text'
				),
				
				array(
					'name'	=>	'label_suburb',
					'label'	=>	__('Suburb/City (default: Suburb)', 'epl'),
					'type'	=>	'text'
				),

				array(
					'name'	=>	'label_postcode',
					'label'	=>	__('Postcode Label (default: Postcode)', 'epl'),
					'type'	=>	'text'
				),

				array(
					'name'	=>	'label_home_open',
					'label'	=>	__('Home Open Label (default: Home Open)', 'epl'),
					'type'	=>	'text'
				),

				array(
					'name'	=>	'label_new',
					'label'	=>	__('New Home Label (default: New)', 'epl'),
					'type'	=>	'text',
					'default'	=>	'new'
				),
				
				array(
					'name'	=>	'label_poa',
					'label'	=>	__('No Price Label (default: POA)', 'epl'),
					'type'	=>	'text'
				)
			)
		),
		
		array(
			'label'		=>	__('Search Widget: Tab Labels', 'epl'),
			'class'		=>	'core',
			'id'		=>	'labels',
			'help'		=>	__('Customise the tab labels of the EPL - Search Widget.', 'epl'),
			'fields'	=>	array(

				array(
					'name'	=>	'widget_label_property',
					'label'	=>	__('Property', 'epl'),
					'type'	=>	'text',
					'default'	=>	'Property'
				),
				array(
					'name'	=>	'widget_label_land',
					'label'	=>	__('Land', 'epl'),
					'type'	=>	'text',
					'default'	=>	'Land'
				),
				array(
					'name'	=>	'widget_label_rental',
					'label'	=>	__('Rental', 'epl'),
					'type'	=>	'text',
					'default'	=>	'Rental'
				),
				array(
					'name'	=>	'widget_label_rural',
					'label'	=>	__('Rural', 'epl'),
					'type'	=>	'text',
					'default'	=>	'Rural'
				),
				array(
					'name'	=>	'widget_label_commercial',
					'label'	=>	__('Commercial', 'epl'),
					'type'	=>	'text',
					'default'	=>	'Commercial'
				),
				array(
					'name'	=>	'widget_label_commercial_land',
					'label'	=>	__('Commercial Land', 'epl'),
					'type'	=>	'text',
					'default'	=>	'Commercial Land'
				),
				array(
					'name'	=>	'widget_label_business',
					'label'	=>	__('Business', 'epl'),
					'type'	=>	'text',
					'default'	=>	'Business'
				)
			)
		),

		array(
			'label'		=>	__('Debug' , 'epl'),
			'class'		=>	'core',
			'id'		=>	'general',
			'fields'	=>	array(
				
				array(
					'name'	=>	'debug',
					'label'	=>	__('Display Listing Coordinate results', 'epl'),
					'type'	=>	'radio',
					'opts'	=>	array(
						1	=>	'Enable',
						0	=>	'Disable'
					),
					'help'	=>	__('This will listing lat/long results on listing pages.', 'epl')
				),
				
			),
		)
	);
	
	$fields = apply_filters('epl_display_options_filter', $fields);
	return $fields;
}
