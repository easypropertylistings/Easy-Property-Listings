<?php
/**
 * SHORTCODE :: Listing Results [listing_results]
 *
 * @package     EPL
 * @subpackage  Shortcode/ListingResults
 * @copyright   Copyright (c) 2023, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.5.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Page Builder Archive / Search Results Loop
 * Load the Easy Property Listings Loop using a shortcode. For use in Page Builders like Elementor, Divi, WP Bakery, Visual Composer.
 *
 * @param array $atts Shortcode options.
 *
 * @return false|string
 * @since 3.5.0
 */
function epl_listing_results_loop_callback( $atts ) {

	$attributes = shortcode_atts(
		array(
			'tools_top'    => 'off', // Tools before the loop like Sorter and Grid on or off.
			'tools_bottom' => 'off', // Tools after the loop like pagination on or off.
			'pagination'   => 'on', // Enable or disable pagination.
		),
		$atts
	);

	ob_start();

	if ( ( function_exists( 'epl_is_search' ) && true === epl_is_search() ) || ( function_exists( 'is_epl_core_post' ) && true === is_epl_core_post() ) ) {

		if ( have_posts() ) :
			?>

			<div class="epl-template-blog">
				<?php
				if ( 'on' === $attributes['tools_top'] ) {
					do_action( 'epl_property_loop_start', $attributes );
				}
				?>

				<?php
				while ( have_posts() ) :
					the_post();
					?>
					<?php do_action( 'epl_property_blog' ); ?>
				<?php endwhile; ?>

				<?php
				if ( 'on' === $attributes['tools_bottom'] ) {
					do_action( 'epl_property_loop_end' );
				}
				?>
			</div>

			<div class="loop-footer">
				<!-- Previous/Next page navigation -->
				<div class="loop-utility clearfix">
					<?php
					if ( 'on' === $attributes['pagination'] ) {
						do_action( 'epl_pagination' );
					}
					?>
				</div>
			</div>

		<?php else : ?>

			<?php do_action( 'epl_property_search_not_found' ); ?>

			<?php
		endif;
	}

	return ob_get_clean();

}
add_shortcode( 'listing_results', 'epl_listing_results_loop_callback' );
