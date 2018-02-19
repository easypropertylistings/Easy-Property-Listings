<?php
/**
 * The Template for displaying all Easy Property Listings archive/loop posts with the iThemes Builder Theme Framework
 *
 * @package EPL
 * @subpackage Templates/Themes/Heuman
 * @since 1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

function epl_archive_render_content() {
	if ( have_posts() ) : ?>
		<div class="loop">
			<div class="loop-header">
				<h4 class="loop-title">
					<?php do_action( 'epl_the_archive_title' ); ?>
				</h4>
			</div>

			<div class="loop-content">
				<?php do_action( 'epl_property_loop_start' ); ?>
				<?php while ( have_posts() ) : // The Loop
						the_post();
						do_action('epl_property_blog');
					endwhile; // end of one post
				?>
				<?php do_action( 'epl_property_loop_end' ); ?>
			</div>

			<div class="loop-footer">
				<!-- Previous/Next page navigation -->
				<div class="loop-utility clearfix">
					<?php do_action('epl_pagination'); ?>
				</div>
			</div>
		</div>
		<?php
	else :
		//do_action( 'builder_template_show_not_found' );
		?><div class="hentry">
			<?php do_action( 'epl_property_search_not_found' ); ?>
		</div><?php
	endif;
}
add_action( 'builder_layout_engine_render_content', 'epl_archive_render_content' );
do_action( 'builder_layout_engine_render', basename( __FILE__ ) );
