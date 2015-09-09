<?php
/**
 * Uninstall Easy Property Listings
 *
 * @package     EPL
 * @subpackage  Uninstall
 * @copyright   Copyright (c) 2014, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.2
*/

// Exit if accessed directly
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) exit;

// Load EPL file
include_once( 'easy-property-listings.php' );

global $wpdb, $wp_roles;

if( epl_get_option( 'uninstall_on_delete' ) == 1 ) {

	/** Delete All the Custom Post Types */
	$epl_taxonomies = array( 'location', 'tax_feature', 'tax_business_listing', );
	$epl_post_types = array( 'property', 'rental', 'land', 'rural', 'commercial', 'commercial_land' );
	foreach ( $epl_post_types as $post_type ) {

		$epl_taxonomies = array_merge( $epl_taxonomies, get_object_taxonomies( $post_type ) );
		$items = get_posts( array( 'post_type' => $post_type, 'post_status' => 'any', 'numberposts' => -1, 'fields' => 'ids' ) );

		if ( $items ) {
			foreach ( $items as $item ) {
				wp_delete_post( $item, true);
			}
		}
	}

	/** Delete All the Terms & Taxonomies */
	foreach ( array_unique( array_filter( $epl_taxonomies ) ) as $taxonomy ) {

		$terms = $wpdb->get_results( $wpdb->prepare( "SELECT t.*, tt.* FROM $wpdb->terms AS t INNER JOIN $wpdb->term_taxonomy AS tt ON t.term_id = tt.term_id WHERE tt.taxonomy IN ('%s') ORDER BY t.name ASC", $taxonomy ) );

		// Delete Terms
		if ( $terms ) {
			foreach ( $terms as $term ) {
				$wpdb->delete( $wpdb->term_taxonomy, array( 'term_taxonomy_id' => $term->term_taxonomy_id ) );
				$wpdb->delete( $wpdb->terms, array( 'term_id' => $term->term_id ) );
			}
		}

		// Delete Taxonomies
		$wpdb->delete( $wpdb->term_taxonomy, array( 'taxonomy' => $taxonomy ), array( '%s' ) );
	}

	/** Delete all the Plugin Options */
	delete_option( 'epl_settings' );
	delete_option( 'epl_version' );
	delete_option( 'epl_version_upgraded_from' );
	delete_option( 'epl_rewrite_rules' );
	delete_option( 'epl_license' );

}