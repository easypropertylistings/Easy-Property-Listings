<?php
/**
 * The Template for displaying all Easy Property Listings archive/loop posts with the TwentyFourteen Theme
 *
 * @package EPL
 * @subpackage Templates/Themes/TwentyFourteen
 * @since 3.0
 */

get_header(); ?>

	<section id="primary" class="content-area epl-archive-default <?php echo epl_get_active_theme_name(); ?>">
		<div id="content" class="site-content <?php echo epl_template_class( 'twentyfourteen', 'archive' ); ?>" role="main">

			<?php if ( have_posts() ) : ?>

			<header class="page-header">
				<h1 class="page-title">
					<?php do_action( 'epl_the_archive_title' ); ?>
				</h1>
			</header><!-- .page-header -->
			<?php
					do_action( 'epl_property_loop_start' );
					// Start the Loop.
					while ( have_posts() ) : the_post();

						/*
						 * Include the post format-specific template for the content. If you want to
						 * use this in a child theme, then include a file called called content-___.php
						 * (where ___ is the post format) and that will be used instead.
						 */
						do_action('epl_property_blog');

					endwhile;

					do_action( 'epl_property_loop_end' );

					// Previous/next page navigation.
					do_action('epl_pagination');

				else :
					?><div class="hentry">
						<?php do_action( 'epl_property_search_not_found' ); ?>
					</div>
					<?php
				endif;
			?>
		</div><!-- #content -->
	</section><!-- #primary -->

<?php
get_sidebar( 'content' );
get_sidebar();
get_footer();
