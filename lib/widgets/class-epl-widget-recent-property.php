<?php
/**
 * WIDGET :: Recent Property
 *
 * @package     EPL
 * @subpackage  Widget/Listing
 * @copyright   Copyright (c) 2019, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * EPL_Widget_Recent_Property class
 *
 * @since 1.0
 */
class EPL_Widget_Recent_Property extends WP_Widget {

	/**
	 * Construct the widget.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		parent::__construct( false, __( 'EPL - Listing', 'easy-property-listings' ), array( 'description' => __( 'Add listings to a sidebar.', 'easy-property-listings' ) ) );
		// Widget name for filter: epl_recent_property.
	}

	/**
	 * Widget function.
	 *
	 * @since 1.0
	 * @param array $args Widget arguments.
	 * @param array $instance Widget instance.
	 */
	public function widget( $args, $instance ) {
		$property_types = epl_get_active_post_types();
		if ( ! empty( $property_types ) ) {
			$property_types = array_keys( $property_types );
		}

		$defaults = array(
			'title'             => '', // widget title.
			'types'             => $property_types, // listing types to show in widget.
			'featured'          => 0, // show only featured listings.
			'status'            => array( 'current', 'sold', 'leased' ), // property status to show.
			'view'              => 'default', // template to render results : templates/content/widget/listing, override in theme.
			'display'           => 'image', // show featured image.
			'image'             => 'thumbnail', // image size.
			'archive'           => 0, // if this is selected, widget will dynamially show listings of currently displayed listing post type.
			'order_rand'        => 0, // randomly order.
			'open_house'        => 0, // show listings open for inspection.
			'd_title'           => 0, // show title of listing.

			'more_text'         => __( 'Read More', 'easy-property-listings' ), // read more text.
			'd_excerpt'         => 'off', // show excerpt.
			'd_suburb'          => 'on', // show suburb.
			'd_street'          => 'on', // show street address.
			'd_price'           => 'on', // show listing price.
			'd_more'            => 'on', // show read more button.
			'd_inspection_time' => 'off', // show inspection dates.
			'd_ical_link'       => 'on', // link inspection dates to ical.

			'd_icons'           => 'none', // show icons ? none | bed & bath | all.
			'p_number'          => 1, // number of listings to show.
			'p_skip'            => 0, // number of listings to skip.

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
			if ( ! in_array( 'any', $instance['status'], true ) ) {
				$query_args['meta_query'][] = array(
					'key'     => 'property_status',
					'value'   => $instance['status'],
					'compare' => 'IN',
				);
			}
		}
		// Loading featured listings.
		if ( 'on' === $instance['featured'] ) {
			$query_args['meta_query'][] = array(
				'key'   => 'property_featured',
				'value' => 'yes',
			);
		}

		// Loading open house listings.
		if ( 'on' === $instance['open_house'] ) {
			$query_args['meta_query'][] = array(
				'key'     => 'property_inspection_times',
				'value'   => '^\s*$',
				'compare' => 'NOT REGEXP',
			);
		}

		// Loading current archive page listings.
		if ( 'on' === $instance['archive'] && is_post_type_archive() ) {
			$get_current_type = get_post_type();
			if ( false !== $get_current_type && in_array( $get_current_type, $property_types, true ) ) {
				$query_args['post_type'] = $get_current_type;
			}
		}
		// Using random order for listings.
		if ( 'on' === $instance['order_rand'] ) {
			$query_args['orderby'] = 'rand';
		}

		$query = new WP_Query( $query_args );
		epl_get_template_part(
			'widgets/listing/' . ( strlen( trim( $instance['view'] ) ) ? trim( $instance['view'] ) : 'default' ) . '.php',
			array(
				'args'     => $args,
				'instance' => $instance,
				'query'    => $query,
			)
		);
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
		$instance          = $old_instance;
		$instance['title'] = wp_strip_all_tags( $new_instance['title'] );
		// Saving listing type.
		$epl_post_types = epl_get_active_post_types();
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
		$listing_statuses = apply_filters(
			'epl_widget_listing_property_status',
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

		$all_fields = array(
			'featured',
			'view',
			'display',
			'image',
			'archive',
			'open_house',
			'd_title',
			'more_text',
			'd_excerpt',
			'd_suburb',
			'd_street',
			'd_price',
			'd_more',
			'd_inspection_time',
			'd_ical_link',
			'd_icons',
			'p_number',
			'p_skip',
			'order_rand',
		);

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

		// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped

		$defaults = array(
			'title'             => '',
			'types'             => 'property',
			'featured'          => 0,
			'status'            => 'any',
			'view'              => 'default',
			'display'           => 'image',
			'image'             => 'thumbnail',
			'archive'           => 0,
			'order_rand'        => 0,
			'open_house'        => 0,
			'd_title'           => 0,

			'more_text'         => __( 'Read More', 'easy-property-listings' ),
			'd_excerpt'         => 'off',
			'd_suburb'          => 'on',
			'd_street'          => 'on',
			'd_price'           => 'on',
			'd_more'            => 'on',
			'd_inspection_time' => 0,
			'd_ical_link'       => 1,

			'd_icons'           => 'none',
			'p_number'          => 1,
			'p_skip'            => 0,
		);
		$instance = wp_parse_args( (array) $instance, $defaults );

		$title = esc_attr( $instance['title'] );

		$featured = esc_attr( $instance['featured'] );
		$display  = esc_attr( $instance['display'] );
		$image    = esc_attr( $instance['image'] );
		$archive  = esc_attr( $instance['archive'] );
		$d_title  = esc_attr( $instance['d_title'] );

		$more_text = esc_attr( $instance['more_text'] );
		$d_excerpt = esc_attr( $instance['d_excerpt'] );
		$d_suburb  = esc_attr( $instance['d_suburb'] );
		$d_street  = esc_attr( $instance['d_street'] );
		$d_price   = esc_attr( $instance['d_price'] );
		$d_more    = esc_attr( $instance['d_more'] );

		$d_icons           = esc_attr( $instance['d_icons'] );
		$p_number          = esc_attr( $instance['p_number'] );
		$p_skip            = esc_attr( $instance['p_skip'] );
		$order_rand        = esc_attr( $instance['order_rand'] );
		$open_house        = esc_attr( $instance['open_house'] );
		$d_inspection_time = esc_attr( $instance['d_inspection_time'] );
		$d_ical_link       = esc_attr( $instance['d_ical_link'] );
		?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php esc_html_e( 'Title', 'easy-property-listings' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
		</p>
		<p> <strong><?php esc_html_e( 'Query Settings', 'easy-property-listings' ); ?></strong></p>
		<p>
			<label for="<?php echo $this->get_field_id( 'types' ); ?>"><?php esc_html_e( 'Listing Type', 'easy-property-listings' ); ?></label>
			<select class="widefat" id="<?php echo $this->get_field_id( 'types' ); ?>" name="<?php echo $this->get_field_name( 'types' ); ?>[]" multiple="multiple">
				<?php
					$options = epl_get_active_post_types();
				if ( ! empty( $options ) ) {
					foreach ( $options as $k => $v ) {
						if ( is_array( $instance['types'] ) ) {
							$selected = in_array( $k, $instance['types'], true ) ? true : false;
						} else {
							$selected = $k === $instance['types'] ? true : false;
						}
						echo '<option value="' . esc_attr( $k ) . '" id="' . esc_attr( $k ) . '"', $selected ? ' selected="selected"' : '', '>', $v, '</option>';
					}
				}
				?>
			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'status' ); ?>"><?php esc_html_e( 'Listing Status', 'easy-property-listings' ); ?></label>
			<select class="widefat" id="<?php echo $this->get_field_id( 'status' ); ?>" name="<?php echo $this->get_field_name( 'status' ); ?>[]" multiple="multiple">
				<?php
					$options = apply_filters(
						'epl_widget_listing_property_status',
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
							$selected = in_array( $key, $instance['status'], true ) ? true : false;
						} else {
							$selected = $key === $instance['status'] ? true : false;
						}
						echo '<option value="' . esc_attr( $key ) . '" id="' . esc_attr( $key ) . '"' . selected( $selected, true, false ) . '>' . $value . '</option>';
					}
				}
				?>
			</select>
		</p>

		<p>
			<select id="<?php echo $this->get_field_id( 'p_number' ); ?>" name="<?php echo $this->get_field_name( 'p_number' ); ?>">
				<?php
				for ( $i = 1;$i <= 20;$i++ ) {
					echo '<option value="' . $i . '"';
					if ( $i == $instance['p_number'] ) { //phpcs:ignore
						echo ' selected="selected"';
					} echo '>' . $i . '</option>';
				}
				?>
			</select>
			<label for="<?php echo $this->get_field_id( 'p_number' ); ?>"><?php esc_html_e( 'Number of Properties', 'easy-property-listings' ); ?></label>
		</p>

		<p>
			<select id="<?php echo $this->get_field_id( 'p_skip' ); ?>" name="<?php echo $this->get_field_name( 'p_skip' ); ?>">
				<?php
				for ( $i = 0;$i <= 20;$i++ ) {
					echo '<option value="' . $i . '"';
					if ( $i == $instance['p_skip'] ) { //phpcs:ignore
						echo ' selected="selected"';
					} echo '>' . $i . '</option>';
				}
				?>
			</select>
			<label for="<?php echo $this->get_field_id( 'p_skip' ); ?>"><?php esc_html_e( 'Properties to Skip', 'easy-property-listings' ); ?></label>
		</p>

		<p>
			<input type="checkbox" id="<?php echo $this->get_field_id( 'featured' ); ?>" name="<?php echo $this->get_field_name( 'featured' ); ?>"
												<?php
												if ( $instance['featured'] ) {
													echo 'checked="checked"';}
												?>
			/>
			<label for="<?php echo $this->get_field_id( 'featured' ); ?>"><?php esc_html_e( 'Featured Properties', 'easy-property-listings' ); ?></label>
		</p>

		<p>
			<input type="checkbox" id="<?php echo $this->get_field_id( 'open_house' ); ?>" name="<?php echo $this->get_field_name( 'open_house' ); ?>"
												<?php
												if ( $instance['open_house'] ) {
													echo 'checked="checked"';}
												?>
			/>
			<label for="<?php echo $this->get_field_id( 'open_house' ); ?>"><?php echo epl_labels( 'label_home_open' ) . ' ' . __( 'Listings', 'easy-property-listings' ); ?></label>
		</p>

		<p>
			<input type="checkbox" id="<?php echo $this->get_field_id( 'archive' ); ?>" name="<?php echo $this->get_field_name( 'archive' ); ?>"
												<?php
												if ( $instance['archive'] ) {
													echo 'checked="checked"';}
												?>
			/>
			<label for="<?php echo $this->get_field_id( 'archive' ); ?>"><?php esc_html_e( 'Dynamic', 'easy-property-listings' ); ?></label>
		</p>

		<p>
			<input type="checkbox" id="<?php echo $this->get_field_id( 'order_rand' ); ?>" name="<?php echo $this->get_field_name( 'order_rand' ); ?>"
												<?php
												if ( $instance['order_rand'] ) {
													echo 'checked="checked"';}
												?>
			/>
			<label for="<?php echo $this->get_field_id( 'order_rand' ); ?>"><?php esc_html_e( 'Random Order', 'easy-property-listings' ); ?></label>
		</p>

		<p> <strong><?php esc_html_e( 'Display Settings', 'easy-property-listings' ); ?></strong></p>

		<p>
			<label for="<?php echo $this->get_field_id( 'view' ); ?>"><?php esc_html_e( 'View', 'easy-property-listings' ); ?></label>
			<select class="widefat" id="<?php echo $this->get_field_id( 'view' ); ?>" name="<?php echo $this->get_field_name( 'view' ); ?>">
				<?php
				$options = apply_filters( 'epl_listing_widget_view', array( 'default' => __( 'Default', 'easy-property-listings' ) ) );
				if ( count( $options ) ) {
					foreach ( $options as $key => $value ) {
						echo '<option value="' . esc_attr( $key ) . '"' . selected( $key, $instance['view'], false ) . '>' . $value . '</option>';
					}
				}
				?>
			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'display' ); ?>"><?php esc_html_e( 'Display Style', 'easy-property-listings' ); ?></label>
			<select class="widefat" id="<?php echo $this->get_field_id( 'display' ); ?>" name="<?php echo $this->get_field_name( 'display' ); ?>">
				<?php
					/**
					 * Usage
					 *
					 * Add more templates to this by add key pair values to this array,
					 * key will be name of template example 'my-custom-widget-template.php'
					 * and value will be label for tpl, example 'My Template'
					 */
					$options = apply_filters(
						'epl_listing_widget_templates',
						array(
							'image'      => __( 'Image', 'easy-property-listings' ),
							'image-only' => __( 'Image Only', 'easy-property-listings' ),
							'hide-image' => __( 'Hide Image', 'easy-property-listings' ),
							'list'       => __( 'List', 'easy-property-listings' ),
						)
					);
				foreach ( $options as $option_key    => $option_label ) {
					echo '<option value="' . $option_key . '" id="' . $option_key . '"', $instance['display'] === $option_key ? ' selected="selected"' : '', '>', $option_label, '</option>';
				}
				?>
			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'image' ); ?>"><?php esc_html_e( 'Image Size', 'easy-property-listings' ); ?></label>
			<select class="widefat" id="<?php echo $this->get_field_id( 'image' ); ?>" name="<?php echo $this->get_field_name( 'image' ); ?>">
				<?php
					$sizes = epl_get_thumbnail_sizes();
				foreach ( $sizes as $k => $v ) {
					$v = implode( ' x ', $v );
					echo '<option class="widefat" value="' . $k . '" id="' . $k . '"', $instance['image'] === $k ? ' selected="selected"' : '', '>', $k . ' (' . $v . ' )', '</option>';
				}
				?>
			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'd_icons' ); ?>"><?php esc_html_e( 'Icon Style', 'easy-property-listings' ); ?></label>
			<select class="widefat" id="<?php echo $this->get_field_id( 'd_icons' ); ?>" name="<?php echo $this->get_field_name( 'd_icons' ); ?>">
				<?php
					$options = array( 'none', 'all', 'beds/baths' );
				foreach ( $options as $option ) {
					echo '<option value="' . $option . '" id="' . $option . '"', $instance['d_icons'] === $option ? ' selected="selected"' : '', '>', $option, '</option>';
				}
				?>
			</select>
		</p>



		<p>
			<input type="checkbox" id="<?php echo $this->get_field_id( 'd_title' ); ?>" name="<?php echo $this->get_field_name( 'd_title' ); ?>"
												<?php
												if ( $instance['d_title'] ) {
													echo 'checked="checked"';}
												?>
			/>
			<label for="<?php echo $this->get_field_id( 'd_title' ); ?>"><?php esc_html_e( 'Property Headline', 'easy-property-listings' ); ?></label>
		</p>

		<p>
			<input type="checkbox" id="<?php echo $this->get_field_id( 'd_excerpt' ); ?>" name="<?php echo $this->get_field_name( 'd_excerpt' ); ?>"
												<?php
												if ( $instance['d_excerpt'] ) {
													echo 'checked="checked"';}
												?>
			/>
			<label for="<?php echo $this->get_field_id( 'd_excerpt' ); ?>"><?php esc_html_e( 'Excerpt', 'easy-property-listings' ); ?></label>
		</p>

		<p>
			<input type="checkbox" id="<?php echo $this->get_field_id( 'd_suburb' ); ?>" name="<?php echo $this->get_field_name( 'd_suburb' ); ?>"
												<?php
												if ( $instance['d_suburb'] ) {
													echo 'checked="checked"';}
												?>
			/>
			<label for="<?php echo $this->get_field_id( 'd_suburb' ); ?>"><?php echo epl_labels( 'label_suburb' ); ?></label>
		</p>

		<p>
			<input type="checkbox" id="<?php echo $this->get_field_id( 'd_street' ); ?>" name="<?php echo $this->get_field_name( 'd_street' ); ?>"
												<?php
												if ( $instance['d_street'] ) {
													echo 'checked="checked"';}
												?>
			/>
			<label for="<?php echo $this->get_field_id( 'd_street' ); ?>"><?php esc_html_e( 'Street Address', 'easy-property-listings' ); ?></label>
		</p>

		<p>
			<input type="checkbox" id="<?php echo $this->get_field_id( 'd_price' ); ?>" name="<?php echo $this->get_field_name( 'd_price' ); ?>"
												<?php
												if ( $instance['d_price'] ) {
													echo 'checked="checked"';}
												?>
			/>
			<label for="<?php echo $this->get_field_id( 'd_price' ); ?>"><?php esc_html_e( 'Price', 'easy-property-listings' ); ?></label>
		</p>

		<p>
			<input type="checkbox" id="<?php echo $this->get_field_id( 'd_inspection_time' ); ?>" name="<?php echo $this->get_field_name( 'd_inspection_time' ); ?>"
												<?php
												if ( $instance['d_inspection_time'] ) {
													echo 'checked="checked"';}
												?>
			/>
			<label for="<?php echo $this->get_field_id( 'd_inspection_time' ); ?>"><?php esc_html_e( 'Inspection Times', 'easy-property-listings' ); ?></label>
		</p>

		<p>
			<input type="checkbox" id="<?php echo $this->get_field_id( 'd_ical_link' ); ?>" name="<?php echo $this->get_field_name( 'd_ical_link' ); ?>"
												<?php
												if ( $instance['d_ical_link'] ) {
													echo 'checked="checked"';}
												?>
			/>
			<label for="<?php echo $this->get_field_id( 'd_ical_link' ); ?>"><?php esc_html_e( 'Inspection Time iCal Link', 'easy-property-listings' ); ?></label>
		</p>

		<p>
			<input type="checkbox" id="<?php echo $this->get_field_id( 'd_more' ); ?>" name="<?php echo $this->get_field_name( 'd_more' ); ?>"
												<?php
												if ( $instance['d_more'] ) {
													echo 'checked="checked"';}
												?>
			/>
			<label for="<?php echo $this->get_field_id( 'd_more' ); ?>"><?php esc_html_e( 'Read More Button', 'easy-property-listings' ); ?></label>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'more_text' ); ?>"><?php esc_html_e( 'Read More Label', 'easy-property-listings' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'more_text' ); ?>" name="<?php echo $this->get_field_name( 'more_text' ); ?>" type="text" value="<?php echo $more_text; ?>" />
		</p>

		<?php
	}
}

/**
 * Register Recent Property Widget.
 *
 * Registers the EPL Widgets.
 *
 * @since 3.2.2
 * @return void
 */
function epl_register_widget_recent_property() {
	register_widget( 'EPL_Widget_Recent_Property' );
}
add_action( 'widgets_init', 'epl_register_widget_recent_property' );

