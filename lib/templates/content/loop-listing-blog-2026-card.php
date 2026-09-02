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

global $property;
?>

<div id="post-<?php the_ID(); ?>" <?php post_class( 'epl-listing-post epl-property-blog epl-property-blog-card epl-listing-grid-view-forced' ); ?> <?php do_action( 'epl_archive_listing_atts' ); ?>>
	<div class="epl-property-blog-entry-wrapper">

		<?php do_action( 'epl_property_before_content' ); ?>
			<?php if ( has_post_thumbnail() ) : ?>
				<div class="epl-row epl-row--header epl-row--featured-image">
					<?php do_action( 'epl_property_archive_featured_image' ); ?>
				</div>
			<?php endif; ?>

			<div class="epl-row epl-row--content">

				<?php
				$element_address = true;
				if ( $element_address ) {
					?>
					<div class="epl-listing-meta epl-listing-meta--address">
						<?php do_action( 'epl_property_address' ); ?>
					</div>
					<?php
				}
				?>

				<?php
				$element_price = true;
				if ( $element_price ) {
					?>
					<div class="epl-listing-meta epl-listing-meta--price">
						<?php do_action( 'epl_property_price' ); ?>
					</div>
					<?php
				}
				?>
				
				<?php
				$element_icons = true;
				if ( $element_icons ) {
					?>
					<div class="epl-icons property-feature-icons">
						<?php do_action( 'epl_property_icons' ); ?>
					</div>
					<?php
				}
				?>
			</div>

		<?php do_action( 'epl_property_after_content' ); ?>
	</div>
	
	<?php
	$card_link = true;
	if ( $card_link ) {
		?>
		<a href="<?php the_permalink(); ?>" class="epl-row__link">View <?php do_action( 'epl_property_address' ); ?></a>
		<?php
	}
	?>
</div>
