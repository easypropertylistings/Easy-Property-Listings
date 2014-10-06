<?php
/**
 * TAXONOMY :: Location
 *
 * @package	 EPL
 * @subpackage  Taxonomy/Location
 * @copyright   Copyright (c) 2014, Merv Barrett
 * @license	 http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since	1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

function epl_register_taxonomy_location() {
	$location_label = epl_tax_location_label();
	$location_slug = sanitize_title($location_label);
	$hierarchical = defined( 'EPL_LOCATION_HIERARCHICAL' ) && EPL_LOCATION_HIERARCHICAL ? true : false;
		/* add define ('EPL_LOCATION_HIERARCHICAL', 'true'); into theme functions.php for hierarchical location taxonomy */
	
	$labels = array(
		'name'							=> _x( $location_label, 'Taxonomy General Name', 'epl'),
		'singular_name'					=> _x( $location_label, 'Taxonomy Singular Name', 'epl'),
		'menu_name'						=> __( $location_label, 'epl' ),
		'all_items'						=> __( 'All '.$location_label, 'epl' ),
		'parent_item'					=> __( 'Parent '.$location_label, 'epl' ),
		'parent_item_colon'				=> __( 'Parent '.$location_label.':', 'epl' ),
		'new_item_name'					=> __( 'New '.$location_label.' Name', 'epl' ),
		'add_new_item'					=> __( 'Add New '.$location_label, 'epl' ),
		'edit_item'						=> __( 'Edit '.$location_label, 'epl' ),
		'update_item'					=> __( 'Update '.$location_label, 'epl' ),
		'separate_items_with_commas'	=> __( 'Separate '.$location_label.' with commas', 'epl' ),
		'search_items'					=> __( 'Search '.$location_label, 'epl' ),
		'add_or_remove_items'	 		=> __( 'Add or remove '.$location_label, 'epl' ),
		'choose_from_most_used'	 		=> __( 'Choose from the most used '.$location_label, 'epl' ),
		'not_found'						=> __( $location_label.' Not Found', 'epl' ),
	);
	$rewrite = array(
		'slug'						=> $location_slug,
		'with_front'					=> true,
		'hierarchical'					=> $hierarchical,
	);
	$args = array(
		'labels'					=> $labels,
		'hierarchical'					=> $hierarchical,
		'public'					=> true,
		'show_ui'					=> true,
		'show_admin_column'				=> true,
		'show_in_nav_menus'				=> true,
		'show_tagcloud'					=> true,
		'rewrite'						=> $rewrite,
	);
	register_taxonomy( 'location', array( 'property' , 'rental' , 'land', 'rural' , 'business', 'commercial' , 'commercial_land' ,'testimonial' ,'location',  ) , $args );
}
add_action( 'init', 'epl_register_taxonomy_location', 0 );
