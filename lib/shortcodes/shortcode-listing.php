<?php
/**
 * SHORTCODE :: Listing [listing]
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
 * [listing post_type="property,rental" status="current,sold,leased"] option. You can also 
 * limit the number of entries that display. using  [listing limit="5"]
 */
function epl_shortcode_listing_callback( $atts ) {
	$property_types = epl_get_active_post_types();
	if(!empty($property_types)) {
		 $property_types = array_keys($property_types);
	}
	
	extract( shortcode_atts( array(
		'post_type' =>	$property_types, //Post Type
		'status'	=>	array('current' , 'sold' , 'leased' ),
		'limit'		=>	'10', // Number of maximum posts to show
	), $atts ) );
	
	if(!empty($status)) {
		if(!is_array($status)) {
			$status = array($status);
		}
	}
	
	ob_start();
	$paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;
	$args = array(
		'post_type' 		=>	$post_type,
		'posts_per_page'	=>	$limit,
		'paged' 			=>	$paged,
		'meta_key' 			=>	'property_status',
		'meta_query' => array(
			array(
				'key' => 'property_status',
				'value' => $status,
				'compare' => 'IN',
			)
		)
	);
	
	$query_open = new WP_Query( $args );
	if ( $query_open->have_posts() ) { ?>
		<div class="loop">
			<div class="loop-content">
				<?php
					while ( $query_open->have_posts() ) {
						$query_open->the_post();
						echo epl_property_blog_slim();
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
add_shortcode( 'listing', 'epl_shortcode_listing_callback' );
