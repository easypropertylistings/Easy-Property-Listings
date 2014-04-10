<?php
/**
 * Plugin Name: Easy Property Listings
 * Plugin URI: http://www.easypropertylistings.com.au
 * Description:  Serve Property Listings Through WordPress
 * Author: Merv Barrett
 * Author URI: http://www.realestateconnected.com.au
 * Version: 1.0.1
 * Text Domain: epl
 * Domain Path: languages
 *
 * Easy Property Listings is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * Easy Property Listings is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Easy Digital Downloads. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package EPL
 * @category Core
 * @author Merv Barrett
 * @version 0.0.1
 */

include('lib/index.php'); // Libraries

if ( !is_admin() ) { // NOT IN ADMIN
	// TEMPLATES

	// Template - Templates (Various Ones)
	if ( $epl_template_property_templates = locate_template( 'content-tab-templates.php' ) ) {
		// locate_template() returns path to file
		// if either the child theme or the parent theme have overridden the template
		load_template( $epl_template_property_templates );
	} else {
		// If neither the child nor parent theme have overridden the template,
		// we load the template from the 'templates' sub-directory of the directory this file is in
		load_template( dirname( __FILE__ ) . '/lib/templates/content-tab-templates.php' );
	}

	//// Mapping Templates
	// Template - Property Map
	if ( $epl_template_property_map = locate_template( 'property-map.php' ) ) {
		// locate_template() returns path to file
		// if either the child theme or the parent theme have overridden the template
		load_template( $epl_template_property_map );
	} else {
		// If neither the child nor parent theme have overridden the template,
		// we load the template from the 'templates' sub-directory of the directory this file is in
		load_template( dirname( __FILE__ ) . '/lib/templates/property-map.php' );
	}
	
	// Template - GeoRSS Property Map Local
	if ( $epl_template_property_map_local = locate_template( 'property-map-local.php' ) ) {
		// locate_template() returns path to file
		// if either the child theme or the parent theme have overridden the template
		load_template( $epl_template_property_map_local );
	} else {
		// If neither the child nor parent theme have overridden the template,
		// we load the template from the 'templates' sub-directory of the directory this file is in
		load_template( dirname( __FILE__ ) . '/lib/templates/property-map-local.php' );
	}
	
	// Template - Tabbed Property Map Local
	if ( $epl_template_property_map_local_tabbed = locate_template( 'property-map-local-tabbed.php' ) ) {
		// locate_template() returns path to file
		// if either the child theme or the parent theme have overridden the template
		load_template( $epl_template_property_map_local_tabbed );
	} else {
		// If neither the child nor parent theme have overridden the template,
		// we load the template from the 'templates' sub-directory of the directory this file is in
		load_template( dirname( __FILE__ ) . '/lib/templates/property-map-local-tabbed.php' );
	}
	
	// Supermap Elements
	if ( $epl_template_supermap_elements_script = locate_template( 'supermap-elements-script.php' ) ) {
		// locate_template() returns path to file
		// if either the child theme or the parent theme have overridden the template
		load_template( $epl_template_supermap_elements_script );
	} else {
		// If neither the child nor parent theme have overridden the template,
		// we load the template from the 'templates' sub-directory of the directory this file is in
		load_template( dirname( __FILE__ ) . '/lib/templates/supermap-elements-script.php' );
	}
	//// END Map Templates

	// Template - Testimonial Template Functions
	if ( $epl_template_testimonial = locate_template( 'content-testimonial.php' ) ) {
		// locate_template() returns path to file
		// if either the child theme or the parent theme have overridden the template
		load_template( $epl_template_testimonial );
	} else {
		// If neither the child nor parent theme have overridden the template,
		// we load the template from the 'templates' sub-directory of the directory this file is in
		load_template( dirname( __FILE__ ) . '/lib/templates/content-testimonial.php' );
	}
	
	// Template - Suburb Profile Single
	if ( $epl_template_suburb_profile_single = locate_template( 'content-suburb_profile-single.php' ) ) {
		// locate_template() returns path to file
		// if either the child theme or the parent theme have overridden the template
		load_template( $epl_template_suburb_profile_single );
	} else {
		// If neither the child nor parent theme have overridden the template,
		// we load the template from the 'templates' sub-directory of the directory this file is in
		load_template( dirname( __FILE__ ) . '/lib/templates/content-suburb_profile-single.php' );
	}
	
	// Template - Suburb Profile Single
	if ( $epl_template_suburb_profile_blog = locate_template( 'content-suburb_profile-card.php' ) ) {
		// locate_template() returns path to file
		// if either the child theme or the parent theme have overridden the template
		load_template( $epl_template_suburb_profile_blog );
	} else {
		// If neither the child nor parent theme have overridden the template,
		// we load the template from the 'templates' sub-directory of the directory this file is in
		load_template( dirname( __FILE__ ) . '/lib/templates/content-suburb_profile-card.php' );
	}
	
	// Template - Single Property / Rental Page
	if ( $epl_template_property_single = locate_template( 'content-property-single.php' ) ) {
		// locate_template() returns path to file
		// if either the child theme or the parent theme have overridden the template
		load_template( $epl_template_property_single );
	} else {
		// If neither the child nor parent theme have overridden the template,
		// we load the template from the 'templates' sub-directory of the directory this file is in
		load_template( dirname( __FILE__ ) . '/lib/templates/content-property-single.php' );
	}
	
	// Template - Property Cards
	if ( $epl_template_property_card = locate_template( 'content-property-card.php' ) ) {
		// locate_template() returns path to file
		// if either the child theme or the parent theme have overridden the template
		load_template( $epl_template_property_card );
	} else {
		// If neither the child nor parent theme have overridden the template,
		// we load the template from the 'templates' sub-directory of the directory this file is in
		load_template( dirname( __FILE__ ) . '/lib/templates/content-property-card.php' );
	}
	
	// Template - Post Loops
	if ( $epl_template_post_card = locate_template( 'content-post-card.php' ) ) {
		// locate_template() returns path to file
		// if either the child theme or the parent theme have overridden the template
		load_template( $epl_template_post_card );
	} else {
		// If neither the child nor parent theme have overridden the template,
		// we load the template from the 'templates' sub-directory of the directory this file is in
		load_template( dirname( __FILE__ ) . '/lib/templates/content-post-card.php' );
	}
	
	// Template - Author Bio
	if ( $epl_template_property_card = locate_template( 'content-author.php' ) ) {
		// locate_template() returns path to file
		// if either the child theme or the parent theme have overridden the template
		load_template( $epl_template_property_card );
	} else {
		// If neither the child nor parent theme have overridden the template,
		// we load the template from the 'templates' sub-directory of the directory this file is in
		load_template( dirname( __FILE__ ) . '/lib/templates/content-author.php' );
	}

	// Template - Author Cards
	if ( $epl_template_property_card = locate_template( 'content-author-card.php' ) ) {
		// locate_template() returns path to file
		// if either the child theme or the parent theme have overridden the template
		load_template( $epl_template_property_card );
	} else {
		// If neither the child nor parent theme have overridden the template,
		// we load the template from the 'templates' sub-directory of the directory this file is in
		load_template( dirname( __FILE__ ) . '/lib/templates/content-author-card.php' );
	}
	
	// Template - Graphs
	if ( $epl_property_graphs = locate_template( 'content-tab-graphs.php' ) ) {
		// locate_template() returns path to file
		// if either the child theme or the parent theme have overridden the template
		load_template( $epl_property_graphs );
	} else {
		// If neither the child nor parent theme have overridden the template,
		// we load the template from the 'templates' sub-directory of the directory this file is in
		load_template( dirname( __FILE__ ) . '/lib/templates/content-tab-graphs.php' );
	}

	// Template - Supermap
	if ( $epl_property_supermap = locate_template( 'property-supermap.php' ) ) {
		// locate_template() returns path to file
		// if either the child theme or the parent theme have overridden the template
		load_template( $epl_property_supermap );
	} else {
		// If neither the child nor parent theme have overridden the template,
		// we load the template from the 'templates' sub-directory of the directory this file is in
		load_template( dirname( __FILE__ ) . '/lib/templates/property-supermap.php' );
	}
	
	// Template - Supermap Schools
	if ( $epl_property_map_schools = locate_template( 'property-map-schools.php' ) ) {
		// locate_template() returns path to file
		// if either the child theme or the parent theme have overridden the template
		load_template( $epl_property_map_schools );
	} else {
		// If neither the child nor parent theme have overridden the template,
		// we load the template from the 'templates' sub-directory of the directory this file is in
		load_template( dirname( __FILE__ ) . '/lib/templates/property-map-schools.php' );
	}

	// Property and Rental Inspection Buttons 
	// 1Form Application Form
	if ( $epl_button_1form = locate_template( 'content-button-1form.php' ) ) {
		// locate_template() returns path to file
		// if either the child theme or the parent theme have overridden the template
		load_template( $epl_button_1form );
	} else {
		// If neither the child nor parent theme have overridden the template,
		// we load the template from the 'templates' sub-directory of the directory this file is in
		load_template( dirname( __FILE__ ) . '/lib/templates/content-button-1form.php' );
	}
	
	// 'Inspect Real Estate' Book Inspection Integration
	if ( $epl_button_book_inspection = locate_template( 'content-button-book-inspection.php' ) ) {
		// locate_template() returns path to file
		// if either the child theme or the parent theme have overridden the template
		load_template( $epl_button_book_inspection );
	} else {
		// If neither the child nor parent theme have overridden the template,
		// we load the template from the 'templates' sub-directory of the directory this file is in
		load_template( dirname( __FILE__ ) . '/lib/templates/content-button-book-inspection.php' );
	}
}
