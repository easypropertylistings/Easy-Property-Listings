// global namespace
var epl = {
	
	/** ui **/
	ui : {

		init			: null,
		prefix 			: 'epl_ui_',
		tabs 			: null,
		accordion 		: null
	},

	/** helpers **/
	helpers : {
	
		error 							: null,
		strip_html_special_chars		: null,
		string_to_name					: null,
	}

};



(function($){

	/** init commoan functions for use **/
	epl.ui.init = function() {
		epl.ui.tabs('.epl-tabs, .epl-tabs-horizontal');
		epl.ui.tabs({
			selector :'.epl-tabs-vertical',
			type	 : 'vertical'
		});
		epl.ui.accordion('.epl-accordion');
	}

	/** thorows custom errors **/
	epl.helpers.error = function(msg) {
		throw  msg;
	}

	/** ajax form submit wrapper **/
	epl.helpers.ajax_submit = function(el,action='') {
		$(document).on('submit',$(el), function(e) {
			e.preventDefault();
			$.ajax({
				method: "POST",
				url: epl_frontend_vars.ajaxurl,
				data: { action: action, data: $(el).serialize() }
			})
			.done(function( msg ) {
				
			});
		});
	}
	
	/** remove html tags & special chars from string **/
	
	epl.helpers.strip_html_special_chars = function(dirtyString) {
		var container = document.createElement('div');
		container.innerHTML = dirtyString;
		dirtyString = container.textContent || container.innerText;
		return dirtyString.replace(/\W+/g, " ");
	}
	
	/** get an input string and returns a field name **/
	epl.helpers.string_to_name = function(string) {
		string = epl.helpers.strip_html_special_chars(string);
		string = string.toLowerCase(string);
		return $.trim(string.replace(/ /g,"_"));
	}
	
	/** takes a string and returns a unique name for input field **/
	epl.helpers.get_unique_name = function(string,array=null) {
		var name = epl.helpers.string_to_name(string);
		name_orig = array == null ? name : array+'['+name+']';
		if (! $('[name="'+name_orig+'"]').length) {
			return name_orig;
		} else {
			var i = 1;
			while( $('[name="'+name_orig+'"]').length > 0 ) {
				name_orig = array == null ? name+'_'+i : array+'['+name+'_'+i+']';
				i++;
			}
			return name_orig;
		}
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
			
			$(el).each(function(){
				$(this).addClass(epl.ui.prefix+'tab_wrapper '+epl.ui.prefix+'tab_wrapper_'+opts.type+' epl-clearfix');
			});
			
			$(el).each(function(){
				$(this).children('div').each(function(i) {
					if(i == opts.first) {
						$(this).addClass(epl.ui.prefix+'tab_content_current');
					}
				
					$(this).addClass(epl.ui.prefix+'tab_content');
				});
			});
			
			$(el).each(function(){
				$(this).find('ul:first li').each(function(i) {
					if(i == opts.first) {
						$(this).addClass(epl.ui.prefix+'tab_menu_current');
						$(this).closest('ul').addClass(epl.ui.prefix+'tab_menu_wrapper '+epl.ui.prefix+'tab_menu_wrapper_'+opts.type);
					}
				
					$(this).addClass(epl.ui.prefix+'tab_menu');
					$(this).on('click',function(e) {
						var ref = $(this).children('a:first').attr('href');
						e.preventDefault();
						$(this).trigger('tabchange',$(this).index());
						$(this).siblings().removeClass(epl.ui.prefix+'tab_menu_current');
						$(this).addClass(epl.ui.prefix+'tab_menu_current');
						$(this).closest(el).find(ref).siblings().removeClass(epl.ui.prefix+'tab_content_current');
						$(this).closest(el).find(ref).addClass(epl.ui.prefix+'tab_content_current');
					});
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
			$(el).each(function() {
				$(this).addClass(epl.ui.prefix+'accordion_wrapper');
			});
			
			$(el).each(function() {
				$(this).children(opts.head).each(function(i) {
					if(i == opts.first) {
						$(this).addClass(epl.ui.prefix+'accordion_menu_current');
						$(this).next().addClass(epl.ui.prefix+'accordion_content_current');
					}
				
					$(this).addClass(epl.ui.prefix+'accordion_menu');
					$(this).next().addClass(epl.ui.prefix+'accordion_content');
				
					$(this).on('click',function(e) {
						$(this).toggleClass(epl.ui.prefix+'accordion_menu_current');
						$(this).siblings(opts.head).removeClass(epl.ui.prefix+'accordion_menu_current');
						$(this).next().slideToggle('fast');
						$(this).siblings('div').not($(this).next()).slideUp('fast');
					});
				});
			});
			
		} else {
			epl.helpers.error('no arguments passed to tabs');
		}
	
	}
	
})(jQuery);

jQuery(document).on('ready',function($){
		epl.ui.init();
});
