<?php
/**
 * The main EPL Listing template file
 *
 * @package EPL
 * @subpackage Templates/Themes/TwentyTwenty
 * @since 3.4.14
 */

get_header();
?>

<main id="site-content" role="main">
	<?php
	if ( have_posts() ) :
		?>

		<header class="archive-header has-text-align-center header-footer-group">

			<div class="archive-header-inner section-inner medium">
					<h1 class="archive-title"><?php do_action( 'epl_the_archive_title' ); ?></h1>

			</div><!-- .archive-header-inner -->

		</header><!-- .archive-header -->

		<div class="section-inner medium loop pad">

			<div class="loop-content <?php echo esc_attr( epl_template_class( 'twentytwenty', 'archive' ) ); ?>">
				<?php do_action( 'epl_property_loop_start' ); ?>
				<?php
				while ( have_posts() ) : // The Loop.
						the_post();
						do_action( 'epl_property_blog' );
					endwhile; // end of one post.
				?>
				<?php do_action( 'epl_property_loop_end' ); ?>
			</div>

			<div class="loop-footer">
				<!-- Previous/Next page navigation -->
				<div class="loop-utility clearfix">
					<?php do_action( 'epl_pagination' ); ?>

					<?php //get_template_part( 'template-parts/pagination' ); // Twenty Twenty Pagination ?>
				</div>
			</div>
		</div>
		<?php
		else :
			?>
		<header class="archive-header has-text-align-center header-footer-group">

			<div class="archive-header-inner section-inner medium">
					<div class="archive-title"><?php do_action( 'epl_property_search_not_found' ); ?></div>
			</div><!-- .archive-header-inner -->

		</header><!-- .archive-header -->

		<?php endif; ?>

</main><!-- #site-content -->

<?php get_template_part( 'template-parts/footer-menus-widgets' ); ?>

<?php
get_footer();
