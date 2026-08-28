<?php
/**
 * Elementor Agent Photo Widget
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
 * EPL_Elementor_Agent_Photo Class
 *
 * Displays the agent photo. Reads from $epl_current_agent context.
 *
 * @since 3.6.0
 */
class EPL_Elementor_Agent_Photo extends \Elementor\Widget_Base {
	use EPL_Elementor_Dynamic_Widget;

	/**
	 * Get widget name.
	 */
	public function get_name() {
		return 'epl-agent-photo';
	}

	/**
	 * Get widget title.
	 */
	public function get_title() {
		return esc_html__( 'Photo', 'easy-property-listings' );
	}

	/**
	 * Get widget icon.
	 */
	public function get_icon() {
		return 'eicon-image';
	}

	/**
	 * Get widget categories.
	 */
	public function get_categories() {
		return array( 'epl-staff', 'epl-author' );
	}

	/**
	 * Get widget keywords.
	 */
	public function get_keywords() {
		return array( 'agent', 'photo', 'avatar', 'image', 'epl' );
	}

	/**
	 * Register widget controls.
	 */
	protected function register_controls() {

		$this->start_controls_section(
			'section_content',
			array(
				'label' => esc_html__( 'Photo', 'easy-property-listings' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_responsive_control(
			'photo_size',
			array(
				'label'      => esc_html__( 'Size', 'easy-property-listings' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 50,
						'max' => 400,
					),
				),
				'default'    => array(
					'size' => 150,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .epl-agent-photo img' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'link_to_author',
			array(
				'label'        => esc_html__( 'Link to Author Page', 'easy-property-listings' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'easy-property-listings' ),
				'label_off'    => esc_html__( 'No', 'easy-property-listings' ),
				'return_value' => 'yes',
				'default'      => '',
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

		$this->add_responsive_control(
			'border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'easy-property-listings' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'default'    => array(
					'top'    => '50',
					'right'  => '50',
					'bottom' => '50',
					'left'   => '50',
					'unit'   => '%',
				),
				'selectors'  => array(
					'{{WRAPPER}} .epl-agent-photo img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			array(
				'name'     => 'photo_border',
				'selector' => '{{WRAPPER}} .epl-agent-photo img',
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'photo_shadow',
				'selector' => '{{WRAPPER}} .epl-agent-photo img',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render widget output.
	 */
	protected function render() {
		$author = $this->get_current_agent();

		if ( ! $author ) {
			if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
				echo '<div class="epl-elementor-placeholder">' . esc_html__( 'Agent Photo - No agent context.', 'easy-property-listings' ) . '</div>';
			}
			return;
		}

		$settings   = $this->get_settings_for_display();
		$size       = isset( $settings['photo_size']['size'] ) ? intval( $settings['photo_size']['size'] ) : 150;
		$link       = 'yes' === $settings['link_to_author'];
		$permalink  = apply_filters( 'epl_author_profile_link', get_author_posts_url( $author->author_id ), $author );

		echo '<div class="epl-agent-photo">';

		if ( $link ) {
			echo '<a href="' . esc_url( $permalink ) . '">';
		}

		echo wp_kses_post( EPL_Elementor::get_agent_image( $author, $size ) );

		if ( $link ) {
			echo '</a>';
		}

		echo '</div>';
	}

	/**
	 * Get the current agent from context or fallback.
	 *
	 * @return EPL_Author_Loader|null
	 */
	private function get_current_agent() {
		global $epl_current_agent;

		// First check if we're in an agents loop.
		if ( ! empty( $epl_current_agent ) ) {
			return $epl_current_agent;
		}

		// Fallback to primary author.
		$property = EPL_Elementor::setup_listing_context();

		if ( $property ) {
			$author = EPL_Elementor::get_listing_agent( $property );
			EPL_Elementor::restore_listing_context();
			return $author;
		}

		EPL_Elementor::restore_listing_context();
		return null;
	}
}
