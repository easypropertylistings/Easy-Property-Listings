<?php
/**
 * EPL Archives template
 *
 * @package Avada
 * @subpackage Templates
 * @theme_version 6.2.2
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}
get_header(); ?>

<section id="content" <?php Avada()->layout->add_class( 'content_class' ); ?> <?php Avada()->layout->add_style( 'content_style' ); ?>>

	<?php
	if ( have_posts() ) :
		?>
		<div class="loop pad">

			<div class="entry-content loop-content <?php echo epl_template_class( 'default', 'archive' ); ?>">
				<?php do_action( 'epl_property_loop_start' ); ?>
				<?php
				while ( have_posts() ) : // The Loop
						the_post();
						do_action( 'epl_property_blog' );
					endwhile; // end of one post
				?>
				<?php do_action( 'epl_property_loop_end' ); ?>
			</div>

			<div class="loop-footer">
				<!-- Previous/Next page navigation -->
				<div class="loop-utility clearfix">
					<?php do_action( 'epl_pagination' ); ?>
				</div>
			</div>
		</div>
		<?php
	else :
		?>
		<div class="hentry">
			<?php do_action( 'epl_property_search_not_found' ); ?>
		</div>
	<?php endif; ?>

</section>
<?php do_action( 'avada_after_content' ); ?>
<?php
get_footer();

/* Omit closing PHP tag to avoid "Headers already sent" issues. */
