<?php
/**
 * Listing widget default view
 *
 * @package     EPL
 * @subpackage  Widgets/Templates
 * @copyright   Copyright (c) 2016, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @var array 	 $args
 * @var array 	 $instance
 * @var WP_Query $query
 */

echo $args['before_widget'];
$title = apply_filters( 'widget_title', $instance['title'] );
if ( strlen( trim( $title ) ) ) {
	echo $args['before_title'] . trim( $title ) . $args['after_title'];
}

if ( $query->have_posts() ) {
	echo '<div class="epl-property-widget-' . esc_attr( $instance['display'] ) . '-wrapper">';
		if ( 'list' === $instance['display'] ) {
			echo '<ul>';
		}
		while ( $query->have_posts() ) {
			$query->the_post();
			epl_property_widget( $instance['display'], $instance['image'], $instance['d_title'], $instance['d_icons'], $instance['more_text'], $instance['d_excerpt'], $instance['d_suburb'], $instance['d_street'], $instance['d_price'], $instance['d_more'] );
		}
		wp_reset_postdata();
		if ( 'list' === $instance['display'] ) {
			echo '</ul>';
		}
	echo '</div>';
}
echo $args['after_widget'];
