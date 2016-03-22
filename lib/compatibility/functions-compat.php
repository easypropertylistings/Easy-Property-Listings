<?php
/**
 * EPL Functions Compatibility
 *
 * @package     EPL
 * @subpackage  Compatibility/Functions
 * @copyright   Copyright (c) 2014, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Get EPL author meta
 *
 * @since 1.0
 * @return the list of author meta variables
 */
function epl_get_author_meta() {
	global $epl_author_meta_sent;
	if($epl_author_meta_sent) {
		return;
	}

	require_once EPL_PATH_LIB . 'templates/content/author-meta.php';
	$epl_author_meta_sent = true;
}

/**
 * Get EPL property address
 *
 * @since 1.0
 * @return the string for address
 */
function epl_get_property_address($post_ID='') {
	if($post_ID == '') {
		$post_ID = get_the_ID();
	}
	$property_meta = epl_get_property_meta($post_ID);

	$address = '';

	if(isset($property_meta['property_address_street_number']) && !empty($property_meta['property_address_street_number'])) {
		$property_address_street_number = $property_meta['property_address_street_number'][0];
		if( $property_address_street_number != '' ) {
			$address .= $property_address_street_number . ", ";
		}
	}

	if(isset($property_meta['property_address_street']) && !empty($property_meta['property_address_street'])) {
		$property_address_street = $property_meta['property_address_street'][0];
		if( $property_address_street != '' ) {
			$address .= $property_address_street . ", ";
		}
	}

	if(isset($property_meta['property_address_suburb']) && !empty($property_meta['property_address_suburb'])) {
		$property_address_suburb = $property_meta['property_address_suburb'][0];
		if( $property_address_suburb != '' ) {
			$address .= $property_address_suburb . ", ";
		}
	}

	if(isset($property_meta['property_address_state']) && !empty($property_meta['property_address_state'])) {
		$property_address_state = $property_meta['property_address_state'][0];
		if( $property_address_state != '' ) {
			$address .= $property_address_state . ", ";
		}
	}

	if(isset($property_meta['property_address_postal_code']) && !empty($property_meta['property_address_postal_code'])) {
		$property_address_postal_code = $property_meta['property_address_postal_code'][0];
		if( $property_address_postal_code != '' ) {
			$address .= $property_address_postal_code . ", ";
		}
	}

	$address = trim($address); $address = trim($address, ","); $address = trim($address);
	return apply_filters('epl_get_property_address_filter', $address);
}

// Front End Functions
if ( ! is_admin() )
	return;

/**
 * Listing Function for paged card display
 *
 * @compat	not being used @since 1.3 in core, but still kept for extensions which may be using this function
 * @since	1.3
 */
function epl_property_blog_default() {

	global $property,$epl_settings;
	$property_status = $property->get_property_meta('property_status');
	// Status Removal Do Not Display Withdrawn or OffMarket listings
	if ( $property_status == 'withdrawn' || $property_status == 'offmarket' ) {
		// Do Not Display Withdrawn or OffMarket listings
	} else {
		$option = '';
		if(!empty($epl_settings) && isset($epl_settings['epl_property_card_style'])) {
			$option = $epl_settings['epl_property_card_style'];
		}

		$action_check = has_action( 'epl_loop_template' );
		if ( $action_check != '' && $option !== 0 ) {
			do_action( 'epl_loop_template' );
		} else {
			epl_get_template_part('loop-listing-blog-default.php');
		}
	} // End Status Removal
}

/**
 * Listing Function for slim view
 *
 * @compat	not being used @since 1.3 in core, but still kept for extensions which may be using this function
 * @since	1.3
 */
function epl_property_blog_slim() {
	global $property,$epl_settings;
	if( is_null($property) ) {
		return;
	}
	$property_status = $property->get_property_meta('property_status');
	// Status Removal
	if ( $property_status == 'withdrawn' || $property_status == 'offmarket' ) {
		// Do Not Display Withdrawn or OffMarket listings
	} else {
		$option = '';
		if(!empty($epl_settings) && isset($epl_settings['epl_property_card_style'])) {
			$option = $epl_settings['epl_property_card_style'];
		}

		$action_check = has_action( 'epl_loop_template' );
		if ( $action_check != '' && $option !== 0 ) {
			do_action( 'epl_loop_template' );
		} else {
			epl_get_template_part('loop-listing-blog-slim.php');
		}
	} // End Status Removal
}

/*
 * Listing Function for table open
 *
 * @since 2.1.6
 */
function epl_property_blog_table() {
	global $property,$epl_settings;
	if( is_null($property) ) {
		return;
	}
	$property_status = $property->get_property_meta('property_status');
	// Status Removal
	if ( $property_status == 'withdrawn' || $property_status == 'offmarket' ) {
		// Do Not Display Withdrawn or OffMarket listings
	} else {
		$option = '';
		if(!empty($epl_settings) && isset($epl_settings['epl_property_card_style'])) {
			$option = $epl_settings['epl_property_card_style'];
		}

		$action_check = has_action( 'epl_loop_template' );
		if ( $action_check != '' && $option !== 0 ) {
			do_action( 'epl_loop_template' );
		} else {
			epl_get_template_part('loop-listing-blog-table.php');
		}
	} // End Status Removal
}

/*
 * Listing Function for table open
 * Kept for extensions which may be using this function
 *
 * @since 2.1.8
 */
function epl_property_blog_table_open() {
	global $property,$epl_settings;
	if( is_null($property) ) {
		return;
	}
	$property_status = $property->get_property_meta('property_status');
	// Status Removal
	if ( $property_status == 'withdrawn' || $property_status == 'offmarket' ) {
		// Do Not Display Withdrawn or OffMarket listings
	} else {
		$option = '';
		if(!empty($epl_settings) && isset($epl_settings['epl_property_card_style'])) {
			$option = $epl_settings['epl_property_card_style'];
		}

		$action_check = has_action( 'epl_loop_template' );
		if ( $action_check != '' && $option !== 0 ) {
			do_action( 'epl_loop_template' );
		} else {
			epl_get_template_part('loop-listing-blog-table-open.php');
		}
	} // End Status Removal
}

/*
 * TEMPLATE - Leased/sold property list
 * Kept for extensions which may be using this function
 *
 * @since 1.3
 */
function epl_property_sold_leased() {
	$property_suburb = get_post_custom_values('property_address_suburb');
	$post_id = $property_suburb[0]['ID'];
	$terms = get_the_terms( $post->ID, 'location' );
	if( $terms != '' ) {
		global $post;
		foreach($terms as $term){
			$term->slug;
		}
	}

	$post_type = get_post_type();

	if ( 'property' == $post_type ) {
		$query = new WP_Query( array (
			'post_type' => 'property',
			'location' => $term->slug,
			'meta_query' => array(
				array(
					'key' => 'property_status',
					'value' => 'sold'
				)
			),
			'posts_per_page' => '5'
		) );
	} elseif ( 'land' == $post_type ) {
		$query = new WP_Query( array (
			'post_type' => 'land',
			'meta_query' => array(
				array(
					'key' => 'property_status',
					'value' => 'sold'
				)
			),
			'property_status' => 'sold',
			'posts_per_page' => '5'
		) );
	} elseif ( 'rural' == $post_type ) {
		$query = new WP_Query( array (
			'post_type' => 'rural',
			'location' => $term->slug,
			'meta_query' => array(
				array(
					'key' => 'property_status',
					'value' => 'sold'
				)
			),
			'posts_per_page' => '5'
		) );
	} else {
		$query = new WP_Query( array (
			'post_type' => 'rental',
			'location' => $term->slug,
			'meta_query' => array(
				array(
					'key' => 'property_status',
					'value' => 'leased'
				)
			),
			'posts_per_page' => '5'
		) );
	}

	if ( $query->have_posts() ) { ?>
		<div class="epl-tab-section epl-tab-section-listing-history">
			<?php if ( 'property' == $post_type || 'land' == $post_type || 'rural' == $post_type) { ?>
				<h5 class="epl-tab-title epl-tab-title-sales tab-title"><?php _e('Recently Sold', 'easy-property-listings'); ?></h5>
			<?php } else { ?>
				<h5 class="epl-tab-title epl-tab-title-leased tab-title"><?php _e('Recently Leased', 'easy-property-listings'); ?></h5>
			<?php } ?>
			<div class="tab-content">
				<ul>
					<?php
						while ( $query->have_posts() ) {
							$query->the_post(); ?>

							<!-- Suburb Tab -->
							<li><a href="<?php the_permalink(); ?>"><?php the_title(); ?><?php echo $suburb[0]; ?></a></li>
							<?php
						}
					?>
				</ul>
			</div>
		</div>
		<?php
	}
	wp_reset_postdata();
}

/*
 * Staff Directory Card
 * Kept for extensions which may be using this function
 *
 * @since 1.3
 */
function epl_property_author_card( $display , $image , $title , $icons) {
	global $property,$epl_author;
	if( is_null($epl_author) )
		return;
	$property_status = $property->get_property_meta('property_status');
	// Status Removal
	if ( $property_status == 'withdrawn' || $property_status == 'offmarket' ) {
		// Do Not Display Withdrawn or OffMarket listings
	} else {
		//$arg_list = get_defined_vars();
		//epl_get_template_part('widget-content-author.php',$arg_list);

		?>
			<div id="post-<?php the_ID(); ?>" style="width: 20%;margin-right: 1em;float: left;" class="epl-widget property-widget-image hentry">
				<div class="entry-header">
					<?php if ( has_post_thumbnail() ) : ?>
						<div class="epl-img-widget">
							<a href="<?php the_permalink(); ?>">
								<?php the_post_thumbnail( $image ); ?>
							</a>
						</div>
					<?php endif; ?>
				</div>

				<div class="entry-content">
					<?php
						// Heading Options
						if ($title == 'on') { ?>
							<h5 class="property-meta heading"><?php echo $the_property_heading; ?></h5>
						<?php }
					?>

					<!-- Address -->
					<div class="property-address">
						<?php do_action('epl_property_address'); ?>
					</div>
					<!-- END Address -->

					<?php
						// Icon Options
						if ( $icons == 'all' ) { ?>
							<div class="property-meta property-feature-icons"><?php epl_property_icons(); ?></div>
						<?php } elseif ($icons == 'bb') { ?>
							<div class="property-meta property-feature-icons"><?php echo epl_get_property_bb_icons(); ?></div>
						<?php } ?>

					<div class="property-meta price"><?php epl_property_price() ?></div>
					<form class="epl-property-button" action="<?php the_permalink(); ?>" method="post">
						<input type=submit value="<?php _e('Read More', 'easy-property-listings'); ?>" />
					</form>
				</div>
			</div>

	<?php
	} // End Status Removal
}