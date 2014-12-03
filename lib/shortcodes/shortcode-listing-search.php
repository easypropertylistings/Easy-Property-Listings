<?php
/**
 * SHORTCODE :: Listing Search [listing_search]
 *
 * @package     EPL
 * @subpackage  Shotrcode/listing_search
 * @copyright   Copyright (c) 2014, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.2
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


// Only load on front
if( is_admin() ) {
	return; 
}
/**
 * This shortcode allows for you to specify the property type(s) using 
 * [listing_search title="" post_type="property" property_status="current/sold/leased" search_house_category="on/off" search_price="on/off" search_bed="on/off" search_bath="on/off" search_car="on/off" search_other="on/off"] option
 */
function epl_shortcode_listing_search_callback( $atts ) {
	global $wpdb;
	$atts = shortcode_atts( array(
		'show_title'				=>	true, //For disable title in case of widget calling (true/false)
		'title'						=>	'', // Freeform text
		'post_type'					=>	array('property'), // Post type name array
		'property_status'			=>	'', 	// Singular: current / sold / leased or '' for any
		'search_house_category'		=>	'on', 	// on or off
		'search_price'				=>	'on', 	// on or off
		'search_bed'				=>	'on', 	// on or off
		'search_bath'				=>	'on', 	// on or off
		'search_car'				=>	'on', 	// on or off
		'search_other'				=>	'on',  	// on or off
		'search_id'					=>	'on', 	// on or off
		'search_land_area'			=>	'on',	// on or off
		'search_building_area'		=>	'on',	// on or off
	), $atts);
	extract($atts);
	extract( $_GET );
	if(!is_array($post_type)){
		$post_type = explode(",", $post_type);
		$post_type = array_map('trim', $post_type);
	}
	$post_types = $post_type;
	global $epl_settings;
	ob_start();	
	$tabcounter = 1;
	if(!empty($post_types)):
	echo '<ul class="property_search-tabs">';
	foreach($post_types as $post_type):
	
		$is_sb_current = $tabcounter == 1?'epl-sb-current':'';
		echo '<li data-tab="epl_ps_tab_'.$tabcounter.'" class="tab-link '.$is_sb_current.'">'.isset($epl_settings['widget_label_'.$post_type])?$epl_settings['widget_label_'.$post_type]:$post_type.'</li>';
		$tabcounter++;
	endforeach;
	echo '</ul>';
	?>
	<div class="epl-search-forms-wrapper">
		<?php
			$tabcounter = 1; // reset tab counter
			foreach($post_types as $post_type):
			$is_sb_current = $tabcounter == 1?'epl-sb-current':'';
		?>
		</ul>
		<div class="epl-search-form <?php echo $is_sb_current; ?>" id="epl_ps_tab_<?php echo $tabcounter; ?>">
			<?php
				if($show_title) {
					if(!empty($title)) {
						?><h3><?php echo $title; ?></h3><?php
					}
				}
			?>
			<form method="get" action="<?php echo esc_url( home_url('/') ); ?>">
				<input type="hidden" name="action" value="epl_search" />
				<input type="hidden" name="property_status" value="<?php echo $property_status; ?>" />
				<input type="hidden" name="post_type" value="<?php echo $post_type; ?>" />
				
				<?php
					if ( $search_id == 'on' ) {
						?>
							<div class="fm-block bdr-btm">
								<label for="property_id" class="fm-label">
									<?php
										_e('Search by Property ID', 'epl');
									?>
								</label>
								<div class="field">
									<input type="text" class="in-field field-width" name="property_id" value="<?php echo (isset($property_id) && $property_id != '')? intval($property_id) : ''; ?>" />
								</div>
							</div>
						<?php
					}
				?>
				<div class="fm-block bdr-btm">
					<label for="property_location" class="fm-label">
						<?php
							$label_location = epl_tax_location_label();
							_e($label_location.':', 'epl');
						?>
					</label>
					<div class="field">
							<?php
								$available_loc_query = "
									SELECT DISTINCT (
										tt.term_id
									)
									FROM {$wpdb->prefix}posts p
									LEFT JOIN {$wpdb->prefix}postmeta pm ON ( p.ID = pm.post_id )
									LEFT JOIN {$wpdb->prefix}term_relationships tr ON ( p.ID = tr.object_id )
									LEFT JOIN {$wpdb->prefix}term_taxonomy tt ON ( tr.term_taxonomy_id = tt.term_taxonomy_id ) WHERE
									tt.taxonomy 			= 'location'
									AND p.post_status 		= 'publish'
									AND p.post_type 		= '{$post_type}'";
									if($property_status != '') {
										$available_loc_query .= "
											AND pm.meta_key 		= 'property_status'
											AND pm.meta_value 		= '{$property_status}'";
									}
								$available_locs = $wpdb->get_col($available_loc_query);
								$locations = get_terms('location',array('hide_empty'	=> true,'include'	=>	$available_locs));
								if(!empty($locations)) {
									$arr = array();
									foreach($locations as $location) {
										$arr[$location->term_id] = $location->name;
									}
							?>
							<select name="property_location" id="property_location" class="in-field field-width">
								<option value=""><?php _e('Any', 'epl'); ?></option>
					
							<?php
									foreach($arr as $k=>$v) {
										$selected = '';
										if(isset($property_location) && $k == $property_location) {
											$selected = 'selected="selected"';
										}
										echo '<option value="'.$k.'" '.$selected.'>'. __($v, 'epl') .'</option>';
									}
								}											
							?>
						</select>
					</div>
				</div>
			
				<?php
					if($search_house_category == 'on' && $post_type != 'land') { ?>
						<div class="fm-block bdr-btm">
							<label for="property_category" class="fm-label"><?php _e('House Category:', 'epl'); ?></label>
							<div class="field">
								<?php
									$arr = array();
									$meta_vals = epl_get_meta_values( 'property_category', $post_type, 'publish' );
									if(!empty($meta_vals)) {
					
										$arr = epl_listing_load_meta_property_category();
										if(!empty($arr)) {
											foreach($arr as $k=>$v) {
												if(!in_array($k, $meta_vals)) {
													unset($arr[$k]);
												}
											}
										}
									}

								?>
								<select name="property_category" id="property_category" class="in-field field-width">
									<option value=""><?php _e('Any', 'epl'); ?></option>
					
									<?php
										foreach($arr as $k=>$v) {
											$selected = '';
											if(isset($property_category) && $k == $property_category) {
												$selected = 'selected="selected"';
											}
											echo '<option value="'.$k.'" '.$selected.'>'. __($v, 'epl') .'</option>';
										}
									?>
								</select>
							</div>
						</div>
						<?php
					}
			
					if ( $search_price == 'on' ) {
						if( $post_type == 'rental' || $post_type == 'holiday_rental' ) {
							$prices_arr = array(
								50	=>	epl_currency_formatted_amount(50),
								100	=>	epl_currency_formatted_amount(100),
								150	=>	epl_currency_formatted_amount(150),
								200	=>	epl_currency_formatted_amount(200),
								250	=>	epl_currency_formatted_amount(250),
								300	=>	epl_currency_formatted_amount(300),
								350	=>	epl_currency_formatted_amount(350),
								400	=>	epl_currency_formatted_amount(400),
								450	=>	epl_currency_formatted_amount(450),
								500	=>	epl_currency_formatted_amount(500),
								550	=>	epl_currency_formatted_amount(550),
								600	=>	epl_currency_formatted_amount(600),
								650	=>	epl_currency_formatted_amount(650),
								700	=>	epl_currency_formatted_amount(700),
								750	=>	epl_currency_formatted_amount(750),
								800	=>	epl_currency_formatted_amount(800),
								850	=>	epl_currency_formatted_amount(850),
								900	=>	epl_currency_formatted_amount(900),
								950	=>	epl_currency_formatted_amount(950),
								1000=>	epl_currency_formatted_amount(1000),
								1100=>	epl_currency_formatted_amount(1100),
								1200=>	epl_currency_formatted_amount(1200),
								1300=>	epl_currency_formatted_amount(1300),
								1400=>	epl_currency_formatted_amount(1400),
								1500=>	epl_currency_formatted_amount(1500),
								1600=>	epl_currency_formatted_amount(1600),
								1700=>	epl_currency_formatted_amount(1700),
								1800=>	epl_currency_formatted_amount(1800),
								1900=>	epl_currency_formatted_amount(1900),
								2000=>	epl_currency_formatted_amount(2000),
								2500=>	epl_currency_formatted_amount(2500),
								3000=>	epl_currency_formatted_amount(3000),
								3500=>	epl_currency_formatted_amount(3500),
								4000=>	epl_currency_formatted_amount(4000),
								4500=>	epl_currency_formatted_amount(4500),
								5000=>	epl_currency_formatted_amount(5000),
							);
							$prices_arr = apply_filters('epl_listing_search_price_rental', $prices_arr);
						} else {
							$prices_arr = array(
								50000	=>	epl_currency_formatted_amount(50000),
								100000	=>	epl_currency_formatted_amount(100000),
								150000	=>	epl_currency_formatted_amount(150000),
								200000	=>	epl_currency_formatted_amount(200000),
								250000	=>	epl_currency_formatted_amount(250000),
								300000	=>	epl_currency_formatted_amount(300000),
								350000	=>	epl_currency_formatted_amount(350000),
								400000	=>	epl_currency_formatted_amount(400000),
								450000	=>	epl_currency_formatted_amount(450000),
								500000	=>	epl_currency_formatted_amount(500000),
								550000	=>	epl_currency_formatted_amount(550000),
								600000	=>	epl_currency_formatted_amount(600000),
								650000	=>	epl_currency_formatted_amount(650000),
								700000	=>	epl_currency_formatted_amount(700000),
								750000	=>	epl_currency_formatted_amount(750000),
								800000	=>	epl_currency_formatted_amount(800000),
								850000	=>	epl_currency_formatted_amount(850000),
								900000	=>	epl_currency_formatted_amount(900000),
								950000	=>	epl_currency_formatted_amount(950000),
								1000000	=>	epl_currency_formatted_amount(1000000),
								1250000	=>	epl_currency_formatted_amount(1250000),
								1500000	=>	epl_currency_formatted_amount(1500000),
								1750000	=>	epl_currency_formatted_amount(1750000),
								2000000	=>	epl_currency_formatted_amount(2000000),
								2500000	=>	epl_currency_formatted_amount(2500000),
								3000000	=>	epl_currency_formatted_amount(3000000),
								4000000	=>	epl_currency_formatted_amount(4000000),
								5000000	=>	epl_currency_formatted_amount(5000000),
								10000000=>	epl_currency_formatted_amount(10000000),
							);
							$prices_arr = apply_filters('epl_listing_search_price_sale', $prices_arr);
						} ?>
			
						<div class="fm-block bdr-btm">
							<div class="fm-block-half">
								<label for="property_price_from" class="fm-label"><?php _e('From', 'epl'); ?></label>
								<div class="field">
									<select name="property_price_from" id="property_price_from" class="in-field field-width">
										<option value=""><?php _e('Any', 'epl'); ?></option>
								
										<?php
											foreach($prices_arr as $k=>$v) {
												$selected = '';
												if(isset($property_price_from) && $k == $property_price_from) {
													$selected = 'selected="selected"';
												}
												echo '<option value="'.$k.'" '.$selected.'>'. __($v, 'epl') .'</option>';
											}
										?>
									</select>
								</div>
							</div>
							<div class="fm-block-half">
								<label for="property_price_to" class="fm-label"><?php _e('To', 'epl'); ?></label>
								<div class="field">
									<select name="property_price_to" id="property_price_to" class="in-field field-width">
										<option value=""><?php _e('Any', 'epl'); ?></option>
								
										<?php
											foreach($prices_arr as $k=>$v) {
												$selected = '';
												if(isset($property_price_to) && $k == $property_price_to) {
													$selected = 'selected="selected"';
												}
												echo '<option value="'.$k.'" '.$selected.'>'. __($v, 'epl') .'</option>';
											}
										?>
									</select>
								</div>
							</div>
						</div>
						<?php
					}
			
					if ( $search_bed == 'on' &&  $post_type != 'land' ) { ?>
						<div class="fm-block bdr-btm">
							<div class="fm-block-half">
								<label for="property_bedrooms_min" class="fm-label"><?php _e('Min Bedrooms:', 'epl'); ?></label>
								<div class="field">
									<select name="property_bedrooms_min" id="property_bedrooms_min" class="in-field field-width">
										<option value=""><?php _e('Any', 'epl'); ?></option>
								
										<?php
											$arr = array(
												'1'	=>	'1',
												'2'	=>	'2',
												'3'	=>	'3',
												'4'	=>	'4',
												'5'	=>	'5',
												'6'	=>	'6',
												'7'	=>	'7',
												'8'	=>	'8',
												'9'	=>	'9',
												'10'	=>	'10',
											);
											foreach($arr as $k=>$v) {
												$selected = '';
												if(isset($property_bedrooms_min) && $k == $property_bedrooms_min) {
													$selected = 'selected="selected"';
												}
												echo '<option value="'.$k.'" '.$selected.'>'. __($v, 'epl') .'</option>';
											}
										?>
									</select>
								</div>
							</div>
							<div class="fm-block-half">
								<label for="property_bedrooms_max" class="fm-label"><?php _e('Max Bedrooms:', 'epl'); ?></label>
								<div class="field">
									<select name="property_bedrooms_max" id="property_bedrooms_max" class="in-field field-width">
										<option value=""><?php _e('Any', 'epl'); ?></option>
								
										<?php
											$arr = array(
												'1'	=>	'1',
												'2'	=>	'2',
												'3'	=>	'3',
												'4'	=>	'4',
												'5'	=>	'5',
												'6'	=>	'6',
												'7'	=>	'7',
												'8'	=>	'8',
												'9'	=>	'9',
												'10'	=>	'10',
											);
											foreach($arr as $k=>$v) {
												$selected = '';
												if(isset($property_bedrooms_max) && $k == $property_bedrooms_max) {
													$selected = 'selected="selected"';
												}
												echo '<option value="'.$k.'" '.$selected.'>'. __($v, 'epl') .'</option>';
											}
										?>
									</select>
								</div>
							</div>
						</div>
						<?php
					}
					
					$search_row = '';	
					if ( $search_bath == 'on' &&  $post_type != 'land'  ) {
						$search_row .= '
							<div class="fm-block-half">
								<label for="property_bathrooms" class="fm-label">'.__('Bathrooms:', 'epl').'</label>
								<div class="field">
									<select name="property_bathrooms" id="property_bathrooms" class="in-field field-width">
										<option value="">'.__('Any', 'epl').'</option>';
										
											$arr = array(
												'1'	=>	'1+',
												'2'	=>	'2+',
												'3'	=>	'3+'
											);
											foreach($arr as $k=>$v) {
												$selected = '';
												if(isset($property_bathrooms) && $k == $property_bathrooms) {
													$selected = 'selected="selected"';
												}
												$search_row .= '<option value="'.$k.'" '.$selected.'>'. __($v, 'epl') .'</option>';
											}
											$search_row .= '
									</select>
								</div>
							</div>
						';
					}
					
									
					if ( $search_car == 'on' &&  $post_type != 'land'  ) {
						$search_row .= '
							<div class="fm-block-half">
								<label for="property_carport" class="fm-label">'.__('Car Spaces:', 'epl').'</label>
								<div class="field">
									<select name="property_carport" id="property_carport" class="in-field field-width">
										<option value="">'.__('Any', 'epl').'</option>';
							
										$arr = array(
											'1'	=>	'1+',
											'2'	=>	'2+'
										);
										foreach($arr as $k=>$v) {
											$selected = '';
											if(isset($property_carport) && $k == $property_carport) {
												$selected = 'selected="selected"';
											}
											$search_row .= '<option value="'.$k.'" '.$selected.'>'. __($v, 'epl') .'</option>';
										}
										$search_row .= '
									</select>
								</div>
							</div>
						';
					}
					if ( !empty($search_row) ) {
						echo '<div class="fm-block bdr-btm">'.$search_row.'</div>';
					}
					
					if ( $search_land_area == 'on' ) { ?>
					
						<div class="fm-block bdr-btm">
							<div class="fm-block-third">
								<label for="property_land_area_min" class="fm-label"><?php _e('Min Land Area:', 'epl'); ?></label>
								<div class="field">
									<input type="number" name="property_land_area_min" id="property_land_area_min" class="in-field field-width" value="<?php echo (isset($property_land_area_min) && $property_land_area_min != '')? intval($property_land_area_min) : ''; ?>"/>
								</div>
							</div>
							
							<div class="fm-block-third">
								<label for="property_land_area_max" class="fm-label"><?php _e('Max Land Area:', 'epl'); ?></label>
								<div class="field">
									<input type="number"  name="property_land_area_max" id="property_land_area_max" class="in-field field-width" value="<?php echo (isset($property_land_area_max) && $property_land_area_max != '') ? intval($property_land_area_max) : ''; ?>"/>
								</div>
							</div>
							
							<div class="fm-block-third">
								<label for="property_land_area_unit" class="fm-label"><?php _e('Land Area Unit:', 'epl'); ?></label>
								<div class="field">
									<select name="property_land_area_unit" id="property_land_area_unit" class="in-field field-width">
								
										<?php
											$arr = array(
												'square'				=>	'Square',
												'squareMeter'			=>	'Square Meter',
												'acre'					=>	'Acre',
												'hectare'				=>	'Hectare',
												'sqft'					=>	'Square Feet',
											);
											foreach($arr as $k=>$v) {
												$selected = '';
												if(isset($property_land_area_unit) && $k == $property_land_area_unit) {
													$selected = 'selected="selected"';
												}
												echo '<option value="'.$k.'" '.$selected.'>'. __($v, 'epl') .'</option>';
											}
										?>
									</select>
								</div>
							</div>
						</div>
						<?php
					}
				
					if ( $search_building_area == 'on' &&  $post_type != 'land'  ) { ?>
					
						<div class="fm-block bdr-btm">
							<div class="fm-block-third">
								<label for="property_building_area_min" class="fm-label"><?php _e('Min building Area:', 'epl'); ?></label>
								<div class="field">
									<input type="number" name="property_building_area_min" id="property_building_area_min" class="in-field field-width" value="<?php echo (isset($property_building_area_min) && $property_building_area_min != '') ? intval($property_building_area_min) : ''; ?>"/>
								</div>
							</div>
							
							<div class="fm-block-third">
								<label for="property_building_area_max" class="fm-label"><?php _e('Max Building Area:', 'epl'); ?></label>
								<div class="field">
									<input type="number"  name="property_building_area_max" id="property_Building_area_max" class="in-field field-width" value="<?php echo (isset($property_building_area_max) && $property_building_area_max != '') ? intval($property_building_area_max) : ''; ?>"/>
								</div>
							</div>
							
							<div class="fm-block-third">
								<label for="property_building_area_unit" class="fm-label"><?php _e('Building Area Unit:', 'epl'); ?></label>
								<div class="field">
									<select name="property_building_area_unit" id="property_building_area_unit" class="in-field field-width">
								
										<?php
											$arr = array(
												'square'				=>	'Square',
												'squareMeter'			=>	'Square Meter',
												'acre'					=>	'Acre',
												'hectare'				=>	'Hectare',
												'sqft'					=>	'Square Feet',
											);
											foreach($arr as $k=>$v) {
												$selected = '';
												if(isset($property_building_area_unit) && $k == $property_building_area_unit) {
													$selected = 'selected="selected"';
												}
												echo '<option value="'.$k.'" '.$selected.'>'. __($v, 'epl') .'</option>';
											}
										?>
									</select>
								</div>
							</div>
						</div>
						<?php
					}
					
				
					if ( $search_other == 'on' ) { ?>
						<div class="fm-block bdr-btm">
							<span class="checkbox">
								<input type="checkbox" name="property_air_conditioning" id="property_air_conditioning" class="in-field" <?php if(isset($property_air_conditioning) && !empty($property_air_conditioning)) { echo 'checked="checked"'; } ?> />
								<label for="property_air_conditioning" class="check-label"><?php _e('Air Conditioning', 'epl'); ?></label>
								<span class="epl-clearfix"></span>
							</span>
							<span class="checkbox top-mrgn">
								<input type="checkbox" name="property_pool" id="property_pool" class="in-field" <?php if(isset($property_pool) && !empty($property_pool)) { echo 'checked="checked"'; } ?> />
								<label for="property_pool" class="check-label"><?php _e('Pool', 'epl'); ?></label>
								<span class="epl-clearfix"></span>
							</span>
							<span class="checkbox top-mrgn">
								<input type="checkbox" name="property_security_system" id="property_security_system" class="in-field" <?php if(isset($property_security_system) && !empty($property_security_system)) { echo 'checked="checked"'; } ?> />
								<label for="property_security_system" class="check-label"><?php _e('Security System:', 'epl'); ?></label>
								<span class="epl-clearfix"></span>
							</span>
						</div>
						<?php
					}
				?>
		
				<div class="property-type-search">
					<input type="submit" value="<?php _e('Find me a Property!', 'epl'); ?>" class="search-btn" />
				</div>
			</form>
		</div>
		<?php $tabcounter++; endforeach; endif; ?>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'listing_search', 'epl_shortcode_listing_search_callback' );
