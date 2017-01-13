<?php
/**
 * Contact Actions
 *
 * @package     EPL
 * @subpackage  Contacts/Actions
 * @copyright   Copyright (c) 2016, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Processes a custom edit
 *
 * @since  3.0
 * @param  array $args The $_POST array being passeed
 * @return array $output Response messages
 */
function epl_edit_contact( $args ) {
	if ( ! is_admin() || ! epl_contact_access() ) {
		wp_die( __( 'You do not have permission to edit this contact.', 'easy-property-listings'  ) );
	}

	if ( empty( $args ) ) {
		return;
	}

	$contact_info = $args['contactinfo'];
	$contact_id   = (int)$args['contactinfo']['id'];
	$nonce         = $args['_wpnonce'];

	if ( ! wp_verify_nonce( $nonce, 'edit-contact' ) ) {
		wp_die( __( 'Cheatin\' eh?!', 'easy-property-listings'  ) );
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
		epl_set_error( 'epl-invalid-email', __( 'Please enter a valid email address.', 'easy-property-listings'  ) );
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
 * @since  3.0
 * @param  array $args The $_POST array being passeed
 * @return int         Wether it was a successful deletion
 */
function epl_contact_delete( $args ) {

	if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
		wp_die( __( 'You do not have permission to delete this contact.', 'easy-property-listings'  ) );
	}

	if ( empty( $args ) ) {
		return;
	}

	$contact_id   = (int)$args['contact_id'];
	$confirm       = ! empty( $args['epl-contact-delete-confirm'] ) ? true : false;
	$nonce         = $args['_wpnonce'];

	if ( ! wp_verify_nonce( $nonce, 'delete-contact' ) ) {
		wp_die( __( 'Cheatin\' eh?!', 'easy-property-listings'  ) );
	}

	if ( ! $confirm ) {
		epl_set_error( 'contact-delete-no-confirm', __( 'Please confirm you want to delete this contact', 'easy-property-listings'  ) );
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
		if( !empty($listings_array) ) {
			foreach($listings_array as $listing_id) {
				$contact->remove_listing($listing_id);
			}
		}

		$success        = $contact->delete( $contact->id );

		if ( $success ) {

			$redirect = admin_url( 'admin.php?page=epl-contacts&epl-message=contact-deleted' );

		} else {

			epl_set_error( 'epl-contact-delete-failed', __( 'Error deleting contact', 'easy-property-listings'  ) );
			$redirect = admin_url( 'admin.php?page=epl-contacts&view=delete&id=' . $contact_id );

		}

	} else {

		epl_set_error( 'epl-contact-delete-invalid-id', __( 'Invalid Contact ID', 'easy-property-listings'  ) );
		$redirect = admin_url( 'admin.php?page=epl-contacts' );

	}

	wp_redirect( $redirect );
	exit;

}
add_action( 'epl_delete-contact', 'epl_contact_delete', 10, 1 );

/**
 * Save a customer note being added
 *
 * @since  3.0
 * @param  array $args The $_POST array being passeed
 * @return object         the comment object
 */
function epl_contact_save_note( $args ) {

	if ( ! is_admin() || ! epl_contact_access() ) {
		wp_die( __( 'You do not have permission to save note.', 'easy-property-listings'  ) );
	}

	if ( empty( $args ) ) {
		return;
	}

	$contact_note 	= trim( sanitize_text_field( $args['contact_note'] ) );
	$listing_id 	= trim( sanitize_text_field( $args['listing_id'] ) );
	$note_type 	    = trim( sanitize_text_field( $args['note_type'] ) );

	$contact_id   = (int)$args['contact_id'];
	$nonce         = $args['add_contact_note_nonce'];

	if ( ! wp_verify_nonce( $nonce, 'add_contact_note_nonce' ) ) {
		wp_die( __( 'Cheatin\' eh?!', 'easy-property-listings'  ) );
	}

	if ( empty( $contact_note ) ) {
		epl_set_error( 'empty-customer-note', __( 'A note is required', 'easy-property-listings'  ) );
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
		<tr data-activity-id="<?php echo $note_object->comment_ID ;?>" id="activity-id-<?php echo $note_object->comment_ID ;?>" class="epl-contact-activity-row epl-contact-activity-<?php echo $note_object->comment_type; ?>" >
			<td><?php echo stripslashes( $contact->get_activity_type($note_object->comment_type ) ); ?></td>
			<td>
				<?php
					if($note_object->comment_post_ID > 0) {
						echo '<div class="epl-contact-inline-lis-details">';
						echo '<span class="epl-contact-inline-lis-img">';
						echo get_the_post_thumbnail($note_object->comment_post_ID, array(50,50));
						echo '</span>';
						echo '<span class="epl-contact-inline-lis-title">';
						echo '<a href="'.get_permalink($note_object->comment_post_ID).'">'.get_the_title($note_object->comment_post_ID).'</a>';
						echo '</span>';
						echo '</div>';
					}
					echo stripslashes( $note_object->comment_content );
				?>
			</td>
			<td>
				<?php
					echo date_i18n( get_option( 'date_format' ), strtotime( $note_object->comment_date ) );
				?>
			</td>
		</tr>
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
 * Save a contact listing being added
 *
 * @since  3.0
 * @param  array $args The $_POST array being passeed
 * @return object
 */
function epl_contact_save_listing( $args ) {
	$contact_add_listing_role = apply_filters( 'epl_add_contacts_listing', 'manage_options' );

	if ( ! is_admin() || ! current_user_can( $contact_add_listing_role ) ) {
		wp_die( __( 'You do not have permission to add listing.', 'easy-property-listings'  ) );
	}

	if ( empty( $args ) ) {
		return;
	}

	$post_fields = array('post_title','post_type');

	$ignore_fields = array('add_contact_listing_nonce','epl_actiion','contact_id');

	$nonce         = $args['add_contact_listing_nonce'];

	if ( ! wp_verify_nonce( $nonce, 'add_contact_listing_nonce' ) ) {
		wp_die( __( 'Cheatin\' eh?!', 'easy-property-listings'  ) );
	}
	if ( epl_get_errors() ) {
		epl_print_error();
		return;
	}

	do_action( 'epl_pre_insert_contact_listing', $args );
	if($args['property_owner'] > 0) {
		$insert_post_array = array('post_status'    =>  'publish');
		$insert_meta_array = array();
		foreach($args as $arg_key   =>  $arg_value) {
			if( in_array($arg_key,$post_fields) ) {
				$insert_post_array[$arg_key] = $arg_value;
			} elseif(!in_array($arg_key,$ignore_fields)) {
				$insert_meta_array[$arg_key] = $arg_value;
			}
		}
		if($insert_id = wp_insert_post($insert_post_array)) {
			foreach($insert_meta_array as $meta_key =>  $meta_value) {
				update_post_meta($insert_id,$meta_key,$meta_value);
			}
		} else {
			return false;
		}

	}
	$inserted_lisitng = get_post($insert_id);
	if ( ! empty( $inserted_lisitng )  ) {

		ob_start();
		?>
		<tr data-activity-id="<?php echo $inserted_lisitng->ID ;?>" id="activity-id-<?php echo $inserted_lisitng->ID ;?>" class="epl-contact-activity-row " >
			<td><?php echo $inserted_lisitng->post_type; ?></td>
			<td>
				<?php
					echo '<a href="'.get_edit_post_link($inserted_lisitng->ID).'">'.$inserted_lisitng->post_title.'</a>';
				?>
			</td>
			<td>
				<?php echo get_post_meta($inserted_lisitng->ID,'property_status',true); ?>
			</td>
		</tr>
		<?php
		$output = ob_get_contents();
		ob_end_clean();

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			echo $output;
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
 * @param  array $args The $_POST array being passeed
 * @return object
 */
function epl_contact_assign_existing_listing( $args ) {

	if ( ! is_admin() || ! epl_contact_access() ) {
		wp_die( __( 'You do not have permission to assign listings.', 'easy-property-listings'  ) );
	}

	if ( empty( $args ) ) {
		return;
	}

	do_action( 'epl_pre_assign_contact_listing', $args );
	if($args['contact_id'] > 0 && $args['id'] > 0) {
		update_post_meta($args['id'],'property_owner',$args['contact_id']);

	} else {
		wp_die();
	}
	$inserted_lisitng = get_post($args['id']);
	if ( ! empty( $inserted_lisitng )  ) {

		ob_start();
		?>
		<tr data-activity-id="<?php echo $inserted_lisitng->ID ;?>" id="activity-id-<?php echo $inserted_lisitng->ID ;?>" class="epl-contact-activity-row " >
			<td><?php echo $inserted_lisitng->post_type; ?></td>
			<td>
				<?php
				echo '<a href="'.get_edit_post_link($inserted_lisitng->ID).'">'.$inserted_lisitng->post_title.'</a>';
				?>
			</td>
			<td>
				<?php echo get_post_meta($inserted_lisitng->ID,'property_status',true); ?>
			</td>
		</tr>
		<?php
		$output = ob_get_contents();
		ob_end_clean();

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			echo $output;
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
 * @param  array $args The $_POST array being passeed
 * @return array $output Response messages
 */
function epl_meta_contact( $args ) {

	if ( ! is_admin() || ! epl_contact_access() ) {
		wp_die( __( 'You do not have permission to update this contact.', 'easy-property-listings'  ) );
	}

	if ( empty( $args ) ) {
		return;
	}

	$nonce         = $args['_wpnonce'];
	if ( ! wp_verify_nonce( $nonce, 'meta-contact' ) ) {
		wp_die( __( 'Cheatin\' eh?!', 'easy-property-listings'  ) );
	}

	$contact_id   = (int)$args['contact_id'];
	$contact = new EPL_Contact( $contact_id );
	if ( empty( $contact->ID ) ) {
		return false;
	}

	$not_meta_fields = array('epl_form_builder_form_submit','contact_id','_wpnonce','epl_action');

	$post_fields = array('post_title','post_content','ID','post_author');

	$field_updates = array('ID' =>  $contact_id);
	foreach($args as $key	=>	$value) {
		if( !in_array($key,$not_meta_fields) ) {

			// check if post fields
			if( in_array($key,$post_fields) ) {
				$field_updates[$key] = $value;
			} else {
				$contact->update_meta($key,$value);
			}


		}

	}
	wp_update_post($field_updates);

	$redirect = admin_url( 'admin.php?page=epl-contacts&view=meta&id=' . $contact_id );
	wp_redirect( $redirect );
	exit;

}
add_action( 'epl_meta-contact', 'epl_meta_contact', 10, 1 );

/**
 * create a new contact from backend
 *
 * @since  3.0
 * @param  array $args The $_POST array being passeed
 * @return array $output Response messages
 */
function epl_new_contact( $args ) {

	if ( ! is_admin() || ! epl_contact_access() ) {
		wp_die( __( 'You do not have permission to create contacts.', 'easy-property-listings'  ) );
	}

	if ( empty( $args ) ) {
		return;
	}

	$nonce         = $args['_wpnonce'];
	if ( ! wp_verify_nonce( $nonce, 'new-contact' ) ) {
		wp_die( __( 'Cheatin\' uhh?!', 'easy-property-listings'  ) );
	}

	$contact_id   = (int)$args['contact_id'];
	$contact = new EPL_Contact( $contact_id );
	if ( empty( $contact->ID ) ) {
		return false;
	}

	if( $contact->contact_exists($args['email']) ) {
		wp_die( __( 'A contact with this email already exists !', 'easy-property-listings'  ) );
	}
	if ( $contact->update( array('name'	=>	$args['title'], 'email' => $args['email']  ) ) ) {
		$contact->update_meta('contact_first_name',$args['first_name']);
		$contact->update_meta('contact_last_name',$args['last_name']);
		$contact->update_meta('contact_phones',array('phone' =>  $args['phone']) );
		$contact->update_meta('contact_category','new');
	}


	$redirect = admin_url( 'admin.php?page=epl-contacts&view=meta&id=' . $contact_id );
	wp_redirect( $redirect );
	exit;

}
add_action( 'epl_new-contact', 'epl_new_contact', 10, 1 );

/**
 * Update contact category
 *
 * @since 3.0
 * @return bool true if updated
 */
function epl_contact_category_update() {
	if( (int) $_POST['contact_id'] > 0 && trim($_POST['type']) != '' ) {

		$contact = new EPL_Contact($_POST['contact_id']);
		echo $contact->update_meta( 'contact_category',trim($_POST['type']) );
		wp_die();
	}
}
add_action('wp_ajax_epl_contact_category_update','epl_contact_category_update');

/**
 * Add/Update contact tags
 *
 * @since 3.0
 * @return bool true if updated
 */
function epl_contact_tag_add() {

	if( ( trim($_POST['term_id']) != '' ) ) {

		if( is_numeric($_POST['term_id']) ) {
			$_POST['term_id'] = (int) $_POST['term_id'];
		}

		// update tag for a contact
		if( (int) $_POST['contact_id'] > 0 ) {
			$terms = wp_set_object_terms( absint($_POST['contact_id']), $_POST['term_id'], 'epl_contact_tag', true );
			wp_die( current($terms) );
		} else {
			// update the tag
			if( $_POST['bg'])
				epl_update_contact_tag_bgcolor($_POST['term_id'],$_POST['bg']);

			if( $_POST['label'])
				wp_update_term( $_POST['term_id'], 'epl_contact_tag', array('name'	=>	$_POST['label']) );

			if( $_POST['delete'])
				wp_delete_term( $_POST['term_id'], 'epl_contact_tag' );

			wp_die(1);

		}

	}
}
add_action('wp_ajax_contact_tags_update','epl_contact_tag_add');

/**
 * delete contact tags
 *
 * @since 3.0
 * @return bool true if updated
 */
function epl_contact_tag_remove() {
	if( (int) $_POST['contact_id'] > 0 && (int) $_POST['term_id'] > 0 ) {

		wp_remove_object_terms( absint($_POST['contact_id']), absint($_POST['term_id']), 'epl_contact_tag' );
		wp_die(1);
	}
}
add_action('wp_ajax_epl_contact_tag_remove','epl_contact_tag_remove');

/**
 * Contact Action Menus
 *
 * Renders contact action menus
 *
 * @param $contact
 * @since 3.0
 */
function epl_contact_action_menus($contact) { ?>
	<div class="contact-action-menu epl-clearfix">
		<ul class="epl_contact_quick_actions">
			<li>
				<a  class="contact-action-category" href="#" title="<?php _e('Contact Category'); ?>">
					<span class="dashicons dashicons-flag"></span>
					<b class="caret"></b>
				</a>
				<ul class="epl-contact_category_suggestions">
					<?php

						$cats = epl_get_contact_categories();

						foreach($cats as $cat_key   =>  $cat_label) :
							echo '<li> <a href="#" data-key="'.$cat_key.'" data-label="'.$cat_label.'">'.$cat_label.'</a></li>';
						endforeach;
					?>
				</ul>
			</li>

			<li>
				<a class="contact-action-tag" href="#" title="<?php _e('Contact Tags' , 'easy-property-listings'  ); ?>">
					<span class="dashicons dashicons-tag"></span>
					<b class="caret"></b>
				</a>
				<div class="epl-contact-tags-find">
					<input type="text" id="contact-tag-hint" value=""/>
					<ul class="epl-contact_tags_suggestions">
						<?php
							$contact_tags = get_terms('epl_contact_tag',array( 'hide_empty' =>  false));
							if( !empty($contact_tags) ) {

								foreach($contact_tags as $contact_tag) {
									$bgcolor = epl_get_contact_tag_bgcolor($contact_tag->term_id);

									echo '<li data-bg="'.$bgcolor.'" style="background:'.$bgcolor.';color:#fff" data-id="'.$contact_tag->term_id.'" >'.$contact_tag->name.'</li>';
								}
							}
						?>
					</ul>
				</div>
			</li>
			<?php do_action('post_contact_custom_quick_edit_options', $contact); ?>
		</ul>

		<div class="epl_contact_pagination">
			<?php if(epl_get_prev_contact_link($contact->id)): ?>
				<a class="epl_contact_pagination_prev " href="<?php echo epl_get_prev_contact_link($contact->id); ?>">
					<span class="dashicons dashicons-arrow-left"></span>
				</a>
			<?php endif; ?>

			<?php if(epl_get_next_contact_link($contact->id)): ?>
				<a class="epl_contact_pagination_next " href="<?php echo epl_get_next_contact_link($contact->id); ?>">
					<span class="dashicons dashicons-arrow-right">
					</span>
				</a>
			<?php endif; ?>
		</div>
	</div>
<?php
}
add_action('epl_contact_action_menus','epl_contact_action_menus');

/**
 * Contact Entry Header
 *
 * Renders contact header
 *
 * @param $contact
 * @since 3.0
 */
function epl_contact_entry_header($contact) { ?>
	<div class="epl-contact-entry-header">
		<h1 class="epl-contact-title">
			<?php
				echo $contact->heading;
			?>
		</h1>
		<span>
			<?php
				echo $contact->get_category_label();
			?>
		</span>
	</div> <?php
}
add_action('epl_contact_entry_header','epl_contact_entry_header');

/**
 * Contact Header Editable
 *
 * @param $contact
 * @since 3.0
 */
function epl_contact_entry_header_editable($contact) { ?>
	<div class="epl-contact-entry-header">
		<input class="epl-contact-title-editable" type="text" name="post_title" value="<?php echo $contact->heading; ?>"/>
		<span>
			<?php
			echo $contact->get_meta('contact_category');
			?>
		</span>
	</div>
<?php
}
add_action('epl_contact_entry_header_editable','epl_contact_entry_header_editable');

/**
 * Contact Assigned Tags
 *
 * @param $contact
 * @since 3.0
 */
function epl_contact_assigned_tags($contact) { ?>
	<div class="epl-contact-assigned-tags-wrap">
		<ul class="epl-contact-assigned-tags">
			<?php
				$contact_tags = wp_get_object_terms( $contact->id,  'epl_contact_tag' );
				if ( ! empty( $contact_tags ) ) {
					if ( ! is_wp_error( $contact_tags ) ) {
						foreach( $contact_tags as $term ) {
							$bgcolor = epl_get_contact_tag_bgcolor( $term->term_id);
							echo '<li data-id="'.$term->term_id.'" id="contact-tag-'.$term->term_id.'" style="background:'.$bgcolor.'">' . esc_html( $term->name ) . '<span class="dashicons dashicons-no epl-contact-tag-del"></span></li>';
						}
					}
				}
			?>
		</ul>
	</div>
<?php
}
add_action('epl_contact_assigned_tags','epl_contact_assigned_tags');

/**
 * Contact Background Info
 *
 * @param $contact
 * @since 3.0
 */
function epl_contact_background_info($contact) {
	echo '<div class="epl-contact-bg-info-wrap">';
		echo '<h4>'.__('Background Info','easy-property-listings' ).'</h4>';
		echo '<div class="epl-contact-bg-info">';
			echo $contact->background_info;
		echo '</div>';

	echo '</div>';
}
add_action('epl_contact_background_info','epl_contact_background_info');

/**
 * Contact Avatar Image
 *
 * @param $contact
 * @since 3.0
 */
function epl_contact_avatar($contact) { ?>
	<div class="avatar-wrap left" id="contact-avatar">
		<?php echo get_avatar( $contact->email , apply_filters('epl_contact_gravatar_size',160) ); ?><br />
	</div>
<?php
}
add_action('epl_contact_avatar','epl_contact_avatar');

/**
 * Contact Social Icons
 *
 * @param $contact
 * @since 3.0
 */
function epl_contact_social_icons($contact) { ?>

	<?php if( $contact->get_meta('contact_facebook') != '' ) :?>
		<a href="<?php echo $contact->get_meta('contact_facebook'); ?>">
			<span class="epl-contact-social-icon">f</span>
		</a>
	<?php endif; ?>
	<?php if( $contact->get_meta('contact_twitter') != '' ) :?>
		<a href="<?php echo $contact->get_meta('contact_twitter'); ?>">
			<span class="epl-contact-social-icon">t</span>
		</a>
	<?php endif; ?>
	<?php if( $contact->get_meta('contact_google_plus') != '' ) :?>
		<a href="<?php echo $contact->get_meta('contact_google_plus'); ?>">
			<span class="epl-contact-social-icon">g+</span>
		</a>
	<?php endif; ?>
	<?php if( $contact->get_meta('contact_linked_in') != '' ) :?>
		<a href="<?php echo $contact->get_meta('contact_linked_in'); ?>">
			<span class="epl-contact-social-icon">in</span>
		</a>
	<?php endif; ?>
	<?php do_action('epl_contact_more_social_icons',$contact);
}
add_action('epl_contact_social_icons','epl_contact_social_icons');

/**
 * Contact Details
 *
 * @param $contact
 * @since 3.0
 */
function epl_contact_contact_details($contact) { ?>

	<span class="epl-contact-name epl-info-item editable">
		<span data-key="name">
			<?php echo $contact->get_meta('contact_first_name').' '.$contact->get_meta('contact_last_name'); ?>
		</span>
	</span>
	<?php echo $contact->get_emails();?>

	<?php echo $contact->get_phones();?>

	<?php if( $contact->get_meta('contact_website') != '' ) :?>
		<span class="contact_website epl-info-item editable" data-key="website">
			<span class="dashicons dashicons-admin-links epl-contact-icons"></span>
			<?php echo $contact->get_meta('contact_website'); ?>
		</span>
	<?php endif; ?>
	<span class="contact_website epl-info-item editable" data-key="address">
		<span class="dashicons dashicons-admin-home epl-contact-icons"></span>
		<?php echo $contact->epl_contact_get_address(); ?>
	</span>
	<span class="contact-since epl-info-item">
		<span class="dashicons dashicons-clock epl-contact-icons"></span>
		<?php _e( 'Contact since', 'easy-property-listings'  ); ?>
		<?php echo date_i18n( get_option( 'date_format' ), strtotime( $contact->date_created ) ) ?>
	</span>
	<span class="epl-contact-view-more">
		<span class="dashicons dashicons-arrow-right-alt epl-contact-icons"></span>
		<?php _e( 'View More', 'easy-property-listings'  ); ?>
	</span>
<?php
}
add_action('epl_contact_contact_details','epl_contact_contact_details');

/**
 * function epl_contact_recent_interests($contact) { ?>
 * 	<h3><?php _e( 'Listings', 'easy-property-listings'  ); ?></h3>
 * 	<?php
 * 		$listing_ids = $contact->listing_ids;
 * 		if( !empty($listing_ids) ) {
 * 			$listings    = get_posts( array( 'post__in' => $listing_ids, 'post_type'	=>	epl_all_post_types() ) );
 * 			$listings    = array_slice( $listings, 0, 10 );
 * 		}
 * 	?>
 * 	<table class="wp-list-table widefat striped listings">
 * 		<thead>
 * 		<tr>
 * 			<th><?php _e( 'ID', 'easy-property-listings'  ); ?></th>
 * 			<th><?php _e( 'Title', 'easy-property-listings'  ); ?></th>
 * 			<th><?php _e( 'Published Date', 'easy-property-listings'  ); ?></th>
 * 			<th><?php _e( 'Status', 'easy-property-listings'  ); ?></th>
 * 			<th><?php _e( 'Actions', 'easy-property-listings'  ); ?></th>
 * 		</tr>
 * 		</thead>
 * 		<tbody>
 * 		<?php if ( ! empty( $listings ) ) : ?>
 * 			<?php foreach ( $listings as $listing ) : ?>
 * 				<tr>
 * 					<td><?php echo $listing->ID; ?></td>
 * 					<td><?php echo  $listing->post_title; ?></td>
 * 					<td><?php echo date_i18n( get_option( 'date_format' ), strtotime( $listing->post_date ) ); ?></td>
 * 					<td><?php echo get_post_meta( $listing->ID,'property_status',true) ?></td>
 * 					<td>
 * 						<a title="<?php _e( 'View Details for Listing', 'easy-property-listings'  ); echo ' ' . $listing->ID; ?>" href="<?php echo admin_url( 'post.php?&action=edit&post=' . $listing->ID ); ?>">
 * 							<?php _e( 'View Details', 'easy-property-listings'  ); ?>
 * 						</a>
 * 						<?php do_action( 'epl_contact_recent_listings_actions', $contact, $listing ); ?>
 * 					</td>
 * 				</tr>
 * 			<?php endforeach; ?>
 * 		<?php else: ?>
 * 			<tr><td colspan="5"><?php _e( 'No Listings Found', 'easy-property-listings'  ); ?></td></tr>
 * 		<?php endif; ?>
 * 		</tbody>
 * 	</table> <?php
 * }
 * add_action('epl_contact_recent_interests','epl_contact_recent_interests');
 */

/**
 * Contact Recent Interest in Listings
 *
 * @param $contact
 * @param $number
 * @param $paged
 * @param $orderby
 * @param $order
 * @since 3.0
 */
function epl_contact_recent_interests( $contact , $number = 10 , $paged = 1 , $orderby = 'post_date' , $order = 'DESC') { ?>
	<?php do_action('epl_contact_add_listing_form', $contact); ?>
	<h3 class="epl-contact-activity-title">
		<?php _e( 'Listings', 'easy-property-listings'  ); ?>
	</h3>
	<span class="epl-contact-add-old-listing-form-wrap">
		<input id="epl_contact_listing_search" type="text" placeholder="<?php _e('Search Listings', 'easy-property-listings' ); ?>"/>
	</span>
	<span class="epl-contact-or"><?php _e('Or','easy-property-listings' );?></span>
	<span class="epl-contact-add-listing"><?php _e('Add New'); ?></span>

	<input type="hidden" id="epl-listing-table-orderby" value="<?php echo $orderby; ?>"/>
	<input type="hidden" id="epl-listing-table-order" value="<?php echo $order; ?>">
	<?php
	epl_contact_get_listings_html($contact,$number, $paged,$orderby, $order);
}
add_action('epl_contact_recent_interests','epl_contact_recent_interests');

/**
 * Contact Recent Activities
 *
 * @param $contact
 * @param $number
 * @param $paged
 * @param $orderby
 * @param $order
 * @since 3.0
 */
function epl_contact_recent_activities( $contact , $number = 10 , $paged = 1 , $orderby = 'comment_date' , $order = 'DESC') { ?>
	<?php do_action('epl_contact_add_activity_form', $contact); ?>
	<h3 class="epl-contact-activity-title"><?php _e( 'Activities', 'easy-property-listings'  ); ?> </h3>
	<span class="epl-contact-add-activity"><?php _e('Add New'); ?></span>
	<input type="hidden" id="epl-contact-table-orderby" value="<?php echo $orderby; ?>"/>
	<input type="hidden" id="epl-contact-table-order" value="<?php echo $order; ?>">
	<?php
	epl_contact_get_activities_html($contact,$number, $paged,$orderby, $order);
}
add_action('epl_contact_recent_activities','epl_contact_recent_activities');

/**
 * Contact Get Activities HTML
 *
 * @param $contact
 * @param $number
 * @param $paged
 * @param $orderby
 * @param $order
 * @since 3.0
 */
function epl_contact_get_activities_html( $contact , $number = 10 , $paged = 1 , $orderby = 'comment_date' , $order = 'DESC') {

	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
		extract($_POST);
	}
	if( !is_object($contact) )
		$contact = new EPL_Contact( $contact );

	//epl_print_r($contact,true);
	$activities = $contact->get_notes($number,$paged,$orderby,$order);
	?>
	<div id="epl-contact-activity-table-wrapper">
		<table class="wp-list-table widefat striped epl-contact-activities">
			<thead>
			<tr class="epl-contact-activities-table-heads">
				<th class="epl-sorted-<?php echo strtolower($order); ?>" data-sort="comment_type"><?php _e( 'Type', 'easy-property-listings'  ); ?></th>
				<th class="epl-sorted-<?php echo strtolower($order); ?>" data-sort="comment_content"><?php _e( 'Comment', 'easy-property-listings'  ); ?></th>
				<th class="epl-sorted-<?php echo strtolower($order); ?>" data-sort="comment_date"><?php _e( 'Date', 'easy-property-listings'  ); ?></th>
			</tr>
			</thead>
			<tbody>
			<?php if ( ! empty( $activities ) ) : ?>
				<?php foreach ( $activities as $activity ) : ?>
					<tr data-activity-id="<?php echo $activity->comment_ID ;?>" id="activity-id-<?php echo $activity->comment_ID ;?>" class="epl-contact-activity-row epl-contact-activity-<?php echo $activity->comment_type; ?>" >
						<td><?php echo $contact->get_activity_type($activity->comment_type) ?></td>
						<td>
							<?php
								if($activity->comment_post_ID > 0) {
									echo '<div class="epl-contact-inline-lis-details">';
									echo '<span class="epl-contact-inline-lis-img">';
										echo get_the_post_thumbnail($activity->comment_post_ID, array(50,50));
									echo '</span>';
									echo '<span class="epl-contact-inline-lis-title">';
									echo '<a href="'.get_permalink($activity->comment_post_ID).'">'.get_the_title($activity->comment_post_ID).'</a>';
									echo '</span>';
									echo '</div>';
								}

								echo  $activity->comment_content;
							?>
						</td>
						<td><?php echo date_i18n( get_option( 'date_format' ), strtotime( $activity->comment_date ) ); ?></td>
					</tr>
				<?php endforeach; ?>
			<?php else: ?>
				<tr><td colspan="5"><?php _e( 'No Activities', 'easy-property-listings'  ); ?></td></tr>
			<?php endif; ?>
			</tbody>
		</table>
		<span  data-page="<?php echo $paged + 1; ?>" class="epl-contact-load-activities"><?php _e('Load More'); ?> </span>
	</div><?php
	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
		wp_die();
	}
}
add_action('wp_ajax_epl_contact_get_activity_table','epl_contact_get_activities_html',10,5);

/**
 * Contact Get Listings HTML
 *
 * @param $contact
 * @param $number
 * @param $paged
 * @param $orderby
 * @param $order
 * @since 3.0
 */
function epl_contact_get_listings_html( $contact , $number = 10 , $paged = 1 , $orderby = 'post_date' , $order = 'DESC') {

	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
		extract($_POST);
	}
	if( !is_object($contact) )
		$contact = new EPL_Contact( $contact );

	//epl_print_r($contact,true);
	$activities = get_posts(
		array(
			'post_type'     =>  array('property', 'rural', 'commercial', 'commercial_land', 'business', 'rental', 'land'),
			'post_status'   =>  'publish',
			'meta_key'      =>  'property_owner',
			'meta_value'    =>  $contact->id,
			'posts_per_page'    =>  -1
		)
	);
	?>
	<div id="epl-contact-listing-table-wrapper">
	<table class="wp-list-table widefat striped epl-contact-listings">
		<thead>
		<tr class="epl-contact-listings-table-heads">
			<th class="epl-sorted-<?php echo strtolower($order); ?>" data-sort="listing_type"><?php _e( 'Type', 'easy-property-listings'  ); ?></th>
			<th class="epl-sorted-<?php echo strtolower($order); ?>" data-sort="post_content"><?php _e( 'Title', 'easy-property-listings'  ); ?></th>
			<th class="epl-sorted-<?php echo strtolower($order); ?>" data-sort="listing_status"><?php _e( 'Status', 'easy-property-listings'  ); ?></th>
		</tr>
		</thead>
		<tbody>
		<?php if ( ! empty( $activities ) ) : ?>
			<?php foreach ( $activities as $inserted_lisitng ) : ?>
				<tr data-activity-id="<?php echo $inserted_lisitng->ID ;?>" id="activity-id-<?php echo $inserted_lisitng->ID ;?>" class="epl-contact-activity-row " >
					<td><?php echo $inserted_lisitng->post_type; ?></td>
					<td>
						<?php
							echo '<a href="'.get_edit_post_link($inserted_lisitng->ID).'">'.$inserted_lisitng->post_title.'</a>';
						?>
					</td>
					<td>
						<?php echo get_post_meta($inserted_lisitng->ID,'property_status',true); ?>
					</td>
				</tr>
			<?php endforeach; ?>
		<?php else: ?>
			<tr><td colspan="5"><?php _e( 'No Listings Found', 'easy-property-listings'  ); ?></td></tr>
		<?php endif; ?>
		</tbody>
	</table>
	</div><?php
	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
		wp_die();
	}
}
add_action('wp_ajax_epl_contact_get_listing_table','epl_contact_get_listings_html',10,5);

/**
 * Contact Add Activity Form
 *
 * @param $contact
 * @since 3.0
 */
function epl_contact_add_activity_form( $contact ) {
	$form_builder = new EPL_FORM_BUILDER();
	$listing_ids = $contact->listing_ids;
	$listings_opts = array(''   =>  __('No Listing') );
	if( !empty($listing_ids) ) {
		$listings    = get_posts( array( 'post__in' => $listing_ids, 'post_type'	=>	epl_all_post_types() ) );
			if( !empty($listings) ) :
			foreach($listings as $listing) :
				$listings_opts[$listing->ID] = $listing->post_title;
			endforeach;
		endif;
	}
	$fields = array(
		array(
			'label'		=>	__('Add Activity' , 'easy-property-listings' ),
			'class'		=>	'col-1 epl-inner-div',
			'id'		=>	'epl-contact-add-activity-wrap',
			'help'		=>	'<hr/>',
			'fields'	=>	array(
				array(
					'name'		=>	'epl_contact_activity_content',
					'class'		=>	'epl-contact-note-input',
					'type'		=>	'textarea',
				),
				array(
					'name'		=>	'epl_contact_activity_type',
					'class'		=>	'epl-contact-note-select',
					'type'		=>	'select',
					'opts'		=>	$contact->get_activity_types()
				),
				array(
					'name'		=>	'epl_contact_activity_listing',
					'class'		=>	'epl-contact-note-select',
					'type'		=>	'select',
					'opts'		=>	$listings_opts
				),
				array(
					'name'		=>	'epl_contact_activity_submit',
					'value'		=>	__('Add','easy-property-listings' ),
					'class'		=>	'button button-primary',
					'type'		=>	'submit',
				),
			)
		),
	);
	$form_builder->set_form_attributes( array('name'    =>  'epl_contact_add_activity_form', 'id'    =>  'epl_contact_add_activity_form') );
	$form_builder->add_nonce('add_contact_note_nonce');
	$form_builder->add_sections($fields);
	echo '<div class="epl-contact-add-activity-form-wrap">';
		$form_builder->render_form();
	echo '</div>';
}
add_action('epl_contact_add_activity_form','epl_contact_add_activity_form');

/**
 * Contact Add Listing Form
 *
 * @param $contact
 * @since 3.0
 */
function epl_contact_add_listing_form( $contact ) {
	global $epl_settings;
	$form_builder = new EPL_FORM_BUILDER();
	$listing_types = epl_get_active_post_types();
	$fields = array(
		array(
			'label'		=>	__('Add Listing' , 'easy-property-listings' ),
			'class'		=>	'col-1 epl-inner-div',
			'id'		=>	'epl-contact-add-listing-wrap',
			'help'		=>	'<hr/>',
			'fields'	=>	array(

				array(
					'name'		=>	'property_owner',
					'label'		=>	'',
					'type'		=>	'hidden',
					'value'		=>	$contact->id,
				),
				array(
					'name'		=>	'property_address_lot_number',
					'label'		=>	__('Lot', 'easy-property-listings' ),
					'type'		=>	'text',
					'maxlength'	=>	'40',
					'include'	=>	array('land', 'commercial_land')
				),

				array(
					'name'		=>	'property_address_sub_number',
					'label'		=>	__('Unit', 'easy-property-listings' ),
					'type'		=>	'text',
					'maxlength'	=>	'40',
					'exclude'	=>	array('land', 'commercial_land')
				),

				array(
					'name'		=>	'property_address_street_number',
					'label'		=>	__('Street Number', 'easy-property-listings' ),
					'type'		=>	'text',
					'maxlength'	=>	'40'
				),

				array(
					'name'		=>	'property_address_street',
					'label'		=>	__('Street Name', 'easy-property-listings' ),
					'type'		=>	'text',
					'maxlength'	=>	'80'
				),

				array(
					'name'		=>	'property_address_suburb',
					'label'		=>	epl_labels('label_suburb'),
					'type'		=>	'text',
					'maxlength'	=>	'80'
				),

				array(
					'name'		=>	'property_address_state',
					'label'		=>	epl_labels('label_state'),
					'type'		=>	'text',
					'maxlength'	=>	'80'
				),

				array(
					'name'		=>	'property_address_postal_code',
					'label'		=>	epl_labels('label_postcode'),
					'type'		=>	'text',
					'maxlength'	=>	'30'
				),

				array(
					'name'		=>	'property_address_country',
					'label'		=>	__('Country', 'easy-property-listings' ),
					'type'		=>	'text',
					'maxlength'	=>	'40'
				),
				array(
					'name'		=>	'post_title',
					'label'		=>	__('Full Address','easy-property-listings' ),
					'type'		=>	'text',
				),
				array(
					'name'		=>	'post_type',
					'label'		=>	__('Listing Type','easy-property-listings' ),
					'type'		=>	'select',
					'class'		=>	'epl-contact-note-select',
					'opts'		=>	$listing_types,
					'maxlength'	=>	'200',
				),
				array(
					'name'		=>	'property_status',
					'label'		=>	__('Listing Status','easy-property-listings' ),
					'type'		=>	'select',
					'class'		=>	'epl-contact-note-select',
					'opts'		=> apply_filters('epl_contact_property_status', array(
						'current' 	=>  __('Current','easy-property-listings' ),
						'sold'       	=>  __('Sold','easy-property-listings' ),
						'leased'       	=>  __('Leased','easy-property-listings' ),
					)),
					'maxlength'	=>	'200',
				),

				array(
					'name'		=>	'contact_listing_submit',
					'value'		=>	__('Add','easy-property-listings' ),
					'class'		=>	'button button-primary',
					'type'		=>	'submit',
				),
			)
		),
	);
	$form_builder->set_form_attributes( array('name'    =>  'epl_contact_add_listing_form', 'id'    =>  'epl_contact_add_listing_form') );
	$form_builder->add_nonce('add_contact_listing_nonce');
	$form_builder->add_sections($fields);
	echo '<div class="epl-contact-add-listing-form-wrap">';
	$form_builder->render_form();
	echo '</div>';
}
add_action('epl_contact_add_listing_form','epl_contact_add_listing_form');

/**
 * Displays Contact details in listing owner meta box
 *
 * @param $post
 * @param $value
 * @since 3.0
 */
function epl_before_meta_field_property_owner($post,$value) {

	if(intval($value) == 0 )
		return;

	$url = admin_url('admin.php?page=epl-contacts&view=overview&id='.$value);
	$contact = new EPL_Contact($value);

	if( !$contact || $contact->ID <= 0 )
		return;

	echo '<tr class="form-field"><td>';
	echo '
			<div class="epl-listing-owner-details">
				<div class="epl-listing-owner-grav">
					'.get_avatar( $contact->email , apply_filters('epl_contact_gravatar_size',160) ).'
				</div>
				<div class="epl-listing-owner-mail">
					'.$contact->get_emails().'
				</div>
				<div class="epl-listing-owner-heading">
					'.$contact->heading.'
				</div>
				<a class="epl-listing-contact-url" href="'.$url.'">'.
					__('View Contact').'
				</a>
			</div>';
	echo '</td></tr>';
}
add_action('epl_before_meta_field_property_owner','epl_before_meta_field_property_owner',10,2);

/**
 * Search Contact
 *
 * @since 3.0
 */
function epl_search_contact() {

	$search_array = array(
		's'				=> $_POST['user_name'],
		'showposts'   			=> 6,
		'post_type' 			=> 'epl_contact',
		'post_status' 			=> 'publish',
		'post_password' 		=> '',
		'suppress_filters' 		=> true
	);

	$query = http_build_query($search_array);

	$listings = get_posts(  $query );

	if( !empty($listings) ) {
		echo '<ul class="epl-contact-listing-suggestion">';
		foreach( $listings as  $listing) {
			echo '<li data-id="'.$listing->ID.'">'.get_post_meta($listing->ID,"contact_first_name",true).' '.get_post_meta($listing->ID,"contact_last_name",true).'</li>';
		}
		echo '</ul>';
	}
	wp_die();
}
add_action('wp_ajax_epl_search_contact','epl_search_contact');

/**
 * Search Listing
 *
 * @since 3.0
 */
function epl_search_contact_listing() {

	$search_array = array(
		's'				=> sanitize_text_field($_POST['s']),
		'showposts'   			=> 6,
		'post_type' 			=> epl_get_core_post_types(),
		'post_status' 			=> 'publish',
	);

	$query = http_build_query($search_array);

	$listings = get_posts(  $query );

	if( !empty($listings) ) {
		echo '<ul class="epl-contact-owned-listing-suggestion striped">';
		foreach( $listings as  $listing) {
			$status = get_post_meta($listing->ID,'property_status',true);
			echo '<li data-id="'.$listing->ID.'"><span class="epl-contact-listing-type">'.$listing->post_type.'</span>'.$listing->post_title.'<span class="epl-contact-listing-status type_'.$status.'">'.$status.'</span></li>';
		}
		echo '</ul>';
	}
	wp_die();
}
add_action('wp_ajax_epl_search_contact_listing','epl_search_contact_listing');

/**
 * Search Users
 *
 * @since 3.0
 */
function epl_search_user() {
	$users = get_users(
			array(
				'search'       =>  $_REQUEST['user_name']. '*',
				'number'       =>  5
			)
		);

	if( !empty($users) && !is_wp_error($users) ) {
		ob_start();
		echo '<ul class="epl-contact-user-suggestion">';
		foreach( $users as  $user) {
			echo '<li data-uname="'.$user->data->user_login.'" data-id="'.$user->ID.'">'.$user->data->display_name.'</li>';
		}
		echo '</ul>';
		echo ob_get_clean();
	}
	exit;
}
add_action('wp_ajax_epl_search_user','epl_search_user');

/**
 * Save a customer note being added
 *
 * @since  3.0
 * @param  array $args The $_POST array being passeed
 * @return object         the comment object
 */
function epl_contact_save_note_note_tab( $args ) {

	if ( ! is_admin() || ! epl_contact_access() ) {
		wp_die( __( 'You do not have permission to save note.', 'easy-property-listings'  ) );
	}
	if ( empty( $args ) ) {
		return;
	}
	$contact_note 	= trim( sanitize_text_field( $args['contact_note'] ) );
	$listing_id 	= trim( sanitize_text_field( $args['listing_id'] ) );
	$note_type 		= trim( sanitize_text_field( $args['activity_type'] ) );

	$contact_id   = (int)$args['contact_id'];
	$nonce         = $args['add_contact_note_nonce'];
	if ( ! wp_verify_nonce( $nonce, 'add-contact-note' ) ) {
		wp_die( __( 'Cheatin\' eh?!', 'easy-property-listings'  ) );
	}
	if ( empty( $contact_note ) ) {
		epl_set_error( 'empty-contact-note', __( 'A note is required', 'easy-property-listings'  ) );
	}
	if ( epl_get_errors() ) {
		epl_set_error();
		return;
	}
	do_action( 'epl_pre_insert_contact_note', $contact_id, $new_note, $listing_id, $note_type );

	$contact = new EPL_contact( $contact_id );
	$note_object = $contact->add_note( $contact_note,$note_type,$listing_id );

	if ( ! empty( $note_object ) && ! empty( $contact->id ) ) {
		ob_start();
		?>
		<div class="epl-contact-note-wrapper dashboard-comment-wrap comment-item epl-admin-note">
			<span class="epl-note-content-meta">
				<span class="epl-note-for-listing">
					<?php
					echo isset($note_object->comment_post_ID) ? get_the_title($note_object->comment_post_ID) : '';
					?>
				 </span>
				 <span class="epl-note-time">
					<?php
					echo date_i18n( get_option( 'date_format' ), strtotime( $note_object->comment_date ) );
					?>
				 </span>
			</span>
			<span class="epl-note-content-wrap">
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
add_action( 'epl_add-contact-note-note-tab', 'epl_contact_save_note_note_tab', 10, 1 );
