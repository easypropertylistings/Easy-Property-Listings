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
		return esc_html__( 'EPL Listing Advanced', 'easy-property-listings' );
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
		$this->add_control( 'limit', array( 'label' => esc_html__( 'Listings Per Page', 'easy-property-listings' ), 'type' => \Elementor\Controls_Manager::NUMBER, 'min' => 1, 'max' => 100, 'default' => 10 ) );
		$this->add_control( 'offset', array( 'label' => esc_html__( 'Offset', 'easy-property-listings' ), 'type' => \Elementor\Controls_Manager::NUMBER, 'min' => 0, 'default' => 0, 'description' => esc_html__( 'Using an offset disables pagination.', 'easy-property-listings' ) ) );
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
			$renderers['elementor'] = esc_html__( 'Elementor Loop Template', 'easy-property-listings' );
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
			$this->add_control( 'elementor_template_id', array( 'label' => esc_html__( 'Loop Template', 'easy-property-listings' ), 'type' => \Elementor\Controls_Manager::SELECT2, 'options' => $templates, 'label_block' => true, 'condition' => array( 'renderer' => 'elementor' ) ) );
		}
		$this->add_control( 'tools_top', $this->switcher_args( esc_html__( 'Archive Tools Above', 'easy-property-listings' ), 'on', 'off' ) );
		$this->add_control( 'tools_bottom', $this->switcher_args( esc_html__( 'Archive Tools Below', 'easy-property-listings' ), 'on', 'off' ) );
		$this->add_control( 'pagination', $this->switcher_args( esc_html__( 'Pagination', 'easy-property-listings' ), 'on', 'off', 'on' ) );
		$this->add_responsive_control( 'columns', array( 'label' => esc_html__( 'Columns', 'easy-property-listings' ), 'type' => \Elementor\Controls_Manager::SELECT, 'default' => '3', 'tablet_default' => '2', 'mobile_default' => '1', 'options' => array( '1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6' ), 'selectors' => array( '{{WRAPPER}} .epl-elementor-advanced-grid' => '--epl-elementor-advanced-columns: {{VALUE}};' ) ) );
		$this->end_controls_section();

		$this->start_controls_section( 'section_grid_style', array( 'label' => esc_html__( 'Grid', 'easy-property-listings' ), 'tab' => \Elementor\Controls_Manager::TAB_STYLE ) );
		$this->add_responsive_control( 'column_gap', array( 'label' => esc_html__( 'Column Gap', 'easy-property-listings' ), 'type' => \Elementor\Controls_Manager::SLIDER, 'size_units' => array( 'px', 'em', 'rem' ), 'default' => array( 'size' => 24, 'unit' => 'px' ), 'selectors' => array( '{{WRAPPER}} .epl-elementor-advanced-grid' => 'column-gap: {{SIZE}}{{UNIT}};' ) ) );
		$this->add_responsive_control( 'row_gap', array( 'label' => esc_html__( 'Row Gap', 'easy-property-listings' ), 'type' => \Elementor\Controls_Manager::SLIDER, 'size_units' => array( 'px', 'em', 'rem' ), 'default' => array( 'size' => 32, 'unit' => 'px' ), 'selectors' => array( '{{WRAPPER}} .epl-elementor-advanced-grid' => 'row-gap: {{SIZE}}{{UNIT}};' ) ) );
		$this->end_controls_section();
	}

	protected function render() {
		$settings  = $this->get_settings_for_display();
		$atts      = $this->build_shortcode_attributes( $settings );
		$shortcode = new EPL_Advanced_Shortcode_Listing( $atts );

		if ( 'elementor' !== $settings['renderer'] || empty( $settings['elementor_template_id'] ) || ! $this->get_elementor_loop_templates() ) {
			echo $shortcode->render(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- EPL templates escape their output.
			return;
		}

		$this->render_elementor_template( $shortcode, absint( $settings['elementor_template_id'] ) );
	}

	private function build_shortcode_attributes( $settings ) {
		$keys = array( 'post_type', 'status', 'commercial_listing_type', 'feature', 'feature_id', 'limit', 'offset', 'author', 'agent', 'featured', 'open_house', 'auction', 'template', 'location', 'location_id', 'tools_top', 'tools_bottom', 'sortby', 'orderby_clause', 'sort_order', 'pagination', 'post__in', 'post__not_in' );
		$atts = array(
			'instance_id' => 'elementor-' . $this->get_id(),
			'class'       => 'epl-elementor-advanced-grid',
		);
		foreach ( $keys as $key ) {
			$value = isset( $settings[ $key ] ) ? $settings[ $key ] : '';
			$atts[ $key ] = is_array( $value ) ? implode( ',', array_filter( $value, 'strlen' ) ) : $value;
		}
		return apply_filters( 'epl_elementor_listing_advanced_atts', $atts, $settings, $this );
	}

	private function render_elementor_template( $shortcode, $template_id ) {
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
		$with_css          = true;
		if ( 'on' === $shortcode->attributes['tools_top'] ) {
			do_action( 'epl_property_loop_start', $shortcode->attributes );
		}
		echo '<div class="epl-elementor-advanced-grid">';
		while ( $query->have_posts() ) {
			$query->the_post();
			$property = new EPL_Property_Meta( get_post() ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- EPL loop context restored below.
			echo '<div class="epl-elementor-advanced-item">';
			echo \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $template_id, $with_css ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Elementor-rendered template.
			$with_css = false;
			echo '</div>';
		}
		echo '</div>';
		if ( 'on' === $shortcode->attributes['tools_bottom'] ) {
			do_action( 'epl_property_loop_end' );
		}
		if ( 'on' === $shortcode->attributes['pagination'] ) {
			epl_pagination( array( 'query' => $query ) );
		}
		wp_reset_postdata();
		$property = $original_property; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- Restore caller context.
	}

	private function get_elementor_loop_templates() {
		$widgets_manager = isset( \Elementor\Plugin::$instance->widgets_manager ) ? \Elementor\Plugin::$instance->widgets_manager : null;
		if ( ! $widgets_manager || ! $widgets_manager->get_widget_types( 'loop-grid' ) ) {
			return array();
		}
		$posts = get_posts( array( 'post_type' => 'elementor_library', 'post_status' => 'publish', 'posts_per_page' => -1, 'orderby' => 'title', 'order' => 'ASC', 'meta_key' => '_elementor_template_type', 'meta_value' => 'loop-item' ) ); // phpcs:ignore WordPress.DB.SlowDBQuery
		$options = array();
		foreach ( $posts as $post ) {
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
