jQuery(document).ready(function($) {

	var formfield;

	function epl_update_query_string(key, value, url) {
		if (!url) url = window.location.href;
		var re = new RegExp("([?&])" + key + "=.*?(&|#|$)(.*)", "gi"),
		    hash;

		if (re.test(url)) {
		    if (typeof value !== 'undefined' && value !== null)
		        return url.replace(re, '$1' + key + "=" + value + '$2$3');
		    else {
		        hash = url.split('#');
		        url = hash[0].replace(re, '$1$3').replace(/(&|\?)$/, '');
		        if (typeof hash[1] !== 'undefined' && hash[1] !== null) 
		            url += '#' + hash[1];
		        return url;
		    }
		}
		else {
		    if (typeof value !== 'undefined' && value !== null) {
		        var separator = url.indexOf('?') !== -1 ? '&' : '?';
		        hash = url.split('#');
		        url = hash[0] + separator + key + '=' + value;
		        if (typeof hash[1] !== 'undefined' && hash[1] !== null) 
		            url += '#' + hash[1];
		        return url;
		    }
		    else
		        return url;
		}
	}

    if ($('input[name="epl_upload_button"]').length > 0) {
		if ( typeof wp !== 'undefined' && wp.media && wp.media.editor) {
		    $(document).on('click', 'input[name="epl_upload_button"]', function(e) {
		        e.preventDefault();
		        var button = $(this);
		        var id = button.prev();
		        wp.media.editor.send.attachment = function(props, attachment) {
		        	console.log(attachment);
		            id.val(attachment.url);
		        };
		        wp.media.editor.open(button);
		        return false;
		    });
		}
	}

	$('.epl-geocoder-button').click(function() {
		var $obj = $(this);
		$obj.parent().addClass('disabled');
		if($obj.closest('form').find('#property_address_sub_number').length) {
			listingUnit = $obj.closest('form').find('#property_address_sub_number').val();
		} else if($obj.closest('form').find('#property_address_lot_number').length) {
			listingUnit = $obj.closest('form').find('#property_address_lot_number').val();
		} else {
			listingUnit = '';
		}
		$.ajax({
			type: "POST",
			url: ajaxurl,
			data: {
				'property_address_sub_number'	:	listingUnit,
				'property_address_street_number':	$obj.closest('form').find('#property_address_street_number').val(),
				'property_address_street'		:	$obj.closest('form').find('#property_address_street').val(),
				'property_address_suburb'		:	$obj.closest('form').find('#property_address_suburb').val(),
				'property_address_state'		:	$obj.closest('form').find('#property_address_state').val(),
				'property_address_postal_code'	:	$obj.closest('form').find('#property_address_postal_code').val(),
				'action'						:	'epl_get_geocoordinates'
			},
			success: function(response) {
				$obj.prev('input').val( response );
				$obj.parent().removeClass('disabled');
				
				if( $obj.next('iframe').length ) {
					if(response != '') {
						$obj.next('iframe').attr('src', '//maps.google.com/?q='+response+'&output=embed&z=14');
					} else {
						$obj.next('iframe').remove();
					}
				} else {
					$obj.after('<iframe width="100%" height="200" frameborder="0" scrolling="no" src="//maps.google.com/?q='+response+'&output=embed&z=14" style="margin:5px 0 0 0;"></iframe>');
				}
			}
		});
	});

	$('.dependency-true').each(function() {
		var $this = $(this);
		var data_parent = $this.attr('data-parent');
		if( $('select[name="'+data_parent+'"]').length) {
			if( $this.attr('data-type') == 'taxonomy' ) {
				var default_value = $this.attr('data-default');
				$('select[name="'+data_parent+'"]').change(function() {
					$.ajax({
						type: "POST",
						url: ajaxurl,
						data: {
							'parent_id'		:	$(this).val(),
							'type_name'		:	$this.attr('data-type-name'),
							'type'			:	$this.attr('data-type'),
							'default_value'	:	default_value,
							'action'		:	'epl_get_terms_drop_list'
						},
						success: function(response) {
							$this.html( response );
						}
					});
				}).trigger('change');
			}
		}
	});
	
	/* add datepicker for input type date 
	if($( ".epldatepicker" ).length){
		$( ".epldatepicker" ).datetimepicker({
            'timepicker':false,
            format: "Y-m-d",
            validateOnBlur: false,
            onSelectTime:function(ct,$i){
                var value = $i.val();
                value = value.replace(' ', 'T');
                $i.val(value);
            },
            onSelectDate:function(ct,$i){
                var value = $i.val();
                value = value.replace(' ', 'T');
                $i.val(value);
            },
            onGenerate:function(ct,$i){
                var value = $i.val();
                value = value.replace(' ', 'T');
                $i.val(value);
            }   
            
		});
	}
	*/
	
	if($( "#property_auction" ).length){
		$( "#property_auction" ).datetimepicker({
			format: "Y-m-d H:i",
			validateOnBlur: false,
			onSelectTime:function(ct,$i){
				var value = $i.val();
				value = value.replace(' ', 'T');
				$i.val(value);
			},
			onSelectDate:function(ct,$i){
				var value = $i.val();
				value = value.replace(' ', 'T');
				$i.val(value);
			},
			onGenerate:function(ct,$i){
			 	var value = $i.val();
				value = value.replace(' ', 'T');
				$i.val(value);
			}
		});
	}
	
	if($( "#property_sold_date" ).length){
		$( "#property_sold_date" ).datetimepicker({
			'timepicker':false,
			format: "Y-m-d",
			validateOnBlur: false,
			onSelectTime:function(ct,$i){
				var value = $i.val();
				value = value.replace(' ', 'T');
				$i.val(value);
			},
			onSelectDate:function(ct,$i){
				var value = $i.val();
				value = value.replace(' ', 'T');
				$i.val(value);
			},
			onGenerate:function(ct,$i){
			 	var value = $i.val();
				value = value.replace(' ', 'T');
				$i.val(value);
			}
		});
	}
	
		if($( "#property_date_available" ).length){
		$( "#property_date_available" ).datetimepicker({
			'timepicker':false,
			format: "Y-m-d",
			validateOnBlur: false,
			onSelectTime:function(ct,$i){
				var value = $i.val();
				value = value.replace(' ', 'T');
				$i.val(value);
			},
			onSelectDate:function(ct,$i){
				var value = $i.val();
				value = value.replace(' ', 'T');
				$i.val(value);
			},
			onGenerate:function(ct,$i){
			 	var value = $i.val();
				value = value.replace(' ', 'T');
				$i.val(value);
			}
		});
	}
	
	if($( "#property_inspection_times" ).length){
	
		$( "#property_inspection_times" ).hide();
		var eplAddedInspection = $( "#property_inspection_times" ).val();
		eplAddedInspection = eplAddedInspection.split('\n');
		
		var epl_inspection_markup = epl_generate_inspection_markup();
		epl_inspection_markup = '<tr class="form-field"><td>'+epl_inspection_markup+'</td></tr>';
		$( "#property_inspection_times" ).closest('.form-field').after(epl_inspection_markup);
		
		if($.trim(eplAddedInspection) != '') {
			$.each( eplAddedInspection, function( key, value ) {
				$('.epl-added-inspection').append('<span><span class="epl-inspection-text">'+value+'</span><span class="del-inspection-time">X</span></span>');
			});
		}
		
		jQuery('#epl-inspection-date').datetimepicker({'timepicker':false, 'format':'d-M-Y','closeOnDateSelect':true,'allowBlank':false});
		jQuery('#epl-inspection-start-hh').datetimepicker({'datepicker':false,'format':'h','hours12':true});
		jQuery('#epl-inspection-start-mm').datetimepicker({
			'datepicker':false,
			'format':'i',
			'allowTimes': ['00:00','00:05','00:10','00:15','00:20','00:25','00:30','00:35','00:40','00:45','00:50','00:55']
		});
		jQuery('#epl-inspection-end-hh').datetimepicker({'datepicker':false,'format':'h','hours12':true});
		jQuery('#epl-inspection-end-mm').datetimepicker({
			'datepicker':false,
			'format':'i',
			'allowTimes': ['00:00','00:05','00:10','00:15','00:20','00:25','00:30','00:35','00:40','00:45','00:50','00:55']
		});

		

		
	}
	
	if(jQuery("form").length) {
		jQuery("form").validationEngine();
	}
	
	/* Handle Delition of inspection times */
	$(document).on('click','.del-inspection-time',function() {
		var currInspection 		= $(this).parent().find('.epl-inspection-text').text();
		var eplAddedInspection 	= $( "#property_inspection_times" ).val();
		eplAddedInspection 		= eplAddedInspection.split('\n');
		eplAddedInspection 		= jQuery.grep(eplAddedInspection, function(value) {
									  return value != currInspection;
									});
		eplAddedInspection 		= eplAddedInspection.join('\n');
		$( "#property_inspection_times" ).val(eplAddedInspection);
		 $(this).parent().fadeOut(600,function(){
		 	$(this).remove();
		 })

	});
	
	/* Handle addition of inspection time */
	
	$(document).on('click','#epl-inspection-add',function(e) {
		e.preventDefault();
		// make inspection time as per format
		var added = $('#epl-inspection-date').val()+' '+$('#epl-inspection-start-hh').val()+':'+$('#epl-inspection-start-mm').val()+$('#epl-inspection-start-ampm').val()+' to '+$('#epl-inspection-end-hh').val()+':'+$('#epl-inspection-end-mm').val()+$('#epl-inspection-end-ampm').val();

		var eplAddedInspection 	= $( "#property_inspection_times" ).val();
		eplAddedInspection 		= eplAddedInspection.split('\n');
		
		if($.inArray( added, eplAddedInspection ) == -1 ) {
			eplAddedInspection.push(added);
			var newInspection = $('<span><span class="epl-inspection-text">'+added+'</span><span class="del-inspection-time">X</span></span>');
			eplAddedInspection 		= eplAddedInspection.join('\n');
			$( "#property_inspection_times" ).val(eplAddedInspection);
			newInspection.hide().appendTo('.epl-added-inspection').fadeIn(600);
		
		}

	});
	
	$('.epl-price-bar').hover(
		   function(){ $(this).addClass('shine') },
		   function(){ $(this).removeClass('shine') }
	)
	
	/* Extensions menus tabs */
	jQuery('.epl-fields-tab-menu ul li').click(function(){
		var fields_tab_id = jQuery(this).attr('data-tab');
		
		/** hack to not load page on click of sub tabs **/
		var revised_url = epl_update_query_string('sub_tab',fields_tab_id);
		history.pushState('', '', revised_url);
		
		jQuery('.epl-fields-tab-menu ul li').removeClass('epl-fields-menu-current');
		jQuery('.epl-fields-single-menu').removeClass('epl-fields-field-current');
		
		// show / hide corresponding help text
		jQuery('.epl-field-intro').removeClass('active epl-show-help');
		jQuery('[data-help="'+fields_tab_id+'"]').addClass('active epl-show-help');

		jQuery(this).addClass('epl-fields-menu-current');
		console.log("#tab-menu-"+fields_tab_id);
		jQuery("#"+fields_tab_id).addClass('epl-fields-field-current');
	});
	//jQuery('.epl-fields-tab-menu ul li:first').trigger('click');
	
	/**
	 * Contact management screen JS
	 */
	var EPL_Contact = {

		vars: {
			contact_card_wrap_editable:  $( '.epl-contact-card-wrapper .editable' ),
			contact_card_wrap_edit_item: $( '.epl-contact-card-wrapper .edit-item' ),
			user_id: $('input[name="contactinfo[user_id]"]'),
			state_input: $(':input[name="contactinfo[state]"]'),
			note: $( '#contact_activity_content' ),
		},
		init : function() {
			this.edit_contact();
			this.user_search();
			this.remove_user();
			this.cancel_edit();
			this.change_country();
			this.add_note();
            this.add_listing();
			this.delete_checked();
		},
		edit_contact: function() {
			$( document.body ).on( 'click', '#edit-contact', function( e ) {
				e.preventDefault();

				EPL_Contact.vars.contact_card_wrap_editable.hide();
				EPL_Contact.vars.contact_card_wrap_edit_item.fadeIn().css( 'display', 'block' );
			});
		},
		user_search: function() {
			// Upon selecting a user from the dropdown, we need to update the User ID
			$( document.body ).on('click.eplSelectUser', '.epl_user_search_results a', function( e ) {
				e.preventDefault();
				var user_id = $(this).data('userid');
				EPL_Contact.vars.user_id.val(user_id);
			});
		},
		remove_user: function() {
			$( document.body ).on( 'click', '#disconnect-contact', function( e ) {
				e.preventDefault();
				var contact_id = $('input[name="contactinfo[id]"]').val();

				var postData = {
					epl_action:   'disconnect-userid',
					contact_id: contact_id,
					_wpnonce:     $( '#edit-contact-info #_wpnonce' ).val()
				};

				$.post(ajaxurl, postData, function( response ) {

					window.location.href=window.location.href;

				}, 'json');

			});
		},
		cancel_edit: function() {
			$( document.body ).on( 'click', '#epl-edit-contact-cancel', function( e ) {
				e.preventDefault();
				EPL_Contact.vars.contact_card_wrap_edit_item.hide();
				EPL_Contact.vars.contact_card_wrap_editable.show();

				$( '.epl_user_search_results' ).html('');
			});
		},
		change_country: function() {
			$('select[name="contactinfo[country]"]').change(function() {
				var $this = $(this);
				data = {
					action: 'epl_get_shop_states',
					country: $this.val(),
					field_name: 'contactinfo[state]'
				};
				$.post(ajaxurl, data, function (response) {
					if( 'nostates' == response ) {
						EPL_Contact.vars.state_input.replaceWith( '<input type="text" name="' + data.field_name + '" value="" class="epl-edit-toggles medium-text"/>' );
					} else {
						EPL_Contact.vars.state_input.replaceWith( response );
					}
				});

				return false;
			});
		},
		add_note : function() {
			$( document.body ).on( 'click', '#contact_activity_submit', function( e ) {
				e.preventDefault();
				var postData = {
					epl_action : 'add-contact-note',
					contact_id : $( '#epl_contact_id' ).val(),
					listing_id : $( '#contact_activity_listing' ).val(),
					note_type : $( '#contact_activity_type' ).val(),
					contact_note : EPL_Contact.vars.note.val(),
					add_contact_note_nonce: $( '#add_contact_note_nonce' ).val()
				};

				if( postData.contact_note ) {

					$.ajax({
						type: "POST",
						data: postData,
						url: ajaxurl,
						success: function ( response ) {
							EPL_Contact.vars.note.val( '' );
                            $('.epl-contact-activities tbody').prepend(response);
						}
					}).fail( function ( data ) {
						if ( window.console && window.console.log ) {
							console.log( data );
						}
					});

				} else {
					var border_color = EPL_Contact.vars.note.css( 'border-color' );
					EPL_Contact.vars.note.css( 'border-color', 'red' );
					setTimeout( function() {
						EPL_Contact.vars.note.css( 'border-color', border_color );
					}, 500 );
				}
			});
		},
        add_listing : function() {
            $( document.body ).on( 'click', '#contact_listing_submit', function( e ) {
                e.preventDefault();
                var postData = data = $('#epl_contact_add_listing_form').serialize();
                postData += '&epl_action=add-contact-listing&contact_id='+$( '#epl_contact_id' ).val();

                if( postData ) {
                   $.ajax({
                        type: "POST",
                        data: postData,
                        url: ajaxurl,
                        success: function ( response ) {
                            EPL_Contact.vars.note.val( '' );
                            $('.epl-contact-listings tbody').prepend(response);
                        }
                    }).fail( function ( data ) {
                        if ( window.console && window.console.log ) {
                            console.log( data );
                        }
                    });

                } else {
                    var border_color = EPL_Contact.vars.note.css( 'border-color' );
                    EPL_Contact.vars.note.css( 'border-color', 'red' );
                    setTimeout( function() {
                        EPL_Contact.vars.note.css( 'border-color', border_color );
                    }, 500 );
                }
            });
        },
		delete_checked: function() {
			$( '#epl-contact-delete-confirm' ).change( function() {
				var records_input = $('#epl-contact-delete-records');
				var submit_button = $('#epl-delete-contact');

				if ( $(this).prop('checked') ) {
					records_input.attr('disabled', false);
					submit_button.attr('disabled', false);
				} else {
					records_input.attr('disabled', true);
					records_input.prop('checked', false);
					submit_button.attr('disabled', true);
				}
			});
		}

	};
	EPL_Contact.init();

    $('.contact-action-category').on('click',function(e) {
        e.preventDefault();
        e.stopPropagation();
        jQuery('.contact_category_suggestions').slideToggle(100);
    });

    $('.contact-action-tag').on('click',function(e) {
        e.preventDefault();
        e.stopPropagation();
        jQuery('.contact-tags-find').slideToggle(100);
        $('#contact-tag-hint').focus();
    });


    $(document).on('click',function(e) {

        if($('.contact_category_suggestions').is(':visible')) {
            jQuery('.contact_category_suggestions').slideUp(100);
        }

        if($('.contact-tags-find').is(':visible') && ! $('.contact-tags-find > input').is(':focus') ) {
            jQuery('.contact-tags-find').slideUp(100);
        }

    });

    $('.contact_category_suggestions > li >  a').on('click',function(e) {
        e.preventDefault();
        var label = $(this).data('label')
        $.ajax({
            type: "POST",
            data: {

                action : "contact_category_update",
                type : $(this).data('key'),
                contact_id : $('#epl_contact_id').val()
            },
            dataType: "json",
            url: ajaxurl,
            success: function (success) {
                if(success) {
                    $('.epl-contact-title span').text(label);
                }

            }
        });
    });

    $('.contact_tags_suggestions > li').on('click',function(e) {
        e.preventDefault();
        var data = {
            action : "contact_tags_update",
            contact_id : $('#epl_contact_id').val(),
            term_id : $(this).data('id'),
            bg : $(this).data('bg'),
            label : $(this).text(),
        }
        if( $('#contact-tag-'+data.term_id).length >= 1 )
            return false;

        $.ajax({
            type: "POST",
            data: data,
            url: ajaxurl,
            success: function (success) {
                $('#contact-tag-hint').val('');
                $('.contact_tags_suggestions > li').hide();
                if(success) {
                    $('#contact-tag-hint').val('');
                    var tpl = '<li style="background:'+data.bg+'" data-id="'+data.term_id+'" id="contact-tag-'+data.term_id+'">'+data.label+'<span class="dashicons dashicons-no contact-tag-del"></span></li>';
                    $('.contact-assigned-tags').append(tpl);
                }

            }
        });
    });

    $('.contact-assigned-tags').on('click','.contact-tag-del',function(e) {
        e.preventDefault();
        var _this = $(this);
        var data = {
            action : "contact_tag_remove",
            contact_id : $('#epl_contact_id').val(),
            term_id : $(this).parent().data('id'),
            label : $(this).parent().text(),
        }
        $.ajax({
            type: "POST",
            data: data,
            url: ajaxurl,
            success: function (success) {
                if(success)
                    _this.parent().fadeIn(200).remove();

            }
        });
    });

    $('#contact-tag-hint').keyup(function(){
        var valThis = $(this).val();

        if(valThis == '')
            return false;

        $('.contact_tags_suggestions>li').each(function(){
            var text = $(this).text().toLowerCase();
            (text.indexOf(valThis) == 0) ? $(this).show() : $(this).hide();
        });
    });

    $('.epl-contact-add-activity').on('click',function() {
        $('#epl-contact-add-activity-wrap').slideToggle(100);
    });

    $('.epl-contact-add-listing').on('click',function() {
        $('#epl-contact-add-listing-wrap').slideToggle(100);
    });


    $('#epl-item-tables-wrapper').on('click','th',function(e) {

        e.preventDefault();
        var _this = $(this);
        $('#epl-contact-table-orderby').val($(this).data('sort'));
        console.log($('#epl-contact-table-orderby').val());

        if( _this.hasClass('epl-sorted-desc') ) {
            _this.addClass('epl-sorted-asc');
            $('#epl-contact-table-order').val('ASC');
        } else {
            $('#epl-contact-table-order').val('DESC');
            _this.addClass('epl-sorted-desc');
        }
        var data = {
            action : "epl_contact_get_activity_table",
            contact : $('#epl_contact_id').val(),
            number : 10, // change later
            paged : 1,
            orderby : $('#epl-contact-table-orderby').val(),
            order : $('#epl-contact-table-order').val()

        }
        $.ajax({
            type: "POST",
            data: data,
            url: ajaxurl,
            success: function (success) {
                if(success) {
                    $('#epl-contact-activity-table-wrapper').html(success);
                }

            }
        });
    });

    $(document).on('click','.epl-contact-load-activities',function(e) {
        var next = $(this).data('page');
        var data = {
            action : "epl_contact_get_activity_table",
            contact : $('#epl_contact_id').val(),
            number : 10, // change later
            paged : next,
            orderby : $('#epl-contact-table-orderby').val(),
            order : $('#epl-contact-table-order').val()

        }
        $.ajax({
            type: "POST",
            data: data,
            url: ajaxurl,
            success: function (success) {
                if(success != false) {
                    var newenteries = $(success).find('tbody').html();
                    var pager = $(success).find('.epl-contact-load-activities').data('page');
                    $('#epl-contact-activity-table-wrapper').find('tbody').append(newenteries);
                    $('.epl-contact-load-activities').data('page',pager);
                }

            }
        });
    });
	
		/**
	 * Reports / Exports screen JS
	 */
	var EPL_Reports = {

		init : function() {
			this.date_options();
		},

		date_options : function() {

			// Show hide extended date options
			$( '#epl-graphs-date-options' ).change( function() {
				var $this = $(this),
					date_range_options = $( '#epl-date-range-options' );

				if ( 'other' === $this.val() ) {
					date_range_options.show();
				} else {
					date_range_options.hide();
				}
			});

		},


	};
	EPL_Reports.init();
	
	// Ajax user search
	$('.epl-ajax-user-search').keyup(function() {
		var user_search = $(this).val();
		var exclude     = '';

		if ( $(this).data('exclude') ) {
			exclude = $(this).data('exclude');
		}

		$('.edd-ajax').show();
		data = {
			action: 'epl_search_users',
			user_name: user_search,
			exclude: exclude
		};

		document.body.style.cursor = 'wait';

		$.ajax({
			type: "POST",
			data: data,
			dataType: "json",
			url: ajaxurl,
			success: function (search_response) {

				$('.epl-ajax').hide();
				$('.epl_user_search_results').removeClass('hidden');
				$('.epl_user_search_results span').html('');
				$(search_response.results).appendTo('.epl_user_search_results span');
				document.body.style.cursor = 'default';
			}
		});
	});
	
	$( document.body ).on('click.eplSelectUser', '.epl_user_search_results span a', function(e) {
		e.preventDefault();
		var login = $(this).data('login');
		$('.epl-ajax-user-search').val(login);
		$('.epl_user_search_results').addClass('hidden');
		$('.epl_user_search_results span').html('');
	});

	$( document.body ).on('click.eplCancelUserSearch', '.epl_user_search_results a.epl-ajax-user-cancel', function(e) {
		e.preventDefault();
		$('.epl-ajax-user-search').val('');
		$('.epl_user_search_results').addClass('hidden');
		$('.epl_user_search_results span').html('');
	});

});

function epl_generate_inspection_markup() {
	var tpl;
	var Year = new Date().getFullYear();
	tpl = '<div class="epl-added-inspection"></div><div id="epl-inspection-markup" class="epl-inspection-markup">';
	tpl +=	'<input type="text" style="width:6em;" autocomplete="off" id="epl-inspection-date" maxlength="2" size="2" placeholder="01"> ';
	tpl +=	' From ';
	tpl +=	'<input class="validate[custom[onlyNumber]]" type="text" autocomplete="off" id="epl-inspection-start-hh" maxlength="2" size="2" placeholder="01">';
	tpl +=	':<input type="text" autocomplete="off" id="epl-inspection-start-mm" maxlength="2" size="2" placeholder="01"> ';
	tpl +=	'<select id="epl-inspection-start-ampm" class="epl-inspection-ampm">';
	tpl +=		'<option value="AM">AM</option>';
	tpl +=		'<option value="PM">PM</option>';
	tpl +=	'</select>';
	tpl +=	' | To ';
	tpl +=	'<input class="validate[custom[onlyNumber]]" type="text" autocomplete="off" id="epl-inspection-end-hh" maxlength="2" size="2" placeholder="01">';
	tpl +=	':<input class="validate[custom[onlyNumber]]" type="text" autocomplete="off" id="epl-inspection-end-mm" maxlength="2" size="2" placeholder="01"> ';

	tpl +=	'<select id="epl-inspection-end-ampm" class="epl-inspection-ampm">';
	tpl +=		'<option value="AM">AM</option>';
	tpl +=		'<option value="PM">PM</option>';
	tpl +=	'</select>';

	tpl += '<a id="epl-inspection-add" class="button">Add</a></div>';
	return tpl;
}


