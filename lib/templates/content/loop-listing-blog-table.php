<?php
/**
 * Loop Property Template: Table
 *
 * @package easy-property-listings
 * @subpackage Theme
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
global $property;
?>

<div id="post-<?php the_ID(); ?>" <?php post_class('epl-listing-post epl-property-blog epl-property-table epl-clearfix'); ?>>
	<?php do_action('epl_property_before_content'); ?>				
		<div class="property-box property-box-left property-featured-image-wrapper">
			<a href="<?php the_permalink(); ?>">
				<div class="epl-blog-image">
					<?php the_post_thumbnail( 'epl-image-medium-crop', array( 'class' => 'teaser-left-thumb' ) ); ?>
				</div>
			</a>
		</div>

		<div class="property-box property-box-right property-content">
			<!-- Address -->
			<div class="epl-table-box property-address">
				<a href="<?php the_permalink(); ?>">
					<?php do_action('epl_property_address'); ?>
				</a>
			</div>
			<!-- Property Featured Icons -->
			<div class="epl-table-box property-feature-icons">
				<?php do_action('epl_property_icons'); ?>				
			</div>
			<!-- Price -->
			<div class="epl-table-box price">
				<?php do_action('epl_property_price'); ?>
			</div>
		</div>	
	<?php do_action('epl_property_after_content'); ?>
</div>
