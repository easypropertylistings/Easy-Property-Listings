<?php
/**
 * Elementor EPL Listing Document base class.
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
 * Shared behaviour for the EPL Single Listing and EPL Listing Card documents.
 *
 * Both document types are designed with the regular EPL Elementor widgets and
 * are rendered by EPL itself rather than through Elementor Pro's Theme Builder
 * conditions, so they opt out of the conditions UI and instead expose a
 * lightweight editor-canvas preview listing control.
 */
abstract class EPL_Elementor_Listing_Document extends \Elementor\Modules\Library\Documents\Container {

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

	/**
	 * Register document controls.
	 */
	protected function register_controls() {
		parent::register_controls();

		$this->start_controls_section(
			'epl_template_settings',
			array(
				'label' => esc_html__( 'EPL Template Settings', 'easy-property-listings' ),
				'tab'   => \Elementor\Controls_Manager::TAB_SETTINGS,
			)
		);

		$this->add_control(
			'epl_preview_listing',
			array(
				'label'       => esc_html__( 'Preview Listing ID', 'easy-property-listings' ),
				'type'        => \Elementor\Controls_Manager::NUMBER,
				'default'     => '',
				'description' => esc_html__( 'Optional. Show a specific listing on the editor canvas instead of an automatically selected one.', 'easy-property-listings' ),
				'render_type' => 'template',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Save the document, flagging it in plain post meta as an EPL listing
	 * template so EPL's front-end router can query for it without
	 * unserializing the full Elementor page-settings meta on every request.
	 *
	 * @param array $data Document data.
	 * @return bool|array
	 */
	public function save( $data ) {
		$result = parent::save( $data );

		if ( $result ) {
			update_post_meta( $this->post->ID, '_epl_elementor_template_context', 'listing' );
		}

		return $result;
	}
}
