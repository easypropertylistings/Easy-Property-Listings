<?php
/**
 * SHORTCODE :: Listing Results [listing_results]
 *
 * @package     EPL
 * @subpackage  Shortcode/ListingResults
 * @copyright   Copyright (c) 2023, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.5.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Is this page showing listing results?
 *
 * True on an EPL search, a listing type archive, a location, feature or business category
 * archive, or any archive whose listings are EPL listings. Reads the page WordPress is
 * showing, so it still works inside page builder templates (Elementor switches the current
 * post to its template while rendering).
 *
 * @since 3.6 New: used by [listing_results].
 *
 * @return bool
 */
function epl_is_listing_results_page() {
	global $wp_the_query;

	$is_results = ( function_exists( 'epl_is_search' ) && true === epl_is_search() ) || is_epl_core_post();

	if ( ! $is_results && $wp_the_query instanceof WP_Query && ! $wp_the_query->is_singular() ) {
		$is_results = $wp_the_query->is_post_type_archive( epl_get_core_post_types() )
			|| $wp_the_query->is_tax( array( 'location', 'tax_feature', 'tax_business_listing' ) )
			|| ( $wp_the_query->is_archive() && ! empty( $wp_the_query->posts ) && is_epl_core_post( get_post_type( $wp_the_query->posts[0] ) ) );
	}

	/**
	 * Filters whether [listing_results] treats this page as listing results.
	 *
	 * @since 3.6
	 *
	 * @param bool $is_results True on EPL searches and listing archives.
	 */
	return (bool) apply_filters( 'epl_is_listing_results_page', $is_results );
}

/**
 * Is a page builder showing its editor or preview?
 *
 * Elementor, Divi, WPBakery, Beaver Builder, Bricks and Oxygen.
 *
 * @since 3.6 New: so [listing_results] shows sample listings while a template is designed.
 *
 * @return bool
 */
function epl_is_page_builder_preview() {
	// phpcs:disable WordPress.Security.NonceVerification
	$preview = isset( $_GET['elementor-preview'] ) || isset( $_GET['et_fb'] ) || isset( $_GET['vc_editable'] ) || isset( $_GET['fl_builder'] ) || isset( $_GET['ct_builder'] )
		|| ( isset( $_GET['bricks'] ) && 'run' === $_GET['bricks'] );
	// Elementor renders widgets over AJAX in its editor.
	if ( ! $preview && wp_doing_ajax() && isset( $_REQUEST['action'] ) && 'elementor_ajax' === $_REQUEST['action'] ) {
		$preview = true;
	}
	// phpcs:enable WordPress.Security.NonceVerification
	if ( ! $preview && class_exists( '\Elementor\Plugin' ) && isset( \Elementor\Plugin::$instance ) ) {
		$elementor = \Elementor\Plugin::$instance;
		// The editor page renders each widget once when it opens (post.php?action=elementor).
		if ( isset( $elementor->editor ) && is_object( $elementor->editor ) && method_exists( $elementor->editor, 'is_edit_mode' ) && $elementor->editor->is_edit_mode() ) {
			$preview = true;
		} elseif ( isset( $elementor->preview ) && is_object( $elementor->preview ) && method_exists( $elementor->preview, 'is_preview_mode' ) && $elementor->preview->is_preview_mode() ) {
			$preview = true;
		}
	}

	/**
	 * Filters whether a page builder editor or preview is showing.
	 *
	 * @since 3.6
	 *
	 * @param bool $preview True in a page builder editor or preview.
	 */
	return (bool) apply_filters( 'epl_is_page_builder_preview', $preview );
}

/**
 * Page Builder Archive / Search Results Loop
 * Load the Easy Property Listings Loop using a shortcode. For use in Page Builders like Elementor, Divi, WP Bakery, Visual Composer.
 *
 * On search results and listing archives it shows the page's listings. In a page builder
 * editor it shows the latest listings, so the template can be designed.
 *
 * @param array $atts Shortcode options.
 *
 * @return false|string
 * @since 3.5.0
 * @since 3.6 Works in Elementor templates (it showed nothing when the template switched the current post) and on location, feature and empty archives. Shows the latest listings in page builder editors. New options: template, class, title and preview_limit.
 */
function epl_listing_results_loop_callback( $atts ) {

	$attributes = shortcode_atts(
		array(
			'tools_top'     => 'off', // Tools before the loop like Sorter and Grid on or off.
			'tools_bottom'  => 'off', // Tools after the loop like pagination on or off.
			'pagination'    => 'on', // Enable or disable pagination.
			'template'      => '', // Listing template, for example card, slim or table, as in [listing].
			'class'         => '', // Extra wrapper class.
			'title'         => 'off', // Show the archive or search title.
			'preview_limit' => '', // Listings to show in a page builder editor. Default: Settings > Reading.
		),
		$atts
	);

	$preview = false;
	$query   = null;

	if ( epl_is_listing_results_page() ) {
		global $wp_query;
		$query = $wp_query;
	} elseif ( epl_is_page_builder_preview() ) {
		$preview = true;
		$query   = new WP_Query(
			array(
				'post_type'      => array_keys( epl_get_active_post_types() ),
				'post_status'    => 'publish',
				'posts_per_page' => '' !== $attributes['preview_limit'] ? (int) $attributes['preview_limit'] : (int) get_option( 'posts_per_page' ),
			)
		);
	}

	if ( null === $query ) {
		return '';
	}

	$template      = str_replace( '_', '-', sanitize_key( $attributes['template'] ) );
	$wrapper_class = epl_template_class( $template, 'archive' );
	if ( '' !== $attributes['class'] ) {
		$wrapper_class .= ' ' . $attributes['class'];
	}

	ob_start();

	if ( $query->have_posts() ) :
		?>

		<?php if ( 'on' === $attributes['title'] ) : ?>
			<header class="archive-header entry-header loop-header">
				<h4 class="archive-title loop-title">
					<?php do_action( 'epl_the_archive_title' ); ?>
				</h4>
			</header>
		<?php endif; ?>

		<div class="<?php echo esc_attr( $wrapper_class ); ?>">
			<?php
			if ( 'on' === $attributes['tools_top'] ) {
				do_action( 'epl_property_loop_start', $attributes );
			}
			?>

			<?php
			while ( $query->have_posts() ) :
				$query->the_post();
				if ( '' === $template ) {
					do_action( 'epl_property_blog' );
				} else {
					epl_property_blog( $template );
				}
			endwhile;
			?>

			<?php
			if ( 'on' === $attributes['tools_bottom'] ) {
				do_action( 'epl_property_loop_end' );
			}
			?>
		</div>

		<div class="loop-footer">
			<!-- Previous/Next page navigation -->
			<div class="loop-utility clearfix">
				<?php
				if ( 'on' === $attributes['pagination'] ) {
					if ( $preview ) {
						do_action( 'epl_pagination', array( 'query' => $query ) );
					} else {
						do_action( 'epl_pagination' );
					}
				}
				?>
			</div>
		</div>

	<?php else : ?>

		<?php do_action( 'epl_property_search_not_found' ); ?>

		<?php
	endif;

	if ( $preview ) {
		wp_reset_postdata();
	}

	return ob_get_clean();
}
add_shortcode( 'listing_results', 'epl_listing_results_loop_callback' );
