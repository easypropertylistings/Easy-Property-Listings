<?php
/**
 * Elementor Property Status Widget
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
 * EPL_Elementor_Property_Status Class
 *
 * @since 3.6.0
 */
class EPL_Elementor_Property_Status extends \Elementor\Widget_Base {
	use EPL_Elementor_Dynamic_Widget;

	/**
	 * Get widget name.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'epl-property-status';
	}

	/**
	 * Get widget title.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Status', 'easy-property-listings' );
	}

	/**
	 * Get widget icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-post-info';
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
		return array( 'property', 'status', 'stickers', 'sold', 'new', 'epl' );
	}

	/**
	 * Register widget controls.
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_labels',
			array(
				'label' => esc_html__( 'Status Labels', 'easy-property-listings' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$labels = array(
			'current_sale_label'       => array( 'Current Sale', 'For Sale' ),
			'current_rental_label'     => array( 'Current Rental', 'For Lease' ),
			'current_com_sale_label'   => array( 'Current Commercial Sale', 'For Sale' ),
			'current_com_lease_label'  => array( 'Current Commercial Lease', 'For Lease' ),
			'current_com_both_label'   => array( 'Current Commercial Sale/Lease', 'For Sale / Lease' ),
			'under_offer_label'        => array( 'Under Offer', 'Under Offer' ),
			'sold_label'               => array( 'Sold', 'Sold' ),
			'leased_label'             => array( 'Leased', 'Leased' ),
			'withdrawn_label'          => array( 'Withdrawn', 'Withdrawn' ),
			'offmarket_label'          => array( 'Off Market', 'Off Market' ),
			'deleted_label'            => array( 'Deleted', 'Deleted' ),
			'fallback_label'           => array( 'Unknown Status', '' ),
		);

		foreach ( $labels as $control => $label ) {
			$this->add_control(
				$control,
				array(
					'label'   => esc_html__( $label[0], 'easy-property-listings' ),
					'type'    => \Elementor\Controls_Manager::TEXT,
					'default' => esc_html__( $label[1], 'easy-property-listings' ),
				)
			);
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

		$this->add_responsive_control(
			'margin',
			array(
				'label'      => esc_html__( 'Margin', 'easy-property-listings' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .epl-property-status' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'text_color',
			array(
				'label'     => esc_html__( 'Text Color', 'easy-property-listings' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .epl-property-status-label' => 'color: {{VALUE}};' ),
			)
		);

		$this->add_control(
			'background_color',
			array(
				'label'     => esc_html__( 'Default Background', 'easy-property-listings' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .epl-property-status-label' => 'background-color: {{VALUE}};' ),
			)
		);

		$this->add_control( 'background_heading', array( 'label' => esc_html__( 'Status Backgrounds', 'easy-property-listings' ), 'type' => \Elementor\Controls_Manager::HEADING, 'separator' => 'before' ) );
		$backgrounds = array(
			'current_sale_background'      => array( 'Current Sale', 'current-sale' ),
			'current_rental_background'    => array( 'Current Rental', 'current-rental' ),
			'current_com_sale_background'  => array( 'Commercial Sale', 'current-commercial-sale' ),
			'current_com_lease_background' => array( 'Commercial Lease', 'current-commercial-lease' ),
			'current_com_both_background'  => array( 'Commercial Sale/Lease', 'current-commercial-both' ),
			'under_offer_background'       => array( 'Under Offer', 'under-offer' ),
			'sold_background'              => array( 'Sold', 'sold' ),
			'leased_background'            => array( 'Leased', 'leased' ),
			'withdrawn_background'         => array( 'Withdrawn', 'withdrawn' ),
			'offmarket_background'         => array( 'Off Market', 'offmarket' ),
			'deleted_background'           => array( 'Deleted', 'deleted' ),
		);
		foreach ( $backgrounds as $control => $background ) {
			$this->add_control(
				$control,
				array(
					'label'     => esc_html( $background[0] ),
					'type'      => \Elementor\Controls_Manager::COLOR,
					'selectors' => array( '{{WRAPPER}} .epl-property-status-kind-' . $background[1] . ' .epl-property-status-label' => 'background-color: {{VALUE}};' ),
				)
			);
		}

		$this->add_responsive_control(
			'padding',
			array(
				'label'      => esc_html__( 'Padding', 'easy-property-listings' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array( '{{WRAPPER}} .epl-property-status-label' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
			)
		);

		$this->add_responsive_control(
			'border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'easy-property-listings' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array( '{{WRAPPER}} .epl-property-status-label' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'typography',
				'selector' => '{{WRAPPER}} .epl-property-status-label',
			)
		);

		$this->add_responsive_control(
			'align',
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
					'{{WRAPPER}} .epl-property-status' => 'text-align: {{VALUE}};',
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
				echo '<div class="epl-elementor-placeholder">' . esc_html__( 'Property Status - No listings found.', 'easy-property-listings' ) . '</div>';
			}
			EPL_Elementor::restore_listing_context();
			return;
		}

		$settings = $this->get_settings_for_display();
		$status   = sanitize_key( (string) $property->get_property_meta( 'property_status' ) );
		$status   = $status ? $status : 'current';
		$label    = '';
		$kind     = $status;

		if ( 'current' === $status && 'yes' === $property->get_property_meta( 'property_under_offer' ) ) {
			$label = $settings['under_offer_label'];
			$kind  = 'under-offer';
		} elseif ( 'current' === $status ) {
			$post_type = $property->post->post_type;
			if ( 'rental' === $post_type ) {
				$label = $settings['current_rental_label'];
				$kind  = 'current-rental';
			} elseif ( in_array( $post_type, array( 'commercial', 'commercial_land', 'business' ), true ) ) {
				$listing_type = $property->get_property_meta( 'property_com_listing_type' );
				if ( 'lease' === $listing_type ) {
					$label = $settings['current_com_lease_label'];
					$kind  = 'current-commercial-lease';
				} elseif ( 'both' === $listing_type ) {
					$label = $settings['current_com_both_label'];
					$kind  = 'current-commercial-both';
				} else {
					$label = $settings['current_com_sale_label'];
					$kind  = 'current-commercial-sale';
				}
			} else {
				$label = $settings['current_sale_label'];
				$kind  = 'current-sale';
			}
		} else {
			$key   = $status . '_label';
			$label = isset( $settings[ $key ] ) ? $settings[ $key ] : $settings['fallback_label'];
		}

		$label = apply_filters( 'epl_elementor_property_status_label', $label, $status, $property, $settings );
		if ( '' !== trim( (string) $label ) ) {
			echo '<div class="epl-property-status epl-property-status-' . esc_attr( $status ) . ' epl-property-status-kind-' . esc_attr( $kind ) . '"><span class="epl-property-status-label">' . esc_html( $label ) . '</span></div>';
		}

		EPL_Elementor::restore_listing_context();
	}
}
