<?php
/**
 * Elementor Agent Social Widget
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
 * EPL_Elementor_Agent_Social Class
 *
 * Displays agent social media links.
 * Reads from $epl_current_agent context.
 *
 * @since 3.6.0
 */
class EPL_Elementor_Agent_Social extends \Elementor\Widget_Base {
	use EPL_Elementor_Dynamic_Widget;

	/**
	 * Get widget name.
	 */
	public function get_name() {
		return 'epl-agent-social';
	}

	/**
	 * Get widget title.
	 */
	public function get_title() {
		return esc_html__( 'EPL Agent Social', 'easy-property-listings' );
	}

	/**
	 * Get widget icon.
	 */
	public function get_icon() {
		return 'eicon-social-icons';
	}

	/**
	 * Get widget categories.
	 */
	public function get_categories() {
		return array( 'epl-staff', 'epl-elements' );
	}

	/**
	 * Get widget keywords.
	 */
	public function get_keywords() {
		return array( 'agent', 'social', 'facebook', 'twitter', 'linkedin', 'epl' );
	}

	/**
	 * Register widget controls.
	 */
	protected function register_controls() {

		$this->start_controls_section(
			'section_content',
			array(
				'label' => esc_html__( 'Social Icons', 'easy-property-listings' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'social_network',
			array(
				'label'   => esc_html__( 'Network', 'easy-property-listings' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'facebook',
				'options' => array(
					'facebook'  => 'Facebook',
					'twitter'   => 'Twitter',
					'instagram' => 'Instagram',
					'linkedin'  => 'LinkedIn',
					'youtube'   => 'YouTube',
					'pinterest' => 'Pinterest',
					'website'   => 'Website',
				),
			)
		);

		$repeater->add_control(
			'social_icon',
			array(
				'label' => esc_html__( 'Icon', 'easy-property-listings' ),
				'type'  => \Elementor\Controls_Manager::ICONS,
				'default' => array(
					'value' => 'fab fa-facebook',
					'library' => 'fa-brands',
				),
			)
		);

		$repeater->add_control(
			'item_icon_color',
			array(
				'label'     => esc_html__( 'Color', 'easy-property-listings' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} {{CURRENT_ITEM}} i' => 'color: {{VALUE}}',
					'{{WRAPPER}} {{CURRENT_ITEM}} svg' => 'fill: {{VALUE}}',
				),
			)
		);

		$repeater->add_control(
			'item_icon_hover_color',
			array(
				'label'     => esc_html__( 'Hover Color', 'easy-property-listings' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} {{CURRENT_ITEM}}:hover i' => 'color: {{VALUE}}',
					'{{WRAPPER}} {{CURRENT_ITEM}}:hover svg' => 'fill: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'social_icon_list',
			array(
				'label'       => esc_html__( 'Social Icons', 'easy-property-listings' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => array(
					array(
						'social_network' => 'facebook',
						'social_icon'    => array( 'value' => 'fab fa-facebook', 'library' => 'fa-brands' ),
					),
					array(
						'social_network' => 'twitter',
						'social_icon'    => array( 'value' => 'fab fa-twitter', 'library' => 'fa-brands' ),
					),
					array(
						'social_network' => 'instagram',
						'social_icon'    => array( 'value' => 'fab fa-instagram', 'library' => 'fa-brands' ),
					),
					array(
						'social_network' => 'linkedin',
						'social_icon'    => array( 'value' => 'fab fa-linkedin', 'library' => 'fa-brands' ),
					),
				),
				'title_field' => '{{{ social_network }}}',
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
				'label'     => esc_html__( 'Primary Color', 'easy-property-listings' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .epl-agent-social a i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .epl-agent-social a svg' => 'fill: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'icon_hover_color',
			array(
				'label'     => esc_html__( 'Primary Hover Color', 'easy-property-listings' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .epl-agent-social a:hover i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .epl-agent-social a:hover svg' => 'fill: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'icon_size',
			array(
				'label'      => esc_html__( 'Icon Size', 'easy-property-listings' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'range'      => array(
					'px' => array(
						'min' => 10,
						'max' => 100,
					),
				),
				'default'    => array(
					'size' => 20,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .epl-agent-social a' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .epl-agent-social a svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'icon_gap',
			array(
				'label'      => esc_html__( 'Gap Between Icons', 'easy-property-listings' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'default'    => array(
					'size' => 10,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .epl-agent-social' => 'gap: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .epl-agent-social' => 'justify-content: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render widget output.
	 */
	public function get_style_depends() {
		return array( 'elementor-icons-fa-brands', 'elementor-icons-fa-solid' );
	}

	/**
	 * Render widget output.
	 */
	protected function render() {
		$author_loader = $this->get_current_agent();

		if ( ! $author_loader ) {
			if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
				echo '<div class="epl-elementor-placeholder">' . esc_html__( 'Agent Social - No agent context.', 'easy-property-listings' ) . '</div>';
			}
			return;
		}

		// Access the inner object if available to ensure method checks work.
		$author = ( isset( $author_loader->object ) ) ? $author_loader->object : $author_loader;

		$settings = $this->get_settings_for_display();

		if ( empty( $settings['social_icon_list'] ) ) {
			return;
		}

		echo '<div class="epl-agent-social">';

		foreach ( $settings['social_icon_list'] as $index => $item ) {
			$network = $item['social_network'];
			$method  = 'get_' . $network;

			// Check if URL exists for this agent.
			if ( method_exists( $author, $method ) ) {
				$url = call_user_func( array( $author, $method ) );

				if ( ! empty( $url ) ) {
					$label = ucfirst( $network );

					// Add Repeater element attributes for styling matching {{CURRENT_ITEM}}.
					$repeater_setting_key = $this->get_repeater_setting_key( 'social_icon', 'social_icon_list', $index );
					$this->add_render_attribute( $repeater_setting_key, 'class', 'epl-social-icon' );
					$this->add_render_attribute( $repeater_setting_key, 'class', 'epl-social-' . esc_attr( $network ) );
					$this->add_render_attribute( $repeater_setting_key, 'class', 'elementor-repeater-item-' . $item['_id'] );
					$this->add_render_attribute( $repeater_setting_key, 'href', esc_url( $url ) );
					$this->add_render_attribute( $repeater_setting_key, 'target', '_blank' );
					$this->add_render_attribute( $repeater_setting_key, 'rel', 'noopener noreferrer' );
					$this->add_render_attribute( $repeater_setting_key, 'aria-label', esc_attr( $label ) );

					?>
					<a <?php echo $this->get_render_attribute_string( $repeater_setting_key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
						<?php \Elementor\Icons_Manager::render_icon( $item['social_icon'], array( 'aria-hidden' => 'true' ) ); ?>
					</a>
					<?php
				}
			}
		}

		echo '</div>';
	}

	/**
	 * Get the current agent from context or fallback.
	 *
	 * @return EPL_Author_Loader|null
	 */
	private function get_current_agent() {
		global $epl_current_agent;

		if ( ! empty( $epl_current_agent ) ) {
			return $epl_current_agent;
		}

		$property = EPL_Elementor::setup_listing_context();

		if ( $property ) {
			$author = EPL_Elementor::get_listing_agent( $property );
			EPL_Elementor::restore_listing_context();
			return $author;
		}

		EPL_Elementor::restore_listing_context();
		return null;
	}
}
