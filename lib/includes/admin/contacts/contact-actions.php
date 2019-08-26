<?php
/**
 * Contact Actions
 *
 * @package     EPL
 * @subpackage  Contacts/Actions
 * @copyright   Copyright (c) 2019, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// phpcs:disable WordPress.DB.SlowDBQuery

/**
 * Processes a custom edit
 *
 * @since  3.0
 * @param  array $args The $_POST array being passeed.
 * @return array $output Response messages
 */
function epl_edit_contact( $args ) {
	if ( ! is_admin() || ! epl_contact_access() ) {
		wp_die( esc_html__( 'You do not have permission to edit this contact.', 'easy-property-listings' ) );
	}

	if ( empty( $args ) ) {
		return;
	}

	$contact_info = $args['contactinfo'];
	$contact_id   = (int) $args['contactinfo']['id'];
	$nonce        = $args['_wpnonce'];

	if ( ! wp_verify_nonce( $nonce, 'edit-contact' ) ) {
		wp_die( esc_html__( 'Cheatin\' eh?!', 'easy-property-listings' ) );
	}

	$contact = new EPL_Contact( $contact_id );
	if ( empty( $contact->ID ) ) {
		return false;
	}

	$defaults = array(
		'name'    => '',
		'email'   => '',
		'user_id' => 0,
	);

	$contact_info = wp_parse_args( $contact_info, $defaults );

	if ( ! is_email( $contact_info['email'] ) ) {
		epl_set_error( 'epl-invalid-email', esc_html__( 'Please enter a valid email address.', 'easy-property-listings' ) );
	}

	if ( epl_get_errors() ) {
		return;
	}

	// Sanitize the inputs.
	$contact_data          = array();
	$contact_data['name']  = wp_strip_all_tags( stripslashes( $contact_info['name'] ) );
	$contact_data['email'] = $contact_info['email'];

	$contact_data = apply_filters( 'epl_edit_contact_info', $contact_data, $contact_id );

	$contact_data = array_map( 'sanitize_text_field', $contact_data );

	do_action( 'epl_pre_edit_contact', $contact_id, $contact_data );

	$output = array();

	if ( $contact->update( $contact_data ) ) {

		$output['success']      = true;
		$output['contact_info'] = $contact_data;

	} else {

		$output['success'] = false;

	}

	do_action( 'epl_post_edit_contact', $contact_id, $contact_data );

	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
		header( 'Content-Type: application/json' );
		echo wp_json_encode( $output );
		wp_die();
	}

	return $output;

}
add_action( 'epl_edit-contact', 'epl_edit_contact', 10, 1 );

/**
 * Delete a contact
 *
 * @param array $args The $_POST array being passeed.
 *
 * @return void Wether it was a successful deletion
 * @since  3.0
 */
function epl_contact_delete( $args ) {

	if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have permission to delete this contact.', 'easy-property-listings' ) );
	}

	if ( empty( $args ) ) {
		return;
	}

	$contact_id = (int) $args['contact_id'];
	$confirm    = ! empty( $args['epl-contact-delete-confirm'] ) ? true : false;
	$nonce      = $args['_wpnonce'];

	if ( ! wp_verify_nonce( $nonce, 'delete-contact' ) ) {
		wp_die( esc_html__( 'Cheatin\' eh?!', 'easy-property-listings' ) );
	}

	if ( ! $confirm ) {
		epl_set_error( 'contact-delete-no-confirm', esc_html__( 'Please confirm you want to delete this contact', 'easy-property-listings' ) );
	}

	if ( epl_get_errors() ) {
		wp_safe_redirect( admin_url( 'edit.php?page=epl-contacts&view=overview&id=' . $contact_id ) );
		exit;
	}

	$contact = new EPL_Contact( $contact_id );

	do_action( 'epl_pre_delete_contact', $contact_id, $confirm );

	$success = false;

	if ( $contact->ID > 0 ) {

		$listings_array = $contact->listing_ids;

		// Delete contact from meta of interested listings.
		if ( ! empty( $listings_array ) ) {
			foreach ( $listings_array as $listing_id ) {
				$contact->remove_listing( $listing_id );
			}
		}

		$success = $contact->delete();

		if ( $success ) {

			$redirect = admin_url( 'admin.php?page=epl-contacts&epl-message=contact-deleted' );

		} else {

			epl_set_error( 'epl-contact-delete-failed', esc_html__( 'Error deleting contact', 'easy-property-listings' ) );
			$redirect = admin_url( 'admin.php?page=epl-contacts&view=delete&id=' . $contact_id );

		}
	} else {

		epl_set_error( 'epl-contact-delete-invalid-id', esc_html__( 'Invalid Contact ID', 'easy-property-listings' ) );
		$redirect = admin_url( 'admin.php?page=epl-contacts' );

	}

	wp_safe_redirect( $redirect );
	exit;

}
add_action( 'epl_delete-contact', 'epl_contact_delete', 10, 1 );

/**
 * Save a customer note being added
 *
 * @since  3.0
 * @param  array $args The $_POST array being passeed.
 * @return object         the comment object
 */
function epl_contact_save_note( $args ) {

	if ( ! is_admin() || ! epl_contact_access() ) {
		wp_die( esc_html__( 'You do not have permission to save note.', 'easy-property-listings' ) );
	}

	if ( empty( $args ) ) {
		return;
	}

	$contact_note = trim( sanitize_text_field( $args['contact_note'] ) );
	$listing_id   = trim( sanitize_text_field( $args['listing_id'] ) );
	$note_type    = trim( sanitize_text_field( $args['note_type'] ) );

	$contact_id = (int) $args['contact_id'];
	$nonce      = $args['add_contact_note_nonce'];

	if ( ! wp_verify_nonce( $nonce, 'add_contact_note_nonce' ) ) {
		wp_die( esc_html__( 'Cheatin\' eh?!', 'easy-property-listings' ) );
	}

	if ( empty( $contact_note ) ) {
		epl_set_error( 'empty-customer-note', esc_html__( 'A note is required', 'easy-property-listings' ) );
	}

	if ( epl_get_errors() ) {
		epl_set_error();
		return;
	}

	do_action( 'epl_pre_insert_contact_note', $contact_id, $note_type, $listing_id, $note_type );

	$contact     = new EPL_Contact( $contact_id );
	$note_object = $contact->add_note( $contact_note, $note_type, $listing_id );

	if ( ! empty( $note_object ) && ! empty( $contact->id ) ) {

		ob_start();
		?>
		<tr data-activity-id="<?php echo esc_attr( $note_object->comment_ID ); ?>" id="activity-id-<?php echo esc_attr( $note_object->comment_ID ); ?>" class="epl-contact-activity-row epl-contact-activity-<?php echo esc_attr( $note_object->comment_type ); ?>" >
			<td><?php echo esc_attr( $contact->get_activity_type( $note_object->comment_type ) ); ?></td>
			<td>
				<?php
				if ( $note_object->comment_post_ID > 0 ) {
					echo '<div class="epl-contact-inline-lis-details">';
					echo '<span class="epl-contact-inline-lis-img">';
					echo get_the_post_thumbnail( $note_object->comment_post_ID, array( 50, 50 ) );
					echo '</span>';
					echo '<span class="epl-contact-inline-lis-title">';
					echo '<a href="' . esc_url( get_permalink( $note_object->comment_post_ID ) ) . '">' . esc_attr( get_the_title( $note_object->comment_post_ID ) ) . '</a>';
					echo '</span>';
					echo '</div>';
				}
					echo wp_kses_post( $note_object->comment_content );
				?>
			</td>
			<td>
				<?php
					echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $note_object->comment_date ) ) );
				?>
			</td>
		</tr>
		<?php
		$output = ob_get_contents();
		ob_end_clean();

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			echo wp_kses_post( $output );
			exit;
		}

		return $note_object;

	}

	return false;

}
add_action( 'epl_add-contact-note', 'epl_contact_save_note', 10, 1 );

/**
 * Save a contact listing being added
 *
 * @since  3.0
 * @param  array $args The $_POST array being passeed.
 * @return object
 */
function epl_contact_save_listing( $args ) {
	$contact_add_listing_role = apply_filters( 'epl_add_contacts_listing', 'manage_options' );

	if ( ! is_admin() || ! current_user_can( $contact_add_listing_role ) ) {
		wp_die( esc_html__( 'You do not have permission to add listing.', 'easy-property-listings' ) );
	}

	if ( empty( $args ) ) {
		return;
	}

	$post_fields = array( 'post_title', 'post_type' );

	$ignore_fields = array( 'add_contact_listing_nonce', 'epl_actiion', 'contact_id' );

	$nonce = $args['add_contact_listing_nonce'];

	if ( ! wp_verify_nonce( $nonce, 'add_contact_listing_nonce' ) ) {
		wp_die( esc_html__( 'Cheatin\' eh?!', 'easy-property-listings' ) );
	}
	if ( epl_get_errors() ) {
		epl_print_error();
		return;
	}

	do_action( 'epl_pre_insert_contact_listing', $args );
	if ( $args['property_owner'] > 0 ) {
		$insert_post_array = array( 'post_status' => 'publish' );
		$insert_meta_array = array();
		foreach ( $args as $arg_key   => $arg_value ) {
			if ( in_array( $arg_key, $post_fields, true ) ) {
				$insert_post_array[ $arg_key ] = $arg_value;
			} elseif ( ! in_array( $arg_key, $ignore_fields, true ) ) {
				$insert_meta_array[ $arg_key ] = $arg_value;
			}
		}
		$insert_id = wp_insert_post( $insert_post_array );
		if ( $insert_id ) {
			foreach ( $insert_meta_array as $meta_key => $meta_value ) {
				update_post_meta( $insert_id, $meta_key, $meta_value );
			}
		} else {
			return false;
		}
	}
	$inserted_lisitng = get_post( $insert_id );
	if ( ! empty( $inserted_lisitng ) ) {

		ob_start();
		?>
		<tr data-activity-id="<?php echo esc_attr( $inserted_lisitng->ID ); ?>" id="activity-id-<?php echo esc_attr( $inserted_lisitng->ID ); ?>" class="epl-contact-activity-row " >
			<td><?php echo esc_attr( $inserted_lisitng->post_type ); ?></td>
			<td>
				<?php
					echo '<a href="' . esc_url( get_edit_post_link( $inserted_lisitng->ID ) ) . '">' . esc_attr( $inserted_lisitng->post_title ) . '</a>';
				?>
			</td>
			<td>
				<?php echo esc_attr( get_post_meta( $inserted_lisitng->ID, 'property_status', true ) ); ?>
			</td>
		</tr>
		<?php
		$output = ob_get_contents();
		ob_end_clean();

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			echo wp_kses_post( $output );
			exit;
		}

		return $inserted_lisitng;

	}

	return false;

}
add_action( 'epl_add-contact-listing', 'epl_contact_save_listing', 10, 1 );

/**
 * Add an existing listing to a contact
 *
 * @since  3.0
 * @param  array $args The $_POST array being passeed.
 * @return object
 */
function epl_contact_assign_existing_listing( $args ) {

	if ( ! is_admin() || ! epl_contact_access() ) {
		wp_die( esc_html__( 'You do not have permission to assign listings.', 'easy-property-listings' ) );
	}

	if ( empty( $args ) ) {
		return;
	}

	do_action( 'epl_pre_assign_contact_listing', $args );
	if ( $args['contact_id'] > 0 && $args['id'] > 0 ) {
		update_post_meta( $args['id'], 'property_owner', $args['contact_id'] );

	} else {
		wp_die();
	}
	$inserted_lisitng = get_post( $args['id'] );
	if ( ! empty( $inserted_lisitng ) ) {

		ob_start();
		?>
		<tr data-activity-id="<?php echo esc_attr( $inserted_lisitng->ID ); ?>" id="activity-id-<?php echo esc_attr( $inserted_lisitng->ID ); ?>" class="epl-contact-activity-row " >
			<td><?php echo esc_attr( $inserted_lisitng->post_type ); ?></td>
			<td>
				<?php
				echo '<a href="' . esc_url( get_edit_post_link( $inserted_lisitng->ID ) ) . '">' . esc_attr( $inserted_lisitng->post_title ) . '</a>';
				?>
			</td>
			<td>
				<?php echo esc_attr( get_post_meta( $inserted_lisitng->ID, 'property_status', true ) ); ?>
			</td>
		</tr>
		<?php
		$output = ob_get_contents();
		ob_end_clean();

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			echo wp_kses_post( $output );
			exit;
		}

		return $inserted_lisitng;

	}

	return false;

}
add_action( 'epl_add-existing-contact-listing', 'epl_contact_assign_existing_listing', 10, 1 );

/**
 * Processes a custom edit
 *
 * @since  3.0
 * @param array $args The $_POST array being passeed.
 * @return bool|void $output Response messages
 */
function epl_meta_contact( $args ) {

	if ( ! is_admin() || ! epl_contact_access() ) {
		wp_die( esc_html__( 'You do not have permission to update this contact.', 'easy-property-listings' ) );
	}

	if ( empty( $args ) ) {
		return;
	}

	$nonce = $args['_wpnonce'];
	if ( ! wp_verify_nonce( $nonce, 'meta-contact' ) ) {
		wp_die( esc_html__( 'Cheatin\' eh?!', 'easy-property-listings' ) );
	}

	$contact_id = (int) $args['contact_id'];
	$contact    = new EPL_Contact( $contact_id );
	if ( empty( $contact->ID ) ) {
		return false;
	}

	$not_meta_fields = array( 'epl_form_builder_form_submit', 'contact_id', '_wpnonce', 'epl_action' );

	$post_fields = array( 'post_title', 'post_content', 'ID', 'post_author' );

	$field_updates = array( 'ID' => $contact_id );
	foreach ( $args as $key   => $value ) {
		if ( ! in_array( $key, $not_meta_fields, true ) ) {

			// Check if post fields.
			if ( in_array( $key, $post_fields, true ) ) {
				$field_updates[ $key ] = $value;
			} else {
				if ( is_array( $value ) ) {
					$value = array_map( 'sanitize_text_field', $value );
				} else {
					$value = sanitize_text_field( $value );
				}
				$contact->update_meta( $key, $value );
			}
		}
	}
	wp_update_post( $field_updates );

	$redirect = admin_url( 'admin.php?page=epl-contacts&view=meta&id=' . $contact_id );
	wp_safe_redirect( $redirect );
	exit;

}
add_action( 'epl_meta-contact', 'epl_meta_contact', 10, 1 );

/**
 * Create a new contact from backend
 *
 * @since  3.0
 * @param array $args The $_POST array being passeed.
 * @return bool|void $output Response messages
 */
function epl_new_contact( $args ) {

	$args = array_map( 'trim', $args );

	if ( ! is_admin() || ! epl_contact_access() ) {
		wp_die( esc_html__( 'You do not have permission to create contacts.', 'easy-property-listings' ) );
	}

	if ( empty( $args ) ) {
		return;
	}

	$nonce = $args['_wpnonce'];
	if ( ! wp_verify_nonce( $nonce, 'new-contact' ) ) {
		wp_die( esc_html__( 'Cheatin\' uhh?!', 'easy-property-listings' ) );
	}

	$contact_id = (int) $args['contact_id'];
	$contact    = new EPL_Contact( $contact_id );
	if ( empty( $contact->ID ) ) {
		return false;
	}

	if ( empty( $args['email'] ) || ! is_email( $args['email'] ) ) {
		wp_die( esc_html__( 'Please provide valid email address', 'easy-property-listings' ) );
	}

	if ( $contact->contact_exists( $args['email'] ) ) {
		wp_die( esc_html__( 'A contact with this email already exists !', 'easy-property-listings' ) );
	}

	if ( empty( $args['title'] ) && ( ! empty( $args['first_name'] ) || ! empty( $args['last_name'] ) ) ) {
		$args['title'] = $args['first_name'] . ' ' . $args['last_name'];
	}

	if ( empty( $args['title'] ) && ( ! empty( $args['email'] ) ) ) {
		$args['title'] = $args['email'];
	}

	$contact->update(
		array(
			'name'  => $args['title'],
			'email' => $args['email'],
		)
	);
	$contact->update_meta( 'contact_first_name', $args['first_name'] );
	$contact->update_meta( 'contact_last_name', $args['last_name'] );
	$contact->update_meta( 'contact_phones', array( 'phone' => $args['phone'] ) );
	$contact->update_meta( 'contact_category', 'new' );

	$redirect = admin_url( 'admin.php?page=epl-contacts&view=meta&id=' . $contact_id );
	wp_safe_redirect( $redirect );
	exit;

}
add_action( 'epl_new-contact', 'epl_new_contact', 10, 1 );

/**
 * Update contact category
 *
 * @since 3.0
 */
function epl_contact_category_update() {

	check_ajax_referer( 'epl_ajax_nonce', '_epl_nonce' );

	if ( ! empty( $_POST['contact_id'] ) && (int) $_POST['contact_id'] > 0 && ! empty( $_POST['type'] ) ) {

		$contact = new EPL_Contact( sanitize_text_field( wp_unslash( $_POST['contact_id'] ) ) );
		$status  = $contact->update_meta( 'contact_category', trim( sanitize_text_field( wp_unslash( $_POST['type'] ) ) ) );
		echo esc_attr( $status );
		wp_die();
	}
}
add_action( 'wp_ajax_epl_contact_category_update', 'epl_contact_category_update' );

/**
 * Add/Update contact tags
 *
 * @since 3.0
 */
function epl_contact_tag_add() {

	check_ajax_referer( 'epl_ajax_nonce', '_epl_nonce' );

	if ( ( ! empty( $_POST['term_id'] ) ) ) {

		$term_id = sanitize_text_field( wp_unslash( $_POST['term_id'] ) );

		if ( is_numeric( $term_id ) ) {
			$term_id = (int) $term_id;
		}

		// update tag for a contact.
		if ( isset( $_POST['contact_id'] ) && (int) $_POST['contact_id'] > 0 ) {
			$terms = wp_set_object_terms( absint( wp_unslash( $_POST['contact_id'] ) ), $term_id, 'epl_contact_tag', true );
			wp_die( esc_attr( current( $terms ) ) );
		} else {
			// update the tag.
			if ( ! empty( $_POST['bg'] ) ) {
				epl_update_contact_tag_bgcolor( $term_id, sanitize_text_field( wp_unslash( $_POST['bg'] ) ) );
			}

			if ( ! empty( $_POST['label'] ) ) {
				wp_update_term( $term_id, 'epl_contact_tag', array( 'name' => sanitize_text_field( wp_unslash( $_POST['label'] ) ) ) );
			}

			if ( ! empty( $_POST['delete'] ) ) {
				wp_delete_term( $term_id, 'epl_contact_tag' );
			}

			wp_die( 1 );

		}
	}
}
add_action( 'wp_ajax_contact_tags_update', 'epl_contact_tag_add' );

/**
 * Delete contact tags
 *
 * @since 3.0
 */
function epl_contact_tag_remove() {

	check_ajax_referer( 'epl_ajax_nonce', '_epl_nonce' );

	if ( ! empty( $_POST['term_id'] ) && ! empty( $_POST['contact_id'] ) && (int) $_POST['contact_id'] > 0 && (int) $_POST['term_id'] > 0 ) {

		wp_remove_object_terms( absint( wp_unslash( $_POST['contact_id'] ) ), absint( wp_unslash( $_POST['term_id'] ) ), 'epl_contact_tag' );
		wp_die( 1 );
	}
}
add_action( 'wp_ajax_epl_contact_tag_remove', 'epl_contact_tag_remove' );

/**
 * Contact Action Menus, Renders contact action menus
 *
 * @param array $contact contact object.
 * @since 3.0
 */
function epl_contact_action_menus( $contact ) {
	?>
	<div class="contact-action-menu epl-clearfix">
		<ul class="epl_contact_quick_actions">
			<li>
				<a  class="contact-action-category" href="#" title="<?php esc_html_e( 'Contact Category', 'easy-property-listings' ); ?>">
					<span class="dashicons dashicons-flag"></span>
					<b class="caret"></b>
				</a>
				<ul class="epl-contact_category_suggestions">
					<?php

						$cats = epl_get_contact_categories();

					foreach ( $cats as $cat_key   => $cat_label ) :
						echo '<li> <a href="#" data-key="' . esc_attr( $cat_key ) . '" data-label="' . esc_attr( $cat_label ) . '">' . esc_attr( $cat_label ) . '</a></li>';
						endforeach;
					?>
				</ul>
			</li>

			<li>
				<a class="contact-action-tag" href="#" title="<?php esc_html_e( 'Contact Tags', 'easy-property-listings' ); ?>">
					<span class="dashicons dashicons-tag"></span>
					<b class="caret"></b>
				</a>
				<div class="epl-contact-tags-find">
					<input type="text" id="contact-tag-hint" value=""/>
					<ul class="epl-contact_tags_suggestions">
						<?php
							$contact_tags = get_terms( 'epl_contact_tag', array( 'hide_empty' => false ) );
						if ( ! empty( $contact_tags ) ) {

							foreach ( $contact_tags as $contact_tag ) {
								$bgcolor = epl_get_contact_tag_bgcolor( $contact_tag->term_id );

								echo '<li data-bg="' . esc_attr( $bgcolor ) . '" style="background:' . esc_attr( $bgcolor ) . ';color:#fff" data-id="' . esc_attr( $contact_tag->term_id ) . '" >' . esc_attr( $contact_tag->name ) . '</li>';
							}
						}
						?>
					</ul>
				</div>
			</li>
			<?php do_action( 'post_contact_custom_quick_edit_options', $contact ); ?>
		</ul>

		<div class="epl_contact_pagination">
			<?php if ( epl_get_prev_contact_link( $contact->id ) ) : ?>
				<a class="epl_contact_pagination_prev " href="<?php echo esc_url( epl_get_prev_contact_link( $contact->id ) ); ?>">
					<span class="dashicons dashicons-arrow-left"></span>
				</a>
			<?php endif; ?>

			<?php if ( epl_get_next_contact_link( $contact->id ) ) : ?>
				<a class="epl_contact_pagination_next " href="<?php echo esc_url( epl_get_next_contact_link( $contact->id ) ); ?>">
					<span class="dashicons dashicons-arrow-right">
					</span>
				</a>
			<?php endif; ?>
		</div>
	</div>
	<?php
}
add_action( 'epl_contact_action_menus', 'epl_contact_action_menus' );

/**
 * Contact Entry Header, Renders contact header
 *
 * @param array $contact contact Object.
 * @since 3.0
 */
function epl_contact_entry_header( $contact ) {
	?>
	<div class="epl-contact-entry-header">
		<h1 class="epl-contact-title">
			<?php
				echo esc_attr( $contact->heading );
			?>
		</h1>
		<span>
			<?php
				echo esc_attr( $contact->get_category_label() );
			?>
		</span>
	</div>
	<?php
}
add_action( 'epl_contact_entry_header', 'epl_contact_entry_header' );

/**
 * Contact Header Editable
 *
 * @param array $contact object.
 * @since 3.0
 */
function epl_contact_entry_header_editable( $contact ) {
	?>
	<div class="epl-contact-entry-header">
		<input class="epl-contact-title-editable" type="text" name="post_title" value="<?php echo esc_attr( $contact->heading ); ?>"/>
		<span>
			<?php
			echo esc_attr( $contact->get_meta( 'contact_category' ) );
			?>
		</span>
	</div>
	<?php
}
add_action( 'epl_contact_entry_header_editable', 'epl_contact_entry_header_editable' );

/**
 * Contact Assigned Tags
 *
 * @param array $contact object.
 * @since 3.0
 */
function epl_contact_assigned_tags( $contact ) {
	?>
	<div class="epl-contact-assigned-tags-wrap">
		<ul class="epl-contact-assigned-tags">
			<?php
				$contact_tags = wp_get_object_terms( $contact->id, 'epl_contact_tag' );
			if ( ! empty( $contact_tags ) ) {
				if ( ! is_wp_error( $contact_tags ) ) {
					foreach ( $contact_tags as $term ) {
						$bgcolor = epl_get_contact_tag_bgcolor( $term->term_id );
						echo '<li data-id="' . esc_attr( $term->term_id ) . '" id="contact-tag-' . esc_attr( $term->term_id ) . '" style="background:' . esc_attr( $bgcolor ) . '">' . esc_html( $term->name ) . '<span class="dashicons dashicons-no epl-contact-tag-del"></span></li>';
					}
				}
			}
			?>
		</ul>
	</div>
	<?php
}
add_action( 'epl_contact_assigned_tags', 'epl_contact_assigned_tags' );

/**
 * Contact Background Info
 *
 * @param array $contact object.
 * @since 3.0
 */
function epl_contact_background_info( $contact ) {
	echo '<div class="epl-contact-bg-info-wrap">';
		echo '<h4>' . esc_html__( 'Background Info', 'easy-property-listings' ) . '</h4>';
		echo '<div class="epl-contact-bg-info">';
			echo wp_kses_post( $contact->background_info );
		echo '</div>';

	echo '</div>';
}
add_action( 'epl_contact_background_info', 'epl_contact_background_info' );

/**
 * Contact Avatar Image
 *
 * @param array $contact object.
 * @since 3.0
 */
function epl_contact_avatar( $contact ) {
	?>
	<div class="avatar-wrap left" id="contact-avatar">
		<?php
			echo wp_kses_post( get_avatar( $contact->email, apply_filters( 'epl_contact_gravatar_size', 160 ) ) );
		?>
		<br />
	</div>
	<?php
}
add_action( 'epl_contact_avatar', 'epl_contact_avatar' );

/**
 * Contact Social Icons
 *
 * @param array $contact object.
 * @since 3.0
 */
function epl_contact_social_icons( $contact ) {

	$fb      = $contact->get_meta( 'contact_facebook' );
	$twitter = $contact->get_meta( 'contact_twitter' );
	$gplus   = $contact->get_meta( 'contact_google_plus' );
	$linked  = $contact->get_meta( 'contact_linked_in' );
	?>

	<?php if ( ! empty( $fb ) ) : ?>
		<a href="<?php echo esc_url( $fb ); ?>">
			<span class="epl-contact-social-icon">f</span>
		</a>
	<?php endif; ?>
	<?php if ( ! empty( $twitter ) ) : ?>
		<a href="<?php echo esc_url( $twitter ); ?>">
			<span class="epl-contact-social-icon">t</span>
		</a>
	<?php endif; ?>
	<?php if ( ! empty( $gplus ) ) : ?>
		<a href="<?php echo esc_url( $gplus ); ?>">
			<span class="epl-contact-social-icon">g+</span>
		</a>
	<?php endif; ?>
	<?php if ( ! empty( $linked ) ) : ?>
		<a href="<?php echo esc_url( $linked ); ?>">
			<span class="epl-contact-social-icon">in</span>
		</a>
	<?php endif; ?>
	<?php
	do_action( 'epl_contact_more_social_icons', $contact );
}
add_action( 'epl_contact_social_icons', 'epl_contact_social_icons' );

/**
 * Contact Details
 *
 * @param array $contact object.
 * @since 3.0
 * @since 3.4.1 Added wrapper classes to contact values.
 */
function epl_contact_contact_details( $contact ) {
	?>

	<span class="epl-contact-name epl-info-item editable">
		<span data-key="name">
			<span class="epl-contact-value"><?php echo esc_attr( $contact->get_meta( 'contact_first_name' ) . ' ' . $contact->get_meta( 'contact_last_name' ) ); ?></span>
		</span>
	</span>
	<?php echo wp_kses_post( $contact->get_emails() ); ?>

	<?php
		echo wp_kses_post( $contact->get_phones() );
		$web  = $contact->get_meta( 'contact_website' );
		$addr = $contact->epl_contact_get_address();
	?>

	<?php if ( ! empty( $web ) ) : ?>
		<span class="contact-website epl-info-item editable" data-key="website">
			<span class="dashicons dashicons-admin-site-alt3 epl-contact-icons"></span>
			<span class="epl-contact-value"><?php echo esc_attr( $web ); ?></span>
		</span>
	<?php endif; ?>
	<?php // TODO: Fix this as it should not display if address is empty. ?>
	<?php if ( ! empty( $addr ) ) : ?>
		<span class="contact-address epl-info-item editable" data-key="address">
			<span class="dashicons dashicons-admin-home epl-contact-icons"></span>
			<span class="epl-contact-value"><?php echo esc_attr( $addr ); ?></span>
		</span>
	<?php endif; ?>
	<span class="contact-since epl-info-item">
		<span class="dashicons dashicons-clock epl-contact-icons"></span>
		<span class="epl-contact-value"><?php esc_html_e( 'Contact since', 'easy-property-listings' ); ?>
		<?php echo esc_attr( date_i18n( get_option( 'date_format' ), strtotime( $contact->date_created ) ) ); ?></span>
	</span>
	<span class="epl-contact-view-more">
		<span class="dashicons dashicons-arrow-right-alt epl-contact-icons"></span>
		<span class="epl-contact-value"><?php esc_html_e( 'View More', 'easy-property-listings' ); ?></span>
	</span>
	<?php
}
add_action( 'epl_contact_contact_details', 'epl_contact_contact_details' );

/**
 * Contact Recent Interest in Listings
 *
 * @param array  $contact object.
 * @param int    $number Number page.
 * @param int    $paged Paging or not.
 * @param string $orderby Order by.
 * @param string $order Order ASC or DESC.
 * @since 3.0
 */
function epl_contact_recent_interests( $contact, $number = 10, $paged = 1, $orderby = 'post_date', $order = 'DESC' ) {
	?>
	<?php do_action( 'epl_contact_add_listing_form', $contact ); ?>
	<h3 class="epl-contact-activity-title">
		<?php esc_html_e( 'Listings', 'easy-property-listings' ); ?>
	</h3>
	<span class="epl-contact-add-old-listing-form-wrap">
		<input id="epl_contact_listing_search" type="text" placeholder="<?php esc_html_e( 'Search Listings', 'easy-property-listings' ); ?>"/>
	</span>
	<span class="epl-contact-or"><?php esc_html_e( 'Or', 'easy-property-listings' ); ?></span>
	<span class="epl-contact-add-listing"><?php esc_html_e( 'Add New', 'easy-property-listings' ); ?></span>

	<input type="hidden" id="epl-listing-table-orderby" value="<?php echo esc_attr( $orderby ); ?>"/>
	<input type="hidden" id="epl-listing-table-order" value="<?php echo esc_attr( $order ); ?>">
	<?php
	epl_contact_get_listings_html( $contact, $number, $paged, $orderby, $order );
}
add_action( 'epl_contact_recent_interests', 'epl_contact_recent_interests' );

/**
 * Contact Recent Activities
 *
 * @param array  $contact object.
 * @param int    $number Number page.
 * @param int    $paged Paging or not.
 * @param string $orderby Order by.
 * @param string $order Order ASC or DESC.
 * @since 3.0
 */
function epl_contact_recent_activities( $contact, $number = 10, $paged = 1, $orderby = 'comment_date', $order = 'DESC' ) {
	?>
	<?php do_action( 'epl_contact_add_activity_form', $contact ); ?>
	<h3 class="epl-contact-activity-title"><?php esc_html_e( 'Activities', 'easy-property-listings' ); ?> </h3>
	<span class="epl-contact-add-activity"><?php esc_html_e( 'Add New', 'easy-property-listings' ); ?></span>
	<input type="hidden" id="epl-contact-table-orderby" value="<?php echo esc_attr( $orderby ); ?>"/>
	<input type="hidden" id="epl-contact-table-order" value="<?php echo esc_attr( $order ); ?>">
	<?php
	epl_contact_get_activities_html( $contact, $number, $paged, $orderby, $order );
}
add_action( 'epl_contact_recent_activities', 'epl_contact_recent_activities' );

/**
 * Contact Get Activities HTML
 *
 * @param array  $contact object.
 * @param int    $number Number page.
 * @param int    $paged Paging or not.
 * @param string $orderby Order by.
 * @param string $order Order ASC or DESC.
 * @since 3.0
 */
function epl_contact_get_activities_html( $contact, $number = 10, $paged = 1, $orderby = 'comment_date', $order = 'DESC' ) {

	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {

		check_ajax_referer( 'epl_ajax_nonce', '_epl_nonce' );

		$contact = isset( $_POST['contact'] ) ? sanitize_text_field( wp_unslash( $_POST['contact'] ) ) : '';
		$number  = isset( $_POST['number'] ) ? sanitize_text_field( wp_unslash( $_POST['number'] ) ) : '';
		$paged   = isset( $_POST['paged'] ) ? sanitize_text_field( wp_unslash( $_POST['paged'] ) ) : '';
		$orderby = isset( $_POST['orderby'] ) ? sanitize_text_field( wp_unslash( $_POST['orderby'] ) ) : '';
		$order   = isset( $_POST['order'] ) ? sanitize_text_field( wp_unslash( $_POST['order'] ) ) : '';
	}
	if ( ! is_object( $contact ) ) {
		$contact = new EPL_Contact( $contact );
	}

	$activities = $contact->get_notes( $number, $paged, $orderby, $order );
	?>
	<div id="epl-contact-activity-table-wrapper">
		<table class="wp-list-table widefat striped epl-contact-activities">
			<thead>
			<tr class="epl-contact-activities-table-heads">
				<th class="epl-sorted-<?php echo esc_attr( strtolower( $order ) ); ?>" data-sort="comment_type"><?php esc_html_e( 'Type', 'easy-property-listings' ); ?></th>
				<th class="epl-sorted-<?php echo esc_attr( strtolower( $order ) ); ?>" data-sort="comment_content"><?php esc_html_e( 'Comment', 'easy-property-listings' ); ?></th>
				<th class="epl-sorted-<?php echo esc_attr( strtolower( $order ) ); ?>" data-sort="comment_date"><?php esc_html_e( 'Date', 'easy-property-listings' ); ?></th>
			</tr>
			</thead>
			<tbody>
			<?php if ( ! empty( $activities ) ) : ?>
				<?php foreach ( $activities as $activity ) : ?>
					<tr data-activity-id="<?php echo esc_attr( $activity->comment_ID ); ?>" id="activity-id-<?php echo esc_attr( $activity->comment_ID ); ?>" class="epl-contact-activity-row epl-contact-activity-<?php echo esc_attr( $activity->comment_type ); ?>" >
						<td><?php echo esc_attr( $contact->get_activity_type( $activity->comment_type ) ); ?></td>
						<td>
							<?php
							if ( $activity->comment_post_ID > 0 ) {
								echo '<div class="epl-contact-inline-lis-details">';
								echo '<span class="epl-contact-inline-lis-img">';
									echo wp_kses_post( get_the_post_thumbnail( $activity->comment_post_ID, array( 50, 50 ) ) );
								echo '</span>';
								echo '<span class="epl-contact-inline-lis-title">';
								echo '<a href="' . esc_url( get_permalink( $activity->comment_post_ID ) ) . '">' . esc_attr( get_the_title( $activity->comment_post_ID ) ) . '</a>';
								echo '</span>';
								echo '</div>';
							}

								echo wp_kses_post( $activity->comment_content );
							?>
						</td>
						<td><?php echo esc_attr( date_i18n( get_option( 'date_format' ), strtotime( $activity->comment_date ) ) ); ?></td>
					</tr>
				<?php endforeach; ?>
			<?php else : ?>
				<tr><td colspan="5"><?php esc_html_e( 'No Activities', 'easy-property-listings' ); ?></td></tr>
			<?php endif; ?>
			</tbody>
		</table>
		<?php $new_paged = $paged + 1; ?>
		<span  data-page="<?php echo esc_attr( $new_paged ); ?>" class="epl-contact-load-activities"><?php esc_html_e( 'Load More', 'easy-property-listings' ); ?> </span>
	</div>
	<?php
	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
		wp_die();
	}
}
add_action( 'wp_ajax_epl_contact_get_activity_table', 'epl_contact_get_activities_html', 10, 5 );

/**
 * Contact Get Listings HTML
 *
 * @param array  $contact object.
 * @param int    $number Number page.
 * @param int    $paged Paging or not.
 * @param string $orderby Order by.
 * @param string $order Order ASC or DESC.
 * @since 3.0
 */
function epl_contact_get_listings_html( $contact, $number = 10, $paged = 1, $orderby = 'post_date', $order = 'DESC' ) {

	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {

		check_ajax_referer( 'epl_ajax_nonce', '_epl_nonce' );
		$contact = isset( $_POST['contact'] ) ? sanitize_text_field( wp_unslash( $_POST['contact'] ) ) : '';
		$number  = isset( $_POST['number'] ) ? sanitize_text_field( wp_unslash( $_POST['number'] ) ) : '';
		$paged   = isset( $_POST['paged'] ) ? sanitize_text_field( wp_unslash( $_POST['paged'] ) ) : '';
		$orderby = isset( $_POST['orderby'] ) ? sanitize_text_field( wp_unslash( $_POST['orderby'] ) ) : '';
		$order   = isset( $_POST['order'] ) ? sanitize_text_field( wp_unslash( $_POST['order'] ) ) : '';
	}
	if ( ! is_object( $contact ) ) {
		$contact = new EPL_Contact( $contact );
	}

	$activities = get_posts(
		array(
			'post_type'      => array( 'property', 'rural', 'commercial', 'commercial_land', 'business', 'rental', 'land' ),
			'post_status'    => 'publish',
			'meta_key'       => 'property_owner',
			'meta_value'     => $contact->id,
			'posts_per_page' => -1,
		)
	);
	?>
	<div id="epl-contact-listing-table-wrapper">
	<table class="wp-list-table widefat striped epl-contact-listings">
		<thead>
		<tr class="epl-contact-listings-table-heads">
			<th class="epl-sorted-<?php echo esc_attr( strtolower( $order ) ); ?>" data-sort="listing_type"><?php esc_html_e( 'Type', 'easy-property-listings' ); ?></th>
			<th class="epl-sorted-<?php echo esc_attr( strtolower( $order ) ); ?>" data-sort="post_content"><?php esc_html_e( 'Title', 'easy-property-listings' ); ?></th>
			<th class="epl-sorted-<?php echo esc_attr( strtolower( $order ) ); ?>" data-sort="listing_status"><?php esc_html_e( 'Status', 'easy-property-listings' ); ?></th>
		</tr>
		</thead>
		<tbody>
		<?php if ( ! empty( $activities ) ) : ?>
			<?php foreach ( $activities as $inserted_lisitng ) : ?>
				<tr data-activity-id="<?php echo esc_attr( $inserted_lisitng->ID ); ?>" id="activity-id-<?php echo esc_attr( $inserted_lisitng->ID ); ?>" class="epl-contact-activity-row " >
					<td><?php echo esc_attr( $inserted_lisitng->post_type ); ?></td>
					<td>
						<?php
							echo '<a href="' . esc_url( get_edit_post_link( $inserted_lisitng->ID ) ) . '">' . esc_attr( $inserted_lisitng->post_title ) . '</a>';
						?>
					</td>
					<td>
						<?php echo esc_attr( get_post_meta( $inserted_lisitng->ID, 'property_status', true ) ); ?>
					</td>
				</tr>
			<?php endforeach; ?>
		<?php else : ?>
			<tr><td colspan="5"><?php esc_html_e( 'No Listings Found', 'easy-property-listings' ); ?></td></tr>
		<?php endif; ?>
		</tbody>
	</table>
	</div>
	<?php
	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
		wp_die();
	}
}
add_action( 'wp_ajax_epl_contact_get_listing_table', 'epl_contact_get_listings_html', 10, 5 );

/**
 * Contact Add Activity Form
 *
 * @param array $contact object.
 * @since 3.0
 */
function epl_contact_add_activity_form( $contact ) {
	$form_builder  = new EPL_FORM_BUILDER();
	$listing_ids   = $contact->listing_ids;
	$listings_opts = array( '' => esc_html__( 'No Listing', 'easy-property-listings' ) );
	if ( ! empty( $listing_ids ) ) {
		$listings = get_posts(
			array(
				'post__in'  => $listing_ids,
				'post_type' => epl_all_post_types(),
			)
		);
		if ( ! empty( $listings ) ) :
			foreach ( $listings as $listing ) :
				$listings_opts[ $listing->ID ] = $listing->post_title;
			endforeach;
		endif;
	}
	$fields = array(
		array(
			'label'  => esc_html__( 'Add Activity', 'easy-property-listings' ),
			'class'  => 'col-1 epl-inner-div',
			'id'     => 'epl-contact-add-activity-wrap',
			'help'   => '<hr/>',
			'fields' => array(
				array(
					'name'  => 'epl_contact_activity_content',
					'class' => 'epl-contact-note-input',
					'type'  => 'textarea',
				),
				array(
					'name'  => 'epl_contact_activity_type',
					'class' => 'epl-contact-note-select',
					'type'  => 'select',
					'opts'  => $contact->get_activity_types(),
				),
				array(
					'name'  => 'epl_contact_activity_listing',
					'class' => 'epl-contact-note-select',
					'type'  => 'select',
					'opts'  => $listings_opts,
				),
				array(
					'name'  => 'epl_contact_activity_submit',
					'value' => esc_html__( 'Add', 'easy-property-listings' ),
					'class' => 'button button-primary',
					'type'  => 'submit',
				),
			),
		),
	);
	$form_builder->set_form_attributes(
		array(
			'name' => 'epl_contact_add_activity_form',
			'id'   => 'epl_contact_add_activity_form',
		)
	);
	$form_builder->add_nonce( 'add_contact_note_nonce' );
	$form_builder->add_sections( $fields );
	echo '<div class="epl-contact-add-activity-form-wrap">';
		$form_builder->render_form();
	echo '</div>';
}
add_action( 'epl_contact_add_activity_form', 'epl_contact_add_activity_form' );

/**
 * Contact Add Listing Form
 *
 * @param array $contact contact object.
 * @since 3.0
 */
function epl_contact_add_listing_form( $contact ) {
	global $epl_settings;
	$form_builder  = new EPL_FORM_BUILDER();
	$listing_types = epl_get_active_post_types();
	$fields        = array(
		array(
			'label'  => esc_html__( 'Add Listing', 'easy-property-listings' ),
			'class'  => 'col-1 epl-inner-div',
			'id'     => 'epl-contact-add-listing-wrap',
			'help'   => '<hr/>',
			'fields' => array(

				array(
					'name'  => 'property_owner',
					'label' => '',
					'type'  => 'hidden',
					'value' => $contact->id,
				),
				array(
					'name'      => 'property_address_lot_number',
					'label'     => esc_html__( 'Lot', 'easy-property-listings' ),
					'type'      => 'text',
					'maxlength' => '40',
					'include'   => array( 'land', 'commercial_land' ),
				),

				array(
					'name'      => 'property_address_sub_number',
					'label'     => esc_html__( 'Unit', 'easy-property-listings' ),
					'type'      => 'text',
					'maxlength' => '40',
					'exclude'   => array( 'land', 'commercial_land' ),
				),

				array(
					'name'      => 'property_address_street_number',
					'label'     => esc_html__( 'Street Number', 'easy-property-listings' ),
					'type'      => 'text',
					'maxlength' => '40',
				),

				array(
					'name'      => 'property_address_street',
					'label'     => esc_html__( 'Street Name', 'easy-property-listings' ),
					'type'      => 'text',
					'maxlength' => '80',
				),

				array(
					'name'      => 'property_address_suburb',
					'label'     => epl_labels( 'label_suburb' ),
					'type'      => 'text',
					'maxlength' => '80',
				),

				array(
					'name'      => 'property_address_state',
					'label'     => epl_labels( 'label_state' ),
					'type'      => 'text',
					'maxlength' => '80',
				),

				array(
					'name'      => 'property_address_postal_code',
					'label'     => epl_labels( 'label_postcode' ),
					'type'      => 'text',
					'maxlength' => '30',
				),

				array(
					'name'      => 'property_address_country',
					'label'     => esc_html__( 'Country', 'easy-property-listings' ),
					'type'      => 'text',
					'maxlength' => '40',
				),
				array(
					'name'  => 'post_title',
					'label' => esc_html__( 'Full Address', 'easy-property-listings' ),
					'type'  => 'text',
				),
				array(
					'name'      => 'post_type',
					'label'     => esc_html__( 'Listing Type', 'easy-property-listings' ),
					'type'      => 'select',
					'class'     => 'epl-contact-note-select',
					'opts'      => $listing_types,
					'maxlength' => '200',
				),
				array(
					'name'      => 'property_status',
					'label'     => esc_html__( 'Listing Status', 'easy-property-listings' ),
					'type'      => 'select',
					'class'     => 'epl-contact-note-select',
					'opts'      => apply_filters(
						'epl_contact_property_status',
						array(
							'current' => esc_html__( 'Current', 'easy-property-listings' ),
							'sold'    => esc_html__( 'Sold', 'easy-property-listings' ),
							'leased'  => esc_html__( 'Leased', 'easy-property-listings' ),
						)
					),
					'maxlength' => '200',
				),

				array(
					'name'  => 'contact_listing_submit',
					'value' => esc_html__( 'Add', 'easy-property-listings' ),
					'class' => 'button button-primary',
					'type'  => 'submit',
				),
			),
		),
	);
	$form_builder->set_form_attributes(
		array(
			'name' => 'epl_contact_add_listing_form',
			'id'   => 'epl_contact_add_listing_form',
		)
	);
	$form_builder->add_nonce( 'add_contact_listing_nonce' );
	$form_builder->add_sections( $fields );
	echo '<div class="epl-contact-add-listing-form-wrap">';
	$form_builder->render_form();
	echo '</div>';
}
add_action( 'epl_contact_add_listing_form', 'epl_contact_add_listing_form' );

/**
 * Displays Contact details in listing owner meta box
 *
 * @param array  $post object.
 * @param string $value contact ID.
 * @since 3.0
 */
function epl_before_meta_field_property_owner( $post, $value ) {

	if ( 0 === intval( $value ) ) {
		return;
	}

	$url     = admin_url( 'admin.php?page=epl-contacts&view=overview&id=' . $value );
	$contact = new EPL_Contact( $value );

	if ( ! $contact || $contact->ID <= 0 ) {
		return;
	}

	echo '<tr class="form-field"><td>';
	echo '
			<div class="epl-listing-owner-details">
				<div class="epl-listing-owner-grav">
					' . wp_kses_post( get_avatar( $contact->email, apply_filters( 'epl_contact_gravatar_size', 160 ) ) ) . '
				</div>
				<div class="epl-listing-owner-mail">
					' . wp_kses_post( $contact->get_emails() ) . '
				</div>
				<div class="epl-listing-owner-heading">
					' . wp_kses_post( $contact->heading ) . '
				</div>
				<a class="epl-listing-contact-url" href="' . esc_url( $url ) . '">' .
					esc_html__( 'View Contact', 'easy-property-listings' ) . '
				</a>
			</div>';
	echo '</td></tr>';
}
add_action( 'epl_before_meta_field_property_owner', 'epl_before_meta_field_property_owner', 10, 2 );

/**
 * Search Contact
 *
 * @since 3.0
 */
function epl_search_contact() {

	check_ajax_referer( 'epl_ajax_nonce', '_epl_nonce' );

	$u_name       = isset( $_POST['user_name'] ) ? sanitize_text_field( wp_unslash( $_POST['user_name'] ) ) : '';
	$search_array = array(
		'showposts'        => 6,
		'post_type'        => 'epl_contact',
		'post_password'    => '',
		'suppress_filters' => true,
		'meta_query'       => array(
			'relation' => 'OR',
			array(
				'key'     => 'contact_first_name',
				'value'   => $u_name,
				'compare' => 'LIKE',
			),
			array(
				'key'     => 'contact_last_name',
				'value'   => $u_name,
				'compare' => 'LIKE',
			),
		),
	);

	$listings = new WP_Query( $search_array );

	if ( ! empty( $listings->posts ) ) {
		echo '<ul class="epl-contact-listing-suggestion">';
		foreach ( $listings->posts as  $listing ) {
			echo '<li data-id="' . esc_attr( $listing->ID ) . '">' . esc_attr( get_post_meta( $listing->ID, 'contact_first_name', true ) ) . ' ' . esc_attr( get_post_meta( $listing->ID, 'contact_last_name', true ) ) . '</li>';
		}
		echo '</ul>';
	}
	wp_die();
}
add_action( 'wp_ajax_epl_search_contact', 'epl_search_contact' );

/**
 * Search Listing
 *
 * @since 3.0
 */
function epl_search_contact_listing() {

	check_ajax_referer( 'epl_ajax_nonce', '_epl_nonce' );
	$search_term  = isset( $_POST['s'] ) ? sanitize_text_field( wp_unslash( $_POST['s'] ) ) : '';
	$search_array = array(
		's'           => $search_term,
		'showposts'   => 6,
		'post_type'   => epl_get_core_post_types(),
		'post_status' => 'publish',
	);

	$query = http_build_query( $search_array );

	$listings = get_posts( $query );

	if ( ! empty( $listings ) ) {
		echo '<ul class="epl-contact-owned-listing-suggestion striped">';
		foreach ( $listings as  $listing ) {
			$status = get_post_meta( $listing->ID, 'property_status', true );
			echo '<li data-id="' . esc_attr( $listing->ID ) . '"><span class="epl-contact-listing-type">' . esc_attr( $listing->post_type ) . '</span>' . esc_attr( $listing->post_title ) . '<span class="epl-contact-listing-status type_' . esc_attr( $status ) . '">' . esc_attr( $status ) . '</span></li>';
		}
		echo '</ul>';
	}
	wp_die();
}
add_action( 'wp_ajax_epl_search_contact_listing', 'epl_search_contact_listing' );

/**
 * Search Users
 *
 * @since 3.0
 * @since  3.4.1 removed wp_kses_post filter to support data atts in output
 */
function epl_search_user() {

	check_ajax_referer( 'epl_ajax_nonce', '_epl_nonce' );

	$search_term = isset( $_REQUEST['user_name'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['user_name'] ) ) : '';

	$args = array(
		'search' => $search_term . '*',
		'number' => 5,
	);

	if ( isset( $_REQUEST['exclude_roles'] ) ) {
		$exclude_roles        = explode( ',', sanitize_text_field( wp_unslash( $_REQUEST['exclude_roles'] ) ) );
		$exclude_roles        = array_map( 'sanitize_text_field', $exclude_roles );
		$args['role__not_in'] = $exclude_roles;
	}

	$users = get_users( $args );

	if ( ! empty( $users ) && ! is_wp_error( $users ) ) {
		ob_start();
		echo '<ul class="epl-contact-user-suggestion">';
		foreach ( $users as  $user ) {
			echo '<li data-uname="' . esc_attr( $user->data->user_login ) . '" data-id="' . esc_attr( $user->ID ) . '">' . esc_attr( $user->data->display_name ) . '</li>';
		}
		echo '</ul>';
		echo ob_get_clean(); //phpcs:ignore
	}
	exit;
}
add_action( 'wp_ajax_epl_search_user', 'epl_search_user' );

/**
 * Save a customer note being added
 *
 * @since  3.0
 * @param  array $args The $_POST array being passeed.
 * @return object         the comment object
 */
function epl_contact_save_note_note_tab( $args ) {

	if ( ! is_admin() || ! epl_contact_access() ) {
		wp_die( esc_html__( 'You do not have permission to save note.', 'easy-property-listings' ) );
	}
	if ( empty( $args ) ) {
		return;
	}
	$contact_note = trim( sanitize_text_field( $args['contact_note'] ) );
	$listing_id   = trim( sanitize_text_field( $args['listing_id'] ) );
	$note_type    = trim( sanitize_text_field( $args['activity_type'] ) );

	$contact_id = (int) $args['contact_id'];
	$nonce      = $args['add_contact_note_nonce'];
	if ( ! wp_verify_nonce( $nonce, 'add-contact-note' ) ) {
		wp_die( esc_html__( 'Cheatin\' eh?!', 'easy-property-listings' ) );
	}
	if ( empty( $contact_note ) ) {
		epl_set_error( 'empty-contact-note', esc_html__( 'A note is required', 'easy-property-listings' ) );
	}
	if ( epl_get_errors() ) {
		epl_set_error();
		return;
	}
	do_action( 'epl_pre_insert_contact_note', $contact_id, $contact_note, $listing_id, $note_type );

	$contact     = new EPL_contact( $contact_id );
	$note_object = $contact->add_note( $contact_note, $note_type, $listing_id );

	if ( ! empty( $note_object ) && ! empty( $contact->id ) ) {
		ob_start();
		?>
		<div class="epl-contact-note-wrapper dashboard-comment-wrap comment-item epl-admin-note">
			<span class="epl-note-content-meta">
				<span class="epl-note-for-listing">
					<?php
					echo isset( $note_object->comment_post_ID ) ? esc_attr( get_the_title( $note_object->comment_post_ID ) ) : '';
					?>
				</span>
				<span class="epl-note-time">
					<?php
					echo esc_attr( date_i18n( get_option( 'date_format' ), strtotime( $note_object->comment_date ) ) );
					?>
				</span>
			</span>
			<span class="epl-note-content-wrap">
				<?php echo wp_kses_post( stripslashes( $note_object->comment_content ) ); ?>
			</span>
		</div>
		<?php
		$output = ob_get_contents();
		ob_end_clean();
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			echo wp_kses_post( $output );
			exit;
		}
		return $note_object;
	}
	return false;
}
add_action( 'epl_add-contact-note-note-tab', 'epl_contact_save_note_note_tab', 10, 1 );
