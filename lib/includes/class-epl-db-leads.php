<?php
/**
 * Leads DB class
 *
 * This class is for interacting with the leads' database table
 *
 * @package     EPL
 * @since       2.4
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * EPL_DB_Leads Class
 *
 * @since 2.4
 */
class EPL_DB_Leads extends EPL_DB  {

	/**
	 * Get things started
	 *
	 * @access  public
	 * @since   2.4
	*/
	public function __construct() {

		global $wpdb;

		$this->table_name  = $wpdb->prefix . 'epl_leads';
		$this->primary_key = 'id';
		$this->version     = '1.0';

		add_action( 'profile_update', array( $this, 'update_lead_email_on_user_update' ), 10, 2 );

	}

	/**
	 * Get columns and formats
	 *
	 * @access  public
	 * @since   2.4
	*/
	public function get_columns() {
		return array(
			'id'             => '%d',
			'user_id'        => '%d',
			'name'           => '%s',
			'email'          => '%s',
			'listing_ids'    => '%s',
			'listing_count' => '%d',
			'date_created'   => '%s',
		);
	}

	/**
	 * Get default column values
	 *
	 * @access  public
	 * @since   2.4
	*/
	public function get_column_defaults() {
		return array(
			'user_id'        => 0,
			'email'          => '',
			'name'           => '',
			'listing_ids'    => '',
			'listing_count' => 0,
			'date_created'   => date( 'Y-m-d H:i:s' ),
		);
	}

	/**
	 * Add a lead
	 *
	 * @access  public
	 * @since   2.4
	*/
	public function add( $data = array() ) {

		$defaults = array(
			'listing_ids' => ''
		);

		$args = wp_parse_args( $data, $defaults );

		if( empty( $args['email'] ) ) {
			return false;
		}

		if( ! empty( $args['listing_ids'] ) && is_array( $args['listing_ids'] ) ) {
			$args['listing_ids'] = implode( ',', array_unique( array_values( $args['listing_ids'] ) ) );
		}

		$lead = $this->get_lead_by( 'email', $args['email'] );

		if( $lead ) {
			// update an existing lead

			// Update the listing IDs attached to the lead
			if( ! empty( $args['listing_ids'] ) ) {

				if( empty( $lead->listing_ids ) ) {

					$lead->listing_ids = $args['listing_ids'];

				} else {

					$existing_ids = array_map( 'absint', explode( ',', $lead->listing_ids ) );
					$listing_ids  = array_map( 'absint', explode( ',', $args['listing_ids'] ) );
					$listing_ids  = array_merge( $listing_ids, $existing_ids );
					$lead->listing_ids = implode( ',', array_unique( array_values( $listing_ids ) ) );

				}

				$args['listing_ids'] = $lead->listing_ids;

			}

			$this->update( $lead->id, $args );

			return $lead->id;

		} else {

			return $this->insert( $args, 'lead' );

		}

	}

	/**
	 * Delete a lead
	 *
	 * NOTE: This should not be called directly as it does not make necessary changes to
	 * the listing meta and logs. Use epl_lead_delete() instead
	 *
	 * @access  public
	 * @since   2.3.1
	*/
	public function delete( $_id_or_email = false ) {

		if ( empty( $_id_or_email ) ) {
			return false;
		}

		$column   = is_email( $_id_or_email ) ? 'email' : 'id';
		$lead = $this->get_lead_by( $column, $_id_or_email );

		if ( $lead->id > 0 ) {

			global $wpdb;
			return $wpdb->delete( $this->table_name, array( 'id' => $lead->id ), array( '%d' ) );

		} else {
			return false;
		}

	}

	/**
	 * Checks if a lead exists
	 *
	 * @access  public
	 * @since   2.4
	*/
	public function exists( $value = '', $field = 'email' ) {

		$columns = $this->get_columns();
		if ( ! array_key_exists( $field, $columns ) ) {
			return false;
		}

		return (bool) $this->get_column_by( 'id', $field, $value );

	}

	/**
	 * Attaches a listing ID to a lead
	 *
	 * @access  public
	 * @since   2.4
	*/
	public function attach_listing( $lead_id = 0, $listing_id = 0 ) {

		$lead = new EPL_Lead( $lead_id );

		if( empty( $lead->id ) ) {
			return false;
		}

		// Attach the listing, but don't increment stats, as this function previously did not
		return $lead->attach_listing( $listing_id, false );

	}

	/**
	 * Removes a listing ID from a lead
	 *
	 * @access  public
	 * @since   2.4
	*/
	public function remove_listing( $lead_id = 0, $listing_id = 0 ) {

		$lead = new EPL_Lead( $lead_id );

		if( ! $lead ) {
			return false;
		}

		// Remove the listing, but don't decrease stats, as this function previously did not
		return $lead->remove_listing( $listing_id, false );

	}

	/**
	 * Increments lead listing stats
	 *
	 * @access  public
	 * @since   2.4
	*/
	public function increment_stats( $lead_id = 0) {

		$lead = new EPL_Lead( $lead_id );

		if( empty( $lead->id ) ) {
			return false;
		}

		$increased_count = $lead->increase_listing_count();

		return $increased_count ? true : false;

	}

	/**
	 * Decrements lead listing stats
	 *
	 * @access  public
	 * @since   2.4
	*/
	public function decrement_stats( $lead_id = 0, $amount = 0.00 ) {

		$lead = new EPL_Lead( $lead_id );

		if( ! $lead ) {
			return false;
		}

		$decreased_count = $lead->decrease_listing_count();

		return $decreased_coun ? true : false;

	}

	/**
	 * Updates the email address of a lead record when the email on a user is updated
	 *
	 * @access  public
	 * @since   2.4
	*/
	public function update_lead_email_on_user_update( $user_id = 0, $old_user_data ) {

		$lead = new EPL_Lead( $user_id, true );

		if( ! $lead ) {
			return false;
		}

		$user = get_userdata( $user_id );

		if( ! empty( $user ) && $user->user_email !== $lead->email ) {

			if( ! $this->get_lead_by( 'email', $user->user_email ) ) {

				$success = $this->update( $lead->id, array( 'email' => $user->user_email ) );

				if( $success ) {
					do_action( 'epl_update_lead_email_on_user_update', $user, $lead );

				}

			}

		}

	}

	/**
	 * Retrieves a single lead from the database
	 *
	 * @access public
	 * @since  2.3
	 * @param  string $column id or email
	 * @param  mixed  $value  The Lead ID or email to search
	 * @return mixed          Upon success, an object of the lead. Upon failure, NULL
	 */
	public function get_lead_by( $field = 'id', $value = 0 ) {
		global $wpdb;

		if ( empty( $field ) || empty( $value ) ) {
			return NULL;
		}

		if ( 'id' == $field || 'user_id' == $field ) {
			// Make sure the value is numeric to avoid casting objects, for example,
			// to int 1.
			if ( ! is_numeric( $value ) ) {
				return false;
			}

			$value = intval( $value );

			if ( $value < 1 ) {
				return false;
			}

		} elseif ( 'email' === $field ) {

			if ( ! is_email( $value ) ) {
				return false;
			}

			$value = trim( $value );
		}

		if ( ! $value ) {
			return false;
		}

		switch ( $field ) {
			case 'id':
				$db_field = 'id';
				break;
			case 'email':
				$value    = sanitize_text_field( $value );
				$db_field = 'email';
				break;
			case 'user_id':
				$db_field = 'user_id';
				break;
			default:
				return false;
		}

		if ( ! $lead = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $this->table_name WHERE $db_field = %s LIMIT 1", $value ) ) ) {
			return false;
		}

		return $lead;
	}

	/**
	 * Retrieve leads from the database
	 *
	 * @access  public
	 * @since   2.4
	*/
	public function get_leads( $args = array() ) {

		global $wpdb;

		$defaults = array(
			'number'       => 20,
			'offset'       => 0,
			'user_id'      => 0,
			'orderby'      => 'id',
			'order'        => 'DESC'
		);

		$args  = wp_parse_args( $args, $defaults );

		if( $args['number'] < 1 ) {
			$args['number'] = 999999999999;
		}

		$where = ' WHERE 1=1 ';

		// specific leads
		if( ! empty( $args['id'] ) ) {

			if( is_array( $args['id'] ) ) {
				$ids = implode( ',', array_map('intval', $args['id'] ) );
			} else {
				$ids = intval( $args['id'] );
			}

			$where .= " AND `id` IN( {$ids} ) ";

		}

		// leads for specific user accounts
		if( ! empty( $args['user_id'] ) ) {

			if( is_array( $args['user_id'] ) ) {
				$user_ids = implode( ',', array_map('intval', $args['user_id'] ) );
			} else {
				$user_ids = intval( $args['user_id'] );
			}

			$where .= " AND `user_id` IN( {$user_ids} ) ";

		}

		//specific leads by email
		if( ! empty( $args['email'] ) ) {

			if( is_array( $args['email'] ) ) {

				$emails_count       = count( $args['email'] );
				$emails_placeholder = array_fill( 0, $emails_count, '%s' );
				$emails             = implode( ', ', $emails_placeholder );

				$where .= $wpdb->prepare( " AND `email` IN( $emails ) ", $args['email'] );
			} else {
				$where .= $wpdb->prepare( " AND `email` = %s ", $args['email'] );
			}
		}

		// specific leads by name
		if( ! empty( $args['name'] ) ) {
			$where .= $wpdb->prepare( " AND `name` LIKE '%%%%" . '%s' . "%%%%' ", $args['name'] );
		}

		// Leads created for a specific date or in a date range
		if( ! empty( $args['date'] ) ) {

			if( is_array( $args['date'] ) ) {

				if( ! empty( $args['date']['start'] ) ) {

					$start = date( 'Y-m-d H:i:s', strtotime( $args['date']['start'] ) );

					$where .= " AND `date_created` >= '{$start}'";

				}

				if( ! empty( $args['date']['end'] ) ) {

					$end = date( 'Y-m-d H:i:s', strtotime( $args['date']['end'] ) );

					$where .= " AND `date_created` <= '{$end}'";

				}

			} else {

				$year  = date( 'Y', strtotime( $args['date'] ) );
				$month = date( 'm', strtotime( $args['date'] ) );
				$day   = date( 'd', strtotime( $args['date'] ) );

				$where .= " AND $year = YEAR ( date_created ) AND $month = MONTH ( date_created ) AND $day = DAY ( date_created )";
			}

		}

		$args['orderby'] = ! array_key_exists( $args['orderby'], $this->get_columns() ) ? 'id' : $args['orderby'];

		$cache_key = md5( 'epl_leads_' . serialize( $args ) );

		$leads = wp_cache_get( $cache_key, 'leads' );

		$args['orderby'] = esc_sql( $args['orderby'] );
		$args['order']   = esc_sql( $args['order'] );

		if( $leads === false ) {
			$leads = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM  $this->table_name $where ORDER BY {$args['orderby']} {$args['order']} LIMIT %d,%d;", absint( $args['offset'] ), absint( $args['number'] ) ) );
			wp_cache_set( $cache_key, $leads, 'leads', 3600 );
		}

		return $leads;

	}


	/**
	 * Count the total number of leads in the database
	 *
	 * @access  public
	 * @since   2.4
	*/
	public function count( $args = array() ) {

		global $wpdb;

		$where = ' WHERE 1=1 ';

		if( ! empty( $args['date'] ) ) {

			if( is_array( $args['date'] ) ) {

				$start = date( 'Y-m-d H:i:s', strtotime( $args['date']['start'] ) );
				$end   = date( 'Y-m-d H:i:s', strtotime( $args['date']['end'] ) );

				$where .= " AND `date_created` >= '{$start}' AND `date_created` <= '{$end}'";

			} else {

				$year  = date( 'Y', strtotime( $args['date'] ) );
				$month = date( 'm', strtotime( $args['date'] ) );
				$day   = date( 'd', strtotime( $args['date'] ) );

				$where .= " AND $year = YEAR ( date_created ) AND $month = MONTH ( date_created ) AND $day = DAY ( date_created )";
			}

		}


		$cache_key = md5( 'epl_leads_count' . serialize( $args ) );

		$count = wp_cache_get( $cache_key, 'leads' );

		if( $count === false ) {
			$count = $wpdb->get_var( "SELECT COUNT($this->primary_key) FROM " . $this->table_name . "{$where};" );
			wp_cache_set( $cache_key, $count, 'leads', 3600 );
		}

		return absint( $count );

	}

	/**
	 * Create the table
	 *
	 * @access  public
	 * @since   2.4
	*/
	public function create_table() {

		global $wpdb;

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		$sql = "CREATE TABLE " . $this->table_name . " (
		id bigint(20) NOT NULL AUTO_INCREMENT,
		user_id bigint(20) NOT NULL,
		email varchar(50) NOT NULL,
		name mediumtext NOT NULL,
		listing_count bigint(20) NOT NULL,
		listing_ids longtext NOT NULL,
		date_created datetime NOT NULL,
		PRIMARY KEY  (id),
		UNIQUE KEY email (email),
		KEY user (user_id)
		) CHARACTER SET utf8 COLLATE utf8_general_ci;";

		dbDelta( $sql );

		update_option( $this->table_name . '_db_version', $this->version );
	}

	/**
	 * Check if the Leads table was ever installed
	 *
	 * @since  2.4
	 * @return bool Returns if the leads table was installed and upgrade routine run
	 */
	public function installed() {
		return $this->table_exists( $this->table_name );
	}
}
