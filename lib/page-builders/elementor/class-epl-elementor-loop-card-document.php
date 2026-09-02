<?php
/**
 * Elementor EPL Listing Card document.
 *
 * @package    EPL
 * @subpackage PageBuilders/Elementor
 * @since      3.7.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Dedicated saved-template document for a single listing "card".
 *
 * The free-Elementor equivalent of an Elementor Pro Loop Grid item. Not
 * applied automatically anywhere — a card only renders where a widget's own
 * "Listing Card" control explicitly picks it (EPL Archive Results, EPL
 * Listing Advanced, EPL Listings), via EPL_Elementor_Template_Router. There
 * is deliberately no site-wide "default card" — a card is designed to sit in
 * a grid column, and forcing it onto ungridded contexts (e.g. the [listing]
 * shortcode's plain loop) breaks its layout.
 */
class EPL_Elementor_Loop_Card_Document extends EPL_Elementor_Listing_Document {

	/** Document type slug. */
	const TYPE = 'epl-loop-card';

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
		return esc_html__( 'EPL Listing Card', 'easy-property-listings' );
	}

	/**
	 * Get plural title.
	 *
	 * @return string
	 */
	public static function get_plural_title() {
		return esc_html__( 'EPL Listing Cards', 'easy-property-listings' );
	}

	/**
	 * Register document controls.
	 */
	protected function register_controls() {
		parent::register_controls();

		$this->start_controls_section(
			'epl_card_settings',
			array(
				'label' => esc_html__( 'EPL Card Settings', 'easy-property-listings' ),
				'tab'   => \Elementor\Controls_Manager::TAB_SETTINGS,
			)
		);

		$this->add_control(
			'epl_card_preview_width',
			array(
				'label'       => esc_html__( 'Editing Preview Width (px)', 'easy-property-listings' ),
				'type'        => \Elementor\Controls_Manager::NUMBER,
				'min'         => 150,
				'max'         => 800,
				'step'        => 10,
				'default'     => 320,
				'description' => esc_html__( 'A card is normally seen sitting narrow inside a grid, not stretched across the whole screen. This just narrows the editing canvas to roughly that width so it\'s easier to design — it has no effect on the front end, where the card always fills whatever grid column it\'s placed in.', 'easy-property-listings' ),
			)
		);

		$this->end_controls_section();
	}
}
