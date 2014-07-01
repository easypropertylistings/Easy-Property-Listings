<?php
/**
 * WIDGET :: Property Search
 *
 * @package     EPL
 * @subpackage  Widget/Search
 * @copyright   Copyright (c) 2014, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class EPL_Widget_Property_Search extends WP_Widget {

	function __construct() {
		parent::WP_Widget( false, $name = __('EPL - Listing Search', 'epl') );
	}

	function widget($args, $instance) {
		extract( $args );
		extract( $_GET );
		$title 		= apply_filters('widget_title', $instance['title']);
		
		echo $before_widget;
		if ( $title )
			echo $before_title . $title . $after_title; ?>
				
		<div class="epl-search-form">
			<form action="" method="get">
				<input type="hidden" name="action" value="epl_search" />
				<input type="hidden" name="property_status" value="<?php echo $instance['property_status']; ?>" />
				<input type="hidden" name="property_type" value="<?php echo $instance['property_type']; ?>" />
				
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
											'post_type'		=>	$instance['property_type'],
											'tax_query'		=>	array(
												array(
													'taxonomy'	=>	'location',
													'field'		=>	'id',
													'terms'		=>	$location->term_id
												)
											)
										);
										
										if(isset($instance['property_status']) && !empty($instance['property_status'])) {
											$args['meta_query'][] = array(
												'key'		=>	'property_status',
												'value'		=>	$instance['property_status'],
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
				
				<?php if(!$instance['disable_house_category']) { ?>
					<div class="fm-block bdr-btm">
						<label for="property_category" class="fm-label"><?php _e('House Category:', 'epl'); ?></label>
						<div class="field">
							<select name="property_category" id="property_category" class="in-field field-width">
								<option value=""><?php _e('Any', 'epl'); ?></option>
						
								<?php
									$arr = array();
									$meta_vals = epl_get_meta_values( 'property_category', $instance['property_type'], 'publish' );
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
				<?php } ?>
				
				<?php
					if( $instance['property_type'] == 'rental' || $instance['property_type'] == 'holiday_rental' ) {
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
					}
				?>
				
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
				<div class="fm-block bdr-btm">
					<div class="fm-block-half">
						<label for="property_carport" class="fm-label"><?php _e('Car Spaces:', 'epl'); ?></label>
						<div class="field">
							<select name="property_carport" id="property_carport" class="in-field field-width">
								<option value=""><?php _e('Any', 'epl'); ?></option>
								
								<?php
									$arr = array(
										'1'	=>	'1+',
										'2'	=>	'2+'
									);
									foreach($arr as $k=>$v) {
										$selected = '';
										if(isset($property_carport) && $k == $property_carport) {
											$selected = 'selected="selected"';
										}
										echo '<option value="'.$k.'" '.$selected.'>'. __($v, 'epl') .'</option>';
									}
								?>
							</select>
						</div>
					</div>
					<div class="fm-block-half">
						<label for="property_bathrooms" class="fm-label"><?php _e('Bathrooms:', 'epl'); ?></label>
						<div class="field">
							<select name="property_bathrooms" id="property_bathrooms" class="in-field field-width">
								<option value=""><?php _e('Any', 'epl'); ?></option>
								
								<?php
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
										echo '<option value="'.$k.'" '.$selected.'>'. __($v, 'epl') .'</option>';
									}
								?>
							</select>
						</div>
					</div>
				</div>
				<div class="fm-block bdr-btm">
					<span class="checkbox">
						<input type="checkbox" name="property_air_conditioning" id="property_air_conditioning" class="in-field" <?php if(isset($property_bathrooms) && !empty($property_bathrooms)) { echo 'checked="checked"'; } ?> />
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
				<div class="property-type-search">
					<input type="submit" value="<?php _e('Find me a Property!', 'epl'); ?>" class="search-btn" />
				</div>
			</form>
		</div>
		<?php
		echo $after_widget;
	}

	function update($new_instance, $old_instance) {		
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['property_type'] = strip_tags($new_instance['property_type']);
		$instance['property_status'] = strip_tags($new_instance['property_status']);
		$instance['disable_house_category'] = strip_tags($new_instance['disable_house_category']);
		return $instance;
	}

	function form($instance) {
		$defaults = array(
						'title'						=>	'',
						'property_type'				=>	'property',
						'property_status'			=>	'any',
						'disable_house_category'	=>	0
					);
		$instance = wp_parse_args( (array) $instance, $defaults ); 	
	
		$title					=	esc_attr($instance['title']);
		$property_type			=	esc_attr($instance['property_type']);
		$property_status		=	esc_attr($instance['property_status']);
		$disable_house_category	=	esc_attr($instance['disable_house_category']);
		?>
			<p>
				<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'epl'); ?></label> 
				<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
			</p>
			
			<p>
				<label for="<?php echo $this->get_field_id('property_type'); ?>"><?php _e('Property Type:', 'epl'); ?></label> 
				<select class="widefat" id="<?php echo $this->get_field_id('property_type'); ?>" name="<?php echo $this->get_field_name('property_type'); ?>">
					<?php
						$property_types = epl_get_active_post_types();
						if(!empty($property_types)) {
							foreach($property_types as $k=>$v) {
								$selected = '';
								if($k == $property_type) {
									$selected = 'selected="selected"';
								}
								echo '<option value="'.$k.'" '.$selected.'>'.__($v, 'epl').'</option>';
							}
						}
					?>
				</select>
			</p>
			
			<p>
				<label for="<?php echo $this->get_field_id('property_status'); ?>"><?php _e('Status:', 'epl'); ?></label> 
				<select class="widefat" id="<?php echo $this->get_field_id('property_status'); ?>" name="<?php echo $this->get_field_name('property_status'); ?>">
					<?php
						$status_list = array(
							''			=>	'Any',
							'current'	=>	'Current',
							'sold'		=>	'Sold',
							'leased'	=>	'Leased'
						);
						
						foreach($status_list as $k=>$v) {
							$selected = '';
							if(isset($property_status) && $k == $property_status) {
								$selected = 'selected="selected"';
							}
							echo '<option value="'.$k.'" '.$selected.'>'.__($v, 'epl').'</option>';
						}
					?>
				</select>
			</p>
			
			<p>
				<label for="<?php echo $this->get_field_id('disable_house_category'); ?>"><?php _e('Disable House Category:', 'epl'); ?></label>
				<input id="<?php echo $this->get_field_id('disable_house_category'); ?>" name="<?php echo $this->get_field_name('disable_house_category'); ?>" type="checkbox" value="1" <?php if(isset($disable_house_category) && $disable_house_category) { echo 'checked="checked"'; } ?> />
			</p>
		<?php 
	}
}
add_action( 'widgets_init', create_function('', 'return register_widget("EPL_Widget_Property_Search");') );

//Property Search Query
function epl_search_pre_get_posts( $query ) {
	if ( is_admin() || !$query->is_main_query() ) {
		return;
	}
	
	if( epl_is_search() ) {
		$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
		
		$query->init();
		$query->set('posts_per_page', get_option('posts_per_page'));
		$query->set('paged', $paged);
		
		extract($_REQUEST);
		if(isset($property_type) && !empty($property_type)) {
			$query->set('post_type', $property_type);
		} else {
			$epl_post_types = epl_get_active_post_types();
			if(!empty($epl_post_types)) {
				$epl_post_types = array_keys($epl_post_types);
				$query->set('post_type', $epl_post_types);
			}
		}

		$meta_query = array();
		if(isset($property_security_system) && !empty($property_security_system)) {
			$meta_query[] = array(
				'key'		=>	'property_security_system',
				'value'		=>	array('yes', '1'),
				'compare'	=>	'IN'
			);
		}
		if(isset($property_air_conditioning) && !empty($property_air_conditioning)) {
			$meta_query[] = array(
				'key'		=>	'property_air_conditioning',
				'value'		=>	array('yes', '1'),
				'compare'	=>	'IN'
			);
		}
		if(isset($property_pool) && !empty($property_pool)) {
			$meta_query[] = array(
				'key'		=>	'property_pool',
				'value'		=>	array('yes', '1'),
				'compare'	=>	'IN'
			);
		}
		if(isset($property_bedrooms_min) && !empty($property_bedrooms_min)) {
			$meta_query[] = array(
				'key'		=>	'property_bedrooms',
				'value'		=>	$property_bedrooms_min,
				'type'		=>	'numeric',
				'compare'	=>	'>='
			);
		}
		if(isset($property_bedrooms_max) && !empty($property_bedrooms_max)) {
			$meta_query[] = array(
				'key'		=>	'property_bedrooms',
				'value'		=>	$property_bedrooms_max,
				'type'		=>	'numeric',
				'compare'	=>	'<='
			);
		}
		if(isset($property_bathrooms) && !empty($property_bathrooms)) {
			$meta_query[] = array(
				'key'		=>	'property_bathrooms',
				'value'		=>	$property_bathrooms,
				'type'		=>	'numeric',
				'compare'	=>	'>='
			);
		}
		if(isset($property_carport) && !empty($property_carport)) {
			$meta_query[] = array(
				'key'		=>	'property_carport',
				'value'		=>	$property_carport,
				'type'		=>	'numeric',
				'compare'	=>	'>='
			);
		}
		if(isset($property_category) && !empty($property_category)) {
			$meta_query[] = array(
				'key'		=>	'property_category',
				'value'		=>	$property_category,
				'compare'	=>	'='
			);
		}
		
		if( isset($property_type) && ($property_type == 'rental' || $property_type == 'commercial' || $property_type == 'business' || $property_type == 'holiday_rental' || $property_type == 'commercial_land') ) {
			$key = 'property_rent';
		} else {
			$key = 'property_price';
		}			
		if(isset($property_price_from) && !empty($property_price_from)) {
			$meta_query[] = array(
				'key'		=>	$key,
				'value'		=>	$property_price_from,
				'type'		=>	'numeric',
				'compare'	=>	'>='
			);
		}
		if(isset($property_price_to) && !empty($property_price_to)) {
			$meta_query[] = array(
				'key'		=>	$key,
				'value'		=>	$property_price_to,
				'type'		=>	'numeric',
				'compare'	=>	'<='
			);
		}
		
		if(isset($property_status) && !empty($property_status)) {
			$meta_query[] = array(
				'key'		=>	'property_status',
				'value'		=>	$property_status,
				'compare'	=>	'='
			);
		}
		
		if(!empty($meta_query)) {
			$query->set('meta_query', $meta_query);;
		}
		
		$tax_query = array();
		if(isset($property_location) && !empty($property_location)) {
			$tax_query[] = array(
				'taxonomy'	=>	'location',
				'field'		=>	'id',
				'terms'		=>	$property_location
			);
		}
		
		if(!empty($tax_query)) {
			$query->set('tax_query', $tax_query);;
		}
		$query->parse_query();
	}
}
add_action( 'pre_get_posts', 'epl_search_pre_get_posts' );

//Is Property Search
function epl_is_search() {
	if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'epl_search') {
		return true;
	}
	return false;
}

function epl_get_meta_values( $key = '', $type = 'post', $status = 'publish' ) {
	if( empty($key) ) {
		return;
	}
	
	global $wpdb;	
	$results = $wpdb->get_results( $wpdb->prepare( "SELECT distinct(pm.`meta_value`) FROM {$wpdb->postmeta} pm LEFT JOIN {$wpdb->posts} p ON p.`ID` = pm.`post_id` WHERE pm.`meta_key` = '%s' AND p.`post_status` = '%s' AND p.`post_type` = '%s' AND pm.`meta_value` != ''", $key, $status, $type ));
	if(!empty($results)) {
		$return = array();
		foreach($results as $result) {
			$return[] = $result->meta_value;
		}
		return $return;
	}
}
