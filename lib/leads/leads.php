<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Leads Page
 *
 * Renders the leads page contents.
 *
 * @since  2.4
 * @return void
*/
function epl_leads_page() {
	$default_views = epl_lead_views();
	$requested_view = isset( $_GET['view'] ) ? sanitize_text_field( $_GET['view'] ) : 'leads';
	if ( array_key_exists( $requested_view, $default_views ) && function_exists( $default_views[$requested_view] ) ) {
		epl_render_lead_view( $requested_view, $default_views );
	} else {
		epl_leads_list();
	}
}

/**
 * Register the views for lead management
 *
 * @since  2.4
 * @return array Array of views and their callbacks
 */
function epl_lead_views() {

	$views = array();
	return apply_filters( 'epl_lead_views', $views );

}

/**
 * Register the tabs for lead management
 *
 * @since  2.4
 * @return array Array of tabs for the lead
 */
function epl_lead_tabs() {

	$tabs = array();
	return apply_filters( 'epl_lead_tabs', $tabs );

}

/**
 * List table of leads
 *
 * @since  2.4
 * @return void
 */
function epl_leads_list() {
	include( dirname( __FILE__ ) . '/class-lead-table.php' );

	$leads_table = new EPL_Lead_Reports_Table();
	$leads_table->prepare_items();
	?>
	<div class="wrap">
		<h2><?php _e( 'Leads', 'epl' ); ?></h2>
		<?php do_action( 'epl_leads_table_top' ); ?>
		<form id="epl-leads-filter" method="get" action="<?php echo admin_url( 'admin.php?page=epl-leads' ); ?>">
			<?php
			$leads_table->search_box( __( 'Search Leads', 'epl' ), 'epl-leads' );
			$leads_table->display();
			?>
			<input type="hidden" name="post_type" value="download" />
			<input type="hidden" name="page" value="epl-leads" />
			<input type="hidden" name="view" value="leads" />
		</form>
		<?php do_action( 'epl_leads_table_bottom' ); ?>
	</div>
	<?php
}

/**
 * Renders the lead view wrapper
 *
 * @since  2.4
 * @param  string $view      The View being requested
 * @param  array $callbacks  The Registered views and their callback functions
 * @return void
 */
function epl_render_lead_view( $view, $callbacks ) {

	$render = true;

	$lead_view_role = apply_filters( 'epl_view_leads_role', 'manage_options' );

	if ( ! current_user_can( $lead_view_role ) ) {
		epl_set_error( 'epl-no-access', __( 'You are not permitted to view this data.', 'epl' ) );
		$render = false;
	}

	if ( ! isset( $_GET['id'] ) || ! is_numeric( $_GET['id'] ) ) {
		epl_set_error( 'epl-invalid_lead', __( 'Invalid Lead ID Provided.', 'epl' ) );
		$render = false;
	}

	$lead_id = (int)$_GET['id'];
	$lead    = new EPL_Lead( $lead_id );

	if ( empty( $lead->id ) ) {
		epl_set_error( 'epl-invalid_lead', __( 'Invalid Lead ID Provided.', 'epl' ) );
		$render = false;
	}

	$lead_tabs = epl_lead_tabs();
	?>

	<div class='wrap'>
		<h2><?php _e( 'Lead Details', 'epl' );?></h2>
		<?php if ( epl_get_errors() ) :?>
			<div class="error settings-error">
				<?php epl_print_errors(); ?>
			</div>
		<?php endif; ?>

		<?php if ( $lead && $render ) : ?>

			<div id="epl-item-tab-wrapper" class="lead-tab-wrapper">
				<ul id="epl-item-tab-wrapper-list" class="ustomer-tab-wrapper-list">
				<?php foreach ( $lead_tabs as $key => $tab ) : ?>
					<?php $active = $key === $view ? true : false; ?>
					<?php $class  = $active ? 'active' : 'inactive'; ?>

					<?php if ( ! $active ) : ?>
					<a title="<?php echo esc_attr( $tab['title'] ); ?>" aria-label="<?php echo esc_attr( $tab['title'] ); ?>" href="<?php echo esc_url( admin_url( 'admin.php?page=epl-leads&view=' . $key . '&id=' . $lead->id ) ); ?>">
					<?php endif; ?>

					<li class="<?php echo sanitize_html_class( $class ); ?>"><span class="dashicons <?php echo sanitize_html_class( $tab['dashicon'] ); ?>"></span></li>

					<?php if ( ! $active ) : ?>
					</a>
					<?php endif; ?>

				<?php endforeach; ?>
				</ul>
			</div>

			<div id="epl-item-card-wrapper" class="epl-lead-card-wrapper" style="float: left">
				<?php $callbacks[$view]( $lead ) ?>
			</div>

		<?php endif; ?>

	</div>
	<?php

}


/**
 * View a lead
 *
 * @since  2.4
 * @param  $lead The Lead object being displayed
 * @return void
 */
function epl_leads_view( $lead ) {

	$lead_edit_role = apply_filters( 'epl_edit_leads_role', 'manage_options' );

	?>

	<?php do_action( 'epl_lead_card_top', $lead ); ?>

	<div class="info-wrapper lead-section">

		<form id="edit-lead-info" method="post" action="<?php echo admin_url( 'admin.php?page=epl-leads&view=overview&id=' . $lead->id ); ?>">

			<div class="epl-item-info lead-info">

				<div class="avatar-wrap left" id="lead-avatar">
					<?php echo get_avatar( $lead->email ); ?><br />
					<?php if ( current_user_can( $lead_edit_role ) ): ?>
						<span class="info-item editable lead-edit-link"><a title="<?php _e( 'Edit Lead', 'epl' ); ?>" href="#" id="edit-lead"><?php _e( 'Edit Lead', 'epl' ); ?></a></span>
					<?php endif; ?>
				</div>

				<div class="lead-id right">
					#<?php echo $lead->id; ?>
				</div>


				<div class="lead-main-wrapper left">

					<span class="lead-name info-item edit-item"><input size="15" data-key="name" name="leadinfo[name]" type="text" value="<?php echo esc_attr( $lead->name ); ?>" placeholder="<?php _e( 'Lead Name', 'epl' ); ?>" /></span>
					<span class="lead-name info-item editable"><span data-key="name"><?php echo $lead->name; ?></span></span>
					<span class="lead-name info-item edit-item"><input size="20" data-key="email" name="leadinfo[email]" type="text" value="<?php echo $lead->email; ?>" placeholder="<?php _e( 'Lead Email', 'epl' ); ?>" /></span>
					<span class="lead-email info-item editable" data-key="email"><?php echo $lead->email; ?></span>
					<span class="lead-since info-item">
						<?php _e( 'Lead since', 'epl' ); ?>
						<?php echo date_i18n( get_option( 'date_format' ), strtotime( $lead->date_created ) ) ?>
					</span>
					<span class="lead-user-id info-item edit-item">
						<?php
							$user_id    		= $lead->user_id > 0 ? $lead->user_id : '';
							$userdata 			= get_userdata( $user_id );
						?>
						<input size="20" class="epl-ajax-user-search" data-key="user_login" name="leadinfo[user_login]" type="text" value="<?php echo $userdata->user_login; ?>" placeholder="<?php _e( 'Lead Email', 'epl' ); ?>" />
						<span class="epl_user_search_results hidden">
							<a href="#" aria-label="Cancel" title="Cancel" class="epl-ajax-user-cancel">x</a>
							<span></span>
						</span>
						<input type="hidden" name="leadinfo[user_id]" data-key="user_id" value="<?php echo $lead->user_id; ?>" />
					</span>

					<span class="lead-user-id info-item editable">
						<?php _e( 'User ID', 'epl' ); ?>:&nbsp;
						<?php if( intval( $lead->user_id ) > 0 ) : ?>
							<span data-key="user_id"><a href="<?php echo admin_url( 'user-edit.php?user_id=' . $lead->user_id ); ?>"><?php echo $lead->user_id; ?></a></span>
						<?php else : ?>
							<span data-key="user_id"><?php _e( 'none', 'epl' ); ?></span>
						<?php endif; ?>
						<?php if ( current_user_can( $lead_edit_role ) && intval( $lead->user_id ) > 0 ) : ?>
							<span class="disconnect-user"> - <a id="disconnect-lead" href="#disconnect" title="<?php _e( 'Disconnects the current user ID from this lead record', 'epl' ); ?>"><?php _e( 'Disconnect User', 'epl' ); ?></a></span>
						<?php endif; ?>
					</span>

				</div>

			</div>

			<span id="lead-edit-actions" class="edit-item">
				<input type="hidden" data-key="id" name="leadinfo[id]" value="<?php echo $lead->id; ?>" />
				<?php wp_nonce_field( 'edit-lead', '_wpnonce', false, true ); ?>
				<input type="hidden" name="epl_action" value="edit-lead" />
				<input type="submit" id="epl-edit-lead-save" class="button-secondary" value="<?php _e( 'Update Lead', 'epl' ); ?>" />
				<a id="epl-edit-lead-cancel" href="" class="delete"><?php _e( 'Cancel', 'epl' ); ?></a>
			</span>

		</form>
	</div>

	<?php do_action( 'epl_lead_before_tables_wrapper', $lead ); ?>

	<div id="epl-item-tables-wrapper" class="lead-tables-wrapper lead-section">

		<?php do_action( 'epl_lead_before_tables', $lead ); ?>

		<h3><?php _e( 'Recent Interests', 'epl' ); ?></h3>
		<?php
			$listing_ids = explode( ',', $lead->listing_ids );
			$listings    = get_posts( array( 'post__in' => $listing_ids, 'post_type'	=>	epl_all_post_types() ) );
			$listings    = array_slice( $listings, 0, 10 );
		?>
		<table class="wp-list-table widefat striped listings">
			<thead>
				<tr>
					<th><?php _e( 'ID', 'epl' ); ?></th>
					<th><?php _e( 'Title', 'epl' ); ?></th>
					<th><?php _e( 'Published Date', 'epl' ); ?></th>
					<th><?php _e( 'Status', 'epl' ); ?></th>
					<th><?php _e( 'Actions', 'epl' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php if ( ! empty( $listings ) ) : ?>
					<?php foreach ( $listings as $listing ) : ?>
						<tr>
							<td><?php echo $listing->ID; ?></td>
							<td><?php echo  $listing->post_title; ?></td>
							<td><?php echo date_i18n( get_option( 'date_format' ), strtotime( $listing->post_date ) ); ?></td>
							<td><?php echo get_post_meta( $listing->ID,'property_status',true) ?></td>
							<td>
								<a title="<?php _e( 'View Details for Listing', 'epl' ); echo ' ' . $listing->ID; ?>" href="<?php echo admin_url( 'post.php?&action=edit&post=' . $listing->ID ); ?>">
									<?php _e( 'View Details', 'epl' ); ?>
								</a>
								<?php do_action( 'epl_lead_recent_listings_actions', $lead, $listing ); ?>
							</td>
						</tr>
					<?php endforeach; ?>
				<?php else: ?>
					<tr><td colspan="5"><?php _e( 'No Listings Found', 'epl' ); ?></td></tr>
				<?php endif; ?>
			</tbody>
		</table>

		<?php do_action( 'epl_lead_after_tables', $lead ); ?>

	</div>
	<?php do_action( 'epl_lead_card_bottom', $lead ); ?>

	<?php
}

/**
 * View the notes of a lead
 *
 * @since  2.4
 * @param  $lead The Lead being displayed
 * @return void
 */
function epl_lead_notes_view( $lead ) {

	$paged       = isset( $_GET['paged'] ) && is_numeric( $_GET['paged'] ) ? $_GET['paged'] : 1;
	$paged       = absint( $paged );
	$note_count  = $lead->get_notes_count();
	$per_page    = apply_filters( 'epl_lead_notes_per_page', 20 );
	$total_pages = ceil( $note_count / $per_page );

	$lead_notes = $lead->get_notes( $per_page, $paged );
	?>

	<div id="epl-item-notes-wrapper">
		<div class="epl-item-notes-header">
			<?php echo get_avatar( $lead->email, 30 ); ?> <span><?php echo $lead->name; ?></span>
		</div>
		<h3><?php _e( 'Notes', 'epl' ); ?></h3>

		<?php if ( 1 == $paged ) : ?>
		<div style="display: block; margin-bottom: 35px;">
			<form id="epl-add-lead-note" method="post" action="<?php echo admin_url( 'admin.php?page=epl-leads&view=notes&id=' . $lead->id ); ?>">
				<textarea id="lead-note" name="lead_note" class="lead-note-input" rows="10"></textarea>
				<br />
				<input type="hidden" id="lead-id" name="lead_id" value="<?php echo $lead->id; ?>" />
				<input type="hidden" name="epl_action" value="add-lead-note" />
				<br />
				<label class ="lead-select-label" > 
					<select class="lead-note-select" id="lead-note-listing">
						<option value=''><?php _e('Note for Listing', 'epl'); ?></option>
						<?php
							if( trim($lead->listing_ids) != '' ) {
								foreach( explode(',',$lead->listing_ids) as $listing_id ) {
									echo '<option value="'.$listing_id.'">'.get_the_title($listing_id).'</option>';
								}
							}
						?>
					</select>
				</label>
				<label class ="lead-select-label" > 
					<select class="lead-note-select" id="lead-note-notify">
						<option value='no'><?php _e('Private Note', 'epl'); ?></option>
						<option value='yes'><?php _e('Notify to Lead', 'epl'); ?></option>
					</select>
				</label>
				<br />
				<?php wp_nonce_field( 'add-lead-note', 'add_lead_note_nonce', true, true ); ?>
				<input id="add-lead-note" class="right button-primary" type="submit" value="Add Note" />
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

		<div id="epl-lead-notes">
		<?php if ( count( $lead_notes ) > 0 ) : ?>
			<?php foreach( $lead_notes as $key => $note ) : ?>
				<div class="lead-note-wrapper dashboard-comment-wrap comment-item <?php echo $note->comment_type; ?>" >
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
			<div class="epl-no-lead-notes">
				<?php _e( 'No Lead Notes', 'epl' ); ?>
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

function epl_leads_delete_view( $lead ) {
	$lead_edit_role = apply_filters( 'epl_edit_leads_role', 'manage_options' );

	?>

	<?php do_action( 'epl_lead_delete_top', $lead ); ?>

	<div class="info-wrapper lead-section">

		<form id="delete-lead" method="post" action="<?php echo admin_url( 'admin.php?page=epl-leads&view=delete&id=' . $lead->id ); ?>">

				<div class="epl-item-notes-header">
				<?php echo get_avatar( $lead->email, 30 ); ?> <span><?php echo $lead->name; ?></span>
			</div>


			<div class="lead-info delete-lead">

				<span class="delete-lead-options">
					<p>
						<input type="checkbox" class="epl-checkbox epl-lead-delete-confirm" id="epl-lead-delete-confirm" name="epl-lead-delete-confirm">
						<label for="epl-lead-delete-confirm"><?php _e( 'Are you sure you want to delete this lead?', 'epl' ); ?></label>
					</p>

					<?php do_action( 'epl_lead_delete_inputs', $lead ); ?>
				</span>

				<span id="lead-edit-actions">
					<input type="hidden" name="lead_id" value="<?php echo $lead->id; ?>" />
					<?php wp_nonce_field( 'delete-lead', '_wpnonce', false, true ); ?>
					<input type="hidden" name="epl_action" value="delete-lead" />
					<input type="submit" disabled="disabled" id="epl-delete-lead" class="button-primary" value="<?php _e( 'Delete Lead', 'epl' ); ?>" />
					<a id="epl-delete-lead-cancel" href="<?php echo admin_url( 'admin.php?page=epl-leads&view=overview&id=' . $lead->id ); ?>" class="delete"><?php _e( 'Cancel', 'epl' ); ?></a>
				</span>

			</div>

		</form>
	</div>

	<?php

	do_action( 'epl_lead_delete_bottom', $lead );
}

