<?php
/**
 * The Default Template for displaying all Easy Property Listings archive/loop posts with WordPress Themes
 *
 * @package EPL
 * @subpackage Templates/Themes/Default
 * @since 1.0
 * @since 3.6 Tools moved outside of loop wrapper.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header(); ?>
<section id="primary" class="site-content content epl-archive-default <?php echo esc_attr( epl_get_active_theme_name() ); ?>">
	<div id="content" role="main">
		
		
		
		<?php
		/**
		 * Test code
		**/
		
			?>
			
			<div class="epl-scss-test-wrapper">
				<h2>Default</h2>
				<?php echo do_shortcode( '[listing limit=1 pagination=off]' ) ;?>
			
			</div>
			
			<div class="epl-scss-test-wrapper">
				<h2>Slim</h2>
				<?php echo do_shortcode( '[listing limit=1 template=slim pagination=off]' ) ;?>
			
			</div>
			
			<div class="epl-scss-test-wrapper">
				<h2>Card</h2>
				<?php echo do_shortcode( '[listing limit=1 template=card pagination=off]' ) ;?>
			
			</div>
			
			<div class="epl-scss-test-wrapper">
				<h2>Search</h2>
				<?php echo do_shortcode( '[listing_search]' ) ;?>
			
			</div>
			
			<?php
		
		
		
		?>
		
		
		<?php
		if ( have_posts() ) :
			?>
			<div class="loop pad">
				<header class="archive-header entry-header loop-header">
					<h4 class="archive-title loop-title">
						<?php do_action( 'epl_the_archive_title' ); ?>
					</h4>
				</header>

				<?php do_action( 'epl_property_loop_start' ); ?>
				<div class="entry-content loop-content epl-template-blog <?php echo esc_attr( epl_template_class( 'default', 'archive' ) ); ?>">
					<?php
					while ( have_posts() ) : // The Loop.
							the_post();
							do_action( 'epl_property_blog' );
						endwhile; // end of one post.
					?>
				</div>
				<?php do_action( 'epl_property_loop_end' ); ?>

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
	</div>
</section>
<?php
// get_sidebar();
get_footer();
