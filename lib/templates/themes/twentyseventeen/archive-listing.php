<?php
/**
 * The template for displaying archive pages
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage Twenty_Seventeen
 * @since 1.0
 * @version 1.0
 */

get_header(); ?>

<div class="wrap">

	<?php if ( have_posts() ) : ?>
		<header class="page-header">
			<?php
				the_archive_title( '<h1 class="page-title">', '</h1>' );
				the_archive_description( '<div class="taxonomy-description">', '</div>' );
			?>
		</header><!-- .page-header -->
	<?php endif; ?>

	<div id="primary" class="content-area <?php echo esc_attr( epl_get_active_theme_name() ); ?>">
		<main id="main" class="site-main <?php echo esc_attr( epl_template_class( 'twentyseventeen', 'archive' ) ); ?>" role="main">

		<?php
		if ( have_posts() ) :
			?>
			<?php
			do_action( 'epl_property_loop_start' );
			/* Start the Loop */
			while ( have_posts() ) :
				the_post();

				do_action( 'epl_property_blog' );

			endwhile;

			do_action( 'epl_property_loop_end' );

			// Previous/next page navigation.
			do_action( 'epl_pagination' );

			else :

				?>
			<div class="hentry">
				<?php do_action( 'epl_property_search_not_found' ); ?>
			</div>
				<?php

		endif;
			?>

		</main><!-- #main -->
	</div><!-- #primary -->
	<?php get_sidebar(); ?>
</div><!-- .wrap -->

<?php
get_footer();
