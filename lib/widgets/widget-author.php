<?php
/*
 * WIDGET :: Author
 */

class EPL_Widget_Author extends WP_Widget {

	function __construct() {
		parent::WP_Widget( false, $name = __('EPL - Author', 'epl') );	
	}

	function widget($args, $instance) {
		extract( $args );
		$title 		= apply_filters('widget_title', $instance['title']);
		$message 	= $instance['message'];
		$display	= $instance['display'];
		$image		= $instance['image'];
		$d_image	= $instance['d_image'];
		$d_icons	= $instance['d_icons'];
		$d_bio		= $instance['d_bio'];
		
		echo $before_widget;
		if ( $title )
			echo $before_title . $title . $after_title;
			
		epl_property_author_box_simple_card_tall( $d_image , $image , $d_icons , $d_bio);
		echo $after_widget;
	}

	function update($new_instance, $old_instance) {		
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['message'] = strip_tags($new_instance['message']);
		$instance['display'] = strip_tags($new_instance['display']);
		$instance['image'] = strip_tags($new_instance['image']);
		$instance['d_image'] = strip_tags($new_instance['d_image']);
		$instance['d_icons'] = strip_tags($new_instance['d_icons']);
		$instance['d_bio'] = strip_tags($new_instance['d_bio']);
		return $instance;
	}

	function form($instance) {
		$title 		= esc_attr($instance['title']);
		$message	= esc_attr($instance['message']);
		$display	= esc_attr($instance['display']);
		$image		= esc_attr($instance['image']);
		$d_image	= esc_attr($instance['d_image']);
		$d_icons	= esc_attr($instance['d_icons']);
		$d_bio		= esc_attr($instance['d_bio']); ?>
		
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id('image'); ?>"><?php _e('Image Size'); ?></label>
			<select class="widefat" id="<?php echo $this->get_field_id('image'); ?>" name="<?php echo $this->get_field_name('image'); ?>">
				<?php
					$sizes = get_thumbnail_sizes();
					foreach ($sizes as $k=>$v) {
						$v = implode(" x ", $v);
						echo '<option class="widefat" value="' . $k . '" id="' . $k . '"', $instance['image'] == $k ? ' selected="selected"' : '', '>', $k . ' (' . $v . ' )', '</option>';
					}
				?>
			</select>
		</p>
		
		<p>
			<input type="checkbox" id="<?php echo $this->get_field_id('d_image'); ?>" name="<?php echo $this->get_field_name('d_image'); ?>" <?php if ($instance['d_image']) echo 'checked="checked"' ?> />
			<label for="<?php echo $this->get_field_id('d_image'); ?>"><?php _e('Display Featured Image'); ?></label>
		</p>
		
		<p>
			<input type="checkbox" id="<?php echo $this->get_field_id('d_icons'); ?>" name="<?php echo $this->get_field_name('d_icons'); ?>" <?php if ($instance['d_icons']) echo 'checked="checked"' ?> />
			<label for="<?php echo $this->get_field_id('d_icons'); ?>"><?php _e('Display Icons'); ?></label>
		</p>
		
		<p>
			<input type="checkbox" id="<?php echo $this->get_field_id('d_bio'); ?>" name="<?php echo $this->get_field_name('d_bio'); ?>" <?php if ($instance['d_bio']) echo 'checked="checked"' ?> />
			<label for="<?php echo $this->get_field_id('d_bio'); ?>"><?php _e('Display Bio'); ?></label>
		</p>
		<?php 
	}
}
add_action( 'widgets_init', create_function('', 'return register_widget("EPL_Widget_Author");') );
