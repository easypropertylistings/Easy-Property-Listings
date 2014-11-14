<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

function render_content() {
	if ( have_posts() ) : ?>
		<div class="loop">
			<div class="loop-content">
				<?php
					while ( have_posts() ) : // The Loop
						the_post();
						if ( function_exists('epl_property_single') ) {
							epl_property_single();
						}
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
add_action( 'builder_layout_engine_render_content', 'render_content' );
do_action( 'builder_layout_engine_render', basename( __FILE__ ) );
