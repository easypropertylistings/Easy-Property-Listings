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
function epl_edit_lead( $args ) {
	$lead_edit_role = apply_filters( 'epl_edit_leads_role', 'manage_options' );

	if ( ! is_admin() || ! current_user_can( $lead_edit_role ) ) {
		wp_die( __( 'You do not have permission to edit this lead.', 'epl' ) );
	}

	if ( empty( $args ) ) {
		return;
	}

	$lead_info = $args['leadinfo'];
	$lead_id   = (int)$args['leadinfo']['id'];
	$nonce         = $args['_wpnonce'];

	if ( ! wp_verify_nonce( $nonce, 'edit-lead' ) ) {
		wp_die( __( 'Cheatin\' eh?!', 'epl' ) );
	}

	$lead = new EPL_Lead( $lead_id );
	if ( empty( $lead->id ) ) {
		return false;
	}

	$defaults = array(
		'name'    => '',
		'email'   => '',
		'user_id' => 0
	);

	$lead_info = wp_parse_args( $lead_info, $defaults );

	if ( ! is_email( $lead_info['email'] ) ) {
		epl_set_error( 'epl-invalid-email', __( 'Please enter a valid email address.', 'epl' ) );
	}

	if ( (int) $lead_info['user_id'] != (int) $lead->user_id ) {

		// Make sure we don't already have this user attached to a lead
		if ( ! empty( $lead_info['user_id'] ) && false !== EPL()->leads->get_lead_by( 'user_id', $lead_info['user_id'] ) ) {
			epl_set_error( 'epl-invalid-lead-user_id', sprintf( __( 'The User ID %d is already associated with a different lead.', 'epl' ), $lead_info['user_id'] ) );
		}

		// Make sure it's actually a user
		$user = get_user_by( 'id', $lead_info['user_id'] );
		if ( ! empty( $lead_info['user_id'] ) && false === $user ) {
			epl_set_error( 'epl-invalid-user_id', sprintf( __( 'The User ID %d does not exist. Please assign an existing user.', 'epl' ), $lead_info['user_id'] ) );
		}

	}

	// Record this for later
	$previous_user_id  = $lead->user_id;

	if ( epl_get_errors() ) {
		return;
	}

	if ( intval( $lead_info['user_id'] ) > 0 ) {

		// save extra meta fields for this user

	}

	// Sanitize the inputs
	$lead_data            = array();
	$lead_data['name']    = strip_tags( stripslashes( $lead_info['name'] ) );
	$lead_data['email']   = $lead_info['email'];
	$lead_data['user_id'] = $lead_info['user_id'];

	$lead_data = apply_filters( 'epl_edit_lead_info', $lead_data, $lead_id );

	$lead_data = array_map( 'sanitize_text_field', $lead_data );

	do_action( 'epl_pre_edit_lead', $lead_id, $lead_data );

	$output         = array();
	$previous_email = $lead->email;

	if ( $lead->update( $lead_data ) ) {

		$output['success']       = true;
		$output['lead_info'] = $lead_data;

	} else {

		$output['success'] = false;

	}

	do_action( 'epl_post_edit_lead', $lead_id, $lead_data );

	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
		header( 'Content-Type: application/json' );
		echo json_encode( $output );
		wp_die();
	}

	return $output;

}
add_action( 'epl_edit-lead', 'epl_edit_lead', 10, 1 );

/**
 * Delete a lead
 *
 * @since  2.4
 * @param  array $args The $_POST array being passeed
 * @return int         Wether it was a successful deletion
 */
function epl_lead_delete( $args ) {

	if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
		wp_die( __( 'You do not have permission to delete this lead.', 'epl' ) );
	}

	if ( empty( $args ) ) {
		return;
	}

	$lead_id   = (int)$args['lead_id'];
	$confirm       = ! empty( $args['epl-lead-delete-confirm'] ) ? true : false;
	$nonce         = $args['_wpnonce'];

	if ( ! wp_verify_nonce( $nonce, 'delete-lead' ) ) {
		wp_die( __( 'Cheatin\' eh?!', 'epl' ) );
	}

	if ( ! $confirm ) {
		epl_set_error( 'lead-delete-no-confirm', __( 'Please confirm you want to delete this lead', 'epl' ) );
	}

	if ( epl_get_errors() ) {
		wp_redirect( admin_url( 'edit.php?page=epl-leads&view=overview&id=' . $lead_id ) );
		exit;
	}

	$lead = new EPL_Lead( $lead_id );

	do_action( 'epl_pre_delete_lead', $lead_id, $confirm );

	$success = false;

	if ( $lead->id > 0 ) {

		$listings_array = explode( ',', $lead->listing_ids );
		
		// delete lead from meta of interested listings
		foreach($listings_array as $listing_id) {
			$lead->remove_listing($listing_id);
		}
			

		$success        = EPL()->leads->delete( $lead->id );

		if ( $success ) {
			
			$redirect = admin_url( 'admin.php?page=epl-leads&epl-message=lead-deleted' );

		} else {

			epl_set_error( 'epl-lead-delete-failed', __( 'Error deleting lead', 'epl' ) );
			$redirect = admin_url( 'admin.php?page=epl-leads&view=delete&id=' . $lead_id );

		}

	} else {

		epl_set_error( 'epl-lead-delete-invalid-id', __( 'Invalid Lead ID', 'epl' ) );
		$redirect = admin_url( 'admin.php?page=epl-leads' );

	}

	wp_redirect( $redirect );
	exit;

}
add_action( 'epl_delete-lead', 'epl_lead_delete', 10, 1 );

/**
 * Disconnect a user ID from a lead
 *
 * @since  2.4
 * @param  array $args Array of arguements
 * @return bool        If the disconnect was sucessful
 */
function epl_disconnect_lead_user_id( $args ) {

	if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
		wp_die( __( 'You do not have permission to edit this lead.', 'epl' ) );
	}

	if ( empty( $args ) ) {
		return;
	}

	$lead_id   = (int)$args['lead_id'];
	$nonce         = $args['_wpnonce'];

	if ( ! wp_verify_nonce( $nonce, 'edit-lead' ) ) {
		wp_die( __( 'Cheatin\' eh?!', 'epl' ) );
	}

	$lead = new EPL_Lead( $lead_id );
	if ( empty( $lead->id ) ) {
		return false;
	}

	do_action( 'epl_pre_lead_disconnect_user_id', $lead_id, $lead->user_id );

	$lead_args = array( 'user_id' => 0 );

	if ( $lead->update( $lead_args ) ) {

		$output['success'] = true;

	} else {

		$output['success'] = false;
		epl_set_error( 'epl-disconnect-user-fail', __( 'Failed to disconnect user from lead', 'epl' ) );
	}

	do_action( 'epl_post_lead_disconnect_user_id', $lead_id );

	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
		header( 'Content-Type: application/json' );
		echo json_encode( $output );
		wp_die();
	}

	return $output;

}
add_action( 'epl_disconnect-userid', 'epl_disconnect_lead_user_id', 10, 1 );

/**
 * Searches for users via ajax and returns a list of results
 *
 * @since 2.0
 * @return void
 */
function epl_ajax_search_users() {

	if( current_user_can( 'manage_options' ) ) {

		$search_query = trim( $_POST['user_name'] );
		$exclude      = trim( $_POST['exclude'] );

		$get_users_args = array(
			'number' => 9999,
			'search' => $search_query . '*'
		);

		if ( ! empty( $exclude ) ) {
			$exclude_array = explode( ',', $exclude );
			$get_users_args['exclude'] = $exclude_array;
		}

		$get_users_args = apply_filters( 'epl_search_users_args', $get_users_args );

		$found_users = apply_filters( 'epl_ajax_found_users', get_users( $get_users_args ), $search_query );

		$user_list = '<ul>';
		if( $found_users ) {
			foreach( $found_users as $user ) {
				$user_list .= '<li><a href="#" data-userid="' . esc_attr( $user->ID ) . '" data-login="' . esc_attr( $user->user_login ) . '">' . esc_html( $user->user_login ) . '</a></li>';
			}
		} else {
			$user_list .= '<li>' . __( 'No users found', 'epl' ) . '</li>';
		}
		$user_list .= '</ul>';

		echo json_encode( array( 'results' => $user_list ) );

	}
	die();
}
add_action( 'wp_ajax_epl_search_users', 'epl_ajax_search_users' );


/**
 * Save a customer note being added
 *
 * @since  2.4
 * @param  array $args The $_POST array being passeed
 * @return object         the comment object
 */
function epl_lead_save_note( $args ) {

	$lead_view_role = apply_filters( 'epl_view_leads_role', 'manage_options' );

	if ( ! is_admin() || ! current_user_can( $lead_view_role ) ) {
		wp_die( __( 'You do not have permission to edit this customer.', 'epl' ) );
	}

	if ( empty( $args ) ) {
		return;
	}

	$lead_note 		= trim( sanitize_text_field( $args['lead_note'] ) );
	$listing_id 	= trim( sanitize_text_field( $args['listing_id'] ) );
	$note_notify 	= trim( sanitize_text_field( $args['note_notify'] ) );
	$note_type		= 'epl-admin-note';
	
	$lead_id   = (int)$args['lead_id'];
	$nonce         = $args['add_lead_note_nonce'];

	if ( ! wp_verify_nonce( $nonce, 'add-lead-note' ) ) {
		wp_die( __( 'Cheatin\' eh?!', 'epl' ) );
	}

	if ( empty( $lead_note ) ) {
		epl_set_error( 'empty-customer-note', __( 'A note is required', 'epl' ) );
	}

	if ( epl_get_errors() ) {
		epl_set_error();
		return;
	}

	do_action( 'epl_pre_insert_lead_note', $lead_id, $new_note, $listing_id, $note_type );
	
	$lead = new EPL_Lead( $lead_id );
	$note_object = $lead->add_note( $lead_note,$note_type,$listing_id );

	

	if ( ! empty( $note_object ) && ! empty( $lead->id ) ) {

		ob_start();
		?>
		<div class="lead-note-wrapper dashboard-comment-wrap comment-item epl-admin-note">
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
add_action( 'epl_add-lead-note', 'epl_lead_save_note', 10, 1 );
