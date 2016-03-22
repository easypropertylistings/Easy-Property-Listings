<?php
$epl_settings = epl_settings();
$new_fields_defaults = array(
	'widget_label_property'			=> __('Property', 'easy-property-listings'),
	'widget_label_land'			=> __('Land', 'easy-property-listings'),
	'widget_label_rental'			=> __('Rental', 'easy-property-listings'),
	'widget_label_rural'			=> __('Rural', 'easy-property-listings'),
	'widget_label_business'			=> __('Business', 'easy-property-listings'),
	'widget_label_commercial'		=> __('Commercial', 'easy-property-listings'),
	'widget_label_commercial_land'		=> __('Commercial Land', 'easy-property-listings'),
	'epl_max_graph_sales_price'		=> 2000000,
	'epl_max_graph_rent_price'		=> 2000,
	'sticker_new_range'			=> 7,
	'label_bond'				=> __('Bond', 'easy-property-listings'),
	'label_new'				=> __('New', 'easy-property-listings')
);

foreach($new_fields_defaults as $key	=>	$value) {
	if(!isset($epl_settings[$key])) {
		// sure upgrade, fields are not set lets set them for very first time
		$epl_settings[$key] = $value;
	}
}
update_option( 'epl_settings', $epl_settings );
