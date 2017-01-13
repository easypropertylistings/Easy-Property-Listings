<?php
/**
 * Property Meta Fields
 *
 * @package     EPL
 * @subpackage	Classes/PropertyMeta
 * @copyright   Copyright (c) 2016, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * EPL_Property_Meta Class
 *
 * @since      2.0
 */
class EPL_Property_Meta {

	/**
	 * Global Instance
	 *
	 * @since 2.0
	 */
	static $instance;

	/**
	 * Global post
	 *
	 * @since 2.0
	 */
	public $post;

	/**
	 * Global epl_settings
	 *
	 * @since 2.0
	 */
	public $epl_settings;

	/**
	 * Global meta
	 *
	 * @since 2.0
	 */
	public $meta;

	/**
	 * Global post type
	 *
	 * @since 2.0
	 */
	public $post_type;

	/**
	 * Global EPL Post Type
	 *
	 * @since 2.0
	 */
	public $property_post_type;

	/**
	 * Get things going
	 *
	 * @since 2.0
	 */
	public function __construct($post) {

        $this->post 				= $post;
        $this->epl_settings 		= epl_settings();
        $this->meta 				= get_post_custom($post->ID);
        $this->post_type 			= $this->post->post_type;
        $this->property_post_type	= $this->post->post_type;

        $this->epl_labels();
    }

	/**
	 * Define Admin labels in epl_settings global variable
	 *
	 * @since 2.0
	 * @return array Adds the custom admin labels
	 */
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

	/**
	 * Return the meta value based on the meta key.
	 *
	 * Usage is $property->get_property_meta('meta_key') with the global $property variable defined
	 *
	 * @since 2.0
	 * @param  string $meta_key The meta key to get the value
	 * @param  bool $allowzero Return a 0 value or if false and a value of 0 return nothing. Default True.
	 * @return string|integer 	Return the value of the meta key, string, or integer.
	 */
	public function get_property_meta( $meta_key , $allowzero = true ) {
		if(isset($this->meta[$meta_key])) {
			if(isset($this->meta[$meta_key][0])) {
				if($allowzero === true){
					return  maybe_unserialize($this->meta[$meta_key][0]);
				} elseif(intval($this->meta[$meta_key][0]) == 0) {
					return;
				} else {
					return maybe_unserialize($this->meta[$meta_key][0]);
				}
			}
		}
	}

	/**
	 * Outputs the inspection times based on settings format
	 *
	 * @since 2.0
	 * @param  string $meta_key The meta key to get the value from default is property_inspection_times
	 * @return mixed Return formatted inspection times with a iCal link
	 */
	public function get_property_inspection_times( $meta_key = 'property_inspection_times' ) {
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
						if(strtotime($endtime) > current_time( 'timestamp' ) ) {
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

	/**
	 * Get the setting based on the key
	 *
	 * Usage is $property->get_epl_settings('meta_key') with the global $property variable defined
	 *
	 * @since 2.0
	 * @param  string $meta_key The meta key to get the setting from
	 * @return string Value of the setting or if not set nothing is returned
	 */
	public function get_epl_settings( $key ) {
		$value = '';
		if(!empty($this->epl_settings) && isset($this->epl_settings[$key])) {
			$value = $this->epl_settings[$key];
		}
		return $value;
	}

	/**
	 * Process Property Features Taxonomy
	 *
	 * @since 2.0
	 * @return mixed List of features
	 */
	public function get_property_feature_taxonomy() {
		return apply_filters('epl_get_property_feature_taxonomy',get_the_term_list($this->post->ID, 'tax_feature', '<li>', '</li><li>', '</li>' ));
	}

	/**
	 * Suburb profile
	 *
	 * @since 2.0
	 * @return string Suburb/Location name
	 */
	public function get_suburb_profile() {
		$profiles = get_the_terms( $this->post->ID, 'location' );
		if ( is_array ($profiles) ){
			$profile = end($profiles);
			return $profile->name;
		}
	}

	/**
	 * Auction Date/Time
	 *
	 * @since 2.0
	 * @param $admin Bool true/false
	 * @return string formatted auction date
	 */
	public function get_property_auction( $admin=false ) {
		$format = $admin == true ? apply_filters ( 'epl_get_property_auction_date' , 'l jS M \a\t g:i a') : apply_filters ( 'epl_get_property_auction_date' , 'l jS M \a\t g:i a');
		if(isset($this->meta['property_auction'])) {
			if(isset($this->meta['property_auction'][0])) {
					if ( '' != $this->meta['property_auction'][0] ) {
						if(strpos($this->meta['property_auction'][0], 'T') === FALSE){
							$feed_format 	= apply_filters('epl_auction_feed_format','Y-m-d-H:i:s');
                            $epl_date 		= DateTime::createFromFormat($feed_format, $this->meta['property_auction'][0]);

                            if($epl_date) {
                            	$primary_feed_format 	= apply_filters('epl_auction_primary_feed_format','Y-m-d\TH:i');
                                $this->meta['property_auction'][0] = $epl_date->format($primary_feed_format);
                            }
                        }
						return apply_filters('epl_get_property_auction',date( $format, strtotime($this->meta['property_auction'][0]) ));
					}
			}
		}
	}

	/**
	 * Commercial Rent
	 *
	 * @since 2.0
	 * @return string Currency Formatted price
	 */
	public function get_property_com_rent(){
		$rent =  epl_currency_formatted_amount( $this->get_property_meta('property_com_rent') );

		return apply_filters('epl_get_property_com_rent',$rent);
	}

	/**
	 * Price Display
	 *
	 * @since 2.0
	 * @return string Return formatted price based on selected options
	 */
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
			$property_price = apply_filters('epl_get_property_auction_label',__( 'Auction' , 'easy-property-listings' ) ). ' ' . $this->get_property_auction();
		}
		// Commercial Lease Override
		if ( $this->get_property_meta('property_com_listing_type') == 'lease' ) {
			$property_price = $property_price_view;
		}
		return apply_filters('epl_get_property_price_display',$property_price);
	}

	/**
	 * Sold price display
	 *
	 * @since 2.0
	 * @return string Return sold price if selected or nothing
	 */
	public function get_property_price_sold_display( $admin = false ) {
		$property_sold_price	= $this->get_property_meta('property_sold_price', false );
		$property_sold_display	= $this->get_property_meta('property_sold_price_display');

		if ( $property_sold_price != '' ) {
			if ( $property_sold_display == 'yes' || $admin == true ) {
				$property_sold_price = ' ' . epl_currency_formatted_amount( $property_sold_price );

				return apply_filters('epl_get_property_price_sold_display',$property_sold_price);
			}
		}
	}

	/**
	 * Sold price display
	 *
	 * @since 3.1
	 * @return string Return lease price if selected or nothing
	 */
	public function get_property_price_lease_display( $admin = false ) {
		$property_sold_price	= $this->get_property_price_display();
		$property_sold_display	= $this->get_property_meta('property_sold_price_display');

		if ( $property_sold_price != '' ) {
			if ( $property_sold_display == 'yes' || $admin == true ) {

				return apply_filters('epl_get_property_price_lease_display',$property_sold_price);
			}
		}
	}

	/**
	 * Sold date display
	 *
	 * @since 2.0
	 * @param integer $sold_price Sold price
	 * @return string Return sold date if sold
	 */
	public function get_property_price_sold_date( $sold_price = null ) {

		if ( $sold_price == null )
			return;

		$property_sold_date	= $this->get_property_meta('property_sold_date' );

		if ( $property_sold_date != '' ) {
			return apply_filters('epl_get_property_price_sold_date',$sold_price . ' ' . $property_sold_date);
		}
	}

	/**
	 * Rental Price
	 *
	 * @since 2.0
	 * @return string Currency Formatted Rental Price
	 */
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
		return apply_filters('epl_get_property_rent',$rental_price);
	}

	/**
	 * Rental Bond
	 *
	 * @since 2.0
	 * @return string Formatted Bond price
	 */
	public function get_property_bond ( ) {
		if($this->post_type != 'rental')
			return;

		$bond_position = apply_filters('epl_property_bond_position','after');

		$bond = '';
		if ( $this->get_property_meta('property_bond') != '' && $bond_position == 'before' ) {
			$bond =  $this->label_bond . ' ' . epl_currency_formatted_amount($this->get_property_meta('property_bond'));
		} elseif ( $this->get_property_meta('property_bond') != '' ) {
			$bond =  epl_currency_formatted_amount($this->get_property_meta('property_bond')).' '.$this->label_bond;
		}
		return apply_filters('epl_get_property_bond',$bond);
	}

	/**
	 * Rental Available Date
	 *
	 * @since 2.0
	 * @param bool $admin
	 * @return string Formatted date
	 */
	public function get_property_available( $admin = false ) {
		$format = $admin == true ? apply_filters('epl_property_available_date_format_admin','l jS M \a\t g:i a') : apply_filters('epl_property_available_date_format','l jS F Y') ;
		if(isset($this->meta['property_date_available'])) {
			if(isset($this->meta['property_date_available'][0])) {
				if ( '' != $this->meta['property_date_available'][0] ) {
					$av_date_array = date_parse($this->meta['property_date_available'][0]);
					$av_date = ( isset($av_date_array['year']) && isset($av_date_array['month']) && isset($av_date_array['day']) ) ?
						$av_date_array['year'].'-'.$av_date_array['month'].'-'.$av_date_array['day']: $this->meta['property_date_available'][0];
					if(current_time( 'timestamp' ) > strtotime($av_date) ) {
						return apply_filters( 'epl_property_sub_title_available_now_label' , __('now','easy-property-listings' ) );
					} else {
						return apply_filters('epl_get_property_available',date( $format, strtotime($av_date) ));
					}

				}
			}
		}
	}

	/**
	 * Land category
	 *
	 * @since 2.0
	 * @return string
	 */
	public function get_property_land_category() {
		if ( !in_array( $this->post_type, array('land','commercial_land') ) )
			return;

		if ( $this->get_property_meta('property_land_category') !='' ) {
			return apply_filters('epl_get_property_land_category',epl_listing_meta_land_category_value( $this->get_property_meta('property_land_category') ) );
		}
	}

	/**
	 * Formatted Address based on selected display option
	 *
	 * @since 2.0
	 * @return string formatted street address
	 */
	public function get_formatted_property_address() {
		$street =  $this->get_property_meta('property_address_lot_number').' ';

		if($this->get_property_meta('property_address_sub_number') != '')
			$street .= $this->get_property_meta('property_address_sub_number').'/';

		$street .= $this->get_property_meta('property_address_street_number').' ';
		$street .= $this->get_property_meta('property_address_street').' ';
		//$street .=$this->get_property_meta('property_address_suburb');

		return apply_filters('epl_get_formatted_property_address',$street);
	}

	/**
	 * Listing Category
	 *
	 * @since 2.0
	 * @return string
	 */
	public function get_property_category () {
		return apply_filters('epl_get_property_category',epl_listing_meta_property_category_value( $this->get_property_meta('property_category') ));
	}

	/**
	 * Price Class
	 *
	 * @since 2.0
	 * @return string
	 */
	public function get_price_class() {
		if($this->get_property_meta('property_status') == 'sold'){
			return apply_filters('property_price_class','epl-price sold');
		}
	}

	/**
	 * Tax
	 *
	 * @since 2.0
	 * @return string
	 */
	public function get_property_tax() {
		$property_tax = '';
		if( $this->get_property_meta('property_tax') != '' ) {
				$property_tax = $this->get_property_meta('property_tax');
			}
		if ( $property_tax == 'exempt' ) {
			$property_tax = ' '.__('Inc. GST', 'easy-property-listings' );
		} elseif ( $property_tax == 'inclusive' ) {
			$property_tax = ' '.__('Inc. GST', 'easy-property-listings' );
		} elseif ( $property_tax == 'exclusive' ) {
			$property_tax = ' +  '.__('GST', 'easy-property-listings' );
		} else {
			$property_tax = '';
		}
		return apply_filters('epl_get_property_tax',$property_tax);
	}

	/**
	 * Plain price value
	 *
	 * @since 2.0
	 * @return string
	 */
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
				} else {
					$price_plain_value = __( 'POA' , 'easy-property-listings' );
				}
			}
			if ( 'yes' == $this->get_property_meta('property_under_offer') && 'sold' != $this->get_property_meta('property_status')) {

				$price_plain_value = $this->label_under_offer;
			}

		} elseif('rental' == $this->post_type) {

			if( '' != $this->get_property_rent() && 'yes' == $this->get_property_meta('property_rent_display') && 'leased' != $this->get_property_meta('property_status') ) {

				$price_plain_value = $this->get_property_rent() . '/' .  __( ucfirst($this->get_property_meta('property_rent_period')), 'easy-property-listings');
				if ( '' != $this->get_property_meta('property_rent_view') ) {
					//$price_plain_value = $this->get_property_rent();
					$price_plain_value = $this->get_property_meta('property_rent_view');
				}

				if($this->get_property_bond() != '' && $this->epl_settings['display_bond'] == 'yes')
					$price_plain_value = $this->get_property_bond();

			} elseif('leased' == $this->get_property_meta('property_status')) {
				$price_plain_value = $this->label_leased . ' ' . $this->get_property_rent();

			} else {
				$price_plain_value = __('TBA', 'easy-property-listings' );
			}

		} elseif ( 'commercial' == $this->post_type || 'business' == $this->post_type || 'commercial_land' == $this->post_type) {
			$rent_lease_type =
				$this->get_property_meta('property_com_rent_period') != '' ? epl_listing_load_meta_commercial_rent_period_value( $this->get_property_meta('property_com_rent_period') ) : 'P.A.';
			// Sale or Both
			$price_plain_value = '';
			if ( $this->get_property_meta('property_com_listing_type') == 'sale' || $this->get_property_meta('property_com_listing_type') == 'both' ) {

				if ( $this->get_property_meta('property_com_authority') == 'auction' ) {	// Auction
					$price_plain_value = '<span class="page-price auction">' . apply_filters('epl_commercial_auction_label',__( 'Auction' , 'easy-property-listings' ) ) . ' ' . $this->get_property_auction() . '</span>';
				}
				elseif ( '' != $this->get_property_price_display() && 'yes' == $this->get_property_meta('property_price_display') ) {	// Property
					$price_plain_value = apply_filters( 'epl_commercial_for_sale_label' , __('For Sale', 'easy-property-listings' ) ).': '. $this->get_property_price_display() . $this->get_property_tax();
				} else {
					if(!empty($this->epl_settings) && isset($this->epl_settings['label_poa'])) {
						$price_plain_value = apply_filters( 'epl_commercial_for_sale_label' , __('For Sale', 'easy-property-listings' ) ).' ' . $this->epl_settings['label_poa'];
					}
				}
			}
			// Lease or Both
			if ( $this->get_property_meta('property_com_listing_type') == 'lease' || $this->get_property_meta('property_com_listing_type') == 'both' ) { // Both
				$both = $this->get_property_meta('property_com_listing_type') == 'both' ? '<div class="epl-clear"></div>' : '';
				if ( $this->get_property_com_rent() != '' && $this->get_property_price_display() == '' ) {
					$price_plain_value .= $both . apply_filters( 'epl_commercial_for_lease_label' , __('For Lease', 'easy-property-listings' ) ).' ' . $this->get_property_com_rent() . ' '.__($rent_lease_type, 'easy-property-listings' );
				} elseif ( $this->get_property_price_display() != '' && $this->get_property_meta('property_com_listing_type') == 'lease' ) {
					$price_plain_value .= $both .apply_filters( 'epl_commercial_for_lease_label' , __('For Lease', 'easy-property-listings' ) ).' ' . $this->get_property_price_display();
				} elseif ( $this->get_property_meta('property_com_listing_type') == 'both' ) {
					$price_plain_value .= $both .apply_filters( 'epl_commercial_for_lease_label' , __('For Lease', 'easy-property-listings' ) ).' ' . $this->get_property_com_rent() . ' '.__($rent_lease_type, 'easy-property-listings' );
				} else {
					if(!empty($this->epl_settings) && isset($this->epl_settings['label_poa'])) {
						$price_plain_value .= $both . apply_filters( 'epl_commercial_for_lease_label' , __('For Lease', 'easy-property-listings' ) ).' ' . $this->epl_settings['label_poa'];
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
		return apply_filters('epl_get_price_plain_value',$price_plain_value);
	}

	/**
	 * Get Price
	 *
	 * @since 2.0
	 * @return string
	 */
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
				$price = '<span class="page-price auction">' . apply_filters('epl_get_property_auction_label', __( 'Auction' , 'easy-property-listings' ) ) . ' ' . $this->get_property_auction() . '</span>';
			}
			else {
				$price_plain_value_poa = __( 'POA' , 'easy-property-listings' );
				if(!empty($this->epl_settings) && isset($this->epl_settings['label_poa'])) {
					$price_plain_value_poa = $this->epl_settings['label_poa'];
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
				if ( '' == $this->get_property_meta('property_rent_view') ) {
					$price .=	'<span class="rent-period">' .$epl_property_price_rent_separator.''. __( ucfirst($this->get_property_meta('property_rent_period')), 'easy-property-listings') . '</span>';
				}
				$price .= '</span>';

				if( $this->get_property_bond() != '' && in_array($this->get_epl_settings('display_bond'),array(1,'yes')))
					$price .= '<span class="bond">' . $this->get_property_bond() . '</span>';

			} elseif('leased' == $this->get_property_meta('property_status')) {
				$price = '<span class="page-price sold-status">'.$this->label_leased.'</span>';

			} else {
				$price = '<span class="page-price">'.__('TBA', 'easy-property-listings' ).'</span>';
			}

		} elseif ( 'commercial' == $this->post_type || 'business' == $this->post_type || 'commercial_land' == $this->post_type) {
			$rent_lease_type =
				$this->get_property_meta('property_com_rent_period') != '' ? epl_listing_load_meta_commercial_rent_period_value( $this->get_property_meta('property_com_rent_period') ) : __('P.A.' , 'easy-property-listings' );

			// Sale or both
			$price = '';
			if ( $this->get_property_meta('property_com_listing_type') == 'sale' || $this->get_property_meta('property_com_listing_type') == 'both' ) {

				if ( $this->get_property_meta('property_com_authority') == 'auction' ) {	// Auction
					$price = '<span class="page-price auction">' . apply_filters('epl_commercial_auction_label',__( 'Auction' , 'easy-property-listings' ) ) . ' ' . $this->get_property_auction() . '</span>';
				} elseif ( '' != $this->get_property_price_display() && 'yes' == $this->get_property_meta('property_price_display') ) {	// Property
					$price = '<span class="page-price"><span class="page-price-prefix">'.apply_filters( 'epl_commercial_for_sale_label' , __('For Sale', 'easy-property-listings' ) ).'</span> '. $this->get_property_price_display() . $this->get_property_tax() . '</span>';
				} else {
					$price_plain_value = '';
					if(!empty($this->epl_settings) && isset($this->epl_settings['label_poa'])) {
						$price_plain_value = $this->epl_settings['label_poa'];
					}
					$price = '<span class="page-price"><span class="page-price-prefix">'.apply_filters( 'epl_commercial_for_sale_label' , __('For Sale', 'easy-property-listings' ) ).'</span> ' . $price_plain_value . '</span>';
				}
			}

			// Lease
			if ( $this->get_property_meta('property_com_listing_type') == 'lease' ) {
				$both = $this->get_property_meta('property_com_listing_type') == 'both' ? '<div class="epl-clear"></div>' : '';

				// Show Price
				if ( $this->get_property_meta( 'property_price_display' ) == 'yes' ) {

					if ( $this->get_property_price_display() != '' ) {
						$price .= $both . '<span class="page-price"><span class="page-price-prefix">'.apply_filters( 'epl_commercial_for_lease_label' , __('For Lease', 'easy-property-listings' ) ).'</span> ' . $this->get_property_price_display() . '</span>';

					}

					elseif ( $this->get_property_com_rent() != '' ) {
						$price .= $both . '<span class="page-price"><span class="page-price-prefix">'.apply_filters( 'epl_commercial_for_lease_label' , __('For Lease', 'easy-property-listings' ) ).'</span> ' . $this->get_property_com_rent() . ' '.__($rent_lease_type, 'easy-property-listings' ).'</span>';
					}
				}

				// Hide Price
				else {
					if(!empty($this->epl_settings) && isset($this->epl_settings['label_poa'])) {
						$price .= $both . '<span class="page-price"><span class="page-price-prefix">'.apply_filters( 'epl_commercial_for_lease_label' , __('For Lease', 'easy-property-listings' ) ).'</span> ' . $this->epl_settings['label_poa'] . '</span>';
					}
				}
			}

			// Both (Add Lease Price Details)
			if ( $this->get_property_meta('property_com_listing_type') == 'both' ) {
				$both = $this->get_property_meta('property_com_listing_type') == 'both' ? '<div class="epl-clear"></div>' : '';

				// Show Price
				if ( $this->get_property_meta( 'property_price_display' ) == 'yes' ) {

					if ( $this->get_property_com_rent() != '' ) {
						$price .= $both . '<span class="page-price"><span class="page-price-prefix">'.apply_filters( 'epl_commercial_for_lease_label' , __('For Lease', 'easy-property-listings' ) ).'</span> ' . $this->get_property_com_rent() . ' '.__($rent_lease_type, 'easy-property-listings' ).'</span>';
					}
				}

				// Hide Price
				else {
					if(!empty($this->epl_settings) && isset($this->epl_settings['label_poa'])) {
						$price .= $both . '<span class="page-price"><span class="page-price-prefix">'.apply_filters( 'epl_commercial_for_lease_label' , __('For Lease', 'easy-property-listings' ) ).'</span> ' . $this->epl_settings['label_poa'] . '</span>';
					}
				}
			}

			// Status
			if ( 'sold' == $this->get_property_meta('property_status') ){
				$price = '<span class="page-price sold-status">'.$this->label_sold.'</span>' . $this->get_property_price_sold_display();
			}
			if ( 'yes' == $this->get_property_meta('property_under_offer') && 'sold' != $this->get_property_meta('property_status') ) { // Under Offer
				$price = '<div class="page-price under-offer-status">'.$this->label_under_offer.'</div>';
			}
			if ( 'leased' == $this->get_property_meta('property_status') ) {
				$price = '<span class="page-price leased-status sold-status">'.$this->label_leased.'</span>' . $this->get_property_price_lease_display();
			}
		}
		return apply_filters('epl_get_price',$price);
	}

	// price sticker
	/**
	 * Price Sticker
	 *
	 * @since 2.0
	 * @return string
	 */
	public function get_price_sticker() {
		$price_sticker = '';
		$date = new DateTime($this->post->post_date);
		$now = new DateTime();

		// php > 5.3
		if( method_exists($now,'diff') ) {

			$diff = $now->diff($date);
			$diff = $diff->days;
		} else {
			$diff = strtotime($now->format('M d Y ')) - strtotime($date->format('M d Y ') ) ;
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
			if ( 'leased' == $this->get_property_meta('property_status') ){
				$price_sticker .= '<span class="status-sticker leased">'.$this->label_leased.'</span>';
			}
			if ( 'yes' == $this->get_property_meta('property_under_offer') && 'sold' != $this->get_property_meta('property_status')) {

				$price_sticker .= '<span class="status-sticker under-offer">'.$this->label_under_offer.'</span>';
			}

		}
		return apply_filters('epl_get_price_sticker',$price_sticker);
	}

	/**
	 * Get list style price
	 *
	 * @since 2.0
	 * @return string
	 */
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
				$this->get_property_meta('property_com_rent_period') != '' ? epl_listing_load_meta_commercial_rent_period_value( $this->get_property_meta('property_com_rent_period') ) : __('P.A.' , 'easy-property-listings' );
			if ( 'sold' == $this->get_property_meta('property_status') ){
				$l_price = '<li class="status-sticker sold">'.$this->label_sold.'</li>';
			} elseif ( '' != $this->get_property_price_display() && 'yes' == $this->get_property_meta('property_price_display') ) {	// Property
				$l_price = '<li class="page-price"><span class="page-price-prefix">'.apply_filters( 'epl_commercial_for_sale_label' , __('For Sale', 'easy-property-listings' ) ).'</span> '. $this->get_property_price_display() . $this->get_property_tax() . '</li>';
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

				$l_price .= '<li class="page-price"><span class="page-price-prefix">'.apply_filters( 'epl_commercial_for_lease_label' , __('For Lease', 'easy-property-listings' ) ).'</span> ' . $this->get_property_com_rent() . ' '.__($rent_lease_type, 'easy-property-listings' ).'</li>';
			} elseif($this->get_property_com_rent() != '' && $this->get_property_meta('property_com_listing_type') == 'lease') {
				$l_price = '<li class="page-price"><span class="page-price-prefix">'.apply_filters( 'epl_commercial_for_lease_label' , __('For Lease', 'easy-property-listings' ) ).'</span> ' . $this->get_property_com_rent() . ' '.__($rent_lease_type, 'easy-property-listings' ).'</li>';
			}
		}
		return apply_filters('epl_get_price_in_list',$l_price);
	}

	/**
	 * Get Commercial Category
	 *
	 * @since 2.0
	 * @param string $tag HTML wrapper type, default div
	 * @return string
	 */
	public function get_property_commercial_category( $tag = 'div' ) {
		$property_commercial_category = epl_listing_load_meta_commercial_category_value( $this->get_property_meta('property_commercial_category') );
		$property_commercial_category = '<'.$tag.' class="commercial-category">' . __($property_commercial_category,'easy-property-listings' ) . '</'.$tag.'>';

		return apply_filters('epl_get_property_commercial_category',$property_commercial_category);
	}

	/**
	 * Get Year Built
	 *
	 * @since 2.0
	 * @param string $returntype Options i = span, d = string, l = list item
	 * @return string based on $returntype Options i = span, d = string, l = list item
	 */
	public function get_property_year_built( $returntype = 'i' ) {
		if( $this->get_property_meta('property_year_built') == '' )
			return;
		$year_built['i'] = '<span title="'.apply_filters('epl_get_property_year_built_label',__('Built', 'easy-property-listings' ) ).'" class="icon year-built"><span class="icon-value">'. $this->get_property_meta('property_year_built') . '</span></span>';
		$year_built['d'] = apply_filters('epl_get_property_year_built_label',__('Built', 'easy-property-listings' ) ) . ' ' . $this->get_property_meta('property_year_built') . ' ';
		$year_built['l'] = '<li class="year-built">'.apply_filters('epl_get_property_year_built_label',__('Built', 'easy-property-listings' ) ) . ' ' . $this->get_property_meta('property_year_built') . '</li>';

		return apply_filters('epl_get_property_year_built',$year_built[$returntype]);
	}

	/**
	 * Get Bedrooms
	 *
	 * @since 2.0
	 * @param string $returntype Options i = span, d = string, l = list item
	 * @return string based on $returntype Options i = span, d = string, l = list item
	 */
	public function get_property_bed( $returntype = 'i' ) {
		if( $this->get_property_meta('property_bedrooms' , false ) == '' )
			return;
		$bed['i'] = '<span title="'.apply_filters('epl_get_property_bedrooms_label',__('Bedrooms', 'easy-property-listings' ) ).'" class="icon beds"><span class="icon-value">'. $this->get_property_meta('property_bedrooms') . '</span></span>';
		$bed['d'] = $this->get_property_meta('property_bedrooms') . ' '.apply_filters('epl_get_property_bed_label',__('bed', 'easy-property-listings' ) ).' ';
		$bed['l'] = '<li class="bedrooms">' . $this->get_property_meta('property_bedrooms') . ' '.apply_filters('epl_get_property_bed_label',__('bed', 'easy-property-listings' ) ).'</li>';

		return apply_filters('epl_get_property_bed',$bed[$returntype]);
	}

	/**
	 * Get Bathrooms
	 *
	 * @since 2.0
	 * @param string $returntype Options i = span, d = string, l = list item
	 * @return string based on $returntype Options i = span, d = string, l = list item
	 */
	public function get_property_bath( $returntype = 'i' ) {
		if( $this->get_property_meta('property_bathrooms' , false ) == '' )
			return;
		$bath['i'] = '<span title="'.apply_filters('epl_get_property_bathrooms_label',__('Bathrooms', 'easy-property-listings' ) ).'" class="icon bath"><span class="icon-value">'. $this->get_property_meta('property_bathrooms') . '</span></span>';
		$bath['d'] = $this->get_property_meta('property_bathrooms') . ' '.apply_filters('epl_get_property_bath_label',__('bath', 'easy-property-listings' ) ).' ';
		$bath['l'] = '<li class="bathrooms">' . $this->get_property_meta('property_bathrooms') . ' '.apply_filters('epl_get_property_bath_label',__('bath', 'easy-property-listings' ) ).'</li>';

		return apply_filters('epl_get_property_bath',$bath[$returntype]);
	}

	/**
	 * Get Rooms
	 *
	 * @since 2.0
	 * @param string $returntype Options i = span, d = string, l = list item
	 * @return string based on $returntype Options i = span, d = string, l = list item
	 */
	public function get_property_rooms( $returntype = 'i' ) {
		if( $this->get_property_meta('property_rooms' , false ) == '' )
			return;
		$rooms['i'] = '<span title="'.apply_filters('epl_get_property_rooms_label',__('Rooms', 'easy-property-listings' ) ).'" class="icon rooms"><span class="icon-value">'. $this->get_property_meta('property_rooms') . '</span></span>';
		$rooms['d'] = $this->get_property_meta('property_rooms') . ' '.apply_filters('epl_get_property_rooms_label',__('rooms', 'easy-property-listings' ) ).' ';
		$rooms['l'] = '<li class="rooms">' . $this->get_property_meta('property_rooms') . ' ' . apply_filters('epl_get_property_rooms_label',__('rooms', 'easy-property-listings' ) ).'</li>';

		return apply_filters('epl_get_property_rooms',$rooms[$returntype]);
	}

	/**
	 * Get Parking
	 *
	 * Caluclated value based on number of garages + carports
	 *
	 * @since 2.0
	 * @param string $returntype Options i = span, d = string, l = list item
	 * @return string based on $returntype Options i = span, d = string, l = list item
	 */
	public function get_property_parking( $returntype = 'i' ) {
		if( $this->get_property_meta('property_garage') == '' && $this->get_property_meta('property_carport') == '' )
			return;
		$property_garage 	= intval($this->get_property_meta('property_garage'));
		$property_carport 	= intval($this->get_property_meta('property_carport'));
		$property_parking 	= $property_carport + $property_garage;
		if ( $property_parking == 0)
			return;
		$label = apply_filters('epl_get_parking_spaces_label',__('Parking Spaces', 'easy-property-listings' ) );
		$parking['i'] = '<span title="'.__('Parking Spaces', 'easy-property-listings' ).'" class="icon parking"><span class="icon-value">' .$property_parking. '</span></span>';
		$parking['d'] = $property_parking . ' '.$label.' ';
		$parking['l'] = '<li class="parking">' . $property_parking . ' '.$label.'</li>';

		return apply_filters('epl_get_property_parking',$parking[$returntype]);
	}

	/**
	 * Get Garage
	 *
	 * @since 2.0
	 * @param string $returntype Options i = span, d = string, l = list item
	 * @return string based on $returntype Options i = span, d = string, l = list item
	 */
	public function get_property_garage( $returntype = 'i' ) {
		if($this->get_property_meta('property_garage') == '')
			return;

		$label = apply_filters('epl_get_property_garage_label',__('garage', 'easy-property-listings' ) );
		$garage['i'] = '<span title="'.$label.'" class="icon parking"><span class="icon-value">'. $this->get_property_meta('property_garage') . '</span></span>';
		$garage['l'] = '<li class="garage">' . $this->get_property_meta('property_garage') . ' '.$label.'</li>';
		$garage['d'] = $this->get_property_meta('property_garage') . ' '.$label.' ';

		return apply_filters('epl_get_property_garage',$garage[$returntype]);
	}

	/**
	 * Get Carport
	 *
	 * @since 2.0
	 * @param string $returntype Options i = span, d = string, l = list item
	 * @return string based on $returntype Options i = span, d = string, l = list item
	 */
	public function get_property_carport( $returntype = 'i' ) {
		if($this->get_property_meta('property_carport') == '')
			return;
		$label = apply_filters('epl_get_property_carport_label',__('carport', 'easy-property-listings' ) );
		$carport['i'] = '<span title="'.$label.'" class="icon parking"><span class="icon-value">'. $this->get_property_meta('property_carport') . '</span></span>';
		$carport['l'] = '<li class="carport">' . $this->get_property_meta('property_carport') . ' '.$label.'</li>';
		$carport['d'] = $this->get_property_meta('property_carport') . ' '.$label.' ';

		return apply_filters('epl_get_property_carport',$carport[$returntype]);
	}

	/**
	 * Get Air Conditioning
	 *
	 * @since 2.0
	 * @param string $returntype Options i = span, d = string, l = list item
	 * @return string based on $returntype Options i = span, d = string, l = list item
	 */
	public function get_property_air_conditioning( $returntype = 'i' ) {
		if($this->get_property_meta('property_air_conditioning') == '')
			return;

		$label = apply_filters('epl_get_property_air_conditioning_label',__('Air Conditioning', 'easy-property-listings' ) );
		$property_air_conditioning = $this->get_property_meta('property_air_conditioning');
		if( isset($property_air_conditioning) && ($property_air_conditioning == 1 || $property_air_conditioning == 'yes') ) {
			$air['i'] = '<span title="'.$label.'" class="icon air"></span>';
			$air['l'] = '<li class="air">'.$label.'</li>';

			return apply_filters('epl_get_property_air_conditioning',$air[$returntype]);
		}
	}

	/**
	 * Get Pool
	 *
	 * @since 2.0
	 * @param string $returntype Options i = span, d = string, l = list item
	 * @return string based on $returntype Options i = span, d = string, l = list item
	 */
	public function get_property_pool( $returntype = 'i' ) {
		if($this->get_property_meta('property_pool') == '')
			return;
		$label = apply_filters('epl_get_property_pool_label',__('Pool', 'easy-property-listings' ) );
		$property_pool = $this->get_property_meta('property_pool');
		if( isset($property_pool) && ($property_pool == 1 || $property_pool == 'yes') ) {
			$pool['i'] = '<span title="'.$label.'" class="icon pool"></span>';
			$pool['l'] = '<li class="pool">'.$label.'</li>';

			return apply_filters('epl_get_property_pool',$pool[$returntype]);
		}
	}

	/**
	 * Get Security System
	 *
	 * @since 2.0
	 * @param string $returntype Options i = span, d = string, l = list item
	 * @return string based on $returntype Options i = span, d = string, l = list item
	 */
	public function get_property_security_system( $returntype = 'i' ) {
		if($this->get_property_meta('property_security_system') == '')
			return;
		$label = apply_filters('epl_get_property_security_system_label',__('Alarm System', 'easy-property-listings' ) );
		$property_security_system = $this->get_property_meta('property_security_system');
		if( isset($property_security_system) && ($property_security_system == 1 || $property_security_system == 'yes') ) {
			$security_system['i'] = '<span title="'.$label.'" class="icon alarm"></span>';
			$security_system['l'] = '<li class="alarm">'.$label.'</li>';

			return apply_filters('epl_get_property_security_system',$security_system[$returntype]);
		}
	}

	/**
	 * Get Land Value
	 *
	 * @since 2.0
	 * @param string $returntype Options i = span, d = string, l = list item
	 * @return string based on $returntype Options i = span, d = string, l = list item
	 */
	public function get_property_land_value( $returntype = 'i' ) {
		$property_land_area_unit = $this->get_property_meta('property_land_area_unit');
		if ( $property_land_area_unit == 'squareMeter' ) {
			$property_land_area_unit = __('sqm' , 'easy-property-listings' );
		} else {
			// translation for land area unit
			$property_land_area_unit = __($property_land_area_unit , 'easy-property-listings' );
		}
		if(is_numeric($this->get_property_meta('property_land_area')) ) {
			$label = apply_filters('epl_get_property_land_area_label',__('Land is', 'easy-property-listings' ) );
			$return = '
				<li class="land-size">'. $label.' ' . $this->get_property_meta('property_land_area') .' '.$property_land_area_unit.'</li>';

			return apply_filters('epl_get_property_land_value',$return);
		}
	}

	/**
	 * Get Building area
	 *
	 * @since 2.0
	 * @param string $returntype Options i = span, d = string, l = list item
	 * @return string based on $returntype Options i = span, d = string, l = list item
	 */
	public function get_property_building_area_value( $returntype = 'i' ) {
		$building_unit = $this->get_property_meta('property_building_area_unit');
		if ( $building_unit == 'squareMeter' ) {
			$building_unit = __('m&#178;' , 'easy-property-listings' );
		} else {
			// translation for building area unit
			$building_unit = __($building_unit , 'easy-property-listings' );
		}

		if(intval($this->get_property_meta('property_building_area')) != 0 ) {
			$label = apply_filters('epl_get_property_building_area_label',__('Floor Area is', 'easy-property-listings' ) );
			$return = '
			<li class="land-size">'.$label.' ' .
                $this->get_property_meta('property_building_area') .' '.$building_unit.
			'</li>';
			return apply_filters('epl_get_property_building_area_value',$return);
		}

	}

	/**
	 * Get New Construction
	 *
	 * @since 2.0
	 * @param string $returntype Options i = span, d = string, l = list item
	 * @return string based on $returntype Options i = span, d = string, l = list item
	 */
	public function get_property_new_construction( $returntype = 'i' ) {
		$property_new_construction = $this->get_property_meta('property_new_construction');
		if( isset($property_new_construction) && ($property_new_construction == 1 || $property_new_construction == 'yes') ) {
			$label = apply_filters('epl_get_property_new_construction_label',__('New Construction', 'easy-property-listings' ) );
			$return_construction = array();
			$return_construction['i'] = '<span title="'.$label.'" class="icon new_construction"></span>';
			$return_construction['l'] = '<li class="new_construction">'.$label.'</li>';

			return apply_filters('epl_get_property_new_construction',$return_construction[$returntype]);
		}
	}

	/**
	 * Get Additional Features by meta key
	 *
	 * @since 2.0
	 * @param string $metakey
	 * @return mixed Value wrapped in a list item
	 */
	public function get_additional_features_html( $metakey ) {

            $metavalue = $this->get_property_meta($metakey);
			$return = '';
			if( $metavalue != '' || intval($metavalue) != 0) {
				if($metakey == 'property_com_car_spaces'){
					$metavalue = $metavalue.apply_filters('epl_get_property_com_car_spaces_label','' );
				}

				if($metakey == 'property_category'){
					$metavalue = $this->get_property_category();
				}

                switch($metavalue) {

                    case 1:
                    case 'yes':
	                case 'on':
                        $return = '<li class="'.$this->get_class_from_metakey($metakey).'">'.apply_filters('epl_get_'.$metakey.'_label',__($this->get_label_from_metakey($metakey), 'easy-property-listings' ) ).'</li>';
                    break;
					case 0:
					case 'no':
	                case 'off':
						$return = '';
					break;
                    default:
                        $return = '<li class="'.$this->get_class_from_metakey($metakey).'">'.__($metavalue,'easy-property-listings' ).' '.apply_filters('epl_get_'.$metakey.'_label',__($this->get_label_from_metakey($metakey), 'easy-property-listings' ) ).'</li>';
                    break;
                }
            }

		return apply_filters('epl_get_additional_features_html',$return);
	}

	/**
	 * Get Additional Rural Features by meta key
	 *
	 * @since 2.0
	 * @param string $metakey
	 * @return mixed Value formatted and wrapped in div with title
	 */
	public function get_additional_rural_features_html( $metakey ) {
			$metavalue = $this->get_property_meta($metakey);
			if( isset($metavalue) && $metavalue != '' ) {
				$return = '<div class="'.$this->get_class_from_metakey($metakey,$search= 'property_rural_').'">
							<h6>'.__($this->get_label_from_metakey($metakey,'property_rural_'), 'easy-property-listings' ).'</h6>'.
							'<p>'.__($metavalue,'easy-property-listings' ).'</p>'.
						'</div>';
				return apply_filters('epl_get_additional_rural_features_html',$return);
			}
	}

	/**
	 * Get Additional Commercial Features by meta key
	 *
	 * @since 2.0
	 * @param string $metakey
	 * @return mixed Value formatted and wrapped in div with title
	 */
	public function get_additional_commerical_features_html( $metakey ) {
			$metavalue = $this->get_property_meta($metakey);
			if( isset($metavalue) && $metavalue != '' ) {
				$return = '<div class="'.$this->get_class_from_metakey($metakey,$search= 'property_com_').'">
							<h6>'.__($this->get_label_from_metakey($metakey,'property_com_'), 'easy-property-listings' ).'</h6>'.
							'<p>'.__($metavalue,'easy-property-listings' ).'</p>'.
						'</div>';
				return apply_filters('epl_get_additional_commerical_features_html',$return);
			}
	}

	/**
	 * Get class name from meta key
	 *
	 * @since 2.0
	 * @param string $key Meta key
	 * @param string $search Meta key prefix to search for and remove from class, Default property_
	 * @return string Formatted class name
	 */
	public function get_class_from_metakey( $key , $search = 'property_' ){
		 return str_replace("property_", "", $key);

	}

	/**
	 * Get Label from meta key
	 *
	 * @since 2.0
	 * @param string $key Meta key
	 * @param string $search Meta key prefix to search for and remove from class, Default property_
	 * @return string Formatted uppercase words
	 */
	public function get_label_from_metakey( $key , $search = 'property_' ){
		 return ucwords(str_replace('_',' ',str_replace($search, "", $key)));
	}

	/**
	 * Get features from taxonomy
	 *
	 * @since 2.0
	 * @return mixed Formatted list items
	 */
	public function get_features_from_taxonomy() {
		$property_feature_taxonomy = '';
		if ( taxonomy_exists('tax_feature') ) {
			global $post;
			$property_feature_taxonomy = epl_get_the_term_list($this->post->ID, 'tax_feature', '<li>', '</li><li>', '</li>' );
		}
		return apply_filters('epl_get_features_from_taxonomy',$property_feature_taxonomy);
	}
}
