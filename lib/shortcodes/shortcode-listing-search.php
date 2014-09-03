<?php
/**
 * SHORTCODE :: Listing Search [listing_search]
 *
 * @package     EPL
 * @subpackage  Shotrcode/listing_search
 * @copyright   Copyright (c) 2014, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// Only load on front
if( is_admin() ) {
	return; 
}
/**
 * This shortcode allows for you to specify the property type(s) using 
 * [listing_search title="" property_type="property" property_status="current/sold/leased" search_house_category="on/off" search_price="on/off" search_bed="on/off" search_bath="on/off" search_car="on/off" search_other="on/off"] option. You can also
 */
function epl_shortcode_listing_search_callback( $atts ) {
	$atts = shortcode_atts( array(
		'title'						=>	'',
		'property_type'				=>	'property',
		'property_status'			=>	'any',
		'search_house_category'		=>	'on',
		'search_price'				=>	'on',
		'search_bed'				=>	'on',
		'search_bath'				=>	'on',
		'search_car'				=>	'on',
		'search_other'				=>	'on'
	), $atts);
	
	extract($atts);
	extract( $_GET );
	
	ob_start();	
	?><div class="epl-search-form">
		<?php
			if(!empty($title)) {
				?><h3><?php echo $title; ?></h3><?php
			}
		?>
		<form action="" method="get">
			<input type="hidden" name="action" value="epl_search" />
			<input type="hidden" name="property_status" value="<?php echo $property_status; ?>" />
			<input type="hidden" name="property_type" value="<?php echo $property_type; ?>" />
			
			<div class="fm-block bdr-btm">
				<label for="property_location" class="fm-label">
					<?php
						$label_location = epl_tax_location_label();
						_e($label_location.':', 'epl');
					?>
				</label>
				<div class="field">
					<select name="property_location" id="property_location" class="in-field field-width">
						<option value=""><?php _e('Any', 'epl'); ?></option>
					
						<?php
							$locations = get_terms('location');
							if(!empty($locations)) {
								$arr = array();
								foreach($locations as $location) {
								
									//For check if term has atleast one post for current post type - START
									$args = array(
										'posts_per_page'=> 1,
										'post_type'		=>	$property_type,
										'tax_query'		=>	array(
											array(
												'taxonomy'	=>	'location',
												'field'		=>	'id',
												'terms'		=>	$location->term_id
											)
										)
									);
									
									if(isset($property_status) && !empty($property_status)) {
										$args['meta_query'][] = array(
											'key'		=>	'property_status',
											'value'		=>	$property_status,
											'compare'	=>	'='
										);
									}

									$results = get_posts($args);
									if(empty($results)) {
										continue;
									}
									//For check if term has atleast one post for current post type - END
									
									$arr[$location->term_id] = $location->name;
								}
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
				if($search_house_category == 'on') { ?>
					<div class="fm-block bdr-btm">
						<label for="property_category" class="fm-label"><?php _e('House Category:', 'epl'); ?></label>
						<div class="field">
							<select name="property_category" id="property_category" class="in-field field-width">
								<option value=""><?php _e('Any', 'epl'); ?></option>
					
								<?php
									$arr = array();
									$meta_vals = epl_get_meta_values( 'property_category', $property_type, 'publish' );
									if(!empty($meta_vals)) {

										$arr = epl_listing_load_meta_property_category();

										foreach($arr as $k=>$v) {
											if(!in_array($k, $meta_vals)) {
												unset($arr[$k]);
											}
										}
									}
							
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
					if( $property_type == 'rental' || $property_type == 'holiday_rental' ) {
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
			
				if ( $search_bed == 'on' ) { ?>
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
											'10'=>	'10',
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
											'10'=>	'10',
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
				if ( $search_car == 'on' ) {
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
			
				if ( $search_bath == 'on' ) {
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
				
				if ( !empty($search_row) ) {
					echo '<div class="fm-block bdr-btm">'.$search_row.'</div>';
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
	</div><?php
	return ob_get_clean();
}
add_shortcode( 'listing_search', 'epl_shortcode_listing_search_callback' );
