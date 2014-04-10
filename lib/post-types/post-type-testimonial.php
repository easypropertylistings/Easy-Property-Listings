<?php
/*
 * POST TYPE :: Testimonial
 */
 
function epl_register_custom_post_type_testimonial() {
	$labels = array(
		'name'                => _x( 'Testimonials', 'Post Type General Name', 'epl_textdomian' ),
		'singular_name'       => _x( 'Testimonial', 'Post Type Singular Name', 'epl_textdomian' ),
		'menu_name'           => __( 'Testimonials', 'epl_textdomian' ),
		'parent_item_colon'   => __( 'Parent Testimonial:', 'epl_textdomian' ),
		'all_items'           => __( 'All Testimonial', 'epl_textdomian' ),
		'view_item'           => __( 'View Testimonial', 'epl_textdomian' ),
		'add_new_item'        => __( 'Add New Testimonial', 'epl_textdomian' ),
		'add_new'             => __( 'Add New', 'epl_textdomian' ),
		'edit_item'           => __( 'Edit Testimonial', 'epl_textdomian' ),
		'update_item'         => __( 'Update Testimonial', 'epl_textdomian' ),
		'search_items'        => __( 'Search Testimonial', 'epl_textdomian' ),
		'not_found'           => __( 'Testimonial Not found', 'epl_textdomian' ),
		'not_found_in_trash'  => __( 'Testimonial Not found in Trash', 'epl_textdomian' ),
	);
	$rewrite = array(
		'slug'                => 'success-story',
		'with_front'          => true,
		'pages'               => true,
		'feeds'               => true,
	);
	$args = array(
		'label'               => __( 'epl_testimonial', 'epl_textdomian' ),
		'description'         => __( 'Store your Testimonials Here ', 'epl_textdomian' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail' ),
		'taxonomies'          => array ('epl_tax_suburb'),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => true,
		'show_in_admin_bar'   => true,
		'menu_position'       => 9,
		'menu_icon'           => 'dashicons-format-chat',
		//'menu_icon'           => plugins_url( 'post-types/icons/quill.png' , dirname(__FILE__) ),
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'rewrite'             => $rewrite,
		'capability_type'     => 'page',
	);
	register_post_type( 'epl_testimonial', $args );
}
add_action( 'init', 'epl_register_custom_post_type_testimonial', 0 );
