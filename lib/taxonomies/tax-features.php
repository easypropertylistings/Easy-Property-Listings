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
		'name'                       => _x( 'Features', 'Taxonomy General Name', 'easy-property-listings'  ),
		'singular_name'              => _x( 'Feature', 'Taxonomy Singular Name', 'easy-property-listings'  ),
		'menu_name'                  => __( 'Features', 'easy-property-listings'  ),
		'all_items'                  => __( 'All Features', 'easy-property-listings'  ),
		'parent_item'                => __( 'Parent Feature', 'easy-property-listings'  ),
		'parent_item_colon'          => __( 'Parent Feature:', 'easy-property-listings'  ),
		'new_item_name'              => __( 'New Feature Name', 'easy-property-listings'  ),
		'add_new_item'               => __( 'Add New Feature', 'easy-property-listings'  ),
		'edit_item'                  => __( 'Edit Feature', 'easy-property-listings'  ),
		'update_item'                => __( 'Update Feature', 'easy-property-listings'  ),
		'separate_items_with_commas' => __( 'Separate Feature with commas', 'easy-property-listings'  ),
		'search_items'               => __( 'Search Feature', 'easy-property-listings'  ),
		'add_or_remove_items'        => __( 'Add or remove Feature', 'easy-property-listings'  ),
		'choose_from_most_used'      => __( 'Choose from the most used Feature', 'easy-property-listings'  ),
		'not_found'                  => __( 'Feature Not Found', 'easy-property-listings'  ),
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
