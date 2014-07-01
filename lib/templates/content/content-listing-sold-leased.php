<?php
/*
 * WebConnected Property Templates
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// TEMPLATE - Leased/sold property list
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
		<div class="epl-tab-section">
			<?php if ( 'property' == $post_type || 'land' == $post_type || 'rural' == $post_type) { ?>
				<h5 class="tab-title"><?php _e('Recently Sold', 'epl'); ?></h5>
			<?php } else { ?>
				<h5 class="tab-title"><?php _e('Recently Leased', 'epl'); ?></h5>
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
