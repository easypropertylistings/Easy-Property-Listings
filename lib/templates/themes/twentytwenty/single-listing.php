<?php
/**
 * The EPL Listing single template file
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
		<div class="loop section-inner medium">
			<div class="loop-content <?php echo esc_attr( epl_template_class( 'twentytwenty', 'single' ) ); ?>">
				<?php
				while ( have_posts() ) : // The Loop.
					the_post();
					do_action( 'epl_property_single' );
					comments_template(); // include comments template.
					endwhile; // end of one post.
				?>
			</div>
		</div>
	<?php endif; ?>

</main><!-- #site-content -->

<?php get_template_part( 'template-parts/footer-menus-widgets' ); ?>

<?php get_footer(); ?>
