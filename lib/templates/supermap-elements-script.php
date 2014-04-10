<?php
/*
 * Map Script
 */

function supermap_elements_script() {

	// Listings 
	$map_args = array(
		'post_type' => 'epl_rental',
		'posts_per_page' => '10'
	);	
	$map_query = new WP_Query( $map_args );
	if ( $map_query->have_posts() ) { ?>
		<script>
			var property_list = [];
			var default_mapcenter = [];
		
			<?php 
				while ( $map_query->have_posts() ) {
					$map_query->the_post();
					include( 'property-meta.php' ); ?>
					
					var property = {
						thumb: '<a class="map-thumb" href="<?php the_permalink() ?>"><?php if ( has_post_thumbnail() ) { the_post_thumbnail( 'thumbnail' ); } ?></a>',
						price: "<?php echo $price; ?>",
						fullPrice: "<?php echo $price; ?>",
						bed: "<?php echo $property_bedrooms; ?> Beds",
						bath: "<?php echo $property_bathrooms; ?> Baths",
						size: "<?php echo $property_land; ?>",
						street: "<?php echo $property_address_street; ?>",
						suburb: "<?php echo $property_address_suburb; ?>",
						state: "<?php echo $property_address_state; ?>",
						zip: "<?php echo $property_address_postal_code; ?>",
						latlong: "<?php echo $coords; ?>",
						permalink: "<?php the_permalink() ?>",
						agentThumb: "http://wp.contempographicdesign.com/wp_real_estate_6/wp-content/uploads/2014/01/syd.jpg",
						agentName: "Sydney Taft",
						agentTagline: "Luxury Specialist",
						agentPhone: "619-555-8979",
						agentEmail: "syd@test.com",
						isHome: "true"
					}
					property_list.push(property);
					<?php
				}
			?>
		</script>
		<?php
	}
	wp_reset_postdata();
}
