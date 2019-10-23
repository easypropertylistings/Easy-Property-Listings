<?php
/**
 * SHORTCODE :: Listing Category [listing_category]
 *
 * @package     EPL
 * @subpackage  Shortcode/ListingCategory
 * @copyright   Copyright (c) 2019, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.1.1
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// phpcs:disable WordPress.DB.SlowDBQuery

/**
 * Listing Category
 *
 * This shortcode allows for you to specify the property type(s) using
 * [listing_category post_type="property" status="current,sold,leased" category_key="property_rural_category" category_key="farm"] option. You can also
 * limit the number of entries that display. using  [listing_category limit="5"]
 * Added Commercial Category Support
 *
 * @param array $atts Shortcode attributes.
 *
 * @return false|string|void
 * @since       1.1.1
 */
function epl_shortcode_listing_category_callback( $atts ) {
	$property_types = epl_get_active_post_types();
	if ( ! empty( $property_types ) ) {
		$property_types = array_keys( $property_types );
	}

	$attributes = shortcode_atts(
		array(
			'post_type'               => $property_types,
			'status'                  => array( 'current', 'sold', 'leased' ),
			'commercial_listing_type' => '', // Listing Type, 'sale' , 'lease', 'both'.
			'category_key'            => '', // Meta field key.
			'category_value'          => '', // Meta field value.
			'category_compare'        => 'IN', // Compare using '=', '!=', '>', '>=', '<', '<=', 'LIKE', 'NOT LIKE', 'EXISTS', 'NOT EXISTS', 'IN','NOT IN','BETWEEN','NOT BETWEEN'.
			'limit'                   => '10', // Number of maximum posts to show.
			'offset'                  => '', // Offset posts. When used, pagination is disabled.
			'author'                  => '', // Author of listings.
			'agent'                   => '', // Agent of listings.
			'template'                => false, // Template can be set to "slim" for home open style template.
			'location'                => '', // Location slug. Should be a name like sorrento.
			'tools_top'               => 'off', // Tools before the loop like Sorter and Grid on or off.
			'tools_bottom'            => 'off', // Tools after the loop like pagination on or off.
			'sortby'                  => '', // Options: price, date, status, rand (for random) : Default date.
			'sort_order'              => 'DESC', // Sort by ASC or DESC.
			'pagination'              => 'on', // Enable or disable pagination.
			'instance_id'             => '1', // Set instance ID when using multiple shortcodes on the same page.
			'class'                   => '', // Custom class.
		),
		$atts
	);

	$post_type               = $attributes['post_type'];
	$status                  = $attributes['status'];
	$commercial_listing_type = $attributes['commercial_listing_type'];
	$category_key            = $attributes['category_key'];
	$category_value          = $attributes['category_value'];
	$category_compare        = $attributes['category_compare'];
	$limit                   = $attributes['limit'];
	$offset                  = $attributes['offset'];
	$author                  = $attributes['author'];
	$agent                   = $attributes['agent'];
	$template                = $attributes['template'];
	$location                = $attributes['location'];
	$tools_top               = $attributes['tools_top'];
	$tools_bottom            = $attributes['tools_bottom'];
	$sortby                  = $attributes['sortby'];
	$sort_order              = $attributes['sort_order'];
	$pagination              = $attributes['pagination'];
	$instance_id             = $attributes['instance_id'];
	$class                   = $attributes['class'];

	if ( empty( $post_type ) ) {
		return;
	}

	if ( is_string( $post_type ) && 'rental' === $post_type ) {
		$meta_key_price = 'property_rent';
	} else {
		$meta_key_price = 'property_price';
	}

	$sort_options = array(
		'price' => $meta_key_price,
		'date'  => 'post_date',
	);
	if ( ! is_array( $post_type ) ) {
		$post_type = array_map( 'trim', explode( ',', $post_type ) );
	}
	ob_start();
	$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
	$args  = array(
		'post_type'      => $post_type,
		'posts_per_page' => $limit,
		'paged'          => $paged,
	);

	// Offset query does not work with pagination.
	if ( ! empty( $offset ) ) {
		$args['offset'] = $offset;
		$pagination     = 'off'; // Disable pagination when offset is used.
	}

	if ( ! empty( $location ) ) {
		if ( ! is_array( $location ) ) {
			$location = explode( ',', $location );
			$location = array_map( 'trim', $location );

			$args['tax_query'][] = array(
				'taxonomy' => 'location',
				'field'    => 'slug',
				'terms'    => $location,
			);
		}
	}

	if ( ! empty( $status ) ) {
		if ( ! is_array( $status ) ) {
			$status = explode( ',', $status );
			$status = array_map( 'trim', $status );

			$args['meta_query'][] = array(
				'key'     => 'property_status',
				'value'   => $status,
				'compare' => 'IN',
			);
		}
	}

	// Listings of specified author.
	if ( ! empty( $author ) ) {
		$author = (array) $author;
		if ( is_array( $author ) ) {
			$author_ids = array_map( 'epl_get_author_id_from_name', $author );
			$author     = implode( ',', $author_ids );
		}
		$args['author'] = trim( $author );
	}

	// Listings by specified agent.
	if ( ! empty( $agent ) ) {

		$agent            = array_map( 'trim', explode( ',', $agent ) );
		$agent            = array_filter( $agent );
		$agent_meta_query = array(
			'relation' => 'OR',
		);

		foreach ( $agent as $single_agent ) {
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

		$args['meta_query'][] = $agent_meta_query;
	}

	if ( ! empty( $commercial_listing_type ) ) {
		if ( ! is_array( $commercial_listing_type ) ) {
			$commercial_listing_type = explode( ',', $commercial_listing_type );
			$commercial_listing_type = array_map( 'trim', $commercial_listing_type );

			$args['meta_query'][] = array(
				'key'     => 'property_com_listing_type',
				'value'   => $commercial_listing_type,
				'compare' => 'IN',
			);
		}
	}

	if ( ! empty( $category_key ) && ! empty( $category_value ) ) {

		if ( ! is_array( $category_value ) && in_array( $category_compare, array( 'IN', 'NOT IN', 'BETWEEN', 'NOT BETWEEN' ), true ) ) {
			$category_value = explode( ',', $category_value );
			$category_value = array_map( 'trim', $category_value );
		}

		$this_meta_query = array(
			'key'     => $category_key,
			'value'   => $category_value,
			'compare' => $category_compare,
		);

		if ( in_array( $category_compare, array( 'BETWEEN', 'NOT BETWEEN' ), true ) ) {

			if ( is_numeric( $category_value[0] ) ) {
				$this_meta_query['type'] = 'numeric';
			}
		}
		$args['meta_query'][] = $this_meta_query;
	}

	if ( ! empty( $sortby ) ) {

		if ( 'price' === $sortby ) {
			$args['orderby']  = 'meta_value_num';
			$args['meta_key'] = $meta_key_price;
		} elseif ( 'rand' === $sortby ) {
			$args['orderby'] = 'rand';
		} elseif ( 'status' === $sortby ) {
			$args['orderby']  = 'meta_value';
			$args['meta_key'] = 'property_status';
		} else {
			$args['orderby'] = 'post_date';
			$args['order']   = 'DESC';
		}
		$args['order'] = $sort_order;
	}

	$args['instance_id'] = $attributes['instance_id'];
	// add sortby arguments to query, if listings sorted by $_GET['sortby'];.
	$args = epl_add_orderby_args( $args, 'shortcode', 'listing_category' );

	// Option to filter args.
	$args = apply_filters( 'epl_shortcode_listing_category_args', $args, $attributes );

	$query_open = new WP_Query( $args );
	if ( $query_open->have_posts() ) { ?>
		<div class="loop epl-shortcode">
			<div class="loop-content epl-shortcode-listing-category
			<?php
			echo esc_attr( epl_template_class( $template, 'archive' ) );
			echo esc_attr( $attributes['class'] );
			?>
			">
				<?php
				if ( 'on' === $tools_top ) {
					do_action( 'epl_property_loop_start' );
				}
				while ( $query_open->have_posts() ) {
					$query_open->the_post();

					$template = str_replace( '_', '-', $template );
					epl_property_blog( $template );
				}
				if ( 'on' === $tools_bottom ) {
					do_action( 'epl_property_loop_end' );
				}
				?>
			</div>
			<div class="loop-footer">
				<?php
				if ( 'on' === $pagination ) {
					do_action( 'epl_pagination', array( 'query' => $query_open ) );
				}
				?>
			</div>
		</div>
		<?php
	} else {
		do_action( 'epl_shortcode_results_message' );
	}
	wp_reset_postdata();
	return ob_get_clean();
}
add_shortcode( 'listing_category', 'epl_shortcode_listing_category_callback' );
