<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Contacts Page
 *
 * Renders the contacts page contents.
 *
 * @since  2.4
 * @return void
*/
function epl_contacts_page() {
	$default_views = epl_contact_views();
	$requested_view = isset( $_GET['view'] ) ? sanitize_text_field( $_GET['view'] ) : 'contacts';
	if ( array_key_exists( $requested_view, $default_views ) && function_exists( $default_views[$requested_view] ) ) {
		epl_render_contact_view( $requested_view, $default_views );
	} else {
		epl_contacts_list();
	}
}

/**
 * Register the views for contact management
 *
 * @since  2.4
 * @return array Array of views and their callbacks
 */
function epl_contact_views() {

	$views = array();
	return apply_filters( 'epl_contact_views', $views );

}

/**
 * Register the tabs for contact management
 *
 * @since  2.4
 * @return array Array of tabs for the contact
 */
function epl_contact_tabs() {

	$tabs = array();
	return apply_filters( 'epl_contact_tabs', $tabs );

}

/**
 * List table of contacts
 *
 * @since  2.4
 * @return void
 */
function epl_contacts_list() {
	include( dirname( __FILE__ ) . '/class-contact-table.php' );

	$contacts_table = new EPL_Contact_Reports_Table();
	$contacts_table->prepare_items();
	?>
	<div class="wrap">
		<h2><?php _e( 'Contacts', 'epl' ); ?>
			<a class="add-new-h2" href="<?php echo admin_url('admin.php?page=epl-contacts&view=new-contact'); ?>"><?php _e('Add New','epl'); ?></a>
		</h2>
		<?php do_action( 'epl_contacts_table_top' ); ?>
		<form id="epl-contacts-filter" method="get" action="<?php echo admin_url( 'admin.php?page=epl-contacts' ); ?>">
			<?php
			$contacts_table->search_box( __( 'Search Contacts', 'epl' ), 'epl-contacts' );
			$contacts_table->display();
			?>
			<input type="hidden" name="page" value="epl-contacts" />
			<input type="hidden" name="view" value="contacts" />
		</form>
		<?php do_action( 'epl_contacts_table_bottom' ); ?>
	</div>
	<?php
}

/**
 * Renders the contact view wrapper
 *
 * @since  2.4
 * @param  string $view      The View being requested
 * @param  array $callbacks  The Registered views and their callback functions
 * @return void
 */
function epl_render_contact_view( $view, $callbacks ) {
	
	$render = true;

	$contact_view_role = apply_filters( 'epl_view_contacts_role', 'manage_options' );

	if ( ! current_user_can( $contact_view_role ) ) {
		epl_set_error( 'epl-no-access', __( 'You are not permitted to view this data.', 'epl' ) );
		$render = false;
	}
	if($view == 'new-contact') {
		$callbacks[$view]();
		return;
	}
	
	if ( ! isset( $_GET['id'] ) || ! is_numeric( $_GET['id'] ) ) {
		epl_set_error( 'epl-invalid_contact', __( 'Invalid Contact ID Provided.', 'epl' ) );
		$render = false;
	}

	$contact_id = (int)$_GET['id'];
	$contact    = new EPL_Contact( $contact_id );

	if ( empty( $contact->ID ) ) {
		epl_set_error( 'epl-invalid_contact', __( 'Invalid Contact ID Provided.', 'epl' ) );
		$render = false;
	}

	$contact_tabs = epl_contact_tabs();
	?>

	<div class='wrap'>
		<h2><?php _e( 'Contact Details', 'epl' );?></h2>
		<?php if ( epl_get_errors() ) :?>
			<div class="error settings-error">
				<?php epl_print_errors(); ?>
			</div>
		<?php endif; ?>

		<?php if ( $contact && $render ) : ?>

			<div id="epl-item-tab-wrapper" class="contact-tab-wrapper">
				<ul id="epl-item-tab-wrapper-list" class="ustomer-tab-wrapper-list">
				<?php foreach ( $contact_tabs as $key => $tab ) : ?>
					<?php $active = $key === $view ? true : false; ?>
					<?php $class  = $active ? 'active' : 'inactive'; ?>

					<?php if ( ! $active ) : ?>
					<a title="<?php echo esc_attr( $tab['title'] ); ?>" aria-label="<?php echo esc_attr( $tab['title'] ); ?>" href="<?php echo esc_url( admin_url( 'admin.php?page=epl-contacts&view=' . $key . '&id=' . $contact->id ) ); ?>">
					<?php endif; ?>

					<li class="<?php echo sanitize_html_class( $class ); ?>"><span class="dashicons <?php echo sanitize_html_class( $tab['dashicon'] ); ?>"></span></li>

					<?php if ( ! $active ) : ?>
					</a>
					<?php endif; ?>

				<?php endforeach; ?>
				</ul>
			</div>

			<div id="epl-item-card-wrapper" class="epl-contact-card-wrapper" style="float: left">
				<?php $callbacks[$view]( $contact ) ?>
			</div>

		<?php endif; ?>

	</div>
	<?php

}

/**
 * Create a contact
 *
 * @since  2.4
 * @return void
 */
function epl_new_contact_view() { ?>

	<?php
		if( isset($_GET['id']) ) {
			$redirect = admin_url( 'admin.php?page=epl-contacts&view=meta&id=' . $contact_id );
			wp_redirect( $redirect );
			exit;
		}
	?>
	<div class='wrap'>
		<h2><?php _e( 'Add Contact', 'epl' );?></h2>
		<?php if ( epl_get_errors() ) :?>
			<div class="error settings-error">
				<?php epl_print_errors(); ?>
			</div>
		<?php endif; ?>

		<div id="epl-item-tab-wrapper" class="contact-tab-wrapper">
		</div>

		<div id="epl-item-card-wrapper" class="epl-contact-card-wrapper" style="float: left">
		
				<div class="info-wrapper contact-section">
					<?php
					
						$args = array(
							'post_type'		=>	'epl_contact',
							'post_status'	=>	'auto-draft',
							'post_title'	=>	__('Contact Name','epl')
						);
						$contact_id = wp_insert_post($args);
						epl_var_dump($contact_id);
						if($contact_id ):
					

					?>

					<form id="meta-contact" method="post" action="<?php echo admin_url( 'admin.php?page=epl-contacts&view=new-contact&id=' . $contact_id ); ?>">

						<div class="contact-info meta-contact">
							<?php do_action( 'epl_new_contact_fields'); ?>
				
							<?php
										$contact = new EPL_Contact( $contact_id );
										$contact_meta_fields = new EPL_FORM_BUILDER();
										$contact_fields	=	apply_filters('epl_contact_new_fields',
											array(

												array(
													'name'		=>	'name',
													'label'		=>	__('Contact Name','epl'),
													'type'		=>	'text',
													'maxlength'	=>	'60',
													'value'		=>	$contact->name
												),
			
												array(
													'name'		=>	'email',
													'label'		=>	__('Contact Email','epl'),
													'type'		=>	'email',
													'maxlength'	=>	'60',
													'value'		=>	$contact->get_meta('lead_email')
												),
			
											) 
										);
										
										$contact_meta_fields->add_fields($contact_fields);
										$contact_meta_fields->render_form();

							?>

									<span id="contact-edit-actions">
										<input type="hidden" name="contact_id" value="<?php echo $contact->ID; ?>" />
										<input type="hidden" name="ID" value="<?php echo $contact->ID; ?>" />
										<?php wp_nonce_field( 'new-contact', '_wpnonce', false, true ); ?>
										<input type="hidden" name="epl_action" value="new-contact" />
										<input type="submit" id="epl-new-contact" class="button-primary" value="<?php _e( 'Create', 'epl' ); ?>" />
									</span>

						</div>

					</form>
					<?php endif; ?>

				</div>
		</div>

	</div>
	<?php
	
}

/**
 * View a contact
 *
 * @since  2.4
 * @param  $contact The Contact object being displayed
 * @return void
 */
function epl_contacts_view( $contact ) {

	$contact_edit_role = apply_filters( 'epl_edit_contacts_role', 'manage_options' );

	?>

	<?php do_action( 'epl_contact_card_top', $contact ); ?>

	<?php do_action('epl_pre_contact_quick_edit_options', $contact); ?>

	<?php do_action('epl_contact_action_menus',$contact); ?>

	<?php do_action('epl_contact_entry_header',$contact); ?>

	<?php do_action('epl_contact_assigned_tags',$contact); ?>

	<?php do_action('epl_post_contact_quick_edit_options', $contact); ?>


	<div class="info-wrapper contact-section">
		<input type="hidden" id="epl_contact_id" value="<?php echo $contact->id; ?>"/>

			<div class="epl-item-info contact-info">

				<?php do_action('epl_contact_avatar',$contact); ?>

				<div class="contact-id right">
					<?php do_action('epl_contact_social_icons',$contact); ?>
				</div>


				<div class="contact-main-wrapper left">

					<?php do_action('epl_contact_contact_details',$contact); ?>
					<span class="contact-since info-item">
						<?php _e( 'Contact since', 'epl' ); ?>
						<?php echo date_i18n( get_option( 'date_format' ), strtotime( $contact->date_created ) ) ?>
					</span>

				</div>

			</div>

	</div>

	<?php do_action( 'epl_contact_before_tables_wrapper', $contact ); ?>

	<?php do_action( 'epl_contact_background_info', $contact ); ?>

	<div id="epl-item-tables-wrapper" class="contact-tables-wrapper contact-section">

		<?php do_action( 'epl_contact_before_tables', $contact ); ?>

		<?php do_action('epl_contact_recent_activities',$contact); ?>

		<?php //do_action('epl_contact_recent_interests',$contact); ?>

		<?php do_action( 'epl_contact_after_tables', $contact ); ?>

	</div>
	<?php do_action( 'epl_contact_card_bottom', $contact ); ?>

	<?php
}


/**
 * View the meta of a contact
 *
 * @since  2.4
 * @param  $contact The Contact being displayed
 * @return void
 */
function epl_contact_meta_view($contact) {

	$contact_edit_role = apply_filters( 'epl_edit_contacts_role', 'manage_options' );

	?>

	<?php do_action( 'epl_contact_edit_top', $contact ); ?>

	<?php do_action('epl_pre_contact_quick_edit_options', $contact); ?>

	<?php do_action('epl_contact_action_menus',$contact); ?>

	<?php do_action('epl_contact_entry_header',$contact); ?>

	<?php do_action('epl_contact_assigned_tags',$contact); ?>

	<?php do_action('epl_post_contact_quick_edit_options', $contact); ?>


	<div class="info-wrapper contact-section">

		<div class="epl-item-info contact-info">

			<?php do_action('epl_contact_avatar',$contact); ?>

			<div class="contact-edit-main-wrapper left">
				<form id="meta-contact" method="post" action="<?php echo admin_url( 'admin.php?page=epl-contacts&view=meta&id=' . $contact->ID ); ?>">

					<input type="hidden" id="epl_contact_id" value="<?php echo $contact->id; ?>"/>


					<div class="contact-info meta-contact">
						<?php do_action( 'epl_contact_meta_fields', $contact ); ?>

						<?php
							$contact_meta_fields = new EPL_FORM_BUILDER();

							$fields = array(
								array(
									'class'		=>	'col-1 epl-inner-div',
									'id'		=>	'epl-contact-basic-details',
									'fields'	=>	apply_filters('epl_contact_basic_fields',
										array(
											array(
												'name'		=>	'contact_first_name',
												'type'		=>	'text',
												'maxlength'	=>	'60',
												'value'		=>	$contact->get_meta('contact_first_name')
											),

											array(
												'name'		=>	'contact_last_name',
												'type'		=>	'text',
												'maxlength'	=>	'60',
												'value'		=>	$contact->get_meta('contact_last_name')
											),

										)
									)
								),
								array(
									'label'		=>	__('' , 'epl'),
									'class'		=>	'col-1 epl-inner-div',
									'id'		=>	'epl-contact-professional-details',
									'help'		=>	__('Professional Details' , 'epl') . '<hr/>',
									'fields'	=>	apply_filters('epl_contact_professional_fields',
										array(
											array(
												'name'		=>	'post_title',
												'label'		=>	__('Title','epl'),
												'type'		=>	'text',
												'value'		=>	$contact->name
											),
											array(
												'name'		=>	'contact_company',
												'label'		=>	__('Company','epl'),
												'type'		=>	'text',
												'value'		=>	$contact->get_meta('contact_company')
											),
										)
									)
								),
								array(
									'label'		=>	__('' , 'epl'),
									'class'		=>	'col-1 epl-inner-div',
									'id'		=>	'epl-contact-contact-details',
									'help'		=>	__('Contact Details' , 'epl') . '<hr/>',
									'fields'	=>	apply_filters('epl_contact_contact_fields',
										array(
											array(
												'name'		=>	'contact_phone',
												'label'		=>	__('Phone','epl'),
												'type'		=>	'text',
												'maxlength'	=>	'60',
												'value'		=>	$contact->get_meta('contact_phone')
											),

											array(
												'name'		=>	'contact_mobile',
												'label'		=>	__('Mobile','epl'),
												'type'		=>	'text',
												'maxlength'	=>	'60',
												'value'		=>	$contact->get_meta('contact_mobile')
											),

											array(
												'name'		=>	'contact_email',
												'label'		=>	__('Email','epl'),
												'type'		=>	'email',
												'maxlength'	=>	'60',
												'value'		=>	$contact->get_meta('contact_email')
											),
											array(
												'name'		=>	'contact_website',
												'label'		=>	__('Website','epl'),
												'type'		=>	'text',
												'maxlength'	=>	'60',
												'value'		=>	$contact->get_meta('contact_website')
											),
										)
									)
								),
								array(
									'label'		=>	__('' , 'epl'),
									'class'		=>	'col-1 epl-inner-div',
									'id'		=>	'epl-contact-custom-details',
									'help'		=>	__('Custom Fields' , 'epl') . '<hr/>',
									'fields'	=>	apply_filters('epl_contact_custom_fields',
										array(
											array(
												'name'		=>	'contact_referred_by',
												'label'		=>	__('Referred By','epl'),
												'type'		=>	'text',
												'maxlength'	=>	'60',
												'value'		=>	$contact->get_meta('contact_referred_by')
											),
											array(
												'name'		=>	'contact_custom_1',
												'label'		=>	__('Custom Field 1','epl'),
												'type'		=>	'text',
												'maxlength'	=>	'60',
												'value'		=>	$contact->get_meta('contact_custom_1')
											),
											array(
												'name'		=>	'contact_custom_2',
												'label'		=>	__('Custom Field 2','epl'),
												'type'		=>	'text',
												'maxlength'	=>	'60',
												'value'		=>	$contact->get_meta('contact_custom_2')
											),
										)
									)
								),
								array(
									'label'		=>	__('' , 'epl'),
									'class'		=>	'col-1 epl-inner-div',
									'id'		=>	'epl-contact-social-details',
									'help'		=>	__('Social Networks' , 'epl') . '<hr/>',
									'fields'	=>	apply_filters('epl_contact_social_fields',
										array(

											array(
												'name'		=>	'contact_facebook',
												'label'		=>	__('Facebook','epl'),
												'type'		=>	'text',
												'maxlength'	=>	'60',
												'value'		=>	$contact->get_meta('contact_facebook')
											),
											array(
												'name'		=>	'contact_twitter',
												'label'		=>	__('Twitter','epl'),
												'type'		=>	'text',
												'maxlength'	=>	'60',
												'value'		=>	$contact->get_meta('contact_twitter')
											),
											array(
												'name'		=>	'contact_google_plus',
												'label'		=>	__('Google Plus','epl'),
												'type'		=>	'text',
												'maxlength'	=>	'60',
												'value'		=>	$contact->get_meta('contact_google_plus')
											),
											array(
												'name'		=>	'contact_linked_in',
												'label'		=>	__('Linked In','epl'),
												'type'		=>	'text',
												'maxlength'	=>	'60',
												'value'		=>	$contact->get_meta('contact_linked_in')
											),


										)
									)
								),
								array(
									'label'		=>	__('' , 'epl'),
									'class'		=>	'col-1 epl-inner-div',
									'id'		=>	'epl-contact-address-details',
									'help'		=>	__('Address' , 'epl') . '<hr/>',
									'fields'	=>	array(
										array(
											'name'		=>	'contact_street_number',
											'label'		=>	__('Street Number','epl'),
											'type'		=>	'text',
											'maxlength'	=>	'200',
											'value'		=>	$contact->get_meta('contact_street_number')
										),
										array(
											'name'		=>	'contact_street_name',
											'label'		=>	__('Street Name','epl'),
											'type'		=>	'text',
											'maxlength'	=>	'200',
											'value'		=>	$contact->get_meta('contact_street_name')
										),
										array(
											'name'		=>	'contact_suburb',
											'label'		=>	__('Suburb','epl'),
											'type'		=>	'text',
											'maxlength'	=>	'200',
											'value'		=>	$contact->get_meta('contact_suburb')
										),
										array(
											'name'		=>	'contact_state',
											'label'		=>	__('State','epl'),
											'type'		=>	'text',
											'maxlength'	=>	'200',
											'value'		=>	$contact->get_meta('contact_state')
										),
										array(
											'name'		=>	'contact_postcode',
											'label'		=>	__('Postcode','epl'),
											'type'		=>	'text',
											'maxlength'	=>	'200',
											'value'		=>	$contact->get_meta('contact_postcode')
										),
										array(
											'name'		=>	'contact_country',
											'label'		=>	__('Country','epl'),
											'type'		=>	'text',
											'maxlength'	=>	'200',
											'value'		=>	$contact->get_meta('contact_country')
										),
									)
								),
								array(
									'label'		=>	__('' , 'epl'),
									'class'		=>	'col-1 epl-inner-div',
									'id'		=>	'epl-contact-bginfo-details',
									'help'		=>	__('Background Info' , 'epl') . '<hr/>',
									'fields'	=>	apply_filters('epl_contact_background_info_fields',
										array(
											array(
												'name'		=>	'post_content',
												'type'		=>	'textarea',
												'value'		=>	$contact->background_info
											),

										)
									)
								),


							);
							$contact_meta_fields->add_sections($fields);
							$contact_meta_fields->render_form();


						?>

						<span id="contact-edit-actions">
							<input type="hidden" name="contact_id" value="<?php echo $contact->ID; ?>" />
							<?php wp_nonce_field( 'meta-contact', '_wpnonce', false, true ); ?>
							<input type="hidden" name="epl_action" value="meta-contact" />
							<input type="submit" id="epl-meta-contact" class="button-primary" value="<?php _e( 'Update', 'epl' ); ?>" />
						</span>

					</div>

				</form>


			</div>

		</div>


	</div>

	<?php

}

/**
 * View the notes of a contact
 *
 * @since  2.4
 * @param  $contact The Contact being displayed
 * @return void
 */
function epl_contact_notes_view( $contact ) {

	$paged       = isset( $_GET['paged'] ) && is_numeric( $_GET['paged'] ) ? $_GET['paged'] : 1;
	$paged       = absint( $paged );
	$note_count  = $contact->get_notes_count();
	$per_page    = apply_filters( 'epl_contact_notes_per_page', 20 );
	$total_pages = ceil( $note_count / $per_page );

	$contact_notes = $contact->get_notes( $per_page, $paged );
	?>

	<div id="epl-item-notes-wrapper">
		<div class="epl-item-notes-header">
			<?php echo get_avatar( $contact->email, 30 ); ?> <span><?php echo $contact->name; ?></span>
		</div>
		<h3><?php _e( 'Notes', 'epl' ); ?></h3>

		<?php if ( 1 == $paged ) : ?>
		<div style="display: block; margin-bottom: 35px;">
			<form id="epl-add-contact-note" method="post" action="<?php echo admin_url( 'admin.php?page=epl-contacts&view=notes&id=' . $contact->id ); ?>">
				<textarea id="contact-note" name="contact_note" class="contact-note-input" rows="10"></textarea>
				<br />
				<input type="hidden" id="contact-id" name="contact_id" value="<?php echo $contact->id; ?>" />
				<input type="hidden" name="epl_action" value="add-contact-note" />
				<br />
				<label class ="contact-select-label" > 
					<select class="contact-note-select" id="contact-note-listing">
						<option value=''><?php _e('Note for Listing', 'epl'); ?></option>
						<?php
							if( !empty($contact->listing_ids) ) {
								foreach( $contact->listing_ids as $listing_id ) {
									echo '<option value="'.$listing_id.'">'.get_the_title($listing_id).'</option>';
								}
							}
						?>
					</select>
				</label>
				<label class ="contact-select-label" > 
					<select class="contact-note-select" id="contact-note-notify">
						<option value='no'><?php _e('Private Note', 'epl'); ?></option>
						<option value='yes'><?php _e('Notify to Contact', 'epl'); ?></option>
					</select>
				</label>
				<br />
				<?php wp_nonce_field( 'add-contact-note', 'add_contact_note_nonce', true, true ); ?>
				<input id="add-contact-note" class="right button-primary" type="submit" value="Add Note" />
			</form>
		</div>
		<?php endif; ?>

		<?php
		$pagination_args = array(
			'base'     => '%_%',
			'format'   => '?paged=%#%',
			'total'    => $total_pages,
			'current'  => $paged,
			'show_all' => true
		);

		?>

		<div id="epl-contact-notes">
		<?php if ( count( $contact_notes ) > 0 ) : ?>
			<?php foreach( $contact_notes as $key => $note ) : ?>
				<div class="contact-note-wrapper dashboard-comment-wrap comment-item <?php echo $note->comment_type; ?>" >
					<span class="note-content-meta">
						<span class="note-for-listing">
							<?php 
								echo isset($note->post_title) ? $note->post_title : '';
							 ?>
						 </span>
						 <span class="note-time">
							<?php 
								echo date_i18n( get_option( 'date_format' ), strtotime( $note->comment_date ) );
							 ?>
						 </span>
					</span>
					<span class="note-content-wrap">
						<?php echo stripslashes( $note->comment_content ); ?>
					</span>
				</div>
			<?php endforeach; ?>
		<?php else: ?>
			<div class="epl-no-contact-notes">
				<?php _e( 'No Contact Notes', 'epl' ); ?>
			</div>
		<?php endif; ?>
		</div>
		<div class="epl-note-pagination">
			<?php 
				echo paginate_links( $pagination_args ); 
			?>
		</div>

	</div>

	<?php
}

function epl_contacts_delete_view( $contact ) {
	$contact_edit_role = apply_filters( 'epl_edit_contacts_role', 'manage_options' );

	?>

	<?php do_action( 'epl_contact_delete_top', $contact ); ?>

	<div class="info-wrapper contact-section">

		<form id="delete-contact" method="post" action="<?php echo admin_url( 'admin.php?page=epl-contacts&view=delete&id=' . $contact->id ); ?>">

				<div class="epl-item-notes-header">
				<?php echo get_avatar( $contact->email, 30 ); ?> <span><?php echo $contact->name; ?></span>
			</div>


			<div class="contact-info delete-contact">

				<span class="delete-contact-options">
					<p>
						<input type="checkbox" class="epl-checkbox epl-contact-delete-confirm" id="epl-contact-delete-confirm" name="epl-contact-delete-confirm">
						<label for="epl-contact-delete-confirm"><?php _e( 'Are you sure you want to delete this contact?', 'epl' ); ?></label>
					</p>

					<?php do_action( 'epl_contact_delete_inputs', $contact ); ?>
				</span>

				<span id="contact-edit-actions">
					<input type="hidden" name="contact_id" value="<?php echo $contact->id; ?>" />
					<?php wp_nonce_field( 'delete-contact', '_wpnonce', false, true ); ?>
					<input type="hidden" name="epl_action" value="delete-contact" />
					<input type="submit" disabled="disabled" id="epl-delete-contact" class="button-primary" value="<?php _e( 'Delete Contact', 'epl' ); ?>" />
					<a id="epl-delete-contact-cancel" href="<?php echo admin_url( 'admin.php?page=epl-contacts&view=overview&id=' . $contact->id ); ?>" class="delete"><?php _e( 'Cancel', 'epl' ); ?></a>
				</span>

			</div>

		</form>
	</div>

	<?php

	do_action( 'epl_contact_delete_bottom', $contact );
}

/**
 * view contact listings
 * @param $contact
 */
function epl_contacts_listing_view( $contact ) {

	?>

	<?php do_action( 'epl_contact_card_top', $contact ); ?>

	<?php do_action('epl_pre_contact_quick_edit_options', $contact); ?>

	<?php do_action('epl_contact_action_menus',$contact); ?>

	<?php do_action('epl_contact_entry_header',$contact); ?>

	<?php do_action('epl_contact_assigned_tags',$contact); ?>

	<?php do_action('epl_post_contact_quick_edit_options', $contact); ?>


	<div class="info-wrapper contact-section">
		<input type="hidden" id="epl_contact_id" value="<?php echo $contact->id; ?>"/>

		<div class="epl-item-info contact-info">

			<?php do_action('epl_contact_avatar',$contact); ?>

			<div class="contact-id right">
				<?php do_action('epl_contact_social_icons',$contact); ?>
			</div>


			<div class="contact-main-wrapper left">

				<?php do_action('epl_contact_contact_details',$contact); ?>
				<span class="contact-since info-item">
						<?php _e( 'Contact since', 'epl' ); ?>
						<?php echo date_i18n( get_option( 'date_format' ), strtotime( $contact->date_created ) ) ?>
					</span>

			</div>

		</div>

	</div>

	<?php do_action( 'epl_contact_before_tables_wrapper', $contact ); ?>

	<?php //do_action( 'epl_contact_background_info', $contact ); ?>

	<div id="epl-item-tables-wrapper" class="contact-tables-wrapper contact-section">

		<?php do_action( 'epl_contact_before_tables', $contact ); ?>

		<?php do_action('epl_contact_recent_interests',$contact); ?>

		<?php do_action( 'epl_contact_after_tables', $contact ); ?>

	</div>
	<?php do_action( 'epl_contact_card_bottom', $contact ); ?>

<?php
}
