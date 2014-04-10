<?php
/*
 * Template for Supermap Schools
 * 
 * @package        WebConnected - Property
 * @subpackage     Template
 * @since          1.0.0
 */

function epl_supermap_shortcode_schools( $atts ) {
	extract( shortcode_atts( array(
		'post_type' => 'epl_rental',
		'coords' => '-31.9522, 115.8589',
	), $atts ) ); ?>
	
	<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=true&libraries=places"></script>
	<script>
		var map;
		var infowindow;

		function initialize() {
			var school = new google.maps.LatLng(<?php echo $coords; ?>);
			map = new google.maps.Map(document.getElementById('supermap-canvas'), {
				mapTypeId: google.maps.MapTypeId.ROADMAP,
				center: school,
				zoom: 15
			});

			var request = {
				location: school,
				radius: 5000,
				types: ['school']
			};

			infowindow = new google.maps.InfoWindow();
			var service = new google.maps.places.PlacesService(map);
			service.nearbySearch(request, callback);
		}

		function callback(results, status) {
			if (status == google.maps.places.PlacesServiceStatus.OK) {
				for (var i = 0; i < results.length; i++) {
					createMarker(results[i]);
				}
			}
		}

		function createMarker(place) {
			var placeLoc = place.geometry.location;
			var marker = new google.maps.Marker({
				map: map,
				position: place.geometry.location
			});
			google.maps.event.addListener(marker, 'click', function() {
				infowindow.setContent(place.name);
				infowindow.open(map, this);
			});
		}
		
		google.maps.event.addDomListener(window, 'load', initialize);
	</script>
	<div id="supermap-canvas"></div>
	<?php
}
add_shortcode( 'epl-supermap-schools' , 'epl_supermap_shortcode_schools' );
