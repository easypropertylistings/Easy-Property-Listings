<?php
/*
 * Template: Author Cards
 */

// TEMPLATE - Author Bio	
function epl_property_bio() {
	include('author-meta.php');
	$author_image_type = get_option('epl_staff_image_type');
	$author_style = get_option('epl_staff_link_to'); ?>
	
	<div class="author-col author-photo">
		<?php
			if ( $author_image_type == 0 && function_exists('get_avatar') ) {
				echo get_avatar( get_the_author_email(), '80' );
			
				$author_ID = get_the_author_meta('ID'); 
				$author_args = array(
					'post_type' => 'epl_directory',
					'author' => $author_ID
				);

				// The Query
				$the_author_image_query = new WP_Query( $author_args );
				// The Loop
				if ( $the_author_image_query->have_posts() ) {
					while ( $the_author_image_query->have_posts() ) {
						$the_author_image_query->the_post();

						$directory_link = get_permalink();
					}
				} else {
					// no posts found
				}
				/* Restore original Post Data */
				wp_reset_postdata();

			} else {
				$author_ID = get_the_author_meta('ID'); 
				$author_args = array(
					'post_type' => 'epl_directory',
					'author' => $author_ID
				);

				// The Query
				$the_author_image_query = new WP_Query( $author_args );
				// The Loop
				if ( $the_author_image_query->have_posts() ) {
					while ( $the_author_image_query->have_posts() ) {
						$the_author_image_query->the_post();
					
						if ( has_post_thumbnail() ) { 
							the_post_thumbnail( 'thumbnail');
						}
						$directory_link = get_permalink();
					}
				} else {
					// no posts found
				}
				/* Restore original Post Data */
				wp_reset_postdata();
			}
		?>
	</div>
	<div class="author-col author-text">  
		<?php
			if ( $author_style == 1) { ?> 
			<h6>About <a href="<?php echo $directory_link; ?>"><? echo $name; ?></a></h6>
		<?php } else { ?>
			<h6>About <?php the_author_posts_link(); ?></h6>
		<?php } ?>
		
		<p><?php the_author_description(); ?></p>  
	</div>
	<?php
}

function epl_property_author_box_simple_card() {
	include('author-meta.php');
	$author_style = get_option('epl_staff_link_to');
	$epl_staff_excerpt = get_option( 'epl_staff_excerpt' ); ?>
	
	<!-- Author Box Container -->
	<div class="epl-author-card author">
		<div class="entry-content">
			<div class="author-box author-image">
				<!-- Featured Image -->
				<?php
					if ( has_post_thumbnail() ) { 
						the_post_thumbnail( 'index_thumbnail', array( 'class' => 'index-thumbnail' ) ); 

					} elseif (function_exists('get_avatar')) {
						echo get_avatar( get_the_author_email(), '180' ); 
					}
				?>
			</div>
			
			<div class="author-box author-details">
				<!--- Author Page Style --->
				<?php if ( $author_style == 1) { ?>
					<h5 class="entry-title"><a href="<?php the_permalink(); ?>"><? the_title(); ?></a></h5>
				<?php } else { ?>
					<h5 class="entry-title"><?php the_author_posts_link(); ?></h5>
				<?php } ?>
				
				<div class="author-position"><?php echo $position ?></div>
				<div class="author-contact">
					<?php if ( $mobile != '' ) { ?>
						<span class="label-mobile">Mobile </span><span class="mobile"><?php echo $mobile ?></span>
					<?php } ?>
				</div>
				<div class="author-social-buttons">
					<?php
						echo $i_email;
						echo $i_twitter;
						echo $i_facebook;
						echo $i_google;
						echo $i_linkedin;
						echo $i_skype;
					?>
				</div>
					<?php
						if ( $epl_staff_excerpt == 1) {
							echo '<p>';
								the_excerpt();
							echo '</p>';
						}
					?>
			</div>
		</div>
	<!-- END Author Box Container -->
	</div>
	<?php
}

function epl_property_author_box_simple_grav() {
	include('author-meta.php');
	$author_style = get_option('epl_staff_link_to'); ?>
	
	<!-- Author Box Container -->
	<div class="epl-author-card author">
		<div class="entry-content">
			<div class="author-box author-image">
				<?php
					if (function_exists('get_avatar')) { 
						echo get_avatar( get_the_author_email(), '180' );
					}
				?>
			</div>
			
			<div class="author-box author-details">	
				<!--- Author Page Style --->
				<?php if ( $author_style == 1) { ?>
					<h5 class="entry-title"><a href="<?php the_permalink(); ?>"><? the_title(); ?></a></h5>
				<?php } else { ?>
					<h5 class="entry-title"><?php the_author_posts_link(); ?></h5>
				<?php } ?>
				
				<div class="author-position"><?php echo $position ?></div>
				<div class="author-contact">
					<?php if ( $mobile != '' ) { ?>
						<span class="label-mobile">Mobile </span><span class="mobile"><?php echo $mobile ?></span>
					<?php } ?>
				</div>
				<div class="author-social-buttons">
					<?php
						echo $i_email;
						echo $i_twitter;
						echo $i_facebook;
						echo $i_google;
						echo $i_linkedin;
						echo $i_skype;
					?>
				</div>			
			</div>
		</div>
	<!-- END Author Box Container -->
	</div>
	<?php
}

// Widget Sidebar Function
function epl_property_author_box_simple_card_tall( $d_image , $image , $d_icons , $d_bio) {
	include('author-meta.php');
	$author_image_type = get_option('epl_staff_image_type');
	$author_style = get_option('epl_staff_link_to'); ?>

	<!-- Author Box Container -->
	<div class="epl-author-card author">
		<div class="entry-content">
			<div class="author-box-tall author-image">
				<?php 
					if ( $author_image_type == 0 && function_exists('get_avatar') ) {
						echo get_avatar( get_the_author_email(), '180' ); 
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
								$directory_link = get_permalink();						
								if ( has_post_thumbnail() && $d_image == 'on' ) { 
									the_post_thumbnail( $image, array( 'class' => "$image index-thumbnail" ) );
								}
							}
						}
						wp_reset_postdata();
					}
				?>
			</div>
			
			<div class="author-box-tall author-details">
				<?php 
					if ( $author_style == 1) { ?> 
						<h5 class="entry-title"><a href="<?php echo $directory_link; ?>"><? echo $name; ?></a></h5>
					<?php } else { ?>
						<h5 class="entry-title"><?php the_author_posts_link(); ?></h5>
					<?php } ?>
					
					<div class="author-position"><?php echo $position ?></div>
					<div class="author-contact">
						<?php if ( $mobile != '' ) { ?>
							<span class="label-mobile">Mobile </span><span class="mobile"><?php echo $mobile ?></span>
						<?php } ?>
					</div>		
					<?php if ( $d_icons == 'on' ) { ?>
						<div class="author-social-buttons">
							<?php
								echo $i_email;
								echo $i_twitter;
								echo $i_facebook;
								echo $i_google;
								echo $i_linkedin;
								echo $i_skype;
							?>
						</div>
					<?php }
				
					if ( $d_bio == 'on' ) {
						echo '<p>';
							the_author_meta( 'description' );
						echo '</p>';
					
						if ( $author_style == 1) { ?> 
							<span class="bio-more"><a href="<?php echo $directory_link ?>">Read More</span></a>
						<?php } else { ?>
							<span class="bio-more"><a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>">Read More</a></span>
						<?php }
					}
				?>
			</div>	
		</div>
		<!-- END Author Box Container -->
	</div>
	<?php
}
