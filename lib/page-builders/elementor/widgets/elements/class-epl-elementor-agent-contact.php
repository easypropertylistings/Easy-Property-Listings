<?php
/**
 * Elementor Agent Contact Widget
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
 * EPL_Elementor_Agent_Contact Class
 *
 * Displays agent contact info (mobile, office phone, email).
 * Reads from $epl_current_agent context.
 *
 * @since 3.6.0
 */
class EPL_Elementor_Agent_Contact extends \Elementor\Widget_Base {
	use EPL_Elementor_Dynamic_Widget;

	/**
	 * Get widget name.
	 */
	public function get_name() {
		return 'epl-agent-contact';
	}

	/**
	 * Get widget title.
	 */
	public function get_title() {
		return esc_html__( 'EPL Agent Contact', 'easy-property-listings' );
	}

	/**
	 * Get widget icon.
	 */
	public function get_icon() {
		return 'eicon-tel-field';
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
		return array( 'agent', 'contact', 'phone', 'mobile', 'email', 'epl' );
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
			'show_mobile',
			array(
				'label'        => esc_html__( 'Show Mobile', 'easy-property-listings' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'easy-property-listings' ),
				'label_off'    => esc_html__( 'No', 'easy-property-listings' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'show_office_phone',
			array(
				'label'        => esc_html__( 'Show Office Phone', 'easy-property-listings' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'easy-property-listings' ),
				'label_off'    => esc_html__( 'No', 'easy-property-listings' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_control(
			'show_email',
			array(
				'label'        => esc_html__( 'Show Email', 'easy-property-listings' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'easy-property-listings' ),
				'label_off'    => esc_html__( 'No', 'easy-property-listings' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'show_labels',
			array(
				'label'        => esc_html__( 'Show Labels', 'easy-property-listings' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'easy-property-listings' ),
				'label_off'    => esc_html__( 'No', 'easy-property-listings' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'show_icons',
			array(
				'label'        => esc_html__( 'Show Icons', 'easy-property-listings' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'easy-property-listings' ),
				'label_off'    => esc_html__( 'No', 'easy-property-listings' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_control(
			'layout',
			array(
				'label'   => esc_html__( 'Layout', 'easy-property-listings' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => array(
					'stacked' => esc_html__( 'Stacked', 'easy-property-listings' ),
					'inline'  => esc_html__( 'Inline', 'easy-property-listings' ),
				),
				'default' => 'stacked',
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
				'label'     => esc_html__( 'Text Color', 'easy-property-listings' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .epl-agent-contact' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'link_color',
			array(
				'label'     => esc_html__( 'Link Color', 'easy-property-listings' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .epl-agent-contact a' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'typography',
				'selector' => '{{WRAPPER}} .epl-agent-contact',
			)
		);

		$this->add_responsive_control(
			'item_gap',
			array(
				'label'      => esc_html__( 'Gap Between Items', 'easy-property-listings' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 30,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .epl-agent-contact' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Get style dependencies so the Font Awesome icons always load.
	 *
	 * @return array
	 */
	public function get_style_depends() {
		return array( 'elementor-icons-fa-solid' );
	}

	/**
	 * Render widget output.
	 */
	protected function render() {
		$author = $this->get_current_agent();

		if ( ! $author ) {
			if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
				echo '<div class="epl-elementor-placeholder">' . esc_html__( 'Agent Contact - No agent context.', 'easy-property-listings' ) . '</div>';
			}
			return;
		}

		$settings     = $this->get_settings_for_display();
		$show_labels  = 'yes' === $settings['show_labels'];
		$show_icons   = 'yes' === $settings['show_icons'];
		$layout_class = 'inline' === $settings['layout'] ? 'epl-contact-inline' : 'epl-contact-stacked';

		$mobile       = $author->get_author_mobile();
		$office_phone = $author->get_author_office_phone();
		$email        = $author->email;

		echo '<div class="epl-agent-contact ' . esc_attr( $layout_class ) . '">';

		// Mobile.
		if ( 'yes' === $settings['show_mobile'] && ! empty( $mobile ) ) {
			echo '<div class="epl-contact-item epl-contact-mobile">';
			if ( $show_icons ) {
				echo '<i class="fas fa-mobile-alt"></i> ';
			}
			if ( $show_labels ) {
				echo '<span class="epl-contact-label">' . esc_html__( 'Mobile:', 'easy-property-listings' ) . '</span> ';
			}
			echo '<a href="tel:' . esc_attr( $mobile ) . '">' . esc_html( $mobile ) . '</a>';
			echo '</div>';
		}

		// Office Phone.
		if ( 'yes' === $settings['show_office_phone'] && ! empty( $office_phone ) ) {
			echo '<div class="epl-contact-item epl-contact-office">';
			if ( $show_icons ) {
				echo '<i class="fas fa-phone"></i> ';
			}
			if ( $show_labels ) {
				echo '<span class="epl-contact-label">' . esc_html__( 'Office:', 'easy-property-listings' ) . '</span> ';
			}
			echo '<a href="tel:' . esc_attr( $office_phone ) . '">' . esc_html( $office_phone ) . '</a>';
			echo '</div>';
		}

		// Email.
		if ( 'yes' === $settings['show_email'] && ! empty( $email ) ) {
			echo '<div class="epl-contact-item epl-contact-email">';
			if ( $show_icons ) {
				echo '<i class="fas fa-envelope"></i> ';
			}
			if ( $show_labels ) {
				echo '<span class="epl-contact-label">' . esc_html__( 'Email:', 'easy-property-listings' ) . '</span> ';
			}
			echo '<a href="mailto:' . esc_attr( $email ) . '">' . esc_html( $email ) . '</a>';
			echo '</div>';
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
