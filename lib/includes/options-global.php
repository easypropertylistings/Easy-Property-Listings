<?php
/**
 * Global Functions
 *
 * @package     EPL
 * @subpackage  Functions/Global
 * @copyright   Copyright (c) 2020, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get list of all EPL image sizes
 *
 * @since 3.2.3
 */
function get_epl_image_sizes() {
	return apply_filters(
		'epl_image_sizes',
		array(
			array(
				'id'     => 'admin-list-thumb',
				'height' => 100,
				'width'  => 100,
				'crop'   => true,
			),
			array(
				'id'     => 'epl-image-medium-crop',
				'height' => 200,
				'width'  => 300,
				'crop'   => true,
			),
		)
	);
}

/**
 * Register image sizes for the admin list of property
 * and a hard cropped 300x200 px image for use in widgets
 *
 * @since 1.0
 */
function epl_image_sizes() {

	$epl_sizes = get_epl_image_sizes();

	foreach ( $epl_sizes as $epl_size ) {
		add_image_size( $epl_size['id'], $epl_size['width'], $epl_size['height'], $epl_size['crop'] );
	}
}
add_action( 'after_setup_theme', 'epl_image_sizes' );

/**
 * Gives the hard cropped image a friendly name
 *
 * @param array $epl_sizes Array of image sizes.
 *
 * @return array
 * @since 1.0
 */
function epl_image_size_names_choose( $epl_sizes ) {
	return array_merge(
		$epl_sizes,
		array(
			'epl-image-medium-crop' => __( 'Medium 300x200 hard Crop', 'easy-property-listings' ),
		)
	);
}
add_filter( 'image_size_names_choose', 'epl_image_size_names_choose' );

/**
 * Add Custom Post Types to Author Archives
 *
 * @param array $query WP Query object.
 * @since 1.0
 */
function epl_property_author_archives( $query ) {
	// Add 'Rentals' post type to author archives.
	if ( $query->is_author ) {
		$query->set( 'post_type', array( 'directory', 'rental', 'testimonial', 'property', 'post' ) );
	}

	// Remove the action after it's run.
	remove_action( 'pre_get_posts', 'epl_property_author_archives' );
}
add_action( 'pre_get_posts', 'epl_property_author_archives' );

/**
 * Add Custom Post Types to Author Archive Page
 *
 * @param array $query WP Query object.
 * @since 1.0
 */
function epl_custom_post_author_archive( $query ) {
	if ( $query->is_author ) {
		$query->set( 'post_type', array( 'property', 'rental', 'testimonial', 'post', 'commercial', 'land' ) );
	}

	remove_action( 'pre_get_posts', 'epl_custom_post_author_archive' );
}
add_action( 'pre_get_posts', 'epl_custom_post_author_archive' );

/**
 * Gravity Forms Filter for populating contact form with author email address
 *
 * Create a gravity from and add a hidden field and enable "Allow field to be populated dynamically"
 * option on the advanced tab. Use author_email as the Parameter Name and when a form is submitted
 * the email address of the author of that property/page/post will be added to the hidden field.
 * You can then setup the forwarding and notifications directly to the author as their email
 * will be part of the form.
 *
 * @param string $value Form pre populating field.
 *
 * @return string
 * @since 1.0
 * @since 3.5.14 Improvements to author email return values in gravity forms email hook.
 */
function epl_populate_post_author_email( $value ) {

	if ( is_author() ) {
		$author = get_queried_object();
		if ( $author instanceof WP_User ) {
			return $author->user_email;
		}
	}

	global $post;
	if ( $post instanceof WP_Post ) {
		$author_email = get_the_author_meta( 'user_email', $post->post_author );
		if ( ! empty( $author_email ) ) {
			return $author_email;
		}
	}

	return '';
}
add_filter( 'gform_field_value_author_email', 'epl_populate_post_author_email' );

