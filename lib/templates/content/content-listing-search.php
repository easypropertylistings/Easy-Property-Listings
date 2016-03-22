<?php
/*
 * Search Widget Content.
 *
 * @package     EPL
 * @subpackage  Templates/Content
 * @copyright   Copyright (c) 2015, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
$_REQUEST = array_map('sanitize_text_field',$_REQUEST);

extract($_REQUEST);

$args = array();
if(!empty($property_type)) {
	$args['post_type']	=	$property_type;
} else {
	$args['post_type']	=	array('property', 'rental', 'commercial' , 'land');
}

$meta_query = array();
if(isset($property_security_system) && in_array($property_security_system, array('yes', '1')) ) {
	$meta_query[] = array(
		'key'		=>	'property_security_system',
		'value'		=>	array('yes', '1'),
		'compare'	=>	'IN'
	);
}
if(isset($property_air_conditioning) && in_array($property_air_conditioning, array('yes', '1')) ) {
	$meta_query[] = array(
		'key'		=>	'property_air_conditioning',
		'value'		=>	array('yes', '1'),
		'compare'	=>	'IN'
	);
}
if(isset($property_pool) && in_array($property_pool, array('yes', '1')) ) {
	$meta_query[] = array(
		'key'		=>	'property_pool',
		'value'		=>	array('yes', '1'),
		'compare'	=>	'IN'
	);
}
if(intval($property_bedrooms) > 0) {
	$meta_query[] = array(
		'key'		=>	'property_bedrooms',
		'value'		=>	intval($property_bedrooms),
		'compare'	=>	'>='
	);
}
if(intval($property_bathrooms) > 0) {
	$meta_query[] = array(
		'key'		=>	'property_bathrooms',
		'value'		=>	intval($property_bathrooms),
		'compare'	=>	'>='
	);
}
if(intval($property_rooms) > 0) {
	$meta_query[] = array(
		'key'		=>	'property_rooms',
		'value'		=>	intval($property_rooms),
		'compare'	=>	'>='
	);
}
if(intval($property_carport) > 0) {
	$meta_query[] = array(
		'key'		=>	'property_carport',
		'value'		=>	intval($property_carport),
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
