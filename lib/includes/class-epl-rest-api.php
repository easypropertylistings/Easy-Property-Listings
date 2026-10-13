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
				foreach ( $this->get_private_meta_keys( $post ) as $key ) {
					unset( $response->data['meta'][ $key ] );
				}
			}

			return $response;
		}

		/**
		 * Meta keys that must not be returned to users who cannot edit the listing.
		 *
		 * Internal fields are always removed. Price, rent, sold price, street address and map
		 * fields are removed when the listing's display settings hide them on the front end.
		 *
		 * @param WP_Post $post The post.
		 *
		 * @return array Meta keys, filterable with epl_rest_api_private_meta_keys.
		 *
		 * @since 3.5.27
		 */
		public function get_private_meta_keys( $post ) {

			$keys = array(
				'property_owner',
				'property_office_id',
				'property_mod_date',
				'property_images_mod_date',
				'property_floorplan_mod_date',
				'epl_remover_days',
			);

			if ( 'yes' !== get_post_meta( $post->ID, 'property_price_display', true ) ) {
				$keys = array_merge(
					$keys,
					array(
						'property_price',
						'property_price_view',
						'property_price_global',
						'property_com_rent',
						'property_com_rent_range_min',
						'property_com_rent_range_max',
					)
				);
			}

			if ( 'yes' !== get_post_meta( $post->ID, 'property_sold_price_display', true ) ) {
				$keys[] = 'property_sold_price';
			}

			if ( 'yes' !== get_post_meta( $post->ID, 'property_rent_display', true ) ) {
				$keys = array_merge( $keys, array( 'property_rent', 'property_rent_view' ) );
			}

			if ( 'yes' !== get_post_meta( $post->ID, 'property_address_display', true ) ) {
				$keys = array_merge(
					$keys,
					array(
						'property_address_sub_number',
						'property_address_lot_number',
						'property_address_street_number',
						'property_address_street',
						'property_address_coordinates',
					)
				);
			}

			if ( 'yes' === get_post_meta( $post->ID, 'property_address_hide_map', true ) ) {
				$keys[] = 'property_address_coordinates';
			}

			return array_unique( (array) apply_filters( 'epl_rest_api_private_meta_keys', $keys, $post ) );
		}
	}
endif;

EPL_Rest_API::instance();
