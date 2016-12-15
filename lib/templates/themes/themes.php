<?php
/**
 * Loading the templates
 * Needs to work with other themes. These template files af a function that iThemes Builder needs to render the template.
 *
 * @package     EPL
 * @subpackage  Templates/Themes
 * @copyright   Copyright (c) 2015, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Load Custom Template from Plugin Directory
 *
 * @since 1.0
 */
function epl_load_core_templates($template) {

	global $epl_settings;

	$template_path = epl_get_content_path();

	if( isset($epl_settings['epl_feeling_lucky']) && $epl_settings['epl_feeling_lucky'] == 'on') {
		return $template;
	}

	$post_tpl	=	'';
	if ( is_epl_post_single() ) {

		$common_tpl		= apply_filters('epl_common_single_template','single-listing.php');
		$post_tpl 		= 'single-'.str_Replace('_','-',get_post_type()).'.php';
		$find[] 		= $post_tpl;
		$find[] 		= epl_template_path() . $post_tpl;
		$find[] 		= $common_tpl;
		$find[] 		= epl_template_path() . $common_tpl;

	} elseif ( is_epl_post_archive() ) {
		$common_tpl		= apply_filters('epl_common_archive_template','archive-listing.php');
		$post_tpl 		= 'archive-'.str_Replace('_','-',get_post_type()).'.php';
		$find[] 		=  $post_tpl;
		$find[] 		= epl_template_path() . $post_tpl;
		$find[] 		=  $common_tpl;
		$find[] 		= epl_template_path() . $common_tpl;
	} elseif ( is_tax ( 'location' ) || is_tax ( 'tax_feature' ) ) {

		$term   		= get_queried_object();
		$common_tpl		= apply_filters('epl_common_taxonomy_template','archive-listing.php');;
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
        /*** Template found in theme ? ***/
		$template       = locate_template( array_unique( $find ) );
		if(!$template) {
            /*** If not found, fallback to extension's default template ***/
			$template	=	$template_path . $common_tpl;

            if( !file_exists($template) ) {
                /*** If extension doesnt have templates, fallback to core templates ***/
                $template	=	EPL_PATH_TEMPLATES_CONTENT . $common_tpl;
            }
		}
	}
	return $template;

}
add_filter( 'template_include', 'epl_load_core_templates' );

/**
 * Template Loader for Single Listings
 *
 * @since 2.0
 */
function epl_render_single_post() {

	if( epl_is_builder_framework_theme() ) {
		$template_path = EPL_PATH_TEMPLATES_POST_TYPES_ITHEMES;
	} elseif ( epl_is_genesis_framework_theme() ) {
		$template_path = EPL_PATH_TEMPLATES_POST_TYPES_GENESIS;
	}  elseif ( epl_is_divi_framework_theme() ) {
		$template_path = EPL_PATH_TEMPLATES_POST_TYPES. 'divi/';
	} elseif ( file_exists(EPL_PATH_TEMPLATES_POST_TYPES.epl_get_active_theme()) ) {
		$template_path = EPL_PATH_TEMPLATES_POST_TYPES . trailingslashit( epl_get_active_theme() );
	} else {
		$template_path = EPL_PATH_TEMPLATES_POST_TYPES_DEFAULT;
	}
	$common_tpl		= 'single-listing.php';
	$template		=	$template_path . $common_tpl;
	include($template);
}
add_action('epl_render_single_post','epl_render_single_post');

/**
 * Template Loader for Archive
 *
 * @since 2.0
 */
function epl_render_archive_post() {

	if( epl_is_builder_framework_theme() ) {
		$template_path = EPL_PATH_TEMPLATES_POST_TYPES_ITHEMES;
	} elseif ( epl_is_genesis_framework_theme() ) {
		$template_path = EPL_PATH_TEMPLATES_POST_TYPES_GENESIS;
	} elseif ( epl_is_divi_framework_theme() ) {
		$template_path = EPL_PATH_TEMPLATES_POST_TYPES. 'divi/';
	} elseif ( file_exists(EPL_PATH_TEMPLATES_POST_TYPES.epl_get_active_theme()) ) {
		$template_path = EPL_PATH_TEMPLATES_POST_TYPES . trailingslashit( epl_get_active_theme() );
	} else {
		$template_path = EPL_PATH_TEMPLATES_POST_TYPES_DEFAULT;
	}
	$common_tpl		= 'archive-listing.php';
	$template		=	$template_path . $common_tpl;
	include($template);
}
add_action('epl_render_archive_post','epl_render_archive_post');

/**
 * Add extra class for twentysixteen theme
 *
 * @since 3.0
 */
function epl_active_theme_name_twentysixteen($class) {

	if( epl_get_active_theme() == 'twentysixteen' && ! is_single() ) {
		$class = $class.' content-area';
	}
	return $class;
}
add_filter('epl_active_theme_name','epl_active_theme_name_twentysixteen');
