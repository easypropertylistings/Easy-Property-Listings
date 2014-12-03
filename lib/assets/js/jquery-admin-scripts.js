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
	if( $('form#post').length ) {
		jQuery("form#post").validationEngine();
	}
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
