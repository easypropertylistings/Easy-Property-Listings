<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

function render_content() {
	if ( have_posts() ) : ?>
		<div class="loop">
			<div class="loop-header">
				<h4 class="loop-title">
					<?php
						the_post();
							 
						if ( is_tax() && function_exists( 'epl_is_search' ) && false == epl_is_search() ) { // Tag Archive
							$term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );
							$title = sprintf( __( 'Property in %s', 'epl' ), $term->name );
						}
						else if ( function_exists( 'epl_is_search' ) && epl_is_search() ) { // Search Result
							$title = __( 'Search Result', 'epl' );
						}
						
						else if ( function_exists( 'is_post_type_archive' ) && is_post_type_archive() && function_exists( 'post_type_archive_title' ) ) { // Post Type Archive
							$title = post_type_archive_title( '', false );
						} 
						
						else { // Default catchall just in case
							$title = __( 'Listing', 'epl' );
						}
						
						if ( is_paged() )
							printf( '%s &ndash; Page %d', $title, get_query_var( 'paged' ) );
						else
							echo $title;
						
						rewind_posts();
					?>
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
			<div class="entry-header clearfix">
				<h3 class="entry-title"><?php _e('Listing not Found', 'epl'); ?></h3>
			</div>
			
			<div class="entry-content clearfix">
				<p><?php _e('Listing not found, expand your search criteria and try again.', 'epl'); ?></p>
			</div>
		</div><?php		
	endif;
}
add_action( 'builder_layout_engine_render_content', 'render_content' );
do_action( 'builder_layout_engine_render', basename( __FILE__ ) );
