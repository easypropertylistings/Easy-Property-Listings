<?php
/*
 * Template for Property Map with GeoRSS input
 * 
 * @package        Builder
 * @subpackage     BuilderChild-EasyFirstSale
 * @since          1.0.0
 */

function epl_property_map_local_tabbed($coords, $post_type) {
	$coords_new = $coords; //Because same $coords variable is coming from property-mate.php so no conflict
	include 'property-meta.php';
	$coords = $coords_new;	
	$suburb = $property_address_suburb;
	
	// These are for the GEO RSS feed
	$suburb_comp = strtolower( $suburb );
	$suburb_comp = str_replace( " " , "-" , $suburb_comp ); ?>

	<script src="https://maps.googleapis.com/maps/api/js?sensor=false"></script>
	<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=true&libraries=places"></script>
	<script>
		// Globals
		var map1, map2, map3, map4, map5, map6, map7;
		var iconBase = '<?php echo plugins_url( 'images/', dirname(__FILE__) ); ?>';
		var iconShadow = {
			url: iconBase + 'icon-shadow.png',
			anchor: new google.maps.Point(16, 34)
		};


		<?php if ($post_type == 'epl_suburb') { ?>
			var contentString = '<div id="map-content">'+
				<?php if ( has_post_thumbnail() ) { ?>
					'<div class="map-entry-image">'+
					'<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'thumbnail' ); ?></a>'+
					'</div>'+
				<?php } else { ?>
					'<h6 id="firstHeading" class="map-heading" style="margin:0;"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h6>'+		
				<?php } ?>
			'</div>';
		<?php } else { ?>
			var contentString = '<div id="map-content">'+
				'<div class="map-entry-image">'+
					<?php if ( has_post_thumbnail() ) : ?>
						'<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'thumbnail' ); ?></a>'+
					<?php endif; ?>
				'</div>'+
				'<a href="<?php the_permalink(); ?>"><div id="firstHeading" class="sub-title" style="margin:0;"><?php echo $property_address_street ?></div>'+
				'<div class="subHeading"><?php echo $suburb; ?></div></a>'+
				'<div id="map-body-content">'+
				'<div><?php echo $price_plain_value; ?></div>'+
				'<div><?php echo $property_house_type; ?><?php if( $property_bedrooms > 1 ) { echo  ' | ' . $property_bedrooms , ' beds '; } ?><?php if($property_bathrooms > 1 ) { echo ' | ' . $property_bathrooms , ' baths'; } ?></div>'+
				'</div>'+
			'</div>';
		<?php } ?>

		function toggleMap(num, map) {
			for (var i = 1; i < 8; i++) {
				if (i != num)
					document.getElementById("map" + i).style.display = "none";
			}
			document.getElementById("map" + num).style.display = "block";
			document.getElementById("epl-map-report").innerHTML = "Map " + num;

			// Trigger 'resize' event of previously hidden map
			var center = map.getCenter();
			google.maps.event.trigger(map, "resize");
			map.setCenter(center); 
		}

		window.onload = function() {
			var g = google.maps;
			var infowindow = new google.maps.InfoWindow({
				content: contentString
			});
		
			// Local Map
			var localmap = new g.LatLng(<?php echo $coords; ?>);
			map1 = new g.Map(document.getElementById("map1"), {
				center: localmap,
				<?php
					// Suburb Profile Zoom Level
					if ('epl_suburb' == $post_type ) { ?>
						zoom: 14,
					<?php } else { ?>
						zoom: 19,
					<?php }
				?>
				mapTypeId: google.maps.MapTypeId.SATELLITE,
				streetViewControl: true,
				zoomControlOptions: {
					style: g.ZoomControlStyle.SMALL
				}
			});

			var image_for_sale = iconBase + 'icon-for-sale.png';
			var marker1 = new google.maps.Marker({
				position: localmap,
				map: map1,
				icon: image_for_sale,
				shadow: iconShadow,
				title: '<?php the_title(); ?>'
			});

			//infowindow.open(map1,marker1);
			google.maps.event.addListener(marker1, 'click', function() {
				infowindow.open(map1,marker1);
			}); 

			// Transit
			var transit = new g.LatLng(<?php echo $coords; ?>);
			map2 = new g.Map(document.getElementById("map2"), {
				center: transit,
				zoom: 14,
				mapTypeId: "roadmap",
				streetViewControl: false,
				zoomControlOptions: {
					style: g.ZoomControlStyle.SMALL
				}
			});
			var marker2 = new google.maps.Marker({
				position: transit,
				map: map2,
				icon: image_for_sale,
				title: '<?php the_title(); ?>'
			});
			var transitLayer = new google.maps.TransitLayer();
			transitLayer.setMap(map2);
			google.maps.event.addListener(marker2, 'click', function() {
				infowindow.open(map2,marker2);
			}); 

			// Schools
			var school = new g.LatLng(<?php echo $coords; ?>);
			map3 = new g.Map(document.getElementById("map3"), {
				mapTypeId: google.maps.MapTypeId.ROADMAP,
				center: school,
				zoom: 15
			});
			var request_sch = {
				location: school,
				radius: 5000,
				types: ['school']
			};
			infowindow_school = new google.maps.InfoWindow();
			var service = new google.maps.places.PlacesService(map3);
			service.nearbySearch(request_sch, callback);

			var marker3 = new google.maps.Marker({
				position: school,
				map: map3,
				icon: image_for_sale,
				title: '<?php the_title(); ?>'
			});

			function callback(results, status) {
				if (status == google.maps.places.PlacesServiceStatus.OK) {
					for (var i = 0; i < results.length; i++) {
						createMarker(results[i]);
					}
				}
			}

			function createMarker(place) {
				var placeLoc = place.geometry.location;
				var markerSchool = new google.maps.Marker({
					map: map3,
					icon: iconBase + 'icon-school.png',
					position: place.geometry.location
				});

				google.maps.event.addListener(markerSchool, 'click', function() {
					infowindow_school.setContent(place.name);
					infowindow_school.open(map3, this);
				});
			}

			// Google Place Amenities
			var gplace = new g.LatLng(<?php echo $coords; ?>);
			map4 = new g.Map(document.getElementById("map4"), {
				center: gplace,
				zoom: 16,
				mapTypeId: "roadmap",
				streetViewControl: false,
				zoomControlOptions: {
					style: g.ZoomControlStyle.SMALL
				}
			});

			var marker3 = new google.maps.Marker({
				position: gplace,
				map: map4,
				icon: image_for_sale,
				title: '<?php the_title(); ?>'
			});

			var request2 = {
				location: gplace,
				radius: 5000,
				types: ['shopping_mall'],
				reference: 'CnRkAAAAGnBVNFDeQoOQHzgdOpOqJNV7K9-c5IQrWFUYD9TNhUmz5-aHhfqyKH0zmAcUlkqVCrpaKcV8ZjGQKzB6GXxtzUYcP-muHafGsmW-1CwjTPBCmK43AZpAwW0FRtQDQADj3H2bzwwHVIXlQAiccm7r4xIQmjt_Oqm2FejWpBxLWs3L_RoUbharABi5FMnKnzmRL2TGju6UA4k'
			};

			var infowindow1 = new google.maps.InfoWindow();
			var service = new google.maps.places.PlacesService(map4);
			service.getDetails(request2, function(place, status) {
				if (status == google.maps.places.PlacesServiceStatus.OK) {
					var marker4 = new google.maps.Marker({
						map: map4,
						position: place.geometry.location
					});
					google.maps.event.addListener(marker4, 'click', function() {
						infowindow.setContent(place.name);
						infowindow.open(map4, marker4);
					});
				}
			});

			// Bike
			var Bike = new g.LatLng(<?php echo $coords; ?>);
			map5 = new g.Map(document.getElementById("map5"), {
				center: Bike,
				zoom: 13,
				mapTypeId: "roadmap",
				streetViewControl: false,
				zoomControlOptions: {
					style: g.ZoomControlStyle.SMALL
				}
			});

			var marker5 = new google.maps.Marker({
				position: Bike,
				map: map5,
				icon: image_for_sale,
				title: '<?php the_title(); ?>'
			});

			var bikeLayer = new google.maps.BicyclingLayer();
			bikeLayer.setMap(map5);
			google.maps.event.addListener(marker5, 'click', function() {
				infowindow.open(map5,marker5);
			}); 

			<?php
				if ( $post_type == 'epl_rental' || $post_type == 'epl_property' ) {
					$suburb_slug = $suburb_comp;

					if ( $post_type == 'epl_rental' ) {
						$post_type_slug = 'rental';
					}
				
					if ( $post_type == 'epl_property' ) {
						$post_type_slug = 'property';
					}
				
					if ( $post_type == 'epl_land' ) {
						$post_type_slug = 'land';
					}
				
					if ( $post_type == 'epl_commercial' ) {
						$post_type_slug = 'commercial';
					} ?>

					// comparable properry and rental geoRss feed layer
					var georssfeed = new g.LatLng(<?php echo $coords; ?>);
					map6 = new g.Map(document.getElementById("map6"), {
						zoom: 13,
						center: georssfeed,
						mapTypeId: google.maps.MapTypeId.ROADMAP
					});

					var marker6 = new google.maps.Marker({
						position: georssfeed,
						map: map6,
						title: "<?php the_title(); ?>"
					});

					var georssLayer = new google.maps.KmlLayer({
						preserveViewport: true,
						url: '<?php bloginfo( 'url' ); ?>/suburb/<?php echo $suburb_slug; ?>/feed/georss/?post_type=<?php echo $post_type; ?>'
					});	
					georssLayer.setMap(map6);

					google.maps.event.addListener(marker6, 'click', function() {
						infowindow.open(map6,marker6);
					});
					<?php
				}
			
				if ( $post_type == 'epl_suburb' ) {
					global $wp_post_types;
					$obj = $wp_post_types['epl_suburb'];
					$obj_type = $obj->rewrite;
					$obj_type = $obj_type['slug']; ?>
				
					// comparable properry and rental geoRss feed layer
					var georssfeed = new g.LatLng(<?php echo $coords; ?>);
					map6 = new g.Map(document.getElementById("map6"), {
						zoom: 13,
						center: georssfeed,
						mapTypeId: google.maps.MapTypeId.ROADMAP
					});

					var marker6 = new google.maps.Marker({
						position: georssfeed,
						map: map6,
						title: "<?php the_title(); ?>" 
					});

					var georssLayer = new google.maps.KmlLayer({
						url: '<?php bloginfo( 'url' ); ?>/<?php echo $obj_type; ?>/?feed=georss'
					});	
					georssLayer.setMap(map6);

					google.maps.event.addListener(marker6, 'click', function() {
						infowindow.open(map6,marker6);
					}); 
					<?php
				}
			?>
			// End GeoRSS Switch
		};

		// Trigger 'resize' event of all maps
		// when the browser window is resized
		window.onresize = function() {
			var allmaps = [map1, map2, map3, map4, map5, map6];
			for (var i = 0, m; m = allmaps[i]; i++) {
				var center = m.getCenter();
				google.maps.event.trigger(m, "resize");
				m.setCenter(center);
			}
		};
	</script>

	<!-- IMPORTANT Triggers Map resize on click-->
	<div id="epl-map-report">Map 1</div>
	<div id="epl-map-box" class="epl-map-box">
		<ul id="tabs" class="map-tabs">
			<li href="#" class="tbhead current" onclick="javascript:toggleMap(7, map7);return false;">Photo</li>
			<li href="#" class="tbhead" onclick="javascript:toggleMap(1, map1);return false;">Local Map</li>
			<li href="#" class="tbhead" onclick="javascript:toggleMap(2, map2);return false;">Transit</li>
			<li href="#" class="tbhead" onclick="javascript:toggleMap(3, map3);return false;">Schools</li>
			<li href="#" class="tbhead" onclick="javascript:toggleMap(4, map4);return false;">Amenities</li>
			<li href="#" class="tbhead" onclick="javascript:toggleMap(5, map5);return false;">Bike</li>
			<li href="#" class="tbhead" onclick="javascript:toggleMap(6, map6);return false;">Comparable</li>
		</ul>
		<div id="map-container">	
			<?php
				//Featured Image
				if ( has_post_thumbnail() ) : ?>
					<div id="map7" class="entry-image">
						<div class="it-featured-image">
							<a href="<?php the_permalink(); ?>">
								<?php the_post_thumbnail( 'index_thumbnail', array( 'class' => 'index-thumbnail' ) ); ?>
							</a>
						</div>
					</div>
					<?php
				endif;
			
				echo '<div id="map1"></div>';
				echo '<div id="map2"></div>';
				echo '<div id="map3"></div>';
				echo '<div id="map4"></div>';
				echo '<div id="map5"></div>';
				echo '<div id="map6"></div>';
			?>
		</div>
	</div>
	<?php
}
