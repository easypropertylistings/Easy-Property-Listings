<?php
/**
 * Loop Property Template: Default
 *
 * @package easy-property-listings
 * @subpackage Theme
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
global $property;
?>

<div id="post-<?php the_ID(); ?>" <?php post_class('epl-listing-post epl-property-blog epl-clearfix'); ?> <?php do_action('epl_archive_listing_atts'); ?>>
	<?php do_action('epl_property_before_content'); ?>				
		<?php if ( has_post_thumbnail() ) : ?>
			<div class="property-box property-box-left property-featured-image-wrapper">
				<?php do_action('epl_property_archive_featured_image'); ?>
				<!-- Home Open -->
				<?php do_action('epl_property_inspection_times'); ?>
			</div>
		<?php endif; ?>

		<div class="property-box property-box-right property-content">
			<!-- Heading -->
			<h3 class="entry-title"><a href="<?php the_permalink() ?>"><?php do_action('epl_property_heading'); ?></a></h3>
			<div class="entry-content">
				<?php epl_the_excerpt(); ?>
			</div>
			<!-- Address -->
			<div class="property-address">
				<a href="<?php the_permalink(); ?>">
					<?php do_action('epl_property_address'); ?>
				</a>
			</div>
			<!-- Property Featured Icons -->
			<div class="property-feature-icons">
				<?php do_action('epl_property_icons'); ?>				
			</div>
			<!-- Price -->
			<div class="price">
				<?php do_action('epl_property_price'); ?>
			</div>
		</div>	
	<?php do_action('epl_property_after_content'); ?>
</div>
