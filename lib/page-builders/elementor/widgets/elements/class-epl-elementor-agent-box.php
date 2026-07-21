<?php
/**
 * Elementor Agent Box Widget
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
 * EPL_Elementor_Agent_Box Class
 *
 * Displays the listing agent/author information.
 *
 * @since 3.6.0
 */
class EPL_Elementor_Agent_Box extends \Elementor\Widget_Base {
	use EPL_Elementor_Dynamic_Widget;

	/**
	 * Get widget name.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'epl-agent-box';
	}

	/**
	 * Get widget title.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'EPL Agent Box', 'easy-property-listings' );
	}

	/**
	 * Get widget icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-person';
	}

	/**
	 * Get widget categories.
	 *
	 * @return array
	 */
	public function get_categories() {
		return array( 'epl-staff', 'epl-elements' );
	}

	/**
	 * Get widget keywords.
	 *
	 * @return array
	 */
	public function get_keywords() {
		return array( 'property', 'agent', 'author', 'realtor', 'epl' );
	}

	/**
	 * Register widget controls.
	 */
	protected function register_controls() {

		// Content Section - Layout.
		$this->start_controls_section(
			'section_layout',
			array(
				'label' => esc_html__( 'Layout', 'easy-property-listings' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'layout',
			array(
				'label'   => esc_html__( 'Layout', 'easy-property-listings' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => array(
					'vertical'   => esc_html__( 'Vertical (Stacked)', 'easy-property-listings' ),
					'horizontal' => esc_html__( 'Horizontal (Side by Side)', 'easy-property-listings' ),
				),
				'default' => 'vertical',
			)
		);

		$this->end_controls_section();

		// Content Section - Elements.
		$this->start_controls_section(
			'section_elements',
			array(
				'label' => esc_html__( 'Elements', 'easy-property-listings' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'show_photo',
			array(
				'label'        => esc_html__( 'Show Photo', 'easy-property-listings' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'easy-property-listings' ),
				'label_off'    => esc_html__( 'No', 'easy-property-listings' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'photo_size',
			array(
				'label'     => esc_html__( 'Photo Size', 'easy-property-listings' ),
				'type'      => \Elementor\Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min' => 50,
						'max' => 300,
					),
				),
				'default'   => array(
					'size' => 150,
					'unit' => 'px',
				),
				'condition' => array(
					'show_photo' => 'yes',
				),
			)
		);

		$this->add_control(
			'show_name',
			array(
				'label'        => esc_html__( 'Show Name', 'easy-property-listings' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'easy-property-listings' ),
				'label_off'    => esc_html__( 'No', 'easy-property-listings' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'link_name',
			array(
				'label'        => esc_html__( 'Link Name to Author Page', 'easy-property-listings' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'easy-property-listings' ),
				'label_off'    => esc_html__( 'No', 'easy-property-listings' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => array(
					'show_name' => 'yes',
				),
			)
		);

		$this->add_control(
			'show_position',
			array(
				'label'        => esc_html__( 'Show Position/Title', 'easy-property-listings' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'easy-property-listings' ),
				'label_off'    => esc_html__( 'No', 'easy-property-listings' ),
				'return_value' => 'yes',
				'default'      => 'yes',
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
			'show_social_icons',
			array(
				'label'        => esc_html__( 'Show Social Icons', 'easy-property-listings' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'easy-property-listings' ),
				'label_off'    => esc_html__( 'No', 'easy-property-listings' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'show_bio',
			array(
				'label'        => esc_html__( 'Show Bio', 'easy-property-listings' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'easy-property-listings' ),
				'label_off'    => esc_html__( 'No', 'easy-property-listings' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->end_controls_section();

		// Style Section - Container.
		$this->start_controls_section(
			'section_style_container',
			array(
				'label' => esc_html__( 'Container', 'easy-property-listings' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'container_bg_color',
			array(
				'label'     => esc_html__( 'Background Color', 'easy-property-listings' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .epl-agent-box' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'container_padding',
			array(
				'label'      => esc_html__( 'Padding', 'easy-property-listings' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .epl-agent-box' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			array(
				'name'     => 'container_border',
				'selector' => '{{WRAPPER}} .epl-agent-box',
			)
		);

		$this->add_responsive_control(
			'container_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'easy-property-listings' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .epl-agent-box' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'container_box_shadow',
				'selector' => '{{WRAPPER}} .epl-agent-box',
			)
		);

		$this->end_controls_section();

		// Style Section - Photo.
		$this->start_controls_section(
			'section_style_photo',
			array(
				'label'     => esc_html__( 'Photo', 'easy-property-listings' ),
				'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => array(
					'show_photo' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'photo_border_radius',
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

		$this->end_controls_section();

		// Style Section - Name.
		$this->start_controls_section(
			'section_style_name',
			array(
				'label'     => esc_html__( 'Name', 'easy-property-listings' ),
				'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => array(
					'show_name' => 'yes',
				),
			)
		);

		$this->add_control(
			'name_color',
			array(
				'label'     => esc_html__( 'Color', 'easy-property-listings' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .epl-agent-name' => 'color: {{VALUE}};',
					'{{WRAPPER}} .epl-agent-name a' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'name_typography',
				'selector' => '{{WRAPPER}} .epl-agent-name',
			)
		);

		$this->add_responsive_control(
			'name_spacing',
			array(
				'label'      => esc_html__( 'Spacing', 'easy-property-listings' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .epl-agent-name' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		// Style Section - Contact Info.
		$this->start_controls_section(
			'section_style_contact',
			array(
				'label' => esc_html__( 'Contact Info', 'easy-property-listings' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'contact_color',
			array(
				'label'     => esc_html__( 'Color', 'easy-property-listings' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .epl-agent-contact' => 'color: {{VALUE}};',
					'{{WRAPPER}} .epl-agent-contact a' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'contact_typography',
				'selector' => '{{WRAPPER}} .epl-agent-contact',
			)
		);

		$this->end_controls_section();

		// Style Section - Social Icons.
		$this->start_controls_section(
			'section_style_social',
			array(
				'label'     => esc_html__( 'Social Icons', 'easy-property-listings' ),
				'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => array(
					'show_social_icons' => 'yes',
				),
			)
		);

		$this->add_control(
			'social_icon_color',
			array(
				'label'     => esc_html__( 'Icon Color', 'easy-property-listings' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .epl-agent-social a' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'social_icon_size',
			array(
				'label'      => esc_html__( 'Icon Size', 'easy-property-listings' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 10,
						'max' => 50,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .epl-agent-social a' => 'font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'social_icon_gap',
			array(
				'label'      => esc_html__( 'Gap Between Icons', 'easy-property-listings' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 30,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .epl-agent-social' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		// Style Section - Bio.
		$this->start_controls_section(
			'section_style_bio',
			array(
				'label'     => esc_html__( 'Bio', 'easy-property-listings' ),
				'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => array(
					'show_bio' => 'yes',
				),
			)
		);

		$this->add_control(
			'bio_color',
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
				'name'     => 'bio_typography',
				'selector' => '{{WRAPPER}} .epl-agent-bio',
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
				echo '<div class="epl-elementor-placeholder">' . esc_html__( 'Agent Box - No listings found.', 'easy-property-listings' ) . '</div>';
			}
			EPL_Elementor::restore_listing_context();
			return;
		}

		$settings = $this->get_settings_for_display();

		// Always resolve against this listing. Elementor nested loops can leave
		// the core global author pointing at the previous loop item.
		$agent = EPL_Elementor::get_listing_agent( $property );

		if ( ! $agent ) {
			EPL_Elementor::restore_listing_context();
			return;
		}

		$author_id = $agent->author_id;

		$layout_class = 'horizontal' === $settings['layout'] ? 'epl-agent-horizontal' : 'epl-agent-vertical';

		echo '<div class="epl-agent-box ' . esc_attr( $layout_class ) . '">';

		if ( 'yes' === $settings['show_photo'] ) {
			$photo_size = isset( $settings['photo_size']['size'] ) ? intval( $settings['photo_size']['size'] ) : 150;
			echo '<div class="epl-agent-photo">';
			echo wp_kses_post( EPL_Elementor::get_agent_image( $agent, $photo_size ) );
			echo '</div>';
		}

		echo '<div class="epl-agent-details">';

		// Name.
		if ( 'yes' === $settings['show_name'] ) {
			$display_name = $agent->get_author_name();
			echo '<div class="epl-agent-name">';
			if ( 'yes' === $settings['link_name'] ) {
				$permalink = apply_filters( 'epl_author_profile_link', get_author_posts_url( $author_id ), $agent );
				echo '<a href="' . esc_url( $permalink ) . '">' . esc_html( $display_name ) . '</a>';
			} else {
				echo esc_html( $display_name );
			}
			echo '</div>';
		}

		// Position.
		if ( 'yes' === $settings['show_position'] ) {
			$position = $agent->get_author_position();
			if ( ! empty( $position ) ) {
				echo '<div class="epl-agent-position">' . esc_html( $position ) . '</div>';
			}
		}

		// Contact Info.
		echo '<div class="epl-agent-contact">';

		// Mobile.
		if ( 'yes' === $settings['show_mobile'] ) {
			$mobile = $agent->get_author_mobile();
			if ( ! empty( $mobile ) ) {
				echo '<div class="epl-agent-mobile">';
				echo '<span class="epl-agent-label">' . esc_html__( 'Mobile:', 'easy-property-listings' ) . '</span> ';
				echo '<a href="tel:' . esc_attr( $mobile ) . '">' . esc_html( $mobile ) . '</a>';
				echo '</div>';
			}
		}

		// Office Phone.
		if ( 'yes' === $settings['show_office_phone'] ) {
			$office_phone = $agent->get_author_office_phone();
			if ( ! empty( $office_phone ) ) {
				echo '<div class="epl-agent-office-phone">';
				echo '<span class="epl-agent-label">' . esc_html__( 'Office:', 'easy-property-listings' ) . '</span> ';
				echo '<a href="tel:' . esc_attr( $office_phone ) . '">' . esc_html( $office_phone ) . '</a>';
				echo '</div>';
			}
		}

		// Email.
		if ( 'yes' === $settings['show_email'] ) {
			$email = $agent->email;
			if ( ! empty( $email ) ) {
				echo '<div class="epl-agent-email">';
				echo '<span class="epl-agent-label">' . esc_html__( 'Email:', 'easy-property-listings' ) . '</span> ';
				echo '<a href="mailto:' . esc_attr( $email ) . '">' . esc_html( $email ) . '</a>';
				echo '</div>';
			}
		}

		echo '</div>'; // End contact.

		// Social Icons.
		if ( 'yes' === $settings['show_social_icons'] ) {
			$social_icons = array( 'facebook', 'twitter', 'instagram', 'linkedin', 'youtube', 'pinterest' );

			// The loader proxies methods via __call, which method_exists() cannot
			// see — check against the wrapped author object instead.
			$author_object = $agent instanceof EPL_Author_Loader ? $agent->object : $agent;

			$social_html = '';
			foreach ( $social_icons as $social ) {
				$method = 'get_' . $social . '_html';
				if ( $author_object && is_callable( array( $author_object, $method ) ) ) {
					$social_html .= (string) call_user_func( array( $author_object, $method ) );
				}
			}

			if ( '' !== trim( $social_html ) ) {
				echo '<div class="epl-agent-social">' . wp_kses_post( $social_html ) . '</div>';
			}
		}

		// Bio.
		if ( 'yes' === $settings['show_bio'] ) {
			$bio = $agent->get_description_html();
			if ( ! empty( $bio ) ) {
				echo '<div class="epl-agent-bio">' . wp_kses_post( $bio ) . '</div>';
			}
		}

		echo '</div>'; // End details.
		echo '</div>'; // End agent-box.

		EPL_Elementor::restore_listing_context();
	}
}
