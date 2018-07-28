<?php
/**
 * SHORTCODE :: Listing [listing]
 *
 * @package     EPL
 * @subpackage  Shortcode/Listing
 * @copyright   Copyright (c) 2014, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Listing Shortcode [listing]
 *
 * This shortcode allows for you to specify the property type(s) using
 * [listing post_type="property,rental" status="current,sold,leased" template="default"] option. You can also
 * limit the number of entries that display. using  [listing limit="5"]
 *
 * @since       1.0
 */
function epl_shortcode_listing_callback( $atts ) {


	$property_types = epl_get_active_post_types();
	if ( ! empty($property_types ) ) {
		$property_types = array_keys( $property_types );
	}

	$attributes = shortcode_atts( array(
		'post_type'	=> $property_types, //Post Type
		'status'	=> array( 'current', 'sold', 'leased' ),
		'limit'		=> '', // Number of maximum posts to show
		'offset'	=> '', // Offset posts. When used, pagination is disabled
		'author'	=> '',	// Author of listings.
		'featured'	=> 0,	// Featured listings.
		'template'	=> false, // Template can be set to "slim" for home open style template
		'location'	=> '', // Location slug. Should be a name like sorrento
		'tools_top'	=> 'off', // Tools before the loop like Sorter and Grid on or off
		'tools_bottom'	=> 'off', // Tools after the loop like pagination on or off
		'sortby'	=> 'date', // Options: price, date : Default date
		'sort_order'	=> 'DESC', // Sort by ASC or DESC
		'query_object'	=> '', // only for internal use . if provided use it instead of custom query
		'pagination'	=> 'on' // Enable or disable pagination
	), $atts );

	if ( is_string( $attributes['post_type'] ) && $attributes['post_type'] == 'rental' ) {
		$meta_key_price = 'property_rent';
	} else {
		$meta_key_price = 'property_price';
	}

	$sort_options = array(
		'price'			=>	$meta_key_price,
		'date'			=>	'post_date',
	);
	if ( ! is_array( $attributes['post_type'] ) ) {
		$attributes['post_type'] = array_map( 'trim', explode( ',',$attributes['post_type'] ) );
	}

	$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
	$args = array(
		'post_type'      =>	$attributes['post_type'],
		'posts_per_page' =>	$attributes['limit'],
		'paged'          =>	absint( $paged ),
	);

	// Offset query does not work with pagination
	if ( ! empty ( $attributes['offset'] ) ) {
		$args['offset'] 		= $attributes['offset'];
		$attributes['pagination'] 	= 'off'; // Disable pagination when offset is used
	}

	$args['meta_query'] = epl_parse_atts($atts);

	// Listings of specified author.
	if ( ! empty( $attributes['author'] ) ) {
		$attributes['author'] = (array) $attributes['author'];
		if ( is_array( $attributes['author'] ) ) {
			$author_ids = array_map( 'epl_get_author_id_from_name', $attributes['author'] );
			$attributes['author'] = implode( ',', $author_ids );
		}
		$args['author'] = trim( $attributes['author'] );
	}

	// Featured listings.
	if ( $attributes['featured'] ) {
		$args['meta_query'][] = array(
			'key'   => 'property_featured',
			'value' => 'yes',
		);
	}

	if ( ! empty( $attributes['location'] ) ) {
		if ( ! is_array( $attributes['location'] ) ) {
			$attributes['location'] = array_map( 'trim', explode( ',', $attributes['location'] ) );

			$args['tax_query'][] = array(
				'taxonomy'	=> 'location',
				'field'		=> 'slug',
				'terms' 	=> $attributes['location'],
			);
		}
	}

	if ( ! empty( $attributes['status'] ) ) {
		if ( ! is_array( $attributes['status'] ) ) {
			$attributes['status'] = array_map( 'trim', explode( ',', $attributes['status'] ) );

			$args['meta_query'][] = array(
				'key'		=> 'property_status',
				'value'		=> $attributes['status'],
				'compare'	=> 'IN',
			);

			add_filter( 'epl_sorting_options', 'epl_sorting_options_callback' );
		}
	}

	if ( ! empty ( $attributes['sortby'] ) ) {
		if ( $attributes['sortby'] == 'price' ) {
			$args['orderby']  = 'meta_value_num';
			$args['meta_key'] =	$meta_key_price;
		} else {
			$args['orderby']  = 'post_date';
			$args['order']    = 'DESC';
		}
		$args['order']        = $attributes['sort_order'];
	}

	// add sortby arguments to query, if listings sorted by $_GET['sortby'];
	$args = epl_add_orderby_args($args);
	$query_open = new WP_Query( $args );

	if ( is_object( $attributes['query_object'] ) ) {
		$query_open = $attributes['query_object'];
	}

	ob_start();
        
        if ( $query_open->have_posts() ) {
	?>
	<div class="loop epl-shortcode">
            
            
<?php
                    ?>

		<div class="loop-content epl-shortcode-listing <?php echo epl_template_class( $attributes['template'], 'archive' ); ?>">

			<?php
			if ( $attributes['tools_top'] == 'on' ) {
				do_action( 'epl_property_loop_start' );
			}
                        
                        echo '<ul class="homecurrentslider">'; 
                       //  $counter = 0;
                      //  $oldDate = "";				
			while ( $query_open->have_posts() ) {
                               // $posts_open_ispection_id = $query_open->posts[$counter]->ID;
//                                $counter++;					
				$query_open->the_post();
//				$attributes['template'] = str_replace( '_', '-', $attributes['template'] );
//				epl_property_blog( $attributes['template'] );
                               ?>
                       <?php 
// $post_inspection_date = get_post_meta($posts_open_ispection_id, 'property_inspection_times', true );
//$post_inspection_date_all = explode('to' , $post_inspection_date);
//$post_inspection_date_new = explode(' ' ,$post_inspection_date_all[0]);
//$final_time = date('Y-m-d' , strtotime($post_inspection_date_new[0] ));

//if($oldDate != $final_time){
//    ?>
<!--    <li>
        <div class="epl-listing-post epl-property-blog epl-clearfix opne-first-block">
                                      <header data-tile="opening-times" class="tile date-tile slick-slide slick-active" style="width: 220px;">
                              <h6 class="event-date">
                                  <span class="day">//<?php echo date("l", strtotime($final_time)); ?></span><br>
                                  <span class="date no-22">//<?php echo date("j", strtotime($final_time)); ?></span><br>
<span class="month">//<?php echo date("F", strtotime($final_time)); ?></span><br>
</h6>      	
                                </header>
                                
                                
                                
                            </div>
    </li>-->
    <?php
//}
//$oldDate = $final_time;
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

        
        
//	epl_get_template_part(
//		'shortcode-listing.php',
//		array(
//			'attributes' => $attributes,
//			'query_open' => $query_open,
//		)
//	);
	return ob_get_clean();
}
add_shortcode( 'listing', 'epl_shortcode_listing_callback' );


function epl_shortcode_listing_callback_current( $atts ) {


	$property_types = epl_get_active_post_types();
	if ( ! empty($property_types ) ) {
		$property_types = array_keys( $property_types );
	}

	$attributes = shortcode_atts( array(
		'post_type'	=> $property_types, //Post Type
		'status'	=> array( 'current', 'sold', 'leased' ),
		'limit'		=> '-1', // Number of maximum posts to show
		'offset'	=> '', // Offset posts. When used, pagination is disabled
		'author'	=> '',	// Author of listings.
		'featured'	=> 0,	// Featured listings.
		'template'	=> false, // Template can be set to "slim" for home open style template
		'location'	=> '', // Location slug. Should be a name like sorrento
		'tools_top'	=> 'off', // Tools before the loop like Sorter and Grid on or off
		'tools_bottom'	=> 'off', // Tools after the loop like pagination on or off
		'sortby'	=> 'date', // Options: price, date : Default date
		'sort_order'	=> 'DESC', // Sort by ASC or DESC
		'query_object'	=> '', // only for internal use . if provided use it instead of custom query
		'pagination'	=> 'on' // Enable or disable pagination
	), $atts );

	if ( is_string( $attributes['post_type'] ) && $attributes['post_type'] == 'rental' ) {
		$meta_key_price = 'property_rent';
	} else {
		$meta_key_price = 'property_price';
	}

	$sort_options = array(
		'price'			=>	$meta_key_price,
		'date'			=>	'post_date',
	);
	if ( ! is_array( $attributes['post_type'] ) ) {
		$attributes['post_type'] = array_map( 'trim', explode( ',',$attributes['post_type'] ) );
	}

	$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
	$args = array(
		'post_type'      =>	$attributes['post_type'],
		'posts_per_page' =>	$attributes['limit'],
		'paged'          =>	absint( $paged ),
	);

	// Offset query does not work with pagination
	if ( ! empty ( $attributes['offset'] ) ) {
		$args['offset'] 		= $attributes['offset'];
		$attributes['pagination'] 	= 'off'; // Disable pagination when offset is used
	}

	$args['meta_query'] = epl_parse_atts($atts);

	// Listings of specified author.
	if ( ! empty( $attributes['author'] ) ) {
		$attributes['author'] = (array) $attributes['author'];
		if ( is_array( $attributes['author'] ) ) {
			$author_ids = array_map( 'epl_get_author_id_from_name', $attributes['author'] );
			$attributes['author'] = implode( ',', $author_ids );
		}
		$args['author'] = trim( $attributes['author'] );
	}

	// Featured listings.
	if ( $attributes['featured'] ) {
		$args['meta_query'][] = array(
			'key'   => 'property_featured',
			'value' => 'yes',
		);
	}

	if ( ! empty( $attributes['location'] ) ) {
		if ( ! is_array( $attributes['location'] ) ) {
			$attributes['location'] = array_map( 'trim', explode( ',', $attributes['location'] ) );

			$args['tax_query'][] = array(
				'taxonomy'	=> 'location',
				'field'		=> 'slug',
				'terms' 	=> $attributes['location'],
			);
		}
	}

	if ( ! empty( $attributes['status'] ) ) {
		if ( ! is_array( $attributes['status'] ) ) {
			$attributes['status'] = array_map( 'trim', explode( ',', $attributes['status'] ) );

			$args['meta_query'][] = array(
				'key'		=> 'property_status',
				'value'		=> $attributes['status'],
				'compare'	=> 'IN',
			);

			add_filter( 'epl_sorting_options', 'epl_sorting_options_callback' );
		}
	}

	if ( ! empty ( $attributes['sortby'] ) ) {
		if ( $attributes['sortby'] == 'price' ) {
			$args['orderby']  = 'meta_value_num';
			$args['meta_key'] =	$meta_key_price;
		} else {
			$args['orderby']  = 'post_date';
			$args['order']    = 'DESC';
		}
		$args['order']        = $attributes['sort_order'];
	}

	// add sortby arguments to query, if listings sorted by $_GET['sortby'];
	$args = epl_add_orderby_args($args);
	$query_open = new WP_Query( $args );

	if ( is_object( $attributes['query_object'] ) ) {
		$query_open = $attributes['query_object'];
	}

	ob_start();
        
        if ( $query_open->have_posts() ) {
	?>
	<div class="loop epl-shortcode">
            
            
<?php
                    ?>

		<div class="loop-content epl-shortcode-listing <?php echo epl_template_class( $attributes['template'], 'archive' ); ?>">

			<?php
			if ( $attributes['tools_top'] == 'on' ) {
				do_action( 'epl_property_loop_start' );
			}
                        
                        echo '<ul class="currentinnerslider">'; 
                         $counter = 0;
                        $oldDate = "";				
			while ( $query_open->have_posts() ) {
                                $posts_open_ispection_id = $query_open->posts[$counter]->ID;
                                $counter++;					
				$query_open->the_post();
//				$attributes['template'] = str_replace( '_', '-', $attributes['template'] );
//				epl_property_blog( $attributes['template'] );
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

        
        
//	epl_get_template_part(
//		'shortcode-listing.php',
//		array(
//			'attributes' => $attributes,
//			'query_open' => $query_open,
//		)
//	);
	return ob_get_clean();
}
add_shortcode( 'listing_current', 'epl_shortcode_listing_callback_current' );


function epl_shortcode_listing_callback_sales( $atts ) {


	$property_types = epl_get_active_post_types();
	if ( ! empty($property_types ) ) {
		$property_types = array_keys( $property_types );
	}

	$attributes = shortcode_atts( array(
		'post_type'	=> $property_types, //Post Type
		'status'	=> array( 'current', 'sold', 'leased' ),
		'limit'		=> '', // Number of maximum posts to show
		'offset'	=> '', // Offset posts. When used, pagination is disabled
		'author'	=> '',	// Author of listings.
		'featured'	=> 0,	// Featured listings.
		'template'	=> false, // Template can be set to "slim" for home open style template
		'location'	=> '', // Location slug. Should be a name like sorrento
		'tools_top'	=> 'off', // Tools before the loop like Sorter and Grid on or off
		'tools_bottom'	=> 'off', // Tools after the loop like pagination on or off
		'sortby'	=> 'sold_date', // Options: price, date : Default date
		'sort_order'	=> 'DESC', // Sort by ASC or DESC
		'query_object'	=> '', // only for internal use . if provided use it instead of custom query
		'pagination'	=> 'on' // Enable or disable pagination
	), $atts );

	if ( is_string( $attributes['post_type'] ) && $attributes['post_type'] == 'rental' ) {
		$meta_key_price = 'property_rent';
	} else {
		$meta_key_price = 'property_price';
	}

	$sort_options = array(
		'price'			=>	$meta_key_price,
		'date'			=>	'post_date',
	);
	if ( ! is_array( $attributes['post_type'] ) ) {
		$attributes['post_type'] = array_map( 'trim', explode( ',',$attributes['post_type'] ) );
	}

	$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
	$args = array(
		'post_type'      =>	$attributes['post_type'],
		'posts_per_page' =>	$attributes['limit'],
		'paged'          =>	absint( $paged ),
	);

	// Offset query does not work with pagination
	if ( ! empty ( $attributes['offset'] ) ) {
		$args['offset'] 		= $attributes['offset'];
		$attributes['pagination'] 	= 'off'; // Disable pagination when offset is used
	}

	$args['meta_query'] = epl_parse_atts($atts);

	// Listings of specified author.
	if ( ! empty( $attributes['author'] ) ) {
		$attributes['author'] = (array) $attributes['author'];
		if ( is_array( $attributes['author'] ) ) {
			$author_ids = array_map( 'epl_get_author_id_from_name', $attributes['author'] );
			$attributes['author'] = implode( ',', $author_ids );
		}
		$args['author'] = trim( $attributes['author'] );
	}

	// Featured listings.
	if ( $attributes['featured'] ) {
		$args['meta_query'][] = array(
			'key'   => 'property_featured',
			'value' => 'yes',
		);
	}

	if ( ! empty( $attributes['location'] ) ) {
		if ( ! is_array( $attributes['location'] ) ) {
			$attributes['location'] = array_map( 'trim', explode( ',', $attributes['location'] ) );

			$args['tax_query'][] = array(
				'taxonomy'	=> 'location',
				'field'		=> 'slug',
				'terms' 	=> $attributes['location'],
			);
		}
	}

	if ( ! empty( $attributes['status'] ) ) {
		if ( ! is_array( $attributes['status'] ) ) {
			$attributes['status'] = array_map( 'trim', explode( ',', $attributes['status'] ) );

			$args['meta_query'][] = array(
				'key'		=> 'property_status',
				'value'		=> $attributes['status'],
				'compare'	=> 'IN',
			);

			add_filter( 'epl_sorting_options', 'epl_sorting_options_callback' );
		}
	}

	if ( ! empty ( $attributes['sortby'] ) ) {
	if ( $attributes['sortby'] == 'price' ) {
			$args['orderby']  = 'meta_value_num';
			$args['meta_key'] =	$meta_key_price;
		}
                
                if ( $attributes['sortby'] == 'sold_date' )  {
                    	$args['orderby']	=	'meta_value';
			$args['meta_key']	=	'property_sold_date';
                        $args['order']		=	'DESC';
                    
                
                }else {
                            $args['orderby']  = 'post_date';
                            $args['order']    = 'DESC';
                    }
                    $args['order']        = $attributes['sort_order'];
	}

	// add sortby arguments to query, if listings sorted by $_GET['sortby'];
	$args = epl_add_orderby_args($args);
	$query_open = new WP_Query( $args );

	if ( is_object( $attributes['query_object'] ) ) {
		$query_open = $attributes['query_object'];
	}

	ob_start();
        
        if ( $query_open->have_posts() ) {
	?>
	<div class="loop epl-shortcode">
            
            
<?php
                    ?>

		<div class="loop-content epl-shortcode-listing <?php echo epl_template_class( $attributes['template'], 'archive' ); ?>">

			<?php
			if ( $attributes['tools_top'] == 'on' ) {
				do_action( 'epl_property_loop_start' );
			}
                        
                        echo '<ul class="">'; 
                        
			while ( $query_open->have_posts() ) {
				$query_open->the_post();
//				$attributes['template'] = str_replace( '_', '-', $attributes['template'] );
//				epl_property_blog( $attributes['template'] );
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

        
        
//	epl_get_template_part(
//		'shortcode-listing.php',
//		array(
//			'attributes' => $attributes,
//			'query_open' => $query_open,
//		)
//	);
	return ob_get_clean();
}
add_shortcode( 'listingsales', 'epl_shortcode_listing_callback_sales' );


/**
 * Listing Shortcode Sorting
 *
 * @since       1.0
 */
function epl_sorting_options_callback( $sorters ) {
	foreach ( $sorters as $key => &$sorter ) {
		if ( $sorter['id'] == 'status_asc' || $sorter['id'] == 'status_desc' ) {
			unset( $sorters[ $key ] );
		}
	}
	return $sorters;
}

/**
 * Get author id from name
 *
 * @since       3.1.1
 */
function epl_get_author_id_from_name($author) {
	if( is_numeric($author) ) {
		return absint($author);
	} else {
		$user = get_user_by( 'login', $author );
		return $user->ID;
	}
}
