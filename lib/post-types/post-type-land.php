<?php
/*
 * POST TYPE :: Land
 */

function epl_register_custom_post_type_land() {
	$labels = array(
		'name'					=>	__('Land', 'epl'),
		'singular_name'			=>	__('Land', 'epl'),
		'menu_name'				=>	__('Land', 'epl'),
		'add_new'				=>	__('Add New', 'epl'),
		'add_new_item'			=>	__('Add New Land Listing', 'epl'),
		'edit_item'				=>	__('Edit Land Listing', 'epl'),
		'new_item'				=>	__('New Land Listing', 'epl'),
		'update_item'			=>	__('Update Land Listing', 'epl'),
		'all_items'				=>	__('All Land Listings', 'epl'),
		'view_item'				=>	__('View Land Listing', 'epl'),
		'search_items'			=>	__('Search Land Listing', 'epl'),
		'not_found'				=>	__('Land Listing Not Found', 'epl'),
		'not_found_in_trash'	=>	__('Land Listing Not Found in Trash', 'epl'),
		'parent_item_colon'		=>	__('Parent Land Listing:', 'epl')
	);

	$args = array(
		'labels'				=>	$labels,
		'public'				=>	true,
		'publicly_queryable'	=>	true,
		'show_ui'				=>	true,
		'show_in_menu'			=>	true,
		'query_var'				=>	true,
		'rewrite'				=>	array( 'slug' => 'land' ),
		'menu_icon'				=>	'dashicons-image-crop',
		//'menu_icon'				=>	plugins_url( 'post-types/icons/land.png' , dirname(__FILE__) ),
		'capability_type'		=>	'post',
		'has_archive'			=>	true,
		'hierarchical'			=>	false,
		'menu_position'			=>	5,
		'taxonomies'			=>	array( 'epl_tax_suburb', 'epl_tax_feature' ),
		'supports'				=>	array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments', 'revisions' )
	);
	register_post_type( 'epl_land', $args );
}
add_action( 'init', 'epl_register_custom_post_type_land', 0 );

// Manage Listing Columns
function manage_epl_land_columns_heading( $columns ) {
	$columns = array(
		'cb' => '<input type="checkbox" />',
		'property_thumb' => 'Featured Image',
		'title' => __( 'Address' ),
		'property_address_suburb' => __( 'Suburb' ),
		'property_heading' => __( 'Heading' ),
		'property_price' => __( 'Price' ),
		'under_offer' => __( 'U/O' ),
		'geo' => __( 'Geo' ),
		'property_price_view' => __( 'Price View' ),
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
add_filter( 'manage_edit-epl_land_columns', 'manage_epl_land_columns_heading' ) ;

function manage_epl_land_columns_value( $column, $post_id ) {
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
			
		/* If displaying the 'Under Offer' column. */
		case 'under_offer' :
			/* Get the post meta. */
			$property_under_offer = get_post_meta( $post_id, 'property_under_offer', true );

			/* If no duration is found, output a default message. */
			if ( empty( $property_under_offer) )
				echo __( '' );

			/* If there is a duration, append 'minutes' to the text string. */
			else
				 echo 'Yes';
			break;

		/* If displaying the 'Geocoding Debub' column. */
		case 'geo' :
			/* Get the post meta. */
			$property_address_coordinates = get_post_meta( $post_id, 'property_address_coordinates', true );

			/* If no duration is found, output a default message. */
			if (  $property_address_coordinates == ',' )
				echo 'NO' ;

			/* If there is a duration, append 'minutes' to the text string. */
			else
				// echo 'Yes';
				echo $property_address_coordinates;
			break;	
			
		/* If displaying the 'Price' column. */
		case 'property_price' :
			/* Get the post meta. */
			$price = get_post_meta( $post_id, 'property_price', true );

			/* If no duration is found, output a default message. */
			if ( empty( $price) )
				echo '';//echo __( '<strong>No Price Set</strong>' );

			/* If there is a duration, append 'minutes' to the text string. */
			else
				 echo '$' , $price;
			break;

		/* If displaying the 'Price View' column. */
		case 'property_price_view' :
			/* Get the post meta. */
			$view = get_post_meta( $post_id, 'property_price_view', true );

			/* If no duration is found, output a default message. */
			if ( empty( $view) )
				echo '';//echo __( '<strong>No Rent Set</strong>' );

			/* If there is a duration, append 'minutes' to the text string. */
			else
				 echo $view;
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
add_action( 'manage_epl_land_posts_custom_column', 'manage_epl_land_columns_value', 10, 2 );

// Manage Columns Sorting
function manage_epl_land_sortable_columns( $columns ) {
	$columns['property_status'] = 'property_status';
	$columns['property_address_suburb'] = 'property_address_suburb';
	return $columns;
}
add_filter( 'manage_edit-epl_land_sortable_columns', 'manage_epl_land_sortable_columns' );
