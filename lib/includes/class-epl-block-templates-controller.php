<?php
/**
 * Block Templates Controller for Easy Property Listings
 * 
 * Handles Full Site Editing (FSE) support similar to WooCommerce implementation
 *
 * @package EPL
 * @subpackage Block Templates
 * @since 3.6.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'EPL_Block_Templates_Controller' ) ) :
	
	/**
	 * EPL Block Templates Controller
	 * 
	 * @since 3.6.0
	 */
	class EPL_Block_Templates_Controller {

		/**
		 * Constructor
		 *
		 * @since 3.6.0
		 */
		public function __construct() {
			$this->init();
		}

		/**
		 * Initialize the controller
		 *
		 * @since 3.6.0
		 */
		public function init() {
			// Only proceed if current theme supports block templates
			if ( ! $this->theme_supports_block_templates() ) {
				return;
			}

			add_filter( 'get_block_templates', array( $this, 'add_block_templates' ), 10, 3 );
			add_filter( 'pre_get_block_file_template', array( $this, 'get_block_file_template' ), 10, 3 );
			add_filter( 'get_block_template', array( $this, 'add_block_template_object' ), 10, 3 );
		}

		/**
		 * Check if the current theme supports block templates
		 *
		 * @since 3.6.0
		 * @return bool
		 */
		private function theme_supports_block_templates() {
			return (
				current_theme_supports( 'block-templates' ) ||
				wp_is_block_theme()
			);
		}

		/**
		 * Add EPL block templates to the list
		 *
		 * @since 3.6.0
		 * @param WP_Block_Template[] $templates Array of block templates.
		 * @param array               $query Template query args.
		 * @param string              $template_type Template type: 'wp_template' or 'wp_template_part'.
		 * @return WP_Block_Template[]
		 */
		public function add_block_templates( $templates, $query, $template_type ) {
                        
			if ( 'wp_template' === $template_type ) {
				$epl_templates = $this->get_block_templates( $query );
                                $epl_template_parts = $this->get_block_template_parts( $query );
                                
				return array_merge( $templates, $epl_templates, $epl_template_parts );
			}
                        
			return $templates;
		}

		/**
		 * Get EPL block templates
		 *
		 * @since 3.6.0
		 * @param array $query Template query args.
		 * @return WP_Block_Template[]
		 */
		private function get_block_templates( $query = array() ) {
			$templates = array();
			$template_files = $this->get_block_template_files();

			foreach ( $template_files as $template_file ) {
				$template = $this->build_block_template_result_from_file( $template_file, 'wp_template' );
				if ( null !== $template && $this->template_matches_query( $template, $query ) ) {
					$templates[] = $template;
				}
			}

			return $templates;
		}

		/**
		 * Get EPL block template parts
		 *
		 * @since 3.6.0
		 * @param array $query Template query args.
		 * @return WP_Block_Template[]
		 */
		private function get_block_template_parts( $query = array() ) {
			$template_parts = array();
			$template_part_files = $this->get_block_template_part_files();

			foreach ( $template_part_files as $template_file ) {
				$template = $this->build_block_template_result_from_file( $template_file, 'wp_template_part' );
				if ( null !== $template && $this->template_matches_query( $template, $query ) ) {
					$template_parts[] = $template;
				}
			}

			return $template_parts;
		}

		/**
		 * Get list of EPL block template files
		 *
		 * @since 3.6.0
		 * @return array
		 */
		private function get_block_template_files() {
			$template_path = EPL_PATH_LIB . 'templates/block-templates/';
			$template_files = array();

			if ( ! is_dir( $template_path ) ) {
				return $template_files;
			}

			$files = glob( $template_path . '*.html' );
			
			foreach ( $files as $file ) {
				$slug = basename( $file, '.html' );
				$template_files[] = array(
					'slug' => $slug,
					'path' => $file,
					'theme' => 'epl-block-templates',
				);
			}

			return $template_files;
		}

		/**
		 * Get list of EPL block template part files
		 *
		 * @since 3.6.0
		 * @return array
		 */
		private function get_block_template_part_files() {
			$template_path = EPL_PLUGIN_PATH . 'templates/block-template-parts/';
			$template_files = array();

			if ( ! is_dir( $template_path ) ) {
				return $template_files;
			}

			$files = glob( $template_path . '*.html' );
			
			foreach ( $files as $file ) {
				$slug = basename( $file, '.html' );
				$template_files[] = array(
					'slug' => $slug,
					'path' => $file,
					'theme' => 'epl-block-templates',
				);
			}

			return $template_files;
		}

		/**
		 * Build a block template object from file
		 *
		 * @since 3.6.0
		 * @param array  $template_file Template file data.
		 * @param string $template_type Template type.
		 * @return WP_Block_Template|null
		 */
		private function build_block_template_result_from_file( $template_file, $template_type ) {
			if ( ! file_exists( $template_file['path'] ) ) {
				return null;
			}

			$content = file_get_contents( $template_file['path'] );
			
			if ( false === $content ) {
				return null;
			}

			$template = new WP_Block_Template();
			$template->id             = 'epl-block-templates//' . $template_file['slug'];
			$template->theme          = 'epl-block-templates';
			$template->content        = $content;
			$template->slug           = $template_file['slug'];
			$template->source         = 'plugin';
			$template->type           = $template_type;
			$template->title          = $this->get_template_title( $template_file['slug'] );
			$template->status         = 'publish';
			$template->has_theme_file = true;
			$template->is_custom      = false;

			// Set area for template parts
			if ( 'wp_template_part' === $template_type ) {
				$template->area = 'uncategorized'; // Default area for EPL template parts
			}

			return $template;
		}

		/**
		 * Get template title from slug
		 *
		 * @since 3.6.0
		 * @param string $slug Template slug.
		 * @return string
		 */
		private function get_template_title( $slug ) {
			$titles = array(
				'single-property'        => __( 'Single Property', 'easy-property-listings' ),
				'single-rental'          => __( 'Single Rental', 'easy-property-listings' ),
				'single-land'            => __( 'Single Land', 'easy-property-listings' ),
				'single-commercial'      => __( 'Single Commercial', 'easy-property-listings' ),
				'single-commercial_land' => __( 'Single Commercial Land', 'easy-property-listings' ),
				'single-rural'           => __( 'Single Rural', 'easy-property-listings' ),
				'single-business'        => __( 'Single Business', 'easy-property-listings' ),
				'archive-property'       => __( 'Property Archive', 'easy-property-listings' ),
				'archive-rental'         => __( 'Rental Archive', 'easy-property-listings' ),
				'archive-land'           => __( 'Land Archive', 'easy-property-listings' ),
				'archive-commercial'     => __( 'Commercial Archive', 'easy-property-listings' ),
				'archive-commercial_land'=> __( 'Commercial Land Archive', 'easy-property-listings' ),
				'archive-rural'          => __( 'Rural Archive', 'easy-property-listings' ),
				'archive-business'       => __( 'Business Archive', 'easy-property-listings' ),
				'taxonomy-location'      => __( 'Location Taxonomy', 'easy-property-listings' ),
				'taxonomy-tax_feature'   => __( 'Feature Taxonomy', 'easy-property-listings' ),
			);

			return isset( $titles[ $slug ] ) ? $titles[ $slug ] : ucwords( str_replace( array( '-', '_' ), ' ', $slug ) );
		}

		/**
		 * Check if template matches query
		 *
		 * @since 3.6.0
		 * @param WP_Block_Template $template Template object.
		 * @param array             $query Query args.
		 * @return bool
		 */
		private function template_matches_query( $template, $query ) {
			if ( isset( $query['slug__in'] ) && is_array( $query['slug__in'] ) ) {
				return in_array( $template->slug, $query['slug__in'], true );
			}

			if ( isset( $query['slug'] ) ) {
				return $template->slug === $query['slug'];
			}

			// Check if this is an EPL post type template
			$epl_post_types = epl_get_active_post_types();
			$template_slug_parts = explode( '-', $template->slug );
			
			if ( count( $template_slug_parts ) >= 2 ) {
				$post_type = end( $template_slug_parts );
				if ( in_array( $post_type, $epl_post_types, true ) ) {
					return true;
				}
			}

			return true;
		}

		/**
		 * Get block file template
		 *
		 * @since 3.6.0
		 * @param WP_Block_Template|null $template The found block template.
		 * @param string                 $id Template unique identifier.
		 * @param string                 $template_type Template type.
		 * @return WP_Block_Template|null
		 */
		public function get_block_file_template( $template, $id, $template_type ) {
			if ( ! in_array( $template_type, array( 'wp_template', 'wp_template_part' ), true ) ) {
				return $template;
			}

			$parts = explode( '//', $id );
			if ( count( $parts ) < 2 || 'epl-block-templates' !== $parts[0] ) {
				return $template;
			}

			$slug = $parts[1];
			
			// Determine the correct template directory
			if ( 'wp_template' === $template_type ) {
				$template_file = EPL_PATH_LIB . 'templates/block-templates/' . $slug . '.html';
			} else {
				$template_file = EPL_PATH_LIB . 'templates/block-template-parts/' . $slug . '.html';
			}
			
			if ( file_exists( $template_file ) ) {
				return $this->build_block_template_result_from_file(
					array(
						'slug' => $slug,
						'path' => $template_file,
					),
					$template_type
				);
			}

			return $template;
		}

		/**
		 * Add block template object
		 *
		 * @since 3.6.0
		 * @param WP_Block_Template|null $template The found block template.
		 * @param string                 $id Template unique identifier.
		 * @param string                 $template_type Template type.
		 * @return WP_Block_Template|null
		 */
		public function add_block_template_object( $template, $id, $template_type ) {
			return $this->get_block_file_template( $template, $id, $template_type );
		}

		/**
		 * Check if template is available
		 *
		 * @since 3.6.0
		 * @param string $template_name Template name.
		 * @return bool
		 */
		public function block_template_is_available( $template_name ) {
			if ( ! $template_name ) {
				return false;
			}

			$template_filename = $template_name . '.html';
			$template_path = EPL_PATH_LIB . 'templates/block-templates/' . $template_filename;
			return file_exists( $template_path );
		}

		/**
		 * Get EPL active post types
		 *
		 * @since 3.6.0
		 * @return array
		 */
		private function get_epl_post_types() {
			global $epl_settings;
			
			$post_types = array();
			
			if ( isset( $epl_settings['activate_post_types'] ) && is_array( $epl_settings['activate_post_types'] ) ) {
				$post_types = $epl_settings['activate_post_types'];
			}
			
			return $post_types;
		}

                /**
                 * Get template file.
                 */
                public function get_template_file( $template ) {
			

                        $template_file = EPL_PATH_LIB . 'templates/block-templates/' . $template . '.html';
			
			if ( file_exists( $template_file ) ) {
				return $template_file;
			}

			
		}
	}

endif;