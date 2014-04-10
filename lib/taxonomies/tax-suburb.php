<?php
/*
 * TAXONOMY :: Suburb
 */

function epl_register_taxonomy_suburb() {
	$labels = array(
		'name'                       => _x( 'Suburbs', 'Taxonomy General Name', 'epl_textdomain' ),
		'singular_name'              => _x( 'Suburb', 'Taxonomy Singular Name', 'epl_textdomain' ),
		'menu_name'                  => __( 'Suburbs', 'epl_textdomain' ),
		'all_items'                  => __( 'All Suburbs', 'epl_textdomain' ),
		'parent_item'                => __( 'Parent Suburb', 'epl_textdomain' ),
		'parent_item_colon'          => __( 'Parent Suburb:', 'epl_textdomain' ),
		'new_item_name'              => __( 'New Suburb Name', 'epl_textdomain' ),
		'add_new_item'               => __( 'Add New Suburb', 'epl_textdomain' ),
		'edit_item'                  => __( 'Edit Suburb', 'epl_textdomain' ),
		'update_item'                => __( 'Update Suburb', 'epl_textdomain' ),
		'separate_items_with_commas' => __( 'Separate Suburb with commas', 'epl_textdomain' ),
		'search_items'               => __( 'Search Suburb', 'epl_textdomain' ),
		'add_or_remove_items'        => __( 'Add or remove Suburb', 'epl_textdomain' ),
		'choose_from_most_used'      => __( 'Choose from the most used Suburb', 'epl_textdomain' ),
		'not_found'                  => __( 'Suburb Not Found', 'epl_textdomain' ),
	);
	$rewrite = array(
		'slug'                       => 'link-suburb',
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
	register_taxonomy( 'epl_tax_suburb', array( 'epl_property','epl_rental','epl_land', 'epl_commercial' ,'epl_testimonial' ,'epl_suburb', 'epl_rural' ) , $args );
}
add_action( 'init', 'epl_register_taxonomy_suburb', 0 );
