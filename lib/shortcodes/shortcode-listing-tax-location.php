<?php
/**
 * SHORTCODE :: Listing Location Taxonomy [listing_feature]
 *
 * @package     EPL
 * @subpackage  Shortcode/map
 * @copyright   Copyright (c) 2014, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.1.2
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// Only load on front
if( is_admin() ) {
	return;
}
/**
 * This shortcode allows for you to specify feature property type(s) using 
 * [listing_location post_type="property" location="sorrento" location_id="6" status="current,sold,leased" template="default"] option. You can also 
 * limit the number of entries that display. using  [listing_location limit="5"]
 */
function epl_shortcode_listing_tax_location_callback( $atts ) {
	$property_types = epl_get_active_post_types();
	if(!empty($property_types)) {
		 $property_types = array_keys($property_types);
	}
	
	extract( shortcode_atts( array(
		'post_type' 		=>	$property_types, //Post Type
		'status'		=>	array('current' , 'sold' , 'leased' ),
		'location'		=>	'',
		'location_id'		=>	'',
		'limit'			=>	'10', // Number of maximum posts to show
		'template'		=>	false, // Template can be set to "slim" for home open style template
	), $atts ) );
	
	if(empty($post_type)) {
		return;
	}
	
	$sort_options = array(
		'price'			=>	'property_price',
		'date'			=>	'post_date'
	);
	
	ob_start();
	$paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;
	$args = array(
		'post_type' 		=>	$post_type,
		'posts_per_page'	=>	$limit,
		'paged' 		=>	$paged
	);
	
	if(!empty($location) ) {
		if( !is_array( $location ) ) {
			$location = explode(",", $location);
			$location = array_map('trim', $location);
			
			$args['tax_query'][] = array(
				'taxonomy'	=> 'location',
				'field' 	=> 'slug',
				'terms' 	=> $location
			);
		}
	}
	
	if(!empty($location_id) ) {
		if( !is_array( $location_id ) ) {
			$location_id = explode(",", $location_id);
			$location_id = array_map('trim', $location_id);
			
			$args['tax_query'][] = array(
				'taxonomy'	=> 'location',
				'field'		=> 'id',
				'terms' 	=> $location_id
			);
		}
	}
	
	if(!empty($status)) {
		if(!is_array($status)) {
			$status = explode(",", $status);
			$status = array_map('trim', $status);
			
			$args['meta_query'][] = array(
				'key' => 'property_status',
				'value' => $status,
				'compare' => 'IN'
			);
		}
	}
	
	if( isset( $_GET['sortby'] ) ) {
		$orderby = sanitize_text_field( trim($_GET['sortby']) );
		if($orderby == 'high') {
			$args['orderby']	=	'meta_value_num';
			$args['meta_key']	=	'property_price';
			$args['order']		=	'DESC';
		} elseif($orderby == 'low') {
			$args['orderby']	=	'meta_value_num';
			$args['meta_key']	=	'property_price';
			$args['order']		=	'ASC';
		} elseif($orderby == 'new') {
			$args['orderby']	=	'post_date';
			$args['order']		=	'DESC';
		} elseif($orderby == 'old') {
			$args['orderby']	=	'post_date';
			$args['order']		=	'ASC';
		}
		
	}
	
	$query_open = new WP_Query( $args );
	if ( $query_open->have_posts() ) { ?>
		<div class="loop epl-shortcode">
			<div class="loop-content epl-shortcode-listing-location">
				<?php
					do_action( 'epl_property_loop_start' );
					while ( $query_open->have_posts() ) {
						$query_open->the_post();
						if ( $template == 'slim' ) {
							epl_property_blog_slim();
						} else {
							epl_property_blog();
						}
					}
					do_action( 'epl_property_loop_end' );
				?>
			</div>
			<div class="loop-footer">
				<!-- Previous/Next page navigation -->
				<div class="loop-utility epl-clearfix">
					<div class="alignleft"><?php previous_posts_link( __( '&laquo; Previous Page', 'epl' ), $query_open->max_num_pages ); ?></div>
					<div class="alignright"><?php next_posts_link( __( 'Next Page &raquo;', 'epl' ), $query_open->max_num_pages ); ?></div>
				</div>
			</div>
		</div>
		<?php
	} else {
		echo '<h3>'.__('Nothing found, please check back later.', 'epl').'</h3>';
	}
	wp_reset_postdata();
	return ob_get_clean();
}
add_shortcode( 'listing_location', 'epl_shortcode_listing_tax_location_callback' );
