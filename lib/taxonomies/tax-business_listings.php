<?php
/**
 * TAXONOMY Business Listings
 *
 * @package     EPL
 * @subpackage  Taxonomy
 * @copyright   Copyright (c) 2014, Merv Barrett
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
	$labels = array(
		'name'                       => _x( 'Business Listings', 'Taxonomy General Name', 'epl' ),
		'singular_name'              => _x( 'Business Listing', 'Taxonomy Singular Name', 'epl' ),
		'menu_name'                  => __( 'Business Listings', 'epl' ),
		'all_items'                  => __( 'All Business Listings', 'epl' ),
		'parent_item'                => __( 'Parent Business Listing', 'epl' ),
		'parent_item_colon'          => __( 'Parent Business Listing:', 'epl' ),
		'new_item_name'              => __( 'New Business Listing Name', 'epl' ),
		'add_new_item'               => __( 'Add New Business Listing', 'epl' ),
		'edit_item'                  => __( 'Edit Business Listing', 'epl' ),
		'update_item'                => __( 'Update Business Listing', 'epl' ),
		'separate_items_with_commas' => __( 'Separate Business Listing with commas', 'epl' ),
		'search_items'               => __( 'Search Business Listing', 'epl' ),
		'add_or_remove_items'        => __( 'Add or remove Business Listing', 'epl' ),
		'choose_from_most_used'      => __( 'Choose from the most used Business Listing', 'epl' ),
		'not_found'                  => __( 'Business Listing Not Found', 'epl' ),
	);
	$rewrite = array(
		'slug'                       => 'business_listing',
		'with_front'                 => true,
		'hierarchical'               => false,
	);
	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => true,
		'public'                     => true,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => true,
		'show_tagcloud'              => true,
		'rewrite'                    => $rewrite,
	);
	register_taxonomy( 'tax_business_listing', array( 'business' ) , $args );
}
add_action( 'init', 'epl_register_taxonomy_business_listings', 0 );
