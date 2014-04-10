<?php
/*
 * Template: Author Profile
 */

// TEMPLATE - Author Bio
function epl_property_author_widget() { ?>
	<div class="author-col author-photo">
		<?php if (function_exists('get_avatar')) { echo get_avatar( get_the_author_email(), '80' ); }?>  
	</div>
	<div class="author-col author-text">  
	   <h6>About <?php the_author_posts_link(); ?></h6>  
	   <p><?php the_author_description(); ?></p>  
	</div>
	<?php
}

// Template: Author Box
function epl_property_author_box() {
	include('author-meta.php'); ?>

	<!-- Author Box Container -->
	<div id="epl_box" class="epl_box">
		<div class="entry-content">			
			<div class="author-box author-image">
				<?php
					if (function_exists('get_avatar')) {
						echo get_avatar( get_the_author_email(), '180' );
					}
				?>
			</div>
			
			<div class="author-box author-details">
				<h5 class="entry-title"><?php the_author_posts_link(); ?></h5>
				<div class="author-position"><?php echo $position ?></div>
				<div class="author-contact">
					<span class="label-mobile">Mobile </span><span class="mobile"><?php echo $mobile ?></span>
				</div>
				<div class="author-contact">
					<span class="label-email">Email </span><a href="mailto:<?php the_author_email(); ?>"><span class="email"><?php the_author_email(); ?></span></a>
				</div>			
			</div>			

			<h6 class="author-box-title">Social</h6>
			<?php
				echo $i_twitter;
				echo $i_facebook;
				echo $i_google;
				echo $i_linkedin;
				echo $i_skype;
				
				if ( $slogan != '' ) { ?>
					<h6 class="author-box-title">Slogan</h6>
					<div class="author-slogan"><?php echo  $slogan ?></div>
				<?php }
			
				if ( get_the_author_description() != '' ) { ?>
					<h6 class="author-box-title">Bio</h6>
					<div class="author-description"><?php the_author_description(); ?></div>
				<?php }
				
				if ( $video != '' ) { ?>
					<h6 class="author-box-title">Video</h6>
					<div class="author-video"><?php echo $e_video ?></div>
				<?php }
			?>			

			<h6 class="">Linked Content</h6>
			<div class="">Content</div>			
		</div>
	<!-- END Author Box Container -->
	</div>
	<?php
}

// Template: Author Box
function epl_property_advanced_author_box() {
	include('author-meta.php'); ?>

	<?php
		// Recent Listings Query
		$mlq = new WP_Query( array (
			'post_type' => array( 'wpc_property', 'wpc_rental', 'wpc_land', 'wpc_commercial' ),
			'posts_per_page' => 6
		) );
	?>

				
	<!-- Author Box Container -->
	<div id="epl-box" class="epl-author-box">		
		<ul class="author-tabs">
			<li class="tab-link author-current" data-tab="tab-1">About</li>
			<?php
				if ( get_the_author_description() != '' ) { ?>
					<li class="tab-link" data-tab="tab-2">Bio</li>
				<?php }
				
				if ( $video != '' ) { ?>
					<li class="tab-link" data-tab="tab-3">Video</li>
				<?php }
				
				if( $mlq->have_posts() ) { ?>
					<li class="tab-link" data-tab="tab-4">Recent Listings</li>
				<?php }
				
				if ( function_exists('gravity_form') && $contact_form != '') { ?>
					<li class="tab-link" data-tab="tab-6">Contact</li>
				<?php }
			?>			
		</ul>
	
		<div class="author-box-outer-wrapper epl-clearfix">			
			<div class="author-box author-image">
				<?php 
					$author_image_type = get_option( 'epl_staff_image_type' );
					$author_style = get_option( 'epl_staff_link_to' );
			
					if ( $author_image_type == 0 && function_exists('get_avatar') ) {
						echo get_avatar( get_the_author_email(), '80' );
				
						$author_ID = get_the_author_meta('ID'); 
						$author_args = array(
							'post_type' => 'epl_directory',
							'author' => $author_ID
						);
						$the_author_image_query = new WP_Query( $author_args );
						if ( $the_author_image_query->have_posts() ) {
							while ( $the_author_image_query->have_posts() ) {
								$the_author_image_query->the_post();
								$directory_link = get_permalink();
							}
						}
						wp_reset_postdata();
					} else {
						$author_ID = get_the_author_meta('ID'); 
						$author_args = array(
							'post_type' => 'epl_directory',
							'author' => $author_ID
						);
						$the_author_image_query = new WP_Query( $author_args );
						if ( $the_author_image_query->have_posts() ) {
							while ( $the_author_image_query->have_posts() ) {
								$the_author_image_query->the_post();						
								if ( has_post_thumbnail() ) { 
									the_post_thumbnail( 'thumbnail');
								}
								$directory_link = get_permalink();
							}
						}
						wp_reset_postdata();
					}
				?>
			</div>
			
			<div id="tab-1" class="author-box author-details author-tab-content author-current">
				<div class="author-contact-details">
				
					<!--- Author Page Style --->
					<?php if ( $author_style == 1) { ?> 
						<h5 class="entry-title"><a href="<?php echo $directory_link ?>"><?php the_author_meta( 'display_name' ); ?></a></h5>
					<?php } else { ?>
						<h5 class="entry-title"><?php the_author_posts_link(); ?></h5>
					<?php } ?>
				
					<div class="author-position"><?php echo $position ?></div>
					<div class="author-contact">
						<span class="label-mobile"></span><span class="mobile"><?php echo $mobile ?></span>
					</div>
					<!--<div class="author-contact">
						<span class="label-email"></span><a href="mailto:<?php //the_author_email(); ?>"><span class="email"><?php //the_author_email(); ?></span></a>
					</div>-->
				</div>
				
				<?php if ( $slogan != '' ) { ?>
					<div class="author-slogan"><?php echo $slogan ?></div>
				<?php } ?>
				
				<div class="epl-clearfix"></div>
				<div class="author-social-buttons">
					<?php
						echo $i_email;
						echo $i_facebook;
						echo $i_twitter;
						echo $i_google;
						echo $i_linkedin;
						echo $i_skype;
					?>
				</div>
			</div>

			<?php
				if ( get_the_author_description() != '' ) { ?>
					<div id="tab-2" class="author-box author-bio author-tab-content">
						<h6 class="author-box-title">Bio</h6>
						<div class="author-content"><?php the_author_description(); ?></div>
						<?php if ( $author_style == 1) { ?> 
							<span class="bio-more"><a href="<?php echo $directory_link ?>">Read More</span></a>
						<?php } else { ?>
							<span class="bio-more"><a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>">Read More</a></span>
						<?php } ?>
					</div>
					<?php
				}
			
				if ( $video != '' ) { ?>
					<div id="tab-3" class="author-box author-description author-tab-content">
						<div class="author-video"><?php echo $e_video ?></div>
					</div>
					<?php
				}
			
				if( $mlq->have_posts() ) { ?>
					<div id="tab-4" class="author-box author-listings author-tab-content">
						<?php while($mlq->have_posts()) {
							$mlq->the_post(); 
								epl_property_widget_list_option();
						} ?>
					</div>
					<?php
				}
				wp_reset_query(); 
			?>

			<div id="tab-5" class="author-box author-social author-tab-content">
				<h6 class="author-box-title">Social</h6>
			</div>
			<div id="tab-6" class="author-box author-contact-form author-tab-content">
				<h6 class="author-box-title">Contact</h6>
					<?php
						//echo do_shortcode($contact_form);
						if ( function_exists('gravity_form') && $contact_form != ''){
							gravity_form($contact_form, false, false, false, '', false); 
						}
					?>
			</div>			
		</div>
		<!-- END Author Box Container -->
	</div>
	<?php
}

// Directory Content Page / Author Page
function epl_property_directory_content_simple() {
	include('author-meta.php'); ?>

	<div id="post-<?php the_ID(); ?>" <?php post_class('directory-page epl-clearfix'); ?>>
		<div class="entry-header">
			<?php
				//Featured Image
				if ( has_post_thumbnail() ) {
					the_post_thumbnail( 'medium', array( 'class' => 'index-thumbnail alignright' ) );
				} elseif (function_exists('get_avatar')) {
					echo '<div class="alignright">';
					echo get_avatar( get_the_author_email(), '180' );
					echo '</div>';
				}
			?>

			<h1 class="staff-title"><?php the_title(); ?></h1>			
			<h4><?php echo $position; ?></h4>
			
			<!-- Custom Taxonomy -->
			<div class="department">
				<?php echo get_the_term_list($post->ID, 'department', '', ', ', ''); ?>
			</div>

			<div class="contact-details">
				<?php 
					//Mobile
					if( $mobile != '') {
					  echo '<div class="contact-mobile">Mobile ' , $mobile , '</div>';
					} 
				
					if( get_the_author_email() != '') {
					  echo '<div class="contact-email"><a href="mailto:' , the_author_email() , '">' , the_author_email() , '</a></div>';
					} 
				?>
			</div>
		</div>
	
		<div class="entry-content">
			<?php
				the_content();
				wp_link_pages( array( 'before' => '<p><strong>' . __( 'Pages:', 'it-l10n-Builder' ) . '</strong> ', 'after' => '</p>', 'next_or_number' => 'number' ) );
				edit_post_link( __( 'Edit this entry.', 'it-l10n-Builder' ), '<p class="edit-entry-link">', '</p>' );
			?>
		</div>
	</div>
	<?php
}

// Directory Content Page / Author Page
function epl_property_directory_content() { 
	include('author-meta.php'); ?>

	<div id="post-<?php the_ID(); ?>" <?php post_class('directory-page-dynamic epl-clearfix'); ?>>
		<div class="entry-header">
			<?php echo epl_property_advanced_author_box(); ?>
		</div>

		<div class="entry-content">
			<?php
				the_content();
				// Recent Posts
				$post_args = array(
					'post_type' => 'post',
					'author' => $author_id,
					'posts_per_page' => '4'
				);
				$post_query = new WP_Query( $post_args );
				if ( $post_query->have_posts() ) { ?>
				   	<div class="directory-section epl-clearfix">
						<h4>Latest Updates</h4>
						<?php
							while ( $post_query->have_posts() ) {
								$post_query->the_post();
								echo epl_property_post_cards();
							}
						?>
					</div>
					<?php
				}
				wp_reset_postdata();

				// Latest Testimonial
				$test_args = array(
					'post_type' => 'epl_testimonial',
					'author' => $author_id,
					'posts_per_page' => '1'
				);		
				$test_query = new WP_Query( $test_args );
				if ( $test_query->have_posts() ) { ?>
					<div class="directory-section-testimonial epl-clearfix">
						<?php
							while ( $test_query->have_posts() ) {
								$test_query->the_post();
								echo epl_property_testimonial_archive();
							}
						?>
					</div>
					<?php
				}
				wp_reset_postdata();
			
				if ( function_exists( 'returned_properties' ) ) { ?>
					<div class="agent-current">
						<h4>Current Properties</h4>
						<?php
							$authorid = get_the_author_meta( 'wppropertyid' );
							echo do_shortcode('[property_overview type=land,residential,rental per_page=5 sorter=off sorter_type=none pagination=off wpp_agents=' . $authorid .']' );
						?>
					</div>
					
					<div class="agent-sold">
						<h4>Recent Sales</h4>
						<?php
							$authorid = get_the_author_meta( 'wppropertyid' );
							echo do_shortcode('[property_overview type=sold,rental__leased per_page=5 sorter=off sorter_type=none pagination=off wpp_agents=' . $authorid .']' );
						?>
					</div>
					<?php
				} else {					
					// Listings 
					$list_args = array(
						'post_type' => array('epl_property', 'epl_rental', 'epl_land', 'epl_commercial' ),
						'author' => $author_id,
						'posts_per_page' => '4'
					);
					$display = 'image';
					$image = 'thumbnail';				
					$list_query = new WP_Query( $list_args );
					if ( $list_query->have_posts() ) { ?>
						<div class="directory-section epl-clearfix">
							<h4>Listings</h4>
							<?php
								while ( $list_query->have_posts() ) {
									$list_query->the_post();
									epl_property_author_card($display,$image,$d_title,$d_icons);
								}
							?>
						</div>
						<?php
					}
					wp_reset_postdata();
				}

				//  Suburbs
				$suburbs_args = array(
					'post_type' => 'wpc_suburb',
					'author' => $author_id,
					'posts_per_page' => '4'
				);
				$suburb_query = new WP_Query( $suburbs_args );
				if ( $suburb_query->have_posts() ) { ?>
					<div class="directory-section epl-clearfix">
						<h4>Suburbs</h4>
						<?php
							while ( $suburb_query->have_posts() ) {
								$suburb_query->the_post();
								echo epl_suburb_card();
							}
						?>
					</div>
					<?php
				}
				wp_reset_postdata();
				
				wp_link_pages( array( 'before' => '<p><strong>' . __( 'Pages:', 'it-l10n-Builder' ) . '</strong> ', 'after' => '</p>', 'next_or_number' => 'number' ) );
				edit_post_link( __( 'Edit this entry.', 'it-l10n-Builder' ), '<p class="edit-entry-link">', '</p>' );
			?>
		</div>
	</div>
	<?php
}
