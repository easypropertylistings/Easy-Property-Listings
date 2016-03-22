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
		error 				: null,
		strip_html_special_chars	: null,
		string_to_name			: null,
	},
	
	hooks : null
};

(function($){

	/** init common functions for use **/
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
	epl.helpers.ajax_submit = function(el,action) {
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
	epl.helpers.get_unique_name = function(string,array) {
		var name = epl.helpers.string_to_name(string);
		name_orig = typeof array == 'undefined' ? name : array+'['+name+']';
		if (! $('[name="'+name_orig+'"]').length) {
			return name_orig;
		} else {
			var i = 1;
			while( $('[name="'+name_orig+'"]').length > 0 ) {
				name_orig = array == 'undefined' ? name+'_'+i : array+'['+name+'_'+i+']';
				i++;
			}
			return name_orig;
		}
	}
	
	
	/** a very light tabs functionality **/
	epl.ui.tabs = function() {
	
		var a 		= arguments;
		var l 		= a.length;
		var el 		= null;
		var opts		= {
			selector	: null,
			first		: 0,
			type		: 'horizontal',
		};
		var atts		= {};
		
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
				$(this).children('div').each(function(i) {
					if(i == opts.first) {
						$(this).addClass(epl.ui.prefix+'tab_content_current');
					}
				
					$(this).addClass(epl.ui.prefix+'tab_content');
				});
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
	
		var a 		= arguments;
		var l 		= a.length;
		var el 		= null;
		var opts	= {
			selector	: null,
			first		: 0,
			head		: 'h3'
		};
		var atts		= {};
		
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
	
	/** hooks api **/
	epl.hooks_api = function() {
		var slice = Array.prototype.slice;
		
		/**
		 * Maintain a reference to the object scope so our public methods never get confusing.
		 */
		var MethodsAvailable = {
			removeFilter : removeFilter,
			applyFilters : applyFilters,
			addFilter : addFilter,
			removeAction : removeAction,
			doAction : doAction,
			addAction : addAction
		};

		/**
		 * Contains the hooks that get registered with hooks_api.
		 */
		var STORAGE = {
			actions : {},
			filters : {}
		};

		/**
		 * Adds an action to the hooks_api
		 */
		function addAction( action, callback, priority, context ) {
			if( typeof action === 'string' && typeof callback === 'function' ) {
				priority = parseInt( ( priority || 10 ), 10 );
				_addHook( 'actions', action, callback, priority, context );
			}

			return MethodsAvailable;
		}

		/**
		 * Performs an action if it exists. You can pass as many arguments as you want to this function; the only rule is
		 * that the first argument must always be the action.
		 */
		function doAction( /* action, arg1, arg2, ... */ ) {
			var args = slice.call( arguments );
			var action = args.shift();

			if( typeof action === 'string' ) {
				_runHook( 'actions', action, args );
			}

			return MethodsAvailable;
		}

		/**
		 * Removes the specified action if it contains an action & exists.
		 */
		function removeAction( action, callback ) {
			if( typeof action === 'string' ) {
				_removeHook( 'actions', action, callback );
			}
			return MethodsAvailable;
		}

		/**
		 * Adds a filter to the hooks_api.
		 */
		function addFilter( filter, callback, priority, context ) {
			if( typeof filter === 'string' && typeof callback === 'function' ) {
				priority = parseInt( ( priority || 10 ), 10 );
				_addHook( 'filters', filter, callback, priority, context );
			}
			return MethodsAvailable;
		}

		/**
		 * Performs a filter if it exists. You should only ever pass 1 argument to be filtered. The only rule is that
		 * the first argument must always be the filter.
		 */
		function applyFilters( /* filter, filtered arg, arg2, ... */ ) {
			var args = slice.call( arguments );
			var filter = args.shift();

			if( typeof filter === 'string' ) {
				return _runHook( 'filters', filter, args );
			}
			return MethodsAvailable;
		}

		/**
		 * Removes the specified filter if it contains an filter & exists.
		 */
		function removeFilter( filter, callback ) {
			if( typeof filter === 'string') {
				_removeHook( 'filters', filter, callback );
			}
			return MethodsAvailable;
		}

		/**
		 * Removes the specified hook by resetting the value of it.
		 */
		function _removeHook( type, hook, callback, context ) {
			var handlers, handler, i;
			
			if ( !STORAGE[ type ][ hook ] ) {
				return;
			}
			if ( !callback ) {
				STORAGE[ type ][ hook ] = [];
			} else {
				handlers = STORAGE[ type ][ hook ];
				if ( !context ) {
					for ( i = handlers.length; i--; ) {
						if ( handlers[i].callback === callback ) {
							handlers.splice( i, 1 );
						}
					}
				}
				else {
					for ( i = handlers.length; i--; ) {
						handler = handlers[i];
						if ( handler.callback === callback && handler.context === context) {
							handlers.splice( i, 1 );
						}
					}
				}
			}
		}

		/**
		 * Adds the hook to the appropriate storage container
		 */
		function _addHook( type, hook, callback, priority, context ) {
			var hookObject = {
				callback : callback,
				priority : priority,
				context : context
			};

			var hooks = STORAGE[ type ][ hook ];
			if( hooks ) {
				hooks.push( hookObject );
				hooks = _hookInsertSort( hooks );
			}
			else {
				hooks = [ hookObject ];
			}
			STORAGE[ type ][ hook ] = hooks;
		}

		/**
		 * Use an insert sort for keeping our hooks organized based on priority.
		 */
		function _hookInsertSort( hooks ) {
			var tmpHook, j, prevHook;
			for( var i = 1, len = hooks.length; i < len; i++ ) {
				tmpHook = hooks[ i ];
				j = i;
				while( ( prevHook = hooks[ j - 1 ] ) &&  prevHook.priority > tmpHook.priority ) {
					hooks[ j ] = hooks[ j - 1 ];
					--j;
				}
				hooks[ j ] = tmpHook;
			}
			return hooks;
		}

		/**
		 * Runs the specified hook. If it is an action, the value is not modified but if it is a filter, it is.
		 */
		function _runHook( type, hook, args ) {
			var handlers = STORAGE[ type ][ hook ], i, len;
			
			if ( !handlers ) {
				return (type === 'filters') ? args[0] : false;
			}

			len = handlers.length;
			if ( type === 'filters' ) {
				for ( i = 0; i < len; i++ ) {
					args[ 0 ] = handlers[ i ].callback.apply( handlers[ i ].context, args );
				}
			} else {
				for ( i = 0; i < len; i++ ) {
					handlers[ i ].callback.apply( handlers[ i ].context, args );
				}
			}

			return ( type === 'filters' ) ? args[ 0 ] : true;
		}

		// return all of the publicly available methods
		return MethodsAvailable;

	};
	epl.hooks = new epl.hooks_api();
})(jQuery);

jQuery(document).on('ready',function($){
		epl.ui.init();
		
});
