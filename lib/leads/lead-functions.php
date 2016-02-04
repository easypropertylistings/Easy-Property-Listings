<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Register a view for the single lead view
 *
 * @since  2.4
 * @param  array $views An array of existing views
 * @return array        The altered list of views
 */
function epl_register_default_lead_views( $views ) {

	$default_views = array(
		'overview'  	=> 'epl_leads_view',
		'delete'    	=> 'epl_leads_delete_view',
		'notes'     	=> 'epl_lead_notes_view',
	);

	return array_merge( $views, $default_views );

}
add_filter( 'epl_lead_views', 'epl_register_default_lead_views', 1, 1 );

/**
 * Register a tab for the single lead view
 *
 * @since  2.4
 * @param  array $tabs An array of existing tabs
 * @return array       The altered list of tabs
 */
function epl_register_default_lead_tabs( $tabs ) {

	$default_tabs = array(
		'overview' => array( 'dashicon' => 'dashicons-admin-users', 'title' => __( 'Lead Profile', 'epl' ) ),
		'notes'    => array( 'dashicon' => 'dashicons-admin-comments', 'title' => __( 'Lead Notes', 'epl' ) ),
	);

	return array_merge( $tabs, $default_tabs );
}
add_filter( 'epl_lead_tabs', 'epl_register_default_lead_tabs', 1, 1 );

/**
 * Register the Delete icon as late as possible so it's at the bottom
 *
 * @since  2.4
 * @param  array $tabs An array of existing tabs
 * @return array       The altered list of tabs, with 'delete' at the bottom
 */
function epl_register_delete_lead_tab( $tabs ) {

	$tabs['delete'] = array( 'dashicon' => 'dashicons-trash', 'title' => __( 'Delete Lead', 'epl' ) );

	return $tabs;
}
add_filter( 'epl_lead_tabs', 'epl_register_delete_lead_tab', PHP_INT_MAX, 1 );


