<?php
/**
 * Listing widget default view
 *
 * @package     EPL
 * @subpackage  Widgets/Templates
 * @copyright   Copyright (c) 2019, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

echo wp_kses_post( $args['before_widget'] );
$title = apply_filters( 'widget_title', $instance['title'] ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride
if ( strlen( trim( $title ) ) ) { // phpcs:ignore WordPress.WP.GlobalVariablesOverride
	echo wp_kses_post( $args['before_title'] ) . wp_kses_post( trim( $title ) ) . wp_kses_post( $args['after_title'] );
}

if ( $query->have_posts() ) {
	echo '<div class="epl-property-widget-' . esc_attr( $instance['display'] ) . '-wrapper">';
	if ( 'list' === $instance['display'] ) {
		echo '<ul>';
	}
	while ( $query->have_posts() ) {
		$query->the_post();
		epl_property_widget( $instance['display'], $instance['image'], $instance['d_title'], $instance['d_icons'], $instance['more_text'], $instance['d_excerpt'], $instance['d_suburb'], $instance['d_street'], $instance['d_price'], $instance['d_more'], $instance['d_inspection_time'], $instance['d_ical_link'] );
	}
		wp_reset_postdata();
	if ( 'list' === $instance['display'] ) {
		echo '</ul>';
	}
	echo '</div>';
}
echo wp_kses_post( $args['after_widget'] );
