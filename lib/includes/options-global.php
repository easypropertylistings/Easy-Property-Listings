<?php
// Image Sizes
function epl_image_sizes() {
	add_image_size( 'admin-list-thumb', 100, 100, true );
	add_image_size( 'epl-image-medium-crop', 300, 200, true );
}
add_action( 'after_setup_theme', 'epl_image_sizes' );

// Add image Size Names
function epl_image_size_names_choose( $epl_sizes ) {
	return array_merge( $epl_sizes, array(
		'epl-image-medium-crop' => __('Medium 300x200 hard Crop'),
	) );
}
add_filter( 'image_size_names_choose', 'epl_image_size_names_choose' );

function my_maps_init() {
	add_feed('georss','my_georss_feed');
}
add_action('init', 'my_maps_init');

function my_georss_feed() {
	include( 'templates/feed-georss.php' );
}

// Add Custom Post Types to Author Archives
function epl_property_author_archives($query) {
// Add 'Rentals' post type to author archives
	if ( $query->is_author )
		$query->set( 'post_type', array('epl_directory', 'epl_rental', 'epl_testimonial' , 'epl_property' , 'epl_post') );

	// Remove the action after it's run
	remove_action( 'pre_get_posts', 'epl_property_author_archives' );
}
add_action( 'pre_get_posts', 'epl_property_author_archives' );

// Add Custom Post Types to Author Archive Page	
function custom_post_author_archive($query) {
	if ($query->is_author)
		$query->set( 'post_type', array('epl_property', 'epl_rental', 'epl_testimonial', 'post', 'epl_commercial' , 'epl_land') );
	
	remove_action( 'pre_get_posts', 'custom_post_author_archive' );
}
add_action('pre_get_posts', 'custom_post_author_archive');

// Gravity Forms Filter for populating contact form with author email address
add_filter('gform_field_value_author_email', 'epl_populate_post_author_email');
function epl_populate_post_author_email($value) {
	global $post;    
	$author_email = get_the_author_meta('user_email', $post->post_author);    
	return $author_email;
}
