<?php
/* 
 * Loading the templates
 * Needs to work with other themes. These template files af a function that iThemes Builder needs to render the template.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// Load Custom Template from Plugin Directory
function epl_load_core_templates($template) {

	if( epl_is_builder_framework_theme() ) {
		$template_path = EPL_PATH_TEMPLATES_POST_TYPES_ITHEMES;
	} elseif ( epl_is_genesis_framework_theme() ) {	
		$template_path = EPL_PATH_TEMPLATES_POST_TYPES_GENESIS;
	} else {
		$template_path = EPL_PATH_TEMPLATES_POST_TYPES_DEFAULT;
	}
	$post_tpl	=	'';
	$epl_posts 	= array( 'property' , 'land' , 'rental' , 'rural' , 'commercial' , 'business' , 'commercial_land' );
	
	if ( is_single() && in_array( get_post_type(), $epl_posts ) ) {
	
		$common_tpl		= 'single-listing.php';
		$post_tpl 		= 'single-'.get_post_type().'.php';
		$find[] 		= $post_tpl;
		$find[] 		= epl_template_path() . $post_tpl;
		$find[] 		=  $common_tpl;
		$find[] 		= $common_tpl;
		$find[] 		= epl_template_path() . $common_tpl;
		
	} elseif ( is_post_type_archive( $epl_posts ) ) {
		$common_tpl		= 'archive-listing.php';
		$post_tpl 		= 'archive-'.get_post_type().'.php';
		$find[] 		=  $post_tpl;
		$find[] 		= epl_template_path() . $post_tpl;
		$find[] 		=  $common_tpl;
		$find[] 		= epl_template_path() . $common_tpl;
		
	} elseif ( is_tax ( 'location' ) ) {

		$term   		= get_queried_object();
		$common_tpl		= 'archive-listing.php';
		$post_tpl 		= 'taxonomy-' . $term->taxonomy . '.php';
		$find[] 		= 'taxonomy-' . $term->taxonomy . '-' . $term->slug . '.php';
		$find[] 		= epl_template_path() . 'taxonomy-' . $term->taxonomy . '-' . $term->slug . '.php';
		$find[] 		= 'taxonomy-' . $term->taxonomy . '.php';
		$find[] 		= epl_template_path() . 'taxonomy-' . $term->taxonomy . '.php';
		$find[] 		= $common_tpl;
		$find[] 		= $post_tpl;
		$find[] 		= epl_template_path() . $common_tpl;

	}
	
	if ( $post_tpl ) {
		$template       = locate_template( array_unique( $find ) );
		if(!$template) {
			$template	=	$template_path . $common_tpl;
		}
	}
	return $template;

}

add_filter( 'template_include', 'epl_load_core_templates' );

