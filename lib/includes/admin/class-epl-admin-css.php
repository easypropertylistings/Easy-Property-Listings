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
		public function render_single_css_name( $key ) {
			
			$css_property_key = str_replace( 'theme_setup_single_css_property_', '', $key );
			$css_property_key = str_replace( '_', '-', $css_property_key );
			
			$value = epl_get_option( $key );
			
		
			return $css_property_key . ': ' . $value . ';';
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
				.epl-single-listing .epl-single-default,
				#primary.epl-single-default {
					border: 3px solid red;
					<?php echo esc_attr( $this->render_single_css_name( 'theme_setup_single_css_property_max_width' ) ); ?>
					<?php echo esc_attr( $this->render_single_css_name( 'theme_setup_single_css_property_width' ) ); ?>
					<?php echo esc_attr( $this->render_single_css_name( 'theme_setup_single_css_property_margin' ) ); ?>
					<?php echo esc_attr( $this->render_single_css_name( 'theme_setup_single_css_property_padding' ) ); ?>
				}
				
				/*
				.epl-single-listing .widget-area,
				#secondary {
					border: 3px solid orange;
					margin: auto;
					max-width: 400px;
				}
				*/
				

				
			</style>
			<?php

			return ob_get_clean();
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

			$max_width = $this->get_option( 'theme_setup_archive_max_width' );

			ob_start();
			$this->common();
			?>
			<style>
				.epl-archive-default,
				#primary.epl-archive-default {
					margin: 0 auto;
					margin-top: 2em;
					outline: 3px solid pink;
					max-width: <?php echo esc_attr( $max_width ); ?>;
				}
			</style>
			<?php

			return ob_get_clean();
		}

		

		/**
		 * Common CSS
		 *
		 * @since 3.6.0
		 */
		public function common() {
			?>

			<style>
				body .epl-property-blog {
					-webkit-transition: none;
					-moz-transition: none;
					-o-transition: none;
					transition: none;
				}
				body .epl-property-blog.epl-listing-grid-view {
					-webkit-transition: none;
					-moz-transition: none;
					-o-transition: none;
					transition: none;
				}
				.epl_slider_container-loading {
					opacity: 0;
				}
			</style>
			<?php
		}
	}
endif;
