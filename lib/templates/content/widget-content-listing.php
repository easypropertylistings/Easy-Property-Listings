<?php
/*
 * Widget Property Template: Default
 *
 * @package easy-property-listings
 * @subpackage Theme
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
?>

<div id="post-<?php the_ID(); ?>" class="epl-listing-widget property-widget-image">
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
		<?php // Heading Options
		if ($title == 'on') { ?>
			<h5 class="property-heading"><a href="<?php the_permalink(); ?>"><?php echo $the_property_heading; ?></a></h5>
		<?php } ?>
		<?php if ( $d_excerpt == 'on' ) { 
			the_excerpt(); 
		} ?>
		
		<!-- Address -->
		<div class="property-address">
				<?php
				// Commercial and Business Address
				if ($property_post_type == 'commercial' || $property_post_type == 'business' ){
					if ( $property_address_display == 'no' && $property_com_display_suburb == 'no') { ?>
						<div class="property-meta suburb-name">
							<span class="item-state"><?php echo $property_address_state . ' '; ?></span>
							<span class="item-pcode"><?php echo $property_address_postal_code; ?></span>
						</div>
					<?php } elseif ( $property_address_display == 'no' && $property_com_display_suburb == 'yes') { ?>
							<div class="property-meta suburb-name"><?php echo $property_address_suburb; ?></div>
					<?php } elseif ( $property_address_display == 'yes' && $property_com_display_suburb == 'no') { ?>
						<div class="property-meta suburb-name">
							<span class="item-state"><?php echo $property_address_state . ' '; ?></span>
							<span class="item-pcode"><?php echo $property_address_postal_code; ?></span>
						</div>
					<?php }
				} else {
					// Address Display not Commercial or Business type
					if ( $property_address_display == 'yes' ) { ?>
						<?php // Suburb
						if ( $d_suburb == 'on' ) { ?>
							<div class="property-meta suburb-name"><?php echo $property_address_suburb; ?></div>
						<?php } ?>
						
						<?php // Street
						if ( $d_street == 'on' ) { ?>
							<div class="property-meta street-name"><?php echo $property_address_street; ?></div>
						<?php } ?>
					<?php } else { ?>
						<?php // Suburb
						if ( $d_suburb == 'on' ) { ?>
							<div class="property-meta suburb-name"><?php echo $property_address_suburb; ?></div>
						<?php } ?>
					<?php } 
				} ?>
		</div>

		<?php // Icon Options
		if ( $icons == 'all' ) { ?>
			<div class="property-meta property-feature-icons"><?php echo $property_icons_full; ?></div>
		<?php } elseif ($icons == 'bb') { ?>
			<div class="property-meta property-feature-icons"><?php echo $property_icons_bb; ?></div>
		<?php } ?>
		
		<?php // Price
		if ( $d_price == 'on') { ?>
			<div class="property-meta price"><?php echo $price; ?></div>
		<?php } ?>
		
		<?php // Read More
		if ( $d_more == 'on') { ?>
			<form class="epl-property-button" action="<?php the_permalink(); ?>" method="post">
				<input type=submit value="<?php echo $more_text; ?>" />
			</form>
		<?php } ?>
	</div>
</div>
