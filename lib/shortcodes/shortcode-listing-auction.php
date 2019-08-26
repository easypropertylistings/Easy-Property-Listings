<?php
/**
 * SHORTCODE :: Listing Auction [listing_auction]
 *
 * @package     EPL
 * @subpackage  Shortcode/Listing Auction
 * @copyright   Copyright (c) 2019, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// phpcs:disable WordPress.DB.SlowDBQuery

/**
 * Listing Auction Shortcode
 *
 * This shortcode allows for you to specify the property type(s) using
 * [listing_auction post_type="property,rental" status="current,sold,leased" template="default"] option. You can also
 * limit the number of entries that display. using  [listing_auction limit="5"]
 *
 * @param array $atts Shortcode attributes.
 *
 * @return false|string
 * @since       1.0
 */
function epl_shortcode_listing_auction_callback( $atts ) {
	$property_types = epl_get_active_post_types();
	if ( ! empty( $property_types ) ) {
		$property_types = array_keys( $property_types );
	}

	$attributes = shortcode_atts(
		array(
			'post_type'    => $property_types, // Post Type.
			'status'       => array( 'current', 'sold', 'leased' ),
			'limit'        => '10', // Number of maximum posts to show.
			'template'     => false, // Template can be set to "slim" for home open style template.
			'location'     => '', // Location slug. Should be a name like sorrento.
			'tools_top'    => 'off', // Tools before the loop like Sorter and Grid on or off.
			'tools_bottom' => 'off', // Tools after the loop like pagination on or off.
			'sortby'       => '', // Options: price, date, status : Default date.
			'sort_order'   => 'DESC', // Sort order ASC or DESC.
			'query_object' => '', // only for internal use . if provided use it instead of custom query.
			'pagination'   => 'on', // Pagination on or off.
			'instance_id'  => '1', // Set instance ID when using multiple shortcodes on the same page.
			'class'        => '', // Custom class.
		),
		$atts
	);

	if ( is_string( $attributes['post_type'] ) && 'rental' === $attributes['post_type'] ) {
		$meta_key_price = 'property_rent';
	} else {
		$meta_key_price = 'property_price';
	}

	$sort_options = array(
		'price' => $meta_key_price,
		'date'  => 'post_date',
	);

	$post_type = $attributes['post_type'];

	if ( ! is_array( $attributes['post_type'] ) ) {
		$post_type = array_map( 'trim', explode( ',', $attributes['post_type'] ) );
	}
	ob_start();
	$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
	$args  = array(
		'post_type'      => $post_type,
		'posts_per_page' => $attributes['limit'],
		'paged'          => $paged,
	);

	// Only properties which are not under offer should be allowed.
	$args['meta_query'][] = array(
		'key'     => 'property_under_offer',
		'value'   => 'yes',
		'compare' => '!=',
	);

	// Only properties which are not auction should be allowed.
	$args['meta_query'][] = array(
		'key'   => 'property_authority',
		'value' => 'auction',
	);

	if ( ! empty( $attributes['location'] ) ) {
		if ( ! is_array( $attributes['location'] ) ) {
			$location = explode( ',', $attributes['location'] );
			$location = array_map( 'trim', $location );

			$args['tax_query'][] = array(
				'taxonomy' => 'location',
				'field'    => 'slug',
				'terms'    => $location,
			);
		}
	}

	if ( ! empty( $attributes['status'] ) ) {
		if ( ! is_array( $attributes['status'] ) ) {
			$status = explode( ',', $attributes['status'] );
			$status = array_map( 'trim', $status );

			$args['meta_query'][] = array(
				'key'     => 'property_status',
				'value'   => $status,
				'compare' => 'IN',
			);

			add_filter( 'epl_sorting_options', 'epl_sorting_options_callback' );
		}
	}

	if ( ! empty( $attributes['sortby'] ) ) {

		if ( 'price' === $attributes['sortby'] ) {
			$args['orderby']  = 'meta_value_num';
			$args['meta_key'] = $meta_key_price;
		} elseif ( 'status' === $attributes['sortby'] ) {
			$args['orderby']  = 'meta_value';
			$args['meta_key'] = 'property_status';
		} else {
			$args['orderby'] = 'post_date';
			$args['order']   = 'DESC';
		}
		$args['order'] = $attributes['sort_order'];
	}

	$args['instance_id'] = $attributes['instance_id'];
	// add sortby arguments to query, if listings sorted by $_GET['sortby'];.
	$args = epl_add_orderby_args( $args, 'shortcode', 'listing_auction' );

	// Option to filter args.
	$args = apply_filters( 'epl_shortcode_listing_auction_args', $args, $attributes );

	$query_open = new WP_Query( $args );

	if ( is_object( $attributes['query_object'] ) ) {
		$query_open = $attributes['query_object'];
	}

	if ( $query_open->have_posts() ) { ?>
		<div class="loop epl-shortcode">
			<div class="loop-content epl-shortcode-listing
			<?php
			echo esc_attr( epl_template_class( $attributes['template'], 'archive' ) );
			echo esc_attr( $attributes['class'] );
			?>
			">
				<?php
				if ( 'on' === $attributes['tools_top'] ) {
					do_action( 'epl_property_loop_start' );
				}
				while ( $query_open->have_posts() ) {
					$query_open->the_post();
					$template = str_replace( '_', '-', $attributes['template'] );
					epl_property_blog( $template );
				}
				if ( 'on' === $attributes['tools_bottom'] ) {
					do_action( 'epl_property_loop_end' );
				}
				?>
			</div>
			<div class="loop-footer">
				<?php
				if ( 'on' === $attributes['pagination'] ) {
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
add_shortcode( 'listing_auction', 'epl_shortcode_listing_auction_callback' );
