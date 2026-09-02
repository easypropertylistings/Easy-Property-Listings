<?php
/**
 * Elementor Listing Results Widget
 *
 * @package     EPL
 * @subpackage  PageBuilders/Elementor/Widgets/Archive
 * @since       3.7.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * EPL_Elementor_Listing_Results Class
 *
 * Thin Elementor wrapper around `[listing_results]`. Iterates WordPress's
 * real main query, so it is the building block for an "Archive Page" —
 * drop it (with EPL Listing Search and EPL Pagination) on any page assigned
 * as the listings archive page, and it renders the actual archive, search
 * or taxonomy results with real pagination.
 *
 * @since 3.7.0
 */
class EPL_Elementor_Listing_Results extends \Elementor\Widget_Base {
	use EPL_Elementor_Dynamic_Widget;

	/**
	 * Get widget name.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'epl-listing-results';
	}

	/**
	 * Get widget title.
	 *
	 * Renamed from "EPL Listing Results" to "EPL Archive Results" for
	 * clarity — get_name() (the internal widgetType, e.g. in already-saved
	 * page JSON) is left unchanged so existing pages keep rendering.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Listings Results', 'easy-property-listings' );
	}

	/**
	 * Get widget icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-archive-title';
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
		return array( 'listing', 'results', 'archive', 'query', 'loop', 'property', 'epl' );
	}

	/**
	 * Register widget controls.
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_content',
			array(
				'label' => esc_html__( 'Listing Results', 'easy-property-listings' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'results_notice',
			array(
				'type'            => \Elementor\Controls_Manager::RAW_HTML,
				'raw'             => esc_html__( 'Shows whatever listings the visitor is actually looking at — the archive, a search, or a location/feature page — with real pagination. Pair it with the EPL Listing Search widget above it for a working search bar, and EPL Pagination below it for page numbers. Only use this on a page assigned as the "Listings Archive Page" in EPL settings; elsewhere there is no real listings query for it to show.', 'easy-property-listings' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			)
		);

		$this->add_control(
			'card_template_id',
			array(
				'label'       => esc_html__( 'Listing Card', 'easy-property-listings' ),
				'type'        => \Elementor\Controls_Manager::SELECT2,
				'label_block' => true,
				'options'     => $this->get_card_template_options(),
				'default'     => (string) EPL_Elementor_Template_Seeder::get_default_card_id(),
				'description' => esc_html__( 'Pick which card design listings use on this page. "EPL Card" is the ready-made design that ships with the plugin — edit it under Elementor > Templates > Saved Templates, or duplicate it to build your own. Choose "EPL Built-in Card" to fall back to the plugin\'s original PHP design instead. To use a different card for a different listing type, assign a separate archive page per listing type in EPL Settings > Elementor Templates.', 'easy-property-listings' ),
			)
		);

		$this->add_control(
			'tools_top',
			array(
				'label'        => esc_html__( 'Show Tools Above Results', 'easy-property-listings' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'easy-property-listings' ),
				'label_off'    => esc_html__( 'No', 'easy-property-listings' ),
				'return_value' => 'yes',
				'default'      => '',
				'description'  => esc_html__( 'Sorter and grid/list view toggle.', 'easy-property-listings' ),
			)
		);

		$this->add_control(
			'tools_bottom',
			array(
				'label'        => esc_html__( 'Show Tools Below Results', 'easy-property-listings' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'easy-property-listings' ),
				'label_off'    => esc_html__( 'No', 'easy-property-listings' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_control(
			'pagination',
			array(
				'label'        => esc_html__( 'Show Pagination', 'easy-property-listings' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'easy-property-listings' ),
				'label_off'    => esc_html__( 'No', 'easy-property-listings' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_layout',
			array(
				'label' => esc_html__( 'Grid Layout', 'easy-property-listings' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'grid_mode',
			array(
				'label'     => esc_html__( 'Layout', 'easy-property-listings' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'default'   => 'columns',
				'options'   => array(
					'columns' => esc_html__( 'Fixed Columns', 'easy-property-listings' ),
					'auto'    => esc_html__( 'Auto-Fit by Card Width', 'easy-property-listings' ),
				),
				'selectors' => array(
					'{{WRAPPER}} .epl-template-blog'     => 'display: grid;',
					'{{WRAPPER}} .epl-template-blog > *' => 'min-width: 0; box-sizing: border-box;',
				),
			)
		);

		$this->add_responsive_control(
			'grid_columns',
			array(
				'label'          => esc_html__( 'Columns', 'easy-property-listings' ),
				'type'           => \Elementor\Controls_Manager::SELECT,
				'default'        => '3',
				'tablet_default' => '2',
				'mobile_default' => '1',
				'options'        => array(
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
				),
				'condition'      => array( 'grid_mode' => 'columns' ),
				'selectors'      => array(
					'{{WRAPPER}} .epl-template-blog' => 'grid-template-columns: repeat({{VALUE}}, minmax(0, 1fr));',
				),
			)
		);

		$this->add_responsive_control(
			'card_min_width',
			array(
				'label'      => esc_html__( 'Minimum Card Width', 'easy-property-listings' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 150,
						'max' => 600,
					),
				),
				'default'    => array(
					'size' => 280,
					'unit' => 'px',
				),
				'condition'  => array( 'grid_mode' => 'auto' ),
				'selectors'  => array(
					'{{WRAPPER}} .epl-template-blog' => 'grid-template-columns: repeat(auto-fill, minmax({{SIZE}}{{UNIT}}, 1fr));',
				),
			)
		);

		$this->add_responsive_control(
			'grid_gap',
			array(
				'label'      => esc_html__( 'Gap', 'easy-property-listings' ),
				'type'       => \Elementor\Controls_Manager::GAPS,
				'size_units' => array( 'px', '%', 'em' ),
				'default'    => array(
					'row'      => '32',
					'column'   => '24',
					'unit'     => 'px',
					'isLinked' => false,
				),
				'selectors'  => array(
					'{{WRAPPER}} .epl-template-blog' => 'gap: {{ROW}}{{UNIT}} {{COLUMN}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Published EPL Listing Card documents, keyed by post ID, plus a "Site
	 * Default" option meaning "defer to whichever card is marked default".
	 *
	 * @return array
	 */
	private function get_card_template_options() {
		$options = array(
			'' => esc_html__( 'EPL Built-in Card', 'easy-property-listings' ),
		);

		$cards = get_posts(
			array(
				'post_type'      => 'elementor_library',
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'orderby'        => 'title',
				'order'          => 'ASC',
				'meta_key'       => '_elementor_template_type', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- elementor_library is small; matches existing EPL Elementor queries.
				'meta_value'     => EPL_Elementor_Loop_Card_Document::TYPE, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			)
		);

		$bundled_default = EPL_Elementor_Template_Seeder::get_default_card_id();

		foreach ( $cards as $card ) {
			$options[ $card->ID ] = ( $card->ID === $bundled_default )
				/* translators: %s: template name. */
				? sprintf( esc_html__( '%s (Default)', 'easy-property-listings' ), $card->post_title )
				: $card->post_title;
		}

		return $options;
	}

	/**
	 * Render widget output.
	 */
	protected function render() {
		if ( EPL_Elementor::is_editor() ) {
			$this->render_editor_preview();
			return;
		}

		$settings = $this->get_settings_for_display();
		$atts     = array(
			'tools_top'    => ! empty( $settings['tools_top'] ) ? 'on' : 'off',
			'tools_bottom' => ! empty( $settings['tools_bottom'] ) ? 'on' : 'off',
			'pagination'   => ! empty( $settings['pagination'] ) ? 'on' : 'off',
		);

		$card_override = ! empty( $settings['card_template_id'] ) ? absint( $settings['card_template_id'] ) : 0;
		if ( $card_override ) {
			EPL_Elementor_Template_Router::begin_card_override( $card_override );
		}

		echo epl_listing_results_loop_callback( $atts ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in the shortcode callback.

		if ( $card_override ) {
			EPL_Elementor_Template_Router::end_card_override();
		}
	}

	/**
	 * Editor canvas has no real archive/search/taxonomy query, so render a
	 * small, clearly-labelled sample loop instead of nothing.
	 */
	private function render_editor_preview() {
		echo '<div class="epl-elementor-listing-results-preview">';
		echo '<p class="epl-elementor-editor-notice">' . esc_html__( 'Preview only — shows the real archive, search or taxonomy results on the front end.', 'easy-property-listings' ) . '</p>';

		$sample_posts = get_posts(
			array(
				'post_type'      => array_keys( epl_get_active_post_types() ),
				'post_status'    => 'publish',
				'posts_per_page' => 6,
			)
		);

		if ( empty( $sample_posts ) ) {
			echo '<p>' . esc_html__( 'No published listings found to preview.', 'easy-property-listings' ) . '</p>';
			echo '</div>';
			return;
		}

		$settings      = $this->get_settings_for_display();
		$card_override = ! empty( $settings['card_template_id'] ) ? absint( $settings['card_template_id'] ) : 0;
		if ( $card_override ) {
			EPL_Elementor_Template_Router::begin_card_override( $card_override );
		}

		global $post, $property;
		$original_post      = $post;
		$original_property  = isset( $property ) ? $property : null;

		echo '<div class="epl-template-blog">';
		foreach ( $sample_posts as $sample_post ) {
			$post     = $sample_post; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- Restored below.
			$property = new EPL_Property_Meta( $sample_post ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- Restored below.
			setup_postdata( $post );
			do_action( 'epl_property_blog' );
		}
		echo '</div>';

		if ( $card_override ) {
			EPL_Elementor_Template_Router::end_card_override();
		}

		$post     = $original_post; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- Restore the editor's own post.
		$property = $original_property; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- Restore.
		if ( $post instanceof WP_Post ) {
			setup_postdata( $post );
		} else {
			wp_reset_postdata();
		}

		echo '</div>';
	}
}
