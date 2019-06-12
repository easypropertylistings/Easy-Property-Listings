<?php
/**
 * TAXONOMY Business Listings
 *
 * @package     EPL
 * @subpackage  Taxonomy
 * @copyright   Copyright (c) 2019, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Registers and sets up the tax_business_listing taxonomy
 *
 * @since 1.0
 * @return void
 */
function epl_register_taxonomy_business_listings() {

	$slug		= defined( 'EPL_BUSINESS_CAT_SLUG' ) ? EPL_BUSINESS_CAT_SLUG : 'business-category';
	$hierarchical	= defined( 'EPL_BUSINESS_CAT_HIERARCHICAL' ) && EPL_BUSINESS_CAT_HIERARCHICAL ? false : true;
	$rewrite	= defined( 'EPL_BUSINESS_CAT_DISABLE_REWRITE' ) && EPL_BUSINESS_CAT_DISABLE_REWRITE ? false : array( 'slug' => $slug, 'with_front' => true, 'hierarchical' => $hierarchical );
	$rest		= defined( 'EPL_BUSINESS_CAT_DISABLE_REST' ) && EPL_BUSINESS_CAT_DISABLE_REST ? false : true;

	$labels = array(
		'name'				=> _x( 'Business Categories', 'Taxonomy General Name', 'easy-property-listings'  ),
		'singular_name'			=> _x( 'Business Category', 'Taxonomy Singular Name', 'easy-property-listings'  ),
		'menu_name'			=> __( 'Categories', 'easy-property-listings'  ),
		'all_items'			=> __( 'All Business Categories', 'easy-property-listings'  ),
		'parent_item'			=> __( 'Parent Business Category', 'easy-property-listings'  ),
		'parent_item_colon'		=> __( 'Parent Business Category:', 'easy-property-listings'  ),
		'new_item_name'			=> __( 'New Business Category Name', 'easy-property-listings'  ),
		'add_new_item'			=> __( 'Add New Business Category', 'easy-property-listings'  ),
		'edit_item'			=> __( 'Edit Business Category', 'easy-property-listings'  ),
		'update_item'			=> __( 'Update Business Category', 'easy-property-listings'  ),
		'separate_items_with_commas'	=> __( 'Separate Business Category with commas', 'easy-property-listings'  ),
		'search_items'			=> __( 'Search Business Category', 'easy-property-listings'  ),
		'add_or_remove_items'		=> __( 'Add or remove Business Category', 'easy-property-listings'  ),
		'choose_from_most_used'		=> __( 'Choose from the most used Business Category', 'easy-property-listings'  ),
		'not_found'			=> __( 'Business Category Not Found', 'easy-property-listings'  ),
	);

	$args = array(
		'labels'			=> $labels,
		'hierarchical'			=> $hierarchical,
		'public'			=> true,
		'show_ui'			=> true,
		'show_admin_column'		=> true,
		'show_in_nav_menus'		=> true,
		'show_tagcloud'			=> true,
		'show_in_rest'			=> $rest,
		'rewrite'			=> $rewrite,
	);
	register_taxonomy( 'tax_business_listing', array( 'business' ) , $args );
}
add_action( 'init', 'epl_register_taxonomy_business_listings', 0 );
