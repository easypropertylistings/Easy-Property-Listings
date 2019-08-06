<?php
/**
 * TAXONOMY :: Location
 *
 * @package EPL
 * @subpackage  Taxonomy
 * @copyright   Copyright (c) 2019, Merv Barrett
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since   1.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers and sets up the tax_location taxonomy
 *
 * @since 1.0
 * @return void
 */
function epl_register_taxonomy_location() {
	$location_label = epl_tax_location_label();
	$location_slug  = sanitize_title( $location_label );
	$hierarchical   = defined( 'EPL_LOCATION_HIERARCHICAL' ) && EPL_LOCATION_HIERARCHICAL ? true : false;
	$rest           = defined( 'EPL_LOCATION_DISABLE_REST' ) && EPL_LOCATION_DISABLE_REST ? false : true;
	// Add define ('EPL_LOCATION_HIERARCHICAL', 'true'); into theme functions.php for hierarchical location taxonomy.

	$labels  = array(
		'name'                       => $location_label,
		'singular_name'              => $location_label,
		'menu_name'                  => $location_label, /* translators: %s: Location. */
		'all_items'                  => sprintf( __( 'All %s', 'easy-property-listings' ), $location_label ), /* translators: %s: Location. */
		'parent_item'                => sprintf( __( 'Parent %s', 'easy-property-listings' ), $location_label ), /* translators: %s: Location. */
		'parent_item_colon'          => sprintf( __( 'Parent %s :', 'easy-property-listings' ), $location_label ), /* translators: %s: Location. */
		'new_item_name'              => sprintf( __( 'New %s Name', 'easy-property-listings' ), $location_label ), /* translators: %s: Location. */
		'add_new_item'               => sprintf( __( 'Add New %s', 'easy-property-listings' ), $location_label ), /* translators: %s: Location. */
		'edit_item'                  => sprintf( __( 'Edit %s', 'easy-property-listings' ), $location_label ), /* translators: %s: Location. */
		'update_item'                => sprintf( __( 'Update %s', 'easy-property-listings' ), $location_label ), /* translators: %s: Location. */
		'separate_items_with_commas' => sprintf( __( 'Seperate %s with commas', 'easy-property-listings' ), $location_label ), /* translators: %s: Location. */
		'search_items'               => sprintf( __( 'Search %s', 'easy-property-listings' ), $location_label ), /* translators: %s: Location. */
		'add_or_remove_items'        => sprintf( __( 'Add or remove %s', 'easy-property-listings' ), $location_label ), /* translators: %s: Location. */
		'choose_from_most_used'      => sprintf( __( 'Choose from the most used %s', 'easy-property-listings' ), $location_label ), /* translators: %s: Location. */
		'not_found'                  => sprintf( __( '%s Not Found', 'easy-property-listings' ), $location_label ), /* translators: %s: Location. */
	);
	$rewrite = array(
		'slug'         => $location_slug,
		'with_front'   => true,
		'hierarchical' => $hierarchical,
	);
	$args    = array(
		'labels'            => $labels,
		'hierarchical'      => $hierarchical,
		'public'            => true,
		'show_ui'           => true,
		'show_admin_column' => true,
		'show_in_nav_menus' => true,
		'show_tagcloud'     => true,
		'show_in_rest'      => $rest,
		'rewrite'           => $rewrite,
	);
	register_taxonomy( 'location', array( 'property', 'rental', 'land', 'rural', 'business', 'commercial', 'commercial_land', 'testimonial', 'location' ), $args );
}
add_action( 'init', 'epl_register_taxonomy_location', 0 );
