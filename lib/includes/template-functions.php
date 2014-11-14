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
//include( EPL_PATH_TEMPLATES_CONTENT . 'listing-meta-dev.php' );

// TEMPLATE - Leased/sold property list
function epl_property_sold_leased() {
	$property_suburb = get_post_custom_values('property_address_suburb');
	$post_id = $property_suburb[0]['ID'];
	$terms = get_the_terms( $post->ID, 'location' );
	if( $terms != '' ) {
		global $post;
		foreach($terms as $term){
			$term->slug;
		}
	}
	
	$post_type = get_post_type();

	if ( 'property' == $post_type ) {
		$query = new WP_Query( array (
			'post_type' => 'property',
			'location' => $term->slug,
			'meta_query' => array(
				array(
					'key' => 'property_status',
					'value' => 'sold'
				)
			),
			'posts_per_page' => '5'
		) );
	} elseif ( 'land' == $post_type ) {
		$query = new WP_Query( array (
			'post_type' => 'land',
			'meta_query' => array(
				array(
					'key' => 'property_status',
					'value' => 'sold'
				)
			),
			'property_status' => 'sold',
			'posts_per_page' => '5'
		) );
	} elseif ( 'rural' == $post_type ) {
		$query = new WP_Query( array (
			'post_type' => 'rural',
			'location' => $term->slug,
			'meta_query' => array(
				array(
					'key' => 'property_status',
					'value' => 'sold'
				)
			),
			'posts_per_page' => '5'
		) );
	} else {
		$query = new WP_Query( array (
			'post_type' => 'rental',
			'location' => $term->slug,
			'meta_query' => array(
				array(
					'key' => 'property_status',
					'value' => 'leased'
				)
			),
			'posts_per_page' => '5'
		) );
	}
	
	if ( $query->have_posts() ) { ?>
		<div class="epl-tab-section">
			<?php if ( 'property' == $post_type || 'land' == $post_type || 'rural' == $post_type) { ?>
				<h5 class="tab-title"><?php _e('Recently Sold', 'epl'); ?></h5>
			<?php } else { ?>
				<h5 class="tab-title"><?php _e('Recently Leased', 'epl'); ?></h5>
			<?php } ?>
			<div class="tab-content">
				<ul>
					<?php
						while ( $query->have_posts() ) {
							$query->the_post(); ?>
					
							<!-- Suburb Tab -->
							<li><a href="<?php the_permalink(); ?>"><?php the_title(); ?><?php echo $suburb[0]; ?></a></li>
							<?php
						}
					?>
				</ul>
			</div>
		</div>
		<?php
	}
	wp_reset_postdata();
}
// superglobal object $property for posts 'property','land', 'commercial', 'business', 'commercial_land' , 'location_profile','rental','rural'
function reset_property_object( $post ) {
	$epl_posts = array('property','land', 'commercial', 'business', 'commercial_land' , 'location_profile','rental','rural');
	if(in_array($post->post_type,$epl_posts)){
		global $property;
		$property = new Property_Meta($post);
	}
}
add_action( 'the_post', 'reset_property_object' );

// make $property global available for hooks before the_post
function create_property_object() {
	global $post;
	$epl_posts = array('property','land', 'commercial', 'business', 'commercial_land' , 'location_profile','rental','rural');
	if(in_array($post->post_type,$epl_posts)){
		global $property;
		$property = new Property_Meta($post);
	}
}
add_action( 'wp', 'create_property_object' );

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

/**
 * Featured Image template now loading through filter
 *
 * @since 1.2
 */
function epl_single_listing_featured_image( $image_size = 'index_thumbnail' , $image_class = 'index-thumbnail' ) { 
	
	if ( has_post_thumbnail() ) { ?>
		<div class="entry-image">
			<div class="epl-featured-image it-featured-image">
				<a href="<?php the_permalink(); ?>">
					<?php the_post_thumbnail( $image_size , array( 'class' => $image_class ) ); ?>
				</a>
			</div>
		</div>
	<?php }

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
	global $property;
	$property_status = $property->get_property_meta('property_status');
	// Status Removal Do Not Display Withdrawn or OffMarket listings
	if ( $property_status == 'withdrawn' || $property_status == 'offmarket' ) {
		// Do Not Display Withdrawn or OffMarket listings
	} else {
		include ( EPL_PATH_TEMPLATES_CONTENT . 'loop-listing-blog-default.php' );
	} // End Status Removal
}


// Listing Function for paged card display 
function epl_property_blog_slim() {
	global $property;
	$property_status = $property->get_property_meta('property_status');
	// Status Removal
	if ( $property_status == 'withdrawn' || $property_status == 'offmarket' ) {
		// Do Not Display Withdrawn or OffMarket listings
	} else {
		include ( EPL_PATH_TEMPLATES_CONTENT.'loop-listing-blog-slim.php' );
	} // End Status Removal
}

/*
* Author Card Templates
*/

// AUTHOR CARD : Tabbed Style
function epl_property_author_box() {
	$author_id = get_the_author_meta( 'ID' );

	include( EPL_PATH_TEMPLATES_CONTENT.'author-meta.php' );
	include( EPL_PATH_TEMPLATES_CONTENT.'content-author-box.php' );
}
add_action( 'epl_single_author' , 'epl_property_author_box' , 1 );
 
// AUTHOR CARD : Standard
function epl_property_author_box_simple_card() {
	include(EPL_PATH_TEMPLATES_CONTENT.'author-meta.php');
	
	global $epl_settings;
	
	$author_style = '';
	if(!empty($epl_settings) && isset($epl_settings['epl_staff_link_to'])) {
		$author_style = $epl_settings['epl_staff_link_to'];
	}
	
	$epl_staff_excerpt = '';
	if(!empty($epl_settings) && isset($epl_settings['epl_staff_excerpt'])) {
		$epl_staff_excerpt = $epl_settings['epl_staff_excerpt'];
	}
	
	include( EPL_PATH_TEMPLATES_CONTENT.'content-author-box-simple-card.php' );
	
}

// AUTHOR CARD : Gravatar
function epl_property_author_box_simple_grav() {
	include(EPL_PATH_TEMPLATES_CONTENT.'author-meta.php');
	
	global $epl_settings;
	
	$author_style = '';
	if(!empty($epl_settings) && isset($epl_settings['epl_staff_link_to'])) {
		$author_style = $epl_settings['epl_staff_link_to'];
	}
	
	include( 'content/content-author-box-simple-grav.php' );
}

// AUTHOR LISTING CARDS : Listing Card
function epl_property_author_card($display,$image,$title,$icons) {
	global $property;
	$property_status = $property->get_property_meta('property_status');	
	
	// Status Removal
	if ( $property_status == 'withdrawn' || $property_status == 'offmarket' ) {
		// Do Not Display Withdrawn or OffMarket listings
	} else { 
		include ( EPL_PATH_TEMPLATES_CONTENT.'widget-content-author.php' );
	} // End Status Removal
}

/*
* Widget Templates
*/


// WIDGET LISTING : Listing Card
function epl_property_widget( $display , $image , $title , $icons , $more_text = "__('Read More','epl')" , $d_excerpt , $d_suburb , $d_street , $d_price , $d_more  ) {
	global $property;
	$property_status = $property->get_property_meta('property_status');	
	
	// Status Removal
	if ( $property_status == 'withdrawn' || $property_status == 'offmarket' ) {
		// Do Not Display Withdrawn or OffMarket listings
	} else {
		include ( EPL_PATH_TEMPLATES_CONTENT.'widget-content-listing.php' );
	} // End Status Removal
}

// WIDGET LISTING : Listing List
function epl_property_widget_list_option() {
	$property_status = get_post_meta( get_the_ID(), 'property_status', true );
	// Status Removal
	if ( $property_status == 'withdrawn' || $property_status == 'offmarket' ) {
		// Do Not Display Withdrawn or OffMarket listings
	} else {
		include ( EPL_PATH_TEMPLATES_CONTENT.'widget-content-listing-list.php' );
	} // End Status Removal
}

// WIDGET LISTING : Image Only
function epl_property_widget_image_only_option( $image ) { 
	$property_status = get_post_meta( get_the_ID(), 'property_status', true );
	// Status Removal
	if ( $property_status == 'withdrawn' || $property_status == 'offmarket' ) {
		// Do Not Display Withdrawn or OffMarket listings
	} else {
		include ( EPL_PATH_TEMPLATES_CONTENT.'widget-content-listing-image.php' );
	} // End Status Removal
}

// WIDGET AUTHOR : Widget Tall Card
function epl_property_author_box_simple_card_tall( $d_image , $d_icons , $d_bio) {

	$author_id = get_the_author_meta( 'ID' );

	include(EPL_PATH_TEMPLATES_CONTENT.'author-meta.php');
	include( EPL_PATH_TEMPLATES_CONTENT.'widget-content-author-tall.php' );
	
	// Second Author
	if ( is_single() ) {
	
		$property_second_agent = get_post_meta( get_the_ID() , 'property_second_agent', true);
		
		if ( '' != $property_second_agent ) {
			
			$second_author = get_user_by( 'login' , $property_second_agent );
			$author_id = $second_author->ID;
			
			include( EPL_PATH_TEMPLATES_CONTENT.'author-meta.php');
			include( EPL_PATH_TEMPLATES_CONTENT.'widget-content-author-tall.php' );
		}
		
	}

}

/*=== Callback functions for template hooks ====*/

/**
	@hooked epl_single_the_title
	@hooked property_tab_address
**/

function epl_the_listing_address(){
	global $property;
	// Commercial and Business Address
	if ($property->post_type == 'commercial' || $property->post_type == 'business' ) {
		if ( $property->get_property_meta('property_address_display') == 'yes' && $property->get_property_meta('property_com_display_suburb') == 'yes') { ?>
			<span class="item-street"><?php echo $property->get_formatted_property_address(); ?></span>
			
		<?php } 
		echo '<span class="entry-title-sub">';
		if ( $property->get_property_meta('property_address_display') == 'yes') { ?>
			<span class="item-suburb"><?php echo $property->get_property_meta('property_address_suburb') . ', '; ?></span>
		<?php } ?>
				<span class="item-state"><?php echo $property->get_property_meta('property_address_state') . ' '; ?></span>
				<span class="item-pcode"><?php echo $property->get_property_meta('property_address_postal_code'); ?></span>
			</span>
		<?php 
	} else {
		// Address Display not Commercial or Business type
		if ( $property->get_property_meta('property_address_display') == 'yes' ) { ?>
			<span class="item-street"><?php echo $property->get_formatted_property_address(); ?></span>
			
		<?php } ?>
		<span class="entry-title-sub">
			<span class="item-suburb"><?php echo $property->get_property_meta('property_address_suburb') . ', '; ?></span>
			<span class="item-state"><?php echo $property->get_property_meta('property_address_state') . ' '; ?></span>
			<span class="item-pcode"><?php echo $property->get_property_meta('property_address_postal_code'); ?></span>
		</span>
		<?php
	} 
}
add_action('epl_single_the_title','epl_the_listing_address');
add_action('epl_property_tab_address','epl_the_listing_address');

/**
	@hooked property_price
	@hooked property_price_content
**/
function epl_property_price () {
	echo epl_get_property_price ();
}
add_action('epl_property_price','epl_property_price');
add_action('epl_property_price_content','epl_property_price');

/** 
	@hooked property_price
	@hooked property_price_content
**/
function epl_get_property_icons() {
	global $property;
	return $property->get_property_bed().
		$property->get_property_bath().
		$property->get_property_parking().
		$property->get_property_air_conditioning().
		$property->get_property_pool();
}
function epl_property_icons() {
	echo epl_get_property_icons();
}
add_action('epl_property_icons','epl_property_icons');

function epl_get_property_bb_icons() {
	global $property;
	return $property->get_property_bed().' '.
		$property->get_property_bath();
}

/** 
	@hooked property_land_category
**/
function epl_property_land_category(){
	global $property;
	echo $property->get_property_land_category();
}
add_action('epl_property_land_category','epl_property_land_category');

/** 
	@hooked property_commercial_category
**/
function epl_property_commercial_category(){
	global $property;
	if ( $property->post_type == 'commercial' ) {
		if ( $property->get_property_meta('property_com_plus_outgoings') == 1) {
			echo '<div class="price-type">'.__('Plus Outgoings', 'epl').'</div>';
		}
		echo $property->get_property_commercial_category();
	}
}
add_action('epl_property_commercial_category','epl_property_commercial_category');

/** 
	@hooked property_available_dates
**/
function epl_property_available_dates() {
	global $property;
	if( 'rental' == $property->post_type && $property->get_property_meta('property_date_available') != '' && $property->get_property_meta('property_status') != 'leased' ) { 
		// Rental Specifics
		echo '<div class="property-meta date-available">'.__('Available from', 'epl').' ', $property->get_property_meta('property_date_available'), '</div>';
	}
}
add_action('epl_property_available_dates','epl_property_available_dates');

/** 
	@hooked property_inspection_times
**/
function epl_property_inspection_times(){
	global $property;
	$property_inspection_times = $property->get_property_inspection_times();
	if($property_inspection_times != '') {
		$label_home_open = $property->get_epl_settings('label_home_open');	
	?>
	<div class="home-open">
		<strong><?php echo $label_home_open; ?></strong>
		<?php echo $property_inspection_times; ?>
	</div>
	<?php 
	}
}
add_action('epl_property_inspection_times','epl_property_inspection_times');

/** 
	@hooked the_property_heading
**/
function epl_the_property_heading(){
	global $property;
	echo $property->get_property_meta('property_heading');
}
add_action('epl_the_property_heading','epl_the_property_heading');

/** 
	@hooked property_secondary_heading
**/
function epl_property_secondary_heading() {
	global $property;
	echo $property->get_property_category();
	if($property->get_property_meta('property_status') == 'sold'){
		echo '<span class="sold-status">'.__('Sold', 'epl').'</span>';
	}
	echo ' <span class="suburb"> - ' . $property->get_property_meta('property_address_suburb') . ' </span>';
	echo ' <span class="state">' . $property->get_property_meta('property_address_state') . '</span>';	

}
add_action('epl_property_secondary_heading','epl_property_secondary_heading');

/** 
	@hooked property_after_content
**/
function epl_property_after_content() {
	global $property;
	$property_video_url = $property->get_property_meta('property_video_url');
	if($property_video_url != '') {
		$videoID = epl_get_youtube_id_from_url($property_video_url);
		echo '<div class="videoContainer">';
			// Echo the embed code via oEmbed
			echo wp_oembed_get( ('http://www.youtube.com/watch?v=' . $videoID) , array('width'=>600)  ); 
		echo '</div>';
	}
}
add_action('epl_property_after_content','epl_property_after_content');

/** 
	@hooked property_the_tab_section
**/
function epl_property_the_tab_section() {
	global $property;
	$post_type = $property->post_type;
	$the_property_feature_list = '';
	
	if ( 'commercial' == $post_type || 'commercial_land' == $post_type || 'business' == $post_type ) {
		$the_property_feature_list .= $property->get_property_commercial_category('li');
	}
	
	$the_property_feature_list .= $property->get_property_bed('l').' '.$property->get_property_bath('l').' '.$property->get_property_parking('l').' ';
	$the_property_feature_list .= $property->get_property_air_conditioning('l').' '.$property->get_property_pool('l');
	$the_property_feature_list .= $property->get_property_security_system('l').' '.$property->get_property_land_value('l');
	$the_property_feature_list .= $property->get_property_building_area_value('l').' '.$property->get_property_new_construction('l');
	$common_features 	= array(
							'property_toilet',
							'property_garage',
							'property_carport',
							'property_com_parking_comments',
							'property_com_car_spaces',
							'property_category',
						);
	foreach($common_features as $common_feature){
		$the_property_feature_list .= $property->get_additional_features_html($common_feature);
	}
	$specific_features 	= array (
		'property_remote_garage',
		'property_secure_parking',
		'property_study',
		'property_dishwasher',
		'property_built_in_robes',
		'property_gym',
		'property_workshop',
		'property_rumpus_room',
		'property_floor_boards',
		'property_broadband',
		'property_pay_tv',
		'property_vacuum_system',
		'property_intercom',
		'property_spa',
		'property_tennis_court',
		'property_balcony',
		'property_deck',
		'property_courtyard',
		'property_outdoor_entertaining',
		'property_shed',
		'property_open_fire_place',
		'property_ducted_cooling',
		'property_split_system_heating',
		'property_hydronic_heating',
		'property_split_system_aircon',
		'property_gas_heating',
		'property_reverse_cycle_aircon',
		'property_evaporative_cooling'

	);
	$specific_features = apply_filters('property_specific_features_list',$specific_features);
	
	if ( 'property' == $property->post_type || 'rental' == $property->post_type || 'rural' == $property->post_type){
		foreach($specific_features as $specific_feature){
			$the_property_feature_list .= $property->get_additional_features_html($specific_feature);
		}
	}
	
	if ( $property->post_type != 'land' || $property->post_type != 'business') { ?>
	<h5 class="tab-title"><?php _e('Property Features', 'epl'); ?></h5>
		<div class="tab-content">
			<ul class="listing-info epl-tab-<?php echo $property->get_epl_settings('display_feature_columns'); ?>-columns">
				<?php echo $the_property_feature_list; ?>							
			</ul>
		</div>
	<?php } ?>

	<div class="tab-content">
		<?php
			//Land Category
			if( 'land' == $property->post_type || 'commercial_land' == $property->post_type ) {
				echo $property->get_property_land_category(); //Land Category
			}
			
			//Availability
			if( 'rental' == $property->post_type && $property->get_property_meta('property_date_available') != '' && $property->get_property_meta('property_status') != 'leased' ) {
				echo '<div class="date-available">'.__('Available from', 'epl').' ', $property->get_property_meta('property_date_available'), '</div>';
			}

			//Commercial Options
			if ( $property->post_type == 'commercial' ) {
				if ( $property->get_property_meta('property_com_plus_outgoings') == 1) {
					echo '<div class="price-type">'.__('Plus Outgoings', 'epl').'</div>';
				}
				echo $property->get_property_commercial_category();						
			}
		?>
	</div>
	<?php
}
add_action('epl_property_the_tab_section','epl_property_the_tab_section');

/** 
	@hooked property_after_tab_section
**/
function epl_property_after_tab_section() {
	global $property;
	
	if ( $property->post_type == 'rural') { 
		$the_property_rural_feature_list = '';
		$features_lists = array(
							'property_rural_fencing',
							'property_rural_annual_rainfall',
							'property_rural_soil_types',
							'property_rural_improvements',
							'property_rural_council_rates',
							'property_rural_irrigation',
							'property_rural_carrying_capacity',
		);
		foreach($features_lists as $features_list){
			$the_property_rural_feature_list .= $property->get_additional_rural_features_html($features_list);
		}
	
	?>
		<div class="epl-tab-section">
			<h5 class="tab-title"><?php _e('Rural Features', 'epl'); ?></h5>
			<div class="tab-content">
				<div class="listing-info">
					<?php echo $the_property_rural_feature_list; ?>							
				</div>
			</div>
		</div>
	<?php }
}
add_action('epl_property_after_tab_section','epl_property_after_tab_section');

function epl_get_price_sticker() {
	global $property;
	return $property->get_price_sticker();
}

function epl_get_property_price () {
	global $property;
	return $property->get_price();
}

function epl_widget_listing_address ($d_suburb='',$d_street='') {
	global $property;
	if ($property->post_type == 'commercial' || $property->post_type == 'business' ){
		if ( $property->get_property_meta('property_address_display') == 'no' && $property->get_property_meta('property_com_display_suburb') == 'no') { ?>
			<div class="property-meta suburb-name">
				<span class="item-state"><?php echo $property->get_property_meta('property_address_state') . ' '; ?></span>
				<span class="item-pcode"><?php echo $property->get_property_meta('property_address_postal_code'); ?></span>
			</div>
		<?php } elseif ( $property->get_property_meta('property_address_display') == 'no' && $property->get_property_meta('property_com_display_suburb') == 'yes') { ?>
				<div class="property-meta suburb-name"><?php echo $property->get_property_meta('property_address_suburb'); ?></div>
		<?php } elseif ( $property->get_property_meta('property_address_display') == 'yes' && $property->get_property_meta('property_com_display_suburb') == 'no') { ?>
			<div class="property-meta suburb-name">
				<span class="item-state"><?php echo $property->get_property_meta('property_address_state') . ' '; ?></span>
				<span class="item-pcode"><?php echo $property->get_property_meta('property_address_postal_code'); ?></span>
			</div>
		<?php }
	} else {
		// Address Display not Commercial or Business type
		if ( $property->get_property_meta('property_address_display') == 'yes' ) { ?>
			<?php // Suburb
			if ( $d_suburb == 'on' ) { ?>
				<div class="property-meta suburb-name"><?php echo $property->get_property_meta('property_address_suburb'); ?></div>
			<?php } ?>
			
			<?php // Street
			if ( $d_street == 'on' ) { ?>
				<div class="property-meta street-name"><?php echo $property->get_formatted_property_address(); ?></div>
			<?php } ?>
		<?php } else { ?>
			<?php // Suburb
			if ( $d_suburb == 'on' ) { ?>
				<div class="property-meta suburb-name"><?php echo $property->get_property_meta('property_address_suburb'); ?></div>
			<?php } ?>
		<?php } 
	} 
}

function epl_switch_views_sorting() {
	$sortby = '';
	if(isset($_GET['sortby']) && trim($_GET['sortby']) != ''){
		$sortby = sanitize_text_field(trim($_GET['sortby']));
	}
	?>
	<div class="epl-switching-sorting-wrap epl-clearfix">
		<div class="epl-switch-view epl-clearfix">
			<ul>
				<li title="<?php _e('List','epl'); ?>" class="epl-current-view view-list" data-view="list">
				</li>
				<li title="<?php _e('Grid','epl'); ?>" class="view-grid" data-view="grid">
				</li data-view="grid">
			</ul>
		</div>
		<div class="epl-properties-sorting epl-clearfix">
			<select id="epl-sort-price">
				<option <?php selected( $sortby, '' ); ?> value=""><?php _e('Sort By Price','epl'); ?></option>
				<option <?php selected( $sortby, 'high' ); ?> value="high"><?php _e('High to Low','epl'); ?></option>
				<option <?php selected( $sortby, 'low' ); ?> value="low"><?php _e('Low to High','epl'); ?></option>
			</select>
		</div>
	</div>
	<?php
}
add_action( 'epl_template_before_property_loop' , 'epl_switch_views_sorting' , 20 );

function epl_archive_sorting($query) {
	$post_types_sold 	= array('property','land', 'commercial', 'business', 'commercial_land' , 'location_profile','rural');
	$post_types_rental 	= array('rental');
	$post_type = get_query_var( 'post_type' );
	if(is_post_type_archive( $post_types_sold ) || is_post_type_archive( $post_types_rental )){
		if(!$query->is_main_query()){
			return;
		}

		if(isset($_GET['sortby']) && trim($_GET['sortby']) != ''){
			$orderby = sanitize_text_field(trim($_GET['sortby']));
			if(in_array($post_type,$post_types_sold) ){ // properties other than rental
				if($orderby == 'high'){
					$query->set( 'orderby', 'meta_value_num' );
					$query->set( 'meta_key', 'property_price' );
					$query->set( 'order', 'DESC' );
				} elseif($orderby == 'low') {
					$query->set( 'orderby', 'meta_value_num' );
					$query->set( 'meta_key', 'property_price' );
					$query->set( 'order', 'ASC' );
				}
			}
			elseif(in_array($post_type,$post_types_rental)) { // rental property
				if($orderby == 'high'){
					$query->set( 'orderby', 'meta_value_num' );
					$query->set( 'meta_key', 'property_rent' );
					$query->set( 'order', 'DESC' );
				} elseif($orderby == 'low') {
					$query->set( 'orderby', 'meta_value_num' );
					$query->set( 'meta_key', 'property_rent' );
					$query->set( 'order', 'ASC' );
				}
			}
		} 
	}
}
add_action('pre_get_posts','epl_archive_sorting');
