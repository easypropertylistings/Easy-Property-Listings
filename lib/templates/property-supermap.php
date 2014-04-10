<?php
/*
 * Template for Supermap
 * 
 * @package        WebConnected - Property
 * @subpackage     Template
 * @since          1.0.0
 */

function epl_supermap_shortcode( $atts ) {
	extract( shortcode_atts( array(
		'post_type' => 'rental',
		'coords' => '-31.9522, 115.8589',
	), $atts ) ); ?>
	
	<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false"></script>
	<script>
		function initialize() {
			var myLatlng = new google.maps.LatLng(<?php echo $coords; ?>);
			var mapOptions = {
				zoom: 9,
				center: myLatlng,
				mapTypeId: google.maps.MapTypeId.ROADMAP
			}
			var map = new google.maps.Map(document.getElementById('supermap-canvas'), mapOptions);
			var georssLayer = new google.maps.KmlLayer({
				url: '<?php echo get_bloginfo('url'); ?>/<?php echo $post_type; ?>?feed=georss<?php echo $suburb; ?>'
			});
			georssLayer.setMap(map);
		}
		google.maps.event.addDomListener(window, 'load', initialize);
	</script>

	<div id="supermap-container">
		<div id="supermap-canvas"></div>
	</div>
	
	<?php
}
add_shortcode( 'epl-supermap' , 'epl_supermap_shortcode' );
