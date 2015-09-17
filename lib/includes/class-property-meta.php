<?php
class EPL_Property_Meta {

	static $instance;
	public $post;
	public $epl_settings;
	public $meta;
	public $post_type;
	public $property_post_type;
	
	 public function __construct($post) {
	 
	 $epl_posts 			= array('property','land', 'commercial', 'business', 'commercial_land' , 'location_profile','rental','rural');
        $this->post 			= $post;
        $this->epl_settings 		= epl_settings();
        $this->meta 			= get_post_custom();
        $this->post_type 		= $this->post->post_type;
        $this->property_post_type	= $this->post->post_type;
        
        $this->epl_labels();
        
    }
    
	public function epl_labels() {
	
		$field_groups = epl_get_admin_option_fields();
		
		foreach($field_groups as $field_group) {
			if($field_group['id']	==	'labels') {
				$epl_labels = array_filter($field_group['fields']);
				break;
			}
		}
		
		foreach($epl_labels as $label_key	=>	$label) {
		
			if( $label_key 	= $label['name']) {
				$default	= isset($label['default']) ? $label['default'] : '';
			
				if( isset( $this->epl_settings[$label_key]) && $this->epl_settings[$label_key] != '') {
					$this->{$label_key}	= $this->epl_settings[$label_key];
				} else {
					$this->{$label_key} = $default;
				}
			}
		}
		
	}
	
	public function get_property_meta($meta_key,$allowzero=true) {
		if(isset($this->meta[$meta_key])) {
			if(isset($this->meta[$meta_key][0])) {
				if($allowzero === true){
					return  $this->meta[$meta_key][0];
				} elseif(intval($this->meta[$meta_key][0]) == 0) {
					return;
				} else {
					return $this->meta[$meta_key][0];
				}
			}
		}
	}
	
	public function get_property_inspection_times($meta_key='property_inspection_times') {
		if('leased' == $this->get_property_meta('property_status') || 'sold' == $this->get_property_meta('property_status'))
			return;
			
		$pit = $this->get_property_meta($meta_key);
		$pit = trim($pit);
		if($pit != '') {
			$list = array_filter(explode("\n", $pit));
			if(!empty($list)){
				// there are inspection times
				$inspectarray = array();
				foreach ($list as $num => $item) {
					if(is_numeric($item[0])) {
						$timearr = explode(' ',$item);
						$endtime = current($timearr).' '.end($timearr);
						if(strtotime($endtime) > time()) {
							$item = trim($item);
							$inspectarray[strtotime($endtime)] = $item;
						} else {
							
						}

					}	else	{
						$inspectarray[$num]	=	$item;
					}				
				}
				ksort($inspectarray);
				// update inspection times by removing past dates
				$new_inspection_meta = implode("\n", $inspectarray);
				update_post_meta($this->post->ID,'property_inspection_times',$new_inspection_meta);
				
				$return =  "";
				if(count($inspectarray) >= 1) {
					// unordered list for multiple inspection times
					foreach ($inspectarray as $key => &$element) {
						if(!empty($element)) {
							$element_formatted = apply_filters('epl_inspection_format',$element);
							$return .= "<li class='home-open-date'>
										<a 
											class ='epl_inspection_calendar'
											href='".get_bloginfo('url')."?epl_cal_dl=1&cal=ical&dt=".base64_encode(htmlspecialchars($element))."&propid=".$this->post->ID."' >" 
												. $element_formatted ."
										</a>
									</li>";
						}
					}
					if(!empty($return)) {
						$return = '<ul class="home-open-wrapper">'.$return.'</ul>';
					}

				} 
				return apply_filters('epl_property_inspection_time', $return);
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
	public function get_property_auction($admin=false) {
		$format = $admin == true?'l jS M \a\t g:i a': 'l jS F \a\t g:i a';
		if(isset($this->meta['property_auction'])) {
			if(isset($this->meta['property_auction'][0])) {
					if ( '' != $this->meta['property_auction'][0] ) {
						return date( $format, strtotime($this->meta['property_auction'][0]) );
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
		$property_price_search	= $this->get_property_meta('property_price');
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
		// Commercial Lease Override
		if ( $this->get_property_meta('property_com_listing_type') == 'lease' ) {
			$property_price = $property_price_view; 
		} 
		return $property_price;
	}
	
	// Sold price display
	public function get_property_price_sold_display( $admin = false ) {
		$property_sold_price	= $this->get_property_meta('property_sold_price', false );
		$property_sold_display	= $this->get_property_meta('property_sold_price_display');
		
		if ( $property_sold_price != '' ) {
			if ( $property_sold_display == 'yes' || $admin == true ) {
				$property_sold_price = ' ' . epl_currency_formatted_amount( $property_sold_price );
				return $property_sold_price;
			}
		}
	}
	
	// Sold date display
	public function get_property_price_sold_date( $sold_price = null ) {
	
		if ( $sold_price == null ) 
			return;
			
		$property_sold_date	= $this->get_property_meta('property_sold_date' );
		
		if ( $property_sold_date != '' ) {
			return $sold_price . ' ' . $property_sold_date;
		}
	}
	
	// Rental Price XE Format
	public function get_property_rent () {
		if($this->post_type != 'rental')
			return;
		
		$property_rent_search	= $this->get_property_meta('property_rent');
		$property_rent_view	= $this->get_property_meta('property_rent_view');
		
		if ( $property_rent_search == '') {
			$rental_price = '';
		} elseif( $property_rent_view != '' ) {
			$rental_price = $property_rent_view; 
		} else {
			$rental_price = epl_currency_formatted_amount( $property_rent_search );
		}
		return $rental_price;
	}

	// Rental Bond
	public function get_property_bond ( ) {
		if($this->post_type != 'rental')
			return;
		
		$bond_position = apply_filters('epl_property_bond_position','after');
		
		if ( $this->get_property_meta('property_bond') !='' && $bond_position == 'before' ) {
			return $this->label_bond . ' ' . epl_currency_formatted_amount($this->get_property_meta('property_bond'));
		} elseif ( $this->get_property_meta('property_bond') !='' ) {
			return epl_currency_formatted_amount($this->get_property_meta('property_bond')).' '.$this->label_bond;
		}
	}
	// property rental available
	public function get_property_available( $admin = false ) {
		$format = $admin == true ? 'l jS M \a\t g:i a' : 'l jS F' ;
		if(isset($this->meta['property_date_available'])) {
			if(isset($this->meta['property_date_available'][0])) {
					if ( '' != $this->meta['property_date_available'][0] ) {
						return date( $format, strtotime($this->meta['property_date_available'][0]) );
					}
			}	
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
				$street .= $this->get_property_meta('property_address_street').' ';
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
				$price_plain_value = $this->label_sold;
			} 
			elseif ( '' != $this->get_property_price_display() && 'yes' == $this->get_property_meta('property_price_display') ) {	// Property
				$price_plain_value = $this->get_property_price_display();
			} else {
				if(!empty($this->epl_settings) && isset($this->epl_settings['label_poa'])) {
					$price_plain_value = $this->epl_settings['label_poa'];
				}
				$price_plain_value_poa = __( 'POA' , 'epl');
				if(!empty($this->epl_settings) && isset($this->epl_settings['label_poa'])) {
					$price_plain_value_poa = $this->epl_settings['label_poa'];
				}
				$price_plain_value = $price_plain_value_poa;
			}
			if ( 'yes' == $this->get_property_meta('property_under_offer') && 'sold' != $this->get_property_meta('property_status')) {
				
				$price_plain_value = $this->label_under_offer;
			}
			
		} elseif('rental' == $this->post_type) { 
		
			if( '' != $this->get_property_rent() && 'yes' == $this->get_property_meta('property_rent_display') && 'leased' != $this->get_property_meta('property_status') ) {
			
				$price_plain_value = $this->get_property_rent(). '/' . $this->get_property_meta('property_rent_period');
				
				if($this->get_property_bond() != '' && $this->epl_settings['display_bond'] == 'yes')
					$price_plain_value = $this->get_property_bond();
				
			} elseif('leased' == $this->get_property_meta('property_status')) {
				$price_plain_value = $this->label_leased;
				
			} else {
				$price_plain_value = __('TBA', 'epl');
			}
			
		} elseif ( 'commercial' == $this->post_type || 'business' == $this->post_type || 'commercial_land' == $this->post_type) {
			$rent_lease_type = 
				$this->get_property_meta('property_com_rent_period') != '' ? epl_listing_load_meta_commercial_rent_period_value( $this->get_property_meta('property_com_rent_period') ) : 'P.A.';
			// Sale or Both
			$price_plain_value = '';
			if ( $this->get_property_meta('property_com_listing_type') == 'sale' || $this->get_property_meta('property_com_listing_type') == 'both' ) {
				if ( '' != $this->get_property_price_display() && 'yes' == $this->get_property_meta('property_price_display') ) {	// Property
					$price_plain_value = apply_filters( 'epl_commercial_for_sale_label' , __('For Sale', 'epl') ).': '. $this->get_property_price_display() . $this->get_property_tax();
				} else {
					if(!empty($this->epl_settings) && isset($this->epl_settings['label_poa'])) {
						$price_plain_value = apply_filters( 'epl_commercial_for_sale_label' , __('For Sale', 'epl') ).' ' . $this->epl_settings['label_poa'];
					}
				}
			}
			// Lease or Both
			if ( $this->get_property_meta('property_com_listing_type') == 'lease' || $this->get_property_meta('property_com_listing_type') == 'both' ) { // Both
				$both = $this->get_property_meta('property_com_listing_type') == 'both' ? '<div class="epl-clear"></div>' : '';
				if ( $this->get_property_com_rent() != '' && $this->get_property_price_display() == '' ) {
					$price_plain_value .= $both . apply_filters( 'epl_commercial_for_lease_label' , __('For Lease', 'epl') ).' ' . $this->get_property_com_rent() . ' '.__($rent_lease_type, 'epl');
				} elseif ( $this->get_property_price_display() != '' && $this->get_property_meta('property_com_listing_type') == 'lease' ) {
					$price_plain_value .= $both .apply_filters( 'epl_commercial_for_lease_label' , __('For Lease', 'epl') ).' ' . $this->get_property_price_display();
				} elseif ( $this->get_property_meta('property_com_listing_type') == 'both' ) {
					$price_plain_value .= $both .apply_filters( 'epl_commercial_for_lease_label' , __('For Lease', 'epl') ).' ' . $this->get_property_com_rent() . ' '.__($rent_lease_type, 'epl');
				} else {
					if(!empty($this->epl_settings) && isset($this->epl_settings['label_poa'])) {
						$price_plain_value .= $both . apply_filters( 'epl_commercial_for_lease_label' , __('For Lease', 'epl') ).' ' . $this->epl_settings['label_poa'];
					}
				}
			}
			// Status
			if ( 'sold' == $this->get_property_meta('property_status') ){
				$price_plain_value = $this->label_sold;
			}
			if ( 'yes' == $this->get_property_meta('property_under_offer') && 'sold' != $this->get_property_meta('property_status') ) { // Under Offer
				$price_plain_value = $this->label_under_offer;
			}
			if ( 'leased' == $this->get_property_meta('property_status') ) {
				$price_plain_value = $this->label_leased;
			}
		}
		return $price_plain_value;
	}
	
	// get price 
	public function get_price() {
		$price = '';
		if ( 'property' == $this->post_type || 'land' == $this->post_type || 'rural' == $this->post_type){
			if ( 'sold' == $this->get_property_meta('property_status') ) {
				$price = '<span class="page-price sold-status">'.$this->label_sold . $this->get_property_price_sold_display() . '</span>';
			}
			elseif ( '' != $this->get_property_price_display() && 'yes' == $this->get_property_meta('property_price_display') ) {	// Property
				$price = '<span class="page-price">'. $this->get_property_price_display() . '</span>';
				
			}
			elseif ( $this->get_property_meta('property_authority') == 'auction' && 'no' == $this->get_property_meta('property_price_display') ) {	// Auction
				$price = '<span class="page-price auction">' . __( 'Auction' , 'epl') . ' ' . $this->get_property_auction() . '</span>';
			}
			else {
				$price_plain_value_poa = __( 'POA' , 'epl');
				if(!empty($this->epl_settings) && isset($this->epl_settings['label_poa'])) {
					$price = $this->epl_settings['label_poa'];
				}
				$price = '<span class="page-price">' . $price_plain_value_poa . '</span>';
			}
			if ( 'yes' == $this->get_property_meta('property_under_offer') && 'sold' != $this->get_property_meta('property_status')) {
				$price = '<span class="page-price under-offer-status">'.$this->label_under_offer.'</span>';
			}
			
		} elseif('rental' == $this->post_type) { 
			if( '' != $this->get_property_rent() && 'yes' == $this->get_property_meta('property_rent_display') && 'leased' != $this->get_property_meta('property_status') ) {
				
				$epl_property_price_rent_separator	= apply_filters('epl_property_price_rent_separator','/');
				
				$price = '<span class="page-price-rent">';
				$price .=		'<span class="page-price" style="margin-right:0;">'. $this->get_property_rent() . '</span>';
				if( '' == $this->get_property_meta('property_rent_view') ) {
					$price .=	'<span class="rent-period">' .$epl_property_price_rent_separator.' '. $this->get_property_meta('property_rent_period') . '</span>';
				}	
				$price .= '</span>';
				
				if($this->get_property_bond() != '' && in_array($this->get_epl_settings('display_bond'),array(1,'yes')))
					$price .= '<span class="bond">' . $this->get_property_bond() . '</span>';
					
			} elseif('leased' == $this->get_property_meta('property_status')) {
				$price = '<span class="page-price sold-status">'.$this->label_leased.'</span>';
				
			} else {
				$price = '<span class="page-price">'.__('TBA', 'epl').'</span>';
			}
			
		} elseif ( 'commercial' == $this->post_type || 'business' == $this->post_type || 'commercial_land' == $this->post_type) {
			$rent_lease_type = 
				$this->get_property_meta('property_com_rent_period') != '' ? epl_listing_load_meta_commercial_rent_period_value( $this->get_property_meta('property_com_rent_period') ) : 'P.A.';
			// Sale or both
			$price = '';
			if ( $this->get_property_meta('property_com_listing_type') == 'sale' || $this->get_property_meta('property_com_listing_type') == 'both' ) {
				if ( '' != $this->get_property_price_display() && 'yes' == $this->get_property_meta('property_price_display') ) {	// Property
					$price = '<span class="page-price"><span class="page-price-prefix">'.apply_filters( 'epl_commercial_for_sale_label' , __('For Sale', 'epl') ).'</span> '. $this->get_property_price_display() . $this->get_property_tax() . '</span>';
				} else {
					$price_plain_value = '';
					if(!empty($this->epl_settings) && isset($this->epl_settings['label_poa'])) {
						$price_plain_value = $this->epl_settings['label_poa'];
					}
					$price = '<span class="page-price"><span class="page-price-prefix">'.apply_filters( 'epl_commercial_for_sale_label' , __('For Sale', 'epl') ).'</span> ' . $price_plain_value . '</span>';
				}
			}
			// Lease or Both
			if ( $this->get_property_meta('property_com_listing_type') == 'lease' || $this->get_property_meta('property_com_listing_type') == 'both' ) { // Both
				$both = $this->get_property_meta('property_com_listing_type') == 'both' ? '<div class="epl-clear"></div>' : '';
				if ( $this->get_property_com_rent() != '' && $this->get_property_price_display() == '' ) {
					$price .= $both . '<span class="page-price"><span class="page-price-prefix">'.apply_filters( 'epl_commercial_for_lease_label' , __('For Lease', 'epl') ).'</span> ' . $this->get_property_com_rent() . ' '.__($rent_lease_type, 'epl').'</span>';
				} elseif ( $this->get_property_price_display() != '' && $this->get_property_meta('property_com_listing_type') == 'lease' ) {
					$price .= $both . '<span class="page-price"><span class="page-price-prefix">'.apply_filters( 'epl_commercial_for_lease_label' , __('For Lease', 'epl') ).'</span> ' . $this->get_property_price_display() . '</span>';
				} elseif ( $this->get_property_meta('property_com_listing_type') == 'both' ) {
					$price .= $both . '<span class="page-price"><span class="page-price-prefix">'.apply_filters( 'epl_commercial_for_lease_label' , __('For Lease', 'epl') ).'</span> ' . $this->get_property_com_rent() . ' '.__($rent_lease_type, 'epl'). '</span>';
				} else {
					if(!empty($this->epl_settings) && isset($this->epl_settings['label_poa'])) {
						$price .= $both . '<span class="page-price"><span class="page-price-prefix">'.apply_filters( 'epl_commercial_for_lease_label' , __('For Lease', 'epl') ).'</span> ' . $this->epl_settings['label_poa'] . '</span>';
					}
				}
			}
			// Status
			if ( 'sold' == $this->get_property_meta('property_status') ){
				$price = '<span class="page-price sold-status">'.$this->label_sold.'</span>';
			}
			if ( 'yes' == $this->get_property_meta('property_under_offer') && 'sold' != $this->get_property_meta('property_status') ) { // Under Offer
				$price = '<div class="page-price under-offer-status">'.$this->label_under_offer.'</div>';
			}
			if ( 'leased' == $this->get_property_meta('property_status') ) {
				$price = '<span class="page-price sold-status">'.$this->label_leased.'</span>';
			}
		}
		return $price;
	}
	
	// price sticker
	public function get_price_sticker() {
		$price_sticker = '';
		$date = new DateTime($this->post->post_date);
		$now = new DateTime();
		
		// php > 5.3
		if( method_exists($now,'diff') ) {
		
			$diff = $now->diff($date);
			$diff = $diff->days;
		} else {
			$diff = strtotime($date->format('M d Y ')) - strtotime($now->format('M d Y ') ) ;
			$diff = floor($diff/3600/24);
			
		}
		if ( 'property' == $this->post_type || 'land' == $this->post_type || 'rural' == $this->post_type){
			$price_sticker = '';
			if ( 'sold' == $this->get_property_meta('property_status') ) {
				$price_sticker .= '<span class="status-sticker sold">'.$this->label_sold.'</span>';
			} else {
				// Property
				$price_sticker = '';
				
				if($this->get_epl_settings('sticker_new_range') >=  $diff) 
					$price_sticker .= '<span class="status-sticker new">'.$this->get_epl_settings('label_new').'</span>';

				if ( $this->get_property_meta('property_inspection_times') != '' ){
					$price_sticker .= '<span class="status-sticker open">'.$this->get_epl_settings('label_home_open').'</span>';
				}
			
			}
			if ( 'yes' == $this->get_property_meta('property_under_offer') && 'sold' != $this->get_property_meta('property_status')) {
				$price_sticker = '';
				$price_sticker .= '<span class="status-sticker under-offer">'.$this->label_under_offer.'</span>';
			}
			
			
		} elseif('rental' == $this->post_type) { 
		
			if('leased' == $this->get_property_meta('property_status')) {
				$price_sticker = '';
				$price_sticker .= '<span class="status-sticker leased">'.$this->label_leased.'</span>';
				
			} else {
				$price_sticker = '';
				if($this->get_epl_settings('sticker_new_range') >=  $diff)
					$price_sticker .= '<span class="status-sticker new">'.$this->get_epl_settings('label_new').'</span>';

				if ( $this->get_property_meta('property_inspection_times') != '' ){
					$price_sticker .= '<span class="status-sticker open">'.$this->get_epl_settings('label_home_open').'</span>';
				}
			}
			
		} elseif ( 'commercial' == $this->post_type || 'business' == $this->post_type || 'commercial_land' == $this->post_type) {
			$price_sticker = '';
			if ( 'sold' == $this->get_property_meta('property_status') ){
				$price_sticker .= '<span class="status-sticker sold">'.$this->label_sold.'</span>';
			} 
			if ( 'yes' == $this->get_property_meta('property_under_offer') && 'sold' != $this->get_property_meta('property_status')) {
			
				$price_sticker .= '<span class="status-sticker under-offer">'.$this->label_under_offer.'</span>';
			}
			
		}
		return $price_sticker;
	}
	
	// get l price 
	public function get_l_price() {
		
		if ( 'property' == $this->post_type || 'land' == $this->post_type || 'rural' == $this->post_type){ 
			if ( 'sold' == $this->get_property_meta('property_status') ) {
				$l_price = '<li class="page-price sold-status">'.$this->label_sold.'</li>';
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
				$l_price = '<li class="page-price under-offer-status">'.$this->label_under_offer.'</li>';
			}
			
			
		} elseif('rental' == $this->post_type) { 
		
			if( '' != $this->get_property_rent() && 'yes' == $this->get_property_meta('property_rent_display') && 'leased' != $this->get_property_meta('property_status') ) {
					
				$l_price = '<li class="page-price-rent">
							<span class="page-price" style="margin-right:0;">'. $this->get_property_rent() . '</span>
							<span class="rent-period">/' . $this->get_property_meta('property_rent_period') . '</span>
						</li>';
					
			} elseif('leased' == $this->get_property_meta('property_status')) {
				$l_price = '<li class="page-price sold-status">'.$this->label_leased.'</li>';
				
			}
			
		} elseif ( 'commercial' == $this->post_type || 'business' == $this->post_type || 'commercial_land' == $this->post_type) {
			$rent_lease_type = 
				$this->get_property_meta('property_com_rent_period') != '' ? epl_listing_load_meta_commercial_rent_period_value( $this->get_property_meta('property_com_rent_period') ) : 'P.A.';
			if ( 'sold' == $this->get_property_meta('property_status') ){
				$l_price = '<li class="status-sticker sold">'.$this->label_sold.'</li>';
			} elseif ( '' != $this->get_property_price_display() && 'yes' == $this->get_property_meta('property_price_display') ) {	// Property
				$l_price = '<li class="page-price"><span class="page-price-prefix">'.apply_filters( 'epl_commercial_for_sale_label' , __('For Sale', 'epl') ).'</span> '. $this->get_property_price_display() . $this->get_property_tax() . '</li>';
			} else {
				$price_plain_value = '';
				if(!empty($this->epl_settings) && isset($this->epl_settings['label_poa'])) {
					$price_plain_value = $this->epl_settings['label_poa'];
				}
				$l_price = '<li class="page-price">' . $price_plain_value . '</li>';
			}
			if ( 'yes' == $this->get_property_meta('property_under_offer') && 'sold' != $this->get_property_meta('property_status')) {
				$l_price = '<li class="page-price under-offer-status">'.$this->label_under_offer.'</li>';
			}
			if( $this->get_property_com_rent() != '' && $this->get_property_meta('property_com_listing_type') == 'both') {
				
				$l_price .= '<li class="page-price"><span class="page-price-prefix">'.apply_filters( 'epl_commercial_for_lease_label' , __('For Lease', 'epl') ).'</span> ' . $this->get_property_com_rent() . ' '.__($rent_lease_type, 'epl').'</li>';
			} elseif($this->get_property_com_rent() != '' && $this->get_property_meta('property_com_listing_type') == 'lease') {
				$l_price = '<li class="page-price"><span class="page-price-prefix">'.apply_filters( 'epl_commercial_for_lease_label' , __('For Lease', 'epl') ).'</span> ' . $this->get_property_com_rent() . ' '.__($rent_lease_type, 'epl').'</li>';
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
	
	// property year built
	public function get_property_year_built($returntype = 'i') {
		if($this->get_property_meta('property_year_built') == '')
			return;
		$year_built['i'] = '<span title="'.__('Built', 'epl').'" class="icon year-built"><span class="icon-value">'. $this->get_property_meta('property_year_built') . '</span></span>'; 
		$year_built['d'] = __('Built', 'epl') . ' ' . $this->get_property_meta('property_year_built') . ' ';
		$year_built['l'] = '<li class="year-built">'.__('Built', 'epl') . ' ' . $this->get_property_meta('property_year_built') . '</li>';
		return $year_built[$returntype];
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
		$bath['d'] = $this->get_property_meta('property_bathrooms') . ' '.__('bath', 'epl').' ';
		$bath['l'] = '<li class="bathrooms">' . $this->get_property_meta('property_bathrooms') . ' '.__('bath', 'epl').'</li>';
		return $bath[$returntype];
	}
	
	// property rooms
	public function get_property_rooms($returntype = 'i') {
		if($this->get_property_meta('property_rooms') == '')
			return;
		$rooms['i'] = '<span title="'.__('Rooms', 'epl').'" class="icon rooms"><span class="icon-value">'. $this->get_property_meta('property_rooms') . '</span></span>'; 
		$rooms['d'] = $this->get_property_meta('property_rooms') . ' '.__('rooms', 'epl').' ';
		$rooms['l'] = '<li class="rooms">' . $this->get_property_meta('property_rooms') . ' '.__('rooms', 'epl').'</li>';
		return $rooms[$returntype];
	}

	// property parking for single icon
	public function get_property_parking($returntype = 'i') {
		if( $this->get_property_meta('property_garage') == '' && $this->get_property_meta('property_carport') == '' )
			return;
		$property_garage 	= intval($this->get_property_meta('property_garage'));
		$property_carport 	= intval($this->get_property_meta('property_carport'));
		$property_parking 	= $property_carport + $property_garage;
		if ( $property_parking == 0)
			return;
		$parking['i'] = '<span title="'.__('Parking Spaces', 'epl').'" class="icon parking"><span class="icon-value">' .$property_parking. '</span></span>';
		$parking['d'] = $property_parking . ' '.__('Parking Spaces', 'epl').' ';
		$parking['l'] = '<li class="parking">' . $property_parking . ' '.__('Parking Spaces', 'epl').'</li>';
		return $parking[$returntype];
	}

	// property garage
	public function get_property_garage($returntype = 'i') {
		if($this->get_property_meta('property_garage') == '')
			return;
		$garage['i'] = '<span title="'.__('Garage', 'epl').'" class="icon parking"><span class="icon-value">'. $this->get_property_meta('property_garage') . '</span></span>'; 
		$garage['l'] = '<li class="garage">' . $this->get_property_meta('property_garage') . ' '.__('garage', 'epl').'</li>';
		$garage['d'] = $this->get_property_meta('property_garage') . ' '.__('garage', 'epl').' '; 
		return $garage[$returntype];
	}

	// property cargport
	public function get_property_carport($returntype = 'i') {
		if($this->get_property_meta('property_carport') == '')
			return;
		$carport['i'] = '<span title="'.__('Carport', 'epl').'" class="icon parking"><span class="icon-value">'. $this->get_property_meta('property_carport') . '</span></span>'; 
		$carport['l'] = '<li class="carport">' . $this->get_property_meta('property_carport') . ' '.__('carport', 'epl').'</li>';
		$carport['d'] = $this->get_property_meta('property_carport') . ' '.__('Carport', 'epl').' ';
		
		return $carport[$returntype];
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
			$property_land_area_unit = __('sqm' , 'epl');
		}
		if(intval($this->get_property_meta('property_land_area')) != 0 ) {
			return '
				<li class="land-size">'. __('Land is', 'epl').' ' . $this->get_property_meta('property_land_area') .' '.$property_land_area_unit.'</li>';
		}
	}
	
	// property building area
	public function get_property_building_area_value($returntype = 'i') {
		$building_unit = $this->get_property_meta('property_building_area_unit');
		if ( $building_unit == 'squareMeter' ) {
			$building_unit = 'm²';
		}
		if(intval($this->get_property_meta('property_building_area')) != 0 ) { 
			return '
			<li class="land-size">'.__('Floor Area is', 'epl').' ' .
		 		$this->get_property_meta('property_building_area') .' '.$building_unit.
	 		'</li>';
		}
		
	}
	
	// new construction
	public function get_property_new_construction($returntype = 'i') {
		$property_new_construction = $this->get_property_meta('property_new_construction');
		if( isset($property_new_construction) && ($property_new_construction == 1 || $property_new_construction == 'yes') ) { 
			$return_construction = array();
			$return_construction['i'] = '<span title="'.__('New Construction', 'epl').'" class="icon new_construction"></span>'; 
			$return_construction['l'] = '<li class="new_construction">'.__('New Construction', 'epl').'</li>';
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
				
				if($metakey == 'property_category'){
					$metavalue = $this->get_property_category();
				}
				
				if( (is_numeric($metavalue)) ) {
					if($metavalue == 0)
						return;
					// toggle field types -- 1 for toggle true
					if( $metavalue == 1 ){ 
						return '<li class="'.$this->get_class_from_metakey($metakey).'">'.__($this->get_label_from_metakey($metakey), 'epl').'</li>';
					} elseif(is_numeric($metavalue)) {
						// numbered field types 
						return '<li class="'.$this->get_class_from_metakey($metakey).'">'.$metavalue.' '.__($this->get_label_from_metakey($metakey), 'epl').'</li>';
					} else {
						// others
						return '<li class="'.$this->get_class_from_metakey($metakey).'">'.__($metavalue,'epl').'</li>';
					}
					
				}
				if( ( $metavalue == 'yes' ) ) {
					return '<li class="'.$this->get_class_from_metakey($metakey).'">'.__($this->get_label_from_metakey($metakey), 'epl').'</li>';
				}
				
				if( $metavalue == 'no' )
						return;

				// string value field types
				return '<li class="'.$this->get_class_from_metakey($metakey).'">'.__($metavalue,'epl').'</li>';
			}
	}
	
	// additional rural features html
	public function get_additional_rural_features_html($metakey) {
			$metavalue = $this->get_property_meta($metakey);
			if( isset($metavalue) && $metavalue != '' ) {
				return '<div class="'.$this->get_class_from_metakey($metakey,$search= 'property_rural_').'">
							<h6>'.__($this->get_label_from_metakey($metakey,'property_rural_'), 'epl').'</h6>'.
							'<p>'.__($metavalue,'epl').'</p>'.
						'</div>';
			}
	}
	
	// additional commerical features html
	public function get_additional_commerical_features_html($metakey) {
			$metavalue = $this->get_property_meta($metakey);
			if( isset($metavalue) && $metavalue != '' ) {
				return '<div class="'.$this->get_class_from_metakey($metakey,$search= 'property_com_').'">
							<h6>'.__($this->get_label_from_metakey($metakey,'property_com_'), 'epl').'</h6>'.
							'<p>'.__($metavalue,'epl').'</p>'.
						'</div>';
			}
	}
	
	
	public function get_class_from_metakey($key,$search= 'property_'){
		 return str_replace("property_", "", $key);
		
	}
	
	public function get_label_from_metakey($key,$search= 'property_'){
		 return ucwords(str_replace('_',' ',str_replace($search, "", $key)));
	}
	
	public function get_features_from_taxonomy() {
		$property_feature_taxonomy = '';
		if ( taxonomy_exists('tax_feature') ) {
			global $post;
			$property_feature_taxonomy = epl_get_the_term_list($this->post->ID, 'tax_feature', '<li>', '</li><li>', '</li>' );
		}
		return $property_feature_taxonomy;
	}
}
