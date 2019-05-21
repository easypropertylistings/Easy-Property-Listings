
jQuery(document).ready(function ($) {

	Number.prototype.formatMoney = function(c, d, t){
        var n = this,
            c = isNaN(c = Math.abs(c)) ? 2 : c,
            d = d == undefined ? "." : d,
            t = t == undefined ? "," : t,
            s = n < 0 ? "-" : "",
            i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "",
            j = (j = i.length) > 3 ? j % 3 : 0;
        return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
    };

	EPL_Front_Js = {

		/** initialize all functions required for EPL **/
		init : function() {

			//this.property_search();
			this.sort_listings();
			this.author_tabs();
			this.default_map();
			this.switch_view();
			this.set_default_view();
			this.listing_search_tabs();
			this.field_sliders();
			this.epl_search();
			this.contact_form_submit();

		},

		property_search : function() {
			if( $('#property_id').length == 1) {

				var EplContactListingTimeout = null;

				$('#property_id').keyup(function() {

					if($('#property_id').val() == '') {
						$('.epl-property-suggestion').fadeOut();
						return false;
					}

					if (EplContactListingTimeout != null) {
						clearTimeout(EplContactListingTimeout);
					}
					var search = $(this).val();

					data = {
						action: 'epl_search_listing',
						s: search
					};

					EplContactListingTimeout =  setTimeout(function () {
						EplContactListingTimeout = null;

						$.ajax({
							type: "POST",
							data: data,
							url: ajaxurl,
							success: function (response) {
								if( $('.epl-property-suggestion').length == 1 ) {
									$('.epl-property-suggestion').replaceWith(response);
								} else {
									$('#property_id').after(response);
								}
								$('.epl-property-suggestion').fadeIn();
							}
						});
					},300);

				});

				$( document.body ).on( 'click', '.epl-property-suggestion li', function( e ) {
					$('#property_id').val($(this).data('id'));
					$('.epl-property-suggestion').fadeOut().remove();
				});

			}
		},

		/* Sort properties by price */
		sort_listings : function() {
			
			jQuery('#epl-sort-listings').on('change',function(){
				var sortby 		= jQuery(this).val();
				var url 		= window.location.href.split('?')[0];
				var querystring = window.location.href.split('?')[1];

				/** Resets page when sorting */
				var where = url.search('page');
				if (where > -1) {
					var url = url.substring(0,where);
				}

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

		},

		/* get cookie value */
		get_cookie : function(c_name) {
		    var i,x,y,ARRcookies=document.cookie.split(";");

		    for (i=0;i<ARRcookies.length;i++)
		    {
		        x=ARRcookies[i].substr(0,ARRcookies[i].indexOf("="));
		        y=ARRcookies[i].substr(ARRcookies[i].indexOf("=")+1);
		        x=x.replace(/^\s+|\s+$/g,"");
		        if (x==c_name)
		        {
		            return unescape(y);
		        }
		     }
		},

		/* author tabs functionality */
		author_tabs : function() {
			jQuery('ul.epl-author-tabs li').click(function(){
				var box_id = jQuery(this).closest('.epl-author-box-container').attr('id');
				var tab_id = jQuery(this).attr('data-tab');

				jQuery('#'+box_id+' ul.epl-author-tabs li').removeClass('epl-author-current');
				jQuery('#'+box_id+' .epl-author-tab-content').removeClass('epl-author-current');

				jQuery(this).addClass('epl-author-current');
				jQuery('#'+box_id+' #'+tab_id).addClass('epl-author-current');
			});

		},

		/* render default map on single listings */
		default_map : function() {

			/* render default map for single listings */
			if ( $('#epl-default-map').length && typeof google === 'object' && typeof google.maps === 'object' ) {

				var eplgeocoder;
				var listingcoordinates 	= $('#epl-default-map').data('cord');
				var listingaddress		= $('#epl-default-map').data('address');
				var listingmapzoom		= $('#epl-default-map').data('zoom');
				var listid				= $('#epl-default-map').data('id');
				var suburb_mode			= $('#epl-default-map').data('suburb_mode');

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

					  window.epldefaultmap = new google.maps.Map(document.getElementById('epl-default-map'), mapOptions);
					  epl_marker_icon = 'default.png';
					  window.epldefaultmarker = new google.maps.Marker({
						  map: epldefaultmap,
						  position: eplmapcord,
						  icon : epl_frontend_vars.image_base + epl_marker_icon
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
								  epl_marker_icon = 'default.png';
									// dont save suburb coordinates as listing coordinates
									if(suburb_mode != 1) {
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
										epl_marker_icon = 'suburb.png';
									}

									window.epldefaultmarker = new google.maps.Marker({
										  map: epldefaultmap,
										  position: results[0].geometry.location,
										  icon : epl_frontend_vars.image_base + epl_marker_icon
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

		},

		/** set default view for listings on archive pages - gives preference to user selections fallsback to admin settings **/
		set_default_view : function() {

			/* force default view to grid if its set to default */
			if( eplGetCookie('preferredView') !== undefined) {
				var preferredView = this.get_cookie('preferredView');
				jQuery('.epl-switch-view ul li[data-view="'+preferredView+'"]').trigger('click');

			} else if(epl_frontend_vars.epl_default_view_type == 'grid') {
				jQuery('.epl-switch-view ul li[data-view="grid"]').trigger('click');
			}

		},

		/* switch view list / grid */
		switch_view : function() {

			/* switch views : grid & list on property archive pages */
			jQuery('.epl-switch-view ul li').click(function(){

				var view = jQuery(this).data('view');
				$.ajax({
					type: "POST",
					url: epl_frontend_vars.ajaxurl,
					data: {
							action: "epl_update_default_view",
							view  : view
						}
				})
				.done(function( msg ) {
					
				});

				if(typeof listingsMasonEnabled != 'undefined') {
					return;
				}


				jQuery('.epl-switch-view ul li').removeClass('epl-current-view');
				jQuery(this).addClass('epl-current-view');
				if(view == 'grid'){
					jQuery('.epl-property-blog').addClass('epl-listing-grid-view');
						jQuery('.epl-listing-grid-view.epl-property-blog').each(function(index){
							index = index + 1;
							if((index > 0) && (index  % 3 == 0)){
								/* jQuery(this).after('<div class="epl-clearfix"></div>'); */
							}
						});
				} else {
					jQuery('.epl-property-blog').removeClass('epl-listing-grid-view');
				}

			});

		},

		/** listing search tabs **/
		listing_search_tabs : function() {

			/* tabs for search widgets for multiple properties */
			jQuery('ul.property_search-tabs li').click(function(){
				var tab_id = jQuery(this).attr('data-tab');
				jQuery('ul.property_search-tabs li').removeClass('epl-sb-current');
				jQuery('.epl-search-form').removeClass('epl-sb-current');
				jQuery(this).addClass('epl-sb-current');
				jQuery("#"+tab_id).addClass('epl-sb-current');
			});


		},

		/** get url params as an array **/
		get_url_vars : function(){
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

	  	get_url_var : function(name) {
	  		return this.get_url_vars()[name];
	  	},

	  	field_sliders : function() {

	  		var _search_forms = $('.epl-search-form');
	  		var epl_lf_ranges = epl_frontend_vars.field_sliders;

	  		_search_forms.each( function( form_index ) {

	  			var current_search_form = $(this);

	  			$.each(epl_lf_ranges,function(key, value) {

					elExists = true;

					$.each(value.els,function(k,v) {
						if(!$('.'+v).length) {
							elExists = false;
						}else {
							$('.'+v).closest('.epl-search-row').hide();
						}
					});

					if(elExists) {

						if(value.els.length < 2) {
						
							var min = isNaN(Number( $('.'+value.els[0]).data('min') ) ) 
							? value.min : Number( $('.'+value.els[0]).data('min') );

							var max = isNaN(Number( $('.'+value.els[0]).data('max') ) ) 
							? value.max : Number( $('.'+value.els[0]).data('max') );

							var defaultValues = min;

							var custom_val = $.getUrlVar( value.els[0] );

							if( custom_val !== undefined ){
								defaultValues = parseInt(custom_val);
							}

							$('.'+value.els[0])
							.closest('.epl-search-row')
							.after('<div class="epl-lf-range-slider epl-search-row epl-search-row-select fm-block epl-search-row-full "><label class="epl-search-label fm-label label_'+key+'" for="'+key+'">'+value.label+'</label><div class="field"><div class="'+key+'"></div></div></div>')
							
							$("."+key).each( function(i){
								var _this_el = $(this).closest('.epl-lf-range-slider');
								$( this ).slider({
									min: min ,
									max: max ,
									range: "min",
									animate : true,
									value: defaultValues,
									slide: function( event, ui ) {
										$( '.'+value.els[0] ).append(new Option((ui.value).formatMoney(0), ui.value));
										$( '.'+value.els[0] ).val( ui.value ).change();
										_this_el.find( ".label_"+key ).html( value.label + '<span class="epl-lf-label-txt"> ' + value.prefix + (ui.value).formatMoney(0) + value.suffix + '</span>');
									},
									stop: function( event, ui ) {
										// $( '#'+value.els[0] ).val( ui.values[ 0 ] );
										// $( '#'+value.els[1] ).val( ui.values[ 1 ] );
										// $( '#'+value.els[1] ).trigger('change');
									},
					                create: function( event, ui ) {
					                    _this_el.find( ".label_"+key ).html( value.label + '<span class="epl-lf-label-txt"> ' + value.prefix + (defaultValues).formatMoney(0) + value.suffix + '</span>');
					                },
								});
							});

						} else {

							var el_0 = current_search_form.find('.'+value.els[0]);
							var el_1 = current_search_form.find('.'+value.els[1]);

							var defaultValues = [ el_0.data('min'), el_0.data('max') ];
							var min = isNaN(Number( el_0.data('min') ) ) 
							? value.min : Number( el_0.data('min') );

							var max = isNaN(Number( el_1.data('max') ) ) 
							? value.max : Number( el_1.data('max') );

							var custom_min = $.getUrlVar( value.els[0] );
							var custom_max = $.getUrlVar( value.els[1] );
							
							if( custom_min !== undefined && custom_min != ''){
								custom_min = parseInt(custom_min);
							}

							if( custom_max !== undefined && custom_max != ''){
								custom_max = parseInt(custom_max);
							}
							if( (custom_min !== undefined && custom_min != '') || (custom_max !== undefined && custom_max != '') ){
								defaultValues = [custom_min, custom_max];
							}


							el_0
							.closest('.epl-search-row')
							.after('<div class="epl-lf-range-slider epl-search-row epl-search-row-select fm-block epl-search-row-full "><label class="epl-search-label fm-label label_'+key+'" for="'+key+'">'+value.label+'</label><div class="field"><div class="'+key+'"></div></div></div>')
							
							current_search_form.find("."+key).each( function(i) {
								var _this_el = $(this).closest('.epl-lf-range-slider');
								$( this ).slider({
									range: true,
									step: current_search_form.find('.'+value.els[1]).has('option').length > 0 ? (Number(current_search_form.find('.'+value.els[1]+' option:eq(2)').val()) - Number(current_search_form.find('.'+value.els[1]+' option:eq(1)').val())) : value.step ,
									min: min ,
									max: max ,
									animate : true,
									values: defaultValues,
									slide: function( event, ui ) {
										el_0.append(new Option((ui.values[ 0 ]).formatMoney(0), ui.values[ 0 ]));
										el_0.append(new Option((ui.values[ 1 ]).formatMoney(0), ui.values[ 1 ]));
										el_1.append(new Option((ui.values[ 0 ]).formatMoney(0), ui.values[ 0 ]));
										el_1.append(new Option((ui.values[ 1 ]).formatMoney(0), ui.values[ 1 ]));
										el_0.val( ui.values[ 0 ] ).change();
										el_1.val( ui.values[ 1 ] ).change();
										_this_el.find( ".label_"+key ).html( value.label + '<span class="epl-lf-label-txt"> ' + value.prefix + (ui.values[ 0 ]).formatMoney(0) + value.suffix +  value.seperator + value.prefix +  (ui.values[ 1 ]).formatMoney(0) +  value.suffix + '</span>');
									},
									stop: function( event, ui ) {
										// $( '#'+value.els[0] ).val( ui.values[ 0 ] );
										// $( '#'+value.els[1] ).val( ui.values[ 1 ] );
										// $( '#'+value.els[1] ).trigger('change');
									},
					                create: function( event, ui ) {
					                    _this_el.find( ".label_"+key ).html( value.label + '<span class="epl-lf-label-txt"> ' + value.prefix + (defaultValues[ 0 ]).formatMoney(0) + value.suffix +  value.seperator + value.prefix +  (defaultValues[ 1 ]).formatMoney(0) +  value.suffix + '</span>');
					                },
								});
							});
						}
						
						
					}
				});

	  		});
	  	},

	  	epl_search : function() {
			$( ".epl-search-form form" ).on( "submit", function( event ) {
				event.preventDefault();
				var url = $( this ).serialize();
				var array = url.split('&');
				var resultArray = [];
				for(i in array){
					if(array[i].split('=')[1]){
						resultArray.push(array[i]);
					}
				}
				var url_params = resultArray.join('&');
				var yourURL = epl_frontend_vars.site_url + "?" + url_params;
				location.href = yourURL;
		    });
	  	},

	  	contact_form_submit : function() {
			$( ".epl-contact-capture-form form" ).on( "submit", function( event ) {
				var form = $(this);
				event.preventDefault();
				var data = $( this ).serializeArray();
				data.push({name: 'action', value: 'epl_contact_capture_action'});
				$.ajax({
					type: "POST",
					url: epl_frontend_vars.ajaxurl,
					data: data,
					dataType : 'json'
				})
				.done(function( msg ) {
					$('.epl-contact-widget-msgs').remove();
					form.after('<div class="epl-contact-widget-msgs epl-contact-widget-msg-'+msg.status+'">'+msg.msg+'</div>');
					if( msg.status == 'success'){
						form.trigger('reset');
					}
					
				});
		    });
	  	}

	};

	/** keep EPL running **/ 
	EPL_Front_Js.init();
});


/**** backward compatiblity *****/

/**
* @depricated since 3.0.5
*/
function eplGetCookie(c_name) {
	return EPL_Front_Js.get_cookie(c_name);
}

/**
@depricated since 3.0.5
* Jquery extension to get URL params value 
*/
jQuery.extend({
  getUrlVars: function(){
    return EPL_Front_Js.get_url_vars();
  },
  getUrlVar: function(name){
    return EPL_Front_Js.get_url_var(name);
  }
});
