<?php
/**
 * The Default Template for displaying all Easy Property Listings single posts with the Avada Theme
 *
 * @package EPL
 * @subpackage Templates/Themes/Avada
 * @updated 3.4.41
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $post;
?>
<?php get_header(); ?>

<section id="content" style="<?php esc_attr_e( apply_filters( 'awb_content_tag_style', '' ) ); ?>">
	<?php if ( fusion_get_option( 'blog_pn_nav' ) ) : ?>
		<div class="single-navigation clearfix">
			<?php previous_post_link( '%link', esc_attr__( 'Previous', 'Avada' ) ); ?>
			<?php next_post_link( '%link', esc_attr__( 'Next', 'Avada' ) ); ?>
		</div>
	<?php endif; ?>

	<?php while ( have_posts() ) : ?>
		<?php the_post(); ?>
		
		<?php do_action( 'epl_property_single' ); ?>
		
	<?php endwhile; ?>
</section>
<?php do_action( 'avada_after_content' ); ?>
<?php get_footer(); ?>
