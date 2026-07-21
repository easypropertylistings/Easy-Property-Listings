<?php
/**
 * The Default Template for displaying all Easy Property Listings single posts with WordPress Themes
 *
 * @package EPL
 * @subpackage Templates/Themes/Default
 * @since 1.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

 ?>
<div id="primary" class="site-content content-area epl-single-default <?php echo esc_attr( epl_get_active_theme_name() ); ?>">
	<section class="content">
		<div id="content" class="pad" role="main">
			<?php
			global $post;

			if ( $post instanceof WP_Post ) :
				setup_postdata( $post );
				do_action( 'the_post', $post );
				?>
				<div class="loop">
					<div class="loop-content <?php echo esc_attr( epl_template_class( 'default', 'single' ) ); ?>">
						<?php do_action( 'epl_property_single' ); ?>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</section>
</div>
