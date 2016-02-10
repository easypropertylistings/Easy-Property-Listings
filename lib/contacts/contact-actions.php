<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Processes a custom edit
 *
 * @since  2.4
 * @param  array $args The $_POST array being passeed
 * @return array $output Response messages
 */
function epl_edit_contact( $args ) {
	$contact_edit_role = apply_filters( 'epl_edit_contacts_role', 'manage_options' );

	if ( ! is_admin() || ! current_user_can( $contact_edit_role ) ) {
		wp_die( __( 'You do not have permission to edit this contact.', 'epl' ) );
	}

	if ( empty( $args ) ) {
		return;
	}

	$contact_info = $args['contactinfo'];
	$contact_id   = (int)$args['contactinfo']['id'];
	$nonce         = $args['_wpnonce'];

	if ( ! wp_verify_nonce( $nonce, 'edit-contact' ) ) {
		wp_die( __( 'Cheatin\' eh?!', 'epl' ) );
	}

	$contact = new EPL_Contact( $contact_id );
	if ( empty( $contact->ID ) ) {
		return false;
	}

	$defaults = array(
		'name'    => '',
		'email'   => '',
		'user_id' => 0
	);

	$contact_info = wp_parse_args( $contact_info, $defaults );

	if ( ! is_email( $contact_info['email'] ) ) {
		epl_set_error( 'epl-invalid-email', __( 'Please enter a valid email address.', 'epl' ) );
	}

	if ( epl_get_errors() ) {
		return;
	}

	// Sanitize the inputs
	$contact_data            = array();
	$contact_data['name']    = strip_tags( stripslashes( $contact_info['name'] ) );
	$contact_data['email']   = $contact_info['email'];

	$contact_data = apply_filters( 'epl_edit_contact_info', $contact_data, $contact_id );

	$contact_data = array_map( 'sanitize_text_field', $contact_data );

	do_action( 'epl_pre_edit_contact', $contact_id, $contact_data );

	$output         = array();

	if ( $contact->update( $contact_data ) ) {

		$output['success']       = true;
		$output['contact_info'] = $contact_data;

	} else {

		$output['success'] = false;

	}

	do_action( 'epl_post_edit_contact', $contact_id, $contact_data );

	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
		header( 'Content-Type: application/json' );
		echo json_encode( $output );
		wp_die();
	}

	return $output;

}
add_action( 'epl_edit-contact', 'epl_edit_contact', 10, 1 );

/**
 * Delete a contact
 *
 * @since  2.4
 * @param  array $args The $_POST array being passeed
 * @return int         Wether it was a successful deletion
 */
function epl_contact_delete( $args ) {

	if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
		wp_die( __( 'You do not have permission to delete this contact.', 'epl' ) );
	}

	if ( empty( $args ) ) {
		return;
	}

	$contact_id   = (int)$args['contact_id'];
	$confirm       = ! empty( $args['epl-contact-delete-confirm'] ) ? true : false;
	$nonce         = $args['_wpnonce'];

	if ( ! wp_verify_nonce( $nonce, 'delete-contact' ) ) {
		wp_die( __( 'Cheatin\' eh?!', 'epl' ) );
	}

	if ( ! $confirm ) {
		epl_set_error( 'contact-delete-no-confirm', __( 'Please confirm you want to delete this contact', 'epl' ) );
	}

	if ( epl_get_errors() ) {
		wp_redirect( admin_url( 'edit.php?page=epl-contacts&view=overview&id=' . $contact_id ) );
		exit;
	}

	$contact = new EPL_Contact( $contact_id );

	do_action( 'epl_pre_delete_contact', $contact_id, $confirm );

	$success = false;

	if ( $contact->ID > 0 ) {

		$listings_array = $contact->listing_ids;
		
		// delete contact from meta of interested listings
		foreach($listings_array as $listing_id) {
			$contact->remove_listing($listing_id);
		}
			

		$success        = $contact->delete( $contact->id );

		if ( $success ) {
			
			$redirect = admin_url( 'admin.php?page=epl-contacts&epl-message=contact-deleted' );

		} else {

			epl_set_error( 'epl-contact-delete-failed', __( 'Error deleting contact', 'epl' ) );
			$redirect = admin_url( 'admin.php?page=epl-contacts&view=delete&id=' . $contact_id );

		}

	} else {

		epl_set_error( 'epl-contact-delete-invalid-id', __( 'Invalid Contact ID', 'epl' ) );
		$redirect = admin_url( 'admin.php?page=epl-contacts' );

	}

	wp_redirect( $redirect );
	exit;

}
add_action( 'epl_delete-contact', 'epl_contact_delete', 10, 1 );


/**
 * Save a customer note being added
 *
 * @since  2.4
 * @param  array $args The $_POST array being passeed
 * @return object         the comment object
 */
function epl_contact_save_note( $args ) {

	$contact_view_role = apply_filters( 'epl_view_contacts_role', 'manage_options' );

	if ( ! is_admin() || ! current_user_can( $contact_view_role ) ) {
		wp_die( __( 'You do not have permission to edit this customer.', 'epl' ) );
	}

	if ( empty( $args ) ) {
		return;
	}

	$contact_note 		= trim( sanitize_text_field( $args['contact_note'] ) );
	$listing_id 	= trim( sanitize_text_field( $args['listing_id'] ) );
	$note_notify 	= trim( sanitize_text_field( $args['note_notify'] ) );
	$note_type		= 'epl-admin-note';
	
	$contact_id   = (int)$args['contact_id'];
	$nonce         = $args['add_contact_note_nonce'];

	if ( ! wp_verify_nonce( $nonce, 'add-contact-note' ) ) {
		wp_die( __( 'Cheatin\' eh?!', 'epl' ) );
	}

	if ( empty( $contact_note ) ) {
		epl_set_error( 'empty-customer-note', __( 'A note is required', 'epl' ) );
	}

	if ( epl_get_errors() ) {
		epl_set_error();
		return;
	}

	do_action( 'epl_pre_insert_contact_note', $contact_id, $new_note, $listing_id, $note_type );
	
	$contact = new EPL_Contact( $contact_id );
	$note_object = $contact->add_note( $contact_note,$note_type,$listing_id );

	

	if ( ! empty( $note_object ) && ! empty( $contact->id ) ) {

		ob_start();
		?>
		<div class="contact-note-wrapper dashboard-comment-wrap comment-item epl-admin-note">
			<span class="note-content-meta">
				<span class="note-for-listing">
					<?php
						echo isset($note_object->comment_post_ID) ? get_the_title($note_object->comment_post_ID) : '';
					 ?>
				 </span>
				 <span class="note-time">
					<?php 
						echo date_i18n( get_option( 'date_format' ), strtotime( $note_object->comment_date ) );
					 ?>
				 </span>
			</span>
			<span class="note-content-wrap">
				<?php echo stripslashes( $note_object->comment_content ); ?>
			</span>
		</div>
		<?php
		$output = ob_get_contents();
		ob_end_clean();

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			echo $output;
			exit;
		}

		return $note_object;

	}

	return false;

}
add_action( 'epl_add-contact-note', 'epl_contact_save_note', 10, 1 );


/**
 * Processes a custom edit
 *
 * @since  2.4
 * @param  array $args The $_POST array being passeed
 * @return array $output Response messages
 */
function epl_meta_contact( $args ) {

	$contact_edit_role = apply_filters( 'epl_edit_contacts_role', 'manage_options' );

	if ( ! is_admin() || ! current_user_can( $contact_edit_role ) ) {
		wp_die( __( 'You do not have permission to edit this contact.', 'epl' ) );
	}

	if ( empty( $args ) ) {
		return;
	}
	
	$nonce         = $args['_wpnonce'];
	if ( ! wp_verify_nonce( $nonce, 'meta-contact' ) ) {
		wp_die( __( 'Cheatin\' eh?!', 'epl' ) );
	}

	$contact_id   = (int)$args['contact_id'];
	$contact = new EPL_Contact( $contact_id );
	if ( empty( $contact->ID ) ) {
		return false;
	}
	
	$not_meta_fields = array('epl_form_builder_form_submit','contact_id','_wpnonce','epl_action');
	
	foreach($args as $key	=>	$value) {
		if( !in_array($key,$not_meta_fields) )
			$contact->update_meta($key,$value);
	}

	$redirect = admin_url( 'admin.php?page=epl-contacts&view=meta&id=' . $contact_id );
	wp_redirect( $redirect );
	exit;

}
add_action( 'epl_meta-contact', 'epl_meta_contact', 10, 1 );


/**
 * create a new contact from backend
 *
 * @since  2.4
 * @param  array $args The $_POST array being passeed
 * @return array $output Response messages
 */
function epl_new_contact( $args ) {

	$contact_create_role = apply_filters( 'epl_create_contacts_role', 'manage_options' );

	if ( ! is_admin() || ! current_user_can( $contact_create_role ) ) {
		wp_die( __( 'You do not have permission to edit this contact.', 'epl' ) );
	}

	if ( empty( $args ) ) {
		return;
	}
	
	$nonce         = $args['_wpnonce'];
	if ( ! wp_verify_nonce( $nonce, 'new-contact' ) ) {
		wp_die( __( 'Cheatin\' uhh?!', 'epl' ) );
	}

	$contact_id   = (int)$args['contact_id'];
	$contact = new EPL_Contact( $contact_id );
	if ( empty( $contact->ID ) ) {
		return false;
	}
	
	$contact->update($args);
	
	$redirect = admin_url( 'admin.php?page=epl-contacts&view=meta&id=' . $contact_id );
	wp_redirect( $redirect );
	exit;

}
add_action( 'epl_new-contact', 'epl_new_contact', 10, 1 );
