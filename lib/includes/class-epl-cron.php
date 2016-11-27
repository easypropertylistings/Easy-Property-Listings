<?php
/**
 * Cron
 *
 * @package     EPL
 * @subpackage  Classes/Cron
 * @copyright   Copyright (c) 2016, Merv Barett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.2
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * EPL_Cron Class
 *
 * This class handles scheduled events
 *
 * @since 3.2
 */
class EPL_Cron {
	/**
	 * Get things going
	 *
	 * @since 3.2
	 * @see EPL_Cron::weekly_events()
	 */
	public function __construct() {
		add_filter( 'cron_schedules', array( $this, 'add_schedules'   ) );
		add_action( 'wp',             array( $this, 'schedule_events' ) );
	}

	/**
	 * Registers new cron schedules
	 *
	 * @since 3.2
	 *
	 * @param array $schedules
	 * @return array
	 */
	public function add_schedules( $schedules = array() ) {
		// Adds once weekly to the existing schedules.
		$schedules['weekly'] = array(
			'interval' => 604800,
			'display'  => __( 'Once Weekly', 'easy-property-listings' )
		);

		return $schedules;
	}

	/**
	 * Schedules our events
	 *
	 * @access public
	 * @since 3.2
	 * @return void
	 */
	public function schedule_events() {
		$this->weekly_events();
		$this->daily_events();
	}

	/**
	 * Schedule weekly events
	 *
	 * @access private
	 * @since 3.2
	 * @return void
	 */
	private function weekly_events() {
		if ( ! wp_next_scheduled( 'epl_weekly_scheduled_events' ) ) {
			wp_schedule_event( current_time( 'timestamp' ), 'weekly', 'epl_weekly_scheduled_events' );
		}
	}

	/**
	 * Schedule daily events
	 *
	 * @access private
	 * @since 3.2
	 * @return void
	 */
	private function daily_events() {
		if ( ! wp_next_scheduled( 'epl_daily_scheduled_events' ) ) {
			wp_schedule_event( current_time( 'timestamp' ), 'daily', 'epl_daily_scheduled_events' );
		}
	}

}
$epl_cron = new EPL_Cron;
