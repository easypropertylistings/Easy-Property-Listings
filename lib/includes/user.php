<?php
/**
 * User Profile 
 *
 * @package     EPL
 * @subpackage  Admin/Author
 * @copyright   Copyright (c) 2014, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// Only load in admin
if( !is_admin() ) {
	return;
}

/**
 * Add Additional Contact methods to author pages. These links 
 * are used in the author widgets and profile boxes
 *
 * @since 1.0
 */
function epl_property_admin_contact ( $contactmethods ) {
	$contactmethods['mobile']	= __('Mobile', 'epl');
	$contactmethods['skype']	= __('Skype', 'epl');
	$contactmethods['twitter']	= __('Twitter', 'epl');
	$contactmethods['facebook']	= __('Facebook', 'epl');
	$contactmethods['google']	= __('Google Plus', 'epl');
	$contactmethods['linkedin']	= __('LinkedIn', 'epl');
	return $contactmethods;
}
add_filter ('user_contactmethods','epl_property_admin_contact',10,1);

/**
 * Add Additional options to the author profiles for use in 
 * the author profile box
 *
 * @since 1.0
 */
function epl_add_custom_user_profile_fields( $user ) {
	$position 			= esc_attr( get_the_author_meta( 'position', $user->ID ) );
	$slogan 			= esc_attr( get_the_author_meta( 'slogan', $user->ID ) );
	$video 				= get_the_author_meta( 'video', $user->ID );
	$contact_form 		= get_the_author_meta( 'contact-form', $user->ID ); ?>
	
	<h3><?php _e('Easy Property Listings: Author Box Profile', 'epl'); ?></h3>
	<p><?php _e('The following details will appear in your author box and widgets.', 'epl'); ?></p>
	
	<table class="form-table">
		<tr>
			<th><label for="position"><?php _e('Position', 'epl'); ?></label></th>
			<td>
				<input type="text" name="position" id="position" value="<?php echo $position; ?>" class="regular-text" /><br />
				<span class="description"><?php _e('Please enter your title/position.', 'epl'); ?></span>
			</td>
		</tr>
		
		<tr>
			<th><label for="slogan"><?php _e('Slogan', 'epl'); ?></label></th>
			<td>
				<input type="text" name="slogan" id="slogan" value="<?php echo $slogan; ?>" class="regular-text" /><br />
				<span class="description"><?php _e('Please enter your slogan.', 'epl'); ?></span>
			</td>
		</tr>

		<tr>
			<th><label for="video"><?php _e('YouTube Video Url', 'epl'); ?></label></th>
			<td>
				<input type="text" name="video" id="video" value="<?php echo $video; ?>" class="regular-text" /><br />
				<span class="description"><?php _e('Please enter your YouTube Profile Video URL.', 'epl'); ?></span>
			</td>
		</tr>
		
		<tr>
			<th><label for="contact-form"><?php _e('Contact Form short code', 'epl'); ?></label></th>
			<td>
				<input type="text" name="contact-form" id="contact-form" value="<?php echo $contact_form; ?>" class="regular-text" /><br />
				<span class="description"><?php _e('Please enter your form short code for author contact tab', 'epl'); ?></span>
			</td>
		</tr>
	</table>
<?php }

/**
 * Save and update additional author profile box information
 *
 * @since 1.0
 */
function epl_save_custom_user_profile_fields( $user_id ) {	
	if ( !current_user_can( 'edit_user', $user_id ) )
		return FALSE;

	update_user_meta( $user_id, 'position', $_POST['position'] );
	update_user_meta( $user_id, 'slogan', $_POST['slogan'] );
	update_user_meta( $user_id, 'video', $_POST['video'] );
	update_user_meta( $user_id, 'contact-form', $_POST['contact-form'] );
}

add_action( 'show_user_profile', 'epl_add_custom_user_profile_fields' );
add_action( 'edit_user_profile', 'epl_add_custom_user_profile_fields' );

add_action( 'personal_options_update', 'epl_save_custom_user_profile_fields' );
add_action( 'edit_user_profile_update', 'epl_save_custom_user_profile_fields' );
