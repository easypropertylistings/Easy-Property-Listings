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

<div id="post-<?php the_ID(); ?>" <?php post_class( 'epl-listing-post epl-property-blog epl-property-table epl-table epl-table-open epl-listing-grid-view-forced' ); ?>>
	<?php do_action( 'epl_property_before_content' ); ?>
		<div class="epl-table-column-image property-featured-image-wrapper">
			<?php do_action( 'epl_property_archive_featured_image', 'epl-image-medium-crop', 'teaser-left-thumb', true, false ); ?>
		</div>

		<div class="epl-table-column-content property-content">
			<!-- Address -->
			<div class="epl-table-box epl-table-column epl-table-column-left">
				<div class="epl-table-address property-address">
					<a href="<?php the_permalink(); ?>"><?php do_action( 'epl_property_address' ); ?></a>
				</div>
				<div class="property-feature-icons">
					<?php do_action( 'epl_property_icons' ); ?>
				</div>
			</div>
			<!-- Property Featured Icons -->
			<div class="epl-table-box epl-table-column epl-table-column-middle price">
				<?php do_action( 'epl_property_price' ); ?>
			</div>
			<!-- Price -->
			<div class="epl-table-box epl-table-column epl-table-column-right">
				<?php do_action( 'epl_property_inspection_times' ); ?>
			</div>
		</div>
	<?php do_action( 'epl_property_after_content' ); ?>
	
	<?php
	$card_link = true;
	if ( $card_link ) {
		?>
		<a href="<?php the_permalink(); ?>" class="epl-row__link">View <?php do_action( 'epl_property_address' ); ?></a>
		<?php
	}
	?>
</div>
