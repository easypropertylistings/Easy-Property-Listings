<?php
/**
 * WIDGET :: Recent Property
 *
 * @package     EPL
 * @subpackage  Widget/Recent_Property
 * @copyright   Copyright (c) 2014, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class EPL_Widget_Recent_Property extends WP_Widget {

	function __construct() {
		parent::__construct( false, $name = __('EPL - Listing', 'epl') );
	}

	function widget( $args, $instance ) {

		$defaults = array(
			'title'		=>	'',
			'types'		=>	'property',
			'featured'	=>	0,
			'status'	=>	'any',
			'display'	=>	'image',
			'image'		=>	'thumbnail',
			'archive'	=>	0,
			'order_rand'	=>	0,
			'd_title'	=>	0,

			'more_text'	=>	'Read More',
			'd_excerpt'	=>	'off',
			'd_suburb'	=>	'on',
			'd_street'	=>	'on',
			'd_price'	=>	'on',
			'd_more'	=>	'on',

			'd_icons'	=>	'none',
			'p_number'	=>	1,
			'p_skip'	=>	0,
		);
		$instance = wp_parse_args( (array) $instance, $defaults );

		extract( $args );
		$title 		= apply_filters('widget_title', $instance['title']);
		$display	= $instance['display'];
		$image		= $instance['image'];
		$archive	= $instance['archive'];

		$d_title	= $instance['d_title'];

		$more_text	= $instance['more_text'];
		$d_excerpt	= $instance['d_excerpt'];
		$d_suburb	= $instance['d_suburb'];
		$d_street	= $instance['d_street'];
		$d_price	= $instance['d_price'];
		$d_more		= $instance['d_more'];

		$d_icons	= $instance['d_icons'];

		$p_number	= $instance['p_number'];
		$p_skip		= $instance['p_skip'];
		$types		= $instance['types'];
		$featured	= $instance['featured'];
		$status		= $instance['status'];
		$order_rand	= $instance['order_rand'];

		if ( $types == '' ) { $types = 'property'; }
		if ( $p_number == '' ) { $p_number = 1; }
		if ( $d_icons == 'beds/baths' ) {
			$d_icons = 'bb';
		}

		echo $before_widget;
		if ( $title )
			echo $before_title . $title . $after_title;

		if ( $featured == 'on' ) {
			$args = array(
				'post_type' 	=> $types,
				'showposts' 	=> $p_number,
				'offset'	=> $p_skip,
				'paged'		=> '1',
				'epl_nopaging'	=> 'true',
				'meta_query' 	=> array(
					array(
						'key' 	=> 'property_featured',
						'value'	=> 'yes'
					)
				)
			);
		} elseif ( $archive == 'on' && is_post_type_archive() ) {
			$get_current_type 	= get_post_type();
			$active_types 		= epl_get_active_post_types();

			if ( !array_key_exists( $get_current_type , $active_types  ) ) {
				$args = array(
					'post_type' 	=> $types,
					'showposts' 	=> $p_number,
					'offset'	=> $p_skip,
					'paged'		=> '1',
					'epl_nopaging'	=> 'true'
				);
			} else {
				$args = array(
					'post_type' 	=> $get_current_type,
					'showposts' 	=> $p_number,
					'offset'	=> $p_skip,
					'paged'		=> '1',
					'epl_nopaging'	=> 'true'
				);
			}

		} else {
			if ( $status == 'Current' ) {
				$args = array(
					'post_type' 	=> $types,
					'showposts' 	=> $p_number,
					'offset'	=> $p_skip,
					'paged'		=> '1',
					'epl_nopaging'	=> 'true',
					'meta_query' 	=> array(
						array(
							'key' => 'property_status',
							'value' => 'current'
						)
					)
				);

			} elseif ( $status == 'Sold' ) {
				$args = array(
					'post_type' 	=> $types,
					'showposts' 	=> $p_number,
					'offset'	=> $p_skip,
					'paged'		=> '1',
					'epl_nopaging'	=> 'true',
					'meta_query' 	=> array(
						array(
							'key' => 'property_status',
							'value' => 'sold'
						)
					)
				);

			} elseif ( $status == 'Leased' ) {
				$args = array(
					'post_type'	=> $types,
					'showposts'	=> $p_number,
					'offset'	=> $p_skip,
					'paged'		=> '1',
					'epl_nopaging'	=> 'true',
					'meta_query'	=> array(
						array(
							'key' => 'property_status',
							'value' => 'leased'
						)
					)
				);

			} else {
				$args = array(
					'post_type' 	=> $types,
					'showposts' 	=> $p_number,
					'offset'	=> $p_skip,
					'paged'		=> '1',
					'epl_nopaging'	=> 'true'
				);
			}
		}

		if ( $order_rand == 'on' ) {
			$args['orderby'] = 'rand';
		}

		$query = new WP_Query ( $args );
		if( $query->have_posts() ) :
			echo "<div class='epl-property-widget-$display-wrapper'>";
				if ( $display == 'list' ) {
						echo '<ul>';
				}
				while($query->have_posts()) : $query->the_post();
					epl_property_widget( $display , $image , $d_title , $d_icons , $more_text , $d_excerpt , $d_suburb , $d_street , $d_price , $d_more  );
					wp_reset_query();
				endwhile;
				if ( $display == 'list' ) {
					echo '</ul>';
				}
			echo '</div>';
		endif;
		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title']      = strip_tags( $new_instance['title'] );
		$instance['types']      = strip_tags( $new_instance['types'] );
		$instance['featured']   = strip_tags( $new_instance['featured'] );
		$instance['status']     = strip_tags( $new_instance['status'] );
		$instance['view']		= strip_tags( $new_instance['view'] );
		$instance['display']    = strip_tags( $new_instance['display'] );
		$instance['image']      = strip_tags( $new_instance['image'] );
		$instance['archive']    = strip_tags( $new_instance['archive'] );
		$instance['d_title']    = strip_tags( $new_instance['d_title'] );

		$instance['more_text']  = strip_tags( $new_instance['more_text'] );
		$instance['d_excerpt']  = strip_tags( $new_instance['d_excerpt'] );
		$instance['d_suburb']   = strip_tags( $new_instance['d_suburb'] );
		$instance['d_street']   = strip_tags( $new_instance['d_street'] );
		$instance['d_price']    = strip_tags( $new_instance['d_price'] );
		$instance['d_more']     = strip_tags( $new_instance['d_more'] );

		$instance['d_icons']    = strip_tags( $new_instance['d_icons'] );
		$instance['p_number']   = strip_tags( $new_instance['p_number'] );
		$instance['p_skip']     = strip_tags( $new_instance['p_skip'] );
		$instance['order_rand'] = strip_tags( $new_instance['order_rand'] );
		return $instance;
	}

	function form( $instance ) {

		$defaults = array(
			'title'      =>	'',
			'types'      =>	'property',
			'featured'   =>	0,
			'status'     =>	'any',
			'view'       => 'default',
			'display'    =>	'image',
			'image'      =>	'thumbnail',
			'archive'    =>	0,
			'order_rand' =>	0,
			'd_title'    =>	0,

			'more_text'  =>	__('Read More', 'epl'),
			'd_excerpt'  =>	'off',
			'd_suburb'   =>	'on',
			'd_street'   =>	'on',
			'd_price'    =>	'on',
			'd_more'     =>	'on',

			'd_icons'    =>	'none',
			'p_number'   =>	1,
			'p_skip'     =>	0,
		);
		$instance = wp_parse_args( (array) $instance, $defaults );

		$title 		= esc_attr($instance['title']);
		$types		= esc_attr($instance['types']);

		$featured	= esc_attr($instance['featured']);
		$status		= esc_attr($instance['status']);
		$display 	= esc_attr($instance['display']);
		$image	 	= esc_attr($instance['image']);
		$archive	= esc_attr($instance['archive']);
		$d_title 	= esc_attr($instance['d_title']);

		$more_text 	= esc_attr($instance['more_text']);
		$d_excerpt 	= esc_attr($instance['d_excerpt']);
		$d_suburb 	= esc_attr($instance['d_suburb']);
		$d_street 	= esc_attr($instance['d_street']);
		$d_price 	= esc_attr($instance['d_price']);
		$d_more 	= esc_attr($instance['d_more']);

		$d_icons 	= esc_attr($instance['d_icons']);
		$p_number	= esc_attr($instance['p_number']);
		$p_skip		= esc_attr($instance['p_skip']);
		$order_rand	= esc_attr($instance['order_rand']); ?>

		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', 'epl'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('types'); ?>"><?php _e('Property Type', 'epl'); ?></label>
			<select class="widefat" id="<?php echo $this->get_field_id('types'); ?>" name="<?php echo $this->get_field_name('types'); ?> ">
				<?php
					$options = epl_get_active_post_types();
					if(!empty($options)) {
						foreach ($options as $k=>$v) {
							echo '<option value="' . $k . '" id="' . $k . '"', $instance['types'] == $k ? ' selected="selected"' : '', '>', __($v, 'epl'), '</option>';
						}
					}
				?>
			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('status'); ?>"><?php _e('Property Status', 'epl'); ?></label>
			<select class="widefat" id="<?php echo $this->get_field_id('status'); ?>" name="<?php echo $this->get_field_name('status'); ?> ">
				<?php
					$options = array( 'Any' , 'Current', 'Sold' , 'Leased' );
					foreach ($options as $option) {
						echo '<option value="' . $option . '" id="' . $option . '"', $instance['status'] == $option ? ' selected="selected"' : '', '>', __($option, 'epl'), '</option>';
					}
				?>
			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'view' ); ?>"><?php _e( 'View', 'epl' ); ?></label>
			<select class="widefat" id="<?php echo $this->get_field_id( 'view' ); ?>" name="<?php echo $this->get_field_name( 'view' ); ?>">
				<?php
				$options = apply_filters( 'epl_listing_widget_view', array( 'default' => __( 'Default', 'epl' ) ) );
				if ( count( $options ) ) {
					foreach ( $options as $key => $value ) {
						echo '<option value="' . esc_attr( $key ) . '"' . selected( $key, $instance['view'], false ) . '>' . __( $value, 'epl' ) . '</option>';
					}
				}
				?>
			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('display'); ?>"><?php _e('Display Style', 'epl'); ?></label>
			<select class="widefat" id="<?php echo $this->get_field_id('display'); ?>" name="<?php echo $this->get_field_name('display'); ?>">
				<?php
					/** usage **
					 *
					 *  add more templates to this by add key pair values to this array,
					 *  key will be name of template example 'my-custom-widget-template.php' and value will be label for tpl, example 'My Template'
					 */
					$options = apply_filters(
									'epl_listing_widget_templates',
									array(
										'image'      =>	__( 'Image','epl' ),
										'image-only' =>	__( 'Image Only','epl' ),
										'list'       =>	__( 'List','epl' ),
									)
								);
					foreach ($options as $option_key	=>	$option_label) {
						echo '<option value="' . $option_key . '" id="' . $option_key . '"', $instance['display'] == $option_key ? ' selected="selected"' : '', '>', __($option_label, 'epl'), '</option>';
					}
				?>
			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('image'); ?>"><?php _e('Image Size', 'epl'); ?></label>
			<select class="widefat" id="<?php echo $this->get_field_id('image'); ?>" name="<?php echo $this->get_field_name('image'); ?>">
				<?php
					$sizes = epl_get_thumbnail_sizes();
					foreach ($sizes as $k=>$v) {
						$v = implode(" x ", $v);
						echo '<option class="widefat" value="' . $k . '" id="' . $k . '"', $instance['image'] == $k ? ' selected="selected"' : '', '>', __($k, 'epl') . ' (' . __($v, 'epl') . ' )', '</option>';
					}
				?>
			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('d_icons'); ?>"><?php _e('Icon Style', 'epl'); ?></label>
			<select class="widefat" id="<?php echo $this->get_field_id('d_icons'); ?>" name="<?php echo $this->get_field_name('d_icons'); ?>">
				<?php
					$options = array('none', 'all', 'beds/baths');
					foreach ($options as $option) {
						echo '<option value="' . $option . '" id="' . $option . '"', $instance['d_icons'] == $option ? ' selected="selected"' : '', '>', __($option, 'epl'), '</option>';
					}
				?>
			</select>
		</p>

		<p>
			<select id="<?php echo $this->get_field_id('p_number'); ?>" name="<?php echo $this->get_field_name('p_number'); ?>">
				<?php
					for ($i=1;$i<=20;$i++) {
						echo '<option value="'.$i.'"'; 	if ($i==$instance['p_number']) echo ' selected="selected"'; echo '>'.__($i, 'epl').'</option>';
					}
				?>
			</select>
			<label for="<?php echo $this->get_field_id('p_number'); ?>"><?php _e('Number of Properties', 'epl'); ?></label>
		</p>

		<p>
			<select id="<?php echo $this->get_field_id('p_skip'); ?>" name="<?php echo $this->get_field_name('p_skip'); ?>">
				<?php
					for ($i=0;$i<=20;$i++) {
						echo '<option value="'.$i.'"'; 	if ($i==$instance['p_skip']) echo ' selected="selected"'; echo '>'.__($i, 'epl').'</option>';
					}
				?>
			</select>
			<label for="<?php echo $this->get_field_id('p_skip'); ?>"><?php _e('Properties to Skip', 'epl'); ?></label>
		</p>

		<p>
			<input type="checkbox" id="<?php echo $this->get_field_id('featured'); ?>" name="<?php echo $this->get_field_name('featured'); ?>" <?php if ($instance['featured']) echo 'checked="checked"' ?> />
			<label for="<?php echo $this->get_field_id('featured'); ?>"><?php _e('Only Show Featured Properties', 'epl'); ?></label>
		</p>

		<p>
			<input type="checkbox" id="<?php echo $this->get_field_id('d_title'); ?>" name="<?php echo $this->get_field_name('d_title'); ?>" <?php if ($instance['d_title']) echo 'checked="checked"' ?> />
			<label for="<?php echo $this->get_field_id('d_title'); ?>"><?php _e('Property Headline', 'epl'); ?></label>
		</p>

		<p>
			<input type="checkbox" id="<?php echo $this->get_field_id('d_excerpt'); ?>" name="<?php echo $this->get_field_name('d_excerpt'); ?>" <?php if ($instance['d_excerpt']) echo 'checked="checked"' ?> />
			<label for="<?php echo $this->get_field_id('d_excerpt'); ?>"><?php _e('Excerpt', 'epl'); ?></label>
		</p>

		<p>
			<input type="checkbox" id="<?php echo $this->get_field_id('d_suburb'); ?>" name="<?php echo $this->get_field_name('d_suburb'); ?>" <?php if ($instance['d_suburb']) echo 'checked="checked"' ?> />
			<label for="<?php echo $this->get_field_id('d_suburb'); ?>"><?php echo epl_labels('label_suburb'); ?></label>
		</p>

		<p>
			<input type="checkbox" id="<?php echo $this->get_field_id('d_street'); ?>" name="<?php echo $this->get_field_name('d_street'); ?>" <?php if ($instance['d_street']) echo 'checked="checked"' ?> />
			<label for="<?php echo $this->get_field_id('d_street'); ?>"><?php _e('Street Address', 'epl'); ?></label>
		</p>

		<p>
			<input type="checkbox" id="<?php echo $this->get_field_id('d_price'); ?>" name="<?php echo $this->get_field_name('d_price'); ?>" <?php if ($instance['d_price']) echo 'checked="checked"' ?> />
			<label for="<?php echo $this->get_field_id('d_price'); ?>"><?php _e('Price', 'epl'); ?></label>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('more_text'); ?>"><?php _e('Read More Label', 'epl'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('more_text'); ?>" name="<?php echo $this->get_field_name('more_text'); ?>" type="text" value="<?php echo $more_text; ?>" />
		</p>

		<p>
			<input type="checkbox" id="<?php echo $this->get_field_id('d_more'); ?>" name="<?php echo $this->get_field_name('d_more'); ?>" <?php if ($instance['d_more']) echo 'checked="checked"' ?> />
			<label for="<?php echo $this->get_field_id('d_more'); ?>"><?php _e('Read More Button', 'epl'); ?></label>
		</p>

		<p>
			<input type="checkbox" id="<?php echo $this->get_field_id('archive'); ?>" name="<?php echo $this->get_field_name('archive'); ?>" <?php if ($instance['archive']) echo 'checked="checked"' ?> />
			<label for="<?php echo $this->get_field_id('archive'); ?>"><?php _e('Dynamic', 'epl'); ?></label>
		</p>

		<p>
			<input type="checkbox" id="<?php echo $this->get_field_id('order_rand'); ?>" name="<?php echo $this->get_field_name('order_rand'); ?>" <?php if ($instance['order_rand']) echo 'checked="checked"' ?> />
			<label for="<?php echo $this->get_field_id('order_rand'); ?>"><?php _e('Random Order', 'epl'); ?></label>
		</p>
        <?php
	}
}
add_action( 'widgets_init', create_function('', 'return register_widget("EPL_Widget_Recent_Property");') );
