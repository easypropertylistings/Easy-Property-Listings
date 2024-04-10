<?php
/**
 * SHORTCODE :: Listing Feature [listing_feature]
 *
 * @package     EPL
 * @subpackage  Shortcode/ListingFeature
 * @copyright   Copyright (c) 2019, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.1.2
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// phpcs:disable WordPress.DB.SlowDBQuery

/**
 * Listing Feature [listing_feature]
 *
 * This shortcode allows for you to specify feature property type(s) using
 * [listing_feature post_type="property" status="current,sold,leased" feature_id="" feature="" template="default"] option. You can also
 * limit the number of entries that display. using  [listing_category limit="5"]
 *
 * @param array $atts Shortcode attributes.
 *
 * @return false|string|void
 * @since 1.1.2
 * @since 3.4.27 Fixed the issue: no spaces between classes when both class & template attributs are set.
 * @since 3.4.48 Fixed class name.
 * @since 3.5.5 Sorting not working.
 */
function epl_shortcode_listing_tax_feature_callback( $atts ) {
	$property_types = epl_get_active_post_types();
	if ( ! empty( $property_types ) ) {
		$property_types = array_keys( $property_types );
	}

	$attributes = shortcode_atts(
		array(
			'post_type'    => $property_types, // Post Type.
			'status'       => array( 'current', 'sold', 'leased' ),
			'feature'      => '',     // Feature slug.
			'feature_id'   => '',     // Feature ID.
			'limit'        => '10',   // Number of maximum posts to show.
			'offset'       => '',     // Offset posts. When used, pagination is disabled.
			'template'     => false,  // Template can be set to "slim" for home open style template.
			'location'     => '',     // Location slug. Should be a name like sorrento.
			'tools_top'    => 'off',  // Tools before the loop like Sorter and Grid on or off.
			'tools_bottom' => 'off',  // Tools after the loop like pagination on or off.
			'sortby'       => '',     // Options: price, date, status : Default date.
			'sort_order'   => 'DESC', // Sort by ASC or DESC.
			'pagination'   => 'on',   // Enable or disable pagination.
			'instance_id'  => '1',    // Set instance ID when using multiple shortcodes on the same page.
			'class'        => '',     // Custom class.
		),
		$atts
	);

	$post_type  = $attributes['post_type'];
	$status     = $attributes['status'];
	$limit      = $attributes['limit'];
	$feature    = $attributes['feature'];
	$feature_id = $attributes['feature_id'];
	$offset     = $attributes['offset'];
	// $agent      = $attributes['agent'];
	$template     = $attributes['template'];
	$location     = $attributes['location'];
	$tools_top    = $attributes['tools_top'];
	$tools_bottom = $attributes['tools_bottom'];
	$sortby       = $attributes['sortby'];
	$sort_order   = $attributes['sort_order'];
	$pagination   = $attributes['pagination'];
	$instance_id  = $attributes['instance_id'];
	$class        = $attributes['class'];

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

	if ( ! empty( $feature ) ) {
		if ( ! is_array( $feature ) ) {
			$feature = explode( ',', $feature );
			$feature = array_map( 'trim', $feature );

			$args['tax_query'][] = array(
				'taxonomy' => 'tax_feature',
				'field'    => 'slug',
				'terms'    => $feature,
			);
		}
	}

	if ( ! empty( $feature_id ) ) {
		if ( ! is_array( $feature_id ) ) {
			$feature_id = explode( ',', $feature_id );
			$feature_id = array_map( 'trim', $feature_id );

			$args['tax_query'][] = array(
				'taxonomy' => 'tax_feature',
				'field'    => 'id',
				'terms'    => $feature_id,
			);
		}
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
	if ( ! empty( $sortby ) ) {

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
	$args = epl_add_orderby_args( $args, 'shortcode', 'listing_feature' );

	// Option to filter args.
	$args = apply_filters( 'epl_shortcode_listing_feature_args', $args, $attributes );

	$query_open = new WP_Query( $args );
	if ( $query_open->have_posts() ) { ?>
		<div class="loop epl-shortcode">
			<div class="loop-content epl-shortcode-listing-feature <?php echo ' ' . esc_attr( epl_template_class( $template, 'archive' ) ) . ' ' . esc_attr( $attributes['class'] ); ?>">
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
		do_action( 'epl_shortcode_results_message' );
	}
	wp_reset_postdata();
	return ob_get_clean();
}
add_shortcode( 'listing_feature', 'epl_shortcode_listing_tax_feature_callback' );
