<?php
/**
 * SHORTCODE :: Listing Category [listing_category]
 *
 * @package     EPL
 * @subpackage  Shortcode/ListingCategory
 * @copyright   Copyright (c) 2014, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.1.1
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Listing Category
 *
 * This shortcode allows for you to specify the property type(s) using
 * [listing_category post_type="property" status="current,sold,leased" category_key="property_rural_category" category_key="farm"] option. You can also
 * limit the number of entries that display. using  [listing_category limit="5"]
 * Added Commercial Category Support
 *
 * @since       1.1.1
 */
function epl_shortcode_listing_category_callback( $atts ) {
	$property_types = epl_get_active_post_types();
	if(!empty($property_types)) {
		 $property_types = array_keys($property_types);
	}

	extract( shortcode_atts( array(
		'post_type' 			=>	$property_types,
		'status'			=>	array('current' , 'sold' , 'leased' ),
		'commercial_listing_type'	=>	'', // Listing Type, 'sale' , 'lease', 'both'
		'category_key'			=>	'', // Meta field key
		'category_value'		=>	'', // Meta field value
		'category_compare'		=>	'IN', // Compare using 'IN','NOT IN','BETWEEN','NOT BETWEEN'
		'limit'				=>	'', // Number of maximum posts to show
		'offset'			=>	'', // Offset posts. When used, pagination is disabled
		'template'			=>	false, // Template can be set to "slim" for home open style template
		'location'			=>	'', // Location slug. Should be a name like sorrento
		'tools_top'			=>	'off', // Tools before the loop like Sorter and Grid on or off
		'tools_bottom'			=>	'off', // Tools after the loop like pagination on or off
		'sortby'			=>	'property_auction', // Options: price, date : Default date
		'sort_order'			=>	'', // Sort by ASC or DESC
		'pagination'			=>	'on' // Enable or disable pagination
	), $atts ) );

	if(empty($post_type)) {
		return;
	}

	if(is_string($post_type) && $post_type == 'rental') {
		$meta_key_price = 'property_rent';
	} else {
		$meta_key_price = 'property_price';
	}

	$sort_options = array(
		'price'			=>	$meta_key_price,
		'date'			=>	'post_date'
	);
	if( !is_array($post_type) ) {
		$post_type 			= array_map('trim',explode(',',$post_type) );
	}
	ob_start();
	$paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;
	$args = array(
		'post_type' 		=>	$post_type,
		'posts_per_page'	=>	$limit,
		'paged' 		=>	$paged
	);

	// Offset query does not work with pagination
	if ( ! empty ( $offset ) ) {
		$args['offset'] 	= $offset;
		$pagination	 	= 'off'; // Disable pagination when offset is used
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

		if(!is_array($category_value) && in_array($category_compare,array('IN','NOT IN','BETWEEN','NOT BETWEEN') ) ) {
			$category_value = explode(",", $category_value);
			$category_value = array_map('trim', $category_value);

		}

		$args['meta_query'][] = array(
			'key' 		=> $category_key,
			'value' 	=> $category_value,
			'compare' 	=> $category_compare
		);

	}

	if( $sortby != '' ) {

		if($sortby == 'price') {
			$args['orderby']	=	'meta_value_num';
			$args['meta_key']	=	$meta_key_price;
                } else if ($sortby == 'property_auction'){                    
                $args['orderby']	=	'meta_value_num';
		$args['meta_key']	=	'property_auction';
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
        $arr =$query_open->query['meta_query'];
        
 $args['meta_query'][] = array(
				'key' => 'property_status',
				'value' => $status,
				'compare' => 'IN'
			);
 
//$post_inspection_date1 = get_post_meta($posts_open_auction_id, 'property_status', true );
$posts_open_auction_id = $query_open->posts[0]->ID;
            $meta_data = get_post_custom($posts_open_auction_id);

	if ( $query_open->have_posts() ) { ?>
		<div class="loop epl-shortcode">
                    
                    
<?php     
$post_prop_status = get_post_meta($posts_open_auction_id, 'property_status', true );
if (($meta_data['property_authority'][0] != "")&& ($post_prop_status == 'current')){ ?>
    
    
			<div class="loop-content epl-shortcode-listing-category <?php echo epl_template_class( $template, 'archive' ); ?>">
                            
                            <?php
					if ( $tools_top == 'on' ) {
						do_action( 'epl_property_loop_start' );
					}?>
                               
				<?php	echo '<ul class="auctionslider">'; 
                                        $counter = 0;
                                        $oldDate = "";
					while ( $query_open->have_posts() ) {                                               
                                                    $posts_open_auction_id = $query_open->posts[$counter]->ID;
                                                    $counter++;
						$query_open->the_post();
                                               ?> 
   <?php 
$post_inspection_date = get_post_meta($posts_open_auction_id, 'property_auction', true );
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
					
                                        ?>
                                           
                                                 <?php 
					if ( $tools_bottom == 'on' ) {
						do_action( 'epl_property_loop_end' );
					}
				?>
			</div>
<?php } else{?>  

			<div class="loop-content epl-shortcode-listing-category <?php echo epl_template_class( $template, 'archive' ); ?>">
                       	
                            
                            <?php
					if ( $tools_top == 'on' ) {
						do_action( 'epl_property_loop_start' );
					}?>
                                             <ul class="bxslider">
					<?php while ( $query_open->have_posts() ) {
						$query_open->the_post();

						$template = str_replace('_','-',$template);
						epl_property_blog($template);
					}
                                        ?>
                                             </ul>
                                                 <?php 
					if ( $tools_bottom == 'on' ) {
						do_action( 'epl_property_loop_end' );
					}
				?>
			</div>
    
   
<?php }        ?>
			<div class="loop-footer">
				<?php
					if( $pagination == 'on')
					do_action('epl_pagination',array('query'	=>	$query_open));
				?>
			</div>
		</div>
		<?php
	} else {
		do_action( 'epl_shortcode_results_message' );
	}
	wp_reset_postdata();
	return ob_get_clean();
}
add_shortcode( 'listing_category', 'epl_shortcode_listing_category_callback' );


function epl_shortcode_listing_category_callback_inner( $atts ) {
	$property_types = epl_get_active_post_types();
	if(!empty($property_types)) {
		 $property_types = array_keys($property_types);
	}

	extract( shortcode_atts( array(
		'post_type' 			=>	$property_types,
		'status'			=>	array('current' , 'sold' , 'leased' ),
		'commercial_listing_type'	=>	'', // Listing Type, 'sale' , 'lease', 'both'
		'category_key'			=>	'', // Meta field key
		'category_value'		=>	'', // Meta field value
		'category_compare'		=>	'IN', // Compare using 'IN','NOT IN','BETWEEN','NOT BETWEEN'
		'limit'				=>	'', // Number of maximum posts to show
		'offset'			=>	'', // Offset posts. When used, pagination is disabled
		'template'			=>	false, // Template can be set to "slim" for home open style template
		'location'			=>	'', // Location slug. Should be a name like sorrento
		'tools_top'			=>	'off', // Tools before the loop like Sorter and Grid on or off
		'tools_bottom'			=>	'off', // Tools after the loop like pagination on or off
		'sortby'			=>	'property_auction', // Options: price, date : Default date
		'sort_order'			=>	'', // Sort by ASC or DESC
		'pagination'			=>	'on' // Enable or disable pagination
	), $atts ) );

	if(empty($post_type)) {
		return;
	}

	if(is_string($post_type) && $post_type == 'rental') {
		$meta_key_price = 'property_rent';
	} else {
		$meta_key_price = 'property_price';
	}

	$sort_options = array(
		'price'			=>	$meta_key_price,
		'date'			=>	'post_date'
	);
	if( !is_array($post_type) ) {
		$post_type 			= array_map('trim',explode(',',$post_type) );
	}
	ob_start();
	$paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;
	$args = array(
		'post_type' 		=>	$post_type,
		'posts_per_page'	=>	$limit,
		'paged' 		=>	$paged
	);

	// Offset query does not work with pagination
	if ( ! empty ( $offset ) ) {
		$args['offset'] 	= $offset;
		$pagination	 	= 'off'; // Disable pagination when offset is used
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

		if(!is_array($category_value) && in_array($category_compare,array('IN','NOT IN','BETWEEN','NOT BETWEEN') ) ) {
			$category_value = explode(",", $category_value);
			$category_value = array_map('trim', $category_value);

		}

		$args['meta_query'][] = array(
			'key' 		=> $category_key,
			'value' 	=> $category_value,
			'compare' 	=> $category_compare
		);

	}

	if( $sortby != '' ) {

		if($sortby == 'price') {
			$args['orderby']	=	'meta_value_num';
			$args['meta_key']	=	$meta_key_price;
		}
             else if($sortby == 'property_auction') {
			$args['orderby']	=	'meta_value_num';
			$args['meta_key']	=	'property_auction';
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
        $arr =$query_open->query['meta_query'];
        
 $args['meta_query'][] = array(
				'key' => 'property_status',
				'value' => $status,
				'compare' => 'IN'
			);
 
//$post_inspection_date1 = get_post_meta($posts_open_auction_id, 'property_status', true );
$posts_open_auction_id = $query_open->posts[0]->ID;
            $meta_data = get_post_custom($posts_open_auction_id);

	if ( $query_open->have_posts() ) { ?>
		<div class="loop epl-shortcode">
                    
                    
<?php     
$post_prop_status = get_post_meta($posts_open_auction_id, 'property_status', true );
if (($meta_data['property_authority'][0] != "")&& ($post_prop_status == 'current')){ ?>
    
    
			<div class="loop-content epl-shortcode-listing-category <?php echo epl_template_class( $template, 'archive' ); ?>">
                            
                            <?php
					if ( $tools_top == 'on' ) {
						do_action( 'epl_property_loop_start' );
					}?>
                               
				<?php	echo '<ul class="auctioninnerslider">'; 
                                        $counter = 0;
                                        $oldDate = "";
					while ( $query_open->have_posts() ) {                                               
                                                    $posts_open_auction_id = $query_open->posts[$counter]->ID;
                                                    $counter++;
						$query_open->the_post();
                                               ?> 
   <?php 
$post_inspection_date = get_post_meta($posts_open_auction_id, 'property_auction', true );
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
                                        <?php //do_action('epl_property_inspection_times'); ?>
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
					
                                        ?>
                                           
                                                 <?php 
					if ( $tools_bottom == 'on' ) {
						do_action( 'epl_property_loop_end' );
					}
				?>
			</div>
<?php } else{?>  

			<div class="loop-content epl-shortcode-listing-category <?php echo epl_template_class( $template, 'archive' ); ?>">
                       	
                            
                            <?php
					if ( $tools_top == 'on' ) {
						do_action( 'epl_property_loop_start' );
					}?>
                                             <ul class="bxslider">
					<?php while ( $query_open->have_posts() ) {
						$query_open->the_post();

						$template = str_replace('_','-',$template);
						epl_property_blog($template);
					}
                                        ?>
                                             </ul>
                                                 <?php 
					if ( $tools_bottom == 'on' ) {
						do_action( 'epl_property_loop_end' );
					}
				?>
			</div>
    
   
<?php }        ?>
			<div class="loop-footer">
				<?php
					if( $pagination == 'on')
					do_action('epl_pagination',array('query'	=>	$query_open));
				?>
			</div>
		</div>
		<?php
	} else {
		do_action( 'epl_shortcode_results_message' );
	}
	wp_reset_postdata();
	return ob_get_clean();
}
add_shortcode( 'listing_category_inner', 'epl_shortcode_listing_category_callback_inner' );


function epl_shortcode_listing_category_callback_auction( $atts ) {
	$property_types = epl_get_active_post_types();
	if(!empty($property_types)) {
		 $property_types = array_keys($property_types);
	}

	extract( shortcode_atts( array(
		'post_type' 			=>	$property_types,
		'status'			=>	array('current' , 'sold' , 'leased' ),
		'commercial_listing_type'	=>	'', // Listing Type, 'sale' , 'lease', 'both'
		'category_key'			=>	'', // Meta field key
		'category_value'		=>	'', // Meta field value
		'category_compare'		=>	'IN', // Compare using 'IN','NOT IN','BETWEEN','NOT BETWEEN'
		'limit'				=>	'', // Number of maximum posts to show
		'offset'			=>	'', // Offset posts. When used, pagination is disabled
		'template'			=>	false, // Template can be set to "slim" for home open style template
		'location'			=>	'', // Location slug. Should be a name like sorrento
		'tools_top'			=>	'off', // Tools before the loop like Sorter and Grid on or off
		'tools_bottom'			=>	'off', // Tools after the loop like pagination on or off
		'sortby'			=>	'property_auction', // Options: price, date : Default date
		'sort_order'			=>	'', // Sort by ASC or DESC
		'pagination'			=>	'on' // Enable or disable pagination
	), $atts ) );

	if(empty($post_type)) {
		return;
	}

	if(is_string($post_type) && $post_type == 'rental') {
		$meta_key_price = 'property_rent';
	} else {
		$meta_key_price = 'property_price';
	}

	$sort_options = array(
		'price'			=>	$meta_key_price,
		'date'			=>	'post_date'
	);
	if( !is_array($post_type) ) {
		$post_type 			= array_map('trim',explode(',',$post_type) );
	}
	ob_start();
	$paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;
	$args = array(
		'post_type' 		=>	$post_type,
		'posts_per_page'	=>	$limit,
		'paged' 		=>	$paged
	);

	// Offset query does not work with pagination
	if ( ! empty ( $offset ) ) {
		$args['offset'] 	= $offset;
		$pagination	 	= 'off'; // Disable pagination when offset is used
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

		if(!is_array($category_value) && in_array($category_compare,array('IN','NOT IN','BETWEEN','NOT BETWEEN') ) ) {
			$category_value = explode(",", $category_value);
			$category_value = array_map('trim', $category_value);

		}

		$args['meta_query'][] = array(
			'key' 		=> $category_key,
			'value' 	=> $category_value,
			'compare' 	=> $category_compare
		);

	}

	if( $sortby != '' ) {

		if($sortby == 'price') {
			$args['orderby']	=	'meta_value_num';
			$args['meta_key']	=	$meta_key_price;
		}
                else if($sortby == 'property_auction') {
			$args['orderby']	=	'meta_value_num';
			$args['meta_key']	=	'property_auction';
                        $args['order']		=	'DESC';
		}
                else {
			$args['orderby']	=	'post_date';
			$args['order']		=	'DESC';

		}
		$args['order']			=	$sort_order;
	}


	// add sortby arguments to query, if listings sorted by $_GET['sortby'];
	$args = epl_add_orderby_args($args);
	$query_open = new WP_Query( $args );
        $arr =$query_open->query['meta_query'];
        
 $args['meta_query'][] = array(
				'key' => 'property_status',
				'value' => $status,
				'compare' => 'IN'
			);
 

 $post_inspection_date1 = get_post_meta($posts_open_auction_id, 'property_status', true );
$posts_open_auction_id = $query_open->posts[0]->ID;
            $meta_data = get_post_custom($posts_open_auction_id);

	if ( $query_open->have_posts() ) { ?>
		<div class="loop epl-shortcode">
                    
                    
<?php     $post_inspection_date = get_post_meta($posts_open_auction_id, 'property_auction', true );
$post_prop_status = get_post_meta($posts_open_auction_id, 'property_status', true );
$post_inspection_date_all = explode('to' , $post_inspection_date);
$final_date = date('Y-m-d' , strtotime($post_inspection_date_all[0]));  

if (($meta_data['property_authority'][0] != "")&& ($post_prop_status == 'current')){ ?>
    
    
			<div class="loop-content epl-shortcode-listing-category <?php echo epl_template_class( $template, 'archive' ); ?>">   
                            <?php
					if ( $tools_top == 'on' ) {
						do_action( 'epl_property_loop_start' );
					}
                                        
                                        $oldDate = "";
                                        $counter = 0;
                                        while ( $query_open->have_posts() ) {
                                            $query_open->the_post();
                                            $posts_open_auction_id = $query_open->posts[$counter]->ID;
                                            //$counter++;
                                            $post_inspection_date = get_post_meta($posts_open_auction_id, 'property_auction', true );
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
                                        <?php //do_action('epl_property_inspection_times'); ?>
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
<?php }?>
			<div class="loop-footer">
				<?php
					if( $pagination == 'on')
					do_action('epl_pagination',array('query'	=>	$query_open));
				?>
			</div>
		</div>
		<?php
	} else {
		do_action( 'epl_shortcode_results_message' );
	}
	wp_reset_postdata();
	return ob_get_clean();
}
add_shortcode( 'listing_category_auction', 'epl_shortcode_listing_category_callback_auction' );

function epl_shortcode_listing_category_callback_sale_home( $atts ) {
	$property_types = epl_get_active_post_types();
	if(!empty($property_types)) {
		 $property_types = array_keys($property_types);
	}

	extract( shortcode_atts( array(
		'post_type' 			=>	$property_types,
		'status'			=>	array('current' , 'sold' , 'leased' ),
		'commercial_listing_type'	=>	'', // Listing Type, 'sale' , 'lease', 'both'
		'category_key'			=>	'', // Meta field key
		'category_value'		=>	'', // Meta field value
		'category_compare'		=>	'IN', // Compare using 'IN','NOT IN','BETWEEN','NOT BETWEEN'
		'limit'				=>	'', // Number of maximum posts to show
		'offset'			=>	'', // Offset posts. When used, pagination is disabled
		'template'			=>	false, // Template can be set to "slim" for home open style template
		'location'			=>	'', // Location slug. Should be a name like sorrento
		'tools_top'			=>	'off', // Tools before the loop like Sorter and Grid on or off
		'tools_bottom'			=>	'off', // Tools after the loop like pagination on or off
		'sortby'			=>	'property_sold_date', // Options: price, date : Default date
		'sort_order'			=>	'DESC', // Sort by ASC or DESC
		'pagination'			=>	'on' // Enable or disable pagination
	), $atts ) );

	if(empty($post_type)) {
		return;
	}

	if(is_string($post_type) && $post_type == 'rental') {
		$meta_key_price = 'property_rent';
	} else {
		$meta_key_price = 'property_price';
	}

	$sort_options = array(
		'price'			=>	$meta_key_price,
		'date'			=>	'post_date'
	);
	if( !is_array($post_type) ) {
		$post_type 			= array_map('trim',explode(',',$post_type) );
	}
	ob_start();
	$paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;
	$args = array(
		'post_type' 		=>	$post_type,
		'posts_per_page'	=>	$limit,
		'paged' 		=>	$paged
	);

	// Offset query does not work with pagination
	if ( ! empty ( $offset ) ) {
		$args['offset'] 	= $offset;
		$pagination	 	= 'off'; // Disable pagination when offset is used
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

		if(!is_array($category_value) && in_array($category_compare,array('IN','NOT IN','BETWEEN','NOT BETWEEN') ) ) {
			$category_value = explode(",", $category_value);
			$category_value = array_map('trim', $category_value);

		}

		$args['meta_query'][] = array(
			'key' 		=> $category_key,
			'value' 	=> $category_value,
			'compare' 	=> $category_compare
		);

	}

	if( $sortby != '' ) {

		if($sortby == 'price') {
			$args['orderby']	=	'meta_value_num';
			$args['meta_key']	=	$meta_key_price;
		}
                
                elseif($sortby == 'property_sold_date'){
                    
                       	$args['orderby']	=	'meta_value';
			$args['meta_key']	=	'property_sold_date';
                        $args['order']		=	'DESC';
                    
                }
                
                
                else {
			$args['orderby']	=	'post_date';
			$args['order']		=	'DESC';

		}
		$args['order']			=	$sort_order;
	}


	// add sortby arguments to query, if listings sorted by $_GET['sortby'];
	$args = epl_add_orderby_args($args);
	$query_open = new WP_Query( $args );
        $arr =$query_open->query['meta_query'];
        
 $args['meta_query'][] = array(
				'key' => 'property_status',
				'value' => $status,
				'compare' => 'IN'
			);
        
 

        
 $post_inspection_date1 = get_post_meta($posts_open_auction_id, 'property_status', true );
$posts_open_auction_id = $query_open->posts[0]->ID;
            $meta_data = get_post_custom($posts_open_auction_id);

	if ( $query_open->have_posts() ) { ?>
		<div class="loop epl-shortcode">
			<div class="loop-content epl-shortcode-listing-category <?php echo epl_template_class( $template, 'archive' ); ?>">
                       	
                            
                            <?php
					if ( $tools_top == 'on' ) {
						do_action( 'epl_property_loop_start' );
					}?>
                                             <ul class="bxslider">
					<?php while ( $query_open->have_posts() ) {
						$query_open->the_post();

						$template = str_replace('_','-',$template);
						epl_property_blog($template);
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
		do_action( 'epl_shortcode_results_message' );
	}
	wp_reset_postdata();
	return ob_get_clean();
}
add_shortcode( 'listing_category_sale_home', 'epl_shortcode_listing_category_callback_sale_home' );

function epl_shortcode_listing_category_callback_sale_inner( $atts ) {
	$property_types = epl_get_active_post_types();
	if(!empty($property_types)) {
		 $property_types = array_keys($property_types);
	}

	extract( shortcode_atts( array(
		'post_type' 			=>	$property_types,
		'status'			=>	array('current' , 'sold' , 'leased' ),
		'commercial_listing_type'	=>	'', // Listing Type, 'sale' , 'lease', 'both'
		'category_key'			=>	'', // Meta field key
		'category_value'		=>	'', // Meta field value
		'category_compare'		=>	'IN', // Compare using 'IN','NOT IN','BETWEEN','NOT BETWEEN'
		'limit'				=>	'', // Number of maximum posts to show
		'offset'			=>	'', // Offset posts. When used, pagination is disabled
		'template'			=>	false, // Template can be set to "slim" for home open style template
		'location'			=>	'', // Location slug. Should be a name like sorrento
		'tools_top'			=>	'off', // Tools before the loop like Sorter and Grid on or off
		'tools_bottom'			=>	'off', // Tools after the loop like pagination on or off
		'sortby'			=>	'property_sold_date', // Options: price, date : Default date
		'sort_order'			=>	'DESC', // Sort by ASC or DESC
		'pagination'			=>	'on' // Enable or disable pagination
	), $atts ) );

	if(empty($post_type)) {
		return;
	}

	if(is_string($post_type) && $post_type == 'rental') {
		$meta_key_price = 'property_rent';
	} else {
		$meta_key_price = 'property_price';
	}

	$sort_options = array(
		'price'			=>	$meta_key_price,
		'date'			=>	'post_date'
	);
	if( !is_array($post_type) ) {
		$post_type 			= array_map('trim',explode(',',$post_type) );
	}
	ob_start();
	$paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;
	$args = array(
		'post_type' 		=>	$post_type,
		'posts_per_page'	=>	$limit,
		'paged' 		=>	$paged
	);

	// Offset query does not work with pagination
	if ( ! empty ( $offset ) ) {
		$args['offset'] 	= $offset;
		$pagination	 	= 'off'; // Disable pagination when offset is used
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

		if(!is_array($category_value) && in_array($category_compare,array('IN','NOT IN','BETWEEN','NOT BETWEEN') ) ) {
			$category_value = explode(",", $category_value);
			$category_value = array_map('trim', $category_value);

		}

		$args['meta_query'][] = array(
			'key' 		=> $category_key,
			'value' 	=> $category_value,
			'compare' 	=> $category_compare
		);

	}

	if( $sortby != '' ) {

		if($sortby == 'price') {
			$args['orderby']	=	'meta_value_num';
			$args['meta_key']	=	$meta_key_price;
		} 
                
                elseif($sortby == 'property_sold_date'){
                       	$args['orderby']	=	'meta_value';
			$args['meta_key']	=	'property_sold_date';
                        $args['order']		=	'DESC';
                    
                }
                else {
			$args['orderby']	=	'post_date';
			$args['order']		=	'DESC';

		}
		$args['order']			=	$sort_order;
	}


	// add sortby arguments to query, if listings sorted by $_GET['sortby'];
	$args = epl_add_orderby_args($args);
	$query_open = new WP_Query( $args );
        $arr =$query_open->query['meta_query'];
        
 $args['meta_query'][] = array(
				'key' => 'property_status',
				'value' => $status,
				'compare' => 'IN'
			);
        
 

        
 $post_inspection_date1 = get_post_meta($posts_open_auction_id, 'property_status', true );
$posts_open_auction_id = $query_open->posts[0]->ID;
            $meta_data = get_post_custom($posts_open_auction_id);

	if ( $query_open->have_posts() ) { ?>
		<div class="loop epl-shortcode">
			<div class="loop-content epl-shortcode-listing-category <?php echo epl_template_class( $template, 'archive' ); ?>">
                       	
                            
                            <?php
					if ( $tools_top == 'on' ) {
						do_action( 'epl_property_loop_start' );
					}?>
                                             <ul class="salesinner">
					<?php while ( $query_open->have_posts() ) {
						$query_open->the_post();

						$template = str_replace('_','-',$template);
						epl_property_blog($template);
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
		do_action( 'epl_shortcode_results_message' );
	}
	wp_reset_postdata();
	return ob_get_clean();
}
add_shortcode( 'listing_category_sale_inner', 'epl_shortcode_listing_category_callback_sale_inner' );
