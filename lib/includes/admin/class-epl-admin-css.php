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
		 * @param EPL_Slider_Config $view The object.
		 *
		 * @since 3.6.0
		 */
		public function __construct( EPL_Slider_Config $view ) {
			$this->slider = $view;
		}

		/**
		 * Ensure only one instance of this class is running
		 *
		 * @param EPL_Slider_Config $view The object.
		 *
		 * @since 3.6.0
		 */
		public static function get_instance( $view ) {

			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof EPL_Admin_CSS ) ) {
				self::$instance = new EPL_Admin_CSS( $view );
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
			return $this->view->epl_get_option( $key );
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

			$max_width = $this->get_option( 'theme_setup_single_max_width' );

			ob_start();
			$this->common();
			?>
			<style>
				.epl-single-default {
					max-width: <?php echo esc_attr( $max_width ); ?>
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
