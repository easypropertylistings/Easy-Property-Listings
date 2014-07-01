<?php
/**
 * Hook for External Link Buttons on Property Templates
 *
 * @package     EPL
 * @subpackage  epl_buttons_single_property
 * @copyright   Copyright (c) 2014, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Outputs any external links for virtual tours on the property templates
 *
 * When the hook epl_buttons_single_property is used and the property 
 * has external links they will be output on the template
 */
function epl_button_external_link() {
	$external_link		= get_post_meta( get_the_ID() , 'property_external_link' , true );
	$external_link_2	= get_post_meta( get_the_ID() , 'property_external_link_2' , true );
	$external_link_3	= get_post_meta( get_the_ID() , 'property_external_link_3' , true );
	
	$links = array();
	if(!empty($external_link)) {
		$links[] = $external_link;
	}
	if(!empty($external_link_2)) {
		$links[] = $external_link_2;
	}
	if(!empty($external_link_3)) {
		$links[] = $external_link_3;
	}
	
	if ( !empty($links) ) {
		foreach ( $links as $k=>$link ) {
			if(!empty($link)) {
				$number_string = '';
				if($k > 0) {
					$number_string = ' ' . $k + 1;
				}
				?><button type="button" class="epl-button epl-external-link" onclick="location.href='<?php echo $link; ?>'"><?php echo __('Tour ', 'epl') . $number_string; ?></button><?php
			}
		}
	}
}
add_action('epl_buttons_single_property', 'epl_button_external_link');
