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
	use EPL_Elementor_Dynamic_Widget;

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
				'label' => esc_html__( 'Search Form', 'easy-property-listings' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		// The WordPress widget, shortcode and extensions all use this schema.
		// Consuming it here keeps Elementor in sync when EPL adds or filters fields.
		foreach ( epl_search_widget_fields() as $field ) {
			$this->register_search_control( $field );
		}

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
					'{{WRAPPER}} .epl-search-btn' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'button_text_color',
			array(
				'label'     => esc_html__( 'Button Text Color', 'easy-property-listings' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .epl-search-btn' => 'color: {{VALUE}};',
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
		$atts     = array();

		foreach ( epl_search_widget_fields() as $field ) {
			$key = $field['key'];
			if ( array_key_exists( $key, $settings ) ) {
				$atts[ $key ] = $settings[ $key ];
			}
		}

		echo '<div class="epl-search-widget">';

		if ( ! empty( $atts['title'] ) ) {
			echo '<h3 class="epl-search-title">' . esc_html( $atts['title'] ) . '</h3>';
		}

		// Call the [listing_search] callback directly: no shortcode string to
		// break on brackets/quotes in user values, and no double escaping.
		echo epl_shortcode_listing_search_callback( $atts ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in template.

		echo '</div>';
	}

	/**
	 * Convert a canonical EPL WordPress-widget field to an Elementor control.
	 *
	 * @param array $field EPL search widget field definition.
	 */
	private function register_search_control( $field ) {
		if ( empty( $field['key'] ) || empty( $field['type'] ) ) {
			return;
		}

		$key     = sanitize_key( $field['key'] );
		$default = isset( $field['default'] ) ? $field['default'] : '';
		$args    = array(
			'label'       => isset( $field['label'] ) ? $field['label'] : $key,
			'default'     => $default,
			'description' => isset( $field['help'] ) ? wp_strip_all_tags( $field['help'] ) : '',
		);

		switch ( $field['type'] ) {
			case 'checkbox':
				$args['type']         = \Elementor\Controls_Manager::SWITCHER;
				$args['label_on']     = esc_html__( 'Yes', 'easy-property-listings' );
				$args['label_off']    = esc_html__( 'No', 'easy-property-listings' );
				$args['return_value'] = 'on';
				break;

			case 'select':
				$args['type']        = ! empty( $field['multiple'] ) ? \Elementor\Controls_Manager::SELECT2 : \Elementor\Controls_Manager::SELECT;
				$args['options']     = isset( $field['options'] ) ? $field['options'] : array();
				$args['multiple']    = ! empty( $field['multiple'] );
				$args['label_block'] = true;
				break;

			case 'number':
				$args['type'] = \Elementor\Controls_Manager::NUMBER;
				break;

			case 'textarea':
				$args['type'] = \Elementor\Controls_Manager::TEXTAREA;
				break;

			case 'hidden':
				$args['type'] = \Elementor\Controls_Manager::HIDDEN;
				break;

			case 'text':
			default:
				$args['type']        = \Elementor\Controls_Manager::TEXT;
				$args['label_block'] = true;
				break;
		}

		$this->add_control( $key, $args );
	}
}
