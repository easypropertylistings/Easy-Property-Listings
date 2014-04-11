<?php
/*
 * POST TYPE :: Holiday Rental
 */
 
function epl_register_custom_post_type_holiday_rental() {
	$labels = array(
		'name'					=>	__('Holiday Rentals', 'epl'),
		'singular_name'			=>	__('Holiday Rental', 'epl'),
		'menu_name'				=>	__('Holiday Rentals', 'epl'),
		'add_new'				=>	__('Add New', 'epl'),
		'add_new_item'			=>	__('Add New Holiday Rental', 'epl'),
		'edit_item'				=>	__('Edit Holiday Rental', 'epl'),
		'new_item'				=>	__('New Holiday Rental', 'epl'),
		'update_item'			=>	__('Update Holiday Rental', 'epl'),
		'all_items'				=>	__('All Holiday Rentals', 'epl'),
		'view_item'				=>	__('View Holiday Rental', 'epl'),
		'search_items'			=>	__('Search Holiday Rentals', 'epl'),
		'not_found'				=>	__('Holiday Rental Not Found', 'epl'),
		'not_found_in_trash'	=>	__('Holiday Rental Not Found in Trash', 'epl'),
		'parent_item_colon'		=>	__('Parent Holiday Rental:', 'epl')
	);

	$args = array(
		'labels'				=>	$labels,
		'public'				=>	true,
		'publicly_queryable'	=>	true,
		'show_ui'				=>	true,
		'show_in_menu'			=>	true,
		'query_var'				=>	true,
		'rewrite'				=>	array( 'slug' => 'holiday_rental' ),
		'menu_icon'				=>	plugins_url( 'post-types/icons/home.png' , dirname(__FILE__) ),
		'capability_type'		=>	'post',
		'has_archive'			=>	true,
		'hierarchical'			=>	false,
		'menu_position'			=>	5,
		'taxonomies'			=>	array( 'epl_tax_suburb', 'epl_tax_feature' ),
		'supports'				=>	array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments', 'revisions' )
	);
	register_post_type( 'epl_holiday_rental', $args );
}
add_action( 'init', 'epl_register_custom_post_type_holiday_rental', 0 );

// Manage Listing Columns
function epl_manage_epl_holiday_rental_columns_heading( $columns ) {
	$columns = array(
		'cb' => '<input type="checkbox" />',
		'property_thumb' => 'Featured Image',
		'title' => __( 'Address' ),
		'property_address_suburb' => __( 'Suburb' ),
		'property_heading' => __( 'Heading' ),
		'property_rent' => __( 'Rent' ),
		'property_bond' => __( 'Bond' ),
		'geo' => __( 'Geocoded' ),
		'property_inspection_times' => __('Inspection Times'),
		'property_status' => ('Type'),
		'author' => __( 'Agent' ),
		'date' => __( 'Date' )
	);
	
	$geo_debug = get_option('epl_debug');
	if ( $geo_debug != 1 ) {
		unset($columns['geo']);
	}
	return $columns;
}
add_filter( 'manage_edit-epl_holiday_rental_columns', 'epl_manage_epl_holiday_rental_columns_heading' ) ;

function epl_manage_epl_holiday_rental_columns_value( $column, $post_id ) {
	global $post;
	switch( $column ) {
	
		/* If displaying the 'Featured' image column. */
		case 'property_thumb' :
			/* Get the featured Image */
			if( function_exists('the_post_thumbnail') )
				echo the_post_thumbnail('admin-list-thumb');
			break;
			
		case 'property_address_suburb' :
			/* Get the post meta. */
			$property_address_suburb = stripslashes(get_post_meta( $post_id, 'property_address_suburb', true ));
			echo $property_address_suburb;
			break;
	
		/* If displaying the 'Heading' column. */
		case 'property_heading' :
			/* Get the post meta. */
			$heading = get_post_meta( $post_id, 'property_heading', true );

			/* If no duration is found, output a default message. */
			if ( empty( $heading) )
				echo __( '<strong>Important! Set a Heading</strong>' );

			/* If there is a duration, append 'minutes' to the text string. */
			else
				 echo $heading;
			break;

		/* If displaying the 'Rent' column. */
		case 'property_rent' :
			/* Get the post meta. */
			$rent = get_post_meta( $post_id, 'property_rent', true );

			/* If no duration is found, output a default message. */
			if ( empty( $rent) )
				echo '';//echo __( '<strong>No Rent Set</strong>' );

			/* If there is a duration, append 'minutes' to the text string. */
			else
				 echo '$',$rent;
			break;

		/* If displaying the 'Bond' column. */
		case 'property_bond' :
			/* Get the post meta. */
			$bond = get_post_meta( $post_id, 'property_bond', true );

			/* If no duration is found, output a default message. */
			if ( empty( $bond) )
				echo '';//echo __( '<strong>No Bond Set</strong>' );

			/* If there is a duration, append 'minutes' to the text string. */
			else
				 echo '$',$bond;
			break;
			
		/* If displaying the 'Geocoding' column. */
		case 'geo' :
			/* Get the post meta. */
			$property_address_coordinates = get_post_meta( $post_id, 'property_address_coordinates', true );

			/* If no duration is found, output a default message. */
			if (  $property_address_coordinates == ',' )
				echo 'NO';

			/* If there is a duration, append 'minutes' to the text string. */
			else
				echo $property_address_coordinates;
			break;
			
		/* If displaying the 'home-open' column. */
		case 'property_inspection_times' :
			/* Get the post meta. */
			$homeopen = get_post_meta( $post_id, 'property_inspection_times', true );
			echo $homeopen;
			break;

		/* If displaying the 'real-estate' column. */
		case 'property_status' :
			/* Get the genres for the post. */
			$property_status = ucfirst( get_post_meta( $post_id, 'property_status', true ) );
			echo '<span class="type_'.strtolower($property_status).'">'.$property_status.'</span>';
			break;

		/* Just break out of the switch statement for everything else. */
		default :
			break;
	}
}
add_action( 'manage_epl_holiday_rental_posts_custom_column', 'epl_manage_epl_holiday_rental_columns_value', 10, 2 );

// Manage Columns Sorting
function epl_manage_epl_holiday_rental_sortable_columns( $columns ) {
	$columns['property_status'] = 'property_status';
	$columns['property_inspection_times'] = 'property_inspection_times';
	$columns['property_address_suburb'] = 'property_address_suburb';
	return $columns;
}
add_filter( 'manage_edit-epl_holiday_rental_sortable_columns', 'epl_manage_epl_holiday_rental_sortable_columns' );
