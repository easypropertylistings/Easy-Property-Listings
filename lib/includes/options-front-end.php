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
 * Modify the Read More Link of archive pages which can be styled with
 * CSS using the epl-more-link selector
 *
 * @param string $more Existing read more, normally three dots.
 *
 * @return string
 * @since 1.0
 * @since 3.4.42 Fix : Notice when $post is null
 **/
function epl_property_new_excerpt_more( $more ) {
	global $post;
        if( is_null( $post ) ) {
		return $more;
	}
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
 * @since 3.4.4 Added support for youtube shorts videos.
 */
function epl_get_youtube_id_from_url( $text ) {
	$text = preg_replace( "/^(?:http(?:s)?:\/\/)?(?:www\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user|shorts)\/))([^\?&\"'>]+)/", '$1', $text );
	$text = explode( '?', $text );
	return $text[0];
}
