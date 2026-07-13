<?php
/**
 * Register Block for EPL
 *
 * @package EPL
 * @subpackage Block
 * @since 3.6.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'EPL_Register_Blocks' ) ) :
	
	/**
	 * EPL Block Template Utils
	 * 
	 * @since 3.6.0
	 */
	class EPL_Register_Blocks {

                /**
		 * Instance
		 *
		 * @var EPL_Register_Blocks
		 * @since 1.0.0
		 */
		private static $instance;

		/**
		 * Constructor
		 *
		 * @since 3.6.0
		 */
		public function __construct() {
			add_action( 'init', array( $this, 'register_blocks' ) );
			add_action( 'enqueue_block_assets', array( $this, 'enqueue_editor_styles' ) );
		}

                public static function instance() {
			if ( ! isset( self::$instance )  ) {
				self::$instance = new EPL_Register_Blocks();
			}
			return self::$instance;
		}

		public function archive_block( $attributes, $content ) {
			if ( ! $this->is_editor_preview() && ! $this->is_listing_archive_context() ) {
				return '';
			}

			$post_type = isset( $attributes['postType'] ) ? sanitize_key( $attributes['postType'] ) : 'property';

			return $this->render_template( 'fse/archive-listing.php', $post_type, false );
		}

		public function single_block( $attributes, $content ) {
			if ( ! $this->is_editor_preview() && ! is_epl_post_single() ) {
				return '';
			}

			$post_type = isset( $attributes['postType'] ) ? sanitize_key( $attributes['postType'] ) : 'property';

			return $this->render_template( 'fse/single-listing.php', $post_type, true );
		}

		public function archive_directory_block( $attributes, $content ) {
			if ( ! $this->is_editor_preview() && ! is_post_type_archive( 'directory' ) ) {
				return '';
			}

			return $this->render_template( 'fse/archive-directory.php', 'directory', false );
		}

		public function single_directory_block( $attributes, $content ) {
			if ( ! $this->is_editor_preview() && ! is_singular( 'directory' ) ) {
				return '';
			}

			return $this->render_template( 'fse/single-directory.php', 'directory', true );
		}

		/**
		 * Whether a dynamic block is being rendered by the editor REST endpoint.
		 *
		 * @return bool
		 */
		private function is_editor_preview() {
			return defined( 'REST_REQUEST' ) && REST_REQUEST;
		}

		/**
		 * Whether the request uses an EPL listing archive-style query.
		 *
		 * @return bool
		 */
		private function is_listing_archive_context() {
			if ( function_exists( 'epl_is_search' ) && epl_is_search() ) {
				return true;
			}

			if ( is_epl_post_archive() ) {
				return true;
			}

			return is_tax( array( 'location', 'tax_feature', 'tax_business_listing' ) );
		}

		/**
		 * Render an EPL FSE template and preserve the main query globals.
		 *
		 * Editor requests receive representative published content because the Site
		 * Editor does not carry the frontend archive/singular query into REST calls.
		 *
		 * @param string $template  Relative EPL template path.
		 * @param string $post_type Preview post type.
		 * @param bool   $single    Whether to query a single preview item.
		 * @return string
		 */
		private function render_template( $template, $post_type, $single ) {
			global $wp_query, $post;

			$original_query = $wp_query;
			$original_post  = $post;
			$preview_query  = null;

			if ( $this->is_editor_preview() ) {
				$allowed_post_types = array_merge( EPL_Block_Template_Utils::get_supported_post_types(), array( 'directory' ) );
				if ( ! in_array( $post_type, $allowed_post_types, true ) || ! post_type_exists( $post_type ) ) {
					$post_type = 'property';
				}

				$preview_query = new WP_Query(
					array(
						'post_type'           => $post_type,
						'post_status'         => 'publish',
						'posts_per_page'      => $single ? 1 : 3,
						'ignore_sticky_posts' => true,
						'no_found_rows'       => true,
					)
				);

				$wp_query = $preview_query;
				$post     = $preview_query->post;
			}

			$query_state = $this->capture_query_state( $wp_query );

			ob_start();
			epl_get_template_part( $template );
			$output = ob_get_clean();

			$this->restore_query_state( $wp_query, $query_state );
			$post = $original_post;

			if ( $preview_query ) {
				$wp_query = $original_query;
				wp_reset_postdata();
			}

			return $output;
		}

		/**
		 * Capture mutable WP_Query loop state.
		 *
		 * @param WP_Query $query Query object.
		 * @return array
		 */
		private function capture_query_state( $query ) {
			return array(
				'current_post' => $query->current_post,
				'in_the_loop'  => $query->in_the_loop,
				'before_loop'  => isset( $query->before_loop ) ? $query->before_loop : null,
				'post'         => $query->post,
			);
		}

		/**
		 * Restore mutable WP_Query loop state.
		 *
		 * @param WP_Query $query Query object.
		 * @param array    $state Captured state.
		 * @return void
		 */
		private function restore_query_state( $query, $state ) {
			$query->current_post = $state['current_post'];
			$query->in_the_loop  = $state['in_the_loop'];
			$query->post         = $state['post'];

			if ( null !== $state['before_loop'] ) {
				$query->before_loop = $state['before_loop'];
			}
		}

		/**
		 * Load frontend EPL presentation styles inside the block-editor iframe.
		 *
		 * @return void
		 */
		public function enqueue_editor_styles() {
			if ( ! is_admin() ) {
				return;
			}

			if ( function_exists( 'epl_enqueue_front_styles' ) ) {
				epl_enqueue_front_styles( true );
			}

			if ( function_exists( 'epl_sd_enqueue_scripts' ) ) {
				epl_sd_enqueue_scripts();
			}
		}

		/**
		 * Register EPL dynamic blocks.
		 *
		 * @since 3.6.0
		 */
		public function register_blocks() {
			if ( ! function_exists( 'register_block_type_from_metadata' ) ) {
				return;
			}

			$blocks = array(
				'epl-archive'           => array( $this, 'archive_block' ),
				'epl-single'            => array( $this, 'single_block' ),
				'epl-archive-directory' => array( $this, 'archive_directory_block' ),
				'epl-single-directory'  => array( $this, 'single_directory_block' ),
			);

			foreach ( $blocks as $directory => $callback ) {
				$block = register_block_type(
					EPL_PATH_LIB . 'blocks/' . $directory,
					array( 'render_callback' => $callback )
				);

				if ( $block && function_exists( 'wp_set_script_translations' ) ) {
					$handles = isset( $block->editor_script_handles ) ? $block->editor_script_handles : array();
					if ( empty( $handles ) && ! empty( $block->editor_script ) ) {
						$handles = array( $block->editor_script );
					}

					foreach ( $handles as $handle ) {
						wp_set_script_translations( $handle, 'easy-property-listings', EPL_PLUGIN_PATH . 'languages' );
					}
				}
			}
		}
	}

        EPL_Register_Blocks::instance();
endif;
