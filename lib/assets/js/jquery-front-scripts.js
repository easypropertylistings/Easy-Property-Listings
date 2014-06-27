jQuery(document).ready(function(){

	jQuery('ul.author-tabs li').click(function(){
		var tab_id = jQuery(this).attr('data-tab');

		jQuery('ul.author-tabs li').removeClass('author-current');
		jQuery('.author-tab-content').removeClass('author-current');

		jQuery(this).addClass('author-current');
		jQuery("#"+tab_id).addClass('author-current');
	})
	
	jQuery('ul.map-tabs li').click(function(){
		var map_tab_id = jQuery(this).attr('onclick');

		jQuery('ul.map-tabs li').removeClass('current');
		jQuery('.author-tab-content').removeClass('current');

		jQuery(this).addClass('current');
		jQuery("#"+map_tab_id).addClass('current');
	})

})
