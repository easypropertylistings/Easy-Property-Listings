<?php
/*
 * Template: Graphs
 */

// TEMPLATE - Single Sales Graph
function epl_property_graph() {
	$post_type = get_post_type();
	function epl_graph_property() {
		// Property Template - Graph
		$terms = get_the_terms( $post->ID, 'epl_tax_suburb' );
		if ( $terms != '' ){
			global $post;
			foreach($terms as $term){
				$term->slug;
			}
			$suburb_name = $term->name;
		}
				
		// The Query
		$exclude_comparable = array( get_the_ID() );
		$query = new WP_Query( array (
			'post_type' => 'epl_property',
			//'post__not_in' => $exclude_comparable,
			'epl_tax_suburb' => $term->slug,
			'posts_per_page' => '10'
			//'property_status' => 'sold'
		) );

		if ( $query->have_posts() ) { ?>
			<!-- Graph -->
			<div id="graph-container" class="graph-content clearfix">
				<!--<p class="graph-sub-title">For Sale and Sold Comparables</p>-->
				<div class="graphbox big green">
					<dl>
						<?php
							while ( $query->have_posts() ) {
								$query->the_post();
								// Define Options for each post element
								$meta = get_post_custom();
								// Property Data
								$garage = $meta['property_garage'][0];
								$carport = $meta['property_carport'][0];
								/// Move to top
								$street_address = $meta['property_address_street_number'][0] . ' ' . $meta['property_address_street'][0];
								$building_type = $meta['property_house_type'][0];
								$security = $meta['property_security_system'][0];
								$beds = 'Beds ' . $meta['property_bedrooms'][0];
								$baths = ' / Baths ' . $meta['property_bathrooms'][0];
								$pool = $meta['property_pool'][0];
								// Graph Settings
								$property_search_price = $meta['property_price'][0];
								$max_price = get_option('epl_max_graph_property_price');
								$asking_price = $meta['property_price_view'][0];
								$sale_price = $meta['property_sold_price'][0];
								
								if ( $sale_price == 0 && $property_search_price == '') {
								
								} elseif ( $sale_price != 0 ) {
									$graph_price_display = $sale_price;
									$graph_price = $graph_price_display;
									$english_format_number = number_format($graph_price);
								} else {
									$graph_price_display = $meta['property_price'][0];
									$graph_price = $graph_price_display;
									$english_format_number = number_format($graph_price);
								}
								//$graph_price = ereg_replace("[^0-9]", "", $graph_price_display[0]);

								$total_percent = "100";
								$percent_value = $graph_price / $max_price * $total_percent;
								// Format Sale Date
								$sale_date = $meta['property_sold_date'][0];
								if ( $sale_date != '') {
									$format = 'Y-m-d-H:i:s';									
									if(phpversion() >= 5.3) {
										$d = DateTime::createFromFormat( $format , $sale_date );
										$new_date = $d->format('d M Y');
									}
								} ?>
							
								<dt id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
									<?php
										// the_date(); 
										//echo get_post_meta( get_the_ID(), 'property_address_street_number', true );
										//echo get_post_meta( get_the_ID(), 'property_address_street', true );
									?>
								</dt>

								<dd id="graph-info">
									<?php
										// Property Status
										$under_offer = $meta['property_under_offer'][0];
										$status = $meta['property_status'][0];
							
										// Bar Colors
										if ( $exclude_comparable[0] == $post->ID ) { 
											$bar_color = 'pink_bar strong-bar';
										} elseif ($status == 'sold') {
											$bar_color = 'red_bar';
										} elseif ($under_offer == 1) {
											$bar_color = 'orange_bar';
										} else {
											$bar_color = 'green_bar';
										}
									?>
		
									<a href="<?php echo get_permalink(); ?>#tab-graph">
										<span class="data<?php echo round($percent_value), ' ' , $bar_color; ?>">
											<?php if ( $percent_value > 17 ) { echo '$' , $english_format_number; } ?>
										</span>
									</a>

									<div class="graph-info-tab">						 
										<?php the_post_thumbnail( 'thumbnail' , array( 'class' => 'info-tab-image' ) ); ?>
										<div class="info-tab-content">
											<div class="info-tab-meta">
												<div class="info-meta-street"><strong><?php echo $street_address; ?></strong></div>
												<?php echo $building_type; ?>
											</div>
											<div class="info-tab-meta">
												<div class="meta beds-baths">
													<?php
														if ( $beds != 0) { echo $beds; };
														if ( $baths != 0) { echo $baths; };
													?>
												</div>
												<div class="meta features">
													<?php
														if (1 == $pool) { echo 'Pool '; }
														if (1 == $security ) { echo 'Alarm '; }
														if (0 != $garage ) { echo $garage , ' Garage '; }
														if (0 != $carport ) { echo $carport , ' Carport '; } 
													?>
												</div>
											</div>
											<div class="info-tab-meta">
												<?php
													// Status 
													if ( $status == 'sold' ) { 
														echo '<div class="meta-title property-sold">Sold</div>';
														setlocale(LC_MONETARY, 'en_US');
														echo '<div class="meta-value">' . money_format('%(.0n', $sale_price) . '</div>';
														echo '<div class="meta-value">' . $new_date . '</div>'; 
													}
													elseif ($under_offer == 1) { 
														echo '<div class="meta-title property-under-offer">Under Offer</div>';
														echo '<div class="meta-title">Was Asking</div>';
														echo '<div class="meta-value">' . $asking_price . '</div>';
													}
													else {
														echo '<div class="meta-title">Asking</div>';
														echo '<div class="meta-value">' . $asking_price . '</div>';
													}
												?>
											</div>
										</div>	
									</div>
								</dd>
								<?php
							}
						?>
					</dl>
				</div>
			</div>
			
			<?php
		}
		wp_reset_postdata();
		// END Property Template
	}
		
	function epl_graph_rental() {
		// Graph Rental Template
		$terms = get_the_terms( $post->ID, 'epl_tax_suburb' );
		if ( $terms != '' ){
			global $post;
			foreach($terms as $term){
				$term->slug;
			}
			$suburb_name = $term->name;
		}

		// The Query
		$exclude_comparable = array( get_the_ID() );
		$query = new WP_Query( array (
			'post_type' => 'epl_rental',
			//'post__not_in' => $exclude_comparable,
			'epl_tax_suburb' => $term->slug,
			'posts_per_page' => '10'
			//'property_status' => 'sold'
		) );
		
		if ( $query->have_posts() ) { ?>
			<!-- Graph -->
			<div id="graph-container" class="graph-content clearfix">
				<p class="graph-sub-title">For Rent and Leased Comparables</p>
				<div class="graphbox big green">
					<dl>
						<?php
							while ( $query->have_posts() ) {
								$query->the_post();
								// Define Options for each post element
								$meta = get_post_custom();
								// Property Data
								$garage = $meta['property_garage'][0];
								$carport = $meta['property_carport'][0];
								/// Move to top
								$street_address = $meta['property_address_street_number'][0] . ' ' . $meta['property_address_street'][0];
								$building_type = $meta['property_house_type'][0];
								$security = $meta['property_security_system'][0];
								$beds = 'Beds ' . $meta['property_bedrooms'][0];
								$baths = ' / Baths ' . $meta['property_bathrooms'][0];
								// Graph Settings
								$max_price = get_option('epl_max_graph_rental_price');
								$asking_price = $meta['property_rent'][0];
								$graph_price_display = $asking_price;
								//$graph_price = ereg_replace("[^0-9]", "", $graph_price_display[0]);
								$graph_price = $graph_price_display;
								$english_format_number = number_format($graph_price);
								$total_percent = "100";
								$percent_value = $graph_price / $max_price * $total_percent; ?>
							
								<dt id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
									<?php
										// the_date(); 
										//echo get_post_meta( get_the_ID(), 'property_address_street_number', true );
										//echo get_post_meta( get_the_ID(), 'property_address_street', true );
									?>
								</dt>
							
								<dd id="graph-info">
									<?php
										// Rental Status
										$status = $meta['property_status'][0];
							
										// Bar Colors
										if ( $exclude_comparable[0] == $post->ID ) { 
											$bar_color = 'pink_bar strong-bar';
										} elseif ($status == 'leased') {
											$bar_color = 'red_bar';
										} else {
											$bar_color = 'green_bar';
										}
									?>
								
									<a href="<?php echo get_permalink(); ?>">
										<span class="data<?php echo round($percent_value), ' ' , $bar_color; ?>">
											<?php if ( $percent_value > 10 ) { echo '$' , $english_format_number; } ?>
										</span>
									</a>

									<div class="graph-info-tab">						 
										<?php the_post_thumbnail( 'thumbnail' , array( 'class' => 'info-tab-image' ) ); ?>
										<div class="info-tab-content">
											<div class="info-tab-meta">
												<div class="meta street"><strong><?php echo $street_address; ?></strong></div>
												<div class="meta building"><?php echo $building_type; ?></div>
											</div>
											<div class="info-tab-meta">
												<div class="meta beds-baths">
													<?php
														if ( $beds != 0) { echo $beds; };
														if ( $baths != 0) { echo $baths; };
													?>
												</div>
												<div class="meta features">
													<?php
														if (1 == $pool) { echo 'Pool '; }
														if (1 == $security ) { echo 'Alarm '; }
														if (0 != $garage ) { echo $garage , ' Garage '; }
														if (0 != $carport ) { echo $carport , ' Carport '; } 
													?>
												</div>
											</div>
											<div class="info-tab-meta">
												<?php // Status 
												if ($status == 'leased') {
													echo '<div class="meta-title property-leased">Leased</div>';
													echo '<div class="meta-title">Rent Was</div>';
													setlocale(LC_MONETARY, 'en_US');
													echo '<div class="meta-value">' . money_format('%(.0n', $asking_price) . '</div>';
												}
												else { 
													echo '<div class="meta-title">Asking Rent</div>';
													setlocale(LC_MONETARY, 'en_US');
													echo '<div class="meta-value">' . money_format('%(.0n', $asking_price) . '</div>';
												}
												?>
											</div>
										</div>
									</div>
								</dd>
								<?php
							}
						?>
					</dl>
				</div>
			</div>
			<?php
		}
		wp_reset_postdata();
		// END Rental Template
		////}
	}// END RENTAL GRAPH FUNCTION
	
	if ($post_type == 'epl_suburb') {
		echo epl_graph_property();
		echo epl_graph_rental();
	} elseif ($post_type == 'epl_property') {
		echo epl_graph_property();
	} elseif ($post_type == 'epl_rental') {
		echo epl_graph_rental();
	}
}
