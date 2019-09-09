<?php
/**
 * Listing Search Commercial Shortcode Default View
 *
 * @package     EPL
 * @subpackage  Shortcodes/Templates
 * @copyright   Copyright (c) 2019, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.0
 * @since       3.4.0 Security fixes as per WP standards
 * @since       3.4.1 Fixed search templates error : ignoring field added via filters
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// phpcs:disable WordPress.WP.GlobalVariablesOverride
// phpcs:disable WordPress.Security.NonceVerification

/**
 * Listing Search Commercial Shortcode Default View
 *
 * @var array  $atts    Shortcode attributes.
 */
foreach ( $atts as $atts_key => $atts_val ) {

	if ( is_array( $atts_val ) ) {
		$atts_val = epl_array_map_recursive( 'sanitize_text_field', $atts_val );
	} else {
		${$atts_key} = sanitize_text_field( $atts_val );
	}
}

$selected_post_types = $atts['post_type'];
$get_data            = epl_array_map_recursive( 'sanitize_text_field', $_GET );

/** Overwrite Atts with Get data, if set */

foreach ( $get_data as $get_key => $get_val ) {

	if ( ! empty( $get_data[ $get_key ] ) ) {
		${$get_key} = $get_val;
	}
}

$queried_post_type = isset( $get_data['post_type'] ) ? sanitize_text_field( $get_data['post_type'] ) : 'commercial';

if ( ! is_array( $selected_post_types ) ) {
	$selected_post_types = explode( ',', $selected_post_types );
	$selected_post_types = array_map( 'trim', $selected_post_types );
}

global $epl_settings;

$tabcounter = 1;
if ( ! empty( $selected_post_types ) ) :
	if ( count( $selected_post_types ) > 1 ) :
		echo "<ul class='epl-search-tabs property_search-tabs epl-search-" . esc_attr( $style ) . "'>";
		foreach ( $selected_post_types as $post_type ) :

			if ( isset( $get_data['action'] ) && 'epl_search' === $get_data['action'] ) {
				if ( $queried_post_type === $post_type ) {
					$is_sb_current = 'epl-sb-current';
				} else {
					$is_sb_current = '';
				}
			} else {
				$is_sb_current = 1 === $tabcounter ? 'epl-sb-current' : '';
			}
			$post_type_label = isset( $epl_settings[ 'widget_label_' . $post_type ] ) ? $epl_settings[ 'widget_label_' . $post_type ] : $post_type;
			echo '<li data-tab="epl_ps_tab_' . esc_attr( $tabcounter ) . '" class="tab-link ' . esc_attr( $is_sb_current ) . '">' . esc_attr( $post_type_label ) . '</li>';
			$tabcounter++;

		endforeach;
		echo '</ul>';
	endif;
	?>
	<div class="epl-search-forms-wrapper epl-search-<?php echo esc_attr( $style ); ?>">
	<?php
	$tabcounter = 1; // reset tab counter.

	foreach ( $selected_post_types as $post_type ) :

		if ( isset( $get_data['action'] ) && 'epl_search' === $get_data['action'] ) {
			if ( $queried_post_type === $post_type ) {
				$is_sb_current = 'epl-sb-current';
			} else {
				$is_sb_current = '';
			}
		} else {
			$is_sb_current = 1 === $tabcounter ? 'epl-sb-current' : '';
		}
		?>
		<div class="epl-search-form <?php echo esc_attr( $is_sb_current ); ?>" id="epl_ps_tab_<?php echo esc_attr( $tabcounter ); ?>">
		<?php
		if ( isset( $show_title ) && 'true' === $show_title ) {
			if ( ! empty( $title ) ) {
				?>
					<h3><?php echo esc_attr( $title ); ?></h3>
					<?php
			}
		}
		?>
		<form method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
			<input type="hidden" name="action" value="epl_search" />
			<?php
				$epl_frontend_fields = epl_listing_search_commercial_widget_fields_frontend( $post_type, $property_status );
			foreach ( $epl_frontend_fields as &$epl_frontend_field ) {

				if ( 'property_status' === $epl_frontend_field['key'] && 'on' === $show_property_status_frontend ) {
					$epl_frontend_field['type']   = 'text';
					$epl_frontend_field['config'] = 'on';
				}

				if ( 'search_house_category' === $epl_frontend_field['key'] && isset( $house_category_multiple ) && 'on' === $house_category_multiple ) {
					$epl_frontend_field['multiple'] = true;
					$epl_frontend_field['query']    = array(
						'query'   => 'meta',
						'compare' => 'IN',
					);
				}

				$config = isset( ${$epl_frontend_field['key']} ) ? ${$epl_frontend_field['key']} : '';
				if ( empty( $config ) && isset( $epl_frontend_field['config'] ) ) {
					$config = $epl_frontend_field['config'];
				}
				$value = isset( ${$epl_frontend_field['meta_key']} ) ? ${$epl_frontend_field['meta_key']} : '';
				epl_widget_render_frontend_fields( $epl_frontend_field, $config, $value, $post_type, $property_status );
			}
			?>
				<div class="epl-search-submit-row epl-search-submit property-type-search">
					<input type="submit" value="<?php echo ! empty( $submit_label ) ? esc_attr( $submit_label ) : esc_html__( 'Search', 'easy-property-listings' ); ?>" class="epl-search-btn" />
				</div>
			</form>
			</div>
		<?php
		$tabcounter++;
	endforeach;
	?>
	</div>
	<?php endif; ?>
