<?php
/**
 * Elementor Integration for Easy Property Listings
 *
 * @package     EPL
 * @subpackage  PageBuilders/Elementor
 * @copyright   Copyright (c) 2024
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.6.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Ensure listing-context widgets are never stored in Elementor Element Cache. */
trait EPL_Elementor_Dynamic_Widget {
	protected function is_dynamic_content(): bool {
		return true;
	}
}

/**
 * EPL_Elementor Class
 *
 * Main class for Elementor integration.
 *
 * @since 3.6.0
 */
class EPL_Elementor {

	/**
	 * Instance
	 *
	 * @var EPL_Elementor The single instance of the class.
	 * @since 3.6.0
	 */
	private static $instance = null;

	/**
	 * Minimum Elementor Version
	 *
	 * The elementor/widgets/register hook and Widgets_Manager::register()
	 * used below were introduced in Elementor 3.5.
	 *
	 * @var string Minimum Elementor version required.
	 * @since 3.6.0
	 */
	const MINIMUM_ELEMENTOR_VERSION = '3.5.0';

	/**
	 * Widgets path
	 *
	 * @var string Path to widgets directory.
	 * @since 3.6.0
	 */
	private $widgets_path;

	/** @var array<string,WP_Query> Queries produced by Elementor Loop Grid widgets. */
	private $loop_grid_queries = array();

	/** @var array Settings from the Loop Grid currently being rendered. */
	private $active_loop_grid_settings = array();

	/**
	 * Constructor
	 *
	 * @since 3.6.0
	 */
	public function __construct() {
		$this->widgets_path = EPL_PATH_LIB . 'page-builders/elementor/widgets/';
		require_once EPL_PATH_LIB . 'page-builders/elementor/class-epl-elementor-agent-document.php';
		require_once EPL_PATH_LIB . 'page-builders/elementor/class-epl-elementor-listing-document.php';
		require_once EPL_PATH_LIB . 'page-builders/elementor/class-epl-elementor-single-document.php';
		require_once EPL_PATH_LIB . 'page-builders/elementor/class-epl-elementor-loop-card-document.php';
		require_once EPL_PATH_LIB . 'page-builders/elementor/class-epl-elementor-template-router.php';
		require_once EPL_PATH_LIB . 'page-builders/elementor/class-epl-elementor-template-seeder.php';

		// Check Elementor version.
		if ( ! $this->is_compatible() ) {
			add_action( 'admin_notices', array( $this, 'admin_notice_minimum_elementor_version' ) );
			return;
		}

		// Register widget categories.
		add_action( 'elementor/elements/categories_registered', array( $this, 'register_widget_categories' ) );

		// Register widgets.
		// Register after Elementor Pro so Loop Grid availability can be detected
		// from the widget registry rather than from plugin names or class files.
		add_action( 'elementor/widgets/register', array( $this, 'register_widgets' ), 100 );
		add_action( 'elementor/documents/register', array( $this, 'register_documents' ) );

		// Enqueue styles.
		add_action( 'elementor/frontend/after_enqueue_styles', array( $this, 'frontend_styles' ) );
		add_action( 'elementor/editor/after_enqueue_scripts', array( $this, 'editor_scripts' ) );

		// Give Elementor Pro Loop Grid users a documented EPL query ID.
		add_action( 'elementor/query/epl_listings', array( $this, 'elementor_listings_query' ), 10, 2 );
		add_action( 'elementor/query/epl_staff', array( $this, 'elementor_staff_query' ), 10, 2 );
		add_filter( 'elementor/query/query_args', array( $this, 'elementor_loop_query_args' ), 20, 2 );
		add_filter( 'elementor/query/get_query_args/current_query', array( $this, 'elementor_current_query_args' ), 20 );
		add_action( 'elementor/frontend/widget/before_render', array( $this, 'begin_loop_grid_render' ) );
		add_action( 'elementor/frontend/widget/after_render', array( $this, 'end_loop_grid_render' ) );
		add_action( 'elementor/query/query_results', array( $this, 'capture_loop_grid_query' ), 20, 2 );
		add_action( 'elementor/element/loop-grid/section_pagination/before_section_end', array( $this, 'register_loop_grid_pagination_provider' ) );
		add_filter( 'elementor/widget/render_content', array( $this, 'render_loop_grid_epl_pagination' ), 20, 2 );
		add_filter( 'pre_handle_404', array( $this, 'allow_epl_loop_grid_page' ), 20, 2 );
		add_filter( 'elementor/theme/need_override_location', array( $this, 'override_staff_directory_location' ), 20, 3 );

		// Elementor can clear Theme Builder conditions while an Archive document
		// is switched to/from a Loop Item in the editor. Keep a last-known-good
		// copy for EPL archive documents and restore it after that save request.
		add_action( 'added_post_meta', array( $this, 'track_archive_conditions' ), 10, 4 );
		add_action( 'updated_post_meta', array( $this, 'track_archive_conditions' ), 10, 4 );
		add_action( 'deleted_post_meta', array( $this, 'track_archive_conditions' ), 10, 4 );

		// Free-Elementor Theme Builder equivalent: single listing templates,
		// default listing cards, and archive pages.
		new EPL_Elementor_Template_Router();

		// Install the design templates that ship with the plugin.
		new EPL_Elementor_Template_Seeder();
	}

	/**
	 * Register dedicated Elementor documents.
	 *
	 * @param \Elementor\Core\Documents_Manager $documents_manager Documents manager.
	 */
	public function register_documents( $documents_manager ) {
		$documents_manager->register_document_type( EPL_Elementor_Agent_Document::TYPE, EPL_Elementor_Agent_Document::get_class_full_name() );
		$documents_manager->register_document_type( EPL_Elementor_Single_Document::TYPE, EPL_Elementor_Single_Document::get_class_full_name() );
		$documents_manager->register_document_type( EPL_Elementor_Loop_Card_Document::TYPE, EPL_Elementor_Loop_Card_Document::get_class_full_name() );
	}

	/**
	 * Instance
	 *
	 * @return EPL_Elementor
	 * @since 3.6.0
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Check compatibility
	 *
	 * @return bool
	 * @since 3.6.0
	 */
	private function is_compatible() {
		if ( ! defined( 'ELEMENTOR_VERSION' ) ) {
			return false;
		}
		return version_compare( ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=' );
	}

	/**
	 * Admin notice for minimum Elementor version
	 *
	 * @since 3.6.0
	 */
	public function admin_notice_minimum_elementor_version() {
		?>
		<div class="notice notice-warning is-dismissible">
			<p>
				<?php
				printf(
					/* translators: 1: Plugin name 2: Elementor 3: Version */
					esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'easy-property-listings' ),
					'<strong>' . esc_html__( 'Easy Property Listings - Elementor', 'easy-property-listings' ) . '</strong>',
					'<strong>Elementor</strong>',
					self::MINIMUM_ELEMENTOR_VERSION
				);
				?>
			</p>
		</div>
		<?php
	}

	/**
	 * Register Widget Categories
	 *
	 * @param \Elementor\Elements_Manager $elements_manager Elementor elements manager.
	 * @since 3.6.0
	 */
	public function register_widget_categories( $elements_manager ) {
		$elements_manager->add_category(
			'epl-elements',
			array(
				'title' => esc_html__( 'EPL Elements', 'easy-property-listings' ),
				'icon'  => 'eicon-apps',
			)
		);

		$elements_manager->add_category(
			'epl-archive',
			array(
				'title' => esc_html__( 'EPL Archive', 'easy-property-listings' ),
				'icon'  => 'eicon-archive',
			)
		);

		$elements_manager->add_category(
			'epl-staff',
			array(
				'title' => esc_html__( 'EPL Staff Directory', 'easy-property-listings' ),
				'icon'  => 'eicon-person',
			)
		);
	}

	/**
	 * Register Widgets
	 *
	 * @param \Elementor\Widgets_Manager $widgets_manager Elementor widgets manager.
	 * @since 3.6.0
	 */
	public function register_widgets( $widgets_manager ) {
		$this->include_widget_files();

		// Element widgets.
		$element_widgets = array(
			'EPL_Elementor_Property_Title',
			'EPL_Elementor_Property_Heading',
			'EPL_Elementor_Property_Price',
			'EPL_Elementor_Property_Address',
			'EPL_Elementor_Property_Icons',
			'EPL_Elementor_Property_Image',
			'EPL_Elementor_Property_Content',
			'EPL_Elementor_Property_Excerpt',
			'EPL_Elementor_Property_Features',
			'EPL_Elementor_Property_Inspection',
			'EPL_Elementor_Agent_Box',
			'EPL_Elementor_Listing_Agents',
			'EPL_Elementor_Agent_Photo',
			'EPL_Elementor_Agent_Name',
			'EPL_Elementor_Agent_Position',
			'EPL_Elementor_Agent_Contact',
			'EPL_Elementor_Agent_Social',
			'EPL_Elementor_Agent_Bio',
			'EPL_Elementor_Staff_Listings',
			'EPL_Elementor_Property_Map',
			'EPL_Elementor_Property_Gallery',
			'EPL_Elementor_Property_Video',
			'EPL_Elementor_Property_Buttons',
			'EPL_Elementor_Property_Status',
			'EPL_Elementor_Property_Meta',
			'EPL_Elementor_Property_Taxonomy',
			'EPL_Elementor_Single_Extensions',
		);

		foreach ( $element_widgets as $widget_class ) {
			if ( class_exists( $widget_class ) ) {
				$widgets_manager->register( new $widget_class() );
			}
		}

		// Archive widgets.
		$archive_widgets = array(
			'EPL_Elementor_Listing_Advanced',
			'EPL_Elementor_Listing_Search',
			'EPL_Elementor_Listing_Results',
			'EPL_Elementor_Pagination',
		);

		// Elementor's Loop Grid is a Pro feature. Free users get an EPL-native
		// listings widget; Pro users keep one clear loop-building workflow.
		if ( ! $this->has_elementor_loop_grid( $widgets_manager ) ) {
			$archive_widgets[] = 'EPL_Elementor_Listings';
		}

		foreach ( $archive_widgets as $widget_class ) {
			if ( class_exists( $widget_class ) ) {
				$widgets_manager->register( new $widget_class() );
			}
		}
	}

	/**
	 * Include Widget Files
	 *
	 * @since 3.6.0
	 */
	private function include_widget_files() {
		// Element widgets.
		$elements = array(
			'class-epl-elementor-property-title.php',
			'class-epl-elementor-property-heading.php',
			'class-epl-elementor-property-price.php',
			'class-epl-elementor-property-address.php',
			'class-epl-elementor-property-icons.php',
			'class-epl-elementor-property-image.php',
			'class-epl-elementor-property-content.php',
			'class-epl-elementor-property-excerpt.php',
			'class-epl-elementor-property-features.php',
			'class-epl-elementor-property-inspection.php',
			'class-epl-elementor-agent-box.php',
			'class-epl-elementor-agents-loop.php',
			'class-epl-elementor-agent-photo.php',
			'class-epl-elementor-agent-name.php',
			'class-epl-elementor-agent-position.php',
			'class-epl-elementor-agent-contact.php',
			'class-epl-elementor-agent-social.php',
			'class-epl-elementor-agent-bio.php',
			'class-epl-elementor-staff-listings.php',
			'class-epl-elementor-property-map.php',
			'class-epl-elementor-property-gallery.php',
			'class-epl-elementor-property-video.php',
			'class-epl-elementor-property-buttons.php',
			'class-epl-elementor-property-status.php',
			'class-epl-elementor-property-meta.php',
			'class-epl-elementor-property-taxonomy.php',
			'class-epl-elementor-single-extensions.php',
		);

		foreach ( $elements as $file ) {
			$path = $this->widgets_path . 'elements/' . $file;
			if ( file_exists( $path ) ) {
				require_once $path;
			}
		}

		// Archive widgets.
		$archives = array(
			'class-epl-elementor-listing-advanced.php',
			'class-epl-elementor-listings.php',
			'class-epl-elementor-listing-search.php',
			'class-epl-elementor-listing-results.php',
			'class-epl-elementor-pagination.php',
		);

		foreach ( $archives as $file ) {
			$path = $this->widgets_path . 'archive/' . $file;
			if ( file_exists( $path ) ) {
				require_once $path;
			}
		}
	}

	/**
	 * Determine whether Elementor's native Loop Grid is available.
	 *
	 * Checking the registered widget supports Elementor Pro as well as
	 * compatible distributions without relying on a plugin basename.
	 *
	 * @param \Elementor\Widgets_Manager $widgets_manager Widgets manager.
	 * @return bool
	 */
	private function has_elementor_loop_grid( $widgets_manager ) {
		$available = (bool) $widgets_manager->get_widget_types( 'loop-grid' );

		return (bool) apply_filters( 'epl_elementor_has_loop_grid', $available, $widgets_manager );
	}

	/**
	 * Frontend Styles
	 *
	 * @since 3.6.0
	 */
	public function frontend_styles() {
		wp_enqueue_style(
			'epl-elementor',
			EPL_PLUGIN_URL . 'lib/assets/css/epl-elementor.css',
			array(),
			EPL_PROPERTY_VER
		);

		// Tabs functionality for the agents loop widget. A delegated listener
		// keeps working for content injected after load (Elementor editor
		// preview) and scopes the tab lookup to the widget container so
		// multiple widgets on one page do not interfere with each other.
		wp_register_script( 'epl-elementor-tabs', '', array(), EPL_PROPERTY_VER, true );
		wp_enqueue_script( 'epl-elementor-tabs' );
		wp_add_inline_script(
			'epl-elementor-tabs',
			'
			document.addEventListener("click", function(e) {
				var button = e.target.closest(".epl-agents-tabs .epl-tab-button");
				if (!button) {
					return;
				}
				var container = button.closest(".epl-agents-tabs");
				var tabId = button.getAttribute("data-tab");

				container.querySelectorAll(".epl-tab-button").forEach(function(btn) {
					btn.classList.remove("active");
				});
				container.querySelectorAll(".epl-tab-content").forEach(function(content) {
					content.classList.remove("active");
				});

				button.classList.add("active");
				var content = container.querySelector("[id=\"" + tabId + "\"]") || document.getElementById(tabId);
				if (content) {
					content.classList.add("active");
				}
			});
			'
		);

		// Sync dual-view Listing Advanced grids with EPL's grid/list switch. The
		// switch is client-side only (it flips a CSS class, no re-render), so we
		// mirror the selected view onto our own grid container. The initial state
		// is set server-side to avoid a flash of the wrong view on load.
		wp_add_inline_script(
			'epl-elementor-tabs',
			'
			document.addEventListener("click", function(e) {
				var toggle = e.target.closest(".epl-switch-view ul li[data-view]");
				if (!toggle) {
					return;
				}
				var isList = "list" === toggle.getAttribute("data-view");
				document.querySelectorAll(".epl-elementor-advanced-grid.epl-ea-dual-view").forEach(function(grid) {
					grid.classList.toggle("is-list-view", isList);
					grid.classList.toggle("is-grid-view", !isList);
				});
			});
			'
		);
	}

	/**
	 * Editor Scripts
	 *
	 * Two independent editor-only fixes, both injected as a single inline
	 * script on the real `elementor-editor` script:
	 *
	 * 1. Works around an Elementor editor bug where a responsive control that
	 *    also has a `condition` (e.g. `grid_columns` on the Archive Results
	 *    widget) can get stuck with a stale inline `display: none;` on its
	 *    wrapper — set once during the control's initial render and never
	 *    cleared afterwards, even once its condition is satisfied and
	 *    Elementor's own Conditions module has correctly removed the
	 *    `elementor-hidden-control` class. A responsive control with no
	 *    `condition` (e.g. `grid_gap`) never has this problem: Elementor's
	 *    device-tab switcher shows/hides those purely via CSS classes, never
	 *    inline styles, so an inline `display: none;` with the hidden-control
	 *    class absent is always this bug, never a legitimate state — safe to
	 *    clear unconditionally, for any widget's controls, not just EPL's own.
	 *
	 * 2. Narrows the editing canvas to roughly a single card's width while
	 *    editing an EPL Listing Card document, per its own "Editing Preview
	 *    Width" document setting — see EPL_Elementor_Loop_Card_Document.
	 *
	 * @since 3.7.0
	 */
	public function editor_scripts() {
		wp_register_script( 'epl-elementor-editor-fixes', '', array( 'elementor-editor' ), EPL_PROPERTY_VER, true );
		wp_enqueue_script( 'epl-elementor-editor-fixes' );
		wp_add_inline_script(
			'epl-elementor-editor-fixes',
			'
			( function () {
				function isStuckHidden( el ) {
					return el.style.display === "none" && ! el.classList.contains( "elementor-hidden-control" );
				}

				function sweep( root ) {
					root.querySelectorAll( ".elementor-control" ).forEach( function ( el ) {
						if ( isStuckHidden( el ) ) {
							el.style.removeProperty( "display" );
						}
					} );
				}

				function watch( panel ) {
					sweep( panel );

					new MutationObserver( function ( mutations ) {
						mutations.forEach( function ( mutation ) {
							if ( "attributes" === mutation.type ) {
								if ( mutation.target.classList && mutation.target.classList.contains( "elementor-control" ) && isStuckHidden( mutation.target ) ) {
									mutation.target.style.removeProperty( "display" );
								}
								return;
							}

							mutation.addedNodes.forEach( function ( node ) {
								if ( 1 === node.nodeType ) {
									sweep( node );
								}
							} );
						} );
					} ).observe( panel, {
						childList: true,
						subtree: true,
						attributes: true,
						attributeFilter: [ "style", "class" ],
					} );
				}

				elementor.on( "panel:init", function () {
					var panel = document.getElementById( "elementor-panel" );
					if ( panel ) {
						watch( panel );
					}
				} );
			} )();

			// An EPL Listing Card document is a single card, meant to sit
			// narrow inside a grid — left alone it renders full page width in
			// the editor, which makes it hard to judge how it will actually
			// look. When editing this document type, narrow the preview
			// canvas to the "Editing Preview Width" document setting
			// (default 320px) and frame it like an isolated card on a grey
			// backdrop. Purely an editor affordance — never touches front-end
			// output, and every other document type renders full width as
			// before.
			( function () {
				function applyPreviewWidth() {
					var config = elementor.config && elementor.config.document;
					if ( ! config || "epl-loop-card" !== config.type ) {
						return;
					}

					var iframe = document.getElementById( "elementor-preview-iframe" );
					var doc = iframe && iframe.contentDocument;
					if ( ! doc || ! doc.head ) {
						return;
					}

					var width = 320;
					if ( elementor.settings && elementor.settings.page && elementor.settings.page.model ) {
						var configured = parseInt( elementor.settings.page.model.get( "epl_card_preview_width" ), 10 );
						if ( configured ) {
							width = configured;
						}
					}

					var style = doc.getElementById( "epl-card-preview-style" );
					if ( ! style ) {
						style = doc.createElement( "style" );
						style.id = "epl-card-preview-style";
						doc.head.appendChild( style );
					}

					// The card document still renders through the active theme
					// normal single-post wrapper (header, page title, footer) —
					// there is no bare data-elementor-type directly under body
					// to key off. Hide that surrounding chrome and narrow the
					// actual Elementor wrapper wherever it sits in the tree
					// instead.
					style.textContent = (
						"body { background: #e4e4e4 !important; }" +
						"#site-header, #site-footer, .page-header, .site-header, .site-footer { display: none !important; }" +
						"[data-elementor-type] { max-width: " + width + "px !important; margin: 40px auto !important; float: none !important; " +
						"background: #fff !important; box-shadow: 0 0 0 1px rgba(0,0,0,.08), 0 12px 28px rgba(0,0,0,.15) !important; }"
					);
				}

				elementor.on( "preview:loaded", function () {
					applyPreviewWidth();

					if ( elementor.settings && elementor.settings.page && elementor.settings.page.model ) {
						elementor.settings.page.model.on( "change:epl_card_preview_width", applyPreviewWidth );
					}
				} );
			} )();
			'
		);
	}

	/**
	 * Get EPL Post Types
	 *
	 * @return array
	 * @since 3.6.0
	 */
	public static function get_post_types() {
		$post_types = array();
		$epl_posts  = epl_get_core_post_types();

		foreach ( $epl_posts as $post_type ) {
			$obj = get_post_type_object( $post_type );
			if ( $obj ) {
				$post_types[ $post_type ] = $obj->labels->singular_name;
			}
		}

		return $post_types;
	}

	/**
	 * Get Listing Status Options
	 *
	 * @return array
	 * @since 3.6.0
	 */
	public static function get_status_options() {
		return array(
			''          => esc_html__( 'All', 'easy-property-listings' ),
			'current'   => esc_html__( 'Current', 'easy-property-listings' ),
			'sold'      => esc_html__( 'Sold', 'easy-property-listings' ),
			'leased'    => esc_html__( 'Leased', 'easy-property-listings' ),
			'withdrawn' => esc_html__( 'Withdrawn', 'easy-property-listings' ),
			'offmarket' => esc_html__( 'Off Market', 'easy-property-listings' ),
		);
	}

	/**
	 * Listing context stack for widget renders.
	 *
	 * @var array
	 * @since 3.6.0
	 */
	private static $context_stack = array();

	/**
	 * Set up the global listing context for a widget render.
	 *
	 * Resolves the property from the existing global, the current post, or —
	 * in the Elementor editor — a preview listing. When a preview listing is
	 * used, the global $post is switched as well so core template callbacks
	 * that read the global post (galleries, stickers, content) work too.
	 *
	 * Always pair with restore_listing_context() before render() returns.
	 *
	 * @return EPL_Property_Meta|false The property object or false if none found.
	 * @since 3.6.0
	 */
	public static function setup_listing_context() {
		global $property, $post, $epl_author, $epl_author_secondary, $epl_author_third, $epl_author_fourth;

		$frame = array(
			'post'                 => isset( $post ) ? $post : null,
			'property'             => isset( $property ) ? $property : null,
			'epl_author'           => isset( $epl_author ) ? $epl_author : null,
			'epl_author_secondary' => isset( $epl_author_secondary ) ? $epl_author_secondary : null,
			'epl_author_third'     => isset( $epl_author_third ) ? $epl_author_third : null,
			'epl_author_fourth'    => isset( $epl_author_fourth ) ? $epl_author_fourth : null,
		);

		// The current loop post is authoritative. A global property can be stale
		// while Elementor renders nested loop documents.
		if ( $post && is_epl_post( $post->post_type ) && ( ! $property || (int) $property->post->ID !== (int) $post->ID ) ) {
			$property = new EPL_Property_Meta( $post );
		}

		// Nested Loop Item documents can temporarily expose the Archive document
		// or a regular post as the global post while Elementor builds its editor
		// canvas. In that case a stale global property must not suppress the EPL
		// preview fallback.
		if ( ( self::is_editor() || self::is_epl_elementor_document_context() ) && ( ! $post || ! is_epl_post( $post->post_type ) ) ) {
			$preview = self::get_preview_property();

			if ( $preview ) {
				$property = $preview;
				$post     = $preview->post; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- Restored in restore_listing_context().
				setup_postdata( $post );
			}
		}

		self::$context_stack[] = $frame;

		return $property ? $property : false;
	}

	/**
	 * Restore the global listing context after a widget render.
	 *
	 * @since 3.6.0
	 */
	public static function restore_listing_context() {
		global $property, $post, $epl_author, $epl_author_secondary, $epl_author_third, $epl_author_fourth;

		$frame = array_pop( self::$context_stack );

		if ( null === $frame ) {
			return;
		}

		$post                 = $frame['post']; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- Restoring original.
		$property             = $frame['property']; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- Restoring original.
		$epl_author           = $frame['epl_author']; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- Restoring original.
		$epl_author_secondary = $frame['epl_author_secondary']; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- Restoring original.
		$epl_author_third     = $frame['epl_author_third']; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- Restoring original.
		$epl_author_fourth    = $frame['epl_author_fourth']; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- Restoring original.

		if ( $post instanceof WP_Post ) {
			setup_postdata( $post );
		}
	}

	/**
	 * Whether Elementor is rendering an editor preview.
	 *
	 * @return bool
	 */
	public static function is_editor() {
		$is_edit_mode       = isset( \Elementor\Plugin::$instance->editor ) && \Elementor\Plugin::$instance->editor->is_edit_mode();
		$is_preview         = isset( \Elementor\Plugin::$instance->preview ) && \Elementor\Plugin::$instance->preview->is_preview_mode();
		$is_preview_request = isset( $_GET['elementor-preview'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only preview detection.
		$ajax_action       = isset( $_REQUEST['action'] ) ? sanitize_key( wp_unslash( $_REQUEST['action'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only editor request detection.
		$is_elementor_ajax = wp_doing_ajax() && 0 === strpos( $ajax_action, 'elementor' );

		return $is_edit_mode || $is_preview || $is_preview_request || $is_elementor_ajax;
	}

	/**
	 * Detect an EPL Loop Item while Elementor renders a nested document.
	 *
	 * Elementor does not consistently expose edit/preview mode while a Loop Item
	 * is rendered inside an Archive document. Inspect the active Elementor
	 * documents so dynamic EPL widgets can still receive preview listing data.
	 *
	 * @return bool
	 */
	private static function is_epl_elementor_document_context() {
		$document_ids = array();
		$current_post = get_post();

		if ( $current_post && 'elementor_library' === $current_post->post_type ) {
			$document_ids[] = (int) $current_post->ID;
		}

		if ( isset( \Elementor\Plugin::$instance->documents ) ) {
			$current_document = \Elementor\Plugin::$instance->documents->get_current();
			if ( $current_document ) {
				$document_ids[] = (int) $current_document->get_main_id();
			}
		}

		foreach ( array_unique( array_filter( $document_ids ) ) as $document_id ) {
			$data = (string) get_post_meta( $document_id, '_elementor_data', true );
			if ( 'listing' === self::get_epl_template_context( $document_id ) ) {
				return true;
			}

			if ( preg_match_all( '/"template_id":(?:"?)(\d+)(?:"?)/', $data, $matches ) ) {
				foreach ( $matches[1] as $template_id ) {
					if ( 'listing' === self::get_epl_template_context( absint( $template_id ) ) ) {
						return true;
					}
				}
			}
		}

		return false;
	}

	/**
	 * Resolve one listing agent without assuming agents are WordPress users.
	 *
	 * @param EPL_Property_Meta $property Listing property.
	 * @param int               $index    Zero-based agent position.
	 * @return EPL_Author_Loader|null
	 */
	public static function get_listing_agent( $property, $index = 0 ) {
		if ( 0 === $index && isset( $property->post ) && 'directory' === $property->post->post_type ) {
			return new EPL_Author_Loader( $property->post->ID );
		}

		$keys = array( 'property_agent', 'property_second_agent', 'property_third_agent', 'property_fourth_agent' );
		$key  = isset( $keys[ $index ] ) ? $keys[ $index ] : $keys[0];
		$raw  = $property->get_property_meta( $key );
		$id   = 0;

		// Staff Directory stores the canonical identity in a directory post.
		if ( 'directory' === epl_get_option( 'epl_sd_staff_details_location', 'wp_user' ) && post_type_exists( 'directory' ) && ! empty( $raw ) ) {
			$directory = self::get_directory_agent( $raw );
			$id        = $directory instanceof WP_Post ? $directory->ID : 0;
		} elseif ( ! empty( $raw ) ) {
			$user = get_user_by( 'login', sanitize_user( (string) $raw ) );
			$id   = $user ? $user->ID : ( is_numeric( $raw ) ? absint( $raw ) : 0 );
		}

		if ( 0 === $index && ! $id && isset( $property->post->post_author ) ) {
			$id = absint( $property->post->post_author );
		}

		$id = absint( apply_filters( 'epl_elementor_agent_id', $id, $raw, $index, $property ) );
		return $id ? new EPL_Author_Loader( $id ) : null;
	}

	/**
	 * Resolve a Staff Directory username deterministically.
	 *
	 * Imports can leave more than one directory post with the same staff
	 * username. Prefer the record with a featured image and then the oldest ID,
	 * so editor and frontend queries always select the same canonical record.
	 *
	 * @param string $username Staff username stored on the listing.
	 * @return WP_Post|null
	 */
	private static function get_directory_agent( $username ) {
		$posts = get_posts(
			array(
				'post_type'      => 'directory',
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'orderby'        => 'ID',
				'order'          => 'ASC',
				'meta_key'       => 'property_staff_username', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				'meta_value'     => sanitize_user( (string) $username ), // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			)
		);

		if ( empty( $posts ) ) {
			return null;
		}

		foreach ( $posts as $directory ) {
			if ( has_post_thumbnail( $directory ) ) {
				return apply_filters( 'epl_elementor_directory_agent', $directory, $posts, $username );
			}
		}

		return apply_filters( 'epl_elementor_directory_agent', reset( $posts ), $posts, $username );
	}

	/**
	 * Resolve all unique agents assigned to a listing.
	 *
	 * @param EPL_Property_Meta $property Listing property.
	 * @return array
	 */
	public static function get_listing_agents( $property ) {
		$agents = array();
		$seen   = array();

		for ( $index = 0; $index < 4; $index++ ) {
			$agent = self::get_listing_agent( $property, $index );
			if ( $agent && ! isset( $seen[ (string) $agent->author_id ] ) ) {
				$seen[ (string) $agent->author_id ] = true;
				$agents[]                          = $agent;
			}
		}

		return apply_filters( 'epl_elementor_listing_agents', $agents, $property );
	}

	/**
	 * Get an agent image, including Staff Directory post-backed images.
	 *
	 * Staff Directory's author-box image filter is conditionally attached and
	 * can be unavailable while Elementor renders nested documents on an archive.
	 * Resolve its canonical directory image first, then retain the public EPL
	 * filter so extensions can make a final adjustment.
	 *
	 * @param EPL_Author_Loader $agent Agent loader.
	 * @param int               $size  Requested image size in pixels.
	 * @return string
	 */
	public static function get_agent_image( $agent, $size = 150 ) {
		$size       = max( 1, absint( $size ) );
		$image_html = get_avatar( $agent->email, $size );
		$directory  = ! empty( $agent->directory ) && $agent->directory instanceof WP_Post ? $agent->directory : null;

		if ( $directory && 'directory' === $directory->post_type && 1 === (int) epl_get_option( 'epl_sd_staff_image_type', 0 ) ) {
			$image_size = apply_filters( 'epl_sd_author_tab_image_size', epl_get_option( 'epl_sd_staff_image_size_box', 'thumbnail' ) );
			if ( has_post_thumbnail( $directory ) ) {
				$image_html = wp_get_attachment_image( get_post_thumbnail_id( $directory ), $image_size );
			} elseif ( function_exists( 'epl_sd_has_fallback_featured_image' ) && epl_sd_has_fallback_featured_image() && function_exists( 'epl_sd_get_the_fallback_featured_image' ) ) {
				$image_html = epl_sd_get_the_fallback_featured_image( $image_size, array( 'class' => 'epl-sd-fallback-image' ) );
			}
		}

		$image_html = apply_filters( 'epl_author_tab_image', $image_html, $agent );
		return apply_filters( 'epl_elementor_agent_image', $image_html, $agent, $size );
	}

	/**
	 * Apply EPL defaults to an Elementor Pro Loop Grid query whose Query ID is
	 * "epl_listings".
	 *
	 * @param WP_Query               $query  Elementor query.
	 * @param \Elementor\Widget_Base $widget Elementor widget.
	 */
	public function elementor_listings_query( $query, $widget ) {
		$post_type = $query->get( 'post_type' );
		if ( empty( $post_type ) || 'post' === $post_type ) {
			$query->set( 'post_type', epl_get_core_post_types() );
		}
		$query->set( 'epl_elementor_loop', true );
		do_action( 'epl_elementor_loop_query', $query, $widget );
	}

	/**
	 * Configure an Elementor Pro Loop Grid whose Query ID is "epl_staff".
	 *
	 * @param WP_Query               $query  Elementor query.
	 * @param \Elementor\Widget_Base $widget Elementor widget.
	 */
	public function elementor_staff_query( $query, $widget ) {
		$query->set( 'post_type', 'directory' );
		$query->set( 'epl_elementor_context', 'staff' );
		do_action( 'epl_elementor_staff_query', $query, $widget );
	}

	/**
	 * Ensure Theme Builder owns Staff Directory single/archive requests.
	 *
	 * Staff Directory renders through EPL's PHP templates, which do not call
	 * Elementor's single/archive theme locations. Force Elementor's header/footer
	 * page template only for Directory requests where a matching Theme Builder
	 * document has already been selected.
	 *
	 * @param bool   $override          Whether Elementor should override.
	 * @param string $location          Theme location.
	 * @param object $locations_manager Elementor locations manager.
	 * @return bool
	 */
	public function override_staff_directory_location( $override, $location, $locations_manager ) {
		if ( 'single' === $location && is_singular( 'directory' ) ) {
			return true;
		}
		if ( 'archive' === $location && ( is_post_type_archive( 'directory' ) || is_tax( 'department' ) ) ) {
			return true;
		}
		return $override;
	}

	/**
	 * Make Loop Grids that use an EPL Loop Item query EPL listings in both the
	 * Theme Builder editor and on the archive front end. Elementor otherwise
	 * defaults an unset Loop Grid source to regular posts.
	 *
	 * @param array                  $query_args Elementor query arguments.
	 * @param \Elementor\Widget_Base $widget     Elementor widget.
	 * @return array
	 */
	public function elementor_loop_query_args( $query_args, $widget ) {
		if ( ! $widget || 'loop-grid' !== $widget->get_name() ) {
			return $query_args;
		}

		$template_id = absint( $widget->get_settings( 'template_id' ) );
		$context     = $template_id ? self::get_epl_template_context( $template_id ) : '';
		if ( ! $context ) {
			return $query_args;
		}

		$source    = (string) $widget->get_settings( 'post_query_post_type' );
		$is_editor = self::is_editor();

		// Elementor resolves "Current Query" from the global query. Inside the
		// Theme Builder editor that global points at the elementor_library archive
		// document, producing an empty Loop Grid. Use representative EPL posts for
		// the editor only; the live archive must continue inheriting its real query.
		if ( $is_editor && 'current_query' === $source ) {
			$query_args = array(
				'post_type'           => 'staff' === $context ? array( 'directory' ) : self::get_archive_preview_post_types(),
				'post_status'         => 'publish',
				'posts_per_page'      => max( 1, absint( $widget->get_settings( 'posts_per_page' ) ) ),
				'paged'               => 1,
				'orderby'             => 'date',
				'order'               => 'DESC',
				'ignore_sticky_posts' => true,
			);
		}

		$post_type = isset( $query_args['post_type'] ) ? $query_args['post_type'] : '';
		if ( empty( $post_type ) || 'post' === $post_type ) {
			$query_args['post_type'] = 'staff' === $context ? array( 'directory' ) : self::get_archive_preview_post_types();
		}

		$query_args['epl_elementor_loop'] = true;
		$query_args['epl_elementor_context'] = $context;
		return apply_filters( 'epl_elementor_loop_query_args', $query_args, $widget, $template_id );
	}

	/**
	 * Supply representative listings for a Current Query Loop Grid in the editor.
	 *
	 * Elementor returns early for Current Query and therefore does not run its
	 * general elementor/query/query_args filter. Its Theme Builder preview query
	 * normally contains one post, irrespective of the Loop Grid item count.
	 *
	 * @param array $query_args Current global query variables.
	 * @return array
	 */
	public function elementor_current_query_args( $query_args ) {
		// Prefer the live widget model. This includes unsaved editor changes and
		// avoids stale document/element caches. Parsing the saved archive remains
		// a fallback for render paths that do not fire Elementor's widget hooks.
		$loop_settings = $this->active_loop_grid_settings;
		if ( empty( $loop_settings ) ) {
			$loop_settings = self::get_current_epl_loop_grid_settings();
		}
		if ( empty( $loop_settings ) ) {
			return $query_args;
		}

		$posts_per_page = isset( $loop_settings['posts_per_page'] ) ? absint( $loop_settings['posts_per_page'] ) : 3;
		$posts_per_page = max( 1, $posts_per_page );

		// Elementor's Current Query source copies the archive's main query and
		// returns before its normal Loop Grid query filters run. That also copies
		// the site's Reading setting (commonly 8 or 10 posts), silently ignoring
		// the Loop Grid's Items Per Page control. Keep the real archive filters on
		// the frontend, but make the saved widget value authoritative.
		if ( ! self::is_editor() ) {
			$query_args['posts_per_page']     = $posts_per_page;
			$query_args['epl_elementor_loop'] = true;
			if ( ! empty( $loop_settings['_epl_widget_id'] ) ) {
				$query_args['instance_id']        = 'elementor-' . sanitize_key( $loop_settings['_epl_widget_id'] );
				$query_args['is_epl_shortcode']   = true;
				$query_args['epl_shortcode_name'] = 'listing_advanced';
			}
			return $query_args;
		}

		return array(
			'post_type'           => self::get_archive_preview_post_types(),
			'post_status'         => 'publish',
			'posts_per_page'      => $posts_per_page,
			'paged'               => 1,
			'orderby'             => 'date',
			'order'               => 'DESC',
			'ignore_sticky_posts' => true,
			'epl_elementor_loop'  => true,
		);
	}

	/** Capture live Loop Grid settings immediately before its query is created. */
	public function begin_loop_grid_render( $widget ) {
		if ( ! $widget || 'loop-grid' !== $widget->get_name() ) {
			return;
		}
		$template_id = absint( $widget->get_settings( 'template_id' ) );
		if ( $template_id && 'listing' === self::get_epl_template_context( $template_id ) ) {
			$this->active_loop_grid_settings = $widget->get_settings_for_display();
			$this->active_loop_grid_settings['_epl_widget_id'] = $widget->get_id();
		}
	}

	/** Clear live Loop Grid state after rendering to avoid affecting another query. */
	public function end_loop_grid_render( $widget ) {
		if ( $widget && 'loop-grid' === $widget->get_name() ) {
			$this->active_loop_grid_settings = array();
		}
	}

	/**
	 * Add an EPL renderer choice to Elementor Pro's native Loop Grid pagination.
	 *
	 * Elementor remains the default and retains all of its AJAX/load-more modes.
	 * EPL pagination is available for the numbered pagination modes.
	 *
	 * @param \Elementor\Element_Base $element Loop Grid element.
	 */
	public function register_loop_grid_pagination_provider( $element ) {
		$element->add_control(
			'epl_pagination_provider',
			array(
				'label'     => esc_html__( 'Pagination Renderer', 'easy-property-listings' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'default'   => 'elementor',
				'options'   => array(
					'elementor' => esc_html__( 'Elementor', 'easy-property-listings' ),
					'epl'       => esc_html__( 'EPL', 'easy-property-listings' ),
				),
				'condition' => array(
					'pagination_type!' => array( '', 'load_more_on_click', 'load_more_infinite_scroll' ),
				),
			)
		);
		$element->add_control(
			'epl_pagination_style',
			array(
				'label'     => esc_html__( 'EPL Pagination Type', 'easy-property-listings' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'default'   => 'default',
				'options'   => array(
					'fancy'   => esc_html__( 'Fancy', 'easy-property-listings' ),
					'default' => esc_html__( 'WordPress Default', 'easy-property-listings' ),
				),
				'condition' => array(
					'epl_pagination_provider' => 'epl',
				),
			)
		);
	}

	/**
	 * Retain the exact query Elementor used so EPL pagination cannot drift from it.
	 *
	 * @param WP_Query              $query  Elementor query.
	 * @param \Elementor\Widget_Base $widget Elementor widget.
	 */
	public function capture_loop_grid_query( $query, $widget ) {
		if ( $query instanceof WP_Query && $widget && 'loop-grid' === $widget->get_name() ) {
			$this->loop_grid_queries[ $widget->get_id() ] = $query;
		}
	}

	/**
	 * Replace Loop Grid's native numbered navigation with EPL navigation on demand.
	 *
	 * @param string                 $content Rendered widget HTML.
	 * @param \Elementor\Widget_Base $widget  Elementor widget.
	 * @return string
	 */
	public function render_loop_grid_epl_pagination( $content, $widget ) {
		if ( ! $widget || 'loop-grid' !== $widget->get_name() ) {
			return $content;
		}
		$integrated_epl = 'epl' === $widget->get_settings( 'epl_pagination_provider' );
		$standalone_epl = self::current_document_has_epl_pagination_widget();
		if ( ! $integrated_epl && ! $standalone_epl ) {
			return $content;
		}

		$query = $this->loop_grid_queries[ $widget->get_id() ] ?? null;
		if ( ! $query instanceof WP_Query ) {
			return $content;
		}

		// Native numbered pagination is removed only after Elementor has used its
		// settings to calculate the correct current page and query offset.
		$content = preg_replace( '#\s*<div class="e-load-more-anchor"[^>]*></div>\s*<nav class="elementor-pagination".*?</nav>#s', '', $content );
		if ( $standalone_epl ) {
			return $content;
		}
		ob_start();
		$pagination_style = $widget->get_settings( 'epl_pagination_style' );
		self::render_loop_grid_epl_navigation( $query, $widget->get_id(), $pagination_style ? $pagination_style : 'default' );
		return $content . ob_get_clean();
	}

	/** Whether the active Elementor archive already contains EPL Pagination. */
	private static function current_document_has_epl_pagination_widget() {
		$document_id = get_the_ID();
		if ( isset( \Elementor\Plugin::$instance->documents ) ) {
			$document = \Elementor\Plugin::$instance->documents->get_current();
			if ( $document ) {
				$document_id = $document->get_main_id();
			}
		}
		$data = $document_id ? (string) get_post_meta( $document_id, '_elementor_data', true ) : '';
		return false !== strpos( $data, '"widgetType":"epl-pagination"' );
	}

	/**
	 * Render canonical EPL navigation for an Elementor Loop Grid.
	 *
	 * @param WP_Query $query     Loop Grid query.
	 * @param string   $widget_id Elementor widget ID.
	 */
	public static function render_loop_grid_epl_navigation( $query, $widget_id, $pagination_style = '' ) {
		if ( ! $query instanceof WP_Query || $query->max_num_pages < 2 ) {
			return;
		}
		$original = array(
			'instance_id'        => $query->get( 'instance_id' ),
			'is_epl_shortcode'   => $query->get( 'is_epl_shortcode' ),
			'epl_shortcode_name' => $query->get( 'epl_shortcode_name' ),
		);
		$query->set( 'instance_id', 'elementor-' . sanitize_key( $widget_id ) );
		$query->set( 'is_epl_shortcode', true );
		$query->set( 'epl_shortcode_name', 'listing_advanced' );
		self::render_epl_pagination( $query, $pagination_style );
		foreach ( $original as $key => $value ) {
			$query->set( $key, $value );
		}
	}

	/**
	 * Render EPL pagination while optionally overriding its global style setting.
	 *
	 * @param WP_Query $query            Query being paginated.
	 * @param string   $pagination_style Either "fancy", "default", or empty to
	 *                                   follow the global EPL setting.
	 */
	public static function render_epl_pagination( $query, $pagination_style = '' ) {
		$style_filter = null;
		if ( in_array( $pagination_style, array( 'fancy', 'default' ), true ) ) {
			$style_filter = static function () use ( $pagination_style ) {
				return 'fancy' === $pagination_style ? 1 : 0;
			};
			add_filter( 'epl_get_option_use_fancy_navigation', $style_filter, PHP_INT_MAX );
		}

		do_action( 'epl_pagination', array( 'query' => $query ) );

		if ( $style_filter ) {
			remove_filter( 'epl_get_option_use_fancy_navigation', $style_filter, PHP_INT_MAX );
		}
	}

	/** Return the most recently rendered Loop Grid query for a following widget. */
	public function get_last_loop_grid_query() {
		if ( empty( $this->loop_grid_queries ) ) {
			return array();
		}
		$widget_id = array_key_last( $this->loop_grid_queries );
		return array( 'widget_id' => $widget_id, 'query' => $this->loop_grid_queries[ $widget_id ] );
	}

	/** Prevent the archive main-query page size from 404ing an EPL Loop Grid page. */
	public function allow_epl_loop_grid_page( $preempt, $query ) {
		$instance_id = isset( $_GET['pagination_id'] ) ? sanitize_text_field( wp_unslash( $_GET['pagination_id'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Public pagination state.
		if ( 0 !== strpos( $instance_id, 'elementor-' ) || ! is_post_type_archive( epl_get_core_post_types() ) ) {
			return $preempt;
		}
		return true;
	}

	/**
	 * Find the EPL Loop Grid settings in the active Elementor document.
	 *
	 * @return array
	 */
	private static function get_current_epl_loop_grid_settings() {
		$document_ids = array();
		$current_post = get_post();

		if ( $current_post && 'elementor_library' === $current_post->post_type ) {
			$document_ids[] = (int) $current_post->ID;
		}

		if ( isset( \Elementor\Plugin::$instance->documents ) ) {
			$current_document = \Elementor\Plugin::$instance->documents->get_current();
			if ( $current_document ) {
				$document_ids[] = (int) $current_document->get_main_id();
			}
		}

		foreach ( array_unique( array_filter( $document_ids ) ) as $document_id ) {
			$elements = json_decode( (string) get_post_meta( $document_id, '_elementor_data', true ), true );
			$stack    = is_array( $elements ) ? $elements : array();

			while ( $stack ) {
				$element = array_pop( $stack );
				if ( ! is_array( $element ) ) {
					continue;
				}

				if ( 'loop-grid' === ( $element['widgetType'] ?? '' ) ) {
					$settings    = isset( $element['settings'] ) && is_array( $element['settings'] ) ? $element['settings'] : array();
					$template_id = isset( $settings['template_id'] ) ? absint( $settings['template_id'] ) : 0;
					if ( $template_id && 'listing' === self::get_epl_template_context( $template_id ) ) {
						$settings['_epl_widget_id'] = isset( $element['id'] ) ? sanitize_key( $element['id'] ) : '';
						return $settings;
					}
				}

				if ( ! empty( $element['elements'] ) && is_array( $element['elements'] ) ) {
					$stack = array_merge( $stack, $element['elements'] );
				}
			}
		}

		return array();
	}

	/**
	 * Determine whether a Loop Item is for listings or Staff Directory posts.
	 *
	 * @param int $template_id Elementor template ID.
	 * @return string "listing", "staff", or an empty string.
	 */
	private static function get_epl_template_context( $template_id ) {
		$stored_context = (string) get_post_meta( $template_id, '_epl_elementor_template_context', true );
		if ( in_array( $stored_context, array( 'listing', 'staff' ), true ) ) {
			return $stored_context;
		}

		// A brand-new EPL Single Listing / Listing Card document has no
		// content yet to sniff below, but its type alone already implies
		// listing context (its own save() sets the meta above afterwards).
		$template_type = get_post_meta( $template_id, '_elementor_template_type', true );
		if ( in_array( $template_type, array( EPL_Elementor_Single_Document::TYPE, EPL_Elementor_Loop_Card_Document::TYPE ), true ) ) {
			return 'listing';
		}

		$data = (string) get_post_meta( $template_id, '_elementor_data', true );
		if ( false !== strpos( $data, '"widgetType":"epl-property-' ) || false !== strpos( $data, '"widgetType":"epl-listing-agents"' ) ) {
			return 'listing';
		}
		if ( false !== strpos( $data, '"widgetType":"epl-agent-' ) ) {
			return 'staff';
		}
		return '';
	}

	/**
	 * Infer the intended EPL archive post type from the current archive
	 * document's display conditions. Falls back to every core listing type.
	 *
	 * @return array
	 */
	private static function get_archive_preview_post_types() {
		$post_types  = epl_get_core_post_types();
		$document_id = get_the_ID();
		if ( isset( \Elementor\Plugin::$instance->documents ) ) {
			$current_document = \Elementor\Plugin::$instance->documents->get_current();
			if ( $current_document ) {
				$document_id = $current_document->get_main_id();
			}
		}
		$conditions  = $document_id ? get_post_meta( $document_id, '_elementor_conditions', true ) : array();

		if ( is_array( $conditions ) ) {
			foreach ( $conditions as $condition ) {
				$parts = explode( '/', (string) $condition );
				$last  = end( $parts );
				if ( substr( $last, -8 ) === '_archive' ) {
					$candidate = substr( $last, 0, -8 );
					if ( in_array( $candidate, $post_types, true ) ) {
						return array( $candidate );
					}
				}
			}
		}

		return $post_types;
	}

	/**
	 * Track and, when necessary, restore EPL archive display conditions.
	 *
	 * @param int          $meta_id    Metadata ID.
	 * @param int          $post_id    Template post ID.
	 * @param string       $meta_key   Metadata key.
	 * @param string|array $meta_value Metadata value.
	 */
	public function track_archive_conditions( $meta_id, $post_id, $meta_key, $meta_value ) {
		if ( '_elementor_conditions' !== $meta_key || 'archive' !== get_post_meta( $post_id, '_elementor_template_type', true ) ) {
			return;
		}

		if ( ! empty( $meta_value ) && is_array( $meta_value ) ) {
			update_post_meta( $post_id, '_epl_elementor_conditions_backup', $meta_value );
		}

		self::$archive_condition_checks[ $post_id ] = true;
		if ( ! self::$archive_condition_shutdown_registered ) {
			self::$archive_condition_shutdown_registered = true;
			add_action( 'shutdown', array( $this, 'restore_archive_conditions' ), 1 );
		}
	}

	/**
	 * Archive template IDs whose conditions changed during this request.
	 *
	 * @var array
	 */
	private static $archive_condition_checks = array();

	/**
	 * Whether the shutdown restoration callback is registered.
	 *
	 * @var bool
	 */
	private static $archive_condition_shutdown_registered = false;

	/**
	 * Restore accidentally emptied conditions after Elementor finishes saving.
	 */
	public function restore_archive_conditions() {
		foreach ( array_keys( self::$archive_condition_checks ) as $post_id ) {
			$current = get_post_meta( $post_id, '_elementor_conditions', true );
			$backup  = get_post_meta( $post_id, '_epl_elementor_conditions_backup', true );

			if ( empty( $current ) && ! empty( $backup ) && apply_filters( 'epl_elementor_restore_archive_conditions', true, $post_id, $backup ) ) {
				update_post_meta( $post_id, '_elementor_conditions', $backup );
			}
		}
	}

	/**
	 * Get a sample property for editor preview.
	 *
	 * Fetches a random published EPL listing to use as preview data in the Elementor editor.
	 *
	 * @return EPL_Property_Meta|false The property object or false if none found.
	 * @since 3.6.0
	 */
	public static function get_preview_property() {
		static $preview_property = null;

		// Return cached if already fetched.
		if ( null !== $preview_property ) {
			return $preview_property;
		}

		$requested_id = isset( $_GET['epl_elementor_preview_id'] ) ? absint( wp_unslash( $_GET['epl_elementor_preview_id'] ) ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only editor preview.
		$requested_id = absint( apply_filters( 'epl_elementor_preview_listing_id', $requested_id ) );
		if ( $requested_id ) {
			$requested_post = get_post( $requested_id );
			if ( $requested_post && is_epl_post( $requested_post->post_type ) ) {
				$preview_property = new EPL_Property_Meta( $requested_post );
				return $preview_property;
			}
		}

		// Prefer the listing type selected by the active Archive document. Editing
		// a Loop Item directly has no archive condition, so this naturally falls
		// back to every core EPL listing type.
		$post_types = self::get_archive_preview_post_types();

		if ( empty( $post_types ) ) {
			$preview_property = false;
			return false;
		}

		// Prefer a listing with a featured image so image-led Loop Items produce a
		// useful preview while Elementor switches templates. Fall back to any EPL
		// listing below for sites whose listings do not use featured images.
		$args = array(
			'post_type'      => $post_types,
			'post_status'    => 'publish',
			'posts_per_page' => 1,
			'orderby'        => 'date',
			'order'          => 'DESC',
			'no_found_rows'  => true,
			'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- Single editor-preview record only.
				array(
					'key'     => '_thumbnail_id',
					'compare' => 'EXISTS',
				),
			),
		);

		$query = new WP_Query( $args );
		if ( ! $query->have_posts() ) {
			unset( $args['meta_query'] );
			$query = new WP_Query( $args );
		}

		if ( $query->have_posts() ) {
			$preview_property = new EPL_Property_Meta( $query->posts[0] );
		} else {
			$preview_property = false;
		}
		return $preview_property;
	}
}

// Initialize.
add_action( 'elementor/loaded', array( 'EPL_Elementor', 'instance' ) );
