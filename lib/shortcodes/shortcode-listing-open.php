<?php
/**
 * SHORTCODE :: Open For Inspection [listing_open]
 *
 * @package     EPL
 * @subpackage  Shortcode/ListingOpen
 * @copyright   Copyright (c) 2020, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// phpcs:disable WordPress.DB.SlowDBQuery

/**
 * Listing Open Shortcode [listing_open]
 *
 * This shortcode allows for you to specify the property type(s) using
 * [listing_open post_type="property,rental"] option. You can also
 * limit the number of entries that display. using  [epl-property-open limit="5"]
 *
 * @param array $atts Shortcode attributes.
 *
 * @return false|string
 * @since 1.0
 * @since 3.4.48 Fixed class name.
 * @since 3.5.5 Sorting not working.
 */
function epl_shortcode_property_open_callback( $atts ) {
	$property_types = epl_get_active_post_types();
	if ( ! empty( $property_types ) ) {
		$property_types = array_keys( $property_types );
	}

	$attributes = shortcode_atts(
		array(
			'post_type'    => $property_types, // Post Type.
			'limit'        => '-1',   // Number of maximum posts to show.
			'template'     => false,  // Template. slim, table.
			'location'     => '',     // Location slug. Should be a name like sorrento.
			'tools_top'    => 'off',  // Tools before the loop like Sorter and Grid on or off.
			'tools_bottom' => 'off',  // Tools after the loop like pagination on or off.
			'sortby'       => '',     // Options: price, date : Default date.
			'sort_order'   => 'DESC', // Sort by ASC or DESC.
			'pagination'   => 'on',   // Enable or disable pagination.
			'instance_id'  => '1',    // Set instance ID when using multiple shortcodes on the same page.
			'class'        => '',     // Custom class.

		),
		$atts
	);

	$post_type    = $attributes['post_type'];
	$limit        = $attributes['limit'];
	$template     = $attributes['template'];
	$location     = $attributes['location'];
	$tools_top    = $attributes['tools_top'];
	$tools_bottom = $attributes['tools_bottom'];
	$sortby       = $attributes['sortby'];
	$sort_order   = $attributes['sort_order'];
	$pagination   = $attributes['pagination'];
	$instance_id  = $attributes['instance_id'];
	$class        = $attributes['class'];

	if ( is_string( $post_type ) && 'rental' === $post_type ) {
		$meta_key_price = 'property_rent';
	} else {
		$meta_key_price = 'property_price';
	}

	$sort_options = array(
		'price' => $meta_key_price,
		'date'  => 'post_date',
	);

	ob_start();
	if ( ! is_array( $post_type ) ) {
		$post_type = array_map( 'trim', explode( ',', $post_type ) );
	}

	$args = array(
		'post_type'      => $post_type,
		'posts_per_page' => $limit,
		'meta_key'       => 'property_inspection_times',
		'meta_query'     => array(
			array(
				'key'     => 'property_inspection_times',
				'value'   => '^\s*$',
				'compare' => 'NOT REGEXP',
			),
			array(
				'key'     => 'property_status',
				'value'   => array( 'leased', 'sold' ),
				'compare' => 'NOT IN',
			),
		),
	);

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

	if ( '' !== $sortby ) {

		if ( 'price' === $sortby ) {
			$args['orderby']  = 'meta_value_num';
			$args['meta_key'] = $meta_key_price;
		} elseif ( 'status' === $sortby ) {
			$args['orderby']  = 'meta_value';
			$args['meta_key'] = 'property_status';
		} else {
			$args['orderby'] = 'post_date';
		}
		$args['order'] = $sort_order;
	}

	$args['instance_id'] = $attributes['instance_id'];
	// add sortby arguments to query, if listings sorted by $_GET['sortby'];.
	$args = epl_add_orderby_args( $args, 'shortcode', 'listing_open' );

	// Option to filter args.
	$args = apply_filters( 'epl_shortcode_listing_open_args', $args, $attributes );

	$query_open = new WP_Query( $args );
	if ( $query_open->have_posts() ) { ?>
		<div class="loop epl-shortcode">
			<div class="loop-content epl-shortcode-listing-open <?php echo ' ' . esc_attr( epl_template_class( $template, 'archive' ) ) . ' ' . esc_attr( $attributes['class'] ); ?>">
				<?php
				if ( 'on' === $tools_top ) {
					do_action( 'epl_property_loop_start', $attributes );
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
		do_action( 'epl_shortcode_results_message', 'open' );
	}
	wp_reset_postdata();
	return ob_get_clean();
}
add_shortcode( 'home_open_list', 'epl_shortcode_property_open_callback' );
add_shortcode( 'listing_open', 'epl_shortcode_property_open_callback' );
