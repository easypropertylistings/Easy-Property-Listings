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


