<?php
/*
 * Shortcode Listing Template
 * @package     EPL
 * @subpackage  Templates/Content
 * @copyright   Copyright (c) 2015, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @var $attributes array 		Shortcode Attributes.
 * @var $query_open WP_Query 	Query object for listings.
 */

  $posts_open_current_date = $query_open->posts[0]->post_date;

if ( $query_open->have_posts() ) {
	?>
	<div class="loop epl-shortcode">
            
            
<?php
//$post_inspection_date_all = explode('to' , $posts_open_current_date);
//$final_date = date('Y-m-d' , strtotime($post_inspection_date_all[0]));  
                    ?>

		<div class="loop-content epl-shortcode-listing <?php echo epl_template_class( $attributes['template'], 'archive' ); ?>">

			<?php
			if ( $attributes['tools_top'] == 'on' ) {
				do_action( 'epl_property_loop_start' );
			}
			while ( $query_open->have_posts() ) {
				$query_open->the_post();
				$attributes['template'] = str_replace( '_', '-', $attributes['template'] );
				epl_property_blog( $attributes['template'] );
			}
			if ( $attributes['tools_bottom'] == 'on' ) {
				do_action( 'epl_property_loop_end' );
			}
			?>
		</div>
		<div class="loop-footer">
				<?php
					if ( $attributes['pagination'] == 'on' ) {
						do_action( 'epl_pagination',array( 'query'	=> $query_open ) );
					}
				?>
		</div>
	</div>
	<?php
	wp_reset_postdata();
} else {
	echo '<h3>' . __( 'Nothing found, please check back later.', 'easy-property-listings'  ) . '</h3>';
}
