<?php
/* 
 * Loading the templates
 * Needs to work with other themes. These template files af a function that iThemes Builder needs to render the template.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// Load Custom Template from Plugin Directory
function epl_load_core_single_templates($single_template) {
	if( epl_is_builder_framework_theme() ) {
		$template_path = EPL_PATH_TEMPLATES_POST_TYPES_ITHEMES;
	} elseif ( epl_is_genesis_framework_theme() ) {	
		$template_path = EPL_PATH_TEMPLATES_POST_TYPES_GENESIS;
	} else {
		$template_path = EPL_PATH_TEMPLATES_POST_TYPES_DEFAULT;
	}
	
	// Load Template from Child Theme Directory
	if ( $single_template_child = locate_template( 'single-listing.php' ) ) {
	
		global $post;
		if (  $post->post_type == 'property'  ) {
			$single_template = $single_template_child;
		} elseif (  $post->post_type == 'rental'  ) {
			$single_template = $single_template_child;
		} elseif (  $post->post_type == 'land'  ) {
			$single_template = $single_template_child;
		} elseif (  $post->post_type == 'commercial'  ) {
			$single_template = $single_template_child;
		} elseif (  $post->post_type == 'rural'  ) {
			$single_template = $single_template_child;
		} elseif (  $post->post_type == 'business'  ) {
			$single_template = $single_template_child;
		} elseif (  $post->post_type == 'commercial_land'  ) {
			$single_template = $single_template_child;
		}
		return $single_template;
	}
	else {

		global $post;
		if (  $post->post_type == 'property'  ) {
			$single_template = $template_path.'single-listing.php';
		} elseif (  $post->post_type == 'rental'  ) {
			$single_template = $template_path.'single-listing.php';
		} elseif (  $post->post_type == 'land'  ) {
			$single_template = $template_path.'single-listing.php';
		} elseif (  $post->post_type == 'commercial'  ) {
			$single_template = $template_path.'single-listing.php';
		} elseif (  $post->post_type == 'rural'  ) {
			$single_template = $template_path.'single-listing.php';
		} elseif (  $post->post_type == 'business'  ) {
			$single_template = $template_path.'single-listing.php';
		} elseif (  $post->post_type == 'commercial_land'  ) {
			$single_template = $template_path.'single-listing.php';
		}
		return $single_template;
	}

}

// Archive Templates
function epl_load_core_archive_templates( $archive_template ) {
	
	if(epl_is_builder_framework_theme()) {
		$template_path = EPL_PATH_TEMPLATES_POST_TYPES_ITHEMES;
	} elseif ( epl_is_genesis_framework_theme() ) {	
		$template_path = EPL_PATH_TEMPLATES_POST_TYPES_GENESIS;
	} else {
		$template_path = EPL_PATH_TEMPLATES_POST_TYPES_DEFAULT;
	}
	
	
	// Load Template from Child Theme Directory
	if ( $archive_template_child = locate_template( 'archive-listing.php' ) ) {
		global $post;
		if (  $post->post_type == 'property'  ) {
			$archive_template = $archive_template_child;
		} elseif (  $post->post_type == 'rental'  ) {
			$archive_template = $archive_template_child;
		} elseif (  $post->post_type == 'land'  ) {
			$archive_template = $archive_template_child;
		} elseif (  $post->post_type == 'commercial'  ) {
			$archive_template = $archive_template_child;
		} elseif (  $post->post_type == 'rural'  ) {
			$archive_template = $archive_template_child;
		} elseif (  $post->post_type == 'business'  ) {
			$archive_template = $archive_template_child;
		} elseif (  $post->post_type == 'commercial_land'  ) {
			$archive_template = $archive_template_child;
		}
		echo $archive_template; die;
		return $archive_template;
	} else {
		global $post;
		if ( is_post_type_archive ( 'property' ) ) {
			$archive_template = $template_path.'archive-listing.php';
		} elseif ( is_post_type_archive ( 'rental' ) ) {
			$archive_template = $template_path.'archive-listing.php';
		} elseif ( is_post_type_archive ( 'land' ) ) {
			$archive_template = $template_path.'archive-listing.php';
		} elseif ( is_post_type_archive ( 'commercial' ) ) {
			$archive_template = $template_path.'archive-listing.php';
		} elseif ( is_post_type_archive ( 'rural' ) ) {
			$archive_template = $template_path.'archive-listing.php';
		} elseif ( is_post_type_archive ( 'business' ) ) {
			$archive_template = $template_path.'archive-listing.php';
		} elseif ( is_post_type_archive ( 'commercial_land' ) ) {
			$archive_template = $template_path.'archive-listing.php';
		}
		return $archive_template;
	}
}

// Taxonomy Templates
function epl_load_core_taxonomy_templates( $taxonomy_template ) {
	if(epl_is_builder_framework_theme()) {
		$template_path = EPL_PATH_TEMPLATES_POST_TYPES_ITHEMES;
	} elseif ( epl_is_genesis_framework_theme() ) {	
		$template_path = EPL_PATH_TEMPLATES_POST_TYPES_GENESIS;
	} else {
		$template_path = EPL_PATH_TEMPLATES_POST_TYPES_DEFAULT;
	}
	
	// Load Template from Child Theme Directory
	if ( $taxonomy_template_child = locate_template( 'archive-listing.php' ) ) {
		
		global $post;
		if ( is_tax ( 'tax_feature' ) ) {
			$taxonomy_template = $taxonomy_template_child;
		} elseif ( is_tax ( 'location' ) ) {
			$taxonomy_template = $taxonomy_template_child;
		} 
		return $taxonomy_template;
		
	} else {
		
		global $post;
		if ( is_tax ( 'tax_feature' ) ) {
			$taxonomy_template = $template_path.'archive-listing.php';
		} elseif ( is_tax ( 'location' ) ) {
			$taxonomy_template = $template_path.'archive-listing.php';
		} 
		return $taxonomy_template;
	}
}

add_filter( 'single_template', 'epl_load_core_single_templates' );
add_filter( 'archive_template', 'epl_load_core_archive_templates' );
add_filter( 'taxonomy_template', 'epl_load_core_taxonomy_templates' ) ;

