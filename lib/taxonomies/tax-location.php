<?php
/**
 * TAXONOMY :: Location
 *
 * @package	EPL
 * @subpackage  Taxonomy
 * @copyright   Copyright (c) 2014, Merv Barrett
 * @license	http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since	1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Registers and sets up the tax_location taxonomy
 *
 * @since 1.0
 * @return void
 */
function epl_register_taxonomy_location() {
	$location_label = epl_tax_location_label();
	$location_slug = sanitize_title($location_label);
	$hierarchical = defined( 'EPL_LOCATION_HIERARCHICAL' ) && EPL_LOCATION_HIERARCHICAL ? true : false;
		/* add define ('EPL_LOCATION_HIERARCHICAL', 'true'); into theme functions.php for hierarchical location taxonomy */

	$labels = array(
		'name'				=> _x( $location_label, 'Taxonomy General Name', 'easy-property-listings' ),
		'singular_name'			=> _x( $location_label, 'Taxonomy Singular Name', 'easy-property-listings' ),
		'menu_name'			=> __( $location_label, 'easy-property-listings'  ),
		'all_items'			=> __( 'All '.$location_label, 'easy-property-listings'  ),
		'parent_item'			=> __( 'Parent '.$location_label, 'easy-property-listings'  ),
		'parent_item_colon'		=> __( 'Parent '.$location_label.':', 'easy-property-listings'  ),
		'new_item_name'			=> __( 'New '.$location_label.' Name', 'easy-property-listings'  ),
		'add_new_item'			=> __( 'Add New '.$location_label, 'easy-property-listings'  ),
		'edit_item'			=> __( 'Edit '.$location_label, 'easy-property-listings'  ),
		'update_item'			=> __( 'Update '.$location_label, 'easy-property-listings'  ),
		'separate_items_with_commas'	=> __( 'Separate '.$location_label.' with commas', 'easy-property-listings'  ),
		'search_items'			=> __( 'Search '.$location_label, 'easy-property-listings'  ),
		'add_or_remove_items'	 	=> __( 'Add or remove '.$location_label, 'easy-property-listings'  ),
		'choose_from_most_used'	 	=> __( 'Choose from the most used '.$location_label, 'easy-property-listings'  ),
		'not_found'			=> __( $location_label.' Not Found', 'easy-property-listings'  ),
	);
	$rewrite = array(
		'slug'				=> $location_slug,
		'with_front'			=> true,
		'hierarchical'			=> $hierarchical,
	);
	$args = array(
		'labels'			=> $labels,
		'hierarchical'			=> $hierarchical,
		'public'			=> true,
		'show_ui'			=> true,
		'show_admin_column'		=> true,
		'show_in_nav_menus'		=> true,
		'show_tagcloud'			=> true,
		'rewrite'			=> $rewrite,
	);
	register_taxonomy( 'location', array( 'property' , 'rental' , 'land', 'rural' , 'business', 'commercial' , 'commercial_land' ,'testimonial' ,'location',  ) , $args );
}
add_action( 'init', 'epl_register_taxonomy_location', 0 );
