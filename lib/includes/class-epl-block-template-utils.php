<?php
/**
 * Block Template Utils for Easy Property Listings
 * 
 * Utility functions for managing block templates
 *
 * @package EPL
 * @subpackage Block Templates
 * @since 3.6.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'EPL_Block_Template_Utils' ) ) :
	
	/**
	 * EPL Block Template Utils
	 * 
	 * @since 3.6.0
	 */
	class EPL_Block_Template_Utils {

		/**
		 * Template directory path
		 *
		 * @since 3.6.0
		 * @var string
		 */
		const TEMPLATES_ROOT_DIR = 'templates';

		/**
		 * Block templates subdirectory
		 *
		 * @since 3.6.0
		 * @var string
		 */
		const BLOCK_TEMPLATES_DIR = 'block-templates';

		/**
		 * Block template parts subdirectory
		 *
		 * @since 3.6.0
		 * @var string
		 */
		const BLOCK_TEMPLATE_PARTS_DIR = 'block-template-parts';

		/**
		 * Get the path to templates directory
		 *
		 * @since 3.6.0
		 * @param string $template_type Optional. Type of template (block-templates or block-template-parts).
		 * @return string
		 */
		public static function get_templates_directory( $template_type = '' ) {
			$base_path = EPL_PATH_LIB . self::TEMPLATES_ROOT_DIR . '/';
			
			if ( empty( $template_type ) ) {
				return $base_path;
			}

			return $base_path . $template_type . '/';
		}

		/**
		 * Get template file path
		 *
		 * @since 3.6.0
		 * @param string $template_slug Template slug.
		 * @param string $template_type Template type (block-templates or block-template-parts).
		 * @return string|null
		 */
		public static function get_template_file_path( $template_slug, $template_type = 'block-templates' ) {
			$directory = self::get_templates_directory( $template_type );
			$file_path = $directory . $template_slug . '.html';
			
			if ( file_exists( $file_path ) ) {
				return $file_path;
			}
			
			return null;
		}

		/**
		 * Get all template files of a specific type
		 *
		 * @since 3.6.0
		 * @param string $template_type Template type (block-templates or block-template-parts).
		 * @return array
		 */
		public static function get_template_files( $template_type = 'block-templates' ) {
			$directory = self::get_templates_directory( $template_type );
			$template_files = array();

			if ( ! is_dir( $directory ) ) {
				return $template_files;
			}

			$files = glob( $directory . '*.html' );
			
			foreach ( $files as $file ) {
				$slug = basename( $file, '.html' );
				$template_files[] = array(
					'slug'     => $slug,
					'path'     => $file,
					'type'     => $template_type,
					'basename' => basename( $file ),
				);
			}

			return $template_files;
		}

		/**
		 * Check if a template file exists
		 *
		 * @since 3.6.0
		 * @param string $template_slug Template slug.
		 * @param string $template_type Template type.
		 * @return bool
		 */
		public static function template_file_exists( $template_slug, $template_type = 'block-templates' ) {
			return null !== self::get_template_file_path( $template_slug, $template_type );
		}

		/**
		 * Get template content
		 *
		 * @since 3.6.0
		 * @param string $template_slug Template slug.
		 * @param string $template_type Template type.
		 * @return string|false
		 */
		public static function get_template_content( $template_slug, $template_type = 'block-templates' ) {
			$file_path = self::get_template_file_path( $template_slug, $template_type );
			
			if ( null === $file_path ) {
				return false;
			}
			
			return file_get_contents( $file_path );
		}

		/**
		 * Get template hierarchy for EPL post types
		 *
		 * @since 3.6.0
		 * @param string $template_type Template type (single, archive, taxonomy).
		 * @param string $post_type Post type.
		 * @param string $taxonomy Taxonomy (for taxonomy templates).
		 * @return array
		 */
		public static function get_template_hierarchy( $template_type, $post_type = '', $taxonomy = '' ) {
			$hierarchy = array();

			switch ( $template_type ) {
				case 'single':
					if ( ! empty( $post_type ) ) {
						$hierarchy[] = 'single-' . $post_type;
					}
					$hierarchy[] = 'single-listing';
					break;

				case 'archive':
					if ( ! empty( $post_type ) ) {
						$hierarchy[] = 'archive-' . $post_type;
					}
					$hierarchy[] = 'archive-listing';
					break;

				case 'taxonomy':
					if ( ! empty( $taxonomy ) ) {
						$hierarchy[] = 'taxonomy-' . $taxonomy;
					}
					$hierarchy[] = 'taxonomy-listing';
					break;
			}

			return $hierarchy;
		}

		/**
		 * Get EPL post types that support templates
		 *
		 * @since 3.6.0
		 * @return array
		 */
		public static function get_supported_post_types() {
			return array(
				'property',
				'rental', 
				'land',
				'commercial',
				'commercial_land',
				'rural',
				'business',
			);
		}

		/**
		 * Get EPL taxonomies that support templates
		 *
		 * @since 3.6.0
		 * @return array
		 */
		public static function get_supported_taxonomies() {
			return array(
				'location',
				'tax_feature',
				'tax_business_listing',
			);
		}

		/**
		 * Build template object from file
		 *
		 * @since 3.6.0
		 * @param array  $template_file Template file data.
		 * @param string $template_type Template type (wp_template or wp_template_part).
		 * @return WP_Block_Template|null
		 */
		public static function build_template_object( $template_file, $template_type = 'wp_template' ) {
			if ( ! isset( $template_file['path'] ) || ! file_exists( $template_file['path'] ) ) {
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
			$template->title          = self::get_template_title( $template_file['slug'] );
			$template->description    = self::get_template_description( $template_file['slug'] );
			$template->status         = 'publish';
			$template->has_theme_file = true;
			$template->is_custom      = false;
			$template->author         = null;

			return $template;
		}

		/**
		 * Get human-readable title for template
		 *
		 * @since 3.6.0
		 * @param string $template_slug Template slug.
		 * @return string
		 */
		public static function get_template_title( $template_slug ) {
			$titles = array(
				// Single templates
				'single-property'        => __( 'Single Property', 'easy-property-listings' ),
				'single-rental'          => __( 'Single Rental', 'easy-property-listings' ),
				'single-land'            => __( 'Single Land', 'easy-property-listings' ),
				'single-commercial'      => __( 'Single Commercial Property', 'easy-property-listings' ),
				'single-commercial_land' => __( 'Single Commercial Land', 'easy-property-listings' ),
				'single-rural'           => __( 'Single Rural Property', 'easy-property-listings' ),
				'single-business'        => __( 'Single Business', 'easy-property-listings' ),
				'single-listing'         => __( 'Single Listing', 'easy-property-listings' ),

				// Archive templates
				'archive-property'       => __( 'Property Archive', 'easy-property-listings' ),
				'archive-rental'         => __( 'Rental Archive', 'easy-property-listings' ),
				'archive-land'           => __( 'Land Archive', 'easy-property-listings' ),
				'archive-commercial'     => __( 'Commercial Property Archive', 'easy-property-listings' ),
				'archive-commercial_land'=> __( 'Commercial Land Archive', 'easy-property-listings' ),
				'archive-rural'          => __( 'Rural Property Archive', 'easy-property-listings' ),
				'archive-business'       => __( 'Business Archive', 'easy-property-listings' ),
				'archive-listing'        => __( 'Listing Archive', 'easy-property-listings' ),

				// Taxonomy templates
				'taxonomy-location'      => __( 'Location Archive', 'easy-property-listings' ),
				'taxonomy-tax_feature'   => __( 'Feature Archive', 'easy-property-listings' ),
				'taxonomy-tax_business_listing' => __( 'Business Category Archive', 'easy-property-listings' ),

				// Template parts
				'property-card'          => __( 'Property Card', 'easy-property-listings' ),
				'property-details'       => __( 'Property Details', 'easy-property-listings' ),
				'property-gallery'       => __( 'Property Gallery', 'easy-property-listings' ),
				'property-contact-form'  => __( 'Contact Form', 'easy-property-listings' ),
				'search-filters'         => __( 'Search Filters', 'easy-property-listings' ),
			);

			if ( isset( $titles[ $template_slug ] ) ) {
				return $titles[ $template_slug ];
			}

			// Fallback: convert slug to title case
			return ucwords( str_replace( array( '-', '_' ), ' ', $template_slug ) );
		}

		/**
		 * Get template description
		 *
		 * @since 3.6.0
		 * @param string $template_slug Template slug.
		 * @return string
		 */
		public static function get_template_description( $template_slug ) {
			$descriptions = array(
				'single-property'        => __( 'Template for displaying a single property listing.', 'easy-property-listings' ),
				'single-rental'          => __( 'Template for displaying a single rental listing.', 'easy-property-listings' ),
				'single-land'            => __( 'Template for displaying a single land listing.', 'easy-property-listings' ),
				'single-commercial'      => __( 'Template for displaying a single commercial property.', 'easy-property-listings' ),
				'single-commercial_land' => __( 'Template for displaying a single commercial land listing.', 'easy-property-listings' ),
				'single-rural'           => __( 'Template for displaying a single rural property.', 'easy-property-listings' ),
				'single-business'        => __( 'Template for displaying a single business listing.', 'easy-property-listings' ),
				'archive-property'       => __( 'Template for displaying property archive pages.', 'easy-property-listings' ),
				'archive-rental'         => __( 'Template for displaying rental archive pages.', 'easy-property-listings' ),
				'archive-land'           => __( 'Template for displaying land archive pages.', 'easy-property-listings' ),
				'taxonomy-location'      => __( 'Template for displaying location-based listings.', 'easy-property-listings' ),
				'taxonomy-tax_feature'   => __( 'Template for displaying feature-based listings.', 'easy-property-listings' ),
			);

			return isset( $descriptions[ $template_slug ] ) ? $descriptions[ $template_slug ] : '';
		}

		/**
		 * Get template area for template part
		 *
		 * @since 3.6.0
		 * @param string $template_slug Template slug.
		 * @return string
		 */
		public static function get_template_area( $template_slug ) {
			$areas = array(
				'property-card'         => 'uncategorized',
				'property-details'      => 'uncategorized',
				'property-gallery'      => 'uncategorized',
				'property-contact-form' => 'uncategorized',
				'search-filters'        => 'uncategorized',
			);

			return isset( $areas[ $template_slug ] ) ? $areas[ $template_slug ] : 'uncategorized';
		}

		/**
		 * Check if current page requires EPL template
		 *
		 * @since 3.6.0
		 * @return bool
		 */
		public static function is_epl_template_context() {
			global $wp_query;

			if ( ! $wp_query ) {
				return false;
			}

			// Check for EPL post types
			$post_type = get_query_var( 'post_type' );
			if ( in_array( $post_type, self::get_supported_post_types(), true ) ) {
				return true;
			}

			// Check for EPL taxonomies
			if ( is_tax() ) {
				$taxonomy = get_queried_object()->taxonomy ?? '';
				if ( in_array( $taxonomy, self::get_supported_taxonomies(), true ) ) {
					return true;
				}
			}

			// Check for single EPL posts
			if ( is_singular() && in_array( get_post_type(), self::get_supported_post_types(), true ) ) {
				return true;
			}

			return false;
		}

		/**
		 * Get default template content
		 *
		 * @since 3.6.0
		 * @param string $template_slug Template slug.
		 * @return string
		 */
		public static function get_default_template_content( $template_slug ) {
			// Basic HTML structure for different template types
			if ( strpos( $template_slug, 'single-' ) === 0 ) {
				return '<!-- wp:template-part {"slug":"header","tagName":"header"} /-->

<!-- wp:group {"tagName":"main","style":{"spacing":{"margin":{"top":"0","bottom":"0"}}},"layout":{"type":"constrained"}} -->
<main class="wp-block-group" style="margin-top:0;margin-bottom:0">
	<!-- wp:post-title {"level":1} /-->
	
	<!-- wp:post-content /-->
</main>
<!-- /wp:group -->

<!-- wp:template-part {"slug":"footer","tagName":"footer"} /-->';
			}

			if ( strpos( $template_slug, 'archive-' ) === 0 ) {
				return '<!-- wp:template-part {"slug":"header","tagName":"header"} /-->

<!-- wp:group {"tagName":"main","style":{"spacing":{"margin":{"top":"0","bottom":"0"}}},"layout":{"type":"constrained"}} -->
<main class="wp-block-group" style="margin-top:0;margin-bottom:0">
	<!-- wp:query-title {"type":"archive"} /-->
	
	<!-- wp:query {"queryId":0,"query":{"perPage":10,"pages":0,"offset":0,"postType":"property","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":true}} -->
	<div class="wp-block-query">
		<!-- wp:post-template -->
			<!-- wp:post-title {"isLink":true} /-->
			<!-- wp:post-excerpt /-->
		<!-- /wp:post-template -->
		
		<!-- wp:query-pagination -->
			<!-- wp:query-pagination-previous /-->
			<!-- wp:query-pagination-numbers /-->
			<!-- wp:query-pagination-next /-->
		<!-- /wp:query-pagination -->
	</div>
	<!-- /wp:query -->
</main>
<!-- /wp:group -->

<!-- wp:template-part {"slug":"footer","tagName":"footer"} /-->';
			}

			return '';
		}
	}

endif;