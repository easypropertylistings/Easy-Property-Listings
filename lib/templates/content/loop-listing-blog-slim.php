<?php
/*
 * Loop Property Template: Slim home open list
 *
 * @package easy-property-listings
 * @subpackage Theme
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
?>

<div id="post-<?php the_ID(); ?>" <?php post_class('epl-listing-post epl-property-blog-slim epl-clearfix'); ?>>				
	<div id="epl-property-blog-slim" class="epl-property-blog-slim-wrapper-container">		

		<div class="entry-content">			
			<?php if ( has_post_thumbnail() ) : ?>
				<div class="property-box slim property-box-left-slim property-featured-image-wrapper">
					<a href="<?php the_permalink(); ?>">
						<?php the_post_thumbnail( 'thumbnail', array( 'class' => 'teaser-left-thumb' ) ); ?>
					</a>
				</div>
			<?php endif; ?>
	
			<div class="property-box slim property-box-right-slim property-content">
				<!-- Heading -->
				<h3 class="entry-title"><a href="<?php the_permalink() ?>"><?php echo $the_property_heading; ?></a></h3>
				
				<?php //the_excerpt(); ?>
			
			<!-- Address -->
			<div class="property-address">
				<a href="<?php the_permalink(); ?>">
					<?php
					// Commercial and Business Address
					if ($property_post_type == 'commercial' || $property_post_type == 'business' ) {
						if ( $property_address_display == 'no' && $property_com_display_suburb == 'no') { ?>
							<span class="street-address">
								<span class="item-state"><?php echo $property_address_state . ' '; ?></span>
								<span class="item-pcode"><?php echo $property_address_postal_code; ?></span>
							</span>
						<?php } elseif ( $property_address_display == 'no' && $property_com_display_suburb == 'yes') { ?>
							<span class="street-address">
								<span class="item-suburb"><?php echo $property_address_suburb . ', '; ?></span>
								<span class="item-state"><?php echo $property_address_state . ' '; ?></span>
								<span class="item-pcode"><?php echo $property_address_postal_code; ?></span>
							</span>
						<?php } elseif ( $property_address_display == 'yes' && $property_com_display_suburb == 'no') { ?>
							<span class="street-address">
								<span class="item-state"><?php echo $property_address_state . ' '; ?></span>
								<span class="item-pcode"><?php echo $property_address_postal_code; ?></span>
							</span>
						<?php } else { ?>
							<span class="street-address"><?php echo $property_address_street; ?></span>
							<span class="entry-title-sub">
								<span class="item-suburb"><?php echo $property_address_suburb . ', '; ?></span>
								<span class="item-state"><?php echo $property_address_state . ' '; ?></span>
								<span class="item-pcode"><?php echo $property_address_postal_code; ?></span>
							</span>
						<?php
						}
					} else {
						// Address Display not Commercial or Business type
						if ( $property_address_display == 'yes' ) { ?>
							<span class="street-address"><?php echo $property_address_street; ?></span>
							<span class="entry-title-sub">
								<span class="item-suburb"><?php echo $property_address_suburb . ', '; ?></span>
								<span class="item-state"><?php echo $property_address_state . ' '; ?></span>
								<span class="item-pcode"><?php echo $property_address_postal_code; ?></span>
							</span>
						<?php } else { ?>
							<span class="item-suburb"><?php echo $property_address_suburb; ?></span>
							<span class="item-state"><?php echo $property_address_state . ' '; ?></span>
							<span class="item-pcode"><?php echo $property_address_postal_code; ?></span>
						<?php } 
					} ?>
				</a>
			</div>
			
				<!-- Home Open -->
				<?php 
					if($property_inspection_times != '') {
						echo  '<div class="home-open"><strong>'.__('Open', 'epl').' ', $property_inspection_times, '</strong></div>';
					}
				?>
				
				<!-- Property Featured Icons -->
				<div class="property-feature-icons">
					<?php echo $property_icons_full; ?>					
				</div>
				
				<div class="address">
					<?php echo $price; ?>
				</div>
			</div>
		</div>
	</div>
</div>
