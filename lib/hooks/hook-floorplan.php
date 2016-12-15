<?php
/**
 * Hook for Floor plan Buttons on Property Templates
 *
 * @package     EPL
 * @subpackage  Hooks/FloorPlan
 * @copyright   Copyright (c) 2014, Merv Barrett
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
 */
function epl_button_floor_plan() {
	$floor_plan	= get_post_meta( get_the_ID() , 'property_floorplan' , true );
	$floor_plan_2	= get_post_meta( get_the_ID() , 'property_floorplan_2' , true );

	$links = array();
	if(!empty($floor_plan)) {
		$links[] = $floor_plan;
	}
	if(!empty($floor_plan_2)) {
		$links[] = $floor_plan_2;
	}

	if ( !empty($links) ) {
		foreach ( $links as $k=>$link ) {
			if(!empty($link)) {
				$number_string = '';
				if($k > 0) {
					$number_string = ' ' . $k + 1;
				}
				?><span class="epl-floor-plan-button-wrapper<?php echo $number_string; ?>">
				<a type="button" class="fancybox image epl-button epl-floor-plan" href="<?php echo $link; ?>"><?php echo apply_filters( 'epl_button_label_floorplan' , __('Floor Plan', 'easy-property-listings') ) . ' ' . $number_string; ?></a></span><?php
			}
		}
	}
}
add_action('epl_buttons_single_property', 'epl_button_floor_plan');
