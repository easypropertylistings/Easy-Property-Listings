<?php
/**
 * EPL Admin Listings REST API Controller
 *
 * Provides REST API endpoints for the unified admin listings interface.
 *
 * @package     EPL
 * @subpackage  Admin/REST
 * @copyright   Copyright (c) 2024, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       4.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * EPL_Admin_Listings_REST Class
 *
 * Custom REST API controller for unified admin listings.
 *
 * @since 4.0.0
 */
class EPL_Admin_Listings_REST extends WP_REST_Controller {

	/**
	 * API namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'epl/v1';

	/**
	 * REST base for listings.
	 *
	 * @var string
	 */
	protected $rest_base = 'admin/listings';

	/**
	 * REST base for view settings.
	 *
	 * @var string
	 */
	protected $settings_base = 'admin/view-settings';

	/**
	 * Editable fields configuration.
	 *
	 * @var array
	 */
	protected $editable_fields = array();

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->editable_fields = $this->get_editable_fields_config();
	}

	/**
	 * Get editable fields configuration.
	 *
	 * @return array
	 */
	protected function get_editable_fields_config() {
		$fields = array(
			'property_price'            => array(
				'type'       => 'number',
				'label'      => __( 'Price', 'easy-property-listings' ),
				'capability' => 'edit_post',
			),
			'property_rent'             => array(
				'type'       => 'number',
				'label'      => __( 'Rent', 'easy-property-listings' ),
				'capability' => 'edit_post',
			),
			'property_status'           => array(
				'type'       => 'select',
				'label'      => __( 'Status', 'easy-property-listings' ),
				'capability' => 'edit_post',
				'options'    => array( 'current', 'sold', 'leased', 'withdrawn', 'offmarket', 'deleted' ),
			),
			'property_featured'         => array(
				'type'       => 'toggle',
				'label'      => __( 'Featured', 'easy-property-listings' ),
				'capability' => 'edit_post',
			),
			'property_heading'          => array(
				'type'       => 'text',
				'label'      => __( 'Heading', 'easy-property-listings' ),
				'capability' => 'edit_post',
			),
			'property_inspection_times' => array(
				'type'       => 'textarea',
				'label'      => __( 'Inspection Times', 'easy-property-listings' ),
				'capability' => 'edit_post',
			),
			'property_images_mod_date'  => array(
				'type'       => 'text',
				'label'      => __( 'Images Mod Date', 'easy-property-listings' ),
				'capability' => 'edit_post',
			),
			'property_mod_date'         => array(
				'type'       => 'text',
				'label'      => __( 'Property Mod Date', 'easy-property-listings' ),
				'capability' => 'edit_post',
			),
			'location'                  => array(
				'type'       => 'text',
				'label'      => __( 'Location', 'easy-property-listings' ),
				'capability' => 'edit_post',
				'is_meta'    => false,
			),
			'post_status'               => array(
				'type'       => 'select',
				'label'      => __( 'Post Status', 'easy-property-listings' ),
				'capability' => 'edit_post',
				'options'    => array( 'publish', 'draft', 'pending', 'private' ),
				'is_meta'    => false,
			),
			'post_author'               => array(
				'type'       => 'number',
				'label'      => __( 'Author', 'easy-property-listings' ),
				'capability' => 'edit_posts',
				'is_meta'    => false,
			),
			'property_unique_id'        => array(
				'type'       => 'text',
				'label'      => __( 'Listing ID', 'easy-property-listings' ),
				'capability' => 'edit_post',
			),
		);

		/**
		 * Filter the editable fields for inline editing.
		 *
		 * @since 4.0.0
		 * @param array $fields Editable fields configuration.
		 */
		return apply_filters( 'epl_admin_listings_editable_fields', $fields );
	}

	/**
	 * Register REST API routes.
	 */
	public function register_routes() {
		// Listings endpoints.
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base,
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_listings' ),
					'permission_callback' => array( $this, 'get_listings_permissions_check' ),
					'args'                => $this->get_listings_collection_params(),
				),
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/(?P<id>[\d]+)',
			array(
				array(
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'update_listing' ),
					'permission_callback' => array( $this, 'update_listing_permissions_check' ),
					'args'                => $this->get_update_listing_params(),
				),
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/bulk',
			array(
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'bulk_action' ),
					'permission_callback' => array( $this, 'bulk_action_permissions_check' ),
					'args'                => $this->get_bulk_action_params(),
				),
			)
		);

		// View settings endpoints.
		register_rest_route(
			$this->namespace,
			'/' . $this->settings_base,
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_view_settings' ),
					'permission_callback' => array( $this, 'get_listings_permissions_check' ),
				),
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'save_view_settings' ),
					'permission_callback' => array( $this, 'get_listings_permissions_check' ),
				),
			)
		);

		// Schema endpoint.
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/schema',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_schema' ),
					'permission_callback' => array( $this, 'get_listings_permissions_check' ),
				),
			)
		);
	}

	/**
	 * Check permissions for getting listings.
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return bool|WP_Error
	 */
	public function get_listings_permissions_check( $request ) {
		if ( ! current_user_can( 'edit_posts' ) ) {
			return new WP_Error(
				'rest_forbidden',
				__( 'You do not have permission to view listings.', 'easy-property-listings' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}
		return true;
	}

	/**
	 * Check permissions for updating a listing.
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return bool|WP_Error
	 */
	public function update_listing_permissions_check( $request ) {
		$post_id = absint( $request->get_param( 'id' ) );

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return new WP_Error(
				'rest_forbidden',
				__( 'You do not have permission to edit this listing.', 'easy-property-listings' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}
		return true;
	}

	/**
	 * Check permissions for bulk actions.
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return bool|WP_Error
	 */
	public function bulk_action_permissions_check( $request ) {
		if ( ! current_user_can( 'edit_posts' ) ) {
			return new WP_Error(
				'rest_forbidden',
				__( 'You do not have permission to perform bulk actions.', 'easy-property-listings' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}
		return true;
	}

	/**
	 * Get listings with filters.
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response|WP_Error
	 */
	public function get_listings( $request ) {
		$params = $request->get_params();

		// Build query arguments.
		$args = array(
			'post_type'      => $this->get_requested_post_types( $params ),
			'posts_per_page' => isset( $params['per_page'] ) ? absint( $params['per_page'] ) : 25,
			'paged'          => isset( $params['page'] ) ? absint( $params['page'] ) : 1,
			'post_status'    => 'any',
		);

		// Search.
		if ( ! empty( $params['search'] ) ) {
			$args['s'] = sanitize_text_field( $params['search'] );
		}

		// Sorting.
		if ( ! empty( $params['orderby'] ) ) {
			$orderby = sanitize_text_field( $params['orderby'] );
			$order   = isset( $params['order'] ) ? strtoupper( sanitize_text_field( $params['order'] ) ) : 'DESC';

			if ( in_array( $orderby, array( 'date', 'title', 'modified', 'ID' ), true ) ) {
				$args['orderby'] = $orderby;
				$args['order']   = $order;
			} elseif ( strpos( $orderby, 'property_' ) === 0 ) {
				$args['meta_key'] = $orderby;
				$args['orderby']  = 'meta_value';
				$args['order']    = $order;

				// Numeric sorting for price fields.
				if ( in_array( $orderby, array( 'property_price', 'property_rent' ), true ) ) {
					$args['orderby'] = 'meta_value_num';
				}
			}
		} else {
			$args['orderby'] = 'modified';
			$args['order']   = 'DESC';
		}

		// Meta query for filters.
		$meta_query = array();

		// Status filter.
		if ( ! empty( $params['status'] ) ) {
			$meta_query[] = array(
				'key'   => 'property_status',
				'value' => sanitize_text_field( $params['status'] ),
			);
		}

		// Author/Agent filter.
		if ( ! empty( $params['author'] ) ) {
			$author = absint( $params['author'] );
			$user   = get_user_by( 'id', $author );

			if ( $user ) {
				$meta_query[] = array(
					'relation' => 'OR',
					array(
						'key'   => 'property_agent',
						'value' => $user->user_login,
					),
					array(
						'key'   => 'property_second_agent',
						'value' => $user->user_login,
					),
				);
			}
		}

		// Suburb filter (meta-based).
		if ( ! empty( $params['suburb'] ) ) {
			$meta_query[] = array(
				'key'     => 'property_address_suburb',
				'value'   => sanitize_text_field( $params['suburb'] ),
				'compare' => 'LIKE',
			);
		}

		// Location taxonomy filter (for suburb taxonomy).
		if ( ! empty( $params['location'] ) ) {
			$args['tax_query'][] = array(
				'taxonomy' => 'location',
				'field'    => 'slug',
				'terms'    => sanitize_text_field( $params['location'] ),
			);
		}

		// Featured filter.
		if ( isset( $params['featured'] ) && '' !== $params['featured'] ) {
			$meta_query[] = array(
				'key'   => 'property_featured',
				'value' => $params['featured'] ? 'yes' : 'no',
			);
		}

		// Price filters.
		if ( ! empty( $params['price_min'] ) ) {
			$meta_query[] = array(
				'key'     => 'property_price',
				'value'   => floatval( $params['price_min'] ),
				'compare' => '>=',
				'type'    => 'NUMERIC',
			);
		}
		if ( ! empty( $params['price_max'] ) ) {
			$meta_query[] = array(
				'key'     => 'property_price',
				'value'   => floatval( $params['price_max'] ),
				'compare' => '<=',
				'type'    => 'NUMERIC',
			);
		}

		// Rent filters.
		if ( ! empty( $params['rent_min'] ) ) {
			$meta_query[] = array(
				'key'     => 'property_rent',
				'value'   => floatval( $params['rent_min'] ),
				'compare' => '>=',
				'type'    => 'NUMERIC',
			);
		}
		if ( ! empty( $params['rent_max'] ) ) {
			$meta_query[] = array(
				'key'     => 'property_rent',
				'value'   => floatval( $params['rent_max'] ),
				'compare' => '<=',
				'type'    => 'NUMERIC',
			);
		}

		// Bedrooms filter.
		if ( ! empty( $params['beds_min'] ) ) {
			$meta_query[] = array(
				'key'     => 'property_bedrooms',
				'value'   => absint( $params['beds_min'] ),
				'compare' => '>=',
				'type'    => 'NUMERIC',
			);
		}

		// Bathrooms filter.
		if ( ! empty( $params['baths_min'] ) ) {
			$meta_query[] = array(
				'key'     => 'property_bathrooms',
				'value'   => absint( $params['baths_min'] ),
				'compare' => '>=',
				'type'    => 'NUMERIC',
			);
		}

		// Category filter.
		if ( ! empty( $params['property_category'] ) ) {
			$meta_query[] = array(
				'key'   => 'property_category',
				'value' => sanitize_text_field( $params['property_category'] ),
			);
		}

		// Date range filter.
		if ( ! empty( $params['date_after'] ) ) {
			$args['date_query'][] = array(
				'after'     => sanitize_text_field( $params['date_after'] ),
				'inclusive' => true,
			);
		}

		if ( ! empty( $params['date_before'] ) ) {
			$args['date_query'][] = array(
				'before'    => sanitize_text_field( $params['date_before'] ),
				'inclusive' => true,
			);
		}

		if ( ! empty( $meta_query ) ) {
			$args['meta_query'] = $meta_query;
		}

		/**
		 * Filter the WP_Query args for admin listings.
		 *
		 * @since 4.0.0
		 * @param array           $args    WP_Query arguments.
		 * @param WP_REST_Request $request REST request object.
		 */
		$args = apply_filters( 'epl_admin_listings_query_args', $args, $request );

		// Execute query.
		$query = new WP_Query( $args );

		// Build response.
		$listings = array();
		$image_size = isset( $params['image_size'] ) ? sanitize_text_field( $params['image_size'] ) : 'thumbnail';

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$listings[] = $this->prepare_listing_for_response( get_post(), $image_size );
			}
			wp_reset_postdata();
		}

		// Get post type counts.
		$post_type_counts = array();
		$all_post_types   = $this->get_requested_post_types( array() ); // Get all EPL post types.
		foreach ( $all_post_types as $pt ) {
			$count_query = new WP_Query(
				array(
					'post_type'      => $pt,
					'post_status'    => 'any',
					'posts_per_page' => 1,
					'fields'         => 'ids',
				)
			);
			$post_type_counts[ $pt ] = $count_query->found_posts;
		}

		$response = array(
			'listings'       => $listings,
			'total'          => $query->found_posts,
			'totalPages'     => $query->max_num_pages,
			'page'           => $args['paged'],
			'perPage'        => $args['posts_per_page'],
			'postTypeCounts' => $post_type_counts,
		);

		return rest_ensure_response( $response );
	}

	/**
	 * Get requested post types from params.
	 *
	 * @param array $params Request parameters.
	 * @return array
	 */
	protected function get_requested_post_types( $params ) {
		$all_types = epl_get_core_post_types();

		if ( ! empty( $params['post_type'] ) ) {
			$requested = array_map( 'sanitize_text_field', (array) $params['post_type'] );
			return array_intersect( $requested, $all_types );
		}

		return $all_types;
	}

	/**
	 * Prepare a single listing for API response.
	 *
	 * @param WP_Post $post       Post object.
	 * @param string  $image_size Image size.
	 * @return array
	 */
	protected function prepare_listing_for_response( $post, $image_size = 'thumbnail' ) {
		$thumbnail_id  = get_post_thumbnail_id( $post->ID );
		$thumbnail_url = $thumbnail_id ? wp_get_attachment_image_url( $thumbnail_id, $image_size ) : '';

		// Get property meta.
		$meta = array(
			'property_heading'          => get_post_meta( $post->ID, 'property_heading', true ),
			'property_price'            => get_post_meta( $post->ID, 'property_price', true ),
			'property_rent'             => get_post_meta( $post->ID, 'property_rent', true ),
			'property_status'           => get_post_meta( $post->ID, 'property_status', true ),
			'property_featured'         => get_post_meta( $post->ID, 'property_featured', true ),
			'property_unique_id'        => get_post_meta( $post->ID, 'property_unique_id', true ),
			'property_address_suburb'   => get_post_meta( $post->ID, 'property_address_suburb', true ),
			'property_address_street'   => get_post_meta( $post->ID, 'property_address_street', true ),
			'property_bedrooms'         => get_post_meta( $post->ID, 'property_bedrooms', true ),
			'property_bathrooms'        => get_post_meta( $post->ID, 'property_bathrooms', true ),
			'property_inspection_times' => get_post_meta( $post->ID, 'property_inspection_times', true ),
			'property_agent'            => get_post_meta( $post->ID, 'property_agent', true ),
			'property_address_coordinates' => get_post_meta( $post->ID, 'property_address_coordinates', true ),
			'property_under_offer'      => get_post_meta( $post->ID, 'property_under_offer', true ),
			'property_images_mod_date'  => get_post_meta( $post->ID, 'property_images_mod_date', true ),
			'property_mod_date'         => get_post_meta( $post->ID, 'property_mod_date', true ),
		);

		// Get location terms.
		$locations = get_the_terms( $post->ID, 'location' );
		$location_names = array();
		$location_slugs = array();
		if ( $locations && ! is_wp_error( $locations ) ) {
			$location_names = wp_list_pluck( $locations, 'name' );
			$location_slugs = wp_list_pluck( $locations, 'slug' );
		}

		// Get feature terms.
		$features = get_the_terms( $post->ID, 'tax_feature' );
		$feature_names = array();
		if ( $features && ! is_wp_error( $features ) ) {
			$feature_names = wp_list_pluck( $features, 'name' );
		}

		// Determine price display.
		$price_display = '';
		if ( 'rental' === $post->post_type ) {
			$price_display = $meta['property_rent'] ? epl_currency_formatted_amount( $meta['property_rent'] ) : '';
		} else {
			$price_display = $meta['property_price'] ? epl_currency_formatted_amount( $meta['property_price'] ) : '';
		}

		$listing = array(
			'id'              => $post->ID,
			'title'           => $post->post_title,
			'heading'         => $meta['property_heading'],
			'postType'        => $post->post_type,
			'postTypeLabel'   => get_post_type_object( $post->post_type )->labels->singular_name,
			'status'          => $post->post_status,
			'postStatus'      => $post->post_status,
			'propertyStatus'  => $meta['property_status'],
			'price'           => $meta['property_price'],
			'rent'            => $meta['property_rent'],
			'priceDisplay'    => $price_display,
			'featured'        => 'yes' === $meta['property_featured'],
			'underOffer'      => 'yes' === $meta['property_under_offer'],
			'listingId'       => $meta['property_unique_id'],
			'suburb'          => $meta['property_address_suburb'],
			'street'          => $meta['property_address_street'],
			'address'         => trim( $meta['property_address_street'] . ', ' . $meta['property_address_suburb'], ', ' ),
			'bedrooms'        => $meta['property_bedrooms'],
			'bathrooms'       => $meta['property_bathrooms'],
			'inspectionTimes' => $meta['property_inspection_times'],
			'agent'           => $meta['property_agent'],
			'locations'       => $location_names,
			'locationSlugs'   => $location_slugs,
			'features'        => $feature_names,
			'hasGeocode'      => ! empty( $meta['property_address_coordinates'] ) && ',' !== $meta['property_address_coordinates'],
			'thumbnail'       => $thumbnail_url,
			'date'            => $post->post_date,
			'modified'        => $post->post_modified,
			'propertyImagesModDate' => $meta['property_images_mod_date'],
			'propertyModDate'       => $meta['property_mod_date'],
			'author'          => array(
				'id'   => $post->post_author,
				'name' => get_the_author_meta( 'display_name', $post->post_author ),
			),
			'editLink'        => get_edit_post_link( $post->ID, 'raw' ),
			'viewLink'        => get_permalink( $post->ID ),
			'canEdit'         => current_user_can( 'edit_post', $post->ID ),
			'canDelete'       => current_user_can( 'delete_post', $post->ID ),
		);


		/**
		 * Filter the listing data for REST response.
		 *
		 * @since 4.0.0
		 * @param array   $listing Prepared listing data.
		 * @param WP_Post $post    Original post object.
		 */
		return apply_filters( 'epl_admin_listings_prepare_response', $listing, $post );
	}

	/**
	 * Update a single listing (inline edit).
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response|WP_Error
	 */
	public function update_listing( $request ) {
		$post_id = absint( $request->get_param( 'id' ) );
		$post    = get_post( $post_id );

		if ( ! $post || ! in_array( $post->post_type, epl_get_core_post_types(), true ) ) {
			return new WP_Error(
				'rest_post_invalid_id',
				__( 'Invalid listing ID.', 'easy-property-listings' ),
				array( 'status' => 404 )
			);
		}

		$fields = $request->get_param( 'fields' );

		if ( empty( $fields ) || ! is_array( $fields ) ) {
			return new WP_Error(
				'rest_invalid_param',
				__( 'No fields to update.', 'easy-property-listings' ),
				array( 'status' => 400 )
			);
		}

		$updated_fields = array();
		$errors         = array();

		foreach ( $fields as $field_key => $field_value ) {
			// Check if field is editable.
			if ( ! isset( $this->editable_fields[ $field_key ] ) ) {
				$errors[] = sprintf( __( 'Field "%s" is not editable.', 'easy-property-listings' ), $field_key );
				continue;
			}

			$field_config = $this->editable_fields[ $field_key ];

			// Check capability.
			if ( ! current_user_can( $field_config['capability'], $post_id ) ) {
				$errors[] = sprintf( __( 'You do not have permission to edit "%s".', 'easy-property-listings' ), $field_key );
				continue;
			}

			// Sanitize and validate based on type.
			$sanitized_value = $this->sanitize_field_value( $field_value, $field_config );

			// Validate.
			if ( 'select' === $field_config['type'] && isset( $field_config['options'] ) ) {
				if ( ! in_array( $sanitized_value, $field_config['options'], true ) ) {
					$errors[] = sprintf( __( 'Invalid value for "%s".', 'easy-property-listings' ), $field_key );
					continue;
				}
			}

			// Update meta or special fields.
			if ( isset( $field_config['is_meta'] ) && false === $field_config['is_meta'] ) {
				if ( 'post_status' === $field_key ) {
					wp_update_post( array(
						'ID'          => $post_id,
						'post_status' => $sanitized_value,
					) );
				} elseif ( 'post_author' === $field_key ) {
					wp_update_post( array(
						'ID'          => $post_id,
						'post_author' => $sanitized_value,
					) );
				} elseif ( 'location' === $field_key ) {
					wp_set_object_terms( $post_id, $sanitized_value, 'location' );
					$term = get_term_by( 'slug', $sanitized_value, 'location' );
					if ( $term && ! is_wp_error( $term ) ) {
						update_post_meta( $post_id, 'property_address_suburb', $term->name );
						$updated_fields['property_address_suburb'] = $term->name;
					}
				}
			} else {
				update_post_meta( $post_id, $field_key, $sanitized_value );
			}
			$updated_fields[ $field_key ] = $sanitized_value;
		}

		// Handle post date update.
		if ( isset( $fields['post_date'] ) ) {
			$date = sanitize_text_field( $fields['post_date'] );
			wp_update_post(
				array(
					'ID'            => $post_id,
					'post_date'     => $date,
					'post_date_gmt' => get_gmt_from_date( $date ),
				)
			);
			$updated_fields['post_date'] = $date;
		}

		// Handle post modified update.
		if ( isset( $fields['post_modified'] ) ) {
			$date = sanitize_text_field( $fields['post_modified'] );
			wp_update_post(
				array(
					'ID'                => $post_id,
					'post_modified'     => $date,
					'post_modified_gmt' => get_gmt_from_date( $date ),
				)
			);
			$updated_fields['post_modified'] = $date;
		}

		/**
		 * Action fired after a listing is updated via inline edit.
		 *
		 * @since 4.0.0
		 * @param int   $post_id        Post ID.
		 * @param array $updated_fields Updated field values.
		 */
		do_action( 'epl_admin_listing_inline_updated', $post_id, $updated_fields );

		// Refresh the listing data.
		$updated_listing = $this->prepare_listing_for_response( get_post( $post_id ) );

		return rest_ensure_response(
			array(
				'success' => empty( $errors ),
				'listing' => $updated_listing,
				'updated' => $updated_fields,
				'errors'  => $errors,
			)
		);
	}

	/**
	 * Sanitize field value based on type.
	 *
	 * @param mixed $value        Field value.
	 * @param array $field_config Field configuration.
	 * @return mixed
	 */
	protected function sanitize_field_value( $value, $field_config ) {
		switch ( $field_config['type'] ) {
			case 'number':
				return is_numeric( $value ) ? floatval( $value ) : 0;

			case 'toggle':
				return $value ? 'yes' : 'no';

			case 'textarea':
				return sanitize_textarea_field( $value );

			case 'text':
			case 'select':
			default:
				return sanitize_text_field( $value );
		}
	}

	/**
	 * Perform bulk action on listings.
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response|WP_Error
	 */
	public function bulk_action( $request ) {
		$action   = sanitize_text_field( $request->get_param( 'action' ) );
		$post_ids = array_map( 'absint', (array) $request->get_param( 'ids' ) );
		$value    = $request->get_param( 'value' );

		if ( empty( $post_ids ) ) {
			return new WP_Error(
				'rest_invalid_param',
				__( 'No listings selected.', 'easy-property-listings' ),
				array( 'status' => 400 )
			);
		}

		$results = array(
			'success' => array(),
			'failed'  => array(),
		);

		foreach ( $post_ids as $post_id ) {
			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				$results['failed'][] = $post_id;
				continue;
			}

			$success = false;

			switch ( $action ) {
				case 'trash':
					$success = wp_trash_post( $post_id );
					break;

				case 'restore':
					$success = wp_untrash_post( $post_id );
					break;

				case 'delete':
					if ( current_user_can( 'delete_post', $post_id ) ) {
						$success = wp_delete_post( $post_id, true );
					}
					break;

				case 'set_status':
					if ( $value && in_array( $value, array( 'current', 'sold', 'leased', 'withdrawn', 'offmarket', 'deleted' ), true ) ) {
						$success = update_post_meta( $post_id, 'property_status', sanitize_text_field( $value ) );
					}
					break;

				case 'set_featured':
					$success = update_post_meta( $post_id, 'property_featured', $value ? 'yes' : 'no' );
					break;

				default:
					/**
					 * Action for custom bulk actions.
					 *
					 * @since 4.0.0
					 * @param int    $post_id Post ID.
					 * @param string $action  Action name.
					 * @param mixed  $value   Action value.
					 */
					do_action( 'epl_admin_listings_bulk_action_' . $action, $post_id, $value );
					$success = true;
					break;
			}

			if ( $success ) {
				$results['success'][] = $post_id;
			} else {
				$results['failed'][] = $post_id;
			}
		}

		return rest_ensure_response( $results );
	}

	/**
	 * Get view settings for current user.
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response
	 */
	public function get_view_settings( $request ) {
		$user_id  = get_current_user_id();
		$settings = get_user_meta( $user_id, 'epl_admin_listings_view_settings', true );

		if ( empty( $settings ) ) {
			$settings = $this->get_default_view_settings();
		}

		return rest_ensure_response( $settings );
	}

	/**
	 * Save view settings for current user.
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response
	 */
	public function save_view_settings( $request ) {
		$user_id  = get_current_user_id();
		$settings = $request->get_param( 'settings' );

		if ( ! is_array( $settings ) ) {
			return new WP_Error(
				'rest_invalid_param',
				__( 'Invalid settings format.', 'easy-property-listings' ),
				array( 'status' => 400 )
			);
		}

		// Sanitize settings.
		$sanitized = $this->sanitize_view_settings( $settings );

		update_user_meta( $user_id, 'epl_admin_listings_view_settings', $sanitized );

		return rest_ensure_response(
			array(
				'success'  => true,
				'settings' => $sanitized,
			)
		);
	}

	/**
	 * Get schema information.
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response
	 */
	public function get_schema( $request ) {
		$post_types = array();
		foreach ( epl_get_core_post_types() as $type ) {
			$obj          = get_post_type_object( $type );
			$post_types[] = array(
				'name'  => $type,
				'label' => $obj ? $obj->labels->singular_name : $type,
			);
		}

		$status_options = array(
			array( 'value' => 'current', 'label' => epl_get_the_status_label( 'current' ) ),
			array( 'value' => 'sold', 'label' => epl_get_the_status_label( 'sold' ) ),
			array( 'value' => 'leased', 'label' => epl_get_the_status_label( 'leased' ) ),
			array( 'value' => 'withdrawn', 'label' => epl_get_the_status_label( 'withdrawn' ) ),
			array( 'value' => 'offmarket', 'label' => epl_get_the_status_label( 'offmarket' ) ),
			array( 'value' => 'deleted', 'label' => epl_get_the_status_label( 'deleted' ) ),
		);

		return rest_ensure_response(
			array(
				'postTypes'      => $post_types,
				'statusOptions'  => $status_options,
				'editableFields' => $this->editable_fields,
				'columns'        => $this->get_available_columns(),
			)
		);
	}

	/**
	 * Get available columns for the list view.
	 *
	 * @return array
	 */
	protected function get_available_columns() {
		$columns = array(
			'thumbnail'      => __( 'Image', 'easy-property-listings' ),
			'title'          => __( 'Title', 'easy-property-listings' ),
			'heading'        => __( 'Heading', 'easy-property-listings' ),
			'address'        => __( 'Address', 'easy-property-listings' ),
			'suburb'         => __( 'Suburb', 'easy-property-listings' ),
			'listingId'      => __( 'Listing ID', 'easy-property-listings' ),
			'postType'       => __( 'Type', 'easy-property-listings' ),
			'propertyStatus' => __( 'Status', 'easy-property-listings' ),
			'priceDisplay'   => __( 'Price', 'easy-property-listings' ),
			'bedrooms'       => __( 'Beds', 'easy-property-listings' ),
			'bathrooms'      => __( 'Baths', 'easy-property-listings' ),
			'featured'       => __( 'Featured', 'easy-property-listings' ),
			'author'         => __( 'Author', 'easy-property-listings' ),
			'agent'          => __( 'Agent', 'easy-property-listings' ),
			'date'           => __( 'Date', 'easy-property-listings' ),
			'modified'       => __( 'Modified', 'easy-property-listings' ),
		);

		/**
		 * Filter available columns for admin listings.
		 *
		 * @since 4.0.0
		 * @param array $columns Available columns.
		 */
		return apply_filters( 'epl_admin_listings_columns', $columns );
	}

	/**
	 * Get default view settings.
	 *
	 * @return array
	 */
	protected function get_default_view_settings() {
		return array(
			'viewMode' => 'list',
			'list'     => array(
				'columns'     => array( 'thumbnail', 'heading', 'address', 'priceDisplay', 'propertyStatus', 'author', 'modified' ),
				'perPage'     => 25,
			),
			'grid'     => array(
				'cardsPerRow' => 4,
				'showPrice'   => true,
				'showStatus'  => true,
				'showBeds'    => true,
				'showBaths'   => true,
			),
			'compact'  => array(
				'fields'  => array( 'address', 'priceDisplay', 'propertyStatus' ),
				'perPage' => 50,
			),
		);
	}

	/**
	 * Sanitize view settings.
	 *
	 * @param array $settings Settings to sanitize.
	 * @return array
	 */
	protected function sanitize_view_settings( $settings ) {
		$sanitized = array();

		if ( isset( $settings['viewMode'] ) ) {
			$sanitized['viewMode'] = in_array( $settings['viewMode'], array( 'list', 'grid', 'compact' ), true )
				? $settings['viewMode']
				: 'list';
		}

		if ( isset( $settings['list'] ) && is_array( $settings['list'] ) ) {
			$sanitized['list'] = array(
				'columns'   => isset( $settings['list']['columns'] ) ? array_map( 'sanitize_text_field', $settings['list']['columns'] ) : array(),
				'perPage'   => isset( $settings['list']['perPage'] ) ? absint( $settings['list']['perPage'] ) : 25,
				'imageSize' => isset( $settings['list']['imageSize'] ) ? sanitize_text_field( $settings['list']['imageSize'] ) : 'thumbnail',
			);
		}

		if ( isset( $settings['grid'] ) && is_array( $settings['grid'] ) ) {
			$sanitized['grid'] = array(
				'cardsPerRow' => isset( $settings['grid']['cardsPerRow'] ) ? absint( $settings['grid']['cardsPerRow'] ) : 4,
				'showPrice'   => isset( $settings['grid']['showPrice'] ) ? (bool) $settings['grid']['showPrice'] : true,
				'showStatus'  => isset( $settings['grid']['showStatus'] ) ? (bool) $settings['grid']['showStatus'] : true,
				'showBeds'    => isset( $settings['grid']['showBeds'] ) ? (bool) $settings['grid']['showBeds'] : true,
				'showBaths'   => isset( $settings['grid']['showBaths'] ) ? (bool) $settings['grid']['showBaths'] : true,
			);
		}

		if ( isset( $settings['compact'] ) && is_array( $settings['compact'] ) ) {
			$sanitized['compact'] = array(
				'fields'  => isset( $settings['compact']['fields'] ) ? array_map( 'sanitize_text_field', $settings['compact']['fields'] ) : array(),
				'perPage' => isset( $settings['compact']['perPage'] ) ? absint( $settings['compact']['perPage'] ) : 50,
			);
		}

		return $sanitized;
	}

	/**
	 * Get collection params for listings endpoint.
	 *
	 * @return array
	 */
	public function get_listings_collection_params() {
		return array(
			'page'        => array(
				'description' => __( 'Current page of the collection.', 'easy-property-listings' ),
				'type'        => 'integer',
				'default'     => 1,
				'minimum'     => 1,
			),
			'per_page'    => array(
				'description' => __( 'Maximum number of items per page.', 'easy-property-listings' ),
				'type'        => 'integer',
				'default'     => 25,
				'minimum'     => 1,
				'maximum'     => 100,
			),
			'search'      => array(
				'description' => __( 'Search term.', 'easy-property-listings' ),
				'type'        => 'string',
			),
			'post_type'   => array(
				'description' => __( 'Post type(s) to query.', 'easy-property-listings' ),
				'type'        => 'array',
				'items'       => array( 'type' => 'string' ),
			),
			'status'      => array(
				'description' => __( 'Property status.', 'easy-property-listings' ),
				'type'        => 'string',
				'enum'        => array( 'current', 'sold', 'leased', 'withdrawn', 'offmarket', 'deleted' ),
			),
			'author'      => array(
				'description' => __( 'Author user ID.', 'easy-property-listings' ),
				'type'        => 'integer',
			),
			'suburb'      => array(
				'description' => __( 'Suburb filter.', 'easy-property-listings' ),
				'type'        => 'string',
			),
			'featured'    => array(
				'description' => __( 'Featured listings only.', 'easy-property-listings' ),
				'type'        => 'boolean',
			),
			'orderby'     => array(
				'description' => __( 'Sort field.', 'easy-property-listings' ),
				'type'        => 'string',
				'default'     => 'modified',
			),
			'order'       => array(
				'description' => __( 'Sort order.', 'easy-property-listings' ),
				'type'        => 'string',
				'default'     => 'desc',
				'enum'        => array( 'asc', 'desc' ),
			),
			'date_after'  => array(
				'description' => __( 'Listings after this date.', 'easy-property-listings' ),
				'type'        => 'string',
				'format'      => 'date-time',
			),
			'date_before' => array(
				'description' => __( 'Listings before this date.', 'easy-property-listings' ),
				'type'        => 'string',
				'format'      => 'date-time',
			),
		);
	}

	/**
	 * Get params for update listing endpoint.
	 *
	 * @return array
	 */
	public function get_update_listing_params() {
		return array(
			'id'     => array(
				'description' => __( 'Post ID.', 'easy-property-listings' ),
				'type'        => 'integer',
				'required'    => true,
			),
			'fields' => array(
				'description' => __( 'Fields to update.', 'easy-property-listings' ),
				'type'        => 'object',
				'required'    => true,
			),
		);
	}

	/**
	 * Get params for bulk action endpoint.
	 *
	 * @return array
	 */
	public function get_bulk_action_params() {
		return array(
			'action' => array(
				'description' => __( 'Bulk action to perform.', 'easy-property-listings' ),
				'type'        => 'string',
				'required'    => true,
				'enum'        => array( 'trash', 'restore', 'delete', 'set_status', 'set_featured' ),
			),
			'ids'    => array(
				'description' => __( 'Post IDs to act on.', 'easy-property-listings' ),
				'type'        => 'array',
				'items'       => array( 'type' => 'integer' ),
				'required'    => true,
			),
			'value'  => array(
				'description' => __( 'Value for the action.', 'easy-property-listings' ),
				'type'        => array( 'string', 'boolean' ),
			),
		);
	}
}

/**
 * Register the EPL Admin Listings REST routes.
 *
 * @since 4.0.0
 */
function epl_register_admin_listings_rest_routes() {
	$controller = new EPL_Admin_Listings_REST();
	$controller->register_routes();
}
add_action( 'rest_api_init', 'epl_register_admin_listings_rest_routes' );
