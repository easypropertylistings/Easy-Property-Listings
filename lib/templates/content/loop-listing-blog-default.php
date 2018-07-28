<?php
/**
 * Loop Property Template: Default
 *
 * @package     EPL
 * @subpackage  Templates/Content
 * @copyright   Copyright (c) 2015, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
global $property;
?>


<li>
<div id="post-<?php the_ID(); ?>" <?php post_class('epl-listing-post epl-property-blog epl-clearfix'); ?> <?php do_action('epl_archive_listing_atts'); ?>>
	<div class="epl-property-blog-entry-wrapper epl-clearfix">
		<?php do_action('epl_property_before_content'); ?>
			<?php if ( has_post_thumbnail() ) : ?>
				<div class="property-box property-box-left property-featured-image-wrapper">
					<?php do_action('epl_property_archive_featured_image'); ?>
					<!-- Home Open -->
					
				</div>
			<?php endif; ?>

			<div class="property-box property-box-right property-content">
				<!-- Heading -->
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
                                        <?php do_action('epl_property_inspection_times'); ?>
					<?php do_action('epl_property_price'); ?>
                                  
				</div>
                       
                                <h3 class="entry-title"><a href="<?php the_permalink() ?>"><?php do_action('epl_property_heading'); ?></a></h3>
				<div class="entry-content">
					<?php epl_the_excerpt(); ?>
				</div>
			</div>
		<?php do_action('epl_property_after_content'); ?>
	</div>
</div>

</li>
