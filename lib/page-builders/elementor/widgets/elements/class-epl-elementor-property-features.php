<?php
/**
 * Elementor Property Features Widget
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
 * EPL_Elementor_Property_Features Class
 *
 * @since 3.6.0
 */
class EPL_Elementor_Property_Features extends \Elementor\Widget_Base {
	use EPL_Elementor_Dynamic_Widget;

	/**
	 * Get widget name.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'epl-property-features';
	}

	/**
	 * Get widget title.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Feature List', 'easy-property-listings' );
	}

	/**
	 * Get widget icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-bullet-list';
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
		return array( 'property', 'features', 'amenities', 'epl' );
	}

	/**
	 * Register widget controls.
	 */
	protected function register_controls() {

		$this->start_controls_section(
			'section_content',
			array(
				'label' => esc_html__( 'Content', 'easy-property-listings' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'show_title',
			array(
				'label'        => esc_html__( 'Show Title', 'easy-property-listings' ),
				'description'  => esc_html__( 'The features list already includes its own heading; enable this only for an additional custom title.', 'easy-property-listings' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'easy-property-listings' ),
				'label_off'    => esc_html__( 'No', 'easy-property-listings' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_control(
			'title_text',
			array(
				'label'     => esc_html__( 'Title', 'easy-property-listings' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'default'   => esc_html__( 'Features', 'easy-property-listings' ),
				'condition' => array(
					'show_title' => 'yes',
				),
			)
		);

		$this->end_controls_section();

		// Style Section.
		$this->start_controls_section(
			'section_style',
			array(
				'label' => esc_html__( 'Style', 'easy-property-listings' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'text_color',
			array(
				'label'     => esc_html__( 'Color', 'easy-property-listings' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .epl-property-features' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'typography',
				'selector' => '{{WRAPPER}} .epl-property-features',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render widget output.
	 */
	protected function render() {
		$property  = EPL_Elementor::setup_listing_context();
		$is_editor = \Elementor\Plugin::$instance->editor->is_edit_mode();

		if ( ! $property ) {
			if ( $is_editor ) {
				echo '<div class="epl-elementor-placeholder">' . esc_html__( 'Property Features - No listings found.', 'easy-property-listings' ) . '</div>';
			}
			EPL_Elementor::restore_listing_context();
			return;
		}

		$settings = $this->get_settings_for_display();

		echo '<div class="epl-property-features">';

		if ( 'yes' === $settings['show_title'] && ! empty( $settings['title_text'] ) ) {
			echo '<h5 class="epl-features-title">' . esc_html( $settings['title_text'] ) . '</h5>';
		}

		do_action( 'epl_property_features' );

		echo '</div>';

		EPL_Elementor::restore_listing_context();
	}
}
