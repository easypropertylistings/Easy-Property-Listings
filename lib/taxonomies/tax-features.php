<?php
/**
 * TAXONOMY :: Features
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
 * Registers and sets up the tax_feature taxonomy
 *
 * @since 1.0
 * @return void
 */
function epl_register_taxonomy_features() {
	$labels = array(
		'name'                       => _x( 'Features', 'Taxonomy General Name', 'epl' ),
		'singular_name'              => _x( 'Feature', 'Taxonomy Singular Name', 'epl' ),
		'menu_name'                  => __( 'Features', 'epl' ),
		'all_items'                  => __( 'All Features', 'epl' ),
		'parent_item'                => __( 'Parent Feature', 'epl' ),
		'parent_item_colon'          => __( 'Parent Feature:', 'epl' ),
		'new_item_name'              => __( 'New Feature Name', 'epl' ),
		'add_new_item'               => __( 'Add New Feature', 'epl' ),
		'edit_item'                  => __( 'Edit Feature', 'epl' ),
		'update_item'                => __( 'Update Feature', 'epl' ),
		'separate_items_with_commas' => __( 'Separate Feature with commas', 'epl' ),
		'search_items'               => __( 'Search Feature', 'epl' ),
		'add_or_remove_items'        => __( 'Add or remove Feature', 'epl' ),
		'choose_from_most_used'      => __( 'Choose from the most used Feature', 'epl' ),
		'not_found'                  => __( 'Feature Not Found', 'epl' ),
	);
	$rewrite = array(
		'slug'                       => 'feature',
		'with_front'                 => true,
		'hierarchical'               => false,
	);
	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => false,
		'public'                     => true,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => true,
		'show_tagcloud'              => true,
		'rewrite'                    => $rewrite,
	);
	register_taxonomy( 'tax_feature', array( 'property' , 'rental' , 'land', 'rural' , 'business', 'commercial' , 'commercial_land' ) , $args );
}
add_action( 'init', 'epl_register_taxonomy_features', 0 );
