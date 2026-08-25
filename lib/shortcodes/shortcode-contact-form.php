<?php
/**
 * SHORTCODE :: Contact Capture [listing_contact]
 *
 * @package     EPL
 * @subpackage  Shortcode/ContactForm
 * @copyright   Copyright (c) 2019, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Listing Contact Shortcode [listing_contact] This shortcode allows for you to capture contacts for listings
 *
 * @param array $atts Shortcode array of options.
 *
 * @return false|string
 * @since 3.0
 */
function epl_contact_capture_form( $atts ) {
	$defaults   = epl_contact_capture_get_widget_defaults();
	$attributes = shortcode_atts( $defaults, $atts );
	$fields     = epl_contact_capture_get_widget_fields( $attributes );

	if ( isset( $attributes['submit'] ) && ! empty( $attributes['submit'] ) ) {
		foreach ( $fields as &$field ) {
			if ( 'epl_contact_submit' === $field['name'] ) {
				$field['value'] = $attributes['submit'];
			}
		}
	}

	ob_start();
	$contact_form = new EPL_FORM_BUILDER( array( 'callback_action' => 'contact_capture_form' ) );

	$fields = array(
		array(
			'label'  => '',
			'class'  => 'col-1 epl-inner-div',
			'id'     => '',
			'help'   => '',
			'fields' => $fields,
		),

	);
	$contact_form->add_sections( $fields );
	$contact_form->add_fields();
	$contact_form->add_nonce( 'epl_contact_widget' );
	echo '<div class="epl-contact-capture-form">';?>
	<div class="epl-contact-capture-form-desc">
		<?php
			echo isset( $atts['description'] ) ? esc_html( $atts['description'] ) : '';
		?>
	</div>
	<?php

	$contact_form->render_form();
	echo '</div>';
	return ob_get_clean();
}
add_shortcode( 'epl_contact_form', 'epl_contact_capture_form' );
add_shortcode( 'listing_contact', 'epl_contact_capture_form' );


/**
 * Contact Form Callback
 *
 * @param array $form_data Array of form data.
 * @param array $request Request from url for antispam check.
 *
 * @since 3.0
 * @since 3.5.25 Added nonce check and shared request validation.
 */
function epl_contact_capture_form_callback( $form_data, $request ) {
	if (
		! isset( $request['epl_contact_widget'] ) ||
		! wp_verify_nonce( sanitize_text_field( wp_unslash( $request['epl_contact_widget'] ) ), 'epl_contact_widget' )
	) {
		return;
	}

	epl_process_contact_capture_request( $request );
}
add_action( 'epl_form_builder_contact_capture_form', 'epl_contact_capture_form_callback', 10, 2 );
