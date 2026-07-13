<?php
/**
 * Elementor Property Image Widget
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
 * EPL_Elementor_Property_Image Class
 *
 * @since 3.6.0
 */
class EPL_Elementor_Property_Image extends \Elementor\Widget_Base {
	use EPL_Elementor_Dynamic_Widget;

	/**
	 * Get widget name.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'epl-property-image';
	}

	/**
	 * Get widget title.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'EPL Property Image', 'easy-property-listings' );
	}

	/**
	 * Get widget icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-image';
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
		return array( 'property', 'image', 'featured', 'photo', 'epl' );
	}

	/**
	 * Register widget controls.
	 */
	protected function register_controls() {

		// Content Section.
		$this->start_controls_section(
			'section_content',
			array(
				'label' => esc_html__( 'Image', 'easy-property-listings' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'image_size',
			array(
				'label'   => esc_html__( 'Image Size', 'easy-property-listings' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => array(
					'thumbnail'             => esc_html__( 'Thumbnail', 'easy-property-listings' ),
					'medium'                => esc_html__( 'Medium', 'easy-property-listings' ),
					'medium_large'          => esc_html__( 'Medium Large', 'easy-property-listings' ),
					'large'                 => esc_html__( 'Large', 'easy-property-listings' ),
					'full'                  => esc_html__( 'Full', 'easy-property-listings' ),
					'epl-image-medium-crop' => esc_html__( 'EPL Medium Crop', 'easy-property-listings' ),
				),
				'default' => 'large',
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
				'default'      => 'yes',
			)
		);

		$this->end_controls_section();

		// Stickers Section.
		$this->start_controls_section(
			'section_stickers',
			array(
				'label' => esc_html__( 'Stickers', 'easy-property-listings' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'show_stickers',
			array(
				'label'        => esc_html__( 'Show Status Stickers', 'easy-property-listings' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'easy-property-listings' ),
				'label_off'    => esc_html__( 'No', 'easy-property-listings' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'description'  => esc_html__( 'Show status stickers (Under Offer, Sold, Leased, etc.)', 'easy-property-listings' ),
			)
		);

		$this->add_control(
			'sticker_position',
			array(
				'label'     => esc_html__( 'Sticker Position', 'easy-property-listings' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'options'   => array(
					'top-left'     => esc_html__( 'Top Left', 'easy-property-listings' ),
					'top-right'    => esc_html__( 'Top Right', 'easy-property-listings' ),
					'bottom-left'  => esc_html__( 'Bottom Left', 'easy-property-listings' ),
					'bottom-right' => esc_html__( 'Bottom Right', 'easy-property-listings' ),
				),
				'default'   => 'top-left',
				'condition' => array(
					'show_stickers' => 'yes',
				),
			)
		);

		$this->end_controls_section();

		// Overlay Section.
		$this->start_controls_section(
			'section_overlay',
			array(
				'label' => esc_html__( 'Overlay', 'easy-property-listings' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'show_overlay',
			array(
				'label'        => esc_html__( 'Show Overlay on Hover', 'easy-property-listings' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'easy-property-listings' ),
				'label_off'    => esc_html__( 'No', 'easy-property-listings' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_control(
			'overlay_color',
			array(
				'label'     => esc_html__( 'Overlay Color', 'easy-property-listings' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => 'rgba(0,0,0,0.5)',
				'condition' => array(
					'show_overlay' => 'yes',
				),
				'selectors' => array(
					'{{WRAPPER}} .epl-property-image .epl-image-overlay' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'overlay_icon',
			array(
				'label'     => esc_html__( 'Overlay Icon', 'easy-property-listings' ),
				'type'      => \Elementor\Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'fas fa-search-plus',
					'library' => 'fa-solid',
				),
				'condition' => array(
					'show_overlay' => 'yes',
				),
			)
		);

		$this->add_control(
			'overlay_icon_color',
			array(
				'label'     => esc_html__( 'Overlay Icon Color', 'easy-property-listings' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'condition' => array(
					'show_overlay' => 'yes',
				),
				'selectors' => array(
					'{{WRAPPER}} .epl-property-image .epl-image-overlay i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .epl-property-image .epl-image-overlay svg' => 'fill: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		// Style Section - Image.
		$this->start_controls_section(
			'section_style_image',
			array(
				'label' => esc_html__( 'Image', 'easy-property-listings' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'image_width',
			array(
				'label'      => esc_html__( 'Width', 'easy-property-listings' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( '%', 'px', 'vw' ),
				'range'      => array(
					'%'  => array(
						'min' => 10,
						'max' => 100,
					),
					'px' => array(
						'min' => 50,
						'max' => 1000,
					),
					'vw' => array(
						'min' => 10,
						'max' => 100,
					),
				),
				'default'    => array(
					'size' => 100,
					'unit' => '%',
				),
				'selectors'  => array(
					'{{WRAPPER}} .epl-property-image img' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'image_height',
			array(
				'label'      => esc_html__( 'Height', 'easy-property-listings' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'vh', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 50,
						'max' => 800,
					),
					'vh' => array(
						'min' => 10,
						'max' => 100,
					),
					'%'  => array(
						'min' => 10,
						'max' => 200,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .epl-property-image img' => 'height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'object_fit',
			array(
				'label'     => esc_html__( 'Object Fit', 'easy-property-listings' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'options'   => array(
					'fill'       => esc_html__( 'Fill', 'easy-property-listings' ),
					'cover'      => esc_html__( 'Cover', 'easy-property-listings' ),
					'contain'    => esc_html__( 'Contain', 'easy-property-listings' ),
					'scale-down' => esc_html__( 'Scale Down', 'easy-property-listings' ),
					'none'       => esc_html__( 'None', 'easy-property-listings' ),
				),
				'default'   => 'cover',
				'selectors' => array(
					'{{WRAPPER}} .epl-property-image img' => 'object-fit: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'object_position',
			array(
				'label'     => esc_html__( 'Object Position', 'easy-property-listings' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'options'   => array(
					'center center' => esc_html__( 'Center Center', 'easy-property-listings' ),
					'center top'    => esc_html__( 'Center Top', 'easy-property-listings' ),
					'center bottom' => esc_html__( 'Center Bottom', 'easy-property-listings' ),
					'left center'   => esc_html__( 'Left Center', 'easy-property-listings' ),
					'right center'  => esc_html__( 'Right Center', 'easy-property-listings' ),
				),
				'default'   => 'center center',
				'selectors' => array(
					'{{WRAPPER}} .epl-property-image img' => 'object-position: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'image_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'easy-property-listings' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .epl-property-image, {{WRAPPER}} .epl-property-image img, {{WRAPPER}} .epl-property-image .epl-image-overlay, {{WRAPPER}} .epl-property-image a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			array(
				'name'     => 'image_border',
				'selector' => '{{WRAPPER}} .epl-property-image img',
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'image_box_shadow',
				'selector' => '{{WRAPPER}} .epl-property-image',
			)
		);

		$this->end_controls_section();

		// Style Section - Hover Effects.
		$this->start_controls_section(
			'section_style_hover',
			array(
				'label' => esc_html__( 'Hover Effects', 'easy-property-listings' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'enable_hover_effects',
			array(
				'label'        => esc_html__( 'Enable Hover Effects', 'easy-property-listings' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'easy-property-listings' ),
				'label_off'    => esc_html__( 'No', 'easy-property-listings' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_control(
			'image_hover_zoom',
			array(
				'label'      => esc_html__( 'Zoom', 'easy-property-listings' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( '%' ),
				'range'      => array( '%' => array( 'min' => 100, 'max' => 150 ) ),
				'default'    => array( 'size' => 105, 'unit' => '%' ),
				'condition'  => array( 'enable_hover_effects' => 'yes' ),
				'selectors'  => array( '{{WRAPPER}} .epl-property-image' => '--epl-image-hover-scale: {{SIZE}};' ),
			)
		);

		$this->add_control(
			'image_hover_blur',
			array(
				'label'      => esc_html__( 'Blur', 'easy-property-listings' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 20, 'step' => 0.1 ) ),
				'default'    => array( 'size' => 0, 'unit' => 'px' ),
				'condition'  => array( 'enable_hover_effects' => 'yes' ),
				'selectors'  => array( '{{WRAPPER}} .epl-property-image' => '--epl-image-hover-blur: {{SIZE}}{{UNIT}};' ),
			)
		);

		foreach (
			array(
				'brightness' => array( 'Brightness', 100, 0, 200 ),
				'contrast'   => array( 'Contrast', 100, 0, 200 ),
				'saturate'   => array( 'Saturation', 100, 0, 300 ),
				'grayscale'  => array( 'Grayscale', 0, 0, 100 ),
				'opacity'    => array( 'Opacity', 100, 0, 100 ),
			) as $filter => $control
		) {
			$this->add_control(
				'image_hover_' . $filter,
				array(
					'label'      => esc_html__( $control[0], 'easy-property-listings' ),
					'type'       => \Elementor\Controls_Manager::SLIDER,
					'size_units' => array( '%' ),
					'range'      => array( '%' => array( 'min' => $control[2], 'max' => $control[3] ) ),
					'default'    => array( 'size' => $control[1], 'unit' => '%' ),
					'condition'  => array( 'enable_hover_effects' => 'yes' ),
					'selectors'  => array( '{{WRAPPER}} .epl-property-image' => '--epl-image-hover-' . $filter . ': {{SIZE}}%;' ),
				)
			);
		}

		$this->add_control(
			'image_hover_transition',
			array(
				'label'      => esc_html__( 'Transition Duration', 'easy-property-listings' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 's' ),
				'range'      => array( 's' => array( 'min' => 0, 'max' => 3, 'step' => 0.05 ) ),
				'default'    => array( 'size' => 0.3, 'unit' => 's' ),
				'condition'  => array( 'enable_hover_effects' => 'yes' ),
				'selectors'  => array( '{{WRAPPER}} .epl-property-image' => '--epl-image-hover-duration: {{SIZE}}{{UNIT}};' ),
			)
		);

		$this->end_controls_section();

		// Style Section - Stickers.
		$this->start_controls_section(
			'section_style_stickers',
			array(
				'label'     => esc_html__( 'Stickers', 'easy-property-listings' ),
				'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => array(
					'show_stickers' => 'yes',
				),
			)
		);

		$this->add_control(
			'sticker_bg_color',
			array(
				'label'     => esc_html__( 'Background Color', 'easy-property-listings' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .epl-property-image .epl-sticker-wrapper .status-sticker' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'sticker_text_color',
			array(
				'label'     => esc_html__( 'Text Color', 'easy-property-listings' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .epl-property-image .epl-sticker-wrapper .status-sticker' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'sticker_typography',
				'selector' => '{{WRAPPER}} .epl-property-image .epl-sticker-wrapper .status-sticker',
			)
		);

		$this->add_responsive_control(
			'sticker_padding',
			array(
				'label'      => esc_html__( 'Padding', 'easy-property-listings' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .epl-property-image .epl-sticker-wrapper .status-sticker' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
				echo '<div class="epl-elementor-placeholder">' . esc_html__( 'Property Image - No listings found.', 'easy-property-listings' ) . '</div>';
			}
			EPL_Elementor::restore_listing_context();
			return;
		}

		$settings         = $this->get_settings_for_display();
		$image_size       = $settings['image_size'];
		$link             = 'yes' === $settings['link_to_listing'];
		$show_stickers    = 'yes' === $settings['show_stickers'];
		$sticker_position = $settings['sticker_position'];
		$show_overlay     = 'yes' === $settings['show_overlay'];
		$hover_effects    = 'yes' === $settings['enable_hover_effects'];

		$wrapper_class = 'epl-property-image epl-sticker-' . $sticker_position;
		if ( $show_overlay ) {
			$wrapper_class .= ' epl-has-overlay';
		}
		if ( $hover_effects ) {
			$wrapper_class .= ' epl-has-image-effects';
		}

		echo '<div class="' . esc_attr( $wrapper_class ) . '">';

		if ( has_post_thumbnail( $property->post->ID ) ) {
			if ( $link ) {
				echo '<a href="' . esc_url( get_permalink( $property->post->ID ) ) . '">';
			}

			echo get_the_post_thumbnail( $property->post->ID, $image_size );

			// Overlay.
			if ( $show_overlay ) {
				echo '<div class="epl-image-overlay">';
				if ( ! empty( $settings['overlay_icon']['value'] ) ) {
					\Elementor\Icons_Manager::render_icon( $settings['overlay_icon'], array( 'aria-hidden' => 'true' ) );
				}
				echo '</div>';
			}

			$this->render_stickers( $property, $show_stickers );

			if ( $link ) {
				echo '</a>';
			}
		} else {
			// No featured image placeholder. Stickers still apply.
			echo '<div class="epl-no-image">' . esc_html__( 'No image available', 'easy-property-listings' ) . '</div>';

			$this->render_stickers( $property, $show_stickers );
		}

		echo '</div>';

		EPL_Elementor::restore_listing_context();
	}

	/**
	 * Render status stickers via core, so New/Open/Sold/Leased/Under Offer
	 * rules, the sticker range setting and the epl_get_price_sticker filter
	 * all apply.
	 *
	 * @param EPL_Property_Meta $property      Property object.
	 * @param bool              $show_stickers Whether stickers are enabled.
	 */
	private function render_stickers( $property, $show_stickers ) {
		if ( ! $show_stickers ) {
			return;
		}

		$stickers = $property->get_price_sticker();

		if ( empty( $stickers ) ) {
			return;
		}

		echo '<div class="epl-sticker-wrapper">' . wp_kses_post( $stickers ) . '</div>';
	}
}
