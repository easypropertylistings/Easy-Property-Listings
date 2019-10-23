<?php
/**
 * DASHBOARD WIDGET :: Easy Property Listings Status
 *
 * @package     EPL
 * @subpackage  Admin/DashboardWidget
 * @copyright   Copyright (c) 2019, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.3
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add Dashboard Widgets
 *
 * @since 1.3
 */
function epl_add_dashboard_widgets() {

	wp_add_dashboard_widget(
		'epl_status_dashboard_widget',
		__( 'Listings', 'easy-property-listings' ),
		'epl_status_dashboard_widget_callback'
	);

	wp_add_dashboard_widget(
		'epl_dashboard_activity_widget',
		__( 'Easy Property Listings Activities', 'easy-property-listings' ),
		'epl_dashboard_activity_widget'
	);

}
add_action( 'wp_dashboard_setup', 'epl_add_dashboard_widgets' );

/**
 * Status Dashboard Widget
 *
 * @since 1.3.0
 * @since 3.4.10 Corrected issue with listings dashboard widget when no post types are active.
 */
function epl_status_dashboard_widget_callback() {

	$activate_post_types = epl_get_option( 'activate_post_types', array() );
	$activate_post_types = apply_filters( 'epl_filter_dashboard_widget_posts', $activate_post_types ); ?>
	<div class="main">
		<ul class="epl_status_list">
		<?php
		if ( ! empty( $activate_post_types ) ) {
			$counter = 0;
			foreach ( $activate_post_types as $activate_post_type ) {
				$clear = ( 0 === $counter % 2 && 0 !== $counter ) ? 'epl-clearfix' : '';
				$count = wp_count_posts( $activate_post_type );
				?>
					<li class="epl_type_<?php echo esc_attr( $activate_post_type . ' ' . $clear ); ?>">
						<a href="edit.php?post_type=<?php echo esc_attr( $activate_post_type ); ?>">
							<strong><?php echo esc_attr( epl_get_plural( $count->publish, $activate_post_type ) ); ?></strong>
							<?php epl_posts_highlights( $activate_post_type ); ?>
						</a>

					</li>
					<?php
					$counter++;
			}
		}
		?>
		</ul>
	</div>
	<?php
}

/**
 * Status Dashboard Widget Counter
 *
 * @param int    $count Number.
 * @param string $singular Option for the case.
 *
 * @return bool|string
 * @since 1.3
 */
function epl_get_plural( $count, $singular ) {
	$return = false;

	switch ( $singular ) {
		case 'property':
			// translators: count.
			$return = sprintf( _n( '%s Property', '%s Property', $count, 'easy-property-listings' ), $count );
			break;
		case 'land':
			// translators: count.
			$return = sprintf( _n( '%s Land', '%s Land', $count, 'easy-property-listings' ), $count );
			break;
		case 'rental':
			// translators: count.
			$return = sprintf( _n( '%s Rental', '%s Rental', $count, 'easy-property-listings' ), $count );
			break;
		case 'rural':
			// translators: count.
			$return = sprintf( _n( '%s Rural', '%s Rural', $count, 'easy-property-listings' ), $count );
			break;
		case 'commercial':
			// translators: count.
			$return = sprintf( _n( '%s Commercial', '%s Commercial', $count, 'easy-property-listings' ), $count );
			break;
		case 'commercial_land':
			// translators: count.
			$return = sprintf( _n( '%s Commercial Land', '%s Commercial Land', $count, 'easy-property-listings' ), $count );
			break;
		case 'business':
			// translators: count.
			$return = sprintf( _n( '%s Business', '%s Business', $count, 'easy-property-listings' ), $count );
			break;
		default:
			$singular = ucwords( str_replace( 'epl', '', str_replace( '-', ' ', str_replace( '_', ' ', $singular ) ) ) );
			// translators: count.
			$return = sprintf( _n( '%s ' . $singular, '%s ' . $singular, $count, 'easy-property-listings' ), $count );
			break;
	}

	return $return;
}

/**
 * Status Dashboard Widget Highlights
 *
 * @since 1.3
 * @param string $type Option for the case.
 */
function epl_posts_highlights( $type ) {
	switch ( $type ) {

		case 'rental':
			$filters = array(
				array(
					'key'    => 'property_status',
					'value'  => 'current',
					'string' => __( 'Current', 'easy-property-listings' ),
				),
				array(
					'key'    => 'property_status',
					'value'  => 'leased',
					'string' => apply_filters( 'epl_leased_label_status_filter', __( 'Leased', 'easy-property-listings' ) ),
				),
				array(
					'key'    => 'property_status',
					'value'  => 'withdrawn',
					'string' => __( 'Withdrawn', 'easy-property-listings' ),
				),
				array(
					'key'    => 'property_status',
					'value'  => 'offmarket',
					'string' => __( 'Off Market', 'easy-property-listings' ),
				),
			);

			foreach ( $filters as $filter_key     => $filter_value ) {
				$count = epl_get_post_count( $type, $filter_value['key'], $filter_value['value'] );
				if ( 0 !== (int) $count ) {
					echo '<span>' . esc_attr( $count ) . ' ' . esc_attr( $filter_value['string'] ) . ' </span>';
				}
			}
			break;

		case 'commercial':
			$filters = array(
				array(
					'key'    => 'property_status',
					'value'  => 'current',
					'string' => __( 'Current', 'easy-property-listings' ),
				),
				array(
					'key'    => 'property_authority',
					'value'  => 'auction',
					'string' => __( 'Auction', 'easy-property-listings' ),
				), // ONLY if == current.
				array(
					'key'    => 'property_under_offer',
					'value'  => 'yes',
					'string' => apply_filters( 'epl_under_offer_label_status_filter', __( 'Under Offer', 'easy-property-listings' ) ),
				),
				array(
					'key'    => 'property_status',
					'value'  => 'sold',
					'string' => apply_filters( 'epl_sold_label_status_filter', __( 'Sold', 'easy-property-listings' ) ),
				),
				array(
					'key'    => 'property_status',
					'value'  => 'leased',
					'string' => apply_filters( 'epl_leased_label_status_filter', __( 'Leased', 'easy-property-listings' ) ),
				),
				array(
					'key'    => 'property_status',
					'value'  => 'withdrawn',
					'string' => __( 'Withdrawn', 'easy-property-listings' ),
				),
				array(
					'key'    => 'property_status',
					'value'  => 'offmarket',
					'string' => __( 'Off Market', 'easy-property-listings' ),
				),
			);
			foreach ( $filters as $filter_key     => $filter_value ) {
				$count = epl_get_post_count( $type, $filter_value['key'], $filter_value['value'] );
				if ( 0 !== (int) $count ) {
					echo '<span>' . esc_attr( $count ) . ' ' . esc_attr( $filter_value['string'] ) . ' </span>';
				}
			}
			break;

		case 'property':
		case 'land':
		case 'rural':
		default:
			$filters = array(
				array(
					'key'    => 'property_status',
					'value'  => 'current',
					'string' => __( 'Current', 'easy-property-listings' ),
				),
				array(
					'key'    => 'property_authority',
					'value'  => 'auction',
					'string' => __( 'Auction', 'easy-property-listings' ),
				), // ONLY if == current.
				array(
					'key'    => 'property_under_offer',
					'value'  => 'yes',
					'string' => apply_filters( 'epl_under_offer_label_status_filter', __( 'Under Offer', 'easy-property-listings' ) ),
				),
				array(
					'key'    => 'property_status',
					'value'  => 'sold',
					'string' => apply_filters( 'epl_sold_label_status_filter', __( 'Sold', 'easy-property-listings' ) ),
				),
				array(
					'key'    => 'property_status',
					'value'  => 'withdrawn',
					'string' => __( 'Withdrawn', 'easy-property-listings' ),
				),
				array(
					'key'    => 'property_status',
					'value'  => 'offmarket',
					'string' => __( 'Off Market', 'easy-property-listings' ),
				),
			);
			foreach ( $filters as $filter_key     => $filter_value ) {
				$count = epl_get_post_count( $type, $filter_value['key'], $filter_value['value'] );
				if ( 0 !== (int) $count ) {
					echo '<span>' . esc_attr( $count ) . ' ' . esc_attr( $filter_value['string'] ) . ' </span>';
				}
			}
			break;
	}
}

/**
 * Activity Dashboard Widget
 *
 * @since 3.0
 */
function epl_dashboard_activity_widget() {

	echo '<div id="activity-widget" class="epl-activity-widget">';

		$future_posts = epl_dashboard_recent_posts(
			array(
				'post_type' => epl_get_core_post_types(),
				'max'       => 5,
				'status'    => 'future',
				'order'     => 'ASC',
				'title'     => __( 'Listings Publishing Soon', 'easy-property-listings' ),
				'id'        => 'epl-future-posts',
			)
		);

		$future_posts = epl_dashboard_recent_posts(
			array(
				'post_type' => epl_get_core_post_types(),
				'max'       => 5,
				'status'    => 'publish',
				'order'     => 'ASC',
				'title'     => __( 'Recently Published Listings', 'easy-property-listings' ),
				'id'        => 'epl-recent-posts',
			)
		);

		$recent_comments = epl_dashboard_recent_comments();

	if ( ! $recent_comments ) {
		echo '<div class="no-activity">';
		echo '<p class="smiley"></p>';
		echo '<p>' . esc_html__( 'No activity yet!', 'easy-property-listings' ) . '</p>';
		echo '</div>';
	}

	echo '</div>';
}

/**
 * Show Comments section.
 *
 * @since 3.0
 *
 * @param int $total_items Optional. Number of comments to query. Default 5.
 * @return bool False if no comments were found. True otherwise.
 */
function epl_dashboard_recent_comments( $total_items = 5 ) {
	// Select all comment types and filter out spam later for better query performance.
	$comments = array();

	$comments_query = array(
		'type__in' => array( 'note', 'call', 'email', 'listing_alert' ),
		'number'   => $total_items * 5,
		'offset'   => 0,
		'order'    => 'comment_date',
	);
	if ( ! current_user_can( 'edit_posts' ) ) {
		$comments_query['status'] = 'approve';
	}

	$possible       = get_comments( $comments_query );
	$comments_count = count( $comments );
	while ( $comments_count < $total_items && $possible ) {
		if ( ! is_array( $possible ) ) {
			break;
		}
		foreach ( $possible as $comment ) {
			if ( ! current_user_can( 'read_post', $comment->comment_post_ID ) ) {
				continue;
			}
			$comments[] = $comment;
			$com_count  = count( $comments );
			if ( $com_count === $total_items ) {
				break 2;
			}
		}
		$comments_query['offset'] += $comments_query['number'];
		$comments_query['number']  = $total_items * 10;
		$comments_count            = count( $comments );
	}

	if ( $comments ) {
		$activity_types = array();
		echo '<div id="latest-comments" class="epl-dashboard-activity-feed activity-block epl-activity-block epl-feed-block">';
		echo '<h3>' . esc_html__( 'Activity', 'easy-property-listings' ) . '</h3>';

		echo '<ul id="the-comment-list" data-wp-lists="list:comment">';
		foreach ( $comments as $comment ) {
			$comment->comment_type = array_key_exists( $comment->comment_type, $activity_types ) ?
			$activity_types[ $comment->comment_type ] : $comment->comment_type;
			_wp_dashboard_recent_comments_row( $comment );
		}
		echo '</ul>';

		wp_comment_reply( -1, false, 'dashboard', false );
		wp_comment_trashnotice();

		echo '</div>';
	} else {
		return false;
	}
	return true;
}

/**
 * Generates Publishing Soon and Recently Published listings.
 *
 * @since 3.0
 *
 * @param array $args {
 *     An array of query and display arguments.
 *
 *     @type int    $max     Number of posts to display.
 *     @type string $status  Post status.
 *     @type string $order   Designates ascending ('ASC') or descending ('DESC') order.
 *     @type string $title   Section title.
 *     @type string $id      The container id.
 * }
 * @return bool False if no posts were found. True otherwise.
 */
function epl_dashboard_recent_posts( $args ) {
	$query_args = array(
		'post_type'      => $args['post_type'],
		'post_status'    => $args['status'],
		'orderby'        => 'date',
		'order'          => $args['order'],
		'posts_per_page' => intval( $args['max'] ),
		'no_found_rows'  => true,
		'cache_results'  => false,
		'perm'           => ( 'future' === $args['status'] ) ? 'editable' : 'readable',
	);

	$query_args = apply_filters( 'epl_dashboard_recent_posts_query_args', $query_args );
	$posts      = new WP_Query( $query_args );

	if ( $posts->have_posts() ) {

		echo '<div id="' . esc_attr( $args['id'] ) . '" class="epl-activity-block activity-block">';

		echo '<h3>' . esc_attr( $args['title'] ) . '</h3>';

		echo '<ul>';

		$today    = date( 'Y-m-d', current_time( 'timestamp' ) );
		$tomorrow = date( 'Y-m-d', strtotime( '+1 day', current_time( 'timestamp' ) ) );

		while ( $posts->have_posts() ) {
			$posts->the_post();

			$time = get_the_time( 'U' );
			if ( date( 'Y-m-d', $time ) === $today ) {
				$relative = esc_html__( 'Today', 'easy-property-listings' );
			} elseif ( date( 'Y-m-d', $time ) === $tomorrow ) {
				$relative = esc_html__( 'Tomorrow', 'easy-property-listings' );
			} elseif ( date( 'Y', $time ) !== date( 'Y', current_time( 'timestamp' ) ) ) {
				/* translators: date and time format for recent posts on the dashboard, from a different calendar year, see http://php.net/date */
				$relative = date_i18n( esc_html__( 'M jS Y', 'easy-property-listings' ), $time );
			} else {
				/* translators: date and time format for recent posts on the dashboard, see http://php.net/date */
				$relative = date_i18n( esc_html__( 'M jS', 'easy-property-listings' ), $time );
			}

			// Use the post edit link for those who can edit, the permalink otherwise.
			$recent_post_link = current_user_can( 'edit_post', get_the_ID() ) ? get_edit_post_link() : get_permalink();

			/* translators: 1: relative date, 2: time, 3: post edit link or permalink, 4: post title */
			$format = wp_kses_post( __( '<span>%1$s, %2$s</span> <a href="%3$s">%4$s</a>', 'easy-property-listings' ) );
			printf( "<li>$format</li>", $relative, get_the_time(), $recent_post_link, _draft_or_post_title() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		echo '</ul>';
		echo '</div>';

	} else {
		return false;
	}

	wp_reset_postdata();

	return true;
}
