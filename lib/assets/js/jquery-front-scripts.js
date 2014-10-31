
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
		var tab_id = jQuery(this).attr('data-tab');

		jQuery('ul.author-tabs li').removeClass('author-current');
		jQuery('.author-tab-content').removeClass('author-current');

		jQuery(this).addClass('author-current');
		jQuery("#"+tab_id).addClass('author-current');
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
	
	/* switch views : grid & list on property archive pages */
	jQuery('.epl-switch-view ul li').click(function(){
		var view = jQuery(this).data('view');
		jQuery('.epl-switch-view ul li').removeClass('epl-current-view');
		jQuery(this).addClass('epl-current-view');
		console.log(view);
		if(view == 'grid'){
			jQuery('.epl-listing-post').addClass('epl-listing-grid-view');
				jQuery('.epl-listing-grid-view.epl-listing-post').each(function(index){
					index = index + 1;
					if((index > 0) && (index  % 3 == 0)){
						jQuery(this).after('<div class="epl-clearfix"></div>');
					}
				});
			
		} else {
			jQuery('.epl-listing-post').removeClass('epl-listing-grid-view');
		}
		
	});

	/* Sort properties by price */
	jQuery('#epl-sort-price').on('change',function(){
		var sortby 		= jQuery(this).val();
		var url 		= window.location.href.split('?')[0];
		var querystring = window.location.href.split('?')[1];
		
		if(jQuery.getUrlVar("orderby") != null) {
			querystring = '?'+querystring;
			var regEx 		= /([?&]orderby)=([^#&]*)/g;
			querystring 	= querystring.replace(regEx, '$1='+sortby);
			var newUrl 		= url+querystring;
		} else {
			if(querystring == null) {
				var newUrl 		= url +'?'+'orderby='+sortby;
			}
			else {
				var newUrl 		= url +'?'+querystring+'&orderby='+sortby;
			}
		}
		window.location.replace(url);
		
	});
	
})
