<?php
/*
 * TEMPLATE - Testimonial Single
 */
 
function epl_property_testimonial_single() { ?>
	<div id="post-<?php the_ID(); ?>" <?php post_class( 'epl-testimonial-single' ); ?>>
		<?php
			$testimonial_suburb = get_post_custom('testimonial_suburb');
			$suburb = $testimonial_suburb['testimonial_suburb'][0]['name'];
		?>

		<div class="entry-header">
			<h2><?php echo $suburb; ?></h2>
		</div><!-- .entry-header -->

		<div class="entry-summary">
			<blockquote>			
				<?php
					if ( has_post_thumbnail() ) : ?>
						<div class="entry-thumbnail">
							<?php the_post_thumbnail( 'medium' , array('class' => 'alignright')); ?>
						</div>
					<?php endif;
					
					the_content();
					
					echo '<h3>';
						the_title();
					echo '</h3>';
				?>
			</blockquote>
		</div>
	</div>
	<!-- end .post -->
	<?php
}

/*
 * TEMPLATE - Testimonial Archive
 */ 	
function epl_property_testimonial_archive() { ?>
	<div id="post-<?php the_ID(); ?>" <?php post_class( 'epl-testimonial-single' ); ?>>
		<?php 
			$testimonial_suburb = get_post_custom('testimonial_suburb');
			$suburb = $testimonial_suburb['testimonial_suburb'][0]['name'];
		?>

		<div class="entry-header">
			<h2><?php echo $suburb; ?></h2>
		</div><!-- .entry-header -->

		<div class="entry-summary">
			<blockquote>			
				<?php
					if ( has_post_thumbnail() ) : ?>
						<div class="entry-thumbnail">
							<?php the_post_thumbnail( 'medium' , array('class' => 'alignright')); ?>
						</div>
					<?php endif;
					
					the_excerpt();
				?>
				<a href="<?php the_permalink(); ?>"><h3><?php the_title(); ?></h3></a>
			</blockquote>
		</div>
	</div>
	<!-- end .post -->
	<?php
}

/*
 * TEMPLATE - Testimonial Single
 */ 	
function epl_property_testimonial_widget($location,$display,$d_align,$d_image = 'thumbnail') { ?>		
	<div id="post-<?php the_ID(); ?>" <?php post_class( 'epl-testimonial-widget epl-clearfix' ); ?>>
		<?php
			$testimonial_suburb = get_post_custom('testimonial_suburb');
			$suburb = $testimonial_suburb['testimonial_suburb'][0]['name'];
			if ($location == 'on') { ?>
				<div class="entry-header">
					<h2><?php echo $suburb; ?></h2>
				</div><!-- .entry-header -->
				<?php
			}
		?>
		
		<div class="entry-summary">
			<?php
				if ( has_post_thumbnail() && $display == 'on') : ?>
					<div class="entry-thumbnail">
						<?php the_post_thumbnail( $d_image , array('class' => $d_align)); ?>
					</div>
					<?php
				endif;
				the_excerpt();
			?>
			<div class="testimonial-name"><em><strong>- <?php the_title(); ?> -</strong></em></div>
		</div>
	</div>
	<!-- end .post -->
	<?php
}
