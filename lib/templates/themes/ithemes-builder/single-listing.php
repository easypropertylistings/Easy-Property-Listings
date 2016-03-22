<?php
/**
 * The Template for displaying all Easy Property Listings single posts with the iThemes Builder Theme Framework
 *
 * @package EPL
 * @subpackage Templates/Themes/iThemesBuilder
 * @since 1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

function epl_single_render_content() {
	if ( have_posts() ) : ?>
		<div class="loop">
			<div class="loop-content">
				<?php
					while ( have_posts() ) : // The Loop
						the_post();
						do_action('epl_property_single');
						comments_template(); // include comments template
					endwhile; // end of one post
				?>
			</div>
		</div>
		<?php
	else : // do not delete
		do_action( 'builder_template_show_not_found' );
	endif; // do not delete
}
add_action( 'builder_layout_engine_render_content', 'epl_single_render_content' );
do_action( 'builder_layout_engine_render', basename( __FILE__ ) );
