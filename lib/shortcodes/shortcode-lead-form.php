<?php
/**
 * SHORTCODE :: Lead Capture [lead_capture]
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
 * This shortcode allows for you to capture leads for listings
 */
function epl_lead_capture_callback( $atts ) {

	if( !is_epl_post() ) {
		return;  // dont display lead capture form on post type other than EPL
	}
	
	global $property;
	
	//$attributes = shortcode_atts( array(), $atts );
	
	ob_start();
	$lead_form = new EPL_FORM_BUILDER( array('callback_action'	=>	'lead_capture_form') );
	
	$lead_form->add_fields(
		array(
			array(
				'label'	=>	__('Name','epl'),
				'name'	=>	'epl_lead_name',
				'id'	=>	'epl_lead_name',
				'type'	=>	'text'
			),
			array(
				'label'	=>	__('Email','epl'),
				'name'	=>	'epl_lead_email',
				'id'	=>	'epl_lead_email',
				'type'	=>	'email'
			),
			array(
				'label'	=>	__('Message','epl'),
				'name'	=>	'epl_lead_note',
				'id'	=>	'epl_lead_note',
				'type'	=>	'textarea'
			),
			array(
				'name'	=>	'epl_lead_listing_id',
				'id'	=>	'epl_lead_listing_id',
				'type'	=>	'hidden',
				'value'	=>	$property->post->ID
			),
			array(
				
				'name'	=>	'epl_lead_submit',
				'id'	=>	'epl_lead_submit',
				'type'	=>	'submit',
				'value'	=>	__('Subscribe','epl')
			),
			
		)
	);
	$lead_form->render_form();
	
	
	return ob_get_clean();
}
add_shortcode( 'lead_capture', 'epl_lead_capture_callback' );

function lead_capture_form_callback($form_data,$request) {
	
	$lead = new EPL_lead( $request['epl_lead_email'] );
	
	if ( empty( $lead->id ) ) {
	
		$lead_data = array(
			'name'			=>	$request['epl_lead_name'],
			'email'			=>	$request['epl_lead_email'],
		);
		
		$user_id       = email_exists( $email );
		
		if ( false !== $user_id ) {
			$lead_data['user_id'] = $user_id;
		}

		if ( $lead->create( $lead_data ) ) {
			$lead->attach_listing( $request['epl_lead_listing_id'] );
			$lead->add_note( $request['epl_lead_note'],'epl_user_note',$request['epl_lead_listing_id'] );
		}
	} else {
	
		if ( $lead->update( array('name'	=>	$request['epl_lead_name'] ) ) ) {
			$lead->add_note( $request['epl_lead_note'],'epl_user_note',$request['epl_lead_listing_id'] );
			$lead->attach_listing( $request['epl_lead_listing_id'] );
		}
	}
	

}
add_action('epl_form_builder_lead_capture_form','lead_capture_form_Callback',10,2);

