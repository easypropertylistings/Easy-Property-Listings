<?php
/**
 * Register custom meta fields for property post types
 *
 * @package     EPL
 * @subpackage  Meta
 * @copyright   Copyright (c) 2014, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
 
/**
 * Variables List required for meta boxes
 *
 * @since 1.0
 */

add_action('init', 'epl_meta_box_init'); 
function epl_meta_box_init() {
	global $epl_settings;
	$opts_users = array();
	$users = get_users('orderby=display_name&order=ASC');
	if(!empty($users)) {
		foreach ($users as $user) {
			$opts_users[ $user->ID ] = $user->display_name;
		}
	}

	$opts_property_status = apply_filters (  'epl_opts_property_status_filter', array(
			'current'	=>	__('Current', 'epl'),
			'withdrawn'	=>	__('Withdrawn', 'epl'),
			'offmarket'	=>	__('Off Market', 'epl'),
			'sold'		=>	array(
				'label'		=>	apply_filters( 'epl_sold_label_status_filter' , __('Sold', 'epl') ),
				'exclude'	=>	array('rental')
			),
			'leased'		=>	array(
				'label'		=>	apply_filters( 'epl_leased_label_status_filter' , __('Leased', 'epl') ),
				'include'	=>	array('rental', 'commercial', 'commercial_land', 'business')
			)
		)
	);
	$opts_property_authority = apply_filters (  'epl_property_authority_filter', array(
			'exclusive'	=>	__('Exclusive', 'epl'),
			'auction'	=>	__('Auction', 'epl'),
			'multilist'	=>	__('Multilist', 'epl'),
			'conjunctional'	=>	__('Conjunctional', 'epl'),
			'open'		=>	__('Open', 'epl'),
			'sale'		=>	__('Sale', 'epl'),
			'setsale'	=>	__('Set Sale', 'epl')
		)
	);
	$opts_property_exclusivity = apply_filters (  'epl_opts_property_exclusivity_filter', array(
			'exclusive'	=>	__('Exclusive', 'epl'),
			'open'		=>	__('Open', 'epl')
		)
	);
	$opts_property_com_authority = apply_filters (  'epl_opts_property_com_authority_filter', array(
			'auction'	=>	__('Auction', 'epl'),
			'tender'	=>	__('Tender', 'epl'),
			'eoi'		=>	__('EOI', 'epl'),
			'Sale'		=>	__('Sale', 'epl'),
			'Forsale'	=>	__('For Sale', 'epl'),
			'offers'	=>	__('Offers', 'epl')
		)
	);
	$opts_area_unit = apply_filters (  'epl_opts_area_unit_filter', array(
			'square'	=>	__('Square', 'epl'),
			'squareMeter'	=>	__('Square Meter', 'epl'),
			'acre'		=>	__('Acre', 'epl'),
			'hectare'	=>	__('Hectare', 'epl'),
			'sqft'		=>	__('Square Feet', 'epl')
		)
	);
	$opts_rent_period = apply_filters (  'epl_opts_rent_period_filter', array(
			'day'		=>	__('Day', 'epl'),
			'daily'		=>	__('Daily', 'epl'),
			'week'		=>	__('Week', 'epl'),
			'weekly'	=>	__('Weekly', 'epl'),
			'month'		=>	__('Month', 'epl'),
			'monthly'	=>	__('Monthly', 'epl')
		)
	);
	$opts_property_com_listing_type = apply_filters (  'epl_opts_property_com_listing_type_filter', array(
			'sale'		=>	__('Sale', 'epl'),
			'lease'		=>	__('Lease', 'epl'),
			'both'		=>	__('Both', 'epl')
		)
	);
	$opts_property_com_tenancy = apply_filters (  'epl_opts_property_com_tenancy_filter', array(
			'unknown'	=>	__('Unknown', 'epl'),
			'vacant'	=>	__('Vacant', 'epl'),
			'tenanted'	=>	__('Tenanted', 'epl')
		)
	);
	$opts_property_com_property_extent = apply_filters (  'epl_opts_property_com_property_extent_filter', array(
			'whole'		=>	__('Whole', 'epl'),
			'part'		=>	__('Part', 'epl')
		)
	);

	global $epl_meta_boxes;
	$epl_meta_boxes = array(
		
		array(
			'id'		=>	'epl-property-listing-section-id',
			'label'		=>	__('Listing Details', 'epl'),
			'post_type'	=>	array('property', 'rural', 'rental', 'land', 'commercial', 'commercial_land', 'business'),
			'context'	=>	'normal',
			'priority'	=>	'default',
			'groups'	=>	array(
				array(
					'id'		=>	'property_heading',
					'columns'	=>	'1',
					'label'		=>	'',
					'fields'	=>	array(
						array(
							'name'		=>	'property_heading',
							'label'		=>	__('Heading', 'epl'),
							'type'		=>	'text',
							'maxlength'	=>	'200'
						)
					)
				),
			
				array(
					'id'		=>	'listing_agents',
					'columns'	=>	'1',
					'label'		=>	__('Listing Agent(s)', 'epl'),
					'fields'	=>	array(
						array(
							'name'		=>	'property_office_id',
							'label'		=>	__('Office ID', 'epl'),
							'type'		=>	'text',
							'maxlength'	=>	'50'
						),
						
						array(
							'name'		=>	'property_agent',
							'label'		=>	__('Listing Agent', 'epl'),
							'type'		=>	'text',
							'maxlength'	=>	'40'
						),
					
						array(
							'name'		=>	'property_second_agent',
							'label'		=>	__('Second Listing Agent', 'epl'),
							'type'		=>	'text',
							'maxlength'	=>	'40'
						),

						array(
							'name'		=>	'property_agent_hide_author_box',
							'label'		=>	__('', 'epl'),
							'type'		=>	'checkbox_single',
							'opts'		=>	array(
								'yes'	=>	__('Hide Author Box', 'epl'),
							)
						)
					)
				),
			
				array(
					'id'		=>	'listing_type',
					'columns'	=>	'2',
					'label'		=>	__('Listing Type', 'epl'),
					'fields'	=>	array(
						array(
							'name'		=>	'property_status',
							'label'		=>	__('Property Status', 'epl'),
							'type'		=>	'select',
							'opts'		=>	$opts_property_status
						),
						
						array(
							'name'		=>	'property_list_date',
							'label'		=>	__('Date Listed', 'epl'),
							'type'		=>	'date',
							'maxlength'	=>	'100'
						),
					
						array(
							'name'		=>	'property_authority',
							'label'		=>	__('Authority', 'epl'),
							'type'		=>	'select',
							'opts'		=>	$opts_property_authority,
							'exclude'	=>	array('rental', 'commercial', 'commercial_land')
						),
					
						array(
							'name'		=>	'property_category',
							'label'		=>	__('House Category', 'epl'),
							'type'		=>	'select',
							'opts'		=>	epl_listing_load_meta_property_category(),
							'exclude'	=>	array('land', 'commercial', 'commercial_land', 'business', 'rural')
						),
					
						array(
							'name'		=>	'property_rural_category',
							'label'		=>	__('Rural Category', 'epl'),
							'type'		=>	'select',
							'opts'		=>	epl_listing_load_meta_rural_category(),
							'include'	=>	array('rural')
						),
						
						array(
							'name'		=>	'property_unique_id',
							'label'		=>	__('Unique ID', 'epl'),
							'type'		=>	'text',
							'maxlength'	=>	'50'
						),
					
						array(
							'name'		=>	'property_mod_date',
							'label'		=>	__('XML Importer Mod Date', 'epl'),
							'type'		=>	'text',
							'maxlength'	=>	'60'
						),
					
						array(
							'name'		=>	'property_com_authority',
							'label'		=>	__('Commercial Authority', 'epl'),
							'type'		=>	'select',
							'opts'		=>	$opts_property_com_authority,
							'include'	=>	array('commercial', 'commercial_land', 'business')
						),
					
						array(
							'name'		=>	'property_com_exclusivity',
							'label'		=>	__('Exclusivity', 'epl'),
							'type'		=>	'select',
							'opts'		=>	$opts_property_exclusivity,
							'include'	=>	array('commercial', 'commercial_land', 'business')
						),
					
						array(
							'name'		=>	'property_com_listing_type',
							'label'		=>	__('Commercial Listing Type', 'epl'),
							'type'		=>	'select',
							'opts'		=>	$opts_property_com_listing_type,
							'include'	=>	array('commercial', 'commercial_land' , 'business' )
						),
					
						array(
							'name'		=>	'property_commercial_category',
							'label'		=>	__('Commercial Category', 'epl'),
							'type'		=>	'select',
							'opts'		=>	epl_listing_load_meta_commercial_category(),
							'include'	=>	array('commercial', 'commercial_land')
						),
					)
				),
			
				array(
					'id'		=>	'display_details',
					'columns'	=>	'2',
					'label'		=>	__('Display Details', 'epl'),
					'fields'	=>	array(
						array(
							'name'		=>	'property_featured',
							'label'		=>	__('Featured', 'epl'),
							'type'		=>	'radio',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'epl'),
								'no'	=>	__('No', 'epl')
							),
						),
					
						array(
							'name'		=>	'property_inspection_times',
							'label'		=>	__('Inspection Times ( one per line )', 'epl'),
							'type'		=>	'textarea',
							'maxlength'	=>	'500'
						)
					)
				)
			)
		),
	
		array(
			'id'		=>	'epl-features-section-id',
			'label'		=>	__('Listing Features', 'epl'),
			'post_type'	=>	array('property', 'rural', 'rental' ),
			'context'	=>	'normal',
			'priority'	=>	'default',
			'groups'	=>	array(
				array(
					'id'		=>	'house_features',
					'columns'	=>	'2',
					'label'		=>	__('House Features', 'epl'),
					'fields'	=>	array(
						array(
							'name'		=>	'property_bedrooms',
							'label'		=>	__('Bedrooms', 'epl'),
							'type'		=>	'number',
							'maxlength'	=>	'2'
						),
						
						array(
							'name'		=>	'property_bathrooms',
							'label'		=>	__('Bathrooms', 'epl'),
							'type'		=>	'decimal',
							'maxlength'	=>	'4'
						),
						
						array(
							'name'		=>	'property_rooms',
							'label'		=>	__('Rooms', 'epl'),
							'type'		=>	'number',
							'maxlength'	=>	'3'
						),
						
						array(
							'name'		=>	'property_ensuite',
							'label'		=>	__('Ensuite', 'epl'),
							'type'		=>	'number',
							'maxlength'	=>	'2'
						),
						
						array(
							'name'		=>	'property_toilet',
							'label'		=>	__('Toilet', 'epl'),
							'type'		=>	'number',
							'maxlength'	=>	'2'
						),
					
						array(
							'name'		=>	'property_garage',
							'label'		=>	__('Garage', 'epl'),
							'type'		=>	'number',
							'maxlength'	=>	'2'
						),
					
						array(
							'name'		=>	'property_carport',
							'label'		=>	__('Carport', 'epl'),
							'type'		=>	'number',
							'maxlength'	=>	'2'
						),
						
						array(
							'name'		=>	'property_year_built',
							'label'		=>	__('Year Built', 'epl'),
							'type'		=>	'text',
							'maxlength'	=>	'4'
						),
					
						array(
							'name'		=>	'property_new_construction',
							'label'		=>	__('New Construction', 'epl'),
							'type'		=>	'radio',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'epl'),
								'no'	=>	__('No', 'epl')
							),
							'include'	=>	array('property', 'rental' )
						),
					
						array(
							'name'		=>	'property_pool',
							'label'		=>	__('Pool', 'epl'),
							'type'		=>	'radio',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'epl'),
								'no'	=>	__('No', 'epl')
							),
						),
					
						array(
							'name'		=>	'property_air_conditioning',
							'label'		=>	__('Air Conditioning', 'epl'),
							'type'		=>	'radio',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'epl'),
								'no'	=>	__('No', 'epl')
							),
						),
					
						array(
							'name'		=>	'property_security_system',
							'label'		=>	__('Security System', 'epl'),
							'type'		=>	'radio',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'epl'),
								'no'	=>	__('No', 'epl')
							),
						)
					)
				),
			
				array(
					'id'		=>	'land_details',
					'columns'	=>	'2',
					'label'		=>	__('Land Details', 'epl'),
					'fields'	=>	array(
						array(
							'name'		=>	'property_land_area',
							'label'		=>	__('Land Area', 'epl'),
							'type'		=>	'decimal',
							'maxlength'	=>	'50'
						),
					
						array(
							'name'		=>	'property_land_area_unit',
							'label'		=>	__('Land Unit', 'epl'),
							'type'		=>	'select',
							'opts'		=>	$opts_area_unit
						),
					
						array(
							'name'		=>	'property_building_area',
							'label'		=>	__('Building Area', 'epl'),
							'type'		=>	'number',
							'maxlength'	=>	'50'
						),
					
						array(
							'name'		=>	'property_building_area_unit',
							'label'		=>	__('Building Unit', 'epl'),
							'type'		=>	'select',
							'opts'		=>	$opts_area_unit
						),
					
						array(
							'name'		=>	'property_land_fully_fenced',
							'label'		=>	__('Fully Fenced', 'epl'),
							'type'		=>	'radio',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'epl'),
								'no'	=>	__('No', 'epl')
							),
						)
					)
				)
			)
		),
	
		array( // Additional Features
			'id'		=>	'epl-additional-features-section-id',
			'label'		=>	__('Additional Features', 'epl'),
			'post_type'	=>	array('property', 'rural', 'rental' ),
			'context'	=>	'normal',
			'priority'	=>	'default',
			'groups'	=>	array(
				array(
					'id'		=>	'internal',
					'columns'	=>	'2',
					'label'		=>	__('Internal', 'epl'),
					'fields'	=>	array(
						array(
							'name'		=>	'property_remote_garage',
							'label'		=>	__('Remote Garage', 'epl'),
							'type'		=>	'radio',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'epl'),
								'no'	=>	__('No', 'epl')
							),
						),
					
						array(
							'name'		=>	'property_secure_parking',
							'label'		=>	__('Secure Parking', 'epl'),
							'type'		=>	'radio',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'epl'),
								'no'	=>	__('No', 'epl')
							),
						),
					
						array(
							'name'		=>	'property_study',
							'label'		=>	__('Study', 'epl'),
							'type'		=>	'radio',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'epl'),
								'no'	=>	__('No', 'epl')
							),
						),
					
						array(
							'name'		=>	'property_dishwasher',
							'label'		=>	__('Dishwasher', 'epl'),
							'type'		=>	'radio',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'epl'),
								'no'	=>	__('No', 'epl')
							),
						),
					
						array(
							'name'		=>	'property_built_in_robes',
							'label'		=>	__('Built In Robes', 'epl'),
							'type'		=>	'radio',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'epl'),
								'no'	=>	__('No', 'epl')
							),
						),
					
						array(
							'name'		=>	'property_gym',
							'label'		=>	__('Gym', 'epl'),
							'type'		=>	'radio',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'epl'),
								'no'	=>	__('No', 'epl')
							),
						),
					
						array(
							'name'		=>	'property_workshop',
							'label'		=>	__('Workshop', 'epl'),
							'type'		=>	'radio',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'epl'),
								'no'	=>	__('No', 'epl')
							),
						),
					
						array(
							'name'		=>	'property_rumpus_room',
							'label'		=>	__('Rumpus Room', 'epl'),
							'type'		=>	'radio',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'epl'),
								'no'	=>	__('No', 'epl')
							),
						),
					
						array(
							'name'		=>	'property_floor_boards',
							'label'		=>	__('Floor Boards', 'epl'),
							'type'		=>	'radio',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'epl'),
								'no'	=>	__('No', 'epl')
							),
						),
					
						array(
							'name'		=>	'property_broadband',
							'label'		=>	__('Broadband', 'epl'),
							'type'		=>	'radio',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'epl'),
								'no'	=>	__('No', 'epl')
							),
						),
					
						array(
							'name'		=>	'property_pay_tv',
							'label'		=>	__('Pay TV', 'epl'),
							'type'		=>	'radio',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'epl'),
								'no'	=>	__('No', 'epl')
							),
						),
						array(
							'name'		=>	'property_vacuum_system',
							'label'		=>	__('Vacuum System', 'epl'),
							'type'		=>	'radio',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'epl'),
								'no'	=>	__('No', 'epl')
							),
						),
					
						array(
							'name'		=>	'property_intercom',
							'label'		=>	__('Intercom', 'epl'),
							'type'		=>	'radio',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'epl'),
								'no'	=>	__('No', 'epl')
							),
						),
					
						array(
							'name'		=>	'property_spa',
							'label'		=>	__('Spa', 'epl'),
							'type'		=>	'radio',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'epl'),
								'no'	=>	__('No', 'epl')
							),
						)
					)
				),
			
				array(
					'id'		=>	'external',
					'columns'	=>	'2',
					'label'		=>	__('External', 'epl'),
					'fields'	=>	array(
					
						array(
							'name'		=>	'property_tennis_court',
							'label'		=>	__('Tennis Court', 'epl'),
							'type'		=>	'radio',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'epl'),
								'no'	=>	__('No', 'epl')
							),
						),
					
						array(
							'name'		=>	'property_balcony',
							'label'		=>	__('Balcony', 'epl'),
							'type'		=>	'radio',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'epl'),
								'no'	=>	__('No', 'epl')
							),
						),
					
						array(
							'name'		=>	'property_deck',
							'label'		=>	__('Deck', 'epl'),
							'type'		=>	'radio',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'epl'),
								'no'	=>	__('No', 'epl')
							),
						),
					
						array(
							'name'		=>	'property_courtyard',
							'label'		=>	__('Courtyard', 'epl'),
							'type'		=>	'radio',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'epl'),
								'no'	=>	__('No', 'epl')
							),
						),
					
						array(
							'name'		=>	'property_outdoor_entertaining',
							'label'		=>	__('Outdoor Entertaining', 'epl'),
							'type'		=>	'radio',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'epl'),
								'no'	=>	__('No', 'epl')
							),
						),
					
						array(
							'name'		=>	'property_shed',
							'label'		=>	__('Shed', 'epl'),
							'type'		=>	'radio',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'epl'),
								'no'	=>	__('No', 'epl')
							),
						)
					)
				),
				
				array(
					'id'		=>	'heating_cooling',
					'columns'	=>	'2',
					'label'		=>	__('Heating & Cooling', 'epl'),
					'fields'	=>	array(
					
						array(
							'name'		=>	'property_ducted_heating',
							'label'		=>	__('Ducted Heating', 'epl'),
							'type'		=>	'radio',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'epl'),
								'no'	=>	__('No', 'epl')
							),
						),
					
						array(
							'name'		=>	'property_ducted_cooling',
							'label'		=>	__('Ducted Cooling', 'epl'),
							'type'		=>	'radio',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'epl'),
								'no'	=>	__('No', 'epl')
							),
						),
					
						array(
							'name'		=>	'property_split_system_heating',
							'label'		=>	__('Split System Heating', 'epl'),
							'type'		=>	'radio',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'epl'),
								'no'	=>	__('No', 'epl')
							),
						),
					
						array(
							'name'		=>	'property_hydronic_heating',
							'label'		=>	__('Hydronic Heating', 'epl'),
							'type'		=>	'radio',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'epl'),
								'no'	=>	__('No', 'epl')
							),
						),
					
						array(
							'name'		=>	'property_split_system_aircon',
							'label'		=>	__('Split System Aircon', 'epl'),
							'type'		=>	'radio',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'epl'),
								'no'	=>	__('No', 'epl')
							),
						),
					
						array(
							'name'		=>	'property_gas_heating',
							'label'		=>	__('Gas Heating', 'epl'),
							'type'		=>	'radio',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'epl'),
								'no'	=>	__('No', 'epl')
							),
						),
					
						array(
							'name'		=>	'property_reverse_cycle_aircon',
							'label'		=>	__('Reverse Cycle Aircon', 'epl'),
							'type'		=>	'radio',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'epl'),
								'no'	=>	__('No', 'epl')
							),
						),
					
						array(
							'name'		=>	'property_evaporative_cooling',
							'label'		=>	__('Evaporative Cooling', 'epl'),
							'type'		=>	'radio',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'epl'),
								'no'	=>	__('No', 'epl')
							),
						),
					
						array(
							'name'		=>	'property_open_fire_place',
							'label'		=>	__('Open Fire Place', 'epl'),
							'type'		=>	'radio',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'epl'),
								'no'	=>	__('No', 'epl')
							),
						)
					)
				)
			)
		),
	
		array( //Repeating most from above "epl-features-section-id" because on land it will be single column
			'id'		=>	'epl-features-section-id-single-column',
			'label'		=>	__('Land Details', 'epl'),
			'post_type'	=>	array('land', 'commercial', 'business'),
			'context'	=>	'normal',
			'priority'	=>	'default',
			'groups'	=>	array(
				array(
					'id'		=>	'land_details',
					'columns'	=>	'1',
					'label'		=>	'',
					'fields'	=>	array(
						array(
							'name'		=>	'property_land_area',
							'label'		=>	__('Land Area', 'epl'),
							'type'		=>	'decimal',
							'maxlength'	=>	'50'
						),
					
						array(
							'name'		=>	'property_land_area_unit',
							'label'		=>	__('Land Unit', 'epl'),
							'type'		=>	'select',
							'opts'		=>	$opts_area_unit
						),
					
						array(
							'name'		=>	'property_building_area',
							'label'		=>	__('Building Area', 'epl'),
							'type'		=>	'number',
							'include'	=>	array('commercial','business'),
							'maxlength'	=>	'40'
						),
					
						array(
							'name'		=>	'property_building_area_unit',
							'label'		=>	__('Building Unit', 'epl'),
							'type'		=>	'select',
							'opts'		=>	$opts_area_unit,
							'include'	=>	array('commercial','business')
						),
					
						array(
							'name'		=>	'property_land_category',
							'label'		=>	__('Land Category', 'epl'),
							'type'		=>	'select',
							'opts'		=>	epl_listing_load_meta_land_category(),
							'include'	=>	array('land')
						),
					
						array(
							'name'		=>	'property_land_fully_fenced',
							'label'		=>	__('Fully Fenced', 'epl'),
							'type'		=>	'radio',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'epl'),
								'no'	=>	__('No', 'epl')
							),
							'include'	=>	array('land')
						)
					)
				)
			)
		),
	
		array(
			'id'		=>	'epl-property-address-section-id',
			'label'		=>	__('Property Address', 'epl'),
			'post_type'	=>	array('property', 'rural', 'rental', 'commercial', 'commercial_land', 'business', 'land'),
			'context'	=>	'side',
			'priority'	=>	'core',
			'groups'	=>	array(	apply_filters('epl_listing_meta_address_block',
					array(
						'id'		=>	'address_block',
						'columns'	=>	'1',
						'label'		=>	'',
						'fields'	=>	array(
							array(
								'name'		=>	'property_address_display',
								'label'		=>	__('Display Address?', 'epl'),
								'type'		=>	'radio',
								'opts'		=>	array(
									'yes'	=>	__('Yes', 'epl'),
									'no'	=>	__('No', 'epl')
								),
							),
							
							array(
								'name'		=>	'property_address_lot_number',
								'label'		=>	__('Lot', 'epl'),
								'type'		=>	'text',
								'maxlength'	=>	'40',
								'include'	=>	array('land', 'commercial_land')
							),
							
							array(
								'name'		=>	'property_address_sub_number',
								'label'		=>	__('Unit', 'epl'),
								'type'		=>	'text',
								'maxlength'	=>	'40',
								'exclude'	=>	array('land', 'commercial_land')
							),
							
							array(
								'name'		=>	'property_address_street_number',
								'label'		=>	__('Street Number', 'epl'),
								'type'		=>	'text',
								'maxlength'	=>	'40'
							),
					
							array(
								'name'		=>	'property_address_street',
								'label'		=>	__('Street Name', 'epl'),
								'type'		=>	'text',
								'maxlength'	=>	'80'
							),
					
							array(
								'name'		=>	'property_address_suburb',
								'label'		=>	epl_labels('label_suburb'),
								'type'		=>	'text',
								'maxlength'	=>	'80'
							),
							( isset($epl_settings['epl_enable_city_field'] ) &&  $epl_settings['epl_enable_city_field'] == 'yes' ) ?
							array(
								'name'		=>	'property_address_city',
								'label'		=>	epl_labels('label_city'),
								'type'		=>	'text',
								'maxlength'	=>	'80'
							) : array() ,
							array(
								'name'		=>	'property_address_state',
								'label'		=>	epl_labels('label_state'),
								'type'		=>	'text',
								'maxlength'	=>	'80'
							),


							array(
								'name'		=>	'property_com_display_suburb',
								'label'		=>	__('Display Suburb', 'epl'),
								'type'		=>	'radio',
								'opts'		=>	array(
									'yes'	=>	__('Yes', 'epl'),
									'no'	=>	__('No', 'epl')
								),
								'include'	=>	array('commercial', 'commercial_land', 'business'),
							),
					
							array(
								'name'		=>	'property_address_postal_code',
								'label'		=>	epl_labels('label_postcode'),
								'type'		=>	'text',
								'maxlength'	=>	'30'
							),
							( isset($epl_settings['epl_enable_country_field'] ) &&  $epl_settings['epl_enable_country_field'] == 'yes' ) ?
							array(
								'name'		=>	'property_address_country',
								'label'		=>	__('Country', 'epl'),
								'type'		=>	'text',
								'maxlength'	=>	'40'
							) : array() ,
							
							array(
								'name'		=>	'property_address_coordinates',
								'label'		=>	__('Coordinates', 'epl'),
								'type'		=>	'text',
								'geocoder'	=>	'true',
								'maxlength'	=>	'40'
							),
							array(
								'name'		=>	'property_address_hide_map',
								'label'		=>	__('', 'epl'),
								'type'		=>	'checkbox_single',
								'opts'		=>	array(
									'yes'	=>	__('Hide Map', 'epl'),
								)
							)

						)
					)
				)
			)
		),
	
		array(
			'id'		=>	'epl-pricing-section-id',
			'label'		=>	__('Pricing', 'epl'),
			'post_type'	=>	array('property', 'rural', 'commercial', 'commercial_land', 'business', 'land'),
			'context'	=>	'side',
			'priority'	=>	'core',
			'groups'	=>	array(
				array(
					'id'		=>	'pricing',
					'columns'	=>	'1',
					'label'		=>	'',
					'fields'	=>	array(
						array(
							'name'		=>	'property_price',
							'label'		=>	__('Search Price', 'epl'),
							'type'		=>	'number',
							'maxlength'	=>	'50'
						),
					
						array(
							'name'		=>	'property_price_view',
							'label'		=>	__('Price Text', 'epl'),
							'type'		=>	'text',
							'maxlength'	=>	'50'
						),
					
						array(
							'name'		=>	'property_auction',
							'label'		=>	__('Auction Date', 'epl'),
							'type'		=>	'auction-date',
							'maxlength'	=>	'100'
						),
					
						array(
							'name'		=>	'property_price_display',
							'label'		=>	__('Display Price?', 'epl'),
							'type'		=>	'radio',
							'opts'		=>	array(
								'yes'	=>	__('Yes', 'epl'),
								'no'	=>	__('No', 'epl')
							),
						),
					
						array(
							'name'		=>	'property_under_offer',
							'label'		=>	epl_meta_under_offer_label(),
							'type'		=>	'radio',
							'opts'		=>	array(
								'yes'	=>	__('Yes', 'epl'),
								'no'	=>	__('No', 'epl')
							),
						),
					
						array(
							'name'		=>	'property_is_home_land_package',
							'label'		=>	__('House and Land Package', 'epl'),
							'type'		=>	'radio',
							'opts'		=>	array(
								'yes'	=>	__('Yes', 'epl'),
								'no'	=>	__('No', 'epl')
							),
							'exclude'	=>	array('land', 'rural', 'commercial', 'commercial_land' , 'business')
						)
					)
				),
			
				array(
					'id'		=>	'sale_details',
					'columns'	=>	'1',
					'label'		=>	__('Sale Details', 'epl'),
					'fields'	=>	array(
						array(
							'name'		=>	'property_sold_price',
							'label'		=>	__('Sale Price', 'epl'),
							'type'		=>	'number',
							'maxlength'	=>	'50'
						),
					
						array(
							'name'		=>	'property_sold_date',
							'label'		=>	__('Sale Date', 'epl'),
							'type'		=>	'sold-date',
							'maxlength'	=>	'100'
						),
					
						array(
							'name'		=>	'property_sold_price_display',
							'label'		=>	__('Display Sale Price', 'epl'),
							'type'		=>	'radio',
							'opts'		=>	array(
								'yes'	=>	__('Yes', 'epl'),
								'no'	=>	__('No', 'epl')
							),
						)
					)
				)
			)
		),	
	
		array(
			'id'		=>	'epl-property-rent-id',
			'label'		=>	__('Rental Pricing', 'epl'),
			'post_type'	=>	array('rental'),
			'context'	=>	'side',
			'priority'	=>	'core',
			'groups'	=>	array(
				array(
					'id'		=>	'rental_pricing',
					'columns'	=>	'1',
					'label'		=>	'',
					'fields'	=>	array(
						array(
							'name'		=>	'property_rent',
							'label'		=>	__('Rent Amount', 'epl'),
							'type'		=>	apply_filters('epl_price_number_format','number'),
							'maxlength'	=>	'40'
						),
					
						array(
							'name'		=>	'property_rent_period',
							'label'		=>	__('Rent Period', 'epl'),
							'type'		=>	'select',
							'opts'		=>	$opts_rent_period
						),
						
						array(
							'name'		=>	'property_rent_view',
							'label'		=>	__('Rent Text', 'epl'),
							'type'		=>	'text',
							'maxlength'	=>	'50'
						),
						
						array(
							'name'		=>	'property_rent_display',
							'label'		=>	__('Display Rent?', 'epl'),
							'type'		=>	'radio',
							'opts'		=>	array(
								'yes'	=>	__('Yes', 'epl'),
								'no'	=>	__('No', 'epl')
							),
						),
					
						array(
							'name'		=>	'property_bond',
							'label'		=>	epl_labels('label_bond'),
							'type'		=>	'number',
							'maxlength'	=>	'40'
						),
					
						array(
							'name'		=>	'property_date_available',
							'label'		=>	__('Date Available', 'epl'),
							'type'		=>	'date',
							'maxlength'	=>	'100'
						),
					
						array(
							'name'		=>	'property_furnished',
							'label'		=>	__('Furnished', 'epl'),
							'type'		=>	'radio',
							'opts'		=>	array(
								'yes'	=>	__('Yes', 'epl'),
								'no'	=>	__('No', 'epl')
							),
						),
					
						array(
							'name'		=>	'property_holiday_rental',
							'label'		=>	__('Holiday Rental', 'epl'),
							'type'		=>	'radio',
							'opts'		=>	array(
								'yes'	=>	__('Yes', 'epl'),
								'no'	=>	__('No', 'epl')
							),
							'exclude'	=>	array('rental')
						),
					)
				)
			)
		),
	
		array(
			'id'		=>	'epl-rural-features-id',
			'label'		=>	__('Rural Features', 'epl'),
			'post_type'	=>	array('rural'),
			'context'	=>	'normal',
			'priority'	=>	'default',
			'groups'	=>	array(
				array(
					'id'		=>	'rural_features',
					'columns'	=>	'1',
					'label'		=>	'',
					'fields'	=>	array(
						array(
							'name'		=>	'property_rural_fencing',
							'label'		=>	__('Fencing', 'epl'),
							'type'		=>	'textarea',
							'maxlength'	=>	'65535'
						),
					
						array(
							'name'		=>	'property_rural_annual_rainfall',
							'label'		=>	__('Annual Rainfall', 'epl'),
							'type'		=>	'textarea',
							'maxlength'	=>	'65535'
						),
					
						array(
							'name'		=>	'property_rural_soil_types',
							'label'		=>	__('Soil Types', 'epl'),
							'type'		=>	'textarea',
							'maxlength'	=>	'65535'
						),
					
						array(
							'name'		=>	'property_rural_improvements',
							'label'		=>	__('Improvements', 'epl'),
							'type'		=>	'textarea',
							'maxlength'	=>	'65535'
						),
					
						array(
							'name'		=>	'property_rural_council_rates',
							'label'		=>	__('Council Rates', 'epl'),
							'type'		=>	'textarea',
							'maxlength'	=>	'65535'
						),
					
						array(
							'name'		=>	'property_rural_irrigation',
							'label'		=>	__('Irrigation', 'epl'),
							'type'		=>	'textarea',
							'maxlength'	=>	'65535'
						),
					
						array(
							'name'		=>	'property_rural_carrying_capacity',
							'label'		=>	__('Carrying Capacity', 'epl'),
							'type'		=>	'textarea',
							'maxlength'	=>	'65535'
						),
					
						array(
							'name'		=>	'property_rural_services',
							'label'		=>	__('Services', 'epl'),
							'type'		=>	'textarea',
							'maxlength'	=>	'65535'
						)
					)
				)
			)
		),
	
		array(
			'id'		=>	'epl-commercial-leasing-id',
			'label'		=>	__('Leasing', 'epl'),
			'post_type'	=>	array('commercial', 'commercial_land' , 'business'),
			'context'	=>	'normal',
			'priority'	=>	'default',
			'groups'	=>	array(
				array(
					'id'		=>	'commercial_leasing',
					'columns'	=>	'2',
					'label'		=>	'',
					'fields'	=>	array(
						array(
							'name'		=>	'property_com_rent',
							'label'		=>	__('Commercial Rent', 'epl'),
							'type'		=>	'decimal',
							'maxlength'	=>	'40'
						),
						array(
							'name'		=>	'property_com_rent_period',
							'label'		=>	__('Lease Period', 'epl'),
							'type'		=>	'select',
							'opts'		=>	epl_listing_load_meta_commercial_rent_period()
						),
						array(
							'name'		=>	'property_com_rent_range_min',
							'label'		=>	__('Rent Range Min', 'epl'),
							'type'		=>	'number',
							'maxlength'	=>	'40'
						),
						 
						array(
							'name'		=>	'property_com_rent_range_max',
							'label'		=>	__('Rent Range Max', 'epl'),
							'type'		=>	'number',
							'maxlength'	=>	'40'
						),
					
						array(
							'name'		=>	'property_com_lease_end_date',
							'label'		=>	__('Lease End Date', 'epl'),
							'type'		=>	'date',
							'maxlength'	=>	'100'
						),
					
						array(
							'name'		=>	'property_com_property_extent',
							'label'		=>	__('Property Extent', 'epl'),
							'type'		=>	'select',
							'opts'		=>	$opts_property_com_property_extent
						)
					)
				),
			
				array(
					'id'		=>	'tenant_n_outgoings',
					'columns'	=>	'2',
					'label'		=>	'',
					'fields'	=>	array(
						array(
							'name'		=>	'property_com_tenancy',
							'label'		=>	__('Tenant Status', 'epl'),
							'type'		=>	'select',
							'opts'		=>	$opts_property_com_tenancy,
							'include'	=>	array('commercial')
						),
					
						array(
							'name'		=>	'property_com_outgoings',
							'label'		=>	__('Commercial Outgoings', 'epl'),
							'type'		=>	'number',
							'maxlength'	=>	'40',
							'exclude'	=>	array('business')
						),
					
						array(
							'name'		=>	'property_com_plus_outgoings',
							'label'		=>	__('Plus Outgoings', 'epl'),
							'type'		=>	'radio',
							'opts'		=>	array(
								'yes'	=>	__('Yes', 'epl'),
								'no'	=>	__('No', 'epl')
							),
							'exclude'	=>	array('business')
						),
						
						array(
							'name'		=>	'property_bus_takings',
							'label'		=>	__('Takings', 'epl'),
							'type'		=>	'number',
							'maxlength'	=>	'40',
							'include'	=>	array('business')
						),
						
						array(
							'name'		=>	'property_bus_franchise',
							'label'		=>	__('Franchise', 'epl'),
							'type'		=>	'radio',
							'opts'		=>	array(
								'yes'	=>	__('Yes', 'epl'),
								'no'	=>	__('No', 'epl')
							),
							'include'	=>	array('business')
						),
						
						array(
							'name'		=>	'property_com_return',
							'label'		=>	__('Return', 'epl'),
							'type'		=>	'decimal',
							'maxlength'	=>	'6'
						),
						
						array(
							'name'		=>	'property_bus_terms',
							'label'		=>	__('Terms', 'epl'),
							'type'		=>	'textarea'
						)
					)
				)
			)
		),
	
		array(
			'id'		=>	'epl-commercial-features-id',
			'label'		=>	__('Commercial Features', 'epl'),
			'post_type'	=>	array('commercial'),
			'context'	=>	'normal',
			'priority'	=>	'default',
			'groups'	=>	array(
				array(
					'id'		=>	'commerial_features',
					'columns'	=>	'1',
					'label'		=>	'',
					'fields'	=>	array(
						array(
							'name'		=>	'property_com_further_options',
							'label'		=>	__('Further Options', 'epl'),
							'type'		=>	'textarea',
							'maxlength'	=>	'65535'
						),
					
						array(
							'name'		=>	'property_com_zone',
							'label'		=>	__('Zone', 'epl'),
							'type'		=>	'textarea',
							'maxlength'	=>	'150'
						),
					
						array(
							'name'		=>	'property_com_car_spaces',
							'label'		=>	__('Car Spaces', 'epl'),
							'type'		=>	'number',
							'type'		=>	'textarea',
							'maxlength'	=>	'5'
						),
					
						array(
							'name'		=>	'property_com_highlight_1',
							'label'		=>	__('Highlight 1', 'epl'),
							'type'		=>	'text',
							'maxlength'	=>	'40'
						),
					
						array(
							'name'		=>	'property_com_highlight_2',
							'label'		=>	__('Highlight 2', 'epl'),
							'type'		=>	'text',
							'maxlength'	=>	'40'
						),
					
						array(
							'name'		=>	'property_com_highlight_3',
							'label'		=>	__('Highlight 3', 'epl'),
							'type'		=>	'text',
							'maxlength'	=>	'40'
						),
					
						array(
							'name'		=>	'property_com_parking_comments',
							'label'		=>	__('Parking Comments', 'epl'),
							'type'		=>	'text',
							'maxlength'	=>	'150'
						),
					
						array(
							'name'		=>	'property_com_is_multiple',
							'label'		=>	__('Is Multiple', 'epl'),
							'type'		=>	'radio',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'epl'),
								'no'	=>	__('No', 'epl')
							)
						)
					)
				)
			)
		),
	
		array(
			'id'		=>	'epl-business-features-id',
			'label'		=>	__('Business Categories', 'epl'),
			'post_type'	=>	array('business'),
			'context'	=>	'normal',
			'priority'	=>	'default',
			'groups'	=>	array(
				array(
					'id'		=>	'business_categories',
					'columns'	=>	'1',
					'label'		=>	'',
					'fields'	=>	array(
						array(
							'name'		=>	'property_bus_category',
							'label'		=>	__('Business Category', 'epl'),
							'type'		=>	'select',
							'opt_args'	=>	array(
								'type'	=>	'taxonomy',
								'slug'	=>	'tax_business_listing'
							)
						),
					
						array(
							'name'		=>	'property_bus_sub_category',
							'label'		=>	__('Business Sub Category', 'epl'),
							'type'		=>	'select',
							'opt_args'	=>	array(
								'type'	=>	'taxonomy',
								'slug'	=>	'tax_business_listing',
								'parent'=>	'property_bus_category'
							)
						),
					
						array(
							'name'		=>	'property_bus_category_2',
							'label'		=>	__('Business Category 2', 'epl'),
							'type'		=>	'select',
							'opt_args'	=>	array(
								'type'	=>	'taxonomy',
								'slug'	=>	'tax_business_listing'
							)
						),
					
						array(
							'name'		=>	'property_bus_sub_category_2',
							'label'		=>	__('Business Sub Category 2', 'epl'),
							'type'		=>	'select',
							'opt_args'	=>	array(
								'type'	=>	'taxonomy',
								'slug'	=>	'tax_business_listing',
								'parent'=>	'property_bus_category_2'
							)
						),
					
						array(
							'name'		=>	'property_bus_category_3',
							'label'		=>	__('Business Category 3', 'epl'),
							'type'		=>	'select',
							'opt_args'	=>	array(
								'type'	=>	'taxonomy',
								'slug'	=>	'tax_business_listing'
							)
						),
					
						array(
							'name'		=>	'property_bus_sub_category_3',
							'label'		=>	__('Business Sub Category 3', 'epl'),
							'type'		=>	'select',
							'opt_args'	=>	array(
								'type'	=>	'taxonomy',
								'slug'	=>	'tax_business_listing',
								'parent'=>	'property_bus_category_3'
							)
						),
					)
				)
			)
		),
	
		array(
			'id'		=>	'epl-attachments-section-id',
			'label'		=>	__('Files and Links', 'epl'),
			'post_type'	=>	array('property', 'rural', 'commercial', 'commercial_land', 'business', 'rental', 'land'),
			'context'	=>	'normal',
			'priority'	=>	'default',
			'groups'	=>	array(
				array(
					'id'		=>	'filen_n_links',
					'columns'	=>	'1',
					'label'		=>	'',
					'fields'	=>	array(
						array(
							'name'		=>	'property_video_url',
							'label'		=>	__('Video URL', 'epl'),
							'type'		=>	'url'
						),
					
						array(
							'name'		=>	'property_floorplan',
							'label'		=>	__('Floorplan', 'epl'),
							'type'		=>	'url'
						),
						array(
							'name'		=>	'property_floorplan_2',
							'label'		=>	__('Floorplan 2', 'epl'),
							'type'		=>	'url'
						),
					
						array(
							'name'		=>	'property_external_link',
							'label'		=>	__('External Link', 'epl'),
							'type'		=>	'url'
						),
					
						array(
							'name'		=>	'property_external_link_2',
							'label'		=>	__('External Link 2', 'epl'),
							'type'		=>	'url'
						),
						array(
							'name'		=>	'property_external_link_3',
							'label'		=>	__('External Link 3', 'epl'),
							'type'		=>	'url',
							'include'	=>	array('commercial', 'business', 'commercial_land'),
						),
					
						array(
							'name'		=>	'property_com_mini_web',
							'label'		=>	__('Mini Website URL', 'epl'),
							'type'		=>	'url',
							'include'	=>	array('commercial', 'business', 'commercial_land'),
						),
						array(
							'name'		=>	'property_com_mini_web_2',
							'label'		=>	__('Mini Website URL 2', 'epl'),
							'type'		=>	'url',
							'include'	=>	array('commercial', 'business', 'commercial_land'),
						),	
						array(
							'name'		=>	'property_com_mini_web_3',
							'label'		=>	__('Mini Website URL 3', 'epl'),
							'type'		=>	'url',
							'include'	=>	array('commercial', 'business', 'commercial_land'),
						),						
					)
				)
			)
		)
	);
	if(!empty($epl_meta_boxes)) {
		foreach($epl_meta_boxes as &$epl_meta_box) {
			$meta_box_block_id = str_replace("-","_",$epl_meta_box['id']);
			$epl_meta_box = apply_filters('epl_meta_box_block_'.$meta_box_block_id,$epl_meta_box);
			if(!empty($epl_meta_box['groups'])) {
				foreach($epl_meta_box['groups'] as &$group) {
					$group = apply_filters('epl_meta_groups_'.$group['id'], $group);
					if(!empty($group['fields'])) {
						$group['fields'] = array_filter($group['fields']);
						foreach($group['fields'] as &$fieldvalue) {
							
							$fieldvalue = apply_filters('epl_meta_'.$fieldvalue['name'], $fieldvalue);
						}
					}
					
				}
			}
		}
	}

	$epl_meta_boxes = apply_filters('epl_listing_meta_boxes', $epl_meta_boxes);

	/**
	 * Add meta boxes to the post-edit page
	 *
	 * @since 1.0
	 */
	function epl_add_meta_boxes() {
		global $epl_meta_boxes;
		if(!empty($epl_meta_boxes)) {
			foreach($epl_meta_boxes as $epl_meta_box) {
				if( is_array($epl_meta_box['post_type']) ) {
					foreach($epl_meta_box['post_type'] as $post_type) {
						add_meta_box($epl_meta_box['id'], __( $epl_meta_box['label'], 'epl' ), 'epl_meta_box_inner_custom_box', $post_type, $epl_meta_box['context'], $epl_meta_box['priority'], $epl_meta_box);
					}
				} else {
					add_meta_box($epl_meta_box['id'], __( $epl_meta_box['label'], 'epl' ), 'epl_meta_box_inner_custom_box', $epl_meta_box['post_type'], $epl_meta_box['context'], $epl_meta_box['priority'], $epl_meta_box);
				}
			}
		}
	}
	/**
	 * Add sub meta boxes to the post-edit page
	 *
	 * @since 1.0
	 */
	function epl_meta_box_inner_custom_box($post, $args) {
		$groups = $args['args']['groups'];
		$groups = array_filter($groups);
		if(!empty($groups)) {
			wp_nonce_field( 'epl_inner_custom_box', 'epl_inner_custom_box_nonce' );
			foreach($groups as $group) { ?>
				<div class="epl-inner-div col-<?php echo $group['columns']; ?> table-<?php echo $args['args']['context']; ?>">
					<?php
						$group['label'] = trim($group['label']);
						if(!empty($group['label'])) {
							echo '<h3>'.__($group['label'], 'epl').'</h3>';
						}
					?>
					<table class="form-table epl-form-table">
						<tbody>
							<?php
								$fields = $group['fields'];
								$fields = array_filter($fields);
								if(!empty($fields)) {
									foreach($fields as $field) {
										if(isset($field['exclude']) && !empty($field['exclude'])) {
											if( in_array($post->post_type, $field['exclude']) ) {
												continue;
											}
										}
									
										if(isset($field['include']) && !empty($field['include'])) {
											if( !in_array($post->post_type, $field['include']) ) {
												continue;
											}
										} ?>
										<tr class="form-field">
											<th valign="top" scope="row">
												<label for="<?php echo $field['name']; ?>"><?php _e($field['label'], 'epl'); ?></label>
											</th>
										
											<?php if($group['columns'] > 1) { ?>
												</tr><tr class="form-field">
											<?php } ?>
										
											<td>
												<?php
													$val = get_post_meta($post->ID, $field['name'], true);
													epl_render_html_fields ($field,$val);
												?>
											</td>
										</tr>
									<?php }
								}
							?>
						</tbody>
					</table>
				</div>
				<?php
			} ?>
			<input type="hidden" name="epl_meta_box_ids[]" value="<?php echo $args['id']; ?>" />
			<div class="epl-clear"></div>
			<?php
		}
	}
	add_action( 'add_meta_boxes', 'epl_add_meta_boxes' );
	/**
	 * Save and update meta box values to the post-edit page
	 *
	 * @since 1.0
	 */
	function epl_save_meta_boxes( $post_ID ) {
		if ( ! isset( $_POST['epl_inner_custom_box_nonce'] ) )
			return $post_ID;
		$nonce = $_POST['epl_inner_custom_box_nonce'];
		if ( ! wp_verify_nonce( $nonce, 'epl_inner_custom_box' ) )
			return $post_ID;
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
			return $post_ID;
		if ( 'page' == $_POST['post_type'] ) {
			if ( ! current_user_can( 'edit_page', $post_ID ) )
				return $post_ID;
		} else {
			if ( ! current_user_can( 'edit_post', $post_ID ) )
			return $post_ID;
		}
	
	
		$epl_meta_box_ids = '';
		if(isset($_POST['epl_meta_box_ids'])) {
			$epl_meta_box_ids = $_POST['epl_meta_box_ids'];
		}
	
		if(!empty($epl_meta_box_ids)) {
			global $epl_meta_boxes;
			if(!empty($epl_meta_boxes)) {
				$epl_enable_import_geocode = get_option('epl_enable_import_geocode');
				foreach($epl_meta_box_ids as $epl_meta_box_id) {
					foreach($epl_meta_boxes as $epl_meta_box) {
						if($epl_meta_box['id'] == $epl_meta_box_id) {
							if(!empty($epl_meta_box['groups'])) {
								foreach($epl_meta_box['groups'] as $group) {
								
									$fields = $group['fields'];
									if(!empty($fields)) {
										foreach($fields as $field) {
											if(isset($field['exclude']) && !empty($field['exclude'])) {
												if( in_array($_POST['post_type'], $field['exclude']) ) {
													continue;
												}
											}
									
											if(isset($field['include']) && !empty($field['include'])) {
												if( !in_array($_POST['post_type'], $field['include']) ) {
													continue;
												}
											}

											if( $field['type'] == 'radio' ) {
												if(!isset($_POST[ $field['name'] ])) {
													continue;
												}
											} else if( $field['type'] == 'checkbox_single') {
												if(!isset($_POST[ $field['name'] ])) {
													$_POST[ $field['name'] ] = '';
												}
											} else if( $field['type'] == 'auction-date' && $_POST[ $field['name'] ] != '') {
												$epl_date = $_POST[ $field['name'] ];
												if(strpos($epl_date, 'T') !== FALSE){
													$epl_date = date("Y-m-d\TH:i",strtotime($epl_date));
												} else {
													$epl_date = DateTime::createFromFormat('Y-m-d-H:i:s', $epl_date);
													
													if($epl_date)
														$epl_date = $epl_date->format('Y-m-d\TH:i');
												}
												$_POST[ $field['name'] ] = $epl_date;
											} else if( $field['type'] == 'sold-date' && $_POST[ $field['name'] ] != '') {
												$epl_date = $_POST[ $field['name'] ];
												if(strpos($epl_date, 'T') !== FALSE){
													$epl_date = date("Y-m-d\TH:i",strtotime($epl_date));
												} else {
													$epl_date = DateTime::createFromFormat('Y-m-d', $epl_date);
													
													if($epl_date)
														$epl_date = $epl_date->format('Y-m-d');
												}
												$_POST[ $field['name'] ] = $epl_date;
											} 
											
											update_post_meta( $post_ID, $field['name'], $_POST[ $field['name'] ] );
										}
									}
								}
							}
						}
					}
				}
			}
		}
	}
	add_action( 'save_post', 'epl_save_meta_boxes' );

	/**
	 * Adds geo-coordinate button to the address meta box
	 * If you are importing from XML you can use FeedSync 
	 * to pre-geocode the property elements
	 *
	 * @since 1.0
	 */
	function epl_get_geocoordinates() {
		$address = '';
		if(trim($_POST['property_address_sub_number']) != '') {
			$address .= $_POST['property_address_sub_number'].'/';
		}
		$address .= $_POST['property_address_street_number'] . ' ' . $_POST['property_address_street'] . ' ' . $_POST['property_address_suburb'] . ' ' . $_POST['property_address_state'] . ' ' . $_POST['property_address_postal_code'];
		$address = urlencode(strtolower(trim($address)));
		$geourl = "http://maps.google.com/maps/api/geocode/json?address=". urlencode($address) ."&sensor=false";
		$response = epl_remote_url_get($geourl);
		if(!empty($response)) {
			$geocoordinates = $response[0]->geometry->location->lat . ',' . $response[0]->geometry->location->lng;
			echo $geocoordinates;
		}
		exit;
	}
	add_action( 'wp_ajax_epl_get_geocoordinates', 'epl_get_geocoordinates' );
	/**
	 * Returns a dropdown list for terms
	 *
	 * @since 1.0
	 */
	function epl_get_terms_drop_list() {
		$_POST = array_map('trim', $_POST);
		extract($_POST);
	
		$parent_id = (int) $parent_id;
		$terms = get_terms(
			$type_name,
			array(
				'hide_empty'	=>	0,
				'parent'		=>	$parent_id
			)
		);
		if ( !empty($terms) && !is_wp_error($terms) ) {
			$arr = array('' => '');
			foreach ( $terms as $term ) {
				$arr[$term->term_id] = $term->name;
			}
		
			if(!empty($arr)) {
				foreach($arr as $k=>$v) {
					$selected = '';
					if($default_value == $k) {
						$selected = 'selected="selected"';
					}
					echo '<option value="'.$k.'" '.$selected.'>'.__($v, 'epl').'</option>';
				}
			}
		}
	}
	add_action( 'wp_ajax_epl_get_terms_drop_list', 'epl_get_terms_drop_list' );
}
