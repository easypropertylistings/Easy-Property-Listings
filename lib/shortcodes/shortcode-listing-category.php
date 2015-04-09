<?php
/**
 * SHORTCODE :: Listing Category [listing_category]
 *
 * @package     EPL
 * @subpackage  Shortcode/category
 * @copyright   Copyright (c) 2014, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.1.1
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// Only load on front
if( is_admin() ) {
	return;
}
/**
 * This shortcode allows for you to specify the property type(s) using 
 * [listing_category post_type="property" status="current,sold,leased" category_key="property_rural_category" category_key="farm"] option. You can also 
 * limit the number of entries that display. using  [listing_category limit="5"]
 * Added Commercial Category Support 
 */
function epl_shortcode_listing_category_callback( $atts ) {
	$property_types = epl_get_active_post_types();
	if(!empty($property_types)) {
		 $property_types = array_keys($property_types);
	}
	
	extract( shortcode_atts( array(
		'post_type' 			=>	$property_types,
		'status'			=>	array('current' , 'sold' , 'leased' ),
		'commercial_listing_type'	=>	'',
		'category_key'			=>	'',
		'category_value'		=>	'',
		'limit'				=>	'10', // Number of maximum posts to show
		'template'			=>	false, // Template can be set to "slim" for home open style template
		'location'			=>	'', // Location slug. Should be a name like sorrento
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
				'taxonomy' => 'location',
				'field' => 'slug',
				'terms' => $location
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
	
	if(!empty($commercial_listing_type)) {
		if(!is_array($commercial_listing_type)) {
			$commercial_listing_type = explode(",", $commercial_listing_type);
			$commercial_listing_type = array_map('trim', $commercial_listing_type);
			
			$args['meta_query'][] = array(
				'key' => 'property_com_listing_type',
				'value' => $commercial_listing_type,
				'compare' => 'IN'
			);
		}
	}
	
	if(!empty($category_key) && !empty($category_value)) {
		if(!is_array($category_value)) {
			$category_value = explode(",", $category_value);
			$category_value = array_map('trim', $category_value);
			
			$args['meta_query'][] = array(
				'key' => $category_key,
				'value' => $category_value,
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
			<div class="loop-content epl-shortcode-listing-category <?php echo epl_template_class( $template ); ?>">
				<?php
					do_action( 'epl_property_loop_start' );
					while ( $query_open->have_posts() ) {
						$query_open->the_post();
						
						if ( $template == false ) {
							epl_property_blog();
						} else {
						
							if( function_exists( 'epl_property_blog_'.$template ) ) {
							
								call_user_func( 'epl_property_blog_'.$template );
								
							}
						}
					}
					do_action( 'epl_property_loop_end' );
				?>
			</div>
			<div class="loop-footer">
				<?php do_action('epl_pagination',array('query'	=>	$query_open)); ?>
			</div>
		</div>
		<?php
	} else {
		echo '<h3>'.__('Nothing found, please check back later.', 'epl').'</h3>';
	}
	wp_reset_postdata();
	return ob_get_clean();
}
add_shortcode( 'listing_category', 'epl_shortcode_listing_category_callback' );
