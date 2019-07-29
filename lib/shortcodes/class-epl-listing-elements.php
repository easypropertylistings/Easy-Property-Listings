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

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class EPL_Listing_Elements {

	function __construct() {
		add_shortcode('listing_element',array($this,'listing_element') );
		add_shortcode('epl_listing_action',array($this,'epl_action') );
		add_shortcode('epl_listing_meta',array($this,'epl_meta') );
		add_shortcode('epl_listing_post',array($this,'post') );
		add_action('epl_suburb_profile',array($this,'epl_suburb_profile') );
		add_action('epl_listing_address',array($this,'epl_formatted_address') );
		add_shortcode('epl_listing_excerpt',array($this,'epl_the_excerpt') );

	}

	/**
	 * Output example code or value if in th loop
	 *
	 * @since 3.3
	 */
	function listing_element($atts) {

		global $property;

		if( !isset($atts['type']) )
			return;

		if( is_null($property) ) {

			$key_name = '';

			switch( $atts['type'] ) {

				case 'action' :
					$key_name = 'action_key';
				break;

				case 'meta' :
					$key_name = 'meta_key';
				break;

				case 'post' :
					$key_name = 'post_key';
				break;

			}

			ob_start();
			echo '[ '.__('Listing Element','easy-property-listings').' '.ucwords($atts['type']);

			if( $key_name != ''){
				echo ': '.ucwords(str_replace('_',' ',$atts[$key_name]));
			}
			echo ' ]';

			return ob_get_clean();
		}

		switch( $atts['type'] ) {

			case 'action' :
				return $this->epl_action($atts);
			break;

			case 'meta' :
				return $this->epl_meta($atts);
			break;

			case 'post' :
				return $this->post($atts);
			break;

			case 'suburb_profile' :
				return $this->epl_suburb_profile($atts);
			break;

			case 'formatted_address' :
				return $this->epl_formatted_address($atts);
			break;

			case 'excerpt' :
				return $this->epl_the_excerpt($atts);
			break;

		}
	}

	/**
	 * Output action
	 *
	 * @since 3.3
	 */
	function epl_action($atts) {
		if( !isset($atts['action_key']) )
			return;
		ob_start();
		do_action($atts['action_key']);
		return ob_get_clean();
	}

	/**
	 * Get meta
	 *
	 * @since 3.3
	 */
	function epl_meta($atts) {

		if( !isset($atts['meta_key']) )
			return;

		return get_property_meta($atts['meta_key']);
	}

	/**
	 * Get post
	 *
	 * @since 3.3
	 */
	function post($atts) {

		global $property;
		switch($atts['post_key']) {

			case 'permalink' :
				return get_permalink($property->post->ID);
			break;

			default :
				return isset($property->post->{$atts['post_key']}) ? $property->post->{$atts['post_key']} : '';
			break;

		}

	}

	/**
	 * Get suburb profile
	 *
	 * @since 3.3
	 */
	function epl_suburb_profile(){

		global $property;
		echo $property->get_suburb_profile();
	}

	/**
	 * Get formatted listing address
	 *
	 * @since 3.3
	 */
	function epl_formatted_address(){

		global $property;
		echo  $property->get_formatted_property_address();
	}

	/**
	 * Get the excerpt
	 *
	 * @since 3.3
	 */
	function epl_the_excerpt(){

		global $property;
		return epl_get_the_excerpt();
	}

}

new EPL_Listing_Elements();
