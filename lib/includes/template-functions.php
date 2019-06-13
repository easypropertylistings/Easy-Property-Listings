<?php
/**
 * Template Functions
 *
 * @package     EPL
 * @subpackage  Functions/Templates
 * @copyright   Copyright (c) 2019, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Featured Image on archive template now loading through filter
 *
 * @since 2.2
 */
function epl_reset_property_object( $post ) {

	global $epl_author,$epl_author_secondary;

	if( !is_epl_post() )
		return;

	global $property;
	$property 		= new EPL_Property_Meta($post);

	if( $ID = epl_listing_has_primary_agent() ) {

		$epl_author = new EPL_Author_meta($ID);

	} else {

		$epl_author 	= new EPL_Author_meta($post->post_author);
	}

	if( $SEC_ID = epl_listing_has_secondary_author() ) {
	    $epl_author_secondary = new EPL_Author_meta($SEC_ID);
	}
}
add_action( 'the_post', 'epl_reset_property_object' );

/**
 * make $property global available for hooks before the_post
 *
 * @since 2.2
 */
function epl_create_property_object() {

	global $post,$property,$epl_author,$epl_author_secondary;

	if(is_author()) {
		$author_id 		=  get_query_var( 'author' );
		$epl_author		= new EPL_Author_meta($author_id);
	}
	if(is_null($post)){
		return;
	}
	$epl_author		= new EPL_Author_meta($post->post_author);

	if( is_epl_post() ){
		$property 	= new EPL_Property_Meta($post);
		if( $ID = epl_listing_has_secondary_author() ) {
		    $epl_author_secondary = new EPL_Author_meta($ID);
		}

	}
}

add_action( 'wp', 'epl_create_property_object' );

/**
 * Selecting Card Display Style
 *
 * @since 1.0
 */
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
		epl_property_single_default();
	}
}
add_action('epl_property_single','epl_property_single',10,1);

/**
 * Featured Image template now loading through filter
 *
 * @since 1.2
 */
function epl_property_featured_image( $image_size = 'index_thumbnail' , $image_class = 'index-thumbnail' , $link = true ) {

	/**
	 * Filter: Allow user or extension to enable or disable link behaviour on featured image.
	 */
	$link = apply_filters( 'epl_property_featured_image_link' , $link );

	if ( has_post_thumbnail() ) { ?>
		<div class="entry-image">
			<div class="epl-featured-image it-featured-image">
				<?php if ( $link == true ) { ?>
					<a href="<?php the_permalink(); ?>">
				<?php } ?>
						<?php the_post_thumbnail( $image_size , array( 'class' => $image_class ) ); ?>
				<?php if ( $link == true ) { ?>
					</a>
				<?php } ?>
			</div>
		</div>
	<?php }

}
add_action( 'epl_property_featured_image' , 'epl_property_featured_image' , 10 , 2);
add_action( 'epl_single_featured_image' , 'epl_property_featured_image' , 10 , 2 );

/**
 * Featured Image on archive template now loading through filter
 *
 * @since 2.2
 */
function epl_property_archive_featured_image( $image_size = 'epl-image-medium-crop' , $image_class = 'teaser-left-thumb' , $link = true  ) {

	if( $image_size == '' ) {
		$image_size = 'epl-image-medium-crop';
	}

	/**
	 * Filter: Allow user or extension to enable or disable link behaviour on archive image.
	 */
	$link = apply_filters( 'epl_property_archive_featured_image_link' , $link );

	if ( has_post_thumbnail() ) { ?>
		<div class="epl-archive-entry-image">
			<?php if ( $link == true ) { ?>
				<a href="<?php the_permalink(); ?>">
			<?php } ?>
					<div class="epl-blog-image">
						<div class="epl-stickers-wrapper">
							<?php echo epl_get_price_sticker(); ?>
						</div>
						<?php the_post_thumbnail( $image_size , array( 'class' => $image_class ) ); ?>
					</div>
			<?php if ( $link == true ) { ?>
				</a>
			<?php } ?>
		</div>
	<?php }

}
add_action( 'epl_property_archive_featured_image' , 'epl_property_archive_featured_image' , 10 , 3 );

/**
 * Featured Image in widgets
 *
 * @since 2.2
 */
function epl_property_widgets_featured_image( $image_size = 'epl-image-medium-crop' , $image_class = 'teaser-left-thumb' , $link = true  ) {

	if ( has_post_thumbnail() ) { ?>
		<div class="epl-archive-entry-image">
			<?php if ( $link == true ) { ?>
				<a href="<?php the_permalink(); ?>">
			<?php } ?>
					<div class="epl-blog-image">
						<?php the_post_thumbnail( $image_size , array( 'class' => $image_class ) ); ?>
					</div>
			<?php if ( $link == true ) { ?>
				</a>
			<?php } ?>
		</div>
	<?php }

}
add_action( 'epl_property_widgets_featured_image' , 'epl_property_widgets_featured_image' , 10 , 3 );

/**
 * Single Listing Templates
 *
 * @since 1.0
 */
function epl_property_single_default() {

	global $epl_settings;
	if( isset($epl_settings['epl_feeling_lucky']) && $epl_settings['epl_feeling_lucky'] == 'on') {

		epl_get_template_part('content-listing-single-compatibility.php');

	} else {
		$single_tpl = 'content-listing-single.php';
		$single_tpl = apply_filters('epl_property_single_default',$single_tpl);
		epl_get_template_part($single_tpl);
	}
}

/**
 * Template Path
 *
 * @since 2.0
 */
function epl_get_content_path() {
	return apply_filters('epl_templates_base_path',EPL_PATH_TEMPLATES_CONTENT);
}

/**
 * Template Fallback Path
 *
 * @since 3.0
 */
function epl_get_fallback_content_path() {
	return apply_filters('epl_templates_fallback_base_path',EPL_PATH_TEMPLATES_CONTENT);
}

/**
 * Attempts to load templates in order of priority
 *
 * @since 3.0
 */
function epl_get_template_part( $template , $arguments = array() ) {

	$base_path		= epl_get_content_path();
	$default		= $template;
	$find[] 		= epl_template_path() . $template;
	$template       	= locate_template( array_unique( $find ) );
	if(!$template) {
		$template	= $base_path . $default;
		if(!file_exists( $template ) ) {
			//fallback to core
			$base_path		= epl_get_fallback_content_path();
			$template	= $base_path . $default;
		}
	}
	if( !isset($arguments['epl_author']) ) {
		global $epl_author;
	}
	extract($arguments);

	include( $template);
}

/**
 * Modify the Excerpt length on Archive pages
 *
 * @since 1.0
 */
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

/**
 * Since 3.1.20  filter which listing status shouldnt be displayed
 *
 */
function epl_hide_listing_statuses() {

	return apply_filters( 'epl_hide_listing_statuses', array('withdrawn','offmarket') );
}

/**
 * Selecting Card Display Style
 *
 * Allows the use of one function where we can then select a different template when needed
 *
 * @since 1.0
 */
function epl_property_blog($template='') {

	if($template == '' || $template == 'blog') {
		$template = 'default';
	}
	$template = str_replace('_','-',$template);

	add_filter( 'excerpt_length', 'epl_archive_custom_excerpt_length', 999 );
	global $epl_settings,$property;

	if(	is_null($property) ) {
		return;
	}
	$option = '';
	if(!empty($epl_settings) && isset($epl_settings['epl_property_card_style'])) {
		$option = $epl_settings['epl_property_card_style'];
	}
	$property_status = $property->get_property_meta('property_status');
	// Status Removal Do Not Display Withdrawn or OffMarket listings
	if ( in_array( $property_status,epl_hide_listing_statuses() ) ) {
		// Do Not Display Withdrawn or OffMarket listings
	} else {
		$action_check = has_action( 'epl_loop_template' );
		if ( $action_check != '' && $option !== 0 && in_array($template,array('default','blog') ) ) {
			do_action( 'epl_loop_template' );
		} else {

			if( isset($epl_settings['epl_feeling_lucky']) && $epl_settings['epl_feeling_lucky'] == 'on') {

				epl_get_template_part('loop-listing-blog-'.$template.'-compatibility.php');

			} else {
				$tpl_name = 'loop-listing-blog-'.$template.'.php';
				$tpl_name = apply_filters('epl_property_blog_template',$tpl_name);
				epl_get_template_part($tpl_name);
			}

		}
	} // End Status Removal
}
add_action('epl_property_blog','epl_property_blog',10,1);

/**
 * Renders default author box
 * @since 3.2
 */
function epl_property_author_default() {
	global $epl_author_secondary;
	epl_get_template_part('content-author-box.php');
	if( is_epl_post() && epl_listing_has_secondary_author() ) {
	    epl_get_template_part('content-author-box.php',array('epl_author'	=>	$epl_author_secondary));
	    epl_reset_post_author();
	}
}
/**
 * AUTHOR CARD : Tabbed Style
 *
 * @since 1.0
 */
function epl_property_author_box() {

	if ( has_action( 'epl_author_template' ) ) {
		do_action( 'epl_author_template' );
	} else {
		epl_property_author_default();
	}
}

/**
 * Reset post author
 *
 * @since 1.0
 */
function epl_reset_post_author() {
	global $post, $epl_author;
	if(class_exists('EPL_Author_meta')) {

		if( is_epl_post() && ( $ID = epl_listing_has_primary_agent() ) ) {

			$epl_author = new EPL_Author_meta($ID);

		} else {

			$epl_author 	= new EPL_Author_meta($post->post_author);
		}
	}
}
add_action( 'epl_single_author' , 'epl_property_author_box' , 10 );

/**
 * AUTHOR CARD : Standard
 *
 * @since 1.0
 */
function epl_property_author_box_simple_card() {
	global $property,$epl_author,$epl_author_secondary;
	epl_get_template_part('content-author-box-simple-card.php');
	if( is_epl_post() && epl_listing_has_secondary_author() ) {
		    epl_get_template_part('content-author-box-simple-card.php',array('epl_author'	=>	$epl_author_secondary));
	}
	epl_reset_post_author();
}

/**
 * AUTHOR CARD : Gravatar
 *
 * @since 1.0
 */
function epl_property_author_box_simple_grav() {
	global $property,$epl_author,$epl_author_secondary;
	epl_get_template_part('content-author-box-simple-grav.php');
	if( is_epl_post() && epl_listing_has_secondary_author() ) {
		    epl_get_template_part('content-author-box-simple-grav.php',array('epl_author'	=>	$epl_author_secondary));
	}
	epl_reset_post_author();
}

/**
 * WIDGET LISTING : Listing Card
 *
 * @since 1.0
 */
function epl_property_widget( $display , $image , $title , $icons , $more_text = "__('Read More','easy-property-listings' )" , $d_excerpt , $d_suburb , $d_street , $d_price , $d_more, $d_inspection_time, $d_ical_link  ) {
	global $property;

	if( is_null($property) )
		return;

	$property_status = $property->get_property_meta('property_status');

	switch($display) {
		case 'list':
			$tpl = 'widget-content-listing-list.php';
		break;
		case 'hide-image':
			$tpl = 'widget-content-listing-hide-image.php';
		break;
		case 'image-only':
			$tpl = 'widget-content-listing-image.php';
		break;
		case 'image':
			$tpl = 'widget-content-listing.php';
		break;
		default:
			$tpl = $display;
		break;

	}

	// Status Removal
	if ( in_array( $property_status,epl_hide_listing_statuses() ) ) {
		// Do Not Display Withdrawn or OffMarket listings
	} else {
		$arg_list = get_defined_vars();
		if ( has_action( 'epl_listing_widget_template' ) ) {
			do_action( 'epl_listing_widget_template',$tpl,$arg_list );
		} else {
			epl_get_template_part($tpl,$arg_list);
		}

	} // End Status Removal
}

/**
 * WIDGET LISTING : Listing List
 *
 * @since 1.0
 */
function epl_property_widget_list_option() {
	$property_status = get_post_meta( get_the_ID(), 'property_status', true );
	// Status Removal
	if ( in_array( $property_status,epl_hide_listing_statuses() ) ) {
		// Do Not Display Withdrawn or OffMarket listings
	} else {
		epl_get_template_part('widget-content-listing-list.php');
	} // End Status Removal
}

/**
 * WIDGET LISTING : Image Only
 *
 * @since 1.0
 */
function epl_property_widget_image_only_option( $image ) {
	$property_status = get_post_meta( get_the_ID(), 'property_status', true );
	// Status Removal
	if ( in_array( $property_status,epl_hide_listing_statuses() ) ) {
		// Do Not Display Withdrawn or OffMarket listings
	} else {
		$arg_list = get_defined_vars();
		epl_get_template_part('widget-content-listing-image.php',$arg_list);
	} // End Status Removal
}

/**
 * WIDGET LISTING : Widget Tall Card
 *
 * @since 1.0
 * @revised 3.3
 */
function epl_property_author_box_simple_card_tall( $d_image , $d_icons , $d_bio, $username) {

	if( !empty($username) ) {
		epl_show_author_widget_by_username($d_image , $d_icons , $d_bio, $username);
		return;
	}

	global $property,$epl_author,$epl_author_secondary;
	if( is_null($epl_author) )
		return;

	$arg_list = get_defined_vars();
	epl_get_template_part('widget-content-author-tall.php',$arg_list);

	// Second Author
	if ( is_single() && !is_null($property) ) {
		if( is_epl_post() && epl_listing_has_secondary_author() ) {
				$epl_author = $epl_author_secondary;
			    epl_get_template_part('widget-content-author-tall.php',$arg_list);
		}
		epl_reset_post_author();
	}
}

/**
 * Display widget by username
 *
 * @since 3.3
 */
function epl_show_author_widget_by_username($d_image , $d_icons , $d_bio, $username) {
	$username = explode(',',$username);
	$username = array_filter($username);
	foreach($username as $uname) {
		$author = get_user_by( 'login' , sanitize_user($uname) );
		if($author !== false){
			$epl_author = new EPL_Author_meta($author->ID);
			$arg_list = get_defined_vars();
			epl_get_template_part('widget-content-author-tall.php',$arg_list);
		}
	}
}

/**
 * Get the full address
 *
 * @since 1.0
 */
function epl_property_get_the_full_address(){
	global $property;

		$address = '';
		if($property->get_property_meta('property_address_sub_number') != '') {
			$address .= $property->get_property_meta('property_address_sub_number') . '/';
		}
		$address .= $property->get_property_meta('property_address_street_number') . ' ';
		$address .= $property->get_property_meta('property_address_street') . ' ';
		$address .= $property->get_property_meta('property_address_suburb') . ' ';
		$address .= $property->get_property_meta('property_address_city') . ', ';
		$address .= $property->get_property_meta('property_address_state') . ' ';
		$address .= $property->get_property_meta('property_address_postal_code').' ';
		$address .= $property->get_property_meta('property_address_country');

	return $address;
}

/**
 * Get the full address
 *
 * @since 1.0
 * @revised 3.3.3
 * @hooked epl_property_title
 * @hooked property_tab_address
 */
function epl_property_the_address() {

	$epl_property_address_seperator	= apply_filters('epl_property_address_seperator',',');

	global $property, $epl_settings;

	?>
	<?php if ( $property->get_property_meta('property_address_display') == 'yes' ) { ?>
		<span class="item-street"><?php echo $property->get_formatted_property_address(); ?></span>
	<?php } ?>
	<span class="entry-title-sub">

		<?php
			if ( $property->post_type == 'commercial' || $property->post_type == 'business' ){
				if( $property->get_property_meta('property_com_display_suburb') == 'yes' || $property->get_property_meta('property_address_display') == 'yes' ) { ?>
					<span class="item-suburb"><?php echo $property->get_property_meta('property_address_suburb'); ?></span><?php
					if ( strlen( trim( $property->get_property_meta( 'property_address_suburb' ) ) ) ) {
						echo '<span class="item-seperator">' . $epl_property_address_seperator . '</span>';
					}
				}

			} else { ?>
				<span class="item-suburb"><?php echo $property->get_property_meta('property_address_suburb'); ?></span><?php
				if ( strlen( trim( $property->get_property_meta( 'property_address_suburb' ) ) ) ) {
					echo '<span class="item-seperator">' . $epl_property_address_seperator . '</span>';
				}
			}
		?>

		<?php
			if( $property->get_epl_settings('epl_enable_city_field') == 'yes' ) { ?>
				<span class="item-city"><?php echo $property->get_property_meta('property_address_city'); ?></span><?php
			}
		?>
		<span class="item-state"><?php echo $property->get_property_meta('property_address_state'); ?></span>
		<span class="item-pcode"><?php echo $property->get_property_meta('property_address_postal_code'); ?></span>
		<?php
			if( $property->get_epl_settings('epl_enable_country_field') == 'yes' ) { ?>
				<span class="item-country"><?php echo $property->get_property_meta('property_address_country'); ?></span><?php
			}
		?>
	</span><?php
}
add_action('epl_property_title','epl_property_the_address');
add_action('epl_property_tab_address','epl_property_the_address');
add_action('epl_property_address','epl_property_the_address');

/*
 * Suburb Name
 * Kept for listing templates extensions which use this function
 *
 * @since 1.3
 * @revised 3.1.18
 */
function epl_property_suburb() {
	global $property;
	// Commercial and Business Address
	if ($property->post_type == 'commercial' || $property->post_type == 'business' ) { ?>

		<span class="entry-title-sub">
			<?php if ( $property->get_property_meta('property_com_display_suburb') == 'yes') { ?>
				<span class="item-suburb"><?php echo $property->get_property_meta('property_address_suburb')?></span>
			<?php } else { ?>

				<?php if ( $property->get_property_meta('property_address_city') != '' ) { ?>
					<span class="item-city"><?php echo $property->get_property_meta('property_address_city') . ' '; ?></span>
				<?php } ?>

				<span class="item-state"><?php echo $property->get_property_meta('property_address_state') . ' '; ?></span>
				<span class="item-pcode"><?php echo $property->get_property_meta('property_address_postal_code'); ?></span>
			<?php } ?>
		</span>

	<?php
	} else { ?>
		<span class="entry-title-sub">
			<span class="item-suburb"><?php echo $property->get_property_meta('property_address_suburb')?></span>
		</span>
	<?php
	}
}
add_action( 'epl_property_suburb' , 'epl_property_suburb' );

/**
 * Get the price
 *
 * @since 1.0
 * @hooked property_price
 * @hooked property_price_content
 */
function epl_property_price () {
	echo epl_get_property_price ();
}
add_action('epl_property_price','epl_property_price');
add_action('epl_property_price_content','epl_property_price');

/**
 * Get Property icons
 *
 * @since 1.0
 * @revised 3.3
 */
function epl_get_property_icons( $args = array() , $returntype = 'i') {

	global $property;

	$defaults 	= array('bed','bath','parking','ac','pool');

	$icons 		= apply_filters('epl_get_property_icons', $defaults);
	$returntype	= apply_filters('epl_icons_return_type', $returntype);

	ob_start();

	foreach($icons as $icon) {


		if( !empty($args) && !in_array($icon,$args) ) {
			continue;
		}

		switch($icon) {

			case 'bed' :
				echo $property->get_property_bed($returntype);
			break;

			case 'bath' :
				echo $property->get_property_bath($returntype);
			break;

			case 'parking' :
				echo $property->get_property_parking($returntype);
			break;

			case 'ac' :
				echo $property->get_property_air_conditioning($returntype);
			break;

			case 'pool' :
				echo $property->get_property_pool($returntype);
			break;

			default:
				// action to hook additional icons
				do_action('epl_get_property_icon_'.$icon);
			break;
		}
	}

	return ob_get_clean();
}

/**
 * Property icons
 *
 * @since 1.0
 * @revised 3.3
 */
function epl_property_icons($returntype = 'i') {
	$returntype = $returntype == '' ? 'i' : $returntype;
	echo epl_get_property_icons(array(),$returntype);
}
add_action( 'epl_property_icons' , 'epl_property_icons' , 10 , 1 );

/**
 * Property bed/bath icons
 *
 * @since 1.0
 */
function epl_get_property_bb_icons() {
	global $property;
	return $property->get_property_bed().' '.
		$property->get_property_bath();
}

/**
 * Property land category
 *
 * @since 1.0
 * @hooked property_land_category
 */
function epl_property_land_category(){
	global $property;
	echo $property->get_property_land_category();
}
add_action('epl_property_land_category','epl_property_land_category');

/**
 * Property Commercial category
 *
 * @since 1.0
 * @hooked property_commercial_category
 */
function epl_property_commercial_category(){
	global $property;
	if ( $property->post_type == 'commercial' ) {
		if ( $property->get_property_meta('property_com_plus_outgoings') == 1) {
			echo '<div class="price-type">'. apply_filters( 'epl_property_sub_title_plus_outgoings_label' , __('Plus Outgoings', 'easy-property-listings' ) ) . '</div>';
		}
		echo $property->get_property_commercial_category();
	}
}
add_action('epl_property_commercial_category','epl_property_commercial_category');

/**
 * Property Available Dates
 *
 * @since 1.0
 * @hooked property_available_dates
 */
function epl_property_available_dates() {
	global $property;
	if( 'rental' == $property->post_type && $property->get_property_meta('property_date_available') != '' && $property->get_property_meta('property_status') != 'leased' ) {
		// Rental Specifics
		echo '<div class="property-meta date-available">'. apply_filters( 'epl_property_sub_title_available_from_label' , __('Available from', 'easy-property-listings' ) ) .' ', $property->get_property_available() , '</div>';
	}
}
add_action('epl_property_available_dates','epl_property_available_dates');

/**
 * Property Inspection Times
 *
 * @since 1.0
 * @hooked property_inspection_times
 */
function epl_property_inspection_times(){
	global $property;
	$property_inspection_times = $property->get_property_inspection_times();
	$label_home_open = '';
	if(trim($property_inspection_times) != '') {
		$label_home_open = $property->get_epl_settings('label_home_open');

		$label_home_open = apply_filters('epl_inspection_times_label',$label_home_open);
	?>
	<div class="epl-inspection-times">
		<span class="epl-inspection-times-label">
			<?php echo $label_home_open; ?>
		</span>
		<?php echo $property_inspection_times; ?>
	</div>
	<?php
	}
}
add_action('epl_property_inspection_times','epl_property_inspection_times');

/**
 * Getting heading/title of the listing.
 *
 * @since  2.3.1
 * @param  int|WP_Post|EPL_Property_Meta  $listing
 * @return string listing heading or title
 */
function epl_get_property_heading( $listing = null ) {
	if ( null === $listing ) {
		global $property;
	} else if ( $listing instanceof EPL_Property_Meta ) {
		$property = $listing;
	} else if ( $listing instanceof WP_Post ) {
		$property = new EPL_Property_Meta( $listing );
	} else {
		$property = get_post( $listing );
		$property = new EPL_Property_Meta( $property );
	}

	if ( $property ) {
		$property_heading = $property->get_property_meta( 'property_heading' );
		if ( strlen( trim( $property_heading ) ) ) {
			return $property_heading;
		}
		return get_the_title( $property->post->ID );
	}
	return '';
}

/**
 * Property Heading
 *
 * @since 1.0
 * @hooked the_property_heading
 */
function epl_property_heading( $listing = null ){
	echo epl_get_property_heading( $listing );
}
add_action('epl_property_heading','epl_property_heading');

/**
 * Property Heading
 *
 * @since 1.0
 * @hooked property_secondary_heading
 */
function epl_property_secondary_heading() {
	global $property;


	if ( $property->post_type == 'property' || $property->post_type == 'rental' ) {
		echo $property->get_property_category( 'span' , 'epl-property-category' );
	}

	if ( $property->post_type == 'rural' ) {
		echo $property->get_property_rural_category( 'span' , 'epl-rural-category' );
	}

	if ( $property->post_type == 'commercial' || $property->post_type == 'commercial_land' ) {
		echo $property->get_property_commercial_category( 'span' , 'epl-commercial-category' );
	}

	if($property->get_property_meta('property_status') == 'sold'){
		echo ' <span class="sold-status">'.$property->label_sold.'</span>';
	}
	echo ' <span class="suburb"> - ' . $property->get_property_meta('property_address_suburb') . ' </span>';
	echo ' <span class="state">' . $property->get_property_meta('property_address_state') . '</span>';
}
add_action('epl_property_secondary_heading','epl_property_secondary_heading');

/**
 * Property Category
 *
 * @since 1.0
 */
function epl_property_category() {
	global $property;
	echo $property->get_property_category( 'value' );
}

/**
 * Video type
 *
 * @since 3.3
 */
function epl_get_video_host($url) {

	$host = 'unknown';

	if (strpos($url, 'youtu') > 0) {
		$host = 'youtube';
	} elseif (strpos($url, 'vimeo') > 0) {
		$host = 'vimeo';
	}

	return $host;
}

/**
 * Property Video HTML
 *
 * @since 1.0
 * @revised 3.3
 */
function epl_get_video_html($property_video_url='',$width=600) {

	/** remove related videos from youtube */
	if( epl_get_video_host($property_video_url) == 'youtube' ) {

		if (strpos($property_video_url, '?') > 0) {
			$property_video_url .= '&rel=0';
		} else {
			$property_video_url .= '?rel=0';
		}

	}
	$width = epl_get_option('epl_video_width',$width);
	if($property_video_url != '') {
		$video_html =  '<div class="epl-video-container videoContainer">';

			$video_html .=  wp_oembed_get(
				$property_video_url,
				array( 'width' => apply_filters( 'epl_property_video_width', $width  ) )
			);
		$video_html .= '</div>';
		return $video_html;
	}
}

/**
 * Video Output Function
 *
 * @since 1.0
 * @revised 3.3
 * @hooked property_after_content
 */
function epl_property_video_callback( $width = 600 ) {

	global $property;
	$video_width 		= $width != '' ? $width : 600;
	$property_video_url	= $property->get_property_meta('property_video_url');
	echo epl_get_video_html($property_video_url,$video_width);

}
add_action('epl_property_video','epl_property_video_callback' , 10 , 1);

/**
 * Previous Video Hook, maintained for backward compatibility.
 *
 * @since 3.3
 * @hooked property_after_content
 */
add_action('epl_property_content_after','epl_property_video_callback' , 10 , 1);

/**
 * Property Tab section details output
 *
 * @since 1.0
 * @hooked property_tab_section
 */
function epl_property_tab_section() {
	global $property;
	$post_type = $property->post_type;
	$the_property_feature_list = apply_filters('epl_the_property_feature_list_before', '' );

	$general_features_array = array(
		'category',
		'rural_category',
		'commercial_category',
		'bed',
		'bath',
		'rooms',
		'year_built',
		'parking',
		'ac',
		'pool',
		'security',
		'land_value',
		'building_value',
		'energy_rating',
		'new_construction',
	);

	$general_features_array = apply_filters('epl_property_general_features_list', $general_features_array);

	foreach( $general_features_array as $general_feature) {

		switch($general_feature) {

			case 'category':

				if ( 'property' == $post_type || 'rental' == $post_type ) {
					$the_property_feature_list .= $property->get_property_category('li');
				}

			break;

			case 'rural_category':

				if ( 'rural' == $post_type ) {
					$the_property_feature_list .= $property->get_property_rural_category('li');
				}

			break;

			case 'commercial_category':

				if ( 'commercial' == $post_type || 'commercial_land' == $post_type || 'business' == $post_type ) {
					$the_property_feature_list .= $property->get_property_commercial_category('li');
				}

			break;

			case 'bed':

				$the_property_feature_list .= $property->get_property_bed('l').' ';

			break;

			case 'bath':

				$the_property_feature_list .= $property->get_property_bath('l').' ';

			break;

			case 'rooms':

				$the_property_feature_list .= $property->get_property_rooms('l').' ';

			break;

			case 'year_built':

				$the_property_feature_list .= $property->get_property_year_built('l').' ';

			break;

			case 'parking':

				$the_property_feature_list .= $property->get_property_parking('l').' ';

			break;

			case 'ac':

				$the_property_feature_list .= $property->get_property_air_conditioning('l').' ';

			break;

			case 'pool':

				$the_property_feature_list .= $property->get_property_pool('l');

			break;

			case 'security':

				$the_property_feature_list .= $property->get_property_security_system('l').' ';

			break;

			case 'land_value':

				$the_property_feature_list .= $property->get_property_land_value('l');

			break;

			case 'building_value':

				$the_property_feature_list .= $property->get_property_building_area_value('l').' ';

			break;

			case 'energy_rating':

				$the_property_feature_list .= $property->get_property_energy_rating('l');

			break;

			case 'new_construction':

				$the_property_feature_list .= $property->get_property_new_construction('l');

			break;

			default :

				do_action('epl_property_general_feature_'.$general_feature);

			break;

		}
	}


	$the_property_feature_list .= apply_filters('epl_the_property_feature_list_before_common_features', '' );

	$common_features = array(
				'property_toilet',
				'property_ensuite',
				'property_pet_friendly',
				'property_garage',
				'property_carport',
				'property_open_spaces',
				'property_com_parking_comments',
				'property_com_car_spaces',
				'property_category',
				'property_holiday_rental',
				'property_furnished',
			);
	$common_features = apply_filters('epl_property_common_features_list',$common_features);

	foreach($common_features as $common_feature){
		$the_property_feature_list .= $property->get_additional_features_html($common_feature);
	}

	$the_property_feature_list .= apply_filters('epl_the_property_feature_list_before_additional_features', '' );

	$additional_features 	= array (
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
		'property_ducted_heating',
		'property_ducted_cooling',
		'property_split_system_heating',
		'property_hydronic_heating',
		'property_split_system_aircon',
		'property_gas_heating',
		'property_reverse_cycle_aircon',
		'property_evaporative_cooling',
		'property_land_fully_fenced'
	);
	$additional_features = apply_filters('epl_property_additional_features_list',$additional_features);

	if ( 'property' == $property->post_type || 'rental' == $property->post_type || 'rural' == $property->post_type){
		foreach($additional_features as $additional_feature){
			$the_property_feature_list .= $property->get_additional_features_html($additional_feature);
		}
	}

	$the_property_feature_list .= apply_filters('epl_the_property_feature_list_after', '' );

	if ( $property->post_type != 'land' || $property->post_type != 'business') { ?>
		<?php $property_features_title = apply_filters( 'epl_property_sub_title_property_features' , __('Property Features', 'easy-property-listings' ) ); ?>
		<h5 class="epl-tab-title epl-tab-title-property-features tab-title"><?php echo $property_features_title; ?></h5>
			<div class="epl-tab-content tab-content">
				<ul class="epl-property-features listing-info epl-tab-<?php echo $property->get_epl_settings('display_feature_columns'); ?>-columns">
					<?php echo $the_property_feature_list.' '.$property->get_features_from_taxonomy(); ?>
				</ul>
			</div>
	<?php } ?>

	<div class="epl-tab-content epl-tab-content-additional tab-content">
		<?php
			//Land Category
			if( 'land' == $property->post_type || 'commercial_land' == $property->post_type ) {
				echo '<div class="epl-land-category">' . $property->get_property_land_category('value') . '</div>';
			}

			//Commercial Options
			if ( $property->post_type == 'commercial' ) {
				if ( $property->get_property_meta('property_com_plus_outgoings') == 1) {
					echo '<div class="epl-commercial-outgoings price-type">' . apply_filters( 'epl_property_sub_title_plus_outgoings' ,__('Plus Outgoings', 'easy-property-listings' ) ) .'</div>';
				}
				// echo $property->get_property_commercial_category();
			}
		?>
	</div>
	<?php
}
add_action('epl_property_tab_section','epl_property_tab_section');

/**
 * Property Tab section details output for commercial, business and commercial land
 *
 * @since 1.0
 * @hooked property_after_tab_section
 */
function epl_property_tab_section_after() {
	global $property;
	$post_type = $property->post_type;
	if ( 'commercial' == $post_type || 'business' == $post_type || 'commercial_land' == $post_type) {

		$the_property_commercial_feature_list = '';
		$features_lists = array(
			'property_com_further_options',
			'property_com_highlight_1',
			'property_com_highlight_2',
			'property_com_highlight_3',
			'property_com_zone',
		);

		// Check for values in the commercial features
		$commercial_value = '';

		$result = array();

		foreach ( $features_lists as $feature ) {

			$commercial_value = $property->get_property_meta( $feature );

			if ( $commercial_value != '' ) {
				$result[] = $commercial_value;
			}
		}

		// Display results if $result array is not empty
		if ( ! empty( $result ) ) {

			foreach( $features_lists as $features_list ){
				$the_property_commercial_feature_list .= $property->get_additional_commerical_features_html($features_list);
			}

		?>
			<div class="epl-tab-section epl-tab-section-commercial-features">
				<h5 class="epl-tab-title epl-tab-title-commercial-features tab-title"><?php echo apply_filters( 'epl_property_sub_title_commercial_features' , __('Commercial Features', 'easy-property-listings' ) ); ?></h5>
				<div class="epl-tab-content tab-content">
					<div class="epl-commercial-features listing-info">
						<?php echo $the_property_commercial_feature_list; ?>
					</div>
				</div>
			</div>
		<?php
		}
	}

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

		// Check for values in the rural features
		$rural_value = '';

		$result = array();

		foreach ( $features_lists as $feature ) {

			$rural_value = $property->get_property_meta( $feature );

			if ( $rural_value != '' ) {
				$result[] = $rural_value;
			}
		}


		// Display results if $result array is not empty
		if ( ! empty( $result ) ) {

			foreach($features_lists as $features_list){
				$the_property_rural_feature_list .= $property->get_additional_rural_features_html($features_list);
			}

		?>
		<div class="epl-tab-section epl-tab-section-rural-features">
			<h5 class="epl-tab-title epl-tab-title-rural-features tab-title"><?php echo apply_filters( 'epl_property_sub_title_rural_features' , __('Rural Features', 'easy-property-listings' ) ); ?></h5>
			<div class="epl-tab-content tab-content">
				<div class="epl-rural-features listing-info">
					<?php echo $the_property_rural_feature_list; ?>
				</div>
			</div>
		</div>
		<?php
		}
	}
}
add_action('epl_property_tab_section_after','epl_property_tab_section_after');

/**
 * Get price sticker
 *
 * @since 1.0
 */
function epl_get_price_sticker() {
	global $property;
	return $property->get_price_sticker();
}

/**
 * Get Property Price
 *
 * @since 1.0
 */
function epl_get_property_price () {
	global $property;
	return $property->get_price();
}

/**
 * Get listing Address for Widget
 *
 * @since 1.0
 */
function epl_widget_listing_address ( $d_suburb = '' , $d_street = '' ) {
	global $property;
	if ($property->post_type == 'commercial' || $property->post_type == 'business' ){
		// Address Display not Commercial or Business type
		if ( $property->get_property_meta('property_address_display') == 'yes' ) { ?>
			<?php // Suburb
			if ( $d_suburb == 'on' && $property->get_property_meta('property_com_display_suburb') == 'yes' ) { ?>
				<div class="property-meta suburb-name"><?php echo $property->get_property_meta('property_address_suburb'); ?></div>
			<?php } ?>

			<?php // Street
			if ( $d_street == 'on' ) { ?>
				<div class="property-meta street-name"><?php echo $property->get_formatted_property_address(); ?></div>
			<?php } ?>
		<?php } else { ?>
			<?php // Suburb
			if ( $d_suburb == 'on' && $property->get_property_meta('property_com_display_suburb') == 'yes' ) { ?>
				<div class="property-meta suburb-name"><?php echo $property->get_property_meta('property_address_suburb'); ?></div>
			<?php } ?>
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

/**
 * Get Sorting Options
 *
 * @since 2.0
 */
function epl_sorting_options($post_type = null) {

	if( is_null($post_type) ) {
		$post_type = isset($_GET['post_type']) ? sanitize_text_field($_GET['post_type']) : 'property';
	}

	return apply_filters('epl_sorting_options',array(
		array(
			'id'		=>	'high',
			'label'		=>	__('Price: High to Low','easy-property-listings' ),
			'type'		=>	'meta',
			'key'		=>	'property_price_global',
			'order'		=>	'DESC',
			'orderby'	=>	'meta_value_num',
		),
		array(
			'id'		=>	'low',
			'label'		=>	__('Price: Low to High','easy-property-listings' ),
			'type'		=>	'meta',
			'key'		=>	'property_price_global',
			'order'		=>	'ASC',
			'orderby'	=>	'meta_value_num',

		),
		array(
			'id'		=>	'new',
			'label'		=>	__('Date: Newest First','easy-property-listings' ),
			'type'		=>	'post',
			'key'		=>	'post_date',
			'order'		=>	'DESC'


		),
		array(
			'id'		=>	'old',
			'label'		=>	__('Date: Oldest First','easy-property-listings' ),
			'type'		=>	'post',
			'key'		=>	'post_date',
			'order'		=>	'ASC'
		),
		array(
			'id'		=>	'status_asc',
			'label'		=>	__('Status : Current First','easy-property-listings' ),
			'type'		=>	'meta',
			'key'		=>	'property_status',
			'order'		=>	'ASC',
			'orderby'	=>	'meta_value',

		),
		array(
			'id'		=>	'status_desc',
			'label'		=>	__('Status : Sold/Leased First','easy-property-listings' ),
			'type'		=>	'meta',
			'key'		=>	'property_status',
			'order'		=>	'DESC',
			'orderby'	=>	'meta_value',

		),
		array(
			'id'		=>	'location_asc',
			'label'		=>	epl_labels('label_suburb'). __(' A-Z' , 'easy-property-listings' ),
			'type'		=>	'meta',
			'key'		=>	'property_address_suburb',
			'order'		=>	'ASC',
			'orderby'	=>	'meta_value',

		),
		array(
			'id'		=>	'location_desc',
			'label'		=>	epl_labels('label_suburb'). __(' Z-A' , 'easy-property-listings' ),
			'type'		=>	'meta',
			'key'		=>	'property_address_suburb',
			'order'		=>	'DESC',
			'orderby'	=>	'meta_value',

		),
	) );
}

/**
 * Switch Sorting Wrapper
 *
 * @since 3.3
 */
function epl_tools_utility_wrapper() {

	// Wrapper Start
	do_action('epl_archive_utility_wrap_start');

		do_action('epl_add_custom_menus');

	// Wrapper End
	do_action('epl_archive_utility_wrap_end');

}
add_action( 'epl_property_loop_start' , 'epl_tools_utility_wrapper' , 10 );

/**
 * Switch Sorting Wrapper
 *
 * @since 2.0
 * @revised 3.3
 */
function epl_listing_toolbar_items() {

	echo get_epl_listing_toolbar_items();

}
add_action( 'epl_add_custom_menus' , 'epl_listing_toolbar_items' , 10 );

/**
 * Retrieves the switch and sorting options normally right aligned
 *
 * @since 3.3
 *
 * @return string
 */
function get_epl_listing_toolbar_items( $args = array() ) {

	$defaults = array(
		'switch_views',
		'sorting_tool',
	);

	$tools = apply_filters('epl_listing_toolbar_items', $defaults);

	ob_start();

	// Wrapper
	if ( !empty( $defaults ) ) { ?>
		<div class="epl-loop-tools epl-loop-tools-switch-sort epl-switching-sorting-wrap">
			<?php
				foreach( $tools as $tool ) {

					if( !empty( $args ) && !in_array( $tool, $args ) ) {
						continue;
					}

					switch( $tool ) {

						case 'switch_views' :
							do_action('epl_switch_views');
							break;

						case 'sorting_tool' :
							do_action('epl_sorting_tool');
							break;

						default :
							// action to hook additional tools
							do_action( 'epl_listing_toolbar_' . $tool );
							break;
					}
				}
			?>
		</div>
		<?php
	}
	return ob_get_clean();
}

/**
 * Switch Views
 *
 * @since 2.0
 *
 * @return string
 */
function epl_switch_views() { ?>
	<div class="epl-loop-tool epl-tool-switch epl-switch-view">
		<ul>
			<li title="<?php echo apply_filters( 'epl_switch_views_sorting_title_list' , __('List','easy-property-listings' ) ); ?>" class="epl-current-view view-list" data-view="list">
			</li>
			<li title="<?php echo apply_filters( 'epl_switch_views_sorting_title_grid' , __('Grid','easy-property-listings' ) ); ?>" class="view-grid" data-view="grid">
			</li>
		</ul>
	</div> <?php
}
add_action('epl_switch_views','epl_switch_views');

/**
 * Displays the Switch Sorting select options
 *
 * @since 2.0
 * @revised 3.3
 *
 * @return string
 */
function epl_sorting_tool() {
	$sortby = '';
	if(isset($_GET['sortby']) && trim($_GET['sortby']) != ''){
		$sortby = sanitize_text_field(trim($_GET['sortby']));
	}
	$sorters = epl_sorting_options(); ?>

	<div class="epl-loop-tool epl-tool-sorting epl-properties-sorting epl-clearfix">
		<select id="epl-sort-listings">
			<option <?php selected( $sortby, '' ); ?> value="">
				<?php echo apply_filters( 'epl_switch_views_sorting_title_sort' , __('Sort','easy-property-listings' ) ); ?>
			</option>
			<?php
				foreach($sorters as $sorter) { ?>
					<option <?php selected( $sortby, $sorter['id'] ); ?> value="<?php echo $sorter['id']; ?>">
						<?php echo $sorter['label']; ?>
					</option> <?php
				}
			?>
		</select>
	</div>
<?php
}
add_action('epl_sorting_tool','epl_sorting_tool');

/**
 * Displays the Sorting tabs
 *
 * @since 3.3
 *
 * @return string
 */
function epl_sorting_tabs() {
	$sortby = '';
	if(isset($_GET['sortby']) && trim($_GET['sortby']) != ''){
		$sortby = sanitize_text_field(trim($_GET['sortby']));
	}
	$sorters = epl_sorting_options();

	global $wp;
	$current_url = home_url(add_query_arg(array($_GET), $wp->request)); ?>

	<div class="epl-loop-tool epl-tool-sorting-tabs epl-properties-sorting epl-clearfix">
		<ul id="epl-sort-tabs-listings">

			<?php
				foreach($sorters as $sorter) {
					$href = epl_add_or_update_params($current_url,'sortby',$sorter['id']);
					$class = $sortby == $sorter['id'] ? 'epl-sortby-selected' : '';
					?>
					<li class="epl-sortby-list <?php echo $class?>">
						<a href="<?php echo $href; ?>">
							<?php echo $sorter['label']; ?>
						</a>
					</li> <?php
				}
			?>
		</ul>
	</div>
<?php
}

/**
 * Update parameters
 *
 * @since 3.3
 *
 * @return string
 */
function epl_add_or_update_params($url,$key,$value){

    $a = parse_url($url);
    $query = isset($a['query']) ? $a['query'] : '';
    parse_str($query,$params);
    $params[$key] = $value;
    $query = http_build_query($params);
    $result = '';
    if($a['scheme']){
        $result .= $a['scheme'] . ':';
    }
    if($a['host']){
        $result .= '//' . $a['host'];
    }
    if(!empty($a['port'])){
        $result .= ':' . $a['port'];
    }
    if($a['path']){
        $result .=  $a['path'];
    }
    if($query){
        $result .=  '?' . $query;
    }
    return $result;
}

/**
 * Archive Sorting
 *
 * @since 2.0
 */
function epl_archive_sorting($query) {
	$post_types_sold 	= array('property','land', 'commercial', 'business', 'commercial_land' , 'location_profile','rural');
	$post_types_rental 	= array('rental');

	if(!$query->is_main_query()){
		return;
	}

	if(is_post_type_archive( $post_types_sold ) || is_post_type_archive( $post_types_rental ) || is_tax ( 'location' ) || is_tax ( 'tax_feature' ) || is_tax ( 'tax_business_listing' ) || epl_is_search() ){

		if(isset($_GET['sortby']) && trim($_GET['sortby']) != ''){

			$orderby = sanitize_text_field(trim($_GET['sortby']));
			$sorters = epl_sorting_options( $query->get( 'post_type') );

			foreach($sorters as $sorter) {

				if($orderby == $sorter['id']){

					if($sorter['type'] == 'meta') {
						$query->set( 'orderby', $sorter['orderby'] );
						$query->set( 'meta_key', $sorter['key'] );
					} else {
						$query->set( 'orderby', $sorter['key'] );
					}
					$query->set( 'order', $sorter['order'] );
					break;
				}

			}
		}
	}
}
add_action('pre_get_posts','epl_archive_sorting');

/**
 * Author Tabs
 *
 * @since 1.0
 */
function epl_author_tabs () {
	global $epl_author;
	$author_tabs	=	array(
		'author_id'		=>	__('About','easy-property-listings' ),
		'description'		=>	__('Bio','easy-property-listings' ),
		'video'			=>	__('Video','easy-property-listings' ),
		'contact_form'		=>	__('Contact','easy-property-listings' ),
	);

	return $author_tabs = apply_filters('epl_author_tabs',$author_tabs);
}

/**
 * Author Class
 *
 * @since 2.0
 */
function epl_author_class ( $classes ) {
	$classes 		=	explode(' ',$classes.' epl-author-box author-box');
	$classes		= 	array_filter(array_unique($classes));
	$classes 		=	apply_filters('epl_author_class',$classes);
	if(!empty($classes)){
		echo $classes 	= 	implode(' ',$classes);
	}
}

/**
 * Author Tab ID
 *
 * @since 2.0
 */
function epl_author_tab_author_id( $epl_author = array() ) {

	if(empty($epl_author)) {
		global $epl_author;
	}

	$permalink 	= apply_filters('epl_author_profile_link', get_author_posts_url($epl_author->author_id) , $epl_author);
	$author_title	= apply_filters('epl_author_profile_title',get_the_author_meta( 'display_name',$epl_author->author_id ) ,$epl_author );

	$arg_list = get_defined_vars();

	ob_start();

		epl_get_template_part('content-author-box-tab-details.php' , $arg_list );

	return ob_get_clean();
}

/**
 * Author Tab Image
 *
 * @since 2.0
 */
function epl_author_tab_image ($epl_author = array() ) {

	if(empty($epl_author)) {
		global $epl_author;
	}

	if ( function_exists('get_avatar') ) {
		echo  apply_filters('epl_author_tab_image',get_avatar( $epl_author->email , '150' ),$epl_author );
	}
}
add_action('epl_author_thumbnail','epl_author_tab_image',10,2);

/**
 * Author Tab Description
 *
 * @since 1.0
 */
function epl_author_tab_description( $epl_author = array() ) {
	if(empty($epl_author)) {
		global $epl_author;
	}
	echo $epl_author->get_description_html();
}

/**
 * Author Tab Video
 *
 * @since 1.0
 */
function epl_author_tab_video($epl_author = array() ) {
	if(empty($epl_author)) {
		global $epl_author;
	}
	$video_html = $epl_author->get_video_html();
	if($video_html != '')
		echo '<div class="epl-author-video author-video epl-video-container">'.$video_html.'</div>';
}

/**
 * Author Tab Contact Form
 *
 * @since 1.0
 */
function epl_author_tab_contact_form( $epl_author = array() ) {
	if(empty($epl_author)) {
		global $epl_author;
	}
	echo $epl_author->get_author_contact_form();
}

/**
 * Archive Utility Wrapper Before
 *
 * @since 1.0
 */
function epl_archive_utility_wrap_before() {
	echo '<div class="epl-loop-tools-wrap epl-archive-utility-wrapper epl-clearfix">';
}
add_action('epl_archive_utility_wrap_start', 'epl_archive_utility_wrap_before');

/**
 * Archive Utility Wrapper After
 *
 * @since 1.0
 */
function epl_archive_utility_wrap_after() {
	echo '</div>';
}
add_action('epl_archive_utility_wrap_end', 'epl_archive_utility_wrap_after' );

/**
 * Listing Image Gallery
 *
 * @since 1.0
 * @revised 3.3
 */
function epl_property_gallery () {

	$d_gallery = epl_get_option('display_single_gallery');

	$d_gallery_n = epl_get_option('display_gallery_n');

	if( $d_gallery != 1 )
		return;

	$attachments = get_children( array('post_parent' => get_the_ID(), 'post_type' => 'attachment', 'post_mime_type' => 'image') );

	if ( $attachments  ) { ?>

		<div class="epl-gallery property-gallery">
			<!-- Gallery -->
			<div class="epl-gallery-entry entry-gallery epl-clearfix">
				<?php
					$gallery_shortcode = '[gallery columns="'. $d_gallery_n . '" link="file"]';
					$gallery = apply_filters( 'epl_property_gallery_shortcode' , $gallery_shortcode, $d_gallery_n );
					echo do_shortcode( $gallery );
				?>
			</div>
		</div>
		<?php
	}
}
add_action('epl_property_gallery','epl_property_gallery');

/**
 * Get the template path.
 *
 * @return string
 * @since 1.0
 */
function epl_template_path() {
	return apply_filters( 'epl_template_path', 'easypropertylistings/' );
}

/**
 * Outputs a wrapper div before the first button
 *
 * @since 1.3
 * @return string
 */
function epl_buttons_wrapper_before() {
	echo '<div class="epl-button-wrapper epl-clearfix">';
}

/**
 * Outputs a wrapper div after the last button
 *
 * @since 1.3
 * @return string
 */
function epl_buttons_wrapper_after() {
	echo '</div>';
}
add_action('epl_buttons_single_property', 'epl_buttons_wrapper_before' , 1);
add_action('epl_buttons_single_property', 'epl_buttons_wrapper_after' , 99);

/**
 * Used to mark home inspection on apple devices
 *
 * @since 2.0
 */
function epl_create_ical_file($start='',$end='',$name='',$description='',$location='') {

	$args = get_defined_vars();
	$args = apply_filters('epl_ical_args', $args);
     $data = "BEGIN:VCALENDAR\nVERSION:2.0\nMETHOD:PUBLISH\nBEGIN:VEVENT\nDTSTART:".date("Ymd\THis",strtotime($start))."\nDTEND:".date("Ymd\THis",strtotime($end))."\nLOCATION:".$location."\nTRANSP: OPAQUE\nSEQUENCE:0\nUID:\nDTSTAMP:".date("Ymd\THis\Z")."\nSUMMARY:".$name."\nDESCRIPTION:".$description."\nPRIORITY:1\nCLASS:PUBLIC\nBEGIN:VALARM\nTRIGGER:-PT10080M\nACTION:DISPLAY\nDESCRIPTION:Reminder\nEND:VALARM\nEND:VEVENT\nEND:VCALENDAR\n";

	header("Content-type:text/calendar");
	header('Content-Disposition: attachment; filename="'.$name.'.ics"');
	Header('Content-Length: '.strlen($data));
	Header('Connection: close');
	echo $data;
	die;
}

/**
 * Output iCal clickable dates
 *
 * @since 2.0
 */
function epl_process_event_cal_request () {
	global $epl_settings;
	if(isset($_GET['epl_cal_dl']) && (int) $_GET['epl_cal_dl'] == 1 && intval($_GET['propid']) > 0) {
		if(isset($_GET['cal']) ) {
			$type = sanitize_text_field($_GET['cal']);
			switch($type) {
				case 'ical':
					$item = base64_decode($_GET['dt']);
					if(is_numeric($item[0])) {
						$post_id	= intval($_GET['propid']) ;
						$timearr 	= explode(' ',$item);
						$starttime 	= current($timearr).' '.$timearr[1];
						$endtime 	= current($timearr).' '.end($timearr);
						$post		= get_post($post_id);
						$subject	= $epl_settings['label_home_open'].' - '.get_post_meta($post_id,'property_heading',true);

						$address = '';
						if(get_post_meta($post_id,'property_address_sub_number',true) != '') {
							$address .= get_post_meta($post_id,'property_address_sub_number',true) . '/';
						}
						$address .= get_post_meta($post_id,'property_address_street_number',true) . ' ';
						$address .= get_post_meta($post_id,'property_address_street',true) . ' ';
						$address .= get_post_meta($post_id,'property_address_suburb',true) . ', ';
						$address .= get_post_meta($post_id,'property_address_state',true) . ' ';
						$address .= get_post_meta($post_id,'property_address_postal_code',true);

						epl_create_ical_file($starttime,$endtime,$subject,strip_tags($post->post_content),$address);
					}
				break;
			}
		}
	}
}
add_action('init','epl_process_event_cal_request');

/**
 * Add coordinates to meta for faster loading on second view
 *
 * @since 2.1
 */
function epl_update_listing_coordinates() {
	if( intval($_POST['listid']) == 0 || $_POST['coordinates'] == '')
		return;
		$coordinates = rtrim( ltrim($_POST['coordinates'] , '(') , ')');
		if( update_post_meta(intval($_POST['listid']),'property_address_coordinates',$coordinates) ) {
			die('success');
		} else {
			die('fail');
		}
}
add_action('wp_ajax_epl_update_listing_coordinates','epl_update_listing_coordinates');
add_action('wp_ajax_nopriv_epl_update_listing_coordinates','epl_update_listing_coordinates');

/**
 * Adapted from wp core to add additional filters
 *
 * @since 2.1
 * @revised 3.3
 */
function epl_get_the_term_list( $id, $taxonomy, $before = '', $sep = '', $after = '' ) {
	$terms = get_the_terms( $id, $taxonomy );

	if ( is_wp_error( $terms ) )
		return $terms;

	if ( empty( $terms ) )
		return false;


	foreach ( $terms as $term ) {

		$link = get_term_link( $term, $taxonomy );
		if ( is_wp_error( $link ) )
			return $link;

		if( apply_filters('epl_features_taxonomy_link_filter' , true)  == true ) {

			$term_links[] = '<li class="epl-tax-feature '.$term->slug.' ">'.
						'<a href="' . esc_url( $link ) . '" rel="tag">' . $term->name . '</a>'
					. '</li>'.$sep;

		} else {

			$term_links[] = '<li class="epl-tax-feature '.$term->slug.' ">'.$term->name.'</li>'.$sep;

		}
	}

	$term_links = apply_filters( "term_links-$taxonomy", $term_links );

	$html = $before;
	foreach ($term_links as $term_link) {
		$html .= $term_link;
	}
	$html .= $after;

	return $html;
}

/**
 * Get Property Meta
 *
 * @since 2.1
 */
function get_property_meta( $key ) {
	global $property;
	return $property->get_property_meta($key);
}

/**
 * The Property Meta
 *
 * @since 2.1
 */
function the_property_meta($key) {
	global 	$property;
	echo  	$property->get_property_meta($key);
}

/**
 * Template Class
 *
 * @since 2.1
 */
function epl_template_class( $class = false, $context = 'single' ) {

    if ( $class != false ) {
        $class = 'epl-template-'.$class;
    } else {
        $class = 'epl-template-blog';
    }

    return apply_filters('epl_template_class',$class,$context);
}

/**
 * Pagination
 *
 * @since 2.1
 */
function epl_pagination ($query = array() ) {
	global $epl_settings;
	$fancy_on = ( isset($epl_settings['use_fancy_navigation'] ) && $epl_settings['use_fancy_navigation'] == 1) ? 1 : 0;
	if($fancy_on) {
		epl_fancy_pagination($query);
	} else {
		epl_wp_default_pagination($query);
	}
}
add_action('epl_pagination','epl_pagination');

/**
 * Returns active theme name as a lowercase name
 *
 * @since 3.0
 */
function epl_get_active_theme() {
	if ( function_exists( 'wp_get_theme' ) ) { // wp version >= 3.4
		$active_theme = wp_get_theme();
		$active_theme = $active_theme->get( 'Name' );

	} else {
		// older versions
		$active_theme = get_current_theme();
	}
	$active_theme = str_replace(' ','',strtolower($active_theme));
	return apply_filters('epl_active_theme', $active_theme);
}

/**
 * Returns active theme name as a css class with prefix for use in default templates
 *
 * @since 2.1.2
 */
function epl_get_active_theme_name() {
	$epl_class_prefix = apply_filters('epl_active_theme_prefix','epl-active-theme-');
	$active_theme = epl_get_active_theme();
	return apply_filters('epl_active_theme_name',$epl_class_prefix . $active_theme);
}

/**
 * Returns core shortcode names
 *
 * @since 3.3
 */
function epl_get_shortcode_list() {
	return array(
		'listing',
		'listing_category',
		'listing_open',
		'listing_feature',
		'listing_location',
		'listing_auction',
		'listing_advanced'
	);
}

/**
 * Pagination fix for home
 *
 * @since 2.1.2
 * @revised 3.3
 */
function epl_home_pagination_fix( $query) {

	global $wp_query;
	$queried_post_type = isset( $query->query_vars['post_type'] ) ? (array) $query->query_vars['post_type'] : array();
	$diff = array_diff( $queried_post_type, epl_get_core_post_types() );

	if( isset($wp_query->query['paged']) && count($diff) == 0 ){
		$query->set('paged', $wp_query->query['paged']);
	}

	$shortcodes = epl_get_shortcode_list();

	if( $query->get('is_epl_shortcode') &&
		in_array($query->get('epl_shortcode_name'),$shortcodes) && !wp_doing_ajax() ){

		if( isset($_GET['pagination_id']) && $_GET['pagination_id'] == $query->get('instance_id') ) {
			$query->set('paged', $query->get('paged') );
		} else {
			$query->set('paged', 1 );
		}
	}
}
add_action('pre_get_posts','epl_home_pagination_fix',99);

/**
 * Returns status class
 *
 * @since 2.1.10
 */
function epl_property_widget_status_class() {
	global $property;
	echo 'epl-widget-status-' . $property->get_property_meta('property_status');
}
add_action('epl_property_widget_status_class','epl_property_widget_status_class');

/**
 * Ability to hide map on single listings
 *
 * @since 2.1.8
 */
function epl_hide_map_from_front() {
	$epl_posts 		= epl_get_active_post_types();
	$epl_posts 		= array_keys($epl_posts);

	global $post,$property;

	if( is_single() && in_array($post->post_type,$epl_posts) ) {

		$hide_map = get_post_meta($post->ID,'property_address_hide_map',true);
		if($hide_map == 'yes') {
			remove_all_actions( 'epl_property_map' );
		}
	}
}
add_action('wp','epl_hide_map_from_front',10);

/**
 * Disable paging on listing widget
 *
 * @since 2.1.8
 */
function epl_nopaging($query) {
	$restrict_paging = $query->get('epl_nopaging');
	if($restrict_paging == true) {
		$query->set('paged',1);
	}
}
add_action('pre_get_posts','epl_nopaging');

/**
 * Ability to hide author box on single listings
 *
 * @since 2.1.11
 */
function epl_hide_author_box_from_front() {
	$epl_posts 		= epl_get_active_post_types();
	$epl_posts 		= array_keys($epl_posts);

	global $post,$property;

	if( is_single() && in_array($post->post_type,$epl_posts) ) {

		$hide_author_box = get_post_meta($post->ID,'property_agent_hide_author_box',true);
		if($hide_author_box == 'yes') {
			remove_all_actions( 'epl_single_author' );
		}
	}
}
add_action('wp','epl_hide_author_box_from_front',10);

/**
 * Retain user grid/list view
 *
 * @since 2.1.11
 */
function epl_update_default_view() {

	$view 		= isset( $_POST['view'] ) ? trim( $_POST['view'] ) : '';

	if( in_array($view,array('list','grid') ) ) {

		setcookie("preferredView", $view,0,'/');
	}
	wp_die('success');
}
add_action('wp_ajax_epl_update_default_view','epl_update_default_view');
add_action('wp_ajax_nopriv_epl_update_default_view','epl_update_default_view');

/**
 * Custom the_content filter
 *
 * @since 2.2
 */
function epl_the_content_filters() {

    if ( !has_filter( 'epl_get_the_content', 'wptexturize' ) ) {

        add_filter( 'epl_get_the_content', 'wptexturize'        );
        add_filter( 'epl_get_the_content', 'convert_smilies'    );
        add_filter( 'epl_get_the_content', 'convert_chars'      );
        add_filter( 'epl_get_the_content', 'wpautop'            );
        add_filter( 'epl_get_the_content', 'shortcode_unautop'  );
        add_filter( 'epl_get_the_content', 'prepend_attachment' );
        $vidembed = new WP_Embed();
        add_filter( 'epl_get_the_content', array( &$vidembed, 'run_shortcode'), 8 );
        add_filter( 'epl_get_the_content', array( &$vidembed, 'autoembed'), 8 );
        add_filter( 'epl_get_the_content', 'do_shortcode', 11);
    }

    add_filter( 'epl_get_the_excerpt', 'epl_trim_excerpt'  );
}
add_action('init','epl_the_content_filters',1);

/**
 * Disable property-box left and right class
 *
 * @since 2.2
 */
function epl_compatibility_archive_class_callback() {
	$class = '-disable';
	echo $class;
}

/**
 * Apply the i'm feeling lucky theme options
 *
 * @since 2.2
 */
function epl_apply_feeling_lucky_config() {

	global $epl_settings;

	$epl_posts 	= epl_get_active_post_types();
	$epl_posts 	= array_keys($epl_posts);


	// remove epl featured image on single pages in lucky mode
	if( isset($epl_settings['epl_lucky_disable_single_thumb']) && $epl_settings['epl_lucky_disable_single_thumb'] == 'on') {

		if ( is_single() && in_array( get_post_type(), $epl_posts ) ) {
			remove_all_actions( 'epl_property_featured_image' );
		}
	}

	// remove active theme's featured image on single pages in lucky mode
	if( isset($epl_settings['epl_lucky_disable_theme_single_thumb']) && $epl_settings['epl_lucky_disable_theme_single_thumb'] == 'on') {

		if ( is_single() && in_array( get_post_type(), $epl_posts ) ) {
			add_filter('post_thumbnail_html','epl_remove_single_thumbnail',20,5);
		}
	}

	// remove featured image on archive pages in lucky mode
	if( isset($epl_settings['epl_lucky_disable_archive_thumb']) && $epl_settings['epl_lucky_disable_archive_thumb'] == 'on') {

	if( is_post_type_archive($epl_posts) ) {
			add_filter('post_thumbnail_html','epl_remove_archive_thumbnail',20,5);
		}
	}

    // remove epl featured image on archive pages in lucky mode
    if( isset($epl_settings['epl_lucky_disable_epl_archive_thumb']) && $epl_settings['epl_lucky_disable_epl_archive_thumb'] == 'on') {

    	if( is_post_type_archive($epl_posts) ) {
			remove_all_actions( 'epl_property_archive_featured_image' );

			// Adds class to disable property-box right and left
			add_action('epl_compatibility_archive_class' , 'epl_compatibility_archive_class_callback');
		}
	}

}
add_action('wp','epl_apply_feeling_lucky_config',1);

/**
 * A workaround to avoid duplicate thumbnails for single listings being displayed on archive pages via theme & epl
 * attempts to null the post thumbnail image called from theme & display thumbnail image called from epl
 *
 * @since 2.2
 */
function epl_remove_archive_thumbnail($html, $post_id, $post_thumbnail_id, $size, $attr) {

	if( is_admin() ) {
		return $html;
	}

	if( is_epl_post_archive() ) {
		// allow archive listing images as well as widget images
		if(
			doing_action('epl_property_archive_featured_image') ||
			doing_action('epl_property_widgets_featured_image') ||
			doing_action('epl_author_thumbnail') ||
			doing_action('epl_author_widget_thumbnail')
		) {

		} else {
			$html = '';
		}
	}

	return $html;
}

/**
 * A workaround to avoid duplicate thumbnails for single listings
 *
 * @since 2.2
 */
function epl_remove_single_thumbnail($html, $post_id, $post_thumbnail_id, $size, $attr) {

	if( is_admin() ) {
		return $html;
	}

	if( is_epl_post() ) {
		// allow single listing images as well as widget images
		if( doing_action('epl_property_featured_image') || doing_action('epl_property_widgets_featured_image') ) {

		} else {
			$html = '';
		}
	}
	return $html;
}

/**
 * Custom property the_content
 *
 * @since 2.2
 */
function epl_the_content() {

	global $property;
	$content = apply_filters('epl_get_the_content',get_the_content());
	echo str_replace( ']]>', ']]&gt;', $content );
}
add_action('epl_property_the_content','epl_the_content');

/**
 * Custom property the_content
 *
 * @since 2.2
 */
function epl_feeling_lucky($content) {

	global $epl_settings;

	if( !isset($epl_settings['epl_feeling_lucky']) || $epl_settings['epl_feeling_lucky'] != 'on') {
		return $content;
	}

	$epl_posts 	= epl_get_active_post_types();
	$epl_posts 	= array_keys($epl_posts);

	if ( is_single() && in_array( get_post_type(), $epl_posts ) ) {
		ob_start();
		do_action('epl_property_single');
		return ob_get_clean();
	} elseif( is_post_type_archive($epl_posts) ) {
		ob_start();
		do_action('epl_property_blog');
		/**
		* Using return VS echo resolves issues with Yoast SEO repeating content in some cases but breaks the template loading correctly in compatibility mode.
		*/
		// return ob_get_clean();
		echo ob_get_clean();

	} else {
		return $content;
	}
}

add_filter('the_content','epl_feeling_lucky');

/**
 * Custom property the_excerpt
 *
 * @since 2.2
 */
function epl_trim_excerpt($text = '') {

	$raw_excerpt = $text;
	if ( '' == $text ) {
		$text = get_the_content('');

		$text = strip_shortcodes( $text );

		$text = apply_filters( 'epl_get_the_content', $text );
		$text = str_replace(']]>', ']]&gt;', $text);

		$excerpt_length = apply_filters( 'excerpt_length', 55 );
		$excerpt_more = apply_filters( 'excerpt_more', ' ' . '[&hellip;]' );
		$text = wp_trim_words( $text, $excerpt_length, $excerpt_more );

	}
	return apply_filters( 'epl_trim_excerpt', $text, $raw_excerpt );
}

/**
 * Custom property the_excerpt
 *
 * @since 2.2
 */
function epl_the_excerpt() {
	echo apply_filters( 'epl_the_excerpt', epl_get_the_excerpt() );
}

/**
 * Custom property the_excerpt
 *
 * @since 2.2
 */
function epl_get_the_excerpt( $deprecated = '' ) {
	if ( !empty( $deprecated ) )
		_deprecated_argument( __FUNCTION__, '2.3' );

	$post = get_post();
	if ( empty( $post ) ) {
		return '';
	}

	if ( post_password_required() ) {
		return __( 'There is no excerpt because this is a protected post.' );
	}

	return apply_filters( 'epl_get_the_excerpt', $post->post_excerpt );
}

/**
 * Syntax Highlighter
 *
 * @since 2.2
 */
function epl_syntax_highlight( $str = '' , $class = '' ) {

	return '<pre><code class="'.$class.'">'.htmlentities($str).'</code></pre>';
}

/**
 * Strip Tags
 *
 * @since 2.2
 * @param string $value
 * @param string $allowed_tags
 */
function epl_strip_tags( $value , $allowed_tags = '' ) {

	if( !is_array( $value ) )  {
		return strip_tags( $value , $allowed_tags );
	}
	return $value;
}

/**
 * Esc Attr
 *
 * @since 2.2
 */
function epl_esc_attr($value) {

	if( !is_array($value) )  {
		return esc_attr($value);
	}
	return $value;
}

/**
 * Post Count
 *
 * @since 2.2
 */
function epl_get_post_count($type='',$meta_key,$meta_value,$author_id='') {
	global $wpdb;

	$sql = "
		SELECT count( Distinct p.ID ) AS count
		FROM {$wpdb->prefix}posts AS p
		INNER JOIN $wpdb->postmeta pm  ON (p.ID = pm.post_id)
		INNER JOIN $wpdb->postmeta pm2  ON (p.ID = pm2.post_id)
		WHERE p.post_status = 'publish' ";

		if($type == ''){
			$epl_posts 	= epl_get_active_post_types();
			$epl_posts 		= '"'.implode('","',array_keys($epl_posts) ).'"';

			$sql .=" AND p.post_type IN ( {$epl_posts} )";
		} else {
			$sql .=" AND p.post_type = '{$type}'";
		}

		if($author_id != '') {
			$user_info 		= get_userdata($author_id);
			$sql .=" AND (
						p.post_author =  $author_id
						OR (
							pm2.meta_key 	= 'property_second_agent'
							AND
							pm2.meta_value 	= '$user_info->user_login'
						)
					)";
		}
	$sql .="
		AND p.ID = pm.post_id
		AND pm.meta_key = '{$meta_key}'
		AND pm.meta_value = '{$meta_value}'
	";
	$count = $wpdb->get_row($sql);
	return $count->count;
}

/**
 * Get the inspection date format
 *
 * @revised 3.3
 */
function epl_get_inspection_date_format() {

	$date_format = epl_get_option('inspection_date_format') == 'custom_inspection_date_format'?
		epl_get_option('custom_inspection_date_format') : epl_get_option('inspection_date_format');

	if($date_format == '')
		$date_format = 'd-M-Y';

	return apply_filters('epl_inspection_date_format', $date_format);
}

/**
 * Get the inspection time format
 *
 * @revised 3.3
 */
function epl_get_inspection_time_format() {

	$time_format = epl_get_option('inspection_time_format') == 'custom_inspection_time_format'?
			epl_get_option('custom_inspection_time_format') : epl_get_option('inspection_time_format');

	if($time_format == '')
		$time_format = 'h:i A';

	return apply_filters('epl_inspection_time_format', $time_format);

}

/**
 * Inspection Format
 *
 * @since 2.2
 */
function epl_inspection_format($inspection_date) {

	$formatted_date  = '';
	$inspection_date = explode(' ',$inspection_date);

	$date_format 	= epl_get_inspection_date_format();
	$time_format 	= epl_get_inspection_time_format();

	$date 		= isset($inspection_date[0]) ? date($date_format,strtotime($inspection_date[0])) : '';
	$time_start 	= isset($inspection_date[1]) ? date($time_format,strtotime($inspection_date[1])) : '';
	$time_end 	= isset($inspection_date[3]) ? date($time_format,strtotime($inspection_date[3])) : '';

	return "{$date} {$time_start} to {$time_end}";
}
add_action('epl_inspection_format','epl_inspection_format');

/**
 * Counts the total number of contacts.
 *
 * @access 		public
 * @since 		3.0
 * @return 		int - The total number of contacts.
 */
function epl_count_total_contacts() {
	$counts =  wp_count_posts('epl_contact');
	return $counts->publish;
}

/**
 * Hide contacts notes from showing on frontend
 *
 * @since 3.0
 * @param $comments
 * @param $post_id
 * @return mixed
 */
function epl_filter_listing_comments_array( $comments , $post_id ) {
	foreach($comments as $key   =>  &$comment) {
		if( $comment->comment_agent == 'epl' ) {
			unset($comments[$key]);
		}
	}
	return $comments;
}
add_filter( 'comments_array' , 'epl_filter_listing_comments_array' , 10, 2 );

/**
 * Archive Page Title
 *
 * @since 3.0
 * @return Output the archive title
 */
function epl_archive_title_callback() {
	the_post();

	if ( is_tax() && function_exists( 'epl_is_search' ) && false == epl_is_search() ) { // Tag Archive
		$term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );
		$title = sprintf( __( 'Property in %s', 'easy-property-listings'  ), $term->name );
	}
	else if ( function_exists( 'epl_is_search' ) && epl_is_search() ) { // Search Result
		$title = apply_filters( 'epl_archive_title_search_result' , __( 'Search Result', 'easy-property-listings'  ) );
	}

	else if ( function_exists( 'is_post_type_archive' ) && is_post_type_archive() && function_exists( 'post_type_archive_title' ) ) { // Post Type Archive
		$title = post_type_archive_title( '', false );
	}

	else { // Default catchall just in case
		$title = apply_filters( 'epl_archive_title_fallback' , __( 'Listing', 'easy-property-listings'  ) );
	}

	if ( is_paged() )
		printf( '%s &ndash; Page %d', $title, get_query_var( 'paged' ) );
	else
		echo apply_filters( 'epl_archive_title_default' , $title );

	rewind_posts();
}
add_action( 'epl_the_archive_title' , 'epl_archive_title_callback' );

/**
 * Shortcode Sorter
 *
 * @since 3.0
 * @return $args
 */
function epl_add_orderby_args($args,$type='',$name='') {

	if( $type == 'shortcode' ) {
		$args['is_epl_shortcode'] 	= true;
		$args['epl_shortcode_name'] = $name;
	}

	$post_type = isset($args['post_type']) ? current($args['post_type']) : '';

	if(isset($_GET['sortby']) && trim($_GET['sortby']) != ''){

		$orderby = sanitize_text_field(trim($_GET['sortby']));
		$sorters = epl_sorting_options($post_type);

		foreach($sorters as $sorter) {

			if($orderby == $sorter['id']){

				if($sorter['type'] == 'meta') {
					$args['orderby']  = $sorter['orderby'];
					$args['meta_key'] =	$sorter['key'];
				} else {
					$args['orderby']  = $sorter['key'];
				}
				$args['order']    = $sorter['order'];
				break;
			}

		}
	}
	return $args;
}

/**
 * Shortcode Sorter
 *
 * @since 3.1.5
 */
function epl_shortcode_results_message_callback( $shortcode = 'default' ) {

	$title = apply_filters( 'epl_shortcode_results_message_title' , __('Nothing found, please check back later.', 'easy-property-listings' ) );

	if ( $shortcode == 'open' ) {
		$title = apply_filters( 'epl_shortcode_results_message_title_open' , __('Nothing currently scheduled for inspection, please check back later.', 'easy-property-listings' ) );
	}

	echo '<h3 class="epl-shortcode-listing-open epl-alert">' . $title . '</h3>';

}
add_action( 'epl_shortcode_results_message' , 'epl_shortcode_results_message_callback' );

/**
 * Search Not Found Messages
 *
 * @since 3.1.8
 */
function epl_property_search_not_found_callback() {

	$title 		= apply_filters( 'epl_property_search_not_found_title' , __('Listing not Found', 'easy-property-listings') );;
	$message 	= apply_filters( 'epl_property_search_not_found_message' , __('Listing not found, expand your search criteria and try again.', 'easy-property-listings') );

	?>

	<div class="epl-search-not-found-title entry-header clearfix">
		<h3 class="entry-title"><?php echo $title; ?></h3>
	</div>

	<div class="epl-search-not-found-message entry-content clearfix">
		<p><?php echo $message; ?></p>
	</div>

<?php
}
add_action( 'epl_property_search_not_found' , 'epl_property_search_not_found_callback' );

/**
 * Add Listing Status and Under Offer to Post Class
 *
 * @since 3.1.16
 */
function epl_property_post_class_listing_status_callback( $classes ) {

	if ( is_epl_post() ) {

		$property_status	= get_property_meta('property_status');
		$property_under_offer	= get_property_meta('property_under_offer');
		$commercial_type	= get_property_meta('property_com_listing_type');
		$class_prefix		= 'epl-status-';

		if ( $property_status != '' ) {
			$classes[] = $class_prefix . strtolower( $property_status );
		}
		if ( $property_under_offer == 'yes' && $property_status != 'sold' ) {
			$classes[] = $class_prefix . 'under-offer';
		}
		if ( $commercial_type != '' ) {
			$class_prefix		= 'epl-commercial-type-';
			$classes[] = $class_prefix . strtolower( $commercial_type );
		}
	}
	return $classes;
}
add_filter( 'post_class' , 'epl_property_post_class_listing_status_callback' );

/**
 * Get the author loop
 *
 * @revised 3.3
 */
function epl_archive_author_callback() {
	global $epl_author_secondary;
	epl_get_template_part('content-author-archive-card.php');
	if( is_epl_post() && epl_listing_has_secondary_author() ) {
	    epl_get_template_part('content-author-archive-card.php',array('epl_author'	=>	$epl_author_secondary));
	    epl_reset_post_author();
	}
}
add_action( 'epl_archive_author' , 'epl_archive_author_callback' );

/**
 * Contact capture action and messages
 *
 * @since 3.3
 */
function epl_contact_capture_action() {

	$success = array(
		'status'	=>	'success',
		'msg'		=>	apply_filters('epl_contact_capture_success_msg',__('Form submitted successfully','easy-property-listings') )
	);

	$fail = array(
		'status'	=>	'fail',
		'msg'		=>	apply_filters('epl_contact_capture_fail_msg',__('Some issues with form submitted','easy-property-listings') )
	);

	if( isset($_POST['epl_contact_anti_spam']) && $_POST['epl_contact_anti_spam'] != '' ){
		wp_die( json_encode( $fail ) );
	}

	if( isset($_POST['epl_contact_email']) && $_POST['epl_contact_email'] == '' ){
		wp_die( json_encode( array('status'	=>	'fail', 'msg'	=>	__('Email is required','easy-property-listings') ) ) );
	}

	$contact = new EPL_contact( $_POST['epl_contact_email'] );
	$fname  = isset($_POST['epl_contact_first_name']) ? sanitize_text_field($_POST['epl_contact_first_name']) : '';
	$lname  = isset($_POST['epl_contact_last_name']) ? sanitize_text_field($_POST['epl_contact_last_name']) : '';
	$phone  = isset($_POST['epl_contact_phone']) ? sanitize_text_field($_POST['epl_contact_phone']) : '';
	$title  = isset($_POST['epl_contact_title']) ? sanitize_text_field($_POST['epl_contact_title']) : '';

	if( trim($title) == '' && ( $fname != '' || $lname != ''  ) ){
		$title = $fname.' '.$lname;
	}

	if( trim($title) == '' && ( $_POST['epl_contact_email'] != '' ) ){
		$title = $_POST['epl_contact_email'];
	}

	if ( empty( $contact->id ) ) {

		$contact_data = array(
			'name'			=>	$title,
			'email'			=>	sanitize_email($_POST['epl_contact_email']),
		);
		if ( $contact->create( $contact_data ) ) {
			$contact->update_meta('contact_first_name',$fname);
			$contact->update_meta('contact_last_name',$lname);
			$contact->update_meta('contact_phones',array('phone' =>  $phone) );
			$contact->update_meta('contact_category','widget');
			$contact->attach_listing( $_POST['epl_contact_listing_id'] );
			$contact->add_note( $_POST['epl_contact_note'],'note',$_POST['epl_contact_listing_id'] );
			wp_die( json_encode( $success ) );
		} else {
			wp_die( json_encode( $fail ) );
		}
	} else {

		if ( $contact->update( array('name'	=>	$title ) ) ) {
			$contact->add_note( $_POST['epl_contact_note'],'note',$_POST['epl_contact_listing_id'] );
			$contact->attach_listing( $_POST['epl_contact_listing_id'] );
			wp_die( json_encode( $success ) );
		} else {
			wp_die( json_encode( $fail ) );
		}
	}
}
add_action('wp_ajax_epl_contact_capture_action', 'epl_contact_capture_action');
add_action('wp_ajax_nopriv_epl_contact_capture_action', 'epl_contact_capture_action');