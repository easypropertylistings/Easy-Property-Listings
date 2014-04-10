<?php
/*
 * POST TYPE :: Suburb
 */

function epl_register_custom_post_type_suburb() {
	$labels = array(
		'name'					=>	__('Suburb', 'epl'),
		'singular_name'			=>	__('Suburb Profile', 'epl'),
		'menu_name'				=>	__('Suburb Profiles', 'epl'),
		'add_new'				=>	__('Add New', 'epl'),
		'add_new_item'			=>	__('Add New Suburb Profile', 'epl'),
		'edit_item'				=>	__('Edit Suburb Profile', 'epl'),
		'new_item'				=>	__('New Suburb Profile', 'epl'),
		'update_item'			=>	__('Update Suburb Profile', 'epl'),
		'all_items'				=>	__('All Suburbs', 'epl'),
		'view_item'				=>	__('View Suburb', 'epl'),
		'search_items'			=>	__('Search Suburb Profiles', 'epl'),
		'not_found'				=>	__('Suburb Profile Not Found', 'epl'),
		'not_found_in_trash'	=>	__('Suburb Profile Not Found in Trash', 'epl'),
		'parent_item_colon'		=>	__('Parent Suburb:', 'epl')
	);

	$args = array(
		'labels'				=>	$labels,
		'public'				=>	true,
		'publicly_queryable'	=>	true,
		'show_ui'				=>	true,
		'show_in_menu'			=>	true,
		'query_var'				=>	true,
		'rewrite'				=>	array( 'slug' => 'suburb' ),
		'menu_icon'				=>	plugins_url( 'post-types/icons/maps-stack.png' , dirname(__FILE__) ),
		'menu_icon'				=>	'dashicons-analytics',
		'capability_type'		=>	'post',
		'has_archive'			=>	true,
		'hierarchical'			=>	false,
		'menu_position'			=>	9,
		'taxonomies'			=>	array(	'epl_tax_suburb', 'epl_tax_community_feature' ),
		'supports'				=>	array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments', 'revisions' )
	);
	register_post_type( 'epl_suburb', $args );
}
add_action( 'init', 'epl_register_custom_post_type_suburb', 0 );

// Manage Listing Columns
function manage_epl_suburb_columns_heading( $columns ) {
	$columns = array(
		'cb' => '<input type="checkbox" />',
		'property_thumb' => 'Featured Image',
		'title' => __( 'Address' ),
		'post_code' => __( 'Postcode' ),
		'geo' => __( 'Geo' ),
		'author' => __( 'Agent' ),
		'date' => __( 'Date' )
	);
	
	$geo_debug = get_option('epl_debug');
	if ( $geo_debug != 1 ) {
		unset($columns['geo']);
	}
	return $columns;
}
add_filter( 'manage_edit-epl_suburb_columns', 'manage_epl_suburb_columns_heading' ) ;

function manage_epl_suburb_columns_value( $column, $post_id ) {
	global $post;
	switch( $column ) {	
		/* If displaying the 'Featured' image column. */
		case 'property_thumb' :
			/* Get the featured Image */
			if( function_exists('the_post_thumbnail') )
				echo the_post_thumbnail('admin-list-thumb');
			break;

		/* If displaying the 'Geocoding Debug column. */
		case 'post_code' :
			/* Get the post meta. */
			$suburb_postcode = get_post_meta( $post_id, 'suburb_postcode', true );

			/* If no duration is found, output a default message. */
			if (  $suburb_postcode == '' )
				echo 'Not Set' ;

			/* If there is a duration, append 'minutes' to the text string. */
			else
				// echo 'Yes';
				echo $suburb_postcode;
			break;	
			
		/* If displaying the 'Geocoding Debug column. */
		case 'geo' :
			/* Get the post meta. */
			$suburb_map_location = get_post_meta( $post_id, 'suburb_map_location', true );

			/* If no duration is found, output a default message. */
			if (  $suburb_map_location == ',' )
				echo 'NO' ;

			/* If there is a duration, append 'minutes' to the text string. */
			else
				// echo 'Yes';
				echo $suburb_map_location;
			break;	

		/* Just break out of the switch statement for everything else. */
		default :
			break;
	}
}
add_action( 'manage_epl_suburb_posts_custom_column', 'manage_epl_suburb_columns_value', 10, 2 );

// Manage Columns Sorting
function manage_epl_suburb_sortable_columns( $columns ) {
	$columns['suburb_postcode'] = 'suburb_postcode';
	return $columns;
}
add_filter( 'manage_edit-epl_suburb_sortable_columns', 'manage_epl_suburb_sortable_columns' );
