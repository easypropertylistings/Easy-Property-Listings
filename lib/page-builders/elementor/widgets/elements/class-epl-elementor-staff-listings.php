<?php
/**
 * Elementor Staff Listings Widget.
 *
 * @package     EPL
 * @subpackage  PageBuilders/Elementor/Widgets/Elements
 * @since       3.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Displays the Staff Directory extension's listings for the current staff member. */
class EPL_Elementor_Staff_Listings extends \Elementor\Widget_Base {
	use EPL_Elementor_Dynamic_Widget;

	public function get_name() {
		return 'epl-staff-listings';
	}

	public function get_title() {
		return esc_html__( 'EPL Staff Listings', 'easy-property-listings' );
	}

	public function get_icon() {
		return 'eicon-posts-grid';
	}

	public function get_categories() {
		return array( 'epl-staff' );
	}

	public function get_keywords() {
		return array( 'staff', 'agent', 'listings', 'properties', 'tabs', 'directory', 'epl' );
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_content',
			array(
				'label' => esc_html__( 'Staff Listings', 'easy-property-listings' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'display_mode',
			array(
				'label'   => esc_html__( 'Layout', 'easy-property-listings' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => '',
				'options' => array(
					''        => esc_html__( 'Staff Directory Setting', 'easy-property-listings' ),
					'list'    => esc_html__( 'Combined List', 'easy-property-listings' ),
					'tabbed'  => esc_html__( 'Tabbed', 'easy-property-listings' ),
					'section' => esc_html__( 'Separate Sections', 'easy-property-listings' ),
				),
			)
		);

		$this->add_control(
			'tab_style',
			array(
				'label'     => esc_html__( 'Tab Style', 'easy-property-listings' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'default'   => '',
				'options'   => array(
					''           => esc_html__( 'Staff Directory Setting', 'easy-property-listings' ),
					'horizontal' => esc_html__( 'Horizontal', 'easy-property-listings' ),
					'vertical'   => esc_html__( 'Vertical', 'easy-property-listings' ),
				),
				'condition' => array( 'display_mode' => array( '', 'tabbed' ) ),
			)
		);

		$this->add_control(
			'custom_title',
			array(
				'label'       => esc_html__( 'Title', 'easy-property-listings' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Use Staff Directory setting', 'easy-property-listings' ),
				'label_block' => true,
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_title_style',
			array(
				'label' => esc_html__( 'Headings', 'easy-property-listings' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_control(
			'title_color',
			array(
				'label'     => esc_html__( 'Color', 'easy-property-listings' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .epl-sd-section-title, {{WRAPPER}} .epl-section__title' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .epl-sd-section-title, {{WRAPPER}} .epl-section__title',
			)
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'section_tabs_style',
			array(
				'label' => esc_html__( 'Tabs', 'easy-property-listings' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_control(
			'tab_text_color',
			array(
				'label'     => esc_html__( 'Text Color', 'easy-property-listings' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} [class*="epl-sd-tabs-"] > ul a' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'tab_background',
			array(
				'label'     => esc_html__( 'Background', 'easy-property-listings' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} [class*="epl-sd-tabs-"] > ul a' => 'background-color: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'tab_active_color',
			array(
				'label'     => esc_html__( 'Active Text Color', 'easy-property-listings' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} [class*="epl-sd-tabs-"] > ul .ui-tabs-active a' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'tab_active_background',
			array(
				'label'     => esc_html__( 'Active Background', 'easy-property-listings' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} [class*="epl-sd-tabs-"] > ul .ui-tabs-active a' => 'background-color: {{VALUE}};' ),
			)
		);
		$this->end_controls_section();
	}

	protected function render() {
		if ( ! class_exists( 'EPL_SD_Listings' ) ) {
			$this->render_placeholder( esc_html__( 'The EPL Staff Directory extension is required.', 'easy-property-listings' ) );
			return;
		}

		global $epl_author;
		$property = EPL_Elementor::setup_listing_context();
		$author   = $property ? EPL_Elementor::get_listing_agent( $property ) : null;

		if ( ! $author ) {
			EPL_Elementor::restore_listing_context();
			$this->render_placeholder( esc_html__( 'Select a staff member as the preview context.', 'easy-property-listings' ) );
			return;
		}

		$settings   = $this->get_settings_for_display();
		$epl_author = $author; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- Required by Staff Directory's renderer and restored below.
		$title      = isset( $settings['custom_title'] ) ? trim( (string) $settings['custom_title'] ) : '';
		$filter     = static function ( $existing ) use ( $title ) {
			return '' !== $title ? $title : $existing;
		};

		if ( '' !== $title ) {
			add_filter( 'epl_sd_single_listing_title', $filter );
		}

		echo '<div class="epl-elementor-staff-listings">';
		EPL_SD_Listings::instance()->render_single_staff_listings( $settings['display_mode'], $settings['tab_style'] );
		echo '</div>';

		if ( '' !== $title ) {
			remove_filter( 'epl_sd_single_listing_title', $filter );
		}
		EPL_Elementor::restore_listing_context();
	}

	private function render_placeholder( $message ) {
		if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
			echo '<div class="epl-elementor-placeholder">' . esc_html( $message ) . '</div>';
		}
	}
}
