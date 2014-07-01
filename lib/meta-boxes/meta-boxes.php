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
$opts_users = array();
$users = get_users('orderby=display_name&order=ASC');
if(!empty($users)) {
	foreach ($users as $user) {
		$opts_users[ $user->ID ] = $user->display_name;
	}
}

$opts_property_category = array(
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

$opts_property_holiday_category = array(
	'Alpine'				=>	'Alpine',
	'Unit'					=>	'Unit',
	'ServicedApartment'		=>	'Serviced Apartment',
	'House'					=>	'House',
	'Terrace'				=>	'Terrace',
	'Flat'					=>	'Flat',
	'Studio'				=>	'Studio',
	'Villa'					=>	'Villa',
	'Townhouse'				=>	'Townhouse',
	'Apartment'				=>	'Apartment',
	'DuplexSemi-detached'	=>	'DuplexSemi detached',
	'Retreat'				=>	'Retreat',
	'BackpackerHostel'		=>	'Backpacker Hostel',
	'Campground'			=>	'Camp ground',
	'CaravanHolidayPark'	=>	'Caravan Holiday Park',
	'FarmStay'				=>	'Farm Stay',
	'HouseBoat'				=>	'House Boat',
	'Hotel'					=>	'Hotel',
	'Motel'					=>	'Motel',
	'Lodge'					=>	'Lodge',
	'ExecutiveRental'		=>	'Executive Rental',
	'BedAndBreakfast'		=>	'Bed And Breakfast',
	'Resort'				=>	'Resort',
	'SelfContainedCottage'	=>	'Self Contained Cottage',
	'Other'					=>	'Other',
);

$opts_property_status = array(
	'current'	=>	'Current',
	'withdrawn'	=>	'Withdrawn',
	'offmarket'	=>	'Off Market',
	'sold'		=>	array(
		'label'		=>	'Sold',
		'exclude'	=>	array('rental', 'holiday_rental')
	),
	'leased'		=>	array(
		'label'		=>	'Leased',
		'include'	=>	array('rental', 'holiday_rental', 'commercial', 'commercial_land', 'business')
	)
);

$opts_property_authority = array(
	'auction'		=>	'Auction',
	'exclusive'		=>	'Exclusive',
	'multilist'		=>	'Multilist',
	'conjunctional'	=>	'Conjunctional',
	'open'			=>	'Open',
	'sale'			=>	'Sale',
	'setsale'		=>	'Set Sale'
);

$opts_property_exclusivity = array(
	'exclusive'	=>	'Exclusive',
	'open'		=>	'Open'
);

$opts_property_com_authority = array(
	'auction'	=>	'Auction',
	'tender'	=>	'Tender',
	'eoi'		=>	'EOI',
	'Sale'		=>	'Sale',
	'Forsale'	=>	'For Sale',
	'offers'	=>	'Offers'
);

$opts_area_unit = array(
	'square'		=>	'Square',
	'squareMeter'	=>	'Square Meter',
	'acre'			=>	'Acre',
	'hectare'		=>	'Hectare',
	'sqft'			=>	'Square Feet'
);

$opts_land_category = array(
	'Commercial'	=>	'Commercial',
	'Residential'	=>	'Residential'
);

$opts_rent_period = array(
	'week'		=>	'Week',
	'weekly'	=>	'Weekly'
);

$opts_rural_category = array(
	'Cropping'		=>	'Cropping',
	'Dairy'			=>	'Dairy',
	'Farmlet'		=>	'Farmlet',
	'Horticulture'	=>	'Horticulture',
	'Livestock'		=>	'Livestock',
	'Viticulture'	=>	'Viticulture',
	'MixedFarming'	=>	'Mixed Farming',
	'Lifestyle'		=>	'Lifestyle',
	'Other'			=>	'Other'
);

$opts_property_com_listing_type = array(
	'sale'	=>	'Sale',
	'lease'	=>	'Lease',
	'both'	=>	'Both'
);

$opts_property_commercial_category = array(
	'Commercial Farming'	=>	'Commercial Farming',
	'Land/Development'		=>	'Land/Development',
	'Hotel/Leisure'			=>	'Hotel/Leisure',
	'Industrial/Warehouse'	=>	'Industrial/Warehouse',
	'Medical/Consulting'	=>	'Medical/Consulting',
	'Offices'				=>	'Offices',
	'Retail'				=>	'Retail',
	'Showrooms/Bulky Goods'	=>	'Showrooms/Bulky Goods',
	'Other'					=>	'Other'
);

$opts_property_com_tenancy = array(
	'unknown'	=>	'Unknown',
	'vacant'	=>	'Vacant',
	'tenanted'	=>	'Tenanted'
);

$opts_property_com_property_extent = array(
	'whole'	=>	'Whole',
	'part'	=>	'Part'
);

global $epl_meta_boxes;
$epl_meta_boxes = array(
		
	array(
		'id'		=>	'epl-property-listing-section-id',
		'label'		=>	'Listing Details',
		'post_type'	=>	array('property', 'rural', 'rental', 'holiday_rental', 'land', 'commercial', 'commercial_land', 'business'),
		'context'	=>	'normal',
		'priority'	=>	'default',
		'groups'	=>	array(
			array(
				'columns'	=>	'1',
				'label'		=>	'',
				'fields'	=>	array(
					array(
						'name'		=>	'property_heading',
						'label'		=>	'Heading',
						'type'		=>	'text',
						'maxlength'	=>	'150'
					)
				)
			),
			
			array(
				'columns'	=>	'1',
				'label'		=>	'Listing Agent(s)',
				'fields'	=>	array(
					array(
						'name'		=>	'property_agent',
						'label'		=>	'Listing Agent',
						'type'		=>	'text',
						'maxlength'	=>	'40'
					),
					
					array(
						'name'		=>	'property_second_agent',
						'label'		=>	'Second Listing Agent',
						'type'		=>	'text',
						'maxlength'	=>	'40'
					)
				)
			),
			
			array(
				'columns'	=>	'2',
				'label'		=>	'Listing Type',
				'fields'	=>	array(
					array(
						'name'		=>	'property_status',
						'label'		=>	'Property Status',
						'type'		=>	'select',
						'opts'		=>	$opts_property_status
					),
					
					array(
						'name'		=>	'property_authority',
						'label'		=>	'Authority',
						'type'		=>	'select',
						'opts'		=>	$opts_property_authority,
						'exclude'	=>	array('rental', 'commercial', 'commercial_land')
					),
					
					array(
						'name'		=>	'property_category',
						'label'		=>	'House Category',
						'type'		=>	'select',
						'opts'		=>	$opts_property_category,
						'exclude'	=>	array('land', 'commercial', 'commercial_land', 'business', 'rural')
					),
					
					array(
						'name'		=>	'property_holiday_category',
						'label'		=>	'Holiday Category',
						'type'		=>	'select',
						'opts'		=>	$opts_property_holiday_category,
						'include'	=>	array('holiday_rental')
					),
					
					array(
						'name'		=>	'property_rural_category',
						'label'		=>	'Rural Category',
						'type'		=>	'select',
						'opts'		=>	$opts_rural_category,
						'include'	=>	array('rural')
					),
					
					array(
						'name'		=>	'property_availability_link',
						'label'		=>	'Availablity',
						'type'		=>	'text',
						'include'	=>	array('holiday_rental')
					),
					
					array(
						'name'		=>	'property_unique_id',
						'label'		=>	'Unique ID',
						'type'		=>	'text',
						'maxlength'	=>	'50'
					),
					
					array(
						'name'		=>	'property_mod_date',
						'label'		=>	'XML Importer Mod Date',
						'type'		=>	'text',
						'maxlength'	=>	'60'
					),
					
					array(
						'name'		=>	'property_com_authority',
						'label'		=>	'Commercial Authority',
						'type'		=>	'select',
						'opts'		=>	$opts_property_com_authority,
						'include'	=>	array('commercial', 'commercial_land', 'business')
					),
					
					array(
						'name'		=>	'property_com_exclusivity',
						'label'		=>	'Exclusivity',
						'type'		=>	'select',
						'opts'		=>	$opts_property_exclusivity,
						'include'	=>	array('commercial', 'commercial_land', 'business')
					),
					
					array(
						'name'		=>	'property_com_listing_type',
						'label'		=>	'Commercial Listing Type',
						'type'		=>	'select',
						'opts'		=>	$opts_property_com_listing_type,
						'include'	=>	array('commercial', 'commercial_land')
					),
					
					array(
						'name'		=>	'property_commercial_category',
						'label'		=>	'Commercial Category',
						'type'		=>	'select',
						'opts'		=>	$opts_property_commercial_category,
						'include'	=>	array('commercial', 'commercial_land')
					),
				)
			),
			
			array(
				'columns'	=>	'2',
				'label'		=>	'Display Details',
				'fields'	=>	array(
					array(
						'name'		=>	'property_featured',
						'label'		=>	'Featured',
						'type'		=>	'radio',
						'opts'	=>	array(
							'yes'	=>	'Yes',
							'no'	=>	'No'
						),
					),
					
					array(
						'name'		=>	'property_inspection_times',
						'label'		=>	'Inspection Times',
						'type'		=>	'text',
						'maxlength'	=>	'150'
					)
					
				)
			)
		)
	),
	
	array(
		'id'		=>	'epl-features-section-id',
		'label'		=>	'Listing Features',
		'post_type'	=>	array('property', 'rural', 'rental' ),
		'context'	=>	'normal',
		'priority'	=>	'default',
		'groups'	=>	array(
			array(
				'columns'	=>	'2',
				'label'		=>	'House Features',
				'fields'	=>	array(
					array(
						'name'		=>	'property_bedrooms',
						'label'		=>	'Bedrooms',
						'type'		=>	'number',
						'maxlength'	=>	'2'
					),
					
					array(
						'name'		=>	'property_bathrooms',
						'label'		=>	'Bathrooms',
						'type'		=>	'number',
						'maxlength'	=>	'2'
					),

					array(
						'name'		=>	'property_ensuite',
						'label'		=>	'Ensuite',
						'type'		=>	'number',
						'maxlength'	=>	'2'
					),

					array(
						'name'		=>	'property_toilet',
						'label'		=>	'Toilet',
						'type'		=>	'number',
						'maxlength'	=>	'2'
					),

					
					array(
						'name'		=>	'property_garage',
						'label'		=>	'Garage',
						'type'		=>	'number',
						'maxlength'	=>	'2'
					),
					
					array(
						'name'		=>	'property_carport',
						'label'		=>	'Carport',
						'type'		=>	'number',
						'maxlength'	=>	'2'
					),
					
					array(
						'name'		=>	'property_new_construction',
						'label'		=>	'New Construction',
						'type'		=>	'radio',
						'opts'	=>	array(
							'yes'	=>	'Yes',
							'no'	=>	'No'
						),
						'include'	=>	array('property', 'rental' )
					),
					
					array(
						'name'		=>	'property_pool',
						'label'		=>	'Pool',
						'type'		=>	'radio',
						'opts'	=>	array(
							'yes'	=>	'Yes',
							'no'	=>	'No'
						),
					),
					
					array(
						'name'		=>	'property_air_conditioning',
						'label'		=>	'Air Conditioning',
						'type'		=>	'radio',
						'opts'	=>	array(
							'yes'	=>	'Yes',
							'no'	=>	'No'
						),
					),
					
					array(
						'name'		=>	'property_security_system',
						'label'		=>	'Security System',
						'type'		=>	'radio',
						'opts'	=>	array(
							'yes'	=>	'Yes',
							'no'	=>	'No'
						),
					)
				)
			),
			
			array(
				'columns'	=>	'2',
				'label'		=>	'Land Details',
				'fields'	=>	array(
					array(
						'name'		=>	'property_land_area',
						'label'		=>	'Land Area',
						'type'		=>	'decimal',
						'maxlength'	=>	'50'
					),
					
					array(
						'name'		=>	'property_land_area_unit',
						'label'		=>	'Land Unit',
						'type'		=>	'select',
						'opts'		=>	$opts_area_unit
					),
					
					array(
						'name'		=>	'property_building_area',
						'label'		=>	'Building Area',
						'type'		=>	'number',
						'maxlength'	=>	'50'
					),
					
					array(
						'name'		=>	'property_building_area_unit',
						'label'		=>	'Building Unit',
						'type'		=>	'select',
						'opts'		=>	$opts_area_unit
					),
					
					array(
						'name'		=>	'property_land_fully_fenced',
						'label'		=>	'Fully Fenced',
						'type'		=>	'radio',
						'opts'	=>	array(
							'yes'	=>	'Yes',
							'no'	=>	'No'
						),
					),
				)
			)
		)
	),
	
	array( // Additional Features
		'id'		=>	'epl-additional-features-section-id',
		'label'		=>	'Additional Features',
		'post_type'	=>	array('property', 'rural', 'rental' ),
		'context'	=>	'normal',
		'priority'	=>	'default',
		'groups'	=>	array(
			array(
				'columns'	=>	'2',
				'label'		=>	'Internal',
				'fields'	=>	array(
					array(
						'name'		=>	'property_remote_garage',
						'label'		=>	'Remote Garage',
						'type'		=>	'radio',
						'opts'	=>	array(
							'yes'	=>	'Yes',
							'no'	=>	'No'
						),
					),
					
					array(
						'name'		=>	'property_secure_parking',
						'label'		=>	'Secure Parking',
						'type'		=>	'radio',
						'opts'	=>	array(
							'yes'	=>	'Yes',
							'no'	=>	'No'
						),
					),
					
					array(
						'name'		=>	'property_study',
						'label'		=>	'Study',
						'type'		=>	'radio',
						'opts'	=>	array(
							'yes'	=>	'Yes',
							'no'	=>	'No'
						),
					),
					
					array(
						'name'		=>	'property_dishwasher',
						'label'		=>	'Dishwasher',
						'type'		=>	'radio',
						'opts'	=>	array(
							'yes'	=>	'Yes',
							'no'	=>	'No'
						),
					),
					
					array(
						'name'		=>	'property_built_in_robes',
						'label'		=>	'Built In Robes',
						'type'		=>	'radio',
						'opts'	=>	array(
							'yes'	=>	'Yes',
							'no'	=>	'No'
						),
					),
					
					array(
						'name'		=>	'property_gym',
						'label'		=>	'Gym',
						'type'		=>	'radio',
						'opts'	=>	array(
							'yes'	=>	'Yes',
							'no'	=>	'No'
						),
					),
					
					array(
						'name'		=>	'property_workshop',
						'label'		=>	'Workshop',
						'type'		=>	'radio',
						'opts'	=>	array(
							'yes'	=>	'Yes',
							'no'	=>	'No'
						),
					),
					
					array(
						'name'		=>	'property_rumpus_room',
						'label'		=>	'Rumpus Room',
						'type'		=>	'radio',
						'opts'	=>	array(
							'yes'	=>	'Yes',
							'no'	=>	'No'
						),
					),
					
					array(
						'name'		=>	'property_floor_boards',
						'label'		=>	'Floor Boards',
						'type'		=>	'radio',
						'opts'	=>	array(
							'yes'	=>	'Yes',
							'no'	=>	'No'
						),
					),
					
					array(
						'name'		=>	'property_broadband',
						'label'		=>	'Broadband',
						'type'		=>	'radio',
						'opts'	=>	array(
							'yes'	=>	'Yes',
							'no'	=>	'No'
						),
					),
					
					array(
						'name'		=>	'property_pay_tv',
						'label'		=>	'Pay TV',
						'type'		=>	'radio',
						'opts'	=>	array(
							'yes'	=>	'Yes',
							'no'	=>	'No'
						),
					),

					array(
						'name'		=>	'property_vacuum_system',
						'label'		=>	'Vacuum System',
						'type'		=>	'radio',
						'opts'	=>	array(
							'yes'	=>	'Yes',
							'no'	=>	'No'
						),
					),
					
					array(
						'name'		=>	'property_intercom',
						'label'		=>	'Intercom',
						'type'		=>	'radio',
						'opts'	=>	array(
							'yes'	=>	'Yes',
							'no'	=>	'No'
						),
					),
					
					array(
						'name'		=>	'property_spa',
						'label'		=>	'Spa',
						'type'		=>	'radio',
						'opts'	=>	array(
							'yes'	=>	'Yes',
							'no'	=>	'No'
						),
					)
				)
			),
			
			array(
				'columns'	=>	'2',
				'label'		=>	'External',
				'fields'	=>	array(
					
					array(
						'name'		=>	'property_tennis_court',
						'label'		=>	'Tennis Court',
						'type'		=>	'radio',
						'opts'	=>	array(
							'yes'	=>	'Yes',
							'no'	=>	'No'
						),
					),
					
					array(
						'name'		=>	'property_balcony',
						'label'		=>	'Balcony',
						'type'		=>	'radio',
						'opts'	=>	array(
							'yes'	=>	'Yes',
							'no'	=>	'No'
						),
					),
					
					array(
						'name'		=>	'property_deck',
						'label'		=>	'Deck',
						'type'		=>	'radio',
						'opts'	=>	array(
							'yes'	=>	'Yes',
							'no'	=>	'No'
						),
					),
					
					array(
						'name'		=>	'property_courtyard',
						'label'		=>	'Courtyard',
						'type'		=>	'radio',
						'opts'	=>	array(
							'yes'	=>	'Yes',
							'no'	=>	'No'
						),
					),
					
					array(
						'name'		=>	'property_outdoor_entertaining',
						'label'		=>	'Outdoor Entertaining',
						'type'		=>	'radio',
						'opts'	=>	array(
							'yes'	=>	'Yes',
							'no'	=>	'No'
						),
					),
					
					array(
						'name'		=>	'property_shed',
						'label'		=>	'Shed',
						'type'		=>	'radio',
						'opts'	=>	array(
							'yes'	=>	'Yes',
							'no'	=>	'No'
						),
					)
				)
			),
			array(
				'columns'	=>	'2',
				'label'		=>	'Heating & Cooling',
				'fields'	=>	array(
					
					array(
						'name'		=>	'property_ducted_heating',
						'label'		=>	'Ducted Heating',
						'type'		=>	'radio',
						'opts'	=>	array(
							'yes'	=>	'Yes',
							'no'	=>	'No'
						),
					),
					
					array(
						'name'		=>	'property_ducted_cooling',
						'label'		=>	'Ducted Cooling',
						'type'		=>	'radio',
						'opts'	=>	array(
							'yes'	=>	'Yes',
							'no'	=>	'No'
						),
					),
					
					array(
						'name'		=>	'property_split_system_heating',
						'label'		=>	'Split System Heating',
						'type'		=>	'radio',
						'opts'	=>	array(
							'yes'	=>	'Yes',
							'no'	=>	'No'
						),
					),
					
					array(
						'name'		=>	'property_hydronic_heating',
						'label'		=>	'Hydronic Heating',
						'type'		=>	'radio',
						'opts'	=>	array(
							'yes'	=>	'Yes',
							'no'	=>	'No'
						),
					),
					
					array(
						'name'		=>	'property_split_system_aircon',
						'label'		=>	'Split System Aircon',
						'type'		=>	'radio',
						'opts'	=>	array(
							'yes'	=>	'Yes',
							'no'	=>	'No'
						),
					),
					
					array(
						'name'		=>	'property_gas_heating',
						'label'		=>	'Gas Heating',
						'type'		=>	'radio',
						'opts'	=>	array(
							'yes'	=>	'Yes',
							'no'	=>	'No'
						),
					),
					
					array(
						'name'		=>	'property_reverse_cycle_aircon',
						'label'		=>	'Reverse Cycle Aircon',
						'type'		=>	'radio',
						'opts'	=>	array(
							'yes'	=>	'Yes',
							'no'	=>	'No'
						),
					),
					
					array(
						'name'		=>	'property_evaporative_cooling',
						'label'		=>	'Evaporative Cooling',
						'type'		=>	'radio',
						'opts'	=>	array(
							'yes'	=>	'Yes',
							'no'	=>	'No'
						),
					),
					
					array(
						'name'		=>	'property_open_fire_place',
						'label'		=>	'Open Fire Place',
						'type'		=>	'radio',
						'opts'	=>	array(
							'yes'	=>	'Yes',
							'no'	=>	'No'
						),
					)

				)
			)
		)
	),
	
	array( //Repeating most from above "epl-features-section-id" because on land it will be single column
		'id'		=>	'epl-features-section-id-single-column',
		'label'		=>	'Land Details',
		'post_type'	=>	array('land', 'commercial'),
		'context'	=>	'normal',
		'priority'	=>	'default',
		'groups'	=>	array(
			array(
				'columns'	=>	'1',
				'label'		=>	'',
				'fields'	=>	array(
					array(
						'name'		=>	'property_land_area',
						'label'		=>	'Land Area',
						'type'		=>	'decimal',
						'maxlength'	=>	'50'
					),
					
					array(
						'name'		=>	'property_land_area_unit',
						'label'		=>	'Land Unit',
						'type'		=>	'select',
						'opts'		=>	$opts_area_unit
					),
					
					array(
						'name'		=>	'property_building_area',
						'label'		=>	'Building Area',
						'type'		=>	'number',
						'include'	=>	array('commercial'),
						'maxlength'	=>	'40'
					),
					
					array(
						'name'		=>	'property_building_area_unit',
						'label'		=>	'Building Unit',
						'type'		=>	'select',
						'opts'		=>	$opts_area_unit,
						'include'	=>	array('commercial')
					),
					
					array(
						'name'		=>	'property_land_category',
						'label'		=>	'Land Category',
						'type'		=>	'select',
						'opts'		=>	$opts_land_category,
						'include'	=>	array('land')
					),
					
					array(
						'name'		=>	'property_land_fully_fenced',
						'label'		=>	'Fully Fenced',
						'type'		=>	'radio',
						'opts'	=>	array(
							'yes'	=>	'Yes',
							'no'	=>	'No'
						),
					),
				)
			)
		)
	),
	
	array(
		'id'		=>	'epl-property-address-section-id',
		'label'		=>	'Property Address',
		'post_type'	=>	array('property', 'rural', 'rental', 'holiday_rental', 'commercial', 'commercial_land', 'business', 'land'),
		'context'	=>	'side',
		'priority'	=>	'core',
		'groups'	=>	array(
			array(
				'columns'	=>	'1',
				'label'		=>	'',
				'fields'	=>	array(
					array(
						'name'		=>	'property_address_display',
						'label'		=>	'Display Address?',
						'type'		=>	'radio',
						'opts'		=>	array(
							'yes'	=>	'Yes',
							'no'	=>	'No'
						),
					),
					
					array(
						'name'		=>	'property_address_street_number',
						'label'		=>	'Street Number',
						'type'		=>	'text',
						'maxlength'	=>	'40'
					),
					
					array(
						'name'		=>	'property_address_street',
						'label'		=>	'Street Name',
						'type'		=>	'text',
						'maxlength'	=>	'40'
					),
					
					array(
						'name'		=>	'property_address_suburb',
						'label'		=>	epl_display_label_suburb(),
						'type'		=>	'text',
						'maxlength'	=>	'40'
					),
					
					array(
						'name'		=>	'property_address_state',
						'label'		=>	'State',
						'type'		=>	'text',
						'maxlength'	=>	'40'
					),
					
					array(
						'name'		=>	'property_com_display_suburb',
						'label'		=>	'Display Suburb',
						'type'		=>	'radio',
						'opts'		=>	array(
							'yes'	=>	'Yes',
							'no'	=>	'No'
						),
						'include'	=>	array('commercial', 'commercial_land', 'business'),
					),
					
					array(
						'name'		=>	'property_address_postal_code',
						'label'		=>	epl_display_label_postcode(),
						'type'		=>	'text',
						'maxlength'	=>	'20'
					),
					
					array(
						'name'		=>	'property_address_coordinates',
						'label'		=>	'Coordinates',
						'type'		=>	'text',
						'geocoder'	=>	'true',
						'maxlength'	=>	'40'
					)
				)
			)
		)
	),
	
	array(
		'id'		=>	'epl-pricing-section-id',
		'label'		=>	'Pricing',
		'post_type'	=>	array('property', 'rural', 'commercial', 'commercial_land', 'business', 'land'),
		'context'	=>	'side',
		'priority'	=>	'core',
		'groups'	=>	array(
			array(
				'columns'	=>	'1',
				'label'		=>	'',
				'fields'	=>	array(
					array(
						'name'		=>	'property_price',
						'label'		=>	'Search Price',
						'type'		=>	'number',
						'maxlength'	=>	'50'
					),
					
					array(
						'name'		=>	'property_price_view',
						'label'		=>	'Price Text',
						'type'		=>	'text',
						'maxlength'	=>	'50'
					),
					
					array(
						'name'		=>	'property_auction',
						'label'		=>	'Auction Date / Details',
						'type'		=>	'text',
						'maxlength'	=>	'100'
					),
					
					array(
						'name'		=>	'property_price_display',
						'label'		=>	'Display Price?',
						'type'		=>	'radio',
						'opts'		=>	array(
							'yes'	=>	'Yes',
							'no'	=>	'No'
						),
					),
					
					array(
						'name'		=>	'property_under_offer',
						'label'		=>	'Under Offer',
						'type'		=>	'radio',
						'opts'		=>	array(
							'yes'	=>	'Yes',
							'no'	=>	'No'
						),
					),
					
					array(
						'name'		=>	'property_is_home_land_package',
						'label'		=>	'House and Land Package',
						'type'		=>	'radio',
						'opts'		=>	array(
							'yes'	=>	'Yes',
							'no'	=>	'No'
						),
						'exclude'	=>	array('land', 'rural', 'commercial', 'commercial_land' , 'business')
					)
				)
			),
			
			array(
				'columns'	=>	'1',
				'label'		=>	'Sale Details',
				'fields'	=>	array(
					array(
						'name'		=>	'property_sold_price',
						'label'		=>	'Sale Price',
						'type'		=>	'number',
						'maxlength'	=>	'50'
					),
					
					array(
						'name'		=>	'property_sold_date',
						'label'		=>	'Sale Date',
						'type'		=>	'text',
						'maxlength'	=>	'100'
					),
					
					array(
						'name'		=>	'property_sold_price_display',
						'label'		=>	'Display Sale Price',
						'type'		=>	'radio',
						'opts'		=>	array(
							'yes'	=>	'Yes',
							'no'	=>	'No'
						),
					)
				)
			)
		)
	),	
	
	array(
		'id'		=>	'epl-property-rent-id',
		'label'		=>	'Rental Pricing',
		'post_type'	=>	array('rental', 'holiday_rental'),
		'context'	=>	'side',
		'priority'	=>	'core',
		'groups'	=>	array(
			array(
				'columns'	=>	'1',
				'label'		=>	'',
				'fields'	=>	array(
					array(
						'name'		=>	'property_rent_display',
						'label'		=>	'Display Rent?',
						'type'		=>	'radio',
						'opts'		=>	array(
							'yes'	=>	'Yes',
							'no'	=>	'No'
						),
					),
					
					array(
						'name'		=>	'property_rent',
						'label'		=>	'Rent Amount',
						'type'		=>	'number',
						'maxlength'	=>	'40'
					),
					
					array(
						'name'		=>	'property_rent_period',
						'label'		=>	'Rent Period',
						'type'		=>	'select',
						'opts'		=>	$opts_rent_period
					),
					
					array(
						'name'		=>	'property_bond',
						'label'		=>	'Bond Amount',
						'type'		=>	'number',
						'maxlength'	=>	'40'
					),
					
					array(
						'name'		=>	'property_date_available',
						'label'		=>	'Date Available',
						'type'		=>	'text',
						'maxlength'	=>	'100'
					),
					
					array(
						'name'		=>	'property_furnished',
						'label'		=>	'Furnished',
						'type'		=>	'radio',
						'opts'		=>	array(
							'yes'	=>	'Yes',
							'no'	=>	'No'
						),
					),
					
					array(
						'name'		=>	'property_holiday_rental',
						'label'		=>	'Holiday Rental',
						'type'		=>	'radio',
						'opts'		=>	array(
							'yes'	=>	'Yes',
							'no'	=>	'No'
						),
						'exclude'	=>	array('rental')
					),
				)
			)
		)
	),
	
	array(
		'id'		=>	'epl-rural-features-id',
		'label'		=>	'Rural Features',
		'post_type'	=>	array('rural'),
		'context'	=>	'normal',
		'priority'	=>	'default',
		'groups'	=>	array(
			array(
				'columns'	=>	'1',
				'label'		=>	'',
				'fields'	=>	array(
					array(
						'name'		=>	'property_rural_fencing',
						'label'		=>	'Fencing',
						'type'		=>	'textarea',
						'maxlength'	=>	'65535'
					),
					
					array(
						'name'		=>	'property_rural_annual_rainfall',
						'label'		=>	'Annual Rainfall',
						'type'		=>	'textarea',
						'maxlength'	=>	'65535'
					),
					
					array(
						'name'		=>	'property_rural_soil_types',
						'label'		=>	'Soil Types',
						'type'		=>	'textarea',
						'maxlength'	=>	'65535'
					),
					
					array(
						'name'		=>	'property_rural_improvements',
						'label'		=>	'Improvements',
						'type'		=>	'textarea',
						'maxlength'	=>	'65535'
					),
					
					array(
						'name'		=>	'property_rural_council_rates',
						'label'		=>	'Council Rates',
						'type'		=>	'textarea',
						'maxlength'	=>	'65535'
					),
					
					array(
						'name'		=>	'property_rural_irrigation',
						'label'		=>	'Irrigation',
						'type'		=>	'textarea',
						'maxlength'	=>	'65535'
					),
					
					array(
						'name'		=>	'property_rural_carrying_capacity',
						'label'		=>	'Carrying Capacity',
						'type'		=>	'textarea',
						'maxlength'	=>	'65535'
					),
					
					array(
						'name'		=>	'property_rural_services',
						'label'		=>	'Services',
						'type'		=>	'textarea',
						'maxlength'	=>	'65535'
					)
				)
			)
		)
	),
	
	array(
		'id'		=>	'epl-commercial-leasing-id',
		'label'		=>	'Commercial Leasing',
		'post_type'	=>	array('commercial', 'commercial_land'),
		'context'	=>	'normal',
		'priority'	=>	'default',
		'groups'	=>	array(
			array(
				'columns'	=>	'2',
				'label'		=>	'',
				'fields'	=>	array(
					array(
						'name'		=>	'property_com_rent',
						'label'		=>	'Commercial Rent',
						'type'		=>	'number',
						'maxlength'	=>	'40'
					),
					
					array(
						'name'		=>	'property_com_rent_range_min',
						'label'		=>	'Rent Range Min',
						'type'		=>	'number',
						'maxlength'	=>	'40'
					),
					 
					array(
						'name'		=>	'property_com_rent_range_max',
						'label'		=>	'Rent Range Max',
						'type'		=>	'number',
						'maxlength'	=>	'40'
					),
					
					array(
						'name'		=>	'property_com_lease_end_date',
						'label'		=>	'Lease End Date',
						'type'		=>	'date',
						'maxlength'	=>	'100'
					),
					
					array(
						'name'		=>	'property_com_property_extent',
						'label'		=>	'Property Extent',
						'type'		=>	'select',
						'opts'		=>	$opts_property_com_property_extent
					)
				)
			),
			
			array(
				'columns'	=>	'2',
				'label'		=>	'',
				'fields'	=>	array(
					array(
						'name'		=>	'property_com_tenancy',
						'label'		=>	'Tenant Status',
						'type'		=>	'select',
						'opts'		=>	$opts_property_com_tenancy,
						'exclude'	=>	array('commercial_land')
					),
					
					array(
						'name'		=>	'property_com_outgoings',
						'label'		=>	'Commercial Outgoings',
						'type'		=>	'number',
						'maxlength'	=>	'40'
					)
					,
					
					array(
						'name'		=>	'property_com_plus_outgoings',
						'label'		=>	'Plus Outgoings',
						'type'		=>	'radio',
						'opts'		=>	array(
							'yes'	=>	'Yes',
							'no'	=>	'No'
						),
					),
					
					array(
						'name'		=>	'property_com_return',
						'label'		=>	'Commercial Return',
						'type'		=>	'decimal',
						'maxlength'	=>	'6'
					)
				)
			)
		)
	),
	
	array(
		'id'		=>	'epl-commercial-features-id',
		'label'		=>	'Commercial Features',
		'post_type'	=>	array('commercial'),
		'context'	=>	'normal',
		'priority'	=>	'default',
		'groups'	=>	array(
			array(
				'columns'	=>	'1',
				'label'		=>	'',
				'fields'	=>	array(
					array(
						'name'		=>	'property_com_further_options',
						'label'		=>	'Further Options',
						'type'		=>	'textarea',
						'maxlength'	=>	'65535'
					),
					
					array(
						'name'		=>	'property_com_zone',
						'label'		=>	'Zone',
						'type'		=>	'textarea',
						'maxlength'	=>	'150'
					),
					
					array(
						'name'		=>	'property_com_car_spaces',
						'label'		=>	'Car Spaces',
						'type'		=>	'number',
						'type'		=>	'textarea',
						'maxlength'	=>	'5'
					),
					
					array(
						'name'		=>	'property_com_highlight_1',
						'label'		=>	'Highlight 1',
						'type'		=>	'text',
						'maxlength'	=>	'40'
					),
					
					array(
						'name'		=>	'property_com_highlight_2',
						'label'		=>	'Highlight 2',
						'type'		=>	'text',
						'maxlength'	=>	'40'
					),
					
					array(
						'name'		=>	'property_com_highlight_3',
						'label'		=>	'Highlight 3',
						'type'		=>	'text',
						'maxlength'	=>	'40'
					),
					
					array(
						'name'		=>	'property_com_parking_comments',
						'label'		=>	'Parking Comments',
						'type'		=>	'text',
						'maxlength'	=>	'150'
					),
					
					array(
						'name'		=>	'property_com_is_multiple',
						'label'		=>	'Is Multiple',
						'type'		=>	'radio',
						'opts'	=>	array(
							'yes'	=>	'Yes',
							'no'	=>	'No'
						)
					)
				)
			)
		)
	),
	
	array(
		'id'		=>	'epl-business-features-id',
		'label'		=>	'Business Categories',
		'post_type'	=>	array('business'),
		'context'	=>	'normal',
		'priority'	=>	'default',
		'groups'	=>	array(
			array(
				'columns'	=>	'1',
				'label'		=>	'',
				'fields'	=>	array(
					array(
						'name'		=>	'property_bus_category',
						'label'		=>	'Business Category',
						'type'		=>	'select',
						'opt_args'	=>	array(
							'type'	=>	'taxonomy',
							'slug'	=>	'tax_business_listing'
						)
					),
					
					array(
						'name'		=>	'property_bus_sub_category',
						'label'		=>	'Business Sub Category',
						'type'		=>	'select',
						'opt_args'	=>	array(
							'type'	=>	'taxonomy',
							'slug'	=>	'tax_business_listing',
							'parent'=>	'property_bus_category'
						)
					),
					
					array(
						'name'		=>	'property_bus_category_2',
						'label'		=>	'Business Category 2',
						'type'		=>	'select',
						'opt_args'	=>	array(
							'type'	=>	'taxonomy',
							'slug'	=>	'tax_business_listing'
						)
					),
					
					array(
						'name'		=>	'property_bus_sub_category_2',
						'label'		=>	'Business Sub Category 2',
						'type'		=>	'select',
						'opt_args'	=>	array(
							'type'	=>	'taxonomy',
							'slug'	=>	'tax_business_listing',
							'parent'=>	'property_bus_category_2'
						)
					),
					
					array(
						'name'		=>	'property_bus_category_3',
						'label'		=>	'Business Category 3',
						'type'		=>	'select',
						'opt_args'	=>	array(
							'type'	=>	'taxonomy',
							'slug'	=>	'tax_business_listing'
						)
					),
					
					array(
						'name'		=>	'property_bus_sub_category_3',
						'label'		=>	'Business Sub Category 3',
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
		'label'		=>	'Files and Links',
		'post_type'	=>	array('property', 'rural', 'commercial', 'commercial_land', 'business', 'rental', 'holiday_rental', 'land'),
		'context'	=>	'normal',
		'priority'	=>	'default',
		'groups'	=>	array(
			array(
				'columns'	=>	'1',
				'label'		=>	'',
				'fields'	=>	array(
					array(
						'name'		=>	'property_video_url',
						'label'		=>	'Video URL',
						'type'		=>	'url'
					),
					
					array(
						'name'		=>	'property_floorplan',
						'label'		=>	'Floorplan',
						'type'		=>	'url'
					),

					array(
						'name'		=>	'property_floorplan_2',
						'label'		=>	'Floorplan 2',
						'type'		=>	'url'
					),
					
					array(
						'name'		=>	'property_external_link',
						'label'		=>	'External Link',
						'type'		=>	'url'
					),
					
					array(
						'name'		=>	'property_external_link_2',
						'label'		=>	'External Link 2',
						'type'		=>	'url'
					),

					array(
						'name'		=>	'property_external_link_3',
						'label'		=>	'External Link 3',
						'type'		=>	'url',
						'include'	=>	array('commercial', 'business')
					),
					
					array(
						'name'		=>	'property_com_mini_web',
						'label'		=>	'Mini Website URL',
						'type'		=>	'url',
						'include'	=>	array('commercial', 'business', 'commercial_land'),
					),

					array(
						'name'		=>	'property_com_mini_web_2',
						'label'		=>	'Mini Website URL 2',
						'type'		=>	'url',
						'include'	=>	array('commercial', 'business', 'commercial_land'),
					),	

					array(
						'name'		=>	'property_com_mini_web_3',
						'label'		=>	'Mini Website URL 3',
						'type'		=>	'url',
						'include'	=>	array('commercial', 'business', 'commercial_land'),
					),						
				)
			)
		)
	)
);

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
												switch($field['type']) {
													case 'select':
														$dependency = 'false'; 
														if(isset($field['opt_args']) && !empty($field['opt_args'])) {
															if( isset($field['opt_args']['type']) ) {
																switch($field['opt_args']['type']) {
																	case 'taxonomy':
																		$terms = get_terms(
																			$field['opt_args']['slug'],
																			array(
																				'hide_empty'	=>	0,
																				'parent'		=>	0
																			)
																		);
																		
																		if(!isset($field['opt_args']['parent']) || $field['opt_args']['parent'] == '') {
																			$var = sanitize_title( $field['opt_args']['slug'] );
																			$var = 'var_'.str_replace("-", "_", $var);
																		
																			if(!isset($$var)) {
																				$$var = array();
																				if ( !empty($terms) && !is_wp_error($terms) ) {
																					$arr = array('' => '');
																					foreach ( $terms as $term ) {
																						$arr[$term->term_id] = $term->name;
																					}
																				}
																				$$var = $arr;
																			}
																			$field['opts'] = $$var;
																		} else {
																			$dependency = 'true';
																		}
																		break;
																}
															}
														}
														
														$field_atts = '';
														if($dependency == 'true') {
															$field_atts = 'data-dependency="true" data-type="taxonomy" data-type-name="'.$field['opt_args']['slug'].'" data-parent="'.$field['opt_args']['parent'].'" data-default="'.$val.'"';
														}
														
														echo '<select name="'.$field['name'].'" id="'.$field['name'].'" '.$field_atts.' class="dependency-'.$dependency.'">';
															if(!empty($field['default'])) {
																echo '<option value="" selected="selected">'.__($field['default'], 'epl').'</option>';
															}
															
															if(isset($field['opts']) && !empty($field['opts'])) {
																foreach($field['opts'] as $k=>$v) {
																	$selected = '';
																	if($val == $k) {
																		$selected = 'selected="selected"';
																	}
																	
																	if(is_array($v)) {
																		if(isset($v['exclude']) && !empty($v['exclude'])) {
																			if( in_array($post->post_type, $v['exclude']) ) {
																				continue;
																			}
																		}
																		
																		if(isset($v['include']) && !empty($v['include'])) {
																			if( !in_array($post->post_type, $v['include']) ) {
																				continue;
																			}
																		}
																		$v = $v['label'];
																	}
																	
																	echo '<option value="'.$k.'" '.$selected.'>'.__($v, 'epl').'</option>';
																}
															} else {
																echo '<option value=""> </option>';
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
									
													case 'image':
														if($val != '') {
															$img = $val;
														} else {
															$img = plugin_dir_url( __FILE__ ).'images/no_image.jpg';
														}
														echo '
															<div class="epl-media-row">
																<input type="text" name="'.$field['name'].'" id="'.$field['name'].'" value="'.stripslashes($val).'" />
																&nbsp;&nbsp;<input type="button" name="epl_upload_button" class="button" value="'.__('Add File', 'epl').'" />
																&nbsp;&nbsp;<img src="'.$img.'" alt="" />
																<div class="epl-clear"></div>
															</div>
														';
														break;
							
													case 'editor':
														wp_editor(stripslashes($val), $field['name'], $settings = array('textarea_rows'=>5));
														break;
									
													case 'textarea':
														$atts = '';
														if($field['maxlength'] > 0) {
															$atts = ' maxlength="'.$field['maxlength'].'"';
														}
														echo '<textarea name="'.$field['name'].'" id="'.$field['name'].'" '.$atts.'>'.stripslashes($val).'</textarea>';
														break;
													
													case'decimal':
														$atts = '';
														if($field['maxlength'] > 0) {
															$atts = ' maxlength="'.$field['maxlength'].'"';
														}
														echo '<input type="text" name="'.$field['name'].'" id="'.$field['name'].'" value="'.stripslashes($val).'" class="validate[custom[onlyNumberWithDecimal]]" '.$atts.' />';
														break;
														
													case 'number':
														$atts = '';
														if($field['maxlength'] > 0) {
															$atts = ' maxlength="'.$field['maxlength'].'"';
														}
														echo '<input type="text" name="'.$field['name'].'" id="'.$field['name'].'" value="'.stripslashes($val).'" class="validate[custom[onlyNumber]]" '.$atts.' />';
														break;
														
													case 'url':
														echo '<input type="text" name="'.$field['name'].'" id="'.$field['name'].'" value="'.stripslashes($val).'" class="validate[custom[url]]" />';
														break;
														
													default:
														$atts = '';
														if($field['maxlength'] > 0) {
															$atts = ' maxlength="'.$field['maxlength'].'"';
														}
														echo '<input type="'.$field['type'].'" name="'.$field['name'].'" id="'.$field['name'].'" value="'.stripslashes($val).'" '.$atts.' />';
												}
												
												if( isset($field['geocoder']) && $field['geocoder'] == 'true' ) {
													echo '<span class="epl-geocoder-button"></span>';
												}												
												
												if(isset($field['help'])) {
													$field['help'] = trim($field['help']);
													if(!empty($field['help'])) {
														echo '<span class="epl-help-text">'.__($field['help'], 'epl').'</span>';
													}
												}
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
										
										/* if( isset($field['geocoder']) && $field['geocoder'] == 'true' ) {
											if($epl_enable_import_geocode == 1 || $epl_enable_import_geocode == 'yes') {
												$address = $_POST['property_address_street_number'] . ' ' . $_POST['property_address_street'] . ' ' . $_POST['property_address_suburb'] . ' ' . $_POST['property_address_state'] . ' ' . $_POST['property_address_postal_code'];
												$address = urlencode(strtolower(trim($address)));
												$geourl = "http://maps.google.com/maps/api/geocode/json?address=". $address ."&sensor=false";
												$response = epl_remote_url_get($geourl);												
												$_POST[ $field['name'] ] = $response[0]->geometry->location->lat . ',' . $response[0]->geometry->location->lng;
											}
										} */
										
										
										if( $field['type'] == 'radio' ) {
											if(!isset($_POST[ $field['name'] ])) {
												continue;
											}
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
 * Load javascipt for verifying correct content type for each meta field type
 *
 * @since 1.0
 */
function epl_admin_head_scripts() { ?>
	<script type="text/javascript">
		jQuery(document).ready(function($) {
			var formfield;
			
			if( $('input[name="epl_upload_button"]').length ) {
				$('input[name="epl_upload_button"]').click(function() {
					formfield = $(this);
					tb_show('', 'media-upload.php?TB_iframe=true');
					return false;
				});
				
				window.old_tb_remove = window.tb_remove;
				window.tb_remove = function() {
					window.old_tb_remove();
					formfield = null;
				};

				window.original_send_to_editor = window.send_to_editor;
				window.send_to_editor = function(html){
					if (formfield) {
						fileurl = $('img', html).attr('src');
						$(formfield).closest('div').find('input[type="text"]').val(fileurl);
						$(formfield).closest('div').find('img').attr('src', fileurl);
						tb_remove();
					} else {
						window.original_send_to_editor(html);
					}
				};
			}
			
			$('.epl-geocoder-button').click(function() {
				var $obj = $(this);
				$obj.parent().addClass('disabled');
				$.ajax({
					type: "POST",
					url: ajaxurl,
					data: {
						'property_address_street_number':	$obj.closest('form').find('#property_address_street_number').val(),
						'property_address_street'		:	$obj.closest('form').find('#property_address_street').val(),
						'property_address_suburb'		:	$obj.closest('form').find('#property_address_suburb').val(),
						'property_address_state'		:	$obj.closest('form').find('#property_address_state').val(),
						'property_address_postal_code'	:	$obj.closest('form').find('#property_address_postal_code').val(),
						'action'						:	'epl_get_geocoordinates'
					},
					success: function(response) {
						$obj.prev('input').val( response );
						$obj.parent().removeClass('disabled');
					}
				});
			});
			
			$('.dependency-true').each(function() {
				var $this = $(this);
				var data_parent = $this.attr('data-parent');
				if( $('select[name="'+data_parent+'"]').length) {
					if( $this.attr('data-type') == 'taxonomy' ) {
						var default_value = $this.attr('data-default');
						$('select[name="'+data_parent+'"]').change(function() {
							$.ajax({
								type: "POST",
								url: ajaxurl,
								data: {
									'parent_id'		:	$(this).val(),
									'type_name'		:	$this.attr('data-type-name'),
									'type'			:	$this.attr('data-type'),
									'default_value'	:	default_value,
									'action'		:	'epl_get_terms_drop_list'
								},
								success: function(response) {
									$this.html( response );
								}
							});
						}).trigger('change');
					}
				}
			});
		});
	</script>
<?php }
add_action( 'admin_head', 'epl_admin_head_scripts' );

/**
 * Addes geo coordinate button to the address meta box
 * If you are importing from XML you can use FeedSync 
 * to pre-geocode the property elements
 *
 * @since 1.0
 */
function epl_get_geocoordinates() {
	$address = $_POST['property_address_street_number'] . ' ' . $_POST['property_address_street'] . ' ' . $_POST['property_address_suburb'] . ' ' . $_POST['property_address_state'] . ' ' . $_POST['property_address_postal_code'];
	$address = urlencode(strtolower(trim($address)));
	$geourl = "http://maps.google.com/maps/api/geocode/json?address=". $address ."&sensor=false";
	$response = epl_remote_url_get($geourl);	
	$geocoordinates = $response[0]->geometry->location->lat . ',' . $response[0]->geometry->location->lng;
	echo $geocoordinates;
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
