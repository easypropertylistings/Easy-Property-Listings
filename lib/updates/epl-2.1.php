<?php
$epl_settings = epl_settings();

$new_fields_defaults = array(
	'label_leased'				=>	__('Leased','easy-property-listings'),
	'label_under_offer'			=>	__('Under Offer','easy-property-listings'),
);

foreach($new_fields_defaults as $key	=>	$value) {
	if(!isset($epl_settings[$key])) {
		// sure upgrade, fields are not set lets set them for very first time
		$epl_settings[$key] = $value;
	}
}
update_option( 'epl_settings', $epl_settings );
