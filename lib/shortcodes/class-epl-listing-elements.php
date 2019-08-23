<?php
/**
 * Listing Elements Shortcode class
 *
 * @package     EPL
 * @subpackage  Classes/Listing-Elements
 * @copyright   Copyright (c) 2019, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.3
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * EPL_Advanced_Shortcode_Listing Class
 *
 * @since 3.3.0
 */
class EPL_Listing_Elements {

	/**
	 * Construct the shortcode.
	 *
	 * @since 3.3.0
	 */
	public function __construct() {
		add_shortcode( 'listing_element', array( $this, 'listing_element' ) );
		add_shortcode( 'epl_listing_action', array( $this, 'epl_action' ) );
		add_shortcode( 'epl_listing_meta', array( $this, 'epl_meta' ) );
		add_shortcode( 'epl_listing_post', array( $this, 'post' ) );
		add_action( 'epl_suburb_profile', array( $this, 'epl_suburb_profile' ) );
		add_action( 'epl_listing_address', array( $this, 'epl_formatted_address' ) );
		add_shortcode( 'epl_listing_excerpt', array( $this, 'epl_the_excerpt' ) );

	}

	/**
	 * Output example code or value if in th loop
	 *
	 * @param array $atts Array of attributes.
	 *
	 * @return false|string|void
	 * @since 3.3
	 */
	public function listing_element( $atts ) {

		global $property;

		if ( ! isset( $atts['type'] ) ) {
			return;
		}

		if ( is_null( $property ) ) {

			$key_name = '';

			switch ( $atts['type'] ) {

				case 'action':
					$key_name = 'action_key';
					break;

				case 'meta':
					$key_name = 'meta_key';
					break;

				case 'post':
					$key_name = 'post_key';
					break;

			}

			ob_start();
			echo '[ ' . esc_html__( 'Listing', 'easy-property-listings' ) . ' ' . esc_attr( ucwords( $atts['type'] ) );

			if ( ! empty( $key_name ) ) {
				echo ': ' . esc_attr( ucwords( str_replace( '_', ' ', $atts[ $key_name ] ) ) );
			}
			echo ' ]';

			return ob_get_clean();
		}

		$return = '';

		switch ( $atts['type'] ) {

			case 'action':
				$return = $this->epl_action( $atts );
				break;

			case 'meta':
				$return = $this->epl_meta( $atts );
				break;

			case 'post':
				$return = $this->post( $atts );
				break;

			case 'suburb_profile':
				$return = $this->epl_suburb_profile();
				break;

			case 'formatted_address':
				$return = $this->epl_formatted_address();
				break;

			case 'excerpt':
				$return = $this->epl_the_excerpt();
				break;

		}

		return $return;
	}

	/**
	 * Output action
	 *
	 * @param array $atts Array of attributes for the action.
	 *
	 * @return false|string|void
	 * @since 3.3
	 */
	public function epl_action( $atts ) {
		if ( ! isset( $atts['action_key'] ) ) {
			return;
		}
		ob_start();
		do_action( $atts['action_key'] );
		return ob_get_clean();
	}

	/**
	 * Get meta
	 *
	 * @param array $atts Array of attributes for the meta field.
	 *
	 * @return string|void
	 * @since 3.3
	 */
	public function epl_meta( $atts ) {

		if ( ! isset( $atts['meta_key'] ) ) {
			return;
		}

		return get_property_meta( $atts['meta_key'] );
	}

	/**
	 * Get post
	 *
	 * @param array $atts Array of attributes for the post object.
	 *
	 * @return false|string
	 * @since 3.3
	 */
	public function post( $atts ) {

		global $property;

		$return = '';
		switch ( $atts['post_key'] ) {

			case 'permalink':
				$return = get_permalink( $property->post->ID );
				break;

			default:
				$return = isset( $property->post->{$atts['post_key']} ) ? $property->post->{$atts['post_key']} : '';
				break;

		}

		return $return;

	}

	/**
	 * Get suburb profile
	 *
	 * @since 3.3
	 */
	public function epl_suburb_profile() {

		global $property;
		echo esc_html( $property->get_suburb_profile() );
	}

	/**
	 * Get formatted listing address
	 *
	 * @since 3.3
	 */
	public function epl_formatted_address() {

		global $property;
		echo esc_html( $property->get_formatted_property_address() );
	}

	/**
	 * Get the excerpt
	 *
	 * @since 3.3
	 */
	public function epl_the_excerpt() {

		global $property;
		return esc_html( epl_get_the_excerpt() );
	}

}

new EPL_Listing_Elements();
