<?php
/**
 * SHORTCODE :: Open For Inspection [listing_open]
 *
 * @package     EPL
 * @subpackage  Shotrcode/map
 * @copyright   Copyright (c) 2014, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// Only load on front
if( is_admin() ) {
	return;
}
/**
 * This shortcode allows for you to specify the property type(s) using 
 * [listing_open post_type="property,rental"] option. You can also 
 * limit the number of entries that display. using  [epl-property-open limit="5"]
 */
function epl_shortcode_property_open_callback( $atts ) {
	$property_types = epl_get_active_post_types();
	if(!empty($property_types)) {
		 $property_types = array_keys($property_types);
	}
	
	extract( shortcode_atts( array(
		'post_type' 		=>	$property_types, //Post Type
		'limit'			=>	'-1', // Number of maximum posts to show
		'template'		=>	false, // Template true will display default template
		'location'		=>	'' // Location slug. Should be a name like sorrento
	), $atts ) );
	
	ob_start();
	
	$args = array(
		'post_type' 		=>	$post_type,
		'posts_per_page'	=>	$limit,
		'meta_key' 			=>	'property_inspection_times',
		'meta_query' => array(
			array(
				'key' => 'property_inspection_times',
				'value' => '',
				'compare' => '!=',
			)
		)
	);
	
	if(!empty($location) ) {
		if( !is_array( $location ) ) {
			$location = explode(",", $location);
			$location = array_map('trim', $location);
			
			$args['tax_query'][] = array(
				'taxonomy' => 'location',
				'field' => 'slug',
				'terms' => $location
			);
		}
	}
	
	$query_open = new WP_Query( $args );
	if ( $query_open->have_posts() ) { ?>
		<div class="loop epl-shortcode">
			<div class="loop-content epl-shortcode-listing-open">
				<?php
					while ( $query_open->have_posts() ) {
						$query_open->the_post();
						
						$property_status = get_post_meta( get_the_ID(), 'property_status' , true ); 

						// Status Removal
						if ( $property_status == 'withdrawn' || $property_status == 'offmarket' || $property_status == 'sold'  || $property_status == 'leased') {
							// Do Not Display Withdrawn or OffMarket listings
						} else {
							if ( $template == false ) {
								epl_property_blog();
							} else {
						
								if( function_exists( 'epl_property_blog_'.$template ) ) {
							
									call_user_func( 'epl_property_blog_'.$template );
								
								}
							}
							
						} // End Status Removal
					}
				?>
			</div>
		</div>
	<?php
	} else {
		echo '<h3 class="epl-shortcode-listing-open epl-alert">'.__('Nothing currently scheduled for inspection, please check back later.', 'epl').'</h3>';
	}
	wp_reset_postdata();
	return ob_get_clean();
}
add_shortcode( 'home_open_list', 'epl_shortcode_property_open_callback' );
add_shortcode( 'listing_open', 'epl_shortcode_property_open_callback' );
