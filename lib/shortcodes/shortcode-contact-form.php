<?php
/**
 * SHORTCODE :: Contact Capture [contact_capture]
 *
 * @package     EPL
 * @subpackage  Shortcode
 * @copyright   Copyright (c) 2014, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.4
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// Only load on front
if ( is_admin() ) {
	return;
}
/**
 * This shortcode allows for you to capture contacts for listings
 */
function epl_contact_capture_form( $atts ) {

	if( !is_epl_post() ) {
		return;  // dont display contact capture form on post type other than EPL
	}
	
	global $property;

	$defaults =epl_contact_capture_get_widget_defaults();
	$attributes = shortcode_atts( $defaults, $atts );
	
	ob_start();
	$contact_form = new EPL_FORM_BUILDER( array('callback_action'	=>	'contact_capture_form') );

	$fields = array(
		array(
			'label'		=>	__('' , 'epl'),
			'class'		=>	'col-1 epl-inner-div',
			'id'		=>	'',
			'help'		=>	__('' , 'epl') . '<hr/>',
			'fields'	=>	epl_contact_capture_get_widget_fields()
		),

	);
	$contact_form->add_sections($fields);
	$contact_form->add_fields();
	echo '<div class="epl-contact-capture-form">';
	$contact_form->render_form();
	echo '</div>';
	return ob_get_clean();
}
add_shortcode( 'contact_capture', 'epl_contact_capture_form' );

function contact_capture_form_callback($form_data,$request) {

	$contact = new EPL_contact( $request['epl_contact_email'] );
	if ( empty( $contact->id ) ) {
	
		$contact_data = array(
			'name'			=>	$request['epl_contact_name'],
			'email'			=>	$request['epl_contact_email'],
		);
		if ( $contact->create( $contact_data ) ) {
			$contact->attach_listing( $request['epl_contact_listing_id'] );
			$contact->add_note( $request['epl_contact_note'],'epl_user_note',$request['epl_contact_listing_id'] );
		}
	} else {
	
		if ( $contact->update( array('name'	=>	$request['epl_contact_name'] ) ) ) {
			$contact->add_note( $request['epl_contact_note'],'epl_user_note',$request['epl_contact_listing_id'] );
			$contact->attach_listing( $request['epl_contact_listing_id'] );
		}
	}
	

}
add_action('epl_form_builder_contact_capture_form','contact_capture_form_callback',10,2);

