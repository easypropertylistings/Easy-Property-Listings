<?php
/**
 * The Default Template for displaying all Easy Property Listings single posts with WordPress Themes
 *
 * @package EPL
 * @subpackage Templates/Themes/Divi
 * @since 1.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header(); ?>

<div id="main-content">
	<div class="container">
		<div id="content-area" class="clearfix <?php echo esc_attr( epl_template_class( 'divi', 'single' ) ); ?>">
			<div id="left-area">
			<?php
			while ( have_posts() ) :
				the_post();
				?>
				<?php
				$single_top = et_get_option( 'divi_integration_single_top' );
				if ( ! empty( $single_top ) && 'on' === et_get_option( 'divi_integrate_singletop_enable' ) ) {
					echo wp_kses_post( et_get_option( 'divi_integration_single_top' ) );
				}
				?>

				<article>

					<?php do_action( 'epl_property_single' ); ?>

					<?php
					if ( 'on' === et_get_option( 'divi_468_enable' ) ) {
						echo '<div class="et-single-post-ad">';
						$divi_adsense = et_get_option( 'divi_468_adsense' );
						if ( ! empty( $divi_adsense ) ) {
							echo wp_kses_post( et_get_option( 'divi_468_adsense' ) );
						} else {
							?>
							<a href="<?php echo esc_url( et_get_option( 'divi_468_url' ) ); ?>"><img src="<?php echo esc_attr( et_get_option( 'divi_468_image' ) ); ?>" alt="468" class="foursixeight" /></a>
							<?php
						}
						echo '</div> <!-- .et-single-post-ad -->';
					}
					?>

					<?php
					if ( ( comments_open() || get_comments_number() ) && 'on' === et_get_option( 'divi_show_postcomments', 'on' ) ) {
						comments_template( '', true );
					}
					?>
				</article> <!-- .et_pb_post -->

				<?php
				$single_bottom = et_get_option( 'divi_integration_single_bottom' );
				if ( ! empty( $single_bottom ) && 'on' === et_get_option( 'divi_integrate_singlebottom_enable' ) ) {
					echo wp_kses_post( et_get_option( 'divi_integration_single_bottom' ) ); }
				?>
			<?php endwhile; ?>
			</div> <!-- #left-area -->

			<?php get_sidebar(); ?>
		</div> <!-- #content-area -->
	</div> <!-- .container -->
</div> <!-- #main-content -->

<?php get_footer(); ?>
