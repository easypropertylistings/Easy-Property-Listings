<?php
/**
 * EPL Dashboard REST API Controller
 *
 * Provides REST endpoints for dashboard data.
 *
 * @package     EPL
 * @subpackage  Admin/Dashboard
 * @since       4.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class EPL_Dashboard_REST
 *
 * @since 4.0.0
 */
class EPL_Dashboard_REST extends WP_REST_Controller {

	/**
	 * Namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'epl/v1/admin';

	/**
	 * Register REST routes.
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/dashboard',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_dashboard_data' ),
					'permission_callback' => array( $this, 'check_permissions' ),
				),
			)
		);
	}

	/**
	 * Check if user has permission.
	 *
	 * @return bool|WP_Error
	 */
	public function check_permissions() {
		if ( ! current_user_can( 'edit_posts' ) ) {
			return new WP_Error(
				'rest_forbidden',
				__( 'You do not have permission to access the dashboard.', 'easy-property-listings' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}
		return true;
	}

	/**
	 * Get dashboard data.
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response
	 */
	public function get_dashboard_data( $request ) {
		$post_types = epl_get_core_post_types();
		$year       = $request->get_param( 'year' );

		// Default to current year if not specified.
		if ( empty( $year ) ) {
			$year = gmdate( 'Y' );
		}

		$data = array(
			'stats'            => $this->get_stats( $post_types ),
			'byStatus'         => $this->get_counts_by_status( $post_types ),
			'byType'           => $this->get_counts_by_type( $post_types ),
			'monthlyActivity'  => $this->get_monthly_activity( $post_types, $year ),
			'salesData'        => $this->get_sales_data( $post_types, $year ),
			'recentListings'   => $this->get_recent_listings( $post_types ),
			'recentSold'       => $this->get_recent_sold( $post_types ),
			'openInspections'  => $this->get_open_inspections( $post_types ),
			'agentStats'       => $this->get_agent_stats( $post_types ),
			'currentYear'      => (int) $year,
			'availableYears'   => $this->get_available_years( $post_types ),
		);

		/**
		 * Filter the dashboard data.
		 *
		 * @param array $data Dashboard data.
		 */
		$data = apply_filters( 'epl_dashboard_data', $data );

		return rest_ensure_response( $data );
	}

	/**
	 * Get overall stats.
	 *
	 * @param array $post_types Post types.
	 * @return array
	 */
	private function get_stats( $post_types ) {
		global $wpdb;

		// Total listings.
		$total_query = new WP_Query(
			array(
				'post_type'      => $post_types,
				'post_status'    => 'publish',
				'posts_per_page' => 1,
				'fields'         => 'ids',
			)
		);
		$total = $total_query->found_posts;

		// Active (current) listings.
		$active_query = new WP_Query(
			array(
				'post_type'      => $post_types,
				'post_status'    => 'publish',
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'meta_query'     => array(
					array(
						'key'   => 'property_status',
						'value' => 'current',
					),
				),
			)
		);
		$active = $active_query->found_posts;

		// Sold/Leased this month.
		$first_day = gmdate( 'Y-m-01' );
		$sold_query = new WP_Query(
			array(
				'post_type'      => $post_types,
				'post_status'    => 'publish',
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'meta_query'     => array(
					'relation' => 'AND',
					array(
						'key'     => 'property_status',
						'value'   => array( 'sold', 'leased' ),
						'compare' => 'IN',
					),
					array(
						'key'     => 'property_sold_date',
						'value'   => $first_day,
						'compare' => '>=',
						'type'    => 'DATE',
					),
				),
			)
		);
		$sold_this_month = $sold_query->found_posts;

		// Under offer.
		$under_offer_query = new WP_Query(
			array(
				'post_type'      => $post_types,
				'post_status'    => 'publish',
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'meta_query'     => array(
					array(
						'key'   => 'property_under_offer',
						'value' => 'yes',
					),
				),
			)
		);
		$under_offer = $under_offer_query->found_posts;

		return apply_filters(
			'epl_dashboard_stat_cards',
			array(
				'total'         => $total,
				'active'        => $active,
				'soldThisMonth' => $sold_this_month,
				'underOffer'    => $under_offer,
			)
		);
	}

	/**
	 * Get counts by status.
	 *
	 * @param array $post_types Post types.
	 * @return array
	 */
	private function get_counts_by_status( $post_types ) {
		$statuses = array( 'current', 'sold', 'leased', 'withdrawn', 'offmarket' );
		$counts   = array();

		foreach ( $statuses as $status ) {
			$query = new WP_Query(
				array(
					'post_type'      => $post_types,
					'post_status'    => 'publish',
					'posts_per_page' => 1,
					'fields'         => 'ids',
					'meta_query'     => array(
						array(
							'key'   => 'property_status',
							'value' => $status,
						),
					),
				)
			);
			$counts[ $status ] = $query->found_posts;
		}

		return $counts;
	}

	/**
	 * Get counts by post type.
	 *
	 * @param array $post_types Post types.
	 * @return array
	 */
	private function get_counts_by_type( $post_types ) {
		$counts = array();

		foreach ( $post_types as $type ) {
			$query = new WP_Query(
				array(
					'post_type'      => $type,
					'post_status'    => 'publish',
					'posts_per_page' => 1,
					'fields'         => 'ids',
				)
			);
			$obj = get_post_type_object( $type );
			$counts[] = array(
				'type'  => $type,
				'label' => $obj ? $obj->labels->singular_name : $type,
				'count' => $query->found_posts,
			);
		}

		return $counts;
	}

	/**
	 * Get monthly activity for a specific year.
	 *
	 * @param array  $post_types Post types.
	 * @param string $year       Year to filter.
	 * @return array
	 */
	private function get_monthly_activity( $post_types, $year = null ) {
		if ( empty( $year ) ) {
			$year = gmdate( 'Y' );
		}

		$months = array();

		for ( $m = 1; $m <= 12; $m++ ) {
			$month_num = str_pad( $m, 2, '0', STR_PAD_LEFT );
			$label     = gmdate( 'M', strtotime( "$year-$month_num-01" ) );

			// Listings created this month.
			$created_query = new WP_Query(
				array(
					'post_type'      => $post_types,
					'post_status'    => 'publish',
					'posts_per_page' => 1,
					'fields'         => 'ids',
					'date_query'     => array(
						array(
							'year'  => $year,
							'month' => $m,
						),
					),
				)
			);

			$months[] = array(
				'month'    => $label,
				'year'     => $year,
				'monthNum' => $month_num,
				'listings' => $created_query->found_posts,
			);
		}

		return $months;
	}

	/**
	 * Get sales data for charts (monthly count and value).
	 *
	 * @param array  $post_types Post types.
	 * @param string $year       Year to filter.
	 * @return array
	 */
	private function get_sales_data( $post_types, $year = null ) {
		if ( empty( $year ) ) {
			$year = gmdate( 'Y' );
		}

		$months      = array();
		$total_value = 0;
		$total_count = 0;

		for ( $m = 1; $m <= 12; $m++ ) {
			$month_num = str_pad( $m, 2, '0', STR_PAD_LEFT );
			$label     = gmdate( 'M', strtotime( "$year-$month_num-01" ) );
			$first_day = "$year-$month_num-01";
			$last_day  = gmdate( 'Y-m-t', strtotime( $first_day ) );

			// Get sold/leased in this month.
			$query = new WP_Query(
				array(
					'post_type'      => $post_types,
					'post_status'    => 'publish',
					'posts_per_page' => -1,
					'fields'         => 'ids',
					'meta_query'     => array(
						'relation' => 'AND',
						array(
							'key'     => 'property_status',
							'value'   => array( 'sold', 'leased' ),
							'compare' => 'IN',
						),
						array(
							'key'     => 'property_sold_date',
							'value'   => array( $first_day, $last_day ),
							'compare' => 'BETWEEN',
							'type'    => 'DATE',
						),
					),
				)
			);

			$month_count = $query->found_posts;
			$month_value = 0;

			if ( $query->posts ) {
				foreach ( $query->posts as $post_id ) {
					$price = get_post_meta( $post_id, 'property_sold_price', true );
					if ( $price ) {
						$month_value += floatval( $price );
					}
				}
			}

			$total_count += $month_count;
			$total_value += $month_value;

			$months[] = array(
				'month' => $label,
				'count' => $month_count,
				'value' => $month_value,
			);
		}

		return array(
			'months'     => $months,
			'totalCount' => $total_count,
			'totalValue' => $total_value,
		);
	}

	/**
	 * Get available years with listings data.
	 *
	 * @param array $post_types Post types.
	 * @return array
	 */
	private function get_available_years( $post_types ) {
		global $wpdb;

		$post_type_placeholders = implode( ',', array_fill( 0, count( $post_types ), '%s' ) );

		// Get years from post creation dates.
		$years = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT DISTINCT YEAR(post_date) as year
				FROM {$wpdb->posts}
				WHERE post_type IN ($post_type_placeholders)
				AND post_status = 'publish'
				ORDER BY year DESC",
				...$post_types
			)
		);

		// Ensure current year is included.
		$current_year = (int) gmdate( 'Y' );
		if ( ! in_array( $current_year, array_map( 'intval', $years ), true ) ) {
			array_unshift( $years, $current_year );
		}

		return array_map( 'intval', array_slice( $years, 0, 5 ) );
	}

	/**
	 * Get recent listings.
	 *
	 * @param array $post_types Post types.
	 * @return array
	 */
	private function get_recent_listings( $post_types ) {
		$query = new WP_Query(
			array(
				'post_type'      => $post_types,
				'post_status'    => 'publish',
				'posts_per_page' => 5,
				'orderby'        => 'date',
				'order'          => 'DESC',
			)
		);

		$listings = array();
		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$post = get_post();
				$listings[] = $this->format_listing( $post );
			}
			wp_reset_postdata();
		}

		return $listings;
	}

	/**
	 * Get recent sold/leased listings.
	 *
	 * @param array $post_types Post types.
	 * @return array
	 */
	private function get_recent_sold( $post_types ) {
		$query = new WP_Query(
			array(
				'post_type'      => $post_types,
				'post_status'    => 'publish',
				'posts_per_page' => 5,
				'orderby'        => 'meta_value',
				'meta_key'       => 'property_sold_date',
				'order'          => 'DESC',
				'meta_query'     => array(
					array(
						'key'     => 'property_status',
						'value'   => array( 'sold', 'leased' ),
						'compare' => 'IN',
					),
				),
			)
		);

		$listings = array();
		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$post = get_post();
				$listing = $this->format_listing( $post );
				$listing['soldDate'] = get_post_meta( $post->ID, 'property_sold_date', true );
				$listing['soldPrice'] = get_post_meta( $post->ID, 'property_sold_price', true );
				$listings[] = $listing;
			}
			wp_reset_postdata();
		}

		return $listings;
	}

	/**
	 * Get listings with open inspections.
	 *
	 * @param array $post_types Post types.
	 * @return array
	 */
	private function get_open_inspections( $post_types ) {
		$query = new WP_Query(
			array(
				'post_type'      => $post_types,
				'post_status'    => 'publish',
				'posts_per_page' => 10,
				'meta_query'     => array(
					'relation' => 'AND',
					array(
						'key'   => 'property_status',
						'value' => 'current',
					),
					array(
						'key'     => 'property_inspection_times',
						'value'   => '',
						'compare' => '!=',
					),
				),
			)
		);

		$listings = array();
		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$post = get_post();
				$listing = $this->format_listing( $post );
				$listing['inspectionTimes'] = get_post_meta( $post->ID, 'property_inspection_times', true );
				$listings[] = $listing;
			}
			wp_reset_postdata();
		}

		return $listings;
	}

	/**
	 * Get agent statistics.
	 *
	 * @param array $post_types Post types.
	 * @return array
	 */
	private function get_agent_stats( $post_types ) {
		global $wpdb;

		// Get all agents who have listings.
		$agents_sql = $wpdb->prepare(
			"SELECT DISTINCT pm.meta_value
			FROM {$wpdb->postmeta} pm
			INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
			WHERE pm.meta_key = 'property_agent'
			AND p.post_status = 'publish'
			AND p.post_type IN (" . implode( ',', array_fill( 0, count( $post_types ), '%s' ) ) . ')
			AND pm.meta_value != ""
			LIMIT 10',
			...$post_types
		);

		$agent_usernames = $wpdb->get_col( $agents_sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

		$agents = array();
		foreach ( $agent_usernames as $username ) {
			$user = get_user_by( 'login', $username );
			if ( ! $user ) {
				continue;
			}

			// Active count.
			$active_query = new WP_Query(
				array(
					'post_type'      => $post_types,
					'post_status'    => 'publish',
					'posts_per_page' => 1,
					'fields'         => 'ids',
					'meta_query'     => array(
						'relation' => 'AND',
						array(
							'key'   => 'property_agent',
							'value' => $username,
						),
						array(
							'key'   => 'property_status',
							'value' => 'current',
						),
					),
				)
			);

			// Sold count (all time).
			$sold_query = new WP_Query(
				array(
					'post_type'      => $post_types,
					'post_status'    => 'publish',
					'posts_per_page' => 1,
					'fields'         => 'ids',
					'meta_query'     => array(
						'relation' => 'AND',
						array(
							'key'   => 'property_agent',
							'value' => $username,
						),
						array(
							'key'     => 'property_status',
							'value'   => array( 'sold', 'leased' ),
							'compare' => 'IN',
						),
					),
				)
			);

			$agents[] = array(
				'id'          => $user->ID,
				'name'        => $user->display_name,
				'avatar'      => get_avatar_url( $user->ID, array( 'size' => 48 ) ),
				'activeCount' => $active_query->found_posts,
				'soldCount'   => $sold_query->found_posts,
			);
		}

		// Sort by active count descending.
		usort(
			$agents,
			function ( $a, $b ) {
				return $b['activeCount'] - $a['activeCount'];
			}
		);

		return apply_filters( 'epl_dashboard_agent_stats', array_slice( $agents, 0, 5 ) );
	}

	/**
	 * Format a listing for response.
	 *
	 * @param WP_Post $post Post object.
	 * @return array
	 */
	private function format_listing( $post ) {
		$thumbnail_id = get_post_thumbnail_id( $post->ID );

		// Build full address from parts.
		$street_number = get_post_meta( $post->ID, 'property_address_street_number', true );
		$street        = get_post_meta( $post->ID, 'property_address_street', true );
		$suburb        = get_post_meta( $post->ID, 'property_address_suburb', true );

		$address_parts = array_filter( array( $street_number, $street, $suburb ) );
		$full_address  = implode( ' ', $address_parts );

		// If no address parts, use post title.
		if ( empty( $full_address ) ) {
			$full_address = $post->post_title;
		}

		return array(
			'id'        => $post->ID,
			'title'     => $post->post_title,
			'address'   => $full_address,
			'thumbnail' => $thumbnail_id ? wp_get_attachment_image_url( $thumbnail_id, 'thumbnail' ) : '',
			'price'     => get_post_meta( $post->ID, 'property_price', true ),
			'status'    => get_post_meta( $post->ID, 'property_status', true ),
			'postType'  => $post->post_type,
			'date'      => $post->post_date,
			'editUrl'   => get_edit_post_link( $post->ID, 'raw' ),
			'viewUrl'   => get_permalink( $post->ID ),
		);
	}
}
/**
 * Register the EPL Dashboard REST routes.
 *
 * @since 4.0.0
 */
function epl_register_dashboard_rest_routes() {
	$controller = new EPL_Dashboard_REST();
	$controller->register_routes();
}
add_action( 'rest_api_init', 'epl_register_dashboard_rest_routes' );
