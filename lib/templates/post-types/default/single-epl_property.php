<?php
/*
 * Single Template for Property Custom Post Type : epl_property
 */

if ( have_posts() ) : ?>
	<div class="loop">
		<div class="loop-content">
			<?php
				while ( have_posts() ) : // The Loop
					the_post();
					
					if ( function_exists('epl_property_single') ) {
						echo epl_property_single();
					}
					
					comments_template(); // include comments template
				endwhile; // end of one post
			?>
		</div>
	</div>
<?php endif;
