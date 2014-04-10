<?php
/*
 * SHORTCODE :: Open For Inspection [epl-property-open]
 */

function shortcode_epl_property_open_callback( $atts ) {
	extract( shortcode_atts( array(
		'post_type' =>	array('epl_property', 'epl_rental', 'epl_commercial', 'epl_land', 'epl_suburb', 'epl_property_sale'), //Post Type
		'limit'		=>	'-1', // Number of maximum posts to show
	), $atts ) );
	
	ob_start();
	$args_open = array(
		'post_type' 		=>	$post_type,
		'posts_per_page'	=>	$limit,
		'property_status'	=>	'current',
		'meta_key' 			=>	'property_inspection_times',
		'meta_query' => array(
			array(
				'key' => 'property_inspection_times',
				'value' => '',
				'compare' => '!=',
			)
		)
	);
	$query_open = new WP_Query( $args_open );
	if ( $query_open->have_posts() ) {
		while ( $query_open->have_posts() ) {
			$query_open->the_post();
			include( '../templates/property-meta.php' );

			// Status Removal
			if ( $property_status == 'withdrawn' || $property_status == 'offmarket' ) {
				// Do Not Display Withdrawn or OffMarket listings
			} else {
				echo epl_property_blog();
			} // End Status Removal
		}
	} else {
		echo '<h3>Nothing currently scheduled for inspection, please check back later.</h3>';
	}
	wp_reset_postdata();
	return ob_get_clean();
}
add_shortcode( 'epl-property-open', 'shortcode_epl_property_open_callback' );
