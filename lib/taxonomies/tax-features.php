<?php
/*
 * TAXONOMY :: Feature
 */

function epl_register_taxonomy_features() {
	$labels = array(
		'name'                       => _x( 'Features', 'Taxonomy General Name', 'epl_textdomain' ),
		'singular_name'              => _x( 'Feature', 'Taxonomy Singular Name', 'epl_textdomain' ),
		'menu_name'                  => __( 'Features', 'epl_textdomain' ),
		'all_items'                  => __( 'All Features', 'epl_textdomain' ),
		'parent_item'                => __( 'Parent Feature', 'epl_textdomain' ),
		'parent_item_colon'          => __( 'Parent Feature:', 'epl_textdomain' ),
		'new_item_name'              => __( 'New Feature Name', 'epl_textdomain' ),
		'add_new_item'               => __( 'Add New Feature', 'epl_textdomain' ),
		'edit_item'                  => __( 'Edit Feature', 'epl_textdomain' ),
		'update_item'                => __( 'Update Feature', 'epl_textdomain' ),
		'separate_items_with_commas' => __( 'Separate Feature with commas', 'epl_textdomain' ),
		'search_items'               => __( 'Search Feature', 'epl_textdomain' ),
		'add_or_remove_items'        => __( 'Add or remove Feature', 'epl_textdomain' ),
		'choose_from_most_used'      => __( 'Choose from the most used Feature', 'epl_textdomain' ),
		'not_found'                  => __( 'Feature Not Found', 'epl_textdomain' ),
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
	register_taxonomy( 'epl_tax_feature', array( 'epl_property','epl_rental','epl_land', 'epl_commercial','epl_rural' ) , $args );
}
add_action( 'init', 'epl_register_taxonomy_features', 0 );
