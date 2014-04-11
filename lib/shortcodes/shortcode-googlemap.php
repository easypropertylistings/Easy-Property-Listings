<?php
/*
 * SHORTCODE :: Simple Google Map
 */

function epl_shortcode_googlemap_callback($atts, $content = null) {
	extract( shortcode_atts( array(
		'width' => '640',
		'height' => '480',
		'src' => ''
	), $atts) );
	return '<iframe width="'.$width.'" height="'.$height.'" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="'.$src.'&amp;output=embed"></iframe>';
}
add_shortcode("epl-googlemap", "epl_shortcode_googlemap_callback");
