<?php
$epl_settings = epl_settings();

$new_fields_defaults = array(
	'epl_default_country'			=> 'Australia',
	'epl_icons_svg_listings'		=> 'off',
	'epl_icons_svg_author'			=> 'off',
);

foreach($new_fields_defaults as $key	=>	$value) {
    if(!isset($epl_settings[$key])) {
        // sure upgrade, fields are not set lets set them for very first time
        $epl_settings[$key] = $value;
    }
}
update_option( 'epl_settings', $epl_settings );