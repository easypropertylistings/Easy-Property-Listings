<?php
/**
 * EPL Admin Functions
 *
 * @package     EPL
 * @subpackage  Classes/Author
 * @copyright   Copyright (c) 2019, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.3
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * EPL_Author_Meta Class
 *
 * @since 1.3
 */
if( !class_exists('EPL_Author_Meta') ) :

	class EPL_Author_Meta {

		private $author_id;
		private $name;
		private $mobile;
		private $office_phone;
		private $facebook;
		private $linkedin;
		private $google;
		private $twitter;
		private $instagram;
		private $pinterest;
		private $youtube;
		private $email;
		private $skype;
		private $slogan;
		private $position;
		private $video;
		private $contact_form;
		private $description;

		/**
		 *
		 * @param [type]
		 */
		function __construct($author_id) {
			$this->author_id 	= $author_id;
			$this->name 		= get_the_author_meta( 'display_name' , $this->author_id);
			$this->mobile 		= get_the_author_meta( 'mobile' , $this->author_id);
			$this->office_phone 	= get_the_author_meta( 'office_phone' , $this->author_id);
			$this->facebook 	= get_the_author_meta( 'facebook' , $this->author_id);
			$this->linkedin 	= get_the_author_meta( 'linkedin' , $this->author_id);
			$this->google 		= get_the_author_meta( 'google' , $this->author_id);
			$this->twitter 		= get_the_author_meta( 'twitter' , $this->author_id);
			$this->instagram 	= get_the_author_meta( 'instagram' , $this->author_id);
			$this->pinterest 	= get_the_author_meta( 'pinterest' , $this->author_id);
			$this->youtube 		= get_the_author_meta( 'youtube' , $this->author_id);
			$this->email 		= get_the_author_meta( 'email' , $this->author_id);
			$this->skype 		= get_the_author_meta( 'skype' , $this->author_id);
			$this->slogan 		= get_the_author_meta( 'slogan' , $this->author_id);
			$this->position 	= get_the_author_meta( 'position' , $this->author_id);
			$this->video 		= get_the_author_meta( 'video' , $this->author_id);
			$this->contact_form 	= get_the_author_meta( 'contact-form' , $this->author_id);
			$this->description 	= get_the_author_meta( 'description' , $this->author_id);
		}

		/**
		* @param  [type]
		* @return [type]
		*/
		function __get($property) {
			if(isset($this->{$property}) && $this->{$property} != ''){
				return $this->{$property};
			} elseif( $return = get_user_meta($this->author_id,$property,true) ) {
				return $return;
			}
		}

		/**
		 * Author Name
		 *
		 * @since version 1.3
		 */
		function get_author_name() {
			if($this->name != '')
				return apply_filters('epl_author_name',$this->name,$this);
		}

		/**
		 * Get Email
		 *
		 * @since version 3.2
		 */
		function get_email() {
			if($this->email != '')
				return apply_filters('epl_author_email',$this->email,$this);
		}

		/**
		 * Author Email html Box
		 *
		 * @since version 1.3
		 */
		function get_email_html( $html = '' , $style = 'i' ) {

			if ( $this->email != '' ) {

				$style	=	$style == 'i' && epl_get_option('epl_icons_svg_author') == 'on' ? 's' : $style;

				if ( $style == 'i' ) {
					$html	= '
						<a class="epl-author-icon author-icon email-icon-24"
							href="mailto:' . $this->get_email() . '" title="'.__('Contact', 'easy-property-listings' ).' '.$this->get_author_name().' '.__('by Email', 'easy-property-listings' ).'">'.
							apply_filters( 'epl_author_icon_email' , __('Email', 'easy-property-listings' )).
							'</a>';
				} else {
					$svg	= '<svg viewBox="0 0 100 100" class="epl-icon-svg-email"><use xlink:href="#epl-icon-svg-email"></use></svg>';
					$html	=
						'<div class="epl-icon-svg-container epl-icon-container-email">
							<a class="epl-author-icon-svg author-icon-svg email-icon"
								href="mailto:' . $this->get_email() . '" title="'.__('Contact', 'easy-property-listings' ).' '.$this->get_author_name().' '.__('by Email', 'easy-property-listings' ).'">' . $svg .
							'</a>
						</div>';
				}
			}
			$html = apply_filters('epl_author_email_html',$html,$this);
			return $html ;
		}

		/**
		 * Get Twitter
		 *
		 * @since version 3.2
		 */
		function get_twitter() {

			$twitter = '';
			if($this->twitter != '') {

				if( (strpos($this->twitter,'http://' ) === 0 ) || (strpos($this->twitter,'https://' ) === 0 ) ) {
					// absolute url
					$twitter = $this->twitter;

				} else {
					// relative url
					$twitter = 'http://twitter.com/' . $this->twitter;
				}

			}
			return apply_filters('epl_author_twitter',$twitter,$this);
		}

		/**
		 * Get Instagram
		 *
		 * @since version 3.3
		 */
		function get_instagram() {

			$instagram = '';
			if($this->instagram != '') {

				if( (strpos($this->instagram,'http://' ) === 0 ) || (strpos($this->instagram,'https://' ) === 0 ) ) {
					// absolute url
					$instagram = $this->instagram;

				} else {
					// relative url
					$instagram = 'http://instagram.com/' . $this->instagram;
				}

			}
			return apply_filters('epl_author_instagram',$instagram,$this);
		}

		/**
		 * Get YouTube
		 *
		 * @since version 3.3
		 */
		function get_youtube() {

			$youtube = '';
			if($this->youtube != '') {

				if( (strpos($this->youtube,'http://' ) === 0 ) || (strpos($this->youtube,'https://' ) === 0 ) ) {
					// absolute url
					$youtube = $this->youtube;

				} else {
					// relative url
					$youtube = 'http://youtube.com/' . $this->youtube;
				}

			}
			return apply_filters('epl_author_youtube',$youtube,$this);
		}

		/**
		 * Get Pinterest
		 *
		 * @since version 3.3
		 */
		function get_pinterest() {

			$pinterest = '';
			if($this->pinterest != '') {

				if( (strpos($this->pinterest,'http://' ) === 0 ) || (strpos($this->pinterest,'https://' ) === 0 ) ) {
					// absolute url
					$pinterest = $this->pinterest;

				} else {
					// relative url
					$pinterest = 'http://pinterest.com/' . $this->pinterest;
				}

			}
			return apply_filters('epl_author_pinterest',$pinterest,$this);
		}

		/*
		 * Author Twitter html Box
		 *
		 * @since version 1.3
		 */
		function get_twitter_html( $html = '' , $style = 'i' ){

			$link_target = defined( 'EPL_SOCIAL_LINK_TARGET_BLANK' ) && EPL_SOCIAL_LINK_TARGET_BLANK ? 'target="_blank" ' : '';

			if ( $this->get_twitter() != '' ) {

				$style	=	$style == 'i' && epl_get_option('epl_icons_svg_author') == 'on' ? 's' : $style;

				if ( $style == 'i' ) {
					$html	= '
						<a class="epl-author-icon author-icon twitter-icon-24"
							href="' . $this->get_twitter() . '" title="'.__('Follow', 'easy-property-listings' ).' '.$this->get_author_name().' '.__('on Twitter', 'easy-property-listings' ). '"' . $link_target . '>'.
							apply_filters( 'epl_author_icon_twitter' , __('Twitter', 'easy-property-listings' )).
						'</a>';
				} else {
					$svg	= '<svg viewBox="0 0 100 100" class="epl-icon-svg-twitter"><use xlink:href="#epl-icon-svg-twitter"></use></svg>';
					$html	=
						'<div class="epl-icon-svg-container epl-icon-container-twitter">
							<a class="epl-author-icon-svg author-icon-svg twitter-icon"
								href="' . $this->get_twitter() . '" title="'.__('Follow', 'easy-property-listings' ).' '.$this->get_author_name().' '.__('on Twitter', 'easy-property-listings' ).'"' . $link_target . '>' . $svg .
							'</a>
						</div>';
				}
			}
			$html = apply_filters('epl_author_twitter_html',$html,$this);
			return $html;
		}

		/*
		 * Author Instagram html Box
		 *
		 * @since version 3.3
		 */
		function get_instagram_html( $html = '' , $style = 'i' ){

			$link_target = defined( 'EPL_SOCIAL_LINK_TARGET_BLANK' ) && EPL_SOCIAL_LINK_TARGET_BLANK ? 'target="_blank" ' : '';

			if ( $this->get_instagram() != '' ) {

				$style	=	$style == 'i' && epl_get_option('epl_icons_svg_author') == 'on' ? 's' : $style;

				if ( $style == 'i' ) {
					$html	= '
						<a class="epl-author-icon author-icon instagram-icon-24"
							href="' . $this->get_instagram() . '" title="'.__('Follow', 'easy-property-listings' ).' '.$this->get_author_name().' '.__('on Instagram', 'easy-property-listings' ). '"' . $link_target . '>'.
							apply_filters( 'epl_author_icon_instagram' , __('Instagram', 'easy-property-listings' )).
						'</a>';
				} else {
					$svg	= '<svg viewBox="0 0 100 100" class="epl-icon-svg-instagram"><use xlink:href="#epl-icon-svg-instagram"></use></svg>';
					$html	=
						'<div class="epl-icon-svg-container epl-icon-container-instagram">
							<a class="epl-author-icon-svg author-icon-svg instagram-icon"
								href="' . $this->get_instagram() . '" title="'.__('Follow', 'easy-property-listings' ).' '.$this->get_author_name().' '.__('on Instagram', 'easy-property-listings' ).'"' . $link_target . '>' . $svg .
							'</a>
						</div>';
				}
			}
			$html = apply_filters('epl_author_instagram_html',$html,$this);
			return $html;
		}

		/*
		 * Author Youtube html Box
		 *
		 * @since version 3.3
		 */
		function get_youtube_html( $html = '' , $style = 'i' ){

			$link_target = defined( 'EPL_SOCIAL_LINK_TARGET_BLANK' ) && EPL_SOCIAL_LINK_TARGET_BLANK ? 'target="_blank" ' : '';

			if ( $this->get_youtube() != '' ) {

				$style	=	$style == 'i' && epl_get_option('epl_icons_svg_author') == 'on' ? 's' : $style;

				if ( $style == 'i' ) {
					$html	= '
						<a class="epl-author-icon author-icon youtube-icon-24"
							href="' . $this->get_youtube() . '" title="'.__('Follow', 'easy-property-listings' ).' '.$this->get_author_name().' '.__('on YouTube', 'easy-property-listings' ). '"' . $link_target . '>'.
							apply_filters( 'epl_author_icon_youtube' , __('YouTube', 'easy-property-listings' )).
						'</a>';
				} else {
					$svg	= '<svg viewBox="0 0 100 100" class="epl-icon-svg-youtube"><use xlink:href="#epl-icon-svg-youtube"></use></svg>';
					$html	=
						'<div class="epl-icon-svg-container epl-icon-container-youtube">
							<a class="epl-author-icon-svg author-icon-svg youtube-icon"
								href="' . $this->get_youtube() . '" title="'.__('Follow', 'easy-property-listings' ).' '.$this->get_author_name().' '.__('on YouTube', 'easy-property-listings' ).'"' . $link_target . '>' . $svg .
							'</a>
						</div>';
				}
			}
			$html = apply_filters('epl_author_youtube_html',$html,$this);
			return $html;
		}

		/*
		 * Author Pinterest html Box
		 *
		 * @since version 3.3
		 */
		function get_pinterest_html( $html = '' , $style = 'i' ){

			$link_target = defined( 'EPL_SOCIAL_LINK_TARGET_BLANK' ) && EPL_SOCIAL_LINK_TARGET_BLANK ? 'target="_blank" ' : '';

			if ( $this->get_pinterest() != '' ) {

				$style	=	$style == 'i' && epl_get_option('epl_icons_svg_author') == 'on' ? 's' : $style;

				if ( $style == 'i' ) {
					$html	= '
						<a class="epl-author-icon author-icon pinterest-icon-24"
							href="' . $this->get_pinterest() . '" title="'.__('Follow', 'easy-property-listings' ).' '.$this->get_author_name().' '.__('on Pinterest', 'easy-property-listings' ). '"' . $link_target . '>'.
							apply_filters( 'epl_author_icon_pinterest' , __('Pinterest', 'easy-property-listings' )).
						'</a>';
				} else {
					$svg	= '<svg viewBox="0 0 100 100" class="epl-icon-svg-pinterest"><use xlink:href="#epl-icon-svg-pinterest"></use></svg>';
					$html	=
						'<div class="epl-icon-svg-container epl-icon-container-pinterest">
							<a class="epl-author-icon-svg author-icon-svg pinterest-icon"
								href="' . $this->get_instagram() . '" title="'.__('Follow', 'easy-property-listings' ).' '.$this->get_author_name().' '.__('on Pinterest', 'easy-property-listings' ).'"' . $link_target . '>' . $svg .
							'</a>
						</div>';
				}
			}
			$html = apply_filters('epl_author_pinterest_html',$html,$this);
			return $html;
		}

		/**
		 * Get Google
		 *
		 * @since version 3.2
		 */
		function get_google() {

			$google = '';
			if($this->google != '') {

				if( (strpos($this->google,'http://' ) === 0 ) || (strpos($this->google,'https://' ) === 0 ) ) {
					// absolute url
					$google = $this->google;

				} else {
					// relative url
					$google = 'http://plus.google.com/' . $this->google;
				}


			}
			return apply_filters('epl_author_google',$google,$this);
		}

		/**
		 * Author Google html Box
		 *
		 * @since version 1.3
		 */
		function get_google_html( $html = '' , $style = 'i' ){

			$link_target = defined( 'EPL_SOCIAL_LINK_TARGET_BLANK' ) && EPL_SOCIAL_LINK_TARGET_BLANK ? 'target="_blank" ' : '';

			if ( $this->get_google() != '' ) {

				$style	=	$style == 'i' && epl_get_option('epl_icons_svg_author') == 'on' ? 's' : $style;

				if ( $style == 'i' ) {
					$html = '
						<a class="epl-author-icon author-icon google-icon-24"
							href="' . $this->get_google() . '" title="'.__('Follow', 'easy-property-listings' ).' '.$this->get_author_name().' '.__('on Google', 'easy-property-listings' ).'"' . $link_target . '>'.
							apply_filters( 'epl_author_icon_google' , __('Google', 'easy-property-listings' )).
						'</a>';
				} else {
					$svg	= '<svg viewBox="0 0 100 100" class="epl-icon-svg-google-plus"><use xlink:href="#epl-icon-svg-google-plus"></use></svg>';
					$html	=
						'<div class="epl-icon-svg-container epl-icon-container-google-plus">
							<a class="epl-author-icon-svg author-icon-svg google-plus-icon"
								href="' . $this->get_google() . '" title="'.__('Follow', 'easy-property-listings' ).' '.$this->get_author_name().' '.__('on Google', 'easy-property-listings' ).'"' . $link_target . '>' . $svg .
							'</a>
						</div>';
				}
			}
			$html = apply_filters('epl_author_google_html',$html,$this);
			return $html;
		}

		/**
		 * Get Facebook
		 *
		 * @since version 3.2
		 */
		function get_facebook() {

			$facebook = '';

			if($this->facebook != ''){

				if( (strpos( $this->facebook,'http://' ) === 0 ) || (strpos( $this->facebook,'https://' ) === 0 ) ) {
					// absolute url
					$facebook = $this->facebook;

				} else {
					// relative url
					$facebook = 'http://facebook.com/' . $this->facebook;
				}


			}
			return apply_filters('epl_author_facebook',$facebook,$this);
		}

		/**
		 * Author Facebook html Box
		 *
		 * @since version 1.3
		 */
		function get_facebook_html( $html = '' , $style = 'i' ){

			$link_target = defined( 'EPL_SOCIAL_LINK_TARGET_BLANK' ) && EPL_SOCIAL_LINK_TARGET_BLANK ? 'target="_blank" ' : '';

			if ( $this->get_facebook() != '' ) {

				$style	=	$style == 'i' && epl_get_option('epl_icons_svg_author') == 'on' ? 's' : $style;

				if ( $style == 'i' ) {
					$html = '
						<a class="epl-author-icon author-icon facebook-icon-24"
							href="' . $this->get_facebook() . '" title="'.__('Follow', 'easy-property-listings' ).' '.$this->get_author_name().' '.__('on Facebook', 'easy-property-listings' ).'"' . $link_target . '>'.
							apply_filters( 'epl_author_icon_facebook' , __('Facebook', 'easy-property-listings' )).
						'</a>';
				} else {
					$svg	= '<svg viewBox="0 0 100 100" class="epl-icon-svg-facebook"><use xlink:href="#epl-icon-svg-facebook"></use></svg>';
					$html	=
						'<div class="epl-icon-svg-container epl-icon-container-facebook">
							<a class="epl-author-icon-svg author-icon-svg facebook-icon"
								href="' . $this->get_facebook() . '" title="'.__('Follow', 'easy-property-listings' ).' '.$this->get_author_name().' '.__('on Facebook', 'easy-property-listings' ).'"' . $link_target . '>' . $svg .
							'</a>
						</div>';
				}
			}
			$html = apply_filters('epl_author_facebook_html',$html,$this);
			return $html;
		}

		/**
		 * Get linkedin
		 *
		 * @since version 3.2
		 */
		function get_linkedin() {

			$linkedin = '';

			if($this->linkedin != '') {

				if( (strpos( $this->linkedin,'http://' ) === 0 ) || (strpos( $this->linkedin,'https://' ) === 0 ) ) {
					// absolute url
					$linkedin = $this->linkedin;

				} else {
					// relative url
					$linkedin = 'http://linkedin.com/pub/' . $this->linkedin;
				}

			}
			return apply_filters('epl_author_linkedin',$linkedin,$this);
		}

		/**
		 * Author LinkedIn html Box
		 *
		 * @since version 1.3
		 */
		function get_linkedin_html( $html = '' , $style = 'i' ) {

			$link_target = defined( 'EPL_SOCIAL_LINK_TARGET_BLANK' ) && EPL_SOCIAL_LINK_TARGET_BLANK ? 'target="_blank" ' : '';

			if ( $this->get_linkedin() != '' ) {

				$style	=	$style == 'i' && epl_get_option('epl_icons_svg_author') == 'on' ? 's' : $style;

				if ( $style == 'i' ) {

					$html = '
						<a class="epl-author-icon author-icon linkedin-icon-24"
							href="' . $this->get_linkedin() . '" title="'.__('Follow', 'easy-property-listings' ).' '.$this->get_author_name().' '.__('on LinkedIn', 'easy-property-listings' ).'"' . $link_target . '>'.
							apply_filters( 'epl_author_icon_linkedin' , __('LinkedIn', 'easy-property-listings' )).
						'</a>';
				} else {
					$svg	= '<svg viewBox="0 0 100 100" class="epl-icon-svg-linkedin"><use xlink:href="#epl-icon-svg-linkedin"></use></svg>';
					$html	=
						'<div class="epl-icon-svg-container epl-icon-container-linkedin">
							<a class="epl-author-icon-svg author-icon-svg linkedin-icon"
								href="' . $this->get_linkedin() . '" title="'.__('Follow', 'easy-property-listings' ).' '.$this->get_author_name().' '.__('on LinkedIn', 'easy-property-listings' ).'"' . $link_target . '>' . $svg .
							'</a>
						</div>';
					}
			}
			$html = apply_filters('epl_author_linkedin_html',$html,$this);
			return $html;
		}

		/**
		 * Get skype
		 *
		 * @since version 3.2
		 */
		function get_skype() {

			$skype = '';
			if($this->skype != ''){

				if( (strpos( $this->skype,'skype:' ) === 0 ) ) {
					// absolute url
					$skype = $this->skype;

				} else {
					// relative url
					$skype = 'skype:' . $this->skype;
				}


			}
			return apply_filters('epl_author_skype',$skype,$this);
		}

		/**
		 * Author Skype html Box
		 *
		 * @since version 1.3
		 */
		function get_skype_html( $html = '' , $style = 'i' ) {

			$link_target = defined( 'EPL_SOCIAL_LINK_TARGET_BLANK' ) && EPL_SOCIAL_LINK_TARGET_BLANK ? 'target="_blank" ' : '';

			if ( $this->get_skype() != '' ) {

				$style	=	$style == 'i' && epl_get_option('epl_icons_svg_author') == 'on' ? 's' : $style;

				if ( $style == 'i' ) {

					$html = '
						<a class="epl-author-icon author-icon skype-icon-24"
							href="' . $this->get_skype() . '" title="'.__('Follow', 'easy-property-listings' ).' '.$this->get_author_name().' '.__('on Skype', 'easy-property-listings' ).'"' . $link_target . '>'.
							apply_filters( 'epl_author_icon_skype' , __('Skype', 'easy-property-listings' )).
						'</a>';
				} else {
					$svg	= '<svg viewBox="0 0 100 100" class="epl-icon-svg-skype"><use xlink:href="#epl-icon-svg-skype"></use></svg>';
					$html	=
						'<div class="epl-icon-svg-container epl-icon-container-skype">
							<a class="epl-author-icon-svg author-icon-svg skype-icon"
								href="' . $this->get_skype() . '" title="'.__('Follow', 'easy-property-listings' ).' '.$this->get_author_name().' '.__('on Skype', 'easy-property-listings' ).'"' . $link_target . '>'. $svg .
							'</a>
						</div>';
				}
			}
			$html = apply_filters('epl_author_skype_html',$html,$this);
			return $html;
		}

		/**
		 * Author video html Box
		 *
		 * @since version 1.3
		 */
		function get_video_html($html = '') {
			if($this->video != '') {
				$video 	= apply_filters('epl_author_video_html',$this->video,$this);
				$html 	= wp_oembed_get($video);
			}
			return apply_filters('epl_author_video',$html,$this);
		}

		/**
		 * Get description
		 *
		 * @since version 3.2
		 */
		function get_description() {
			if($this->description != '')
				return apply_filters('epl_author_description',$this->description,$this);
		}

		/**
		 * Author description html
		 *
		 * @since version 1.3
		 */
		function get_description_html($html = '') {
			if ( $this->get_description() != '' ) {

			$permalink 	= apply_filters('epl_author_profile_link', get_author_posts_url($this->author_id) ,$this);

			$html = '
				<div class="epl-author-content author-content">'.$this->get_description().'</div>
					<span class="bio-more">
						<a href="'.$permalink.'">'.
							apply_filters('epl_author_read_more_label',__('Read More', 'easy-property-listings' ) ).'
						</a>
					</span>
			';
			}
			return apply_filters('epl_author_description_html',$html,$this);
		}

		/**
		 * Author mobile
		 *
		 * @since version 1.3
		 */
		function get_author_mobile() {
			if($this->mobile != '')
				return apply_filters('epl_author_mobile',$this->mobile,$this);
		}

		/**
		 * Author office phone
		 *
		 * @since version 3.3
		 */
		function get_author_office_phone() {
			if($this->office_phone != '')
				return apply_filters('epl_author_office_phone',$this->office_phone,$this);
		}

		/**
		 * Author Id
		 *
		 * @since version 1.3
		 */
		function get_author_id() {
			if($this->author_id != '')
				return apply_filters('epl_author_id',$this->author_id,$this);
		}

		/**
		 * Author Slogan
		 *
		 * @since version 1.3
		 */
		function get_author_slogan() {
			if($this->slogan != '')
				return apply_filters('epl_author_slogan',$this->slogan,$this);
		}

		/**
		 * Author Position
		 *
		 * @since version 1.3
		 */
		function get_author_position() {
			if($this->position != '')
				return apply_filters('epl_author_position',$this->position,$this);
		}

		/**
		 * Author Contact Form
		 *
		 * @since version 1.3
		 */
		function get_author_contact_form() {
			if($this->contact_form != ''){
				$cf = apply_filters('epl_author_contact_form',$this->contact_form,$this);
				return do_shortcode($cf);
			}
		}
	}
endif;
