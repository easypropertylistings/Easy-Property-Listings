<?php

// Modify the Excerpt length on Archive pages
function epl_archive_custom_excerpt_length( $length ) {
	$op = get_option( 'epl_property_card_excerpt_length' );
	if ( $op == '') {
		return 22;
	} else {
		return $op;
	}
}

// Selecting Card Display Style
function epl_property_blog() {
	$option = get_option( 'epl_property_card_style' );
	//echo 'Option:', $option;
	add_filter( 'excerpt_length', 'epl_archive_custom_excerpt_length', 999 );

	// Dynamic Description Option 
	if ( $option == 1 ) {
		echo epl_property_blog_top();
	} elseif ($option == 2) {
		echo epl_property_blog_slim();
	} elseif ($option == 3) {
		echo epl_property_blog_suburb();
	} else {
		echo epl_property_blog_default();
	}
}

// Property Function for paged card display 
function epl_property_blog_default() {
	// Get Property Custom Values 
	include( 'property-meta.php' );

	// Status Removal Do Not Display Withdrawn or OffMarket listings
	if ( $property_status == 'withdrawn' || $property_status == 'offmarket' ) {

	} else { ?>
		<div id="post-<?php the_ID(); ?>" <?php post_class('property-blog epl-clearfix'); ?>>				
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
							if( $property_inspection_times_1 != '') {
								echo  '<div class="home-open"><strong>Open ', $property_inspection_times, '</strong></div>';
							}
						?>					
					</div>
				<?php endif; ?>
		
				<div class="property-box property-box-right property-content">
					<!-- Heading -->
					<h3 class="entry-title"><a href="<?php the_permalink() ?>"><?php echo $the_property_heading; ?></a></h3>
					<div class="entry-content">
						<?php
							// Dynamic Description Option
							if ( $epl_dynamic_description == 1) {
								echo $property_dynamic_description;
							} else {
								echo the_excerpt();
							}
						?>
					</div>
					
					<!-- Address -->
					<div class="property-address">
						<a href="<?php the_permalink(); ?>">
							<?php
							// Commercial and Business Address
							if ($property_post_type == 'epl_commercial' || $property_post_type == 'epl_business' ){

								if ( $property_address_display == 'no' && $property_com_display_suburb == 'no') { ?>
									<span class="street-address">
										<span class="item-state"><?php echo $property_address_state . ' '; ?></span>
										<span class="item-pcode"><?php echo $property_address_postal_code; ?></span>
									</span>
								<?php 
								} 
								elseif ( $property_address_display == 'no' && $property_com_display_suburb == 'yes') { ?>
									<span class="street-address">
										<span class="item-suburb"><?php echo $property_address_suburb . ', '; ?></span>
										<span class="item-state"><?php echo $property_address_state . ' '; ?></span>
										<span class="item-pcode"><?php echo $property_address_postal_code; ?></span>
									</span>
								<?php 
								}
								elseif ( $property_address_display == 'yes' && $property_com_display_suburb == 'no') { ?>
									<span class="street-address">
										<span class="item-state"><?php echo $property_address_state . ' '; ?></span>
										<span class="item-pcode"><?php echo $property_address_postal_code; ?></span>
									</span>
								<?php 
								}
								else { ?>
									<span class="street-address"><?php echo $property_address_street; ?></span>
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
		<?php
	} // End Status Removal
}

// Property Function for paged card display 
function epl_property_blog_top() {
	include( 'property-meta.php' );
	
	// Status Removal
	if ( $property_status == 'withdrawn' || $property_status == 'offmarket' ) {
		// Do Not Display Withdrawn or OffMarket listings
	} else { ?>

		<div id="post-<?php the_ID(); ?>" <?php post_class('property-blog card-top epl-clearfix'); ?>>
			<div class="entry-header">
				<!-- Address -->
				<h3 class="property-address">
					<span class="street-address alignleft">
					<!-- Address -->

						<a href="<?php the_permalink(); ?>">
							<?php
							// Commercial and Business Address
							if ($property_post_type == 'epl_commercial' || $property_post_type == 'epl_business' ){

								if ( $property_address_display == 'no' && $property_com_display_suburb == 'no') { ?>
									<span class="street-address">
										<span class="item-state"><?php echo $property_address_state . ' '; ?></span>
										<span class="item-pcode"><?php echo $property_address_postal_code; ?></span>
									</span>
								<?php 
								} 
								elseif ( $property_address_display == 'no' && $property_com_display_suburb == 'yes') { ?>
									<span class="street-address">
										<span class="item-suburb"><?php echo $property_address_suburb . ', '; ?></span>
										<span class="item-state"><?php echo $property_address_state . ' '; ?></span>
										<span class="item-pcode"><?php echo $property_address_postal_code; ?></span>
									</span>
								<?php 
								}
								elseif ( $property_address_display == 'yes' && $property_com_display_suburb == 'no') { ?>
									<span class="street-address">
										<span class="item-state"><?php echo $property_address_state . ' '; ?></span>
										<span class="item-pcode"><?php echo $property_address_postal_code; ?></span>
									</span>
								<?php 
								}
								else { ?>
									<span class="street-address"><?php echo $property_address_street; ?></span>
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
					</span>
					<span class="address price alignright"><?php echo $price; ?></span>
				</h3>			
			</div>
			<div class="entry-content">
				<?php if ( has_post_thumbnail() ) : ?>
					<div class="property-box property-box-left property-featured-image-wrapper">
						<a href="<?php the_permalink(); ?>">
							<div class="epl-blog-image">
								<?php the_post_thumbnail( 'epl-image-medium-crop', array( 'class' => 'teaser-left-thumb' ) ); ?>
							</div>
						</a>
					</div>
				<?php endif; ?>
		
				<div class="property-box property-box-right property-content">
					<!-- Property Featured Icons -->
					<div class="property-feature-icons">
						<?php echo '<span class="property-type">',$property_house_type, '</span>' , $property_icons_full; ?>				
					</div>
					<!-- Heading -->
					<h3 class="entry-title"><a href="<?php the_permalink() ?>"><?php echo $the_property_heading; ?></a></h3>
				
					<div class="entry-content">
						<?php
							// Dynamic Description Option 
							if ( $epl_dynamic_description == 1) {
								echo $property_dynamic_description;
							} elseif ( $epl_property_card_excerpt == 1 ) { 
								// Not Text
							} else {
								echo the_excerpt( );
							}
						
							//Home Open
							if( $property_inspection_times != '') {
								echo  '<div class="home-open"><strong>Open ', $property_inspection_times, '</strong></div>';
							}
						?>
						<div class="card-buttons epl-clearfix">
							<!--- Details  --->
							<div class="book-inspection">
								<form action="<?php the_permalink(); ?>" method="get">
									<input type="submit" value="Details">
								</form>
							</div>
							<!--- Online Inspection v2 --->
							<?php 
								$button_book_inspection_option = $epl_book_inspection;
								if ( 'current' == $property_status && $property_under_offer != 1 && $button_book_inspection_option == 1) {
									echo epl_button_book_inspection();
								 }
							?>
						 </div>
					</div>
				</div>	
			</div>
		</div>
		<?php
	} // End Status Removal
}

function epl_property_blog_suburb() {
	// Get Property Custom Values 
	include( 'property-meta.php' );

	// Status Removal
	if ( $property_status == 'withdrawn' || $property_status == 'offmarket' ) {
		// Do Not Display Withdrawn or OffMarket listings
	} else { ?>
		<div id="post-<?php the_ID(); ?>" <?php post_class('property-blog suburb-top epl-clearfix'); ?>>
			<!--<div id="epl-property-blog" class="property-blog-wrapper-container">-->
			<div class="entry-header address-header">
				<!-- Address -->
				<div class="address alignleft">
						<?php
						// Commercial and Business Address
						if ($property_post_type == 'epl_commercial' || $property_post_type == 'epl_business' ){

							if ( $property_address_display == 'no' && $property_com_display_suburb == 'no') { ?>
								<span class="street-address">
									<span class="item-state"><?php echo $property_address_state . ' '; ?></span>
									<span class="item-pcode"><?php echo $property_address_postal_code; ?></span>
								</span>
							<?php 
							} 
							elseif ( $property_address_display == 'no' && $property_com_display_suburb == 'yes') { ?>
								<span class="street-address">
									<span class="item-suburb"><?php echo $property_address_suburb; ?></span>
								</span>
							<?php 
							}
							elseif ( $property_address_display == 'yes' && $property_com_display_suburb == 'no') { ?>
								<span class="street-address">
									<span class="item-state"><?php echo $property_address_state . ' '; ?></span>
									<span class="item-pcode"><?php echo $property_address_postal_code; ?></span>
								</span>
							<?php 
							}
							else { ?>
								<span class="entry-title-sub">
									<span class="item-suburb"><?php echo $property_address_suburb; ?></span>
								</span>
							<?php
							}
						} 
						else {
						// Address Display not Commercial or Business type
							if ( $property_address_display == 'yes' ) { ?>
								<span class="entry-title-sub">
									<span class="item-suburb"><?php echo $property_address_suburb; ?></span>
								</span>
							<?php } else { ?>
								<span class="item-suburb"><?php echo $property_address_suburb; ?></span>
							<?php } 
						} ?>
				</div>
				<div class="address price alignright">
					<?php echo $price; ?>
				</div>
			</div>
		
			<div class="entry-content">
				<?php if ( has_post_thumbnail() ) : ?>
					<div class="property-box property-box-left property-featured-image-wrapper">
						<a href="<?php the_permalink(); ?>">
							<?php the_post_thumbnail( 'epl-image-medium-crop', array( 'class' => 'teaser-left-thumb' ) ); ?>
						</a>
					</div>
				<?php endif; ?>
		
				<div class="property-box property-box-right property-content">
					<!-- Heading -->
					<h3 class="entry-title"><a href="<?php the_permalink() ?>"><?php echo $the_property_heading; ?></a></h3>
					<?php
						// Dynamic Description Option 
						if ( epl_dynamic_description == 1) {
							echo $property_dynamic_description;
						} else {
							echo the_excerpt();
						}
					?>
					
					<!-- Address -->
					<div class="property-address">
						<a href="<?php the_permalink(); ?>">
							<?php
							// Commercial and Business Address
							if ($property_post_type == 'epl_commercial' || $property_post_type == 'epl_business' ){

								if ( $property_address_display == 'no' && $property_com_display_suburb == 'no') { ?>
									<span class="street-address">
										<span class="item-state"><?php echo $property_address_state . ' '; ?></span>
										<span class="item-pcode"><?php echo $property_address_postal_code; ?></span>
									</span>
								<?php 
								} 
								elseif ( $property_address_display == 'no' && $property_com_display_suburb == 'yes') { ?>
									<span class="street-address">
										<span class="item-suburb"><?php echo $property_address_suburb . ', '; ?></span>
										<span class="item-state"><?php echo $property_address_state . ' '; ?></span>
										<span class="item-pcode"><?php echo $property_address_postal_code; ?></span>
									</span>
								<?php 
								}
								elseif ( $property_address_display == 'yes' && $property_com_display_suburb == 'no') { ?>
									<span class="street-address">
										<span class="item-state"><?php echo $property_address_state . ' '; ?></span>
										<span class="item-pcode"><?php echo $property_address_postal_code; ?></span>
									</span>
								<?php 
								}
								else { ?>
									<span class="street-address"><?php echo $property_address_street; ?></span>
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
							echo  '<div class="home-open"><strong>Open ', $property_inspection_times, '</strong></div>';
						}
					?>
					
					<!-- Property Featured Icons -->
					<div class="property-feature-icons">
						<?php echo '<span class="property-type">',$property_house_type, '</span>' , $property_icons_full; ?>					
					</div>
				</div>	
			</div>
		</div>
		<?php
	} // End Status Removal
}

// Property Function for paged card display 
function epl_property_blog_slim() {
	include( 'property-meta.php' );

	// Status Removal
	if ( $property_status == 'withdrawn' || $property_status == 'offmarket' ) {
		// Do Not Display Withdrawn or OffMarket listings
	} else { ?>
		<div id="post-<?php the_ID(); ?>" <?php post_class('property-blog-slim epl-clearfix'); ?>>				
			<div id="epl-property-blog-slim" class="property-blog-slim-wrapper-container">		
				<div class="entry-header address-header">
					<!-- Address -->
					<div class="address alignleft">
						<?php
						// Commercial and Business Address
						if ($property_post_type == 'epl_commercial' || $property_post_type == 'epl_business' ){

							if ( $property_address_display == 'no' && $property_com_display_suburb == 'no') { ?>
								<span class="street-address">
									<span class="item-state"><?php echo $property_address_state . ' '; ?></span>
									<span class="item-pcode"><?php echo $property_address_postal_code; ?></span>
								</span>
							<?php 
							} 
							elseif ( $property_address_display == 'no' && $property_com_display_suburb == 'yes') { ?>
								<span class="street-address">
									<span class="item-suburb"><?php echo $property_address_suburb; ?></span>
								</span>
							<?php 
							}
							elseif ( $property_address_display == 'yes' && $property_com_display_suburb == 'no') { ?>
								<span class="street-address">
									<span class="item-state"><?php echo $property_address_state . ' '; ?></span>
									<span class="item-pcode"><?php echo $property_address_postal_code; ?></span>
								</span>
							<?php 
							}
							else { ?>
								<span class="entry-title-sub">
									<span class="item-suburb"><?php echo $property_address_suburb; ?></span>
								</span>
							<?php
							}
						} 
						else {
						// Address Display not Commercial or Business type
							if ( $property_address_display == 'yes' ) { ?>
								<span class="entry-title-sub">
									<span class="item-suburb"><?php echo $property_address_suburb; ?></span>
								</span>
							<?php } else { ?>
								<span class="item-suburb"><?php echo $property_address_suburb; ?></span>
							<?php } 
						} ?>
					</div>
					<div class="address alignright">
						<?php echo $price; ?>
					</div>
				</div>
		
				<div class="entry-content">			
					<?php if ( has_post_thumbnail() ) : ?>
						<div class="property-box slim property-box-left property-featured-image-wrapper">
							<a href="<?php the_permalink(); ?>">
								<?php the_post_thumbnail( 'admin-list-thumb', array( 'class' => 'teaser-left-thumb' ) ); ?>
							</a>
						</div>
					<?php endif; ?>
			
					<div class="property-box slim property-box-right property-content">
						<!-- Heading -->
						<h3 class="entry-title"><a href="<?php the_permalink() ?>"><?php echo $the_property_heading; ?></a></h3>
						
						<?php //the_excerpt(); ?>
					
					<!-- Address -->
					<div class="property-address">
						<a href="<?php the_permalink(); ?>">
							<?php
							// Commercial and Business Address
							if ($property_post_type == 'epl_commercial' || $property_post_type == 'epl_business' ){

								if ( $property_address_display == 'no' && $property_com_display_suburb == 'no') { ?>
									<span class="street-address">
										<span class="item-state"><?php echo $property_address_state . ' '; ?></span>
										<span class="item-pcode"><?php echo $property_address_postal_code; ?></span>
									</span>
								<?php 
								} 
								elseif ( $property_address_display == 'no' && $property_com_display_suburb == 'yes') { ?>
									<span class="street-address">
										<span class="item-suburb"><?php echo $property_address_suburb . ', '; ?></span>
										<span class="item-state"><?php echo $property_address_state . ' '; ?></span>
										<span class="item-pcode"><?php echo $property_address_postal_code; ?></span>
									</span>
								<?php 
								}
								elseif ( $property_address_display == 'yes' && $property_com_display_suburb == 'no') { ?>
									<span class="street-address">
										<span class="item-state"><?php echo $property_address_state . ' '; ?></span>
										<span class="item-pcode"><?php echo $property_address_postal_code; ?></span>
									</span>
								<?php 
								}
								else { ?>
									<span class="street-address"><?php echo $property_address_street; ?></span>
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
								echo  '<div class="home-open"><strong>Open ', $property_inspection_times, '</strong></div>';
							}
						?>
						
						<!-- Property Featured Icons -->
						<div class="property-feature-icons">
							<?php echo $property_icons_full; ?>					
						</div>
					</div>
				</div>
			</div>
		</div>
	<?php
	} // End Status Removal
}

// AUTHOR PROPERTY CARDS : Property Card
function epl_property_author_card($display,$image,$title,$icons) {
	// Get Property Custom Values 
	include( 'property-meta.php' );	
	
	// Status Removal
	if ( $property_status == 'withdrawn' || $property_status == 'offmarket' ) {
		// Do Not Display Withdrawn or OffMarket listings
	} else { ?>
		<div id="post-<?php the_ID(); ?>" class="property-widget-image hentry" <?php //post_class('property-widget-image'); ?>>
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
							// Commercial and Business Address
							if ($property_post_type == 'epl_commercial' || $property_post_type == 'epl_business' ){

								if ( $property_address_display == 'no' && $property_com_display_suburb == 'no') { ?>
									<div class="property-meta suburb-name">
										<span class="item-state"><?php echo $property_address_state . ' '; ?></span>
										<span class="item-pcode"><?php echo $property_address_postal_code; ?></span>
									</div>
								<?php 
								} 
								elseif ( $property_address_display == 'no' && $property_com_display_suburb == 'yes') { ?>
										<div class="property-meta suburb-name"><?php echo $property_address_suburb; ?></div>
								<?php 
								}
								elseif ( $property_address_display == 'yes' && $property_com_display_suburb == 'no') { ?>
									<div class="property-meta suburb-name">
										<span class="item-state"><?php echo $property_address_state . ' '; ?></span>
										<span class="item-pcode"><?php echo $property_address_postal_code; ?></span>
									</div>
								<?php 
								}
								else { ?>
									<div class="property-meta suburb-name"><?php echo $property_address_suburb; ?></div>
									<div class="property-meta street-name"><?php echo $property_address_street; ?></div>
								<?php
								}
							} 
							else {
							// Address Display not Commercial or Business type
								if ( $property_address_display == 'yes' ) { ?>
									<div class="property-meta suburb-name"><?php echo $property_address_suburb; ?></div>
									<div class="property-meta street-name"><?php echo $property_address_street; ?></div>
								<?php } else { ?>
									<div class="property-meta suburb-name"><?php echo $property_address_suburb; ?></div>
								<?php } 
							} ?>
					</div>
				<!-- END Address -->
				<?php
					// Icon Options
					if ($icons == 'all') { ?>
						<div class="property-meta property-feature-icons"><?php echo $property_icons_full; ?></div>
					<?php } elseif ($icons == 'bb') { ?>
						<div class="property-meta property-feature-icons"><?php echo $property_icons_bb; ?></div>
					<?php } ?>

				<div class="property-meta price"><?php echo $price; ?></div>
				<form class="epl-property-button" action="<?php the_permalink(); ?>" method="post">
					<input type=submit value="Read More">
				</form>
			</div>
		</div>
		<?php
	} // End Status Removal
}

// WIDGET : Property Card
function epl_property_widget($display,$image,$title,$icons) {
	// Get Property Custom Values 
	include( 'property-meta.php' );	
	
	// Status Removal
	if ( $property_status == 'withdrawn' || $property_status == 'offmarket' ) {
		// Do Not Display Withdrawn or OffMarket listings
	} else { ?>
		<div id="post-<?php the_ID(); ?>" class="property-widget-image" <?php //post_class('property-widget-image'); ?>>
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
					<h5 class="property-heading"><?php echo $the_property_heading; ?></h5>
				<?php } ?>
				<!-- Address -->
					<div class="property-address">
							<?php
							// Commercial and Business Address
							if ($property_post_type == 'epl_commercial' || $property_post_type == 'epl_business' ){

								if ( $property_address_display == 'no' && $property_com_display_suburb == 'no') { ?>
									<div class="property-meta suburb-name">
										<span class="item-state"><?php echo $property_address_state . ' '; ?></span>
										<span class="item-pcode"><?php echo $property_address_postal_code; ?></span>
									</div>
								<?php 
								} 
								elseif ( $property_address_display == 'no' && $property_com_display_suburb == 'yes') { ?>
										<div class="property-meta suburb-name"><?php echo $property_address_suburb; ?></div>
								<?php 
								}
								elseif ( $property_address_display == 'yes' && $property_com_display_suburb == 'no') { ?>
									<div class="property-meta suburb-name">
										<span class="item-state"><?php echo $property_address_state . ' '; ?></span>
										<span class="item-pcode"><?php echo $property_address_postal_code; ?></span>
									</div>
								<?php 
								}
								else { ?>
									<div class="property-meta suburb-name"><?php echo $property_address_suburb; ?></div>
									<div class="property-meta street-name"><?php echo $property_address_street; ?></div>
								<?php
								}
							} 
							else {
							// Address Display not Commercial or Business type
								if ( $property_address_display == 'yes' ) { ?>
									<div class="property-meta suburb-name"><?php echo $property_address_suburb; ?></div>
									<div class="property-meta street-name"><?php echo $property_address_street; ?></div>
								<?php } else { ?>
									<div class="property-meta suburb-name"><?php echo $property_address_suburb; ?></div>
								<?php } 
							} ?>
					</div>
				<!-- END Address -->

				<?php // Icon Options
				if ($icons == 'all') { ?>
					<div class="property-meta property-feature-icons"><?php echo $property_icons_full; ?></div>
				<?php } elseif ($icons == 'bb') { ?>
					<div class="property-meta property-feature-icons"><?php echo $property_icons_bb; ?></div>
				<?php } ?>		

				<div class="property-meta price"><?php echo $price; ?></div>
				<form class="epl-property-button" action="<?php the_permalink(); ?>" method="post">
					<input type=submit value="Read More">
				</form>
			</div>
		</div>
		<?php
	} // End Status Removal
}

// WIDGET : Property List
function epl_property_widget_list_option() {
	$property_status = get_post_meta( get_the_ID(), 'property_status', true );
	// Status Removal
	if ( $property_status == 'withdrawn' || $property_status == 'offmarket' ) {
		// Do Not Display Withdrawn or OffMarket listings
	} else { ?>
		<li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
		<?php
	} // End Status Removal
}

// WIDGET : Image Only
function epl_property_widget_image_only_option($image) { ?>
	<div id="post-<?php the_ID(); ?>" class="property-widget-image" <?php //post_class('epl-clearfix'); ?>>
		<div class="entry-header">
			<?php if ( has_post_thumbnail() ) : ?>
				<div class="epl-img-widget">
					<a href="<?php the_permalink(); ?>">
						<?php the_post_thumbnail( $image ); ?>
					</a>
				</div>
			<?php endif; ?>		
		</div>
	</div>
	<?php
}

function epl_supermap_property_card() {	
	// Featured Property Option Array
	$args = array(
		'post_type' => 'epl_rental', 
		'showposts' => 5
	);

	$mlq = new WP_Query ( $args );
	if( $mlq->have_posts() ) :
		while($mlq->have_posts()) : $mlq->the_post();
			include('property-meta.php'); ?>
			<div class="supermap-property-card info-window epl-clearfix">
				<div class="supermap-card-boxed">
					<div class="map-card-boxed-left">
						<?php the_post_thumbnail('admin-list-thumb'); ?>
						<div><?php echo $property_address_street; ?></div>
						<p>
							<?php echo $property_address_suburb; ?></br>
							<?php echo $price; ?>
						</p>
					</div>
				</div>
			</div>
			<?php
			// END Post
			wp_reset_query(); 
		endwhile; 
	endif;
}
