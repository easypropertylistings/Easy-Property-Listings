<?php

// Query filter for property_address_suburb custom field sortable in posts listing
add_filter( 'request', 'epl_property_address_suburb_column_orderby' );
function epl_property_address_suburb_column_orderby( $vars ) {
	if ( isset( $vars['orderby'] ) && 'property_address_suburb' == $vars['orderby'] ) {
		$vars = array_merge( $vars, array(
			'meta_key' => 'property_address_suburb',
			'orderby' => 'meta_value'
		) );
	}

	return $vars;
}

// Add custom filters to post type posts listings
add_action( 'restrict_manage_posts', 'epl_custom_restrict_manage_posts' );
add_filter( 'parse_query', 'epl_admin_posts_filter' );

function epl_custom_restrict_manage_posts() {
	global $post_type;
	if($post_type == 'epl_property' || $post_type == 'epl_rental' || $post_type == 'epl_land' || $post_type == 'epl_commercial' || $post_type == 'epl_rural' || $post_type == 'epl_business' || $post_type == 'epl_holiday_rental' || $post_type == 'epl_commercial_land') {
		//Filter by property_status
		$fields = array(
			'current'	=>	'Current',
			'withdrawn'	=>	'Withdrawn',
			'offmarket'	=>	'Off Market'
		);
		
		if($post_type != 'epl_rental' && $post_type != 'epl_holiday_rental') {
			$fields['sold'] = 'Sold';
		}
		
		if($post_type == 'epl_rental' || $post_type == 'epl_holiday_rental' || $post_type == 'epl_commercial' || $post_type == 'epl_business' || $post_type == 'epl_commercial_land') {
			$fields['leased'] = 'Leased';
		}
		
		if(!empty($fields)) {
			echo '<select name="property_status">';
				echo '<option value="">'.__('Filter By Type', 'epl').'</option>';
				foreach($fields as $k=>$v) {
					$selected = ($_GET['property_status'] == $k ? 'selected="selected"' : '');
					echo '<option value="'.$k.'" '.$selected.'>'.__($v, 'epl').'</option>';
				}
			echo '</select>';
		}
		
		//Filter by Suburb
		echo '<input type="text" name="property_address_suburb" placeholder="Search Suburbs" value="'.stripslashes($_GET['property_address_suburb']).'" />';
	}
}

function epl_admin_posts_filter( $query ) {
	global $pagenow;
	if( is_admin() && $pagenow == 'edit.php' ) {
		$meta_query = $query->get('meta_query');
		
		if(isset($_GET['property_status']) && $_GET['property_status'] != '') {
			$meta_query[] = array(
				'key'       => 'property_status',
				'value'     => $_GET['property_status']
			);
		}
		
		if(isset($_GET['property_address_suburb']) && trim($_GET['property_address_suburb']) != '') {
			$meta_query[] = array(
				'key'       => 'property_address_suburb',
				'value'     => $_GET['property_address_suburb'],
				'compare'   => 'LIKE'
			);
		}
		
		if(!empty($meta_query)) {
			$query->set('meta_query', $meta_query);
		}
	}
}
