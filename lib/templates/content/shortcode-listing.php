<?php
/**
 * Shortcode Listing Template
 *
 * @package     EPL
 * @subpackage  Templates/Content
 * @copyright   Copyright (c) 2019, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Shortcode listing template
 *
 * @var $attributes array   Shortcode Attributes.
 * @var $query_open WP_Query    Query object for listings.
 */
if ( $query_open->have_posts() ) {
	$attributes['class'] = isset( $attributes['class'] ) ? $attributes['class'] : 'epl-shortcode-listing';
	?>
	<div class="loop epl-shortcode epl-clearfix">
		<div class="loop-content <?php echo esc_attr( $attributes['class'] ); ?> <?php echo esc_attr( epl_template_class( $attributes['template'], 'archive' ) ); ?> epl-clearfix">
			<?php
			if ( 'on' === $attributes['tools_top'] ) {
				do_action( 'epl_property_loop_start' );
			}
			while ( $query_open->have_posts() ) {
				$query_open->the_post();
				$attributes['template'] = str_replace( '_', '-', $attributes['template'] );
				epl_property_blog( $attributes['template'] );
			}
			if ( 'on' === $attributes['tools_bottom'] ) {
				do_action( 'epl_property_loop_end' );
			}
			?>
		</div>
		<div class="loop-footer epl-clearfix">
				<?php
				if ( 'on' === $attributes['pagination'] ) {
					do_action( 'epl_pagination', array( 'query' => $query_open ) );
				}
				?>
		</div>
	</div>
	<?php
	wp_reset_postdata();
} else {
	echo '<h3>' . esc_html__( 'Nothing found, please check back later.', 'easy-property-listings' ) . '</h3>';
}
