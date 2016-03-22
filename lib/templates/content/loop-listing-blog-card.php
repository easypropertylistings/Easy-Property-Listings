<?php
/**
 * Loop Property Template: Card home open list
 *
 * @package     EPL
 * @subpackage  Templates/Content
 * @copyright   Copyright (c) 2015, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
?>

<div id="post-<?php the_ID(); ?>" <?php post_class('epl-listing-post epl-property-blog epl-property-blog-card'); ?>>
	<?php do_action('epl_property_loop_before_content'); ?>
		<div class="epl-entry-content">
			<?php if ( has_post_thumbnail() ) : ?>
			<div class="property-featured-image-wrapper">
				<?php do_action('epl_property_archive_featured_image'); ?>
				<!-- Home Open -->
			</div>
		<?php endif; ?>

			<div class="property-content">
				<!-- Address -->
				<div class="property-address">
					<a href="<?php the_permalink(); ?>">
						<?php do_action('epl_property_tab_address'); ?>
					</a>
				</div>

				<div class="price">
					<?php do_action('epl_property_price'); ?>
				</div>

				<!-- Property Featured Icons -->
				<div class="property-feature-icons">
					<?php do_action('epl_property_icons'); ?>
				</div>

			</div>
		</div>
	<?php do_action('epl_property_loop_after_content'); ?>
</div>
