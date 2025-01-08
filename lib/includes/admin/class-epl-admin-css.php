<?php
/**
 * EPL Author Class. Allows for overriding.
 *
 * @package     EPL
 * @subpackage  Classes/Admin/CSS
 * @copyright   Copyright (c) 2022, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.6
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * EPL_Admin_CSS Class
 *
 * @since 3.6.0
 */
if ( ! class_exists( 'EPL_Admin_CSS' ) ) :

	/**
	 * EPL_Admin_CSS Class
	 *
	 * @since 3.6.0
	 */
	class EPL_Admin_CSS {

		/**
		 * Slider
		 *
		 * @var $view
		 * @since 3.6.0
		 */
		public $view;

		/**
		 * Instance
		 *
		 * @var $instance
		 * @since 3.6.0
		 */
		private static $instance;

		/**
		 * Constructor.
		 *
		 * @since 3.6.0
		 */
		public function __construct() {
		}

		/**
		 * Ensure only one instance of this class is running
		 *
		 * @return EPL_Admin_CSS
		 * @since 3.6.0
		 */
		public static function get_instance() {

			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof EPL_Admin_CSS ) ) {
				self::$instance = new EPL_Admin_CSS();
			}

			return self::$instance;

		}

		/**
		 * Get Slider Option
		 *
		 * @param string $key The key.
		 * @since 3.6.0
		 */
		public function get_option( $key ) {
			return epl_get_option( $key );
		}

		/**
		 * Single Theme Setup CSS
		 *
		 * @since 3.6.0
		 */
		public function render_css_property( $prefix, $key, $default = 'initial' ) {
			
			if ( $prefix && $key ) {
				$css_property_key = str_replace( $prefix, '', $key );
				$css_property_key = str_replace( '_', '-', $css_property_key );
				
				$value = epl_get_option( $prefix . $key, $default );
				
				
				return $css_property_key . ': ' . $value . ';';
			} else {
				return 'initial';
			}
			
			
		}
		
		/**
		 * Common CSS
		 *
		 * @since 3.6.0
		 */
		public function common() {
			?>
		
			<style>
				
				.epl-container {
					display: grid;
					grid-template-columns: 1fr 360px;
				}
				
				.epl-post-type-archive .ast-container {
					display: block;
				}
				
				.epl-single-listing .ast-container {
					display: block;
				}

			</style>
			<?php
		}
		
		/**
		 * Archive Theme Setup CSS
		 *
		 * @since 3.6.0
		 */
		public function archive_css() {
			global $post, $property, $epl_settings;
		
			if ( is_null( $post ) ) {
				return null;
			}
		
			ob_start();
			$this->common();
			?>
			<style>
				
				.epl-container--archive {
					<?php echo esc_attr( $this->render_css_property( 'theme_setup_archive_css_property_', 'margin', 0 ) ); ?>
					<?php echo esc_attr( $this->render_css_property( 'theme_setup_archive_css_property_', 'padding', 0 ) ); ?>
					<?php echo esc_attr( $this->render_css_property( 'theme_setup_archive_css_property_', 'display', 'grid' ) ); ?>
					<?php echo esc_attr( $this->render_css_property( 'theme_setup_archive_css_property_', 'grid_template_columns', '1fr 360px' ) ); ?>
					<?php echo esc_attr( $this->render_css_property( 'theme_setup_archive_css_property_', 'gap', '2em' ) ); ?>
					<?php echo esc_attr( $this->render_css_property( 'theme_setup_archive_css_property_', 'max_width', 'initial' ) ); ?>
					
					
				}
				
				
				
				.epl-container .epl-archive-default,
				.epl-container #epl-listing-container-archive.epl-archive-default {
					
				

					margin: 0 auto;
					
					
					
					outline: 3px solid pink;
				}
				
				.epl-container .epl-sidebar--archive {
					<?php echo esc_attr( $this->render_css_property( 'theme_setup_archive_sidebar_css_property_', 'max_width' ) ); ?>
					<?php echo esc_attr( $this->render_css_property( 'theme_setup_archive_sidebar_css_property_', 'width' ) ); ?>
					<?php echo esc_attr( $this->render_css_property( 'theme_setup_archive_sidebar_css_property_', 'margin' ) ); ?>
					<?php echo esc_attr( $this->render_css_property( 'theme_setup_archive_sidebar_css_property_', 'padding' ) ); ?>
					
					
					border: 3px solid orange;
				
				}
			</style>
			<?php
		
			return ob_get_clean();
		}

		/**
		 * Single Theme Setup CSS
		 *
		 * @since 3.6.0
		 */
		public function single_css() {
			global $post, $property, $epl_settings;

			if ( is_null( $post ) ) {
				return null;
			}

			ob_start();
			$this->common();
			?>
			<style>
				.epl-container .epl-single-default,
				.epl-container #epl-listing-container-single.epl-single-default {
					<?php echo esc_attr( $this->render_css_property( 'theme_setup_single_css_property_', 'max_width' ) ); ?>
					<?php echo esc_attr( $this->render_css_property( 'theme_setup_single_css_property_', 'width' ) ); ?>
					<?php echo esc_attr( $this->render_css_property( 'theme_setup_single_css_property_', 'margin' ) ); ?>
					<?php echo esc_attr( $this->render_css_property( 'theme_setup_single_css_property_', 'padding' ) ); ?>
					
					border: 3px solid turquoise;
				}

				.epl-container .epl-sidebar--single {
					<?php echo esc_attr( $this->render_css_property( 'theme_setup_single_sidebar_css_property_', 'max_width' ) ); ?>
					<?php echo esc_attr( $this->render_css_property( 'theme_setup_single_sidebar_css_property_', 'width' ) ); ?>
					<?php echo esc_attr( $this->render_css_property( 'theme_setup_single_sidebar_css_property_', 'margin' ) ); ?>
					<?php echo esc_attr( $this->render_css_property( 'theme_setup_single_sidebar_css_property_', 'padding' ) ); ?>
					
					
					border: 3px solid orange;

				}
				
				

				
			</style>
			<?php

			return ob_get_clean();
		}

               

		

		
	}
endif;
