<?php
/**
 * The Default Template for displaying all Easy Property Listings archive/loop posts with WordPress Themes
 *
 * @package EPL
 * @subpackage Templates/Themes/Default
 * @since 1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

get_header(); ?>
<section id="primary" class="site-content content epl-archive-default <?php echo epl_get_active_theme_name(); ?>">
	<div id="content" role="main">
		<?php
		if ( have_posts() ) : ?>
			<div class="loop pad">
				<header class="archive-header entry-header loop-header">
					<h4 class="archive-title loop-title">
						<?php do_action( 'epl_the_archive_title' ); ?>
					</h4>
				</header>

				<div class="entry-content loop-content">
					<?php do_action( 'epl_property_loop_start' ); ?>
					<?php while ( have_posts() ) : // The Loop
							the_post();
							do_action('epl_property_blog');
						endwhile; // end of one post
					?>
					<?php do_action( 'epl_property_loop_end' ); ?>
				</div>

				<div class="loop-footer">
					<!-- Previous/Next page navigation -->
					<div class="loop-utility clearfix">
						<?php do_action('epl_pagination'); ?>
					</div>
				</div>
			</div>
		<?php
		else :
			?><div class="hentry">
				<div class="entry-header clearfix">
					<h3 class="entry-title"><?php apply_filters( 'epl_property_search_not_found_title' , _e('Listing not Found', 'easy-property-listings') ); ?></h3>
				</div>

				<div class="entry-content clearfix">
					<p><?php apply_filters( 'epl_property_search_not_found_message' , _e('Listing not found, expand your search criteria and try again.', 'easy-property-listings') ); ?></p>
				</div>
			</div>
		<?php endif; ?>
	</div>
</section>
<?php
get_sidebar();
get_footer();
