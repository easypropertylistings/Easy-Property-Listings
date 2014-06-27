<?php
/**
 * SHORTCODE :: Simple Google Map
 *
 * @package     EPL
 * @subpackage  Shotrcode/map
 * @copyright   Copyright (c) 2014, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

function epl_shortcode_googlemap_callback($atts, $content = null) {
	extract( shortcode_atts( array(
		'width' => '100%',
		'height' => '350',
		'zoom' => '12',
		'q' => ''
	), $atts) );
	
	if(!empty($q)) {
		return '<div class="epl-tab-section"><iframe width="'.$width.'" height="'.$height.'" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.com/?q='.$q.'&amp;output=embed&amp;&z='.$zoom.'"></iframe></div>';
	}
}
add_shortcode('listing_map', 'epl_shortcode_googlemap_callback');
