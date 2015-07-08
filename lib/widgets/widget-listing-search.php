<?php
/**
 * WIDGET :: Property Search
 *
 * @package     EPL
 * @subpackage  Widget/Search
 * @copyright   Copyright (c) 2014, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class EPL_Widget_Property_Search extends WP_Widget {

	function __construct() {
		parent::WP_Widget( false, $name = __('EPL - Listing Search', 'epl') );
	}

	function widget($args, $instance) {
		
		$defaults = epl_search_get_defaults();
		$instance = wp_parse_args( (array) $instance, $defaults );
		 
		extract( $args );
		
		echo $before_widget;
		
		$title	= apply_filters('widget_title', $instance['title']);
		
		if ( $instance['show_title'] == 'true' && $title ) {
			echo $before_title . $title . $after_title;			
		}
		
		echo epl_shortcode_listing_search_callback($instance);
		
		echo $after_widget;
	}

	function update($new_instance, $old_instance) {
		return array_map('epl_strip_tags',$new_instance);
	}

	function form($instance) {
	
		$defaults 			= epl_search_get_defaults();
		$instance 			= wp_parse_args( (array) $instance, $defaults );
		$instance 			= array_map('epl_esc_attr',$instance);
		extract($instance);
		$post_types			=	$post_type; 
		$fields 			= 	epl_search_widget_fields();
		
		foreach($fields as $field) {
			$field_value	=	${$field['key']};
			epl_widget_render_backend_field($field,$this,$field_value);
		}
		?>
		
		
		<?php 
	}
}

add_action( 'widgets_init', create_function('', 'return register_widget("EPL_Widget_Property_Search");') );


