<?php

//Variables List required for meta boxes
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
		'exclude'	=>	array('epl_rental', 'epl_holiday_rental')
	),
	'leased'		=>	array(
		'label'		=>	'Leased',
		'include'	=>	array('epl_rental', 'epl_holiday_rental', 'epl_commercial', 'epl_commercial_land', 'epl_business')
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
	'hectare'		=>	'Hectare'
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

$opts_land_category = array(
	'Commercial'	=>	'Commercial',
	'Residential'	=>	'Residential'
);

$opts_property_com_property_extent = array(
	'whole'	=>	'Whole',
	'part'	=>	'Part'
);

global $epl_meta_boxes;
$epl_meta_boxes = array(
	array(
		'id'		=>	'epl-suburb-section-id',
		'label'		=>	'Suburb Details',
		'post_type'	=>	'epl_suburb',
		'context'	=>	'normal',
		'priority'	=>	'high',
		'groups'	=>	array(
			array(
				'columns'	=>	'1',
				'label'		=>	'',
				'fields'	=>	array(
					array(
						'name'		=>	'suburb_staff_member',
						'label'		=>	'Staff Member',
						'type'		=>	'select',
						'opts'		=>	$opts_users,
						'default'	=> '-- Select One --'
					),
	
					array(
						'name'		=>	'suburb_name',
						'label'		=>	'Suburb Name',
						'type'		=>	'text'
					),
	
					array(
						'name'		=>	'suburb_video_url',
						'label'		=>	'Video Link',
						'type'		=>	'text'
					),
	
					array(
						'name'		=>	'suburb_map_location',
						'label'		=>	'Suburb Map Location',
						'type'		=>	'text'
					),
	
					array(
						'name'		=>	'suburb_postcode',
						'label'		=>	'Postcode',
						'type'		=>	'text',
						'help'		=>	'Suburb Postcode'
					),
	
					array(
						'name'		=>	'suburb_local_council',
						'label'		=>	'Local Council',
						'type'		=>	'text'
					),
	
					array(
						'name'		=>	'suburb_investors_are',
						'label'		=>	'Investors are',
						'type'		=>	'text'
					),
					
					array(
						'name'		=>	'suburb_agent_service',
						'label'		=>	'Agent Service',
						'type'		=>	'editor'
					),
					
					array(
						'name'		=>	'suburb_people_property',
						'label'		=>	'People & Property',
						'type'		=>	'editor'
					),
	
					array(
						'name'		=>	'suburb_location',
						'label'		=>	'Location',
						'type'		=>	'editor'
					),
	
					array(
						'name'		=>	'suburb_amenities',
						'label'		=>	'Amenities',
						'type'		=>	'editor'
					),
	
					array(
						'name'		=>	'suburb_recreation',
						'label'		=>	'Recreation',
						'type'		=>	'editor'
					),
	
					array(
						'name'		=>	'suburb_transport',
						'label'		=>	'Transport',
						'type'		=>	'editor'
					)
				)
			)
		)
	),
	
	array(
		'id'		=>	'epl-property-listing-section-id',
		'label'		=>	'Listing Details',
		'post_type'	=>	array('epl_property', 'epl_rural', 'epl_rental', 'epl_holiday_rental', 'epl_land', 'epl_commercial', 'epl_commercial_land', 'epl_business'),
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
						'type'		=>	'text'
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
						'type'		=>	'text'
					),
					
					array(
						'name'		=>	'property_second_agent',
						'label'		=>	'Second Listing Agent',
						'type'		=>	'text'
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
						'exclude'	=>	array('epl_rental', 'epl_commercial', 'epl_commercial_land')
					),
					
					array(
						'name'		=>	'property_category',
						'label'		=>	'House Category',
						'type'		=>	'select',
						'opts'		=>	$opts_property_category,
						'exclude'	=>	array('epl_land', 'epl_commercial', 'epl_commercial_land', 'epl_business', 'epl_rural')
					),
					
					array(
						'name'		=>	'property_holiday_category',
						'label'		=>	'Holiday Category',
						'type'		=>	'select',
						'opts'		=>	$opts_property_holiday_category,
						'include'	=>	array('epl_holiday_rental')
					),
					
					array(
						'name'		=>	'property_category',
						'label'		=>	'Rural Category',
						'type'		=>	'select',
						'opts'		=>	$opts_rural_category,
						'include'	=>	array('epl_rural')
					),
					
					array(
						'name'		=>	'property_availability_link',
						'label'		=>	'Availablity',
						'type'		=>	'text',
						'include'	=>	array('epl_holiday_rental')
					),
					
					array(
						'name'		=>	'property_unique_id',
						'label'		=>	'Unique ID',
						'type'		=>	'text'
					),
					
					array(
						'name'		=>	'property_mod_date',
						'label'		=>	'XML Importer Mod Date',
						'type'		=>	'text'
					),
					
					array(
						'name'		=>	'property_com_authority',
						'label'		=>	'Commercial Authority',
						'type'		=>	'select',
						'opts'		=>	$opts_property_com_authority,
						'include'	=>	array('epl_commercial', 'epl_commercial_land', 'epl_business')
					),
					
					array(
						'name'		=>	'property_exclusivity',
						'label'		=>	'Exclusivity',
						'type'		=>	'select',
						'opts'		=>	$opts_property_exclusivity,
						'include'	=>	array('epl_commercial', 'epl_commercial_land', 'epl_business')
					),
					
					array(
						'name'		=>	'property_com_listing_type',
						'label'		=>	'Commercial Listing Type',
						'type'		=>	'select',
						'opts'		=>	$opts_property_com_listing_type,
						'include'	=>	array('epl_commercial', 'epl_commercial_land')
					),
					
					array(
						'name'		=>	'property_commercial_category',
						'label'		=>	'Commercial Category',
						'type'		=>	'select',
						'opts'		=>	$opts_property_commercial_category,
						'include'	=>	array('epl_commercial', 'epl_commercial_land')
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
						'type'		=>	'text'
					)
					
				)
			)
		)
	),
	
	array(
		'id'		=>	'epl-features-section-id',
		'label'		=>	'Features',
		'post_type'	=>	array('epl_property', 'epl_rural', 'epl_rental', 'epl_holiday_rental'),
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
						'type'		=>	'number'
					),
					
					array(
						'name'		=>	'property_bathrooms',
						'label'		=>	'Bathrooms',
						'type'		=>	'number'
					),
					
					array(
						'name'		=>	'property_garage',
						'label'		=>	'Garage',
						'type'		=>	'number'
					),
					
					array(
						'name'		=>	'property_carport',
						'label'		=>	'Carport',
						'type'		=>	'number'
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
					),
					
					array(
						'name'		=>	'property_new_construction',
						'label'		=>	'New Construction',
						'type'		=>	'radio',
						'opts'	=>	array(
							'yes'	=>	'Yes',
							'no'	=>	'No'
						),
						'include'	=>	array('epl_property', 'epl_rental')
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
						'type'		=>	'decimal'
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
						'type'		=>	'number'
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
	
	array( //Repeating most from above "epl-features-section-id" because on land it will be single column
		'id'		=>	'epl-features-section-id',
		'label'		=>	'Land Details',
		'post_type'	=>	array('epl_land', 'epl_commercial'),
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
						'type'		=>	'decimal'
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
						'include'	=>	array('epl_commercial')
					),
					
					array(
						'name'		=>	'property_building_area_unit',
						'label'		=>	'Building Unit',
						'type'		=>	'select',
						'opts'		=>	$opts_area_unit,
						'include'	=>	array('epl_commercial')
					),
					
					array(
						'name'		=>	'property_land_category',
						'label'		=>	'Land Category',
						'type'		=>	'select',
						'opts'		=>	$opts_land_category,
						'include'	=>	array('epl_land')
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
		'post_type'	=>	array('epl_property', 'epl_rural', 'epl_rental', 'epl_holiday_rental', 'epl_commercial', 'epl_commercial_land', 'epl_business', 'epl_land'),
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
						'type'		=>	'text'
					),
					
					array(
						'name'		=>	'property_address_street',
						'label'		=>	'Street Name',
						'type'		=>	'text'
					),
					
					array(
						'name'		=>	'property_address_suburb',
						'label'		=>	'Suburb',
						'type'		=>	'text'
					),
					
					array(
						'name'		=>	'property_address_state',
						'label'		=>	'State',
						'type'		=>	'text'
					),
					
					array(
						'name'		=>	'property_com_display_suburb',
						'label'		=>	'Display Suburb',
						'type'		=>	'radio',
						'opts'		=>	array(
							'yes'	=>	'Yes',
							'no'	=>	'No'
						),
						'include'	=>	array('epl_commercial', 'epl_commercial_land', 'epl_business'),
					),
					
					array(
						'name'		=>	'property_address_postal_code',
						'label'		=>	'Postal Code',
						'type'		=>	'text'
					),
					
					array(
						'name'		=>	'property_address_coordinates',
						'label'		=>	'Coordinates',
						'type'		=>	'text',
						'geocoder'	=>	'true'
					)
				)
			)
		)
	),
	
	array(
		'id'		=>	'epl-pricing-section-id',
		'label'		=>	'Pricing',
		'post_type'	=>	array('epl_property', 'epl_rural', 'epl_commercial', 'epl_commercial_land', 'epl_business', 'epl_land'),
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
						'type'		=>	'number'
					),
					
					array(
						'name'		=>	'property_price_view',
						'label'		=>	'Price Text',
						'type'		=>	'text'
					),
					
					array(
						'name'		=>	'property_auction',
						'label'		=>	'Auction Date / Details',
						'type'		=>	'text'
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
						'exclude'	=>	array('epl_land', 'epl_rural', 'epl_commercial', 'epl_commercial_land')
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
						'type'		=>	'number'
					),
					
					array(
						'name'		=>	'property_sold_date',
						'label'		=>	'Sale Date : DATE',
						'type'		=>	'text'
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
		'post_type'	=>	array('epl_rental', 'epl_holiday_rental'),
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
						'type'		=>	'number'
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
						'type'		=>	'number'
					),
					
					array(
						'name'		=>	'property_date_available',
						'label'		=>	'Date Available',
						'type'		=>	'text'
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
						'exclude'	=>	array('epl_rental')
					),
				)
			)
		)
	),
	
	array(
		'id'		=>	'epl-rural-features-id',
		'label'		=>	'Rural Features',
		'post_type'	=>	array('epl_rural'),
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
						'type'		=>	'text'
					),
					
					array(
						'name'		=>	'property_rural_annual_rainfall',
						'label'		=>	'Annual Rainfall',
						'type'		=>	'text'
					),
					
					array(
						'name'		=>	'property_rural_soil_types',
						'label'		=>	'Soil Types',
						'type'		=>	'text'
					),
					
					array(
						'name'		=>	'property_rural_improvements',
						'label'		=>	'Improvements',
						'type'		=>	'text'
					),
					
					array(
						'name'		=>	'property_rural_council_rates',
						'label'		=>	'Council Rates',
						'type'		=>	'text'
					),
					
					array(
						'name'		=>	'property_rural_irrigation',
						'label'		=>	'Irrigation',
						'type'		=>	'text'
					),
					
					array(
						'name'		=>	'property_rural_carrying_capacity',
						'label'		=>	'Carrying Capacity',
						'type'		=>	'text'
					),
					
					array(
						'name'		=>	'property_rural_services',
						'label'		=>	'Services',
						'type'		=>	'text'
					)
				)
			)
		)
	),
	
	array(
		'id'		=>	'epl-attachments-section-id',
		'label'		=>	'Files and Links',
		'post_type'	=>	array('epl_property', 'epl_rural', 'epl_commercial', 'epl_commercial_land', 'epl_business', 'epl_rental', 'epl_holiday_rental', 'epl_land'),
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
						'name'		=>	'property_com_mini_web',
						'label'		=>	'Mini Website URL',
						'type'		=>	'url'
					),
					
					array(
						'name'		=>	'property_com_external_link',
						'label'		=>	'External Link',
						'type'		=>	'url'
					),
					
					array(
						'name'		=>	'property_com_external_link_1',
						'label'		=>	'External Link 2',
						'type'		=>	'url'
					),
					
					array(
						'name'		=>	'property_com_external_link_2',
						'label'		=>	'External Link 3',
						'type'		=>	'url'
					)					
				)
			)
		)
	),
	
	array(
		'id'		=>	'epl-commercial-leasing-id',
		'label'		=>	'Commercial Leasing',
		'post_type'	=>	array('epl_commercial', 'epl_commercial_land'),
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
						'type'		=>	'number'
					),
					
					array(
						'name'		=>	'property_com_rent_range_min',
						'label'		=>	'Rent Range Min',
						'type'		=>	'number'
					),
					
					array(
						'name'		=>	'property_com_rent_range_max',
						'label'		=>	'Rent Range Max',
						'type'		=>	'number'
					),
					
					array(
						'name'		=>	'property_com_lease_end_date',
						'label'		=>	'Lease End Date',
						'type'		=>	'date'
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
						'exclude'	=>	array('epl_commercial_land')
					),
					
					array(
						'name'		=>	'property_com_outgoings',
						'label'		=>	'Commercial Outgoings',
						'type'		=>	'number'
					),
					
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
						'type'		=>	'decimal'
					)
				)
			)
		)
	),
	
	array(
		'id'		=>	'epl-commercial-features-id',
		'label'		=>	'Commercial Features',
		'post_type'	=>	array('epl_commercial'),
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
						'type'		=>	'textarea'
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
						'type'		=>	'number'
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
						'type'		=>	'text'
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
	)
);

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
									if(!empty($field['exclude'])) {
										if( in_array($post->post_type, $field['exclude']) ) {
											continue;
										}
									}
									
									if(!empty($field['include'])) {
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
														echo '<select name="'.$field['name'].'" id="'.$field['name'].'">';
															if(!empty($field['default'])) {
																echo '<option value="" selected="selected">'.__($field['default'], 'epl').'</option>';
															}
										
															if(!empty($field['opts'])) {
																foreach($field['opts'] as $k=>$v) {
																	$selected = '';
																	if($val == $k) {
																		$selected = 'selected="selected"';
																	}
																	
																	if(is_array($v)) {
																		if(!empty($v['exclude'])) {
																			if( in_array($post->post_type, $v['exclude']) ) {
																				continue;
																			}
																		} else if(!empty($v['include'])) {
																			if( !in_array($post->post_type, $v['include']) ) {
																				continue;
																			}
																		}
																		$v = $v['label'];
																	}
																	
																	echo '<option value="'.$k.'" '.$selected.'>'.__($v, 'epl').'</option>';
																}
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
	
	$epl_meta_box_ids = $_POST['epl_meta_box_ids'];
	if(!empty($epl_meta_box_ids)) {
		global $epl_meta_boxes;
		if(!empty($epl_meta_boxes)) {
			$epl_enable_import_geocode = get_option('epl_enable_import_geocode');
			foreach($epl_meta_box_ids as $epl_meta_box_id) {
				foreach($epl_meta_boxes as $epl_meta_box) {
					if($epl_meta_box['id'] == $epl_meta_box_id) {
						if(!empty($epl_meta_box['groups'])) {
							foreach($epl_meta_box['groups'] as $group) {
								if(!empty($group['fields'])) {
									foreach($group['fields'] as $field) {
										/* if( isset($field['geocoder']) && $field['geocoder'] == 'true' ) {
											if($epl_enable_import_geocode == 1 || $epl_enable_import_geocode == 'yes') {
												$address = $_POST['property_address_street_number'] . ' ' . $_POST['property_address_street'] . ' ' . $_POST['property_address_suburb'] . ' ' . $_POST['property_address_state'] . ' ' . $_POST['property_address_postal_code'];
												$address = urlencode(strtolower(trim($address)));
												$geourl = "http://maps.google.com/maps/api/geocode/json?address=". $address ."&sensor=false";
												$response = epl_remote_url_get($geourl);												
												$_POST[ $field['name'] ] = $response[0]->geometry->location->lat . ',' . $response[0]->geometry->location->lng;
											}
										} */
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
		});
	</script>
<?php }
add_action( 'admin_head', 'epl_admin_head_scripts' );

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
