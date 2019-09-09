<?php
/**
 * WIDGET :: Property Search
 *
 * @package     EPL
 * @subpackage  Widget/Search
 * @copyright   Copyright (c) 2019, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * EPL_Widget_Property_Search class
 *
 * @since 1.0
 */
class EPL_Widget_Property_Search extends WP_Widget {

	/**
	 * Construct the widget.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		parent::__construct( false, $name = __( 'EPL - Listing Search', 'easy-property-listings' ), array( 'description' => __( 'Search listings.', 'easy-property-listings' ) ) );
		// Widget name for filter: epl_property_search.
	}

	/**
	 * Widget function.
	 *
	 * @since 1.0
	 * @param array $args Widget arguments.
	 * @param array $instance Widget instance.
	 */
	public function widget( $args, $instance ) {

		// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped

		$defaults = epl_search_get_defaults();
		$instance = wp_parse_args( (array) $instance, $defaults );

		foreach ( $args as $arg_key => $arg_val ) {

			${$arg_key} = $arg_val;
		}

		echo $before_widget;

		$title = apply_filters( 'widget_title', $instance['title'] );

		if ( $title ) {
			echo $before_title . $title . $after_title;
		}

		if ( ! is_admin() ) {
			echo epl_shortcode_listing_search_callback( $instance );
		}

		echo $after_widget;
	}

	/**
	 * Widget update.
	 *
	 * @param array $new_instance New values.
	 * @param array $old_instance Old values.
	 *
	 * @return array
	 * @since 1.0
	 */
	public function update( $new_instance, $old_instance ) {
		$instance   = $old_instance;
		$all_fields = epl_search_widget_fields();
		foreach ( $all_fields as $all_field ) {

			if ( isset( $new_instance[ $all_field['key'] ] ) ) {
				$instance[ $all_field['key'] ] = epl_strip_tags( $new_instance[ $all_field['key'] ] );
			} else {

				$instance[ $all_field['key'] ] = '';

			}
		}
		return $instance;
	}

	/**
	 * Render the widget form.
	 *
	 * @since 1.0
	 * @param array $instance options.
	 */
	public function form( $instance ) {

		// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped

		$defaults = epl_search_get_defaults();
		$instance = wp_parse_args( (array) $instance, $defaults );
		$instance = array_map( 'epl_esc_attr', $instance );

		foreach ( $instance as $in_key => $in_val ) {

			${$in_key} = $in_val;
		}

		$post_types = $post_type;
		$fields     = epl_search_widget_fields();

		foreach ( $fields as $field ) {
			$field_value = ${$field['key']};
			epl_widget_render_backend_field( $field, $this, $field_value );
		}
	}
}

/**
 * Register Listing Search Widget.
 *
 * Registers the EPL Widgets.
 *
 * @since 3.2.2
 * @return void
 */
function epl_register_widget_property_search() {
	register_widget( 'EPL_Widget_Property_Search' );
}
add_action( 'widgets_init', 'epl_register_widget_property_search' );
