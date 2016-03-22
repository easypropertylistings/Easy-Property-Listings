<?php
/**
 * EPL Author Meta Compatibility
 *
 * @package     EPL
 * @subpackage  Compatibility/Author
 * @copyright   Copyright (c) 2014, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/*
 * Author Meta: Depreciated.
 *
 * This prepares the meta data for the author profile and author box
 *
 * @since       1.0
 */

$name 			= get_the_author_meta( 'display_name' , $author_id);
$mobile 		= get_the_author_meta( 'mobile' , $author_id);
$facebook 		= get_the_author_meta( 'facebook' , $author_id);
$linkedin 		= get_the_author_meta( 'linkedin' , $author_id);
$google 		= get_the_author_meta( 'google' , $author_id);
$twitter 		= get_the_author_meta( 'twitter' , $author_id);
$email 			= get_the_author_meta( 'email' , $author_id);
$skype 			= get_the_author_meta( 'skype' , $author_id);
$slogan 		= get_the_author_meta( 'slogan' , $author_id);
$position 		= get_the_author_meta( 'position' , $author_id);
$video 			= get_the_author_meta( 'video' , $author_id);
$e_video 		= wp_oembed_get($video);
$contact_form 	= get_the_author_meta( 'contact-form' , $author_id);

// Prepare Social Icons // Need to convert to for each loop

// Email
if ( $email != '' ) {
	$i_email = '<a class="author-icon email-icon-24" href="mailto:' . $email . '" title="'.__('Contact', 'easy-property-listings').' '.$name.' '.__('by Email', 'easy-property-listings').'">'.__('Email', 'easy-property-listings').'</a>';
}

// Twitter
if ( $twitter != '' ) {
	$i_twitter = '<a class="author-icon twitter-icon-24" href="http://twitter.com/' . $twitter . '" title="'.__('Follow', 'easy-property-listings').' '.$name.' '.__('on Twitter', 'easy-property-listings').'">'.__('Twitter', 'easy-property-listings').'</a>';
}

// Google
if ( $google != '' ) {
	$i_google = '<a class="author-icon google-icon-24" href="https://plus.google.com/' . $google . '" title="'.__('Follow', 'easy-property-listings').' '.$name.' '.__('on Google', 'easy-property-listings').'">'.__('Google', 'easy-property-listings').'</a>';
}

// Facebook
if ( $facebook != '' ) {
	$i_facebook = '<a class="author-icon facebook-icon-24" href="http://facebook.com/' . $facebook . '" title="'.__('Follow', 'easy-property-listings').' '.$name.' '.__('on Facebook', 'easy-property-listings').'">'.__('Facebook', 'easy-property-listings').'</a>';
}

// Linked In
if ( $linkedin != '' ) {
	$i_linkedin = '<a class="author-icon linkedin-icon-24" href="http://au.linkedin.com/in/' . $linkedin . '" title="'.__('Follow', 'easy-property-listings').' '.$name.' '.__('on Linkedin', 'easy-property-listings').'">'.__('Linkedin', 'easy-property-listings').'</a>';
}

// Skype
if ( $skype != '' ) {
	$i_skype = '<a class="author-icon skype-icon-24" href="http://skype.com/' . $skype . '" title="'.__('Follow', 'easy-property-listings').' '.$name.' '.__('on Skype', 'easy-property-listings').'">'.__('Skype', 'easy-property-listings').'</a>';
}
