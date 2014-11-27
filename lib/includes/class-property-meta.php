<?php
class Property_Meta {

	static $instance;
	public $post;
	public $epl_settings;
	public $meta;
	public $post_type;
	public $property_post_type;
	
	 public function __construct($post) {
	 	$epl_posts = array('property','land', 'commercial', 'business', 'commercial_land' , 'location_profile','rental','rural');
        $this->post 				= $post;
        $this->epl_settings 		= epl_settings();
        $this->meta 				= get_post_custom();
        $this->post_type 			= $this->post->post_type;
        $this->property_post_type	= $this->post->post_type;
    }
    
	
	public function get_property_meta($meta_key,$allowzero=true) {
		if(isset($this->meta[$meta_key])) {
			if(isset($this->meta[$meta_key][0])) {
				if($allowzero === true){
					return  $this->meta[$meta_key][0];
				}	elseif(intval($this->meta[$meta_key][0]) == 0) {
					return;
				}
			
				
			}
		}
	}
	
	public function get_property_inspection_times($meta_key='property_inspection_times') {
		$pit = $this->get_property_meta($meta_key);
		if($pit != '') {
			$list = array_filter(explode("\n", $pit));
			if(!empty($list)){
				$inspectarray = array();
				foreach ($list as $num => $item) {
					$timearr = explode(' ',$item);
					$endtime = current($timearr).' '.end($timearr);
					if(strtotime($endtime) > time()) {
						$item = trim($item);
						$inspectarray[strtotime($endtime)] = $item;
						
					}
				}
				ksort($inspectarray);
				$return =  "";
				foreach ($inspectarray as $key => $element) {
					if(!empty($element)) {
						$return .= "<li>" . htmlspecialchars($element) . "</li>";
					}
				}
				if(!empty($return)) {
					$return = '<ul>'.$return.'</ul>';
					return  $return;
				}
			}
		}
	}
	
	// wrapper function for epl settings 
	
	public function get_epl_settings($key) {
		$value = '';
		if(!empty($this->epl_settings) && isset($this->epl_settings[$key])) {
			$value = $this->epl_settings[$key];
		}
		return $value;
	}

	// Process Property Features Taxonomy
	public function get_property_feature_taxonomy() {
		return get_the_term_list($this->post->ID, 'tax_feature', '<li>', '</li><li>', '</li>' );
	}
	
	// suburb profile
	public function get_suburb_profile() {
		$profiles = get_the_terms( $this->post->ID, 'location' );
		if ( is_array ($profiles) ){
			$profile = end($profiles);
			return $profile->name;
		}
	}
	
	// property auction
	public function get_property_auction() {
		if(isset($this->meta['property_auction'])) {
			if(isset($this->meta['property_auction'][0])) {
					if ( '' != $this->meta['property_auction'][0] ) {
						return date( 'l jS F \a\t H:i' , strtotime($this->meta['property_auction'][0]) );
					}
			}	
		}
	}
	// property com rent
	public function get_property_com_rent(){
		return epl_currency_formatted_amount( $this->get_property_meta('property_com_rent') );
	}
	
	// price display
	public function get_property_price_display() {
		$property_price_search =  $this->get_property_meta('property_price');
		$property_price_view	= $this->get_property_meta('property_price_view');
		if ( $property_price_search == '') {
			$property_price = '';
		} elseif( $property_price_view != '' ) {
			$property_price = $property_price_view; 
		} else {
			$property_price = epl_currency_formatted_amount( $property_price_search );
		}
		// Auction Details and Price override
		if ( $this->get_property_meta('property_authority') == 'auction') {
			$property_price = __( 'Auction' , 'epl') . ' ' . $this->get_property_auction();
		}
		return $property_price;
	}
	
	// Rental Price XE Format
	public function get_property_rent () {
		if($this->post_type != 'rental')
			return;
			
		if ( $this->get_property_meta('property_rent') !='' ) {
			return epl_currency_formatted_amount($this->get_property_meta('property_rent'));
		}
	}
	
	// Rental Bond
	public function get_property_bond () {
		if($this->post_type != 'rental')
			return;
			
		if ( $this->get_property_meta('property_bond') !='' ) {
			return epl_currency_formatted_amount($this->get_property_meta('property_bond'));
		}
	}
	
	// property land category
	public function get_property_land_category() {
		if ( 'land' != $this->post_type || 'commercial_land' != $this->post_type )
			return;
			
		if ( $this->get_property_meta('property_land_category') !='' ) {
			return epl_listing_meta_land_category_value( $this->get_property_meta('property_land_category') );
		}
	}
	
	// formatted property 
	public function get_formatted_property_address() {
		$street =  $this->get_property_meta('property_address_lot_number').' ';
		
				if($this->get_property_meta('property_address_sub_number') != '')
					$street .= $this->get_property_meta('property_address_sub_number').'/';
					
				$street .= $this->get_property_meta('property_address_street_number').' ';
				$street .=$this->get_property_meta('property_address_street').' ';
				//$street .=$this->get_property_meta('property_address_suburb');
		return $street;
	}
	
	public function get_property_category () {
		return epl_listing_meta_property_category_value( $this->get_property_meta('property_category') );
	}
	
	public function get_price_class() {
		if($this->get_property_meta('property_status') == 'sold'){
			return apply_filters('property_price_class','epl-price sold');
		}
	}
	
	public function get_property_tax() {
		$property_tax = '';
		if( $this->get_property_meta('property_tax') != '' ) {
				$property_tax = $this->get_property_meta('property_tax');
			}
		if ( $property_tax == 'exempt' ) {
			$property_tax = ' '.__('Inc. GST', 'epl');
		} elseif ( $property_tax == 'inclusive' ) {
			$property_tax = ' '.__('Inc. GST', 'epl');
		} elseif ( $property_tax == 'exclusive' ) {
			$property_tax = ' +  '.__('GST', 'epl');
		} else {
			$property_tax = '';
		}
		return $property_tax;
	}
	
	// plain price value
	public function get_price_plain_value() {
		
		if ( 'property' == $this->post_type || 'land' == $this->post_type || 'rural' == $this->post_type){ 
			if ( 'sold' == $this->get_property_meta('property_status') ) {
				$price_plain_value = __('Sold', 'epl');
			} 
			elseif ( '' != $this->get_property_price_display() && 'yes' == $this->get_property_meta('property_price_display') ) {	// Property
				$price_plain_value = $this->get_property_price_display();
			} else {
				if(!empty($this->epl_settings) && isset($this->epl_settings['label_poa'])) {
					$price_plain_value = $this->epl_settings['label_poa'];
				}
			}
			if ( 'yes' == $this->get_property_meta('property_under_offer') && 'sold' != $this->get_property_meta('property_status')) {
				$price_plain_value = __('Under Offer', 'epl');
			}
			
			
		} elseif('rental' == $this->post_type) { 
		
			if( '' != $this->get_property_rent() && 'yes' == $this->get_property_meta('property_rent_display') && 'leased' != $this->get_property_meta('property_status') ) {
			
				$price_plain_value = $this->get_property_rent(). '/' . $this->get_property_meta('property_rent_period');
				
				if($this->get_property_bond() != '' && $this->epl_settings['display_bond'] == 'yes')
					$price_plain_value = $this->get_property_bond().__(' bond','epl');
				
			} elseif('leased' == $this->get_property_meta('property_status')) {
				$price_plain_value = __('Leased', 'epl');
				
			} else {
				$price_plain_value = __('TBA', 'epl');
			}
			
		} elseif ( 'commercial' == $this->post_type || 'business' == $this->post_type || 'commercial_land' == $this->post_type) {
			if ( 'sold' == $this->get_property_meta('property_status') ){
				$price_plain_value = __('Sold', 'epl');
			} elseif ( '' != $this->get_property_price_display() && 'yes' == $this->get_property_meta('property_price_display') ) {	// Property
				$price_plain_value = __('For Sale', 'epl').' ' .$this->get_property_price_display().$this->get_property_tax();
			} else {
				if(!empty($this->epl_settings) && isset($this->epl_settings['label_poa'])) {
					$price_plain_value = $this->epl_settings['label_poa'];
				}
			}
			if ( 'yes' == $this->get_property_meta('property_under_offer') && 'sold' != $this->get_property_meta('property_status')) {
				$price_plain_value = __('Under Offer', 'epl');
			}
			if( $this->get_property_com_rent() != '' && $this->get_property_meta('property_com_listing_type') == 'both') {
				$price_plain_value .= '</br>'.__('For Lease', 'epl').' ' . $this->get_property_com_rent() . ' '.__('P.A.', 'epl');
			} elseif($this->get_property_com_rent() != '' && $this->get_property_meta('property_com_listing_type') == 'lease') {
				$price_plain_value = ' '.__('For Lease', 'epl').' ' . $this->get_property_com_rent() . ' '.__('P.A.', 'epl');
			}
		}
		return $price_plain_value;
	}
	
	// get price 
	public function get_price() {
		$price = '';
		if ( 'property' == $this->post_type || 'land' == $this->post_type || 'rural' == $this->post_type){
			if ( 'sold' == $this->get_property_meta('property_status') ) {
				$price = '<span class="page-price sold-status">'.__('Sold', 'epl').'</span>';
			}
			elseif ( '' != $this->get_property_price_display() && 'yes' == $this->get_property_meta('property_price_display') ) {	// Property
				$price = '<span class="page-price">'. $this->get_property_price_display() . '</span>';
				
			} else {
				if(!empty($this->epl_settings) && isset($this->epl_settings['label_poa'])) {
					$price_plain_value = $this->epl_settings['label_poa'];
				}
				$price = '<span class="page-price">' . $price_plain_value . '</span>';
			}
			if ( 'yes' == $this->get_property_meta('property_under_offer') && 'sold' != $this->get_property_meta('property_status')) {
				$price = '<span class="page-price under-offer-status">'.__('Under Offer', 'epl').'</span>';
			}
			
			
		} elseif('rental' == $this->post_type) { 
		
			if( '' != $this->get_property_rent() && 'yes' == $this->get_property_meta('property_rent_display') && 'leased' != $this->get_property_meta('property_status') ) {
					
				$price = '
							<span class="page-price-rent">
								<span class="page-price" style="margin-right:0;">'. $this->get_property_rent() . '</span>
								<span class="rent-period">/' . $this->get_property_meta('property_rent_period') . '</span>
							</span>
						';
				$yo = $this->get_property_bond();
				if($this->get_property_bond() != '' && in_array($this->epl_settings['display_bond'],array(1,'yes')))
					$price .= '<span class="bond">' . $this->get_property_bond() . ' bond</span>';
					
			} elseif('leased' == $this->get_property_meta('property_status')) {
				$price = '<span class="page-price sold-status">'.__('Leased', 'epl').'</span>';
				
			} else {
				$price = '<span class="page-price">'.__('TBA', 'epl').'</span>';
			}
			
		} elseif ( 'commercial' == $this->post_type || 'business' == $this->post_type || 'commercial_land' == $this->post_type) {
			if ( 'sold' == $this->get_property_meta('property_status') ){
				$price = '<span class="page-price sold-status">'.__('Sold', 'epl').'</span>';
			} elseif ( '' != $this->get_property_price_display() && 'yes' == $this->get_property_meta('property_price_display') ) {	// Property
				$price = '<span class="page-price">'.__('For Sale', 'epl').' '. $this->get_property_price_display() . $this->get_property_tax() . '</span>';
			} else {
				$price_plain_value = '';
				if(!empty($this->epl_settings) && isset($this->epl_settings['label_poa'])) {
					$price_plain_value = $this->epl_settings['label_poa'];
				}
				$price = '<span class="page-price">' . $price_plain_value . '</span>';
			}
			if ( 'yes' == $this->get_property_meta('property_under_offer') && 'sold' != $this->get_property_meta('property_status')) {
				$price = '<div class="page-price under-offer-status">'.__('Under Offer', 'epl').'</div>';
			}
			if( $this->get_property_com_rent() != '' && $this->get_property_meta('property_com_listing_type') == 'both') {
				
				$price .= '<div class="epl-clear"></div><span class="page-price">'.__('For Lease', 'epl').' ' . $this->get_property_com_rent() . ' '.__('P.A.', 'epl').'</span>';
			} elseif($this->get_property_com_rent() != '' && $this->get_property_meta('property_com_listing_type') == 'lease') {
				$price = '<span class="page-price">'.__('For Lease', 'epl').' ' . $this->get_property_com_rent() . ' '.__('P.A.', 'epl').'</span>';
			}
		}
		return $price;
	}
	
	// price sticker
	public function get_price_sticker() {
		$price_sticker = '';
		$date = new DateTime($this->post->post_date);
		$now = new DateTime();
		$diff = $now->diff($date);
		if ( 'property' == $this->post_type || 'land' == $this->post_type || 'rural' == $this->post_type){
			if ( 'sold' == $this->get_property_meta('property_status') ) {
				$price_sticker .= '<span class="status-sticker sold">'.__('Sold', 'epl').'</span>';
			} 
			elseif ( '' != $this->get_property_price_display() && 'yes' == $this->get_property_meta('property_price_display') ) {
				// Property
				if ( $this->get_property_meta('property_inspection_times') != '' ){
					if($this->get_epl_settings('sticker_new_range') >=  $diff->days) 
						$price_sticker .= '<span class="status-sticker new">'.$this->get_epl_settings('label_new').'</span>';
						
					$price_sticker .= '<span class="status-sticker open">'.$this->get_epl_settings('label_home_open').'</span>';
				}
			
			}
			if ( 'yes' == $this->get_property_meta('property_under_offer') && 'sold' != $this->get_property_meta('property_status')) {
			
				if($this->get_epl_settings('sticker_new_range') >=  $diff->days)
						$price_sticker .= '<span class="status-sticker new">'.$this->get_epl_settings('label_new').'</span>';
						
				$price_sticker .= '<span class="status-sticker under-offer">'.__('Under Offer', 'epl').'</span>';
			}
			
			
		} elseif('rental' == $this->post_type) { 
		
			if('leased' == $this->get_property_meta('property_status')) {
				
				$price_sticker .= '<span class="status-sticker leased">'.__('Leased', 'epl').'</span>';
				
			} else {
			
				if ( $this->get_property_meta('property_inspection_times') != '' ){
					
					if($this->get_epl_settings('sticker_new_range') >=  $diff->days)
						$price_sticker .= '<span class="status-sticker new">'.$this->get_epl_settings('label_new').'</span>';
						
					$price_sticker .= '<span class="status-sticker open">'.$this->get_epl_settings('label_home_open').'</span>';
				}
			}
			
		} elseif ( 'commercial' == $this->post_type || 'business' == $this->post_type || 'commercial_land' == $this->post_type) {
			if ( 'sold' == $this->get_property_meta('property_status') ){
				$price_sticker .= '<span class="status-sticker sold">'.__('Sold', 'epl').'</span>';
			} 
			if ( 'yes' == $this->get_property_meta('property_under_offer') && 'sold' != $this->get_property_meta('property_status')) {
			
				if($this->get_epl_settings('sticker_new_range') >=  $diff->days)
						$price_sticker .= '<span class="status-sticker new">'.$this->get_epl_settings('label_new').'</span>';
						
				$price_sticker .= '<span class="status-sticker under-offer">'.__('Under Offer', 'epl').'</span>';
			}
			
		}
		return $price_sticker;
	}
	
	// get l price 
	public function get_l_price() {
		
		if ( 'property' == $this->post_type || 'land' == $this->post_type || 'rural' == $this->post_type){ 
			if ( 'sold' == $this->get_property_meta('property_status') ) {
				$l_price = '<li class="page-price sold-status">'.__('Sold', 'epl').'</li>';
			} 
			elseif ( '' != $this->get_property_price_display() && 'yes' == $this->get_property_meta('property_price_display') ) {	// Property
				$l_price = '<li class="page-price">'. $this->get_property_price_display() . '</li>';
			} else {
				if(!empty($this->epl_settings) && isset($this->epl_settings['label_poa'])) {
					$price_plain_value = $this->epl_settings['label_poa'];
				}
				$l_price = '<li class="page-price">' . $price_plain_value . '</li>';
			}
			if ( 'yes' == $this->get_property_meta('property_under_offer') && 'sold' != $this->get_property_meta('property_status')) {
				$l_price = '<li class="page-price under-offer-status">'.__('Under Offer', 'epl').'</li>';
			}
			
			
		} elseif('rental' == $this->post_type) { 
		
			if( '' != $this->get_property_rent() && 'yes' == $this->get_property_meta('property_rent_display') && 'leased' != $this->get_property_meta('property_status') ) {
					
				$l_price = '
								<li class="page-price-rent">
									<span class="page-price" style="margin-right:0;">'. $this->get_property_rent() . '</span>
									<span class="rent-period">/' . $this->get_property_meta('property_rent_period') . '</span>
								</li>
							';
					
			} elseif('leased' == $this->get_property_meta('property_status')) {
				$l_price = '<li class="page-price sold-status">'.__('Leased', 'epl').'</li>';
				
			}
			
		} elseif ( 'commercial' == $this->post_type || 'business' == $this->post_type || 'commercial_land' == $this->post_type) {
			if ( 'sold' == $this->get_property_meta('property_status') ){
				$l_price = '<li class="status-sticker sold">'.__('Sold', 'epl').'</li>';
			} elseif ( '' != $this->get_property_price_display() && 'yes' == $this->get_property_meta('property_price_display') ) {	// Property
				$l_price = '<li class="page-price">'.__('For Sale', 'epl').' '. $this->get_property_price_display() . $this->get_property_tax() . '</li>';
			} else {
				$price_plain_value = '';
				if(!empty($this->epl_settings) && isset($this->epl_settings['label_poa'])) {
					$price_plain_value = $this->epl_settings['label_poa'];
				}
				$l_price = '<li class="page-price">' . $price_plain_value . '</li>';
			}
			if ( 'yes' == $this->get_property_meta('property_under_offer') && 'sold' != $this->get_property_meta('property_status')) {
				$l_price = '<li class="page-price under-offer-status">'.__('Under Offer', 'epl').'</li>';
			}
			if( $this->get_property_com_rent() != '' && $this->get_property_meta('property_com_listing_type') == 'both') {
				
				$l_price .= '<li class="page-price">'.__('For Lease', 'epl').' ' . $this->get_property_com_rent() . ' '.__('P.A.', 'epl').'</li>';
			} elseif($this->get_property_com_rent() != '' && $this->get_property_meta('property_com_listing_type') == 'lease') {
				$l_price = '<li class="page-price">'.__('For Lease', 'epl').' ' . $this->get_property_com_rent() . ' '.__('P.A.', 'epl').'</li>';
			}
		}
		return $l_price;
	}
	
	// property commercial category
	public function get_property_commercial_category($tag='div') {
		$property_commercial_category = epl_listing_load_meta_commercial_category_value( $this->get_property_meta('property_commercial_category') );
		$property_commercial_category = '<'.$tag.' class="commercial-category">' . $property_commercial_category . '</'.$tag.'>';
		return $property_commercial_category;
	}
	
	// property bed
	
	public function get_property_bed($returntype = 'i') {
		if($this->get_property_meta('property_bedrooms') == '')
			return;
		$bed['i'] = '<span title="'.__('Bedrooms', 'epl').'" class="icon beds"><span class="icon-value">'. $this->get_property_meta('property_bedrooms') . '</span></span>'; 
		$bed['d'] = $this->get_property_meta('property_bedrooms') . ' '.__('bed', 'epl').' ';
		$bed['l'] = '<li class="bedrooms">' . $this->get_property_meta('property_bedrooms') . ' '.__('bed', 'epl').'</li>';
		return $bed[$returntype];
	}
	
	// property bathrooms
	
	public function get_property_bath($returntype = 'i') {
		if($this->get_property_meta('property_bathrooms') == '')
			return;
		$bath['i'] = '<span title="'.__('Bathrooms', 'epl').'" class="icon bath"><span class="icon-value">'. $this->get_property_meta('property_bathrooms') . '</span></span>'; 
		$bath['d'] = $this->get_property_meta('property_bedrooms') . ' '.__('bath', 'epl').' ';
		$bath['l'] = '<li class="bathrooms">' . $this->get_property_meta('property_bathrooms') . ' '.__('bath', 'epl').'</li>';
		return $bath[$returntype];
	}
	
	// property parking
	public function get_property_parking() {
		if($this->get_property_meta('property_parking') == '')
			return;
		if($this->get_property_meta('property_parking') != ''){
			return '<span title="'.__('Parking Spaces', 'epl').'" class="icon parking"><span class="icon-value">' . $this->get_property_meta('property_parking') . '</span></span>';
		}
	}
	
	// property ac
	public function get_property_air_conditioning($returntype = 'i') {
		if($this->get_property_meta('property_air_conditioning') == '')
			return;
		$property_air_conditioning = $this->get_property_meta('property_air_conditioning');
		if( isset($property_air_conditioning) && ($property_air_conditioning == 1 || $property_air_conditioning == 'yes') ) { 
			$air['i'] = '<span title="'.__('Air Conditioning', 'epl').'" class="icon air"></span>'; 
			$air['l'] = '<li class="air">'.__('Air conditioning', 'epl').'</li>';
			return $air[$returntype];
		}
	}
	
	// property pool
	public function get_property_pool($returntype = 'i') {
		if($this->get_property_meta('property_pool') == '')
			return;
		$property_pool = $this->get_property_meta('property_pool');
		if( isset($property_pool) && ($property_pool == 1 || $property_pool == 'yes') ) { 
			$pool['i'] = '<span title="'.__('Pool', 'epl').'" class="icon pool"></span>'; 
			$pool['l'] = '<li class="pool">'.__('Pool', 'epl').'</li>';
			return $pool[$returntype];
		}
	}
	
	// property security system
	public function get_property_security_system($returntype = 'i') {
		if($this->get_property_meta('property_security_system') == '')
			return;
		$property_security_system = $this->get_property_meta('property_security_system');
		if( isset($property_security_system) && ($property_security_system == 1 || $property_security_system == 'yes') ) { 
			$security_system['i'] = '<span title="'.__('Alarm System', 'epl').'" class="icon alarm"></span>'; 
			$security_system['l'] = '<li class="alarm">'.__('Alarm System', 'epl').'</li>';
			return $security_system[$returntype];
		}
	}
	
	// property land value
	public function get_property_land_value($returntype = 'i') {
		$property_land_area_unit = $this->get_property_meta('property_land_area_unit');
		if ( $property_land_area_unit == 'squareMeter' ) {
			$property_land_area_unit = 'sqm';
		}
		if($this->get_property_meta('property_land_area') != '') {
			return '
				<li class="land-size">'.
					__('Land is', 'epl').' ' . $this->get_property_meta('property_land_area') .' '.$property_land_area_unit.'
			
				</li>';
		}
	}
	
	// property building area
	public function get_property_building_area_value($returntype = 'i') {
		$building_unit = $this->get_property_meta('property_building_area_unit');
		if ( $building_unit == 'squareMeter' ) {
			$building_unit = 'm²';
		}
		return '
			<li class="land-size">'.__('Floor Area is', 'epl').' ' .
		 		$this->get_property_meta('property_building_area') .' '.$building_unit.
	 		'</li>';
	}
	
	// new construction
	public function get_property_new_construction($returntype = 'i') {
		$property_new_construction = $this->get_property_meta('property_new_construction');
		if( isset($property_new_construction) && ($property_new_construction == 1 || $property_new_construction == 'yes') ) { 
			$return_construction = array();
			$return_construction['i'] = '<span title="'.__('New Construction', 'epl').'" class="icon new_construction"></span>'; 
			$return_construction['l'] = '<li class="pool">'.__('New Construction', 'epl').'</li>';
			return $return_construction[$returntype];
		}
	}
	
	// additional features html
	public function get_additional_features_html($metakey) {
			$metavalue = $this->get_property_meta($metakey);
			if( $metavalue != '' || intval($metavalue) != 0) {
				if($metakey == 'property_com_car_spaces'){
					$metavalue = $metavalue.__(' Car Spaces', 'epl');
				}
				
				if( (is_numeric($metavalue))|| $metavalue == 'yes' ) {
					// toggle field types -- yes or 1 for toggle true
					if($metavalue == 'yes' || $metavalue == 1){ 
						return '<li class="'.$this->get_class_from_metakey($metakey).'">'.__($this->get_label_from_metakey($metakey), 'epl').'</li>';	
					} elseif(is_numeric($metavalue)) {
						// numbered field types 
						return '
								<li class="'.$this->get_class_from_metakey($metakey).'">'.
									$metavalue.' '.__($this->get_label_from_metakey($metakey), 'epl').
								'</li>';
					} else {
						// others
						return '<li class="'.$this->get_class_from_metakey($metakey).'">'.$metavalue.'</li>';
					}
					
				}
				// string value field types
				return '<li class="'.$this->get_class_from_metakey($metakey).'">'.$metavalue.'</li>';
			}
	}
	
	// additional rural features html
	public function get_additional_rural_features_html($metakey) {
			$metavalue = $this->get_property_meta($metakey);
			if( isset($metavalue) && $metavalue != '' ) {
				return '<div class="'.$this->get_class_from_metakey($metakey,$search= 'property_rural_').'">
							<h6>'.__($this->get_label_from_metakey($metakey,'property_rural_'), 'epl').'</h6>'.
							'<p>'.$metavalue.'</p>'.
						'</div>';
			}
	}
	
	public function get_class_from_metakey($key,$search= 'property_'){
		 return str_replace("property_", "", $key);
		
	}
	
	public function get_label_from_metakey($key,$search= 'property_'){
		 return ucwords(str_replace('_',' ',str_replace($search, "", $key)));
	}
}


