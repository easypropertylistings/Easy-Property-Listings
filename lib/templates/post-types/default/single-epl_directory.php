<?php
/*
 * Single Template for Staff Directory Custom Post Type : epl_directory
 */
 
if ( have_posts() ) : ?>
	<div class="loop">
		<div class="loop-content">
			<?php
				while ( have_posts() ) : // The Loop
					the_post();
					
					if ( function_exists('epl_property_author_box_directory')) {
						echo epl_property_author_box_directory();
					}
					
					if ( function_exists('epl_property_directory_content')) {
						echo epl_property_directory_content();
					}
					
					comments_template(); // include comments template
				endwhile; // end of one post
			?>
		</div>
	</div>
<?php endif;
