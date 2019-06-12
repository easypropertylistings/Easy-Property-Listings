<?php
/**
 * Register custom meta fields for property post types
 *
 * @package     EPL
 * @subpackage  Meta
 * @copyright   Copyright (c) 2019, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Get meta boxes
 *
 * @since 1.0
 */
function epl_get_meta_boxes() {

	global $epl_settings;

	$opts_property_status 			= epl_get_property_status_opts();
	$opts_property_authority 		= epl_get_property_authority_opts();
	$opts_property_exclusivity 		= epl_get_property_exclusivity_opts();
	$opts_property_com_authority 		= epl_get_property_com_authority_opts();
	$opts_area_unit 			= epl_get_property_area_unit_opts();
	$opts_rent_period 			= epl_get_property_rent_period_opts();
	$opts_property_com_listing_type 	= epl_get_property_com_listing_type_opts();
	$opts_property_com_tenancy 		= epl_get_property_com_tenancy_opts();
	$opts_property_com_property_extent 	= epl_get_property_com_property_extent_opts();

	$epl_meta_boxes = array(

		array(
			'id'		=>	'epl-property-listing-section-id',
			'label'		=>	__('Listing Details', 'easy-property-listings' ),
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
							'label'		=>	__('Heading', 'easy-property-listings' ),
							'type'		=>	'text',
							'maxlength'	=>	'200',
							'class'		=>	'epl-property-heading'
						)
					)
				),

				array(
					'id'		=>	'listing_agents',
					'columns'	=>	'1',
					'label'		=>	__('Listing Agent(s)', 'easy-property-listings' ),
					'fields'	=>	array(
						array(
							'name'		=>	'property_office_id',
							'label'		=>	__('Office ID', 'easy-property-listings' ),
							'type'		=>	'text',
							'maxlength'	=>	'50',
							'class'		=>	'epl-property-office-id'
						),

						array(
							'name'		=>	'property_agent',
							'label'		=>	__('Listing Agent', 'easy-property-listings' ),
							'type'		=>	'text',
							'maxlength'	=>	'40',
							'help'		=>	__('Search for primary agent.','easy-property-listings' ),
							'class'		=>	'epl-property-agent'
						),

						array(
							'name'		=>	'property_second_agent',
							'label'		=>	__('Second Listing Agent', 'easy-property-listings' ),
							'type'		=>	'text',
							'maxlength'	=>	'40',
							'help'		=>	__('Search for secondary agent.','easy-property-listings' ),
							'class'		=>	'epl-property-second-agent'
						),

						array(
							'name'		=>	'property_agent_hide_author_box',
							'label'		=>	__('Hide Author Box', 'easy-property-listings' ),
							'type'		=>	'checkbox_single',
							'opts'		=>	array(
								'yes'	=>	__('Hide Author Box', 'easy-property-listings' ),
							),
							'class'		=>	'epl-property-agent-hide-author-box'
						)
					)
				),

				array(
					'id'		=>	'listing_type',
					'columns'	=>	'2',
					'label'		=>	__('Listing Type', 'easy-property-listings' ),
					'width'		=>	'2',
					'fields'	=>	array(
						array(
							'name'		=>	'property_status',
							'label'		=>	__('Property Status', 'easy-property-listings' ),
							'type'		=>	'select',
							'opts'		=>	$opts_property_status
						),

						array(
							'name'		=>	'property_list_date',
							'label'		=>	__('Date Listed', 'easy-property-listings' ),
							'type'		=>	'date',
							'maxlength'	=>	'100'
						),

						array(
							'name'		=>	'property_authority',
							'label'		=>	__('Authority', 'easy-property-listings' ),
							'type'		=>	'select',
							'opts'		=>	$opts_property_authority,
							'exclude'	=>	array('rental', 'commercial', 'commercial_land')
						),

												array(
							'name'		=>	'property_unique_id',
							'label'		=>	__('Unique ID', 'easy-property-listings' ),
							'type'		=>	'text',
							'maxlength'	=>	'50'
						),

						array(
							'name'		=>	'property_category',
							'label'		=>	__('House Category', 'easy-property-listings' ),
							'type'		=>	'select',
							'opts'		=>	epl_listing_load_meta_property_category(),
							'exclude'	=>	array('land', 'commercial', 'commercial_land', 'business', 'rural')
						),

						array(
							'name'		=>	'property_rural_category',
							'label'		=>	__('Rural Category', 'easy-property-listings' ),
							'type'		=>	'select',
							'opts'		=>	epl_listing_load_meta_rural_category(),
							'include'	=>	array('rural')
						),

						array(
							'name'		=>	'property_mod_date',
							'label'		=>	__('XML Importer Mod Date', 'easy-property-listings' ),
							'type'		=>	'text',
							'maxlength'	=>	'60'
						),

						array(
							'name'		=>	'property_images_mod_date',
							'label'		=>	__('Image Modified Date', 'easy-property-listings' ),
							'type'		=>	'hidden',
							'maxlength'	=>	'60'
						),

						array(
							'name'		=>	'property_com_authority',
							'label'		=>	__('Commercial Authority', 'easy-property-listings' ),
							'type'		=>	'select',
							'opts'		=>	$opts_property_com_authority,
							'include'	=>	array('commercial', 'commercial_land', 'business')
						),

						array(
							'name'		=>	'property_com_exclusivity',
							'label'		=>	__('Exclusivity', 'easy-property-listings' ),
							'type'		=>	'select',
							'opts'		=>	$opts_property_exclusivity,
							'include'	=>	array('commercial', 'commercial_land', 'business')
						),

						array(
							'name'		=>	'property_com_listing_type',
							'label'		=>	__('Commercial Listing Type', 'easy-property-listings' ),
							'type'		=>	'select',
							'opts'		=>	$opts_property_com_listing_type,
							'include'	=>	array('commercial', 'commercial_land' , 'business' )
						),

						array(
							'name'		=>	'property_commercial_category',
							'label'		=>	__('Commercial Category', 'easy-property-listings' ),
							'type'		=>	'select',
							'opts'		=>	epl_listing_load_meta_commercial_category(),
							'include'	=>	array('commercial', 'commercial_land')
						),
					)
				),

				array(
					'id'		=>	'display_details',
					'columns'	=>	'2',
					'label'		=>	__('Display Details', 'easy-property-listings' ),
					'fields'	=>	array(

						array(
							'name'		=>	'property_inspection_times',
							'label'		=>	__('Inspection Times ( one per line )', 'easy-property-listings' ),
							'type'		=>	'textarea',
							'maxlength'	=>	'500'
						),

						array(
							'name'		=>	'property_featured',
							'label'		=>	__('Featured Listing', 'easy-property-listings' ),
							'type'		=>	'checkbox_single',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'easy-property-listings' ),
							),
						)
					)
				)
			)
		),

		array(
			'id'		=>	'epl-features-section-id',
			'label'		=>	__('Listing Features', 'easy-property-listings' ),
			'post_type'	=>	array('property', 'rural', 'rental' ),
			'context'	=>	'normal',
			'priority'	=>	'default',
			'groups'	=>	array(
				array(
					'id'		=>	'house_features',
					'columns'	=>	'2',
					'width'		=>	'2', // default for this group
					'label'		=>	__('House Features', 'easy-property-listings' ),
					'fields'	=>	array(
						array(
							'name'		=>	'property_bedrooms',
							'label'		=>	__('Bedrooms', 'easy-property-listings' ),
							'type'		=>	'text',
							'class'		=>	'validate[custom[bedroom]]',
							'width'		=>	'2', // override width for this field
						),

						array(
							'name'		=>	'property_bathrooms',
							'label'		=>	__('Bathrooms', 'easy-property-listings' ),
							'type'		=>	'decimal',
							'maxlength'	=>	'4'
						),

						array(
							'name'		=>	'property_toilet',
							'label'		=>	__('Toilet', 'easy-property-listings' ),
							'type'		=>	'decimal',
							'maxlength'	=>	'4'
						),

						array(
							'name'		=>	'property_ensuite',
							'label'		=>	__('Ensuite', 'easy-property-listings' ),
							'type'		=>	'number',
							'maxlength'	=>	'2'
						),

						array(
							'name'		=>	'property_garage',
							'label'		=>	__('Garage', 'easy-property-listings' ),
							'type'		=>	'number',
							'maxlength'	=>	'2'
						),

						array(
							'name'		=>	'property_carport',
							'label'		=>	__('Carport', 'easy-property-listings' ),
							'type'		=>	'number',
							'maxlength'	=>	'2'
						),

						array(
							'name'		=>	'property_open_spaces',
							'label'		=>	__('Open Parking Spaces', 'easy-property-listings' ),
							'type'		=>	'number',
							'maxlength'	=>	'2'
						),

						array(
							'name'		=>	'property_rooms',
							'label'		=>	__('Rooms', 'easy-property-listings' ),
							'type'		=>	'number',
							'maxlength'	=>	'3'
						),

						array(
							'name'		=>	'property_year_built',
							'label'		=>	__('Year Built', 'easy-property-listings' ),
							'type'		=>	'text',
							'maxlength'	=>	'4',
							'width'		=>	'1'
						),

						array(
							'name'		=>	'property_new_construction',
							'label'		=>	__('New Construction', 'easy-property-listings' ),
							'type'		=>	'checkbox_single',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'easy-property-listings' ),
							),
							'include'	=>	array('property', 'rental' )
						),

						array(
							'name'		=>	'property_pool',
							'label'		=>	__('Pool', 'easy-property-listings' ),
							'type'		=>	'checkbox_single',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'easy-property-listings' ),
							),
						),

						array(
							'name'		=>	'property_air_conditioning',
							'label'		=>	__('Air Conditioning', 'easy-property-listings' ),
							'type'		=>	'checkbox_single',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'easy-property-listings' ),
							),
						),

						array(
							'name'		=>	'property_security_system',
							'label'		=>	__('Security System', 'easy-property-listings' ),
							'type'		=>	'checkbox_single',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'easy-property-listings' ),
							),
						),

						array(
							'name'		=>	'property_pet_friendly',
							'label'		=>	__('Pet Friendly', 'easy-property-listings' ),
							'type'		=>	'checkbox_single',
							'include'	=>	array('rental'),
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'easy-property-listings' ),
							),
						)
					)
				),

				array(
					'id'		=>	'land_details',
					'columns'	=>	'2',
					'label'		=>	__('Land Details', 'easy-property-listings' ),
					'fields'	=>	array(
						array(
							'name'		=>	'property_land_area',
							'label'		=>	__('Land Area', 'easy-property-listings' ),
							'type'		=>	'decimal',
							'maxlength'	=>	'50',
							'width'		=>	'2-3'
						),

						array(
							'name'		=>	'property_land_area_unit',
							'label'		=>	__('Land Unit', 'easy-property-listings' ),
							'type'		=>	'select',
							'opts'		=>	$opts_area_unit,
							'width'		=>	'3'
						),

						array(
							'name'		=>	'property_building_area',
							'label'		=>	__('Building Area', 'easy-property-listings' ),
							'type'		=>	'decimal',
							'maxlength'	=>	'50',
							'width'		=>	'2-3'
						),

						array(
							'name'		=>	'property_building_area_unit',
							'label'		=>	__('Building Unit', 'easy-property-listings' ),
							'type'		=>	'select',
							'opts'		=>	$opts_area_unit,
							'width'		=>	'3'
						),

						array(
							'name'		=>	'property_energy_rating',
							'label'		=>	__('Energy Rating', 'easy-property-listings' ),
							'type'		=>	'text',
							'maxlength'	=>	'10',
							'exclude'	=>	array( 'land' , 'commercial_land')
						),

						array(
							'name'		=>	'property_land_fully_fenced',
							'label'		=>	__('Fully Fenced', 'easy-property-listings' ),
							'type'		=>	'checkbox_single',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'easy-property-listings' ),
							),
						)
					)
				)
			)
		),

		array( // Additional Features
			'id'		=>	'epl-additional-features-section-id',
			'label'		=>	__('Additional Features', 'easy-property-listings' ),
			'post_type'	=>	array('property', 'rural', 'rental' ),
			'context'	=>	'normal',
			'priority'	=>	'default',
			'groups'	=>	array(
				array(
					'id'		=>	'internal',
					'columns'	=>	'3',
					'label'		=>	__('Internal', 'easy-property-listings' ),
					'fields'	=>	array(

						array(
							'name'		=>	'property_remote_garage',
							'label'		=>	__('Remote Garage', 'easy-property-listings' ),
							'type'		=>	'checkbox_single',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'easy-property-listings' ),
							),
						),

						array(
							'name'		=>	'property_secure_parking',
							'label'		=>	__('Secure Parking', 'easy-property-listings' ),
							'type'		=>	'checkbox_single',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'easy-property-listings' ),
							),
						),

						array(
							'name'		=>	'property_study',
							'label'		=>	__('Study', 'easy-property-listings' ),
							'type'		=>	'checkbox_single',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'easy-property-listings' ),
							),
						),

						array(
							'name'		=>	'property_dishwasher',
							'label'		=>	__('Dishwasher', 'easy-property-listings' ),
							'type'		=>	'checkbox_single',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'easy-property-listings' ),
							),
						),

						array(
							'name'		=>	'property_built_in_robes',
							'label'		=>	__('Built In Robes', 'easy-property-listings' ),
							'type'		=>	'checkbox_single',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'easy-property-listings' ),
							),
						),

						array(
							'name'		=>	'property_gym',
							'label'		=>	__('Gym', 'easy-property-listings' ),
							'type'		=>	'checkbox_single',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'easy-property-listings' ),
							),
						),

						array(
							'name'		=>	'property_workshop',
							'label'		=>	__('Workshop', 'easy-property-listings' ),
							'type'		=>	'checkbox_single',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'easy-property-listings' ),
							),
						),

						array(
							'name'		=>	'property_rumpus_room',
							'label'		=>	__('Rumpus Room', 'easy-property-listings' ),
							'type'		=>	'checkbox_single',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'easy-property-listings' ),
							),
						),

						array(
							'name'		=>	'property_floor_boards',
							'label'		=>	__('Floor Boards', 'easy-property-listings' ),
							'type'		=>	'checkbox_single',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'easy-property-listings' ),
							),
						),

						array(
							'name'		=>	'property_broadband',
							'label'		=>	__('Broadband', 'easy-property-listings' ),
							'type'		=>	'checkbox_single',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'easy-property-listings' ),
							),
						),

						array(
							'name'		=>	'property_pay_tv',
							'label'		=>	__('Pay TV', 'easy-property-listings' ),
							'type'		=>	'checkbox_single',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'easy-property-listings' ),
							),
						),
						array(
							'name'		=>	'property_vacuum_system',
							'label'		=>	__('Vacuum System', 'easy-property-listings' ),
							'type'		=>	'checkbox_single',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'easy-property-listings' ),
							),
						),

						array(
							'name'		=>	'property_intercom',
							'label'		=>	__('Intercom', 'easy-property-listings' ),
							'type'		=>	'checkbox_single',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'easy-property-listings' ),
							),
						),

						array(
							'name'		=>	'property_spa',
							'label'		=>	__('Spa', 'easy-property-listings' ),
							'type'		=>	'checkbox_single',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'easy-property-listings' ),
							),
						)
					)
				),

				array(
					'id'		=>	'external',
					'columns'	=>	'3',
					'label'		=>	__('External', 'easy-property-listings' ),
					'fields'	=>	array(

						array(
							'name'		=>	'property_tennis_court',
							'label'		=>	__('Tennis Court', 'easy-property-listings' ),
							'type'		=>	'checkbox_single',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'easy-property-listings' ),
							),
						),

						array(
							'name'		=>	'property_balcony',
							'label'		=>	__('Balcony', 'easy-property-listings' ),
							'type'		=>	'checkbox_single',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'easy-property-listings' ),
							),
						),

						array(
							'name'		=>	'property_deck',
							'label'		=>	__('Deck', 'easy-property-listings' ),
							'type'		=>	'checkbox_single',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'easy-property-listings' ),
							),
						),

						array(
							'name'		=>	'property_courtyard',
							'label'		=>	__('Courtyard', 'easy-property-listings' ),
							'type'		=>	'checkbox_single',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'easy-property-listings' ),
							),
						),

						array(
							'name'		=>	'property_outdoor_entertaining',
							'label'		=>	__('Outdoor Entertaining', 'easy-property-listings' ),
							'type'		=>	'checkbox_single',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'easy-property-listings' ),
							),
						),

						array(
							'name'		=>	'property_shed',
							'label'		=>	__('Shed', 'easy-property-listings' ),
							'type'		=>	'checkbox_single',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'easy-property-listings' ),
							),
						)
					)
				),

				array(
					'id'		=>	'heating_cooling',
					'columns'	=>	'3',
					'label'		=>	__('Heating & Cooling', 'easy-property-listings' ),
					'fields'	=>	array(

						array(
							'name'		=>	'property_ducted_heating',
							'label'		=>	__('Ducted Heating', 'easy-property-listings' ),
							'type'		=>	'checkbox_single',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'easy-property-listings' ),
							),
						),

						array(
							'name'		=>	'property_ducted_cooling',
							'label'		=>	__('Ducted Cooling', 'easy-property-listings' ),
							'type'		=>	'checkbox_single',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'easy-property-listings' ),
							),
						),

						array(
							'name'		=>	'property_split_system_heating',
							'label'		=>	__('Split System Heating', 'easy-property-listings' ),
							'type'		=>	'checkbox_single',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'easy-property-listings' ),
							),
						),

						array(
							'name'		=>	'property_hydronic_heating',
							'label'		=>	__('Hydronic Heating', 'easy-property-listings' ),
							'type'		=>	'checkbox_single',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'easy-property-listings' ),
							),
						),

						array(
							'name'		=>	'property_split_system_aircon',
							'label'		=>	__('Split System Aircon', 'easy-property-listings' ),
							'type'		=>	'checkbox_single',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'easy-property-listings' ),
							),
						),

						array(
							'name'		=>	'property_gas_heating',
							'label'		=>	__('Gas Heating', 'easy-property-listings' ),
							'type'		=>	'checkbox_single',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'easy-property-listings' ),
							),
						),

						array(
							'name'		=>	'property_reverse_cycle_aircon',
							'label'		=>	__('Reverse Cycle Aircon', 'easy-property-listings' ),
							'type'		=>	'checkbox_single',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'easy-property-listings' ),
							),
						),

						array(
							'name'		=>	'property_evaporative_cooling',
							'label'		=>	__('Evaporative Cooling', 'easy-property-listings' ),
							'type'		=>	'checkbox_single',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'easy-property-listings' ),
							),
						),

						array(
							'name'		=>	'property_open_fire_place',
							'label'		=>	__('Open Fire Place', 'easy-property-listings' ),
							'type'		=>	'checkbox_single',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'easy-property-listings' ),
							),
						)
					)
				)
			)
		),

		array( //Repeating most from above "epl-features-section-id" because on land it will be single column
			'id'		=>	'epl-features-section-id-single-column',
			'label'		=>	__('Land Details', 'easy-property-listings' ),
			'post_type'	=>	array('land', 'commercial', 'commercial_land' , 'business'),
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
							'label'		=>	__('Land Area', 'easy-property-listings' ),
							'type'		=>	'decimal',
							'maxlength'	=>	'50'
						),

						array(
							'name'		=>	'property_land_area_unit',
							'label'		=>	__('Land Unit', 'easy-property-listings' ),
							'type'		=>	'select',
							'opts'		=>	$opts_area_unit
						),

						array(
							'name'		=>	'property_building_area',
							'label'		=>	__('Building Area', 'easy-property-listings' ),
							'type'		=>	'decimal',
							'include'	=>	array('commercial','business'),
							'maxlength'	=>	'40'
						),

						array(
							'name'		=>	'property_building_area_unit',
							'label'		=>	__('Building Unit', 'easy-property-listings' ),
							'type'		=>	'select',
							'opts'		=>	$opts_area_unit,
							'include'	=>	array('commercial','business')
						),

						array(
							'name'		=>	'property_energy_rating',
							'label'		=>	__('Energy Rating', 'easy-property-listings' ),
							'type'		=>	'decimal',
							'maxlength'	=>	'6'
						),

						array(
							'name'		=>	'property_land_category',
							'label'		=>	__('Land Category', 'easy-property-listings' ),
							'type'		=>	'select',
							'opts'		=>	epl_listing_load_meta_land_category(),
							'include'	=>	array('land')
						),

						array(
							'name'		=>	'property_land_fully_fenced',
							'label'		=>	__('Fully Fenced', 'easy-property-listings' ),
							'type'		=>	'checkbox_single',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'easy-property-listings' ),
							),
							'include'	=>	array('land')
						)
					)
				)
			)
		),

		array(
			'id'		=>	'epl-property-address-section-id',
			'label'		=>	__('Property Address', 'easy-property-listings' ),
			'post_type'	=>	array('property', 'rural', 'rental', 'commercial', 'commercial_land', 'business', 'land','contact_listing'),
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
								'label'		=>	__('Display Street Address?', 'easy-property-listings' ),
								'type'		=>	'checkbox_single',
								'opts'		=>	array(
									'yes'	=>	__('Yes', 'easy-property-listings' ),
								),
							),

							array(
								'name'		=>	'property_address_lot_number',
								'label'		=>	__('Lot', 'easy-property-listings' ),
								'type'		=>	'text',
								'maxlength'	=>	'40',
								'include'	=>	array('land', 'commercial_land')
							),

							array(
								'name'		=>	'property_address_sub_number',
								'label'		=>	__('Unit', 'easy-property-listings' ),
								'type'		=>	'text',
								'maxlength'	=>	'40',
								'exclude'	=>	array('land', 'commercial_land')
							),

							array(
								'name'		=>	'property_address_street_number',
								'label'		=>	__('Street Number', 'easy-property-listings' ),
								'type'		=>	'text',
								'maxlength'	=>	'40'
							),

							array(
								'name'		=>	'property_address_street',
								'label'		=>	__('Street Name', 'easy-property-listings' ),
								'type'		=>	'text',
								'maxlength'	=>	'80'
							),

							array(
								'name'		=>	'property_address_suburb',
								'label'		=>	epl_labels('label_suburb'),
								'type'		=>	'text',
								'maxlength'	=>	'80'
							),

							array(
								'name'		=>	'property_com_display_suburb',
								'label'		=>	__('Display', 'easy-property-listings' ) . ' ' .epl_labels('label_suburb'),
								'type'		=>	'checkbox_single',
								'opts'		=>	array(
									'yes'	=>	__('Yes', 'easy-property-listings' ),
								),
								'include'	=>	array('commercial', 'commercial_land', 'business'),
							),

							( isset($epl_settings['epl_enable_city_field'] ) &&  $epl_settings['epl_enable_city_field'] == 'yes' ) ?
							array(
								'name'		=>	'property_address_city',
								'label'		=>	epl_labels('label_city'),
								'type'		=>	'text',
								'maxlength'	=>	'80'
							) : array(),

							array(
								'name'		=>	'property_address_state',
								'label'		=>	epl_labels('label_state'),
								'type'		=>	'text',
								'maxlength'	=>	'80'
							),

							array(
								'name'		=>	'property_address_postal_code',
								'label'		=>	epl_labels('label_postcode'),
								'type'		=>	'text',
								'maxlength'	=>	'30'
							),

							array(
								'name'		=>	'property_address_country',
								'label'		=>	__('Country', 'easy-property-listings' ),
								'type'		=>	'text',
								'maxlength'	=>	'40'
							),

							array(
								'name'		=>	'property_address_coordinates',
								'label'		=>	__('Coordinates', 'easy-property-listings' ),
								'type'		=>	'text',
								'help'		=>	__('Drag the pin to manually set listing coordinates', 'easy-property-listings' ),
								'geocoder'	=>	'true',
								'maxlength'	=>	'40'
							),
							array(
								'name'		=>	'property_address_hide_map',
								'label'		=>	__('Hide Map', 'easy-property-listings' ),
								'type'		=>	'checkbox_single',
								'opts'		=>	array(
									'yes'	=>	__('Yes', 'easy-property-listings' ),
								)
							)
						)
					)
				)
			)
		),

		array(
			'id'		=>	'epl-pricing-section-id',
			'label'		=>	__('Pricing', 'easy-property-listings' ),
			'post_type'	=>	array('property', 'rural', 'commercial', 'commercial_land', 'business', 'land'),
			'context'	=>	'side',
			'priority'	=>	'core',
			'groups'		=>	array(
				array(
					'id'		=>	'pricing',
					'columns'	=>	'1',
					'label'		=>	'',
					'fields'		=>	array(

						array(
							'name'		=>	'property_price_global',
							'label'		=>	__('Global Price', 'easy-property-listings' ),
							'type'		=>	'hidden',
							'maxlength'	=>	'50'
						),

						array(
							'name'		=>	'property_price',
							'label'		=>	__('Search Price', 'easy-property-listings' ),
							'type'		=>	apply_filters('epl_price_number_format','decimal'),
							'maxlength'	=>	'50'
						),

						array(
							'name'		=>	'property_price_view',
							'label'		=>	__('Price Text', 'easy-property-listings' ),
							'type'		=>	'text',
							'maxlength'	=>	'50'
						),

						array(
							'name'		=>	'property_auction',
							'label'		=>	__('Auction Date', 'easy-property-listings' ),
							'type'		=>	'auction-date',
							'maxlength'	=>	'100'
						),

						array(
							'name'		=>	'property_price_display',
							'label'		=>	__('Display Price?', 'easy-property-listings' ),
							'type'		=>	'checkbox_single',
							'opts'		=>	array(
								'yes'	=>	__('Yes', 'easy-property-listings' ),
							),
						),

						array(
							'name'		=>	'property_under_offer',
							'label'		=>	epl_meta_under_offer_label(),
							'type'		=>	'checkbox_single',
							'opts'		=>	array(
								'yes'	=>	__('Yes', 'easy-property-listings' ),
							),
						),

						array(
							'name'		=>	'property_is_home_land_package',
							'label'		=>	__('House and Land Package', 'easy-property-listings' ),
							'type'		=>	'checkbox_single',
							'opts'		=>	array(
								'yes'	=>	__('Yes', 'easy-property-listings' ),
							),
							'exclude'	=>	array('land', 'rural', 'commercial', 'commercial_land' , 'business')
						)
					)
				),

				array(
					'id'		=>	'sale_details',
					'columns'	=>	'1',
					'label'		=>	__('Sale Details', 'easy-property-listings' ),
					'fields'		=>	array(
						array(
							'name'		=>	'property_sold_price',
							'label'		=>	__('Sale Price', 'easy-property-listings' ),
							'type'		=>	apply_filters('epl_price_number_format','decimal'),
							'maxlength'	=>	'50'
						),

						array(
							'name'		=>	'property_sold_date',
							'label'		=>	__('Sale Date', 'easy-property-listings' ),
							'type'		=>	'sold-date',
							'maxlength'	=>	'100'
						),

						array(
							'name'		=>	'property_sold_price_display',
							'label'		=>	__('Display Sale Price', 'easy-property-listings' ),
							'type'		=>	'checkbox_single',
							'opts'		=>	array(
								'yes'	=>	__('Yes', 'easy-property-listings' ),
							),
						)
					)
				)
			)
		),

		array(
			'id'		=>	'epl-property-rent-id',
			'label'		=>	__('Rental Pricing', 'easy-property-listings' ),
			'post_type'	=>	array('rental'),
			'context'	=>	'side',
			'priority'	=>	'core',
			'groups'		=>	array(
				array(
					'id'		=>	'rental_pricing',
					'columns'	=>	'1',
					'label'		=>	'',
					'fields'		=>	array(

						array(
							'name'		=>	'property_price_global',
							'label'		=>	__('Global Price', 'easy-property-listings' ),
							'type'		=>	'hidden',
							'maxlength'	=>	'50'
						),

						array(
							'name'		=>	'property_rent',
							'label'		=>	__('Rent Amount', 'easy-property-listings' ),
							'type'		=>	apply_filters('epl_price_number_format','decimal'),
							'maxlength'	=>	'40'
						),

						array(
							'name'		=>	'property_rent_period',
							'label'		=>	__('Rent Period', 'easy-property-listings' ),
							'type'		=>	'select',
							'opts'		=>	$opts_rent_period
						),

						array(
							'name'		=>	'property_rent_view',
							'label'		=>	__('Rent Text', 'easy-property-listings' ),
							'type'		=>	'text',
							'maxlength'	=>	'50'
						),

						array(
							'name'		=>	'property_rent_display',
							'label'		=>	__('Display Rent?', 'easy-property-listings' ),
							'type'		=>	'checkbox_single',
							'opts'		=>	array(
								'yes'	=>	__('Yes', 'easy-property-listings' ),
							),
						),

						array(
							'name'		=>	'property_bond',
							'label'		=>	epl_labels('label_bond'),
							'type'		=>	apply_filters('epl_bond_number_format','decimal'),
							'maxlength'	=>	'40'
						),

						array(
							'name'		=>	'property_date_available',
							'label'		=>	__('Date Available', 'easy-property-listings' ),
							'type'		=>	'date',
							'maxlength'	=>	'100'
						),

						array(
							'name'		=>	'property_date_leased',
							'label'		=>	__('Leased Date', 'easy-property-listings' ),
							'type'		=>	'date',
							'maxlength'	=>	'100'
						),

						array(
							'name'		=>	'property_furnished',
							'label'		=>	__('Furnished', 'easy-property-listings' ),
							'type'		=>	'checkbox_single',
							'opts'		=>	array(
								'yes'	=>	__('Yes', 'easy-property-listings' ),
							),
						),

						array(
							'name'		=>	'property_holiday_rental',
							'label'		=>	__('Holiday Rental', 'easy-property-listings' ),
							'type'		=>	'checkbox_single',
							'opts'		=>	array(
								'yes'	=>	__('Yes', 'easy-property-listings' ),
							),
							'exclude'	=>	array('rental')
						),
					)
				)
			)
		),

		array(
			'id'		=>	'epl-rural-features-id',
			'label'		=>	__('Rural Features', 'easy-property-listings' ),
			'post_type'	=>	array('rural'),
			'context'	=>	'normal',
			'priority'	=>	'default',
			'groups'		=>	array(
				array(
					'id'		=>	'rural_features',
					'columns'	=>	'1',
					'label'		=>	'',
					'fields'	=>	array(
						array(
							'name'		=>	'property_rural_fencing',
							'label'		=>	__('Fencing', 'easy-property-listings' ),
							'type'		=>	'textarea',
							'maxlength'	=>	'65535'
						),

						array(
							'name'		=>	'property_rural_annual_rainfall',
							'label'		=>	__('Annual Rainfall', 'easy-property-listings' ),
							'type'		=>	'textarea',
							'maxlength'	=>	'65535'
						),

						array(
							'name'		=>	'property_rural_soil_types',
							'label'		=>	__('Soil Types', 'easy-property-listings' ),
							'type'		=>	'textarea',
							'maxlength'	=>	'65535'
						),

						array(
							'name'		=>	'property_rural_improvements',
							'label'		=>	__('Improvements', 'easy-property-listings' ),
							'type'		=>	'textarea',
							'maxlength'	=>	'65535'
						),

						array(
							'name'		=>	'property_rural_council_rates',
							'label'		=>	__('Council Rates', 'easy-property-listings' ),
							'type'		=>	'textarea',
							'maxlength'	=>	'65535'
						),

						array(
							'name'		=>	'property_rural_irrigation',
							'label'		=>	__('Irrigation', 'easy-property-listings' ),
							'type'		=>	'textarea',
							'maxlength'	=>	'65535'
						),

						array(
							'name'		=>	'property_rural_carrying_capacity',
							'label'		=>	__('Carrying Capacity', 'easy-property-listings' ),
							'type'		=>	'textarea',
							'maxlength'	=>	'65535'
						),

						array(
							'name'		=>	'property_rural_services',
							'label'		=>	__('Services', 'easy-property-listings' ),
							'type'		=>	'textarea',
							'maxlength'	=>	'65535'
						)
					)
				)
			)
		),

		array(
			'id'		=>	'epl-commercial-leasing-id',
			'label'		=>	__('Leasing', 'easy-property-listings' ),
			'post_type'	=>	array('commercial', 'commercial_land' , 'business'),
			'context'	=>	'normal',
			'priority'	=>	'default',
			'groups'		=>	array(
				array(
					'id'		=>	'commercial_leasing',
					'columns'	=>	'2',
					'label'		=>	'',
					'fields'	=>	array(
						array(
							'name'		=>	'property_com_rent',
							'label'		=>	__('Commercial Rent', 'easy-property-listings' ),
							'type'		=>	'decimal',
							'maxlength'	=>	'40',
							'help'		=>	__('Price Text in Pricing box over-rides displayed price' , 'easy-property-listings' ) . '. <br>' .
										__('Select Display Price to show.' , 'easy-property-listings' )
						),
						array(
							'name'		=>	'property_com_rent_period',
							'label'		=>	__('Lease Period', 'easy-property-listings' ),
							'type'		=>	'select',
							'opts'		=>	epl_listing_load_meta_commercial_rent_period()
						),
						array(
							'name'		=>	'property_com_rent_range_min',
							'label'		=>	__('Rent Range Min', 'easy-property-listings' ),
							'type'		=>	'number',
							'maxlength'	=>	'40'
						),

						array(
							'name'		=>	'property_com_rent_range_max',
							'label'		=>	__('Rent Range Max', 'easy-property-listings' ),
							'type'		=>	'number',
							'maxlength'	=>	'40'
						),

						array(
							'name'		=>	'property_com_lease_end_date',
							'label'		=>	__('Lease End Date', 'easy-property-listings' ),
							'type'		=>	'date',
							'maxlength'	=>	'100'
						),

						array(
							'name'		=>	'property_com_property_extent',
							'label'		=>	__('Property Extent', 'easy-property-listings' ),
							'type'		=>	'select',
							'opts'		=>	$opts_property_com_property_extent
						)
					)
				),

				array(
					'id'		=>	'tenant_n_outgoings',
					'columns'	=>	'2',
					'label'		=>	'',
					'fields'		=>	array(
						array(
							'name'		=>	'property_com_tenancy',
							'label'		=>	__('Tenant Status', 'easy-property-listings' ),
							'type'		=>	'select',
							'opts'		=>	$opts_property_com_tenancy,
							'include'	=>	array('commercial')
						),

						array(
							'name'		=>	'property_com_outgoings',
							'label'		=>	__('Commercial Outgoings', 'easy-property-listings' ),
							'type'		=>	'number',
							'maxlength'	=>	'40',
							'exclude'	=>	array('business')
						),

						array(
							'name'		=>	'property_com_plus_outgoings',
							'label'		=>	__('Plus Outgoings', 'easy-property-listings' ),
							'type'		=>	'checkbox_single',
							'opts'		=>	array(
								'yes'	=>	__('Yes', 'easy-property-listings' ),
							),
							'exclude'	=>	array('business')
						),

						array(
							'name'		=>	'property_bus_takings',
							'label'		=>	__('Takings', 'easy-property-listings' ),
							'type'		=>	'number',
							'maxlength'	=>	'40',
							'include'	=>	array('business')
						),

						array(
							'name'		=>	'property_bus_franchise',
							'label'		=>	__('Franchise', 'easy-property-listings' ),
							'type'		=>	'checkbox_single',
							'opts'		=>	array(
								'yes'	=>	__('Yes', 'easy-property-listings' ),
							),
							'include'	=>	array('business')
						),

						array(
							'name'		=>	'property_com_return',
							'label'		=>	__('Return', 'easy-property-listings' ),
							'type'		=>	'decimal',
							'maxlength'	=>	'6'
						),

						array(
							'name'		=>	'property_bus_terms',
							'label'		=>	__('Terms', 'easy-property-listings' ),
							'type'		=>	'textarea'
						)
					)
				)
			)
		),

		array(
			'id'		=>	'epl-commercial-features-id',
			'label'		=>	__('Commercial Features', 'easy-property-listings' ),
			'post_type'	=>	array('commercial'),
			'context'	=>	'normal',
			'priority'	=>	'default',
			'groups'		=>	array(
				array(
					'id'		=>	'commercial_features',
					'columns'	=>	'1',
					'label'		=>	'',
					'fields'	=>	array(
						array(
							'name'		=>	'property_com_further_options',
							'label'		=>	__('Further Options', 'easy-property-listings' ),
							'type'		=>	'textarea',
							'maxlength'	=>	'65535'
						),

						array(
							'name'		=>	'property_com_zone',
							'label'		=>	__('Zone', 'easy-property-listings' ),
							'type'		=>	'textarea',
							'maxlength'	=>	'150'
						),

						array(
							'name'		=>	'property_com_car_spaces',
							'label'		=>	__('Car Spaces', 'easy-property-listings' ),
							'type'		=>	'number',
							'type'		=>	'textarea',
							'maxlength'	=>	'5'
						),

						array(
							'name'		=>	'property_com_highlight_1',
							'label'		=>	__('Highlight 1', 'easy-property-listings' ),
							'type'		=>	'text',
							'maxlength'	=>	'40'
						),

						array(
							'name'		=>	'property_com_highlight_2',
							'label'		=>	__('Highlight 2', 'easy-property-listings' ),
							'type'		=>	'text',
							'maxlength'	=>	'40'
						),

						array(
							'name'		=>	'property_com_highlight_3',
							'label'		=>	__('Highlight 3', 'easy-property-listings' ),
							'type'		=>	'text',
							'maxlength'	=>	'40'
						),

						array(
							'name'		=>	'property_com_parking_comments',
							'label'		=>	__('Parking Comments', 'easy-property-listings' ),
							'type'		=>	'text',
							'maxlength'	=>	'150'
						),

						array(
							'name'		=>	'property_com_is_multiple',
							'label'		=>	__('Is Multiple', 'easy-property-listings' ),
							'type'		=>	'checkbox_single',
							'opts'	=>	array(
								'yes'	=>	__('Yes', 'easy-property-listings' ),
							)
						)
					)
				)
			)
		),

		array(
			'id'		=>	'epl-attachments-section-id',
			'label'		=>	__('Files and Links', 'easy-property-listings' ),
			'post_type'	=>	array('property', 'rural', 'commercial', 'commercial_land', 'business', 'rental', 'land'),
			'context'	=>	'normal',
			'priority'	=>	'default',
			'groups'		=>	array(
				array(
					'id'		=>	'files_n_links',
					'columns'	=>	'2',
					'label'		=>	'',
					'fields'	=>	array(
						array(
							'name'		=>	'property_video_url',
							'label'		=>	__('Video URL', 'easy-property-listings' ),
							'type'		=>	'url'
						),

						array(
							'name'		=>	'property_external_link',
							'label'		=>	__('External Link', 'easy-property-listings' ),
							'type'		=>	'url',
							'width'		=>	'2-3'
						),

						array(
							'name'		=>	'property_external_link_label',
							'label'		=>	__('Title', 'easy-property-listings' ),
							'type'		=>	'text',
							'maxlength'	=>	'40',
							'width'		=>	'3'
						),

						array(
							'name'		=>	'property_external_link_2',
							'label'		=>	__('External Link 2', 'easy-property-listings' ),
							'type'		=>	'url',
							'width'		=>	'2-3'
						),

						array(
							'name'		=>	'property_external_link_2_label',
							'label'		=>	__('Title', 'easy-property-listings' ),
							'type'		=>	'text',
							'maxlength'	=>	'40',
							'width'		=>	'3'
						),

						array(
							'name'		=>	'property_external_link_3',
							'label'		=>	__('External Link 3', 'easy-property-listings' ),
							'type'		=>	'url',
							'width'		=>	'2-3'
						),

						array(
							'name'		=>	'property_external_link_3_label',
							'label'		=>	__('Title', 'easy-property-listings' ),
							'type'		=>	'text',
							'maxlength'	=>	'40',
							'width'		=>	'3'
						),

						array(
							'name'		=>	'property_com_mini_web',
							'label'		=>	__('Mini Website URL', 'easy-property-listings' ),
							'type'		=>	'url',
						),

						array(
							'name'		=>	'property_com_mini_web_2',
							'label'		=>	__('Mini Website URL 2', 'easy-property-listings' ),
							'type'		=>	'url',
						),

						array(
							'name'		=>	'property_com_mini_web_3',
							'label'		=>	__('Mini Website URL 3', 'easy-property-listings' ),
							'type'		=>	'url',
							'include'	=>	array('commercial', 'business', 'commercial_land'),
						),
					)
				),

				array(
					'id'		=>	'files_n_links_2',
					'columns'	=>	'2',
					'label'		=>	'',
					'fields'		=>	array(

						array(
							'name'		=>	'property_floorplan',
							'label'		=>	__('Floorplan', 'easy-property-listings' ),
							'type'		=>	'file',
							'width'		=>	'2-3'
						),

						array(
							'name'		=>	'property_floorplan_label',
							'label'		=>	__('Title', 'easy-property-listings' ),
							'type'		=>	'text',
							'maxlength'	=>	'40',
							'width'		=>	'3'
						),

						array(
							'name'		=>	'property_floorplan_2',
							'label'		=>	__('Floorplan 2', 'easy-property-listings' ),
							'type'		=>	'file',
							'width'		=>	'2-3'
						),

						array(
							'name'		=>	'property_floorplan_2_label',
							'label'		=>	__('Title', 'easy-property-listings' ),
							'type'		=>	'text',
							'maxlength'	=>	'40',
							'width'		=>	'3'
						),

						array(
							'name'		=>	'property_energy_certificate',
							'label'		=>	__('Energy Certificate', 'easy-property-listings' ),
							'type'		=>	'file',
							'width'		=>	'2-3',
							'exclude'	=>	array('land', 'commercial_land'),
						),

						array(
							'name'		=>	'property_energy_certificate_label',
							'label'		=>	__('Title', 'easy-property-listings' ),
							'type'		=>	'file',
							'type'		=>	'text',
							'maxlength'	=>	'40',
							'width'		=>	'3',
							'exclude'	=>	array('land', 'commercial_land'),
						)
					)
				)
			)
		),

		array(
			'id'		=>	'epl-owner-listings-section-id',
			'label'		=>	__('Linked Contact', 'easy-property-listings' ),
			'post_type'	=>	array('property', 'rural', 'commercial', 'commercial_land', 'business', 'rental', 'land'),
			'context'	=>	'side',
			'priority'	=>	'default',
			'groups'		=>	array(
				array(
					'id'		=>	'owner_details',
					'columns'	=>	'1',
					'label'		=>	'',
					'fields'	=>	array(
						array(
							'name'		=>	'property_owner',
							'label'		=>	__('Contact ID','easy-property-listings' ),
							'type'		=>	'text',
							'help'		=>	__('Search for contact and update to save.','easy-property-listings' )
						),
					)
				)
			)
		),
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

	return apply_filters('epl_listing_meta_boxes', $epl_meta_boxes);
}

/**
 * Get field labels from meta box
 *
 * @since 1.0
 * @return [type] [description]
 */
function epl_get_meta_field_labels() {

	global $epl_meta_boxes;
	global $epl_meta_labels;

	if( is_null($epl_meta_boxes)  || empty($epl_meta_boxes) ) {

		$epl_meta_boxes = epl_get_meta_boxes();
	}

	foreach($epl_meta_boxes as &$epl_meta_box) {

		if(!empty($epl_meta_box['groups'])) {
			foreach($epl_meta_box['groups'] as &$group) {
				if(!empty($group['fields'])) {
					$group['fields'] = array_filter($group['fields']);
					foreach($group['fields'] as &$field) {

						$epl_meta_labels[$field['name']] = $field['label'];
 					}
				}
			}
		}
	}

	return apply_filters('epl_meta_labels',$epl_meta_labels);
}

/**
 * Get meta field label
 *
 * @since 1.0
 * @param  string $key [description]
 * @return [type]      [description]
 */
function epl_get_meta_field_label($key = '') {

	if($key == '')
		return;

	global $epl_meta_labels;

	if( is_null($epl_meta_labels)  || empty($epl_meta_labels) ) {

		$epl_meta_labels = epl_get_meta_field_labels();
	}

	$label = isset( $epl_meta_labels[$key] ) ? $epl_meta_labels[$key] : '';

	$filter_prefix = 'epl_front_meta_label_';

	if( is_admin() && !wp_doing_ajax() ) {
		$filter_prefix = 'epl_admin_meta_label_';
	}

	return apply_filters($filter_prefix.$key,$label);
}