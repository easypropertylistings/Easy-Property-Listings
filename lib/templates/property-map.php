<?php
/*
 * Template for Property Map
 * 
 * @package        Builder
 * @subpackage     BuilderChild-EasyFirstSale
 * @since          1.0.0
 */

function epl_property_map() {
	if(function_exists('fn_googleMaps')){ ?>
		<div class="suburb-map">
			<?php
				$address_coordinates = get_post_custom_values("address_coordinates");
				$address = get_post_custom_values("address");
				//echo $suburb_coordinates[0];
				//echo $address
				echo do_shortcode('[googlemap width="100%" height="400" src="http://maps.google.com.au/maps?q=' . $address[0] . '&sll=' . $address_coordinates[0] . '&hl=en&gl=au&t=m&z=18"]');
			?>
		</div>	
		<?php
	} else if ( current_user_can('publish_posts') ) {
		echo '<strong>The WebConnected Property Plugin is disabled and the Google Map is not showing</strong>';
	}
} 
