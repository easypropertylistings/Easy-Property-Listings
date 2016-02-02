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
	 * Lead management screen JS
	 */
	var EPL_Lead = {

		vars: {
			lead_card_wrap_editable:  $( '.epl-lead-card-wrapper .editable' ),
			lead_card_wrap_edit_item: $( '.epl-lead-card-wrapper .edit-item' ),
			user_id: $('input[name="leadinfo[user_id]"]'),
			state_input: $(':input[name="leadinfo[state]"]'),
			note: $( '#lead-note' ),
		},
		init : function() {
			this.edit_lead();
			this.user_search();
			this.remove_user();
			this.cancel_edit();
			this.change_country();
			this.add_note();
			this.delete_checked();
		},
		edit_lead: function() {
			$( document.body ).on( 'click', '#edit-lead', function( e ) {
				e.preventDefault();

				EPL_Lead.vars.lead_card_wrap_editable.hide();
				EPL_Lead.vars.lead_card_wrap_edit_item.fadeIn().css( 'display', 'block' );
			});
		},
		user_search: function() {
			// Upon selecting a user from the dropdown, we need to update the User ID
			$( document.body ).on('click.eplSelectUser', '.epl_user_search_results a', function( e ) {
				e.preventDefault();
				var user_id = $(this).data('userid');
				EPL_Lead.vars.user_id.val(user_id);
			});
		},
		remove_user: function() {
			$( document.body ).on( 'click', '#disconnect-lead', function( e ) {
				e.preventDefault();
				var lead_id = $('input[name="leadinfo[id]"]').val();

				var postData = {
					epl_action:   'disconnect-userid',
					lead_id: lead_id,
					_wpnonce:     $( '#edit-lead-info #_wpnonce' ).val()
				};

				$.post(ajaxurl, postData, function( response ) {

					window.location.href=window.location.href;

				}, 'json');

			});
		},
		cancel_edit: function() {
			$( document.body ).on( 'click', '#epl-edit-lead-cancel', function( e ) {
				e.preventDefault();
				EPL_Lead.vars.lead_card_wrap_edit_item.hide();
				EPL_Lead.vars.lead_card_wrap_editable.show();

				$( '.epl_user_search_results' ).html('');
			});
		},
		change_country: function() {
			$('select[name="leadinfo[country]"]').change(function() {
				var $this = $(this);
				data = {
					action: 'epl_get_shop_states',
					country: $this.val(),
					field_name: 'leadinfo[state]'
				};
				$.post(ajaxurl, data, function (response) {
					if( 'nostates' == response ) {
						EPL_Lead.vars.state_input.replaceWith( '<input type="text" name="' + data.field_name + '" value="" class="epl-edit-toggles medium-text"/>' );
					} else {
						EPL_Lead.vars.state_input.replaceWith( response );
					}
				});

				return false;
			});
		},
		add_note : function() {
			$( document.body ).on( 'click', '#add-lead-note', function( e ) {
				e.preventDefault();
				var postData = {
					epl_action : 'add-lead-note',
					lead_id : $( '#lead-id' ).val(),
					listing_id : $( '#lead-note-listing' ).val(),
					note_notify : $( '#lead-note-notify' ).val(),
					lead_note : EPL_Lead.vars.note.val(),
					add_lead_note_nonce: $( '#add_lead_note_nonce' ).val()
				};

				if( postData.lead_note ) {

					$.ajax({
						type: "POST",
						data: postData,
						url: ajaxurl,
						success: function ( response ) {
							$( '#epl-lead-notes' ).prepend( response );
							$( '.epl-no-lead-notes' ).hide();
							EPL_Lead.vars.note.val( '' );
						}
					}).fail( function ( data ) {
						if ( window.console && window.console.log ) {
							console.log( data );
						}
					});

				} else {
					var border_color = EPL_Lead.vars.note.css( 'border-color' );
					EPL_Lead.vars.note.css( 'border-color', 'red' );
					setTimeout( function() {
						EPL_Lead.vars.note.css( 'border-color', border_color );
					}, 500 );
				}
			});
		},
		delete_checked: function() {
			$( '#epl-lead-delete-confirm' ).change( function() {
				var records_input = $('#epl-lead-delete-records');
				var submit_button = $('#epl-delete-lead');

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
	EPL_Lead.init();
	
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


