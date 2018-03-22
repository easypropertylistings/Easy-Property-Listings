<?php
/**
 * Hook for External Link Buttons on Property Templates
 *
 * @package     EPL
 * @subpackage  Hooks/EnergyCertificate
 * @copyright   Copyright (c) 2018, Merv Barrett
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

	$link = get_post_meta( get_the_ID() , 'property_energy_certificate' , true );

	if( !empty($link) ) { ?>
		<button type="button" class="epl-button epl-energy-certificate" onclick="window.open('<?php echo $link; ?>')">
			<?php
				$label = apply_filters( 'epl_button_label_energy_certificate' , __('Energy Certificate ', 'easy-property-listings') );
			?>
			<?php echo $label ?>
		</button> <?php

	}

}
add_action('epl_buttons_single_property', 'epl_button_energy_certificate');
