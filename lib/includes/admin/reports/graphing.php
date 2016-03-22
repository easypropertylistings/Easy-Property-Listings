<?php
/**
 * Graphing Functions
 *
 * @package     EPL
 * @subpackage  Admin/Reports
 * @copyright   Copyright (c) 2016, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Show report graphs
 *
 * @since 3.0
 * @return void
*/
function epl_reports_graph($sold_status='sold',$current_status='current',$sold_color=null,$current_color=null) {
	// Retrieve the queried dates
	$dates = epl_get_report_dates();

	// Determine graph options
	switch ( $dates['range'] ) :
		case 'today' :
		case 'yesterday' :
			$day_by_day	= true;
			break;
		case 'last_year' :
		case 'this_year' :
		case 'last_quarter' :
		case 'this_quarter' :
			$day_by_day = false;
			break;
		case 'other' :
			if( $dates['m_end'] - $dates['m_start'] >= 2 || $dates['year_end'] > $dates['year'] && ( $dates['m_start'] != '12' && $dates['m_end'] != '1' ) ) {
				$day_by_day = false;
			} else {
				$day_by_day = true;
			}
			break;
		default:
			$day_by_day = true;
			break;
	endswitch;

	$current_listings_totals = 0.00; // Total current_listings for time period shown
	$sales_totals    = 0;    // Total sales for time period shown

	$listings_data = array();
	$sales_data    = array();

	if( $dates['range'] == 'today' || $dates['range'] == 'yesterday' ) {
		// Hour by hour
		$hour  = 1;
		$month = $dates['m_start'];
		while ( $hour <= 23 ) {

			$sales    = epl_get_sales_by_date( $dates['day'], $month, $dates['year'], $hour,$sold_status,$day_by_day );
			$current_listings = epl_get_sales_by_date( $dates['day'], $month, $dates['year'], $hour,$current_status,$day_by_day );

			$sales_totals    += $sales;
			$listings_totals += $current_listings;

			$date            = mktime( $hour, 0, 0, $month, $dates['day'], $dates['year'] ) * 1000;
			$sales_data[]    = array( $date, $sales );
			$current_listings_data[] = array( $date, $current_listings );

			$hour++;
		}

	} elseif ( $dates['range'] == 'this_week' || $dates['range'] == 'last_week' ) {

		$num_of_days = cal_days_in_month( CAL_GREGORIAN, $dates['m_start'], $dates['year'] );

		$report_dates = array();
		$i = 0;
		while ( $i <= 6 ) {

			if ( ( $dates['day'] + $i ) <= $num_of_days ) {
				$report_dates[ $i ] = array(
					'day'   => (string) $dates['day'] + $i,
					'month' => $dates['m_start'],
					'year'  => $dates['year'],
				);
			} else {
				$report_dates[ $i ] = array(
					'day'   => (string) $i,
					'month' => $dates['m_end'],
					'year'  => $dates['year_end'],
				);
			}

			$i++;
		}

		foreach ( $report_dates as $report_date ) {
			$sales = epl_get_sales_by_date( $report_date['day'], $report_date['month'], $report_date['year'],$sold_status,$day_by_day );
			$sales_totals += $sales;

			$current_listings        = epl_get_sales_by_date( $report_date['day'], $report_date['month'], $report_date['year'] , null, $current_status,$day_by_day );
			$current_listings_totals += $current_listings;

			$date            = mktime( 0, 0, 0,  $report_date['month'], $report_date['day'], $report_date['year']  ) * 1000;
			$sales_data[]    = array( $date, $sales );
			$current_listings_data[] = array( $date, $current_listings );
		}

	} else {

		$y = $dates['year'];

		while( $y <= $dates['year_end'] ) {

			$last_year = false;

			if( $dates['year'] == $dates['year_end'] ) {
				$month_start = $dates['m_start'];
				$month_end   = $dates['m_end'];
				$last_year   = true;
			} elseif( $y == $dates['year'] ) {
				$month_start = $dates['m_start'];
				$month_end   = 12;
			} elseif ( $y == $dates['year_end'] ) {
				$month_start = 01;
				$month_end   = $dates['m_end'];
			} else {
				$month_start = 01;
				$month_end   = 12;
			}

			$i = $month_start;
			while ( $i <= $month_end ) {

				if ( $day_by_day ) {

					$d = $dates['day'];

					if( $i == $month_end ) {

						$num_of_days = $dates['day_end'];

						if ( $month_start < $month_end ) {

							$d = 1;

						}

					} else {

						$num_of_days = cal_days_in_month( CAL_GREGORIAN, $i, $y );

					}




					while ( $d <= $num_of_days ) {

						$sales = epl_get_sales_by_date( $d, $i, $y,null,$sold_status,$day_by_day );
						$sales_totals += $sales;

						$current_listings = epl_get_sales_by_date( $d, $i, $y, null, $current_status,$day_by_day );
						$current_listings_totals += $current_listings;

						$date = mktime( 0, 0, 0, $i, $d, $y ) * 1000;
						$sales_data[] = array( $date, $sales );
						$current_listings_data[] = array( $date, $current_listings );
						$d++;

					}

				} else {

					$sales = epl_get_sales_by_date( null, $i, $y, null, $sold_status,$day_by_day );
					$sales_totals += $sales;

					$current_listings = epl_get_sales_by_date( null, $i, $y, null, $current_status,$day_by_day );
					$current_listings_totals += $current_listings;

					if( $i == $month_end && $last_year ) {

						$num_of_days = cal_days_in_month( CAL_GREGORIAN, $i, $y );

					} else {

						$num_of_days = 1;

					}

					$date = mktime( 0, 0, 0, $i, $num_of_days, $y ) * 1000;
					$sales_data[] = array( $date, $sales );
					$current_listings_data[] = array( $date, $current_listings );

				}

				$i++;

			}

			$y++;
		}

	}

	$data = array(
		$current_status	=>	array(
			'label'	=>	__( ucfirst($current_status), 'easy-property-listings'  ),
			'id'	=>	$current_status,
			'data'	=>	$current_listings_data
		),
		$sold_status	=>	array(
			'label'	=>	__( ucfirst($sold_status), 'easy-property-listings'  ),
			'id'	=>	$current_status,
			'data'	=>	$sales_data
		)
	);

	! is_null($sold_color) ? $data[$sold_status]['color'] = $sold_color : null;
	! is_null($current_color) ? $data[$current_status]['color'] = $current_color : null;

	// start our own output buffer
	ob_start();
	?>
	<div id="epl-dashboard-widgets-wrap">
		<div class="metabox-holder" style="padding-top: 0;">
			<div class="postbox">
				<h3><span><?php _e('Listings Over Time','easy-property-listings'  ); ?></span></h3>

				<div class="inside">
					<?php
					epl_reports_graph_controls();
					$graph = new EPL_Graph( $data );
					$graph->set( 'x_mode', 'time' );
					$graph->set( 'multiple_y_axes', true );
					$graph->display();

					?>

					<p class="epl_graph_totals">
						<strong>
							<?php
								_e( 'Total '.ucfirst($current_status).' listings for period shown: ', 'easy-property-listings'  );
								echo epl_format_amount( $current_listings_totals );
							?>
						</strong>
					</p>
					<p class="epl_graph_totals">
						<strong>
							<?php
								_e( 'Total '.ucfirst($sold_status).' listings for period shown: ', 'easy-property-listings'  );
								echo epl_format_amount( $sales_totals );
							?>
						</strong>
					</p>


					<?php do_action( 'epl_reports_graph_additional_stats' ); ?>

				</div>
			</div>
		</div>
	</div>
	<?php
	// get output buffer contents and end our own buffer
	$output = ob_get_contents();
	ob_end_clean();

	echo $output;
}

/**
 * Show report graph date filters
 *
 * @since 3.0
 * @return void
*/
function epl_reports_graph_controls() {
	$date_options = apply_filters( 'epl_report_date_options', array(
		'today'        => __( 'Today', 'easy-property-listings'  ),
		'yesterday'    => __( 'Yesterday', 'easy-property-listings'  ),
		'this_week'    => __( 'This Week', 'easy-property-listings'  ),
		'last_week'    => __( 'Last Week', 'easy-property-listings'  ),
		'this_month'   => __( 'This Month', 'easy-property-listings'  ),
		'last_month'   => __( 'Last Month', 'easy-property-listings'  ),
		'this_quarter' => __( 'This Quarter', 'easy-property-listings'  ),
		'last_quarter' => __( 'Last Quarter', 'easy-property-listings'  ),
		'this_year'    => __( 'This Year', 'easy-property-listings'  ),
		'last_year'    => __( 'Last Year', 'easy-property-listings'  ),
		'other'        => __( 'Custom', 'easy-property-listings'  )
	) );

	$dates   = epl_get_report_dates();
	$display = $dates['range'] == 'other' ? '' : 'style="display:none;"';
	$view    = epl_get_reporting_view();

	if( empty( $dates['day_end'] ) ) {
		$dates['day_end'] = cal_days_in_month( CAL_GREGORIAN, date( 'n' ), date( 'Y' ) );
	}

	?>
	<form id="epl-graphs-filter" method="get">
		<div class="tablenav top">
			<div class="alignleft actions">

				<input type="hidden" name="page" value="epl-reports"/>
				<input type="hidden" name="view" value="<?php echo esc_attr( $view ); ?>"/>

				<select id="epl-graphs-date-options" name="range">
				<?php foreach ( $date_options as $key => $option ) : ?>
						<option value="<?php echo esc_attr( $key ); ?>"<?php selected( $key, $dates['range'] ); ?>><?php echo esc_html( $option ); ?></option>
					<?php endforeach; ?>
				</select>

				<div id="epl-date-range-options" <?php echo $display; ?>>
					<span><?php _e( 'From', 'easy-property-listings'  ); ?>&nbsp;</span>
					<select id="epl-graphs-month-start" name="m_start">
						<?php for ( $i = 1; $i <= 12; $i++ ) : ?>
							<option value="<?php echo absint( $i ); ?>" <?php selected( $i, $dates['m_start'] ); ?>><?php echo epl_month_num_to_name( $i ); ?></option>
						<?php endfor; ?>
					</select>
					<select id="epl-graphs-day-start" name="day">
						<?php for ( $i = 1; $i <= 31; $i++ ) : ?>
							<option value="<?php echo absint( $i ); ?>" <?php selected( $i, $dates['day'] ); ?>><?php echo $i; ?></option>
						<?php endfor; ?>
					</select>
					<select id="epl-graphs-year-start" name="year">
						<?php for ( $i = 2007; $i <= date( 'Y' ); $i++ ) : ?>
							<option value="<?php echo absint( $i ); ?>" <?php selected( $i, $dates['year'] ); ?>><?php echo $i; ?></option>
						<?php endfor; ?>
					</select>
					<span><?php _e( 'To', 'easy-property-listings'  ); ?>&nbsp;</span>
					<select id="epl-graphs-month-end" name="m_end">
						<?php for ( $i = 1; $i <= 12; $i++ ) : ?>
							<option value="<?php echo absint( $i ); ?>" <?php selected( $i, $dates['m_end'] ); ?>><?php echo epl_month_num_to_name( $i ); ?></option>
						<?php endfor; ?>
					</select>
					<select id="epl-graphs-day-end" name="day_end">
						<?php for ( $i = 1; $i <= 31; $i++ ) : ?>
							<option value="<?php echo absint( $i ); ?>" <?php selected( $i, $dates['day_end'] ); ?>><?php echo $i; ?></option>
						<?php endfor; ?>
					</select>
					<select id="epl-graphs-year-end" name="year_end">
						<?php for ( $i = 2007; $i <= date( 'Y' ); $i++ ) : ?>
						<option value="<?php echo absint( $i ); ?>" <?php selected( $i, $dates['year_end'] ); ?>><?php echo $i; ?></option>
						<?php endfor; ?>
					</select>
				</div>

				<div class="epl-graph-filter-submit graph-option-section">
					<input type="hidden" name="epl_action" value="filter_reports" />
					<input type="submit" class="button-secondary" value="<?php _e( 'Filter', 'easy-property-listings'  ); ?>"/>
				</div>
			</div>
		</div>
	</form>
	<?php
}

/**
 * Sets up the dates used to filter graph data
 *
 * Date sent via $_GET is read first and then modified (if needed) to match the
 * selected date-range (if any)
 *
 * @since 3.0
 * @return array
*/
function epl_get_report_dates() {
	$dates = array();

	$current_time = current_time( 'timestamp' );

	$dates['range']      = isset( $_GET['range'] )   ? $_GET['range']   : 'this_month';
	$dates['year']       = isset( $_GET['year'] )    ? $_GET['year']    : date( 'Y' );
	$dates['year_end']   = isset( $_GET['year_end'] )? $_GET['year_end']: date( 'Y' );
	$dates['m_start']    = isset( $_GET['m_start'] ) ? $_GET['m_start'] : 1;
	$dates['m_end']      = isset( $_GET['m_end'] )   ? $_GET['m_end']   : 12;
	$dates['day']        = isset( $_GET['day'] )     ? $_GET['day']     : 1;
	$dates['day_end']    = isset( $_GET['day_end'] ) ? $_GET['day_end'] : cal_days_in_month( CAL_GREGORIAN, $dates['m_end'], $dates['year'] );

	// Modify dates based on predefined ranges
	switch ( $dates['range'] ) :

		case 'this_month' :
			$dates['m_start']  = date( 'n', $current_time );
			$dates['m_end']    = date( 'n', $current_time );
			$dates['day']      = 1;
			$dates['day_end']  = cal_days_in_month( CAL_GREGORIAN, $dates['m_end'], $dates['year'] );
			$dates['year']     = date( 'Y' );
			$dates['year_end'] = date( 'Y' );
		break;

		case 'last_month' :
			if( date( 'n' ) == 1 ) {
				$dates['m_start']  = 12;
				$dates['m_end']    = 12;
				$dates['year']     = date( 'Y', $current_time ) - 1;
				$dates['year_end'] = date( 'Y', $current_time ) - 1;
			} else {
				$dates['m_start']  = date( 'n' ) - 1;
				$dates['m_end']    = date( 'n' ) - 1;
				$dates['year_end'] = $dates['year'];
			}
			$dates['day_end'] = cal_days_in_month( CAL_GREGORIAN, $dates['m_end'], $dates['year'] );
		break;

		case 'today' :
			$dates['day']     = date( 'd', $current_time );
			$dates['m_start'] = date( 'n', $current_time );
			$dates['m_end']   = date( 'n', $current_time );
			$dates['year']    = date( 'Y', $current_time );
		break;

		case 'yesterday' :

			$year  = date( 'Y', $current_time );
			$month = date( 'n', $current_time );
			$day   = date( 'd', $current_time );

			if ( $month == 1 && $day == 1 ) {

				$year  -= 1;
				$month = 12;
				$day   = cal_days_in_month( CAL_GREGORIAN, $month, $year );

			} elseif ( $month > 1 && $day == 1 ) {

				$month -= 1;
				$day   = cal_days_in_month( CAL_GREGORIAN, $month, $year );

			} else {

				$day -= 1;

			}

			$dates['day']       = $day;
			$dates['m_start']   = $month;
			$dates['m_end']     = $month;
			$dates['year']      = $year;
			$dates['year_end']  = $year;
		break;

		case 'this_week' :
		case 'last_week' :
			$base_time = $dates['range'] === 'this_week' ? current_time( 'mysql' ) : date( 'Y-n-d h:i:s', current_time( 'timestamp' ) - WEEK_IN_SECONDS );
			$start_end = get_weekstartend( $base_time, get_option( 'start_of_week' ) );

			$dates['day']      = date( 'd', $start_end['start'] );
			$dates['m_start']  = date( 'n', $start_end['start'] );
			$dates['year']     = date( 'Y', $start_end['start'] );

			$dates['day_end']  = date( 'd', $start_end['end'] );
			$dates['m_end']    = date( 'n', $start_end['end'] );
			$dates['year_end'] = date( 'Y', $start_end['end'] );
		break;

		case 'this_quarter' :
			$month_now = date( 'n', $current_time );

			if ( $month_now <= 3 ) {

				$dates['m_start'] = 1;
				$dates['m_end']   = 4;
				$dates['year']    = date( 'Y', $current_time );

			} else if ( $month_now <= 6 ) {

				$dates['m_start'] = 4;
				$dates['m_end']   = 7;
				$dates['year']    = date( 'Y', $current_time );

			} else if ( $month_now <= 9 ) {

				$dates['m_start'] = 7;
				$dates['m_end']   = 10;
				$dates['year']    = date( 'Y', $current_time );

			} else {

				$dates['m_start']  = 10;
				$dates['m_end']    = 1;
				$dates['year']     = date( 'Y', $current_time );
				$dates['year_end'] = date( 'Y', $current_time ) + 1;

			}
		break;

		case 'last_quarter' :
			$month_now = date( 'n' );

			if ( $month_now <= 3 ) {

				$dates['m_start']  = 10;
				$dates['m_end']    = 12;
				$dates['year']     = date( 'Y', $current_time ) - 1; // Previous year
				$dates['year_end'] = date( 'Y', $current_time ) - 1; // Previous year

			} else if ( $month_now <= 6 ) {

				$dates['m_start'] = 1;
				$dates['m_end']   = 3;
				$dates['year']    = date( 'Y', $current_time );

			} else if ( $month_now <= 9 ) {

				$dates['m_start'] = 4;
				$dates['m_end']   = 6;
				$dates['year']    = date( 'Y', $current_time );

			} else {

				$dates['m_start'] = 7;
				$dates['m_end']   = 9;
				$dates['year']    = date( 'Y', $current_time );

			}
		break;

		case 'this_year' :
			$dates['m_start'] = 1;
			$dates['m_end']   = 12;
			$dates['year']    = date( 'Y', $current_time );
		break;

		case 'last_year' :
			$dates['m_start']  = 1;
			$dates['m_end']    = 12;
			$dates['year']     = date( 'Y', $current_time ) - 1;
			$dates['year_end'] = date( 'Y', $current_time ) - 1;
		break;

	endswitch;

	return apply_filters( 'epl_report_dates', $dates );
}