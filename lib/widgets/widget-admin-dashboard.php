<?php
/**
 * DASHBOARD WIDGET :: Easy Property Listings Status
 *
 * @package     EPL
 * @subpackage  Admin/DashboardWidget
 * @copyright   Copyright (c) 2014, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.3
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Add Dashboard Widgets
 *
 * @since 1.3
 */
function epl_add_dashboard_widgets() {

	wp_add_dashboard_widget(
		 'epl_status_dashboard_widget',
		 __( 'Listings', 'epl'),
		 'epl_status_dashboard_widget_callback'
	);

	wp_add_dashboard_widget(
		'epl_dashboard_activity_widget',
		__( 'Easy Property Listings Activities' , 'epl' ),
		'epl_dashboard_activity_widget'
	);

}
add_action( 'wp_dashboard_setup', 'epl_add_dashboard_widgets' );

/**
 * Status Dashboard Widget
 *
 * @since 1.3
 */
function epl_status_dashboard_widget_callback() {
	global $epl_settings;
	$activate_post_types = isset($epl_settings['activate_post_types'])?$epl_settings['activate_post_types'] : array();
	$activate_post_types = apply_filters('epl_filter_dashboard_widget_posts',$activate_post_types); ?>
	<div class="main">
		<ul class="epl_status_list">
		<?php
			if( !empty($activate_post_types) ) {
				$counter = 0;
				foreach($activate_post_types as $activate_post_type){
					$clear = ($counter%2==0 && $counter!= 0)?'epl-clearfix':'';
					$count = wp_count_posts( $activate_post_type );?>
					<li class="epl_type_<?php echo $activate_post_type.' '.$clear; ?>">
						<a href="edit.php?post_type=<?php echo $activate_post_type; ?>">
							<strong><?php echo epl_get_plural($count->publish,$activate_post_type); ?></strong>
							<?php epl_posts_highlights($activate_post_type);?>
						</a>

					</li><?php
					$counter++;
				}
			}
		?>
		</ul>
	</div><?php
}

/**
 * Status Dashboard Widget Counter
 *
 * @since 1.3
 */
function epl_get_plural($count,$singular) {
	switch($singular){
		case 'property':
			return sprintf( _n( '1 '.__('Property','epl'), '%s '.__('Property','epl'), $count, 'epl' ), $count );
		break;
		case 'land':
			return sprintf( _n( '1 '.__('Land','epl'), '%s '.__('Land','epl'), $count, 'epl' ), $count );
		break;
		case 'rental':
			return sprintf( _n( '1 '.__('Rental','epl'), '%s '.__('Rental','epl'), $count, 'epl' ), $count );
		break;
		case 'rural':
			return sprintf( _n( '1 '.__('Rural','epl'), '%s '.__('Rural','epl'), $count, 'epl' ), $count );
		break;
		case 'commercial':
			return sprintf( _n( '1 '.__('Commercial','epl'), '%s '.__('Commercial','epl'), $count, 'epl' ), $count );
		break;
		case 'commercial_land':
			return sprintf( _n( '1 '.__('Commercial Land','epl'), '%s '.__('Commercial Land','epl'), $count, 'epl' ), $count );
		break;
		case 'business':
			return sprintf( _n( '1 '.__('Business','epl'), '%s '.__('Business','epl'), $count, 'epl' ), $count );
		break;
		default:
			$singular = ucwords( str_replace('epl','',str_replace('-',' ',str_replace('_',' ',$singular) ) ) );
			return sprintf( _n( '1 '.__($singular,'epl'), '%s '.__($singular,'epl'), $count, 'epl' ), $count );
		break;
	}

}

/**
 * Status Dashboard Widget Hghlights
 *
 * @since 1.3
 */
function epl_posts_highlights($type) {
	switch($type){

		case 'rental':
			$filters = array(
				array('key'	=>	'property_status','value'	=>	'current','string'	=>	__('Current','epl')),
				array('key'	=>	'property_status','value'	=>	'leased','string'	=>	apply_filters( 'epl_leased_label_status_filter' , __('Leased', 'epl') ) ),
				array('key'	=>	'property_status','value'	=>	'withdrawn','string'	=>	__('Withdrawn','epl')),
				array('key'	=>	'property_status','value'	=>	'offmarket','string'	=>	__('Off Market','epl')),
			);

			foreach($filters as $filter_key 	=>	$filter_value){
				$count = epl_get_post_count($type,$filter_value['key'],$filter_value['value']);
				if($count != 0){
					echo '<span>'.$count.' '.$filter_value['string'].' </span>';
				}

			}
		break;

		case 'commercial':
			$filters = array(
				array('key'	=>	'property_status','value'	=>	'current','string'	=>	__('Current','epl')),
				array('key'	=>	'property_authority','value'	=>	'auction','string'	=>	__('Auction','epl')), // ONLY if == current
				array('key'	=>	'property_under_offer','value'	=>	'yes','string'		=>	apply_filters( 'epl_under_offer_label_status_filter' , __('Under Offer', 'epl') ) ),
				array('key'	=>	'property_status','value'	=>	'sold','string'		=>	apply_filters( 'epl_sold_label_status_filter' , __('Sold', 'epl') ) ),
				array('key'	=>	'property_status','value'	=>	'leased','string'	=>	apply_filters( 'epl_leased_label_status_filter' , __('Leased', 'epl') ) ),
				array('key'	=>	'property_status','value'	=>	'withdrawn','string'	=>	__('Withdrawn','epl')),
				array('key'	=>	'property_status','value'	=>	'offmarket','string'	=>	__('Off Market','epl')),
			);
			foreach($filters as $filter_key 	=>	$filter_value){
				$count = epl_get_post_count($type,$filter_value['key'],$filter_value['value']);
				if($count != 0){
					echo '<span>'.$count.' '.$filter_value['string'].' </span>';
				}

			}
		break;

		case 'property':
		case 'land':
		case 'rural':
		default:
			$filters = array(
				array('key'	=>	'property_status','value'	=>	'current','string'	=>	__('Current','epl')),
				array('key'	=>	'property_authority','value'	=>	'auction','string'	=>	__('Auction','epl')), // ONLY if == current
				array('key'	=>	'property_under_offer','value'	=>	'yes','string'		=>	apply_filters( 'epl_under_offer_label_status_filter' , __('Under Offer', 'epl') ) ),
				array('key'	=>	'property_status','value'	=>	'sold','string'		=>	apply_filters( 'epl_sold_label_status_filter' , __('Sold', 'epl') ) ),
				array('key'	=>	'property_status','value'	=>	'withdrawn','string'	=>	__('Withdrawn','epl')),
				array('key'	=>	'property_status','value'	=>	'offmarket','string'	=>	__('Off Market','epl')),
			);
			foreach($filters as $filter_key 	=>	$filter_value){
				$count = epl_get_post_count($type,$filter_value['key'],$filter_value['value']);
				if($count != 0){
					echo '<span>'.$count.' '.$filter_value['string'].' </span>';
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

	echo '<div id="epl_dashboard_future_listings">';

	$future_posts = epl_dashboard_recent_posts( array(
		'post_type'	=>	epl_get_core_post_types(),
		'max'     => 5,
		'status'  => 'future',
		'order'   => 'ASC',
		'title'   => __( 'Listings Publishing Soon' ),
		'id'      => 'epl-future-posts',
	) );


	echo '</div>';

	echo '<div id="epl_dashboard_recent_listings">';

	$future_posts = epl_dashboard_recent_posts( array(
		'post_type'	=>	epl_get_core_post_types(),
		'max'     => 5,
		'status'  => 'publish',
		'order'   => 'ASC',
		'title'   => __( 'Recently Published Listings' ),
		'id'      => 'epl-recent-posts',
	) );


	echo '</div>';

	echo '<div id="activity-widget">';

	$recent_comments = epl_dashboard_recent_comments();

	if ( !$recent_comments ) {
		echo '<div class="no-activity">';
		echo '<p class="smiley"></p>';
		echo '<p>' . __( 'No activity yet!' ) . '</p>';
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
		'post_type'	=>	epl_get_core_post_types(),
		'number' => $total_items * 5,
		'offset' => 0
	);
	if ( ! current_user_can( 'edit_posts' ) )
		$comments_query['status'] = 'approve';

	while ( count( $comments ) < $total_items && $possible = get_comments( $comments_query ) ) {
		if ( ! is_array( $possible ) ) {
			break;
		}
		foreach ( $possible as $comment ) {
			if ( ! current_user_can( 'read_post', $comment->comment_post_ID ) )
				continue;
			$comments[] = $comment;
			if ( count( $comments ) == $total_items )
				break 2;
		}
		$comments_query['offset'] += $comments_query['number'];
		$comments_query['number'] = $total_items * 10;
	}

	if ( $comments ) {
		echo '<div id="latest-comments" class="activity-block epl-activity-block epl-feed-block">';
		echo '<h3>' . __( 'Comments' ) . '</h3>';

		echo '<div id="the-comment-list" data-wp-lists="list:comment">';
		foreach ( $comments as $comment )
			_wp_dashboard_recent_comments_row( $comment );
		echo '</div>';

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
	$posts = new WP_Query( $query_args );

	if ( $posts->have_posts() ) {

		echo '<div id="' . $args['id'] . '" class="epl-activity-block activity-block">';

		echo '<h3>' . $args['title'] . '</h3>';

		echo '<ul>';

		$today    = date( 'Y-m-d', current_time( 'timestamp' ) );
		$tomorrow = date( 'Y-m-d', strtotime( '+1 day', current_time( 'timestamp' ) ) );

		while ( $posts->have_posts() ) {
			$posts->the_post();

			$time = get_the_time( 'U' );
			if ( date( 'Y-m-d', $time ) == $today ) {
				$relative = __( 'Today' );
			} elseif ( date( 'Y-m-d', $time ) == $tomorrow ) {
				$relative = __( 'Tomorrow' );
			} elseif ( date( 'Y', $time ) !== date( 'Y', current_time( 'timestamp' ) ) ) {
				/* translators: date and time format for recent posts on the dashboard, from a different calendar year, see http://php.net/date */
				$relative = date_i18n( __( 'M jS Y' ), $time );
			} else {
				/* translators: date and time format for recent posts on the dashboard, see http://php.net/date */
				$relative = date_i18n( __( 'M jS' ), $time );
			}

			// Use the post edit link for those who can edit, the permalink otherwise.
			$recent_post_link = current_user_can( 'edit_post', get_the_ID() ) ? get_edit_post_link() : get_permalink();

			/* translators: 1: relative date, 2: time, 3: post edit link or permalink, 4: post title */
			$format = __( '<span>%1$s, %2$s</span> <a href="%3$s">%4$s</a>' );
			printf( "<li>$format</li>", $relative, get_the_time(), $recent_post_link, _draft_or_post_title() );
		}

		echo '</ul>';
		echo '</div>';

	} else {
		return false;
	}

	wp_reset_postdata();

	return true;
}
