<?php
/**
 * The Template for displaying all Easy Property Listings single posts with the Genesis Theme Framework
 *
 * @package EPL
 * @subpackage Templates/Themes/Genesis
 * @since 1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

get_header();
do_action( 'genesis_before_content_sidebar_wrap' );

genesis_markup( array(
		'html5'   => '<div %s>',
		'xhtml'   => '<div id="content-sidebar-wrap">',
		'context' => 'content-sidebar-wrap',
	) );

		do_action( 'genesis_before_content' );
		genesis_markup( array(
			'html5'   => '<main %s>',
			'xhtml'   => '<div id="content" class="hfeed">',
			'context' => 'content',
		) );
			do_action( 'genesis_before_loop' );
				?>
				 <div id="primary">
					 <div id="content" role="main">
						<?php
						if ( have_posts() ) : ?>
							<div class="loop">
								<div class="loop-content <?php echo epl_template_class( 'genesis', 'single' ); ?>">
									<?php
										while ( have_posts() ) : // The Loop
											the_post();

											do_action('epl_property_single');

											comments_template(); // include comments template
										endwhile; // end of one post
									?>
								</div>
							</div>
						<?php endif; ?>
					</div>
				</div>
				<?php
			do_action( 'genesis_after_loop' );
		genesis_markup( array(
			'html5' => '</main>', //* end .content
			'xhtml' => '</div>', //* end #content
		) );
		do_action( 'genesis_after_content' );

	echo '</div>'; //* end .content-sidebar-wrap or #content-sidebar-wrap
do_action( 'genesis_after_content_sidebar_wrap' );
get_footer();
