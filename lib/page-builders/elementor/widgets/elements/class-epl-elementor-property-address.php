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
			'display_mode',
			array(
				'label'       => esc_html__( 'Display', 'easy-property-listings' ),
				'type'        => \Elementor\Controls_Manager::SELECT,
				'default'     => 'full',
				'options'     => array(
					'full'   => esc_html__( 'Full Address', 'easy-property-listings' ),
					'suburb' => esc_html__( 'Suburb Only', 'easy-property-listings' ),
					'street' => esc_html__( 'Street Only', 'easy-property-listings' ),
					'custom' => esc_html__( 'Custom Parts', 'easy-property-listings' ),
				),
				'description' => esc_html__( 'Full address, suburb only, street only, or choose your own combination of address parts.', 'easy-property-listings' ),
			)
		);

		$this->add_control(
			'address_parts',
			array(
				'label'       => esc_html__( 'Address Parts', 'easy-property-listings' ),
				'type'        => \Elementor\Controls_Manager::SELECT2,
				'multiple'    => true,
				'label_block' => true,
				'options'     => self::get_address_part_options(),
				'default'     => array( 'sub_number', 'street_number', 'street' ),
				'description' => esc_html__( 'Select which address components to display. Parts always output in natural address order regardless of selection order.', 'easy-property-listings' ),
				'condition'   => array(
					'display_mode' => 'custom',
				),
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

		$this->add_control(
			'icon_color',
			array(
				'label'     => esc_html__( 'Icon Color', 'easy-property-listings' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .epl-icon-wrapper i'        => 'color: {{VALUE}};',
					'{{WRAPPER}} .epl-icon-wrapper svg'      => 'fill: {{VALUE}};',
					'{{WRAPPER}} .epl-icon-wrapper svg path' => 'fill: {{VALUE}};',
				),
				'condition' => array(
					'show_icon' => 'yes',
				),
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

		$this->add_responsive_control(
			'minimum_height',
			array(
				'label'      => esc_html__( 'Minimum Height', 'easy-property-listings' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'default'    => array(
					'size' => 25,
					'unit' => 'px',
				),
				'description' => esc_html__( 'Keeps the container from collapsing (e.g. suburb-only) so cards stay aligned in a grid.', 'easy-property-listings' ),
				'selectors'  => array(
					'{{WRAPPER}} .epl-property-address' => 'min-height: {{SIZE}}{{UNIT}};',
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

		$display_mode = isset( $settings['display_mode'] ) ? $settings['display_mode'] : 'full';

		// Backward compatibility with earlier builds that used a "Show Suburb Only" switch.
		if ( 'full' === $display_mode && isset( $settings['show_suburb_profile'] ) && 'yes' === $settings['show_suburb_profile'] ) {
			$display_mode = 'suburb';
		}

		switch ( $display_mode ) {
			case 'suburb':
				// Suburb only, respecting commercial/business suburb display rules.
				do_action( 'epl_property_suburb' );
				break;

			case 'street':
			case 'custom':
				// Selected address parts only.
				echo wp_kses_post( $this->get_address_parts_html( $display_mode, $settings ) );
				break;

			case 'full':
			default:
				// Full address. Core respects the property_address_display option,
				// commercial suburb display and the city/country field settings.
				do_action( 'epl_property_address' );
				break;
		}

		if ( 'yes' === $settings['link_to_listing'] ) {
			echo '</a>';
		}

		echo '</div>';

		EPL_Elementor::restore_listing_context();
	}

	/**
	 * Available address parts and their control labels.
	 *
	 * @since 3.6.0
	 *
	 * @return array Machine key => translated label.
	 */
	public static function get_address_part_options() {
		return array(
			'sub_number'    => esc_html__( 'Unit / Sub Number', 'easy-property-listings' ),
			'lot_number'    => esc_html__( 'Lot Number', 'easy-property-listings' ),
			'street_number' => esc_html__( 'Street Number', 'easy-property-listings' ),
			'street'        => esc_html__( 'Street Name', 'easy-property-listings' ),
			'suburb'        => esc_html__( 'Suburb', 'easy-property-listings' ),
			'city'          => esc_html__( 'City', 'easy-property-listings' ),
			'state'         => esc_html__( 'State', 'easy-property-listings' ),
			'postal_code'   => esc_html__( 'Postal Code', 'easy-property-listings' ),
			'country'       => esc_html__( 'Country', 'easy-property-listings' ),
		);
	}

	/**
	 * Build the address output from the selected parts.
	 *
	 * Mirrors the Template Builder behaviour: parts are emitted in their natural
	 * address order (not selection order), each followed by its default separator,
	 * with the trailing separator on the final part stripped. Street level parts
	 * still respect the property_address_display privacy option and country is
	 * only output when explicitly selected, both enforced by epl_get_the_address().
	 *
	 * @since 3.6.0
	 *
	 * @param string $mode     Either 'street' (preset) or 'custom' (user selection).
	 * @param array  $settings Widget settings.
	 * @return string Escaped address HTML/text.
	 */
	protected function get_address_parts_html( $mode, $settings ) {

		// Canonical part order and default separators (matches epl_get_the_address()).
		$all_parts = array(
			'sub_number'    => '/',
			'lot_number'    => ' ',
			'street_number' => ' ',
			'street'        => ', ',
			'suburb'        => ' ',
			'city'          => ' ',
			'state'         => ' ',
			'postal_code'   => ' ',
			'country'       => ' ',
		);

		if ( 'street' === $mode ) {
			$parts = array( 'sub_number', 'lot_number', 'street_number', 'street' );
		} else {
			$parts = ( isset( $settings['address_parts'] ) && is_array( $settings['address_parts'] ) )
				? $settings['address_parts']
				: array();
		}

		if ( empty( $parts ) ) {
			return '';
		}

		// Build args in canonical order, keeping only the enabled parts.
		$address_args = array();
		foreach ( $all_parts as $part => $separator ) {
			if ( in_array( $part, $parts, true ) ) {
				$address_args[ $part ] = $separator;
			}
		}

		if ( empty( $address_args ) ) {
			return '';
		}

		// Strip the trailing separator on the final part.
		$keys     = array_keys( $address_args );
		$last_key = end( $keys );
		if ( $last_key ) {
			$address_args[ $last_key ] = '';
		}

		// Country is only output when explicitly selected.
		$show_country = isset( $address_args['country'] );

		$address = epl_get_the_address( array_keys( $address_args ), $address_args, $show_country );

		return trim( (string) $address );
	}
}
