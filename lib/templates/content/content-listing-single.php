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
			
				<?php do_action('epl_property_before_title'); ?>
				<h1 class="entry-title">
					<?php do_action('epl_property_title'); ?>
				</h1>
				<?php do_action('epl_property_after_title'); ?>
				
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
				<div class="tab-content">
					<div class="tab-content property-details">
						<h3 class="tab-address">
							<?php do_action('epl_property_address'); ?>
						</h3>
						<?php do_action('epl_property_land_category'); ?>
						<?php do_action('epl_property_price_content'); ?>
						<?php do_action('epl_property_commercial_category'); ?>
					</div>
					<div class="property-meta">
						<?php do_action('epl_property_available_dates');// meant for rent only ?>								
						<?php do_action('epl_property_inspection_times'); ?>
					</div>
				</div>
			</div>

			<div class="epl-tab-section">
				<div class="tab-content">
					<!-- heading -->
					<h2 class="entry-title"><?php do_action('epl_property_heading'); ?></h2>
			
					<h3 class="secondary-heading"><?php do_action('epl_property_secondary_heading'); ?></h3>
					<?php
						do_action('epl_property_content_before');
						
						do_action('epl_property_the_content');
						
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
			
			<?php do_action( 'epl_single_author' ); ?>
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
