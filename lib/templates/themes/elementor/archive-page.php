<?php
/**
 * Archive Page wrapper template.
 *
 * Renders the page assigned as the "Listings Archive Page" inside the
 * theme's normal header/footer, in place of EPL's PHP archive/search/
 * taxonomy template. The real main query is never touched, so pagination,
 * `is_archive()`/`is_search()` and the canonical URL all remain correct;
 * only the template file is swapped.
 *
 * @package    EPL
 * @subpackage Templates/Themes/Elementor
 * @since      3.7.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$epl_archive_page_id = EPL_Elementor_Template_Router::get_active_archive_page_id();
$epl_archive_page    = $epl_archive_page_id ? get_post( $epl_archive_page_id ) : null;

echo '<div class="epl-elementor-archive-page">';

if ( $epl_archive_page ) {
	$epl_archive_document = isset( \Elementor\Plugin::$instance->documents ) ? \Elementor\Plugin::$instance->documents->get( $epl_archive_page_id ) : null;

	if ( $epl_archive_document && $epl_archive_document->is_built_with_elementor() ) {
		echo \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $epl_archive_page_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Elementor content is sanitised on save.
	} else {
		global $post;
		$epl_original_post = $post;
		$post               = $epl_archive_page; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- Restored below; the real archive/search/taxonomy query is untouched.
		setup_postdata( $post );

		echo apply_filters( 'the_content', $post->post_content ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Filtered through the standard the_content pipeline.

		$post = $epl_original_post; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- Restore the real archive/search/taxonomy post.
		if ( $post instanceof WP_Post ) {
			setup_postdata( $post );
		} else {
			wp_reset_postdata();
		}
	}
}

echo '</div>';

get_footer();
