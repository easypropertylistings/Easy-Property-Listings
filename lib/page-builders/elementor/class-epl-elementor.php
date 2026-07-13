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

	/**
	 * Constructor
	 *
	 * @since 3.6.0
	 */
	public function __construct() {
		$this->widgets_path = EPL_PATH_LIB . 'page-builders/elementor/widgets/';
		require_once EPL_PATH_LIB . 'page-builders/elementor/class-epl-elementor-agent-document.php';

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

		// Give Elementor Pro Loop Grid users a documented EPL query ID.
		add_action( 'elementor/query/epl_listings', array( $this, 'elementor_listings_query' ), 10, 2 );
		add_action( 'elementor/query/epl_staff', array( $this, 'elementor_staff_query' ), 10, 2 );
		add_filter( 'elementor/query/query_args', array( $this, 'elementor_loop_query_args' ), 20, 2 );
		add_filter( 'elementor/theme/need_override_location', array( $this, 'override_staff_directory_location' ), 20, 3 );

		// Elementor can clear Theme Builder conditions while an Archive document
		// is switched to/from a Loop Item in the editor. Keep a last-known-good
		// copy for EPL archive documents and restore it after that save request.
		add_action( 'added_post_meta', array( $this, 'track_archive_conditions' ), 10, 4 );
		add_action( 'updated_post_meta', array( $this, 'track_archive_conditions' ), 10, 4 );
		add_action( 'deleted_post_meta', array( $this, 'track_archive_conditions' ), 10, 4 );
	}

	/**
	 * Register dedicated Elementor documents.
	 *
	 * @param \Elementor\Core\Documents_Manager $documents_manager Documents manager.
	 */
	public function register_documents( $documents_manager ) {
		$documents_manager->register_document_type( EPL_Elementor_Agent_Document::TYPE, EPL_Elementor_Agent_Document::get_class_full_name() );
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

		if ( ! $property && self::is_editor() ) {
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
		return isset( \Elementor\Plugin::$instance->editor ) && \Elementor\Plugin::$instance->editor->is_edit_mode();
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

		$source        = (string) $widget->get_settings( 'post_query_post_type' );
		$is_edit_mode  = isset( \Elementor\Plugin::$instance->editor ) && \Elementor\Plugin::$instance->editor->is_edit_mode();
		$is_preview    = isset( \Elementor\Plugin::$instance->preview ) && \Elementor\Plugin::$instance->preview->is_preview_mode();
		$is_editor     = $is_edit_mode || $is_preview;

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
	 * Determine whether a Loop Item is for listings or Staff Directory posts.
	 *
	 * @param int $template_id Elementor template ID.
	 * @return string "listing", "staff", or an empty string.
	 */
	private static function get_epl_template_context( $template_id ) {
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

		// Get EPL post types.
		$post_types = epl_get_core_post_types();

		if ( empty( $post_types ) ) {
			$preview_property = false;
			return false;
		}

		// Query for a published listing.
		$args = array(
			'post_type'      => $post_types,
			'post_status'    => 'publish',
			'posts_per_page' => 1,
			'orderby'        => 'date',
			'order'          => 'DESC',
			'no_found_rows'  => true,
		);

		$query = new WP_Query( $args );

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
