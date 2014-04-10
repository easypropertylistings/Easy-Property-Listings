<?php
/*
 * WebConnected Property Templates
 */

// TEMPLATE - Leased/sold property list
function epl_property_sold_leased() {
	$property_suburb = get_post_custom_values('property_address_suburb');
	$post_id = $property_suburb[0]['ID'];
	$terms = get_the_terms( $post->ID, 'epl_tax_suburb' );
	if( $terms != '' ) {
		global $post;
		foreach($terms as $term){
			$term->slug;
		}
	}
	
	$post_type = get_post_type();

	if ( 'epl_property' == $post_type ) {
		$query = new WP_Query( array (
			'post_type' => 'epl_property',
			'epl_tax_suburb' => $term->slug,
			'meta_query' => array(
						array(
							'key' => 'property_status',
							'value' => 'sold'
						)
					),
			'posts_per_page' => '5'
		) );
	} elseif ( 'epl_land' == $post_type ) {
		$query = new WP_Query( array (
			'post_type' => 'epl_land',
			'meta_query' => array(
						array(
							'key' => 'property_status',
							'value' => 'sold'
						)
					),
			'property_status' => 'sold',
			'posts_per_page' => '5'
		) );
	} elseif ( 'epl_rural' == $post_type ) {
		$query = new WP_Query( array (
			'post_type' => 'epl_rural',
			'epl_tax_suburb' => $term->slug,
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
			'post_type' => 'epl_rental',
			'epl_tax_suburb' => $term->slug,
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
		<div class="tab-section">
			<?php if ( 'epl_property' == $post_type || 'epl_land' == $post_type || 'epl_rural' == $post_type) { ?>
				<h5 class="tab-title">Recently Sold</h5>
			<?php } else { ?>
				<h5 class="tab-title">Recently Leased</h5>
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

// TEMPLATE - Suburb Mini Info
function epl_property_tab_suburb( ) {
	//$property_suburb = get_post_custom_values('property_address_suburb');
	//$property_suburb_str = strtolower($property_suburb[0]);
	//$post_id = $property_suburb[0]['ID'];	
	$terms = get_the_terms( $post->ID, 'epl_tax_suburb' );
	if ($terms != '') {
		global $post;
		foreach($terms as $term) {
			$term->slug;
		}
	}
	$query = new WP_Query( array (
		'post_type' => 'epl_suburb',
		'epl_tax_suburb' => $term->slug,
		'posts_per_page' => '1'
	) );
	
	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();
			
			//Suburb Tab
			//Call the Custom Meta
			$meta = get_post_custom();
			$suburb_postcode = $meta['suburb_postcode'][0];
			$suburb_coordinates = $meta['suburb_map_location'][0];
			$suburb_local_council = $meta['suburb_local_council'][0];
			$investors_are = $meta['suburb_investors_are'][0];
			$suburb_geo = $meta['suburb_map_location'][0];
			$suburb_people_property = $meta['suburb_people_property'][0];
			$suburb_people_property_image = $meta['suburb_people_property_image'][0];
			$suburb_location = $meta['suburb_location'][0];
			$suburb_amenities = $meta['suburb_amenities'][0];
			$suburb_recreation = $meta['suburb_recreation'][0];
			$suburb_transport = $meta['suburb_transport'][0];
			$suburb_video = $meta['suburb_video_url'][0]; ?>

			<div class="tab-wrapper">	
				<!-- Tab 8 Start -->
				<div class="tab-section">
					<h5 class="tab-title">Property Management Service</h5>
					<div class="tab-content">
						<p>What to expect from our <?php the_title(); ?> Property Management service-</p>
						<ul class="tick-list" style="margin-left:0;">
							<li>Local area knowledge combined with the industries best technology to lease your <?php the_title(); ?> property faster and keep great tenants paying a good market rate!</li>
							<li>Property Management expertise that you won't find from any other <?php the_title(); ?> real estate agent, who are mainly focussed on selling homes.</li>
							<li>An investor mindset focused on getting the best returns from your property because we are investors ourselves.</li>
							<li><?php the_title(); ?>'s most solid guarantees of service to give you complete peace of mind that your property is being looked after <a href="http://www.investorsedge.com.au/property-management/" title="Property Management Guarantees">read more about them here</a>.</li>
						</ul>
					</div>
				</div>
				<!-- Tab 8 End -->				
				
				<!-- Tab 1 Start -->
				<div class="tab-section">
					<h5 class="tab-title">Fast Facts</h5>
					<div class="tab-content">
						<ul>
							<li><?php the_title(); ?>'s Postcode is <?php echo $suburb_postcode; ?></li>
							<?php
								if ($suburb_local_council != '') {
									echo '<li>Local Council is ' , $suburb_local_council , '</li>';
								}
								
								if ($investors_are != '') {
									echo '<li>Investors are ' , $investors_are , '</li>';
								}
							?>
						</ul>
					</div>
				</div>
				<!-- Tab 1 End -->
					
				<!-- Tab 3 Start -->
				<?php if ($suburb_location != '') { ?>
					<div class="tab-section">
						<h5 class="tab-title">Location</h5>
						<div class="tab-content">
							<?php echo $suburb_location; ?>
						</div>
					</div>
				<?php } ?>
				<!-- Tab 3 End -->
					
				<!-- Tab 4 Start -->
				<?php if ($suburb_amenities != '') { ?>
					<div class="tab-section">
						<h5 class="tab-title">Amenities</h5>
						<div class="tab-content">
							<?php echo $suburb_amenities; ?>
						</div>
					</div>
				<?php } ?>
				<!-- Tab 4 End -->
					
				<!-- Tab 6 Start -->
				<?php if ($suburb_transport != '') { ?>
					<div class="tab-section">
						<h5 class="tab-title">Transport</h5>
						<div class="tab-content">
							<?php echo $suburb_transport; ?>
						</div>
					</div>
				<?php } ?>				
				<!-- Tab 6 End -->
			</div>
			<?php
		}
	}
	wp_reset_postdata();
}
