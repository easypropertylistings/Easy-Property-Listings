<?php
/**
 * The Template for displaying all Easy Property Listings single posts with the TwentyFourteen Theme
 *
 * @package EPL
 * @subpackage Templates/Themes/TwentyFourteen
 * @since 3.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

get_header(); ?>

	<div id="primary" class="content-area epl-single-default <?php echo epl_get_active_theme_name(); ?>">
		<div id="content" class="site-content <?php echo epl_template_class( 'twentyfourteen', 'single' ); ?>" role="main">
			<?php
				// Start the Loop.
				while ( have_posts() ) : the_post();

					/*
					 * Include the post format-specific template for the content. If you want to
					 * use this in a child theme, then include a file called called content-___.php
					 * (where ___ is the post format) and that will be used instead.
					 */

					do_action('epl_property_single');

					// If comments are open or we have at least one comment, load up the comment template.
					if ( comments_open() || get_comments_number() ) {
						comments_template();
					}
				endwhile;
			?>
		</div><!-- #content -->
	</div><!-- #primary -->

<?php
get_sidebar( 'content' );
get_sidebar();
get_footer();
