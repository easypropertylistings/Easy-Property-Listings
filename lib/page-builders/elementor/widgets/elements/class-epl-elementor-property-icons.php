<?php
/**
 * Elementor Property Icons Widget
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
 * EPL_Elementor_Property_Icons Class
 *
 * Displays property icons with sortable, customizable icon list.
 *
 * @since 3.6.0
 */
class EPL_Elementor_Property_Icons extends \Elementor\Widget_Base {
	use EPL_Elementor_Dynamic_Widget;

	/**
	 * Get widget name.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'epl-property-icons';
	}

	/**
	 * Get widget title.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'EPL Property Icons', 'easy-property-listings' );
	}

	/**
	 * Get widget icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-info-circle-o';
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
		return array( 'property', 'icons', 'bed', 'bath', 'parking', 'epl' );
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
					'horizontal' => esc_html__( 'Horizontal', 'easy-property-listings' ),
					'vertical'   => esc_html__( 'Vertical', 'easy-property-listings' ),
				),
				'default' => 'horizontal',
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
				'default'      => '',
				'description'  => esc_html__( 'Show text labels (e.g., "Beds", "Baths")', 'easy-property-listings' ),
			)
		);

		$this->add_control(
			'divider_switch',
			array(
				'label'        => esc_html__( 'Divider', 'easy-property-listings' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'easy-property-listings' ),
				'label_off'    => esc_html__( 'Hide', 'easy-property-listings' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_control(
			'divider_text',
			array(
				'label'     => esc_html__( 'Divider Character', 'easy-property-listings' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'default'   => '|',
				'condition' => array(
					'divider_switch' => 'yes',
				),
			)
		);

		$this->end_controls_section();

		// Content Section - Icons List (Repeater).
		$this->start_controls_section(
			'section_icons',
			array(
				'label' => esc_html__( 'Icons', 'easy-property-listings' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'icon_type',
			array(
				'label'   => esc_html__( 'Data Type', 'easy-property-listings' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => array(
					'bed'      => esc_html__( 'Bedrooms', 'easy-property-listings' ),
					'bath'     => esc_html__( 'Bathrooms', 'easy-property-listings' ),
					'parking'  => esc_html__( 'Parking', 'easy-property-listings' ),
					'land'     => esc_html__( 'Land Size', 'easy-property-listings' ),
					'building' => esc_html__( 'Building Size', 'easy-property-listings' ),
					'rooms'    => esc_html__( 'Rooms', 'easy-property-listings' ),
					'pool'     => esc_html__( 'Pool', 'easy-property-listings' ),
					'ac'       => esc_html__( 'Air Conditioning', 'easy-property-listings' ),
				),
				'default' => 'bed',
			)
		);

		$repeater->add_control(
			'selected_icon',
			array(
				'label'   => esc_html__( 'Icon', 'easy-property-listings' ),
				'type'    => \Elementor\Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'fas fa-bed',
					'library' => 'fa-solid',
				),
			)
		);

		$repeater->add_control(
			'custom_label',
			array(
				'label'       => esc_html__( 'Custom Label', 'easy-property-listings' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => '',
				'placeholder' => esc_html__( 'Leave empty for default', 'easy-property-listings' ),
			)
		);

		$repeater->add_control(
			'hide_if_empty',
			array(
				'label'        => esc_html__( 'Hide if Empty', 'easy-property-listings' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'easy-property-listings' ),
				'label_off'    => esc_html__( 'No', 'easy-property-listings' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'icon_list',
			array(
				'label'       => esc_html__( 'Icon List', 'easy-property-listings' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => array(
					array(
						'icon_type'     => 'bed',
						'selected_icon' => array(
							'value'   => 'fas fa-bed',
							'library' => 'fa-solid',
						),
						'custom_label'  => '',
						'hide_if_empty' => 'yes',
					),
					array(
						'icon_type'     => 'bath',
						'selected_icon' => array(
							'value'   => 'fas fa-bath',
							'library' => 'fa-solid',
						),
						'custom_label'  => '',
						'hide_if_empty' => 'yes',
					),
					array(
						'icon_type'     => 'parking',
						'selected_icon' => array(
							'value'   => 'fas fa-car',
							'library' => 'fa-solid',
						),
						'custom_label'  => '',
						'hide_if_empty' => 'yes',
					),
				),
				'title_field' => '<# var iconLabels = ' . wp_json_encode(
					array(
						'bed'      => esc_html__( 'Bedrooms', 'easy-property-listings' ),
						'bath'     => esc_html__( 'Bathrooms', 'easy-property-listings' ),
						'parking'  => esc_html__( 'Parking', 'easy-property-listings' ),
						'land'     => esc_html__( 'Land Size', 'easy-property-listings' ),
						'building' => esc_html__( 'Building Size', 'easy-property-listings' ),
						'rooms'    => esc_html__( 'Rooms', 'easy-property-listings' ),
						'pool'     => esc_html__( 'Pool', 'easy-property-listings' ),
						'ac'       => esc_html__( 'Air Conditioning', 'easy-property-listings' ),
					)
				) . '; #>{{{ iconLabels[icon_type] || icon_type }}}',
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
			'icon_color',
			array(
				'label'     => esc_html__( 'Icon Color', 'easy-property-listings' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .epl-property-icons .epl-icon-item i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .epl-property-icons .epl-icon-item svg' => 'fill: {{VALUE}};',
				),
				'condition' => array(
					'show_icons' => 'yes',
				),
			)
		);

		$this->add_control(
			'text_color',
			array(
				'label'     => esc_html__( 'Value/Text Color', 'easy-property-listings' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .epl-property-icons .epl-icon-item' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'divider_style_heading',
			array(
				'label'     => esc_html__( 'Divider', 'easy-property-listings' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'divider_switch' => 'yes',
				),
			)
		);

		$this->add_control(
			'divider_color',
			array(
				'label'     => esc_html__( 'Divider Color', 'easy-property-listings' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .epl-property-icons .epl-icon-divider' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'divider_switch' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'divider_size',
			array(
				'label'      => esc_html__( 'Divider Size', 'easy-property-listings' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => 10,
						'max' => 50,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .epl-property-icons .epl-icon-divider' => 'font-size: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'divider_switch' => 'yes',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'typography',
				'selector' => '{{WRAPPER}} .epl-property-icons .epl-icon-item, {{WRAPPER}} .epl-property-icons .epl-icon-divider',
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'icon_size',
			array(
				'label'      => esc_html__( 'Icon Size', 'easy-property-listings' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => 10,
						'max' => 100,
					),
				),
				'default'    => array(
					'size' => 16,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .epl-property-icons .epl-icon-item i' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .epl-property-icons .epl-icon-item svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'show_icons' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'icon_gap',
			array(
				'label'      => esc_html__( 'Content Gap', 'easy-property-listings' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 30,
					),
				),
				'default'    => array(
					'size' => 5,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .epl-property-icons .epl-icon-item' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'item_spacing',
			array(
				'label'      => esc_html__( 'Spacing Between Items', 'easy-property-listings' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'default'    => array(
					'size' => 15,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .epl-property-icons' => 'gap: {{SIZE}}{{UNIT}};',
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
						'max' => 200,
					),
				),
				'default'    => array(
					'size' => 25,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .epl-property-icons' => 'min-height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'alignment',
			array(
				'label'     => esc_html__( 'Alignment', 'easy-property-listings' ),
				'type'      => \Elementor\Controls_Manager::CHOOSE,
				'options'   => array(
					'flex-start' => array(
						'title' => esc_html__( 'Left', 'easy-property-listings' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center'     => array(
						'title' => esc_html__( 'Center', 'easy-property-listings' ),
						'icon'  => 'eicon-text-align-center',
					),
					'flex-end'   => array(
						'title' => esc_html__( 'Right', 'easy-property-listings' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .epl-property-icons' => 'justify-content: {{VALUE}};',
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
				echo '<div class="epl-elementor-placeholder">' . esc_html__( 'Property Icons - No listings found.', 'easy-property-listings' ) . '</div>';
			}
			EPL_Elementor::restore_listing_context();
			return;
		}

		$settings      = $this->get_settings_for_display();
		$layout_class  = 'horizontal' === $settings['layout'] ? 'epl-icons-horizontal' : 'epl-icons-vertical';
		$show_labels   = 'yes' === $settings['show_labels'];
		$show_icons    = 'yes' === $settings['show_icons'];
		$show_divider  = 'yes' === $settings['divider_switch'];
		$divider_char  = ! empty( $settings['divider_text'] ) ? $settings['divider_text'] : '|';
		$icon_list     = $settings['icon_list'];

		if ( empty( $icon_list ) ) {
			EPL_Elementor::restore_listing_context();
			return;
		}

		echo '<div class="epl-property-icons ' . esc_attr( $layout_class ) . '">';

		$visible_items = array();

		// First, filter and collect valid items to know count for divider logic.
		foreach ( $icon_list as $index => $item ) {
			$icon_data = $this->get_icon_data( $property, $item['icon_type'] );

			// Skip if value is empty and hide_if_empty is set.
			if ( 'yes' === $item['hide_if_empty'] && empty( $icon_data['value'] ) ) {
				continue;
			}

			$item['data'] = $icon_data;
			$visible_items[] = $item;
		}

		$total_visible = count( $visible_items );

		foreach ( $visible_items as $index => $item ) {
			$icon_data = $item['data'];
			$value     = $icon_data['value'];
			$label     = ! empty( $item['custom_label'] ) ? $item['custom_label'] : $icon_data['label'];

			echo '<span class="epl-icon-item elementor-repeater-item-' . esc_attr( $item['_id'] ) . '">';

			// Render icon if enabled.
			if ( $show_icons && ! empty( $item['selected_icon']['value'] ) ) {
				\Elementor\Icons_Manager::render_icon( $item['selected_icon'], array( 'aria-hidden' => 'true' ) );
			}

			// Render value.
			echo '<span class="epl-icon-value">' . esc_html( $value ) . '</span>';

			// Render label if enabled.
			if ( $show_labels && ! empty( $label ) ) {
				echo '<span class="epl-icon-label">' . esc_html( $label ) . '</span>';
			}

			echo '</span>';

			// Render Divider (if not last item and enabled).
			if ( $show_divider && $index < ( $total_visible - 1 ) ) {
				echo '<span class="epl-icon-divider" aria-hidden="true">' . esc_html( $divider_char ) . '</span>';
			}
		}

		echo '</div>';

		EPL_Elementor::restore_listing_context();
	}

	/**
	 * Get icon data based on type.
	 *
	 * @param EPL_Property_Meta $property Property object.
	 * @param string            $type     Icon type.
	 * @return array Array with 'value' and 'label' keys.
	 */
	private function get_icon_data( $property, $type ) {
		$data = array(
			'value' => '',
			'label' => '',
		);

		switch ( $type ) {
			case 'bed':
				$data['value'] = $property->get_property_meta( 'property_bedrooms' );
				$data['label'] = esc_html__( 'Beds', 'easy-property-listings' );
				break;

			case 'bath':
				$data['value'] = $property->get_property_meta( 'property_bathrooms' );
				$data['label'] = esc_html__( 'Baths', 'easy-property-listings' );
				break;

			case 'parking':
				$garage  = intval( $property->get_property_meta( 'property_garage' ) );
				$carport = intval( $property->get_property_meta( 'property_carport' ) );
				$data['value'] = $garage + $carport;
				$data['label'] = esc_html__( 'Parking', 'easy-property-listings' );
				break;

			case 'land':
				$land_value = $property->get_property_meta( 'property_land_area' );
				$land_unit  = $this->format_area_unit( $property->get_property_meta( 'property_land_area_unit' ), $land_value );
				$land_unit  = apply_filters( 'epl_property_land_area_unit_label', $land_unit );
				$data['value'] = ! empty( $land_value ) ? $land_value . ( ! empty( $land_unit ) ? ' ' . $land_unit : '' ) : '';
				$data['label'] = esc_html__( 'Land', 'easy-property-listings' );
				break;

			case 'building':
				$building_value = $property->get_property_meta( 'property_building_area' );
				$building_unit  = $this->format_area_unit( $property->get_property_meta( 'property_building_area_unit' ), $building_value );
				$building_unit  = apply_filters( 'epl_property_building_area_unit_label', $building_unit );
				$data['value'] = ! empty( $building_value ) ? $building_value . ( ! empty( $building_unit ) ? ' ' . $building_unit : '' ) : '';
				$data['label'] = esc_html__( 'Building', 'easy-property-listings' );
				break;

			case 'rooms':
				$data['value'] = $property->get_property_meta( 'property_rooms' );
				$data['label'] = esc_html__( 'Rooms', 'easy-property-listings' );
				break;

			case 'pool':
				$pool = strtolower( (string) $property->get_property_meta( 'property_pool' ) );
				$data['value'] = ( '1' === $pool || epl_value_bool_checker( $pool ) ) ? esc_html__( 'Yes', 'easy-property-listings' ) : '';
				$data['label'] = esc_html__( 'Pool', 'easy-property-listings' );
				break;

			case 'ac':
				$ac = strtolower( (string) $property->get_property_meta( 'property_air_conditioning' ) );
				$data['value'] = ( '1' === $ac || epl_value_bool_checker( $ac ) ) ? esc_html__( 'Yes', 'easy-property-listings' ) : '';
				$data['label'] = esc_html__( 'A/C', 'easy-property-listings' );
				break;
		}

		return $data;
	}

	/**
	 * Convert a stored area unit key to its display label, matching core
	 * behaviour in EPL_Property_Meta::get_property_land_value().
	 *
	 * @param string $unit  Stored unit key (e.g. squareMeter, acre, sqft).
	 * @param mixed  $value Area value, used for pluralisation.
	 * @return string
	 */
	private function format_area_unit( $unit, $value ) {
		$unit = (string) $unit;

		if ( 'squareMeter' === $unit ) {
			$unit = html_entity_decode( __( 'm&#178;', 'easy-property-listings' ), ENT_HTML5, 'UTF-8' );
		} elseif ( 'acre' === $unit ) {
			$unit = floatval( $value ) > 1 ? __( 'acres', 'easy-property-listings' ) : __( 'acre', 'easy-property-listings' );
		}

		return $unit;
	}
}
