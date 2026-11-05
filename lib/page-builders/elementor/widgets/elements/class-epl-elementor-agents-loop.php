<?php
/**
 * Elementor Listing Agents Widget
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
 * EPL_Elementor_Listing_Agents Class
 *
 * Renders agents assigned to a listing with a dedicated EPL Agent Template.
 *
 * @since 3.6.0
 */
class EPL_Elementor_Listing_Agents extends \Elementor\Widget_Base {
	use EPL_Elementor_Dynamic_Widget;

	/**
	 * Get widget name.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'epl-listing-agents';
	}

	/**
	 * Get widget title.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'EPL Listing Agents', 'easy-property-listings' );
	}

	/**
	 * Get widget icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-person';
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
		return array( 'agent', 'agents', 'author', 'listing', 'template', 'epl' );
	}

	/**
	 * Template IDs currently being rendered, used as a recursion guard.
	 *
	 * @var array
	 */
	private static $rendering_templates = array();

	/**
	 * Get available Elementor templates.
	 *
	 * @return array
	 */
	private function get_elementor_templates() {
		$templates = array(
			'' => esc_html__( '— Select Template —', 'easy-property-listings' ),
		);

		$args = array(
			'post_type'      => 'elementor_library',
			'posts_per_page' => -1,
			'post_status'    => 'publish',
			'orderby'        => 'title',
			'order'          => 'ASC',
			'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				array(
					'key'     => '_elementor_template_type',
					'value'   => EPL_Elementor_Agent_Document::TYPE,
					'compare' => '=',
				),
			),
		);

		$template_query = new \WP_Query( $args );

		if ( $template_query->have_posts() ) {
			foreach ( $template_query->posts as $post ) {
				$templates[ $post->ID ] = $post->post_title;
			}
		}

		return $templates;
	}

	/**
	 * Register widget controls.
	 */
	protected function register_controls() {

		// Template Section.
		$this->start_controls_section(
			'section_template',
			array(
				'label' => esc_html__( 'Template', 'easy-property-listings' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'render_type',
			array(
				'label'   => esc_html__( 'Render Type', 'easy-property-listings' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => array(
					'default'  => esc_html__( 'Default Agent Box', 'easy-property-listings' ),
					'template' => esc_html__( 'Elementor Template', 'easy-property-listings' ),
				),
				'default' => 'default',
			)
		);

		$this->add_control(
			'template_id',
			array(
				'label'       => esc_html__( 'Agent Template', 'easy-property-listings' ),
				'type'        => \Elementor\Controls_Manager::SELECT,
				'options'     => $this->get_elementor_templates(),
				'default'     => '',
				'description' => esc_html__( 'Only dedicated EPL Agent Templates can be used here.', 'easy-property-listings' ),
				'condition'   => array(
					'render_type' => 'template',
				),
			)
		);

		$this->add_control(
			'template_help',
			array(
				'type'            => \Elementor\Controls_Manager::RAW_HTML,
				'raw'             => sprintf(
					'<p style="color:#666;font-size:12px;">%s <a href="%s" target="_blank">%s</a></p>',
					esc_html__( 'No template?', 'easy-property-listings' ),
					esc_url( \Elementor\Plugin::$instance->documents->get_create_new_post_url( 'elementor_library', EPL_Elementor_Agent_Document::TYPE ) ),
					esc_html__( 'Create an EPL Agent Template', 'easy-property-listings' )
				),
				'content_classes' => 'elementor-panel-alert',
				'condition'       => array(
					'render_type' => 'template',
				),
			)
		);

		$this->end_controls_section();

		// Layout Section.
		$this->start_controls_section(
			'section_layout',
			array(
				'label' => esc_html__( 'Layout', 'easy-property-listings' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'agents_count',
			array(
				'label'   => esc_html__( 'Max Agents', 'easy-property-listings' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'min'     => 1,
				'max'     => 10,
				'step'    => 1,
				'default' => 4,
			)
		);

		$this->add_control(
			'display_mode',
			array(
				'label'   => esc_html__( 'Display Mode', 'easy-property-listings' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => array(
					'normal' => esc_html__( 'Normal (Grid/List)', 'easy-property-listings' ),
					'tabbed' => esc_html__( 'Tabbed', 'easy-property-listings' ),
				),
				'default' => 'normal',
			)
		);

		// Normal mode controls.
		$this->add_control(
			'layout',
			array(
				'label'     => esc_html__( 'Layout', 'easy-property-listings' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'options'   => array(
					'grid' => esc_html__( 'Grid', 'easy-property-listings' ),
					'list' => esc_html__( 'List', 'easy-property-listings' ),
				),
				'default'   => 'grid',
				'condition' => array(
					'display_mode' => 'normal',
				),
			)
		);

		$this->add_responsive_control(
			'columns',
			array(
				'label'          => esc_html__( 'Columns', 'easy-property-listings' ),
				'type'           => \Elementor\Controls_Manager::SELECT,
				'options'        => array(
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
				),
				'default'        => '2',
				'tablet_default' => '2',
				'mobile_default' => '1',
				'selectors'      => array(
					'{{WRAPPER}} .epl-listing-agents-grid' => '--epl-agent-columns: {{VALUE}};',
				),
				'condition'      => array(
					'display_mode' => 'normal',
					'layout'       => 'grid',
				),
			)
		);

		$this->add_responsive_control(
			'gap',
			array(
				'label'      => esc_html__( 'Gap', 'easy-property-listings' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'default'    => array(
					'size' => 20,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .epl-listing-agents-grid' => 'gap: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .epl-listing-agents-list' => 'gap: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'display_mode' => 'normal',
				),
			)
		);

		// Tabbed mode controls.
		$this->add_control(
			'tab_style',
			array(
				'label'     => esc_html__( 'Tab Style', 'easy-property-listings' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'options'   => array(
					'horizontal' => esc_html__( 'Horizontal', 'easy-property-listings' ),
					'vertical'   => esc_html__( 'Vertical', 'easy-property-listings' ),
				),
				'default'   => 'horizontal',
				'condition' => array(
					'display_mode' => 'tabbed',
				),
			)
		);

		$this->add_control(
			'tab_label',
			array(
				'label'     => esc_html__( 'Tab Label', 'easy-property-listings' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'options'   => array(
					'name'     => esc_html__( 'Agent Name', 'easy-property-listings' ),
					'position' => esc_html__( 'Position/Title', 'easy-property-listings' ),
					'both'     => esc_html__( 'Name + Position', 'easy-property-listings' ),
				),
				'default'   => 'name',
				'condition' => array(
					'display_mode' => 'tabbed',
				),
			)
		);

		$this->add_control(
			'empty_message',
			array(
				'label'       => esc_html__( 'Empty Message', 'easy-property-listings' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'No agents assigned to this listing.', 'easy-property-listings' ),
				'placeholder' => esc_html__( 'Message when no agents', 'easy-property-listings' ),
			)
		);

		$this->end_controls_section();

		// Style Section - Tabs.
		$this->start_controls_section(
			'section_style_tabs',
			array(
				'label'     => esc_html__( 'Tabs', 'easy-property-listings' ),
				'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => array(
					'display_mode' => 'tabbed',
				),
			)
		);

		$this->add_control(
			'tab_bg_color',
			array(
				'label'     => esc_html__( 'Tab Background', 'easy-property-listings' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .epl-agents-tabs .epl-tab-button' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'tab_active_bg_color',
			array(
				'label'     => esc_html__( 'Active Tab Background', 'easy-property-listings' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .epl-agents-tabs .epl-tab-button.active' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'tab_text_color',
			array(
				'label'     => esc_html__( 'Tab Text Color', 'easy-property-listings' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .epl-agents-tabs .epl-tab-button' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'tab_active_text_color',
			array(
				'label'     => esc_html__( 'Active Tab Text Color', 'easy-property-listings' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .epl-agents-tabs .epl-tab-button.active' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'tab_typography',
				'selector' => '{{WRAPPER}} .epl-agents-tabs .epl-tab-button',
			)
		);

		$this->add_responsive_control(
			'tab_padding',
			array(
				'label'      => esc_html__( 'Tab Padding', 'easy-property-listings' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .epl-agents-tabs .epl-tab-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Get agents assigned to the current listing.
	 *
	 * @param EPL_Property_Meta $property The property object.
	 * @return array Array of author loader objects.
	 */
	private function get_listing_agents( $property ) {
		return EPL_Elementor::get_listing_agents( $property );
	}

	/**
	 * Get tab label for an agent.
	 *
	 * @param EPL_Author_Loader $author  The author object.
	 * @param string            $format  Label format.
	 * @return string The tab label.
	 */
	private function get_tab_label( $author, $format ) {
		$name     = $author->get_author_name();
		$position = $author->get_author_position();

		switch ( $format ) {
			case 'position':
				return ! empty( $position ) ? $position : $name;
			case 'both':
				return ! empty( $position ) ? $name . ' - ' . $position : $name;
			default:
				return $name;
		}
	}

	/**
	 * Render the selected template or default content.
	 *
	 * @param int    $template_id The template post ID.
	 * @param string $render_type The render type (default/template).
	 */
	private function render_agent_item( $template_id, $render_type ) {
		global $epl_current_agent;

		if ( 'default' === $render_type ) {
			// Render default EPL agent box for the current agent.
			// Passing 'epl_author' arg overrides globals in the template.
			epl_get_template_part( 'content-author-box.php', array( 'epl_author' => $epl_current_agent ) );
		} else {
			// Render Elementor template.
			$template_id = absint( $template_id );

			if ( empty( $template_id ) ) {
				return;
			}

			if ( EPL_Elementor_Agent_Document::TYPE !== get_post_meta( $template_id, '_elementor_template_type', true ) ) {
				if ( EPL_Elementor::is_editor() ) {
					echo '<div class="epl-elementor-placeholder">' . esc_html__( 'The selected template is not an EPL Agent Template.', 'easy-property-listings' ) . '</div>';
				}
				return;
			}

			// Guard against infinite recursion when the selected template
			// contains an Agents Loop widget pointing back at itself.
			if ( isset( self::$rendering_templates[ $template_id ] ) ) {
				return;
			}

			self::$rendering_templates[ $template_id ] = true;
			echo \Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $template_id, true );
			unset( self::$rendering_templates[ $template_id ] );
		}
	}

	/**
	 * Render widget output.
	 */
	protected function render() {
		$property  = EPL_Elementor::setup_listing_context();
		$is_editor = \Elementor\Plugin::$instance->editor->is_edit_mode();

		if ( ! $property ) {
			if ( $is_editor ) {
				echo '<div class="epl-elementor-placeholder">' . esc_html__( 'Listing Agents - No listings found.', 'easy-property-listings' ) . '</div>';
			}
			EPL_Elementor::restore_listing_context();
			return;
		}

		$settings     = $this->get_settings_for_display();
		$template_id  = isset( $settings['template_id'] ) ? $settings['template_id'] : '';
		$render_type  = isset( $settings['render_type'] ) ? $settings['render_type'] : 'default';
		$agents_count = isset( $settings['agents_count'] ) ? intval( $settings['agents_count'] ) : 4;
		$agents       = $this->get_listing_agents( $property );
		$display_mode = $settings['display_mode'];

		// Limit agents based on setting.
		if ( ! empty( $agents ) && $agents_count > 0 ) {
			$agents = array_slice( $agents, 0, $agents_count );
		}

		// Check if template is selected (only if render_type is template).
		if ( 'template' === $render_type && empty( $template_id ) ) {
			if ( $is_editor ) {
				echo '<div class="epl-elementor-placeholder">';
				echo esc_html__( 'Please select an Agent Template from the widget settings.', 'easy-property-listings' );
				echo '<br><br>';
				echo '<small>' . esc_html__( 'Create a template using EPL Agent widgets (Photo, Name, Contact, Social, Bio) and select it above.', 'easy-property-listings' ) . '</small>';
				echo '</div>';
			}
			EPL_Elementor::restore_listing_context();
			return;
		}

		if ( empty( $agents ) ) {
			if ( ! empty( $settings['empty_message'] ) ) {
				echo '<div class="epl-agents-empty">' . esc_html( $settings['empty_message'] ) . '</div>';
			}
			EPL_Elementor::restore_listing_context();
			return;
		}

		if ( 'tabbed' === $display_mode ) {
			$this->render_tabbed( $agents, $settings, $template_id, $render_type );
		} else {
			$this->render_normal( $agents, $settings, $template_id, $render_type );
		}

		EPL_Elementor::restore_listing_context();
	}

	/**
	 * Render normal (grid/list) layout.
	 *
	 * @param array  $agents      Array of agent IDs.
	 * @param array  $settings    Widget settings.
	 * @param int    $template_id Template ID.
	 * @param string $render_type Render type.
	 */
	private function render_normal( $agents, $settings, $template_id, $render_type ) {
		global $epl_current_agent;

		$layout  = $settings['layout'];
		$columns = isset( $settings['columns'] ) ? $settings['columns'] : '2';

		$wrapper_class = 'epl-listing-agents epl-listing-agents-' . $layout;
		if ( 'grid' === $layout ) {
			$wrapper_class .= ' epl-agents-cols-' . $columns;
		}

		echo '<div class="' . esc_attr( $wrapper_class ) . '">';

		$previous_agent = $epl_current_agent;
		foreach ( $agents as $agent ) {
			$epl_current_agent = $agent;

			echo '<div class="epl-agent-item">';
			$this->render_agent_item( $template_id, $render_type );
			echo '</div>';
		}

		echo '</div>';

		// Reset global.
		$epl_current_agent = $previous_agent;
	}

	/**
	 * Render tabbed layout.
	 *
	 * @param array  $agents      Array of agent IDs.
	 * @param array  $settings    Widget settings.
	 * @param int    $template_id Template ID.
	 * @param string $render_type Render type.
	 */
	private function render_tabbed( $agents, $settings, $template_id, $render_type ) {
		global $epl_current_agent;

		$tab_style = $settings['tab_style'];
		$tab_label = $settings['tab_label'];

		$wrapper_class = 'epl-agents-tabs epl-agents-tabs-' . $tab_style;

		echo '<div class="' . esc_attr( $wrapper_class ) . '">';

		// Prefix tab IDs with the widget ID so multiple widgets on the same
		// page (or the same agent in two widgets) never share DOM IDs.
		$tab_id_prefix = 'epl-agent-tab-' . $this->get_id() . '-';

		// Tab buttons.
		echo '<div class="epl-tab-buttons">';
		$index = 0;
		foreach ( $agents as $author ) {
			$label        = $this->get_tab_label( $author, $tab_label );
			$active_class = ( 0 === $index ) ? ' active' : '';

			echo '<button class="epl-tab-button' . esc_attr( $active_class ) . '" data-tab="' . esc_attr( $tab_id_prefix . $author->author_id ) . '">';
			echo esc_html( $label );
			echo '</button>';

			++$index;
		}
		echo '</div>';

		// Tab content.
		echo '<div class="epl-tab-contents">';
		$index = 0;
		$previous_agent = $epl_current_agent;
		foreach ( $agents as $agent ) {
			$epl_current_agent = $agent;
			$active_class      = ( 0 === $index ) ? ' active' : '';

			echo '<div id="' . esc_attr( $tab_id_prefix . $agent->author_id ) . '" class="epl-tab-content' . esc_attr( $active_class ) . '">';
			$this->render_agent_item( $template_id, $render_type );
			echo '</div>';

			++$index;
		}
		echo '</div>';

		echo '</div>';

		// Reset global.
		$epl_current_agent = $previous_agent;
	}
}
