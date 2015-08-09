// global namespace
var epl = {
	
	/** ui **/
	ui : {

		prefix 			: 'epl_ui_',
		tabs 			: null,
		accordion 		: null
	},

	/** helpers **/
	helpers : {
	
		error 		: null
	}

};



(function($){

	/** thorows custom errors **/
	epl.helpers.error = function(msg) {
		throw  msg;
	}


	/** a very light tabs functionality **/
	epl.ui.tabs = function() {
	
		var a 				= arguments;
		var l 				= a.length;
		var el 				= null;
		var opts		= {
			selector	: null,
			first		: 0,
			type		: 'horizontal',
		};
		var atts			= {};
		
		if(l > 0) {
			if( typeof a[0] === 'string') {
				el = a[0];
			} else if(typeof a[0] === 'object') {
				atts = a[0];
				el = atts.selector;
				
			}
			
			$.extend( true, opts, atts );
			$(el).addClass(epl.ui.prefix+'tab_wrapper '+epl.ui.prefix+'tab_wrapper_'+opts.type);
			$(el).children('div').each(function(i) {
				if(i == opts.first) {
					$(this).addClass(epl.ui.prefix+'tab_content_current');
				}
				
				$(this).addClass(epl.ui.prefix+'tab_content');
			});
			
			$(el).find('ul:first li').each(function(i) {
				if(i == opts.first) {
					$(this).addClass(epl.ui.prefix+'tab_menu_current');
					$(this).closest('ul').addClass(epl.ui.prefix+'tab_menu_wrapper '+epl.ui.prefix+'tab_menu_wrapper_'+opts.type);
				}
				
				$(this).addClass(epl.ui.prefix+'tab_menu');
				$(this).on('click',function(e) {
					var ref = $(this).children('a:first').attr('href');
					e.preventDefault();
					$(el).trigger('tabchange',$(this).index());
					$(this).siblings().removeClass(epl.ui.prefix+'tab_menu_current');
					$(this).addClass(epl.ui.prefix+'tab_menu_current');
					$(ref).siblings().removeClass(epl.ui.prefix+'tab_content_current');
					$(ref).addClass(epl.ui.prefix+'tab_content_current');
				});
			});
			
			
			
		} else {
			epl.helpers.error('no arguments passed to tabs');
		}
	
	}
	

	/** accordion functionality **/
	epl.ui.accordion = function() {
	
		var a 				= arguments;
		var l 				= a.length;
		var el 				= null;
		var opts		= {
			selector	: null,
			first		: 0,
			head		: 'h3'
		};
		var atts			= {};
		
		if(l > 0) {
			if( typeof a[0] === 'string') {
				el = a[0];
			} else if(typeof a[0] === 'object') {
				atts = a[0];
				el = atts.selector;
				
			}
			
			$.extend( true, opts, atts );
			$(el).addClass(epl.ui.prefix+'accordion_wrapper');
			$(el).children(opts.head).each(function(i) {
				if(i == opts.first) {
					$(this).addClass(epl.ui.prefix+'accordion_menu_current');
					$(this).next().addClass(epl.ui.prefix+'accordion_content_current');
				}
				
				$(this).addClass(epl.ui.prefix+'accordion_menu');
				$(this).next().addClass(epl.ui.prefix+'accordion_content');
				
				$(this).on('click',function(e) {
					$(this).addClass(epl.ui.prefix+'accordion_menu_current');
					$(this).siblings(opts.head).removeClass(epl.ui.prefix+'accordion_menu_current');
					$(this).next().slideToggle('fast');
					$(this).siblings('div').not($(this).next()).slideUp('fast');
				});
			});
			
		} else {
			epl.helpers.error('no arguments passed to tabs');
		}
	
	}
	
})(jQuery);

