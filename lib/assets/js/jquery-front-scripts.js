
/* Jquery extension to get URL params value */
jQuery.extend({
  getUrlVars: function(){
    var vars = [], hash;
    var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
    for(var i = 0; i < hashes.length; i++)
    {
      hash = hashes[i].split('=');
      vars.push(hash[0]);
      vars[hash[0]] = hash[1];
    }
    return vars;
  },
  getUrlVar: function(name){
    return jQuery.getUrlVars()[name];
  }
});

jQuery(document).ready(function(){

	jQuery('ul.author-tabs li').click(function(){
		var box_id = jQuery(this).closest('.epl-author-box').attr('id');
		var tab_id = jQuery(this).attr('data-tab');

		jQuery('#'+box_id+' ul.author-tabs li').removeClass('author-current');
		jQuery('#'+box_id+' .author-tab-content').removeClass('author-current');

		jQuery(this).addClass('author-current');
		jQuery('#'+box_id+' #'+tab_id).addClass('author-current');
	});
	
	
	/* switch views : grid & list on property archive pages */
	jQuery('.epl-switch-view ul li').click(function(){
		if(typeof listingsMasonEnabled != 'undefined') {
			return;
		}
		var view = jQuery(this).data('view');
		jQuery('.epl-switch-view ul li').removeClass('epl-current-view');
		jQuery(this).addClass('epl-current-view');
		if(view == 'grid'){
			jQuery('.epl-property-blog').addClass('epl-listing-grid-view');
				jQuery('.epl-listing-grid-view.epl-property-blog').each(function(index){
					index = index + 1;
					if((index > 0) && (index  % 3 == 0)){
						jQuery(this).after('<div class="epl-clearfix"></div>');
					}
				});
			
		} else {
			jQuery('.epl-property-blog').removeClass('epl-listing-grid-view');
		}
		
	});
	
	
	/* tabs for maps on single properties pages for different view types */
	jQuery('ul.map-tabs li').click(function(){
		var map_tab_id = jQuery(this).attr('onclick');

		jQuery('ul.map-tabs li').removeClass('current');
		jQuery('.author-tab-content').removeClass('current');

		jQuery(this).addClass('current');
		jQuery("#"+map_tab_id).addClass('current');
	});
	
	/* tabs for search widgets for multiple properties */
	jQuery('ul.property_search-tabs li').click(function(){
		var tab_id = jQuery(this).attr('data-tab');
		jQuery('ul.property_search-tabs li').removeClass('epl-sb-current');
		jQuery('.epl-search-form').removeClass('epl-sb-current');
		jQuery(this).addClass('epl-sb-current');
		jQuery("#"+tab_id).addClass('epl-sb-current');
	});
	


	/* Sort properties by price */
	jQuery('#epl-sort-listings').on('change',function(){
		var sortby 		= jQuery(this).val();
		var url 		= window.location.href.split('?')[0];
		var querystring = window.location.href.split('?')[1];
		
		if(jQuery.getUrlVar("sortby") != null) {
			querystring = '?'+querystring;
			var regEx 		= /([?&]sortby)=([^#&]*)/g;
			querystring 	= querystring.replace(regEx, '$1='+sortby);
			var newUrl 		= url+querystring;
		} else {
			if(querystring == null) {
				var newUrl 		= url +'?'+'sortby='+sortby;
			}
			else {
				var newUrl 		= url +'?'+querystring+'&sortby='+sortby;
			}
		}
		window.location.replace(newUrl);
		
	});
	
})
