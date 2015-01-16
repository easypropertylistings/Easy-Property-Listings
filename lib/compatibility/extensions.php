<?php

function epl_sd_author_box_compat() {
	if(has_action( 'epl_single_author' , 'epl_sd_advanced_author_box' ) || has_action( 'epl_single_author' , 'epl_sd_advanced_author_box_tabs' )) {
		remove_action( 'epl_single_author','epl_property_author_box' );
	}
}

add_action( 'init' , 'epl_sd_author_box_compat' );
	
