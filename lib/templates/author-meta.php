<?php
/*
 * Author Meta
 */

$author_id = get_the_author_meta( 'ID' );
$name = get_the_author_meta( 'display_name' );
$mobile = get_the_author_meta( 'mobile' );
$facebook = get_the_author_meta( 'facebook' );
$linkedin = get_the_author_meta( 'linkedin' );
$google = get_the_author_meta( 'google' );
$twitter = get_the_author_meta( 'twitter' );
$email = get_the_author_meta( 'email' );
$skype =  get_the_author_meta( 'skype' );
$slogan =  get_the_author_meta( 'slogan' );
$position = get_the_author_meta( 'position' );
$video = get_the_author_meta( 'video' );
$e_video = wp_oembed_get($video);
$contact_form = get_the_author_meta( 'contact-form' );

// Prepare Social Icons // Need to convert to for each loop

// Email
if ( $email != '' ) {
	$i_email = '<a class="author-icon email-icon-24" href="mailto:' . $email . '" title="Contact ' . $name . ' by Email">Email</a>';
}
// Twitter
if ( $twitter != '' ) {
	$i_twitter = '<a class="author-icon twitter-icon-24" href="http://twitter.com/' . $twitter . '" title="Follow ' . $name . ' on Twitter">Twitter</a>';
}

// Google
if ( $google != '' ) {
	$i_google = '<a class="author-icon google-icon-24" href="https://plus.google.com/' . $google . '" title="Follow ' . $name . ' on google">Google</a>';
}

// Facebook
if ( $facebook != '' ) {
	$i_facebook = '<a class="author-icon facebook-icon-24" href="http://facebook.com/' . $facebook . '" title="Follow ' . $name . ' on facebook">Facebook</a>';
}

// Linked In
if ( $linkedin != '' ) {
	$i_linkedin = '<a class="author-icon linkedin-icon-24" href="http://au.linkedin.com/in/' . $linkedin . '" title="Follow ' . $name . ' on linkedin">linkedin</a>';
}

// Skype
if ( $skype != '' ) {
	$i_skype = '<a class="author-icon skype-icon-24" href="http://skype.com/' . $skype . '" title="Follow ' . $name . ' on skype">skype</a>';
}
