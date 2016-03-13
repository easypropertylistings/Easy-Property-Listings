<?php
/**
 * Register post type :: Contact Listing
 *
 * @package     EPL
 * @subpackage  Functions/CPT
 * @copyright   Copyright (c) 2014, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Registers and sets up the Contact Listing custom post type
 *
 * @since 3.0
 * @return void
 */
function epl_register_custom_post_type_contact_listing() {

	$archives = defined( 'EPL_CONTACT_LISTING_DISABLE_ARCHIVE' ) && EPL_CONTACT_LISTING_DISABLE_ARCHIVE ? false : true;
	$slug     = defined( 'EPL_CONTACT_LISTING_SLUG' ) ? EPL_CONTACT_LISTING_SLUG : 'contact';
	$rewrite  = defined( 'EPL_CONTACT_LISTING_DISABLE_REWRITE' ) && EPL_CONTACT_LISTING_DISABLE_REWRITE ? false : array('slug' => $slug, 'with_front' => false);

	$labels = apply_filters( 'epl_contact_labels', array(
		'name'			=>	__('contact Listings', 'epl'),
		'singular_name'		=>	__('Contact Listing', 'epl'),
		'menu_name'		=>	__('Contact Listings', 'epl'),
		'add_new'		=>	__('Add New ', 'epl'),
		'add_new_item'		=>	__('Add New Contact Listing', 'epl'),
		'edit_item'		=>	__('Edit Contact Listing', 'epl'),
		'new_item'		=>	__('New Contact Listing', 'epl'),
		'update_item'		=>	__('Update Contact Listing', 'epl'),
		'all_items'		=>	__('All Contact Listings', 'epl'),
		'view_item'		=>	__('View Contact Listings', 'epl'),
		'search_items'		=>	__('Search Contact Listings', 'epl'),
		'not_found'		=>	__('Contact Listing Not Found', 'epl'),
		'not_found_in_trash'	=>	__('Contact Listing Not Found in Trash', 'epl'),
		'parent_item_colon'	=>	__('Parent Contact:', 'epl')
	) );

	$contact_args = array(
		'labels'		=>	$labels,
		'public'		=>	false,
		'publicly_queryable'	=>	false,
		'show_ui'		=>	true,
		'show_in_menu'		=>	true,
		'query_var'		=>	false,
		'rewrite'		=>	$rewrite,
		'menu_icon'		=>	'dashicons-admin-home',
		'capability_type'	=>	'post',
		'has_archive'		=>	$archives,
		'hierarchical'		=>	false,
		'supports'		=>	apply_filters( 'epl_contact_listing_supports', array( 'title', 'editor', 'author', 'thumbnail', 'excerpt' , 'comments' ) ),
	);
	register_post_type( 'contact_listing',  apply_filters( 'epl_contact_listing_post_type_args', $contact_args ) );
}
add_action( 'init', 'epl_register_custom_post_type_contact_listing', 0 );