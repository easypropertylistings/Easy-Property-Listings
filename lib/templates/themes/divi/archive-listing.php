<?php
/**
 * The Default Template for displaying all Easy Property Listings archive/loop posts with the Divi Theme Framework
 *
 * @package EPL
 * @subpackage Templates/Themes/Divi
 * @since 1.0
 * @since 3.4.19 Removed article wrapper
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<?php get_header(); ?>

<div id="main-content">
	<div class="container">
		<div id="content-area" class="clearfix <?php echo esc_attr( epl_template_class( 'divi', 'archive' ) ); ?>">
			<div id="left-area">
			<?php
			if ( have_posts() ) :
				?>
				<?php do_action( 'epl_property_loop_start' ); ?>
				<?php
				while ( have_posts() ) :
					the_post();
					$post_format = get_post_format();
					?>
					<?php do_action( 'epl_property_blog' ); ?>
					<?php
					endwhile;
				?>
				<?php do_action( 'epl_property_loop_end' ); ?>

			<div class="loop-footer">
					<!-- Previous/Next page navigation -->
					<div class="loop-utility clearfix">
						<?php do_action( 'epl_pagination' ); ?>
					</div>
				</div>

				<?php

				else :
					do_action( 'epl_property_search_not_found' );
				endif;
				?>
			</div> <!-- #left-area -->

			<?php get_sidebar(); ?>
		</div> <!-- #content-area -->
	</div> <!-- .container -->
</div> <!-- #main-content -->

<?php get_footer(); ?>
