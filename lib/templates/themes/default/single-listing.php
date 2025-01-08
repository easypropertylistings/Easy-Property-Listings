<?php
/**
 * The Default Template for displaying all Easy Property Listings single posts with WordPress Themes
 *
 * @package EPL
 * @subpackage Templates/Themes/Default
 * @since 1.0.0
 * @since 3.6.0 Hook added for sidebar.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header(); ?>
<section id="epl-listing-container-single" class="epl-container epl-container--single">
	<main id="epl-listing" class="site-content content-area epl-single-default <?php echo esc_attr( epl_get_active_theme_name() ); ?>">
		<section class="content">
			<div id="content" class="pad" role="main">
				<?php
				if ( have_posts() ) :
					?>
					<div class="loop">
						<div class="loop-content <?php echo esc_attr( epl_template_class( 'default', 'single' ) ); ?>">
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
			</div>
		</section>
	</main>
	<?php do_action( 'epl_get_sidebar' ); ?>
</section>
<?php get_footer(); ?>
