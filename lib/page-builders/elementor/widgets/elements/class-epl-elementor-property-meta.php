<?php
/**
 * Elementor Property Meta Widget.
 *
 * @package    EPL
 * @subpackage PageBuilders/Elementor/Widgets/Elements
 * @since      3.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Render an arbitrary EPL listing meta field. */
class EPL_Elementor_Property_Meta extends \Elementor\Widget_Base {
	use EPL_Elementor_Dynamic_Widget;

	public function get_name() {
		return 'epl-property-meta';
	}

	public function get_title() {
		return esc_html__( 'Custom Field', 'easy-property-listings' );
	}

	public function get_icon() {
		return 'eicon-meta-data';
	}

	public function get_categories() {
		return array( 'epl-elements' );
	}

	public function get_keywords() {
		return array( 'property', 'meta', 'custom field', 'value', 'epl' );
	}

	protected function register_controls() {
		$this->start_controls_section( 'section_content', array( 'label' => esc_html__( 'Meta', 'easy-property-listings' ) ) );

		$this->add_control(
			'meta_key',
			array(
				'label'       => esc_html__( 'Meta Key', 'easy-property-listings' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => 'property_land_area',
				'label_block' => true,
			)
		);

		$this->add_control(
			'format',
			array(
				'label'   => esc_html__( 'Format', 'easy-property-listings' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'text',
				'options' => array(
					'text'     => esc_html__( 'Text', 'easy-property-listings' ),
					'number'   => esc_html__( 'Number', 'easy-property-listings' ),
					'currency' => esc_html__( 'Currency', 'easy-property-listings' ),
					'date'     => esc_html__( 'Date', 'easy-property-listings' ),
					'yes_no'   => esc_html__( 'Yes / No', 'easy-property-listings' ),
					'phone'    => esc_html__( 'Phone Link', 'easy-property-listings' ),
					'email'    => esc_html__( 'Email Link', 'easy-property-listings' ),
					'url'      => esc_html__( 'URL Link', 'easy-property-listings' ),
				),
			)
		);

		$this->add_control( 'decimal_places', array( 'label' => esc_html__( 'Decimal Places', 'easy-property-listings' ), 'type' => \Elementor\Controls_Manager::NUMBER, 'min' => 0, 'max' => 6, 'default' => 0, 'condition' => array( 'format' => 'number' ) ) );
		$this->add_control( 'date_format', array( 'label' => esc_html__( 'Date Format', 'easy-property-listings' ), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => 'j F Y', 'condition' => array( 'format' => 'date' ) ) );
		$this->add_control( 'yes_label', array( 'label' => esc_html__( 'Yes Label', 'easy-property-listings' ), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => esc_html__( 'Yes', 'easy-property-listings' ), 'condition' => array( 'format' => 'yes_no' ) ) );
		$this->add_control( 'no_label', array( 'label' => esc_html__( 'No Label', 'easy-property-listings' ), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => esc_html__( 'No', 'easy-property-listings' ), 'condition' => array( 'format' => 'yes_no' ) ) );

		$this->add_control(
			'value_map',
			array(
				'label'       => esc_html__( 'Value Labels', 'easy-property-listings' ),
				'type'        => \Elementor\Controls_Manager::TEXTAREA,
				'description' => esc_html__( 'Optional, one mapping per line: stored_value|Display Label', 'easy-property-listings' ),
				'placeholder' => "yes|Available\nno|Unavailable",
			)
		);

		$this->add_control( 'prefix', array( 'label' => esc_html__( 'Prefix', 'easy-property-listings' ), 'type' => \Elementor\Controls_Manager::TEXT ) );
		$this->add_control( 'suffix', array( 'label' => esc_html__( 'Suffix', 'easy-property-listings' ), 'type' => \Elementor\Controls_Manager::TEXT ) );
		$this->add_control( 'fallback', array( 'label' => esc_html__( 'Empty Value', 'easy-property-listings' ), 'type' => \Elementor\Controls_Manager::TEXT ) );
		$this->add_control(
			'html_tag',
			array(
				'label'   => esc_html__( 'HTML Tag', 'easy-property-listings' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'div',
				'options' => array( 'div' => 'div', 'span' => 'span', 'p' => 'p', 'h2' => 'h2', 'h3' => 'h3', 'h4' => 'h4' ),
			)
		);
		$this->add_control(
			'icon',
			array(
				'label' => esc_html__( 'Icon', 'easy-property-listings' ),
				'type'  => \Elementor\Controls_Manager::ICONS,
			)
		);
		$this->add_control(
			'icon_position',
			array(
				'label'     => esc_html__( 'Icon Position', 'easy-property-listings' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'default'   => 'before',
				'options'   => array(
					'before' => esc_html__( 'Before', 'easy-property-listings' ),
					'after'  => esc_html__( 'After', 'easy-property-listings' ),
				),
				'condition' => array( 'icon[value]!' => '' ),
			)
		);
		$this->end_controls_section();

		$this->start_controls_section( 'section_style', array( 'label' => esc_html__( 'Style', 'easy-property-listings' ), 'tab' => \Elementor\Controls_Manager::TAB_STYLE ) );
		$this->add_control( 'color', array( 'label' => esc_html__( 'Color', 'easy-property-listings' ), 'type' => \Elementor\Controls_Manager::COLOR, 'selectors' => array( '{{WRAPPER}} .epl-property-meta' => 'color: {{VALUE}};' ) ) );
		$this->add_group_control( \Elementor\Group_Control_Typography::get_type(), array( 'name' => 'typography', 'selector' => '{{WRAPPER}} .epl-property-meta' ) );
		$this->add_responsive_control( 'alignment', array( 'label' => esc_html__( 'Alignment', 'easy-property-listings' ), 'type' => \Elementor\Controls_Manager::CHOOSE, 'options' => array( 'left' => array( 'title' => esc_html__( 'Left', 'easy-property-listings' ), 'icon' => 'eicon-text-align-left' ), 'center' => array( 'title' => esc_html__( 'Center', 'easy-property-listings' ), 'icon' => 'eicon-text-align-center' ), 'right' => array( 'title' => esc_html__( 'Right', 'easy-property-listings' ), 'icon' => 'eicon-text-align-right' ) ), 'selectors' => array( '{{WRAPPER}} .epl-property-meta' => 'text-align: {{VALUE}};' ) ) );
		$this->add_control( 'icon_heading', array( 'label' => esc_html__( 'Icon', 'easy-property-listings' ), 'type' => \Elementor\Controls_Manager::HEADING, 'separator' => 'before' ) );
		$this->add_control( 'icon_color', array( 'label' => esc_html__( 'Icon Color', 'easy-property-listings' ), 'type' => \Elementor\Controls_Manager::COLOR, 'selectors' => array( '{{WRAPPER}} .epl-property-meta-icon i' => 'color: {{VALUE}};', '{{WRAPPER}} .epl-property-meta-icon svg' => 'fill: {{VALUE}};' ) ) );
		$this->add_responsive_control(
			'icon_size',
			array(
				'label'      => esc_html__( 'Icon Size', 'easy-property-listings' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'range'      => array( 'px' => array( 'min' => 6, 'max' => 200 ) ),
				'default'    => array( 'size' => 16, 'unit' => 'px' ),
				'selectors'  => array( '{{WRAPPER}} .epl-property-meta-icon' => 'font-size: {{SIZE}}{{UNIT}};', '{{WRAPPER}} .epl-property-meta-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'icon_spacing',
			array(
				'label'      => esc_html__( 'Icon Spacing', 'easy-property-listings' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 100 ) ),
				'default'    => array( 'size' => 8, 'unit' => 'px' ),
				'selectors'  => array( '{{WRAPPER}} .epl-property-meta-content' => 'gap: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->end_controls_section();
	}

	protected function render() {
		$property = EPL_Elementor::setup_listing_context();
		if ( ! $property ) {
			if ( EPL_Elementor::is_editor() ) {
				echo '<div class="epl-elementor-placeholder">' . esc_html__( 'Property Meta - No listings found.', 'easy-property-listings' ) . '</div>';
			}
			EPL_Elementor::restore_listing_context();
			return;
		}

		$settings = $this->get_settings_for_display();
		$key      = sanitize_key( $settings['meta_key'] );
		$value    = $key ? $property->get_property_meta( $key ) : '';
		if ( is_array( $value ) ) {
			$value = implode( ', ', array_map( 'sanitize_text_field', $value ) );
		}
		$value = $this->map_value( (string) $value, $settings['value_map'] );
		$html  = $this->format_value( $value, $settings );

		if ( '' === $html ) {
			$html = esc_html( $settings['fallback'] );
		}
		if ( '' !== $html ) {
			$tag = \Elementor\Utils::validate_html_tag( $settings['html_tag'] );
			$icon = '';
			if ( ! empty( $settings['icon']['value'] ) ) {
				ob_start();
				\Elementor\Icons_Manager::render_icon( $settings['icon'], array( 'aria-hidden' => 'true' ) );
				$icon = '<span class="epl-property-meta-icon">' . ob_get_clean() . '</span>';
			}
			$value_html = '<span class="epl-property-meta-value"><span class="epl-property-meta-prefix">' . esc_html( $settings['prefix'] ) . '</span>' . wp_kses_post( $html ) . '<span class="epl-property-meta-suffix">' . esc_html( $settings['suffix'] ) . '</span></span>';
			$content    = 'after' === $settings['icon_position'] ? $value_html . $icon : $icon . $value_html;
			echo '<' . esc_attr( $tag ) . ' class="epl-property-meta epl-property-meta-' . esc_attr( $key ) . '"><span class="epl-property-meta-content">' . $content . '</span></' . esc_attr( $tag ) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Icon Manager and value formatter escape their output.
		}
		EPL_Elementor::restore_listing_context();
	}

	private function map_value( $value, $map ) {
		foreach ( preg_split( '/\r\n|\r|\n/', (string) $map ) as $line ) {
			$parts = array_map( 'trim', explode( '|', $line, 2 ) );
			if ( 2 === count( $parts ) && $value === $parts[0] ) {
				return $parts[1];
			}
		}
		return $value;
	}

	private function format_value( $value, $settings ) {
		if ( '' === $value ) {
			return '';
		}
		switch ( $settings['format'] ) {
			case 'number':
				return is_numeric( $value ) ? esc_html( number_format_i18n( (float) $value, absint( $settings['decimal_places'] ) ) ) : esc_html( $value );
			case 'currency':
				return is_numeric( $value ) ? esc_html( epl_currency_formatted_amount( $value ) ) : esc_html( $value );
			case 'date':
				$timestamp = is_numeric( $value ) ? (int) $value : strtotime( $value );
				return $timestamp ? esc_html( wp_date( $settings['date_format'], $timestamp ) ) : esc_html( $value );
			case 'yes_no':
				return esc_html( in_array( strtolower( $value ), array( '1', 'yes', 'on', 'true' ), true ) ? $settings['yes_label'] : $settings['no_label'] );
			case 'phone':
				return '<a href="tel:' . esc_attr( preg_replace( '/[^0-9+]/', '', $value ) ) . '">' . esc_html( $value ) . '</a>';
			case 'email':
				return is_email( $value ) ? '<a href="mailto:' . esc_attr( $value ) . '">' . esc_html( $value ) . '</a>' : esc_html( $value );
			case 'url':
				return '<a href="' . esc_url( $value ) . '">' . esc_html( $value ) . '</a>';
			default:
				return esc_html( $value );
		}
	}
}
