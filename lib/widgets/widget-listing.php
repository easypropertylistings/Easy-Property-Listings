<?php
/**
 * WIDGET :: Recent Property
 *
 * @package     EPL
 * @subpackage  Widget/Listing
 * @copyright   Copyright (c) 2014, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * EPL_Widget_Recent_Property class
 *
 * @since 1.0
 */
class EPL_Widget_Recent_Property extends WP_Widget {

	function __construct() {
		parent::__construct( false, __( 'EPL - Listing', 'easy-property-listings' ), array( 'description' => __( 'Add listings to a sidebar.', 'easy-property-listings' ) ) );
	}

	function widget( $args, $instance ) {
		$property_types = epl_get_active_post_types();
		if ( ! empty( $property_types ) ) {
			$property_types = array_keys( $property_types );
		}

		// Prince Annotate options here
		$defaults = array(
			'title'      =>	'',
			'types'      =>	$property_types,
			'featured'   =>	0,
			'status'     =>	array( 'current', 'sold', 'leased' ),
			'view'		 => 'default',
			'display'    =>	'image',
			'image'      =>	'thumbnail',
			'archive'    =>	0,
			'order_rand' =>	0,
			'd_title'    =>	0,

			'more_text'  =>	'Read More',
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

		if ( ! is_array( $instance['types'] ) ) {
			$instance['types'] = array_map( 'trim', explode( ',', $instance['types'] ) );
		}
		if ( ! $instance['p_number'] ) {
			$instance['p_number'] = 1;
		}
		if ( 'beds/baths' === $instance['d_icons'] ) {
			$instance['d_icons'] = 'bb';
		}
		// WP_Query arguments.
		$query_args = array(
			'post_type'    => $instance['types'],
			'showposts'    => absint( $instance['p_number'] ),
			'offset'       => absint( $instance['p_skip'] ),
			'paged'        => '1',
			'epl_nopaging' => 'true',
		);

		// Listing status.
		if ( ! empty( $instance['status'] ) ) {
			if ( ! is_array( $instance['status'] ) ) {
				$instance['status'] = array_map( 'trim', explode( ',', $instance['status'] ) );
			}
			// If property_status is any do not set meta_query for property_status.
			if ( ! in_array( 'any', $instance['status'] ) ) {
				$query_args['meta_query'][] = array(
					'key'		=> 'property_status',
					'value'		=> $instance['status'],
					'compare'	=> 'IN',
				);
			}
		}
		// Loading featured listings.
		if ( 'on' === $instance['featured'] ) {
			$query_args['meta_query'][] = array(
				'key' 	=> 'property_featured',
				'value'	=> 'yes',
			);
		}
		// Loading current archive page listings.
		if ( 'on' === $instance['archive'] && is_post_type_archive() ) {
			$get_current_type = get_post_type();
			if ( false !== $get_current_type && in_array( $get_current_type , $property_types ) ) {
				$query_args['post_type'] = $get_current_type;
			}
		}
		// Using random order for listings.
		if ( 'on' === $instance['order_rand'] ) {
			$query_args['orderby'] = 'rand';
		}

		$query = new WP_Query( $query_args );
		epl_get_template_part( 'widgets/listing/' . ( strlen( trim( $instance['view'] ) ) ? trim( $instance['view'] ) : 'default' ) . '.php',
			array(
				'args'     => $args,
				'instance' => $instance,
				'query'    => $query,
			)
		);
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title']      = strip_tags( $new_instance['title'] );
		// Saving listing type.
		$epl_post_types 		= epl_get_active_post_types();
		if ( isset( $new_instance['types'] ) && is_array( $new_instance['types'] ) ) {
			$types = array();
			foreach ( $new_instance['types'] as $type ) {
				if ( array_key_exists( $type, $epl_post_types ) ) {
					$types[] = $type;
				}
			}
			if ( count( $types ) ) {
				$instance['types'] = $types;
			}
		}
		// Saving listing status.
		$listing_statuses = apply_filters( 'epl_widget_listing_property_status',
			array(
				'current'   => __( 'Current', 'easy-property-listings' ),
				'sold'      => __( 'Sold', 'easy-property-listings' ),
				'leased'    => __( 'Leased', 'easy-property-listings' ),
				'withdrawn' => __( 'Withdrawn', 'easy-property-listings' ),
				'offmarket' => __( 'Off Market', 'easy-property-listings' ),
			)
		);
		if ( isset( $new_instance['status'] ) && is_array( $new_instance['status'] ) ) {
			$statuses = array();
			foreach ( $new_instance['status'] as $status ) {
				if ( array_key_exists( $status, $listing_statuses ) ) {
					$statuses[] = $status;
				}
			}
			if ( count( $statuses ) ) {
				$instance['status'] = $statuses;
			}
		}
		$instance['featured']   = strip_tags( $new_instance['featured'] );
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

			'more_text'  =>	__( 'Read More', 'easy-property-listings' ),
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

		$title 		= esc_attr( $instance['title'] );

		$featured	= esc_attr( $instance['featured'] );
		$display 	= esc_attr( $instance['display'] );
		$image	 	= esc_attr( $instance['image'] );
		$archive	= esc_attr( $instance['archive'] );
		$d_title 	= esc_attr( $instance['d_title'] );

		$more_text 	= esc_attr( $instance['more_text'] );
		$d_excerpt 	= esc_attr( $instance['d_excerpt'] );
		$d_suburb 	= esc_attr( $instance['d_suburb'] );
		$d_street 	= esc_attr( $instance['d_street'] );
		$d_price 	= esc_attr( $instance['d_price'] );
		$d_more 	= esc_attr( $instance['d_more'] );

		$d_icons 	= esc_attr( $instance['d_icons'] );
		$p_number	= esc_attr( $instance['p_number'] );
		$p_skip		= esc_attr( $instance['p_skip'] );
		$order_rand	= esc_attr( $instance['order_rand'] ); ?>

		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', 'easy-property-listings'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'types' ); ?>"><?php _e( 'Listing Type', 'easy-property-listings' ); ?></label>
			<select class="widefat" id="<?php echo $this->get_field_id( 'types' ); ?>" name="<?php echo $this->get_field_name( 'types' ); ?>[]" multiple="multiple">
				<?php
					$options = epl_get_active_post_types();
					if ( ! empty( $options ) ) {
						foreach ( $options as $k => $v ) {
							if ( is_array( $instance['types'] ) ) {
								$selected = in_array( $k, $instance['types'] ) ? true : false;
							} else {
								$selected = $k == $instance['types'] ? true : false;
							}
							echo '<option value="' . esc_attr( $k ) . '" id="' . esc_attr( $k ) . '"', $selected ? ' selected="selected"' : '', '>', __( $v, 'easy-property-listings' ), '</option>';
						}
					}
				?>
			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'status' ); ?>"><?php _e( 'Listing Status', 'easy-property-listings' ); ?></label>
			<select class="widefat" id="<?php echo $this->get_field_id( 'status' ); ?>" name="<?php echo $this->get_field_name( 'status' ); ?>[]" multiple="multiple">
				<?php
					$options = apply_filters( 'epl_widget_listing_property_status',
						array(
							'current'   => __( 'Current', 'easy-property-listings' ),
							'sold'      => __( 'Sold', 'easy-property-listings' ),
							'leased'    => __( 'Leased', 'easy-property-listings' ),
							'withdrawn' => __( 'Withdrawn', 'easy-property-listings' ),
							'offmarket' => __( 'Off Market', 'easy-property-listings' ),
						)
					);
					if ( count( $options ) ) {
						foreach ( $options as $key => $value ) {
							if ( is_array( $instance['status'] ) ) {
								$selected = in_array( $key, $instance['status'] ) ? true : false;
							} else {
								$selected = $key == $instance['status'] ? true : false;
							}
							echo '<option value="' . esc_attr( $key ) . '" id="' . esc_attr( $key ) . '"' . selected( $selected, true, false ) . '>' . $value . '</option>';
						}
					}
				?>
			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'view' ); ?>"><?php _e( 'View', 'easy-property-listings' ); ?></label>
			<select class="widefat" id="<?php echo $this->get_field_id( 'view' ); ?>" name="<?php echo $this->get_field_name( 'view' ); ?>">
				<?php
				$options = apply_filters( 'epl_listing_widget_view', array( 'default' => __( 'Default', 'easy-property-listings' ) ) );
				if ( count( $options ) ) {
					foreach ( $options as $key => $value ) {
						echo '<option value="' . esc_attr( $key ) . '"' . selected( $key, $instance['view'], false ) . '>' . __( $value, 'easy-property-listings' ) . '</option>';
					}
				}
				?>
			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('display'); ?>"><?php _e('Display Style', 'easy-property-listings'); ?></label>
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
										'image'      =>	__( 'Image','easy-property-listings' ),
										'image-only' =>	__( 'Image Only','easy-property-listings' ),
										'hide-image' =>	__( 'Hide Image','easy-property-listings' ),
										'list'       =>	__( 'List','easy-property-listings' ),
									)
								);
					foreach ($options as $option_key	=>	$option_label) {
						echo '<option value="' . $option_key . '" id="' . $option_key . '"', $instance['display'] == $option_key ? ' selected="selected"' : '', '>', __($option_label, 'easy-property-listings'), '</option>';
					}
				?>
			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('image'); ?>"><?php _e('Image Size', 'easy-property-listings'); ?></label>
			<select class="widefat" id="<?php echo $this->get_field_id('image'); ?>" name="<?php echo $this->get_field_name('image'); ?>">
				<?php
					$sizes = epl_get_thumbnail_sizes();
					foreach ($sizes as $k=>$v) {
						$v = implode(" x ", $v);
						echo '<option class="widefat" value="' . $k . '" id="' . $k . '"', $instance['image'] == $k ? ' selected="selected"' : '', '>', __($k, 'easy-property-listings') . ' (' . __($v, 'easy-property-listings') . ' )', '</option>';
					}
				?>
			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('d_icons'); ?>"><?php _e('Icon Style', 'easy-property-listings'); ?></label>
			<select class="widefat" id="<?php echo $this->get_field_id('d_icons'); ?>" name="<?php echo $this->get_field_name('d_icons'); ?>">
				<?php
					$options = array('none', 'all', 'beds/baths');
					foreach ($options as $option) {
						echo '<option value="' . $option . '" id="' . $option . '"', $instance['d_icons'] == $option ? ' selected="selected"' : '', '>', __($option, 'easy-property-listings'), '</option>';
					}
				?>
			</select>
		</p>

		<p>
			<select id="<?php echo $this->get_field_id('p_number'); ?>" name="<?php echo $this->get_field_name('p_number'); ?>">
				<?php
					for ($i=1;$i<=20;$i++) {
						echo '<option value="'.$i.'"'; 	if ($i==$instance['p_number']) echo ' selected="selected"'; echo '>'.__($i, 'easy-property-listings').'</option>';
					}
				?>
			</select>
			<label for="<?php echo $this->get_field_id('p_number'); ?>"><?php _e('Number of Properties', 'easy-property-listings'); ?></label>
		</p>

		<p>
			<select id="<?php echo $this->get_field_id('p_skip'); ?>" name="<?php echo $this->get_field_name('p_skip'); ?>">
				<?php
					for ($i=0;$i<=20;$i++) {
						echo '<option value="'.$i.'"'; 	if ($i==$instance['p_skip']) echo ' selected="selected"'; echo '>'.__($i, 'easy-property-listings').'</option>';
					}
				?>
			</select>
			<label for="<?php echo $this->get_field_id('p_skip'); ?>"><?php _e('Properties to Skip', 'easy-property-listings'); ?></label>
		</p>

		<p>
			<input type="checkbox" id="<?php echo $this->get_field_id('featured'); ?>" name="<?php echo $this->get_field_name('featured'); ?>" <?php if ($instance['featured']) echo 'checked="checked"' ?> />
			<label for="<?php echo $this->get_field_id('featured'); ?>"><?php _e('Only Show Featured Properties', 'easy-property-listings'); ?></label>
		</p>

		<p>
			<input type="checkbox" id="<?php echo $this->get_field_id('d_title'); ?>" name="<?php echo $this->get_field_name('d_title'); ?>" <?php if ($instance['d_title']) echo 'checked="checked"' ?> />
			<label for="<?php echo $this->get_field_id('d_title'); ?>"><?php _e('Property Headline', 'easy-property-listings'); ?></label>
		</p>

		<p>
			<input type="checkbox" id="<?php echo $this->get_field_id('d_excerpt'); ?>" name="<?php echo $this->get_field_name('d_excerpt'); ?>" <?php if ($instance['d_excerpt']) echo 'checked="checked"' ?> />
			<label for="<?php echo $this->get_field_id('d_excerpt'); ?>"><?php _e('Excerpt', 'easy-property-listings'); ?></label>
		</p>

		<p>
			<input type="checkbox" id="<?php echo $this->get_field_id('d_suburb'); ?>" name="<?php echo $this->get_field_name('d_suburb'); ?>" <?php if ($instance['d_suburb']) echo 'checked="checked"' ?> />
			<label for="<?php echo $this->get_field_id('d_suburb'); ?>"><?php echo epl_labels('label_suburb'); ?></label>
		</p>

		<p>
			<input type="checkbox" id="<?php echo $this->get_field_id('d_street'); ?>" name="<?php echo $this->get_field_name('d_street'); ?>" <?php if ($instance['d_street']) echo 'checked="checked"' ?> />
			<label for="<?php echo $this->get_field_id('d_street'); ?>"><?php _e('Street Address', 'easy-property-listings'); ?></label>
		</p>

		<p>
			<input type="checkbox" id="<?php echo $this->get_field_id('d_price'); ?>" name="<?php echo $this->get_field_name('d_price'); ?>" <?php if ($instance['d_price']) echo 'checked="checked"' ?> />
			<label for="<?php echo $this->get_field_id('d_price'); ?>"><?php _e('Price', 'easy-property-listings'); ?></label>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('more_text'); ?>"><?php _e('Read More Label', 'easy-property-listings'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('more_text'); ?>" name="<?php echo $this->get_field_name('more_text'); ?>" type="text" value="<?php echo $more_text; ?>" />
		</p>

		<p>
			<input type="checkbox" id="<?php echo $this->get_field_id('d_more'); ?>" name="<?php echo $this->get_field_name('d_more'); ?>" <?php if ($instance['d_more']) echo 'checked="checked"' ?> />
			<label for="<?php echo $this->get_field_id('d_more'); ?>"><?php _e('Read More Button', 'easy-property-listings'); ?></label>
		</p>

		<p>
			<input type="checkbox" id="<?php echo $this->get_field_id('archive'); ?>" name="<?php echo $this->get_field_name('archive'); ?>" <?php if ($instance['archive']) echo 'checked="checked"' ?> />
			<label for="<?php echo $this->get_field_id('archive'); ?>"><?php _e('Dynamic', 'easy-property-listings'); ?></label>
		</p>

		<p>
			<input type="checkbox" id="<?php echo $this->get_field_id('order_rand'); ?>" name="<?php echo $this->get_field_name('order_rand'); ?>" <?php if ($instance['order_rand']) echo 'checked="checked"' ?> />
			<label for="<?php echo $this->get_field_id('order_rand'); ?>"><?php _e('Random Order', 'easy-property-listings'); ?></label>
		</p>
        <?php
	}
}
add_action( 'widgets_init', create_function( '', 'return register_widget("EPL_Widget_Recent_Property");' ) );
