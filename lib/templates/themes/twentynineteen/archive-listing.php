<?php
/**
 * The template for displaying archive pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package WordPress
 * @subpackage Twenty_Nineteen
 * @since 1.0.0
 */

get_header();
?>

	<section id="primary" class="content-area epl-archive-default <?php echo esc_attr( epl_get_active_theme_name() ); ?>">
		<main id="main" class="site-main">

		<?php if ( have_posts() ) : ?>

			<header class="page-header">
				<h1 class="page-title">
					<?php do_action( 'epl_the_archive_title' ); ?>
				</h1>

			</header><!-- .page-header -->

			<?php
			// Start the Loop.
			do_action( 'epl_property_loop_start' );
			?>

			<div class="epl-outer-grid-wrapper">

			<?php
			while ( have_posts() ) :
				the_post();


				do_action( 'epl_property_blog' );

				?>

				<?php

				// End the loop.
			endwhile;
			?>

			</div>


			<?php

			do_action( 'epl_property_loop_end' );

			// Previous/next page navigation.
			// twentynineteen_the_posts_navigation();.

			?>
			<div class="loop-footer epl-loop-footer">
				<!-- Previous/Next page navigation -->
				<div class="loop-utility clearfix">
					<?php do_action( 'epl_pagination' ); ?>
				</div>
			</div>

				<?php

				// If no content, include the "No posts found" template.
				else :
					get_template_part( 'template-parts/content/content', 'none' );

		endif;
				?>
		</main><!-- #main -->
	</section><!-- #primary -->

<?php
get_footer();
