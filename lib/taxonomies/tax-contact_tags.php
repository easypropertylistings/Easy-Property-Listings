<?php
/**
 * TAXONOMY Contact Tag
 *
 * @package     EPL
 * @subpackage  Taxonomy
 * @copyright   Copyright (c) 2016, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Registers and sets up the epl_contact_tag taxonomy
 *
 * @since 3.0
 * @return void
 */
function epl_register_taxonomy_contact_tag() {
	$labels = array(
		'name'                       => _x( 'Contact Tags', 'Taxonomy General Name', 'easy-property-listings'  ),
		'singular_name'              => _x( 'Contact Tag', 'Taxonomy Singular Name', 'easy-property-listings'  ),
		'menu_name'                  => __( 'Contact Tags', 'easy-property-listings'  ),
		'all_items'                  => __( 'All Contact Tags', 'easy-property-listings'  ),
		'parent_item'                => __( 'Parent Contact Tag', 'easy-property-listings'  ),
		'parent_item_colon'          => __( 'Parent Contact Tag:', 'easy-property-listings'  ),
		'new_item_name'              => __( 'New Contact Tag Name', 'easy-property-listings'  ),
		'add_new_item'               => __( 'Add New Contact Tag', 'easy-property-listings'  ),
		'edit_item'                  => __( 'Edit Contact Tag', 'easy-property-listings'  ),
		'update_item'                => __( 'Update Contact Tag', 'easy-property-listings'  ),
		'separate_items_with_commas' => __( 'Separate Contact Tag with commas', 'easy-property-listings'  ),
		'search_items'               => __( 'Search Contact Tag', 'easy-property-listings'  ),
		'add_or_remove_items'        => __( 'Add or remove Contact Tag', 'easy-property-listings'  ),
		'choose_from_most_used'      => __( 'Choose from the most used Contact Tag', 'easy-property-listings'  ),
		'not_found'                  => __( 'Contact Tag Not Found', 'easy-property-listings'  ),
	);
	$rewrite = array(
		'slug'                       => 'epl_contact_tag',
		'with_front'                 => true,
		'hierarchical'               => false,
	);
	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => false,
		'public'                     => false,
		'show_ui'                    => false,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => false,
		'show_tagcloud'              => true,
		'rewrite'                    => $rewrite,
	);
	register_taxonomy( 'epl_contact_tag', array( 'epl_contact' ) , $args );
}
add_action( 'init', 'epl_register_taxonomy_contact_tag', 0 );

/**
 * Add background color on add contact tag screen
 *
 * @since 3.0
 *
 */
function epl_add_contact_tag_bgcolor_field() {

	echo '<div class="form-field epl-contact-tag-bgwrap">
			<label for="epl_contact_tag_bgcolor">' . __('Background Color', 'easy-property-listings' ) . '</label>
			<input type="color" class="epl_contact_tag_bgcolor" name="epl_contact_tag_bgcolor" id="epl_contact_tag_bgcolor" value="" />
		</div>';
}
add_action('contact_tag_add_form_fields', 'epl_add_contact_tag_bgcolor_field' );

/**
 * Add background color on edit contact tag screen
 *
 * @since 3.0
 * @param $taxonomy term object
 */
function epl_edit_contact_tag_bgcolor_field($taxonomy) {

	$bg_color = epl_get_contact_tag_bgcolor($taxonomy->term_id);
	echo '
		<tr class="form-field epl-contact-tag-bgwrap">
			<th scope="row" valign="top">
				<label for="epl_contact_tag_bgcolor">' . __('Background Color', 'easy-property-listings' ) . '</label>
			</th>
			<td class="eti-image-wrap">
				<input type="color" class="epl_contact_tag_bgcolor" name="epl_contact_tag_bgcolor" id="epl_contact_tag_bgcolor" value="'.$bg_color.'" /><br />
			</td>
		</tr>';

}
add_action('contact_tag_edit_form_fields','epl_edit_contact_tag_bgcolor_field' );

/**
 * Save term background color while saving / editing the term
 *
 * @param $term_id
 * @since 3.0
 */
function epl_save_contact_tag_bgcolor($term_id) {

	if( isset($_POST['epl_contact_tag_bgcolor']) )
		epl_update_contact_tag_bgcolor($term_id,$_POST['epl_contact_tag_bgcolor']);
}
add_action('create_term','epl_save_contact_tag_bgcolor');
add_action('edit_term','epl_save_contact_tag_bgcolor');

/**
 * Returns the background of contact Tag
 *
 * @param $term_id
 * @since 3.0
 * @return string background color of the contact tag
 */
function epl_get_contact_tag_bgcolor($term_id) {

	// support for wp version who doesnt support term meta
	$all_bgs = get_option('epl_contact_tag_bg');

	$term_bg = isset($all_bgs[$term_id]) ? $all_bgs[$term_id] : '';

	if($term_bg == '' && function_exists('get_term_meta') ) {

		$term_bg = get_term_meta($term_id,'term_bg',true);
	}

	return $term_bg == '' ? '#43ac6d': $term_bg;
}

/**
 * Updates the background of contact Tag
 *
 * @param $term_id
 * @param $bg_color
 * @since 3.0
 * @return true if bgcolor updated
 */
function epl_update_contact_tag_bgcolor($term_id,$bg_color = '#43ac6d') {

	if(function_exists('get_term_meta') ) {

		return update_term_meta($term_id,'term_bg',esc_attr($bg_color));
	}

	// support for wp version who doesnt support term meta
	$all_bgs = get_option('epl_contact_tag_bg');

	$all_bgs[$term_id] = esc_attr($bg_color);

	return update_option('epl_contact_tag_bg',$all_bgs);
}
