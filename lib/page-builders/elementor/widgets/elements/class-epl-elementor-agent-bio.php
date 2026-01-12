<?php
/**
 * Elementor Agent Bio Widget
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
 * EPL_Elementor_Agent_Bio Class
 *
 * Displays the agent biography/description.
 * Reads from $epl_current_agent context.
 *
 * @since 3.6.0
 */
class EPL_Elementor_Agent_Bio extends \Elementor\Widget_Base {

	/**
	 * Get widget name.
	 */
	public function get_name() {
		return 'epl-agent-bio';
	}

	/**
	 * Get widget title.
	 */
	public function get_title() {
		return esc_html__( 'EPL Agent Bio', 'easy-property-listings' );
	}

	/**
	 * Get widget icon.
	 */
	public function get_icon() {
		return 'eicon-text-area';
	}

	/**
	 * Get widget categories.
	 */
	public function get_categories() {
		return array( 'epl-elements' );
	}

	/**
	 * Get widget keywords.
	 */
	public function get_keywords() {
		return array( 'agent', 'bio', 'biography', 'description', 'about', 'epl' );
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
			'word_limit',
			array(
				'label'       => esc_html__( 'Word Limit', 'easy-property-listings' ),
				'type'        => \Elementor\Controls_Manager::NUMBER,
				'min'         => 0,
				'max'         => 500,
				'default'     => 0,
				'description' => esc_html__( 'Set 0 for no limit', 'easy-property-listings' ),
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
					'{{WRAPPER}} .epl-agent-bio' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'typography',
				'selector' => '{{WRAPPER}} .epl-agent-bio',
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
					'justify' => array(
						'title' => esc_html__( 'Justify', 'easy-property-listings' ),
						'icon'  => 'eicon-text-align-justify',
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .epl-agent-bio' => 'text-align: {{VALUE}};',
				),
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
				echo '<div class="epl-elementor-placeholder">' . esc_html__( 'Agent Bio - No agent context.', 'easy-property-listings' ) . '</div>';
			}
			return;
		}

		$settings   = $this->get_settings_for_display();
		$bio        = $author->get_description_html();
		$word_limit = intval( $settings['word_limit'] );

		if ( empty( $bio ) ) {
			return;
		}

		// Apply word limit if set.
		if ( $word_limit > 0 ) {
			$bio = wp_trim_words( wp_strip_all_tags( $bio ), $word_limit, '...' );
		}

		echo '<div class="epl-agent-bio">' . wp_kses_post( $bio ) . '</div>';
	}

	/**
	 * Get the current agent from context or fallback.
	 *
	 * @return EPL_Author_Loader|null
	 */
	private function get_current_agent() {
		global $epl_current_agent, $property, $post;

		if ( ! empty( $epl_current_agent ) ) {
			return $epl_current_agent;
		}

		if ( ! $property && $post && is_epl_post( $post->post_type ) ) {
			$property = new EPL_Property_Meta( $post );
		}

		if ( ! $property ) {
			if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
				$property = EPL_Elementor::get_preview_property();
			}
		}

		if ( $property ) {
			return new EPL_Author_Loader( $property->post->post_author );
		}

		return null;
	}
}
