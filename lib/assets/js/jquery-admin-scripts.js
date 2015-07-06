//init highlighter

hljs.configure({
  tabReplace: '    ', // 4 spaces
})
hljs.initHighlightingOnLoad();

jQuery(document).ready(function($) {

	var formfield;

	if( $('input[name="epl_upload_button"]').length ) {
		$('input[name="epl_upload_button"]').click(function() {
			formfield = $(this);
			tb_show('', 'media-upload.php?TB_iframe=true');
			return false;
		});
	
		window.old_tb_remove = window.tb_remove;
		window.tb_remove = function() {
			window.old_tb_remove();
			formfield = null;
		};
		window.original_send_to_editor = window.send_to_editor;
		window.send_to_editor = function(html){
			if (formfield) {
				fileurl = $('img', html).attr('src');
				$(formfield).closest('div').find('input[type="text"]').val(fileurl);
				$(formfield).closest('div').find('img').attr('src', fileurl);
				tb_remove();
			} else {
				window.original_send_to_editor(html);
			}
		};
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
	
	/* add datepicker for input type date */
	if($( ".epldatepicker" ).length){
		$( ".epldatepicker" ).datetimepicker({
			format: "Y-m-d",
			'timepicker':false,
		});
	}
	
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
		
		jQuery('.epl-fields-tab-menu ul li').removeClass('epl-fields-menu-current');
		jQuery('.epl-fields-single-menu').removeClass('epl-fields-field-current');
		
		// show / hide corresponding help text
		jQuery('.epl-field-intro').removeClass('active epl-show-help');
		jQuery('[data-help="'+fields_tab_id+'"]').addClass('active epl-show-help');

		jQuery(this).addClass('epl-fields-menu-current');
		console.log("#tab-menu-"+fields_tab_id);
		jQuery("#"+fields_tab_id).addClass('epl-fields-field-current');
	});
	jQuery('.epl-fields-tab-menu ul li:first').trigger('click');
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
