<?php
/**
 * Search Object
 *
 * @package     EPL
 * @subpackage  Classes/Search
 * @copyright   Copyright (c) 2020, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.1
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * EPL_SEARCH Class
 *
 * @since      3.1
 */
class EPL_SEARCH {

	/**
	 * WP_QUERY Object
	 *
	 * @var object
	 */
	private $query;

	/**
	 * Search data received from search widget / seach shortcode via GET method
	 *
	 * @var array
	 */
	private $get_data = array();

	/**
	 * Search data received from search widget / seach shortcode via POST method
	 *
	 * @var array
	 */
	private $post_data = array();

	/**
	 * Meta query array
	 *
	 * @var array
	 */
	private $meta_query = array();

	/**
	 * Tax query array
	 *
	 * @var array
	 */
	private $tax_query = array();

	/**
	 * Fields to be skipped
	 *
	 * @var array
	 */
	private $skip_field = array();

	/**
	 * Post type to search for
	 *
	 * @var string
	 */
	private $post_type;

	/**
	 * Transaction type for current post type sale/lease
	 *
	 * @var string
	 */
	private $transaction_type = null;

	/**
	 * Holds the submitted form data.
	 *
	 * @var array $data form data.
	 */
	public $data;

	/**
	 * Holds skipped fields.
	 *
	 * @var array $skip_fields Skipped fields.
	 */
	public $skip_fields;

	/**
	 * Holds Array of form fields.
	 *
	 * @var array $form_fields form fields.
	 */
	public $form_fields;


	/**
	 * Get things going
	 *
	 * @since 3.2
	 * @since 3.5.13 Setting page name to null on search pages.
	 *
	 * @param WP_Query $query object of the wp_query object.
	 * @param array    $data  associative array for fields to search.
	 */
	public function __construct( $query, $data ) {

		$this->data = $data;

		$this->query = $query;

		$this->sanitize_data();

		$this->skip_unnecessary_fields();

		$this->query = apply_filters( 'epl_search_query_before_processing', $this->query, $this->get_data );

		$this->set_per_page();

		$this->set_pagination();

		$this->set_title();

		$this->set_property_agent();

		$this->set_post_type();

		$this->modify_fields();

		$this->prepare_query();

		$this->epl_search_query_pre_search();

		$this->query = apply_filters( 'epl_search_query_pre_search', $this->query, $this->get_data );

		$this->set_query();

		$this->query = apply_filters( 'epl_search_query_pre_parse', $this->query, $this->get_data );

		$this->query->parse_query();

		// Disable is_* flags which can create conflict with EPL search query flag to avoid redirection to tax archive url.
		$this->query->is_tax        = false;
		$this->query->is_page       = false;
		$this->query->is_single     = false;
		$this->query->is_singular   = false;
		$this->query->is_epl_search = true;
		set_query_var( 'page_id', null );
		set_query_var( 'pagename', null );
	}

	/**
	 * These are list of fields which are not directly queried,
	 * but their value is used to query other meta data / post data / tax data
	 *
	 * @return array
	 */
	protected function altered_fields() {
		return apply_filters(
			'epl_search_altered_fields',
			array(
				'property_unique_id',
			)
		);
	}

	/**
	 * Sanitize an array of data recursively.
	 *
	 * @param array|string $data The data to be sanitized.
	 * @since 3.5.0
	 */
	public function sanitize_recursive( $data ) {
		if ( is_array( $data ) ) {
			return array_map( array( $this, 'sanitize_recursive' ), $data );
		} else {
			return sanitize_text_field( $data );
		}
	}

	/**
	 * Sanitize whole GET & POST array, insures security from XSS
	 *
	 * @since 3.5.0 replaced depricated code for sanitization.
	 */
	protected function sanitize_data() {

		$this->get_data = $this->sanitize_recursive( $_GET );
		if ( ! empty( $this->data ) ) {
			$this->get_data = $this->data;
		}
		$this->get_data  = apply_filters( 'epl_search_get_data', $this->get_data );
		$this->post_data = $this->sanitize_recursive( $_POST );
		$this->post_data = apply_filters( 'epl_search_post_data', $this->post_data );

		if ( ! isset( $this->get_data['property_status'] ) ) {
			$this->get_data['property_status'] = '';
		}
	}

	/**
	 * Set list of fields which are not to be queried
	 */
	protected function fields_to_skip() {
		// Keys of fields to be skipped.
		$fields            = array( 'action' );
		$this->skip_fields = apply_filters( 'epl_skip_search_fields', $fields, $this->get_data );
	}

	/**
	 * Skip unnecessay fields
	 */
	protected function skip_unnecessary_fields() {
		$this->fields_to_skip();
		if ( ! empty( $this->skip_fields ) ) {
			foreach ( $this->skip_fields as $key => &$field ) {
				if ( isset( $this->get_data[ $key ] ) ) {
					unset( $this->get_data[ $key ] );
				}
			}
		}
	}

	/**
	 * Set per page for query
	 */
	protected function set_per_page() {
		$posts_per_page = apply_filters( 'epl_search_results_per_page', get_option( 'posts_per_page' ) );
		$this->query->set( 'posts_per_page', $posts_per_page );
	}

	/**
	 * Set pagination for query
	 */
	protected function set_pagination() {
		$paged = $this->query->get( 'paged', 1 );
		$this->query->set( 'paged', $paged );
	}

	/**
	 * Set title for query
	 */
	protected function set_title() {
		if ( isset( $this->get_data['property_address'] ) ) {
			$this->query->set( 'epl_post_title', sanitize_text_field( $this->get_data['property_address'] ) );
		}
	}

	/**
	 * Set property agent for query
	 */
	protected function set_property_agent() {
		if ( isset( $this->get_data['property_agent'] ) ) {
			$property_agent = sanitize_title_with_dashes( $this->get_data['property_agent'] );
			$property_agent = get_user_by( 'slug', $property_agent );
			if ( $property_agent ) {
				$this->query->set( 'author__in', array( $property_agent->ID ) );
			}
		}
	}

	/**
	 * Set post type for query
	 *
	 * @since 3.4.12 Added support for 'all' post type.
	 */
	protected function set_post_type() {
		if ( isset( $this->get_data['post_type'] ) && ! empty( $this->get_data['post_type'] ) && 'all' !== $this->get_data['post_type'] ) {
			$this->query->set( 'post_type', $this->get_data['post_type'] );
			$this->post_type = $this->get_data['post_type'];
		} else {
			$epl_post_types = epl_get_active_post_types();
			if ( ! empty( $epl_post_types ) ) {
				$epl_post_types = array_keys( $epl_post_types );
				$this->query->set( 'post_type', $epl_post_types );
				$this->post_type             = $epl_post_types;
				$this->get_data['post_type'] = $this->post_type;
			}
		}
	}

	/**
	 * Modify meta keys according to data received, for special cases
	 */
	protected function modify_fields() {
		// if  post type is commercial and listing type is set, accordingly change the price meta key.
		if ( ( 'commercial' === $this->post_type || 'commercial_land' === $this->post_type ) && isset( $this->get_data['property_com_listing_type'] ) ) {

			$type                   = $this->get_data['property_com_listing_type'];
			$this->transaction_type = 'lease' === $type ? 'lease' : 'sale';

		}
	}

	/**
	 * Set queries meta, Tax
	 */
	protected function set_query() {
		$this->set_meta_query();
		$this->set_tax_query();
	}

	/**
	 * Set meta query for the search object
	 */
	protected function set_meta_query() {
		$this->preprocess_meta_query();
		if ( ! empty( $this->meta_query ) ) {
			$this->query->set( 'meta_query', $this->meta_query );
		}
	}

	/**
	 * Set tax query for the search object
	 */
	protected function set_tax_query() {
		if ( ! empty( $this->tax_query ) ) {
			$this->query->set( 'tax_query', $this->tax_query );
		}
	}

	/**
	 * Prepare the query
	 */
	protected function prepare_query() {

		if ( 'commercial' === $this->get_data['post_type'] || 'commercial_land' === $this->get_data['post_type'] ) {
			if ( function_exists( 'epl_listing_search_commercial_widget_fields_frontend' ) ) {
				$this->form_fields = epl_listing_search_commercial_widget_fields_frontend( $this->get_data['post_type'], $this->get_data['property_status'], $this->transaction_type );

			} else {
				$this->form_fields = epl_search_widget_fields_frontend( $this->get_data['post_type'], $this->get_data['property_status'], $this->transaction_type );
			}
		} else {
			$this->form_fields = epl_search_widget_fields_frontend( $this->get_data['post_type'], $this->get_data['property_status'], $this->transaction_type );
		}

		foreach ( $this->get_data as $key => $data ) {

			if ( isset( $this->form_fields[ $key ] ) ) {

				$query_type = isset( $this->form_fields[ $key ]['query']['query'] ) ?
					$this->form_fields[ $key ]['query']['query'] : 'special';

				if ( in_array( $key, $this->altered_fields(), true ) ) {
					return;
				}

				switch ( $query_type ) {

					case 'meta':
						$this->prepare_meta_query( $this->form_fields[ $key ], $data );

						break;

					case 'tax':
						$this->prepare_tax_query( $this->form_fields[ $key ], $data );

						break;

					case 'special':
						$this->prepare_special_query( $this->form_fields[ $key ], $data );

						break;
				}
			}
		}

	}

	/**
	 * Pre search query
	 */
	public function epl_search_query_pre_search() {

		foreach ( $this->meta_query as $index => &$meta_query ) {

			if ( ! isset( $meta_query['key'] ) ) {
				continue;
			}

			if ( 'property_com_listing_type' === $meta_query['key'] ) {

				$meta_query['compare'] = 'IN';

				switch ( $meta_query['value'] ) {

					case 'sale':
						$meta_query['value'] = array( 'sale', 'both' );
						break;

					case 'lease':
						$meta_query['value'] = array( 'lease', 'both' );
						break;

					default:
						$meta_query['value'] = array( 'lease', 'both', 'sale' );
						break;

				}
			}
		}
	}

	/**
	 * Prepare meta query
	 *
	 * @param  array  $query_field Field to query.
	 * @param  string $value Value of field.
	 */
	protected function prepare_meta_query( $query_field, $value ) {

		if ( $this->is_query_multiple( $query_field ) ) {

			$this->multiple_meta_query( $query_field, $value );

		} else {

			$this->single_meta_query( $query_field, $value );
		}
	}

	/**
	 * Checks if given meta query is multiple
	 *
	 * @param  array $query_field Field to query.
	 * @return boolean
	 */
	protected function is_query_multiple( $query_field ) {

		if ( isset( $query_field['query']['multiple'] ) && true === $query_field['query']['multiple'] ) {
			return true;
		}
		return false;

	}

	/**
	 * Returns relationship for the multiple query
	 *
	 * @param  array $query_field Field to query.
	 * @return string
	 */
	protected function multiple_relation( $query_field ) {

		return isset( $query_field['query']['relation'] ) ? $query_field['query']['relation'] : 'OR';

	}

	/**
	 * Set multiple meta query
	 *
	 * @param  array  $query_field Field to query.
	 * @param  string $data Data value.
	 */
	protected function multiple_meta_query( $query_field, $data ) {

		if ( empty( $data ) ) {
			return;
		}

		$this_meta_query = array();

		if ( isset( $query_field['meta_key'] ) && ! empty( $query_field['meta_key'] ) ) {

			$this_meta_query['relation'] = $this->multiple_relation( $query_field );

			if ( ! empty( $query_field['query']['sub_queries'] ) ) {

				foreach ( $query_field['query']['sub_queries'] as $sub_query ) {

					$this_sub_query    = array(
						'key'     => $sub_query['key'],
						'value'   => $data,
						'type'    => $sub_query['type'],
						'compare' => $sub_query['compare'],
					);
					$this_meta_query[] = $this_sub_query;
				}

				$this->meta_query[] = $this_meta_query;
			}
		}

	}

	/**
	 * Set single meta query
	 *
	 * @param  array  $query_field Field to query.
	 * @param  string $data Data value.
	 */
	protected function single_meta_query( $query_field, $data ) {

		$query_meta_key = isset( $query_field['query']['key'] ) ? $query_field['query']['key'] : $query_field['meta_key'];

		if ( isset( $data ) && ! empty( $data ) ) {

			$this_meta_query = array(
				'key'   => $query_meta_key,
				'value' => $data,
			);

			isset( $query_field['query']['compare'] ) ?
				$this_meta_query['compare'] = $query_field['query']['compare'] : '';

			isset( $query_field['query']['type'] ) ?
				$this_meta_query['type'] = $query_field['query']['type'] : '';

			isset( $query_field['query']['value'] ) ?
				$this_meta_query['value'] = $query_field['query']['value'] : '';

			$this->meta_query[] = $this_meta_query;
		}
	}

	/**
	 * Preprocess meta query
	 *
	 * @since 3.1.0
	 * @since 3.4.30 Fix: Only variables can be passed via reference.
	 */
	protected function preprocess_meta_query() {

		$range_sep  = apply_filters( 'epl_search_field_range_separator', '-' );
		$option_sep = apply_filters( 'epl_search_field_option_separator', ',' );

		foreach ( $this->meta_query as $key => &$query ) {

			if ( isset( $query['compare'] ) && isset( $query['value'] )
				&& in_array( strtoupper( $query['compare'] ), array( 'IN', 'NOT IN', 'BETWEEN', 'NOT BETWEEN' ), true )
				&& ! is_array( $query['value'] ) ) {
				$query['value'] = array_map( 'trim', explode( $option_sep, $query['value'] ) );

				if ( isset( $this->form_fields[ $query['key'] ]['option_type'] ) && 'range' === $this->form_fields[ $query['key'] ]['option_type'] ) {
					$current        = explode( $range_sep, current( $query['value'] ) );
					$next           = explode( $range_sep, end( $query['value'] ) );
					$query['value'] = array(
						current( $current ),
						next( $next ),
					);
				}
			}
		}
		$this->meta_query = apply_filters( 'epl_preprocess_search_meta_query', $this->meta_query );
	}

	/**
	 * Prepare taxonomy query
	 *
	 * @param  array  $query_field Field to query.
	 * @param  string $value Data value.
	 */
	protected function prepare_tax_query( $query_field, $value ) {

		$value = (array) $value;
		$value = array_filter( $value );
		if ( ! empty( $value ) ) {
			$this->tax_query[] = array(
				'taxonomy' => preg_replace( '/^property_/', '', $query_field['meta_key'] ),
				'field'    => 'id',
				'terms'    => $value,
			);
		}

		$this->tax_query = apply_filters( 'epl_preprocess_search_tax_query', $this->tax_query );
	}

	/**
	 * Prepare special query
	 *
	 * @param  array  $query_field Field to query.
	 * @param  string $value Data value.
	 */
	protected function prepare_special_query( $query_field, $value ) {

		$this->query = apply_filters( 'epl_search_special_query', $this->query, $query_field, $value );
	}

	/**
	 * Return array of posts based on search data
	 *
	 * @return array
	 */
	public function get_posts() {

		return $this->query->get_posts();
	}
}
