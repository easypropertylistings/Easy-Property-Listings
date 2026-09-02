<?php
/**
 * Loop Property Template: Slim home open list
 *
 * @package     EPL
 * @subpackage  Templates/LoopListingBlogSlim
 * @copyright   Copyright (c) 2020, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 * @since       3.6 Using epl image size instead of thumbnail.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div id="post-<?php the_ID(); ?>" <?php post_class( 'epl-listing-post epl-property-blog epl-property-blog-slim epl-clearfix' ); ?>>
	<div class="epl-property-blog-entry-wrapper">
		<?php do_action( 'epl_property_loop_before_content' ); ?>
			<?php if ( has_post_thumbnail() ) : ?>
				<div class="property-box slim property-box-left property-box-left-slim property-featured-image-wrapper">
					<?php do_action( 'epl_property_archive_featured_image' ); ?>
				</div>
			<?php endif; ?>

			<div class="property-box slim property-box-right property-box-right-slim property-content">
				<!-- Heading -->
				<h3 class="entry-title"><a href="<?php the_permalink(); ?>"><?php do_action( 'epl_property_heading' ); ?></a></h3>

				<!-- Address -->
				<div class="property-address">
					<a href="<?php the_permalink(); ?>">
						<?php do_action( 'epl_property_tab_address' ); ?>
					</a>
				</div>

				<!-- Home Open -->
				<?php do_action( 'epl_property_inspection_times' ); ?>

				<!-- Property Featured Icons -->
				<div class="property-feature-icons">
					<?php do_action( 'epl_property_icons' ); ?>
				</div>

				<div class="price">
					<?php do_action( 'epl_property_price' ); ?>
				</div>
			</div>
		<?php do_action( 'epl_property_loop_after_content' ); ?>
	</div>
</div>
