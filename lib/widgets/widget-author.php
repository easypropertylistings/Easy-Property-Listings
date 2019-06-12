<?php
/**
 * WIDGET :: Author
 *
 * @package     EPL
 * @subpackage  Widget/Author
 * @copyright   Copyright (c) 2019, Merv Barrett
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
		// Widget name for filter: epl_author
	}

	function widget($args, $instance) {

		$defaults = array(
			'title'		=>	'',
			'username'	=>	'',
			'display'	=>	0,
			'd_image'	=>	0,
			'd_icons'	=>	0,
			'd_bio'		=>	0,
		);

		$instance = wp_parse_args( (array) $instance, $defaults );

		extract( $args );
		$title 		= apply_filters('widget_title', $instance['title']);
		$display	= $instance['display'];
		$d_image	= $instance['d_image'];
		$d_icons	= $instance['d_icons'];
		$d_bio		= $instance['d_bio'];
		$username	= $instance['username'];

		if ( is_epl_post_single() ) {
			// Only retrieve global $property variable if singluar
			global $property;
			$hide_author_box	=	$property->get_property_meta('property_agent_hide_author_box');

			$author_box 		= 	apply_filters( 'epl_widget_author_hide_widget' , 'off' );

			if ( $hide_author_box == 'yes' && $author_box == 'on' ) {
				// Hide Author Box
				// Disabled as it makes no sence
			} else {
				echo $before_widget;
				if ( $title )
					echo $before_title . $title . $after_title;
					epl_property_author_box_simple_card_tall( $d_image , $d_icons , $d_bio, $username);

				echo $after_widget;
			}

		} else {

			echo $before_widget;
			if ( $title )
				echo $before_title . $title . $after_title;
				epl_property_author_box_simple_card_tall( $d_image , $d_icons , $d_bio, $username);

			echo $after_widget;
		}
	}

	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] 	= strip_tags($new_instance['title']);
		$instance['username'] 	= strip_tags($new_instance['username']);
		$instance['display'] 	= strip_tags($new_instance['display']);
		$instance['d_image'] 	= strip_tags($new_instance['d_image']);
		$instance['d_icons'] 	= strip_tags($new_instance['d_icons']);
		$instance['d_bio'] 	= strip_tags($new_instance['d_bio']);
		return $instance;
	}

	function form($instance) {
		$defaults = array(
			'title'		=>	'',
			'username'	=>	'',
			'display'	=>	0,
			'd_image'	=>	0,
			'd_icons'	=>	0,
			'd_bio'		=>	0
		);

		$instance = wp_parse_args( (array) $instance, $defaults );

		$title 		= esc_attr($instance['title']);
		$username 	= esc_attr($instance['username']);
		$display	= esc_attr($instance['display']);
		$d_image	= esc_attr($instance['d_image']);
		$d_icons	= esc_attr($instance['d_icons']);
		$d_bio		= esc_attr($instance['d_bio']); ?>

		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'easy-property-listings'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('username'); ?>"><?php _e('Username:', 'easy-property-listings'); ?></label>
			<input autocomplete="off" class="widefat epl-widget-author-username" id="<?php echo $this->get_field_id('username'); ?>" name="<?php echo $this->get_field_name('username'); ?>" type="text" value="<?php echo $username; ?>" placeholder="<?php _e('Type to search users','easy-property-listings'); ?>"/>
			<span class="help"><?php _e('Search for users, supports multiple','easy-property-listings'); ?></span>
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

/**
 * Register Author Widget.
 *
 * Registers the EPL Widgets.
 *
 * @since 3.2.2
 * @return void
 */
function epl_register_widget_author() {
	register_widget( 'EPL_Widget_Author' );
}
add_action( 'widgets_init', 'epl_register_widget_author' );
