<?php
/**
 * Elementor Agent Name Widget
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
 * EPL_Elementor_Agent_Name Class
 *
 * Displays the agent name. Reads from $epl_current_agent context.
 *
 * @since 3.6.0
 */
class EPL_Elementor_Agent_Name extends \Elementor\Widget_Base {
	use EPL_Elementor_Dynamic_Widget;

	/**
	 * Get widget name.
	 */
	public function get_name() {
		return 'epl-agent-name';
	}

	/**
	 * Get widget title.
	 */
	public function get_title() {
		return esc_html__( 'EPL Agent Name', 'easy-property-listings' );
	}

	/**
	 * Get widget icon.
	 */
	public function get_icon() {
		return 'eicon-heading';
	}

	/**
	 * Get widget categories.
	 */
	public function get_categories() {
		return array( 'epl-staff', 'epl-elements' );
	}

	/**
	 * Get widget keywords.
	 */
	public function get_keywords() {
		return array( 'agent', 'name', 'title', 'epl' );
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
			'html_tag',
			array(
				'label'   => esc_html__( 'HTML Tag', 'easy-property-listings' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => array(
					'h1'   => 'H1',
					'h2'   => 'H2',
					'h3'   => 'H3',
					'h4'   => 'H4',
					'h5'   => 'H5',
					'h6'   => 'H6',
					'div'  => 'div',
					'span' => 'span',
					'p'    => 'p',
				),
				'default' => 'h4',
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
				'default'      => 'yes',
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
					'{{WRAPPER}} .epl-agent-name'   => 'color: {{VALUE}};',
					'{{WRAPPER}} .epl-agent-name a' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'typography',
				'selector' => '{{WRAPPER}} .epl-agent-name',
			)
		);

		$this->add_responsive_control(
			'text_align',
			array(
				'label'     => esc_html__( 'Alignment', 'easy-property-listings' ),
				'type'      => \Elementor\Controls_Manager::CHOOSE,
				'options'   => array(
					'left'   => array(
						'title' => esc_html__( 'Left', 'easy-property-listings' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'easy-property-listings' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'  => array(
						'title' => esc_html__( 'Right', 'easy-property-listings' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .epl-agent-name' => 'text-align: {{VALUE}};',
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
				echo '<div class="epl-elementor-placeholder">' . esc_html__( 'Agent Name - No agent context.', 'easy-property-listings' ) . '</div>';
			}
			return;
		}

		$settings     = $this->get_settings_for_display();
		$tag          = \Elementor\Utils::validate_html_tag( $settings['html_tag'] );
		$link         = 'yes' === $settings['link_to_author'];
		$display_name = $author->get_author_name();
		$permalink    = apply_filters( 'epl_author_profile_link', get_author_posts_url( $author->author_id ), $author );

		echo '<' . esc_attr( $tag ) . ' class="epl-agent-name">';

		if ( $link ) {
			echo '<a href="' . esc_url( $permalink ) . '">' . esc_html( $display_name ) . '</a>';
		} else {
			echo esc_html( $display_name );
		}

		echo '</' . esc_attr( $tag ) . '>';
	}

	/**
	 * Get the current agent from context or fallback.
	 *
	 * @return EPL_Author_Loader|null
	 */
	private function get_current_agent() {
		global $epl_current_agent;

		if ( ! empty( $epl_current_agent ) ) {
			return $epl_current_agent;
		}

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
