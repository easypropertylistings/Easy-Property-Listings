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
	 * @var string Minimum Elementor version required.
	 * @since 3.6.0
	 */
	const MINIMUM_ELEMENTOR_VERSION = '3.0.0';

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

		// Check Elementor version.
		if ( ! $this->is_compatible() ) {
			add_action( 'admin_notices', array( $this, 'admin_notice_minimum_elementor_version' ) );
			return;
		}

		// Register widget categories.
		add_action( 'elementor/elements/categories_registered', array( $this, 'register_widget_categories' ) );

		// Register widgets.
		add_action( 'elementor/widgets/register', array( $this, 'register_widgets' ) );

		// Enqueue styles.
		add_action( 'elementor/frontend/after_enqueue_styles', array( $this, 'frontend_styles' ) );
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
			'EPL_Elementor_Agents_Loop',
			'EPL_Elementor_Agent_Photo',
			'EPL_Elementor_Agent_Name',
			'EPL_Elementor_Agent_Position',
			'EPL_Elementor_Agent_Contact',
			'EPL_Elementor_Agent_Social',
			'EPL_Elementor_Agent_Social',
			'EPL_Elementor_Agent_Bio',
			'EPL_Elementor_Property_Map',
			'EPL_Elementor_Property_Map',
			'EPL_Elementor_Property_Gallery',
			'EPL_Elementor_Property_Video',
			'EPL_Elementor_Property_Buttons',
			'EPL_Elementor_Property_Status',
			'EPL_Elementor_Single_Extensions',
		);

		foreach ( $element_widgets as $widget_class ) {
			if ( class_exists( $widget_class ) ) {
				$widgets_manager->register( new $widget_class() );
			}
		}

		// Archive widgets.
		$archive_widgets = array(
			'EPL_Elementor_Listing_Grid',
			'EPL_Elementor_Listing_Search',
		);

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
			'class-epl-elementor-agent-social.php',
			'class-epl-elementor-agent-bio.php',
			'class-epl-elementor-property-map.php',
			'class-epl-elementor-property-map.php',
			'class-epl-elementor-property-gallery.php',
			'class-epl-elementor-property-video.php',
			'class-epl-elementor-property-buttons.php',
			'class-epl-elementor-property-status.php',
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
			'class-epl-elementor-listing-grid.php',
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

		// Add inline script for tabs functionality.
		wp_add_inline_script(
			'jquery',
			'
			document.addEventListener("DOMContentLoaded", function() {
				document.querySelectorAll(".epl-agents-tabs .epl-tab-button").forEach(function(button) {
					button.addEventListener("click", function() {
						var tabId = this.getAttribute("data-tab");
						var container = this.closest(".epl-agents-tabs");
						
						// Remove active from all buttons and contents.
						container.querySelectorAll(".epl-tab-button").forEach(function(btn) {
							btn.classList.remove("active");
						});
						container.querySelectorAll(".epl-tab-content").forEach(function(content) {
							content.classList.remove("active");
						});
						
						// Add active to clicked button and its content.
						this.classList.add("active");
						document.getElementById(tabId).classList.add("active");
					});
				});
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
