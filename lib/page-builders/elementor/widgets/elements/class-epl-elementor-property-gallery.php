<?php
/**
 * Elementor Property Gallery Widget
 *
 * @package     EPL
 * @subpackage  PageBuilders/Elementor/Widgets/Elements
 * @since       3.6.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * EPL_Elementor_Property_Gallery Class
 *
 * @since 3.6.0
 */
class EPL_Elementor_Property_Gallery extends \Elementor\Widget_Base {

	/**
	 * Get widget name.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'epl-property-gallery';
	}

	/**
	 * Get widget title.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'EPL Property Gallery', 'easy-property-listings' );
	}

	/**
	 * Get widget icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-gallery-grid';
	}

	/**
	 * Get widget categories.
	 *
	 * @return array
	 */
	public function get_categories() {
		return array( 'epl-elements' );
	}

	/**
	 * Get widget keywords.
	 *
	 * @return array
	 */
	public function get_keywords() {
		return array( 'property', 'gallery', 'image', 'epl' );
	}

	/**
	 * Register widget controls.
	 */
	protected function register_controls() {
		// Style Section.
		$this->start_controls_section(
			'section_style',
			array(
				'label' => esc_html__( 'Style', 'easy-property-listings' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'margin',
			array(
				'label'      => esc_html__( 'Margin', 'easy-property-listings' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .epl-property-gallery' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render widget output.
	 */
	protected function render() {
		global $property, $post;

		// Initialize property object from current post if not available.
		if ( ! $property && $post && is_epl_post( $post->post_type ) ) {
			$property = new EPL_Property_Meta( $post );
		}

		// In editor mode, try to get a preview property if none available.
		$is_editor = \Elementor\Plugin::$instance->editor->is_edit_mode();

		if ( ! $property && $is_editor ) {
			$property = EPL_Elementor::get_preview_property();
		}

		if ( ! $property ) {
			if ( $is_editor ) {
				echo '<div class="epl-elementor-placeholder">' . esc_html__( 'Property Gallery - No listings found.', 'easy-property-listings' ) . '</div>';
			}
			return;
		}

		echo '<div class="epl-property-gallery">';
		do_action( 'epl_property_gallery' );
		echo '</div>';
	}
}
