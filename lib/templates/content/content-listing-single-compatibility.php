<?php
/*
 * Single Property Template: Expanded
 *
 * @package easy-property-listings
 * @subpackage Theme
 */
?>

<div id="post-<?php the_ID(); ?>" <?php post_class( 'epl-listing-single epl-property-single view-expanded epl-property-single-theme-compatibility' ); ?>>
	<div class="entry-header epl-header epl-clearfix">
	
		<?php do_action( 'epl_property_featured_image' ); ?>
		
		<?php do_action( 'epl_buttons_single_property' ); ?>
		
		<div class="epl-tab-section">
			<div class="tab-content">

				<?php do_action('epl_property_price_before'); ?>
				<div class="property-meta pricing-compatibility">
					<?php do_action('epl_property_price'); ?>
				</div>
				<?php do_action('epl_property_price_after'); ?>
				<div class="property-feature-icons epl-clearfix">
					<?php do_action('epl_property_icons'); ?>				
				</div>
				<?php do_action('epl_property_land_category'); ?>
				<?php do_action('epl_property_commercial_category'); ?>
				<?php do_action('epl_property_available_dates');// meant for rent only ?>								
				<?php do_action('epl_property_inspection_times'); ?>
			</div>
		</div>
	</div>

	<div class="entry-content epl-content epl-clearfix">

		<div class="tab-wrapper">

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
			
			<?php do_action( 'epl_single_before_author_box' ); ?>
			<?php do_action( 'epl_single_author' ); ?>
			<?php do_action( 'epl_single_after_author_box' ); ?>
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
