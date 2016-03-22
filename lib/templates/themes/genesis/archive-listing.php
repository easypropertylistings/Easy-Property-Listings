<?php
/**
 * The Default Template for displaying all Easy Property Listings archive/loop posts with the Genesis Theme Framework
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
								<div class="loop-header">
									<h4 class="loop-title">
										<?php do_action( 'epl_the_archive_title' ); ?>
									</h4>
								</div>

								<div class="loop-content">
									<?php do_action( 'epl_property_loop_start' ); ?>
									<?php while ( have_posts() ) : // The Loop
											the_post();
											do_action('epl_property_blog');
										endwhile; // end of one post
									?>
									<?php do_action( 'epl_property_loop_end' ); ?>
								</div>

								<div class="loop-footer">
									<!-- Previous/Next page navigation -->
									<div class="loop-utility clearfix">
										<?php do_action('epl_pagination'); ?>
									</div>
								</div>
							</div>
						<?php
						else : ?>
							<div class="hentry">
								<div class="entry-header clearfix">
									<h3 class="entry-title"><?php apply_filters( 'epl_property_search_not_found_title' , _e('Listing not Found', 'easy-property-listings') ); ?></h3>
								</div>

								<div class="entry-content clearfix">
									<p><?php apply_filters( 'epl_property_search_not_found_message' , _e('Listing not found, expand your search criteria and try again.', 'easy-property-listings') ); ?></p>
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
