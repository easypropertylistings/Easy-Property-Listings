<?php
/*
 * Widget Author Template
 *
 * @package easy-property-listings
 * @subpackage Theme
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
?>

<div id="post-<?php the_ID(); ?>" class="epl-widget property-widget-image hentry" <?php //post_class('property-widget-image'); ?>>
	<div class="entry-header">
		<?php if ( has_post_thumbnail() ) : ?>
			<div class="epl-img-widget">
				<a href="<?php the_permalink(); ?>">
					<?php the_post_thumbnail( $image ); ?>
				</a>
			</div>
		<?php endif; ?>		
	</div>
	
	<div class="entry-content">
		<?php
			// Heading Options
			if ($title == 'on') { ?>
				<h5 class="property-meta heading"><?php echo $the_property_heading; ?></h5>
			<?php }
		?>
		
		<!-- Address -->
		<div class="property-address">
			<?php 
				epl_widget_listing_address($d_suburb,$d_street);
			 ?>
		</div>
		<!-- END Address -->
		
		<?php
			// Icon Options
			if ( $icons == 'all' ) { ?>
				<div class="property-meta property-feature-icons"><?php epl_property_icons(); ?></div>
			<?php } elseif ($icons == 'bb') { ?>
				<div class="property-meta property-feature-icons"><?php echo epl_get_property_bb_icons(); ?></div>
			<?php } ?>

		<div class="property-meta price"><?php epl_property_price() ?></div>
		<form class="epl-property-button" action="<?php the_permalink(); ?>" method="post">
			<input type=submit value="<?php _e('Read More', 'epl'); ?>" />
		</form>
	</div>
</div>
