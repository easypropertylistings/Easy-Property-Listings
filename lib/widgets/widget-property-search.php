<?php
/*
 * WIDGET :: Property Search
 */

class EPL_Widget_Property_Search extends WP_Widget {

	function __construct() {
		parent::WP_Widget( false, $name = __('EPL - Search', 'epl') );	
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
					<label for="property_suburb" class="fm-label"><?php _e('Suburbs:', 'epl'); ?></label>
					<div class="field">
						<select name="property_suburb" id="property_suburb" class="in-field field-width">
							<option value=""><?php _e('Any', 'epl'); ?></option>
						
							<?php
								$suburbs = get_terms('epl_tax_suburb');
								if(!empty($suburbs)) {
									$arr = array();
									foreach($suburbs as $suburb) {
									
										//For check if term has atleast one post for current post type - START
										$args = array(
											'posts_per_page'=> 1,
											'post_type'		=>	$instance['property_type'],
											'tax_query'		=>	array(
												array(
													'taxonomy'	=>	'epl_tax_suburb',
													'field'		=>	'id',
													'terms'		=>	$suburb->term_id
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
										
										$arr[$suburb->term_id] = $suburb->name;
									}
									foreach($arr as $k=>$v) {
										$selected = '';
										if($k == $property_suburb) {
											$selected = 'selected="selected"';
										}
										echo '<option value="'.$k.'" '.$selected.'>'.$v.'</option>';
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
									$meta_vals = get_meta_values( 'property_category', $instance['property_type'], 'publish' );
									if(!empty($meta_vals)) {
										$arr = array(
											'House'					=>	'House',
											'Unit'					=>	'Unit',
											'Townhouse'				=>	'Townhouse',
											'Villa'					=>	'Villa',
											'Apartment'				=>	'Apartment',
											'Flat'					=>	'Flat',
											'Studio'				=>	'Studio',
											'Warehouse'				=>	'Warehouse',
											'DuplexSemi-detached'	=>	'Duplex Semi-detached',
											'Alpine'				=>	'Alpine',
											'AcreageSemi-rural'		=>	'Acreage Semi-rural',
											'Retirement'			=>	'Retirement',
											'BlockOfUnits'			=>	'Block Of Units',
											'Terrace'				=>	'Terrace',
											'ServicedApartment'		=>	'Serviced Apartment',
											'Other'					=>	'Other'
										);
										foreach($arr as $k=>$v) {
											if(!in_array($k, $meta_vals)) {
												unset($arr[$k]);
											}
										}
									}
								
									foreach($arr as $k=>$v) {
										$selected = '';
										if($k == $property_category) {
											$selected = 'selected="selected"';
										}
										echo '<option value="'.$k.'" '.$selected.'>'.$v.'</option>';
									}
								?>
							</select>
						</div>
					</div>
				<?php } ?>
				
				<?php
					if( $instance['property_type'] == 'epl_rental' || $instance['property_type'] == 'epl_holiday_rental' ) {
						$prices_arr = array(
							50	=>	'$50',
							100	=>	'$100',
							150	=>	'$150',
							200	=>	'$200',
							250	=>	'$250',
							300	=>	'$300',
							350	=>	'$350',
							400	=>	'$400',
							450	=>	'$450',
							500	=>	'$500',
							550	=>	'$550',
							600	=>	'$600',
							650	=>	'$650',
							700	=>	'$700',
							750	=>	'$750',
							800	=>	'$800',
							850	=>	'$850',
							900	=>	'$900',
							950	=>	'$950',
							1000=>	'$1,000',
							1100=>	'$1,100',
							1200=>	'$1,200',
							1300=>	'$1,300',
							1400=>	'$1,400',
							1500=>	'$1,500',
							1600=>	'$1,600',
							1700=>	'$1,700',
							1800=>	'$1,800',
							1900=>	'$1,900',
							2000=>	'$2,000',
							2500=>	'$2,500',
							3000=>	'$3,000',
							3500=>	'$3,500',
							4000=>	'$4,000',
							4500=>	'$4,500',
							5000=>	'$5,000',
						);
					} else {
						$prices_arr = array(
							50000	=>	'$50,000',
							100000	=>	'$100,000',
							150000	=>	'$150,000',
							200000	=>	'$200,000',
							250000	=>	'$250,000',
							300000	=>	'$300,000',
							350000	=>	'$350,000',
							400000	=>	'$400,000',
							450000	=>	'$450,000',
							500000	=>	'$500,000',
							550000	=>	'$550,000',
							600000	=>	'$600,000',
							650000	=>	'$650,000',
							700000	=>	'$700,000',
							750000	=>	'$750,000',
							800000	=>	'$800,000',
							850000	=>	'$850,000',
							900000	=>	'$900,000',
							950000	=>	'$950,000',
							1000000	=>	'$1,000,000',
							1250000	=>	'$1,250,000',
							1500000	=>	'$1,500,000',
							1750000	=>	'$1,750,000',
							2000000	=>	'$2,000,000',
							2500000	=>	'$2,500,000',
							3000000	=>	'$3,000,000',
							4000000	=>	'$4,000,000',
							5000000	=>	'$5,000,000',
							10000000=>	'$10,000,000',
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
										if($k == $property_price_from) {
											$selected = 'selected="selected"';
										}
										echo '<option value="'.$k.'" '.$selected.'>'.$v.'</option>';
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
										if($k == $property_price_to) {
											$selected = 'selected="selected"';
										}
										echo '<option value="'.$k.'" '.$selected.'>'.$v.'</option>';
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
										if($k == $property_bedrooms_min) {
											$selected = 'selected="selected"';
										}
										echo '<option value="'.$k.'" '.$selected.'>'.$v.'</option>';
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
										if($k == $property_bedrooms_max) {
											$selected = 'selected="selected"';
										}
										echo '<option value="'.$k.'" '.$selected.'>'.$v.'</option>';
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
										if($k == $property_carport) {
											$selected = 'selected="selected"';
										}
										echo '<option value="'.$k.'" '.$selected.'>'.$v.'</option>';
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
										if($k == $property_bathrooms) {
											$selected = 'selected="selected"';
										}
										echo '<option value="'.$k.'" '.$selected.'>'.$v.'</option>';
									}
								?>
							</select>
						</div>
					</div>
				</div>
				<div class="fm-block bdr-btm">
					<span class="checkbox">
						<input type="checkbox" name="property_air_conditioning" id="property_air_conditioning" class="in-field" <?php if(!empty($property_air_conditioning)) { echo 'checked="checked"'; } ?> />
						<label for="property_air_conditioning" class="check-label"><?php _e('Air Conditioning', 'epl'); ?></label>
						<span class="clear"></span>
					</span>
					<span class="checkbox top-mrgn">
						<input type="checkbox" name="property_pool" id="property_pool" class="in-field" <?php if(!empty($property_pool)) { echo 'checked="checked"'; } ?> />
						<label for="property_pool" class="check-label"><?php _e('Pool', 'epl'); ?></label>
						<span class="clear"></span>
					</span>
					<span class="checkbox top-mrgn">
						<input type="checkbox" name="property_security_system" id="property_security_system" class="in-field" <?php if(!empty($property_security_system)) { echo 'checked="checked"'; } ?> />
						<label for="property_security_system" class="check-label"><?php _e('Security System:', 'epl'); ?></label>
						<span class="clear"></span>
					</span>
				</div>
				<div class="property-type-search">
					<input type="submit" value="Find me a Property!" class="search-btn" />
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
		$title		=	esc_attr($instance['title']);
		$property_type		=	esc_attr($instance['property_type']);
		$property_status	=	esc_attr($instance['property_status']);
		$disable_house_category	=	esc_attr($instance['disable_house_category']);
		?>
			<p>
				<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> 
				<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
			</p>
			
			<p>
				<label for="<?php echo $this->get_field_id('property_type'); ?>"><?php _e('Property Type:'); ?></label> 
				<select class="widefat" id="<?php echo $this->get_field_id('property_type'); ?>" name="<?php echo $this->get_field_name('property_type'); ?>">
					<?php
						$property_types = array(
							'epl_commercial'		=>	'Commercial',
							'epl_land'				=>	'Land',
							'epl_property'			=>	'Property',
							'epl_rental'			=>	'Rental',
							'epl_rural'				=>	'Rural',
							'epl_business'			=>	'Business',
							'epl_holiday_rental'	=>	'Holiday Rental',
							'epl_commercial_land'	=>	'Commercial Land',
						);
						
						foreach($property_types as $k=>$v) {
							$selected = '';
							if($k == $property_type) {
								$selected = 'selected="selected"';
							}
							echo '<option value="'.$k.'" '.$selected.'>'.__($v, 'epl').'</option>';
						}
					?>
				</select>
			</p>
			
			<p>
				<label for="<?php echo $this->get_field_id('property_status'); ?>"><?php _e('Status:'); ?></label> 
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
							if($k == $property_status) {
								$selected = 'selected="selected"';
							}
							echo '<option value="'.$k.'" '.$selected.'>'.__($v, 'epl').'</option>';
						}
					?>
				</select>
			</p>
			
			<p>
				<label for="<?php echo $this->get_field_id('disable_house_category'); ?>"><?php _e('Disable House Category:'); ?></label>
				<input id="<?php echo $this->get_field_id('disable_house_category'); ?>" name="<?php echo $this->get_field_name('disable_house_category'); ?>" type="checkbox" value="1" <?php if($disable_house_category) { echo 'checked="checked"'; } ?> />
			</p>
		<?php 
	}
}
add_action( 'widgets_init', create_function('', 'return register_widget("EPL_Widget_Property_Search");') );

//Property Search Query
function epl_pre_get_posts( $query ) {
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}
	
	if( is_epl_search() ) {
		$epl_search_page = get_option('epl_search_page');
		if($epl_search_page > 0) {
			$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
			
			$query->init();
			$query->set('posts_per_page', get_option('posts_per_page'));
			$query->set('paged', $paged);
			
			extract($_REQUEST);
			if(!empty($property_type)) {
				$query->set('post_type', $property_type);
			} else {
				$query->set('post_type', array('epl_property', 'epl_rental', 'epl_commercial' , 'epl_land', 'epl_rural', 'epl_business', 'epl_holiday_rental', 'epl_commercial_land'));
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
			if(!empty($property_bedrooms_min)) {
				$meta_query[] = array(
					'key'		=>	'property_bedrooms',
					'value'		=>	$property_bedrooms_min,
					'type'		=>	'numeric',
					'compare'	=>	'>='
				);
			}
			if(!empty($property_bedrooms_max)) {
				$meta_query[] = array(
					'key'		=>	'property_bedrooms',
					'value'		=>	$property_bedrooms_max,
					'type'		=>	'numeric',
					'compare'	=>	'<='
				);
			}
			if(!empty($property_bathrooms)) {
				$meta_query[] = array(
					'key'		=>	'property_bathrooms',
					'value'		=>	$property_bathrooms,
					'type'		=>	'numeric',
					'compare'	=>	'>='
				);
			}
			if(!empty($property_carport)) {
				$meta_query[] = array(
					'key'		=>	'property_carport',
					'value'		=>	$property_carport,
					'type'		=>	'numeric',
					'compare'	=>	'>='
				);
			}
			if(!empty($property_category)) {
				$meta_query[] = array(
					'key'		=>	'property_category',
					'value'		=>	$property_category,
					'compare'	=>	'='
				);
			}
			
			if( $property_type == 'epl_rental' || $property_type == 'epl_commercial' || $property_type == 'epl_business' || $property_type == 'epl_holiday_rental' || $property_type == 'epl_commercial_land' ) {
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
			if(!empty($property_suburb)) {
				$tax_query[] = array(
					'taxonomy'	=>	'epl_tax_suburb',
					'field'		=>	'id',
					'terms'		=>	$property_suburb
				);
			}
			
			if(!empty($tax_query)) {
				$query->set('tax_query', $tax_query);;
			}
			$query->parse_query();
		}
	}
}
add_action( 'pre_get_posts', 'epl_pre_get_posts' );

//Property Search Template
function epl_init() {
	if( is_epl_search() ) {
		load_template( dirname( __FILE__ ) . '/templates/content-property-card.php' );
	}
}
add_filter('init', 'epl_init');

//Is Property Search
function is_epl_search() {
	if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'epl_search') {
		return true;
	}
	return false;
}

function get_meta_values( $key = '', $type = 'post', $status = 'publish' ) {
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
