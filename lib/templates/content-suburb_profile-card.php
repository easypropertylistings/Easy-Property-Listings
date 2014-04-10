<?php
/*
 * Suburb Function for paged card display
 */

function epl_suburb_blog() {
	$meta = get_post_custom(); ?>

	<div id="post-<?php the_ID(); ?>" <?php post_class('suburb-blog clearfix'); ?>>				
		<div id="epl-suburb-blog" class="suburb-blog-wrapper-container">		
			<div class="entry-header">
				<h3 class="entry-title clearfix"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
			</div>
		
			<div class="entry-content">
				<div class="suburb-box property-box property-box-left property-content">
					<?php the_excerpt(); ?>
				</div>
				
				<?php if ( has_post_thumbnail() ) { ?>
					<div class="suburb-box suburb-box-right property-box property-box-right property-featured-image-wrapper">
						<a href="<?php the_permalink(); ?>">
							<?php //the_post_thumbnail( 'it-teaser-thumb', array( 'class' => 'teaser-left-thumb' ) ); ?>
							<?php the_post_thumbnail( 'medium' ); ?>
							<?php //the_post_thumbnail( array(600,600) ); ?>
						</a>
					</div>
				<?php } ?>
			</div>
		</div>
	</div>
	<?php
}

function epl_suburb_card( $d_image ='thumbnail' ) {
	//$meta = get_post_custom(); ?>

	<div id="post-<?php the_ID(); ?>" <?php post_class('suburb-card'); ?>>
		<div class="entry-header">
			<?php if ( has_post_thumbnail() ) : ?>
				<div class="epl-img-widget">
					<a href="<?php the_permalink(); ?>">
						<?php the_post_thumbnail( $d_image ); ?>
					</a>
				</div>
			<?php endif; ?>	
		</div>
	
		<div class="entry-content">
			<h5><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h5>
			<?php the_excerpt(); ?>
		</div>
	</div>
	<?php
}
