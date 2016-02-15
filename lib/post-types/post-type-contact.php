<?php
/**
 * Register post type :: Contact
 *
 * @package     EPL
 * @subpackage  Meta
 * @copyright   Copyright (c) 2014, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.4
 */
 
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
 
/**
 * Registers and sets up the Contact custom post type
 *
 * @since 2.4
 * @return void
 */
function epl_register_custom_post_type_contact() {

	$archives = defined( 'EPL_LEAD_DISABLE_ARCHIVE' ) && EPL_LEAD_DISABLE_ARCHIVE ? false : true;
	$slug     = defined( 'EPL_LEAD_SLUG' ) ? EPL_LEAD_SLUG : 'contact';
	$rewrite  = defined( 'EPL_LEAD_DISABLE_REWRITE' ) && EPL_LEAD_DISABLE_REWRITE ? false : array('slug' => $slug, 'with_front' => false);

	$labels = apply_filters( 'epl_contact_labels', array(
		'name'			=>	__('contacts', 'epl'),
		'singular_name'		=>	__('Contact', 'epl'),
		'menu_name'		=>	__('Contact', 'epl'),
		'add_new'		=>	__('Add New', 'epl'),
		'add_new_item'		=>	__('Add New Contact', 'epl'),
		'edit_item'		=>	__('Edit Contact', 'epl'),
		'new_item'		=>	__('New Contact', 'epl'),
		'update_item'		=>	__('Update Contact', 'epl'),
		'all_items'		=>	__('All Contacts', 'epl'),
		'view_item'		=>	__('View Contact', 'epl'),
		'search_items'		=>	__('Search Contact', 'epl'),
		'not_found'		=>	__('Contact Not Found', 'epl'),
		'not_found_in_trash'	=>	__('Contact Not Found in Trash', 'epl'),
		'parent_item_colon'	=>	__('Parent Contact:', 'epl')
	) );
	
	$contact_args = array(
		'labels'		=>	$labels,
		'public'		=>	false,
		'publicly_queryable'	=>	false,
		'show_ui'		=>	false,
		'show_in_menu'		=>	false,
		'query_var'		=>	false,
		'rewrite'		=>	$rewrite,
		'menu_icon'		=>	'dashicons-admin-home',
		'capability_type'	=>	'post',
		'has_archive'		=>	$archives,
		'hierarchical'		=>	false,
		'supports'		=>	apply_filters( 'epl_contact_supports', array( 'title', 'editor', 'author', 'thumbnail', 'excerpt' , 'comments' ) ),
	);
	register_post_type( 'epl_contact',  apply_filters( 'epl_contact_post_type_args', $contact_args ) );
}
add_action( 'init', 'epl_register_custom_post_type_contact', 0 );
 

