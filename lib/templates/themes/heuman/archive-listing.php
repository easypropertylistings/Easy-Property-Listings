<?php
/**
 * The Template for displaying all Easy Property Listings archive/loop posts with the Heuman Theme
 *
 * @package EPL
 * @subpackage Templates/Themes/Heuman
 * @since 2.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

get_header(); ?>

<section class="content">

	<?php get_template_part('inc/page-title'); ?>

	<div class="pad group">

		<?php if ((category_description() != '') && !is_paged()) : ?>
			<div class="notebox">
				<?php echo category_description(); ?>
			</div>
		<?php endif; ?>

		<?php if ( have_posts() ) : ?>

			<div class="post-list group">
				<?php do_action( 'epl_property_loop_start' ); ?>
					<?php $i = 1; echo '<div class="post-row">'; while ( have_posts() ): the_post(); ?>
						<?php do_action( 'epl_property_blog' ); ?>
					<?php if($i % 2 == 0) { echo '</div><div class="post-row">'; } $i++; endwhile; echo '</div>'; ?>
				<?php do_action( 'epl_property_loop_end' ); ?>
			</div><!--/.post-list-->

			<?php get_template_part('inc/pagination'); ?>
			<?php do_action( 'epl_property_loop_end' ); ?>
		<?php endif; ?>
			<div class="post-list group">
				<div class="entry-header clearfix">
					<h3 class="entry-title"><?php apply_filters( 'epl_property_search_not_found_title' , _e('Listing not Found', 'easy-property-listings') ); ?></h3>
				</div>

				<div class="entry-content clearfix">
				<p><?php apply_filters( 'epl_property_search_not_found_message' , _e('Listing not found, expand your search criteria and try again.', 'easy-property-listings') ); ?></p>
			</div>
		</div>
	</div><!--/.pad-->

</section><!--/.content-->

<?php get_sidebar(); ?>

<?php get_footer(); ?>