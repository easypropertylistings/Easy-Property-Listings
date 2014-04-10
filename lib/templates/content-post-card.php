<?php
// TEMPLATE - Recent Posts Cards	
function epl_property_post_cards() { ?>
	<div id="post-<?php the_ID(); ?>" <?php post_class( 'epl-post-card' ); ?>>
		<div class="entry-header">
			<?php if ( has_post_thumbnail() ) : ?>
				<div class="entry-thumbnail">
					<?php the_post_thumbnail( 'thumbnail' ); ?>
				</div>
			<?php endif; ?>
		</div><!-- .entry-header -->

		<div class="entry-content">
			<h5><a href="<?php the_permalink() ?>"><?php the_title() ?></a></h5>
		</div>
	</div>
	<!-- end .post -->
	<?php
} 
