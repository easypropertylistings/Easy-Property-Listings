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
			add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_block_editor_assets' ) );
		}

                public static function instance() {
			if ( ! isset( self::$instance )  ) {
				self::$instance = new EPL_Register_Blocks();
			}
			return self::$instance;
		}

                public function archive_block( $attributes, $content ) {
                    ob_start();
                    epl_get_template_part( 'fse/archive-listing.php' );
                    return ob_get_clean();
                }

                public function single_block( $attributes, $content ) {
                    global $wp_query, $post;

                    ob_start();

                    if ( is_singular() && ! have_posts() ) {
                        $wp_query->setup_postdata( $post );
                    }

                    epl_get_template_part( 'fse/single-listing.php' );
                    return ob_get_clean();
                }

                public function archive_directory_block( $attributes, $content ) {
                    ob_start();
                    epl_get_template_part( 'fse/archive-directory.php' );
                    return ob_get_clean();
                }

                public function single_directory_block( $attributes, $content ) {
                    ob_start();
                    epl_get_template_part( 'fse/single-directory.php' );
                    return ob_get_clean();
                }

		/**
		 * Register block patterns
		 *
		 * @since 3.6.0
		 */
		public function enqueue_block_editor_assets() {
			wp_enqueue_script(
				'epl-archive-block',
				EPL_PLUGIN_URL . 'lib/blocks/epl-archive/index.js',
				array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-block-editor' ),
				EPL_PROPERTY_VER
			);
                        wp_enqueue_script(
				'epl-single-block',
				EPL_PLUGIN_URL . 'lib/blocks/epl-single/index.js',
				array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-block-editor' ),
				EPL_PROPERTY_VER
			);
                        wp_enqueue_script(
                                'epl-archive-directory-block',
                                EPL_PLUGIN_URL . 'lib/blocks/epl-archive-directory/index.js',
                                array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-block-editor' ),
                                EPL_PROPERTY_VER
                        );
                        wp_enqueue_script(
                                'epl-single-directory-block',
                                EPL_PLUGIN_URL . 'lib/blocks/epl-single-directory/index.js',
                                array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-block-editor' ),
                                EPL_PROPERTY_VER
                        );
		}

		public function register_blocks() {
			register_block_type( EPL_PATH_LIB . 'blocks/epl-archive', [
                                'render_callback' => [ $this, 'archive_block']
                        ] );
                        register_block_type( EPL_PATH_LIB . 'blocks/epl-single', [
                                'render_callback' => [ $this, 'single_block']
                        ] );
                        register_block_type( EPL_PATH_LIB . 'blocks/epl-archive-directory', [
                                'render_callback' => [ $this, 'archive_directory_block']
                        ] );
                        register_block_type( EPL_PATH_LIB . 'blocks/epl-single-directory', [
                                'render_callback' => [ $this, 'single_directory_block']
                        ] );
		}
	}

        EPL_Register_Blocks::instance();
endif;