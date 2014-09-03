<?php
/*
 * Single Property Template: Expanded
 *
 * @package easy-property-listings
 * @subpackage Theme
 */
?>

<div id="post-<?php the_ID(); ?>" <?php post_class( 'epl-property-single view-expanded' ); ?>>
	<div class="entry-header epl-header epl-clearfix">
		<div class="title-meta-wrapper">
			<div class="entry-col property-details">
				<h1 class="entry-title">
				<?php
					// Commercial and Business Address
					if ($property_post_type == 'commercial' || $property_post_type == 'business' ) {
						if ( $property_address_display == 'no' && $property_com_display_suburb == 'no') { ?>
							<span class="item-street">
								<span class="item-state"><?php echo $property_address_state . ' '; ?></span>
								<span class="item-pcode"><?php echo $property_address_postal_code; ?></span>
							</span>
						<?php } elseif ( $property_address_display == 'no' && $property_com_display_suburb == 'yes') { ?>
							<span class="item-street">
								<span class="item-suburb"><?php echo $property_address_suburb . ', '; ?></span>
								<span class="item-state"><?php echo $property_address_state . ' '; ?></span>
								<span class="item-pcode"><?php echo $property_address_postal_code; ?></span>
							</span>
						<?php } elseif ( $property_address_display == 'yes' && $property_com_display_suburb == 'no') { ?>
							<span class="item-street">
								<span class="item-state"><?php echo $property_address_state . ' '; ?></span>
								<span class="item-pcode"><?php echo $property_address_postal_code; ?></span>
							</span>
						<?php } else { ?>
							<span class="item-street"><?php echo $property_address_street; ?></span>
							<span class="entry-title-sub">
								<span class="item-suburb"><?php echo $property_address_suburb . ', '; ?></span>
								<span class="item-state"><?php echo $property_address_state . ' '; ?></span>
								<span class="item-pcode"><?php echo $property_address_postal_code; ?></span>
							</span>
						<?php }
					} else {
						// Address Display not Commercial or Business type
						if ( $property_address_display == 'yes' ) { ?>
							<span class="item-street"><?php echo $property_address_street; ?></span>
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
				</h1>
			</div>
	
			<div class="entry-col property-pricing-details">
				<div class="property-meta pricing">
					<?php echo $price; ?>
				</div>
				<div class="property-feature-icons epl-clearfix">
					<?php echo $property_icons_full; ?>					
				</div>
			</div>
		</div>
	</div>

	<div class="entry-content epl-content epl-clearfix">
	
		<?php do_action( 'epl_featured_image_single_property' ); ?>
		
		<?php do_action( 'epl_buttons_single_property' ); ?>

		<div class="tab-wrapper">
			<div class="epl-tab-section">
				<h5 class="tab-title"><?php _e('Property Details', 'epl'); ?></h5>
				<div class="tab-content">
					<div class="tab-content property-details">
						<h3 class="tab-address">
							<?php if ( $property_address_display == 1 || $property_address_display == 'yes' ) { ?>
								<span class="item-street"><?php echo $property_address_street; ?></span>
								<span class="entry-title-sub">
									<span class="item-suburb"><?php echo $property_address_suburb . ', '; ?></span>
									<span class="item-state"><?php echo $property_address_state . ' '; ?></span>
									<span class="item-pcode"><?php echo $property_address_postal_code; ?></span>
								</span>
							<?php } else { ?>
								<span class="item-suburb"><?php echo $property_address_suburb; ?></span>
								<span class="item-state"><?php echo $property_address_state . ' '; ?></span>
								<span class="item-pcode"><?php echo $property_address_postal_code; ?></span>
							<?php } ?>
						</h3>

						<?php
							if( 'land' == $post_type || 'commercial_land' == $post_type ) {
								echo $property_land_category; //Land Category
							}

							echo $price; //Price / Rent
							
							//Commercial Options
							if ( $property_post_type == 'commercial' ) {
								if ( $property_com_plus_outgoings == 1) {
									echo '<div class="price-type">'.__('Plus Outgoings', 'epl').'</div>';
								}
								echo $the_property_commercial_category;
							}
						?>	
					</div>
					
						<div class="property-meta">
							<?php				
								if( 'rental' == $post_type && $property_date_available != '' && $property_status != 'leased' ) { 
									// Rental Specifics
									echo '<div class="property-meta date-available">'.__('Available from', 'epl').' ', $property_date_available, '</div>';
								}
								
								if($property_inspection_times != '') {
									$label_home_open = '';
									if(!empty($epl_settings) && isset($epl_settings['label_home_open'])) {
										$label_home_open	= $epl_settings['label_home_open'];
									} ?>
									<div class="home-open"><strong><?php echo $label_home_open; ?>: <?php echo $property_inspection_times; ?></strong></div>
								<?php }
							?>
						</div>
					
					
				</div>
			</div>

			<div class="epl-tab-section">
				<h5 class="tab-title"><?php _e('Description', 'epl'); ?></h5>
				<div class="tab-content">
					<!-- heading -->
					<h2 class="entry-title"><?php echo $the_property_heading; ?></h2>
			
					<?php
						echo '<h3>';
							if($property_category != '') {
								echo ' <span class="type">' . $property_category . ' </span>';
							}
					
							if ( 'sold' == $property_status ) {
								echo '<span class="sold-status">'.__('Sold', 'epl').'</span>';
							} 
						
							echo ' <span class="suburb"> - ' . $property_address_suburb . ' </span>';
							echo ' <span class="state">' . $property_address_state . '</span>';	
						echo '</h3>';
					
						the_content( __( 'Read More &rarr;', 'epl' ) );
					
						//Video
						if($property_video_url != '') {
							$videoID = epl_get_youtube_id_from_url($property_video_url);
							echo '<div class="videoContainer">';
								// Echo the embed code via oEmbed
								echo wp_oembed_get( ('http://www.youtube.com/watch?v=' . $videoID) , array('width'=>600)  ); 
							echo '</div>';
						}
					?>
				</div>
			</div>

			<div class="epl-tab-section">
				<?php if ( $post_type != 'land' || $post_type != 'business') { ?>
				<h5 class="tab-title"><?php _e('Property Features', 'epl'); ?></h5>
					<div class="tab-content">
						<?php
							$d_feature_col = '';
							if(!empty($epl_settings) && isset($epl_settings['display_feature_columns'])) {
								$d_feature_col	= $epl_settings['display_feature_columns'];
							}
						?>
						<ul class="listing-info epl-tab-<?php echo $d_feature_col; ?>-columns">
							<?php echo $the_property_feature_list; ?>							
						</ul>
					</div>
				<?php } ?>
			
				<div class="tab-content">
					<?php
						//Land Category
						if( 'land' == $post_type || 'commercial_land' == $post_type ) {
							echo $property_land_category; //Land Category
						}
						
						//Availability
						if( 'rental' == $post_type && $property_date_available != '' && $property_status != 'leased' ) {
							echo '<div class="date-available">'.__('Available from', 'epl').' ', $property_date_available, '</div>';
						}

						//Commercial Options
						if ( $property_post_type == 'commercial' ) {
							if ( $property_com_plus_outgoings == 1) {
								echo '<div class="price-type">'.__('Plus Outgoings', 'epl').'</div>';
							}
							echo $the_property_commercial_category;						
						}
					?>
				</div>			
			</div>
			
			<?php if ( $post_type == 'rural') { ?>
				<div class="epl-tab-section">
					<h5 class="tab-title"><?php _e('Rural Features', 'epl'); ?></h5>
					<div class="tab-content">
						<div class="listing-info">
							<?php echo $the_property_rural_feature_list; ?>							
						</div>
					</div>
				</div>
			<?php }
			
			// Gallery check if the post has a Post Thumbnail assigned to it.
			$attachments = get_children( array('post_parent' => get_the_ID(), 'post_type' => 'attachment', 'post_mime_type' => 'image') );
			$title = get_the_title();
			if ( $attachments && $d_gallery == 1 ) { ?>
				<div class="property-gallery">
					<!-- Gallery -->
					<div class="entry-gallery epl-clearfix">
						<?php 
							$d_gallery_n = '[gallery columns="'. $d_gallery_n . '" link="file"]';
							echo do_shortcode( $d_gallery_n );
						?>					
					</div>
				</div>
				<?php
			} ?>
			
			
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
