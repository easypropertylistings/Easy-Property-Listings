<?php

	/**
	 *	check if current post is of epl
	 *	@since 2.2
	 */
	function is_epl_post() {
		
		$epl_posts  = epl_get_active_post_types();
		$epl_posts	= array_keys($epl_posts);
		$epl_posts 	= apply_filters('epl_additional_post_types',$epl_posts);
		return 		in_array( get_post_type(), $epl_posts);
	}
	
	/**
	 *	check for epl post type
	 *	@since 2.2
	 */
	function is_epl_post_type($type) {
		
		if( is_array($type) ) {
		
			return in_array( get_post_type(), $type );
		}
		
		return get_post_type() == $type;

	}
	
	/**
	 *	check if current post is of epl
	 *	@since 2.2
	 */
	function is_epl_post_archive() {
		
		$epl_posts  = epl_get_active_post_types();
		$epl_posts	= array_keys($epl_posts);
		$epl_posts 	= apply_filters('epl_additional_post_types',$epl_posts);
		return 		is_post_type_archive($epl_posts);
	}
	
	

	

