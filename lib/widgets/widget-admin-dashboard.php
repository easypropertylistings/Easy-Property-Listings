<?php
/**
 * DASHBOARD WIDGET :: Easy Property Listings Status
 *
 * @package     EPL
 * @subpackage  dashbaord Widget/status
 * @copyright   Copyright (c) 2014, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.3
 */

// Exit if accessed directly
function example_add_dashboard_widgets() {

	wp_add_dashboard_widget(
                 'epl_status_dashboard_widget',
                 __( 'Listings', 'epl'),
                 'epl_status_dashboard_widget_callback'
        );	
}
add_action( 'wp_dashboard_setup', 'example_add_dashboard_widgets' );

function epl_status_dashboard_widget_callback() {
	global $epl_settings;
	$activate_post_types = isset($epl_settings['activate_post_types'])?$epl_settings['activate_post_types'] : array();?>
	<div class="main">
		<ul class="epl_status_list">
		<?php
			if(!empty($activate_post_types)) {
				$counter = 0;
				foreach($activate_post_types as $activate_post_type){
					$clear = ($counter%2==0 && $counter!= 0)?'epl-clearfix':'';
					$count = wp_count_posts( $activate_post_type );?>
					<li class="epl_type_<?php echo $activate_post_type.' '.$clear; ?>">
						<a href="edit.php?post_type=<?php echo $activate_post_type; ?>">
							<strong><?php echo epl_get_plural($count->publish,$activate_post_type); ?></strong>
							<?php epl_posts_highlights($activate_post_type);?>
						</a>
						
					</li><?php
					$counter++;
				}
			}
		?>
		</ul>
	</div><?php
}

function epl_get_plural($count,$singular) {
	switch($singular){
		case 'property':
			return sprintf( _n( '1 '.__('Property','epl'), '%s '.__('Property','epl'), $count, 'epl' ), $count );
		break;
		case 'land':
			return sprintf( _n( '1 '.__('Land','epl'), '%s '.__('Land','epl'), $count, 'epl' ), $count );
		break;
		case 'rental':
			return sprintf( _n( '1 '.__('Rental','epl'), '%s '.__('Rental','epl'), $count, 'epl' ), $count );
		break;
		case 'rural':
			return sprintf( _n( '1 '.__('Rural','epl'), '%s '.__('Rural','epl'), $count, 'epl' ), $count );
		break;
		case 'commercial':
			return sprintf( _n( '1 '.__('Commercial','epl'), '%s '.__('Commercial','epl'), $count, 'epl' ), $count );
		break;
		case 'commercial_land':
			return sprintf( _n( '1 '.__('Commercial Land','epl'), '%s '.__('Commercial Land','epl'), $count, 'epl' ), $count );
		break;
		case 'business':
			return sprintf( _n( '1 '.__('Business','epl'), '%s '.__('Business','epl'), $count, 'epl' ), $count );
		break;
		default:
			$singular = ucwords( str_replace('-',' ',str_replace('_',' ',$singular) ) );
			return sprintf( _n( '1 '.__($singular,'epl'), '%s '.__($singular,'epl'), $count, 'epl' ), $count );
		break;
	}
	
}

function epl_get_post_count($type,$meta_key,$meta_value) {
	global $wpdb;
	$sql = "
		SELECT count( * ) AS count
		FROM {$wpdb->prefix}posts AS p
		INNER JOIN {$wpdb->prefix}postmeta AS pm
		WHERE p.post_type = '{$type}'
		AND p.ID = pm.post_id
		AND pm.meta_key = '{$meta_key}'
		AND pm.meta_value = '{$meta_value}'
	";
	$count = $wpdb->get_row($sql);
	return $count->count;
}

function epl_posts_highlights($type) {
	switch($type){

		case 'rental':
			$filters = array(
				array('key'	=>	'property_status','value'	=>	'current','string'	=>	__('Current','epl')),
				array('key'	=>	'property_status','value'	=>	'leased','string'	=>	apply_filters( 'epl_leased_label_status_filter' , __('Leased', 'epl') ) ),
				array('key'	=>	'property_status','value'	=>	'withdrawn','string'	=>	__('Withdrawn','epl')),
				array('key'	=>	'property_status','value'	=>	'offmarket','string'	=>	__('Off Market','epl')),
			);

			foreach($filters as $filter_key 	=>	$filter_value){
				$count = epl_get_post_count($type,$filter_value['key'],$filter_value['value']);
				if($count != 0){
					echo '<span>'.$count.' '.$filter_value['string'].' </span>';
				}
				
			}
		break;
		
		case 'commercial':
			$filters = array(
				array('key'	=>	'property_status','value'	=>	'current','string'	=>	__('Current','epl')),
				array('key'	=>	'property_authority','value'	=>	'auction','string'	=>	__('Auction','epl')), // ONLY if == current
				array('key'	=>	'property_under_offer','value'	=>	'yes','string'		=>	apply_filters( 'epl_under_offer_label_status_filter' , __('Under Offer', 'epl') ) ),
				array('key'	=>	'property_status','value'	=>	'sold','string'		=>	apply_filters( 'epl_sold_label_status_filter' , __('Sold', 'epl') ) ),
				array('key'	=>	'property_status','value'	=>	'leased','string'	=>	apply_filters( 'epl_leased_label_status_filter' , __('Leased', 'epl') ) ),
				array('key'	=>	'property_status','value'	=>	'withdrawn','string'	=>	__('Withdrawn','epl')),
				array('key'	=>	'property_status','value'	=>	'offmarket','string'	=>	__('Off Market','epl')),
			);
			foreach($filters as $filter_key 	=>	$filter_value){
				$count = epl_get_post_count($type,$filter_value['key'],$filter_value['value']);
				if($count != 0){
					echo '<span>'.$count.' '.$filter_value['string'].' </span>';
				}
				
			}
		break;
		
		case 'property':
		case 'land':
		case 'rural':
		default:
			$filters = array(
				array('key'	=>	'property_status','value'	=>	'current','string'	=>	__('Current','epl')),
				array('key'	=>	'property_authority','value'	=>	'auction','string'	=>	__('Auction','epl')), // ONLY if == current
				array('key'	=>	'property_under_offer','value'	=>	'yes','string'		=>	apply_filters( 'epl_under_offer_label_status_filter' , __('Under Offer', 'epl') ) ),
				array('key'	=>	'property_status','value'	=>	'sold','string'		=>	apply_filters( 'epl_sold_label_status_filter' , __('Sold', 'epl') ) ),
				array('key'	=>	'property_status','value'	=>	'withdrawn','string'	=>	__('Withdrawn','epl')),
				array('key'	=>	'property_status','value'	=>	'offmarket','string'	=>	__('Off Market','epl')),
			);
			foreach($filters as $filter_key 	=>	$filter_value){
				$count = epl_get_post_count($type,$filter_value['key'],$filter_value['value']);
				if($count != 0){
					echo '<span>'.$count.' '.$filter_value['string'].' </span>';
				}
				
			}
		break;
	}
}
