<?php
/**
 * Widget Property Template: Default
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

<div id="post-<?php the_ID(); ?>" class="epl-widget epl-listing-widget property-widget-image <?php do_action('epl_property_widget_status_class'); ?>">
	<div class="entry-header">
		<?php do_action('epl_property_widgets_featured_image' , $image); ?>
	</div>

	<div class="entry-content">
		<?php // Heading Options
		if ($title == 'on') { ?>
			<h5 class="property-heading"><a href="<?php the_permalink(); ?>"><?php do_action('epl_property_heading'); ?></a></h5>
		<?php } ?>

		<?php if ( $d_excerpt == 'on' ) { ?>
			<p class="property-heading">
				<?php
				if( function_exists('epl_the_excerpt') ) {
	        			epl_the_excerpt();
	    			} else {
					the_excerpt();
	    			} ?>
			</p>
		<?php } ?>

		<!-- Address -->
		<div class="property-address">
			<?php epl_widget_listing_address($d_suburb,$d_street); ?>
		</div>

		<?php // Icon Options
		if ( $icons == 'all' ) { ?>
			<div class="property-meta property-feature-icons"><?php epl_property_icons(); ?></div>
		<?php } elseif ($icons == 'bb') { ?>
			<div class="property-meta property-feature-icons"><?php echo epl_get_property_bb_icons(); ?></div>
		<?php } ?>

		<?php // Price
		if ( $d_price == 'on') { ?>
			<div class="property-meta price"><?php epl_property_price() ?></div>
		<?php } ?>

		<?php // Read More
		if ( $d_more == 'on') { ?>
			<form class="epl-property-button" action="<?php the_permalink(); ?>" method="post">
				<input type=submit value="<?php echo $more_text; ?>" />
			</form>
		<?php } ?>
	</div>
</div>
