<?php
/**
 * EPL Rest API Class.
 *
 * @package     EPL
 * @subpackage  Classes/RestAPI
 * @copyright   Copyright (c) 2024, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.5.8
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * EPL_Rest_API Class
 *
 * @since 3.5.8
 */
if ( ! class_exists( 'EPL_Rest_API' ) ) :

	/**
	 * EPL_Rest_API Class
	 *
	 * @since 3.5.8
	 */
	class EPL_Rest_API {

		/**
		 * Instance
		 *
		 * @var EPL_Rest_API instance
		 */
		private static $instance;

		/**
		 * Insures that only one instance of EPL_Rest_API exists in memory.
		 *
		 * @return EPL_Rest_API instance
		 * @static
		 * @staticvar array $instance
		 *
		 * @since 3.5.8
		 */
		public static function instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof EPL_Rest_API ) ) {
				self::$instance = new EPL_Rest_API();
				self::$instance->hooks();
			}
			return self::$instance;
		}

		/**
		 * Add hooks to Rest API.
		 *
		 * @since 3.5.8
		 */
		public function hooks() {
			foreach ( epl_get_core_post_types() as $post_type ) {
				add_filter( 'rest_prepare_' . $post_type, array( $this, 'meta_to_rest_api' ), 10, 3 );
			}
		}

		/**
		 * Add EPL Meta data to Rest API.
		 *
		 * @param array $response The response.
		 * @param array $post     The post.
		 * @param array $request  The request.
		 *
		 * @return WP_REST_Response $response
		 *
		 * @since 3.5.8
		 * @since 3.5.10 Fix error when group fields are empty.
		 * @since 3.5.27 Hidden and internal fields are only returned to users who can edit the listing.
		 * @since 3.5.28 Users who cannot edit the listing only receive public fields the listing displays.
		 */
		public function meta_to_rest_api( $response, $post, $request ) {

			if ( ! is_epl_post( $post->post_type ) ) {
				return $response;
			}

			$meta_field_sections = epl_get_meta_boxes();

			if ( ! empty( $meta_field_sections ) ) {

				foreach ( $meta_field_sections as $epl_meta_box ) {

					$section_post_types = (array) $epl_meta_box['post_type'];

					if ( ! in_array( $post->post_type, $section_post_types, true ) ) {
						continue;
					}

					if ( ! empty( $epl_meta_box['groups'] ) ) {

						foreach ( $epl_meta_box['groups'] as $group ) {

							if ( isset( $group['fields'] ) && is_array( $group['fields'] ) ) {
								$fields = $group['fields'];
								$fields = array_filter( $fields );

							} else {
								$fields = array();
							}

							if ( ! empty( $fields ) ) {

								foreach ( $fields as $field ) {
									if ( isset( $field['exclude'] ) && ! empty( $field['exclude'] ) ) {
										if ( in_array( $post->post_type, $field['exclude'], true ) ) {
											continue;
										}
									}

									if ( isset( $field['include'] ) && ! empty( $field['include'] ) ) {
										if ( ! in_array( $post->post_type, $field['include'], true ) ) {
											continue;
										}
									}
									$response->data['meta'][ $field['name'] ] = get_post_meta( $post->ID, $field['name'], true );
								}
							}
						}
					}
				}
			}

			if ( ! empty( $response->data['meta'] ) && is_array( $response->data['meta'] ) && ! current_user_can( 'edit_post', $post->ID ) ) {
				$response->data['meta'] = array_intersect_key(
					$response->data['meta'],
					array_flip( $this->get_public_meta_keys( $post ) )
				);
			}

			return $response;
		}

		/**
		 * Meta keys returned to users who cannot edit the listing.
		 *
		 * Only fields the listing displays on the front end are allowed. Any other field, including
		 * fields added by extensions, is withheld unless added with the epl_rest_api_public_meta_keys
		 * filter. Fields are then removed when the listing or the site settings hide them.
		 *
		 * @param WP_Post $post The post.
		 *
		 * @return array Meta keys.
		 *
		 * @since 3.5.28
		 */
		public function get_public_meta_keys( $post ) {

			$keys = array(
				// Listing details.
				'property_heading',
				'property_status',
				'property_authority',
				'property_unique_id',
				'property_category',
				'property_rural_category',
				'property_land_category',
				'property_com_authority',
				'property_com_listing_type',
				'property_commercial_category',
				'property_inspection_times',
				'property_featured',
				'property_under_offer',
				'property_auction',
				'property_sold_date',

				// Agents.
				'property_agent',
				'property_second_agent',
				'property_third_agent',
				'property_fourth_agent',
				'property_agent_hide_author_box',

				// Features.
				'property_bedrooms',
				'property_bathrooms',
				'property_toilet',
				'property_ensuite',
				'property_garage',
				'property_carport',
				'property_open_spaces',
				'property_rooms',
				'property_year_built',
				'property_new_construction',
				'property_pool',
				'property_air_conditioning',
				'property_security_system',
				'property_pet_friendly',
				'property_broadband',
				'property_built_in_robes',
				'property_dishwasher',
				'property_floor_boards',
				'property_gym',
				'property_intercom',
				'property_pay_tv',
				'property_remote_garage',
				'property_rumpus_room',
				'property_secure_parking',
				'property_spa',
				'property_study',
				'property_vacuum_system',
				'property_workshop',
				'property_balcony',
				'property_courtyard',
				'property_deck',
				'property_outdoor_entertaining',
				'property_shed',
				'property_tennis_court',
				'property_ducted_cooling',
				'property_ducted_heating',
				'property_evaporative_cooling',
				'property_gas_heating',
				'property_hydronic_heating',
				'property_open_fire_place',
				'property_reverse_cycle_aircon',
				'property_split_system_aircon',
				'property_split_system_heating',

				// Land.
				'property_land_area',
				'property_land_area_unit',
				'property_building_area',
				'property_building_area_unit',
				'property_energy_rating',
				'property_land_fully_fenced',

				// Address.
				'property_address_display',
				'property_address_hide_map',
				'property_com_display_suburb',
				'property_address_lot_number',
				'property_address_sub_number',
				'property_address_street_number',
				'property_address_street',
				'property_address_suburb',
				'property_address_city',
				'property_address_state',
				'property_address_postal_code',
				'property_address_country',
				'property_address_coordinates',

				// Pricing.
				'property_price_display',
				'property_price',
				'property_price_view',
				'property_sold_price_display',
				'property_sold_price',
				'property_rent_display',
				'property_rent',
				'property_rent_period',
				'property_rent_view',
				'property_bond',
				'property_date_available',
				'property_furnished',
				'property_holiday_rental',

				// Rural.
				'property_rural_fencing',
				'property_rural_annual_rainfall',
				'property_rural_soil_types',
				'property_rural_improvements',
				'property_rural_council_rates',
				'property_rural_irrigation',
				'property_rural_carrying_capacity',

				// Commercial.
				'property_com_rent',
				'property_com_rent_period',
				'property_com_plus_outgoings',
				'property_com_further_options',
				'property_com_zone',
				'property_com_car_spaces',
				'property_com_highlight_1',
				'property_com_highlight_2',
				'property_com_highlight_3',
				'property_com_parking_comments',

				// Files and links.
				'property_video_url',
				'property_external_link',
				'property_external_link_label',
				'property_external_link_2',
				'property_external_link_2_label',
				'property_external_link_3',
				'property_external_link_3_label',
				'property_com_mini_web',
				'property_com_mini_web_2',
				'property_com_mini_web_3',
				'property_floorplan',
				'property_floorplan_label',
				'property_floorplan_2',
				'property_floorplan_2_label',
				'property_energy_certificate',
				'property_energy_certificate_label',
			);

			$keys   = (array) apply_filters( 'epl_rest_api_public_meta_keys', $keys, $post );
			$hidden = array();
			$status = get_post_meta( $post->ID, 'property_status', true );

			$show_price = 'yes' === get_post_meta( $post->ID, 'property_price_display', true ) && ! in_array( $status, array( 'sold', 'leased' ), true );
			$show_sold  = 'yes' === get_post_meta( $post->ID, 'property_sold_price_display', true ) && 'sold' === $status;
			$show_rent  = 'yes' === get_post_meta( $post->ID, 'property_rent_display', true ) && 'leased' !== $status;
			$show_bond  = $show_rent && in_array( epl_get_option( 'display_bond' ), array( 1, '1', 'yes' ), true );
			$show_addr  = 'yes' === get_post_meta( $post->ID, 'property_address_display', true );

			if ( ! $show_price ) {
				$hidden = array_merge( $hidden, array( 'property_price', 'property_price_view', 'property_com_rent', 'property_com_rent_period' ) );
			}

			if ( ! $show_sold ) {
				$hidden[] = 'property_sold_price';
			}

			if ( ! $show_rent ) {
				$hidden = array_merge( $hidden, array( 'property_rent', 'property_rent_view', 'property_rent_period' ) );
			}

			if ( ! $show_bond ) {
				$hidden[] = 'property_bond';
			}

			if ( ! $show_addr ) {
				$hidden = array_merge(
					$hidden,
					array(
						'property_address_lot_number',
						'property_address_sub_number',
						'property_address_street_number',
						'property_address_street',
						'property_address_coordinates',
					)
				);

				if ( in_array( $post->post_type, array( 'commercial', 'commercial_land', 'business' ), true ) && 'yes' !== get_post_meta( $post->ID, 'property_com_display_suburb', true ) ) {
					$hidden[] = 'property_address_suburb';
				}
			}

			if ( 'yes' === get_post_meta( $post->ID, 'property_address_hide_map', true ) ) {
				$hidden[] = 'property_address_coordinates';
			}

			if ( 'yes' !== epl_get_option( 'epl_enable_city_field' ) ) {
				$hidden[] = 'property_address_city';
			}

			if ( 'yes' !== epl_get_option( 'epl_enable_country_field' ) ) {
				$hidden[] = 'property_address_country';
			}

			$hidden = (array) apply_filters( 'epl_rest_api_private_meta_keys', $hidden, $post );

			return array_values( array_diff( array_unique( $keys ), $hidden ) );
		}
	}
endif;

EPL_Rest_API::instance();
