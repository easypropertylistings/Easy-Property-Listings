<?php
/*
 * Loop Property Template: table home open list
 *
 * @package easy-property-listings
 * @subpackage Theme
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
?>

<div id="post-<?php the_ID(); ?>" <?php post_class('epl-listing-post epl-property-blog epl-property-blog-table epl-clearfix'); ?>>				
	<div id="epl-property-blog-table" class="epl-property-blog-table-wrapper-container">		
		<?php do_action('epl_property_loop_before_content'); ?>			
		<div class="entry-content">			

			<table class="property-box table property-box-right-table property-content">
				<tr>
					<td>
						<!-- Heading -->
						<h3 class="entry-title"><a href="<?php the_permalink() ?>"><?php do_action('epl_property_heading'); ?></a></h3>
					</td>
				</tr>
				<tr>
					<td>
						<!-- stickers -->
						<?php echo epl_get_price_sticker(); ?>
					</td>
				</tr>
				<tr>
					<td>
						<!-- inspection time -->
						<?php do_action('epl_property_inspection_times'); ?>
					</td>
				</tr>
				<tr>
					<td>
						<!-- inspection time -->
						<?php do_action('epl_property_price'); ?>
					</td>
				</tr>

			</table>
		</div>
		<?php do_action('epl_property_loop_after_content'); ?>			
	</div>
</div>
