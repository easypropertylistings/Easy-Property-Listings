<?php
/**
 * WIDGET :: Contact Capture
 *
 * @package     EPL
 * @subpackage  Widget/Contact
 * @copyright   Copyright (c) 2016, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * EPL_Widget_Contact_Capture class
 *
 * @since 3.0
 */
class EPL_Widget_Contact_Capture extends WP_Widget {

	function __construct() {
		parent::__construct( false, $name = __('EPL - Contact Form', 'easy-property-listings'), array( 'description' => __( 'Add contact form to a sidebar.', 'easy-property-listings' ) ) );
	}

	function widget($args, $instance) {

		$defaults = epl_contact_capture_get_widget_defaults();

		$instance = wp_parse_args( (array) $instance, $defaults );

		extract( $args );
		$title 		    = apply_filters('widget_title', $instance['title']);

		echo $before_widget;
		if ( $title )
			echo $before_title . $title . $after_title;
		echo epl_contact_capture_form($instance);

		echo $after_widget;
	}

	function update($new_instance, $old_instance) {
		$instance 	= $old_instance;
		$all_fields = epl_contact_capture_widget_form_fields();
		foreach($all_fields as $all_field) {
			$instance[$all_field['key']] = epl_strip_tags( $new_instance[$all_field['key']] , apply_filters( 'epl_contact_form_description_allowed_tags' , '<br><p><strong><div><span><hr><img>' ) );
		}
		return $instance;
	}

	function form($instance) {
		$defaults = epl_contact_capture_get_widget_defaults();

		$instance = wp_parse_args( (array) $instance, $defaults );
		$instance = array_map( 'epl_esc_attr', $instance );
		extract( $instance );
		$fields     = epl_contact_capture_widget_form_fields();
		foreach ( $fields as $field ) {
			$field_value = ${$field['key']};
			epl_widget_render_backend_field( $field, $this, $field_value );
		}
	}
}
add_action( 'widgets_init', create_function('', 'return register_widget("EPL_Widget_Contact_Capture");') );
