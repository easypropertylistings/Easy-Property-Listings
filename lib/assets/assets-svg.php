<?php
/**
 * Scripts & Styles
 *
 * @package     EPL
 * @subpackage  Assets/SVG
 * @copyright   Copyright (c) 2019, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.2
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * SVG Listing Icons Loaded in Head.
 *
 * @since 3.2
 */
function epl_load_svg_listing_icons_head() {

	$svg_icons = '

	<svg version="1.1" xmlns="http://www.w3.org/2000/svg"  class="hide" style="display:none">
		<defs>
			<symbol id="epl-icon-svg-bed" class="epl-icon-fill epl-icon-listing">
				<g id="epl-icon-bed-shape-container" class="epl-icon-shape-container">
					<rect class="epl-icon-shape-part-container" width="100" height="100" style="fill:none"/>
				</g>
				<g id="epl-icon-bed-shape-frame" class="epl-icon-shape-frame epl-icon-color-frame">
					<rect class="epl-icon-shape-part-frame" x="19.18" y="49.4" width="70.2" height="6.81" rx="1.98" ry="1.98"/>
					<rect class="epl-icon-shape-part-frame" x="10.94" y="33.62" width="6.62" height="35.56" rx="1.65" ry="1.65"/>
					<path class="epl-icon-shape-part-frame" d="M78.51,57.73V67.81a1.65,1.65,0,0,0,1.65,1.65h3.32a1.65,1.65,0,0,0,1.65-1.65V57.73Z" transform="translate(0 -0.29)"/>
				</g>
				<g id="epl-icon-bed-shape-pillow" class="epl-icon-shape-pillow epl-icon-color-base">
					<path class="epl-icon-shape-part-pillow" d="M42.61,49.45a1.65,1.65,0,0,1-2.1,1L20.06,43.29a1.65,1.65,0,0,1-1-2.1l1.54-4.38a1.65,1.65,0,0,1,2.11-1L43.13,43a1.65,1.65,0,0,1,1,2.1Z" transform="translate(0 -0.29)"/>
				</g>
				<g id="epl-icon-bed-shape-matress" class="epl-icon-shape-matress epl-icon-color-alternate">
					<rect class="epl-icon-shape-part-matress" x="50.89" y="42.84" width="36.31" height="8.7" rx="1.65" ry="1.65"/>
				</g>
			</symbol>

			<symbol id="epl-icon-svg-bath" class="epl-icon-fill epl-icon-listing">
				<g id="epl-icon-bath-shape-container" class="epl-icon-shape-container">
					<rect class="epl-icon-shape-part-container" width="100" height="100" style="fill:none"/>
				</g>
				<g id="epl-icon-bath-shape-frame" class="epl-icon-shape-frame epl-icon-color-frame">
					<rect class="epl-icon-shape-part-frame" x="5.41" y="41.13" width="86.83" height="5.51" rx="2.75" ry="2.75"/>
				</g>
				<g id="epl-icon-bath-shape-base" class="epl-icon-shape-base epl-icon-color-base">
					<path class="epl-icon-shape-part-base" d="M13.25,48.84S16.9,70.64,35,70.64H64.49c18.14,0,21.79-21.79,21.79-21.79Zm4.39,4.26h2.93c.48,2.26,3,11.8,10.74,15.3C20,67.08,17.64,53.1,17.64,53.1Z" transform="translate(0.02)"/>
				</g>
				<g id="epl-icon-bath-shape-feet" class="epl-icon-shape-feet epl-icon-color-base">
					<path class="epl-icon-shape-part-feet" d="M33.2,72.47A18.88,18.88,0,0,1,23.62,70L21.33,75.5s-1.9,3.8,4.42.9l7.77-3.93Z" transform="translate(0.02)"/>
					<path class="epl-icon-shape-part-feet" d="M66.34,72.47A18.88,18.88,0,0,0,75.92,70l2.29,5.49s1.9,3.8-4.42.9L66,72.47Z" transform="translate(0.02)"/>
				</g>
				<g id="epl-icon-bath-shape-tap" class="epl-icon-shape-tap epl-icon-color-base">
					<path class="epl-icon-shape-part-tap epl-icon-shape-part-base" d="M11.06,37.9h5.15s.33-6.39,4.07-6.39c0,0,2,0,3.32,2.81l1.87-1.87s-2.61-2.85-5-2.85S11.6,35.19,11.06,37.9Z" transform="translate(0.02)"/>
				</g>
			</symbol>

			<symbol id="epl-icon-svg-car" class="epl-icon-fill epl-icon-listing">
				<g id="epl-icon-car-shape-container" class="epl-icon-shape-container">
					<rect class="epl-icon-shape-part-container" width="100" height="100" style="fill:none"/>
				</g>
				<g id="epl-icon-car-shape-base" class="epl-icon-shape-car epl-icon-color-base">
					<path class="epl-icon-shape-part-car" d="M70.18,68.94v4.5c0,1.24.54,2.4,2.09,2.4h9.31c1.71,0,2-.93,2.09-2.95.08-2.25,0-16.45,0-18.93,0-3.34,0-4.34-.54-5.35-.93-1.71-2.48-5.9-2.48-5.9a19.83,19.83,0,0,0,3.1-1.16c1.63-.78,1.78-1.24,1.09-2.95s-1.32-2.79-2.56-2.95-4.34-.39-7.52-.62C71.11,29.77,67.31,25,66.23,25H34.58c-1,0-4.89,4.81-8.46,10.08-3.18.16-6.21.39-7.52.62S16.74,36.9,16,38.61s-.62,2.17,1,2.95a19.83,19.83,0,0,0,3.1,1.16s-1.94,4.11-2.79,5.9c-.54,1.09-.54,1.09-.54,3.1,0,3.18-.08,18.85,0,21.1.08,2,.47,2.95,2.09,2.95h9.23c1.55,0,2.09-1.09,2.09-2.4v-4.5H70.18ZM34.58,56.84s-8.92-.39-11.4-.62c-3.1-.31-2.09-5.12-2.09-6.52,0-.08,7.83.78,10.32,1.16s3.1,1.86,3.1,2.4S34.58,56.22,34.58,56.84ZM64,30.39c.62,0,1.63-.23,5.2,4.81,2.87,4,4,7.68,3.1,7.68h-44c-.93,0,.62-4.11,3.1-7.68,3.65-5.12,4.58-4.81,5.2-4.81ZM77.48,56.3c-2.48.23-11.4.62-11.4.62V53.35c0-.54.54-1.86,3.1-2.4s10.39-1.32,10.39-1.16C79.57,51.1,80.58,56,77.48,56.3Z"/>
				</g>
			</symbol>

			<symbol id="epl-icon-svg-air" class="epl-icon-fill epl-icon-listing">
				<g id="epl-icon-air-shape-container" class="epl-icon-shape-container">
					<rect class="epl-icon-shape-part-container" width="100" height="100" style="fill:none"/>
				</g>
				<g id="epl-icon-air-shape-frame" class="epl-icon-shape-frame epl-icon-color-frame">
					<path class="epl-icon-shape-part-frame" d="M50.3,22.9A22.3,22.3,0,1,0,72.6,45.2h0A22.32,22.32,0,0,0,50.3,22.9Zm0,42A19.7,19.7,0,1,1,70,45.2h0A19.67,19.67,0,0,1,50.3,64.9Z"/>
				</g>
				<g id="epl-icon-air-shape-base" class="epl-icon-shape-base epl-icon-color-alternate">
					<path class="epl-icon-shape-part-base" d="M45.8,70.5c-.5,2.2-2.7,3.2-4.7,4.5-2.3,1.4-4.4,1.9-4.4,4.6v1.7H64V79.6c0-2.6-2.1-3.2-4.4-4.6-2-1.3-4.2-2.3-4.7-4.5a27.78,27.78,0,0,1-9.1,0Z"/>
				</g>
				<g id="epl-icon-air-shape-fan" class="epl-icon-shape-fan epl-icon-color-base">
					<path class="epl-icon-shape-part-fan" d="M52.6,40.5c1.2-3.6,1.3-11.4-7-11.4-4.4,0-7.3,5.9-2.2,9,1.4.8,2.7,1.4,3.4,3.2a5.15,5.15,0,0,1,5.8-.8Z"/>
					<path class="epl-icon-shape-part-fan" d="M45.1,45.3c-3.7,1-9.9,4.9-5.7,11.8,2.3,3.8,8.8,3.2,8.8-2.8,0-1.4-.2-2.8.7-4.1A5.13,5.13,0,0,1,45.1,45.3Z"/>
					<path class="epl-icon-shape-part-fan" d="M59.6,41.9c-1.4.8-2.5,1.7-4.4,1.5a5.29,5.29,0,0,1-1.6,5.9c2.8,2.7,9.3,5.8,13-1.3C68.6,44.1,64.7,38.8,59.6,41.9Z"/>
				</g>
				<g id="epl-icon-air-shape-circle" class="epl-icon-shape-circle epl-icon-color-highlight">
					<path class="epl-icon-shape-part-circle" d="M50.3,42.3a2.9,2.9,0,1,0,2.9,2.9h0A2.9,2.9,0,0,0,50.3,42.3Z"/>
				</g>
			</symbol>

			<symbol id="epl-icon-svg-pool" class="epl-icon-fill epl-icon-listing">
				<g id="epl-icon-pool-shape-container" class="epl-icon-shape-container">
					<rect class="epl-icon-shape-part-container" width="100" height="100" style="fill:none"/>
				</g>
				<g id="epl-icon-pool-shape-frame" class="epl-icon-shape-frame epl-icon-color-frame">
					<path class="epl-icon-shape-part-frame" d="M67.35,25.19a7.25,7.25,0,0,0-5.42,2.17c-2.83,3-2.73,7.71-2.71,8.24v5.52h-15v-5.8c0-1.19.27-3.82,1.6-5.18a3.15,3.15,0,0,1,2.39-.92c.58.06,3.81.66,3.81,6.31a2,2,0,0,0,4,0c0-7.9-5-10.22-7.7-10.35a7.25,7.25,0,0,0-5.42,2.17c-2.83,3-2.73,7.71-2.71,8.24v38a2,2,0,1,0,4,0v-3h15v3a2,2,0,1,0,4,0V35.33c0-1.19.27-3.82,1.6-5.18a3.14,3.14,0,0,1,2.39-.92c.58.06,3.81.66,3.81,6.31a2,2,0,1,0,4,0C75,27.62,70,25.31,67.35,25.19ZM59.22,44.66v5.18h-15V44.66Zm0,8.67V58.5h-15V53.33Zm-15,13.81V62h15v5.18Z"/>
				</g>
				<g id="epl-icon-pool-shape-water" class="epl-icon-shape-water epl-icon-color-base">
					<path class="epl-icon-shape-part-water" d="M80.94,55.31a.95.95,0,1,0,1-1.59c-5.3-3.5-11.66-3.75-15.65-.82v2.49l.26-.24C69.28,52.44,75.24,51.56,80.94,55.31ZM27,56.61c-5.82.18-7.3-2.45-7.3-2.56a1,1,0,0,0-1.27-.41A.94.94,0,0,0,18,54.89c.08.16,1.94,3.79,9.12,3.58a13.38,13.38,0,0,0,7.6-3.32,7.79,7.79,0,0,1,3.09-1.82v-2a9.84,9.84,0,0,0-4.44,2.51C31,56.21,27,56.58,27,56.58Zm0,6.12c-5.82.14-7.3-2.45-7.3-2.56a1,1,0,0,0-1.28-.44h0A.94.94,0,0,0,18,61h0c.08.16,1.94,3.79,9.12,3.58a13.38,13.38,0,0,0,7.6-3.32,7.81,7.81,0,0,1,3.09-1.82v-2a9.86,9.86,0,0,0-4.44,2.51C31,62.34,27,62.7,27,62.7ZM82,66c-5.3-3.5-11.66-3.74-15.65-.81v2.49l.26-.24c2.71-2.71,8.67-3.59,14.37.13a.95.95,0,0,0,1-1.59Zm0-6.12c-5.3-3.5-11.66-3.75-15.65-.84V61.5l.26-.24c2.71-2.71,8.67-3.59,14.37.13a.95.95,0,0,0,1-1.59ZM27,68.86c-5.82.14-7.3-2.46-7.3-2.57a1,1,0,0,0-1.27-.41A.94.94,0,0,0,18,67.14h0c.08.16,1.94,3.79,9.12,3.58a13.38,13.38,0,0,0,7.6-3.32,7.77,7.77,0,0,1,3.09-1.82v-2a9.86,9.86,0,0,0-4.44,2.51C31,68.46,27,68.83,27,68.83Z"/>
				</g>
			</symbol>

		</defs>
	</svg>';

	$svg_icons = apply_filters( 'epl_svg_icons', $svg_icons );

	// Only Load SVG icons if epl_icons_svg_listings is on.
	if ( epl_get_option( 'epl_icons_svg_listings' ) === 'on' ) {

		$allowed_tags = epl_get_svg_allowed_tags();

		echo wp_kses( $svg_icons, $allowed_tags );
	}

}
add_action( 'wp_head', 'epl_load_svg_listing_icons_head', 90 );

/**
 * SVG Social Media Icons Loaded in Head.
 *
 * @since 3.2
 */
function epl_load_svg_social_icons_head() {

	$social_icons = '

	<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" class="hide" style="display:none">
		<defs>

			<symbol id="epl-icon-svg-email" class="epl-icon-fill epl-icon-social">
				<path id="epl-icon-email-shape-base" class="epl-icon-shape-base epl-icon-color-base" d="M100,75c0,13.8-11.2,25-25,25H25C11.2,100,0,88.8,0,75V25C0,11.2,11.2,0,25,0h50c13.8,0,25,11.2,25,25V75z"/>
				<g id="epl-icon-email-shape-hollow" class="epl-icon-color-hollow">
					<path class="st1" d="M77.2,39.8c-0.1-0.1-0.2-0.1-0.3-0.2V24c0-0.5-0.4-0.8-0.8-0.8h-20C56.1,23.1,56,23.1,56,23L51,19.1
						c-0.6-0.5-1.5-0.5-2.1,0L44,23c-0.1,0.1-0.1,0.1-0.2,0.2H23.9c-0.4,0-0.8,0.4-0.8,0.8v15.6c-0.1,0-0.2,0.1-0.3,0.2l-3,2.4
						c-0.6,0.4-1,1.4-1,2.2v35.6c0,0.7,0.6,1.3,1.3,1.3h59.9c0.7,0,1.3-0.6,1.3-1.3V44.4c0-0.7-0.5-1.7-1-2.2L77.2,39.8z M72.6,49.7
						c0,0.5-0.3,1-0.7,1.2L50.7,62.6c-0.4,0.2-1,0.2-1.4,0L28.1,50.9c-0.4-0.2-0.7-0.8-0.7-1.2V27.6c0-0.5,0.4-0.8,0.8-0.8h43.5
						c0.4,0,0.8,0.4,0.8,0.8V49.7z"/>
					<rect x="32.2" y="33.8" class="st1" width="12.5" height="12.8"/>
					<rect x="48.4" y="33.8" class="st1" width="21.2" height="2.4"/>
					<rect x="48.4" y="38.6" class="st1" width="21.2" height="2.4"/>
					<rect x="48.4" y="43.4" class="st1" width="8.6" height="2.4"/>
				</g>
			</symbol>

			<symbol id="epl-icon-svg-facebook" class="epl-icon-fill epl-icon-social">
				<g>
					<path id="epl-icon-facebook-shape-base" class="epl-icon-shape-base epl-icon-color-base" d="M100,75c0,13.8-11.2,25-25,25H25C11.2,100,0,88.8,0,75V25C0,11.2,11.2,0,25,0h50c13.8,0,25,11.2,25,25V75z"/>
					<g>
						<path id="epl-icon-facebook-shape-hollow" class="epl-icon-color-hollow" d="M41.7,81.2h12.5c0,0,0-17.3,0-31.2h9.3l1.1-12.5h-9.9v-5c0-2.4,1.6-3,2.8-3c1.1,0,7,0,7,0V18.8l-9.7,0
							c-10.7,0-13.2,8-13.2,13.1v5.6h-6.2V50h6.2C41.7,64.2,41.7,81.2,41.7,81.2z"/>
					</g>
				</g>
			</symbol>

			<symbol id="epl-icon-svg-twitter" class="epl-icon-fill epl-icon-social">
				<g>
					<path id="epl-icon-twitter-shape-base" class="epl-icon-shape-base epl-icon-color-base" d="M100,75.1c0,13.8-11.2,24.9-25,24.9H25C11.2,100,0,88.9,0,75.1v-50C0,11.3,11.2,0,25,0h50
						c13.8,0,25,11.3,25,25.1V75.1z"/>
					<g>
						<path id="epl-icon-twitter-shape-hollow" class="epl-icon-color-hollow" d="M79.6,26c-2.5,1.4-5.2,2.5-8.1,3.1c-2.3-2.5-5.7-4-9.4-4c-7.1,0-12.8,5.7-12.8,12.6c0,1,0.1,2,0.3,2.9
							C38.9,40,29.5,35,23.1,27.4c-1.1,1.9-1.7,4-1.7,6.3c0,4.4,2.3,8.2,5.7,10.5c-2.1-0.1-4.1-0.6-5.8-1.6c0,0.1,0,0.1,0,0.2
							c0,6.1,4.4,11.2,10.3,12.4c-1.1,0.3-2.2,0.4-3.4,0.4c-0.8,0-1.6-0.1-2.4-0.2c1.6,5,6.4,8.7,12,8.8c-4.4,3.4-9.9,5.4-15.9,5.4
							c-1,0-2.1-0.1-3.1-0.2c5.7,3.6,12.4,5.7,19.7,5.7c23.6,0,36.5-19.2,36.5-35.9c0-0.5,0-1.1,0-1.6c2.5-1.8,4.7-4,6.4-6.5
							c-2.3,1-4.8,1.7-7.4,2C76.6,31.4,78.6,28.9,79.6,26z"/>
					</g>
				</g>
			</symbol>

			<symbol id="epl-icon-svg-linkedin" class="epl-icon-fill epl-icon-social">
				<g>
					<path id="epl-icon-linkedin-shape-base" class="epl-icon-shape-base epl-icon-color-base" d="M100,75c0,13.8-11.2,25-25,25H25C11.2,100,0,88.8,0,75V25C0,11.2,11.2,0,25,0h50c13.8,0,25,11.2,25,25V75z"/>
					<g>
						<rect id="epl-icon-linkedin-shape-hollow" class="epl-icon-color-hollow" x="18.8" y="39.6" width="14.6" height="41.7"/>
						<circle id="epl-icon-linkedin-shape-hollow" class="epl-icon-color-hollow" cx="26" cy="26" r="7.3"/>
						<path id="epl-icon-linkedin-shape-hollow" class="epl-icon-color-hollow"  d="M81.2,58.4c0-11.2-2.4-18.8-15.5-18.8c-6.3,0-10.5,2.3-12.3,5.6h-0.2v-5.6H41.7v41.7h12.1V60.6
							c0-5.4,1-10.7,7.8-10.7c6.6,0,7.2,6.2,7.2,11.1v20.3h12.5L81.2,58.4L81.2,58.4z"/>
					</g>
				</g>
			</symbol>

			<symbol id="epl-icon-svg-google-plus" class="epl-icon-fill epl-icon-social">
				<g>
					<path id="epl-icon-google-plus-shape-base" class="epl-icon-shape-base epl-icon-color-base" d="M100,75c0,13.8-11.2,25-25,25H25C11.2,100,0,88.8,0,75V25C0,11.2,11.2,0,25,0h50c13.8,0,25,11.2,25,25V75z"/>
					<g>
						<path id="epl-icon-google-plus-shape-hollow" class="epl-icon-color-hollow" d="M66.5,60.8h2.7c0.7,0,1.3-0.6,1.3-1.3V50h9.4c0.7,0,1.3-0.6,1.3-1.3V46c0-0.7-0.6-1.3-1.3-1.3h-9.4v-9.4
							c0-0.7-0.6-1.3-1.3-1.3h-2.7c-0.7,0-1.3,0.6-1.3,1.3v9.4h-9.4c-0.7,0-1.3,0.6-1.3,1.3v2.7c0,0.7,0.6,1.3,1.3,1.3h9.4v9.4
							C65.2,60.2,65.8,60.8,66.5,60.8z"/>
						<path id="epl-icon-google-plus-shape-hollow" class="epl-icon-color-hollow" d="M29.4,77.2c2.4,0.6,4.9,0.9,7.6,0.9c2.4,0,4.7-0.3,6.8-0.9c6.6-1.9,10.8-6.8,10.8-12.4
							c0-5.4-1.7-8.6-6.3-11.9c-2-1.4-3.8-3.5-3.8-4.1c0-1.2,0.1-1.7,2.7-3.7c3.3-2.6,5.2-6.1,5.2-9.7c0-3.3-1-6.2-2.7-8.3h1.3
							c0.3,0,0.5-0.1,0.8-0.2l3.7-2.7c0.5-0.3,0.7-0.9,0.5-1.5c-0.2-0.5-0.7-0.9-1.2-0.9H38.1c-1.8,0-3.7,0.3-5.5,0.9
							c-6,2.1-10.3,7.2-10.3,12.5c0,7.5,5.8,13.2,13.6,13.4c-0.2,0.6-0.2,1.2-0.2,1.8c0,1.2,0.3,2.2,0.9,3.3c-0.1,0-0.1,0-0.2,0
							c-7.4,0-14.1,3.6-16.6,9c-0.7,1.4-1,2.8-1,4.2c0,1.4,0.4,2.7,1,3.9C21.4,73.8,24.8,76,29.4,77.2z M30.9,35.2
							c-0.4-2.7,0.2-5.2,1.6-6.8c0.9-1,2-1.5,3.3-1.5l0.1,0c3.7,0.1,7.2,4.2,7.8,9.1c0.4,2.8-0.2,5.3-1.6,6.9c-0.9,1-2,1.5-3.4,1.5
							c0,0,0,0,0,0h-0.1C35.1,44.3,31.5,40.1,30.9,35.2z M37.9,57.9l0.2,0c1.2,0,2.4,0.2,3.6,0.5c0.4,0.3,0.8,0.5,1.1,0.8
							c2.6,1.8,4.3,3,4.8,4.9c0.1,0.5,0.2,0.9,0.2,1.4c0,4.9-3.6,7.3-10.8,7.3c-5.4,0-9.7-3.3-9.7-7.6C27.3,61.4,32.3,57.9,37.9,57.9z"
							/>
					</g>
				</g>
			</symbol>

			<symbol id="epl-icon-svg-youtube" class="epl-icon-fill epl-icon-social">
				<g>
					<path id="epl-icon-youtube-shape-base" class="epl-icon-shape-base epl-icon-color-base" d="M100,75c0,13.8-11.2,25-25,25H25C11.2,100,0,88.8,0,75V25C0,11.2,11.2,0,25,0h50c13.8,0,25,11.2,25,25V75z"/>
					<g>
						<path id="epl-icon-youtube-shape-hollow" class="epl-icon-color-hollow"  d="M80.6,37c0,0-0.6-4.5-2.5-6.5c-2.4-2.6-5-2.6-6.3-2.8C63.1,27.1,50,27.1,50,27.1h0c0,0-13.1,0-21.9,0.7
									c-1.2,0.1-3.9,0.2-6.3,2.8c-1.9,2-2.5,6.5-2.5,6.5s-0.6,5.3-0.6,10.5v4.9c0,5.3,0.6,10.6,0.6,10.6s0.6,4.5,2.5,6.5
									c2.4,2.6,5.5,2.5,6.9,2.8c5,0.5,21.2,0.7,21.2,0.7s13.1,0,21.9-0.7c1.2-0.2,3.9-0.2,6.3-2.8c1.9-2,2.5-6.5,2.5-6.5
									s0.6-5.3,0.6-10.5v-4.9C81.2,42.2,80.6,37,80.6,37z M43.5,58.5l0-18.3l16.9,9.2L43.5,58.5z"/>
					</g>
				</g>
			</symbol>

			<symbol id="epl-icon-svg-skype" class="epl-icon-fill epl-icon-social">
				<g>
					<path id="epl-icon-skype-shape-base" class="epl-icon-shape-base epl-icon-color-base" d="M100,75c0,13.8-11.2,25-25,25H25C11.2,100,0,88.8,0,75V25C0,11.2,11.2,0,25,0h50c13.8,0,25,11.2,25,25V75z"/>
					<g>
						<path id="epl-icon-skype-shape-hollow" class="epl-icon-color-hollow" d="M80.1,50.3c0-16.2-13.3-29.4-29.8-29.4c-1.7,0-3.4,0.1-5.1,0.4c-2.7-1.7-5.8-2.6-9.2-2.6
							c-9.6,0-17.3,7.6-17.3,17c0,3.1,0.9,6.1,2.4,8.6c-0.4,1.9-0.6,3.9-0.6,5.9c0,16.2,13.3,29.4,29.8,29.4c1.9,0,3.7-0.2,5.5-0.5
							c2.4,1.3,5.2,2,8.2,2c9.5,0,17.3-7.6,17.3-17c0-2.7-0.6-5.3-1.8-7.6C79.9,54.6,80.1,52.5,80.1,50.3z M65.6,64.4
							c-1.4,1.9-3.4,3.4-6.1,4.5c-2.6,1.1-5.7,1.6-9.3,1.6c-4.3,0-7.9-0.7-10.7-2.2c-2-1.1-3.7-2.5-4.9-4.3c-1.3-1.8-1.9-3.6-1.9-5.3
							c0-1.1,0.4-2,1.2-2.8c0.8-0.8,1.9-1.1,3.1-1.1c1,0,1.9,0.3,2.6,0.9c0.7,0.6,1.3,1.4,1.7,2.5c0.5,1.2,1.1,2.2,1.7,3
							c0.6,0.8,1.4,1.4,2.5,1.9c1.1,0.5,2.5,0.8,4.3,0.8c2.5,0,4.5-0.5,6-1.5c1.5-1,2.2-2.2,2.2-3.7c0-1.2-0.4-2.1-1.1-2.8
							c-0.8-0.7-1.9-1.3-3.2-1.7c-1.4-0.4-3.2-0.9-5.5-1.3c-3.1-0.7-5.7-1.4-7.8-2.3c-2.1-0.9-3.9-2.1-5.1-3.7
							c-1.3-1.6-1.9-3.6-1.9-5.9c0-2.2,0.7-4.2,2-5.9c1.3-1.7,3.3-3,5.8-4c2.5-0.9,5.4-1.4,8.8-1.4c2.7,0,5,0.3,7,0.9
							c2,0.6,3.6,1.4,4.9,2.4c1.3,1,2.3,2.1,2.9,3.2c0.6,1.1,0.9,2.3,0.9,3.3c0,1.1-0.4,2-1.2,2.8c-0.8,0.8-1.8,1.3-3.1,1.3
							c-1.1,0-2-0.3-2.6-0.8c-0.6-0.5-1.1-1.2-1.8-2.3c-0.7-1.4-1.6-2.5-2.7-3.3c-1-0.8-2.7-1.1-5-1.1c-2.1,0-3.9,0.4-5.2,1.3
							c-1.2,0.8-1.8,1.7-1.8,2.8c0,0.7,0.2,1.2,0.6,1.7c0.4,0.5,1,1,1.8,1.3c0.8,0.4,1.6,0.7,2.4,0.9c0.8,0.2,2.2,0.6,4.1,1
							c2.4,0.5,4.6,1.1,6.6,1.7c2,0.6,3.7,1.4,5.1,2.3c1.4,0.9,2.6,2.1,3.4,3.5c0.8,1.4,1.2,3.2,1.2,5.2C67.7,60.2,67,62.4,65.6,64.4z"
							/>
					</g>
				</g>
			</symbol>

			<symbol id="epl-icon-svg-instagram" class="epl-icon-fill epl-icon-social">
				<g>
					<path id="epl-icon-instagram-shape-base" class="epl-icon-shape-base epl-icon-color-base" d="M100,75c0,13.8-11.2,25-25,25H25C11.2,100,0,88.8,0,75V25C0,11.2,11.2,0,25,0h50c13.8,0,25,11.2,25,25V75z"/>
					<g>
						<path id="epl-icon-instagram-shape-hollow" class="epl-icon-color-hollow" d="M69.2,18.8H30.8c-6.7,0-12.1,5.4-12.1,12.1v12.8v25.6c0,6.7,5.4,12.1,12.1,12.1h38.3
							c6.7,0,12.1-5.4,12.1-12.1V43.6V30.8C81.2,24.2,75.8,18.8,69.2,18.8z M72.6,26l1.4,0v1.4v9.2l-10.6,0l0-10.6L72.6,26z M41.1,43.6
							c2-2.8,5.2-4.6,8.9-4.6s6.9,1.8,8.9,4.6c1.3,1.8,2.1,4,2.1,6.4c0,6.1-4.9,11-11,11c-6.1,0-11-4.9-11-11
							C39,47.6,39.8,45.4,41.1,43.6z M75.2,69.2c0,3.3-2.7,6-6,6H30.8c-3.3,0-6-2.7-6-6V43.6h9.3c-0.8,2-1.3,4.1-1.3,6.4
							c0,9.4,7.7,17.1,17.1,17.1S67.1,59.4,67.1,50c0-2.3-0.5-4.4-1.3-6.4h9.3V69.2z"/>
					</g>
				</g>
			</symbol>

			<symbol id="epl-icon-svg-pinterest" class="epl-icon-fill epl-icon-social">
				<g>
					<path id="epl-icon-pinterest-shape-base" class="epl-icon-shape-base epl-icon-color-base" d="M100,75c0,13.8-11.2,25-25,25H25C11.2,100,0,88.8,0,75V25C0,11.2,11.2,0,25,0h50c13.8,0,25,11.2,25,25V75z"/>
					<g>
						<path id="epl-icon-pinterest-shape-hollow" class="epl-icon-color-hollow" d="M35.7,80.9c0.1,0.4,0.6,0.5,0.9,0.2c0.4-0.5,5.1-6.1,6.7-11.7c0.5-1.6,2.6-9.9,2.6-9.9
							c1.3,2.4,5.1,4.5,9.1,4.5c11.9,0,20-10.5,20-24.6c0-10.6-9.3-20.6-23.5-20.6C33.9,18.8,25,31,25,41.2c0,6.2,2.4,11.7,7.6,13.7
							c0.9,0.3,1.6,0,1.9-0.9c0.2-0.6,0.6-2.2,0.8-2.9c0.2-0.9,0.2-1.2-0.5-2c-1.5-1.7-2.5-3.9-2.5-7c0-9.1,7-17.2,18.3-17.2
							c10,0,15.4,5.9,15.4,13.8c0,10.4-4.7,19.1-11.8,19.1c-3.9,0-6.8-3.1-5.9-6.9c1.1-4.5,3.3-9.5,3.3-12.7c0-2.9-1.6-5.4-5-5.4
							c-4,0-7.2,4-7.2,9.3c0,3.4,1.2,5.7,1.2,5.7s-4.1,16.6-4.8,19.6C34.4,73,35.6,80.1,35.7,80.9z"/>
					</g>
				</g>
			</symbol>
		</defs>
	</svg>';

	$social_icons = apply_filters( 'epl_svg_social_icons', $social_icons );

	// Only Load SVG icons if epl_icons_svg_author is on.
	if ( epl_get_option( 'epl_icons_svg_author' ) === 'on' ) {

		$allowed_tags = epl_get_svg_allowed_tags();

		echo wp_kses( $social_icons, $allowed_tags );
	}
}
add_action( 'wp_head', 'epl_load_svg_social_icons_head', 90 );

/**
 * Whitelist display attribute for wp_kses_post
 *
 * @param  string $styles Allowed SVG names.
 *
 * @return array|string
 *
 * @since 3.4
 */
function epl_whitelist_display_attr( $styles ) {

	$styles[] = 'display';
	$styles[] = 'fill';
	return $styles;
}
add_filter( 'safe_style_css', 'epl_whitelist_display_attr' );

/**
 * Svg Allowed tags
 *
 * @since  3.4
 * @since  3.4.17	Allows circle tag.
 */
function epl_get_svg_allowed_tags() {

	$tags = array(
		'svg'     => array(
			'id'              => true,
			'class'           => true,
			'version'         => true,
			'aria-hidden'     => true,
			'aria-labelledby' => true,
			'role'            => true,
			'xmlns'           => true,
			'width'           => true,
			'height'          => true,
			'viewbox'         => true,
			'style'           => true,
			'xmlns:xlink'     => true,
		),
		'g'       => array(
			'id'        => true,
			'fill'      => true,
			'transform' => true,
			'style'     => true,
			'class'     => true,
		),
		'title'   => array( 'title' => true ),
		'path'    => array(
			'd'     => true,
			'fill'  => true,
			'style' => true,
			'id'    => true,
			'class' => true,
		),
		'defs'    => array(
			'id'    => true,
			'class' => true,
			'style' => true,
		),
		'symbol'  => array(
			'id'    => true,
			'class' => true,
			'style' => true,
		),
		'rect'    => array(
			'width'  => true,
			'height' => true,
			'style'  => true,
			'class'  => true,
			'x'      => true,
			'y'      => true,
			'rx'     => true,
			'ry'     => true,
			'fill'   => true,
		),
		'polygon' => array(
			'class'  => true,
			'points' => true,
		),
		'use'     => array(
			'xlink:href' => true,
		),
		'circle'    => array(
			'style'  => true,
			'class'  => true,
			'id'     => true,
			'cx'     => true,
			'cy'     => true,
			'r'      => true
		)

	);
	return apply_filters( 'epl_svg_allowed_tags', $tags );
}
