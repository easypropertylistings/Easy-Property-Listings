<?php
/*
 * POST TYPE :: Directory
 */

function epl_register_custom_post_type_directory() {
	$labels = array(
		'name'                => _x( 'Staff Directory', 'Post Type General Name', 'epl_textdomian' ),
		'singular_name'       => _x( 'Staff', 'Post Type Singular Name', 'epl_textdomian' ),
		'menu_name'           => __( 'Staff', 'epl_textdomian' ),
		'parent_item_colon'   => __( 'Parent Staff Member:', 'epl_textdomian' ),
		'all_items'           => __( 'All Staff Member', 'epl_textdomian' ),
		'view_item'           => __( 'View Staff Member', 'epl_textdomian' ),
		'add_new_item'        => __( 'Add New Staff Member', 'epl_textdomian' ),
		'add_new'             => __( 'Add New', 'epl_textdomian' ),
		'edit_item'           => __( 'Edit Staff Member', 'epl_textdomian' ),
		'update_item'         => __( 'Update Staff Member', 'epl_textdomian' ),
		'search_items'        => __( 'Search Staff Members', 'epl_textdomian' ),
		'not_found'           => __( 'Staff Member Not found', 'epl_textdomian' ),
		'not_found_in_trash'  => __( 'Staff Member Not found in Trash', 'epl_textdomian' ),
	);
	$rewrite = array(
		'slug'                => 'directory',
		'with_front'          => true,
		'pages'               => true,
		'feeds'               => true,
	);
	$args = array(
		'label'               => __( 'epl_directory', 'epl_textdomian' ),
		'description'         => __( 'Store your Staff Members Here ', 'epl_textdomian' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'revisions', ),
		'taxonomies'          => array( 'epl_tax_suburb', 'epl_tax_community_feature' ),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => true,
		'show_in_admin_bar'   => true,
		'menu_position'       => 9,
		'menu_icon'           => 'dashicons-groups',
		//'menu_icon'           => plugins_url( 'post-types/icons/staff.png' , dirname(__FILE__) ),
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'rewrite'             => $rewrite,
		'capability_type'     => 'page',
	);
	register_post_type( 'epl_directory', $args );
}
add_action( 'init', 'epl_register_custom_post_type_directory', 0 );
