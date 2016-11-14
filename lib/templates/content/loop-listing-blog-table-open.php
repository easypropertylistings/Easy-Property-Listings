<?php
/**
 * Loop Property Template: Table Open
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

<div id="post-<?php the_ID(); ?>" <?php post_class('epl-listing-post epl-property-blog epl-property-table epl-table epl-table-open epl-clearfix'); ?>>
	<?php do_action('epl_property_before_content'); ?>
		<div class="epl-table-column-image property-featured-image-wrapper">
			<a href="<?php the_permalink(); ?>">
				<div class="epl-blog-image">
					<?php the_post_thumbnail( 'epl-image-medium-crop', array( 'class' => 'teaser-left-thumb' ) ); ?>
				</div>
			</a>
		</div>

		<div class="epl-table-column-content property-box property-box-right property-content">
			<!-- Address -->
			<div class="epl-table-box epl-table-column epl-table-column-left">
				<div class="epl-table-address property-address">
					<a href="<?php the_permalink(); ?>"><?php do_action('epl_property_address'); ?></a>
				</div>
				<div class="property-feature-icons">
					<?php do_action('epl_property_icons'); ?>
				</div>
			</div>
			<!-- Property Featured Icons -->
			<div class="epl-table-box epl-table-column epl-table-column-middle price">
				<?php do_action('epl_property_price'); ?>
			</div>
			<!-- Price -->
			<div class="epl-table-box epl-table-column epl-table-column-right">
				<?php do_action('epl_property_inspection_times'); ?>
			</div>
		</div>
	<?php do_action('epl_property_after_content'); ?>
</div>
