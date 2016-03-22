<?php
/**
 * User Profile
 *
 * @package     EPL
 * @subpackage  Admin/User
 * @copyright   Copyright (c) 2014, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Add Additional Contact methods to author pages. These links
 * are used in the author widgets and profile boxes
 *
 * @since 1.0
 */
function epl_property_admin_contact ( $contactmethods ) {
	$contactmethods['mobile']	= __( 'Mobile', 'easy-property-listings' );
	$contactmethods['skype']	= __( 'Skype', 'easy-property-listings' );
	$contactmethods['twitter']	= __( 'Twitter', 'easy-property-listings' );
	$contactmethods['facebook']	= __( 'Facebook', 'easy-property-listings' );
	$contactmethods['google']	= __( 'Google Plus', 'easy-property-listings' );
	$contactmethods['linkedin']	= __( 'LinkedIn', 'easy-property-listings' );
	return $contactmethods;
}
add_filter ('user_contactmethods','epl_property_admin_contact',10,1);

/**
 * Add Additional Contact methods to author pages. These links
 * are used in the author widgets and profile boxes
 *
 * @since 1.0
 */
function epl_get_custom_user_profile_fields() {

	return apply_filters(
		'epl_custom_user_profile_fields',
		array(
			array(
				'name'			=>	'position',
				'label'			=>	__('Position', 'easy-property-listings' ),
				'description'		=>	__('Please enter your title/position.', 'easy-property-listings' ),
				'class'			=>	'regular-text',
				'type'			=>	'text',
			),
			array(
				'name'			=>	'slogan',
				'label'			=>	__('Slogan', 'easy-property-listings' ),
				'description'		=>	__('Please enter your slogan.', 'easy-property-listings' ),
				'class'			=>	'regular-text',
				'type'			=>	'text',
			),
			array(
				'name'			=>	'video',
				'label'			=>	__('Youtube Video Url','easy-property-listings' ),
				'description'		=>	__('Please enter your YouTube Profile Video URL.', 'easy-property-listings' ),
				'class'			=>	'regular-text',
				'type'			=>	'text',
			),
			array(
				'name'			=>	'contact-form',
				'label'			=>	__('Contact Form short code','easy-property-listings' ),
				'description'		=>	__('Please enter your form short code for author contact tab.', 'easy-property-listings' ),
				'class'			=>	'regular-text',
				'type'			=>	'text',
			),
		)
	);
}

/**
 * Add Additional options to the author profiles for use in
 * the author profile box
 *
 * @since 1.0
 */
function epl_add_custom_user_profile_fields( $user ) { ?>

	<h3><?php _e('Easy Property Listings: Author Box Profile', 'easy-property-listings' ); ?></h3>
	<p><?php _e('The following details will appear in your author box and widgets.', 'easy-property-listings' ); ?></p>

	<table class="form-table">
		<?php
			$user_fields = epl_get_custom_user_profile_fields();

			foreach($user_fields as $user_field ) { ?>
				<tr>
					<th>
						<label for="<?php echo $user_field['name'] ?>">
							<?php echo $user_field['label'] ?>
						</label>
					</th>
					<td>
						<input
							type="text"
							name="<?php echo $user_field['name'] ?>"
							id="<?php echo $user_field['name'] ?>"
							value="<?php echo get_the_author_meta( $user_field['name'], $user->ID ) ?>"
							class="regular-text"
						/><br />
						<span class="description">
							<?php
								echo isset ($user_field['description']) ?  $user_field['description'] : '';
							?>
						</span>
					</td>
				</tr> <?php
			}
		?>
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

	$user_fields = epl_get_custom_user_profile_fields();

	foreach($user_fields as $user_field ) {

		if( isset($_POST[$user_field['name']]) ) {
			update_user_meta( $user_id, $user_field['name'], sanitize_text_field($_POST[$user_field['name']]) );
		}


	}
}

add_action( 'show_user_profile', 'epl_add_custom_user_profile_fields' );
add_action( 'edit_user_profile', 'epl_add_custom_user_profile_fields' );

add_action( 'personal_options_update', 'epl_save_custom_user_profile_fields' );
add_action( 'edit_user_profile_update', 'epl_save_custom_user_profile_fields' );
