<?php
/**
 * Hook for External Link Buttons on Property Templates
 *
 * @package     EPL
 * @subpackage  Hooks/EnergyCertificate
 * @copyright   Copyright (c) 2019, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.2
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Outputs energy certificate button on the property templates
 *
 * When the hook epl_buttons_single_property is used and the property
 * has an energy certificate they will be output on the template
 *
 * @since 3.2
 */
function epl_button_energy_certificate() {

	$keys = array( 'property_energy_certificate' );

	foreach($keys as $key) {
		$link 		= get_post_meta( get_the_ID() , $key , true );
		$count 		= $key == 'property_energy_certificate' ? '': substr($key, -1);
		$default 	= __('Energy Certificate ', 'easy-property-listings') . $count;
		$meta_label 	= get_post_meta( get_the_ID() , $key.'_label' , true );
		$meta_label 	= $meta_label == '' ? $default : $meta_label;

		if( !empty($link) ) { ?>
			<button type="button" class="epl-button epl-energy-certificate" onclick="window.open('<?php echo $link; ?>')">
				<?php
					if( has_filter('epl_button_label_'.$key ) ) {
						$label = apply_filters('epl_button_label_' . $key , $meta_label );
					} else {
						$label = apply_filters( 'epl_button_label_energy_certificate' , $meta_label );
					}
				?>
				<?php echo $label ?>
			</button> <?php

		}
	}

}
add_action('epl_buttons_single_property', 'epl_button_energy_certificate');
