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

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * EPL_Widget_Author class
 *
 * @since 1.0
 */
class EPL_Widget_Author extends WP_Widget {

	/**
	 * Construct the widget.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		parent::__construct( false, $name = esc_html__( 'EPL - Author', 'easy-property-listings' ), array( 'description' => esc_html__( 'Add an Author profile to a sidebar.', 'easy-property-listings' ) ) );
		// Widget name for filter: epl_author.
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

		$defaults = array(
			'title'    => '',
			'username' => '',
			'display'  => 0,
			'd_image'  => 0,
			'd_icons'  => 0,
			'd_bio'    => 0,
		);

		$instance = wp_parse_args( (array) $instance, $defaults );

		foreach ( $args as $arg_key => $arg_val ) {

			${$arg_key} = $arg_val;
		}

		$title    = apply_filters( 'widget_title', esc_attr( $instance['title'] ) );
		$username = esc_attr( $instance['username'] );
		$display  = esc_attr( $instance['display'] );
		$d_image  = esc_attr( $instance['d_image'] );
		$d_icons  = esc_attr( $instance['d_icons'] );
		$d_bio    = esc_attr( $instance['d_bio'] );

		if ( is_epl_post_single() ) {
			// Only retrieve global $property variable if singluar.
			global $property;
			$hide_author_box = $property->get_property_meta( 'property_agent_hide_author_box' );

			$author_box = apply_filters( 'epl_widget_author_hide_widget', 'off' );

			if ( 'yes' === $hide_author_box && 'on' === $author_box ) {
				// Hide Author Box.
				// Disabled as it makes no sence.
			} else {
				echo $before_widget;
				if ( $title ) {
					echo $before_title . $title . $after_title;
				}
					epl_property_author_box_simple_card_tall( $d_image, $d_icons, $d_bio, $username );

				echo $after_widget;
			}
		} else {

			echo $before_widget;
			if ( $title ) {
				echo $before_title . $title . $after_title;
			}
				epl_property_author_box_simple_card_tall( $d_image, $d_icons, $d_bio, $username );

			echo $after_widget;
		}
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
		$instance = $old_instance;

		$all_fields = array( 'title', 'username', 'display', 'd_image', 'd_icons', 'd_bio' );

		foreach ( $all_fields as $all_field ) {

			if ( isset( $new_instance[ $all_field ] ) ) {
				$instance[ $all_field ] = epl_strip_tags( $new_instance[ $all_field ] );
			} else {

				$instance[ $all_field ] = '';

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

		// phpcs:disable WordPress.Security.EscapeOutput

		$defaults = array(
			'title'    => '',
			'username' => '',
			'display'  => 0,
			'd_image'  => 0,
			'd_icons'  => 0,
			'd_bio'    => 0,
		);

		$instance = wp_parse_args( (array) $instance, $defaults );

		$title    = esc_attr( $instance['title'] );
		$username = esc_attr( $instance['username'] );
		$display  = esc_attr( $instance['display'] );
		$d_image  = esc_attr( $instance['d_image'] );
		$d_icons  = esc_attr( $instance['d_icons'] );
		$d_bio    = esc_attr( $instance['d_bio'] ); ?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'easy-property-listings' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'username' ); ?>"><?php _e( 'Username:', 'easy-property-listings' ); ?></label>
			<input autocomplete="off" class="widefat epl-widget-author-username" id="<?php echo $this->get_field_id( 'username' ); ?>" name="<?php echo $this->get_field_name( 'username' ); ?>" type="text" value="<?php echo $username; ?>" placeholder="<?php _e( 'Type to search users', 'easy-property-listings' ); ?>"/>
			<span class="help"><?php _e( 'Search for users, supports multiple', 'easy-property-listings' ); ?></span>
		</p>

		<p>
			<input type="checkbox" id="<?php echo $this->get_field_id( 'd_image' ); ?>" name="<?php echo $this->get_field_name( 'd_image' ); ?>"
												<?php
												if ( $instance['d_image'] ) {
													echo 'checked="checked"';}
												?>
			/>
			<label for="<?php echo $this->get_field_id( 'd_image' ); ?>"><?php _e( 'Display Author Gravatar', 'easy-property-listings' ); ?></label>
		</p>

		<p>
			<input type="checkbox" id="<?php echo $this->get_field_id( 'd_icons' ); ?>" name="<?php echo $this->get_field_name( 'd_icons' ); ?>"
												<?php
												if ( $instance['d_icons'] ) {
													echo 'checked="checked"';}
												?>
			/>
			<label for="<?php echo $this->get_field_id( 'd_icons' ); ?>"><?php _e( 'Display Icons', 'easy-property-listings' ); ?></label>
		</p>

		<p>
			<input type="checkbox" id="<?php echo $this->get_field_id( 'd_bio' ); ?>" name="<?php echo $this->get_field_name( 'd_bio' ); ?>"
												<?php
												if ( $instance['d_bio'] ) {
													echo 'checked="checked"';}
												?>
			/>
			<label for="<?php echo $this->get_field_id( 'd_bio' ); ?>"><?php _e( 'Display Bio', 'easy-property-listings' ); ?></label>
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
