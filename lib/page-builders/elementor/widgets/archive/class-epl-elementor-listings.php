<?php
/**
 * Elementor Free EPL Listings Widget.
 *
 * @package     EPL
 * @subpackage  PageBuilders/Elementor/Widgets/Archive
 * @since       3.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Render EPL-native listing templates when Elementor Loop Grid is unavailable. */
class EPL_Elementor_Listings extends \Elementor\Widget_Base {
	use EPL_Elementor_Dynamic_Widget;

	public function get_name() {
		return 'epl-listings';
	}

	public function get_title() {
		return esc_html__( 'EPL Listings', 'easy-property-listings' );
	}

	public function get_icon() {
		return 'eicon-posts-grid';
	}

	public function get_categories() {
		return array( 'epl-archive' );
	}

	public function get_keywords() {
		return array( 'listing', 'grid', 'property', 'archive', 'template', 'epl' );
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_query',
			array(
				'label' => esc_html__( 'Query', 'easy-property-listings' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'query_source',
			array(
				'label'   => esc_html__( 'Source', 'easy-property-listings' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'custom',
				'options' => array(
					'custom'          => esc_html__( 'Custom Query', 'easy-property-listings' ),
					'current_archive' => esc_html__( 'Current Archive Query', 'easy-property-listings' ),
				),
			)
		);

		$this->add_control(
			'post_type',
			array(
				'label'       => esc_html__( 'Post Types', 'easy-property-listings' ),
				'type'        => \Elementor\Controls_Manager::SELECT2,
				'multiple'    => true,
				'label_block' => true,
				'options'     => epl_get_active_post_types(),
				'default'     => array( 'property' ),
				'condition'   => array( 'query_source' => 'custom' ),
			)
		);

		$status_options = EPL_Elementor::get_status_options();
		unset( $status_options[''], $status_options['withdrawn'], $status_options['offmarket'] );
		$this->add_control(
			'status',
			array(
				'label'       => esc_html__( 'Listing Status', 'easy-property-listings' ),
				'type'        => \Elementor\Controls_Manager::SELECT2,
				'multiple'    => true,
				'label_block' => true,
				'options'     => $status_options,
				'default'     => array( 'current' ),
				'description' => esc_html__( 'Leave empty to include all statuses EPL permits on the front end.', 'easy-property-listings' ),
				'condition'   => array( 'query_source' => 'custom' ),
			)
		);

		$this->add_control(
			'limit',
			array(
				'label'     => esc_html__( 'Listings Per Page', 'easy-property-listings' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'min'       => 1,
				'max'       => 100,
				'default'   => 10,
				'condition' => array( 'query_source' => 'custom' ),
			)
		);

		$this->add_control(
			'orderby',
			array(
				'label'     => esc_html__( 'Order By', 'easy-property-listings' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'default'   => 'date',
				'options'   => array(
					'date'       => esc_html__( 'Date', 'easy-property-listings' ),
					'price'      => esc_html__( 'Price', 'easy-property-listings' ),
					'title'      => esc_html__( 'Title', 'easy-property-listings' ),
					'rand'       => esc_html__( 'Random', 'easy-property-listings' ),
					'menu_order' => esc_html__( 'Menu Order', 'easy-property-listings' ),
				),
				'condition' => array( 'query_source' => 'custom' ),
			)
		);

		$this->add_control(
			'order',
			array(
				'label'     => esc_html__( 'Order', 'easy-property-listings' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'default'   => 'DESC',
				'options'   => array(
					'DESC' => esc_html__( 'Descending', 'easy-property-listings' ),
					'ASC'  => esc_html__( 'Ascending', 'easy-property-listings' ),
				),
				'condition' => array( 'query_source' => 'custom' ),
			)
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'section_layout',
			array(
				'label' => esc_html__( 'Layout', 'easy-property-listings' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);
		$this->add_control(
			'template',
			array(
				'label'   => esc_html__( 'EPL Template', 'easy-property-listings' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'default',
				'options' => array(
					'default'    => esc_html__( 'Default', 'easy-property-listings' ),
					'card'       => esc_html__( 'Card', 'easy-property-listings' ),
					'slim'       => esc_html__( 'Slim', 'easy-property-listings' ),
					'table'      => esc_html__( 'Table', 'easy-property-listings' ),
					'table-open' => esc_html__( 'Table with Inspection', 'easy-property-listings' ),
				),
			)
		);
		$this->add_responsive_control(
			'columns',
			array(
				'label'          => esc_html__( 'Columns', 'easy-property-listings' ),
				'type'           => \Elementor\Controls_Manager::SELECT,
				'default'        => '3',
				'tablet_default' => '2',
				'mobile_default' => '1',
				'options'        => array( '1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6' ),
				'selectors'      => array( '{{WRAPPER}} .epl-elementor-listings-grid' => '--epl-elementor-listings-columns: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'show_pagination',
			array(
				'label'        => esc_html__( 'Pagination', 'easy-property-listings' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'easy-property-listings' ),
				'label_off'    => esc_html__( 'Hide', 'easy-property-listings' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'section_grid_style',
			array(
				'label' => esc_html__( 'Grid', 'easy-property-listings' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_responsive_control(
			'column_gap',
			array(
				'label'      => esc_html__( 'Column Gap', 'easy-property-listings' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'default'    => array( 'size' => 24, 'unit' => 'px' ),
				'selectors'  => array( '{{WRAPPER}} .epl-elementor-listings-grid' => 'column-gap: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'row_gap',
			array(
				'label'      => esc_html__( 'Row Gap', 'easy-property-listings' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'default'    => array( 'size' => 32, 'unit' => 'px' ),
				'selectors'  => array( '{{WRAPPER}} .epl-elementor-listings-grid' => 'row-gap: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$query    = $this->get_listings_query( $settings );

		if ( ! $query->have_posts() ) {
			if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
				echo '<div class="epl-elementor-placeholder">' . esc_html__( 'No listings found for this query.', 'easy-property-listings' ) . '</div>';
			} else {
				do_action( 'epl_shortcode_results_message', 'default' );
			}
			return;
		}

		global $property;
		$original_property = isset( $property ) ? $property : null;
		$template          = ! empty( $settings['template'] ) ? sanitize_key( $settings['template'] ) : 'default';

		echo '<div class="epl-elementor-listings">';
		echo '<div class="epl-elementor-listings-grid epl-clearfix">';
		while ( $query->have_posts() ) {
			$query->the_post();
			$property = new EPL_Property_Meta( get_post() ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- EPL template context restored below.
			echo '<div class="epl-elementor-listings-item">';
			epl_property_blog( $template );
			echo '</div>';
		}
		echo '</div>';

		if ( 'yes' === $settings['show_pagination'] ) {
			epl_pagination( array( 'query' => $query ) );
		}
		echo '</div>';

		wp_reset_postdata();
		$property = $original_property; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- Restore caller context.
	}

	/** Build either the archive query or an EPL-aware custom query. */
	private function get_listings_query( $settings ) {
		if ( 'current_archive' === $settings['query_source'] && isset( $GLOBALS['wp_query'] ) && $GLOBALS['wp_query'] instanceof WP_Query ) {
			$query = clone $GLOBALS['wp_query'];
			$query->rewind_posts();

			// Theme Builder preview requests can retain the template-library main
			// query. Fall back to listings so the editor never shows template posts.
			$first_post = ! empty( $query->posts[0] ) ? $query->posts[0] : null;
			if ( ! \Elementor\Plugin::$instance->editor->is_edit_mode() || ( $first_post instanceof WP_Post && is_epl_post( $first_post->post_type ) ) ) {
				return $query;
			}
		}

		$post_types = ! empty( $settings['post_type'] ) ? (array) $settings['post_type'] : array_keys( epl_get_active_post_types() );
		$post_types = array_values( array_intersect( array_map( 'sanitize_key', $post_types ), array_keys( epl_get_active_post_types() ) ) );
		if ( empty( $post_types ) ) {
			$post_types = array_keys( epl_get_active_post_types() );
		}
		$statuses   = ! empty( $settings['status'] ) ? array_filter( array_map( 'sanitize_key', (array) $settings['status'] ) ) : array();
		$paged      = max( 1, absint( get_query_var( 'paged' ) ), absint( get_query_var( 'page' ) ) );
		$args       = array(
			'post_type'      => $post_types,
			'posts_per_page' => max( 1, absint( $settings['limit'] ) ),
			'paged'          => $paged,
			'order'          => 'ASC' === $settings['order'] ? 'ASC' : 'DESC',
			'orderby'        => sanitize_key( $settings['orderby'] ),
			'meta_query'     => array(),
		);

		if ( 'price' === $args['orderby'] && 1 === count( $post_types ) ) {
			$args['orderby']  = 'meta_value_num';
			$args['meta_key'] = is_epl_rental_post( reset( $post_types ) ) ? 'property_rent' : 'property_price';
		} elseif ( 'price' === $args['orderby'] ) {
			// EPL synchronizes every listing type to this normalized numeric key,
			// allowing sale, rental, rural and commercial results to sort together.
			$args['orderby']  = 'meta_value_num';
			$args['meta_key'] = 'property_price_global';
		}

		if ( $statuses ) {
			$args['meta_query'][] = array( 'key' => 'property_status', 'value' => $statuses, 'compare' => 'IN' );
		} else {
			$args['meta_query'][] = array(
				'relation' => 'OR',
				array( 'key' => 'property_status', 'value' => epl_hide_listing_statuses(), 'compare' => 'NOT IN' ),
				array( 'key' => 'property_status', 'compare' => 'NOT EXISTS' ),
			);
		}

		$args = apply_filters( 'epl_elementor_listings_query_args', $args, $settings );
		return new WP_Query( $args );
	}
}
