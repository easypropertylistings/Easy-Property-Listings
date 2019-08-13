<?php
/**
 * SHORTCODE :: Listing Meta Doc [listing_meta_doc]
 *
 * @package     EPL
 * @subpackage  Shortcode/Listing Meta Doc
 * @copyright   Copyright (c) 2019, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.3.1
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Listing Meta Doc [listing_meta_doc]
 *
 * This shortcode generates meta fields documentation for all EPL post types
 * [listing_meta_doc post_type="property"] OR [listing_meta_doc] for all post types.
 *
 * @param array $atts Shortcode attributes.
 *
 * @return false|string
 * @since       3.3.3
 */
function epl_shortcode_listing_meta_doc_callback( $atts ) {

	$atts = shortcode_atts(
		array(
			'post_type' => '',
		),
		$atts
	);

	$meta_boxes = epl_get_meta_boxes();
	ob_start();
	?>
		<style>
			ul.striped-list {
				list-style-type: none;
				margin: 0;
				padding: 0;
			}
			ul.striped-list > li:nth-of-type(odd) {
				background-color: rgba(78, 168, 234, 0.1) ;
			}
			ul.striped-list > li {
				border-bottom: 1px solid rgb(221,221,221);
				padding: 6px;
			}
			ul.striped-list > li:last-child {
				border-bottom: none;
			}
			.epl-meta-opt-list {
				margin-left: 35px !important;
			}
		</style>
	<?php
	if ( ! empty( $meta_boxes ) ) {
		foreach ( $meta_boxes as $epl_meta_box ) {

			$epl_meta_box['post_type'] = (array) $epl_meta_box['post_type'];

			if ( ! empty( $atts['post_type'] ) && ! in_array( $atts['post_type'], $epl_meta_box['post_type'], true ) ) {
				continue;
			}

			// Box heading.
			echo '<h4>' . esc_html( $epl_meta_box['label'] ) . '</h4>';

			if ( ! empty( $epl_meta_box['groups'] ) ) {
				foreach ( $epl_meta_box['groups'] as $group ) {

					echo '<div class="epl-meta-group-wrap">';

					// Group heading.
					if ( ! empty( $group['label'] ) ) {
						echo '<h4>' . esc_html( $group['label'] ) . '</h4>';
					}

					$fields = $group['fields'];
					$fields = array_filter( $fields );
					if ( ! empty( $fields ) ) {

						echo '<ul class="striped-list epl-meta-fields-list">';

						foreach ( $fields as $field ) {

							if ( isset( $field['exclude'] ) && ! empty( $field['exclude'] ) ) {
								if ( ! empty( $atts['post_type'] ) && in_array( $atts['post_type'], $field['exclude'], true ) ) {
									continue;
								}
							}

							if ( isset( $field['include'] ) && ! empty( $field['include'] ) ) {
								if ( ! empty( $atts['post_type'] ) && ! in_array( $atts['post_type'], $field['include'], true ) ) {
									continue;
								}
							}

							echo '<li class="epl-meta-field-item">';

							switch ( $field['type'] ) {

								case 'text':
									echo '<strong>' . esc_attr( $field['name'] ) . '</strong><span class="epl-meta-sep"> :: </span>' . esc_attr( $field['type'] );
									break;

								case 'checkbox_single':
									echo '<strong>' . esc_attr( $field['name'] ) . '</strong><span class="epl-meta-sep"> :: </span> "yes" or "no"';
									break;

								case 'select':
									echo '<strong>' . esc_attr( $field['name'] ) . '</strong><span class="epl-meta-sep"> :: </span>' . esc_attr( $field['type'] ) . ' (drop down options) ';

									echo '<ul class="epl-meta-opt-list">';
									foreach ( $field['opts'] as $opt_key => $opt_label ) {
										$label = is_array( $opt_label ) ? $opt_label['label'] : $opt_label;
										echo '<li class="epl-meta-opt-item">';
											echo esc_attr( $label );
										echo '</li>';
									}
									echo '</ul>';
									break;

								default:
									echo '<strong>' . esc_attr( $field['name'] ) . '</strong><span class="epl-meta-sep"> :: </span>' . esc_attr( $field['type'] );
									break;
							}
							echo '</li>';
						}
						echo '</ul>';
					}
					echo '</div><hr>';
				}
			}
		}
	}
	return ob_get_clean();
}
add_shortcode( 'listing_meta_doc', 'epl_shortcode_listing_meta_doc_callback' );
