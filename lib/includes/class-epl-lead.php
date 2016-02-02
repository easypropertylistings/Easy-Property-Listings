<?php
/**
 * Lead Object
 *
 * @package     EPL
 * @since       2.4
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * EPL_Lead Class
 *
 * @since 2.4
 */
class EPL_Lead {

	/**
	 * The lead ID
	 *
	 * @since 2.4
	 */
	public $id = 0;

	/**
	 * The lead's listing count
	 *
	 * @since 2.4
	 */
	public $listing_count = 0;


	/**
	 * The lead's email
	 *
	 * @since 2.4
	 */
	public $email;

	/**
	 * The lead's name
	 *
	 * @since 2.4
	 */
	public $name;

	/**
	 * The lead's creation date
	 *
	 * @since 2.4
	 */
	public $date_created;

	/**
	 * The listing IDs associated with the lead
	 *
	 * @since  2.4
	 */
	public $listing_ids;

	/**
	 * The user ID associated with the lead
	 *
	 * @since  2.4
	 */
	public $user_id;

	/**
	 * Lead Notes
	 *
	 * @since  2.4
	 */
	public $notes;

	/**
	 * The Database Abstraction
	 *
	 * @since  2.4
	 */
	protected $db;

	/**
	 * Get things going
	 *
	 * @since 2.4
	 */
	public function __construct( $_id_or_email = false, $by_user_id = false ) {

		$this->db = new EPL_DB_Leads;

		if ( false === $_id_or_email || ( is_numeric( $_id_or_email ) && (int) $_id_or_email !== absint( $_id_or_email ) ) ) {
			return false;
		}

		$by_user_id = is_bool( $by_user_id ) ? $by_user_id : false;

		if ( is_numeric( $_id_or_email ) ) {
			$field = $by_user_id ? 'user_id' : 'id';
		} else {
			$field = 'email';
		}

		$lead = $this->db->get_lead_by( $field, $_id_or_email );

		if ( empty( $lead ) || ! is_object( $lead ) ) {
			return false;
		}

		$this->setup_lead( $lead );

	}

	/**
	 * Given the lead data, let's set the variables
	 *
	 * @since  2.4
	 * @param  object $lead The Lead Object
	 * @return bool             If the setup was successful or not
	 */
	private function setup_lead( $lead ) {

		if ( ! is_object( $lead ) ) {
			return false;
		}

		foreach ( $lead as $key => $value ) {

			switch ( $key ) {

				case 'notes':
					$this->$key = $this->get_notes();
					break;

				default:
					$this->$key = $value;
					break;

			}

		}

		// Lead ID and email are the only things that are necessary, make sure they exist
		if ( ! empty( $this->id ) && ! empty( $this->email ) ) {
			return true;
		}

		return false;

	}

	/**
	 * Magic __get function to dispatch a call to retrieve a private property
	 *
	 * @since 2.4
	 */
	public function __get( $key ) {

		if( method_exists( $this, 'get_' . $key ) ) {

			return call_user_func( array( $this, 'get_' . $key ) );

		} else {

			return new WP_Error( 'epl-lead-invalid-property', sprintf( __( 'Can\'t get property %s', 'epl' ), $key ) );

		}

	}

	/**
	 * Creates a lead
	 *
	 * @since  2.4
	 * @param  array  $data Array of attributes for a lead
	 * @return mixed        False if not a valid creation, Lead ID if user is found or valid creation
	 */
	public function create( $data = array() ) {

		if ( $this->id != 0 || empty( $data ) ) {
			return false;
		}

		$defaults = array(
			'listing_ids' => ''
		);

		$args = wp_parse_args( $data, $defaults );
		$args = $this->sanitize_columns( $args );

		if ( empty( $args['email'] ) || ! is_email( $args['email'] ) ) {
			return false;
		}

		do_action( 'epl_lead_pre_create', $args );

		$created = false;

		// The DB class 'add' implies an update if the lead being asked to be created already exists
		if ( $this->db->add( $data ) ) {

			// We've successfully added/updated the lead, reset the class vars with the new data
			$lead = $this->db->get_lead_by( 'email', $args['email'] );

			// Setup the lead data with the values from DB
			$this->setup_lead( $lead );

			$created = $this->id;
			
		}

		do_action( 'epl_lead_post_create', $created, $args );

		return $created;

	}

	/**
	 * Update a lead record
	 *
	 * @since  2.4
	 * @param  array  $data Array of data attributes for a lead (checked via whitelist)
	 * @return bool         If the update was successful or not
	 */
	public function update( $data = array() ) {

		if ( empty( $data ) ) {
			return false;
		}

		$data = $this->sanitize_columns( $data );

		do_action( 'epl_lead_pre_update', $this->id, $data );

		$updated = false;

		if ( $this->db->update( $this->id, $data ) ) {

			$lead = $this->db->get_lead_by( 'id', $this->id );
			$this->setup_lead( $lead);

			$updated = true;
		}

		do_action( 'epl_lead_post_update', $updated, $this->id, $data );

		return $updated;
	}


	/**
	 * Attach listing to the lead then triggers increasing stats
	 *
	 * @since  2.4
	 * @param  int $listing_id The listing ID to attach to the lead
	 * @param  bool $update_stats For backwards compatibility, if we should increase the stats or not
	 * @return bool            If the attachment was successfuly
	 */
	public function attach_listing( $listing_id = 0, $update_stats = true ) {

		if( empty( $listing_id ) ) {
			return false;
		}

		if( empty( $this->listing_ids ) ) {

			$new_listing_ids = $listing_id;

		} else {

			$listing_ids = array_map( 'absint', explode( ',', $this->listing_ids ) );

			if ( in_array( $listing_id, $listing_ids ) ) {
				$update_stats = false;
			}

			$listing_ids[] = $listing_id;

			$new_listing_ids = implode( ',', array_unique( array_values( $listing_ids ) ) );

		}

		do_action( 'epl_lead_pre_attach_listing', $listing_id, $this->id );

		$listing_added = $this->update( array( 'listing_ids' => $new_listing_ids ) );

		if ( $listing_added ) {

			$this->listing_ids = $new_listing_ids;

			// We added this listing successfully, increment the stats
			if ( $update_stats ) {

				$this->increase_listing_count();
			} 
			
			// update the leads of listing as well
			$lead_ids = get_post_meta($listing_id,'epl_interested_leads',true);
			if(!empty($lead_ids)) {
				$lead_ids[] = $this->id;
			} else {
				$lead_ids = array($this->id);
			}
			update_post_meta($listing_id,'epl_interested_leads',$lead_ids);

		}

		do_action( 'epl_lead_post_attach_listing', $listing_added, $listing_id, $this->id );

		return $listing_added;
	}


	/**
	 * Remove a listing from this lead, then triggers reducing stats
	 *
	 * @since  2.4
	 * @param  integer $listing_id The Payment ID to remove
	 * @param  bool $update_stats For backwards compatibility, if we should increase the stats or not
	 * @return boolean             If the removal was successful
	 */
	public function remove_listing( $listing_id = 0, $update_stats = true ) {

		if( empty( $listing_id ) ) {
			return false;
		}

		$new_listing_ids = '';

		if( ! empty( $this->listing_ids ) ) {

			$listing_ids = array_map( 'absint', explode( ',', $this->listing_ids ) );

			$pos = array_search( $listing_id, $listing_ids );
			if ( false === $pos ) {
				return false;
			}

			unset( $listing_ids[ $pos ] );
			$listing_ids = array_filter( $listing_ids );

			$new_listing_ids = implode( ',', array_unique( array_values( $listing_ids ) ) );

		}

		do_action( 'epl_lead_pre_remove_listing', $listing_id, $this->id );

		$listing_removed = $this->update( array( 'listing_ids' => $new_listing_ids ) );

		if ( $listing_removed ) {

			$this->listing_ids = $new_listing_ids;

			if ( $update_stats ) {
				$this->decrease_listing_count();
			}

		}
		// update the leads of listing as well
		$lead_ids = get_post_meta($listing_id,'epl_interested_leads',true);
		if(!empty($lead_ids)) {
			$pos = array_search( $this->id, $lead_ids );
			if ( false !== $pos ) {
				unset( $lead_ids[ $pos ] );
				update_post_meta($listing_id,'epl_interested_leads',$lead_ids);		
			}

			
		}

		do_action( 'epl_lead_post_remove_listing', $listing_removed, $listing_id, $this->id );

		return $listing_removed;

	}

	/**
	 * Increase the listing count of a lead
	 *
	 * @since  2.4
	 * @param  integer $count The number to imcrement by
	 * @return int            The listing count
	 */
	public function increase_listing_count( $count = 1 ) {

		// Make sure it's numeric and not negative
		if ( ! is_numeric( $count ) || $count != absint( $count ) ) {
			return false;
		}

		$new_total = (int) $this->listing_count + (int) $count;

		do_action( 'epl_lead_pre_increase_listing_count', $count, $this->id );

		if ( $this->update( array( 'listing_count' => $new_total ) ) ) {
			$this->listing_count = $new_total;
		}

		do_action( 'epl_lead_post_increase_listing_count', $this->listing_count, $count, $this->id );

		return $this->listing_count;
	}

	/**
	 * Decrease the lead listing count
	 *
	 * @since  2.4
	 * @param  integer $count The amount to decrease by
	 * @return mixed          If successful, the new count, otherwise false
	 */
	public function decrease_listing_count( $count = 1 ) {

		// Make sure it's numeric and not negative
		if ( ! is_numeric( $count ) || $count != absint( $count ) ) {
			return false;
		}

		$new_total = (int) $this->listing_count - (int) $count;

		if( $new_total < 0 ) {
			$new_total = 0;
		}

		do_action( 'epl_lead_pre_decrease_listing_count', $count, $this->id );

		if ( $this->update( array( 'listing_count' => $new_total ) ) ) {
			$this->listing_count = $new_total;
		}

		do_action( 'epl_lead_post_decrease_listing_count', $this->listing_count, $count, $this->id );

		return $this->listing_count;
	}

	/**
	 * Get the parsed notes for a lead as an array
	 *
	 * @since  2.4
	 * @param  integer $length The number of notes to get
	 * @param  integer $paged What note to start at
	 * @return array           The notes requsted
	 */
	public function get_notes( $number = 20, $paged = 1 ) {

		$length = is_numeric( $length ) ? $length : 20;
		$offset = is_numeric( $paged ) && $paged != 1 ? ( ( absint( $paged ) - 1 ) * $number ) : 0;

		return get_comments( array( 'meta_key' => 'epl_lead_id', 'meta_value' => $this->id, 'offset'	=>	$offset, 'number'	=>	$number ) );

	}

	/**
	 * Get the total number of notes we have after parsing
	 *
	 * @since  2.4
	 * @return int The number of notes for the lead
	 */
	public function get_notes_count() {
		
		return get_comments( array( 'meta_key' => 'epl_lead_id', 'meta_value' => $this->id , 'count' => true) );

	}

	/**
	 * Add a note for the lead
	 *
	 * @since  2.4
	 * @param string $note The note to add
	 * @return string|boolean The new note if added succesfully, false otherwise
	 */
	public function add_note( $note = '',$note_type='epl_user_note',$listing_id=0 ) {

		$note = trim( $note );
		if ( empty( $note ) ) {
			return false;
		}
		
		$note    = apply_filters( 'epl_lead_add_note_string', $note, $note_type, $listing_id );

		do_action( 'epl_lead_pre_add_note', $note, $this->id );
		
		$notedata = array(
			'comment_post_ID' 		=> absint($listing_id),
			'comment_author' 		=> $this->name,
			'comment_author_email' 	=> $this->email,
			'comment_content' 		=> $note, 
			'comment_type' 			=> $note_type,
			'comment_parent' 		=> 0,
			'comment_agent' 		=> 'epl',
			'user_id' 				=> $this->user_id,
		);

		//Insert new note and get the note ID
		if ( $note_id = wp_insert_comment( $notedata ) ) {
			$this->notes = $this->get_notes();
			update_comment_meta($note_id,'epl_lead_id',$this->id);
		}

		do_action( 'epl_lead_post_add_note', $this->notes, $new_note, $this->id, $note_type, $listing_id );
		
		// Return the note object
		return get_comment($note_id);

	}


	/**
	 * Sanitize the data for update/create
	 *
	 * @since  2.4
	 * @param  array $data The data to sanitize
	 * @return array       The sanitized data, based off column defaults
	 */
	private function sanitize_columns( $data ) {

		$columns        = $this->db->get_columns();
		$default_values = $this->db->get_column_defaults();

		foreach ( $columns as $key => $type ) {

			// Only sanitize data that we were provided
			if ( ! array_key_exists( $key, $data ) ) {
				continue;
			}

			switch( $type ) {

				case '%s':
					if ( 'email' == $key ) {
						$data[$key] = sanitize_email( $data[$key] );
					}else {
						$data[$key] = sanitize_text_field( $data[$key] );
					}
					break;

				case '%d':
					if ( ! is_numeric( $data[$key] ) || (int) $data[$key] !== absint( $data[$key] ) ) {
						$data[$key] = $default_values[$key];
					} else {
						$data[$key] = absint( $data[$key] );
					}
					break;

				case '%f':
					// Convert what was given to a float
					$value = floatval( $data[$key] );

					if ( ! is_float( $value ) ) {
						$data[$key] = $default_values[$key];
					} else {
						$data[$key] = $value;
					}
					break;

				default:
					$data[$key] = sanitize_text_field( $data[$key] );
					break;

			}

		}

		return $data;
	}

}
