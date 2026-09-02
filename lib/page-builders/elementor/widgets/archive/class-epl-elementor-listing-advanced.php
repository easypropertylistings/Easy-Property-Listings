<?php
/**
 * Elementor Listing Advanced Widget.
 *
 * @package     EPL
 * @subpackage  PageBuilders/Elementor/Widgets/Archive
 * @since       3.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Elementor interface for the canonical [listing_advanced] implementation. */
class EPL_Elementor_Listing_Advanced extends \Elementor\Widget_Base {
	use EPL_Elementor_Dynamic_Widget;

	public function get_name() {
		return 'epl-listing-advanced';
	}

	public function get_title() {
		return esc_html__( 'Listings Loop', 'easy-property-listings' );
	}

	public function get_icon() {
		return 'eicon-posts-group';
	}

	public function get_categories() {
		return array( 'epl-archive' );
	}

	public function get_keywords() {
		return array( 'listing', 'advanced', 'property', 'query', 'shortcode', 'epl' );
	}

	protected function register_controls() {
		$this->start_controls_section( 'section_query', array( 'label' => esc_html__( 'Query', 'easy-property-listings' ) ) );
		$this->add_control(
			'post_type',
			array(
				'label' => esc_html__( 'Post Types', 'easy-property-listings' ), 'type' => \Elementor\Controls_Manager::SELECT2,
				'options' => epl_get_active_post_types(), 'multiple' => true, 'label_block' => true,
				'default' => array_keys( epl_get_active_post_types() ),
			)
		);
		$status_options = EPL_Elementor::get_status_options();
		unset( $status_options[''], $status_options['withdrawn'], $status_options['offmarket'] );
		$this->add_control(
			'status',
			array(
				'label' => esc_html__( 'Statuses', 'easy-property-listings' ), 'type' => \Elementor\Controls_Manager::SELECT2,
				'options' => $status_options, 'multiple' => true, 'label_block' => true,
				'default' => array( 'current', 'sold', 'leased' ),
			)
		);
		$this->add_control(
			'commercial_listing_type',
			array(
				'label' => esc_html__( 'Commercial Listing Type', 'easy-property-listings' ), 'type' => \Elementor\Controls_Manager::SELECT2,
				'options' => epl_get_property_com_listing_type_opts(), 'multiple' => true, 'label_block' => true,
			)
		);
		$this->add_control( 'limit', array( 'label' => esc_html__( 'Listings Per Page', 'easy-property-listings' ), 'type' => \Elementor\Controls_Manager::NUMBER, 'min' => 1, 'max' => 100, 'default' => 12 ) );
		$this->add_control( 'offset', array( 'label' => esc_html__( 'Offset', 'easy-property-listings' ), 'type' => \Elementor\Controls_Manager::NUMBER, 'min' => 0, 'default' => 0 ) );
		$this->add_control( 'post__in', array( 'label' => esc_html__( 'Include Listing IDs', 'easy-property-listings' ), 'type' => \Elementor\Controls_Manager::TEXT, 'label_block' => true, 'description' => esc_html__( 'Comma-separated post IDs.', 'easy-property-listings' ) ) );
		$this->add_control( 'post__not_in', array( 'label' => esc_html__( 'Exclude Listing IDs', 'easy-property-listings' ), 'type' => \Elementor\Controls_Manager::TEXT, 'label_block' => true, 'description' => esc_html__( 'Comma-separated post IDs.', 'easy-property-listings' ) ) );
		$this->end_controls_section();

		$this->start_controls_section( 'section_filters', array( 'label' => esc_html__( 'Filters', 'easy-property-listings' ) ) );
		$this->add_control(
			'location_id',
			array(
				'label' => epl_tax_location_label(), 'type' => \Elementor\Controls_Manager::SELECT2,
				'options' => $this->get_term_options( 'location' ), 'multiple' => true, 'label_block' => true,
			)
		);
		$this->add_control(
			'feature_id',
			array(
				'label' => esc_html__( 'Features', 'easy-property-listings' ), 'type' => \Elementor\Controls_Manager::SELECT2,
				'options' => $this->get_term_options( 'tax_feature' ), 'multiple' => true, 'label_block' => true,
			)
		);
		$this->add_control( 'location', array( 'label' => esc_html__( 'Location Slugs', 'easy-property-listings' ), 'type' => \Elementor\Controls_Manager::TEXT, 'label_block' => true, 'description' => esc_html__( 'Optional comma-separated slugs.', 'easy-property-listings' ) ) );
		$this->add_control( 'feature', array( 'label' => esc_html__( 'Feature Slugs', 'easy-property-listings' ), 'type' => \Elementor\Controls_Manager::TEXT, 'label_block' => true, 'description' => esc_html__( 'Optional comma-separated slugs.', 'easy-property-listings' ) ) );
		$this->add_control( 'author', array( 'label' => esc_html__( 'WordPress Authors', 'easy-property-listings' ), 'type' => \Elementor\Controls_Manager::SELECT2, 'options' => $this->get_author_options(), 'multiple' => true, 'label_block' => true ) );
		$this->add_control( 'agent', array( 'label' => esc_html__( 'Listing Agent Usernames', 'easy-property-listings' ), 'type' => \Elementor\Controls_Manager::TEXT, 'label_block' => true, 'description' => esc_html__( 'Comma-separated EPL agent usernames.', 'easy-property-listings' ) ) );
		$this->add_control( 'featured', $this->switcher_args( esc_html__( 'Featured Only', 'easy-property-listings' ) ) );
		$this->add_control( 'open_house', $this->switcher_args( esc_html__( 'Open for Inspection Only', 'easy-property-listings' ) ) );
		$this->add_control( 'auction', $this->switcher_args( esc_html__( 'Auction Only', 'easy-property-listings' ) ) );
		$this->end_controls_section();

		$this->start_controls_section( 'section_dynamic_filters', array( 'label' => esc_html__( 'Dynamic Meta Filters', 'easy-property-listings' ) ) );
		$this->add_control(
			'dynamic_filters_note',
			array(
				'type'            => \Elementor\Controls_Manager::RAW_HTML,
				'raw'             => esc_html__( 'These filters are passed to epl_parse_atts(). Each filter creates a named clause using the meta key plus “_clause”, which can be referenced by Advanced Orderby Clauses.', 'easy-property-listings' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			)
		);
		$filter_repeater = new \Elementor\Repeater();
		$filter_repeater->add_control(
			'meta_key',
			array(
				'label'       => esc_html__( 'Meta Key', 'easy-property-listings' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
				'placeholder' => 'property_bedrooms',
				'description' => esc_html__( 'Enter the meta key without the leading underscore.', 'easy-property-listings' ),
			)
		);
		$filter_repeater->add_control(
			'compare',
			array(
				'label'   => esc_html__( 'Compare', 'easy-property-listings' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'equal',
				'options' => array(
					'equal'       => esc_html__( 'Equal', 'easy-property-listings' ),
					'min'         => esc_html__( 'Minimum (>=)', 'easy-property-listings' ),
					'max'         => esc_html__( 'Maximum (<=)', 'easy-property-listings' ),
					'not_equal'   => esc_html__( 'Not Equal', 'easy-property-listings' ),
					'like'        => esc_html__( 'Like', 'easy-property-listings' ),
					'not_like'    => esc_html__( 'Not Like', 'easy-property-listings' ),
					'exists'      => esc_html__( 'Exists', 'easy-property-listings' ),
					'not_exists'  => esc_html__( 'Not Exists', 'easy-property-listings' ),
					'between'     => esc_html__( 'Between', 'easy-property-listings' ),
					'not_between' => esc_html__( 'Not Between', 'easy-property-listings' ),
					'in'          => esc_html__( 'In', 'easy-property-listings' ),
					'not_in'      => esc_html__( 'Not In', 'easy-property-listings' ),
				),
			)
		);
		$filter_repeater->add_control(
			'value',
			array(
				'label'       => esc_html__( 'Value', 'easy-property-listings' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
				'placeholder' => esc_html__( 'Use commas for Between and In comparisons', 'easy-property-listings' ),
				'condition'   => array( 'compare!' => array( 'exists', 'not_exists' ) ),
			)
		);
		$this->add_control(
			'dynamic_filters',
			array(
				'label'       => esc_html__( 'Meta Filters', 'easy-property-listings' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $filter_repeater->get_controls(),
				'default'     => array(),
				'title_field' => '<# var key = (meta_key || "Meta filter").replace(/^_+/, ""); #>{{{ key }}} <small>{{{ compare || "equal" }}}</small>',
			)
		);
		$this->end_controls_section();

		$this->start_controls_section( 'section_order', array( 'label' => esc_html__( 'Ordering', 'easy-property-listings' ) ) );
		$this->add_control(
			'sortby',
			array(
				'label' => esc_html__( 'Order By', 'easy-property-listings' ), 'type' => \Elementor\Controls_Manager::SELECT, 'default' => 'date',
				'options' => array( 'date' => esc_html__( 'Date', 'easy-property-listings' ), 'price' => esc_html__( 'Price', 'easy-property-listings' ), 'rand' => esc_html__( 'Random', 'easy-property-listings' ), 'status' => esc_html__( 'Status', 'easy-property-listings' ) ),
			)
		);
		$this->add_control( 'sort_order', array( 'label' => esc_html__( 'Direction', 'easy-property-listings' ), 'type' => \Elementor\Controls_Manager::SELECT, 'default' => 'DESC', 'options' => array( 'DESC' => esc_html__( 'Descending', 'easy-property-listings' ), 'ASC' => esc_html__( 'Ascending', 'easy-property-listings' ) ) ) );
		$this->add_control( 'orderby_clause', array( 'label' => esc_html__( 'Advanced Orderby Clauses', 'easy-property-listings' ), 'type' => \Elementor\Controls_Manager::TEXT, 'label_block' => true, 'description' => esc_html__( 'Comma-separated clause|direction pairs.', 'easy-property-listings' ) ) );
		$this->end_controls_section();

		$this->start_controls_section( 'section_output', array( 'label' => esc_html__( 'Output', 'easy-property-listings' ) ) );
		$renderers = array( 'epl' => esc_html__( 'EPL Template', 'easy-property-listings' ) );
		$templates = $this->get_elementor_loop_templates();
		if ( $templates ) {
			$renderers['elementor'] = esc_html__( 'Elementor Loop / EPL Card Template', 'easy-property-listings' );
		}
		$this->add_control( 'renderer', array( 'label' => esc_html__( 'Renderer', 'easy-property-listings' ), 'type' => \Elementor\Controls_Manager::SELECT, 'default' => 'epl', 'options' => $renderers ) );
		$this->add_control(
			'template',
			array(
				'label' => esc_html__( 'EPL Template', 'easy-property-listings' ), 'type' => \Elementor\Controls_Manager::SELECT, 'default' => 'default', 'condition' => array( 'renderer' => 'epl' ),
				'options' => array( 'default' => esc_html__( 'Default', 'easy-property-listings' ), 'card' => esc_html__( 'Card', 'easy-property-listings' ), 'slim' => esc_html__( 'Slim', 'easy-property-listings' ), 'table' => esc_html__( 'Table', 'easy-property-listings' ), 'table-open' => esc_html__( 'Table with Inspection', 'easy-property-listings' ) ),
			)
		);
		if ( $templates ) {
			$this->add_control( 'elementor_template_id', array( 'label' => esc_html__( 'Loop / Card Template', 'easy-property-listings' ), 'type' => \Elementor\Controls_Manager::SELECT2, 'options' => $templates, 'label_block' => true, 'condition' => array( 'renderer' => 'elementor' ) ) );
			$this->add_control(
				'list_template_id',
				array(
					'label'       => esc_html__( 'List View Loop Template', 'easy-property-listings' ),
					'type'        => \Elementor\Controls_Manager::SELECT2,
					'options'     => $templates,
					'label_block' => true,
					'description' => esc_html__( 'Optional. When set, this template is shown while the archive is in List view; the Loop Template above is used for Grid view. Requires Archive Tools (the grid/list switch) to be enabled so visitors can toggle.', 'easy-property-listings' ),
					'condition'   => array( 'renderer' => 'elementor' ),
				)
			);
			$this->add_control(
				'editor_preview_view',
				array(
					'label'       => esc_html__( 'Editor Preview View', 'easy-property-listings' ),
					'type'        => \Elementor\Controls_Manager::CHOOSE,
					'default'     => 'grid',
					'options'     => array(
						'grid' => array(
							'title' => esc_html__( 'Grid', 'easy-property-listings' ),
							'icon'  => 'eicon-gallery-grid',
						),
						'list' => array(
							'title' => esc_html__( 'List', 'easy-property-listings' ),
							'icon'  => 'eicon-editor-list-ul',
						),
					),
					'toggle'      => false,
					'description' => esc_html__( 'Preview only — choose which template shows on the editor canvas. The live site uses the visitor’s grid/list selection.', 'easy-property-listings' ),
					'condition'   => array(
						'renderer'          => 'elementor',
						'list_template_id!' => '',
					),
				)
			);
		}
		$this->add_control( 'tools_top', $this->switcher_args( esc_html__( 'Archive Tools Above', 'easy-property-listings' ), 'on', 'off' ) );
		$this->add_control( 'tools_bottom', $this->switcher_args( esc_html__( 'Archive Tools Below', 'easy-property-listings' ), 'on', 'off' ) );
		$this->add_control(
			'pagination',
			array(
				'label'   => esc_html__( 'Pagination', 'easy-property-listings' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'epl',
				'options' => array(
					'off' => esc_html__( 'None', 'easy-property-listings' ),
					'epl' => esc_html__( 'EPL', 'easy-property-listings' ),
				),
			)
		);
		$this->add_control(
			'epl_pagination_style',
			array(
				'label'     => esc_html__( 'EPL Pagination Type', 'easy-property-listings' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'default'   => 'default',
				'options'   => array(
					'fancy'   => esc_html__( 'Fancy', 'easy-property-listings' ),
					'default' => esc_html__( 'WordPress Default', 'easy-property-listings' ),
				),
				'condition' => array( 'pagination' => 'epl' ),
			)
		);
		$this->add_responsive_control( 'columns', array( 'label' => esc_html__( 'Grid Columns', 'easy-property-listings' ), 'type' => \Elementor\Controls_Manager::SELECT, 'default' => '3', 'tablet_default' => '2', 'mobile_default' => '1', 'options' => array( '1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6' ), 'selectors' => array( '{{WRAPPER}} .epl-elementor-advanced-grid' => '--epl-elementor-advanced-columns: {{VALUE}};' ) ) );
		if ( $templates ) {
			$this->add_responsive_control(
				'list_columns',
				array(
					'label'          => esc_html__( 'List Columns', 'easy-property-listings' ),
					'type'           => \Elementor\Controls_Manager::SELECT,
					'default'        => '1',
					'tablet_default' => '1',
					'mobile_default' => '1',
					'options'        => array( '1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6' ),
					'description'    => esc_html__( 'Columns used while the archive is in List view.', 'easy-property-listings' ),
					'selectors'      => array(
						'{{WRAPPER}} .epl-elementor-advanced-grid.epl-ea-dual-view.is-list-view' => '--epl-elementor-advanced-columns: {{VALUE}};',
					),
					'condition'      => array(
						'renderer'          => 'elementor',
						'list_template_id!' => '',
					),
				)
			);
		}
		$this->end_controls_section();

		$this->start_controls_section( 'section_grid_style', array( 'label' => esc_html__( 'Grid', 'easy-property-listings' ), 'tab' => \Elementor\Controls_Manager::TAB_STYLE ) );
		$this->add_responsive_control( 'column_gap', array( 'label' => esc_html__( 'Column Gap', 'easy-property-listings' ), 'type' => \Elementor\Controls_Manager::SLIDER, 'size_units' => array( 'px', 'em', 'rem' ), 'default' => array( 'size' => 24, 'unit' => 'px' ), 'selectors' => array( '{{WRAPPER}} .epl-elementor-advanced-grid' => 'column-gap: {{SIZE}}{{UNIT}};' ) ) );
		$this->add_responsive_control( 'row_gap', array( 'label' => esc_html__( 'Row Gap', 'easy-property-listings' ), 'type' => \Elementor\Controls_Manager::SLIDER, 'size_units' => array( 'px', 'em', 'rem' ), 'default' => array( 'size' => 32, 'unit' => 'px' ), 'selectors' => array( '{{WRAPPER}} .epl-elementor-advanced-grid' => 'row-gap: {{SIZE}}{{UNIT}};' ) ) );
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
		$settings  = $this->get_settings_for_display();
		$atts      = $this->build_shortcode_attributes( $settings );
		$shortcode = new EPL_Advanced_Shortcode_Listing( $atts );

		if ( 'elementor' !== $settings['renderer'] || empty( $settings['elementor_template_id'] ) || ! $this->get_elementor_loop_templates() ) {
			echo $shortcode->render(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- EPL templates escape their output.
		} else {
			$this->render_elementor_template( $shortcode, absint( $settings['elementor_template_id'] ), absint( $settings['list_template_id'] ?? 0 ), $settings['editor_preview_view'] ?? 'grid' );
		}

		$pagination = empty( $settings['offset'] ) && 'off' !== ( $settings['pagination'] ?? 'epl' ) ? 'epl' : 'off';
		$pagination_style = ! empty( $settings['epl_pagination_style'] ) ? $settings['epl_pagination_style'] : 'default';
		$this->render_pagination( $shortcode->query_open, $pagination, $pagination_style );
	}

	private function build_shortcode_attributes( $settings ) {
		$keys = array( 'post_type', 'status', 'commercial_listing_type', 'feature', 'feature_id', 'limit', 'offset', 'author', 'agent', 'featured', 'open_house', 'auction', 'template', 'location', 'location_id', 'tools_top', 'tools_bottom', 'sortby', 'orderby_clause', 'sort_order', 'post__in', 'post__not_in' );
		$atts = array(
			'instance_id' => 'elementor-' . $this->get_id(),
			'class'       => 'epl-elementor-advanced-grid',
			// Pagination is rendered below so EPL and Elementor providers share the
			// exact same query and never produce duplicate navigation.
			'pagination'  => 'off',
		);
		foreach ( $keys as $key ) {
			$value = isset( $settings[ $key ] ) ? $settings[ $key ] : '';
			$atts[ $key ] = is_array( $value ) ? implode( ',', array_filter( $value, 'strlen' ) ) : $value;
		}

		$compare_suffixes = array(
			'equal'       => '',
			'min'         => '_min',
			'max'         => '_max',
			'not_equal'   => '_not_equal',
			'like'        => '_like',
			'not_like'    => '_not_like',
			'exists'      => '_exists',
			'not_exists'  => '_not_exists',
			'between'     => '_between',
			'not_between' => '_not_between',
			'in'          => '_in',
			'not_in'      => '_not_in',
		);

		foreach ( isset( $settings['dynamic_filters'] ) ? (array) $settings['dynamic_filters'] : array() as $filter ) {
			$meta_key = isset( $filter['meta_key'] ) ? ltrim( sanitize_key( $filter['meta_key'] ), '_' ) : '';
			$compare  = isset( $filter['compare'] ) ? sanitize_key( $filter['compare'] ) : 'equal';
			if ( '' === $meta_key || ! isset( $compare_suffixes[ $compare ] ) ) {
				continue;
			}
			$attribute          = '_' . $meta_key . $compare_suffixes[ $compare ];
			$atts[ $attribute ] = in_array( $compare, array( 'exists', 'not_exists' ), true ) ? '' : (string) ( $filter['value'] ?? '' );
		}

		return apply_filters( 'epl_elementor_listing_advanced_atts', $atts, $settings, $this );
	}

	private function render_elementor_template( $shortcode, $template_id, $list_template_id = 0, $editor_view = 'grid' ) {
		$query = $shortcode->query_open;
		if ( ! $query->have_posts() ) {
			if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
				echo '<div class="epl-elementor-placeholder">' . esc_html__( 'No listings found.', 'easy-property-listings' ) . '</div>';
			} else {
				do_action( 'epl_shortcode_results_message', 'default' );
			}
			return;
		}

		global $property;
		$original_property = isset( $property ) ? $property : null;

		// A distinct list template turns on dual rendering: each item outputs both
		// templates and the grid/list switch toggles which one is visible via CSS.
		$dual_view    = $list_template_id && $list_template_id !== $template_id;
		$grid_classes = 'epl-elementor-advanced-grid';
		if ( $dual_view ) {
			// In the editor the canvas has no visitor toggle, so the dedicated
			// preview control decides which template is shown; the live site
			// resolves from the visitor's saved preference.
			$view          = \Elementor\Plugin::$instance->editor->is_edit_mode()
				? ( 'list' === $editor_view ? 'list' : 'grid' )
				: $this->get_initial_view();
			$grid_classes .= ' epl-ea-dual-view ' . ( 'list' === $view ? 'is-list-view' : 'is-grid-view' );
		}

		// Elementor only needs a template's CSS printed once per page.
		$css_loaded = array();

		if ( 'on' === $shortcode->attributes['tools_top'] ) {
			do_action( 'epl_property_loop_start', $shortcode->attributes );
		}
		echo '<div class="' . esc_attr( $grid_classes ) . '">';
		while ( $query->have_posts() ) {
			$query->the_post();
			$property = new EPL_Property_Meta( get_post() ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- EPL loop context restored below.
			echo '<div class="epl-elementor-advanced-item">';
			if ( $dual_view ) {
				echo '<div class="epl-ea-view epl-ea-view--grid">';
				$this->render_loop_item( $template_id, $css_loaded );
				echo '</div>';
				echo '<div class="epl-ea-view epl-ea-view--list">';
				$this->render_loop_item( $list_template_id, $css_loaded );
				echo '</div>';
			} else {
				$this->render_loop_item( $template_id, $css_loaded );
			}
			echo '</div>';
		}
		echo '</div>';
		if ( 'on' === $shortcode->attributes['tools_bottom'] ) {
			do_action( 'epl_property_loop_end' );
		}
		wp_reset_postdata();
		$property = $original_property; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- Restore caller context.
	}

	/**
	 * Render a single Elementor loop item, printing each template's CSS only once.
	 *
	 * @param int   $template_id Elementor loop-item template ID.
	 * @param array $css_loaded  Map of template IDs whose CSS has already printed.
	 */
	private function render_loop_item( $template_id, &$css_loaded ) {
		$with_css                   = empty( $css_loaded[ $template_id ] );
		$css_loaded[ $template_id ] = true;
		echo \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $template_id, $with_css ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Elementor-rendered template.
	}

	/**
	 * Initial grid/list view for a dual-view widget.
	 *
	 * Mirrors the front-end switch logic: the visitor's saved preference cookie
	 * wins, otherwise the site's default archive view setting, otherwise list.
	 * Resolved server-side so the correct template shows without a flash.
	 *
	 * @return string Either 'grid' or 'list'.
	 */
	private function get_initial_view() {
		if ( isset( $_COOKIE['preferredView'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only display preference.
			$view = sanitize_key( wp_unslash( $_COOKIE['preferredView'] ) );
			if ( in_array( $view, array( 'list', 'grid' ), true ) ) {
				return $view;
			}
		}

		return 'grid' === epl_get_option( 'display_archive_view_type', 'list' ) ? 'grid' : 'list';
	}

	/** Render the selected navigation provider for this widget's private query. */
	private function render_pagination( $query, $provider, $pagination_style = 'default' ) {
		if ( ! $query instanceof WP_Query || $query->max_num_pages < 2 || 'off' === $provider ) {
			return;
		}
		EPL_Elementor::render_epl_pagination( $query, $pagination_style );
	}

	/**
	 * Elementor Loop Grid templates (Pro, when available) plus EPL Listing
	 * Card templates (always) — both render identically via render_loop_item().
	 */
	private function get_elementor_loop_templates() {
		$options = array();

		$widgets_manager = isset( \Elementor\Plugin::$instance->widgets_manager ) ? \Elementor\Plugin::$instance->widgets_manager : null;
		if ( $widgets_manager && $widgets_manager->get_widget_types( 'loop-grid' ) ) {
			$pro_posts = get_posts( array( 'post_type' => 'elementor_library', 'post_status' => 'publish', 'posts_per_page' => -1, 'orderby' => 'title', 'order' => 'ASC', 'meta_key' => '_elementor_template_type', 'meta_value' => 'loop-item' ) ); // phpcs:ignore WordPress.DB.SlowDBQuery
			foreach ( $pro_posts as $post ) {
				$options[ $post->ID ] = $post->post_title;
			}
		}

		$card_posts = get_posts( array( 'post_type' => 'elementor_library', 'post_status' => 'publish', 'posts_per_page' => -1, 'orderby' => 'title', 'order' => 'ASC', 'meta_key' => '_elementor_template_type', 'meta_value' => EPL_Elementor_Loop_Card_Document::TYPE ) ); // phpcs:ignore WordPress.DB.SlowDBQuery
		foreach ( $card_posts as $post ) {
			$options[ $post->ID ] = $post->post_title;
		}

		return $options;
	}

	private function get_term_options( $taxonomy ) {
		$options = array();
		$terms = get_terms( array( 'taxonomy' => $taxonomy, 'hide_empty' => false ) );
		if ( ! is_wp_error( $terms ) ) {
			foreach ( $terms as $term ) {
				$options[ $term->term_id ] = $term->name;
			}
		}
		return $options;
	}

	private function get_author_options() {
		$options = array();
		foreach ( get_users( array( 'fields' => array( 'user_login', 'display_name' ) ) ) as $user ) {
			$options[ $user->user_login ] = $user->display_name;
		}
		return $options;
	}

	private function switcher_args( $label, $on = 'yes', $off = '', $default = '' ) {
		return array( 'label' => $label, 'type' => \Elementor\Controls_Manager::SWITCHER, 'label_on' => esc_html__( 'Yes', 'easy-property-listings' ), 'label_off' => esc_html__( 'No', 'easy-property-listings' ), 'return_value' => $on, 'default' => $default, 'off_value' => $off );
	}
}
