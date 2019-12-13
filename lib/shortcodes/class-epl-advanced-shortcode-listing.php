<?php
/**
 * SHORTCODE :: Listing [listing_advanced]
 *
 * @package     EPL
 * @subpackage  Shortcode/Listing
 * @copyright   Copyright (c) 2019, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.3.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// phpcs:disable WordPress.DB.SlowDBQuery

/**
 * EPL_Advanced_Shortcode_Listing Class
 *
 * @since 3.3.0
 */
class EPL_Advanced_Shortcode_Listing {

	/**
	 * Default arguments.
	 *
	 * @since 3.3.0
	 * @var array $default_args Default arguments.
	 */
	public $default_args;

	/**
	 * Attributes passed to shortcode.
	 *
	 * @since 3.3.0
	 * @var array $atts Shortcode attributes.
	 */
	public $atts;

	/**
	 * Attributes passed to shortcode and defaults.
	 *
	 * @since 3.3.0
	 * @var array $attributes Shortcode attributes.
	 */
	public $attributes;

	/**
	 * Array of arguments passed to WP_Query.
	 *
	 * @since 3.3.0
	 * @var array $args Arguments.
	 */
	public $args;

	/**
	 * Meta price key.
	 *
	 * @since 3.3.0
	 * @var string $meta_key_price Default price meta value.
	 */
	public $meta_key_price = 'property_price';

	/**
	 * Construct the shortcode.
	 *
	 * @since 3.3.0
	 * @param array $atts Shortcode attributes.
	 * @param array $overrides Array of variables to override defaults.
	 */
	public function __construct( $atts, $overrides = array() ) {
		$this->atts = $atts;
		$this->shortcode_atts();
		$this->override_atts( $overrides );
		$this->build_query();
		$this->set_query();
	}

	/**
	 * Override Attributes.
	 *
	 * @since 3.3.0
	 * @param array $overrides Array of variables to override defaults.
	 */
	public function override_atts( $overrides ) {

		if ( ! empty( $overrides ) ) {

			foreach ( $overrides as $key  => $value ) {
				$this->set_attribute( $key, $value );
			}
		}
	}

	/**
	 * Get default post types.
	 *
	 * @since 3.3
	 */
	public function get_default_post_types() {

		$property_types = epl_get_active_post_types();
		if ( ! empty( $property_types ) ) {
			$property_types = array_keys( $property_types );
		}

		return $property_types;
	}

	/**
	 * Get meta key price.
	 *
	 * @since 3.3
	 */
	public function get_meta_key_price() {

		if ( is_string( $this->attributes['post_type'] ) && 'rental' === $this->attributes['post_type'] ) {
			$this->meta_key_price = 'property_rent';
		} else {
			$this->meta_key_price = 'property_price';
		}

		return $this->meta_key_price;
	}

	/**
	 * Get default options.
	 *
	 * @since 3.3
	 */
	public function get_default_args() {

		/**
		 *
		 * Template     : used to render each listing in loop.
		 *
		 * Default to loop-listing-blog-default.php
		 *
		 * To override copy loop-listing-blog-default.php from EPL/templates/content to
		 * active_theme/easypropertylistings and rename the file to loop-listing-blog-{$template}.php
		 *
		 *
		 * wrap_template    : Used to render wrapper of listings render using shortcode.
		 *
		 * Default to shortcode-listing.php
		 *
		 * to override copy shortcode-listing.php from EPL/templates/content to
		 * active_theme/easypropertylistings and rename the file to
		 * shortcode-listing-{$wrap_template}.php
		 */

		$this->default_args = array(
			'post_type'               => $this->get_default_post_types(), // Post Type.
			'status'                  => array( 'current', 'sold', 'leased' ),
			'commercial_listing_type' => '', // Listing Type, sale lease and both.
			'feature'                 => '', // Feature slug.
			'feature_id'              => '', // Feature ID.
			'limit'                   => '10', // Number of maximum posts to show.
			'offset'                  => '', // Offset posts. When used, pagination is disabled.
			'author'                  => '', // Author of listings.
			'agent'                   => '', // listings by agent.
			'featured'                => 0, // Featured listings.
			'open_house'              => false, // only show open house.
			'auction'                 => false, // only show properties for auction.
			'class'                   => 'epl-shortcode-listing', // wrapper class.
			'wrap_template'           => '', // explained in detail below.
			'template'                => false, // Template can be set to "slim" for home open style template.
			'location'                => '', // Location slug. Should be a name like sorrento.
			'location_id'             => '', // Location ID.
			'tools_top'               => 'off', // Tools before the loop like Sorter and Grid on or off.
			'tools_bottom'            => 'off', // Tools after the loop like pagination on or off.
			'sortby'                  => '', // Options: price, date : Default date.
			'orderby_clause'          => '', // order by two different pieces of postmeta (for example, suburb first and state second).
			'sort_order'              => 'DESC', // Sort by ASC or DESC.
			'query_object'            => '', // only for internal use . if provided use it instead of custom query.
			'pagination'              => 'on', // Enable or disable pagination.
			'post__in'                => '', // show only these posts.
			'post__not_in'            => '', // dont show these posts.
			'instance_id'             => '1', // when using multiple shortcodes on once page.
		);

		return $this->default_args;
	}

	/**
	 * Shortcode attributes
	 *
	 * @since 3.3
	 */
	public function shortcode_atts() {
		$this->attributes = shortcode_atts( $this->get_default_args(), $this->atts );
	}

	/**
	 * Set attributes
	 *
	 * @since 3.3
	 * @param string $key Meta key.
	 * @param string $value Meta value.
	 */
	public function set_attribute( $key, $value ) {

		if ( isset( $this->attributes[ $key ] ) ) {
			$this->attributes[ $key ] = $value;
		}
	}

	/**
	 * Get attributes
	 *
	 * @param string $key Meta key.
	 *
	 * @return mixed|null
	 * @since 3.3
	 */
	public function get_attribute( $key ) {

		return isset( $this->attributes[ $key ] ) ? $this->attributes[ $key ] : null;
	}

	/**
	 * Set the post type
	 *
	 * @since 3.3
	 */
	public function set_post_type() {
		if ( ! is_array( $this->attributes['post_type'] ) ) {
			$this->attributes['post_type'] =
			array_map( 'trim', explode( ',', $this->attributes['post_type'] ) );
		}
	}

	/**
	 * Set initial options
	 *
	 * @since 3.3
	 */
	public function set_initial_args() {

		$paged      = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
		$this->args = array(
			'post_type'      => $this->attributes['post_type'],
			'posts_per_page' => $this->attributes['limit'],
			'paged'          => absint( $paged ),
		);
	}

	/**
	 * Set the offset
	 *
	 * @since 3.3
	 */
	public function set_offset() {

		// Offset query does not work with pagination.
		if ( ! empty( $this->attributes['offset'] ) ) {
			$this->args['offset']           = $this->attributes['offset'];
			$this->attributes['pagination'] = 'off'; // Disable pagination when offset is used.
		}
	}

	/**
	 * Set posts in
	 *
	 * @since 3.3
	 */
	public function set_post__in() {
		if ( ! empty( $this->attributes['post__in'] ) ) {
			$post__in = array_map( 'trim', explode( ',', $this->attributes['post__in'] ) );
			if ( ! empty( $post__in ) ) {
				$this->args['post__in'] = $post__in;
			}
		}
	}

	/**
	 * Set posts not in
	 *
	 * @since 3.3
	 */
	public function set_post__not_in() {
		if ( ! empty( $this->attributes['post__not_in'] ) ) {
			$post__not_in = array_map( 'trim', explode( ',', $this->attributes['post__not_in'] ) );
			if ( ! empty( $post__not_in ) ) {
				$this->args['post__not_in'] = $post__not_in;
			}
		}
	}

	/**
	 * Process EPL attributes
	 *
	 * @since 3.3
	 */
	public function process_epl_atts() {

		$this->args['meta_query'] = epl_parse_atts( $this->atts );
	}

	/**
	 * Set the author
	 *
	 * @since 3.3
	 */
	public function set_author() {

		// Listings of specified author.
		if ( ! empty( $this->attributes['author'] ) ) {
			$this->attributes['author'] = (array) $this->attributes['author'];
			if ( is_array( $this->attributes['author'] ) ) {
				$author_ids                 = array_map( 'epl_get_author_id_from_name', $this->attributes['author'] );
				$this->attributes['author'] = implode( ',', $author_ids );
			}
			$this->args['author'] = trim( $this->attributes['author'] );
		}
	}

	/**
	 * Set the agent
	 *
	 * @since 3.3
	 */
	public function set_agent() {

		// Listings by specified agent.
		if ( ! empty( $this->attributes['agent'] ) ) {
			$this->attributes['agent'] = array_map( 'trim', explode( ',', $this->attributes['agent'] ) );
			$this->attributes['agent'] = array_filter( $this->attributes['agent'] );

			$agent_meta_query = array(
				'relation' => 'OR',
			);

			foreach ( $this->attributes['agent'] as $single_agent ) {
				$agent_meta_query[] = array(
					'key'     => 'property_agent',
					'value'   => array( $single_agent, sanitize_user( $single_agent ) ),
					'compare' => 'IN',
				);
				$agent_meta_query[] = array(
					'key'     => 'property_second_agent',
					'value'   => array( $single_agent, sanitize_user( $single_agent ) ),
					'compare' => 'IN',
				);
			}

			$this->args['meta_query']['agent_clause'] = $agent_meta_query;
		}
	}

	/**
	 * Set featured
	 *
	 * @since 3.3
	 */
	public function set_featured() {

		// Featured listings.
		if ( $this->attributes['featured'] ) {
			$this->args['meta_query']['featured_clause'] = array(
				'key'   => 'property_featured',
				'value' => 'yes',
			);
		}
	}

	/**
	 * Set auction
	 *
	 * @since 3.3
	 */
	public function set_auction() {

		// Auction only listings.
		if ( $this->attributes['auction'] ) {
			$this->args['meta_query']['property_authority_clause'] = array(
				'key'   => 'property_authority',
				'value' => 'auction',
			);
		}
	}

	/**
	 * Set open for inspection
	 *
	 * @since 3.3
	 */
	public function set_open_house() {

		// Open house only.
		if ( $this->attributes['open_house'] ) {
			$this->args['meta_query']['open_house_clause'] = array(
				'key'     => 'property_inspection_times',
				'value'   => '',
				'compare' => '!=',
			);
		}
	}

	/**
	 * Set location taxonomy query
	 *
	 * @since 3.3
	 */
	public function set_location_tax_query() {

		// Location taxonomy.

		// Query by slug.
		if ( ! empty( $this->attributes['location'] ) ) {
			if ( ! is_array( $this->attributes['location'] ) ) {
				$this->attributes['location'] = array_map( 'trim', explode( ',', $this->attributes['location'] ) );

				$this->args['tax_query']['tax_location_clause'] = array(
					'taxonomy' => 'location',
					'field'    => 'slug',
					'terms'    => $this->attributes['location'],
				);
			}
		}

		// Query by ID.
		if ( ! empty( $this->attributes['location_id'] ) ) {
			if ( ! is_array( $this->attributes['location_id'] ) ) {
				$this->attributes['location_id'] = array_map( 'trim', explode( ',', $this->attributes['location_id'] ) );

				$this->args['tax_query']['tax_location_id_clause'] = array(
					'taxonomy' => 'location',
					'field'    => 'id',
					'terms'    => $this->attributes['location_id'],
				);
			}
		}
	}

	/**
	 * Set feeatures taxonomy query
	 *
	 * @since 3.3
	 */
	public function set_features_tax_query() {

		// Features taxonomy.

		// Query by slug.
		if ( ! empty( $this->attributes['feature'] ) ) {
			if ( ! is_array( $this->attributes['feature'] ) ) {
				$this->attributes['feature'] = array_map( 'trim', explode( ',', $this->attributes['feature'] ) );

				$this->args['tax_query']['tax_feature_clause'] = array(
					'taxonomy' => 'tax_feature',
					'field'    => 'slug',
					'terms'    => $this->attributes['feature'],
				);
			}
		}
		// Query by ID.
		if ( ! empty( $this->attributes['feature_id'] ) ) {
			if ( ! is_array( $this->attributes['feature_id'] ) ) {
				$this->attributes['feature_id'] = array_map( 'trim', explode( ',', $this->attributes['feature_id'] ) );

				$this->args['tax_query']['tax_feature_id_clause'] = array(
					'taxonomy' => 'tax_feature',
					'field'    => 'id',
					'terms'    => $this->attributes['feature_id'],
				);
			}
		}
	}

	/**
	 * Set status
	 *
	 * @since 3.3
	 */
	public function set_status() {

		if ( ! empty( $this->attributes['status'] ) ) {
			if ( ! is_array( $this->attributes['status'] ) ) {
				$this->attributes['status'] = array_map( 'trim', explode( ',', $this->attributes['status'] ) );

				$this->args['meta_query']['property_status_clause'] = array(
					'key'     => 'property_status',
					'value'   => $this->attributes['status'],
					'compare' => 'IN',
				);

				add_filter( 'epl_sorting_options', 'epl_sorting_options_callback' );
			}
		}
	}

	/**
	 * Set commercial listing type
	 *
	 * @since 3.3
	 */
	public function set_commercial_listing_type() {

		/** Commercial listing type */
		if ( ! empty( $this->attributes['commercial_listing_type'] ) ) {
			if ( ! is_array( $this->attributes['commercial_listing_type'] ) ) {
				$this->attributes['commercial_listing_type'] = array_map( 'trim', explode( ',', $this->attributes['commercial_listing_type'] ) );

				$this->args['meta_query']['property_com_listing_type_clause'] = array(
					'key'     => 'property_com_listing_type',
					'value'   => $this->attributes['commercial_listing_type'],
					'compare' => 'IN',
				);
			}
		}
	}

	/**
	 * Set orderby
	 *
	 * @since 3.3
	 */
	public function set_orderby() {

		if ( ! empty( $this->attributes['sortby'] ) ) {
			if ( 'price' === $this->attributes['sortby'] ) {
				$this->args['orderby']  = 'meta_value_num';
				$this->args['meta_key'] = $this->get_meta_key_price();
			} elseif ( 'rand' === $this->attributes['sortby'] ) {
				$this->args['orderby'] = 'rand';
			} elseif ( 'status' === $this->attributes['sortby'] ) {
				$this->args['orderby']  = 'meta_value';
				$this->args['meta_key'] = 'property_status';
			} else {
				$this->args['orderby'] = 'post_date';
				$this->args['order']   = 'DESC';
			}
			$this->args['order'] = $this->attributes['sort_order'];
		}
	}

	/**
	 * Set orderby clause
	 *
	 * @since 3.3
	 */
	public function set_orderby_clause() {

		/**
		 * Advance orderby using named meta query clauses, will override default orderby.
		 */

		if ( ! empty( $this->attributes['orderby_clause'] ) ) {

			$this->attributes['orderby_clause'] =
			array_map( 'trim', explode( ',', $this->attributes['orderby_clause'] ) );
			foreach ( $this->attributes['orderby_clause'] as $oc ) {
				$oc_array = explode( '|', $oc );

				$this->args['orderby'][ $oc_array[0] ] = $oc_array[1];

			}

			unset( $this->args['order'] );
		}

		$args['instance_id'] = $this->attributes['instance_id'];

		// add sortby arguments to query, if listings sorted by $_GET['sortby'];.
		$args = epl_add_orderby_args( $args, 'shortcode', 'listing_advanced' );

		// Option to filter args.
		$args = apply_filters( 'epl_shortcode_listing_advanced_args', $this->args, $this->attributes );
	}

	/**
	 * Set query
	 *
	 * @since 3.3
	 */
	public function set_query() {

		$this->query_open = new WP_Query( $this->args );

		if ( is_object( $this->attributes['query_object'] ) ) {
			$this->query_open = $this->attributes['query_object'];
		}
	}

	/**
	 * Get the template
	 *
	 * @since 3.3
	 */
	public function get_wrap_template() {

		$attributes['wrap_template'] = str_replace( '_', '-', $this->attributes['wrap_template'] );

		$wrap_template = empty( $this->attributes['wrap_template'] ) ? 'shortcode-listing.php' : 'shortcode-listing-' . $this->attributes['wrap_template'] . '.php';

		return $wrap_template;
	}

	/**
	 * Build the query
	 *
	 * @since 3.3
	 */
	public function build_query() {

		$this->set_post_type();
		$this->set_initial_args();
		$this->set_offset();
		$this->set_post__in();
		$this->set_post__not_in();
		$this->process_epl_atts();
		$this->set_author();
		$this->set_agent();
		$this->set_featured();
		$this->set_auction();
		$this->set_open_house();
		$this->set_location_tax_query();
		$this->set_features_tax_query();
		$this->set_status();
		$this->set_commercial_listing_type();
		$this->set_orderby();
		$this->set_orderby_clause();
	}

	/**
	 * Render the shortcode
	 *
	 * @since 3.3
	 */
	public function render() {
		$wrap_template = $this->get_wrap_template();
		ob_start();
		epl_get_template_part(
			$wrap_template,
			array(
				'attributes' => $this->attributes,
				'query_open' => $this->query_open,
			)
		);

		return ob_get_clean();
	}
}
