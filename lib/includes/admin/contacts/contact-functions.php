<?php
/**
 * Contact Functions
 *
 * @package     EPL
 * @subpackage  Contacts/Functions
 * @copyright   Copyright (c) 2016, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Register a view for the single contact view
 *
 * @since  3.0
 * @param  array $views An array of existing views
 * @return array        The altered list of views
 */
function epl_register_default_contact_views( $views ) {

	$default_views = array(
		'overview'  		=> 'epl_contacts_view',
		'delete'    		=> 'epl_contacts_delete_view',
		'notes'     		=> 'epl_contact_notes_view',
		'meta'     		=> 'epl_contact_meta_view',
		'listings'     		=> 'epl_contacts_listing_view',
		'new-contact'     	=> 'epl_new_contact_view',
		'all_tags'     		=> 'epl_all_tags_view',
	);

	return array_merge( $views, $default_views );
}
add_filter( 'epl_contact_views', 'epl_register_default_contact_views', 1, 1 );

/**
 * Register a tab for the single contact view
 *
 * @since  3.0
 * @param  array $tabs An array of existing tabs
 * @return array       The altered list of tabs
 */
function epl_register_default_contact_tabs( $tabs ) {

	$default_tabs = array(
		'overview'	=> array( 'dashicon' => 'dashicons-admin-users', 'title' => __( 'Contact Profile', 'easy-property-listings'  ) ),
		'meta'		=> array( 'dashicon' => 'dashicons-edit', 'title' => __( 'Contact Info', 'easy-property-listings'  ) ),
		'listings'	=> array( 'dashicon' => 'dashicons-admin-home', 'title' => __( 'Listings', 'easy-property-listings'  ) ),
		'notes'		=> array( 'dashicon' => 'dashicons-admin-comments', 'title' => __( 'Contact Notes', 'easy-property-listings'  ) ),
	);

	return array_merge( $tabs, $default_tabs );
}
add_filter( 'epl_contact_tabs', 'epl_register_default_contact_tabs', 1, 1 );

/**
 * Register the Delete icon as late as possible so it's at the bottom
 *
 * @since  3.0
 * @param  array $tabs An array of existing tabs
 * @return array       The altered list of tabs, with 'delete' at the bottom
 */
function epl_register_delete_contact_tab( $tabs ) {

	$tabs['delete'] = array( 'dashicon' => 'dashicons-trash', 'title' => __( 'Delete Contact', 'easy-property-listings'  ) );

	return $tabs;
}
add_filter( 'epl_contact_tabs', 'epl_register_delete_contact_tab', PHP_INT_MAX, 1 );

/**
 * Next contact link
 *
 * @since  3.0
 */
function epl_get_next_contact_link($contact_id) {
	if(absint($contact_id) < 1 )
		return;

	$contact_id = absint($contact_id);
	global $wpdb;

	$where = " `post_type` = 'epl_contact'
		AND `post_status`	= 'publish'
		AND `ID` > {$contact_id} ";

	$where = apply_filters('epl_get_next_contact_link_query',$where);

	$query = "
		SELECT *
		FROM {$wpdb->prefix}posts
		WHERE ".$where."
		ORDER BY `ID` DESC
		LIMIT 1
	";


	$next = $wpdb->get_col($query);

	if(!empty($next)) {
		return admin_url('admin.php?page=epl-contacts&view=overview&id='.$next[0]);
	}
}

/**
 * Previous contact link
 *
 * @since  3.0
 */
function epl_get_prev_contact_link($contact_id) {

	if(absint($contact_id) < 1 )
		return;

	$contact_id = absint($contact_id);
	global $wpdb;

	$where = " `post_type` = 'epl_contact'
		AND `post_status`	= 'publish'
		AND `ID` < {$contact_id} ";

	$where = apply_filters('epl_get_prev_contact_link_query',$where);

	$query = "
		SELECT *
		FROM {$wpdb->prefix}posts
		WHERE ".$where."
		ORDER BY `ID` DESC
		LIMIT 1
	";


	$next = $wpdb->get_col($query);

	if(!empty($next)) {
		return admin_url('admin.php?page=epl-contacts&view=overview&id='.$next[0]);
	}
}

/**
 * Contact Fields
 *
 * @since  3.0
 */
function epl_contact_contact_fields($contact_fields,$contact) {
	$fields = array();
	$phones = $contact->get_meta('contact_phones');
	if(!empty($phones)) {
		foreach($phones as $phone_name	=>	$phone_value) {
			$label = ucwords(str_replace('_',' ',$phone_name));
			$fields[] = array(
				'name'		=>	"contact_phones[$phone_name]",
				'label'		=>	__($label,'easy-property-listings' ),
				'type'		=>	'text',
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
				'label'		=>	__($label,'easy-property-listings' ),
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

/**
 * Contact Access Roles
 *
 * @since  3.0
 */
function epl_contact_access() {
	$allowed = epl_get_option('min_contact_access');
	$allowed = empty($allowed) ? 'level_10' : $allowed;
	$return	 = current_user_can($allowed) ? true : false;
	return apply_filters('epl_contact_access',$return);
}

