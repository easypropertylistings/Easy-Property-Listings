<?php
	/**
	 * TAXONOMY :: Contact Tag
	 *
	 * @package     EPL
	 * @subpackage  Taxonomy/Contact_Tags
	 * @copyright   Copyright (c) 2014, Merv Barrett
	 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
	 * @since       1.0
	 */

// Exit if accessed directly
	if ( ! defined( 'ABSPATH' ) ) exit;

	function epl_register_taxonomy_contact_tag() {
		$labels = array(
			'name'                       => _x( 'Contact Tags', 'Taxonomy General Name', 'epl' ),
			'singular_name'              => _x( 'Contact Tag', 'Taxonomy Singular Name', 'epl' ),
			'menu_name'                  => __( 'Contact Tags', 'epl' ),
			'all_items'                  => __( 'All Contact Tags', 'epl' ),
			'parent_item'                => __( 'Parent Contact Tag', 'epl' ),
			'parent_item_colon'          => __( 'Parent Contact Tag:', 'epl' ),
			'new_item_name'              => __( 'New Contact Tag Name', 'epl' ),
			'add_new_item'               => __( 'Add New Contact Tag', 'epl' ),
			'edit_item'                  => __( 'Edit Contact Tag', 'epl' ),
			'update_item'                => __( 'Update Contact Tag', 'epl' ),
			'separate_items_with_commas' => __( 'Separate Contact Tag with commas', 'epl' ),
			'search_items'               => __( 'Search Contact Tag', 'epl' ),
			'add_or_remove_items'        => __( 'Add or remove Contact Tag', 'epl' ),
			'choose_from_most_used'      => __( 'Choose from the most used Contact Tag', 'epl' ),
			'not_found'                  => __( 'Contact Tag Not Found', 'epl' ),
		);
		$rewrite = array(
			'slug'                       => 'contact_tag',
			'with_front'                 => true,
			'hierarchical'               => false,
		);
		$args = array(
			'labels'                     => $labels,
			'hierarchical'               => false,
			'public'                     => false,
			'show_ui'                    => true,
			'show_admin_column'          => true,
			'show_in_nav_menus'          => false,
			'show_tagcloud'              => true,
			'rewrite'                    => $rewrite,
		);
		register_taxonomy( 'contact_tag', array( 'epl_contact' ) , $args );
	}
	add_action( 'init', 'epl_register_taxonomy_contact_tag', 0 );


	/**
	 * Add background color on add contact tag screen
	 *
	 * @since 2.4
	 *
	 */
	function add_contact_tag_bgcolor_field() {

		echo '
			<div class="form-field epl-contact-tag-bgwrap">
				<label for="epl_contact_tag_bgcolor">' . __('Background Color', 'epl') . '</label>
				<input type="color" class="epl_contact_tag_bgcolor" name="epl_contact_tag_bgcolor" id="epl_contact_tag_bgcolor" value="" />
			</div>';

	}
	add_action('contact_tag_add_form_fields', 'add_contact_tag_bgcolor_field' );

	/**
	 * Add background color on edit contact tag screen
	 *
	 * @since 2.4
	 * @param $taxonomy term object
	 */
	function edit_contact_tag_bgcolor_field($taxonomy) {

		$bg_color = epl_get_contact_tag_bgcolor($taxonomy->term_id);
		echo '
			<tr class="form-field epl-contact-tag-bgwrap">
				<th scope="row" valign="top">
					<label for="epl_contact_tag_bgcolor">' . __('Background Color', 'epl') . '</label>
				</th>
				<td class="eti-image-wrap">
					<input type="color" class="epl_contact_tag_bgcolor" name="epl_contact_tag_bgcolor" id="epl_contact_tag_bgcolor" value="'.$bg_color.'" /><br />
				</td>
			</tr>';

	}
	add_action('contact_tag_edit_form_fields','edit_contact_tag_bgcolor_field' );

	/**
	 * Save term background color while saving / editing the term
	 * @param $term_id
	 * @since 2.4
	 */
	function epl_save_contact_tag_bgcolor($term_id) {

		if( isset($_POST['epl_contact_tag_bgcolor']) )
			epl_update_contact_tag_bgcolor($term_id,$_POST['epl_contact_tag_bgcolor']);
	}
	add_action('create_term','epl_save_contact_tag_bgcolor');
	add_action('edit_term','epl_save_contact_tag_bgcolor');


	/**
	 * Returns the background of contact Tag
	 * @param $term_id
	 * @since 2.4
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
	 * updates the background of contact Tag
	 * @param $term_id
	 * @param $bg_color
	 * @since 2.4
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
