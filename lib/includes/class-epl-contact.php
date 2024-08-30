<?php
/**
 * Contact Object
 *
 * @package     EPL
 * @subpackage  Classes/Contact
 * @copyright   Copyright (c) 2020, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// phpcs:disable WordPress.DB.SlowDBQuery

/**
 * EPL_Contact Class
 *
 * @since 3.0.0
 */
class EPL_Contact {

	/**
	 * The contact ID
	 *
	 * @var int $ID Contact ID.
	 * @since 3.0.0
	 */
	public $ID = 0;

	/**
	 * The contact ID Fallback
	 *
	 * @var int $id Contact ID.
	 * @since 3.0.0
	 */
	public $id = 0;

	/**
	 * The contact's listing count
	 *
	 * @var int $listing_count Contact listing count.
	 * @since 3.0.0
	 */
	public $listing_count = 0;

	/**
	 * The contact's email
	 *
	 * @var string $email Contact email address.
	 * @since 3.0.0
	 */
	public $email;

	/**
	 * The contact's name
	 *
	 * @var string $name Contact name.
	 * @since 3.0.0
	 */
	public $name;

	/**
	 * The contact's creation date
	 *
	 * @var string $date_created Contact date created.
	 * @since 3.0.0
	 */
	public $date_created;

	/**
	 * The contact's background info
	 *
	 * @var string $background_info.
	 * @since 3.5.0
	 */
	public $background_info;

	/**
	 * The contact's type
	 *
	 * @var string $type.
	 * @since 3.5.0
	 */
	public $type;

	/**
	 * The contact's heading
	 *
	 * @var string $heading.
	 * @since 3.5.0
	 */
	public $heading;

	/**
	 * The listing IDs associated with the contact
	 *
	 * @var string $listing_ids Contact associated listing ids.
	 * @since  3.0
	 */
	public $listing_ids;

	/**
	 * Contact Notes
	 *
	 * @var string notes Contact notes.
	 * @since  3.0
	 */
	public $notes;

	/**
	 * Get things going
	 *
	 * @param bool $_id_or_email Contact ID or email.
	 * @since 3.0.0
	 */
	public function __construct( $_id_or_email = false ) {

		if ( false === $_id_or_email || ( is_numeric( $_id_or_email ) && absint( $_id_or_email ) !== (int) $_id_or_email ) ) {
			return false;
		}

		if ( is_numeric( $_id_or_email ) ) {
			$field = 'id';
		} else {
			$field = 'email';
		}

		$contact = $this->get_contact_by( $field, $_id_or_email );

		if ( empty( $contact ) || ! is_object( $contact ) ) {
			return false;
		}
		$this->setup_contact( $contact );

	}

	/**
	 * Get the primary email of contact / alias of get_primary_email
	 *
	 * @param int $id Contact ID.
	 * @return mixed
	 * @since  3.0
	 */
	public function get_contact_email( $id ) {
		return $this->get_primary_email( $id );
	}

	/**
	 * Update the primary email of contact
	 *
	 * @param int    $id Contact ID.
	 * @param string $value Contact email.
	 * @return bool|int
	 * @since  3.0
	 */
	public function update_contact_email( $id, $value ) {
		$emails          = (array) get_post_meta( $id, 'contact_emails', true );
		$emails['email'] = $value;
		return update_post_meta( $id, 'contact_emails', $emails );
	}

	/**
	 * Given the contact data, let's set the variables
	 *
	 * @since  3.0
	 * @param  object $contact The contact object.
	 * @return bool If the setup was successful or not.
	 * @since 3.5.0 Fixed case of property ID.
	 */
	private function setup_contact( $contact ) {

		if ( ! is_object( $contact ) ) {
			return false;
		}

		foreach ( array( 'ID', 'name', 'email', 'notes', 'listing_ids', 'listing_count', 'date_created', 'background_info', 'type', 'heading' ) as $key ) {

			switch ( $key ) {

				case 'ID':
					$this->$key = $contact->ID;
					$this->ID   = $contact->ID;
					break;

				case 'email':
					$this->$key = $this->get_contact_email( $contact->ID );
					break;

				case 'notes':
					$this->$key = $this->get_notes();
					break;

				case 'listing_ids':
					$this->$key = $this->get_listings();
					break;

				case 'listing_count':
					$this->$key = $this->get_listing_count();
					break;

				case 'date_created':
					$this->$key = $contact->post_date;
					break;

				case 'heading':
					$this->$key = $contact->post_title;
					break;

				case 'name':
					$this->$key = $this->get_meta( 'contact_first_name' ) . ' ' . $this->get_meta( 'contact_last_name' );
					break;

				case 'background_info':
					$this->$key = $contact->post_content;
					break;

				case 'type':
					$this->$key = $this->get_category_label();
					break;
			}
		}

		// Contact ID and email are the only things that are necessary, make sure they exist.
		if ( ! empty( $this->ID ) && ! empty( $this->email ) ) {
			return true;
		}

		return false;

	}

	/**
	 * Retrieves a single contact from the database
	 *
	 * @access public
	 * @param string $field Type of field option, id or email.
	 * @param mixed  $value The Contact ID or email to search.
	 * @return mixed Upon success, an object of the contact. Upon failure, NULL
	 * @since  3.0
	 */
	public function get_contact_by( $field = 'id', $value = 0 ) {

		if ( empty( $field ) || empty( $value ) ) {
			return null;
		}

		if ( 'id' === $field ) {
			// Make sure the value is numeric to avoid casting objects, for example, to int 1.
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

		$contact = false;
		switch ( $field ) {
			case 'id':
				$contact = get_post( $value );
				break;
			case 'email':
				$matched_contacts = new WP_Query(
					array(
						'post_type'   => 'epl_contact',
						'post_status' => 'publish',
						'paged'       => 1,
						'offset'      => 0,
						'meta_query'  => array(
							array(
								'key'     => 'contact_emails',
								'value'   => sprintf( ':"%s";', $value ),
								'compare' => 'LIKE',
							),
						),
					)
				);
				if ( ! empty( $matched_contacts->posts ) ) {

					foreach ( $matched_contacts->posts as $matched_contact ) {
						$emails = get_post_meta( $matched_contact->ID, 'contact_emails', true );

						if ( isset( $emails['email'] ) && $emails['email'] === $value ) {
							$contact = $matched_contacts->post;
						}
					}
				}

				break;
			default:
				return false;
		}

		if ( ! $contact ) {
			return false;
		}

		return $contact;
	}

	/**
	 * Magic __get function to dispatch a call to retrieve a private property
	 *
	 * @param string $key Meta key.
	 * @return mixed|WP_Error
	 * @since 3.0.0
	 */
	public function __get( $key ) {

		if ( method_exists( $this, 'get_' . $key ) ) {
			return call_user_func( array( $this, 'get_' . $key ) );
		} else {
			/* Translators: %s is the meta key. */
			return new WP_Error( 'epl-contact-invalid-property', sprintf( __( 'Can\'t get property %s', 'easy-property-listings' ), $key ) );
		}
	}

	/**
	 * Creates a contact
	 *
	 * @since  3.0
	 * @param  array $data Array of attributes for a contact.
	 * @return mixed False if not a valid creation, Contact ID if user is found or valid creation
	 */
	public function create( $data = array() ) {

		if ( 0 !== $this->ID || empty( $data ) ) {
			return false;
		}

		$defaults = array(
			'name'  => '',
			'email' => '',
		);

		$args = wp_parse_args( $data, $defaults );

		if ( empty( $args['email'] ) || ! is_email( $args['email'] ) ) {
			return false;
		}

		do_action( 'epl_contact_pre_create', $args );

		$created = false;

		$contact = $this->contact_exists( $args['email'] );

		if ( $contact ) {
			wp_update_post(
				array(
					'post_title' => $args['name'],
					'post_type'  => 'epl_contact',
					'ID'         => $contact->ID,
				)
			);

		} else {

			$inserted = wp_insert_post(
				array(
					'post_title'  => $args['name'],
					'post_type'   => 'epl_contact',
					'post_status' => 'publish',
				)
			);

			if ( $inserted ) {
				$this->update_contact_email( $inserted, $args['email'] );
				$contact = $this->get_contact_by( 'email', $args['email'] );
				$this->setup_contact( $contact );
			}
		}
		$created = $contact->ID;

		do_action( 'epl_contact_post_create', $created, $args );
		return $created;
	}

	/**
	 * Checking for contact
	 *
	 * @param null $email Contact email address.
	 * @return bool|WP_Post
	 * @since  3.0
	 */
	public function contact_exists( $email = null ) {
		$matched_contacts = new WP_Query(
			array(
				'post_type'   => 'epl_contact',
				'post_status' => 'publish',
				'meta_query'  => array(
					array(
						'key'     => 'contact_emails',
						'value'   => sprintf( ':"%s";', $email ),
						'compare' => 'LIKE',
					),
				),
			)
		);
		if ( ! empty( $matched_contacts->posts ) ) {

			foreach ( $matched_contacts->posts as $matched_contact ) {
				$emails = get_post_meta( $matched_contact->ID, 'contact_emails', true );

				if ( isset( $emails['email'] ) && $emails['email'] === $email ) {
					return $matched_contacts->post;
				}
			}
		}
		return false;
	}

	/**
	 * Update a contact record
	 *
	 * @since  3.0
	 * @param  array $data Array of data attributes for a contact (checked via whitelist).
	 * @return bool If the update was successful or not
	 */
	public function update( $data = array() ) {

		if ( empty( $data ) ) {
			return false;
		}

		do_action( 'epl_contact_pre_update', $this->ID, $data );

		$updated = false;

		$updated = wp_update_post(
			array(
				'post_title'  => $data['name'],
				'post_type'   => 'epl_contact',
				'ID'          => $this->ID,
				'post_status' => 'publish',
			)
		);

		if ( $updated ) {

			if ( isset( $data['email'] ) && is_email( $data['email'] ) ) {
				// Make sure email is new & not already attached to some other contact.
				if ( ! $this->contact_exists( $data['email'] ) && ( $data['email'] !== $this->email ) ) {
					$this->update_contact_email( $this->ID, $data['email'] );
				}
			}

			$contact = $this->get_contact_by( 'id', $this->ID );
			$this->setup_contact( $contact );

			$updated = true;
		}

		do_action( 'epl_contact_post_update', $updated, $this->ID, $data );

		return $updated;
	}

	/**
	 * Attach listing to the contact then triggers increasing stats
	 *
	 * @since  3.0
	 * @param  int $listing_id The listing ID to attach to the contact.
	 * @return bool If the attachment was successfully
	 */
	public function attach_listing( $listing_id = 0 ) {

		if ( empty( $listing_id ) ) {
			return false;
		}

		if ( empty( $this->listing_ids ) ) {

			$new_listing_ids = array( $listing_id );

		} else {

			$this->listing_ids[] = $listing_id;

			$new_listing_ids = array_unique( array_values( $this->listing_ids ) );

		}

		do_action( 'epl_contact_pre_attach_listing', $listing_id, $this->ID );

		$listing_added = update_post_meta( $this->ID, 'contact_interested_listings', $new_listing_ids );

		if ( $listing_added ) {

			// Update the contacts of listing as well.
			$contact_ids = get_post_meta( $listing_id, 'epl_interested_contacts', true );
			if ( ! empty( $contact_ids ) ) {
				$contact_ids[] = $this->ID;
			} else {
				$contact_ids = array( $this->ID );
			}
			update_post_meta( $listing_id, 'epl_interested_contacts', $contact_ids );

		}

		do_action( 'epl_contact_post_attach_listing', $listing_added, $listing_id, $this->ID );

		return $listing_added;
	}


	/**
	 * Remove a listing from this contact, then triggers reducing stats
	 *
	 * @since  3.0
	 * @param  integer $listing_id The Payment ID to remove.
	 * @return boolean If the removal was successful
	 */
	public function remove_listing( $listing_id = 0 ) {

		if ( empty( $listing_id ) ) {
			return false;
		}

		$new_listing_ids = '';

		if ( ! empty( $this->listing_ids ) ) {

			$pos = array_search( $listing_id, $this->listing_ids ); // phpcs:ignore
			if ( false === $pos ) {
				return false;
			}

			unset( $this->listing_ids[ $pos ] );
			$listing_ids = array_filter( $this->listing_ids );

			$new_listing_ids = array_unique( array_values( $listing_ids ) );

		}

		do_action( 'epl_contact_pre_remove_listing', $listing_id, $this->ID );

		$listing_added = update_post_meta( $this->ID, 'contact_interested_listings', $new_listing_ids );

		$listing_removed = $listing_added;

		// Update the contacts of listing as well.
		$contact_ids = get_post_meta( $listing_id, 'epl_interested_contacts', true );
		if ( ! empty( $contact_ids ) ) {
			$pos = array_search( $this->ID, $contact_ids ); // phpcs:ignore
			if ( false !== $pos ) {
				unset( $contact_ids[ $pos ] );
				update_post_meta( $listing_id, 'epl_interested_contacts', $contact_ids );
			}
		}

		do_action( 'epl_contact_post_remove_listing', $listing_removed, $listing_id, $this->ID );

		return $listing_removed;

	}


	/**
	 * Get listings of contact
	 *
	 * @since  3.0
	 */
	public function get_listings() {

		return get_post_meta( $this->ID, 'contact_interested_listings', true );
	}

	/**
	 * Get listings count
	 *
	 * @since  3.0
	 */
	public function get_listing_count() {

		$listings = (array) get_post_meta( $this->ID, 'contact_interested_listings', true );
		$listings = array_filter( $listings );
		return count( $listings );
	}

	/**
	 * Get the parsed notes for a contact as an array
	 *
	 * @param int    $number Number of notes per page.
	 * @param int    $paged Paged query.
	 * @param string $orderby Order by option.
	 * @param string $order Order option.
	 *
	 * @return array The notes requested
	 */
	public function get_notes( $number = 10, $paged = 1, $orderby = 'comment_date', $order = 'DESC' ) {

		$length = is_numeric( $number ) ? $number : 10;
		$offset = is_numeric( $paged ) && 1 !== $paged ? ( ( absint( $paged ) - 1 ) * $number ) : 0;
		$args   = array(
			'meta_key'   => 'epl_contact_id',
			'meta_value' => $this->ID,
			'offset'     => $offset,
			'number'     => $length,
			'orderby'    => $orderby,
			'order'      => $order,
		);

		return get_comments( $args );

	}

	/**
	 * Get the total number of notes we have after parsing
	 *
	 * @since  3.0
	 * @return int The number of notes for the contact.
	 */
	public function get_notes_count() {

		return get_comments(
			array(
				'meta_key'   => 'epl_contact_id',
				'meta_value' => $this->ID,
				'count'      => true,
			)
		);
	}

	/**
	 * Add a note for the contact
	 *
	 * @param string $note The note to add.
	 * @param string $note_type Type of note.
	 * @param int    $listing_id Listing ID.
	 * @return string|boolean The new note if added successfully, false otherwise
	 * @since  3.0
	 */
	public function add_note( $note = '', $note_type = 'note', $listing_id = 0 ) {

		$note = trim( $note );
		if ( empty( $note ) ) {
			return false;
		}

		$note = apply_filters( 'epl_contact_add_note_string', $note, $note_type, $listing_id );

		do_action( 'epl_contact_pre_add_note', $note, $this->ID );

		$note_data = array(
			'comment_post_ID'      => absint( $listing_id ),
			'comment_author'       => $this->name,
			'comment_author_email' => $this->email,
			'comment_content'      => $note,
			'comment_type'         => $note_type,
			'comment_parent'       => 0,
			'comment_agent'        => 'easy-property-listings',
		);

		// Insert new note and get the note ID.
		$note_id = wp_insert_comment( $note_data );
		if ( $note_id ) {
			$this->notes = $this->get_notes();
			update_comment_meta( $note_id, 'epl_contact_id', $this->ID );
		}

		do_action( 'epl_contact_post_add_note', $this->notes, $note_data, $this->ID );

		// Return the note object.
		return get_comment( $note_id );

	}

	/**
	 * Delete the contact
	 *
	 * @since  3.0
	 */
	public function delete() {
		return wp_delete_post( $this->ID, true );
	}

	/**
	 * Update Contact Meta
	 *
	 * @param string $key Meta key name.
	 * @param string $value Meta value.
	 * @return bool|int
	 * @since  3.0
	 */
	public function update_meta( $key, $value ) {
		return update_post_meta( $this->ID, $key, $value );
	}

	/**
	 * Get Contact Meta
	 *
	 * @param string $key Meta key name.
	 * @return mixed
	 * @since  3.0
	 */
	public function get_meta( $key ) {
		return get_post_meta( $this->ID, $key, true );
	}

	/**
	 * Returns activity type label for activity type
	 *
	 * @param string $key Meta key name.
	 * @return mixed
	 * @since  3.0
	 */
	public function get_activity_type( $key ) {
		$activity_types = $this->get_activity_types();
		return isset( $activity_types[ $key ] ) ? $activity_types[ $key ] : $activity_types['epl-admin-note'];
	}

	/**
	 * Returns all activity types
	 *
	 * @return mixed
	 * @since 3.0.0
	 */
	public static function get_activity_types() {
		return apply_filters(
			'epl_contact_activity_labels',
			array(
				'note'           => __( 'User Activity', 'easy-property-listings' ),
				'epl-admin-note' => __( 'Admin Note', 'easy-property-listings' ),
				'call'           => __( 'Call', 'easy-property-listings' ),
				'email'          => __( 'Email', 'easy-property-listings' ),
				'listing'        => __( 'Listing', 'easy-property-listings' ),
			)
		);
	}

	/**
	 * Returns Contact Address
	 *
	 * @return mixed
	 * @since 3.0.0
	 */
	public function epl_contact_get_address() {

		$addr           = $this->get_meta( 'contact_street_number' ) . ' ';
		$addr          .= $this->get_meta( 'contact_street_name' ) . ' ';
		$contact_suburb = $this->get_meta( 'contact_suburb' );
		if ( ! empty( $contact_suburb ) ) {
			$addr .= $this->get_meta( 'contact_suburb' ) . ', ';
		}

		$addr .= $this->get_meta( 'contact_state' ) . ' ';
		$addr .= $this->get_meta( 'contact_postcode' ) . ' ';
		$addr .= $this->get_meta( 'contact_country' ) . ' ';

		return apply_filters( 'epl_contact_formatted_address', $addr );
	}

	/**
	 * Returns Contact Primary Email
	 *
	 * @param int $id User ID.
	 * @return mixed
	 * @since 3.0.0
	 */
	public function get_primary_email( $id ) {
		$emails = get_post_meta( $id, 'contact_emails', true );
		return isset( $emails['email'] ) ? $emails['email'] : false;
	}

	/**
	 * Returns Contact Email
	 *
	 * @return mixed
	 * @since 3.0.0
	 * @since 3.4.1 Added value span wrapper.
	 */
	public function get_emails() {

		$emails = (array) $this->get_meta( 'contact_emails' );
		$emails = array_filter( $emails );
		if ( ! empty( $emails ) ) {
			ob_start();
			foreach ( $emails as $mail_name   => $mail_value ) {
				$label = ucwords( str_replace( '_', ' ', $mail_name ) ); ?>
				<span class="contact-email epl-info-item editable" data-key="email">
					<span class="dashicons dashicons-email epl-contact-icons"></span>
					<span class="epl-contact-value"><?php echo esc_attr( $label . ' - ' . $mail_value ); ?></span>
				</span>
				<?php
			}
			$email_html = ob_get_clean();
			return apply_filters( 'epl_contact_email_html', $email_html );
		}
	}

	/**
	 * Returns Contact Phone
	 *
	 * @return mixed
	 * @since 3.0.0
	 * @since 3.4.1 Renamed emails to phones and added value span wrapper.
	 */
	public function get_phones() {

		$phone_numbers = $this->get_meta( 'contact_phones' );
		$phone_numbers = array_filter( $phone_numbers );
		if ( ! empty( $phone_numbers ) ) {
			ob_start();
			foreach ( $phone_numbers as $mail_name => $mail_value ) {
				$label = ucwords( str_replace( '_', ' ', $mail_name ) );
				?>
				<span class="contact-phone epl-info-item editable" data-key="phone">
					<span class="dashicons dashicons-phone epl-contact-icons"></span>
					<span class="epl-contact-value"><?php echo esc_attr( $label . ' - ' . $mail_value ); ?></span>
				</span>
				<?php
			}
			$email_html = ob_get_clean();
			return apply_filters( 'epl_contact_phone_html', $email_html );
		}
	}

	/**
	 * Returns Contact Category Label
	 *
	 * @return mixed
	 * @since 3.0.0
	 */
	public function get_category_label() {
		$cat = $this->get_meta( 'contact_category' );
		return get_category_label( $cat );
	}

	/**
	 * Returns Contact Category Name
	 *
	 * @return mixed
	 * @since 3.0.0
	 */
	public function get_category_name() {
		return $this->get_meta( 'contact_category' );
	}
}
