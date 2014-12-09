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
                 'Listings',
                 'epl_status_dashboard_widget_callback'
        );	
}
add_action( 'wp_dashboard_setup', 'example_add_dashboard_widgets' );

function epl_status_dashboard_widget_callback() {
	global $epl_settings;
	$activate_post_types = $epl_settings['activate_post_types'];?>
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
			return sprintf( _n( '1 Property', '%s Property', $count, 'epl' ), $count );
		break;
		case 'land':
			return sprintf( _n( '1 Land', '%s Land', $count, 'epl' ), $count );
		break;
		case 'rental':
			return sprintf( _n( '1 Rental', '%s Rental', $count, 'epl' ), $count );
		break;
		case 'rural':
			return sprintf( _n( '1 Rural', '%s Rural', $count, 'epl' ), $count );
		break;
		case 'commercial':
			return sprintf( _n( '1 Commercial', '%s Commercial', $count, 'epl' ), $count );
		break;
		case 'commercial_land':
			return sprintf( _n( '1 Commercial Land', '%s Commercial Land', $count, 'epl' ), $count );
		break;
		case 'business':
			return sprintf( _n( '1 Business', '%s Business', $count, 'epl' ), $count );
		break;
		default:
			return sprintf( _n( '1 '.$singular, '%s '.$singular, $count, 'epl' ), $count );
		break;
	}
	
}

function epl_get_post_count($type,$meta_key,$meta_value) {
	$args = array(
		'post_type'			=> $type,
		'posts_per_page'	=>	-1,
		'meta_query'		=> array(
			array(
				'key'		=> $meta_key,
				'value'		=> $meta_value,
			)
		)
	);
	$postslist = get_posts( $args );
	return count($postslist);
}

function epl_posts_highlights($type) {
	switch($type){

		case 'rental':
			$filters = array(
							'property_status'		=>	array('value'	=>	'current','string'	=>	__('Current','epl')),
							'property_status'		=>	array('value'	=>	'leased','string'	=>	__('Leased','epl')),
							'property_status'		=>	array('value'	=>	'withdrawn','string'=>	__('Withdrawn','epl')),
							'property_status'		=>	array('value'	=>	'offmarket','string'=>	__('Off Market','epl')),
						);
			foreach($filters as $filter_key 	=>	$filter_value){
				$count = epl_get_post_count($type,$filter_key,$filter_value['value']);
				if($count != 0){
					echo '<span>'.$count.' '.$filter_value['string'].' </span>';
				}
				
			}
		break;
		
		case 'commercial':
			$filters = array(
							'property_status'		=>	array('value'	=>	'current','string'	=>	__('Current','epl')),
							'property_authority'	=>	array('value'	=>	'auction','string'	=>	__('Auction','epl')), // ONLY if == current
							'property_under_offer'	=>	array('value'	=>	'yes','string'		=>	__('Under Offer','epl')),
							'property_status'		=>	array('value'	=>	'sold','string'		=>	__('Sold','epl')),
							'property_status'		=>	array('value'	=>	'leased','string'	=>	__('Leased','epl')),
							'property_status'		=>	array('value'	=>	'withdrawn','string'=>	__('Withdrawn','epl')),
							'property_status'		=>	array('value'	=>	'offmarket','string'=>	__('Off Market','epl')),
						);
			foreach($filters as $filter_key 	=>	$filter_value){
				$count = epl_get_post_count($type,$filter_key,$filter_value['value']);
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
							'property_status'		=>	array('value'	=>	'current','string'	=>	__('Current','epl')),
							'property_authority'	=>	array('value'	=>	'auction','string'	=>	__('Auction','epl')), // ONLY if == current
							'property_under_offer'	=>	array('value'	=>	'yes','string'		=>	__('Under Offer','epl')),
							'property_status'		=>	array('value'	=>	'sold','string'		=>	__('Sold','epl')),
							'property_status'		=>	array('value'	=>	'withdrawn','string'=>	__('Withdrawn','epl')),
							'property_status'		=>	array('value'	=>	'offmarket','string'=>	__('Off Market','epl')),
						);
			foreach($filters as $filter_key 	=>	$filter_value){
				$count = epl_get_post_count($type,$filter_key,$filter_value['value']);
				if($count != 0){
					echo '<span>'.$count.' '.$filter_value['string'].' </span>';
				}
				
			}
		break;
	}
}
