<?php
/* 
Loading the templates
Needs to work with other themes. These template files af a function that iThemes Builder needs to render the template.
*/

// Load Custom Template from Plugin Directory
function epl_load_core_single_templates($single_template) {
	if(epl_is_builder_framework_theme()) {
		$template_path = dirname( __FILE__ ) . '/ithemes-builder/';
	} else {
		$template_path = dirname( __FILE__ ) . '/default/';
	}
	
	global $post;
	if (  $post->post_type == 'epl_property'  ) {
		$single_template = $template_path.'single-epl_property.php';
	} elseif (  $post->post_type == 'epl_rental'  ) {
		$single_template = $template_path.'single-epl_property.php';
	} elseif (  $post->post_type == 'epl_land'  ) {
		$single_template = $template_path.'single-epl_property.php';
	} elseif (  $post->post_type == 'epl_commercial'  ) {
		$single_template = $template_path.'single-epl_property.php';
	} elseif (  $post->post_type == 'epl_rural'  ) {
		$single_template = $template_path.'single-epl_property.php';
	} elseif (  $post->post_type == 'epl_business'  ) {
		$single_template = $template_path.'single-epl_property.php';
	} elseif (  $post->post_type == 'epl_commercial_land'  ) {
		$single_template = $template_path.'single-epl_property.php';
	} elseif (  $post->post_type == 'epl_holiday_rental'  ) {
		$single_template = $template_path.'single-epl_property.php';
	} elseif (  $post->post_type == 'epl_suburb'  ) {
		$single_template = $template_path.'single-epl_suburb.php';
	} elseif (  $post->post_type == 'epl_directory'  ) {
		$single_template = $template_path.'single-epl_directory.php';
	} elseif (  $post->post_type == 'epl_testimonial'  ) {
		$single_template = $template_path.'single-epl_testimonial.php';
	}
	return $single_template;
}


function epl_load_core_archive_templates( $archive_template ) {
	if(epl_is_builder_framework_theme()) {
		$template_path = dirname( __FILE__ ) . '/ithemes-builder/';
	} else {
		$template_path = dirname( __FILE__ ) . '/default/';
	}
	
	global $post;
	if ( is_post_type_archive ( 'epl_property' ) ) {
		$archive_template = $template_path.'archive-epl_property.php';
	} elseif ( is_post_type_archive ( 'epl_rental' ) ) {
		$archive_template = $template_path.'archive-epl_property.php';
	} elseif ( is_post_type_archive ( 'epl_land' ) ) {
		$archive_template = $template_path.'archive-epl_property.php';
	} elseif ( is_post_type_archive ( 'epl_commercial' ) ) {
		$archive_template = $template_path.'archive-epl_property.php';
	} elseif ( is_post_type_archive ( 'epl_rural' ) ) {
		$archive_template = $template_path.'archive-epl_property.php';
	} elseif ( is_post_type_archive ( 'epl_holiday_rental' ) ) {
		$archive_template = $template_path.'archive-epl_property.php';
	} elseif ( is_post_type_archive ( 'epl_business' ) ) {
		$archive_template = $template_path.'archive-epl_property.php';
	} elseif ( is_post_type_archive ( 'epl_commercial_land' ) ) {
		$archive_template = $template_path.'archive-epl_property.php';
	} elseif ( is_post_type_archive ( 'epl_suburb' ) ) {
		$archive_template = $template_path.'archive-epl_suburb.php';
	} elseif ( is_post_type_archive ( 'epl_directory' ) ) {
		$archive_template = $template_path.'archive-epl_directory.php';
	} elseif ( is_post_type_archive ( 'epl_testimonial' ) ) {
		$archive_template = $template_path.'archive-epl_testimonial.php';
	}
	return $archive_template;
}

add_filter( 'single_template', 'epl_load_core_single_templates' );
add_filter( 'archive_template', 'epl_load_core_archive_templates' ) ;
