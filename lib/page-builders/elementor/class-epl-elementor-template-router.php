<?php
/**
 * Routes EPL Single Listing / Listing Card / Archive Page Elementor documents
 * into EPL's existing template pipeline.
 *
 * @package    EPL
 * @subpackage PageBuilders/Elementor
 * @since      3.7.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Free-Elementor equivalent of Elementor Pro's Theme Builder, built entirely
 * out of EPL's own template bypass actions:
 *
 * - Single listings: hooks `epl_single_template` when a published EPL Single
 *   Listing document matches the current post type.
 * - Listing cards: renders an EPL Listing Card document on demand, only where
 *   a widget explicitly opts a loop into one (EPL Archive Results, EPL
 *   Listing Advanced, EPL Listings) — there is no site-wide auto-applied card.
 * - Archive pages: swaps only the resolved template file on `template_include`
 *   for a page assigned as the listings archive, leaving the real main query,
 *   pagination and canonical URL untouched.
 *
 * Elementor Pro's Theme Builder always wins when a matching condition exists
 * for the same location, via `elementor_location_exits()`.
 */
class EPL_Elementor_Template_Router {

	/**
	 * Template IDs currently rendering, keyed by ID, to guard against
	 * recursive self-embedding.
	 *
	 * @var array
	 */
	private static $rendering_cards = array();

	/**
	 * Template IDs whose per-document CSS has already been printed this
	 * request, keyed by ID.
	 *
	 * @var array
	 */
	private static $css_rendered = array();

	/**
	 * Stack of Listing Card document IDs that widgets (e.g. EPL Archive
	 * Results, EPL Listing Advanced) have explicitly chosen for their own
	 * loop. A stack (not a single value) so a widget's own loop can safely
	 * nest inside another's without clobbering the outer choice.
	 *
	 * There is no site-wide "default card" — a card only ever renders where
	 * a widget explicitly opts into one here. Forcing a card (designed to
	 * sit in a grid column) onto every EPL loop indiscriminately — including
	 * ungridded ones like the plain [listing] shortcode — broke its layout,
	 * so that auto-upgrade path was removed entirely.
	 *
	 * @var int[]
	 */
	private static $card_override_stack = array();

	/**
	 * Archive page currently routed for this request, for the wrapper
	 * template to read.
	 *
	 * @var int
	 */
	private static $active_archive_page_id = 0;

	/**
	 * Resolved single-template IDs, keyed by post type, for this request.
	 *
	 * @var array
	 */
	private $single_template_cache = array();

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'wp', array( $this, 'maybe_route_single' ), 20 );

		// Hooked on `init`, not `wp`, so the `epl_loop_template_listing`
		// listener is attached in every context that can call
		// `epl_property_blog()` — including the Elementor editor's own
		// admin-ajax canvas render, which never fires the front-end-only
		// `wp` action but still needs card overrides to render for an
		// accurate WYSIWYG preview.
		add_action( 'init', array( $this, 'hook_card_override_listener' ), 20 );
		add_filter( 'template_include', array( $this, 'maybe_route_archive' ), 10 );
		add_filter( 'epl_elementor_preview_listing_id', array( $this, 'filter_preview_listing_id' ) );
	}

	/**
	 * Whether Elementor Pro's Theme Builder has a matching condition for a
	 * location, so it can take priority over EPL's own routing.
	 *
	 * @param string $location Theme Builder location, e.g. 'single', 'archive'.
	 * @return bool
	 */
	private static function pro_theme_builder_wins( $location ) {
		return function_exists( 'elementor_location_exits' ) && elementor_location_exits( $location, true );
	}

	/**
	 * Force a specific Listing Card document for the duration of the
	 * caller's own loop (e.g. EPL Archive Results' "Listing Card" control).
	 * Callers must always pair this with end_card_override(), even on early
	 * returns.
	 *
	 * @param int|array $override Either a single Listing Card document (post)
	 *                             ID applied to every listing type, or an
	 *                             array keyed by EPL post type (plus an
	 *                             optional `_default` key) for a per-listing-
	 *                             type override, e.g.
	 *                             `array( 'rural' => 123, '_default' => 456 )`.
	 */
	public static function begin_card_override( $override ) {
		self::$card_override_stack[] = is_array( $override ) ? $override : absint( $override );
	}

	/**
	 * End a previously begun card override.
	 */
	public static function end_card_override() {
		array_pop( self::$card_override_stack );
	}

	/**
	 * The innermost active card override for a listing type, if any.
	 *
	 * @param string $post_type EPL post type of the listing currently rendering.
	 * @return int Template (post) ID, or 0 if none applies.
	 */
	private static function get_card_override( $post_type = '' ) {
		if ( empty( self::$card_override_stack ) ) {
			return 0;
		}

		$override = end( self::$card_override_stack );

		if ( ! is_array( $override ) ) {
			return (int) $override;
		}

		if ( $post_type && ! empty( $override[ $post_type ] ) ) {
			return absint( $override[ $post_type ] );
		}

		return ! empty( $override['_default'] ) ? absint( $override['_default'] ) : 0;
	}

	/* ------------------------------------------------------------------ *
	 * Single listing routing.
	 * ------------------------------------------------------------------ */

	/**
	 * Hook the EPL Single Listing document matching the current listing's
	 * post type into `epl_single_template`, if one is published.
	 */
	public function maybe_route_single() {
		if ( EPL_Elementor::is_editor() || ! function_exists( 'is_epl_post_single' ) || ! is_epl_post_single() ) {
			return;
		}

		$post = get_post();
		if ( ! $post || self::pro_theme_builder_wins( 'single' ) ) {
			return;
		}

		$template_id = $this->get_single_template_id( $post->post_type );
		if ( ! $template_id || ! apply_filters( 'epl_elementor_single_template_enabled', true, $template_id, $post->post_type ) ) {
			return;
		}

		add_action(
			'wp_enqueue_scripts',
			function () use ( $template_id ) {
				\Elementor\Core\Files\CSS\Post::create( $template_id )->enqueue();
			}
		);

		add_action(
			'epl_single_template',
			function () use ( $template_id ) {
				echo '<div class="epl-elementor-single-template">';
				echo \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $template_id, false ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Elementor content is sanitised on save.
				echo '</div>';
			}
		);
	}

	/**
	 * Resolve the EPL Single Listing document to use for a post type.
	 *
	 * Explicit `_epl_apply_post_types` matches are preferred over documents
	 * that apply to every listing type; ties are broken by most recently
	 * modified.
	 *
	 * @param string $post_type EPL post type.
	 * @return int Template (post) ID, or 0 if none.
	 */
	private function get_single_template_id( $post_type ) {
		if ( isset( $this->single_template_cache[ $post_type ] ) ) {
			return $this->single_template_cache[ $post_type ];
		}

		$forced = absint( apply_filters( 'epl_elementor_single_template_id', 0, $post_type ) );
		if ( $forced ) {
			$this->single_template_cache[ $post_type ] = $forced;
			return $forced;
		}

		$candidates = get_posts(
			array(
				'post_type'      => 'elementor_library',
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'orderby'        => 'modified',
				'order'          => 'DESC',
				'fields'         => 'ids',
				'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- elementor_library is small; matches existing EPL Elementor queries.
					array(
						'key'   => '_elementor_template_type',
						'value' => EPL_Elementor_Single_Document::TYPE,
					),
				),
			)
		);

		$fallback = 0;
		foreach ( $candidates as $candidate_id ) {
			$applies_to = (array) get_post_meta( $candidate_id, '_epl_apply_post_types', true );

			if ( in_array( $post_type, $applies_to, true ) ) {
				$this->single_template_cache[ $post_type ] = (int) $candidate_id;
				return $this->single_template_cache[ $post_type ];
			}

			if ( ! $fallback && empty( $applies_to ) ) {
				$fallback = (int) $candidate_id;
			}
		}

		$this->single_template_cache[ $post_type ] = $fallback;
		return $fallback;
	}

	/* ------------------------------------------------------------------ *
	 * Listing card rendering.
	 * ------------------------------------------------------------------ */

	/**
	 * Render an EPL Listing Card document for the current loop item.
	 *
	 * Safe to call repeatedly for different listings in the same loop: each
	 * template's CSS is only printed the first time it is used per request,
	 * and self-embedding is guarded against.
	 *
	 * @param int $template_id EPL Listing Card document (post) ID.
	 * @return bool True when the card was rendered, false when it could not be
	 *              (missing, trashed, wrong type, or already rendering) so the
	 *              caller can fall back rather than emit an empty listing.
	 */
	public static function render_card( $template_id ) {
		$template_id = absint( $template_id );

		if ( ! self::is_renderable_card( $template_id ) || isset( self::$rendering_cards[ $template_id ] ) ) {
			return false;
		}

		self::$rendering_cards[ $template_id ] = true;

		$with_css                    = empty( self::$css_rendered[ $template_id ] );
		self::$css_rendered[ $template_id ] = true;

		if ( $with_css ) {
			\Elementor\Core\Files\CSS\Post::create( $template_id )->enqueue();
		}

		// Force nested dynamic/global elements to render fully inline rather
		// than as an unresolved `[elementor-element]` placeholder, since this
		// output does not pass back through `the_content`.
		add_filter( 'elementor/element/should_render_shortcode', '__return_false' );
		echo \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $template_id, false ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Elementor content is sanitised on save.
		remove_filter( 'elementor/element/should_render_shortcode', '__return_false' );

		unset( self::$rendering_cards[ $template_id ] );

		return true;
	}

	/**
	 * Whether a post ID is a usable EPL Listing Card document.
	 *
	 * A card chosen in a widget can be trashed or deleted later, and the widget
	 * keeps the stale ID until the page is re-saved.
	 *
	 * @param int $template_id Candidate card document (post) ID.
	 * @return bool
	 */
	public static function is_renderable_card( $template_id ) {
		$template_id = absint( $template_id );

		if ( ! $template_id || 'publish' !== get_post_status( $template_id ) ) {
			return false;
		}

		return EPL_Elementor_Loop_Card_Document::TYPE === get_post_meta( $template_id, '_elementor_template_type', true );
	}

	/**
	 * Listen on `epl_loop_template_listing` so a widget's own explicit card
	 * override (see begin_card_override()) can render on this hook. There is
	 * no site-wide default card to fall back to — with no override active,
	 * this defers straight back to EPL's normal PHP template, identical to
	 * the hook not being attached at all.
	 */
	public function hook_card_override_listener() {
		add_action( 'epl_loop_template_listing', array( $this, 'render_card_override' ), 10, 1 );
	}

	/**
	 * `epl_loop_template_listing` callback: render a widget's own card
	 * override when one is active, otherwise fall back to EPL's normal PHP
	 * loop template — exactly as if nothing were hooked on this action.
	 *
	 * @param array $vars The `epl_property_blog()` local variables (via `get_defined_vars()`).
	 */
	public function render_card_override( $vars ) {
		$post_type = isset( $vars['property']->post->post_type ) ? $vars['property']->post->post_type : '';
		$override  = self::get_card_override( $post_type );
		if ( $override && self::render_card( $override ) ) {
			return;
		}

		$this->render_php_fallback( $vars );
	}

	/**
	 * Reproduce `epl_property_blog()`'s own PHP-template fallback.
	 *
	 * @param array $vars The `epl_property_blog()` local variables (via `get_defined_vars()`).
	 */
	private function render_php_fallback( $vars ) {
		$template       = isset( $vars['template'] ) ? $vars['template'] : 'default';
		$default_value  = isset( $vars['default_value'] ) ? $vars['default_value'] : 'default';
		$template_name  = apply_filters( 'epl_property_blog_template', 'loop-listing-blog-' . $template . '.php', $vars );
		epl_get_template_part( $template_name, array(), 'loop-listing-blog-' . $default_value . '.php' );
	}

	/**
	 * Editor-preview support: let an EPL Single Listing / Listing Card
	 * document's "Preview Listing ID" control drive `get_preview_property()`.
	 *
	 * @param int $requested_id Previously requested preview listing ID.
	 * @return int
	 */
	public function filter_preview_listing_id( $requested_id ) {
		if ( $requested_id || ! isset( \Elementor\Plugin::$instance->documents ) ) {
			return $requested_id;
		}

		$document = \Elementor\Plugin::$instance->documents->get_current();
		if ( ! $document || ! in_array( $document->get_name(), array( EPL_Elementor_Single_Document::TYPE, EPL_Elementor_Loop_Card_Document::TYPE ), true ) ) {
			return $requested_id;
		}

		return absint( $document->get_settings( 'epl_preview_listing' ) );
	}

	/* ------------------------------------------------------------------ *
	 * Archive pages.
	 * ------------------------------------------------------------------ */

	/**
	 * The archive page currently routed for this request, for the wrapper
	 * template to read.
	 *
	 * @return int Page ID, or 0.
	 */
	public static function get_active_archive_page_id() {
		return self::$active_archive_page_id;
	}

	/**
	 * Swap in the Archive Page wrapper template when the current request is
	 * an EPL archive/search/taxonomy request and a page has been assigned.
	 *
	 * Registered at the same `template_include` priority as, but after,
	 * EPL's own `epl_load_core_templates()` so this sees EPL's resolved pick,
	 * and before Elementor Pro's Theme Builder (priority 11) so Pro keeps
	 * taking priority when it has a matching condition.
	 *
	 * @param string $template Template file resolved so far.
	 * @return string
	 */
	public function maybe_route_archive( $template ) {
		if ( EPL_Elementor::is_editor() ) {
			return $template;
		}

		$page_id = $this->resolve_archive_page_id();
		if ( ! $page_id || self::pro_theme_builder_wins( 'archive' ) ) {
			return $template;
		}

		self::$active_archive_page_id = $page_id;

		add_action(
			'wp_enqueue_scripts',
			function () use ( $page_id ) {
				if ( class_exists( '\Elementor\Core\Files\CSS\Post' ) ) {
					\Elementor\Core\Files\CSS\Post::create( $page_id )->enqueue();
				}
			}
		);

		return EPL_PATH_TEMPLATES_POST_TYPES . 'elementor/archive-page.php';
	}

	/**
	 * Work out which EPL listing type the current archive/search/taxonomy
	 * request is showing, so a per-listing-type archive page can be resolved.
	 *
	 * Post type archives report their own type directly. Search and taxonomy
	 * requests can legitimately span several listing types at once, so they
	 * only report a type when the query is scoped to exactly one — otherwise
	 * there is no single correct per-type page and the default is used.
	 *
	 * @return string Post type key, or '' when it cannot be narrowed to one.
	 */
	private function get_queried_listing_type() {
		$active = array_keys( epl_get_active_post_types() );

		foreach ( $active as $post_type ) {
			if ( is_post_type_archive( $post_type ) ) {
				return $post_type;
			}
		}

		$queried = get_query_var( 'post_type' );
		$queried = array_values( array_intersect( array_filter( (array) $queried ), $active ) );

		return ( 1 === count( $queried ) ) ? sanitize_key( $queried[0] ) : '';
	}

	/**
	 * Resolve the archive page assigned for the current request, honouring
	 * the "apply to search" / "apply to taxonomy" settings.
	 *
	 * @return int Page ID, or 0 if the archive page feature does not apply.
	 */
	private function resolve_archive_page_id() {
		if ( 'yes' !== epl_get_option( 'epl_archive_page_enabled', 'no' ) ) {
			return 0;
		}

		$is_archive  = function_exists( 'is_epl_post_archive' ) && is_epl_post_archive();
		$is_search   = function_exists( 'epl_is_search' ) && epl_is_search() && 'yes' === epl_get_option( 'epl_archive_page_apply_search', 'yes' );
		$is_taxonomy = ( is_tax( 'location' ) || is_tax( 'tax_feature' ) || is_tax( 'tax_business_listing' ) ) && 'yes' === epl_get_option( 'epl_archive_page_apply_taxonomy', 'yes' );

		if ( ! $is_archive && ! $is_search && ! $is_taxonomy ) {
			return 0;
		}

		// A listing type can nominate its own archive page; otherwise the
		// single site-wide "Listings Archive Page" is used for every type.
		$page_id = 0;

		$queried_type = $this->get_queried_listing_type();
		if ( $queried_type ) {
			$page_id = absint( epl_get_option( 'epl_archive_page_id_' . $queried_type, '' ) );
		}

		if ( ! $page_id ) {
			$page_id = absint( epl_get_option( 'epl_archive_page_id', '' ) );
		}

		$page_id = absint( apply_filters( 'epl_archive_page_id', $page_id, $queried_type ) );
		if ( ! $page_id ) {
			return 0;
		}

		$page = get_post( $page_id );
		if ( ! $page || 'page' !== $page->post_type || 'publish' !== $page->post_status ) {
			return 0;
		}

		return $page_id;
	}
}
