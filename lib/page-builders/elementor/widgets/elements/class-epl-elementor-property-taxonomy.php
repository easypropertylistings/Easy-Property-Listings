<?php
/**
 * Elementor Property Taxonomy Widget.
 *
 * @package    EPL
 * @subpackage PageBuilders/Elementor/Widgets/Elements
 * @since      3.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Render terms assigned to an EPL listing. */
class EPL_Elementor_Property_Taxonomy extends \Elementor\Widget_Base {
	use EPL_Elementor_Dynamic_Widget;

	public function get_name() {
		return 'epl-property-taxonomy';
	}

	public function get_title() {
		return esc_html__( 'EPL Property Taxonomy', 'easy-property-listings' );
	}

	public function get_icon() {
		return 'eicon-tags';
	}

	public function get_categories() {
		return array( 'epl-elements' );
	}

	public function get_keywords() {
		return array( 'property', 'taxonomy', 'features', 'location', 'suburb', 'terms', 'epl' );
	}

	private function get_taxonomies() {
		$options    = array();
		$taxonomies = get_object_taxonomies( epl_get_core_post_types(), 'objects' );
		foreach ( $taxonomies as $taxonomy ) {
			if ( $taxonomy->public ) {
				$options[ $taxonomy->name ] = $taxonomy->labels->singular_name . ' (' . $taxonomy->name . ')';
			}
		}
		return $options;
	}

	protected function register_controls() {
		$this->start_controls_section( 'section_content', array( 'label' => esc_html__( 'Taxonomy', 'easy-property-listings' ) ) );
		$this->add_control( 'taxonomy', array( 'label' => esc_html__( 'Taxonomy', 'easy-property-listings' ), 'type' => \Elementor\Controls_Manager::SELECT, 'options' => $this->get_taxonomies(), 'default' => 'tax_feature' ) );
		$this->add_control( 'layout', array( 'label' => esc_html__( 'Layout', 'easy-property-listings' ), 'type' => \Elementor\Controls_Manager::SELECT, 'default' => 'inline', 'options' => array( 'inline' => esc_html__( 'Inline', 'easy-property-listings' ), 'list' => esc_html__( 'List', 'easy-property-listings' ) ) ) );
		$this->add_control( 'separator', array( 'label' => esc_html__( 'Separator', 'easy-property-listings' ), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => ', ', 'condition' => array( 'layout' => 'inline' ) ) );
		$this->add_control( 'link_terms', array( 'label' => esc_html__( 'Link Terms', 'easy-property-listings' ), 'type' => \Elementor\Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => 'yes' ) );
		$this->add_control( 'show_label', array( 'label' => esc_html__( 'Show Taxonomy Label', 'easy-property-listings' ), 'type' => \Elementor\Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => '' ) );
		$this->add_control( 'custom_label', array( 'label' => esc_html__( 'Custom Label', 'easy-property-listings' ), 'type' => \Elementor\Controls_Manager::TEXT, 'condition' => array( 'show_label' => 'yes' ) ) );
		$this->add_control( 'empty_message', array( 'label' => esc_html__( 'Empty Value', 'easy-property-listings' ), 'type' => \Elementor\Controls_Manager::TEXT ) );
		$this->end_controls_section();

		$this->start_controls_section( 'section_style', array( 'label' => esc_html__( 'Terms', 'easy-property-listings' ), 'tab' => \Elementor\Controls_Manager::TAB_STYLE ) );
		$this->add_control( 'color', array( 'label' => esc_html__( 'Text Color', 'easy-property-listings' ), 'type' => \Elementor\Controls_Manager::COLOR, 'selectors' => array( '{{WRAPPER}} .epl-property-taxonomy' => 'color: {{VALUE}};', '{{WRAPPER}} .epl-property-taxonomy a' => 'color: {{VALUE}};' ) ) );
		$this->add_control( 'link_hover_color', array( 'label' => esc_html__( 'Link Hover Color', 'easy-property-listings' ), 'type' => \Elementor\Controls_Manager::COLOR, 'selectors' => array( '{{WRAPPER}} .epl-property-taxonomy a:hover' => 'color: {{VALUE}};' ) ) );
		$this->add_group_control( \Elementor\Group_Control_Typography::get_type(), array( 'name' => 'typography', 'selector' => '{{WRAPPER}} .epl-property-taxonomy' ) );
		$this->add_responsive_control( 'term_gap', array( 'label' => esc_html__( 'List Gap', 'easy-property-listings' ), 'type' => \Elementor\Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 0, 'max' => 100 ) ), 'selectors' => array( '{{WRAPPER}} .epl-property-taxonomy-list' => 'gap: {{SIZE}}{{UNIT}};' ), 'condition' => array( 'layout' => 'list' ) ) );
		$this->add_responsive_control( 'alignment', array( 'label' => esc_html__( 'Alignment', 'easy-property-listings' ), 'type' => \Elementor\Controls_Manager::CHOOSE, 'options' => array( 'left' => array( 'title' => esc_html__( 'Left', 'easy-property-listings' ), 'icon' => 'eicon-text-align-left' ), 'center' => array( 'title' => esc_html__( 'Center', 'easy-property-listings' ), 'icon' => 'eicon-text-align-center' ), 'right' => array( 'title' => esc_html__( 'Right', 'easy-property-listings' ), 'icon' => 'eicon-text-align-right' ) ), 'selectors' => array( '{{WRAPPER}} .epl-property-taxonomy' => 'text-align: {{VALUE}};' ) ) );
		$this->end_controls_section();
	}

	protected function render() {
		$property = EPL_Elementor::setup_listing_context();
		if ( ! $property ) {
			if ( EPL_Elementor::is_editor() ) {
				echo '<div class="epl-elementor-placeholder">' . esc_html__( 'Property Taxonomy - No listings found.', 'easy-property-listings' ) . '</div>';
			}
			EPL_Elementor::restore_listing_context();
			return;
		}

		$settings = $this->get_settings_for_display();
		$taxonomy = sanitize_key( $settings['taxonomy'] );
		$object    = get_taxonomy( $taxonomy );
		$terms     = $object ? wp_get_post_terms( $property->post->ID, $taxonomy ) : array();

		if ( is_wp_error( $terms ) || empty( $terms ) ) {
			if ( ! empty( $settings['empty_message'] ) ) {
				echo '<div class="epl-property-taxonomy epl-property-taxonomy-empty">' . esc_html( $settings['empty_message'] ) . '</div>';
			}
			EPL_Elementor::restore_listing_context();
			return;
		}

		$items = array();
		foreach ( $terms as $term ) {
			$name = esc_html( $term->name );
			if ( 'yes' === $settings['link_terms'] ) {
				$link = get_term_link( $term );
				$name = is_wp_error( $link ) ? $name : '<a href="' . esc_url( $link ) . '">' . $name . '</a>';
			}
			$items[] = '<span class="epl-property-taxonomy-term epl-property-taxonomy-term-' . esc_attr( $term->slug ) . '">' . $name . '</span>';
		}

		$label = '';
		if ( 'yes' === $settings['show_label'] ) {
			$label = $settings['custom_label'] ? $settings['custom_label'] : $object->labels->name;
			$label = '<span class="epl-property-taxonomy-label">' . esc_html( $label ) . '</span>';
		}

		if ( 'list' === $settings['layout'] ) {
			$content = '<ul class="epl-property-taxonomy-list"><li>' . implode( '</li><li>', $items ) . '</li></ul>';
		} else {
			$content = '<span class="epl-property-taxonomy-inline">' . implode( esc_html( $settings['separator'] ), $items ) . '</span>';
		}

		echo '<div class="epl-property-taxonomy epl-property-taxonomy-' . esc_attr( $taxonomy ) . '">' . wp_kses_post( $label . $content ) . '</div>';
		EPL_Elementor::restore_listing_context();
	}
}
