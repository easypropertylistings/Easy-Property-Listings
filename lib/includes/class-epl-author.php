<?php
/**
 * EPL Author Class. Allows for overriding.
 *
 * @package     EPL
 * @subpackage  Classes/AuthorMeta
 * @copyright   Copyright (c) 2022, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.3.0
 * @since       3.4.39 Allows for overriding.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * EPL_Author Class
 *
 * @since 1.3.0
 */
if ( ! class_exists( 'EPL_Author' ) ) :

	/**
	 * EPL_Author Class
	 *
	 * @since 1.3.0
	 */
	class EPL_Author {

		/**
		 * The user ID
		 *
		 * @since 1.3.0
		 * @var string $author_id User ID.
		 */
		public $author_id;

		/**
		 * The username
		 *
		 * @since 1.3.0
		 * @var string $name User name.
		 */
		public $name;

		/**
		 * The user mobile phone number
		 *
		 * @since 1.3.0
		 * @var string $mobile User mobile number.
		 */
		public $mobile;

		/**
		 * The user office phone number
		 *
		 * @since 3.3.0
		 * @var string $office_phone User office phone.
		 */
		public $office_phone;
		
		/**
		 * The user website
		 *
		 * @since 3.5.15
		 * @var string $website User website.
		 */
		public $website;

		/**
		 * The user Facebook URL
		 *
		 * @since 1.3.0
		 * @var string $facebook User Facebook profile URL.
		 */
		public $facebook;

		/**
		 * The user LinkedIn URL
		 *
		 * @since 1.3.0
		 * @var string $linkedin User LinkedIn profile URL.
		 */
		public $linkedin;

		/**
		 * The user Google Plus URL
		 *
		 * @since 1.3.0
		 * @var string $google User Google Plus profile URL.
		 */
		public $google;

		/**
		 * The user Twitter URL
		 *
		 * @since 1.3.0
		 * @var string $twitter User Twitter ID.
		 */
		public $twitter;

		/**
		 * The user Instagram URL
		 *
		 * @since 3.3.0
		 * @var string $instagram User Instagram profile URL.
		 */
		public $instagram;

		/**
		 * The user Pinterest URL
		 *
		 * @since 3.3.0
		 * @var string $pinterest User Pinterest profile URL.
		 */
		public $pinterest;

		/**
		 * The user YouTube URL
		 *
		 * @since 3.3.0
		 * @var string $youtube User YouTube profile URL.
		 */
		public $youtube;

		/**
		 * The user email address
		 *
		 * @since 1.3.0
		 * @var string $email User email address.
		 */
		public $email;

		/**
		 * The user Skype name
		 *
		 * @since 1.3.0
		 * @var string $skype User Skype ID.
		 */
		public $skype;

		/**
		 * The user text slogan
		 *
		 * @since 1.3.0
		 * @var string $slogan User Slogan text string.
		 */
		public $slogan;

		/**
		 * The user position
		 *
		 * @since 1.3.0
		 * @var string $position User position text string.
		 */
		public $position;

		/**
		 * The user video profile
		 *
		 * @since 1.3.0
		 * @var string $video User video profile URL.
		 */
		public $video;

		/**
		 * The user contact form shortcode
		 *
		 * @since 1.3.0
		 * @var string $contact_form User contact shortcode.
		 */
		public $contact_form;

		/**
		 * The user bio
		 *
		 * @since 1.3.0
		 * @var string $description User bio textarea.
		 */
		public $description;

		/**
		 * Get things started
		 *
		 * @param string $author_id The author WordPress user ID.
		 *
		 * @since 1.3.0
		 * @since 3.5.15 Added website (url)
		 */
		public function __construct( $author_id ) {
			$this->author_id    = $author_id;
			$this->name         = get_the_author_meta( 'display_name', $this->author_id );
			$this->mobile       = get_the_author_meta( 'mobile', $this->author_id );
			$this->office_phone = get_the_author_meta( 'office_phone', $this->author_id );
			$this->website      = get_the_author_meta( 'url', $this->author_id );
			$this->facebook     = get_the_author_meta( 'facebook', $this->author_id );
			$this->linkedin     = get_the_author_meta( 'linkedin', $this->author_id );
			$this->google       = get_the_author_meta( 'google', $this->author_id );
			$this->twitter      = get_the_author_meta( 'twitter', $this->author_id );
			$this->instagram    = get_the_author_meta( 'instagram', $this->author_id );
			$this->pinterest    = get_the_author_meta( 'pinterest', $this->author_id );
			$this->youtube      = get_the_author_meta( 'youtube', $this->author_id );
			$this->email        = get_the_author_meta( 'email', $this->author_id );
			$this->skype        = get_the_author_meta( 'skype', $this->author_id );
			$this->slogan       = get_the_author_meta( 'slogan', $this->author_id );
			$this->position     = get_the_author_meta( 'position', $this->author_id );
			$this->video        = get_the_author_meta( 'video', $this->author_id );
			$this->contact_form = get_the_author_meta( 'contact-form', $this->author_id );
			$this->description  = get_the_author_meta( 'description', $this->author_id );
		}

		/**
		 * Get the global property object
		 *
		 * @param array $property Array of property object.
		 *
		 * @return bool|mixed $return Array of values.
		 * @since 1.3.0
		 */
		public function __get( $property ) {
			$prop_val  = ! empty( $this->{$property} ) ? $this->{$property} : false;
			$prop_meta = get_user_meta( $this->author_id, $property, true );

			if ( ! empty( $prop_val ) ) {
				return $prop_val;

			} elseif ( ! empty( $prop_meta ) ) {
				return $prop_meta;
			}
		}

		/**
		 * Author Name
		 *
		 * @since 1.3.0
		 */
		public function get_author_name() {
			if ( ! empty( $this->name ) ) {
				return apply_filters( 'epl_author_name', $this->name, $this );
			}
		}

		/**
		 * Get Email
		 *
		 * @since 3.2.0
		 */
		public function get_email() {
			if ( ! empty( $this->email ) ) {
				return apply_filters( 'epl_author_email', $this->email, $this );
			}
		}

		/**
		 * Author Email html Box
		 *
		 * @param string $html String of html.
		 * @param string $style Option of returntype.
		 *
		 * @return mixed|string|void
		 * @since 1.3.0
		 */
		public function get_email_html( $html = '', $style = 'i' ) {

			if ( ! empty( $this->email ) ) {

				$style = 'i' === $style && 'on' === epl_get_option( 'epl_icons_svg_author' ) ? 's' : $style;

				if ( 'i' === $style ) {
					$html = '
						<a class="epl-author-icon author-icon email-icon-24"
							href="mailto:' . $this->get_email() . '" title="' . __( 'Contact', 'easy-property-listings' ) . ' ' . $this->get_author_name() . ' ' . __( 'by Email', 'easy-property-listings' ) . '">' .
							apply_filters( 'epl_author_icon_email', __( 'Email', 'easy-property-listings' ) ) .
							'</a>';
				} else {
					$svg  = '<svg viewBox="0 0 100 100" class="epl-icon-svg-email"><use xlink:href="#epl-icon-svg-email"></use></svg>';
					$html =
						'<div class="epl-icon-svg-container epl-icon-container-email">
							<a class="epl-author-icon-svg author-icon-svg email-icon"
								href="mailto:' . $this->get_email() . '" title="' . __( 'Contact', 'easy-property-listings' ) . ' ' . $this->get_author_name() . ' ' . __( 'by Email', 'easy-property-listings' ) . '">' . $svg .
							'</a>
						</div>';
				}
			}
			$html = apply_filters( 'epl_author_email_html', $html, $this );
			return $html;
		}
		
		/**
		 * Get website
		 *
		 * @since 3.5.15
		 */
		public function get_website() {
			if ( ! empty( $this->website ) ) {
				return apply_filters( 'epl_author_website', $this->website, $this );
			}
		}
		
		/**
		 * Author website html Box
		 *
		 * @param string $html String of html.
		 * @param string $style Option of returntype.
		 *
		 * @return mixed|string|void
		 *
		 * @since 3.5.15
		 */
		public function get_website_html( $html = '', $style = 'i' ) {
		
			if ( ! empty( $this->website ) ) {
		
				$style = 'i' === $style && 'on' === epl_get_option( 'epl_icons_svg_author' ) ? 's' : $style;
		
				if ( 'i' === $style ) {
					$html = '
						<a class="epl-author-icon author-icon website-icon-24"
							href="' . $this->get_website() . '" title="' . __( 'Contact', 'easy-property-listings' ) . ' ' . $this->get_author_name() . ' ' . __( 'by Website', 'easy-property-listings' ) . '">' .
							apply_filters( 'epl_author_icon_website', __( 'Website', 'easy-property-listings' ) ) .
							'</a>';
				} else {
					$svg  = '<svg viewBox="0 0 100 100" class="epl-icon-svg-website"><use xlink:href="#epl-icon-svg-website"></use></svg>';
					$html =
						'<div class="epl-icon-svg-container epl-icon-container-website">
							<a class="epl-author-icon-svg author-icon-svg website-icon"
								href="' . $this->get_website() . '" title="' . __( 'Contact', 'easy-property-listings' ) . ' ' . $this->get_author_name() . ' ' . __( 'by Website', 'easy-property-listings' ) . '">' . $svg .
							'</a>
						</div>';
				}
			}
			$html = apply_filters( 'epl_author_website_html', $html, $this );
			return $html;
		}

		/**
		 * Get Twitter
		 *
		 * @since 3.2.0
		 */
		public function get_twitter() {

			$twitter = '';
			if ( ! empty( $this->twitter ) ) {

				if ( ( strpos( $this->twitter, 'http://' ) === 0 ) || ( strpos( $this->twitter, 'https://' ) === 0 ) ) {
					// Absolute url.
					$twitter = $this->twitter;

				} else {
					// Relative url.
					$twitter = 'http://twitter.com/' . $this->twitter;
				}
			}
			return apply_filters( 'epl_author_twitter', $twitter, $this );
		}

		/**
		 * Get Instagram
		 *
		 * @since 3.3.0
		 */
		public function get_instagram() {

			$instagram = '';
			if ( ! empty( $this->instagram ) ) {

				if ( ( strpos( $this->instagram, 'http://' ) === 0 ) || ( strpos( $this->instagram, 'https://' ) === 0 ) ) {
					// Absolute url.
					$instagram = $this->instagram;

				} else {
					// Relative url.
					$instagram = 'http://instagram.com/' . $this->instagram;
				}
			}
			return apply_filters( 'epl_author_instagram', $instagram, $this );
		}

		/**
		 * Get YouTube
		 *
		 * @since 3.3.0
		 */
		public function get_youtube() {

			$youtube = '';
			if ( ! empty( $this->youtube ) ) {

				if ( ( strpos( $this->youtube, 'http://' ) === 0 ) || ( strpos( $this->youtube, 'https://' ) === 0 ) ) {
					// Absolute url.
					$youtube = $this->youtube;

				} else {
					// Relative url.
					$youtube = 'http://youtube.com/' . $this->youtube;
				}
			}
			return apply_filters( 'epl_author_youtube', $youtube, $this );
		}

		/**
		 * Get Pinterest
		 *
		 * @since 3.3.0
		 */
		public function get_pinterest() {

			$pinterest = '';
			if ( ! empty( $this->pinterest ) ) {

				if ( ( strpos( $this->pinterest, 'http://' ) === 0 ) || ( strpos( $this->pinterest, 'https://' ) === 0 ) ) {
					// Absolute url.
					$pinterest = $this->pinterest;

				} else {
					// Relative url.
					$pinterest = 'http://pinterest.com/' . $this->pinterest;
				}
			}
			return apply_filters( 'epl_author_pinterest', $pinterest, $this );
		}

		/**
		 * Author Twitter html Box
		 *
		 * @param string $html String of html.
		 * @param string $style Option of returntype.
		 *
		 * @return mixed|string|void
		 * @since 1.3.0
		 * @since 3.5.0 Switched to Twitter X icon.
		 */
		public function get_twitter_html( $html = '', $style = 'i' ) {

			$link_target = defined( 'EPL_SOCIAL_LINK_TARGET_BLANK' ) && EPL_SOCIAL_LINK_TARGET_BLANK ? 'target="_blank" ' : '';

			if ( '' !== $this->get_twitter() ) {

				$style = 'i' === $style && 'on' === epl_get_option( 'epl_icons_svg_author' ) ? 's' : $style;

				if ( 'i' === $style ) {
					$html = '
						<a class="epl-author-icon author-icon twitter-icon-24"
							href="' . $this->get_twitter() . '" title="' . __( 'Follow', 'easy-property-listings' ) . ' ' . $this->get_author_name() . ' ' . __( 'on Twitter', 'easy-property-listings' ) . '"' . $link_target . '>' .
							apply_filters( 'epl_author_icon_twitter', __( 'Twitter', 'easy-property-listings' ) ) .
						'</a>';
				} else {
					$svg  = '<svg viewBox="0 0 100 100" class="epl-icon-svg-twitter-x"><use xlink:href="#epl-icon-svg-twitter-x"></use></svg>';
					$html =
						'<div class="epl-icon-svg-container epl-icon-container-twitter">
							<a class="epl-author-icon-svg author-icon-svg twitter-icon"
								href="' . $this->get_twitter() . '" title="' . __( 'Follow', 'easy-property-listings' ) . ' ' . $this->get_author_name() . ' ' . __( 'on Twitter', 'easy-property-listings' ) . '"' . $link_target . '>' . $svg .
							'</a>
						</div>';
				}
			}
			$html = apply_filters( 'epl_author_twitter_html', $html, $this );
			return $html;
		}

		/**
		 * Author Instagram html Box
		 *
		 * @param string $html String of html.
		 * @param string $style Option of returntype.
		 *
		 * @return mixed|string|void
		 * @since 3.3.0
		 */
		public function get_instagram_html( $html = '', $style = 'i' ) {

			$link_target = defined( 'EPL_SOCIAL_LINK_TARGET_BLANK' ) && EPL_SOCIAL_LINK_TARGET_BLANK ? 'target="_blank" ' : '';

			if ( '' !== $this->get_instagram() ) {

				$style = 'i' === $style && 'on' === epl_get_option( 'epl_icons_svg_author' ) ? 's' : $style;

				if ( 'i' === $style ) {
					$html = '
						<a class="epl-author-icon author-icon instagram-icon-24"
							href="' . $this->get_instagram() . '" title="' . __( 'Follow', 'easy-property-listings' ) . ' ' . $this->get_author_name() . ' ' . __( 'on Instagram', 'easy-property-listings' ) . '"' . $link_target . '>' .
							apply_filters( 'epl_author_icon_instagram', __( 'Instagram', 'easy-property-listings' ) ) .
						'</a>';
				} else {
					$svg  = '<svg viewBox="0 0 100 100" class="epl-icon-svg-instagram"><use xlink:href="#epl-icon-svg-instagram"></use></svg>';
					$html =
						'<div class="epl-icon-svg-container epl-icon-container-instagram">
							<a class="epl-author-icon-svg author-icon-svg instagram-icon"
								href="' . $this->get_instagram() . '" title="' . __( 'Follow', 'easy-property-listings' ) . ' ' . $this->get_author_name() . ' ' . __( 'on Instagram', 'easy-property-listings' ) . '"' . $link_target . '>' . $svg .
							'</a>
						</div>';
				}
			}
			$html = apply_filters( 'epl_author_instagram_html', $html, $this );
			return $html;
		}

		/**
		 * Author YouTube html Box
		 *
		 * @param string $html String of html.
		 * @param string $style Option of returntype.
		 *
		 * @return mixed|string|void
		 * @since 3.3.0
		 */
		public function get_youtube_html( $html = '', $style = 'i' ) {

			$link_target = defined( 'EPL_SOCIAL_LINK_TARGET_BLANK' ) && EPL_SOCIAL_LINK_TARGET_BLANK ? 'target="_blank" ' : '';

			if ( '' !== $this->get_youtube() ) {

				$style = 'i' === $style && 'on' === epl_get_option( 'epl_icons_svg_author' ) ? 's' : $style;

				if ( 'i' === $style ) {
					$html = '
						<a class="epl-author-icon author-icon youtube-icon-24"
							href="' . $this->get_youtube() . '" title="' . __( 'Follow', 'easy-property-listings' ) . ' ' . $this->get_author_name() . ' ' . __( 'on YouTube', 'easy-property-listings' ) . '"' . $link_target . '>' .
							apply_filters( 'epl_author_icon_youtube', __( 'YouTube', 'easy-property-listings' ) ) .
						'</a>';
				} else {
					$svg  = '<svg viewBox="0 0 100 100" class="epl-icon-svg-youtube"><use xlink:href="#epl-icon-svg-youtube"></use></svg>';
					$html =
						'<div class="epl-icon-svg-container epl-icon-container-youtube">
							<a class="epl-author-icon-svg author-icon-svg youtube-icon"
								href="' . $this->get_youtube() . '" title="' . __( 'Follow', 'easy-property-listings' ) . ' ' . $this->get_author_name() . ' ' . __( 'on YouTube', 'easy-property-listings' ) . '"' . $link_target . '>' . $svg .
							'</a>
						</div>';
				}
			}
			$html = apply_filters( 'epl_author_youtube_html', $html, $this );
			return $html;
		}

		/**
		 * Author Pinterest html Box
		 *
		 * @param string $html String of html.
		 * @param string $style Option of returntype.
		 *
		 * @return mixed|string|void
		 * @since 3.3.0
		 */
		public function get_pinterest_html( $html = '', $style = 'i' ) {

			$link_target = defined( 'EPL_SOCIAL_LINK_TARGET_BLANK' ) && EPL_SOCIAL_LINK_TARGET_BLANK ? 'target="_blank" ' : '';

			if ( '' !== $this->get_pinterest() ) {

				$style = 'i' === $style && 'on' === epl_get_option( 'epl_icons_svg_author' ) ? 's' : $style;

				if ( 'i' === $style ) {
					$html = '
						<a class="epl-author-icon author-icon pinterest-icon-24"
							href="' . $this->get_pinterest() . '" title="' . __( 'Follow', 'easy-property-listings' ) . ' ' . $this->get_author_name() . ' ' . __( 'on Pinterest', 'easy-property-listings' ) . '"' . $link_target . '>' .
							apply_filters( 'epl_author_icon_pinterest', __( 'Pinterest', 'easy-property-listings' ) ) .
						'</a>';
				} else {
					$svg  = '<svg viewBox="0 0 100 100" class="epl-icon-svg-pinterest"><use xlink:href="#epl-icon-svg-pinterest"></use></svg>';
					$html =
						'<div class="epl-icon-svg-container epl-icon-container-pinterest">
							<a class="epl-author-icon-svg author-icon-svg pinterest-icon"
								href="' . $this->get_instagram() . '" title="' . __( 'Follow', 'easy-property-listings' ) . ' ' . $this->get_author_name() . ' ' . __( 'on Pinterest', 'easy-property-listings' ) . '"' . $link_target . '>' . $svg .
							'</a>
						</div>';
				}
			}
			$html = apply_filters( 'epl_author_pinterest_html', $html, $this );
			return $html;
		}

		/**
		 * Get Google
		 *
		 * @since 3.2.0
		 * @since 3.3.0 Depreciated as Google Plus no longer exists.
		 */
		public function get_google() {

			$google = '';
			if ( ! empty( $this->google ) ) {

				if ( ( strpos( $this->google, 'http://' ) === 0 ) || ( strpos( $this->google, 'https://' ) === 0 ) ) {
					// absolute url.
					$google = $this->google;

				} else {
					// relative url.
					$google = 'http://plus.google.com/' . $this->google;
				}
			}
			return apply_filters( 'epl_author_google', $google, $this );
		}

		/**
		 * Author Google html Box
		 *
		 * @param string $html String of html.
		 * @param string $style Option of returntype.
		 *
		 * @return mixed|string|void
		 *
		 * @since 1.3.0
		 * @since 3.3.0 Depreciated as Google Plus no longer exists.
		 */
		public function get_google_html( $html = '', $style = 'i' ) {

			$link_target = defined( 'EPL_SOCIAL_LINK_TARGET_BLANK' ) && EPL_SOCIAL_LINK_TARGET_BLANK ? 'target="_blank" ' : '';

			if ( '' !== $this->get_google() ) {

				$style = 'i' === $style && 'on' === epl_get_option( 'epl_icons_svg_author' ) ? 's' : $style;

				if ( 'i' === $style ) {
					$html = '
						<a class="epl-author-icon author-icon google-icon-24"
							href="' . $this->get_google() . '" title="' . __( 'Follow', 'easy-property-listings' ) . ' ' . $this->get_author_name() . ' ' . __( 'on Google', 'easy-property-listings' ) . '"' . $link_target . '>' .
							apply_filters( 'epl_author_icon_google', __( 'Google', 'easy-property-listings' ) ) .
						'</a>';
				} else {
					$svg  = '<svg viewBox="0 0 100 100" class="epl-icon-svg-google-plus"><use xlink:href="#epl-icon-svg-google-plus"></use></svg>';
					$html =
						'<div class="epl-icon-svg-container epl-icon-container-google-plus">
							<a class="epl-author-icon-svg author-icon-svg google-plus-icon"
								href="' . $this->get_google() . '" title="' . __( 'Follow', 'easy-property-listings' ) . ' ' . $this->get_author_name() . ' ' . __( 'on Google', 'easy-property-listings' ) . '"' . $link_target . '>' . $svg .
							'</a>
						</div>';
				}
			}
			$html = apply_filters( 'epl_author_google_html', $html, $this );
			return $html;
		}

		/**
		 * Get Facebook
		 *
		 * @since 3.2.0
		 */
		public function get_facebook() {

			$facebook = '';

			if ( ! empty( $this->facebook ) ) {

				if ( ( strpos( $this->facebook, 'http://' ) === 0 ) || ( strpos( $this->facebook, 'https://' ) === 0 ) ) {
					// absolute url.
					$facebook = $this->facebook;

				} else {
					// relative url.
					$facebook = 'http://facebook.com/' . $this->facebook;
				}
			}
			return apply_filters( 'epl_author_facebook', $facebook, $this );
		}

		/**
		 * Author Facebook html Box
		 *
		 * @param string $html String of html.
		 * @param string $style Option of returntype.
		 *
		 * @return mixed|string|void
		 * @since 1.3.0
		 */
		public function get_facebook_html( $html = '', $style = 'i' ) {

			$link_target = defined( 'EPL_SOCIAL_LINK_TARGET_BLANK' ) && EPL_SOCIAL_LINK_TARGET_BLANK ? 'target="_blank" ' : '';

			if ( '' !== $this->get_facebook() ) {

				$style = 'i' === $style && 'on' === epl_get_option( 'epl_icons_svg_author' ) ? 's' : $style;

				if ( 'i' === $style ) {
					$html = '
						<a class="epl-author-icon author-icon facebook-icon-24"
							href="' . $this->get_facebook() . '" title="' . __( 'Follow', 'easy-property-listings' ) . ' ' . $this->get_author_name() . ' ' . __( 'on Facebook', 'easy-property-listings' ) . '"' . $link_target . '>' .
							apply_filters( 'epl_author_icon_facebook', __( 'Facebook', 'easy-property-listings' ) ) .
						'</a>';
				} else {
					$svg  = '<svg viewBox="0 0 100 100" class="epl-icon-svg-facebook"><use xlink:href="#epl-icon-svg-facebook"></use></svg>';
					$html =
						'<div class="epl-icon-svg-container epl-icon-container-facebook">
							<a class="epl-author-icon-svg author-icon-svg facebook-icon"
								href="' . $this->get_facebook() . '" title="' . __( 'Follow', 'easy-property-listings' ) . ' ' . $this->get_author_name() . ' ' . __( 'on Facebook', 'easy-property-listings' ) . '"' . $link_target . '>' . $svg .
							'</a>
						</div>';
				}
			}
			$html = apply_filters( 'epl_author_facebook_html', $html, $this );
			return $html;
		}

		/**
		 * Get linkedin
		 *
		 * @since 3.2.0
		 */
		public function get_linkedin() {

			$linkedin = '';

			if ( ! empty( $this->linkedin ) ) {

				if ( ( strpos( $this->linkedin, 'http://' ) === 0 ) || ( strpos( $this->linkedin, 'https://' ) === 0 ) ) {
					// absolute url.
					$linkedin = $this->linkedin;

				} else {
					// relative url.
					$linkedin = 'http://linkedin.com/pub/' . $this->linkedin;
				}
			}
			return apply_filters( 'epl_author_linkedin', $linkedin, $this );
		}

		/**
		 * Author LinkedIn html Box
		 *
		 * @param string $html String of html.
		 * @param string $style Option of returntype.
		 *
		 * @return mixed|string|void
		 * @since 1.3.0
		 */
		public function get_linkedin_html( $html = '', $style = 'i' ) {

			$link_target = defined( 'EPL_SOCIAL_LINK_TARGET_BLANK' ) && EPL_SOCIAL_LINK_TARGET_BLANK ? 'target="_blank" ' : '';

			if ( '' !== $this->get_linkedin() ) {

				$style = 'i' === $style && 'on' === epl_get_option( 'epl_icons_svg_author' ) ? 's' : $style;

				if ( 'i' === $style ) {

					$html = '
						<a class="epl-author-icon author-icon linkedin-icon-24"
							href="' . $this->get_linkedin() . '" title="' . __( 'Follow', 'easy-property-listings' ) . ' ' . $this->get_author_name() . ' ' . __( 'on LinkedIn', 'easy-property-listings' ) . '"' . $link_target . '>' .
							apply_filters( 'epl_author_icon_linkedin', __( 'LinkedIn', 'easy-property-listings' ) ) .
						'</a>';
				} else {
					$svg  = '<svg viewBox="0 0 100 100" class="epl-icon-svg-linkedin"><use xlink:href="#epl-icon-svg-linkedin"></use></svg>';
					$html =
						'<div class="epl-icon-svg-container epl-icon-container-linkedin">
							<a class="epl-author-icon-svg author-icon-svg linkedin-icon"
								href="' . $this->get_linkedin() . '" title="' . __( 'Follow', 'easy-property-listings' ) . ' ' . $this->get_author_name() . ' ' . __( 'on LinkedIn', 'easy-property-listings' ) . '"' . $link_target . '>' . $svg .
							'</a>
						</div>';
				}
			}
			$html = apply_filters( 'epl_author_linkedin_html', $html, $this );
			return $html;
		}

		/**
		 * Get skype
		 *
		 * @since 3.2.0
		 */
		public function get_skype() {

			$skype = '';
			if ( ! empty( $this->skype ) ) {

				if ( ( strpos( $this->skype, 'skype:' ) === 0 ) ) {
					// Absolute url.
					$skype = $this->skype;

				} else {
					// Relative url.
					$skype = 'skype:' . $this->skype;
				}
			}
			return apply_filters( 'epl_author_skype', $skype, $this );
		}

		/**
		 * Author Skype html Box
		 *
		 * @param string $html String of html.
		 * @param string $style Option of returntype.
		 *
		 * @return mixed|string|void
		 * @since 1.3.0
		 */
		public function get_skype_html( $html = '', $style = 'i' ) {

			$link_target = defined( 'EPL_SOCIAL_LINK_TARGET_BLANK' ) && EPL_SOCIAL_LINK_TARGET_BLANK ? 'target="_blank" ' : '';

			if ( '' !== $this->get_skype() ) {

				$style = 'i' === $style && 'on' === epl_get_option( 'epl_icons_svg_author' ) ? 's' : $style;

				if ( 'i' === $style ) {

					$html = '
						<a class="epl-author-icon author-icon skype-icon-24"
							href="' . $this->get_skype() . '" title="' . __( 'Follow', 'easy-property-listings' ) . ' ' . $this->get_author_name() . ' ' . __( 'on Skype', 'easy-property-listings' ) . '"' . $link_target . '>' .
							apply_filters( 'epl_author_icon_skype', __( 'Skype', 'easy-property-listings' ) ) .
						'</a>';
				} else {
					$svg  = '<svg viewBox="0 0 100 100" class="epl-icon-svg-skype"><use xlink:href="#epl-icon-svg-skype"></use></svg>';
					$html =
						'<div class="epl-icon-svg-container epl-icon-container-skype">
							<a class="epl-author-icon-svg author-icon-svg skype-icon"
								href="' . $this->get_skype() . '" title="' . __( 'Follow', 'easy-property-listings' ) . ' ' . $this->get_author_name() . ' ' . __( 'on Skype', 'easy-property-listings' ) . '"' . $link_target . '>' . $svg .
							'</a>
						</div>';
				}
			}
			$html = apply_filters( 'epl_author_skype_html', $html, $this );
			return $html;
		}

		/**
		 * Author video html Box
		 *
		 * @param string $html String of video embed.
		 *
		 * @return mixed|void
		 * @since 1.3.0
		 */
		public function get_video_html( $html = '' ) {
			if ( ! empty( $this->video ) ) {
				$video = apply_filters( 'epl_author_video_html', $this->video, $this );
				$html  = wp_oembed_get( $video );
			}
			return apply_filters( 'epl_author_video', $html, $this );
		}

		/**
		 * Get description
		 *
		 * @since 3.2.0
		 */
		public function get_description() {
			if ( ! empty( $this->description ) ) {
				return apply_filters( 'epl_author_description', $this->description, $this );
			}
		}

		/**
		 * Author description html
		 *
		 * @param string $html String of html output.
		 *
		 * @return mixed|void
		 * @since 1.3.0
		 */
		public function get_description_html( $html = '' ) {

			$desc_html = $this->get_description();

			if ( ! empty( $desc_html ) ) {

				$permalink = apply_filters( 'epl_author_profile_link', get_author_posts_url( $this->author_id ), $this );

				$html = '
				<div class="epl-author-content author-content">' . $this->get_description() . '</div>
					<span class="bio-more">
						<a href="' . $permalink . '">' .
							apply_filters( 'epl_author_read_more_label', __( 'Read More', 'easy-property-listings' ) ) . '
						</a>
					</span>
			';
			}
			return apply_filters( 'epl_author_description_html', $html, $this );
		}

		/**
		 * Author mobile
		 *
		 * @since 1.3.0
		 */
		public function get_author_mobile() {
			if ( ! empty( $this->mobile ) ) {
				return apply_filters( 'epl_author_mobile', $this->mobile, $this );
			}
		}

		/**
		 * Author office phone
		 *
		 * @since 3.3.0
		 */
		public function get_author_office_phone() {
			if ( ! empty( $this->office_phone ) ) {
				return apply_filters( 'epl_author_office_phone', $this->office_phone, $this );
			}
		}

		/**
		 * Author ID
		 *
		 * @since 1.3.0
		 */
		public function get_author_id() {
			if ( ! empty( $this->author_id ) ) {
				return apply_filters( 'epl_author_id', $this->author_id, $this );
			}
		}

		/**
		 * Author Slogan
		 *
		 * @since 1.3.0
		 */
		public function get_author_slogan() {
			if ( ! empty( $this->slogan ) ) {
				return apply_filters( 'epl_author_slogan', $this->slogan, $this );
			}
		}

		/**
		 * Author Position
		 *
		 * @since 1.3.0
		 */
		public function get_author_position() {
			if ( ! empty( $this->position ) ) {
				return apply_filters( 'epl_author_position', $this->position, $this );
			}
		}

		/**
		 * Author Contact Form
		 *
		 * @since 1.3.0
		 */
		public function get_author_contact_form() {
			if ( ! empty( $this->contact_form ) ) {
				$contact_form = apply_filters( 'epl_author_contact_form', $this->contact_form, $this );
				return do_shortcode( $contact_form );
			}
		}
	}
endif;
