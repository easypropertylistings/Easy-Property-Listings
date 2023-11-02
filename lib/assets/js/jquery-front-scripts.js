jQuery( document ).ready(
	function ($) {

		Number.prototype.formatMoney = function(c, d, t){
			var n = this,
			c     = isNaN( c = Math.abs( c ) ) ? 2 : c,
			d     = d == undefined ? epl_frontend_vars.decimal_sep : d,
			t     = t == undefined ? epl_frontend_vars.thousand_sep : t,
			s     = n < 0 ? "-" : "",
			i     = parseInt( n = Math.abs( +n || 0 ).toFixed( c ) ) + "",
			j     = (j = i.length) > 3 ? j % 3 : 0;
			return s + (j ? i.substr( 0, j ) + t : "") + i.substr( j ).replace( /(\d{3})(?=\d)/g, "$1" + t ) + (c ? d + Math.abs( n - i ).toFixed( c ).slice( 2 ) : "");
		};

		EPL_Front_Js = {

			/** Initialize all functions required for EPL **/
			init : function() {

				// Commented out: this.property_search(); code.
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
				if ( $( '#property_id' ).length == 1 ) {

					var EplContactListingTimeout = null;

					$( '#property_id' ).keyup(
						function() {

							if ( $( '#property_id' ).val() == '' ) {
								$( '.epl-property-suggestion' ).fadeOut();
								return false;
							}

							if (EplContactListingTimeout != null) {
								clearTimeout( EplContactListingTimeout );
							}
							var search = $( this ).val();

							data = {
								action: 'epl_search_listing',
								s: search
							};

							EplContactListingTimeout = setTimeout(
								function () {
									EplContactListingTimeout = null;

									$.ajax(
										{
											type: "POST",
											data: data,
											url: ajaxurl,
											success: function (response) {
												if ( $( '.epl-property-suggestion' ).length == 1 ) {
													$( '.epl-property-suggestion' ).replaceWith( response );
												} else {
													$( '#property_id' ).after( response );
												}
												$( '.epl-property-suggestion' ).fadeIn();
											}
										}
									);
								},
								300
							);

						}
					);

					$( document.body ).on(
						'click',
						'.epl-property-suggestion li',
						function( e ) {
							$( '#property_id' ).val( $( this ).data( 'id' ) );
							$( '.epl-property-suggestion' ).fadeOut().remove();
						}
					);

				}
			},

			/**
			 * Sort properties by price
			 *
			 * @since 3.4.44 changed #epl-sort-listings to .epl-sort-listings to support multiple instances of sorting dropdown on page.
			 */
			sort_listings : function() {

				jQuery( '.epl-sort-listings' ).on(
					'change',
					function(){
						var sortby   = jQuery( this ).val();
						var instance = jQuery( this ).data( 'instance-id' );
						var url      = window.location.href;
						var newUrl   = EPL_Front_Js.update_url_param( url, 'sortby', sortby );

						if ( instance != '' ) {
								newUrl = EPL_Front_Js.update_url_param( newUrl, 'instance_id', instance );
						}
						window.location.replace( newUrl );
					}
				);

			},

			/**
			 * Get cookie value
			 *
			 * @since 1.0.0
			 */
			get_cookie : function(c_name) {
				var i,x,y,ARRcookies = document.cookie.split( ";" );

				for (i = 0;i < ARRcookies.length;i++) {
					x = ARRcookies[i].substr( 0,ARRcookies[i].indexOf( "=" ) );
					y = ARRcookies[i].substr( ARRcookies[i].indexOf( "=" ) + 1 );
					x = x.replace( /^\s+|\s+$/g,"" );
					if (x == c_name) {
						return unescape( y );
					}
				}
			},

			// Add / Update a key-value pair in the URL query parameters.
			update_url_param : function (uri, key, value) {
				// Remove the hash part before operating on the uri.
				var i    = uri.indexOf( '#' );
				var hash = i === -1 ? '' : uri.substr( i );
					uri  = i === -1 ? uri : uri.substr( 0, i );

				var re        = new RegExp( "([?&])" + key + "=.*?(&|$)", "i" );
				var separator = uri.indexOf( '?' ) !== -1 ? "&" : "?";
				if (uri.match( re )) {
					uri = uri.replace( re, '$1' + key + "=" + value + '$2' );
				} else {
					uri = uri + separator + key + "=" + value;
				}
				return uri + hash;  // Finally append the hash as well.
			},

			/* author tabs functionality */
			author_tabs : function() {
				jQuery( 'ul.epl-author-tabs li' ).click(
					function(){
						var box_id = jQuery( this ).closest( '.epl-author-box-container' ).attr( 'id' );
						var tab_id = jQuery( this ).attr( 'data-tab' );

						jQuery( '#' + box_id + ' ul.epl-author-tabs li' ).removeClass( 'epl-author-current' );
						jQuery( '#' + box_id + ' .epl-author-tab-content' ).removeClass( 'epl-author-current' );

						jQuery( this ).addClass( 'epl-author-current' );
						jQuery( '#' + box_id + ' #' + tab_id ).addClass( 'epl-author-current' );
					}
				);

			},

			/* render default map on single listings */
			default_map: function() {
                                /* render default map for multiple listings */
                                if (typeof google === 'object' && typeof google.maps === 'object') {
                                        $('.epl-map-shortcode').each(function() {
                                                var eplgeocoder;
                                                var $mapElement = $(this);
                                                var listingcoordinates = $mapElement.data('cord');
                                                var listingaddress = $mapElement.data('address');
                                                var listingmapzoom = $mapElement.data('zoom');
                                                var listid = $mapElement.data('id');
                                                var suburb_mode = $mapElement.data('suburb_mode');
                                                
                                                // Use listing coordinates if already present.
                                                if (listingcoordinates != '') {
                                                        listingcoordinates = listingcoordinates.split(',');
                                                        
                                                        function renderdefaultmap() {
                                                                eplgeocoder = new google.maps.Geocoder();
                                                                eplmapcord = new google.maps.LatLng(listingcoordinates[0], listingcoordinates[1]);
                                                                
                                                                var mapOptions = {
                                                                        center: eplmapcord,
                                                                        zoom: listingmapzoom,
                                                                };
                                                                
                                                                var map = new google.maps.Map($mapElement[0], mapOptions);
                                                                var epl_marker_icon = 'default.png';
                                                                var marker = new google.maps.Marker({
                                                                        map: map,
                                                                        position: eplmapcord,
                                                                        icon: epl_frontend_vars.image_base + epl_marker_icon,
                                                                });
                                                        }
                                                        renderdefaultmap();
                                                } else {
                                                        // If listing coordinates not present, use address to fetch them.
                                                        if (listingaddress != '') {
                                                                eplcodeAddress(listingaddress, suburb_mode, listid, $mapElement, listingmapzoom);
                                                        }
                                                }
                                        });
                                }
                                
                                function eplcodeAddress(listingaddress, suburb_mode, listid, $mapElement, listingmapzoom) {
                                        eplgeocoder = new google.maps.Geocoder();
                                        eplgeocoder.geocode({ 'address': listingaddress }, function(results, status) {
                                                if (status == google.maps.GeocoderStatus.OK) {
                                                        var mapOptions = {
                                                                center: results[0].geometry.location,
                                                                zoom: listingmapzoom,
                                                        };
                                                        
                                                        var map = new google.maps.Map($mapElement[0], mapOptions);
                                                        var epl_marker_icon = 'default.png';
                                                        
                                                        // Don't save suburb coordinates as listing coordinates.
                                                        if (suburb_mode != 1) {
                                                                if (listid > 0) {
                                                                        $.ajax({
                                                                                type: "POST",
                                                                                url: epl_frontend_vars.ajaxurl,
                                                                                data: {
                                                                                        action: "epl_update_listing_coordinates",
                                                                                        coordinates: results[0].geometry.location.toString(),
                                                                                        listid: listid,
                                                                                },
                                                                        }).done(function(msg) {
                                                                                // Successfully updated geocode.
                                                                        });
                                                                }
                                                        } else {
                                                                epl_marker_icon = 'suburb.png';
                                                        }
                                                        
                                                        var marker = new google.maps.Marker({
                                                                map: map,
                                                                position: results[0].geometry.location,
                                                                icon: epl_frontend_vars.image_base + epl_marker_icon,
                                                        });
                                                } else {
                                                        // Error in geocoding.
                                                }
                                        });
                                }
                                
                        },
                        

			/** Set default view for listings on archive pages - gives preference to user selections fallback to admin settings **/
			set_default_view : function() {

				/* force default view to grid if its set to default */
				if ( eplGetCookie( 'preferredView' ) !== undefined) {
					var preferredView = this.get_cookie( 'preferredView' );
					jQuery( '.epl-switch-view ul li[data-view="' + preferredView + '"]' ).trigger( 'click' );

				} else if (epl_frontend_vars.epl_default_view_type == 'grid') {
					jQuery( '.epl-switch-view ul li[data-view="grid"]' ).trigger( 'click' );
				}

			},

			/* Switch view list / grid */
			switch_view : function() {

				/* switch views : grid & list on property archive pages */
				jQuery( '.epl-switch-view ul li' ).click(
					function(){

						var view = jQuery( this ).data( 'view' );
						$.ajax(
							{
								type: "POST",
								url: epl_frontend_vars.ajaxurl,
								data: {
									action: "epl_update_default_view",
									view  : view
								}
							}
						)
						.done(
							function( msg ) {

							}
						);

						if (typeof listingsMasonEnabled != 'undefined') {
							return;
						}

						jQuery( '.epl-switch-view ul li' ).removeClass( 'epl-current-view' );
						jQuery( this ).addClass( 'epl-current-view' );
						if (view == 'grid') {
							jQuery( '.epl-property-blog' ).addClass( 'epl-listing-grid-view' );
							jQuery( '.epl-listing-grid-view.epl-property-blog' ).each(
								function(index){
									index = index + 1;
									if ((index > 0) && (index % 3 == 0)) {
										/* jQuery(this).after('<div class="epl-clearfix"></div>'); */
									}
								}
							);
						} else {
							jQuery( '.epl-property-blog' ).removeClass( 'epl-listing-grid-view' );
						}

					}
				);

			},

			/** Listing search tabs **/
			listing_search_tabs :  function() {
				$( '.epl-search-tabs' ).each(
					function() {
						new EplSearchTabManager( $( this ) );
					}
				);

				/** Trigger form tabs on search results page */
				let formID  = $.getUrlVar( 'instance_id' );
				let tabID   = $.getUrlVar( 'form_tab' );
				let formObj = $( '.epl-search-container[data-instance-id="' + formID + '"]' );

				if ( formObj.length ) {

					if ( formObj.find( 'li[data-tab="epl_ps_tab_' + tabID + '"]' ).length ) {

                        setTimeout(
                            () => {
                                console.log( 'tab switched li[data-tab="epl_ps_tab_' + tabID + '"]' );
                                formObj.find( 'li[data-tab="epl_ps_tab_' + tabID + '"]' ).trigger( 'click' );
                            },
                            200
                        );
					}
				}
			},

			/** Get url params as an array **/
			get_url_vars : function(){
				var vars   = [], hash;
				var hashes = window.location.href.slice( window.location.href.indexOf( '?' ) + 1 ).split( '&' );
				for (var i = 0; i < hashes.length; i++) {
					hash = hashes[i].split( '=' );
					vars.push( hash[0] );
					vars[hash[0]] = hash[1];
				}
				return vars;
			},

			get_url_var : function(name) {
				return this.get_url_vars()[name];
			},

			field_sliders : function() {

				var _search_forms = $( '.epl-search-form' );
				var epl_lf_ranges = epl_frontend_vars.field_sliders;

				_search_forms.each(
					function( form_index ) {

						var current_search_form = $( this );

						$.each(
							epl_lf_ranges,
							function(key, value) {

								elExists = true;

								$.each(
									value.els,
									function(k,v) {
										if ( ! $( '.' + v ).length) {
											elExists = false;
										} else {
											$( '.' + v ).closest( '.epl-search-row' ).hide();
										}
									}
								);

								if (elExists) {

									if (value.els.length < 2) {

										var min = isNaN( Number( $( '.' + value.els[0] ).data( 'min' ) ) )
										? value.min : Number( $( '.' + value.els[0] ).data( 'min' ) );

										var max = isNaN( Number( $( '.' + value.els[0] ).data( 'max' ) ) )
										? value.max : Number( $( '.' + value.els[0] ).data( 'max' ) );

										var defaultValues = min;

										var custom_val = $.getUrlVar( value.els[0] );

										if ( custom_val !== undefined ) {
											defaultValues = parseInt( custom_val );
										}

										$( '.' + value.els[0] )
										.closest( '.epl-search-row' )
										.after( '<div class="epl-lf-range-slider epl-search-row epl-search-row-select fm-block epl-search-row-full "><label class="epl-search-label fm-label label_' + key + '" for="' + key + '">' + value.label + '</label><div class="field"><div class="' + key + '"></div></div></div>' )

										$( "." + key ).each(
											function(i){
												var _this_el = $( this ).closest( '.epl-lf-range-slider' );
												$( this ).slider(
													{
														min: min ,
														max: max ,
														range: "min",
														animate : true,
														value: defaultValues,
														slide: function( event, ui ) {
															$( '.' + value.els[0] ).append( new Option( (ui.value).formatMoney( 0 ), ui.value ) );
															$( '.' + value.els[0] ).val( ui.value ).change();

															_this_el.find( ".label_" + key ).html( value.label + epl_frontend_vars.range_html.replace( '{range_start}', value.prefix + (ui.value).formatMoney( 0 ) + value.suffix ).replace( '{range_sep}', '' ).replace( '{range_end}', '' ) );
														},
														stop: function( event, ui ) {
															// $( '#'+value.els[0] ).val( ui.values[ 0 ] );
															// $( '#'+value.els[1] ).val( ui.values[ 1 ] );
															// $( '#'+value.els[1] ).trigger('change');
														},
														create: function( event, ui ) {
															_this_el.find( ".label_" + key ).html( value.label + epl_frontend_vars.range_html.replace( '{range_start}', value.prefix + (defaultValues).formatMoney( 0 ) + value.suffix ).replace( '{range_sep}', '' ).replace( '{range_end}', '' ) );
														},
													}
												);
											}
										);

									} else {

										var el_0 = current_search_form.find( '.' + value.els[0] );
										var el_1 = current_search_form.find( '.' + value.els[1] );

                                        var initialRange = {};

                                        el_1.find( 'option' ).each(
                                            function() {

                                                initialRange[$( this ).attr( 'value' ) ] = $( this ).text();
                                            }
                                        );

										var defaultValues = [ el_0.data( 'min' ), el_0.data( 'max' ) ];
										var min           = isNaN( Number( el_0.data( 'min' ) ) )
										? value.min : Number( el_0.data( 'min' ) );

										var max = isNaN( Number( el_1.data( 'max' ) ) )
										? value.max : Number( el_1.data( 'max' ) );

										var custom_min = $.getUrlVar( value.els[0] );
										var custom_max = $.getUrlVar( value.els[1] );

										if ( custom_min !== undefined && custom_min != '') {
											custom_min = parseInt( custom_min );
										}

										if ( custom_max !== undefined && custom_max != '') {
											custom_max = parseInt( custom_max );
										}
										if ( (custom_min !== undefined && custom_min != '') || (custom_max !== undefined && custom_max != '') ) {
											defaultValues = [custom_min, custom_max];
										}

										el_0
										.closest( '.epl-search-row' )
										.after( '<div class="epl-lf-range-slider epl-search-row epl-search-row-select fm-block epl-search-row-full "><label class="epl-search-label fm-label label_' + key + '" for="' + key + '">' + value.label + '</label><div class="field"><div class="' + key + '"></div></div></div>' )

										current_search_form.find( "." + key ).each(
											function(i) {
												var _this_el = $( this ).closest( '.epl-lf-range-slider' );
												$( this ).slider(
													{
														range: true,
														step: current_search_form.find( '.' + value.els[1] ).has( 'option' ).length > 0 ? (Number( current_search_form.find( '.' + value.els[1] + ' option:eq(2)' ).val() ) - Number( current_search_form.find( '.' + value.els[1] + ' option:eq(1)' ).val() )) : value.step ,
														min: min ,
														max: max ,
														animate : true,
														values: defaultValues,
														slide: function( event, ui ) {
															el_0.append( new Option( (ui.values[ 0 ]).formatMoney( 0 ), ui.values[ 0 ] ) );
															el_0.append( new Option( (ui.values[ 1 ]).formatMoney( 0 ), ui.values[ 1 ] ) );
															el_1.append( new Option( (ui.values[ 0 ]).formatMoney( 0 ), ui.values[ 0 ] ) );
															el_1.append( new Option( (ui.values[ 1 ]).formatMoney( 0 ), ui.values[ 1 ] ) );
															el_0.val( ui.values[ 0 ] ).change();
															el_1.val( ui.values[ 1 ] ).change();

                                                            let label_0 = value.prefix + (ui.values[ 0 ]).formatMoney( 0 ) + value.suffix;
                                                            let label_1 = value.prefix + (ui.values[ 1 ]).formatMoney( 0 ) + value.suffix;

															_this_el.find( ".label_" + key ).html( value.label + epl_frontend_vars.range_html.replace( '{range_start}', label_0 ).replace( '{range_sep}', value.separator ).replace( '{range_end}', label_1 ) );
														},
														stop: function( event, ui ) {
															// $( '#'+value.els[0] ).val( ui.values[ 0 ] );
															// $( '#'+value.els[1] ).val( ui.values[ 1 ] );
															// $( '#'+value.els[1] ).trigger('change');
														},
														create: function( event, ui ) {

                                                            _this_el.find( ".label_" + key ).html( value.label + '<span class="epl-lf-label-txt"> ' + value.prefix + (defaultValues[ 0 ]).formatMoney( 0 ) + value.suffix + value.separator + value.prefix + (defaultValues[ 1 ]).formatMoney( 0 ) + value.suffix + '</span>' );

                                                            let label_0 = (defaultValues[ 0 ]).formatMoney( 0 ) + value.suffix;
                                                            let label_1 = (defaultValues[ 1 ]).formatMoney( 0 ) + value.suffix;

															_this_el.find( ".label_" + key ).html( value.label + epl_frontend_vars.range_html.replace( '{range_start}', label_0 ).replace( '{range_sep}', value.separator ).replace( '{range_end}', label_1 ) );

														},
													}
												);
											}
										);
									}

								}
							}
						);
					}
				);
			},

			/**
			 * Search Functions
			 *
			 * @since 1.0.0
			 * @since 3.4.45 Fix: JS Issue - Assignment to constant variable.
			 */
			epl_search : function() {
				$( ".epl-search-form form" ).on(
					"submit",
					function( event ) {
						event.preventDefault();
						var url         = $( this ).serialize();
						var array       = url.split( '&' );
						var resultArray = [];
						for (var i in array) {
							if ( 'string' === typeof array[i] && array[i].split( '=' )[1] ) {
								resultArray.push( array[i] );
							}
						}
						var url_params = resultArray.join( '&' );
						var yourURL    = epl_frontend_vars.site_url + "?" + url_params;
						location.href  = yourURL;
					}
				);
			},

			contact_form_submit : function() {
				$( ".epl-contact-capture-form form" ).on(
					"submit",
					function( event ) {
						var form = $( this );
						event.preventDefault();
						var data = $( this ).serializeArray();
						data.push( {name: 'action', value: 'epl_contact_capture_action'} );
						$.ajax(
							{
								type: "POST",
								url: epl_frontend_vars.ajaxurl,
								data: data,
								dataType : 'json'
							}
						)
						.done(
							function( msg ) {
								$( '.epl-contact-widget-msgs' ).remove();
								form.after( '<div class="epl-contact-widget-msgs epl-contact-widget-msg-' + msg.status + '">' + msg.msg + '</div>' );
								if ( msg.status == 'success') {
									form.trigger( 'reset' );
								}

							}
						);
					}
				);
			}

		};

				/**
				 * EPL Search Tabs class for EPL Search tabs.
				 */
		class EplSearchTabManager {

			constructor($tabContainer) {
                this.$tabContainer = $tabContainer;
                this.$tabLinks     = this.$tabContainer.find( 'li.tab-link' );
                this.$tabContents  = this.$tabContainer.parent().find( '.epl-search-form' );
                this.setupListeners();
			}

			setupListeners() {
                this.$tabLinks.on(
                    'click',
                    (event) => {
                    this.switchTab( $( event.currentTarget ) );
                    }
                );
			}

			switchTab($clickedTab) {
				const tab_id = $clickedTab.attr( 'data-tab' );
				this.$tabLinks.removeClass( 'epl-sb-current' );
				this.$tabContents.removeClass( 'epl-sb-current' );
				$clickedTab.addClass( 'epl-sb-current' );
				$( "#" + tab_id ).addClass( 'epl-sb-current' );
				this.$tabLinks.trigger( 'after_tab_switch', { content : $( "#" + tab_id ), tab_id : tab_id } );

			}
		}

		/** Keep EPL running. **/
		EPL_Front_Js.init();
	}
);


/**** Backward compatiblity *****/

/**
 * Cookie
 *
 * @depricated since 3.0.5
 */
function eplGetCookie(c_name) {
	return EPL_Front_Js.get_cookie( c_name );
}

/**
 * Jquery extension to get URL params value
 *
 * @depricated since 3.0.5
 */
jQuery.extend(
	{
		getUrlVars: function(){
			return EPL_Front_Js.get_url_vars();
		},
		getUrlVar: function(name){
			return EPL_Front_Js.get_url_var( name );
		}
	}
);
