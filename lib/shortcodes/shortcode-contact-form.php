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
function epl_contact_capture_callback( $atts ) {

	if( !is_epl_post() ) {
		return;  // dont display contact capture form on post type other than EPL
	}
	
	global $property;
	
	//$attributes = shortcode_atts( array(), $atts );
	
	ob_start();
	$contact_form = new EPL_FORM_BUILDER( array('callback_action'	=>	'contact_capture_form') );
	
	$contact_form->add_fields(
		array(
			array(
				'label'	=>	__('Name','epl'),
				'name'	=>	'epl_contact_name',
				'id'	=>	'epl_contact_name',
				'type'	=>	'text'
			),
			array(
				'label'	=>	__('Email','epl'),
				'name'	=>	'epl_contact_email',
				'id'	=>	'epl_contact_email',
				'type'	=>	'email'
			),
			array(
				'label'	=>	__('Message','epl'),
				'name'	=>	'epl_contact_note',
				'id'	=>	'epl_contact_note',
				'type'	=>	'textarea'
			),
			array(
				'name'	=>	'epl_contact_listing_id',
				'id'	=>	'epl_contact_listing_id',
				'type'	=>	'hidden',
				'value'	=>	$property->post->ID
			),
			array(
				
				'name'	=>	'epl_contact_submit',
				'id'	=>	'epl_contact_submit',
				'type'	=>	'submit',
				'value'	=>	__('Subscribe','epl')
			),
			
		)
	);
	$contact_form->render_form();
	
	
	return ob_get_clean();
}
add_shortcode( 'contact_capture', 'epl_contact_capture_callback' );

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
add_action('epl_form_builder_contact_capture_form','contact_capture_form_Callback',10,2);

