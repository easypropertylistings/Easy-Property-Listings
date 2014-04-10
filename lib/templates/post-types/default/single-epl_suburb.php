<?php
/*
 * Single Template for Suburb Custom Post Type : epl_suburb
 */
 
if ( have_posts() ) : ?>
	<div class="loop">
		<div class="loop-content">
			<?php
				while ( have_posts() ) : // The Loop
					the_post();
					if ( function_exists('epl_suburb_single')) {
						echo epl_suburb_single();
					}
					comments_template(); // include comments template
				endwhile; // end of one post
			?>
		</div>
	</div>
<?php endif;
