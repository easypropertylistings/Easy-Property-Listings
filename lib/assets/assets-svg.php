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
 * SVG Icons Loaded in Head
 */
function epl_load_svg_listing_icons_head() {


	$svg_icons = '

	<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" class="hide">
		<defs>
			<symbol id="epl-icon-svg-bed">
				<g id="epl-icon-shape-container" class="epl-icon-shape-container">
					<rect class="epl-icon-shape-part-container" width="100" height="100" style="fill:none"/>
				</g>
				<g id="epl-icon-shape-frame" class="epl-icon-shape-frame epl-icon-color-frame">
					<path class="epl-icon-shape-part-frame" d="M17.8,48.17H91.71a2.21,2.21,0,0,1,2.21,2.21v3.17a2.21,2.21,0,0,1-2.21,2.21H17.8a2.22,2.22,0,0,1-2.22-2.22V50.39A2.22,2.22,0,0,1,17.8,48.17Z"/>
					<rect class="epl-icon-shape-part-frame" x="5.39" y="32.55" width="7.39" height="39.68" rx="1.84" ry="1.84"/>
					<path class="epl-icon-shape-part-frame" d="M81.79,59.44V70.68a1.84,1.84,0,0,0,1.85,1.84h3.7a1.84,1.84,0,0,0,1.84-1.84V59.44Z" transform="translate(0 -0.29)"/>
				</g>
				<g id="epl-icon-shape-pillow" class="epl-icon-shape-pillow epl-icon-color-highlight">
					<path class="epl-icon-shape-part-pillow" d="M41.73,45.6a1.84,1.84,0,0,1-2.35,1.13l-22.81-8a1.84,1.84,0,0,1-1.13-2.35l1.71-4.89a1.84,1.84,0,0,1,2.35-1.13l22.81,8a1.84,1.84,0,0,1,1.13,2.35Z" transform="translate(0 -0.29)"/>
				</g>
				<g id="epl-icon-shape-matress" class="epl-icon-shape-matress epl-icon-color-alternate">
					<rect class="epl-icon-shape-part-matress" x="50.98" y="41.84" width="40.52" height="9.71" rx="1.84" ry="1.84"/>
				</g>
			</symbol>

			<symbol id="epl-icon-svg-bath">
				<g id="epl-icon-shape-container" class="epl-icon-shape-container">
					<rect class="epl-icon-shape-part-container" width="100" height="100" style="fill:none"/>
				</g>
				<g id="epl-icon-shape-frame" class="epl-icon-shape-frame epl-icon-color-frame">
					<rect class="epl-icon-shape-part-frame" x="5.41" y="41.13" width="86.83" height="5.51" rx="2.75" ry="2.75"/>
				</g>
				<g id="epl-icon-shape-base" class="epl-icon-shape-base epl-icon-color-base">
					<path class="epl-icon-shape-part-base" d="M13.25,48.84S16.9,70.64,35,70.64H64.49c18.14,0,21.79-21.79,21.79-21.79Zm4.39,4.26h2.93c.48,2.26,3,11.8,10.74,15.3C20,67.08,17.64,53.1,17.64,53.1Z" transform="translate(0.02)"/>
				</g>
				<g id="epl-icon-shape-feet" class="epl-icon-shape-feet">
					<path class="epl-icon-shape-part-feet" d="M33.2,72.47A18.88,18.88,0,0,1,23.62,70L21.33,75.5s-1.9,3.8,4.42.9l7.77-3.93Z" transform="translate(0.02)"/>
					<path class="epl-icon-shape-part-feet" d="M66.34,72.47A18.88,18.88,0,0,0,75.92,70l2.29,5.49s1.9,3.8-4.42.9L66,72.47Z" transform="translate(0.02)"/>
				</g>
				<g id="epl-icon-shape-tap" class="epl-icon-shape-tap">
					<path class="epl-icon-shape-part-tap" d="M11.06,37.9h5.15s.33-6.39,4.07-6.39c0,0,2,0,3.32,2.81l1.87-1.87s-2.61-2.85-5-2.85S11.6,35.19,11.06,37.9Z" transform="translate(0.02)"/>
				</g>
			</symbol>

			<symbol id="epl-icon-svg-car">
				<g id="epl-icon-shape-container" class="epl-icon-shape-container">
					<rect class="epl-icon-shape-part-container" width="100" height="100" style="fill:none"/>
				</g>
				<g id="epl-icon-shape-frame" class="epl-icon-shape-frame epl-icon-color-frame">
					<path class="epl-icon-shape-part-frame" d="M5.39,30.27V28.59a5.69,5.69,0,0,1,5.68-5.69H88.65a5.69,5.69,0,0,1,5.69,5.69v1.68Z" transform="translate(0.02)"/>
					<path class="epl-icon-shape-part-frame" d="M11.78,74.52a3.22,3.22,0,0,0,6.44,0V33.18H11.78ZM80.89,33.18V74.52a3.22,3.22,0,0,0,6.44,0V33.18Z" transform="translate(0.02)"/>
				</g>
				<g id="epl-icon-shape-car" class="epl-icon-shape-car epl-icon-color-base">
					<path class="epl-icon-shape-part-car" d="M75.15,47.41a22,22,0,0,0-3,.53s-.4.13-.8.31l-.2-.1a2,2,0,0,0,0-.81L68.36,39c-.42-1.4-1.13-2.53-2.53-2.53,0,0-10.64-.61-15.57-.67H50c-4.94.06-15.57.67-15.57.67-1.4,0-2.11,1.14-2.53,2.53l-2.75,8.29a2,2,0,0,0,0,.81l-.28.15a7.45,7.45,0,0,0-1.23-.53,22,22,0,0,0-3-.53c-.79,0-1,1-1,1v1.59c0,1.11,1.64,1.35,1.64,1.35h2.51V64.08a.73.73,0,0,0-.19.13H26.28a.76.76,0,0,0-.77.76v2.3a.77.77,0,0,0,.77.77H28.6v3.22a1.53,1.53,0,0,0,1.53,1.53H32.3a1.53,1.53,0,0,0,1.53-1.53V68H66.46v3.22A1.53,1.53,0,0,0,68,72.79h2.16a1.53,1.53,0,0,0,1.53-1.53V68h2.26a.77.77,0,0,0,.77-.77V65a.77.77,0,0,0-.77-.76h-1a.75.75,0,0,0-.43-.19V51.35h2s1.64-.24,1.64-1.35V48.41S75.94,47.41,75.15,47.41ZM38.9,63.63A1.34,1.34,0,0,1,37.56,65H31.47a1.34,1.34,0,0,1-1.34-1.34V61.48a1.34,1.34,0,0,1,1.34-1.34h6.09a1.34,1.34,0,0,1,1.34,1.34Zm-5.54-14h0c-1.27,0-2.6-.85-2.3-2.3L33.54,40c.38-1.27,1-2.3,2.3-2.3,0,0,9.66-.55,14.14-.61h.27c4.48.06,14.14.61,14.14.61,1.27,0,1.92,1,2.3,2.3l2.5,7.35c.3,1.45-1,2.3-2.3,2.3h0c-4.76-.42-10.51-.69-16.75-.76S38.12,49.22,33.37,49.65Zm37.32,14A1.34,1.34,0,0,1,69.35,65H63.26a1.34,1.34,0,0,1-1.34-1.34V61.48a1.34,1.34,0,0,1,1.34-1.34h6.09a1.34,1.34,0,0,1,1.34,1.34Z" transform="translate(0.02)"/>
				</g>
			</symbol>

			<symbol id="epl-icon-svg-air">
				<g id="epl-icon-shape-container" class="epl-icon-shape-container">
					<rect class="epl-icon-shape-part-container" width="100" height="100" style="fill:none"/>
				</g>
				<g id="epl-icon-shape-frame" class="epl-icon-shape-frame epl-icon-color-frame">
					<path class="epl-icon-shape-part-frame" d="M50.3,22.9A22.3,22.3,0,1,0,72.6,45.2h0A22.32,22.32,0,0,0,50.3,22.9Zm0,42A19.7,19.7,0,1,1,70,45.2h0A19.67,19.67,0,0,1,50.3,64.9Z"/>
				</g>
				<g id="epl-icon-shape-base" class="epl-icon-shape-base epl-icon-color-base">
					<path class="epl-icon-shape-part-base" d="M45.8,70.5c-.5,2.2-2.7,3.2-4.7,4.5-2.3,1.4-4.4,1.9-4.4,4.6v1.7H64V79.6c0-2.6-2.1-3.2-4.4-4.6-2-1.3-4.2-2.3-4.7-4.5a27.78,27.78,0,0,1-9.1,0Z"/>
				</g>
				<g id="epl-icon-shape-fan" class="epl-icon-shape-fan epl-icon-color-alternate">
					<path class="epl-icon-shape-part-fan" d="M52.6,40.5c1.2-3.6,1.3-11.4-7-11.4-4.4,0-7.3,5.9-2.2,9,1.4.8,2.7,1.4,3.4,3.2a5.15,5.15,0,0,1,5.8-.8Z"/>
					<path class="epl-icon-shape-part-fan" d="M45.1,45.3c-3.7,1-9.9,4.9-5.7,11.8,2.3,3.8,8.8,3.2,8.8-2.8,0-1.4-.2-2.8.7-4.1A5.13,5.13,0,0,1,45.1,45.3Z"/>
					<path class="epl-icon-shape-part-fan" d="M59.6,41.9c-1.4.8-2.5,1.7-4.4,1.5a5.29,5.29,0,0,1-1.6,5.9c2.8,2.7,9.3,5.8,13-1.3C68.6,44.1,64.7,38.8,59.6,41.9Z"/>
				</g>
				<g id="epl-icon-shape-circle" class="epl-icon-shape-circle epl-icon-color-highlight">
					<path class="epl-icon-shape-part-circle" d="M50.3,42.3a2.9,2.9,0,1,0,2.9,2.9h0A2.9,2.9,0,0,0,50.3,42.3Z"/>
				</g>
			</symbol>

			<symbol id="epl-icon-svg-pool">
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

	add_filter ( 'epl_svg_icons' , $svg_icons );

	echo $svg_icons;


	// CSS Fill Examples
	/*

		#epl-icon-svg-bed .epl-icon-shape-frame {
			fill: red
		}

		#epl-icon-svg-bed #pillow {
			fill: red
		}

		#epl-icon-svg-bed #matress {
			fill: red
		}
	*/

}
add_action('wp_head', 'epl_load_svg_listing_icons_head' , 99 );



