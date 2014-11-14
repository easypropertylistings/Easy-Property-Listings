<?php
/*
 * Single Template for Property Custom Post Type : property
 */
// Exit if accessed directly

get_header(); ?>
 <div id="primary" class="site-content">
	 <div id="content" role="main">
		<?php
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
		<?php endif; ?>
	</div>
</div>	
<?php get_sidebar(); ?>
<?php get_footer(); ?>