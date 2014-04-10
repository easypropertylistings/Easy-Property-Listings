<?php
/**
 * Template for Property Map with GeoRSS input
 * 
 * @package        Builder
 * @subpackage     BuilderChild-EasyFirstSale
 * @since          1.0.0
 */

function epl_property_map_local($coords, $post_type) {
	// if(function_exists('fn_googleMaps')){ }// Dependency
	// $coords_geo = str_replace( ',', ' ', $coords[0] ); // String Replace
	//Store Current Post Meta ?>
	
	<script>
		function initialize() {
			var myLatlng = new google.maps.LatLng(<?php echo $coords; ?>);
			var mapOptions = {
				zoom: 16,
				center: myLatlng,
				mapTypeId: google.maps.MapTypeId.ROADMAP
			}
			var map = new google.maps.Map(document.getElementById('epl-map-canvas'), mapOptions);
			var contentString = '<div id="content">'+
				'<div id="siteNotice">'+
				'</div>'+
				'<h5 id="firstHeading" class="map-heading"><?php the_title(); ?></h5>'+
				'<div id="map-body-content">'+
				'Heritage Site.</p>'+
				'<p>Attribution: Uluru, <a href="http://en.wikipedia.org/w/index.php?title=Uluru&oldid=297882194">'+
				'http://en.wikipedia.org/w/index.php?title=Uluru</a> '+
				'(last visited June 22, 2009).</p>'+
				'</div>'+
			'</div>';

			var infowindow = new google.maps.InfoWindow({
				content: contentString
			});

			var marker = new google.maps.Marker({
				position: myLatlng,
				map: map,
				title: '<?php the_title(); ?>'
			}); 

			var georssLayer = new google.maps.KmlLayer({
				url: 'http://www.cloudconnected.com.au/?post_type=<?php echo $post_type; ?>&feed=georss',
				preserveViewport: true
			});

			google.maps.event.addListener(marker, 'click', function() {
				infowindow.open(map,marker);
			}); 
			georssLayer.setMap(map);
		}

		google.maps.event.addDomListener(window, 'load', initialize);
	</script>

	<div id="epl-map-canvas" width="100%" height="400px"></div>
	<?php
	// Testing
	print_r($coords);
	print_r($coords[0]);
	print_r($coords_geo);
}
