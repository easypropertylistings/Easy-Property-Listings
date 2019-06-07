<?php
/**
 * Hook for Floor plan Buttons on Property Templates
 *
 * @package     EPL
 * @subpackage  Hooks/FloorPlan
 * @copyright   Copyright (c) 2019, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Outputs any floor plan links for virtual tours on the property templates
 *
 * When the hook epl_buttons_single_property is used and the property
 * has floor plans links they will be output on the template
 *
 * @since 1.0
 * @revised 3.3
 */
function epl_button_floor_plan() {

	$keys = array('property_floorplan','property_floorplan_2');

	foreach($keys as $key) {

		$link 		= get_post_meta( get_the_ID() , $key , true );
		$count 		= $key == 'property_floorplan' ? '': substr($key, -1);
		$default 	= __('Floor Plan ', 'easy-property-listings') . $count;
		$meta_label 	= get_post_meta( get_the_ID() , $key.'_label' , true );
		$meta_label 	= $meta_label == '' ? $default : $meta_label;

		if( !empty($link) ) { ?>

			<button type="button" class="epl-button epl-floor-plan epl-floor-plan-<?php echo $count; ?>" onclick="window.open('<?php echo $link; ?>')">
				<?php
					$filter_key = str_replace('property_','',$key);
					if( has_filter('epl_button_label_'.$filter_key) ) {
						$label = apply_filters('epl_button_label_floorplan_' . $filter_key , $meta_label );
					} else {
						$label = apply_filters( 'epl_button_label_floorplan' , $meta_label );
					}
				?>
				<?php echo $label ?>
			</button> <?php

		}
	}

}
add_action('epl_buttons_single_property', 'epl_button_floor_plan');
