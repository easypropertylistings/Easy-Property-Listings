<?php
/*
 * Property Function for single property
 */ 

// Selecting Card Display Style
function epl_property_single() {
	include('epl-settings.php');
	
	$d_option = $epl_display_single_property;
	$d_gallery = $epl_display_single_gallery;
	$d_gallery_n = $epl_gallery_n;
	$d_map_position = $epl_display_single_map_position;
	//Place in property-meta
	// Dynamic Description Option 
	
	if ( $d_option == 1 ) {
		echo epl_property_single_condensed($d_gallery , $d_gallery_n, $d_map_position);
	} else {
		echo epl_property_single_expanded($d_gallery , $d_gallery_n , $d_map_position);
	}
}

function epl_property_single_expanded($d_gallery , $d_gallery_n, $d_map_position) {
	// Get Property Custom Values
	include( 'property-meta.php' ); ?>

	<div id="post-<?php the_ID(); ?>" <?php post_class( 'epl-property-single view-expanded' ); ?>>
		<!-- title, meta, and date info -->
		<div class="entry-header epl-clearfix">
			<div class="title-meta-wrapper">
				<div class="entry-col property-details">
					<h1 class="entry-title">
					<?php
						// Commercial and Business Address
						if ($property_post_type == 'epl_commercial' || $property_post_type == 'epl_business' ){

							if ( $property_address_display == 'no' && $property_com_display_suburb == 'no') { ?>
								<span class="item-street">
									<span class="item-state"><?php echo $property_address_state . ' '; ?></span>
									<span class="item-pcode"><?php echo $property_address_postal_code; ?></span>
								</span>
							<?php 
							} 
							elseif ( $property_address_display == 'no' && $property_com_display_suburb == 'yes') { ?>
								<span class="item-street">
									<span class="item-suburb"><?php echo $property_address_suburb . ', '; ?></span>
									<span class="item-state"><?php echo $property_address_state . ' '; ?></span>
									<span class="item-pcode"><?php echo $property_address_postal_code; ?></span>
								</span>
							<?php 
							}
							elseif ( $property_address_display == 'yes' && $property_com_display_suburb == 'no') { ?>
								<span class="item-street">
									<span class="item-state"><?php echo $property_address_state . ' '; ?></span>
									<span class="item-pcode"><?php echo $property_address_postal_code; ?></span>
								</span>
							<?php 
							}
							else { ?>
								<span class="item-street"><?php echo $property_address_street; ?></span>
								<span class="entry-title-sub">
									<span class="item-suburb"><?php echo $property_address_suburb . ', '; ?></span>
									<span class="item-state"><?php echo $property_address_state . ' '; ?></span>
									<span class="item-pcode"><?php echo $property_address_postal_code; ?></span>
								</span>
							<?php
							}
						} 
						else {
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
					<!-- Property Price and Availability-->
					<div class="entry-meta pricing">
						<!-- Price / Rent -->
						<?php echo $price; ?>
					</div>
					<div class="property-feature-icons epl-clearfix">
						<?php echo $property_icons_full; ?>					
					</div>
				</div>
			</div>
			<div class="entry-meta">
				<!-- Property Details -->
				<?php
					if ( $epl_dynamic_description == 1) { ?>
						<div class="entry-meta description">
							<?php echo $property_dynamic_description; ?>
						</div>
					<?php }
					
					if( $property_date_available != '' && $property_status != 'leased' ) { 
						// Rental Specifics
						echo '<div class="entry-meta date-available">Available from ', $property_date_available, '</div>'; 
					}
					
					if($property_inspection_times != '') { ?>
						<!-- Home Open Details -->
						<div class="home-open"><strong>Home Open: <?php echo $property_inspection_times; ?></strong></div>
					<?php }
				?>
			</div>
		</div>

		<!-- post content -->
		<div class="entry-content epl-clearfix">
			<?php if ( $d_map_position && $coords != ',' ) { ?>
				<!-- Map Plugin -->
				<div class="tab-section">
					<div class="tab-content">
						<?php
							if(function_exists('epl_property_map_local_tabbed')) { 
								epl_property_map_local_tabbed($coords,$property_post_type); 
							}
						?>
					</div>
				</div>	
				<!-- End Map Plugin -->
			<?php } else { ?>
				<!-- Featured Image -->
				<?php if ( has_post_thumbnail() ) { ?>
					<div class="entry-image">
						<div class="it-featured-image">
							<a href="<?php the_permalink(); ?>">
								<?php the_post_thumbnail( 'index_thumbnail', array( 'class' => 'index-thumbnail' ) ); ?>
							</a>
						</div>
					</div>
				<?php }  ?>
				<!-- END Featured Image -->
			<?php } ?>
	
			<!-- Tab Start -->
			<div class="tab-wrapper">
				<!-- Tab -1 Start - Details -->
				<div class="tab-section">
					<h5 class="tab-title">Property Details</h5>
					<div class="tab-content">
						<div class="tab-content-col property-details">
							<!-- Address -->
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
								echo $land_category; //Land Category
								
								if( $property_date_available != '' && $property_status != 'leased' ) {
									echo '<div class="date-available">Available from ', $property_date_available, '</div>'; //Availability
								}
								
								echo $price; //Price / Rent
								
								//Commercial Options
								if ( $property_post_type == 'epl_commercial' ) {
									if ( $property_com_plus_outgoings == 1) {
										echo '<div class="price-type">Plus Outgoings</div>';
									}
									echo $the_property_commercial_category;
								}
							?>	
						</div>
									
						<div class="tab-content-col inspection-buttons">
							<?php
								//Home Open Details
								if($property_inspection_times != '') { ?>
									<div class="home-open">
										<div class="home-open-title">Home Open</div>
										<div class="home-open-time"><?php echo $property_inspection_times; ?></div>
									</div>
								<?php }
								
								//Inspection Buttons
								$button_1form_option = $epl_1form;
								$button_book_inspection_option = $epl_book_inspection;
								if ( 'epl_rental' == get_post_type() && 'leased' != $property_status && $button_1form_option == 1) {
									echo epl_button_1form();
								}
								if ( 'current' == $property_status && $property_under_offer != 1 && $button_book_inspection_option == 1) {
									echo epl_button_book_inspection();
								}
							?>
						</div>
					</div>
				</div>
				<!-- Tab -1 End -->
			
				<!-- Tab 0 Start - Details -->
				<div class="tab-section">
					<h5 class="tab-title">Description</h5>
					<div class="tab-content">
						<!-- heading -->
						<h2 class="entry-title"><?php echo $the_property_heading; ?></h2>
				
						<?php
							echo '<h3>';
								if($property_house_type != '') {
									echo ' <span class="type">' . $property_house_type . ', </span>';
								}
						
								if ( 'sold' == $property_status ) {
									echo '<span class="sold-status">Sold</span>';
								} else { 
									echo $status_linked; 
								}
							
								echo ' <span class="suburb"> - ' . $property_address_suburb . ' </span>';
								echo ' <span class="state">' . $property_address_state . '</span>';	
							echo '</h3>';
						
							the_content( __( 'Read More &rarr;', 'it-l10n-BuilderChild-Air' ) );
						
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
				<!-- Tab 0 End -->	
			
				<!-- Tab Property Features -->
				<div class="tab-section">
					<?php if ( $post_type != 'epl_land' || $post_type != 'epl_business') { ?>
					<h5 class="tab-title">Property Features</h5>
						<div class="tab-content">
							<ul class="listing-info tab-4-columns">
								<?php echo $the_property_feature_list; ?>							
							</ul>
							<?php echo $l_car_comments; ?> 
						</div>
					<?php } ?>
				
					<div class="tab-content">
						<?php
							//Land Category
							echo $land_category;
							
							//Availability
							if( $property_date_available != '' && $property_status != 'leased' ) {
								echo '<div class="date-available">Available from ', $property_date_available, '</div>';
							}
							
							//Commercial Options
							if ( $property_post_type == 'epl_commercial' ) {
								if ( $property_com_plus_outgoings == 1) {
									echo '<div class="price-type">Plus Outgoings</div>';
								}						
								echo $the_property_commercial_category;						
							}
						?>
					</div>			
				</div>
				<!-- Tab End Property Features -->
				
				<!-- Tab Rural Features -->
				<?php if ( $post_type == 'epl_rural') { ?>
					<div class="tab-section">
						<h5 class="tab-title">Rural Features</h5>
						<div class="tab-content">
							<ul class="listing-info tab-2-columns">
								<?php echo $the_property_rural_feature_list; ?>							
							</ul>
						</div>
					</div>
				<?php } ?>
				<!-- Tab Rural Features End -->
				

				<!-- Agent -->
				<?php if ( get_post_type() != 'epl_rental' ) { ?>
					<div class="tab-section">
						<h5 class="tab-title">Real Estate Agent</h5>
						<div class="tab-content">
							<?php echo epl_property_advanced_author_box(); ?>
						</div>
					</div>
				<?php } else { ?>
					<div class="tab-section">
						<h5 class="tab-title">Property Manager</h5>
						<div class="tab-content">
							<?php
								// echo epl_property_bio();
								echo epl_property_advanced_author_box();
							?>
						</div>
					</div>				
				<?php }
				
				if ( $suburb_name != '' ) {
					//Tab 4 Start - Suburb Info
					echo epl_property_tab_suburb();
					//Tab 4 End
				}
				
				//Tab 11 Start Leased
				echo epl_property_sold_leased();
				//Tab 11 End
				
				//Tab 8 Start
				// check if the post has a Post Thumbnail assigned to it.
				$attachments = get_children( array('post_parent' => get_the_ID(), 'post_type' => 'attachment', 'post_mime_type' => 'image') );
				$title = get_the_title();
				if ( $attachments && $d_gallery == 1 ) { ?>
					<div class="tab-section">
						<h5 class="tab-title">Gallery</h5>
						<!-- Gallery -->
						<div class="entry-gallery clearfix">
							<?php
								$d_gallery_n = '[gallery columns="' . $d_gallery_n . '" link="file"]';
								echo do_shortcode($d_gallery_n);
							?>
						</div>
					</div>
					<?php
				}
				//Tab 8 End

				if ( $d_map_position != 1 && $coords != ',') { ?>
					<!-- Map Plugin -->
						<div class="tab-section">
							<h5 class="tab-title">Map</h5>
							<div class="tab-content">
								<?php if(function_exists('epl_property_map_local_tabbed')) { epl_property_map_local_tabbed($coords,$property_post_type); }?>
							</div>
						</div>	
					<!-- End Map Plugin -->		
					<?php
				}
				
				//Sales Graph
				// Enable or disable Graph System
				$epl_graph_option = $epl_graph_on_off;
				if ( $epl_graph_option == 1) { ?>
					<div id="tab-graph" class="tab-section">
						<h5 class="tab-title">Comparables in <?php echo $property_address_suburb; ?></h5>
						<div class="tab-content">
							<?php if(function_exists('epl_property_graph')) { epl_property_graph(); }?>
						</div>
					</div>
					<?php }
				//End Sales Graph ?>	
			</div>
			<!--END Tabs-->
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

function epl_property_single_condensed($d_gallery , $d_gallery_n , $d_map_position ) {
	// Get Property Custom Values 
	include( 'property-meta.php' );
	include('epl-settings.php'); ?>
	
	<div id="post-<?php the_ID(); ?>" <?php post_class( 'epl-property-single view-condensed' ); ?>>
		<!-- title, meta, and date info -->				
		<div class="entry-header epl-clearfix">
			<div class="title-meta-wrapper">
				<div class="entry-col property-details">
					<h1 class="entry-title">
					<?php
						// Commercial and Business Address
						if ($property_post_type == 'epl_commercial' || $property_post_type == 'epl_business' ){

							if ( $property_address_display == 'no' && $property_com_display_suburb == 'no') { ?>
								<span class="item-street">
									<span class="item-state"><?php echo $property_address_state . ' '; ?></span>
									<span class="item-pcode"><?php echo $property_address_postal_code; ?></span>
								</span>
							<?php 
							} 
							elseif ( $property_address_display == 'no' && $property_com_display_suburb == 'yes') { ?>
								<span class="item-street">
									<span class="item-suburb"><?php echo $property_address_suburb . ', '; ?></span>
									<span class="item-state"><?php echo $property_address_state . ' '; ?></span>
									<span class="item-pcode"><?php echo $property_address_postal_code; ?></span>
								</span>
							<?php 
							}
							elseif ( $property_address_display == 'yes' && $property_com_display_suburb == 'no') { ?>
								<span class="item-street">
									<span class="item-state"><?php echo $property_address_state . ' '; ?></span>
									<span class="item-pcode"><?php echo $property_address_postal_code; ?></span>
								</span>
							<?php 
							}
							else { ?>
								<span class="item-street"><?php echo $property_address_street; ?></span>
								<span class="entry-title-sub">
									<span class="item-suburb"><?php echo $property_address_suburb . ', '; ?></span>
									<span class="item-state"><?php echo $property_address_state . ' '; ?></span>
									<span class="item-pcode"><?php echo $property_address_postal_code; ?></span>
								</span>
							<?php
							}
						} 
						else {
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
					<div class="property-feature-icons epl-clearfix">
						<?php echo '<span class="property-type">',$property_house_type, '</span>' , $property_icons_full; ?>							
					</div>
				</div>
			
				<div class="entry-col property-pricing-details">
					<!-- Property Price-->
					<div class="entry-meta pricing">
						<!-- Price / Rent -->
						<?php echo $price; ?>
					</div>
				</div>
			</div>
		</div>

		<!-- post content -->
		<div class="entry-content clearfix">
	
			<?php
				if ( $d_map_position && $coords != ',' ) { ?>
					<!-- Map Plugin -->
					<div class="tab-content">
						<?php if(function_exists('epl_property_map_local_tabbed')) { epl_property_map_local_tabbed($coords,$property_post_type); }?>
					</div>
					<!-- End Map Plugin -->		
					<?php
				} else {
					//Featured Image
					if ( has_post_thumbnail() ) { ?>
						<div class="entry-image">
							<div class="it-featured-image">
								<a href="<?php the_permalink(); ?>">
									<?php the_post_thumbnail( 'index_thumbnail', array( 'class' => 'index-thumbnail' ) ); ?>
								</a>
							</div>
						</div>	
					<?php }
				}
			
				//Home Open Details
				if($property_inspection_times != '') { ?>
					<div class="tab-content home-open">
						<span class="home-open-title">Home Open</span>
						<span class="home-open-time"><?php echo $property_inspection_times; ?></span>
					</div>
					<?php
				}
			
				//Tab 8 Start
				// check if the post has a Post Thumbnail assigned to it.
				$attachments = get_children( array('post_parent' => get_the_ID(), 'post_type' => 'attachment', 'post_mime_type' => 'image') );
				$title = get_the_title();
				if ( $attachments && $d_gallery == 1 ) { ?>
					<div class="property-gallery">
						<!-- Gallery -->
						<div class="entry-gallery epl-clearfix">
							<?php 
							$d_gallery_n = '[gallery columns="'. $d_gallery_n . '" link="file"]';
							echo do_shortcode($d_gallery_n);
							?>
						
						</div>
					</div>
				<?php }
				//Tab 8 End
			?>
				
			<!-- Tab Start -->
			<div class="tab-wrapper">
				<!-- Tab 0 Start - Details -->
				<div class="tab-section">			
					<div class="tab-content inspection-buttons-full-width">
						<!--- Inspection Buttons --->
						<?php 
							$button_1form_option = $epl_1form;
							$button_book_inspection_option = $epl_book_inspection;
					
							if ( 'epl_rental' == get_post_type() && 'leased' != $property_status && $button_1form_option == 1) { 
								echo epl_button_1form();
							}
							if ( 'current' == $property_status && $property_under_offer != 1 && $button_book_inspection_option == 1) { 
								echo epl_button_book_inspection();
							} 
						?>
					</div>		
				
					<!-- heading -->
					<div class="tab-content">
						<h2 class="entry-title"><?php echo $the_property_heading; ?></h2>
					</div>

					<div class="tab-content">
						<?php
							the_content( __( 'Read More &rarr;', 'it-l10n-BuilderChild-Air' ) );
						
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
				<!-- Tab 0 End -->
				
				<?php if ( $d_map_position != 1 && $coords != ',') { ?>
					<!-- Map Plugin -->
					<div class="tab-section">
						<div class="tab-content">
							<?php if(function_exists('epl_property_map_local_tabbed')) { epl_property_map_local_tabbed($coords,$property_post_type); }?>
						</div>
					</div>	
					<!-- End Map Plugin -->		
				<?php } ?>
				
				<!-- Tab Property Features -->
				<div class="tab-section">
					<?php if ( $post_type != 'epl_land' || $post_type != 'epl_business') { ?>
					<h5 class="tab-title">Property Features</h5>
						<div class="tab-content">
							<ul class="listing-info tab-4-columns">
								<?php echo $the_property_feature_list; ?>							
							</ul>
							<?php echo $l_car_comments; ?> 
						</div>
					<?php } ?>
				
					<div class="tab-content">
						<?php
							//Land Category
							echo $land_category;
							
							//Availability
							if( $property_date_available != '' && $property_status != 'leased' ) {
								echo '<div class="date-available">Available from ', $property_date_available, '</div>';
							}
							
							//Commercial Options
							if ( $property_post_type == 'epl_commercial' ) {
								if ( $property_com_plus_outgoings == 1) {
									echo '<div class="price-type">Plus Outgoings</div>';
								}						
								echo $the_property_commercial_category;						
							}
						?>
					</div>			
				</div>
				<!-- Tab End Property Features -->
				
				<!-- Tab Rural Features -->
				<?php if ( $post_type == 'epl_rural') { ?>
					<div class="tab-section">
						<h5 class="tab-title">Rural Features</h5>
						<div class="tab-content">
							<ul class="listing-info tab-2-columns">
								<?php echo $the_property_rural_feature_list; ?>							
							</ul>
						</div>
					</div>
				<?php } ?>
				<!-- Tab Rural Features End -->
				
				<?php
					if ( $suburb_name != '' ) {
						//Tab 4 Start - Suburb Info
						echo epl_property_tab_suburb();
						//Tab 4 End
					}
					
					//Tab 11 Start Leased
					echo epl_property_sold_leased();
					//Tab 11 End

					//Sales Graph
					// Enable or disable Graph System
					$epl_graph_option = $epl_graph_on_off;
					if ( $epl_graph_option == 1) { ?>
						<div id="tab-graph" class="tab-section">
							<h5 class="tab-title">Comparables in <?php echo $property_address_suburb; ?></h5>
							<div class="tab-content">
								<?php if(function_exists('epl_property_graph')) { epl_property_graph(); }?>
							</div>
						</div>
						<?php
					}
					//End Sales Graph
				
					//Agent
					if ( get_post_type() != 'epl_rental' ) { ?>
						<div class="tab-section">
							<h5 class="tab-title">Real Estate Agent</h5>
							<div class="tab-content">
								<?php echo epl_property_advanced_author_box(); ?>
							</div>
						</div>
					<?php } else { ?>
						<div class="tab-section">
							<h5 class="tab-title">Property Manager</h5>
							<div class="tab-content">
								<?php // echo epl_property_bio(); ?>
								<?php echo epl_property_advanced_author_box(); ?>
							</div>
						</div>
						<?php
					}
				?>	
			</div>		
			<!--END Tabs-->
		</div>
		<!-- categories, tags and comments -->
		<div class="entry-footer epl-clearfix">
			<div class="entry-meta">
				<?php wp_link_pages( array( 'before' => '<div class="entry-utility entry-pages">' . __( 'Pages:', 'it-l10n-Builder' ) . '', 'after' => '</div>', 'next_or_number' => 'number' ) ); ?>		
			</div>
		</div>
	</div>
	<!-- end .post -->
	<?php
}
