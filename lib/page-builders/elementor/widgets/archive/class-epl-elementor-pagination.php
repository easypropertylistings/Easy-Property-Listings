<?php
/**
 * Elementor archive pagination widget.
 *
 * @package EPL
 * @subpackage PageBuilders/Elementor/Widgets/Archive
 * @since 3.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Pagination for the current Elementor/EPL archive query. */
class EPL_Elementor_Pagination extends \Elementor\Widget_Base {
	use EPL_Elementor_Dynamic_Widget;

	public function get_name() {
		return 'epl-pagination';
	}

	public function get_title() {
		return esc_html__( 'Pagination', 'easy-property-listings' );
	}

	public function get_icon() {
		return 'eicon-post-navigation';
	}

	public function get_categories() {
		return array( 'epl-archive' );
	}

	public function get_keywords() {
		return array( 'epl', 'pagination', 'archive', 'listings', 'next', 'previous' );
	}

	protected function register_controls() {
		$this->start_controls_section( 'section_pagination', array( 'label' => esc_html__( 'Pagination', 'easy-property-listings' ) ) );
		$this->add_control(
			'pagination_style',
			array(
				'label'   => esc_html__( 'Pagination Type', 'easy-property-listings' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'default',
				'options' => array(
					'fancy'   => esc_html__( 'Fancy', 'easy-property-listings' ),
					'default' => esc_html__( 'WordPress Default', 'easy-property-listings' ),
				),
			)
		);
		$this->add_control(
			'editor_note',
			array(
				'type'            => \Elementor\Controls_Manager::RAW_HTML,
				'raw'             => esc_html__( 'This widget paginates the current archive query. For Loop Grid, either use its built-in Pagination section or disable that pagination before placing this widget.', 'easy-property-listings' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			)
		);
		$this->end_controls_section();

		$this->start_controls_section( 'section_style', array( 'label' => esc_html__( 'Pagination', 'easy-property-listings' ), 'tab' => \Elementor\Controls_Manager::TAB_STYLE ) );
		$this->add_responsive_control(
			'align',
			array(
				'label'     => esc_html__( 'Alignment', 'easy-property-listings' ),
				'type'      => \Elementor\Controls_Manager::CHOOSE,
				'options'   => array(
					'left'   => array( 'title' => esc_html__( 'Left', 'easy-property-listings' ), 'icon' => 'eicon-text-align-left' ),
					'center' => array( 'title' => esc_html__( 'Center', 'easy-property-listings' ), 'icon' => 'eicon-text-align-center' ),
					'right'  => array( 'title' => esc_html__( 'Right', 'easy-property-listings' ), 'icon' => 'eicon-text-align-right' ),
				),
				'default'   => 'center',
				'selectors' => array( '{{WRAPPER}}' => 'text-align: {{VALUE}};' ),
			)
		);
		$this->add_group_control( \Elementor\Group_Control_Typography::get_type(), array( 'name' => 'typography', 'selector' => '{{WRAPPER}} .epl-pagination a, {{WRAPPER}} .epl-pagination span, {{WRAPPER}} .epl-paginate-default-wrapper a' ) );
		$this->end_controls_section();
	}

	protected function render() {
		global $wp_query;
		$loop_context = EPL_Elementor::instance()->get_last_loop_grid_query();
		$query        = ! empty( $loop_context['query'] ) && $loop_context['query'] instanceof WP_Query ? $loop_context['query'] : ( $wp_query instanceof WP_Query ? $wp_query : null );
		$style        = $this->get_settings_for_display( 'pagination_style' );
		$style        = $style ? $style : 'default';
		if ( ! $query || $query->max_num_pages < 2 ) {
			if ( EPL_Elementor::is_editor() ) {
				if ( 'fancy' === $style ) {
					echo '<div class="wp-pagenavi epl-pagination epl-block-pagination-preview" role="navigation"><span class="pages">' . esc_html__( 'Page 1 of 3', 'easy-property-listings' ) . '</span><span class="current">1</span><a class="page larger">2</a><a class="page larger">3</a><a class="nextpostslink">' . esc_html__( 'Next', 'easy-property-listings' ) . '</a></div>';
				} else {
					echo '<div class="epl-paginate-default-wrapper epl-clearfix epl-elementor-pagination-preview"><div class="alignleft">' . esc_html__( 'Previous Page', 'easy-property-listings' ) . '</div><div class="alignright">' . esc_html__( 'Next Page', 'easy-property-listings' ) . '</div></div>';
				}
			}
			return;
		}

		if ( ! empty( $loop_context['widget_id'] ) ) {
			EPL_Elementor::render_loop_grid_epl_navigation( $query, $loop_context['widget_id'], $style );
			return;
		}
		EPL_Elementor::render_epl_pagination( $query, $style );
	}
}
