<?php
/*
 * EPL-Property Function for single Suburb
 */

function epl_suburb_single() { ?>
	<div id="post-<?php the_ID(); ?>" <?php post_class( 'epl-suburb-single' ); ?>>
		<!-- Get Property Custom Values -->
		<?php
			// Store All Meta
			$meta = get_post_custom();
			$suburb_staff_member = $meta['suburb_staff_member'][0];
			$suburb_name = $meta['suburb_name'][0];
			$suburb_video_url = $meta['suburb_video_url'][0];
			$suburb_map_location = $meta['suburb_map_location'][0];
			$suburb_postcode = $meta['suburb_postcode'][0];
			$suburb_local_council = $meta['suburb_local_council'][0];
			$suburb_investors_are = $meta['suburb_investors_are'][0];
			$suburb_agent_service = $meta['suburb_agent_service'][0];
			$suburb_people_property = $meta['suburb_people_property'][0];
			$suburb_people_property_image = $meta['suburb_people_property_image'][0];
			$suburb_location = $meta['suburb_location'][0];
			$suburb_amenities = $meta['suburb_amenities'][0];
			$suburb_recreation = $meta['suburb_recreation'][0];
			$suburb_transport = $meta['suburb_transport'][0];
			$coords = $suburb_map_location;
			$post_type = get_post_type();
		?>	
		
		<!-- title, meta, and date info -->				
		<div class="entry-header clearfix">
			<!-- Featured Image -->
			<?php if ( has_post_thumbnail() ) : ?>
				<div class="entry-image">
					<div class="epl-featured-image epl-cropped">
						<a href="<?php the_permalink(); ?>">
							<?php the_post_thumbnail( 'large', array( 'class' => 'epl-featured-image' ) ); ?>
						</a>
					</div>
				</div>
			<?php endif; ?>
			<!-- END Featured Image -->
			
			<h1 class="entry-title">
				<a href="<?php the_permalink(); ?>">
					<?php the_title(); ?>
				</a>
			</h1>
		</div>
		
		<!-- post content -->
		<div class="entry-content clearfix">
			<?php
				the_content();
				
				//Video
				if($suburb_video_url != '') {
					$videoID = epl_get_youtube_id_from_url($suburb_video_url);
					echo '<div class="videoContainer">' . wp_oembed_get( ('http://www.youtube.com/watch?v=' . $videoID) , array('width'=>600) ) . '</div>';
				}
			?>
			
			<!-- Suburb Tab -->
			<div class="tab-wrapper">
				<!-- Agent -->
				<div class="tab-section">
					<!--<h5 class="tab-title">Property Manager</h5>-->
					<div class="tab-content">
						<?php echo epl_property_advanced_author_box(); ?>
					</div>
				</div>
				<!-- End Agent -->	
			
				<!-- Agent Service  -->
				<?php if ($suburb_agent_service != '') { ?>
					<div class="tab-section">
						<h5 class="tab-title">Agent Service</h5>
						<div class="tab-content">
							<?php echo $suburb_agent_service; ?>
						</div>
					</div>
				<?php } ?>
				<!-- End Location -->	
		
					
				<!-- Fast Facts -->
				<div class="tab-section">
					<h5 class="tab-title">Fast Facts</h5>
					<div class="tab-content">
						<ul>
							<li><?php the_title(); ?>'s Postcode is <?php echo $suburb_postcode; ?></li>
							<?php if ($suburb_local_council != '') { echo '<li>Local Council is ' , $suburb_local_council , '</li>'; } ?>
							<?php if ($suburb_investors_are != '') { echo '<li>Investors are ' , $suburb_investors_are , '</li>'; } ?>
						</ul>
					</div>	
				</div>	
				<!-- End Fast Facts -->

				<!-- People & Property -->
				<?php if ($suburb_people_property != '') { ?>
					<div class="tab-section">
						<h5 class="tab-title">People & Property</h5>
						<div class="tab-content">
							<?php
								if ($suburb_people_property_image != '') {
								echo '<img class="alignright" width="200" height="200" src="' , $suburb_people_property_image[guid] , '" />';
								}
							?>
							<?php echo $suburb_people_property; ?>
						</div>					
					</div>		
				<?php } ?>
				<!-- End People & Property -->
					
				<!-- Location  -->
				<?php if ($suburb_location != '') { ?>
					<div class="tab-section">
						<h5 class="tab-title">Location</h5>
						<div class="tab-content">
							<?php echo $suburb_location; ?>
						</div>
					</div>
				<?php } ?>
				<!-- End Location -->
					
				<!-- Amenities -->
				<?php if ($suburb_amenities != '') { ?>
					<div class="tab-section">
						<h5 class="tab-title">Amenities</h5>
						<div class="tab-content">
							<?php echo $suburb_amenities; ?>
						</div>
					</div>
				<?php } ?>
				<!-- End Amenities -->
					
				<!-- Recreation -->
				<?php if ($suburb_recreation != '') { ?>
					<div class="tab-section">
						<h5 class="tab-title">Recreation</h5>
						<div class="tab-content">
							<?php echo $suburb_recreation; ?>
						</div>
					</div>
				<?php } ?>
				<!-- End Recreation-->
				
				<!-- Transport -->
				<?php if ($suburb_transport != '') { ?>
					<div class="tab-section">
						<h5 class="tab-title">Transport</h5>
						<div class="tab-content">
							<?php echo $suburb_transport; ?>
						</div>
					</div>
				<?php } ?>				
				<!-- End Transport-->
				
				<!-- Community Features -->
					<!--
					<div class="tab-section">
							<h5 class="tab-title">Suburb Features</h5>
						<div class="tab-content">
							<?php //echo get_the_term_list($post->ID, 'community_feature', '<li>', '</li><li>', '</li>' ); ?>
						</div>
					</div>
					-->
				<!-- End Community Features -->

				<!-- Gallery -->
				<?php // check if the post has a Post Thumbnail assigned to it.
					$attachments = get_children( array('post_parent' => get_the_ID(), 'post_type' => 'attachment', 'post_mime_type' => 'image') );
					if ( $attachments ) { ?>
						<div class="tab-section">
							<h5 class="tab-title">Gallery</h5>
							<div class="tab-content">
								<?php echo do_shortcode('[gallery columns="4" link="file"]'); ?>
							</div>
						</div>
					<?php } ?>
				<!-- End Gallery -->

				<!-- Suburb Map -->
				<?php if ($coords != '') { ?>
					<div class="tab-section">
						<h5 class="tab-title">Map</h5>
						<div class="tab-content">
							<div class="suburb-map">
								<?php echo epl_property_map_local_tabbed($coords , $post_type); ?>
							</div>
						</div>
					</div>
				<?php } ?>
				<!-- End Suburb Map -->					
					
				<!-- Sales Graph -->
				<?php 
				// Enable or disable Graph System
				$epl_graph_option = get_option('epl_settings_epl_graph_on_off');
				if ( $epl_graph_option == 1) { ?>
					<div class="tab-section">
						<h5 class="tab-title">Real Estate Activity</h5>
						<div class="tab-content">
							<?php if(function_exists('epl_property_graph')) { epl_property_graph(); }?>
						</div>
					</div>
				<?php } ?>
				<!-- End Sales Graph-->
			</div>
		</div>
		<!-- categories, tags and comments -->
		<div class="entry-footer clearfix">
			<div class="entry-meta">
				<?php wp_link_pages( array( 'before' => '<div class="entry-utility entry-pages">' . __( 'Pages:', 'it-l10n-Builder' ) . '', 'after' => '</div>', 'next_or_number' => 'number' ) ); ?>		
			</div>
		</div>
	</div>
	<!-- end .post -->
	<?php
}
