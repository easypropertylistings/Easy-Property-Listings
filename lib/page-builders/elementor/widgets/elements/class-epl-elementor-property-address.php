<?php
/**
 * Elementor Property Address Widget
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
 * EPL_Elementor_Property_Address Class
 *
 * @since 3.6.0
 */
class EPL_Elementor_Property_Address extends \Elementor\Widget_Base {
	use EPL_Elementor_Dynamic_Widget;

	/**
	 * Get widget name.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'epl-property-address';
	}

	/**
	 * Get widget title.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'EPL Property Address', 'easy-property-listings' );
	}

	/**
	 * Get widget icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-map-pin';
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
		return array( 'property', 'address', 'location', 'epl' );
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
			'link_to_listing',
			array(
				'label'        => esc_html__( 'Link to Listing', 'easy-property-listings' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'easy-property-listings' ),
				'label_off'    => esc_html__( 'No', 'easy-property-listings' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_control(
			'show_suburb_profile',
			array(
				'label'        => esc_html__( 'Show Suburb Only', 'easy-property-listings' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'easy-property-listings' ),
				'label_off'    => esc_html__( 'No', 'easy-property-listings' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_control(
			'show_icon',
			array(
				'label'        => esc_html__( 'Show Icon', 'easy-property-listings' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'easy-property-listings' ),
				'label_off'    => esc_html__( 'Hide', 'easy-property-listings' ),
				'return_value' => 'yes',
				'default'      => 'no',
			)
		);

		$this->add_control(
			'icon',
			array(
				'label'   => esc_html__( 'Icon', 'easy-property-listings' ),
				'type'    => \Elementor\Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'fas fa-map-marker-alt',
					'library' => 'fa-solid',
				),
				'condition' => array(
					'show_icon' => 'yes',
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
					'{{WRAPPER}} .epl-property-address' => 'color: {{VALUE}};',
					'{{WRAPPER}} .epl-property-address a' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'typography',
				'selector' => '{{WRAPPER}} .epl-property-address',
			)
		);

		$this->add_responsive_control(
			'icon_size',
			array(
				'label'      => esc_html__( 'Icon Size', 'easy-property-listings' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem', 'vw' ),
				'range'      => array(
					'px' => array(
						'min' => 10,
						'max' => 100,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .epl-icon-wrapper i' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .epl-icon-wrapper svg' => 'width: {{SIZE}}{{UNIT}}; height: auto;',
				),
				'condition' => array(
					'show_icon' => 'yes',
				),
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
					'{{WRAPPER}} .epl-property-address' => 'text-align: {{VALUE}};',
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
				echo '<div class="epl-elementor-placeholder">' . esc_html__( 'Property Address - No listings found.', 'easy-property-listings' ) . '</div>';
			}
			EPL_Elementor::restore_listing_context();
			return;
		}

		$settings = $this->get_settings_for_display();

		echo '<div class="epl-property-address">';

		if ( 'yes' === $settings['show_icon'] ) {
			echo '<span class="epl-icon-wrapper">';
			\Elementor\Icons_Manager::render_icon( $settings['icon'], array( 'aria-hidden' => 'true' ) );
			echo '</span>';
			echo '&nbsp;';
		}

		if ( 'yes' === $settings['link_to_listing'] ) {
			echo '<a href="' . esc_url( get_permalink( $property->post->ID ) ) . '">';
		}

		if ( 'yes' === $settings['show_suburb_profile'] ) {
			// Suburb only, respecting commercial/business suburb display rules.
			do_action( 'epl_property_suburb' );
		} else {
			// Full address. Core respects the property_address_display option,
			// commercial suburb display and the city/country field settings.
			do_action( 'epl_property_address' );
		}

		if ( 'yes' === $settings['link_to_listing'] ) {
			echo '</a>';
		}

		echo '</div>';

		EPL_Elementor::restore_listing_context();
	}
}
