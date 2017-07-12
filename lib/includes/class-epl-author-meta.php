<?php
/**
 * EPL Admin Functions
 *
 * @package     EPL
 * @subpackage  Classes/Author
 * @copyright   Copyright (c) 2014, Merv Barrett
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
class EPL_Author_Meta {

	private $author_id;
	private $name;
	private $mobile;
	private $facebook;
	private $linkedin;
	private $google;
	private $twitter;
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
		$this->author_id 		= $author_id;
		$this->name 			= get_the_author_meta( 'display_name' , $this->author_id);
		$this->mobile 			= get_the_author_meta( 'mobile' , $this->author_id);
		$this->facebook 		= get_the_author_meta( 'facebook' , $this->author_id);
		$this->linkedin 		= get_the_author_meta( 'linkedin' , $this->author_id);
		$this->google 			= get_the_author_meta( 'google' , $this->author_id);
		$this->twitter 			= get_the_author_meta( 'twitter' , $this->author_id);
		$this->email 			= get_the_author_meta( 'email' , $this->author_id);
		$this->skype 			= get_the_author_meta( 'skype' , $this->author_id);
		$this->slogan 			= get_the_author_meta( 'slogan' , $this->author_id);
		$this->position 		= get_the_author_meta( 'position' , $this->author_id);
		$this->video 			= get_the_author_meta( 'video' , $this->author_id);
		$this->contact_form 		= get_the_author_meta( 'contact-form' , $this->author_id);
		$this->description 		= get_the_author_meta( 'description' , $this->author_id);
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
	 * Author Email html Box
	 *
	 * @since version 1.3
	 */
    function get_email_html($html = '') {

    	if ( $this->email != '' ) {
			$html = '
				<a class="epl-author-icon author-icon email-icon-24"
					href="mailto:' . $this->email . '" title="'.__('Contact', 'easy-property-listings' ).' '.$this->name.' '.__('by Email', 'easy-property-listings' ).'">'.
					apply_filters( 'epl_author_icon_email' , __('Email', 'easy-property-listings' )).
				'</a>';
		}
		$html = apply_filters('epl_author_email_html',$html,$this);
		return $html ;
    }

	/*
	 * Author Twitter html Box
	 *
	 * @since version 1.3
	 */
    function get_twitter_html($html = ''){

    	if ( $this->twitter != '' ) {

    		if( (strpos('http://',$this->twitter) == 0 ) || (strpos('https://',$this->twitter) == 0 ) ) {
    			// absolute url
    			$twitter = $this->twitter;

    		} else {
    			// relative url
    			$twitter = 'http://twitter.com/' . $this->twitter;
    		}

			$html = '
				<a class="epl-author-icon author-icon twitter-icon-24"
					href="' . $twitter . '" title="'.__('Follow', 'easy-property-listings' ).' '.$this->name.' '.__('on Twitter', 'easy-property-listings' ).'">'.
					apply_filters( 'epl_author_icon_twitter' , __('Twitter', 'easy-property-listings' )).
				'</a>';
		}
		$html = apply_filters('epl_author_twitter_html',$html,$this);
		return $html;
    }

	/**
	 * Author Google html Box
	 *
	 * @since version 1.3
	 */
    function get_google_html($html = ''){
    	if ( $this->google != '' ) {

    		if( (strpos('http://',$this->google) == 0 ) || (strpos('https://',$this->google) == 0 ) ) {
    			// absolute url
    			$google = $this->google;

    		} else {
    			// relative url
    			$google = 'http://plus.google.com/' . $this->google;
    		}

			$html = '
				<a class="epl-author-icon author-icon google-icon-24"
					href="' . $google . '" title="'.__('Follow', 'easy-property-listings' ).' '.$this->name.' '.__('on Google', 'easy-property-listings' ).'">'.
					apply_filters( 'epl_author_icon_google' , __('Google', 'easy-property-listings' )).
				'</a>';
		}
		$html = apply_filters('epl_author_google_html',$html,$this);
		return $html;
    }

	/**
	 * Author Facebook html Box
	 *
	 * @since version 1.3
	 */
    function get_facebook_html($html = ''){

    	if ( $this->facebook != '' ) {

    		if( (strpos('http://',$this->facebook) == 0 ) || (strpos('https://',$this->facebook) == 0 ) ) {
    			// absolute url
    			$facebook = $this->facebook;

    		} else {
    			// relative url
    			$facebook = 'http://facebook.com/' . $this->facebook;
    		}

			$html = '
				<a class="epl-author-icon author-icon facebook-icon-24"
					href="' . $facebook . '" title="'.__('Follow', 'easy-property-listings' ).' '.$this->name.' '.__('on Facebook', 'easy-property-listings' ).'">'.
					apply_filters( 'epl_author_icon_facebook' , __('Facebook', 'easy-property-listings' )).
				'</a>';
		}
		$html = apply_filters('epl_author_facebook_html',$html,$this);
		return $html;
    }

	/**
	 * Author Linkedin html Box
	 *
	 * @since version 1.3
	 */
    function get_linkedin_html($html = '') {
    	if ( $this->linkedin != '' ) {

    		if( (strpos('http://',$this->linkedin) == 0 ) || (strpos('https://',$this->linkedin) == 0 ) ) {
    			// absolute url
    			$linkedin = $this->linkedin;

    		} else {
    			// relative url
    			$linkedin = 'http://linkedin.com/pub/' . $this->linkedin;
    		}

			$html = '
				<a class="epl-author-icon author-icon linkedin-icon-24" href="' . $linkedin . '"
					title="'.__('Follow', 'easy-property-listings' ).' '.$this->name.' '.__('on Linkedin', 'easy-property-listings' ).'">'.
					apply_filters( 'epl_author_icon_linkedin' , __('LinkedIn', 'easy-property-listings' )).
				'</a>';
		}
		$html = apply_filters('epl_author_linkedin_html',$html,$this);
		return $html;
    }

	/**
	 * Author Skype html Box
	 *
	 * @since version 1.3
	 */
    function get_skype_html($html = '') {
    	if ( $this->skype != '' ) {
			$html = '
				<a class="epl-author-icon author-icon skype-icon-24" href="http://skype.com/' . $this->skype . '"
					title="'.__('Follow', 'easy-property-listings' ).' '.$this->name.' '.__('on Skype', 'easy-property-listings' ).'">'.
					apply_filters( 'epl_author_icon_skype' , __('Skype', 'easy-property-listings' )).
				'</a>';
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
    		$video 	= apply_filters('epl_author_video',$this->video);
    		$html 	= wp_oembed_get($video);
		}
		return $html;
    }

	/**
	 * Author description html
	 *
	 * @since version 1.3
	 */
    function get_description_html($html = '') {
    	if ( $this->description != '' ) {

		$permalink 		= apply_filters('epl_author_profile_link', get_author_posts_url($this->author_id) ,$this);

		$html =     '
			<div class="epl-author-content author-content">'.$this->description.'</div>
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
    		return $this->mobile;
    }

	/**
	 * Author Id
	 *
	 * @since version 1.3
	 */
    function get_author_id() {
    	if($this->author_id != '')
    		return $this->author_id;
    }

	/**
	 * Author Slogan
	 *
	 * @since version 1.3
	 */
    function get_author_slogan() {
    	if($this->slogan != '')
    		return $this->slogan;
    }

	/**
	 * Author Position
	 *
	 * @since version 1.3
	 */
    function get_author_position() {
    	if($this->position != '')
    		return $this->position;
    }

	/**
	 * Author Name
	 *
	 * @since version 1.3
	 */
    function get_author_name() {
    	if($this->name != '')
    		return $this->name;
    }

	/**
	 * Author Contact Form
	 *
	 * @since version 1.3
	 */
    function get_author_contact_form() {
    	if($this->contact_form != '')
    		return do_shortcode($this->contact_form);
    }
}
