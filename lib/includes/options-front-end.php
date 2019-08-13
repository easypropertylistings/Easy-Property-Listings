<?php
/**
 * Front End Functions
 *
 * @package     EPL
 * @subpackage  Functions/Front
 * @copyright   Copyright (c) 2019, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Modify the Excerpt length on archive pages
 *
 * @param int $length Excerpt word length.
 *
 * @return int
 * @since 1.0
 */
function epl_excerpt_length( $length ) {
	global $post;
	if ( 'property' === $post->post_type ) {
		return 16;
	} elseif ( 'rental' === $post->post_type ) {
		return 16;
	} elseif ( 'commercial' === $post->post_type ) {
		return 16;
	} elseif ( 'commercial_land' === $post->post_type ) {
		return 16;
	} elseif ( 'business' === $post->post_type ) {
		return 16;
	} elseif ( 'rural' === $post->post_type ) {
		return 16;
	} elseif ( 'land' === $post->post_type ) {
		return 16;
	} elseif ( 'suburb' === $post->post_type ) {
		return 39;
	} else {
		return 55;
	}
}
add_filter( 'excerpt_length', 'epl_excerpt_length', 999 );

/**
 * Modify the Read More Link of archive pages which can be styled with
 * CSS using the epl-more-link selector
 *
 * @param string $more Existing read more, normally three dots.
 *
 * @return string
 * @since 1.0
 **/
function epl_property_new_excerpt_more( $more ) {
	global $post;
	return '...<a href="' . get_permalink( $post->ID ) . '" class="epl-more-link">' . __( 'Read More', 'easy-property-listings' ) . '&rarr;</a>';
}
add_filter( 'excerpt_more', 'epl_property_new_excerpt_more' );

/**
 * Youtube Video Link Filter so the YouTube short links will work
 *
 * @param string $text YouTube URL.
 *
 * @return string
 * @since 1.0
 */
function epl_get_youtube_id_from_url( $text ) {
	$text = preg_replace( "/^(?:http(?:s)?:\/\/)?(?:www\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user)\/))([^\?&\"'>]+)/", '$1', $text );
	$text = explode( '?', $text );
	return $text[0];
}
