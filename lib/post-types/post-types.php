<?php
/**
 * Custom Post Types Functions
 *
 * @package     EPL
 * @subpackage  Functions/CPT
 * @copyright   Copyright (c) 2019, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Query filter for property_address_suburb custom field sortable in posts listing.
 *
 * @param array $vars variables.
 *
 * @return array
 * @since 1.0
 */
function epl_property_address_suburb_column_orderby( $vars ) {
	if ( isset( $vars['orderby'] ) && 'property_address_suburb' === $vars['orderby'] ) {
		$vars = array_merge(
			$vars,
			array(
				'meta_key' => 'property_address_suburb', // phpcs:ignore WordPress.DB.SlowDBQuery
				'orderby'  => 'meta_value',
			)
		);
	}
	return $vars;
}
add_filter( 'request', 'epl_property_address_suburb_column_orderby' );

// Add custom filters to post type posts listings.
add_action( 'restrict_manage_posts', 'epl_custom_restrict_manage_posts' );
add_filter( 'parse_query', 'epl_admin_posts_filter' );

/**
 * Add custom filters to post type posts listings.
 *
 * @since 1.0
 */
function epl_custom_restrict_manage_posts() {
	global $post_type;
	if ( 'property' === $post_type || 'rental' === $post_type || 'land' === $post_type || 'commercial' === $post_type || 'rural' === $post_type || 'business' === $post_type || 'holiday_rental' === $post_type || 'commercial_land' === $post_type ) {

		// Filter by property_status.
		$fields = array(
			'current'   => __( 'Current', 'easy-property-listings' ),
			'withdrawn' => __( 'Withdrawn', 'easy-property-listings' ),
			'offmarket' => __( 'Off Market', 'easy-property-listings' ),
		);

		if ( 'rental' !== $post_type && 'holiday_rental' !== $post_type ) {
			$fields['sold'] = apply_filters( 'epl_sold_label_status_filter', __( 'Sold', 'easy-property-listings' ) );
		}

		if ( 'rental' === $post_type || 'holiday_rental' === $post_type || 'commercial' === $post_type || 'business' === $post_type || 'commercial_land' === $post_type ) {
			$fields['leased'] = apply_filters( 'epl_leased_label_status_filter', __( 'Leased', 'easy-property-listings' ) );
		}

		if ( ! empty( $fields ) ) {
			$_GET['property_status'] = isset( $_GET['property_status'] ) ? sanitize_text_field( wp_unslash( $_GET['property_status'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
			echo '<select name="property_status">';
				echo '<option value="">' . esc_html__( 'Filter By Type', 'easy-property-listings' ) . '</option>';
			foreach ( $fields as $k => $v ) {
				$selected = ( sanitize_text_field( wp_unslash( $_GET['property_status'] ) ) === $k ? 'selected="selected"' : '' );  // phpcs:ignore WordPress.Security.NonceVerification
				echo '<option value="' . esc_attr( $k ) . '" ' . esc_attr( $selected ) . '>' . esc_attr( $v ) . '</option>';
			}
			echo '</select>';
		}

		$property_author = isset( $_GET['property_author'] ) ? intval( $_GET['property_author'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
		// filter by authors.
		wp_dropdown_users(
			array(
				'name'            => 'property_author',
				'selected'        => $property_author,
				'show_option_all' => __( 'All Users', 'easy-property-listings' ),
				'role__not_in'    => array( 'subscriber' ),
			)
		);

		$custom_search_fields = array(
			'property_address_suburb' => epl_labels( 'label_suburb' ),
			'property_office_id'      => __( 'Office ID', 'easy-property-listings' ),
			'property_agent'          => __( 'Listing Agent', 'easy-property-listings' ),
			'property_second_agent'   => __( 'Second Listing Agent', 'easy-property-listings' ),
			'property_unique_id'      => __( 'Property ID', 'easy-property-listings' ),
		);
		$custom_search_fields = apply_filters( 'epl_admin_search_fields', $custom_search_fields );

		if ( ! empty( $custom_search_fields ) ) {
			$sel = isset( $_GET['property_custom_fields'] ) ? sanitize_text_field( wp_unslash( $_GET['property_custom_fields'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
			echo '<select name="property_custom_fields">';
				echo '<option value="">' . esc_html__( 'Search For:', 'easy-property-listings' ) . '</option>';
			foreach ( $custom_search_fields as $k => $v ) {
				echo '<option value="' . esc_attr( $k ) . '" ' . selected( $sel, $k, false ) . '>' . esc_attr( $v ) . '</option>';
			}
			echo '</select>';
		}

		// Filter by Suburb.
		if ( isset( $_GET['property_custom_value'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			$val = sanitize_text_field( wp_unslash( $_GET['property_custom_value'] ) ); // phpcs:ignore WordPress.Security.NonceVerification
		} else {
			$val = '';
		}
		echo '<input type="text" name="property_custom_value" placeholder="' . esc_html__( 'Search Value.', 'easy-property-listings' ) . '" value="' . esc_attr( $val ) . '" />';
	}
}

/**
 * Admin Posts Filter.
 *
 * @param array $query WordPress query.
 *
 * @since 1.0.0
 * @since 3.4.16 Filter by property author now shows results for both primary and secondary author.
 */
function epl_admin_posts_filter( $query ) {
	// phpcs:disable WordPress.Security.NonceVerification
	global $pagenow;
	if ( is_admin() && 'edit.php' === $pagenow && in_array( $query->get( 'post_type' ), epl_get_core_post_types(), true ) ) {
		$meta_query = (array) $query->get( 'meta_query' );

		if ( isset( $_GET['property_status'] ) && ! empty( $_GET['property_status'] ) ) {
			$meta_query[] = array(
				'key'   => 'property_status',
				'value' => sanitize_text_field( wp_unslash( $_GET['property_status'] ) ),
			);
		}

		if ( isset( $_GET['property_author'] ) && ! empty( $_GET['property_author'] ) ) {
			$author = intval( $_GET['property_author'] ); // WPCS: XSS ok.
			$author_object = get_user_by( 'id', $author );
			$meta_query[] = array(
				'relation' => 'OR',
				array(
					'key'   => 'property_agent',
					'value' => $author_object->user_login,
				),
				array(
					'key'   => 'property_second_agent',
					'value' => $author_object->user_login,
				),
			);
		}

		if ( ! empty( $_GET['property_custom_value'] ) && ! empty( $_GET['property_custom_fields'] ) ) {

			$meta_query[] = array(
				'key'     => sanitize_text_field( wp_unslash( $_GET['property_custom_fields'] ) ),
				'value'   => sanitize_text_field( wp_unslash( $_GET['property_custom_value'] ) ),
				'compare' => 'LIKE',
			);

		}

		if ( ! empty( $meta_query ) ) {
			$query->set( 'meta_query', $meta_query );
		}
	}
}

/**
 * Manage Property Columns Sorting.
 *
 * @param array $columns Columns.
 *
 * @return array
 * @since 1.0
 */
function epl_manage_listings_sortable_columns( $columns ) {
	$columns['property_featured'] = 'property_featured';
	$columns['property_rent']     = 'property_rent';
	$columns['property_price']    = 'property_price';
	$columns['property_status']   = 'property_status';
	$columns['listing_id']        = 'listing_id';
	$columns['agent']             = 'agent';
	$columns['property_thumb']    = 'property_thumb';
	return $columns;
}

$epl_posts = array( 'property', 'land', 'commercial', 'business', 'commercial_land', 'location_profile', 'rental', 'rural' );

foreach ( $epl_posts  as $epl_post ) {
	add_filter( 'manage_edit-' . $epl_post . '_sortable_columns', 'epl_manage_listings_sortable_columns' );
}

/**
 * Manage Listing Columns Sorting.
 *
 * @since 1.0
 * @param array $query WordPress query.
 */
function epl_custom_orderby( $query ) {
	if ( ! is_admin() ) {
		return;
	}

	$orderby = $query->get( 'orderby' );

	if ( 'property_price' === $orderby ) {
		$query->set( 'meta_key', 'property_price' );
		$query->set( 'orderby', 'meta_value_num' );
	}

	if ( 'property_rent' === $orderby ) {
		$query->set( 'meta_key', 'property_rent' );
		$query->set( 'orderby', 'meta_value_num' );
	}

	if ( 'listing_id' === $orderby ) {
		$query->set( 'meta_key', 'property_unique_id' );
		$query->set( 'orderby', 'meta_value' );
	}

	if ( 'property_thumb' === $orderby ) {
		$query->set( 'meta_key', '_thumbnail_id' );
		$query->set( 'orderby', 'meta_value' );
	}

	if ( 'property_featured' === $orderby ) {
		$query->set( 'meta_key', 'property_featured' );
		$query->set( 'orderby', 'meta_value' );
	}

}
// handle sorting of admin columns.
add_filter( 'pre_get_posts', 'epl_custom_orderby' );

/**
 * Functions for post column contents.
 *
 * @since 2.2.0
 * @since 3.4.0 Now using epl_get_option function.
 */
function epl_manage_listing_column_property_thumb_callback() {

	if ( function_exists( 'the_post_thumbnail' ) ) {
		$thumb_size = epl_get_option( 'epl_admin_thumb_size', 'admin-list-thumb' );

		the_post_thumbnail( $thumb_size );
	}
}
add_action( 'epl_manage_listing_column_property_thumb', 'epl_manage_listing_column_property_thumb_callback' );

/**
 * Posts Types Columns.
 *
 * @since 1.0
 */
function epl_manage_listing_column_listing_callback() {
	global $post,$property;

	$property_address_suburb = get_the_term_list( $post->ID, 'location', '', ', ', '' );
	$heading                 = $property->get_property_meta( 'property_heading' );
	$homeopen                = $property->get_property_meta( 'property_inspection_times' );
	$homeopen                = trim( $homeopen );
	$beds                    = $property->get_property_meta( 'property_bedrooms' );
	$baths                   = $property->get_property_meta( 'property_bathrooms' );
	$rooms                   = $property->get_property_meta( 'property_rooms', false );
	$land                    = $property->get_property_meta( 'property_land_area', false );
	$land_unit               = $property->get_property_meta( 'property_land_area_unit' );
	$category                = $property->get_property_meta( 'property_category' );

	$commercial_category = $property->get_property_meta( 'property_commercial_category' );
	$outgoings           = $property->get_property_meta( 'property_com_outgoings' );
	$return              = $property->get_property_meta( 'property_com_return' );

	$taking    = $property->get_property_meta( 'property_bus_takings' );
	$franchise = $property->get_property_meta( 'property_bus_franchise' );
	$taking    = $property->get_property_meta( 'property_bus_terms' );

	if ( is_array( $commercial_category ) ) {
		$commercial_category = implode( ', ', $commercial_category );
	}

	// Heading.
	if ( empty( $heading ) ) {
		echo '<strong>' . esc_html__( 'Important! Set a Heading', 'easy-property-listings' ) . '</strong>';
	} else {
		echo '<div class="type_heading"><strong>' , wp_kses(
			$heading,
			array(
				'strong' => array(),
				'b'      => array(),
			)
		) , '</strong></div>';
	}

	// Category for commercial listing lype.
	if ( ! empty( $commercial_category ) ) {
		echo '<div class="epl_meta_category">' , esc_html( $commercial_category ) , '</div>';
	}

	// Need to factor in business category: <businessCategory id="1">.
	// Need to factor in business category: 	<name>Food/Hospitality</name>.
	// Need to factor in business category: 	<businessSubCategory>.
	// Need to factor in business category: 	<name>Takeaway Food</name>.
	// Need to factor in business category: 	</businessSubCategory>.
	// Need to factor in business category: 		</businessCategory>.
	// Need to factor in business category: 	<businessCategory id="2"/>.
	// Need to factor in business category: 	<businessCategory id="3"/>.

	// Need to factor in business fields: property_bus_takings (number).
	// Need to factor in business fields: property_bus_franchise (yes/no).

	// Listing Location Taxonomy.
	echo '<div class="type_suburb">' , wp_kses(
		$property_address_suburb,
		array(
			'strong' => array(),
			'b'      => array(),
			'a'      => array(
				'href' => array(),
			),
		)
	) , '</div>';

	// Listing Category.
	if ( ! empty( $category ) ) {
		echo '<div class="epl_meta_category">' , esc_html( $category ) , '</div>';
	}

	// Outgoings for commercial listing type.
	if ( ! empty( $outgoings ) ) {
		echo '<div class="epl_meta_outgoings">' . esc_html__( 'Outgoings:', 'easy-property-listings' ) . ' ' , esc_html( epl_currency_formatted_amount( $outgoings ) ) , '</div>';
	}

	// Return for commercial listing type.
	if ( ! empty( $return ) ) {
		echo '<div class="epl_meta_return">' . esc_html__( 'Return:', 'easy-property-listings' ) . ' ' , esc_html( $return ) , '%</div>';
	}

	// Bedrooms and Bathrooms.
	if ( ! empty( $beds ) || ! empty( $baths ) ) {
		echo '<div class="epl_meta_beds_baths">';
			echo '<span class="epl_meta_beds">' , esc_attr( $beds )  , ' ' , esc_html__( 'Beds', 'easy-property-listings' ) , ' | </span>';
			echo '<span class="epl_meta_baths">' , esc_attr( $baths ) , ' ' , esc_html__( 'Baths', 'easy-property-listings' ) , '</span>';
		echo '</div>';
	}

	// Rooms.
	if ( ! empty( $rooms ) ) {
		if ( 1 === absint( $rooms ) ) {
			echo '<div class="epl_meta_rooms">' , esc_attr( $rooms ) , ' ' , esc_html__( 'Room', 'easy-property-listings' ) , '</div>';
		} else {
			echo '<div class="epl_meta_rooms">' , esc_attr( $rooms ) , ' ' , esc_html__( 'Rooms', 'easy-property-listings' ) , '</div>';
		}
	}

	// Land area.
	if ( ! empty( $land ) ) {
		echo '<div class="epl_meta_land_details">';
		echo '<span class="epl_meta_land">' , esc_attr( $land ) , '</span>';

		if ( 'squareMeter' === $land_unit ) {
			$land_unit = esc_html__( 'm&#178;', 'easy-property-listings' );
		}

		echo '<span class="epl_meta_land_unit"> ' , esc_attr( $land_unit ) , '</span>';
		echo '</div>';
	}

	// Home Open date and time.
	if ( ! empty( $homeopen ) ) {
		$homeopen          = array_filter( explode( "\n", $homeopen ) );
			$homeopen_list = '<ul class="epl_meta_home_open">';
		foreach ( $homeopen as $num => $item ) {
			$homeopen_list .= '<li>' . htmlspecialchars( $item ) . '</li>';
		}
			$homeopen_list .= '</ul>';
		echo '<div class="epl_meta_home_open_label"><span class="home-open"><strong>' . esc_html( epl_labels( 'label_home_open' ) ) . '</strong></span>' , wp_kses_post( $homeopen_list ) , '</div>';
	}
}
add_action( 'epl_manage_listing_column_listing', 'epl_manage_listing_column_listing_callback' );

/**
 * Get Listing Labels.
 *
 * @param array  $args Array of arguments.
 * @param string $returntype The type of retunr formatting filterable with epl_manage_listing_column_labels_return_type.
 *
 * @return false|string
 * @since 3.3
 */
function epl_get_manage_listing_column_labels( $args = array(), $returntype = 'l' ) {

	global $property;

	$defaults   = array( 'featured' );
	$labels     = apply_filters( 'epl_manage_listing_column_labels', $defaults );
	$returntype = apply_filters( 'epl_manage_listing_column_labels_return_type', $returntype );

	ob_start();

	foreach ( $labels as $label ) {
		if ( ! empty( $args ) && ! in_array( $label, $args, true ) ) {
			continue;
		}

		switch ( $label ) {
			case 'featured':
				echo $property->get_property_featured( $returntype ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				break;

			default:
				// action to hook additional icons.
				do_action( 'epl_get_manage_listing_column_label_' . $label );
				break;
		}
	}

	return ob_get_clean();
}

/**
 * Featured Listing Label to Listing Details column.
 *
 * @since 3.3
 * @param string $returntype The type of retunr formatting filterable with epl_manage_listing_column_labels_return_type.
 */
function epl_manage_listing_column_labels_callback( $returntype = 'l' ) {

	$returntype = empty( $returntype ) ? 'l' : $returntype;

	echo '<ul class="epl-listing-labels">' . epl_get_manage_listing_column_labels( array(), $returntype ) . '</ul>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

}
add_action( 'epl_manage_listing_column_listing', 'epl_manage_listing_column_labels_callback', 20, 1 );

/**
 * Posts Types Column ID
 *
 * @since 1.0
 */
function epl_manage_listing_column_listing_id_callback() {
	global $post;

	$unique_id = get_post_meta( $post->ID, 'property_unique_id', true );
	// If no duration is found, output a default message.
	if ( ! empty( $unique_id ) ) {
		echo esc_attr( $unique_id );
	}
}
add_action( 'epl_manage_listing_column_listing_id', 'epl_manage_listing_column_listing_id_callback' );

/**
 * Posts Types Column Geocode.
 *
 * @since 1.0
 */
function epl_manage_listing_column_geo_callback() {
	global $post;

	$property_address_coordinates = get_post_meta( $post->ID, 'property_address_coordinates', true );
	// If no lat long coordinates saved, display default message.
	if ( ',' === $property_address_coordinates || empty( $property_address_coordinates ) ) {
		esc_html_e( 'No', 'easy-property-listings' );
	} else {
		echo esc_html( $property_address_coordinates );
	}
}
add_action( 'epl_manage_listing_column_geo', 'epl_manage_listing_column_geo_callback' );

/**
 * Posts Types Column Price.
 *
 * @since 1.0.0
 * @since 3.4.0 Now using epl_get_option function.
 */
function epl_manage_listing_column_price_callback() {
	global $post, $property;

	$price                = $property->get_property_meta( 'property_price' );
	$view                 = $property->get_property_meta( 'property_price_view' );
	$property_status      = ucfirst( $property->get_property_meta( 'property_status' ) );
	$property_authority   = $property->get_property_meta( 'property_authority' );
	$sold_price           = $property->get_property_meta( 'property_sold_price' );
	$property_under_offer = $property->get_property_meta( 'property_under_offer' );
	$lease                = $property->get_property_meta( 'property_com_rent' );
	$lease_period         = $property->get_property_meta( 'property_com_rent_period' );
	$lease_date           = $property->get_property_meta( 'property_com_lease_end_date' );
	$d_bond               = '';
	$bond                 = '';

	$max_price = (int) epl_get_option( 'epl_max_graph_sales_price', '2000000' );

	// Rental Listing Type Custom Values.
	if ( 'rental' === $post->post_type ) {

		$price = $property->get_property_meta( 'property_rent' );
		$view  = $property->get_property_meta( 'property_rent_view' );

		$d_bond = epl_get_option( 'display_bond' );
		$bond   = $property->get_property_meta( 'property_bond', false );

		$max_price = (int) epl_get_option( 'epl_max_graph_rent_price', '2000' );
	}

	// Commercial Listing Lease Type Price.
	if ( 'commercial' === $post->post_type && 'lease' === $property->get_property_meta( 'property_com_listing_type' ) ) {

		// Needs consideration and configuring property_com_listing_type.
		// Needs consideration and configuring property_com_rent.
		// Needs consideration and configuring property_com_rent_period.
		// Needs consideration and configuring property_com_rent_range_min.
		// Needs consideration and configuring property_com_rent_range_max.

		$price = $property->get_property_meta( 'property_com_rent' );

		$max_price = (int) epl_get_option( 'epl_max_graph_rent_price', '2000' );
	}

	if ( 'Sold' === $property_status ) {
		$class = 'bar-home-sold';
	} elseif ( 'Leased' === $property_status ) {
		$class = 'bar-home-sold';
	} elseif ( ! empty( $property_under_offer ) && 'yes' === $property_under_offer ) {
		$class = 'bar-under-offer';
	} elseif ( 'Current' === $property_status ) {
		$class = 'bar-home-open';
	} else {
		$class = '';
	}

	// If we have a sold price.
	if ( ! empty( $sold_price ) ) {
		$bar_price = $sold_price;
		// If we have a regular price.
	} elseif ( ! empty( $price ) ) {
		$bar_price = $price;
	}

	// If we have a price to display in the bar.
	if ( ! empty( $bar_price ) ) {
		$barwidth = 0 === $max_price ? 0 : $bar_price / $max_price * 100;
		echo '<div class="epl-price-bar ' . esc_html( $class ) . '">
			<span style="width:' . esc_html( $barwidth ) . '%"></span>
		</div>';
		// Otherwise, there is no price set.
	} else {
		echo esc_html__( 'No price set', 'easy-property-listings' );
	}

	// Display sold price.
	if ( ! empty( $view ) ) {
		echo '<div class="epl_meta_search_price">' . wp_kses_post( $property->get_price_plain_value() ) . ' ';
		echo 'Sold' === $property_status ? esc_html( epl_currency_formatted_amount( $sold_price ) ) : '';
		echo '</div>';
	} else {
		echo '<div class="epl_meta_price">' . esc_html( $property->get_price_plain_value() ) . '</div>';
	}

	// Bond for rental listing type.
	if ( ! empty( $bond ) && 1 === $d_bond ) {
		echo '<div class="epl_meta_bond">' , esc_html( epl_labels( 'label_bond' ) ) , ' ' , esc_html( epl_currency_formatted_amount( $bond ) ) , '</div>';
	}

	// Lease period for commercial listing type.
	if ( ! empty( $lease_date ) ) {
		echo '<div class="epl_meta_lease_date">' . esc_html__( 'Lease End:', 'easy-property-listings' ) . ' ' ,  esc_html( $lease_date ) , '</div>';
	}

	// Lease period for Commercial listing type.
	if ( ! empty( $lease ) ) {
		if ( empty( $lease_period ) ) {
			$lease_period = esc_html( 'annual' );
		}
		echo '<div class="epl_meta_lease_price">Lease: ' , esc_html( epl_currency_formatted_amount( $lease ) ), ' ' , esc_html( epl_listing_load_meta_commercial_rent_period_value( $lease_period ) ) ,'</div>';
	}
}
add_action( 'epl_manage_listing_column_price', 'epl_manage_listing_column_price_callback' );

/**
 * Posts Types Column Status.
 *
 * @since 1.0
 */
function epl_manage_listing_column_property_status_callback() {
	global $post, $property;

	$property_status        = get_post_meta( $post->ID, 'property_status', true );
	$labels_property_status = apply_filters(
		'epl_labels_property_status_filter',
		array(
			'current'   => __( 'Current', 'easy-property-listings' ),
			'withdrawn' => __( 'Withdrawn', 'easy-property-listings' ),
			'offmarket' => __( 'Off Market', 'easy-property-listings' ),
			'sold'      => $property->label_sold,
			'leased'    => $property->label_leased,
		)
	);
	if ( ! empty( $property_status ) ) {
		echo '<span class="type_' . esc_attr( strtolower( $property_status ) ) . '">' . esc_attr( $labels_property_status[ $property_status ] ) . '</span>';
	}
}
add_action( 'epl_manage_listing_column_property_status', 'epl_manage_listing_column_property_status_callback' );

/**
 * Posts Types Column Listing Type
 *
 * @since 1.0
 */
function epl_manage_listing_column_listing_type_callback() {
	global $post;

	// Get the post meta.
	$listing_type = get_post_meta( $post->ID, 'property_com_listing_type', true );
	// If no duration is found, output a default message.
	if ( ! empty( $listing_type ) ) {
		echo esc_html( $listing_type );
	}
}
add_action( 'epl_manage_listing_column_listing_type', 'epl_manage_listing_column_listing_type_callback' );

/**
 * Posts Types Agent/Author
 *
 * @since 1.0
 */
function epl_manage_listing_column_agent_callback() {
	global $post, $property;

	printf(
		'<a href="%s">%s</a>',
		esc_url(
			add_query_arg(
				array(
					'post_type' => $post->post_type,
					'author'    => get_the_author_meta( 'ID' ),
				),
				'edit.php'
			)
		),
		get_the_author()
	);

	$property_second_agent = $property->get_property_meta( 'property_second_agent' );
	if ( ! empty( $property_second_agent ) ) {
		$second_author = get_user_by( 'login', $property_second_agent );
		if ( false !== $second_author ) {
			printf(
				'<br><a href="%s">%s</a>',
				esc_url(
					add_query_arg(
						array(
							'post_type' => $post->post_type,
							'author'    => $second_author->ID,
						),
						'edit.php'
					)
				),
				esc_html( get_the_author_meta( 'display_name', $second_author->ID ) )
			);

		}
		epl_reset_post_author();
	}
}
add_action( 'epl_manage_listing_column_agent', 'epl_manage_listing_column_agent_callback' );

/**
 * Functions for featured listing column.
 *
 * @since 3.3
 */
function epl_manage_listing_column_featured_callback() {

	global $property;

	if ( 'yes' === $property->get_property_meta( 'property_featured' ) ) {
		echo '<span class="dashicons dashicons-star-filled"></span>';
	} else {
		echo '<span class="dashicons dashicons-star-empty"></span>';
	}

}
add_action( 'epl_manage_listing_column_featured', 'epl_manage_listing_column_featured_callback' );
