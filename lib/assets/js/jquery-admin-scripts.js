jQuery(document).ready(function($) {

	if($( "#property_auction" ).length){
		$( "#property_auction,#property_sold_date" ).datetimepicker({
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
	
	
	if($( "#property_inspection_times" ).length){
	
		$( "#property_inspection_times" ).hide();
		var eplAddedInspection = $( "#property_inspection_times" ).val();
		eplAddedInspection = eplAddedInspection.split('\n');
		
		var epl_inspection_markup = epl_generate_inspection_markup();
		epl_inspection_markup = '<tr class="form-field"><td>'+epl_inspection_markup+'</td></tr>';
		$( "#property_inspection_times" ).closest('.form-field').after(epl_inspection_markup);
		
		
		$.each( eplAddedInspection, function( key, value ) {
			$('.epl-added-inspection').append('<span><span class="epl-inspection-text">'+value+'</span><span class="del-inspection-time">X</span></span>');
		});

		jQuery("form#post").validationEngine();
		
		
		jQuery('#epl-inspection-date').datetimepicker({'timepicker':false, 'format':'M-d-Y','closeOnDateSelect':true,'allowBlank':false});
		jQuery('#epl-inspection-start-hh').datetimepicker({'datepicker':false,'format':'h','hours12':true});
		jQuery('#epl-inspection-start-mm').datetimepicker({
			'datepicker':false,
			'format':'i',
			'allowTimes': ['00:05','00:10','00:15','00:20','00:25','00:30','00:35','00:40','00:45','00:50','00:55']
		});
		jQuery('#epl-inspection-end-hh').datetimepicker({'datepicker':false,'format':'h','hours12':true});
		jQuery('#epl-inspection-end-mm').datetimepicker({
			'datepicker':false,
			'format':'i',
			'allowTimes': ['00:05','00:10','00:15','00:20','00:25','00:30','00:35','00:40','00:45','00:50','00:55']
		});

		

		
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

		jQuery(this).addClass('epl-fields-menu-current');
		console.log("#tab-menu-"+fields_tab_id);
		jQuery("#"+fields_tab_id).addClass('epl-fields-field-current');
	});
});

function epl_generate_inspection_markup() {
	var tpl;
	var Year = new Date().getFullYear();
	tpl = '<div class="epl-added-inspection"></div><div class="elp-inspection-markup">';
	tpl +=	'<input type="text" style="width:6em;" autocomplete="off" id="epl-inspection-date" maxlength="2" size="2" placeholder="01"> ';
	tpl +=	' From : ';
	tpl +=	'<input class="validate[custom[onlyNumber]]" type="text" autocomplete="off" id="epl-inspection-start-hh" maxlength="2" size="2" placeholder="01">';
	tpl +=	' : <input type="text" autocomplete="off" id="epl-inspection-start-mm" maxlength="2" size="2" placeholder="01"> ';
	tpl +=	'<select id="epl-inspection-start-ampm" class="epl-inspection-ampm">';
	tpl +=		'<option value="am">Am</option>';
	tpl +=		'<option value="pm">Pm</option>';
	tpl +=	'</select>';
	tpl +=	' To : ';
	tpl +=	'<input class="validate[custom[onlyNumber]]" type="text" autocomplete="off" id="epl-inspection-end-hh" maxlength="2" size="2" placeholder="01">';
	tpl +=	' : <input class="validate[custom[onlyNumber]]" type="text" autocomplete="off" id="epl-inspection-end-mm" maxlength="2" size="2" placeholder="01"> ';

	tpl +=	'<select id="epl-inspection-end-ampm" class="epl-inspection-ampm">';
	tpl +=		'<option value="am">Am</option>';
	tpl +=		'<option value="pm">Pm</option>';
	tpl +=	'</select>';

	tpl += '<a id="epl-inspection-add" class="button">Add</a> </div>';
	return tpl;
}
