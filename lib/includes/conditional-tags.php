<?php

	/** get list of core epl posts 
	*
	*	@since 2.4
	*/
	function epl_get_core_post_types() {
		return apply_filters('epl_core_post_types',array('rural','property','rental','land','commercial','commercial_land','business') );	
	}
	
	/** get list of sales epl posts 
	*
	*	@since 2.4
	*/
	function epl_get_core_sales_post_types() {
		return apply_filters('epl_core_sales_post_types',array('rural','property','land','commercial','commercial_land','business') );	
	}
	
	/** get list of rental epl posts 
	*
	*	@since 2.4
	*/

	function epl_get_core_rental_post_types() {
		return apply_filters('epl_core_rental_post_types',array('rural') );	
	}
	
	/**
	 *	list of all epl cpts 
	 *	@since 2.4
	 */
	function epl_all_post_types() {
		
		$epl_posts  = epl_get_active_post_types();
		$epl_posts	= array_keys($epl_posts);
		return apply_filters('epl_additional_post_types',$epl_posts);
	}

	/**
	 *	check if post is core epl post
	 *	@since 2.4
	 */
	function is_epl_core_post($type=null) {
		$type = is_null($type) ? get_post_type() : $type;
		return in_array($type,epl_get_core_post_types());
	}

	/**
	 *	check if post is sales post
	 *	@since 2.4
	 */
	function is_epl_sales_post($type=null) {
		$type = is_null($type) ? get_post_type() : $type;
		return in_array($type,epl_get_core_sales_post_types());
	}

	/**
	 *	check if post is rental post
	 *	@since 2.4
	 */
	function is_epl_rental_post($type=null) {
		$type = is_null($type) ? get_post_type() : $type;
		return in_array($type,epl_get_core_rental_post_types());
	}

	/**
	 *	check if current post is of epl
	 *	@since 2.2
	 */
	function is_epl_post($type=null) {
		return 		in_array(get_post_type(),epl_all_post_types());
	}
	
	/**
	 *	check if viewing a single post of epl
	 *	@since 2.2
	 */
	function is_epl_post_single() {
		return 		is_singular(epl_all_post_types());
	}
	
	/**
	 *	check if cpt is from epl
	 *	@since 2.2
	 */
	function is_epl_post_type($type) {
		return 		(in_array($type,epl_all_post_types()) && get_post_type() == $type );
	}
	
	/**
	 *	check if current post is of epl
	 *	@since 2.2
	 */
	function is_epl_post_archive() {
		
		return 		is_post_type_archive(epl_all_post_types());
	}
	
	
	
	
	

	

