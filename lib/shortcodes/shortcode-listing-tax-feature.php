<?php
/**
 * SHORTCODE :: Listing Feature Taxonomy [listing_feature]
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
 * [listing_feature post_type="property" status="current,sold,leased" feature_id="" feature="" template="default"] option. You can also 
 * limit the number of entries that display. using  [listing_category limit="5"]
 */
function epl_shortcode_listing_tax_feature_callback( $atts ) {
	$property_types = epl_get_active_post_types();
	if(!empty($property_types)) {
		 $property_types = array_keys($property_types);
	}
	
	extract( shortcode_atts( array(
		'post_type' 		=>	$property_types, //Post Type
		'status'		=>	array('current' , 'sold' , 'leased' ),
		'feature'		=>	'',
		'feature_id'		=>	'',
		'limit'			=>	'10', // Number of maximum posts to show
		'template'		=>	false, // Template can be set to "slim" for home open style template
		'location'		=>	'', // Location slug. Should be a name like sorrento
		'sortby'		=>	'', // Options: price, date : Default date
		'sort_order'		=>	'DESC'
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
		'paged' 			=>	$paged
	);
	
	if(!empty($feature) ) {
		if( !is_array( $feature ) ) {
			$feature = explode(",", $feature);
			$feature = array_map('trim', $feature);
			
			$args['tax_query'][] = array(
				'taxonomy'	=> 'tax_feature',
				'field' 	=> 'slug',
				'terms' 	=> $feature
			);
		}
	}
	
	if(!empty($feature_id) ) {
		if( !is_array( $feature_id ) ) {
			$feature_id = explode(",", $feature_id);
			$feature_id = array_map('trim', $feature_id);
			
			$args['tax_query'][] = array(
				'taxonomy'	=> 'tax_feature',
				'field'		=> 'id',
				'terms' 	=> $feature_id
			);
		}
	}
	
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
			<div class="loop-content epl-shortcode-listing-feature <?php echo epl_template_class( $template ); ?>">
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
add_shortcode( 'listing_feature', 'epl_shortcode_listing_tax_feature_callback' );
