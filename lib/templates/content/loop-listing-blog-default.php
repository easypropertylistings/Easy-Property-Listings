<?php
/*
 * Loop Property Template: Default
 *
 * @package easy-property-listings
 * @subpackage Theme
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
?>

<div id="post-<?php the_ID(); ?>" <?php post_class('epl-property-blog epl-clearfix'); ?>>				
	<div class="entry-content">
		<?php if ( has_post_thumbnail() ) : ?>
			<div class="property-box property-box-left property-featured-image-wrapper">
				<a href="<?php the_permalink(); ?>">
					<div class="epl-blog-image">
						<?php
							echo $price_sticker;
							the_post_thumbnail( 'epl-image-medium-crop', array( 'class' => 'teaser-left-thumb' ) );
						?>
					</div>
				</a>
				
				<!-- Home Open -->
				<?php
					if( $property_inspection_times != '') {
						echo  '<div class="home-open"><strong>', $property_inspection_times, '</strong></div>';
					}
				?>
			</div>
		<?php endif; ?>

		<div class="property-box property-box-right property-content">
			<!-- Heading -->
			<h3 class="entry-title"><a href="<?php the_permalink() ?>"><?php echo $the_property_heading; ?></a></h3>
			<div class="entry-content">
				<?php echo the_excerpt(); ?>
			</div>
			
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
	
			<!-- Property Featured Icons -->
			<div class="property-feature-icons">
				<?php echo $property_icons_full; ?>					
			</div>
			<!-- Price -->
			<div class="address price">
				<?php echo $price; ?>
			</div>
		</div>	
	</div>
</div>
