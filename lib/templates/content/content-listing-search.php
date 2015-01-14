<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

extract($_REQUEST);

$args = array();
if(!empty($property_type)) {
	$args['post_type']	=	$property_type;
} else {
	$args['post_type']	=	array('property', 'rental', 'commercial' , 'land');
}

$meta_query = array();
if(isset($property_security_system) && !empty($property_security_system)) {
	$meta_query[] = array(
		'key'		=>	'property_security_system',
		'value'		=>	array('yes', '1'),
		'compare'	=>	'IN'
	);
}
if(isset($property_air_conditioning) && !empty($property_air_conditioning)) {
	$meta_query[] = array(
		'key'		=>	'property_air_conditioning',
		'value'		=>	array('yes', '1'),
		'compare'	=>	'IN'
	);
}
if(isset($property_pool) && !empty($property_pool)) {
	$meta_query[] = array(
		'key'		=>	'property_pool',
		'value'		=>	array('yes', '1'),
		'compare'	=>	'IN'
	);
}
if(!empty($property_bedrooms)) {
	$meta_query[] = array(
		'key'		=>	'property_bedrooms',
		'value'		=>	$property_bedrooms,
		'compare'	=>	'>='
	);
}
if(!empty($property_rooms)) {
	$meta_query[] = array(
		'key'		=>	'property_rooms',
		'value'		=>	$property_rooms,
		'compare'	=>	'>='
	);
}
if(!empty($property_bathrooms)) {
	$meta_query[] = array(
		'key'		=>	'property_bathrooms',
		'value'		=>	$property_bathrooms,
		'compare'	=>	'>='
	);
}
if(!empty($property_carport)) {
	$meta_query[] = array(
		'key'		=>	'property_carport',
		'value'		=>	$property_carport,
		'compare'	=>	'>='
	);
}
if(!empty($meta_query)) {
	$args['meta_query'] = $meta_query;
}

if(!empty($args)) {
	$args['posts_per_page'] = get_option('posts_per_page');
}


query_posts($args);
if(have_posts()) :
	while ( have_posts() ) : the_post();
		global $post;		
	endwhile;
endif;
wp_reset_query();
