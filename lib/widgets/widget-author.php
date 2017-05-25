<?php
/**
 * WIDGET :: Author
 *
 * @package     EPL
 * @subpackage  Widget/Author
 * @copyright   Copyright (c) 2014, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * EPL_Widget_Author class
 *
 * @since 1.0
 */
class EPL_Widget_Author extends WP_Widget {

	function __construct() {
		parent::__construct( false, $name = __('EPL - Author', 'easy-property-listings'), array( 'description' => __( 'Add an Author profile to a sidebar.', 'easy-property-listings' ) ) );
	}

	function widget($args, $instance) {

		$defaults = array(
			'title'		=>	'',
			'display'	=>	0,
			'd_image'	=>	0,
			'd_icons'	=>	0,
			'd_bio'		=>	0
		);

		$instance = wp_parse_args( (array) $instance, $defaults );

		extract( $args );
		$title 		= apply_filters('widget_title', $instance['title']);
		$display	= $instance['display'];
		$d_image	= $instance['d_image'];
		$d_icons	= $instance['d_icons'];
		$d_bio		= $instance['d_bio'];

		if ( is_epl_post_single() ) {
			// Only retrieve global $property variable if singluar
			global $property;
			$hide_author_box	=	$property->get_property_meta('property_agent_hide_author_box');

			if ( $hide_author_box == 'yes' ) {
				// Hide Author Box
			} else {
				echo $before_widget;
				if ( $title )
					echo $before_title . $title . $after_title;
					epl_property_author_box_simple_card_tall( $d_image , $d_icons , $d_bio);

				echo $after_widget;
			}

		} else {

			echo $before_widget;
			if ( $title )
				echo $before_title . $title . $after_title;
				epl_property_author_box_simple_card_tall( $d_image , $d_icons , $d_bio);

			echo $after_widget;
		}
	}

	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['display'] = strip_tags($new_instance['display']);
		$instance['d_image'] = strip_tags($new_instance['d_image']);
		$instance['d_icons'] = strip_tags($new_instance['d_icons']);
		$instance['d_bio'] = strip_tags($new_instance['d_bio']);
		return $instance;
	}

	function form($instance) {
		$defaults = array(
			'title'		=>	'',
			'display'	=>	0,
			'd_image'	=>	0,
			'd_icons'	=>	0,
			'd_bio'		=>	0
		);


		$instance = wp_parse_args( (array) $instance, $defaults );

		$title 		= esc_attr($instance['title']);
		$display	= esc_attr($instance['display']);
		$d_image	= esc_attr($instance['d_image']);
		$d_icons	= esc_attr($instance['d_icons']);
		$d_bio		= esc_attr($instance['d_bio']); ?>

		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'easy-property-listings'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
		</p>

		<p>
			<input type="checkbox" id="<?php echo $this->get_field_id('d_image'); ?>" name="<?php echo $this->get_field_name('d_image'); ?>" <?php if ($instance['d_image']) echo 'checked="checked"' ?> />
			<label for="<?php echo $this->get_field_id('d_image'); ?>"><?php _e('Display Author Gravatar', 'easy-property-listings'); ?></label>
		</p>

		<p>
			<input type="checkbox" id="<?php echo $this->get_field_id('d_icons'); ?>" name="<?php echo $this->get_field_name('d_icons'); ?>" <?php if ($instance['d_icons']) echo 'checked="checked"' ?> />
			<label for="<?php echo $this->get_field_id('d_icons'); ?>"><?php _e('Display Icons', 'easy-property-listings'); ?></label>
		</p>

		<p>
			<input type="checkbox" id="<?php echo $this->get_field_id('d_bio'); ?>" name="<?php echo $this->get_field_name('d_bio'); ?>" <?php if ($instance['d_bio']) echo 'checked="checked"' ?> />
			<label for="<?php echo $this->get_field_id('d_bio'); ?>"><?php _e('Display Bio', 'easy-property-listings'); ?></label>
		</p>
		<?php
	}
}
add_action( 'widgets_init', create_function('', 'return register_widget("EPL_Widget_Author");') );
