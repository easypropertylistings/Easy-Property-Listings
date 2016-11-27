<?php
/**
 * Contextual Help
 *
 * @package     EPL
 * @subpackage  Admin/Help
 * @copyright   Copyright (c) 2014, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Adds the Contextual Help for the List Listings page
 *
 * @since 3.0
 * @return void
 */
function epl_list_listings_contextual_help() {
	$screen = get_current_screen();
	if (  !in_array( ltrim($screen->id,'edit-'), epl_get_core_post_types()))
		return;

	$screen->set_help_sidebar(
		'<p><strong>' . sprintf( __( 'For more information:', 'easy-property-listings'  ) . '</strong></p>' .
		'<p>' . sprintf( __( '<a href="%s">Documentation on how to add a listing</a>.', 'easy-property-listings'  ), esc_url( 'http://codex.easypropertylistings.com.au/article/135-how-to-add-a-listing-to-easy-property-listings' ) ) ) . '</p>' .
		'<p>' . sprintf(  __( 'Visit the <a href="%s">Store</a>', 'easy-property-listings'  ), esc_url( 'https://easypropertylistings.com.au/extensions/' ) ) . '</p>' .
		'<p>' . sprintf(  __( 'Need help? Open a <a href="%s">Support</a> ticket.', 'easy-property-listings'  ), esc_url( 'https://easypropertylistings.com.au/support-ticket/' ) ) . '</p>'

	);

	// Overview
	$screen->add_help_tab( array(
		'id'		=> 'epl-listing-overview',
		'title'		=> __('Overview' , 'easy-property-listings' ),
		'content'	=> '<p>' . __('This screen provides access to all of your posts. You can customize the display of this screen to suit your workflow.' , 'easy-property-listings' ) . '</p>'

	) );

	// Screen Content
	$screen_content	= '<p>' . __( 'You can customize the display of this screen’s contents in a number of ways:' , 'easy-property-listings'  ) . '</p>';

	$screen_content	.= '<ul>';
	$screen_content	.= '<li>' . __( 'You can hide/display columns based on your needs and decide how many listings to list per screen using the Screen Options tab.' , 'easy-property-listings'  ) . '</li>';
	$screen_content	.= '<li>' . __( 'You can filter the list of listings by post status using the text links above the listings list to only show listings with that status. The default view is to show all listings.' , 'easy-property-listings'  ) . '</li>';
	$screen_content	.= '<li>' . __( 'You can view listings in a simple title list or with an excerpt using the Screen Options tab.' , 'easy-property-listings'  ) . '</li>';
	$screen_content	.= '<li>' . __( 'You can refine the list to show only listings in a specific category or from a specific month by using the dropdown menus above the listings list. Click the Filter button after making your selection. You also can refine the list by clicking on the listings author, category or tag in the listings list.' , 'easy-property-listings'  ) . '</li>';
	$screen_content	.= '</ul>';

	$screen->add_help_tab( array(
		'id'		=> 'epl-listing-screen-content',
		'title'		=> __('Screen Content' , 'easy-property-listings' ),
		'content'	=> $screen_content
	) );



	// Available Actions
	$actions	= '<p>' . __( 'Hovering over a row in the listings list will display action links that allow you to manage your listings. You can perform the following actions:' , 'easy-property-listings'  ) . '</p>';
	$actions	.= '<ul>';
	$actions	.= '<li><strong>' . __('Edit' , 'easy-property-listings'  ) . '</strong> ' . __( 'takes you to the editing screen for that listing. You can also reach that screen by clicking on the listing title.' , 'easy-property-listings'  ) . '</li>';
	$actions	.= '<li><strong>' . __('Quick Edit' , 'easy-property-listings'  ) . '</strong> ' . __( 'provides inline access to the metadata of your listing, allowing you to update listing details without leaving this screen.' , 'easy-property-listings'  ) . '</li>';
	$actions	.= '<li><strong>' . __('Trash' , 'easy-property-listings'  ) . '</strong> ' . __( 'removes your listing from this list and places it in the trash, from which you can permanently delete it.' , 'easy-property-listings'  ) . '</li>';
	$actions	.= '<li><strong>' . __('Preview' , 'easy-property-listings'  ) . '</strong> ' . __( 'will show you what your draft listing will look like if you publish it. View will take you to your live site to view the listing. Which link is available depends on your listing status.' , 'easy-property-listings'  ) . '</li>';
	$actions	.= '</ul>';

	$screen->add_help_tab( array(
	'id'		=> 'epl-listing-actions',
	'title'		=> __('Available Actions' , 'easy-property-listings' ),
	'content'	=> $actions

	) );

	// Bulk Actions
	$bulk_actions	= '<p>' . __( 'You can also edit or move multiple listings to the trash at once. Select the listings you want to act on using the checkboxes, then select the action you want to take from the Bulk Actions menu and click Apply.' , 'easy-property-listings'  ) . '</p>';

	$bulk_actions	.= '<p>' . __( 'When using Bulk Edit, you can change the metadata (features, author, etc.) for all selected posts at once. To remove a listing from the grouping, just click the x next to its name in the Bulk Edit area that appears.' , 'easy-property-listings'  ) . '</p>';

	$screen->add_help_tab( array(
	'id'		=> 'epl-listing-bulk-actions',
	'title'		=> __('Bulk Actions' , 'easy-property-listings' ),
	'content'	=> $bulk_actions

	) );

	/**
	* Fires off in the EPL Contextual Help Screen
	*
	* @since 3.1
	* @param object $screen The current admin screen
	*/
	do_action( 'epl_list_listings_contextual_help', $screen );
}
add_action( 'current_screen', 'epl_list_listings_contextual_help' );