<?php
/**
 * SHORTCODE :: Listing [listing]
 *
 * @package     EPL
 * @subpackage  Shortcode/Listing
 * @copyright   Copyright (c) 2018, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.2.3
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class EPL_Shortcode_Listing {

	public $default_args;

	public $atts; // atts passed to shortcode

	public $attributes; // atts passed to shortcode + defaults

	public $args; // array of arguments passed to WP_Query

	public $meta_key_price = 'property_price';

	function __construct($atts,$overrides = array() ) {
		$this->atts = $atts;
		$this->shortcode_atts();
		$this->override_atts($overrides);
		$this->build_query();
		$this->set_query();
	}

	function override_atts($overrides) {

		if( !empty($overrides) ) {

			foreach($overrides as $key 	=>	$value) {
				$this->set_attribute($key,$value);
			}
		}
	}

	function get_default_post_types() {

		$property_types = epl_get_active_post_types();
		if ( ! empty($property_types ) ) {
			$property_types = array_keys( $property_types );
		}

		return $property_types;
	}

	function get_meta_key_price() {

		if ( is_string( $this->attributes['post_type'] ) && $this->attributes['post_type'] == 'rental' ) {
			$this->meta_key_price = 'property_rent';
		} else {
			$this->meta_key_price = 'property_price';
		}

		return $this->meta_key_price;
	}

	function get_default_args() {

		/**
		 *
		 * Template 		: used to render each listing in loop.
		 * 
		 * Default to loop-listing-blog-default.php
		 *
		 * To override copy loop-listing-blog-default.php from EPL/templates/content to 
		 * active_theme/easypropertylistings and rename the file to loop-listing-blog-{$template}.php
		 *
		 * 
		 * wrap_template 	: Used to render wrapper of listings render using shortcode.
		 *
		 * Default to shortcode-listing.php
		 *
		 * to override copy shortcode-listing.php from EPL/templates/content to 
		 * active_theme/easypropertylistings and rename the file to 
		 * shortcode-listing-{$wrap_template}.php
		 */

		$this->default_args = array(
			'post_type'               => $this->get_default_post_types(), //Post Type
			'status'                  => array( 'current', 'sold', 'leased' ),
			'commercial_listing_type' =>	'', // Listing Type, 'sale' , 'lease', 'both'
			'feature'                 =>	'', // Feature slug
			'feature_id'              =>	'', // Feature ID
			'limit'                   => '10', // Number of maximum posts to show
			'offset'                  => '', // Offset posts. When used, pagination is disabled
			'author'                  => '',	// Author of listings.
			'featured'                => 0,	// Featured listings.
			'open_house'              =>	false, // only show open house
			'auction'                 =>	false, // only show properties for auction
			'class'                   =>	'epl-shortcode-listing', // wrapper class		
			'wrap_template'           =>	'', // explained in detail below
			'template'                => false, // Template can be set to "slim" for home open style template
			'location'                => '', // Location slug. Should be a name like sorrento
			'location_id'             =>	'', // Location ID
			'tools_top'               => 'off', // Tools before the loop like Sorter and Grid on or off
			'tools_bottom'            => 'off', // Tools after the loop like pagination on or off
			'sortby'                  => '', // Options: price, date : Default date
			'orderby_clause'          =>	'', //  order by two different pieces of postmeta (for example, suburb first and state second)
			'sort_order'              => 'DESC', // Sort by ASC or DESC
			'query_object'            => '', // only for internal use . if provided use it instead of custom query
			'pagination'              => 'on', // Enable or disable pagination
			'post__in'                => '',	// show only these posts
			'post__not_in'            => '' // dont show these posts
		);

		return $this->default_args;
	}

	function shortcode_atts() {
		$this->attributes = shortcode_atts( $this->get_default_args(), $this->atts );
	}

	function set_attribute($key,$value) {

		if( isset($this->attributes[$key]) ) {
			$this->attributes[$key] = $value;
		}
	}

	function get_attribute($key) {

		return isset($this->attributes[$key]) ? $this->attributes[$key] : null;
	}

	function set_post_type() {
		if ( ! is_array( $this->attributes['post_type'] ) ) {
			$this->attributes['post_type'] = 
			array_map( 'trim', explode( ',',$this->attributes['post_type'] ) );
		}
	}

	function set_initial_args() {

		$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
		$this->args = array(
			'post_type'      =>	$this->attributes['post_type'],
			'posts_per_page' =>	$this->attributes['limit'],
			'paged'          =>	absint( $paged ),
		);
	}

	function set_offset() {

		// Offset query does not work with pagination
		if ( ! empty ( $this->attributes['offset'] ) ) {
			$this->args['offset'] 		= $this->attributes['offset'];
			$this->attributes['pagination'] 	= 'off'; // Disable pagination when offset is used
		}
	}

	function set_post__in() {
		if ( ! empty( $this->attributes['post__in'] ) ) {
			$post__in = array_map( 'trim', explode( ',',$this->attributes['post__in'] ) );
			if( !empty($post__in) ){
				$this->args['post__in'] = $post__in;
			}
		}
	}

	function set_post__not_in() {
		if ( ! empty( $this->attributes['post__not_in'] ) ) {
			$post__not_in = array_map( 'trim', explode( ',',$this->attributes['post__not_in'] ) );
			if( !empty($post__not_in) ){
				$this->args['post__not_in'] = $post__not_in;
			}
		}
	}

	function process_epl_atts() {

		$this->args['meta_query'] = epl_parse_atts($this->atts);
	}

	function set_author() {

		// Listings of specified author.
		if ( ! empty( $this->attributes['author'] ) ) {
			$this->attributes['author'] = (array) $this->attributes['author'];
			if ( is_array( $this->attributes['author'] ) ) {
				$author_ids = array_map( 'epl_get_author_id_from_name', $this->attributes['author'] );
				$this->attributes['author'] = implode( ',', $author_ids );
			}
			$this->args['author'] = trim( $this->attributes['author'] );
		}
	}

	function set_featured() {

		// Featured listings.
		if ( $this->attributes['featured'] ) {
			$this->args['meta_query']['featured_clause'] = array(
				'key'   => 'property_featured',
				'value' => 'yes',
			);
		}
	}

	function set_auction() {

		// auction only listings
		if ( $this->attributes['auction'] ) {
			$this->args['meta_query']['property_authority_clause'] = array(
				'key' 		=> 'property_authority',
				'value' 	=> 'auction'
			);
		}
	}

	function set_open_house() {

		// open house only ?
		if ( $this->attributes['open_house'] ) {
			$this->args['meta_query']['open_house_clause'] = array(
				'key' 		=> 'property_inspection_times',
				'value' 	=> '',
				'compare' 	=> '!=',
			);
		}
	}

	function set_location_tax_query() {

		/** Location taxonomy */

		/** Query by slug */
		if ( ! empty( $this->attributes['location'] ) ) {
			if ( ! is_array( $this->attributes['location'] ) ) {
				$this->attributes['location'] = array_map( 'trim', explode( ',', $this->attributes['location'] ) );

				$this->args['tax_query']['tax_location_clause'] = array(
					'taxonomy'	=> 'location',
					'field'		=> 'slug',
					'terms' 	=> $this->attributes['location'],
				);
			}
		}

		/** Query by ID */
		if ( ! empty( $this->attributes['location_id'] ) ) {
			if ( ! is_array( $this->attributes['location_id'] ) ) {
				$this->attributes['location_id'] = array_map( 'trim', explode( ',', $this->attributes['location_id'] ) );

				$this->args['tax_query']['tax_location_id_clause'] = array(
					'taxonomy'	=> 'location',
					'field'		=> 'id',
					'terms' 	=> $this->attributes['location_id'],
				);
			}
		}

	}

	function set_features_tax_query() {

		/** Features taxonomy */

		/** Query by slug */
		if ( ! empty( $this->attributes['feature'] ) ) {
			if ( ! is_array( $this->attributes['feature'] ) ) {
				$this->attributes['feature'] = array_map( 'trim', explode( ',', $this->attributes['feature'] ) );

				$this->args['tax_query']['tax_feature_clause'] = array(
					'taxonomy'	=> 'tax_feature',
					'field'		=> 'slug',
					'terms' 	=> $this->attributes['feature'],
				);
			}
		}
		/** Query by ID */
		if ( ! empty( $this->attributes['feature_id'] ) ) {
			if ( ! is_array( $this->attributes['feature_id'] ) ) {
				$this->attributes['feature_id'] = array_map( 'trim', explode( ',', $this->attributes['feature_id'] ) );

				$this->args['tax_query']['tax_feature_id_clause'] = array(
					'taxonomy'	=> 'tax_feature',
					'field'		=> 'id',
					'terms' 	=> $this->attributes['feature_id'],
				);
			}
		}

	}

	function set_status() {

		if ( ! empty( $this->attributes['status'] ) ) {
			if ( ! is_array( $this->attributes['status'] ) ) {
				$this->attributes['status'] = array_map( 'trim', explode( ',', $this->attributes['status'] ) );

				$this->args['meta_query']['property_status_clause'] = array(
					'key'		=> 'property_status',
					'value'		=> $this->attributes['status'],
					'compare'	=> 'IN',
				);

				add_filter( 'epl_sorting_options', 'epl_sorting_options_callback' );
			}
		}
	}

	function set_commercial_listing_type() {

		/** Commercial listing type */
		if ( ! empty( $this->attributes['commercial_listing_type'] ) ) {
			if ( ! is_array( $this->attributes['commercial_listing_type'] ) ) {
				$this->attributes['commercial_listing_type'] = array_map( 'trim', explode( ',', $this->attributes['commercial_listing_type'] ) );

				$this->args['meta_query']['property_com_listing_type_clause'] = array(
					'key'		=> 'property_com_listing_type',
					'value'		=> $this->attributes['commercial_listing_type'],
					'compare'	=> 'IN',
				);

			}
		}
	}

	function set_orderby() {

		if ( ! empty ( $this->attributes['sortby'] ) ) {
			if ( $this->attributes['sortby'] == 'price' ) {
				$this->args['orderby']  = 'meta_value_num';
				$this->args['meta_key'] =	$this->get_meta_key_price();
			} else {
				$this->args['orderby']  = 'post_date';
				$this->args['order']    = 'DESC';
			}
			$this->args['order']        = $this->attributes['sort_order'];
		}
	}

	function set_orderby_clause() {

		/**
		 * Advance orderby using named meta query clauses,
		 * will override default orderby
		 */
		
		if ( ! empty ( $this->attributes['orderby_clause'] ) ) {

			$this->attributes['orderby_clause'] = 
			array_map( 'trim', explode( ',',$this->attributes['orderby_clause'] ) );
			foreach( $this->attributes['orderby_clause'] as $oc ) {
				$oc_array = explode( '|',$oc );
				
				$this->args['orderby'][$oc_array[0]] = $oc_array[1];
				
			}

			unset($this->args['order']);
		}

		// add sortby arguments to query, if listings sorted by $_GET['sortby'];
		$this->args = epl_add_orderby_args($this->args);
	}

	function set_query() {

		$this->query_open = new WP_Query( $this->args );

		if ( is_object( $this->attributes['query_object'] ) ) {
			$this->query_open = $this->attributes['query_object'];
		}
	}

	function get_wrap_template() {

		$attributes['wrap_template'] = str_replace( '_', '-', $this->attributes['wrap_template'] );

		$wrap_template = 
		$this->attributes['wrap_template'] == '' ? 
		'shortcode-listing.php' : 'shortcode-listing-'.$this->attributes['wrap_template'].'.php';

		return $wrap_template;
	}

	function build_query() {

		$this->set_post_type();
		$this->set_initial_args();
		$this->set_offset();
		$this->set_post__in();
		$this->set_post__not_in();
		$this->process_epl_atts();
		$this->set_author();
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

	function render() {
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