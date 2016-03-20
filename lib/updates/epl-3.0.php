<?php
$epl_settings = epl_settings();

$new_fields_defaults = array(
	'inspection_date_format'		=> 'l, dS F',
	'inspection_time_format'		=> 'h:i a',
	'custom_inspection_date_format'		=> 'l, dS F',
	'custom_inspection_time_format'		=> 'h:i a',
	'epl_video_width'			=> 600,
	'min_contact_access'			=> 'level_10',
	'min_reports_access'			=> 'level_10',
);

foreach($new_fields_defaults as $key	=>	$value) {
    if(!isset($epl_settings[$key])) {
        // sure upgrade, fields are not set lets set them for very first time
        $epl_settings[$key] = $value;
    }
}
update_option( 'epl_settings', $epl_settings );
