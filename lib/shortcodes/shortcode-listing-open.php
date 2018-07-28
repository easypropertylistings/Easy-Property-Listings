<?php
/**
 * SHORTCODE :: Open For Inspection [listing_open]
 *
 * @package     EPL
 * @subpackage  Shortcode/ListingOpen
 * @copyright   Copyright (c) 2014, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Listing Open Shortcode
 *
 * This shortcode allows for you to specify the property type(s) using
 * [listing_open post_type="property,rental"] option. You can also
 * limit the number of entries that display. using  [epl-property-open limit="5"]
 *
 * @since       1.0
 */
function epl_shortcode_property_open_callback( $atts ) {
    
    	$property_types = epl_get_active_post_types();
	if(!empty($property_types)) {
		 $property_types = array_keys($property_types);
	}

	extract( shortcode_atts( array(
		'post_type' 		=>	$property_types, //Post Type
		'limit'			=>	'-1', // Number of maximum posts to show
		'template'		=>	false, // Template. slim, table
		'location'		=>	'', // Location slug. Should be a name like sorrento
		'tools_top'		=>	'off', // Tools before the loop like Sorter and Grid on or off
		'tools_bottom'		=>	'off', // Tools after the loop like pagination on or off
		'sortby'		=>	'open_time', // Options: price,open_time, date : Default date
		'sort_order'		=>	'ASC',
		'pagination'		=> 	'on'

	), $atts ) );

	if(is_string($post_type) && $post_type == 'rental') {
		$meta_key_price = 'property_rent';
	} else {
		$meta_key_price = 'property_price';
	}

	$sort_options = array(
		'price'			=>	$meta_key_price,
		'date'			=>	'post_date'
	);

	ob_start();
	if( !is_array($post_type) ) {
		$post_type 			= array_map('trim',explode(',',$post_type) );
	}

	$args = array(
		'post_type' 		=>	$post_type,
		'posts_per_page'	=>	$limit,
		'meta_key' 		=>	'property_inspection_times',
		'meta_query' => array(
			array(
				'key' => 'property_inspection_times',
				'value' => '',
				'compare' => '!=',
			),
           array(
                'key'		=> 'property_status',
                'value'		=> array('leased','sold'),
                'compare'	=> 'NOT IN'
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

	if( $sortby != '' ) {

		if($sortby == 'price') {
			$args['orderby']	=	'meta_value_num';
			$args['meta_key']	=	$meta_key_price;
		}
                else if($sortby == 'open_time') {
			$args['orderby']	=	'meta_value_num';
			$args['meta_key']	=	'property_inspection_times';
                        $args['order']		=	'ASC';
		}
                else {
			$args['orderby']	=	'post_date';
			$args['order']		=	'ASC';

		}
		$args['order']			=	$sort_order;
	}


	// add sortby arguments to query, if listings sorted by $_GET['sortby'];
	$args = epl_add_orderby_args($args);

	$query_open = new WP_Query( $args );
   
       if ( $query_open->have_posts() ) { ?>




		<div class="loop epl-shortcode">
<div class="loop-content epl-shortcode-listing-location <?php echo epl_template_class( $template, 'archive' ); ?>">
				<?php
					if ( $tools_top == 'on' ) {
						do_action( 'epl_property_loop_start' );
					}
//                             
                                        echo '<ul class="opentimebxslider">'; 
                                        $counter = 0;
                                        $oldDate = "";
					while ( $query_open->have_posts() ) {                                               
                                                    $posts_open_ispection_id = $query_open->posts[$counter]->ID;
                                                    $counter++;
						$query_open->the_post();
                                               ?> 
   <?php 
 $post_inspection_date = get_post_meta($posts_open_ispection_id, 'property_inspection_times', true );
$post_inspection_date_all = explode('to' , $post_inspection_date);
$post_inspection_date_new = explode(' ' ,$post_inspection_date_all[0]);
$final_time = date('Y-m-d' , strtotime($post_inspection_date_new[0] ));

if($oldDate != $final_time){
    ?>
    <li>
        <div class="epl-listing-post epl-property-blog epl-clearfix opne-first-block">
                                      <header data-tile="opening-times" class="tile date-tile slick-slide slick-active" style="width: 220px;">
                              <h6 class="event-date">
                                  <span class="day"><?php echo date("l", strtotime($final_time)); ?></span><br>
                                  <span class="date no-22"><?php echo date("j", strtotime($final_time)); ?></span><br>
<span class="month"><?php echo date("F", strtotime($final_time)); ?></span><br>
</h6>      	
                                </header>
                                
                                
                                
                            </div>
    </li>
    <?php
}
$oldDate = $final_time;
                    ?>
                                           <li>
<div id="post-<?php the_ID(); ?>" <?php post_class('epl-listing-post epl-property-blog epl-clearfix'); ?> <?php do_action('epl_archive_listing_atts'); ?>>
	<div class="epl-property-blog-entry-wrapper epl-clearfix">
		<?php do_action('epl_property_before_content'); ?>
			<?php if ( has_post_thumbnail() ) : ?>
				<div class="property-box property-box-left property-featured-image-wrapper">
					<?php do_action('epl_property_archive_featured_image'); ?>
					<!-- Home Open -->
					
				</div>
			<?php endif; ?>

			<div class="property-box property-box-right property-content">
				<!-- Heading -->
				<!-- Address -->
				<div class="property-address">
					<a href="<?php the_permalink(); ?>">
						<?php do_action('epl_property_address'); ?>
					</a>
				</div>
				<!-- Property Featured Icons -->
				<div class="property-feature-icons">
					<?php do_action('epl_property_icons'); ?>
				</div>
				<!-- Price -->
				<div class="price">
                                        <?php do_action('epl_property_inspection_times'); ?>
					<?php do_action('epl_property_price'); ?>
                                  
				</div>
                       
                                <h3 class="entry-title"><a href="<?php the_permalink() ?>"><?php do_action('epl_property_heading'); ?></a></h3>
				<div class="entry-content">
					<?php epl_the_excerpt(); ?>
				</div>
			</div>
		<?php do_action('epl_property_after_content'); ?>
	</div>
</div>

</li>

<?php
					}
                                        echo '</ul>';
					if ( $tools_bottom == 'on' ) {
						do_action( 'epl_property_loop_end' );
					}

				?>
			</div>
			<div class="loop-footer">
				<?php
					if( $pagination == 'on')
					do_action('epl_pagination',array('query'	=>	$query_open));
				?>
			</div>
		</div>
		<?php
	} else {
		do_action( 'epl_shortcode_results_message' , 'open' );
	}
	wp_reset_postdata();
	return ob_get_clean();
}
add_shortcode( 'home_open_list', 'epl_shortcode_property_open_callback' );
add_shortcode( 'listing_open', 'epl_shortcode_property_open_callback' );

/*open page shortcode http://crcanberra.com/open-times/*/


function epl_shortcode_property_open_callback_home( $atts ) {
    
    	$property_types = epl_get_active_post_types();
	if(!empty($property_types)) {
		 $property_types = array_keys($property_types);
	}

	extract( shortcode_atts( array(
		'post_type' 		=>	$property_types, //Post Type
		'limit'			=>	'-1', // Number of maximum posts to show
		'template'		=>	false, // Template. slim, table
		'location'		=>	'', // Location slug. Should be a name like sorrento
		'tools_top'		=>	'off', // Tools before the loop like Sorter and Grid on or off
		'tools_bottom'		=>	'off', // Tools after the loop like pagination on or off
		'sortby'		=>	'open_time', // Options: price,open_time,  date : Default date
		'sort_order'		=>	'ASC',
		'pagination'		=> 	'on'

	), $atts ) );

	if(is_string($post_type) && $post_type == 'rental') {
		$meta_key_price = 'property_rent';
	} else {
		$meta_key_price = 'property_price';
	}

	$sort_options = array(
		'price'			=>	$meta_key_price,
		'date'			=>	'post_date'
	);

	ob_start();
	if( !is_array($post_type) ) {
		$post_type 			= array_map('trim',explode(',',$post_type) );
	}

	$args = array(
		'post_type' 		=>	$post_type,
		'posts_per_page'	=>	$limit,
		'meta_key' 		=>	'property_inspection_times',
		'meta_query' => array(
			array(
				'key' => 'property_inspection_times',
				'value' => '',
				'compare' => '!=',
			),
           array(
                'key'		=> 'property_status',
                'value'		=> array('leased','sold'),
                'compare'	=> 'NOT IN'
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

	if( $sortby != '' ) {

		if($sortby == 'price') {
			$args['orderby']	=	'meta_value_num';
			$args['meta_key']	=	$meta_key_price;
		} 
                else if($sortby == 'open_time') {
			$args['orderby']	=	'meta_value_num';
			$args['meta_key']	=	'property_inspection_times';
                        $args['order']		=	'ASC';
		}
                
                else {
			$args['orderby']	=	'post_date';
			$args['order']		=	'ASC';

		}
		$args['order']			=	$sort_order;
	}


	// add sortby arguments to query, if listings sorted by $_GET['sortby'];
	$args = epl_add_orderby_args($args);

	$query_open = new WP_Query( $args );  
       if ( $query_open->have_posts() ) { ?>
		<div class="loop epl-shortcode">
<div class="loop-content epl-shortcode-listing-category <?php echo epl_template_class( $template, 'archive' ); ?>">   
                            <?php
					if ( $tools_top == 'on' ) {
						do_action( 'epl_property_loop_start' );
					}
                                        
                                        $oldDate = "";
                                        $counter = 0;
                                        while ( $query_open->have_posts() ) {
                                            $query_open->the_post();
                                            $posts_open_ispection_id = $query_open->posts[$counter]->ID;
                                            $post_inspection_date = get_post_meta($posts_open_ispection_id, 'property_inspection_times', true );
                                            $post_inspection_date_all = explode('to' , $post_inspection_date);
                                            $post_inspection_date_new = explode(' ' ,$post_inspection_date_all[0]);
                                            $final_time = date('Y-m-d' , strtotime($post_inspection_date_new[0] ));                                           
                                            if($oldDate != $final_time){
                                                if($counter > 0){
                                                    echo '</ul><h4>'.date('l j F Y' , strtotime($post_inspection_date_new[0] )).'</h4><ul>';
                                                }
                                                if($counter == 0){
                                                    echo '<h4>'.date('l j F Y' , strtotime($post_inspection_date_new[0] )).'</h4>';
                                                    echo '<ul>';
                                                }
                                                $oldDate = $final_time;
                                            }
?>
                                                
<li>
<div id="post-<?php the_ID(); ?>" <?php post_class('epl-listing-post epl-property-blog epl-clearfix'); ?> <?php do_action('epl_archive_listing_atts'); ?>>
	<div class="epl-property-blog-entry-wrapper epl-clearfix">
		<?php do_action('epl_property_before_content'); ?>
			<?php if ( has_post_thumbnail() ) : ?>
				<div class="property-box property-box-left property-featured-image-wrapper">
					<?php do_action('epl_property_archive_featured_image'); ?>
					<!-- Home Open -->
					
				</div>
			<?php endif; ?>

			<div class="property-box property-box-right property-content">
				<!-- Heading -->
				<!-- Address -->
				<div class="property-address">
					<a href="<?php the_permalink(); ?>">
						<?php do_action('epl_property_address'); ?>
					</a>
				</div>
				<!-- Property Featured Icons -->
				<div class="property-feature-icons">
					<?php do_action('epl_property_icons'); ?>
				</div>
				<!-- Price -->
				<div class="price">
                                        <?php do_action('epl_property_inspection_times'); ?>
					<?php do_action('epl_property_price'); ?>
                                  
				</div>
                       
                                <h3 class="entry-title"><a href="<?php the_permalink() ?>"><?php do_action('epl_property_heading'); ?></a></h3>
				<div class="entry-content">
					<?php epl_the_excerpt(); ?>
				</div>
			</div>
		<?php do_action('epl_property_after_content'); ?>
	</div>
</div>

                                                                                           </li>
                                               <?php 

//						$template = str_replace('_','-',$template);
//						epl_property_blog($template);
                                            $counter++;
					}
                                        ?>
                                           </ul>
                                                 <?php 
					if ( $tools_bottom == 'on' ) {
						do_action( 'epl_property_loop_end' );
					}
				?>
			</div>

			<div class="loop-footer">
				<?php
					if( $pagination == 'on')
					do_action('epl_pagination',array('query'	=>	$query_open));
				?>
			</div>
		</div>
		<?php
	} else {
		do_action( 'epl_shortcode_results_message' , 'open' );
	}
	wp_reset_postdata();
	return ob_get_clean();
}
add_shortcode( 'home_open_lists', 'epl_shortcode_property_open_callback_home' );

