<?php
/*
 * Single Property Template: Expanded
 *
 * @package easy-property-listings
 * @subpackage Theme
 */
?>

<div id="post-<?php the_ID(); ?>" <?php post_class( 'epl-listing-single epl-property-single view-expanded' ); ?>>
	<div class="entry-header epl-header epl-clearfix">
		<div class="title-meta-wrapper">
			<div class="entry-col property-details">
			
				<?php do_action('epl_single_before_title'); ?>
				<h1 class="entry-title">
					<?php do_action('epl_single_the_title'); ?>
				</h1>
				<?php do_action('epl_single_after_title'); ?>
				
			</div>
	
			<div class="entry-col property-pricing-details">
			
				<?php do_action('epl_property_price_before'); ?>
				<div class="property-meta pricing">
					<?php do_action('epl_property_price'); ?>
				</div>
				<?php do_action('epl_property_price_after'); ?>
				<div class="property-feature-icons epl-clearfix">
					<?php do_action('epl_property_icons'); ?>				
				</div>
				
			</div>
		</div>
	</div>

	<div class="entry-content epl-content epl-clearfix">
	
		<?php do_action( 'epl_property_featured_image' ); ?>
		
		<?php do_action( 'epl_buttons_single_property' ); ?>

		<div class="tab-wrapper">
			<div class="epl-tab-section">
				<h5 class="tab-title"><?php echo apply_filters('property_tab_title',__('Property Details', 'epl')); ?></h5>
				<div class="tab-content">
					<div class="tab-content property-details">
						<h3 class="tab-address">
							<?php do_action('epl_property_tab_address'); ?>
						</h3>
						<?php do_action('epl_property_land_category'); ?>
						<?php do_action('epl_property_price_content'); ?>
						<?php do_action('epl_property_commercial_category'); ?>
					</div>
					
						<div class="property-meta">
							<?php
								do_action('epl_property_available_dates');		// meant for rent only
								
								do_action('epl_property_inspection_times');	
								
							?>
						</div>
					
					
				</div>
			</div>

			<div class="epl-tab-section">
				<h5 class="tab-title"><?php _e('Description', 'epl'); ?></h5>
				<div class="tab-content">
					<!-- heading -->
					<h2 class="entry-title"><?php do_action('epl_property_heading'); ?></h2>
			
					<?php
						echo '<h3>';
							do_action('epl_property_secondary_heading');
						echo '</h3>';
						do_action('epl_property_content_before');
						the_content();
						do_action('epl_property_content_after');
						
					?>
				</div>
			</div>

			<?php do_action('epl_property_tab_section_before'); ?>
			<div class="epl-tab-section">
					<?php do_action('epl_property_tab_section'); ?>
			</div>
			<?php do_action('epl_property_tab_section_after'); ?>
			
			<?php do_action( 'epl_property_gallery' ); ?>
			
			<?php do_action( 'epl_property_map' ); ?>
			
			<?php do_action( 'epl_single_extensions' ); ?>
			
			
			<!-- Agent -->
			<?php
			if ( get_post_type() != 'rental' ) { ?>
				<div class="epl-tab-section">
					<h5 class="tab-title"><?php _e('Real Estate Agent', 'epl'); ?></h5>
					<div class="tab-content">
						<?php do_action( 'epl_single_author' ); ?>
					</div>
				</div>
			<?php } else { ?>
				<div class="epl-tab-section">
					<h5 class="tab-title"><?php _e('Property Manager', 'epl'); ?></h5>
					<div class="tab-content">
						<?php do_action( 'epl_single_author' ); ?>
					</div>
				</div>				
			<?php } ?>
		</div>
	</div>
	<!-- categories, tags and comments -->
	<div class="entry-footer epl-clearfix">
		<div class="entry-meta">
			<?php wp_link_pages( array( 'before' => '<div class="entry-utility entry-pages">' . __( 'Pages:', 'epl' ) . '', 'after' => '</div>', 'next_or_number' => 'number' ) ); ?>		
		</div>
	</div>
</div>
<!-- end property -->
