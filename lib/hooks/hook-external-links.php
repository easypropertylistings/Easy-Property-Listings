<?php
/**
 * Hook for External Link Buttons on Property Templates
 *
 * @package     EPL
 * @subpackage  Hooks/ExternalLinks
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
 *
 * @since 1.0
 */
function epl_button_external_link() {

	$keys = array('property_external_link','property_external_link_2','property_external_link_3');

	foreach($keys as $key) {
		$link = get_post_meta( get_the_ID() , $key , true );

		$count = $key == 'property_external_link' ? '': substr($key, -1);

		if( !empty($link) ) { ?>
			<button type="button" class="epl-button epl-external-link" onclick="window.open('<?php echo $link; ?>')">
				<?php

					if( has_filter('epl_button_label_'.$key) ) {
						$label = apply_filters('epl_button_label_' . $key , __('Tour ', 'easy-property-listings') . $count );
					} else {
						$label = apply_filters( 'epl_button_label_tour' , __('Tour ', 'easy-property-listings') . $count );
					}
				?>
				<?php echo $label ?>
			</button> <?php

		}
	}

}
add_action('epl_buttons_single_property', 'epl_button_external_link');
