<?php
/**
 * Elementor Property Excerpt Widget
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
 * EPL_Elementor_Property_Excerpt Class
 *
 * @since 3.6.0
 */
class EPL_Elementor_Property_Excerpt extends \Elementor\Widget_Base {
	use EPL_Elementor_Dynamic_Widget;

	/**
	 * Get widget name.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'epl-property-excerpt';
	}

	/**
	 * Get widget title.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'EPL Property Excerpt', 'easy-property-listings' );
	}

	/**
	 * Get widget icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-post-excerpt';
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
		return array( 'property', 'excerpt', 'summary', 'epl' );
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
			'excerpt_length',
			array(
				'label'   => esc_html__( 'Excerpt Length (words)', 'easy-property-listings' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'min'     => 5,
				'max'     => 200,
				'default' => 20,
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
					'{{WRAPPER}} .epl-property-excerpt' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'typography',
				'selector' => '{{WRAPPER}} .epl-property-excerpt',
			)
		);

		$this->add_responsive_control(
			'text_align',
			array(
				'label'     => esc_html__( 'Alignment', 'easy-property-listings' ),
				'type'      => \Elementor\Controls_Manager::CHOOSE,
				'options'   => array(
					'left'    => array(
						'title' => esc_html__( 'Left', 'easy-property-listings' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center'  => array(
						'title' => esc_html__( 'Center', 'easy-property-listings' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'   => array(
						'title' => esc_html__( 'Right', 'easy-property-listings' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .epl-property-excerpt' => 'text-align: {{VALUE}};',
				),
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
				echo '<div class="epl-elementor-placeholder">' . esc_html__( 'Property Excerpt - No listings found.', 'easy-property-listings' ) . '</div>';
			}
			EPL_Elementor::restore_listing_context();
			return;
		}

		$settings = $this->get_settings_for_display();
		$length   = absint( $settings['excerpt_length'] );

		echo '<div class="epl-property-excerpt">';
		echo esc_html( wp_trim_words( get_the_excerpt( $property->post->ID ), $length ) );
		echo '</div>';

		EPL_Elementor::restore_listing_context();
	}
}
