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
	extract( shortcode_atts( array(
		'post_type' 		=>	'',
		'status'			=>	array('current' , 'sold' , 'leased' ),
		'feature'			=>	'',
		'feature_id'		=>	'',
		'limit'				=>	'10', // Number of maximum posts to show
		'template'			=>	false // Template, 
	), $atts ) );
	
	if(empty($post_type)) {
		return;
	}
	
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
				'taxonomy' => 'tax_feature',
				'field' => 'slug',
				'terms' => $feature
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
	
	$query_open = new WP_Query( $args );
	if ( $query_open->have_posts() ) { ?>
		<div class="loop epl-shortcode">
			<div class="loop-content epl-shortcode-listing-feature">
				<?php
					while ( $query_open->have_posts() ) {
						$query_open->the_post();
						if ( $template == 'slim' ) {
							epl_property_blog_slim();
						} elseif ( $template == 'author-widget' ) {
							// Defaults
							$display = 'image';
							$image = 'thumbnail';
							$d_title = FALSE;
							$d_icons = FALSE;
							epl_property_author_card($display,$image,$d_title,$d_icons);
						} else {
							epl_property_blog_default();
						}
					}
				?>
			</div>
			<div class="loop-footer">
				<!-- Previous/Next page navigation -->
				<div class="loop-utility clearfix">
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
add_shortcode( 'listing_feature', 'epl_shortcode_listing_tax_feature_callback' );
