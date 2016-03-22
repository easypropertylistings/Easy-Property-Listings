<?php
/**
 * The Template for displaying the author posts with the Genesis Theme Framework
 *
 * @package EPL
 * @subpackage Templates/Themes/Genesis
 * @since 1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

get_header(); ?>

<div id="primary" class="site-content">
	<?php
	if ( have_posts() ) : ?>
		<div class="loop">
			<div class="loop-header">
				<?php echo epl_property_author_box(); ?>
				<h4 class="loop-title">
					<?php
						the_post();

						if ( is_category() ) { // Category Archive
							$title = sprintf( __( 'Archive for %s', 'easy-property-listings' ), single_cat_title( '', false ) );
						}
						else if ( is_tag() ) { // Tag Archive
							$title = sprintf( __( 'Archive for %s', 'easy-property-listings' ), single_tag_title( '', false ) );
						}
						else if ( is_tax() ) { // Tag Archive
							$title = sprintf( __( 'Archive for %s', 'easy-property-listings' ), builder_get_tax_term_title() );
						}
						else if ( function_exists( 'is_post_type_archive' ) && is_post_type_archive() && function_exists( 'post_type_archive_title' ) ) { // Post Type Archive
							$title = post_type_archive_title( '', false );
						}
						else if ( is_author() ) { // Author Archive
							$title = sprintf( __( 'Author Archive for %s', 'easy-property-listings' ), get_the_author() );
						}
						else if ( is_year() ) { // Year-Specific Archive
							$title = sprintf( __( 'Archive for %s', 'easy-property-listings' ), get_the_time( 'Y' ) );
						}
						else if ( is_month() ) { // Month-Specific Archive
							$title = sprintf( __( 'Archive for %s', 'easy-property-listings' ), get_the_time( 'F Y' ) );
						}
						else if ( is_day() ) { // Day-Specific Archive
							$title = sprintf( __( 'Archive for %s', 'easy-property-listings' ), get_the_date() );
						}
						else if ( is_time() ) { // Time-Specific Archive
							$title = __( 'Time Archive', 'easy-property-listings' );
						}
						else { // Default catchall just in case
							$title = __( 'Archive', 'easy-property-listings' );
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
				<?php
					while ( have_posts() ) : // The Loop
						the_post(); ?>
						<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
							<!-- title, meta, and date info -->
							<div class="entry-header clearfix">
								<h3 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
								<div class="entry-meta">
									<?php
										printf( __( 'By %s', 'easy-property-listings' ), '<span class="meta-author">' . builder_get_author_link() . '</span>' );
										do_action( 'builder_comments_popup_link', '<span class="meta-comments">&middot; ', '</span>', __( 'Comments %s', 'easy-property-listings' ), __( '(0)', 'easy-property-listings' ), __( '(1)', 'easy-property-listings' ), __( '(%)', 'easy-property-listings' ) );
									?>
								</div>

								<div class="entry-meta date">
									<span class="weekday"><?php the_time( 'l' ); ?><span class="weekday-comma">,</span></span>
									<span class="month"><?php the_time( 'F' ); ?></span>
									<span class="day"><?php the_time( 'j' ); ?><span class="day-suffix"><?php the_time( 'S' ); ?></span><span class="day-comma">,</span></span>
									<span class="year"><?php the_time( 'Y' ); ?></span>
								</div>
							</div>

							<!-- post content -->
							<div class="entry-content clearfix">
								<?php the_excerpt(); ?>
							</div>

							<!-- categories, tags and comments -->
							<div class="entry-footer clearfix">
								<?php do_action( 'builder_comments_popup_link', '<div class="entry-meta alignright"><span class="comments">', '</span></div>', __( 'Comments %s', 'easy-property-listings' ), __( '(0)', 'easy-property-listings' ), __( '(1)', 'easy-property-listings' ), __( '(%)', 'easy-property-listings' ) ); ?>
								<div class="entry-meta alignleft">
									<div class="categories"><?php printf( __( 'Categories : %s', 'easy-property-listings' ), get_the_category_list( ', ' ) ); ?></div>
									<?php the_tags( '<div class="tags">' . __( 'Tags : ', 'easy-property-listings' ), ', ', '</div>' ); ?>
								</div>
							</div>
						</div>
						<!-- end .post -->

						<?php comments_template(); // include comments template
					endwhile; // end of one post
				?>
			</div>

			<div class="loop-footer">
				<!-- Previous/Next page navigation -->
				<div class="loop-utility clearfix">
					<div class="alignleft"><?php previous_posts_link( __( '&laquo; Previous Page', 'easy-property-listings' ) ); ?></div>
					<div class="alignright"><?php next_posts_link( __( 'Next Page &raquo;', 'easy-property-listings' ) ); ?></div>
				</div>
			</div>
		</div>
	<?php endif; ?>
</div>

<?php
get_sidebar();
get_footer();
