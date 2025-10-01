<?php
/**
 * EPL Block Templates Controller
 *
 * Handles the registration and management of EPL block templates for FSE themes.
 *
 * @package Easy_Property_Listings
 * @subpackage Includes
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
			if ( ! EPL_Block_Template_Utils::supports_block_templates() ) {
				return;
			}

			add_filter( 'get_block_templates', array( $this, 'add_block_templates_with_epl_slug' ), 10, 3 );
			add_filter( 'pre_get_block_file_template', array( $this, 'get_block_file_template' ), 10, 3 );
			add_filter( 'get_block_template', array( $this, 'add_block_template_object' ), 10, 3 );
			add_action( 'init', array( $this, 'register_block_templates' ), 20 );
		}

		/**
		 * Register EPL block templates with WordPress
		 * This is the key method that registers templates so WordPress knows about them
		 *
		 * @since 3.6.0
		 */
		public function register_block_templates() {
			if ( ! EPL_Block_Template_Utils::supports_block_templates() ) {
				return;
			}

			$template_files = EPL_Block_Template_Utils::get_template_files( 'block-templates' );

			foreach ( $template_files as $template_file ) {
				$template_content = EPL_Block_Template_Utils::get_template_content( $template_file['slug'] );

				if ( false === $template_content ) {
					continue;
				}

				register_block_template(
					'epl-block-templates//' . $template_file['slug'],
					array(
						'title'       => EPL_Block_Template_Utils::get_template_title( $template_file['slug'] ),
						'description' => EPL_Block_Template_Utils::get_template_description( $template_file['slug'] ),
						'content'     => $template_content,
					)
				);
			}
		}

		/**
		 * Add database templates with EPL slug pattern
		 *
		 * @since 3.6.0
		 * @param WP_Block_Template[] $templates Array of block templates.
		 * @param array               $query Template query args.
		 * @param string              $template_type Template type.
		 * @return WP_Block_Template[]
		 */
		public function add_block_templates_with_epl_slug( $templates, $query, $template_type ) {
			$slugs = isset( $query['slug__in'] ) ? $query['slug__in'] : array();
                        
			if ( ! EPL_Block_Template_Utils::supports_block_templates( $template_type ) ) {
				return $templates;
			}

			// Get file templates
			$template_files = $this->get_block_templates( $slugs, $template_type );
			// Only add plugin templates that don't already exist
			$existing_template_slugs = array_column( $templates, 'slug' );
                        

			foreach ( $template_files as $template_file ) {
				if ( 'plugin' === $template_file->source &&
					 'epl-block-templates' === $template_file->theme &&
					 ! in_array( $template_file->slug, $existing_template_slugs, true ) ) {
					$templates[] = $template_file;
				}
			}
                        
			return $templates;
		}

		/**
		 * Get EPL block templates
		 *
		 * @since 3.6.0
		 * @param array $slugs Template slugs to retrieve.
		 * @param string $template_type Template type.
		 * @return WP_Block_Template[]
		 */
		private function get_block_templates( $slugs = array(), $template_type = 'wp_template' ) {
			$template_files = EPL_Block_Template_Utils::get_template_files( 'block-templates' );

			// Filter by slugs if specified
			if ( ! empty( $slugs ) && is_array( $slugs ) ) {
				$template_files = array_filter( $template_files, function( $template_file ) use ( $slugs ) {
					return in_array( $template_file['slug'], $slugs, true );
				});
			}

			$templates = array();

			foreach ( $template_files as $template_file ) {
				$template_content = EPL_Block_Template_Utils::get_template_content( $template_file['slug'] );

				if ( false === $template_content ) {
					continue;
				}

				$template = EPL_Block_Template_Utils::build_template_object( $template_file, $template_type );

				if ( $template ) {
					$templates[] = $template;
				}
			}

			return $templates;
		}

		/**
		 * Get block template for EPL post types
		 *
		 * @since 3.6.0
		 * @param WP_Block_Template|null $template Template object.
		 * @param string                 $id Template ID.
		 * @param string                 $template_type Template type.
		 * @return WP_Block_Template|null
		 */
		public function get_block_template_for_epl( $template, $id, $template_type ) {
			if ( 'wp_template' !== $template_type ) {
				return $template;
			}

			// Only handle our EPL templates
			$parts = explode( '//', $id );
			if ( count( $parts ) < 2 || 'epl-block-templates' !== $parts[0] ) {
				return $template;
			}

			$slug = $parts[1];

			// First check database templates
			$db_templates = EPL_Block_Template_Utils::get_block_templates_from_db( array( $slug ), $template_type );
			foreach ( $db_templates as $db_template ) {
				if ( $db_template->slug === $slug ) {
					return $db_template;
				}
			}

			// Then check file templates
			$file_templates = $this->get_block_templates( array( $slug ), $template_type );
			foreach ( $file_templates as $file_template ) {
				if ( $file_template->slug === $slug ) {
					return $file_template;
				}
			}

			return $template;
		}

		/**
		 * Get the EPL template slug for the current context
		 *
		 * @since 3.6.0
		 * @return string|null
		 */
		private function get_epl_template_slug_for_context() {
			if ( is_singular() ) {
				$post_type = get_post_type();
				if ( in_array( $post_type, array( 'property', 'rental', 'land', 'commercial', 'commercial_land', 'rural', 'business' ), true ) ) {
					return 'single-' . $post_type;
				}
			} elseif ( is_post_type_archive() ) {
				$post_type = get_query_var( 'post_type' );
				if ( is_array( $post_type ) ) {
					$post_type = reset( $post_type );
				}
				if ( in_array( $post_type, array( 'property', 'rental', 'land', 'commercial', 'commercial_land', 'rural', 'business' ), true ) ) {
					return 'archive-' . $post_type;
				}
			} elseif ( is_tax() ) {
				$taxonomy = get_queried_object()->taxonomy;
				if ( in_array( $taxonomy, array( 'location', 'tax_feature' ), true ) ) {
					return 'taxonomy-' . $taxonomy;
				}
			}

			return null;
		}

		/**
		 * Check if we're in an EPL context
		 *
		 * @since 3.6.0
		 * @return bool
		 */
		private function is_epl_context() {
			global $wp_query;

			if ( ! $wp_query ) {
				return false;
			}

			$is_epl = false;

			// Check for EPL post types
			$post_type = get_query_var( 'post_type' );
			if ( is_array( $post_type ) ) {
				$post_type = reset( $post_type );
			}

			$epl_post_types = array( 'property', 'rental', 'land', 'commercial', 'commercial_land', 'rural', 'business' );

			if ( in_array( $post_type, $epl_post_types, true ) ) {
				$is_epl = true;
			}

			// Check for single EPL posts
			if ( is_singular() && in_array( get_post_type(), $epl_post_types, true ) ) {
				$is_epl = true;
			}

			// Check for EPL taxonomies
			if ( is_tax() ) {
				$taxonomy = get_queried_object()->taxonomy ?? '';
				if ( in_array( $taxonomy, array( 'location', 'tax_feature' ), true ) ) {
					$is_epl = true;
				}
			}

			return $is_epl;
		}

		/**
		 * Get list of EPL template slugs
		 *
		 * @since 3.6.0
		 * @param string $template_type Template type.
		 * @return array
		 */
		private function get_epl_template_slugs( $template_type = 'wp_template' ) {
			$template_files = EPL_Block_Template_Utils::get_template_files( 'block-templates' );
			return array_map( function( $template ) {
				return $template['slug'];
			}, $template_files );
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

			$db_templates = EPL_Block_Template_Utils::get_block_templates_from_db( array( $parts[1] ), $template_type );
			if ( ! empty( $db_templates ) ) {
				return $db_templates[0];
			}

			$slug = $parts[1];
			$template_content = EPL_Block_Template_Utils::get_template_content( $slug, 'block-templates' );

			if ( false === $template_content ) {
				return $template;
			}

			return EPL_Block_Template_Utils::build_template_object(
				array(
					'slug' => $slug,
					'path' => EPL_Block_Template_Utils::get_template_file_path( $slug, 'block-templates' ),
				),
				$template_type
			);
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
		 * @return string|false
		 */
		public function get_epl_template_file( $template_name ) {
			$template_file = EPL()->plugin_path() . '/lib/templates/block-templates/' . $template_name . '.html';

			if ( file_exists( $template_file ) ) {
				return $template_file;
			}

		}

		/**
		 * Clear template cache when templates are saved/updated
		 *
		 * @since 3.6.0
		 */
		public function clear_template_cache() {
			wp_cache_delete( 'epl_block_templates', 'epl' );
		}

		/**
		 * Clear cache when templates are saved
		 *
		 * @since 3.6.0
		 * @param int $post_id Post ID.
		 */
		public function clear_template_cache_on_save( $post_id ) {
			$post = get_post( $post_id );

			if ( ! $post || ! in_array( $post->post_type, array( 'wp_template', 'wp_template_part' ), true ) ) {
				return;
			}

			// Check if this is an EPL template by looking at the post title
			if ( strpos( $post->post_title, 'epl-block-templates//' ) === 0 ) {
				$this->clear_template_cache();
			}
		}

		/**
		 * Clear cache when templates are updated
		 *
		 * @since 3.6.0
		 * @param int $post_id Post ID.
		 */
		public function clear_template_cache_on_update( $post_id ) {
			$post = get_post( $post_id );

			if ( ! $post || ! in_array( $post->post_type, array( 'wp_template', 'wp_template_part' ), true ) ) {
				return;
			}

			// Check if this is an EPL template by looking at the post title
			if ( strpos( $post->post_title, 'epl-block-templates//' ) === 0 ) {
				$this->clear_template_cache();
			}
		}

	}

endif;