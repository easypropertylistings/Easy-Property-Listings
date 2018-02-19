<?php
/**
 * The Default Template for displaying all Easy Property Listings single posts with WordPress Themes
 *
 * @package EPL
 * @subpackage Templates/Themes/Default
 * @since 1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

get_header(); ?>
 <div id="primary" class="site-content content-area epl-single-default <?php echo epl_get_active_theme_name(); ?>">
	<section class="content">
		<div id="content" class="pad" role="main">
			<?php
			if ( have_posts() ) : ?>
				<div class="loop">
					<div class="loop-content <?php echo epl_template_class( 'default', 'single' ); ?>">
						<?php
							while ( have_posts() ) : // The Loop
								the_post();
								do_action('epl_property_single');
								comments_template(); // include comments template
							endwhile; // end of one post
						?>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</section>
</div>
<?php get_sidebar(); ?>
<?php get_footer(); ?>
