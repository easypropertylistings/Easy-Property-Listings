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
		'name'                       => _x( 'Business Listings', 'Taxonomy General Name', 'easy-property-listings'  ),
		'singular_name'              => _x( 'Business Listing', 'Taxonomy Singular Name', 'easy-property-listings'  ),
		'menu_name'                  => __( 'Business Listings', 'easy-property-listings'  ),
		'all_items'                  => __( 'All Business Listings', 'easy-property-listings'  ),
		'parent_item'                => __( 'Parent Business Listing', 'easy-property-listings'  ),
		'parent_item_colon'          => __( 'Parent Business Listing:', 'easy-property-listings'  ),
		'new_item_name'              => __( 'New Business Listing Name', 'easy-property-listings'  ),
		'add_new_item'               => __( 'Add New Business Listing', 'easy-property-listings'  ),
		'edit_item'                  => __( 'Edit Business Listing', 'easy-property-listings'  ),
		'update_item'                => __( 'Update Business Listing', 'easy-property-listings'  ),
		'separate_items_with_commas' => __( 'Separate Business Listing with commas', 'easy-property-listings'  ),
		'search_items'               => __( 'Search Business Listing', 'easy-property-listings'  ),
		'add_or_remove_items'        => __( 'Add or remove Business Listing', 'easy-property-listings'  ),
		'choose_from_most_used'      => __( 'Choose from the most used Business Listing', 'easy-property-listings'  ),
		'not_found'                  => __( 'Business Listing Not Found', 'easy-property-listings'  ),
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
