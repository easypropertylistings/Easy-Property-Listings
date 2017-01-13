<?php
/**
 * Conditional Tags
 *
 * @package     EPL
 * @subpackage  Functions/ConditionalTags
 * @copyright   Copyright (c) 2015, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.2
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * get list of core epl posts
 *
 * @since 2.3
 */
function epl_get_core_post_types() {
	return apply_filters('epl_core_post_types',array('rural','property','rental','land','commercial','commercial_land','business') );
}

/**
 * get list of sales epl posts
 *
 * @since 2.3
 */
function epl_get_core_sales_post_types() {
	return apply_filters('epl_core_sales_post_types',array('rural','property','land','commercial','commercial_land','business') );
}

/**
 * get list of rental epl posts
 *
 * @since 2.3
 */

function epl_get_core_rental_post_types() {
	return apply_filters('epl_core_rental_post_types',array('rental') );
}

/**
 * list of all epl cpts
 *
 * @since 2.3
 */
function epl_all_post_types() {
	$epl_posts  = epl_get_active_post_types();
	$epl_posts	= array_keys($epl_posts);
	return apply_filters('epl_additional_post_types',$epl_posts);
}

/**
 * check if post is core epl post
 *
 * @since 2.3
 */
function is_epl_core_post($type=null) {
	$type = is_null($type) ? get_post_type() : $type;
	return in_array($type,epl_get_core_post_types());
}

/**
 * check if post is sales post
 *
 * @since 2.3
 */
function is_epl_sales_post($type=null) {
	$type = is_null($type) ? get_post_type() : $type;
	return in_array($type,epl_get_core_sales_post_types());
}

/**
 * check if post is rental post
 *
 * @since 2.3
 */
function is_epl_rental_post($type=null) {
	$type = is_null($type) ? get_post_type() : $type;
	return in_array($type,epl_get_core_rental_post_types());
}

/**
 * check if current post is of epl
 *
 * @since 2.2
 */
function is_epl_post($type=null) {
	$all_types = epl_all_post_types();
	if ( empty($all_types) )
	return false;


	$type = $type === null ? get_post_type() : $type;
	return 	in_array($type,epl_all_post_types());
}

/**
 * check if viewing a single post of epl
 *
 * @since 2.2
 */
function is_epl_post_single() {

	$all_types = epl_all_post_types();
	if ( empty($all_types) )
	return false;

	return 	is_singular(epl_all_post_types());
}

/**
 * check if cpt is from epl
 *
 * @since 2.2
 */
function is_epl_post_type($type) {

	$all_types = epl_all_post_types();
	if ( empty($all_types) )
	return false;

	return 	(in_array($type,epl_all_post_types()) && get_post_type() == $type );
}

/**
 * check if current post is of epl
 *
 * @since 2.2
 */
function is_epl_post_archive() {

	$all_types = epl_all_post_types();
	if ( empty($all_types) )
	return false;

	return 	is_post_type_archive(epl_all_post_types());
}

/**
 * check if listing has secondary author
 *
 * @since 3.2
 */
function epl_listing_has_secondary_author() {
    $exists = false;
    $property_second_agent = get_property_meta('property_second_agent');
    if ( '' != $property_second_agent ) {
        $second_author = get_user_by( 'login' , $property_second_agent );
        if($second_author !== false){
            $exists = $second_author->ID;
        }
    }
    return $exists;
}
