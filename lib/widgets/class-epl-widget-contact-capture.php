<?php
/**
 * WIDGET :: Contact Capture
 *
 * @package     EPL
 * @subpackage  Widget/Contact
 * @copyright   Copyright (c) 2019, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * EPL_Widget_Contact_Capture class
 *
 * @since 3.0
 */
class EPL_Widget_Contact_Capture extends WP_Widget {

	/**
	 * Construct the widget.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		parent::__construct( false, $name = __( 'EPL - Contact Form', 'easy-property-listings' ), array( 'description' => __( 'Add contact form to a sidebar.', 'easy-property-listings' ) ) );
		// Widget name for filter: epl_contact_capture.
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

		$defaults = epl_contact_capture_get_widget_defaults();

		$instance = wp_parse_args( (array) $instance, $defaults );

		foreach ( $args as $arg_key => $arg_val ) {

			${$arg_key} = $arg_val;
		}

		$title = apply_filters( 'widget_title', $instance['title'] );

		echo $before_widget;
		if ( $title ) {
			echo $before_title . $title . $after_title;
		}
		echo epl_contact_capture_form( $instance );

		echo $after_widget;
	}

	/**
	 * Widget update.
	 *
	 * @param array $new_instance Old values.
	 * @param array $old_instance New values.
	 *
	 * @return array
	 * @since 1.0
	 */
	public function update( $new_instance, $old_instance ) {
		$instance   = $old_instance;
		$all_fields = epl_contact_capture_widget_form_fields();
		foreach ( $all_fields as $all_field ) {

			if ( isset( $new_instance[ $all_field['key'] ] ) ) {
				$instance[ $all_field['key'] ] = epl_strip_tags( $new_instance[ $all_field['key'] ], apply_filters( 'epl_contact_form_description_allowed_tags', '<br><p><strong><div><span><hr><img>' ) );
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
		$defaults = epl_contact_capture_get_widget_defaults();

		$instance = wp_parse_args( (array) $instance, $defaults );
		$instance = array_map( 'epl_esc_attr', $instance );

		foreach ( $instance as $in_key => $in_val ) {

			${$in_key} = $in_val;
		}

		$fields = epl_contact_capture_widget_form_fields();
		foreach ( $fields as $field ) {
			$field_value = ${$field['key']};
			epl_widget_render_backend_field( $field, $this, $field_value );
		}
	}
}

/**
 * Register Contact Widget.
 *
 * Registers the EPL Widgets.
 *
 * @since 3.2.2
 * @return void
 */
function epl_register_widget_contact_form() {
	register_widget( 'EPL_Widget_Contact_Capture' );
}
add_action( 'widgets_init', 'epl_register_widget_contact_form' );
