<?php
/**
 * Contact Functions
 *
 * @package     EPL
 * @subpackage  Contacts
 * @copyright   Copyright (c) 2016, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Contacts Page
 *
 * Renders the contacts page contents.
 *
 * @since  3.0
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
 * @since  3.0
 * @return array Array of views and their callbacks
 */
function epl_contact_views() {

	$views = array();
	return apply_filters( 'epl_contact_views', $views );
}

/**
 * Register the tabs for contact management
 *
 * @since  3.0
 * @return array Array of tabs for the contact
 */
function epl_contact_tabs() {

	$tabs = array();
	return apply_filters( 'epl_contact_tabs', $tabs );
}

/**
 * List table of contacts
 *
 * @since  3.0
 * @return void
 */
function epl_contacts_list() {
	global $epl_contacts_table;
	$epl_contacts_table->prepare_items();

	?>
	<div class="wrap">
		<h2><?php _e( 'Contacts', 'easy-property-listings'  ); ?>
			<a class="add-new-h2" href="<?php echo admin_url('admin.php?page=epl-contacts&view=new-contact'); ?>"><?php _e('Add New','easy-property-listings' ); ?></a>
			<a class="add-new-h2 epl-contact-tags-page" href="<?php echo admin_url('admin.php?page=epl-contacts&view=all_tags'); ?>"><?php _e('Tags','easy-property-listings' ); ?></a>
		</h2>
		<?php do_action( 'epl_contacts_table_top' ); ?>
		<form id="epl-contacts-filter" method="get" action="<?php echo admin_url( 'admin.php?page=epl-contacts' ); ?>">
			<?php
			$epl_contacts_table->search_box( __( 'Search Contacts', 'easy-property-listings'  ), 'epl-contacts' );
			$epl_contacts_table->display();
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
 * @since  3.0
 * @param  string $view      The View being requested
 * @param  array $callbacks  The Registered views and their callback functions
 * @return void
 */
function epl_render_contact_view( $view, $callbacks ) {

	$render = true;

	if ( ! is_admin() || ! epl_contact_access() ) {
		epl_set_error( 'epl-no-access', __( 'You are not permitted to view this data.', 'easy-property-listings'  ) );
		$render = false;
	}
	if($view == 'new-contact' || $view == 'all_tags') {
		$callbacks[$view]();
		return;
	}

	if ( ! isset( $_GET['id'] ) || ! is_numeric( $_GET['id'] ) ) {
		epl_set_error( 'epl-invalid_contact', __( 'Invalid Contact ID Provided.', 'easy-property-listings'  ) );
		$render = false;
	}

	$contact_id = (int)$_GET['id'];
	$contact    = new EPL_Contact( $contact_id );

	if ( empty( $contact->ID ) ) {
		epl_set_error( 'epl-invalid_contact', __( 'Invalid Contact ID Provided.', 'easy-property-listings'  ) );
		$render = false;
	}

	$contact_tabs = epl_contact_tabs();
	?>

	<div class='wrap'>
		<h2><?php _e( 'Contact Details', 'easy-property-listings'  );?></h2>
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
 * Contacts Tags UI
 *
 * @since  3.0
 * @return void
 */
function epl_all_tags_view() {
	include_once('contact-tags-ui.php');
}

/**
 * Create a contact
 *
 * @since  3.0
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
		<h2><?php _e( 'Add Contact', 'easy-property-listings'  );?></h2>
		<?php if ( epl_get_errors() ) :?>
			<div class="error settings-error">
				<?php epl_print_errors(); ?>
			</div>
		<?php endif; ?>

		<div id="epl-item-tab-wrapper" class="contact-tab-wrapper">
		</div>

		<div id="epl-item-card-wrapper" class="epl-contact-card-wrapper" style="float: left">

			<div class="epl-info-wrapper epl-contact-section">
				<?php
					$args = array(
						'post_type'	=>	'epl_contact',
						'post_status'	=>	'auto-draft',
						'post_title'	=>	__('Contact Name','easy-property-listings' )
					);
					$contact_id = wp_insert_post($args);
					if($contact_id ):
				?>

				<form id="epl-meta-contact-form" method="post" action="<?php echo admin_url( 'admin.php?page=epl-contacts&view=new-contact&id=' . $contact_id ); ?>">

					<div class="epl-contact-info epl-meta-contact epl-admin-new-contact">
						<?php do_action( 'epl_new_contact_fields'); ?>

						<?php
							$contact = new EPL_Contact( $contact_id );
							$contact_meta_fields = new EPL_FORM_BUILDER();
							$contact_fields	=	apply_filters('epl_contact_new_fields',
								array(

									array(
										'name'		=>	'title',
										'label'		=>	__('Summary','easy-property-listings' ),
										'type'		=>	'text',
										'maxlength'	=>	'60',
										'value'		=>	$contact->name
									),
									array(
										'name'		=>	'first_name',
										'label'		=>	__('First Name','easy-property-listings' ),
										'type'		=>	'text',
										'maxlength'	=>	'60',
										'value'		=>	$contact->get_meta('contact_first_name')
									),
									array(
										'name'		=>	'last_name',
										'label'		=>	__('Last Name','easy-property-listings' ),
										'type'		=>	'text',
										'maxlength'	=>	'60',
										'value'		=>	$contact->get_meta('contact_last_name')
									),
									array(
										'name'		=>	'email',
										'label'		=>	__('Email','easy-property-listings' ),
										'type'		=>	'email',
										'maxlength'	=>	'60',
										'value'		=>	$contact->get_primary_email($contact->ID)
									),
									array(
										'name'		=>	'phone',
										'label'		=>	__('Phone','easy-property-listings' ),
										'type'		=>	'text',
										'maxlength'	=>	'60',
										'value'		=>	''
									),

								)
							);

							$contact_meta_fields->add_fields($contact_fields);
							$contact_meta_fields->render_form();
						?>

						<span id="epl-contact-edit-actions">
							<input type="hidden" name="contact_id" value="<?php echo $contact->ID; ?>" />
							<input type="hidden" name="ID" value="<?php echo $contact->ID; ?>" />
							<?php wp_nonce_field( 'new-contact', '_wpnonce', false, true ); ?>
							<input type="hidden" name="epl_action" value="new-contact" />
							<input type="submit" id="epl-new-contact" class="button-primary" value="<?php _e( 'Create', 'easy-property-listings'  ); ?>" />
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
 * @since  3.0
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


	<div class="epl-info-wrapper epl-contact-section epl-contact-single-display">
		<input type="hidden" id="epl_contact_id" value="<?php echo $contact->id; ?>"/>

		<div class="epl-item-info epl-contact-info">

			<?php do_action('epl_contact_avatar',$contact); ?>

			<div class="epl-contact-id right">
				<?php do_action('epl_contact_social_icons',$contact); ?>
			</div>

			<div class="epl-contact-main-wrapper left">
				<?php do_action('epl_contact_contact_details',$contact); ?>
			</div>

		</div>
	</div>

	<?php do_action( 'epl_contact_before_tables_wrapper', $contact ); ?>

	<?php do_action( 'epl_contact_background_info', $contact ); ?>

	<div id="epl-item-tables-wrapper" class="contact-tables-wrapper epl-contact-section">

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
 * @since  3.0
 * @param  $contact The Contact being displayed
 * @return void
 */
function epl_contact_meta_view($contact) {

	$this_user = wp_get_current_user();
	if ( ! is_admin() || ! epl_contact_access() ) {
		wp_die( __( 'You do not have permission to see this page.', 'easy-property-listings'  ) );
	}
	?>

	<?php do_action( 'epl_contact_edit_top', $contact ); ?>

	<?php do_action('epl_pre_contact_quick_edit_options', $contact); ?>

	<?php do_action('epl_contact_action_menus',$contact); ?>

	<form id="epl-meta-contact-form" method="post" action="<?php echo admin_url( 'admin.php?page=epl-contacts&view=meta&id=' . $contact->ID ); ?>">

		<?php do_action('epl_contact_entry_header_editable',$contact); ?>

		<?php do_action('epl_contact_assigned_tags',$contact); ?>

		<?php do_action('epl_post_contact_quick_edit_options', $contact); ?>

		<div class="epl-info-wrapper epl-contact-section">

			<div class="epl-item-info epl-contact-info">

				<?php do_action('epl_contact_avatar',$contact); ?>

				<div class="epl-contact-edit-main-wrapper left">

					<input type="hidden" id="epl_contact_id" value="<?php echo $contact->id; ?>"/>

					<div class="epl-contact-info epl-meta-contact">
						<?php do_action( 'epl_contact_meta_fields', $contact ); ?>

						<?php
							$contact_meta_fields = new EPL_FORM_BUILDER();
							$contact_phones = $contact->get_meta('contact_phones');
							$contact_phone  = isset($contact_phones['phone']) ? $contact_phones['phone'] : '';
							$contact_emails = $contact->get_meta('contact_emails');
							$contact_email  = isset($contact_emails['email']) ? $contact_emails['email'] : '';
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
									'label'		=>	'',
									'class'		=>	'col-1 epl-inner-div',
									'id'		=>	'epl-contact-professional-details',
									'help'		=>	__('Professional Details' , 'easy-property-listings' ) . '<hr/>',
									'fields'	=>	apply_filters('epl_contact_professional_fields',
										array(
											array(
												'name'		=>	'contact_title',
												'label'		=>	__('Title','easy-property-listings' ),
												'type'		=>	'text',
												'value'		=>	$contact->get_meta('contact_title')
											),
											array(
												'name'		=>	'contact_company',
												'label'		=>	__('Company','easy-property-listings' ),
												'type'		=>	'text',
												'value'		=>	$contact->get_meta('contact_company')
											),
										)
									)
								),
								array(
									'label'		=>	'',
									'class'		=>	'col-1 epl-inner-div',
									'id'		=>	'epl-contact-contact-details',
									'help'		=>	__('Contact Details' , 'easy-property-listings' ) . '<hr/>',
									'fields'	=>	apply_filters('epl_contact_contact_fields',
										array(
											array(
												'name'		=>	"contact_phones[phone]",
												'label'		=>	__('Phone','easy-property-listings' ),
												'type'		=>	'text',
												'class'		=>	'epl-contact-addable',
												'maxlength'	=>	'60',
												'value'		=>	$contact_phone
											),

											array(
												'name'		=>	"contact_emails[email]",
												'label'		=>	__('Email','easy-property-listings' ),
												'type'		=>	'email',
												'class'		=>	'epl-contact-addable-email',
												'maxlength'	=>	'60',
												'value'		=>	$contact_email
											),

											array(
												'name'		=>	'contact_website',
												'label'		=>	__('Website','easy-property-listings' ),
												'type'		=>	'text',
												'maxlength'	=>	'60',
												'value'		=>	$contact->get_meta('contact_website')
											),
										),$contact
									)
								),
								array(
									'label'		=>	'',
									'class'		=>	'col-1 epl-inner-div',
									'id'		=>	'epl-contact-custom-details',
									'help'		=>	__('Custom Fields' , 'easy-property-listings' ) . '<hr/>',
									'fields'	=>	apply_filters('epl_contact_custom_fields',
										array(
											array(
												'name'		=>	'contact_referred_by',
												'label'		=>	__('Referred By','easy-property-listings' ),
												'type'		=>	'text',
												'maxlength'	=>	'60',
												'value'		=>	$contact->get_meta('contact_referred_by')
											),
											array(
												'name'		=>	'contact_custom_1',
												'label'		=>	__('Custom Field 1','easy-property-listings' ),
												'type'		=>	'text',
												'maxlength'	=>	'60',
												'value'		=>	$contact->get_meta('contact_custom_1')
											),
											array(
												'name'		=>	'contact_custom_2',
												'label'		=>	__('Custom Field 2','easy-property-listings' ),
												'type'		=>	'text',
												'maxlength'	=>	'60',
												'value'		=>	$contact->get_meta('contact_custom_2')
											),
										)
									)
								),
								array(
									'label'		=>	'',
									'class'		=>	'col-1 epl-inner-div',
									'id'		=>	'epl-contact-social-details',
									'help'		=>	__('Social Networks' , 'easy-property-listings' ) . '<hr/>',
									'fields'	=>	apply_filters('epl_contact_social_fields',
										array(

											array(
												'name'		=>	'contact_facebook',
												'label'		=>	__('Facebook','easy-property-listings' ),
												'type'		=>	'text',
												'maxlength'	=>	'60',
												'value'		=>	$contact->get_meta('contact_facebook')
											),
											array(
												'name'		=>	'contact_twitter',
												'label'		=>	__('Twitter','easy-property-listings' ),
												'type'		=>	'text',
												'maxlength'	=>	'60',
												'value'		=>	$contact->get_meta('contact_twitter')
											),
											array(
												'name'		=>	'contact_google_plus',
												'label'		=>	__('Google Plus','easy-property-listings' ),
												'type'		=>	'text',
												'maxlength'	=>	'60',
												'value'		=>	$contact->get_meta('contact_google_plus')
											),
											array(
												'name'		=>	'contact_linked_in',
												'label'		=>	__('Linked In','easy-property-listings' ),
												'type'		=>	'text',
												'maxlength'	=>	'60',
												'value'		=>	$contact->get_meta('contact_linked_in')
											),
										)
									)
								),
								array(
									'label'		=>	'',
									'class'		=>	'col-1 epl-inner-div',
									'id'		=>	'epl-contact-address-details',
									'help'		=>	__('Address' , 'easy-property-listings' ) . '<hr/>',
									'fields'	=>	array(
										array(
											'name'		=>	'contact_street_number',
											'label'		=>	__('Street Number','easy-property-listings' ),
											'type'		=>	'text',
											'maxlength'	=>	'200',
											'value'		=>	$contact->get_meta('contact_street_number')
										),
										array(
											'name'		=>	'contact_street_name',
											'label'		=>	__('Street Name','easy-property-listings' ),
											'type'		=>	'text',
											'maxlength'	=>	'200',
											'value'		=>	$contact->get_meta('contact_street_name')
										),
										array(
											'name'		=>	'contact_suburb',
											'label'		=>	__('Suburb','easy-property-listings' ),
											'type'		=>	'text',
											'maxlength'	=>	'200',
											'value'		=>	$contact->get_meta('contact_suburb')
										),
										array(
											'name'		=>	'contact_state',
											'label'		=>	__('State','easy-property-listings' ),
											'type'		=>	'text',
											'maxlength'	=>	'200',
											'value'		=>	$contact->get_meta('contact_state')
										),
										array(
											'name'		=>	'contact_postcode',
											'label'		=>	__('Postcode','easy-property-listings' ),
											'type'		=>	'text',
											'maxlength'	=>	'200',
											'value'		=>	$contact->get_meta('contact_postcode')
										),
										array(
											'name'		=>	'contact_country',
											'label'		=>	__('Country','easy-property-listings' ),
											'type'		=>	'text',
											'maxlength'	=>	'200',
											'value'		=>	$contact->get_meta('contact_country')
										),
									)
								),
								array(
									'label'		=>	'',
									'class'		=>	'col-1 epl-inner-div',
									'id'		=>	'epl-contact-wpuser-details',
									'help'		=>	__('Author' , 'easy-property-listings' ) . '<hr/>',
									'fields'	=>	apply_filters('epl_contact_wpuser_info_fields',
										array(
											array(
												'name'		=>	'contact_wp_user',
												'id'		=>	'epl_contact_wp_user',
												'type'		=>	'text',
												'autocomplete'	=>	'off',
												'value'		=>	$contact->get_meta('contact_wp_user')
											),
											array(
												'name'		=>	'contact_wp_user_id',
												'id'		=>	'epl_contact_wp_user_id',
												'type'		=>	'hidden',
												'value'		=>	$contact->get_meta('contact_wp_user_id')
											),
										)
									)
								),
								array(
									'label'		=>	'',
									'class'		=>	'col-1 epl-inner-div',
									'id'		=>	'epl-contact-bginfo-details',
									'help'		=>	__('Background Info' , 'easy-property-listings' ) . '<hr/>',
									'fields'	=>	apply_filters('epl_contact_background_info_fields',
										array(
											array(
												'name'		=>	'post_content',
												'type'		=>	'textarea',
												'value'		=>	$contact->background_info
											),
										)
									)
								)
							);
							$contact_meta_fields->add_sections($fields);
							$contact_meta_fields->render_form();
						?>

						<span id="epl-contact-edit-actions">
							<input type="hidden" name="contact_id" value="<?php echo $contact->ID; ?>" />
							<?php wp_nonce_field( 'meta-contact', '_wpnonce', false, true ); ?>
							<input type="hidden" name="epl_action" value="meta-contact" />
							<input type="submit" id="epl-meta-contact" class="button-primary" value="<?php _e( 'Update', 'easy-property-listings'  ); ?>" />
						</span>

					</div>

				</div>

			</div>


		</div>
	</form>
<?php
}

/**
 * View the notes of a contact
 *
 * @since  3.0
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
		<h3><?php _e( 'Notes', 'easy-property-listings'  ); ?></h3>

		<?php if ( 1 == $paged ) : ?>
		<div style="display: block; margin-bottom: 35px;">
			<form id="epl-add-contact-note" method="post" action="<?php echo admin_url( 'admin.php?page=epl-contacts&view=notes&id=' . $contact->id ); ?>">
				<textarea id="contact-note" name="contact_note" class="epl-contact-note-input" rows="10"></textarea>
				<br />
				<input type="hidden" id="epl-contact-id" name="contact_id" value="<?php echo $contact->id; ?>" />
				<input type="hidden" name="epl_action" value="add-contact-note" />
				<br />
				<label class ="epl-contact-select-label" >
					<select class="epl-contact-note-select" id="contact-note-listing">
						<option value=''><?php _e('Note for Listing', 'easy-property-listings' ); ?></option>
						<?php
							if( !empty($contact->listing_ids) ) {
								foreach( $contact->listing_ids as $listing_id ) {
									echo '<option value="'.$listing_id.'">'.get_the_title($listing_id).'</option>';
								}
							}
						?>
					</select>
				</label>
				<label class ="epl-contact-select-label" >
					<select class="epl-contact-note-select" id="contact-activity-type">
						<?php
							foreach( $contact->get_activity_types() as $act_key	=>	$act_name) {
								echo '<option value="'.$act_key.'">'.$act_name.'</option>';
							}
						?>
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
				<div class="epl-contact-note-wrapper dashboard-comment-wrap comment-item <?php echo $note->comment_type; ?>" >
					<span class="epl-note-content-meta">
						<span class="epl-note-for-listing">
							<?php
								echo isset($note->post_title) ? $note->post_title : '';
							 ?>
						 </span>
						 <span class="epl-note-time">
							<?php
								echo date_i18n( get_option( 'date_format' ), strtotime( $note->comment_date ) );
							 ?>
						 </span>
					</span>
					<span class="epl-note-content-wrap">
						<?php echo stripslashes( $note->comment_content ); ?>
					</span>
				</div>
			<?php endforeach; ?>
		<?php else: ?>
			<div class="epl-no-contact-notes">
				<?php _e( 'No Contact Notes', 'easy-property-listings'  ); ?>
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

/**
 * Contact Delete View
 *
 * @since  3.0
 * @param  $contact The Contact being displayed
 * @return void
 */
function epl_contacts_delete_view( $contact ) {
	$contact_edit_role = apply_filters( 'epl_edit_contacts_role', 'manage_options' );
	?>

	<?php do_action( 'epl_contact_delete_top', $contact ); ?>

	<div class="epl-info-wrapper epl-contact-section">

		<form id="epl-delete-contact-form" method="post" action="<?php echo admin_url( 'admin.php?page=epl-contacts&view=delete&id=' . $contact->id ); ?>">

			<div class="epl-item-notes-header">
				<?php echo get_avatar( $contact->email, 30 ); ?> <span><?php echo $contact->name; ?></span>
			</div>

			<div class="epl-contact-info epl-wrapper-delete-contact">

				<span class="epl-delete-contact-options">
					<p>
						<input type="checkbox" class="epl-checkbox epl-contact-delete-confirm" id="epl-contact-delete-confirm" name="epl-contact-delete-confirm">
						<label for="epl-contact-delete-confirm"><?php _e( 'Are you sure you want to delete this contact?', 'easy-property-listings'  ); ?></label>
					</p>

					<?php do_action( 'epl_contact_delete_inputs', $contact ); ?>
				</span>

				<span id="epl-contact-edit-actions">
					<input type="hidden" name="contact_id" value="<?php echo $contact->id; ?>" />
					<?php wp_nonce_field( 'delete-contact', '_wpnonce', false, true ); ?>
					<input type="hidden" name="epl_action" value="delete-contact" />
					<input type="submit" disabled="disabled" id="epl-delete-contact" class="button-primary" value="<?php _e( 'Delete Contact', 'easy-property-listings'  ); ?>" />
					<a id="epl-delete-contact-cancel" href="<?php echo admin_url( 'admin.php?page=epl-contacts&view=overview&id=' . $contact->id ); ?>" class="delete"><?php _e( 'Cancel', 'easy-property-listings'  ); ?></a>
				</span>

			</div>
		</form>
	</div>
<?php
do_action( 'epl_contact_delete_bottom', $contact );
}

/**
 * View contact listings
 *
 * @since  3.0
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


	<div class="epl-info-wrapper epl-contact-section epl-contact-single-display">
		<input type="hidden" id="epl_contact_id" value="<?php echo $contact->id; ?>"/>

		<div class="epl-item-info epl-contact-info">

			<?php do_action('epl_contact_avatar',$contact); ?>

			<div class="epl-contact-id right">
				<?php do_action('epl_contact_social_icons',$contact); ?>
			</div>

			<div class="epl-contact-main-wrapper left">
				<?php do_action('epl_contact_contact_details',$contact); ?>
			</div>

		</div>

	</div>

	<?php do_action( 'epl_contact_before_tables_wrapper', $contact ); ?>

	<?php //do_action( 'epl_contact_background_info', $contact ); ?>

	<div id="epl-item-tables-wrapper" class="contact-tables-wrapper epl-contact-section">

		<?php do_action( 'epl_contact_before_tables', $contact ); ?>

		<?php do_action('epl_contact_recent_interests',$contact); ?>

		<?php do_action( 'epl_contact_after_tables', $contact ); ?>

	</div>
	<?php do_action( 'epl_contact_card_bottom', $contact ); ?>
<?php
}

/**
 * View contact listings
 *
 * @since  3.0
 */
function epl_add_contact_screen_options() {
	global $epl_contacts_table;
	$option = 'per_page';
	$args = array(
		'label'		=> __('Contacts','easy-property-listings' ),
		'default'	=> 10,
		'option'	=> 'contacts_per_page'
	);
	add_screen_option( $option, $args );
	include_once( dirname( __FILE__ ) . '/class-contact-table.php' );
	$epl_contacts_table = new EPL_Contact_Reports_Table();
}

/**
 * Contact Table Options
 *
 * @since  3.0
 */
function epl_contact_table_set_option($status, $option, $value) {
	return $value;
}

/**
 * Contact screen options
 *
 * @since  3.0
 */
function epl_add_contact_screen_opts() {
	global $epl_contact_page_hook;
	add_action( "load-$epl_contact_page_hook", 'epl_add_contact_screen_options' );
	add_filter('set-screen-option', 'epl_contact_table_set_option', 10, 3);
}
add_action('admin_menu','epl_add_contact_screen_opts',99);
