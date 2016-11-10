<?php
/**
 * Hook for Mini Website Links used with commercial and business listings
 *
 * @package     EPL
 * @subpackage  Hooks/WebLink
 * @copyright   Copyright (c) 2014, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Outputs any commercial or business mini web links
 *
 * When the hook epl_buttons_single_property is used and the commercial/business
 * property as a mini web links they will be output on the template
 *
 * @since       1.0
 */
function epl_button_mini_web() {
	$mini_web	= get_post_meta( get_the_ID() , 'property_com_mini_web' , true );
	$mini_web_2	= get_post_meta( get_the_ID() , 'property_com_mini_web_2' , true );
	$mini_web_3	= get_post_meta( get_the_ID() , 'property_com_mini_web_3' , true );

	$links = array();
	if(!empty($mini_web)) {
		$links[] = $mini_web;
	}
	if(!empty($mini_web_2)) {
		$links[] = $mini_web_2;
	}
	if(!empty($mini_web_3)) {
		$links[] = $mini_web_3;
	}

	if ( !empty($links) ) {
		foreach ( $links as $k=>$link ) {
			if(!empty($link)) {
				$number_string = '';
				if($k > 0) {
					$number_string = ' ' . $k + 1;
				}
				?><button type="button" class="epl-button epl-mini-web-link" onclick="window.open('<?php echo $link; ?>')"><?php echo apply_filters( 'epl_button_label_mini_web' , __('Mini Web ', 'easy-property-listings') ) . $number_string; ?></button><?php
			}
		}
	}
}
add_action('epl_buttons_single_property', 'epl_button_mini_web');
