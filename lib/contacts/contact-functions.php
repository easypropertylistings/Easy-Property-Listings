<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Register a view for the single contact view
 *
 * @since  2.4
 * @param  array $views An array of existing views
 * @return array        The altered list of views
 */
function epl_register_default_contact_views( $views ) {

	$default_views = array(
		'overview'  			=> 'epl_contacts_view',
		'delete'    			=> 'epl_contacts_delete_view',
		'notes'     			=> 'epl_contact_notes_view',
		'meta'     				=> 'epl_contact_meta_view',
		'listings'     			=> 'epl_contacts_listing_view',
		'new-contact'     		=> 'epl_new_contact_view',
		'all_tags'     			=> 'epl_all_tags_view',
	);

	return array_merge( $views, $default_views );

}
add_filter( 'epl_contact_views', 'epl_register_default_contact_views', 1, 1 );

/**
 * Register a tab for the single contact view
 *
 * @since  2.4
 * @param  array $tabs An array of existing tabs
 * @return array       The altered list of tabs
 */
function epl_register_default_contact_tabs( $tabs ) {

	$default_tabs = array(
		'overview' => array( 'dashicon' => 'dashicons-admin-users', 'title' => __( 'Contact Profile', 'epl' ) ),
		'meta'      => array( 'dashicon' => 'dashicons-edit', 'title' => __( 'Contact Info', 'epl' ) ),
		'listings'  => array( 'dashicon' => 'dashicons-admin-home', 'title' => __( 'Listings', 'epl' ) ),
		'notes'    => array( 'dashicon' => 'dashicons-admin-comments', 'title' => __( 'Contact Notes', 'epl' ) ),
	);

	return array_merge( $tabs, $default_tabs );
}
add_filter( 'epl_contact_tabs', 'epl_register_default_contact_tabs', 1, 1 );

/**
 * Register the Delete icon as late as possible so it's at the bottom
 *
 * @since  2.4
 * @param  array $tabs An array of existing tabs
 * @return array       The altered list of tabs, with 'delete' at the bottom
 */
function epl_register_delete_contact_tab( $tabs ) {

	$tabs['delete'] = array( 'dashicon' => 'dashicons-trash', 'title' => __( 'Delete Contact', 'epl' ) );

	return $tabs;
}
add_filter( 'epl_contact_tabs', 'epl_register_delete_contact_tab', PHP_INT_MAX, 1 );

function epl_get_next_contact_link($contact_id) {
	if(absint($contact_id) < 1 )
		return;
	global $wpdb;
	$next = $wpdb->get_col("SELECT `ID` from {$wpdb->prefix}posts where `post_type` = 'epl_contact' AND `ID` > {intval($contact_id)} ");
	if(!empty($next)) {
		return admin_url('admin.php?page=epl-contacts&view=overview&id='.$next[0]);
	}
}

function epl_get_prev_contact_link($contact_id) {
	if(absint($contact_id) < 1 )
		return;
	global $wpdb;
	$next = $wpdb->get_col("SELECT `ID` from {$wpdb->prefix}posts where `post_type` = 'epl_contact' AND `ID` < {intval($contact_id)} ");
	if(!empty($next)) {
		return admin_url('admin.php?page=epl-contacts&view=overview&id='.$next[0]);
	}
}

function epl_contact_contact_fields($contact_fields,$contact) {
	$fields = array();
	$phones = $contact->get_meta('contact_phones');
	if(!empty($phones)) {
		foreach($phones as $phone_name	=>	$phone_value) {
			$label = ucwords(str_replace('_',' ',$phone_name));
			$fields[] = array(
				'name'		=>	"contact_phones[$phone_name]",
				'label'		=>	__($label,'epl'),
				'type'		=>	'number',
				'class'		=>	'epl-contact-addable',
				'maxlength'	=>	'60',
				'value'		=>	$phone_value
			);
		}
	}
	$emails = $contact->get_meta('contact_emails');
	if(!empty($emails)) {
		foreach($emails as $mail_name	=>	$mail_value) {
			$label = ucwords(str_replace('_',' ',$mail_name));
			$fields[] = array(
				'name'		=>	"contact_emails[$mail_name]",
				'label'		=>	__($label,'epl'),
				'type'		=>	'email',
				'class'		=>	'epl-contact-addable-email',
				'maxlength'	=>	'60',
				'value'		=>	$mail_value
			);
		}
	}
	$merged_fields = array_merge($fields,$contact_fields);
	return array_intersect_key($merged_fields, array_unique(array_map('serialize', $merged_fields)));
}
add_filter('epl_contact_contact_fields','epl_contact_contact_fields',10,2);

