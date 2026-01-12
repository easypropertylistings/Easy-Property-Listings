<?php
/**
 * Elementor Listing Grid Widget
 *
 * @package     EPL
 * @subpackage  PageBuilders/Elementor/Widgets/Archive
 * @since       3.6.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * EPL_Elementor_Listing_Grid Class
 *
 * @since 3.6.0
 */
class EPL_Elementor_Listing_Grid extends \Elementor\Widget_Base {

	/**
	 * Get widget name.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'epl-listing-grid';
	}

	/**
	 * Get widget title.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'EPL Listing Grid', 'easy-property-listings' );
	}

	/**
	 * Get widget icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-posts-grid';
	}

	/**
	 * Get widget categories.
	 *
	 * @return array
	 */
	public function get_categories() {
		return array( 'epl-archive' );
	}

	/**
	 * Get widget keywords.
	 *
	 * @return array
	 */
	public function get_keywords() {
		return array( 'listing', 'grid', 'property', 'archive', 'epl' );
	}

	/**
	 * Register widget controls.
	 */
	protected function register_controls() {

		// Query Section.
		$this->start_controls_section(
			'section_query',
			array(
				'label' => esc_html__( 'Query', 'easy-property-listings' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'post_type',
			array(
				'label'    => esc_html__( 'Post Type', 'easy-property-listings' ),
				'type'     => \Elementor\Controls_Manager::SELECT2,
				'multiple' => true,
				'options'  => EPL_Elementor::get_post_types(),
				'default'  => array( 'property' ),
			)
		);

		$this->add_control(
			'status',
			array(
				'label'   => esc_html__( 'Listing Status', 'easy-property-listings' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => EPL_Elementor::get_status_options(),
				'default' => 'current',
			)
		);

		$this->add_control(
			'limit',
			array(
				'label'   => esc_html__( 'Number of Listings', 'easy-property-listings' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'min'     => 1,
				'max'     => 50,
				'default' => 6,
			)
		);

		$this->add_control(
			'orderby',
			array(
				'label'   => esc_html__( 'Order By', 'easy-property-listings' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => array(
					'date'       => esc_html__( 'Date', 'easy-property-listings' ),
					'price'      => esc_html__( 'Price', 'easy-property-listings' ),
					'title'      => esc_html__( 'Title', 'easy-property-listings' ),
					'rand'       => esc_html__( 'Random', 'easy-property-listings' ),
					'menu_order' => esc_html__( 'Menu Order', 'easy-property-listings' ),
				),
				'default' => 'date',
			)
		);

		$this->add_control(
			'order',
			array(
				'label'   => esc_html__( 'Order', 'easy-property-listings' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => array(
					'DESC' => esc_html__( 'Descending', 'easy-property-listings' ),
					'ASC'  => esc_html__( 'Ascending', 'easy-property-listings' ),
				),
				'default' => 'DESC',
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
			'template',
			array(
				'label'   => esc_html__( 'Template', 'easy-property-listings' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => array(
					'default' => esc_html__( 'Default', 'easy-property-listings' ),
					'slim'    => esc_html__( 'Slim', 'easy-property-listings' ),
					'table'   => esc_html__( 'Table', 'easy-property-listings' ),
					'card'    => esc_html__( 'Card', 'easy-property-listings' ),
				),
				'default' => 'default',
			)
		);

		$this->add_responsive_control(
			'columns',
			array(
				'label'   => esc_html__( 'Columns', 'easy-property-listings' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => array(
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
				),
				'default' => '3',
			)
		);

		$this->add_control(
			'show_pagination',
			array(
				'label'        => esc_html__( 'Show Pagination', 'easy-property-listings' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'easy-property-listings' ),
				'label_off'    => esc_html__( 'No', 'easy-property-listings' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->end_controls_section();

		// Style Section.
		$this->start_controls_section(
			'section_style',
			array(
				'label' => esc_html__( 'Grid Style', 'easy-property-listings' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'column_gap',
			array(
				'label'      => esc_html__( 'Column Gap', 'easy-property-listings' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
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
					'{{WRAPPER}} .epl-listing-grid' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'row_gap',
			array(
				'label'      => esc_html__( 'Row Gap', 'easy-property-listings' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'default'    => array(
					'size' => 30,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .epl-listing-grid' => 'row-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render widget output.
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		$post_types = $settings['post_type'];
		if ( ! is_array( $post_types ) ) {
			$post_types = array( $post_types );
		}

		$args = array(
			'post_type'      => $post_types,
			'posts_per_page' => absint( $settings['limit'] ),
			'order'          => $settings['order'],
		);

		// Handle orderby.
		if ( 'price' === $settings['orderby'] ) {
			$args['orderby']  = 'meta_value_num';
			$args['meta_key'] = 'property_price';
		} else {
			$args['orderby'] = $settings['orderby'];
		}

		// Handle status filter.
		if ( ! empty( $settings['status'] ) ) {
			$args['meta_query'] = array(
				array(
					'key'   => 'property_status',
					'value' => $settings['status'],
				),
			);
		}

		$query = new WP_Query( $args );

		if ( ! $query->have_posts() ) {
			if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
				echo '<div class="epl-elementor-placeholder">' . esc_html__( 'No listings found.', 'easy-property-listings' ) . '</div>';
			}
			return;
		}

		$columns = absint( $settings['columns'] );
		$template = $settings['template'];

		echo '<div class="epl-listing-grid epl-grid-cols-' . esc_attr( $columns ) . '">';

		global $post;
		$original_post = $post;

		while ( $query->have_posts() ) {
			$query->the_post();

			global $property;
			$property = new EPL_Property_Meta( get_post() );

			echo '<div class="epl-listing-grid-item">';
			epl_property_blog( $template );
			echo '</div>';
		}

		$post = $original_post; // Restore original post data.

		echo '</div>';

		if ( 'yes' === $settings['show_pagination'] ) {
			epl_pagination( $query );
		}
	}
}
