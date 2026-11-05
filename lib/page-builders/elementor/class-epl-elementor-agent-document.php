<?php
/**
 * Elementor EPL Agent Template document.
 *
 * @package    EPL
 * @subpackage PageBuilders/Elementor
 * @since      3.6.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Dedicated saved-template document for Agents Loop items.
 *
 * Agent designs deliberately use a normal library-document lifecycle instead
 * of Elementor Pro's Loop Item lifecycle. They are rendered inside a property
 * Loop Item and must not own an independent query, preview provider, Theme
 * Builder conditions, or Loop CSS selector rewriting.
 */
class EPL_Elementor_Agent_Document extends \Elementor\Modules\Library\Documents\Container {

	/** Document type slug. */
	const TYPE = 'epl-agent';

	/**
	 * Get document name.
	 *
	 * @return string
	 */
	public function get_name() {
		return self::TYPE;
	}

	/**
	 * Get document type.
	 *
	 * @return string
	 */
	public static function get_type() {
		return self::TYPE;
	}

	/**
	 * Get singular title.
	 *
	 * @return string
	 */
	public static function get_title() {
		return esc_html__( 'EPL Agent Template', 'easy-property-listings' );
	}

	/**
	 * Get plural title.
	 *
	 * @return string
	 */
	public static function get_plural_title() {
		return esc_html__( 'EPL Agent Templates', 'easy-property-listings' );
	}

	/**
	 * Document properties.
	 *
	 * @return array
	 */
	public static function get_properties() {
		$properties                       = parent::get_properties();
		$properties['support_conditions'] = false;
		return $properties;
	}
}
