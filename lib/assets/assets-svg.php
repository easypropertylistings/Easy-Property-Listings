<?php
/**
 * Scripts & Styles
 *
 * @package     EPL
 * @subpackage  Assets/SVG
 * @copyright   Copyright (c) 2014, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.2
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * SVG Listing Icons Loaded in Head
 *
 * @since       3.2
 */
function epl_load_svg_listing_icons_head() {

	$svg_icons = '

	<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" class="hide" style="display:none">
		<defs>
			<symbol id="epl-icon-svg-bed" class="epl-icon-fill">
				<g id="epl-icon-shape-container" class="epl-icon-shape-container">
					<rect class="epl-icon-shape-part-container" width="100" height="100" style="fill:none"/>
				</g>
				<g id="epl-icon-shape-frame" class="epl-icon-shape-frame epl-icon-color-frame">
					<rect class="epl-icon-shape-part-frame" x="19.18" y="49.4" width="70.2" height="6.81" rx="1.98" ry="1.98"/>
					<rect class="epl-icon-shape-part-frame" x="10.94" y="33.62" width="6.62" height="35.56" rx="1.65" ry="1.65"/>
					<path class="epl-icon-shape-part-frame" d="M78.51,57.73V67.81a1.65,1.65,0,0,0,1.65,1.65h3.32a1.65,1.65,0,0,0,1.65-1.65V57.73Z" transform="translate(0 -0.29)"/>
				</g>
				<g id="epl-icon-shape-pillow" class="epl-icon-shape-pillow epl-icon-color-base">
					<path class="epl-icon-shape-part-pillow" d="M42.61,49.45a1.65,1.65,0,0,1-2.1,1L20.06,43.29a1.65,1.65,0,0,1-1-2.1l1.54-4.38a1.65,1.65,0,0,1,2.11-1L43.13,43a1.65,1.65,0,0,1,1,2.1Z" transform="translate(0 -0.29)"/>
				</g>
				<g id="epl-icon-shape-matress" class="epl-icon-shape-matress epl-icon-color-alternate">
					<rect class="epl-icon-shape-part-matress" x="50.89" y="42.84" width="36.31" height="8.7" rx="1.65" ry="1.65"/>
				</g>
			</symbol>

			<symbol id="epl-icon-svg-bath" class="epl-icon-fill">
				<g id="epl-icon-shape-container" class="epl-icon-shape-container">
					<rect class="epl-icon-shape-part-container" width="100" height="100" style="fill:none"/>
				</g>
				<g id="epl-icon-shape-frame" class="epl-icon-shape-frame epl-icon-color-frame">
					<rect class="epl-icon-shape-part-frame" x="5.41" y="41.13" width="86.83" height="5.51" rx="2.75" ry="2.75"/>
				</g>
				<g id="epl-icon-shape-base" class="epl-icon-shape-base epl-icon-color-base">
					<path class="epl-icon-shape-part-base" d="M13.25,48.84S16.9,70.64,35,70.64H64.49c18.14,0,21.79-21.79,21.79-21.79Zm4.39,4.26h2.93c.48,2.26,3,11.8,10.74,15.3C20,67.08,17.64,53.1,17.64,53.1Z" transform="translate(0.02)"/>
				</g>
				<g id="epl-icon-shape-feet" class="epl-icon-shape-feet epl-icon-color-base">
					<path class="epl-icon-shape-part-feet" d="M33.2,72.47A18.88,18.88,0,0,1,23.62,70L21.33,75.5s-1.9,3.8,4.42.9l7.77-3.93Z" transform="translate(0.02)"/>
					<path class="epl-icon-shape-part-feet" d="M66.34,72.47A18.88,18.88,0,0,0,75.92,70l2.29,5.49s1.9,3.8-4.42.9L66,72.47Z" transform="translate(0.02)"/>
				</g>
				<g id="epl-icon-shape-tap" class="epl-icon-shape-tap epl-icon-color-base">
					<path class="epl-icon-shape-part-tap epl-icon-shape-part-base" d="M11.06,37.9h5.15s.33-6.39,4.07-6.39c0,0,2,0,3.32,2.81l1.87-1.87s-2.61-2.85-5-2.85S11.6,35.19,11.06,37.9Z" transform="translate(0.02)"/>
				</g>
			</symbol>

			<symbol id="epl-icon-svg-car" class="epl-icon-fill">
				<g id="epl-icon-shape-container" class="epl-icon-shape-container">
					<rect class="epl-icon-shape-part-container" width="100" height="100" style="fill:none"/>
				</g>
				<g id="epl-icon-shape-base" class="epl-icon-shape-car epl-icon-color-base">
					<path class="epl-icon-shape-part-car" d="M70.18,68.94v4.5c0,1.24.54,2.4,2.09,2.4h9.31c1.71,0,2-.93,2.09-2.95.08-2.25,0-16.45,0-18.93,0-3.34,0-4.34-.54-5.35-.93-1.71-2.48-5.9-2.48-5.9a19.83,19.83,0,0,0,3.1-1.16c1.63-.78,1.78-1.24,1.09-2.95s-1.32-2.79-2.56-2.95-4.34-.39-7.52-.62C71.11,29.77,67.31,25,66.23,25H34.58c-1,0-4.89,4.81-8.46,10.08-3.18.16-6.21.39-7.52.62S16.74,36.9,16,38.61s-.62,2.17,1,2.95a19.83,19.83,0,0,0,3.1,1.16s-1.94,4.11-2.79,5.9c-.54,1.09-.54,1.09-.54,3.1,0,3.18-.08,18.85,0,21.1.08,2,.47,2.95,2.09,2.95h9.23c1.55,0,2.09-1.09,2.09-2.4v-4.5H70.18ZM34.58,56.84s-8.92-.39-11.4-.62c-3.1-.31-2.09-5.12-2.09-6.52,0-.08,7.83.78,10.32,1.16s3.1,1.86,3.1,2.4S34.58,56.22,34.58,56.84ZM64,30.39c.62,0,1.63-.23,5.2,4.81,2.87,4,4,7.68,3.1,7.68h-44c-.93,0,.62-4.11,3.1-7.68,3.65-5.12,4.58-4.81,5.2-4.81ZM77.48,56.3c-2.48.23-11.4.62-11.4.62V53.35c0-.54.54-1.86,3.1-2.4s10.39-1.32,10.39-1.16C79.57,51.1,80.58,56,77.48,56.3Z"/>
				</g>
			</symbol>

			<symbol id="epl-icon-svg-air" class="epl-icon-fill">
				<g id="epl-icon-shape-container" class="epl-icon-shape-container">
					<rect class="epl-icon-shape-part-container" width="100" height="100" style="fill:none"/>
				</g>
				<g id="epl-icon-shape-frame" class="epl-icon-shape-frame epl-icon-color-frame">
					<path class="epl-icon-shape-part-frame" d="M50.3,22.9A22.3,22.3,0,1,0,72.6,45.2h0A22.32,22.32,0,0,0,50.3,22.9Zm0,42A19.7,19.7,0,1,1,70,45.2h0A19.67,19.67,0,0,1,50.3,64.9Z"/>
				</g>
				<g id="epl-icon-shape-base" class="epl-icon-shape-base epl-icon-color-alternate">
					<path class="epl-icon-shape-part-base" d="M45.8,70.5c-.5,2.2-2.7,3.2-4.7,4.5-2.3,1.4-4.4,1.9-4.4,4.6v1.7H64V79.6c0-2.6-2.1-3.2-4.4-4.6-2-1.3-4.2-2.3-4.7-4.5a27.78,27.78,0,0,1-9.1,0Z"/>
				</g>
				<g id="epl-icon-shape-fan" class="epl-icon-shape-fan epl-icon-color-base">
					<path class="epl-icon-shape-part-fan" d="M52.6,40.5c1.2-3.6,1.3-11.4-7-11.4-4.4,0-7.3,5.9-2.2,9,1.4.8,2.7,1.4,3.4,3.2a5.15,5.15,0,0,1,5.8-.8Z"/>
					<path class="epl-icon-shape-part-fan" d="M45.1,45.3c-3.7,1-9.9,4.9-5.7,11.8,2.3,3.8,8.8,3.2,8.8-2.8,0-1.4-.2-2.8.7-4.1A5.13,5.13,0,0,1,45.1,45.3Z"/>
					<path class="epl-icon-shape-part-fan" d="M59.6,41.9c-1.4.8-2.5,1.7-4.4,1.5a5.29,5.29,0,0,1-1.6,5.9c2.8,2.7,9.3,5.8,13-1.3C68.6,44.1,64.7,38.8,59.6,41.9Z"/>
				</g>
				<g id="epl-icon-shape-circle" class="epl-icon-shape-circle epl-icon-color-highlight">
					<path class="epl-icon-shape-part-circle" d="M50.3,42.3a2.9,2.9,0,1,0,2.9,2.9h0A2.9,2.9,0,0,0,50.3,42.3Z"/>
				</g>
			</symbol>

			<symbol id="epl-icon-svg-pool" class="epl-icon-fill">
				<g id="epl-icon-shape-container" class="epl-icon-shape-container">
					<rect class="epl-icon-shape-part-container" width="100" height="100" style="fill:none"/>
				</g>
				<g id="epl-icon-shape-frame" class="epl-icon-shape-frame epl-icon-color-frame">
					<path class="epl-icon-shape-part-frame" d="M67.35,25.19a7.25,7.25,0,0,0-5.42,2.17c-2.83,3-2.73,7.71-2.71,8.24v5.52h-15v-5.8c0-1.19.27-3.82,1.6-5.18a3.15,3.15,0,0,1,2.39-.92c.58.06,3.81.66,3.81,6.31a2,2,0,0,0,4,0c0-7.9-5-10.22-7.7-10.35a7.25,7.25,0,0,0-5.42,2.17c-2.83,3-2.73,7.71-2.71,8.24v38a2,2,0,1,0,4,0v-3h15v3a2,2,0,1,0,4,0V35.33c0-1.19.27-3.82,1.6-5.18a3.14,3.14,0,0,1,2.39-.92c.58.06,3.81.66,3.81,6.31a2,2,0,1,0,4,0C75,27.62,70,25.31,67.35,25.19ZM59.22,44.66v5.18h-15V44.66Zm0,8.67V58.5h-15V53.33Zm-15,13.81V62h15v5.18Z"/>
				</g>
				<g id="epl-icon-shape-water" class="epl-icon-shape-water epl-icon-color-base">
					<path class="epl-icon-shape-part-water" d="M80.94,55.31a.95.95,0,1,0,1-1.59c-5.3-3.5-11.66-3.75-15.65-.82v2.49l.26-.24C69.28,52.44,75.24,51.56,80.94,55.31ZM27,56.61c-5.82.18-7.3-2.45-7.3-2.56a1,1,0,0,0-1.27-.41A.94.94,0,0,0,18,54.89c.08.16,1.94,3.79,9.12,3.58a13.38,13.38,0,0,0,7.6-3.32,7.79,7.79,0,0,1,3.09-1.82v-2a9.84,9.84,0,0,0-4.44,2.51C31,56.21,27,56.58,27,56.58Zm0,6.12c-5.82.14-7.3-2.45-7.3-2.56a1,1,0,0,0-1.28-.44h0A.94.94,0,0,0,18,61h0c.08.16,1.94,3.79,9.12,3.58a13.38,13.38,0,0,0,7.6-3.32,7.81,7.81,0,0,1,3.09-1.82v-2a9.86,9.86,0,0,0-4.44,2.51C31,62.34,27,62.7,27,62.7ZM82,66c-5.3-3.5-11.66-3.74-15.65-.81v2.49l.26-.24c2.71-2.71,8.67-3.59,14.37.13a.95.95,0,0,0,1-1.59Zm0-6.12c-5.3-3.5-11.66-3.75-15.65-.84V61.5l.26-.24c2.71-2.71,8.67-3.59,14.37.13a.95.95,0,0,0,1-1.59ZM27,68.86c-5.82.14-7.3-2.46-7.3-2.57a1,1,0,0,0-1.27-.41A.94.94,0,0,0,18,67.14h0c.08.16,1.94,3.79,9.12,3.58a13.38,13.38,0,0,0,7.6-3.32,7.77,7.77,0,0,1,3.09-1.82v-2a9.86,9.86,0,0,0-4.44,2.51C31,68.46,27,68.83,27,68.83Z"/>
				</g>
			</symbol>

		</defs>
	</svg>';

	apply_filters( 'epl_svg_icons' , $svg_icons );

	// Only Load SVG icons if epl_icons_svg_listings is on
	if ( epl_get_option('epl_icons_svg_listings') == 'on' ) {
		echo $svg_icons;
	}

}
add_action('wp_head', 'epl_load_svg_listing_icons_head' , 90 );

/**
 * SVG Social Media Icons Loaded in Head
 *
 * @since       3.2
 */
function epl_load_svg_social_icons_head() {

	$social_icons = '

	<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" class="hide" style="display:none">
		<defs>

			<symbol id="epl-icon-svg-email" class="epl-icon-fill">
				<g id="epl-icon-shape-container" class="epl-icon-shape-container">
					<rect width="100" height="100" style="fill:none"/>
				</g>
				<g id="epl-icon-shape-base" class="epl-icon-shape-base epl-icon-color-base">
					<path class="epl-icon-shape-part-base" id="Email" d="M83.36,17.33H16.88A10.77,10.77,0,0,0,6.13,28.09V73.52A10.77,10.77,0,0,0,16.88,84.28H83.34A10.77,10.77,0,0,0,94.1,73.52V28.11A10.76,10.76,0,0,0,83.36,17.33ZM89.2,73.52a5.85,5.85,0,0,1-5.84,5.84H16.88A5.85,5.85,0,0,1,11,73.52V28.11a5.85,5.85,0,0,1,5.84-5.84H83.34a5.85,5.85,0,0,1,5.84,5.84V73.52Z" transform="translate(0 -0.29)"/>
					<path class="epl-icon-shape-part-highlight" d="M61.61,50.24,83.12,30.95a2.46,2.46,0,1,0-3.29-3.66L50.16,53.92l-5.79-5.17s0,0,0-.05a3.64,3.64,0,0,0-.4-.35L20.38,27.27a2.46,2.46,0,0,0-3.28,3.68L38.87,50.41,17.19,70.7a2.47,2.47,0,0,0-.11,3.48,2.51,2.51,0,0,0,1.8.78,2.46,2.46,0,0,0,1.67-.66l22-20.59,6,5.33a2.45,2.45,0,0,0,3.28,0l6.13-5.5L79.83,74.32a2.46,2.46,0,0,0,3.48-.09,2.47,2.47,0,0,0-.09-3.48Z" transform="translate(0 -0.29)"/>
				</g>
			</symbol>

			<symbol id="epl-icon-svg-facebook" class="epl-icon-fill">
				<g id="epl-icon-shape-container" class="epl-icon-shape-container">
					<rect width="100" height="100" style="fill:none"/>
				</g>
				<g id="epl-icon-shape-base" class="epl-icon-shape-base epl-icon-color-base">
					<path class="epl-icon-shape-part-base" id="Facebook" data-name="Facebook" d="M95,20.29c0-7.88-7.12-15-15-15H20c-7.88,0-15,7.12-15,15v60c0,7.88,7.12,15,15,15H50v-34H39v-15H50V40.44c0-10.08,7.57-19.16,16.88-19.16H79v15H66.88c-1.33,0-2.87,1.61-2.87,4v6H79v15H64v34H80c7.88,0,15-7.12,15-15Z" transform="translate(0 -0.29)"/>
				</g>
			</symbol>

			<symbol id="epl-icon-svg-twitter" class="epl-icon-fill">
				<g id="epl-icon-shape-container" class="epl-icon-shape-container">
					<rect width="100" height="100" style="fill:none"/>
				</g>
				<g id="epl-icon-shape-base" class="epl-icon-shape-base epl-icon-color-base">
					<path class="epl-icon-shape-part-base" id="Twitter" data-name="Twitter" d="M95,22.38a36.87,36.87,0,0,1-10.6,2.91,18.54,18.54,0,0,0,8.12-10.21,37.22,37.22,0,0,1-11.73,4.48A18.48,18.48,0,0,0,49.32,36.4,52.42,52.42,0,0,1,11.27,17.11,18.48,18.48,0,0,0,17,41.75a18.46,18.46,0,0,1-8.36-2.32v.23a18.48,18.48,0,0,0,14.81,18.1,18.7,18.7,0,0,1-4.86.65,17.72,17.72,0,0,1-3.48-.34A18.47,18.47,0,0,0,32.33,70.9,37,37,0,0,1,9.4,78.79,39.24,39.24,0,0,1,5,78.53a52.19,52.19,0,0,0,28.3,8.31c34,0,52.53-28.13,52.53-52.53l-.06-2.39A36.87,36.87,0,0,0,95,22.38Z" transform="translate(0 -0.29)"/>
				</g>
			</symbol>

			<symbol id="epl-icon-svg-linkedin" class="epl-icon-fill">
				<g id="epl-icon-shape-container" class="epl-icon-shape-container">
						<rect width="100" height="100" style="fill:none"/>
				</g>
				<g id="epl-icon-shape-base" class="epl-icon-shape-base epl-icon-color-base">
					<path class="epl-icon-shape-part-base" id="LinkedIn" data-name="LinkedIn" d="M93.37,59.11V91.78H74.43V61.29c0-7.66-2.74-12.88-9.6-12.88-5.23,0-8.35,3.52-9.72,6.93A13,13,0,0,0,54.49,60V91.78H35.54s.26-51.63,0-57H54.49v8.08l-.12.18h.12v-.18c2.52-3.88,7-9.41,17.07-9.41C84,33.46,93.37,41.6,93.37,59.11ZM15.72,7.33C9.24,7.33,5,11.59,5,17.18S9.12,27,15.47,27h.13c6.61,0,10.72-4.38,10.72-9.85S22.2,7.33,15.72,7.33ZM6.13,91.78H25.06v-57H6.13Z" transform="translate(0 -0.29)"/>
				</g>
			</symbol>

			<symbol id="epl-icon-svg-google-plus" class="epl-icon-fill">
				<g id="epl-icon-shape-container" class="epl-icon-shape-container">
					<rect width="100" height="100" style="fill:none"/>
				</g>
				<g id="epl-icon-shape-base" class="epl-icon-shape-base epl-icon-color-base">
					<path class="epl-icon-shape-part-base" id="GooglePlus-g" data-name="GooglePlus-g" d="M63.55,4.62H41.91c-9.64,0-16.32,2.11-22.33,7.08A21.37,21.37,0,0,0,12,27.55c0,9,6.87,18.59,19.6,18.59,1.22,0,2.58-.13,3.78-.24l-.18.43a9.84,9.84,0,0,0-1,4.19c0,3.51,1.7,5.68,3.33,7.78l.21.27-.37,0c-5.26.36-15,1-22.19,5.42-8.44,5-9.1,12.32-9.1,14.44,0,8.42,7.85,16.93,25.39,16.93,20.4,0,31.07-11.25,31.07-22.37,0-8.22-4.82-12.28-9.94-16.59l-4.32-3.36c-1.33-1.11-3-2.48-3-5s1.66-4.16,3.13-5.62l.15-.15c4.66-3.67,9.94-7.83,9.94-16.84s-5.66-13.73-8.38-16h7.18a.46.46,0,0,0,.25-.07L63.8,5.49a.47.47,0,0,0-.25-.87ZM37.31,90.42c-12.43,0-20.79-5.81-20.79-14.45,0-5.64,3.42-9.74,10.15-12.18a47.71,47.71,0,0,1,12.41-1.9,19.08,19.08,0,0,1,2.71.12c8.7,6.19,12.88,9.44,12.88,15.63C54.67,85.52,48,90.42,37.31,90.42Zm-.12-47.59c-10.43,0-14.76-13.72-14.76-21.06,0-3.73.85-6.6,2.6-8.76a10.94,10.94,0,0,1,8.27-3.9C42.87,9.1,48.17,22,48.17,30.87c0,1.4,0,5.68-3,8.64A12.1,12.1,0,0,1,37.19,42.82Z" transform="translate(0 -0.29)"/>
					<path class="epl-icon-shape-part-base" id="GooglePlus-plus" data-name="GooglePlus-plus"  d="M93.89,47H82.48V35.65a.47.47,0,0,0-.47-.47H77.11a.47.47,0,0,0-.47.47V47H65.29a.47.47,0,0,0-.47.47v5a.47.47,0,0,0,.47.47H76.64V64.36a.47.47,0,0,0,.47.47H82a.47.47,0,0,0,.47-.47V52.92h11.4a.47.47,0,0,0,.47-.47v-5A.47.47,0,0,0,93.89,47Z" transform="translate(0 -0.29)"/>
				</g>
			</symbol>

			<symbol id="epl-icon-svg-youtube" class="epl-icon-fill">
				<g id="epl-icon-shape-container" class="epl-icon-shape-container">
					<rect width="100" height="100" style="fill:none"/>
				</g>
				<g id="epl-icon-shape-base" class="epl-icon-shape-base epl-icon-color-base">
					<path class="epl-icon-shape-part-base" id="YouTube" data-name="YouTube" d="M76.7,71.2h-5l0-2.9A2.36,2.36,0,0,1,74.08,66h.32a2.36,2.36,0,0,1,2.36,2.34ZM58,65a2.14,2.14,0,0,0-2.3,1.89V81a2.35,2.35,0,0,0,4.61,0V66.88A2.14,2.14,0,0,0,58,65Zm30.43-7.89V83.94c0,6.44-5.58,11.71-12.41,11.71H25c-6.83,0-12.41-5.27-12.41-11.71V57.09c0-6.44,5.58-11.71,12.41-11.71H76C82.81,45.38,88.39,50.65,88.39,57.09Zm-60,29.76V58.57h6.33V54.38l-16.86,0v4.12l5.26,0V86.85Zm19-24.07H42v15.1a26.76,26.76,0,0,1,0,3.66c-.43,1.17-2.36,2.42-3.11.13a30.05,30.05,0,0,1,0-3.68l0-15.2H33.64l0,15c0,2.29-.05,4,0,4.78.13,1.37.08,3,1.36,3.89,2.37,1.71,6.92-.25,8.06-2.7v3.12h4.24V62.79ZM64.19,80.08V67.51c0-4.79-3.59-7.66-8.45-3.78l0-9.34H50.48l0,32.25,4.33-.06.39-2C60.72,89.65,64.2,86.17,64.19,80.08ZM80.7,78.41l-4,0c0,.16,0,.34,0,.54v2.21a2.16,2.16,0,0,1-2.16,2.14H73.8a2.16,2.16,0,0,1-2.16-2.14v-5.8h9.06V72a53.77,53.77,0,0,0-.27-6.4c-.65-4.5-7-5.22-10.17-2.91a6,6,0,0,0-2.21,3,16.77,16.77,0,0,0-.67,5.31v7.49C67.37,90.89,82.5,89.13,80.7,78.41ZM60.41,37.73a3.59,3.59,0,0,0,1.27,1.6,3.67,3.67,0,0,0,2.16.6,3.36,3.36,0,0,0,2-.63,4.85,4.85,0,0,0,1.48-1.89l-.1,2.07h5.88v-25H68.49V33.93a1.93,1.93,0,0,1-3.85,0V14.47H59.8V31.34c0,2.15,0,3.58.1,4.31A7.25,7.25,0,0,0,60.41,37.73ZM42.6,23.61a20.81,20.81,0,0,1,.6-5.63,6,6,0,0,1,6.15-4.48,7.29,7.29,0,0,1,3.5.79,5.83,5.83,0,0,1,2.26,2.05A8.09,8.09,0,0,1,56.18,19,20.76,20.76,0,0,1,56.48,23v6.32a34.23,34.23,0,0,1-.27,5.11,8.1,8.1,0,0,1-1.16,3,5.5,5.5,0,0,1-2.26,2.08,7.22,7.22,0,0,1-3.19.67,8.86,8.86,0,0,1-3.4-.57A4.67,4.67,0,0,1,44,38a7.7,7.7,0,0,1-1.1-2.8,27,27,0,0,1-.33-4.93V23.61Zm4.6,9.93a2.35,2.35,0,1,0,4.67,0V20.23a2.35,2.35,0,1,0-4.67,0ZM30.93,40.26h5.55V21.08L43,4.66H37l-3.48,12.2L30,4.62h-6l7,16.47Z" transform="translate(0 -0.29)"/>
				</g>
			</symbol>

			<symbol id="epl-icon-svg-skype" class="epl-icon-fill">
				<g id="epl-icon-shape-container" class="epl-icon-shape-container">
					<rect width="100" height="100" style="fill:none"/>
				</g>
				<g id="epl-icon-shape-base" class="epl-icon-shape-base epl-icon-color-base">
					<path class="epl-icon-shape-part-base" id="Skype" data-name="Skype" d="M89.27,55.88a39.25,39.25,0,0,0,.38-5.39A39.9,39.9,0,0,0,43.31,11.33a23.81,23.81,0,0,0-33,32.91,39.7,39.7,0,0,0,39.4,45.95A40.89,40.89,0,0,0,57,89.52,23.82,23.82,0,0,0,89.27,55.88ZM53.48,79.75c-12.68.66-18.61-2.15-24-7.24C23.36,66.8,25.8,60.31,30.75,60s7.91,5.6,10.55,7.25,12.66,5.39,18-.66c5.77-6.59-3.83-10-10.87-11-10-1.48-22.73-6.92-21.74-17.63s9.09-16.19,17.62-17c10.87-1,18,1.65,23.55,6.42,6.47,5.52,3,11.69-1.15,12.19s-8.72-9.08-17.78-9.22c-9.34-.15-15.65,9.72-4.12,12.52s23.88,4,28.34,14.49S66.17,79.08,53.48,79.75Z" transform="translate(0 -0.29)"/>
				</g>
			</symbol>
		</defs>
	</svg>';

	apply_filters( 'epl_svg_social_icons' , $social_icons );

	// Only Load SVG icons if epl_icons_svg_author is on
	if ( epl_get_option('epl_icons_svg_author') == 'on' ) {
		echo $social_icons;
	}
}
add_action('wp_head', 'epl_load_svg_social_icons_head' , 90 );