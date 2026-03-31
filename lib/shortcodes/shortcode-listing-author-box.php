<?php
/**
 * SHORTCODE :: Listing Author Box [listing_author_box]
 *
 * Renders the EPL author box on any page.
 *
 * Usage:
 *   [listing_author_box]                          — Current logged-in user
 *   [listing_author_box author="3"]               — By WordPress user ID
 *   [listing_author_box author="john-doe"]        — By WordPress username (login)
 *   [listing_author_box agent="john-doe"]          — By agent slug (same format as [listing_advanced])
 *   [listing_author_box agent="john-doe,jane-doe"] — Multiple agents
 *   [listing_author_box style="default"]           — Tabbed style (default)
 *   [listing_author_box style="simple-card"]       — Simple card style
 *   [listing_author_box style="simple-grav"]       — Gravatar style
 *
 * @package     EPL
 * @subpackage  Shortcode/AuthorBox
 * @copyright   Copyright (c) 2026, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.6
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Listing Author Box Shortcode [listing_author_box]
 *
 * @param array $atts Shortcode attributes.
 *
 * @return string HTML output.
 * @since 3.6
 */
function epl_shortcode_listing_author_box( $atts ) {

	$attributes = shortcode_atts(
		array(
			'author' => '',    // WP user ID or username (login).
			'agent'  => '',    // Agent slug(s), comma-separated. Same format as [listing_advanced].
			'style'  => '',    // 'default' (tabbed), 'simple-card', 'simple-grav'. Empty = default.
		),
		$atts,
		'listing_author_box'
	);

	// Resolve user IDs from the shortcode attributes.
	$user_ids = epl_shortcode_resolve_author_ids( $attributes );

	if ( empty( $user_ids ) ) {
		return '<!-- listing_author_box: no matching user found -->';
	}

	// Determine which template to render.
	$style    = sanitize_key( $attributes['style'] );
	$template = 'content-author-box.php';
	if ( 'simple-card' === $style ) {
		$template = 'content-author-box-simple-card.php';
	} elseif ( 'simple-grav' === $style ) {
		$template = 'content-author-box-simple-grav.php';
	}

	ob_start();

	global $epl_author;
	// Stash and restore the global so we don't corrupt a surrounding loop.
	$stashed_author = isset( $epl_author ) ? $epl_author : null;

	echo '<div class="epl-shortcode-author-box">';

	foreach ( $user_ids as $uid ) {
		if ( ! class_exists( 'EPL_Author_Meta' ) ) {
			continue;
		}
		$epl_author = new EPL_Author_Meta( $uid );

		/**
		 * Action before individual author box in shortcode.
		 *
		 * @since 3.6
		 * @param EPL_Author_Meta $epl_author Author object.
		 * @param array           $attributes Shortcode attributes.
		 */
		do_action( 'epl_shortcode_author_box_before', $epl_author, $attributes );

		epl_get_template_part( $template, array( 'epl_author' => $epl_author ) );

		/**
		 * Action after individual author box in shortcode.
		 *
		 * @since 3.6
		 * @param EPL_Author_Meta $epl_author Author object.
		 * @param array           $attributes Shortcode attributes.
		 */
		do_action( 'epl_shortcode_author_box_after', $epl_author, $attributes );
	}

	echo '</div>';

	// Restore the stashed global.
	$epl_author = $stashed_author;

	return ob_get_clean();
}
add_shortcode( 'listing_author_box', 'epl_shortcode_listing_author_box' );

/**
 * Resolve author/agent shortcode attributes to an array of WP user IDs.
 *
 * Priority:
 *   1. `author` — numeric user ID or username (login).
 *   2. `agent`  — agent slug(s), looked up via username / nicename / display name.
 *   3. Fallback — current post author (if on a single EPL listing), else current user.
 *
 * @param array $attributes Parsed shortcode attributes.
 *
 * @return int[] Array of WordPress user IDs.
 * @since 3.6
 */
function epl_shortcode_resolve_author_ids( array $attributes ): array {
	$ids = array();

	// ── 1. author="…" ────────────────────────────────────────
	if ( ! empty( $attributes['author'] ) ) {
		$authors = array_filter( array_map( 'trim', explode( ',', $attributes['author'] ) ) );
		foreach ( $authors as $author ) {
			if ( is_numeric( $author ) ) {
				$user = get_user_by( 'id', absint( $author ) );
			} else {
				// Try login first, then slug (nicename).
				$user = get_user_by( 'login', $author );
				if ( ! $user ) {
					$user = get_user_by( 'slug', sanitize_title( $author ) );
				}
			}
			if ( $user ) {
				$ids[] = (int) $user->ID;
			}
		}
		return array_unique( $ids );
	}

	// ── 2. agent="…" ─────────────────────────────────────────
	if ( ! empty( $attributes['agent'] ) ) {
		$agents = array_filter( array_map( 'trim', explode( ',', $attributes['agent'] ) ) );
		foreach ( $agents as $agent_slug ) {
			$sanitized = sanitize_user( $agent_slug );
			// Try login, slug (nicename), then display name.
			$user = get_user_by( 'login', $agent_slug );
			if ( ! $user ) {
				$user = get_user_by( 'login', $sanitized );
			}
			if ( ! $user ) {
				$user = get_user_by( 'slug', $agent_slug );
			}
			if ( ! $user ) {
				$user = get_user_by( 'slug', $sanitized );
			}
			if ( ! $user ) {
				// Last resort: search by display name.
				$found = get_users(
					array(
						'search'         => $agent_slug,
						'search_columns' => array( 'display_name' ),
						'number'         => 1,
					)
				);
				if ( ! empty( $found ) ) {
					$user = $found[0];
				}
			}
			if ( $user ) {
				$ids[] = (int) $user->ID;
			}
		}
		return array_unique( $ids );
	}

	// ── 3. Fallback: post author or current user ─────────────
	global $post;
	if ( $post && function_exists( 'is_epl_post' ) && is_epl_post() ) {
		// On a single listing, show that listing's agent.
		$primary_agent = get_post_meta( $post->ID, 'property_agent', true );
		if ( $primary_agent ) {
			$user = get_user_by( 'login', $primary_agent );
			if ( ! $user ) {
				$user = get_user_by( 'slug', sanitize_title( $primary_agent ) );
			}
			if ( $user ) {
				$ids[] = (int) $user->ID;
			}
		}
		if ( empty( $ids ) ) {
			$ids[] = (int) $post->post_author;
		}
	} elseif ( get_current_user_id() ) {
		$ids[] = get_current_user_id();
	}

	return array_unique( $ids );
}
