<?php
/*
 * Functions of Easy Property Listings templates
 *
 * @package easy-property-listings
 * @subpackage EPL_Theme_Compat
 * @since easy-property-listings (1.0.0)
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// Sold Leased List
include('content/content-listing-sold-leased.php');
 

// Selecting Card Display Style
function epl_property_single() {	
	global $epl_settings;
	
	$d_option = '';
	if(!empty($epl_settings) && isset($epl_settings['epl_display_single_property'])) {
		$d_option = $epl_settings['epl_display_single_property'];
	}
	
	$action_check = has_action( 'epl_single_template' );
	if ( $action_check != '' && $d_option !== 0 ) {
		do_action( 'epl_single_template' );
	} else {
		
		$d_gallery = '';
		if(!empty($epl_settings) && isset($epl_settings['display_single_gallery'])) {
			$d_gallery		= $epl_settings['display_single_gallery'];
		}
		
		$d_gallery_n = '';
		if(!empty($epl_settings) && isset($epl_settings['display_gallery_n'])) {
			$d_gallery_n		= $epl_settings['display_gallery_n'];
		}
		
		$d_map_position = '';

		// Default Template
		echo epl_property_single_default($d_gallery , $d_gallery_n , $d_map_position);
	}
}

function epl_single_listing_featured_image( $image_size = 'index_thumbnail' , $image_class = 'index-thumbnail' ) { 
	
	if ( has_post_thumbnail() ) { ?>
		<div class="entry-image">
			<div class="epl-featured-image it-featured-image">
				<a href="<?php the_permalink(); ?>">
					<?php the_post_thumbnail( $image_size , array( 'class' => $image_class ) ); ?>
				</a>
			</div>
		</div>
	<?php }  ?>

}
add_action( 'epl_single_featured_image' , 'epl_single_listing_featured_image' );

/*
* Single Listing Templates
*/

	/*Template - Default
	*	if ( $epl_property_single_expanded = locate_template( 'content-listing-single.php' ) ) {
	*		load_template( $epl_property_single_expanded );
	*	} else {
	*		load_template( EPL_PATH_TEMPLATES_CONTENT . 'content-listing-single.php' );
	*	}
	*/

// Single Listing Expanded Templates
function epl_property_single_default($d_gallery , $d_gallery_n, $d_map_position) {

	include( EPL_PATH_TEMPLATES_CONTENT . 'listing-meta.php' );
	include( EPL_PATH_TEMPLATES_CONTENT . 'content-listing-single.php' );	

}

/*
* Loop Listing Templates
*/

// Modify the Excerpt length on Archive pages
function epl_archive_custom_excerpt_length( $length ) {
	global $epl_settings;
	
	$excerpt = '';
	if(!empty($epl_settings) && isset($epl_settings['display_excerpt_length'])) {
		$excerpt = $epl_settings['display_excerpt_length'];
	}
	if ( $excerpt == '') {
		return 22;
	} else {
		return $excerpt;
	}
}

// Selecting Card Display Style
// Allows the use of one function where we can then select a different template when needed 
function epl_property_blog() {

	add_filter( 'excerpt_length', 'epl_archive_custom_excerpt_length', 999 );

	global $epl_settings;
	
	$option = '';
	if(!empty($epl_settings) && isset($epl_settings['epl_property_card_style'])) {
		$option = $epl_settings['epl_property_card_style'];
	}
	
	$action_check = has_action( 'epl_loop_template' );
	if ( $action_check != '' && $option !== 0 ) {
		do_action( 'epl_loop_template' );
	} else {

		// Default Template
		echo epl_property_blog_default();
		
	}

}

// Listing Function for paged card display 
function epl_property_blog_default() {
	// Get Listing Custom Values 
	include( EPL_PATH_TEMPLATES_CONTENT . 'listing-meta.php' );

	// Status Removal Do Not Display Withdrawn or OffMarket listings
	if ( $property_status == 'withdrawn' || $property_status == 'offmarket' ) {
		// Do Not Display Withdrawn or OffMarket listings
	} else {
		include ( EPL_PATH_TEMPLATES_CONTENT . 'loop-listing-blog-default.php' );
	} // End Status Removal
}


// Listing Function for paged card display 
function epl_property_blog_slim() {
	include( EPL_PATH_TEMPLATES_CONTENT . 'listing-meta.php' );

	// Status Removal
	if ( $property_status == 'withdrawn' || $property_status == 'offmarket' ) {
		// Do Not Display Withdrawn or OffMarket listings
	} else {
		include ( 'content/loop-listing-blog-slim.php' );
	} // End Status Removal
}

/*
* Author Card Templates
*/

// AUTHOR CARD : Tabbed Style
function epl_property_author_box() {
	$author_id = get_the_author_meta( 'ID' );

	include( 'content/author-meta.php' );
	include( 'content/content-author-box.php' );
}
add_action( 'epl_single_author' , 'epl_property_author_box' , 1 );
 
// AUTHOR CARD : Standard
function epl_property_author_box_simple_card() {
	include('content/author-meta.php');
	
	global $epl_settings;
	
	$author_style = '';
	if(!empty($epl_settings) && isset($epl_settings['epl_staff_link_to'])) {
		$author_style = $epl_settings['epl_staff_link_to'];
	}
	
	$epl_staff_excerpt = '';
	if(!empty($epl_settings) && isset($epl_settings['epl_staff_excerpt'])) {
		$epl_staff_excerpt = $epl_settings['epl_staff_excerpt'];
	}
	
	include( 'content/content-author-box-simple-card.php' );
	
}

// AUTHOR CARD : Gravatar
function epl_property_author_box_simple_grav() {
	include('content/author-meta.php');
	
	global $epl_settings;
	
	$author_style = '';
	if(!empty($epl_settings) && isset($epl_settings['epl_staff_link_to'])) {
		$author_style = $epl_settings['epl_staff_link_to'];
	}
	
	include( 'content/content-author-box-simple-grav.php' );
}

// AUTHOR LISTING CARDS : Listing Card
function epl_property_author_card($display,$image,$title,$icons) {
	// Get Listing Custom Values 
	include( 'content/listing-meta.php' );	
	
	// Status Removal
	if ( $property_status == 'withdrawn' || $property_status == 'offmarket' ) {
		// Do Not Display Withdrawn or OffMarket listings
	} else { 
		include ( 'content/widget-content-author.php' );
	} // End Status Removal
}

/*
* Widget Templates
*/


// WIDGET LISTING : Listing Card
function epl_property_widget( $display , $image , $title , $icons , $more_text = "__('Read More','epl')" , $d_excerpt , $d_suburb , $d_street , $d_price , $d_more  ) {
	// Get Listing Custom Values 
	include( 'content/listing-meta.php' );	
	
	// Status Removal
	if ( $property_status == 'withdrawn' || $property_status == 'offmarket' ) {
		// Do Not Display Withdrawn or OffMarket listings
	} else {
		include ( 'content/widget-content-listing.php' );
	} // End Status Removal
}

// WIDGET LISTING : Listing List
function epl_property_widget_list_option() {
	$property_status = get_post_meta( get_the_ID(), 'property_status', true );
	// Status Removal
	if ( $property_status == 'withdrawn' || $property_status == 'offmarket' ) {
		// Do Not Display Withdrawn or OffMarket listings
	} else {
		include ( 'content/widget-content-listing-list.php' );
	} // End Status Removal
}

// WIDGET LISTING : Image Only
function epl_property_widget_image_only_option( $image ) { 
	$property_status = get_post_meta( get_the_ID(), 'property_status', true );
	// Status Removal
	if ( $property_status == 'withdrawn' || $property_status == 'offmarket' ) {
		// Do Not Display Withdrawn or OffMarket listings
	} else {
		include ( 'content/widget-content-listing-image.php' );
	} // End Status Removal
}

// WIDGET AUTHOR : Widget Tall Card
function epl_property_author_box_simple_card_tall( $d_image , $d_icons , $d_bio) {

	$author_id = get_the_author_meta( 'ID' );

	include('content/author-meta.php');
	include( 'content/widget-content-author-tall.php' );
	
	// Second Author
	if ( is_single() ) {
	
		$property_second_agent = get_post_meta( get_the_ID() , 'property_second_agent', true);
		
		if ( '' != $property_second_agent ) {
			
			$second_author = get_user_by( 'login' , $property_second_agent );
			$author_id = $second_author->ID;
			
			include( 'content/author-meta.php');
			include( 'content/widget-content-author-tall.php' );
		}
		
	}

}
