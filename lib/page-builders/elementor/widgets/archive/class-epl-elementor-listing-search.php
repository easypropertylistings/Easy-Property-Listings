<?php
/**
 * Elementor Listing Search Widget
 *
 * @package     EPL
 * @subpackage  PageBuilders/Elementor/Widgets/Archive
 * @since       3.6.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * EPL_Elementor_Listing_Search Class
 *
 * @since 3.6.0
 */
class EPL_Elementor_Listing_Search extends \Elementor\Widget_Base {

	/**
	 * Get widget name.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'epl-listing-search';
	}

	/**
	 * Get widget title.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'EPL Listing Search', 'easy-property-listings' );
	}

	/**
	 * Get widget icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-search';
	}

	/**
	 * Get widget categories.
	 *
	 * @return array
	 */
	public function get_categories() {
		return array( 'epl-archive' );
	}

	/**
	 * Get widget keywords.
	 *
	 * @return array
	 */
	public function get_keywords() {
		return array( 'listing', 'search', 'property', 'filter', 'epl' );
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
			'post_type',
			array(
				'label'    => esc_html__( 'Post Type', 'easy-property-listings' ),
				'type'     => \Elementor\Controls_Manager::SELECT,
				'options'  => array_merge(
					array( '' => esc_html__( 'All', 'easy-property-listings' ) ),
					EPL_Elementor::get_post_types()
				),
				'default'  => '',
			)
		);

		$this->add_control(
			'show_title',
			array(
				'label'        => esc_html__( 'Show Title', 'easy-property-listings' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'easy-property-listings' ),
				'label_off'    => esc_html__( 'No', 'easy-property-listings' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_control(
			'title',
			array(
				'label'     => esc_html__( 'Title', 'easy-property-listings' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'default'   => esc_html__( 'Search Listings', 'easy-property-listings' ),
				'condition' => array(
					'show_title' => 'yes',
				),
			)
		);

		$this->add_control(
			'button_text',
			array(
				'label'   => esc_html__( 'Button Text', 'easy-property-listings' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Search', 'easy-property-listings' ),
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
			'button_color',
			array(
				'label'     => esc_html__( 'Button Color', 'easy-property-listings' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .epl-search-submit' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'button_text_color',
			array(
				'label'     => esc_html__( 'Button Text Color', 'easy-property-listings' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .epl-search-submit' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render widget output.
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		echo '<div class="epl-search-widget">';

		if ( 'yes' === $settings['show_title'] && ! empty( $settings['title'] ) ) {
			echo '<h3 class="epl-search-title">' . esc_html( $settings['title'] ) . '</h3>';
		}

		$atts = array(
			'show_label' => 'on',
		);

		if ( ! empty( $settings['post_type'] ) ) {
			$atts['post_type'] = $settings['post_type'];
		}

		if ( ! empty( $settings['button_text'] ) ) {
			$atts['submit_label'] = $settings['button_text'];
		}

		// Use EPL's search form shortcode.
		echo do_shortcode( '[listing_search ' . $this->build_shortcode_atts( $atts ) . ']' );

		echo '</div>';
	}

	/**
	 * Build shortcode attributes string.
	 *
	 * @param array $atts Attributes array.
	 * @return string
	 */
	private function build_shortcode_atts( $atts ) {
		$output = '';
		foreach ( $atts as $key => $value ) {
			$output .= $key . '="' . esc_attr( $value ) . '" ';
		}
		return trim( $output );
	}
}
