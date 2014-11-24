<?php

/*
*	@since version 1.3
*/

class Author_Meta {
	
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
	
	function __construct($author_id) {
		$this->author_id 			= $author_id;
		$this->name 				= get_the_author_meta( 'display_name' , $this->author_id);
		$this->mobile 				= get_the_author_meta( 'mobile' , $this->author_id);
		$this->facebook 			= get_the_author_meta( 'facebook' , $this->author_id);
		$this->linkedin 			= get_the_author_meta( 'linkedin' , $this->author_id);
		$this->google 				= get_the_author_meta( 'google' , $this->author_id);
		$this->twitter 				= get_the_author_meta( 'twitter' , $this->author_id);
		$this->email 				= get_the_author_meta( 'email' , $this->author_id);
		$this->skype 				= get_the_author_meta( 'skype' , $this->author_id);
		$this->slogan 				= get_the_author_meta( 'slogan' , $this->author_id);
		$this->position 			= get_the_author_meta( 'position' , $this->author_id);
		$this->video 				= get_the_author_meta( 'video' , $this->author_id);
		$this->contact_form 		= get_the_author_meta( 'contact-form' , $this->author_id);
		$this->description 			= get_the_author_meta( 'description' , $this->author_id);
    }
    
    
    function __get($property) {
    	if(isset($this->{$property}) && $this->{$property} != ''){
    		return $this->{$property};
    	}
    }
    
	/*
	* Author Email html Box
	* @since version 1.3
	*/
    function get_email_html($html = '') {
    	
    	if ( $this->email != '' ) {
			$html = '
						<a class="author-icon email-icon-24" 
							href="mailto:' . $this->email . '" title="'.__('Contact', 'epl').' '.$this->name.' '.__('by Email', 'epl').'">'.
							__('Email', 'epl').
						'</a>';
		}
		$html = apply_filters('epl_author_email_html',$html);
		return $html ;
    }
    
   /*
	* Author Twitter html Box
	* @since version 1.3
	*/
    function get_twitter_html($html = ''){
    	if ( $this->twitter != '' ) {
			$html = '
						<a class="author-icon twitter-icon-24" 
							href="http://twitter.com/' . $twitter . '" title="'.__('Follow', 'epl').' '.$name.' '.__('on Twitter', 'epl').'">'.
							__('Twitter', 'epl').
						'</a>';
		}
		$html = apply_filters('epl_author_twitter_html',$html);
		return $html;
    }
    
   /*
	* Author Google html Box
	* @since version 1.3
	*/
    function get_google_html($html = ''){
    	if ( $this->google != '' ) {
			$html = '
						<a class="author-icon google-icon-24" 
							href="https://plus.google.com/' . $google . '" title="'.__('Follow', 'epl').' '.$name.' '.__('on Google', 'epl').'">'.
							__('Google', 'epl').
						'</a>';
		}
		$html = apply_filters('epl_author_google_html',$html);
		return $html;
    }
    
   /*
	* Author Facebook html Box
	* @since version 1.3
	*/
    function get_facebook_html($html = ''){
    	if ( $this->facebook != '' ) {
			$html = '
						<a class="author-icon facebook-icon-24" 
							href="http://facebook.com/' . $facebook . '" title="'.__('Follow', 'epl').' '.$name.' '.__('on Facebook', 'epl').'">'.
							__('Facebook', 'epl').
						'</a>';
		}
		$html = apply_filters('epl_author_facebook_html',$html);
		return $html;
    }
    
   /*
	* Author Linkedin html Box
	* @since version 1.3
	*/
    function get_linkedin_html($html = '') {
    	if ( $this->linkedin != '' ) {
			$html = '
							<a class="author-icon linkedin-icon-24" href="http://au.linkedin.com/in/' . $linkedin . '" 
								title="'.__('Follow', 'epl').' '.$name.' '.__('on Linkedin', 'epl').'">'.
								__('Linkedin', 'epl').
							'</a>';
		}
		$html = apply_filters('epl_author_linkedin_html',$html);
		return $html;
    }
    
   /*
	* Author Skype html Box
	* @since version 1.3
	*/
    function get_skype_html($html = '') {
    	if ( $this->skype != '' ) {
			$html = '
						<a class="author-icon skype-icon-24" href="http://skype.com/' . $skype . '" 
							title="'.__('Follow', 'epl').' '.$name.' '.__('on Skype', 'epl').'">'.
							__('Skype', 'epl').
						'</a>';
		}
		$html = apply_filters('epl_author_skype_html',$html);
		return $html;
    }
    
   /*
	* Author video html Box
	* @since version 1.3
	*/
    function get_video_html($html = '') {
    	if($this->video != '') {
    		$video 	= apply_filters('epl_author_video',$this->video);
    		$html 	= wp_oembed_get($video);
		}
		return $html;
    }
    
    /*
	* Author description html
	* @since version 1.3
	*/
    function get_description_html($html = '') {
    	if ( $this->description != '' ) { 
			$html =     '
						<div class="author-content">'.$this->description.'</div>
							<span class="bio-more">
								<a href="'.get_author_posts_url($this->author_id).'">'.
									__('Read More', 'epl').'
								</a>
							</span>
			';		
		}
		return $html;
	}
    
    
   /*
	* Author mobile
	* @since version 1.3
	*/
    function get_author_mobile() {
    	if($this->mobile != '')
    		return $this->mobile;
    }
    
    /*
	* Author Id
	* @since version 1.3
	*/
    function get_author_id() {
    	if($this->author_id != '')
    		return $this->author_id;
    }
    
   /*
	* Author Slogan
	* @since version 1.3
	*/
    function get_author_slogan() {
    	if($this->slogan != '')
    		return $this->slogan;
    }
    
    /*
	* Author Position
	* @since version 1.3
	*/
    function get_author_position() {
    	if($this->position != '')
    		return $this->position;
    }
    
    /*
	* Author Name
	* @since version 1.3
	*/
    function get_author_name() {
    	if($this->name != '')
    		return $this->name;
    }
    
    /*
	* Author Contact Form
	* @since version 1.3
	*/
    function get_author_contact_form() {
    	if($this->contact_form != '')
    		return do_shortcode($this->contact_form);
    }
}


