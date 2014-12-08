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
	<p><?php _e('Configure Easy Property Listings general settings. Visit ', 'epl'); ?><a href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'epl-getting-started' ), 'index.php' ) ) ); ?>"><?php _e( 'Getting Started', 'epl' ); ?></a><?php _e(' for help.', 'epl');?></p>
	<div id="epl-menu-general" class="epl-content">
		<form action="" method="post" class="epl-general-form">
			<div class="epl-fields epl-menu-page">
				<?php
					if(!empty($fields)) { ?>
						<div class="epl-field epl-menu-content">
						
							<?php foreach($fields as $field_group) {
								$field_id 		= isset($field_group['id']) ? $field_group['id'] : 'extension';
								$field_class 	= isset($field_group['class']) ? $field_group['class'] : 'extension';
								?>
									<div id="epl-<?php echo $field_id; ?>" class="epl-field epl-menu-section epl-<?php echo $field_class; ?>">
										<?php
										if( !empty($field_group['label']) ) { ?>
												<h3 class="epl-section-title"><?php _e($field_group['label'], 'epl'); ?></h3>
											<?php
										} ?>
										
										<div class="epl-section-content">
											<?php foreach($field_group['fields'] as $field) { ?>
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
		<div class="epl-content-sidebar">
			<?php epl_admin_sidebar (); ?>
		</div>


	</div>
</div><?php

function epl_get_admin_option_fields() {
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

	$epl_currency_positions = array(
			'before'	=> __('Before - $10', 'epl'), 
			'after'		=> __('After - 10$', 'epl')
			);
	$epl_currency_types = epl_get_currencies();
	$epl_post_types = epl_get_post_types();

	$fields = array(
		array(
			'label'		=>	'',
			'fields'	=>	array(
				array(
					'name'	=>	'activate_post_types',
					'label'	=>	__('Listing Types to Enable', 'epl'),
					'type'	=>	'checkbox',
					'opts'	=>	$epl_post_types,
					'help'	=>	__('Refresh the page to see your new activated listing types. Note: If they are not visible on the front end visit Dashboard > Settings > Permalinks and press Save Changes.')
				),

				array(
					'name'	=>	'currency',
					'label'	=>	__('Currency', 'epl'),
					'type'	=>	'select',
					'opts'	=>	$epl_currency_types
				),

				array(
					'name'	=>	'currency_position',
					'label'	=>	__('Currency Symbol Position', 'epl'),
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
				),

				array(
					'name'	=>	'label_location',
					'label'	=>	__('Location label', 'epl'),
					'type'	=>	'text'
				),
				
				array(
					'name'	=>	'epl_max_graph_sales_price',
					'label'	=>	__('Maximum Sales Price', 'epl'),
					'type'	=>	'number',
					'default'	=>	'2000000',
					'help'	=>	__('Will be used by epl bars & charts to determine bar width', 'epl')
				),
				
				array(
					'name'	=>	'epl_max_graph_rent_price',
					'label'	=>	__('Maximum Rent Price', 'epl'),
					'type'	=>	'number',
					'default'	=>	'2000',
					'help'	=>	__('Will be used by epl bars & charts to determine bar width', 'epl'),
				),

				array(
					'name'	=>	'sticker_new_range',
					'label'	=>	__('Keep Listings tagged "New" for', 'epl'),
					'type'	=>	'number',
					'default'	=>	'7',
					'help'	=>	__('listings will have a "NEW" Sticker for the defined number of days', 'epl')
				),
				
				array(
					'name'	=>	'debug',
					'label'	=>	__('Debug', 'epl'),
					'type'	=>	'radio',
					'opts'	=>	array(
						1	=>	'On',
						0	=>	'Off'
					),
					'help'	=>	__('Display Lat/Long coordinates on listing pages in admin', 'epl')
				),
				
				array(
					'name'	=>	'listings_masonry',
					'label'	=>	__('Listings Masonry', 'epl'),
					'type'	=>	'radio',
					'opts'	=>	array(
						1	=>	'On',
						0	=>	'Off'
					),
					'help'	=>	__('enable masonry effect on grid view of listing archives', 'epl')
				),
			),
		),
	);
	return $fields;
}
