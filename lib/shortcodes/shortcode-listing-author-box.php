<?php
/**
 * SHORTCODE :: Listing Author Box [listing_author_box]
 *
 * Renders the EPL author box on any page. Works with both EPL core
 * (WP user-based agents) and EPL Staff Directory (directory CPT mode).
 *
 * Usage:
 *   [listing_author_box]                              — Current post's agent or logged-in user
 *   [listing_author_box author="3"]                   — By WordPress user ID
 *   [listing_author_box author="john-doe"]            — By WordPress username (login)
 *   [listing_author_box agent="john-doe"]             — By agent slug (same format as [listing_advanced])
 *   [listing_author_box agent="john-doe,jane-doe"]    — Multiple agents
 *   [listing_author_box staff="john-doe"]             — By Staff Directory post slug
 *   [listing_author_box staff="123"]                  — By Staff Directory post ID
 *   [listing_author_box staff="john-doe,jane-doe"]    — Multiple staff members
 *   [listing_author_box style="default"]              — Tabbed style (EPL core default)
 *   [listing_author_box style="simple-card"]          — Simple card style
 *   [listing_author_box style="simple-grav"]          — Gravatar style
 *
 * When EPL Staff Directory is active, the shortcode automatically uses the
 * Staff Directory's configured author box template (Bio Card, Bio, Bio 2-Column,
 * or Default Tabbed) unless overridden by the `style` attribute.
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
			'staff'  => '',    // Staff Directory post slug(s) or ID(s), comma-separated.
			'style'  => '',    // 'default' (tabbed), 'simple-card', 'simple-grav'. Empty = auto-detect.
		),
		$atts,
		'listing_author_box'
	);

	$style    = sanitize_key( $attributes['style'] );
	$sd_active = epl_shortcode_author_box_sd_active();

	// ── Resolve to EPL_Author_Meta IDs ───────────────────────
	// In directory mode these are directory post IDs; otherwise WP user IDs.
	$author_ids = epl_shortcode_resolve_author_box_ids( $attributes );

	if ( empty( $author_ids ) ) {
		return '<!-- listing_author_box: no matching author found -->';
	}

	ob_start();

	global $epl_author;
	$stashed_author = isset( $epl_author ) ? $epl_author : null;

	echo '<div class="epl-shortcode-author-box">';

	foreach ( $author_ids as $aid ) {
		if ( ! class_exists( 'EPL_Author_Meta' ) ) {
			continue;
		}
		$epl_author = new EPL_Author_Meta( $aid );

		/**
		 * Action before individual author box in shortcode.
		 *
		 * @since 3.6
		 * @param EPL_Author_Meta $epl_author Author object.
		 * @param array           $attributes Shortcode attributes.
		 */
		do_action( 'epl_shortcode_author_box_before', $epl_author, $attributes );

		// Render using Staff Directory's author box when available.
		if ( $sd_active && empty( $style ) ) {
			epl_shortcode_render_sd_author_box( $epl_author );
		} else {
			epl_shortcode_render_core_author_box( $epl_author, $style );
		}

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

	$epl_author = $stashed_author;

	return ob_get_clean();
}
add_shortcode( 'listing_author_box', 'epl_shortcode_listing_author_box' );

/**
 * Check if EPL Staff Directory is active and using directory mode.
 *
 * @return bool
 * @since 3.6
 */
function epl_shortcode_author_box_sd_active(): bool {
	return class_exists( 'EPL_SD_Author_Box' )
		&& function_exists( 'epl_get_option' );
}

/**
 * Check if Staff Directory stores data on the directory CPT (not WP user).
 *
 * @return bool
 * @since 3.6
 */
function epl_shortcode_author_box_sd_directory_mode(): bool {
	return epl_shortcode_author_box_sd_active()
		&& 'directory' === epl_get_option( 'epl_sd_staff_details_location', 'wp_user' );
}

/**
 * Render the author box using Staff Directory's configured template.
 *
 * Respects the Admin > Extensions > Staff Directory > Author Box > Template setting:
 *   0 = Default Tabbed (EPL core), 1 = Bio, 2 = Bio 2-Column, 3 = Bio Card.
 *
 * @param EPL_Author_Meta $epl_author Author object.
 *
 * @since 3.6
 */
function epl_shortcode_render_sd_author_box( $epl_author ): void {
	$box = EPL_SD_Author_Box::instance();

	// The template setting: 0 = Default Tabbed (core), 1/2/3 = SD Bio variants.
	$box_type = (int) epl_get_option( 'epl_sd_staff_author_box_type', 0 );

	if ( $box_type >= 1 ) {
		// Use Staff Directory's advanced_author_box — it picks
		// Bio / Bio 2-Column / Bio Card based on the setting.
		$box->advanced_author_box( $epl_author );
	} else {
		// Default Tabbed — the core template, but SD's filter_author_tabs
		// hook is already active so it will inject the SD "About" tab.
		epl_get_template_part( 'content-author-box.php', array( 'epl_author' => $epl_author ) );
	}
}

/**
 * Render the author box using EPL core templates.
 *
 * @param EPL_Author_Meta $epl_author Author object.
 * @param string          $style      Template style override.
 *
 * @since 3.6
 */
function epl_shortcode_render_core_author_box( $epl_author, string $style = '' ): void {
	$template = 'content-author-box.php';
	if ( 'simple-card' === $style ) {
		$template = 'content-author-box-simple-card.php';
	} elseif ( 'simple-grav' === $style ) {
		$template = 'content-author-box-simple-grav.php';
	}
	epl_get_template_part( $template, array( 'epl_author' => $epl_author ) );
}

/**
 * Resolve shortcode attributes to an array of IDs suitable for EPL_Author_Meta.
 *
 * When Staff Directory is in "directory" mode, these are directory post IDs.
 * Otherwise, they are WordPress user IDs.
 *
 * Priority:
 *   1. `staff`  — Directory post slug(s) or ID(s). Only when SD is active.
 *   2. `author` — WP user ID or username. Converted to directory post ID when in directory mode.
 *   3. `agent`  — Agent slug(s). Looked up via property_staff_username meta or WP user.
 *   4. Fallback — Current post's agent or logged-in user.
 *
 * @param array $attributes Parsed shortcode attributes.
 *
 * @return int[] Array of IDs for EPL_Author_Meta.
 * @since 3.6
 */
function epl_shortcode_resolve_author_box_ids( array $attributes ): array {
	$directory_mode = epl_shortcode_author_box_sd_directory_mode();

	// ── 1. staff="…" — Direct Staff Directory lookup ─────────
	if ( ! empty( $attributes['staff'] ) && epl_shortcode_author_box_sd_active() ) {
		return epl_shortcode_resolve_staff_ids( $attributes['staff'] );
	}

	// ── 2. agent="…" — Agent slug lookup ─────────────────────
	if ( ! empty( $attributes['agent'] ) ) {
		return epl_shortcode_resolve_agent_ids( $attributes['agent'], $directory_mode );
	}

	// ── 3. author="…" — WP user lookup ──────────────────────
	if ( ! empty( $attributes['author'] ) ) {
		return epl_shortcode_resolve_wp_author_ids( $attributes['author'], $directory_mode );
	}

	// ── 4. Fallback: current post's agent or logged-in user ──
	return epl_shortcode_resolve_fallback_ids( $directory_mode );
}

/**
 * Resolve staff="…" to directory post IDs.
 *
 * Accepts post IDs, post slugs, or property_staff_username values.
 *
 * @param string $staff_attr Comma-separated staff slugs or IDs.
 *
 * @return int[]
 * @since 3.6
 */
function epl_shortcode_resolve_staff_ids( string $staff_attr ): array {
	$ids    = array();
	$slugs  = array_filter( array_map( 'trim', explode( ',', $staff_attr ) ) );

	foreach ( $slugs as $slug ) {
		// Numeric: treat as post ID.
		if ( is_numeric( $slug ) ) {
			$post = get_post( absint( $slug ) );
			if ( $post && 'directory' === $post->post_type ) {
				$ids[] = (int) $post->ID;
				continue;
			}
		}

		// Try by post slug.
		$posts = get_posts( array(
			'post_type'      => 'directory',
			'name'           => sanitize_title( $slug ),
			'posts_per_page' => 1,
			'post_status'    => 'publish',
		) );
		if ( ! empty( $posts ) ) {
			$ids[] = (int) $posts[0]->ID;
			continue;
		}

		// Try by property_staff_username meta (the link to WP user login).
		if ( function_exists( 'epl_sd_get_directory_by_property_agent' ) ) {
			$directory = epl_sd_get_directory_by_property_agent( $slug, false );
			if ( $directory ) {
				$ids[] = (int) $directory->ID;
				continue;
			}
		}
	}

	return array_unique( $ids );
}

/**
 * Resolve agent="…" slugs to IDs for EPL_Author_Meta.
 *
 * In directory mode, finds the directory CPT via property_staff_username.
 * Otherwise, resolves to WP user IDs.
 *
 * @param string $agent_attr    Comma-separated agent slugs.
 * @param bool   $directory_mode Whether SD is in directory mode.
 *
 * @return int[]
 * @since 3.6
 */
function epl_shortcode_resolve_agent_ids( string $agent_attr, bool $directory_mode ): array {
	$ids    = array();
	$agents = array_filter( array_map( 'trim', explode( ',', $agent_attr ) ) );

	foreach ( $agents as $agent_slug ) {
		$sanitized = sanitize_user( $agent_slug );

		// In directory mode, look up via property_staff_username meta.
		if ( $directory_mode && function_exists( 'epl_sd_get_directory_by_property_agent' ) ) {
			$directory = epl_sd_get_directory_by_property_agent( $agent_slug, false );
			if ( ! $directory ) {
				$directory = epl_sd_get_directory_by_property_agent( $sanitized, false );
			}
			if ( $directory ) {
				$ids[] = (int) $directory->ID;
				continue;
			}
		}

		// Fall back to WP user lookup.
		$user = get_user_by( 'login', $agent_slug )
			?: get_user_by( 'login', $sanitized )
			?: get_user_by( 'slug', $agent_slug )
			?: get_user_by( 'slug', $sanitized );

		if ( ! $user ) {
			$found = get_users( array(
				'search'         => $agent_slug,
				'search_columns' => array( 'display_name' ),
				'number'         => 1,
			) );
			if ( ! empty( $found ) ) {
				$user = $found[0];
			}
		}

		if ( $user ) {
			if ( $directory_mode ) {
				// Convert WP user to directory post.
				$dir_id = epl_shortcode_user_to_directory_id( $user->ID );
				if ( $dir_id ) {
					$ids[] = $dir_id;
					continue;
				}
			}
			$ids[] = (int) $user->ID;
		}
	}

	return array_unique( $ids );
}

/**
 * Resolve author="…" to IDs for EPL_Author_Meta.
 *
 * @param string $author_attr   Comma-separated WP user IDs or logins.
 * @param bool   $directory_mode Whether SD is in directory mode.
 *
 * @return int[]
 * @since 3.6
 */
function epl_shortcode_resolve_wp_author_ids( string $author_attr, bool $directory_mode ): array {
	$ids     = array();
	$authors = array_filter( array_map( 'trim', explode( ',', $author_attr ) ) );

	foreach ( $authors as $author ) {
		if ( is_numeric( $author ) ) {
			$user = get_user_by( 'id', absint( $author ) );
		} else {
			$user = get_user_by( 'login', $author )
				?: get_user_by( 'slug', sanitize_title( $author ) );
		}

		if ( ! $user ) {
			continue;
		}

		if ( $directory_mode ) {
			$dir_id = epl_shortcode_user_to_directory_id( $user->ID );
			if ( $dir_id ) {
				$ids[] = $dir_id;
				continue;
			}
		}

		$ids[] = (int) $user->ID;
	}

	return array_unique( $ids );
}

/**
 * Fallback: resolve current post's agent or logged-in user.
 *
 * @param bool $directory_mode Whether SD is in directory mode.
 *
 * @return int[]
 * @since 3.6
 */
function epl_shortcode_resolve_fallback_ids( bool $directory_mode ): array {
	$ids = array();

	global $post;
	if ( $post && function_exists( 'is_epl_post' ) && is_epl_post() ) {
		$primary_agent = get_post_meta( $post->ID, 'property_agent', true );

		if ( $primary_agent && $directory_mode && function_exists( 'epl_sd_get_directory_by_property_agent' ) ) {
			$directory = epl_sd_get_directory_by_property_agent( $primary_agent, false );
			if ( $directory ) {
				$ids[] = (int) $directory->ID;
				return $ids;
			}
		}

		if ( $primary_agent ) {
			$user = get_user_by( 'login', $primary_agent )
				?: get_user_by( 'slug', sanitize_title( $primary_agent ) );
			if ( $user ) {
				if ( $directory_mode ) {
					$dir_id = epl_shortcode_user_to_directory_id( $user->ID );
					if ( $dir_id ) {
						return array( $dir_id );
					}
				}
				$ids[] = (int) $user->ID;
				return $ids;
			}
		}

		if ( empty( $ids ) ) {
			$author_id = (int) $post->post_author;
			if ( $directory_mode ) {
				$dir_id = epl_shortcode_user_to_directory_id( $author_id );
				if ( $dir_id ) {
					return array( $dir_id );
				}
			}
			$ids[] = $author_id;
		}
	} elseif ( get_current_user_id() ) {
		$uid = get_current_user_id();
		if ( $directory_mode ) {
			$dir_id = epl_shortcode_user_to_directory_id( $uid );
			if ( $dir_id ) {
				return array( $dir_id );
			}
		}
		$ids[] = $uid;
	}

	return array_unique( $ids );
}

/**
 * Convert a WordPress user ID to a Staff Directory post ID.
 *
 * Checks for a directory CPT where the post author matches the user,
 * or where property_staff_username matches the user's login.
 *
 * @param int $user_id WordPress user ID.
 *
 * @return int|null Directory post ID or null if not found.
 * @since 3.6
 */
function epl_shortcode_user_to_directory_id( int $user_id ): ?int {
	$user = get_user_by( 'id', $user_id );
	if ( ! $user ) {
		return null;
	}

	// Try by property_staff_username meta (most reliable link).
	if ( function_exists( 'epl_sd_get_directory_by_property_agent' ) ) {
		$directory = epl_sd_get_directory_by_property_agent( $user->user_login, false );
		if ( $directory ) {
			return (int) $directory->ID;
		}
	}

	// Try by post author.
	$posts = get_posts( array(
		'post_type'      => 'directory',
		'author'         => $user_id,
		'posts_per_page' => 1,
		'post_status'    => 'publish',
	) );
	if ( ! empty( $posts ) ) {
		return (int) $posts[0]->ID;
	}

	return null;
}
