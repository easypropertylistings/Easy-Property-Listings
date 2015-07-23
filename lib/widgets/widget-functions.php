<?php
	
	/**
	 * search widget form fields for search widget
	 * @since 2.2
	 */
	function epl_search_widget_fields() {
		$fields = apply_filters( 'epl_search_widget_fields',  array(
	
			array(
				'key'			=>	'title',
				'label'			=>	__('Title','epl'),
				'type'			=>	'text',
				'default'		=>	''
			),
			array(
				'key'			=>	'post_type',
				'label'			=>	__('Post Type','epl'),
				'default'		=>	array('property'),
				'type'			=>	'select',
				'multiple'		=>	true,
				'options'		=>	epl_get_active_post_types(),
			),
			array(
				'key'			=>	'style',
				'label'			=>	__('Style','epl'),
				'default'		=>	'default',
				'type'			=>	'select',
				'options'		=>	array(
					'default'	=>	__('Default' , 'epl'),
					'wide'		=>	__('Wide' , 'epl'),
					'slim'		=>	__('Slim' , 'epl')
				)
			),
			array(
				'key'			=>	'property_status',
				'label'			=>	__('Status','epl'),
				'default'		=>	'',
				'type'			=>	'select',
				'options'		=>	array(
					''		=>	__('Any' , 'epl'),
					'current'	=>	__('Current' , 'epl'),
					'sold'		=>	apply_filters( 'epl_sold_label_status_filter' , __('Sold', 'epl') ),
					'leased'	=>	apply_filters( 'epl_leased_label_status_filter' , __('Leased', 'epl') )
				),

			),
			array(
				'key'			=>	'search_id',
				'label'			=>	__('Property ID','epl'),
				'default'		=>	'off',
				'type'			=>	'checkbox',
			),
			array(
				'key'			=>	'search_agent',
				'label'			=>	__('Property Agent','epl'),
				'default'		=>	'off',
				'type'			=>	'checkbox',
			),
			array(
				'key'			=>	'search_location',
				'label'			=>	epl_tax_location_label(),
				'default'		=>	'on',
				'type'			=>	'checkbox',
			),
			array(
				'key'			=>	'search_city',
				'label'			=>	__('City','epl'),
				'default'		=>	'off',
				'type'			=>	'checkbox',
			),
			array(
				'key'			=>	'search_state',
				'label'			=>	__('State','epl'),
				'default'		=>	'off',
				'type'			=>	'checkbox',
			),
			array(
				'key'			=>	'search_country',
				'label'			=>	__('Country','epl'),
				'default'		=>	'off',
				'type'			=>	'checkbox',
			),
			array(
				'key'			=>	'search_postcode',
				'label'			=>	__('Post Code','epl'),
				'default'		=>	'off',
				'type'			=>	'checkbox',
			),
			array(
				'key'			=>	'search_house_category',
				'label'			=>	__('House Category','epl'),
				'default'		=>	'on',
				'type'			=>	'checkbox',
			),
			array(
				'key'			=>	'house_category_multiple',
				'label'			=>	__('Allow House categories','epl'),
				'default'		=>	'off',
				'type'			=>	'checkbox',
			),
			array(
				'key'			=>	'search_price',
				'label'			=>	__('Price','epl'),
				'default'		=>	'on',
				'type'			=>	'checkbox',
			),
			array(
				'key'			=>	'search_bed',
				'label'			=>	__('Bed','epl'),
				'default'		=>	'on',
				'type'			=>	'checkbox',
			),
			array(
				'key'			=>	'search_bath',
				'label'			=>	__('Bath','epl'),
				'default'		=>	'on',
				'type'			=>	'checkbox',
			),
			array(
				'key'			=>	'search_rooms',
				'label'			=>	__('Rooms','epl'),
				'default'		=>	'off',
				'type'			=>	'checkbox',
			),
			array(
				'key'			=>	'search_car',
				'label'			=>	__('Car','epl'),
				'default'		=>	'off',
				'type'			=>	'checkbox',
			),
			array(
				'key'			=>	'search_land_area',
				'label'			=>	__('Land Area','epl'),
				'default'		=>	'off',
				'type'			=>	'checkbox',
			),
			array(
				'key'			=>	'search_building_area',
				'label'			=>	__('Building Area','epl'),
				'default'		=>	'off',
				'type'			=>	'checkbox',
			),
			array(
				'key'			=>	'search_other',
				'label'			=>	__('Other Search Options','epl'),
				'default'		=>	'on',
				'type'			=>	'checkbox',
			),
			array(
				'key'			=>	'submit_label',
				'label'			=>	__('Submit Label','epl'),
				'type'			=>	'text',
				'default'		=>	__('Find me a Property!','epl')
			),

		) );
		
		return $fields;
	}

	function epl_number_suffix_callback($v,$suffix=' +') {
		return $v.''.$suffix;
	}
	
	/**
	 * search widget form fields for search widget - frontend 
	 * @since 2.2
	 */
	function epl_search_widget_fields_frontend($post_type='',$property_status='') {
		
		if( $post_type == 'rental' || $post_type == 'holiday_rental' ) {
		
			$price_array 	= array_combine(range(50,5000,50),array_map('epl_currency_formatted_amount',range(50,5000,50)) );
			$price_array 	= apply_filters('epl_listing_search_price_rental',$price_array);
		} else {
			$price_array 	= array_combine(range(50000,10000000,50000),array_map('epl_currency_formatted_amount',range(50000,10000000,50000)) );
			$price_array 	= apply_filters('epl_listing_search_price_sale',$price_array);
		}
		
		if( 
			isset($post_type) && 
			($post_type == 'rental' 
				|| $post_type == 'commercial' 
				|| $post_type == 'business' 
				|| $post_type == 'holiday_rental' 
				|| $post_type == 'commercial_land'
			) 
		) {
			$price_meta_key = 'property_rent';
		} else {
			$price_meta_key = 'property_price';
		}	
		
		$fields = apply_filters( 'epl_search_widget_fields_frontend',  array(
	
			array(
				'key'			=>	'post_type',
				'meta_key'		=>	'post_type',
				'type'			=>	'hidden',
			),
			array(
				'key'			=>	'property_status',
				'meta_key'		=>	'property_status',
				'type'			=>	'hidden',
				'query'			=>	array('query'	=>	'meta')

			),
			array(
				'key'			=>	'search_id',
				'meta_key'		=>	'property_id',
				'label'			=>	__('Search by Property ID / Address', 'epl'),
				'type'			=>	'text',
				'exclude'		=>	array('land')
			),
			array(
				'key'			=>	'search_agent',
				'meta_key'		=>	'property_agent',
				'label'			=>	__('Search by Property Agent', 'epl'),
				'type'			=>	'text',
			),
			array(
				'key'			=>	'search_location',
				'meta_key'		=>	'property_location',
				'label'			=>	epl_tax_location_label(),
				'type'			=>	'select',
				'option_filter'		=>	'location',
				'options'		=>	epl_get_available_locations($post_type,$property_status),
				'query'			=>	array('query'	=>	'tax'),
				'class'			=>	'epl-search-row',
			),
			array(
				'key'			=>	'search_city',
				'meta_key'		=>	'property_address_city',
				'label'			=>	__( 'City' , 'epl'),
				'type'			=>	'select',
				'option_filter'		=>	'city',
				'options'		=>	epl_get_unique_post_meta_values('property_address_city', $post_type ),
				'query'			=>	array('query'	=>	'meta'),
				'class'			=>	'epl-search-row-half',
			),
			array(
				'key'			=>	'search_state',
				'meta_key'		=>	'property_address_state',	
				'label'			=>	__( 'State' , 'epl'),
				'type'			=>	'select',
				'option_filter'		=>	'state',
				'options'		=>	epl_get_unique_post_meta_values('property_address_state', $post_type ),
				'query'			=>	array('query'	=>	'meta'),
				'class'			=>	'epl-search-row-half',
			),
			array(
				'key'			=>	'search_country',
				'meta_key'		=>	'property_address_country'	,
				'label'			=>	__( 'Country' , 'epl'),
				'type'			=>	'select',
				'option_filter'		=>	'country',
				'options'		=>	epl_get_unique_post_meta_values('property_address_country', $post_type ),
				'query'			=>	array('query'	=>	'meta'),
				'class'			=>	'epl-search-row-half',
			),
			array(
				'key'			=>	'search_postcode',
				'meta_key'		=>	'property_address_postal_code'	,
				'label'			=>	__( 'Post Code' , 'epl'),
				'type'			=>	'select',
				'option_filter'		=>	'postcode',
				'options'		=>	epl_get_unique_post_meta_values('property_address_postal_code', $post_type ),
				'query'			=>	array('query'	=>	'meta'),
				'class'			=>	'epl-search-row-half',
			),
			array(
				'key'			=>	'search_house_category',
				'meta_key'		=>	'property_category',
				'label'			=>	__('Housing Category','epl'),
				'option_filter'		=>	'category',
				'options'		=>	epl_get_meta_values( 'property_category', $post_type),
				'type'			=>	'select',
				'query'			=>	array('query'	=>	'meta'),
				'class'			=>	'epl-search-row',
			),
			array(
				'key'			=>	'search_price',
				'meta_key'		=>	'property_price_from',
				'label'			=>	__('Price From','epl'),
				'type'			=>	'select',
				'option_filter'		=>	'price_from',
				'options'		=>	$price_array,
				'type'			=>	'select',
				'query'			=>	array(
									'query'		=>	'meta',
									'key'		=>	$price_meta_key,
									'type'		=>	'numeric',
									'compare'	=>	'>='
								),
				'class'			=>	'epl-search-row-half',
			),
			array(
				'key'			=>	'search_price',
				'meta_key'		=>	'property_price_to',
				'label'			=>	__('Price To','epl'),
				'type'			=>	'select',
				'option_filter'		=>	'price_to',
				'options'		=>	$price_array,
				'type'			=>	'select',
				'query'			=>	array(
									'query'		=>	'meta', 
									'key'		=>	$price_meta_key, 
									'type'		=>	'numeric', 
									'compare'	=>	'<=' 
								),
				'class'			=>	'epl-search-row-half',
			),
			array(
				'key'			=>	'search_bed',
				'meta_key'		=>	'property_bedrooms_min',
				'label'			=>	__('Bedrooms Min', 'epl'),
				'option_filter'		=>	'bedrooms_min',
				'options'		=>	apply_filters(
									'epl_listing_search_bed_select_min',
									array_combine(range(1,10),array_map('epl_number_suffix_callback',range(1,10)) )
								),
				'type'			=>	'select',
				'exclude'		=>	array('land'),
				'query'			=>	array(
									'query'		=>	'meta', 
									'key'		=>	'property_bedrooms', 
									'type'		=>	'numeric', 
									'compare'	=>	'>=' 
								),
				'class'			=>	'epl-search-row-half',
			),
			array(
				'key'			=>	'search_bed',
				'meta_key'		=>	'property_bedrooms_max',
				'label'			=>	__('Bedrooms Max', 'epl'),
				'option_filter'		=>	'bedrooms_max',
				'options'		=>	apply_filters(
										'epl_listing_search_bed_select_max',
										array_combine(range(1,10),array_map('epl_number_suffix_callback',range(1,10)) )
									),
				'type'			=>	'select',
				'exclude'		=>	array('land'),
				'query'			=>	array(
									'query'		=>	'meta', 
									'key'		=>	'property_bedrooms', 
									'type'		=>	'numeric', 
									'compare'	=>	'<=' 
								),
				'class'			=>	'epl-search-row-half',
			),
			array(
				'key'			=>	'search_bath',
				'meta_key'		=>	'property_bathrooms',
				'label'			=>	__('Bathrooms', 'epl'),
				'option_filter'		=>	'bathrooms',
				'options'		=>	apply_filters(
										'epl_listing_search_bath_select',
										array_combine(range(1,3),array_map('epl_number_suffix_callback',range(1,3)) )
									),
				'type'			=>	'select',
				'exclude'		=>	array('land'),
				'query'			=>	array(
									'query'		=>	'meta', 
									'type'		=>	'numeric', 
									'compare'	=>	'>=' 
								),
				'class'			=>	'epl-search-row-half',
			),
			array(
				'key'			=>	'search_rooms',
				'meta_key'		=>	'property_rooms',
				'label'			=>	__('Rooms', 'epl'),
				'option_filter'		=>	'rooms',
				'options'		=>	apply_filters(
										'epl_listing_search_room_select',
										array_combine(range(1,3),array_map('epl_number_suffix_callback',range(1,3)) )
									),
				'type'			=>	'select',
				'exclude'		=>	array('land'),
				'query'			=>	array(
									'query'		=>	'meta', 
									'type'		=>	'numeric', 
									'compare'	=>	'>=' 
								),
				'class'			=>	'epl-search-row-half',
			), 
			array(
				'key'			=>	'search_car',
				'meta_key'		=>	'property_carport',
				'label'			=>	__('Car Spaces', 'epl'),
				'option_filter'		=>	'carport',
				'options'		=>	apply_filters(
										'epl_listing_search_parking_select',
										array_combine(range(1,3),array_map('epl_number_suffix_callback',range(1,3)) )
									),
				'type'			=>	'select',
				'class'			=>	'epl-search-row-half',
				'exclude'		=>	array('land'),
				'query'			=>	array(
									'multiple'	=>	true,
									'query'		=>	'meta',
									'relation'	=>	'OR',
									'sub_queries'	=> array( 
										array(
											'key'		=>	'property_carport',
											'type'		=>	'numeric',
											'compare'	=>	'>='
										),
										array(
											'key'		=>	'property_garage',
											'type'		=>	'numeric',
											'compare'	=>	'>='
										)
									)
								)
			), 
			array(
				'key'			=>	'search_land_area',
				'meta_key'		=>	'property_land_area_min',
				'label'			=>	__('Land Min','epl'),
				'type'			=>	has_filter('epl_property_land_area_min') ? apply_filters('epl_property_land_area_min','') : 'number',
				'query'			=>	array(
									'query'		=>	'meta', 
									'type'		=>	'numeric', 
									'compare'	=>	'>=', 
									'key'		=>	'property_land_area' 
								),
				'class'			=>	'epl-search-row-third',
				'wrap_start'		=>	'epl-search-row'
			),
			array(
				'key'			=>	'search_land_area',
				'meta_key'		=>	'property_land_area_max',
				'label'			=>	__('Land Max','epl'),
				'class'			=>	'epl-search-row-third',
				'type'			=>	has_filter('epl_property_land_area_max') ? apply_filters('epl_property_land_area_max','') : 'number',
				'query'			=>	array(
									'query'		=>	'meta', 
									'type'		=>	'numeric', 
									'compare'	=>	'<=', 
									'key'		=>	'property_land_area' 
								)
			),
			array(
				'key'			=>	'search_land_area',
				'meta_key'		=>	'property_land_area_unit',
				'label'			=>	__('Area Unit', 'epl'),
				'class'			=>	'epl-search-row-third',
				'type'			=>	'select',
				'option_filter'		=>	'land_area_unit',
				'options'		=>	apply_filters( 'epl_listing_search_land_unit_label',
										array(
											'square'	=>	'Square',
											'squareMeter'	=>	'Square Meter',
											'acre'		=>	'Acre',
											'hectare'	=>	'Hectare',
											'sqft'		=>	'Square Feet',
										)
									),
				'query'			=>	array('query'	=>	'meta'),
				'wrap_end'		=>	true

			),
			array(
				'key'			=>	'search_building_area',
				'meta_key'		=>	'property_building_area_min',
				'label'			=>	__('Building Min','epl'),
				'class'			=>	'epl-search-row-third',
				'type'			=>	has_filter('epl_property_building_area_min') ? apply_filters('epl_property_building_area_min','') : 'number',
				'exclude'		=>	array('land'),
				'query'			=>	array(
									'query'		=>	'meta', 
									'type'		=>	'numeric', 
									'compare'	=>	'>=', 
									'key'		=>	'property_building_area' 
								),
				'wrap_start'		=>	'epl-search-row'
			),
			array(
				'key'			=>	'search_building_area',
				'meta_key'		=>	'property_building_area_max',
				'label'			=>	__('Building Max','epl'),
				'class'			=>	'epl-search-row-third',
				'type'			=>	has_filter('epl_property_building_area_max') ? apply_filters('epl_property_building_area_max','') : 'number',
				'exclude'		=>	array('land'),
				'query'			=>	array(
									'query'		=>	'meta', 
									'type'		=>	'numeric', 
									'compare'	=>	'<=', 
									'key'		=>	'property_building_area'
								)
			),
			array(
				'key'			=>	'search_building_area',
				'meta_key'		=>	'property_building_area_unit',
				'label'			=>	__('Area Unit', 'epl'),
				'class'			=>	'epl-search-row-third',
				'type'			=>	'select',
				'option_filter'		=>	'building_area_unit',
				'options'		=>	apply_filters( 'epl_listing_search_building_unit_label',
									array(
										'square'		=>	'Square',
										'squareMeter'	=>	'Square Meter',
										'acre'			=>	'Acre',
										'hectare'		=>	'Hectare',
										'sqft'			=>	'Square Feet',
									)
								),
				'exclude'		=>	array('land'),
				'query'			=>	array('query'	=>	'meta'),
				'wrap_end'		=>	true
			),

			array(
				'key'			=>	'search_other',
				'meta_key'		=>	'property_air_conditioning',
				'label'			=>	__('Air Conditioning', 'epl'),
				'type'			=>	'checkbox',
				'exclude'		=>	array('land'),
				'query'			=>	array(
									'query'		=>	'meta', 
									'compare'	=>	'IN', 
									'value'		=>	array('yes','1') 
								),
				'class'			=>	'epl-search-row-half',
			),
			array(
				'key'			=>	'search_other',
				'meta_key'		=>	'property_pool',
				'label'			=>	__('Pool', 'epl'),
				'type'			=>	'checkbox',
				'exclude'		=>	array('land'),
				'query'			=>	array(
									'query'		=>	'meta',
									'compare'	=>	'IN', 
									'value'		=>	array('yes','1') 
								),
				'class'			=>	'epl-search-row-half',
			),
			array(
				'key'			=>	'search_other',
				'meta_key'		=>	'property_security_system',
				'label'			=>	__('Security', 'epl'),
				'type'			=>	'checkbox',
				'exclude'		=>	array('land'),
				'query'			=>	array(
									'query'		=>	'meta',
									'compare'	=>	'IN', 
									'value'		=>	array('yes','1') 
								),
				'class'			=>	'epl-search-row-half',
			)
		) );
		return $fields;
	}
	/**
	 * search widget form fields defaults
	 * @since 2.2
	 */
	function epl_search_get_defaults() {
		
		$defaults 	= array();
		$fields 	= epl_search_widget_fields();
		
		foreach($fields as $field) {
			$defaults[$field['key']] = $field['default'];
		}
		return $defaults;	
			
	}
	
	/**
	 * render widget field blocks -- for backend form
	 * @since 2.2
	 */
	
	function epl_widget_render_backend_field($field,$object,$value='') {

		switch ($field['type']) {
		
			// checkbox
			case "checkbox": ?>
				<p>
					<input 
						id="<?php echo $object->get_field_id($field['key']); ?>" 
						name="<?php echo $object->get_field_name($field['key']); ?>" 
						type="checkbox" 
							<?php 
								if(isset($value) && $value == 'on') { 
									echo 'checked="checked"'; 
								} 
							?>
					/>
					<label for="<?php echo $object->get_field_id($field['key']); ?>">
						<?php echo $field['label']; ?>
					</label>
				</p> <?php
			
			break;
			
			// text
			case "text": ?>
				<p>
					<label for="<?php echo $object->get_field_id($field['key']); ?>">
						<?php echo $field['label']; ?>
					</label>
					<input 
						id="<?php echo $object->get_field_id($field['key']); ?>" 
						name="<?php echo $object->get_field_name($field['key']); ?>" 
						type="text" 
						value="<?php echo $value; ?>"
					/>
				</p> <?php
			
			break;
			
			// select
			case "select": ?>
				<p>
					<label for="<?php echo $object->get_field_id($field['key']); ?>">
						<?php echo $field['label']; ?>
					</label>
					
					<select
					
						<?php echo isset($field['multiple']) ? ' multiple ':' '; ?>
						class="widefat" 
						id="<?php echo $object->get_field_id($field['key']); ?>" 
						name="<?php echo $object->get_field_name($field['key']); echo isset($field['multiple']) ? '[]':''; ?>">
						
						<?php
							foreach($field['options'] as $k=>$v) {
								$selected = '';
								if( isset($field['multiple']) ) {
								
									if(in_array( $k, $value) ) {
										$selected = 'selected="selected"';
									}
									
								} else {
								
									if(isset($value) && $k == $value) {
										$selected = 'selected="selected"';
									}
								}
								echo '<option value="'.$k.'" '.$selected.'>'.$v.'</option>';
							}
						?>
						
					</select>
				</p> <?php

			break;
			
		}
	
	}
	
	/**
	 * render widget field blocks -- for front-end form
	 * @since 2.2
	 */
	
	function epl_widget_render_frontend_fields($field,$config='',$value='',$post_type='',$property_status='') {

		if( $field['type'] != 'hidden') {
			if( $config != 'on' )
				return;
		}
			
			
		if( !empty($field['exclude']) && in_array($post_type,$field['exclude']) )
			return; 
			
		if( isset($field['wrap_start']) ) {
			echo '<div class="'.$field['wrap_start'].'">';
		}
		
		switch ($field['type']) {
		
			// checkbox
			case "checkbox": ?>
				<span class="checkbox top-mrgn <?php echo isset($field['class']) ? $field['class'] : ''; ?>">
				
					<div class="field">
						<input type="checkbox" name="<?php echo $field['meta_key']; ?>" id="<?php echo $field['meta_key']; ?>" class="in-field" 
						<?php if(isset($value) && !empty($value)) { echo 'checked="checked"'; } ?> />
					</div>
					
					<label for="<?php echo $field['meta_key']; ?>" class="check-label">
						<?php echo apply_filters('epl_search_widget_label_'.$field['meta_key'],__($field['label'], 'epl') ); ?>
					</label>
					
					<span class="epl-clearfix"></span>
					
				</span> <?php
			
			break;
			
			// text
			case "text": ?>
				<div class="epl-search-row epl-search-row-full-wrapper epl-<?php echo $field['meta_key']; ?> fm-block <?php echo isset($field['class']) ? $field['class'] : ''; ?>">
				
					<label for="<?php echo $field['meta_key']; ?>" class="epl-search-label fm-label">
						<?php 
							echo apply_filters('epl_search_widget_label_'.$field['meta_key'], $field['label'] ); ?>
					</label>

					<div class="field">
						<input 
							type="text" 
							class="in-field field-width" 
							name="<?php echo $field['meta_key']; ?>" 
							id="<?php echo $field['meta_key']; ?>"
							value="<?php echo $value; ?>" 
						/>
					</div>
					
				</div> <?php
			
			break;
			
			// number
			case "number": ?>
				<div class="epl-search-row epl-search-row-full-wrapper epl-<?php echo $field['meta_key']; ?> fm-block <?php echo isset($field['class']) ? $field['class'] : ''; ?>">
				
					<label for="<?php echo $field['meta_key']; ?>" class="epl-search-label fm-label">
						<?php 
							echo apply_filters('epl_search_widget_label_'.$field['meta_key'], $field['label'] ); ?>
					</label>

					<div class="field">
						<input 
							type="number" 
							class="in-field field-width" 
							name="<?php echo $field['meta_key']; ?>" 
							id="<?php echo $field['meta_key']; ?>"
							value="<?php echo $value; ?>" 
						/>
					</div>
					
				</div> <?php
			
			break;
			
			// select
			case "select": ?>
				<div class="epl-search-row epl-search-row-full-wrapper epl-<?php echo $field['meta_key']; ?> fm-block <?php echo isset($field['class']) ? $field['class'] : ''; ?>">
				
					<label for="<?php echo $field['meta_key']; ?>" class="epl-search-label fm-label">
						<?php 
							echo apply_filters('epl_search_widget_label_'.$field['meta_key'], $field['label'] ); ?>
					</label>
					
					<div class="field">
							<select
								<?php echo isset($field['multiple']) ? ' multiple ':' '; ?>
								name="<?php echo $field['meta_key']; echo isset($field['multiple']) ? '[]':''; ?>" 
								id="<?php echo $field['meta_key']; ?>" 
								class="in-field field-width">
								<option value="">
									<?php echo apply_filters('epl_search_widget_option_label_'.$field['option_filter'],__('Any', 'epl') ); ?>
								</option>
							<?php
								foreach($field['options'] as $k=>$v) {
									$selected = '';
									if( isset($field['multiple']) ) {
								
										if(in_array( $k, $value) ) {
											$selected = 'selected="selected"';
										}
									
									} else {
								
										if(isset($value) && $k == $value) {
											$selected = 'selected="selected"';
										}
									}
									echo '<option value="'.$k.'" '.$selected.'>'.$v.'</option>';
								}
							?>
						</select>
					</div>								
				</div> <?php

			break;
			
			// hidden
			case "hidden": ?>
				<input 
					type="hidden" 
					class="in-field field-width" 
					name="<?php echo $field['meta_key']; ?>" 
					id="<?php echo $field['meta_key']; ?>"
					value="<?php echo $value; ?>" 
				/> <?php
			
			break;
			
		}
		if( isset($field['wrap_end']) ) {
			echo '</div>';
		}

	
	}
	
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
		
		if(isset($property_id) ) {
			if(is_numeric($property_id)) {
				$query->set( 'post__in', array(intval($property_id)) );
			} else {
				$query->set( 'epl_post_title', sanitize_text_field($property_id) );
			}
				
		}
		
		if(isset($property_agent) ) {
			$property_agent = sanitize_title_with_dashes($property_agent);
				
				if( $property_agent = get_user_by('slug',$property_agent) ) {
			
					$query->set( 'post_author', $property_agent->ID );
				}
				
		}
		
		if(isset($post_type) && !empty($post_type)) {
			$query->set('post_type', $post_type);
		} else {
			$epl_post_types = epl_get_active_post_types();
			if(!empty($epl_post_types)) {
				$epl_post_types = array_keys($epl_post_types);
				$query->set('post_type', $epl_post_types);
			}
		}
		
		$epl_meta_query = array();
		
		$epl_search_form_fields = epl_search_widget_fields_frontend($post_type,$property_status);
		
		foreach($epl_search_form_fields as $epl_search_form_field) {
			
			
			
			if( isset($epl_search_form_field['query']) ) {
				
				if($epl_search_form_field['query']['query'] == 'meta') {
				
					$this_meta_query = array();
					
					if( isset($epl_search_form_field['query']['multiple']) && $epl_search_form_field['query']['multiple'] == true) {
					
						if( isset(${$epl_search_form_field['meta_key']}) && !empty(${$epl_search_form_field['meta_key']}) ) {
						
							$this_meta_query['relation'] = 
								isset($epl_search_form_field['query']['relation']) ?
								$epl_search_form_field['query']['relation'] : 'OR';
							
							foreach($epl_search_form_field['query']['sub_queries'] as $sub_query) {
						
								$this_sub_query = array(
									'key'		=>	$sub_query['key'],
									'value'		=>	${$epl_search_form_field['meta_key']},
									'type'		=>	$sub_query['type'],
									'compare'	=>	$sub_query['compare']
								);
								$this_meta_query[] = $this_sub_query;
							}
							$epl_meta_query[] = $this_meta_query;
						}
						
					} else {
						
						$query_meta_key = isset($epl_search_form_field['query']['key']) ? 
						$epl_search_form_field['query']['key'] :
						$epl_search_form_field['meta_key'];
						
						if( isset(${$epl_search_form_field['meta_key']}) && !empty(${$epl_search_form_field['meta_key']}) ) {
						
							$this_meta_query = array(
								'key'	=>	$query_meta_key,
								'value'	=>	${$epl_search_form_field['meta_key']}
							);
						
							isset($epl_search_form_field['query']['compare']) ? $this_meta_query['compare'] = $epl_search_form_field['query']['compare'] : '';
							isset($epl_search_form_field['query']['type']) ? $this_meta_query['type'] = $epl_search_form_field['query']['type'] : '';
							isset($epl_search_form_field['query']['value']) ? $this_meta_query['value'] = $epl_search_form_field['query']['value'] : '';
							$epl_meta_query[] = $this_meta_query;
						}

					}
					
					
					
				}
			}
		}
		if(!empty($epl_meta_query)) {
			$query->set('meta_query', $epl_meta_query);
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
			$query->set('tax_query', $tax_query);
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
		return array_combine($return,$return);
	}
}

function epl_esc_like ($text) {
	 return addcslashes( $text, '_%\\' );
}

function epl_listings_where( $where, &$wp_query ) {
    global $wpdb;
    if ( $epl_post_title = $wp_query->get( 'epl_post_title' ) ) {
        $where .= ' AND ' . $wpdb->posts . '.post_title LIKE \'%' . esc_sql( epl_esc_like( $epl_post_title ) ) . '%\'';
    }
    return $where;
}
add_filter( 'posts_where', 'epl_listings_where', 10, 2 );

function epl_get_available_locations($post_type='',$property_status='') {
	global $wpdb;
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
	$available_locs	= $wpdb->get_col($available_loc_query);
	$locations	= get_terms('location',array('hide_empty'	=> true,'include'	=>	$available_locs));
	$arr = array();
	foreach($locations as $location) {
		$arr[$location->term_id] = $location->name;
	}
	return $arr;

}

/** example to enable multiple house category via filter 

function epl_filter_search_widget_fields_frontend($fields) {
	
	foreach($fields as &$field) {
		if($field['key'] == 'search_house_category') {
			$field['multiple'] 	= true;
			$field['query'] 	= array('query'	=>	'meta','compare'	=>	'IN' );
			break;
		}
	}
	
	return $fields;
}
add_filter('epl_search_widget_fields_frontend','epl_filter_search_widget_fields_frontend');
 **/
