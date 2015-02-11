
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

jQuery(document).ready(function($){

	jQuery('ul.author-tabs li').click(function(){
		var box_id = jQuery(this).closest('.epl-author-box').attr('id');
		var tab_id = jQuery(this).attr('data-tab');

		jQuery('#'+box_id+' ul.author-tabs li').removeClass('author-current');
		jQuery('#'+box_id+' .author-tab-content').removeClass('author-current');

		jQuery(this).addClass('author-current');
		jQuery('#'+box_id+' #'+tab_id).addClass('author-current');
	});
	
	/* render default map for single listings */
	if ( $('#epl-default-map').length ) {
	
		var eplgeocoder, epldefaultmap;
		var listingcoordinates 	= $('#epl-default-map').data('cord');
		var listingaddress		= $('#epl-default-map').data('address');
		var listingmapzoom		= $('#epl-default-map').data('zoom');
		var listid				= $('#epl-default-map').data('id');
		
		// use listing coordinates if already present
		if(listingcoordinates != '') {
		
			listingcoordinates = listingcoordinates.split(',');
			
			function renderdefaultmap() {
			  eplgeocoder = new google.maps.Geocoder();
			  eplmapcord = new google.maps.LatLng(listingcoordinates[0],listingcoordinates[1]);

			  var mapOptions = {
				center: eplmapcord,
				zoom: listingmapzoom,
			  }
			  
			  epldefaultmap = new google.maps.Map(document.getElementById('epl-default-map'), mapOptions);
			  
			  var epldefaultmarker = new google.maps.Marker({
				  map: epldefaultmap,
				  position: eplmapcord
			  });
			}
			renderdefaultmap();

		} else {
			
			// if listing coordinates not present, use address to fetch them
			if(listingaddress != '') { 
				
				/* geocode listing address if coordinates are not already set */
				function eplcodeAddress() {
					
				  eplgeocoder = new google.maps.Geocoder();
				  eplgeocoder.geocode( { 'address': listingaddress}, function(results, status) {
				  
						if (status == google.maps.GeocoderStatus.OK) {
					
						  var mapOptions = {
							center: results[0].geometry.location,
							zoom: listingmapzoom,
						  }
						  
						  epldefaultmap = new google.maps.Map(document.getElementById('epl-default-map'), mapOptions);
						  
						  var epldefaultmarker = new google.maps.Marker({
							  map: epldefaultmap,
							  position: results[0].geometry.location
						  });
							
							$.ajax({
								type: "POST",
								url: epl_frontend_vars.ajaxurl,
								data: { 
										action: "epl_update_listing_coordinates",
										coordinates: results[0].geometry.location.toString() ,
										listid:listid
									}
							})
							.done(function( msg ) {
								// successfully updated geocode
							});
						
						} else {
							// error in geocoding
						}
				  });
				}
				
				eplcodeAddress();

			}
		}
		

	}
	
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
	
	
	/* force default view to grid if its set to default */
	if(epl_frontend_vars.epl_default_view_type == 'grid') {
		jQuery('.epl-switch-view ul li[data-view="grid"]').trigger('click');
	}
	

	
	
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
