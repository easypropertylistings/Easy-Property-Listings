<?php get_header(); ?>

<section class="content">

	<?php get_template_part('inc/page-title'); ?>
	
	<div class="pad group">		
		
		<?php if ((category_description() != '') && !is_paged()) : ?>
			<div class="notebox">
				<?php echo category_description(); ?>
			</div>
		<?php endif; ?>
		
		<?php if ( have_posts() ) : ?>
			
			<div class="post-list group">
			
				<?php $i = 1; echo '<div class="post-row">'; while ( have_posts() ): the_post(); ?>
				<?php do_action( 'epl_property_blog' ); ?>
				<?php if($i % 2 == 0) { echo '</div><div class="post-row">'; } $i++; endwhile; echo '</div>'; ?>
			</div><!--/.post-list-->
		
			<?php get_template_part('inc/pagination'); ?>
			<?php do_action( 'epl_property_loop_end' ); ?>
		<?php endif; ?>
		
	</div><!--/.pad-->
	
</section><!--/.content-->

<?php get_sidebar(); ?>

<?php get_footer(); ?>