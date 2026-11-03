( function( wp, data ) {
	'use strict';

	var el = wp.element.createElement;
	var Fragment = wp.element.Fragment;
	var __ = wp.i18n.__;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var useBlockProps = wp.blockEditor.useBlockProps;
	var Button = wp.components.Button;
	var PanelBody = wp.components.PanelBody;
	var SelectControl = wp.components.SelectControl;
	var TextControl = wp.components.TextControl;
	var ToggleControl = wp.components.ToggleControl;
	var ServerSideRender = wp.serverSideRender;
	var config = data || {};
	var defaults = {
		post_type: Object.keys( config.postTypes || {} ), status: [ 'current', 'sold', 'leased' ],
		commercial_listing_type: [], limit: 10, offset: 0, post__in: '', post__not_in: '',
		location_id: [], feature_id: [], location: '', feature: '', author: [], agent: '',
		featured: '', open_house: '', auction: '', sortby: 'date', sort_order: 'DESC', orderby_clause: '',
		design_source: 'pattern', pattern: 'registered:epl/listing-card-grid', columns: 'pattern', template: 'default',
		tools_top: 'off', tools_bottom: 'off', pagination: 'on'
	};
	var comparisons = [
		[ 'equal', __( 'Equal', 'easy-property-listings' ) ], [ 'min', __( 'Minimum (>=)', 'easy-property-listings' ) ],
		[ 'max', __( 'Maximum (<=)', 'easy-property-listings' ) ], [ 'not_equal', __( 'Not Equal', 'easy-property-listings' ) ],
		[ 'like', __( 'Like', 'easy-property-listings' ) ], [ 'not_like', __( 'Not Like', 'easy-property-listings' ) ],
		[ 'exists', __( 'Exists', 'easy-property-listings' ) ], [ 'not_exists', __( 'Not Exists', 'easy-property-listings' ) ],
		[ 'between', __( 'Between', 'easy-property-listings' ) ], [ 'not_between', __( 'Not Between', 'easy-property-listings' ) ],
		[ 'in', __( 'In', 'easy-property-listings' ) ], [ 'not_in', __( 'Not In', 'easy-property-listings' ) ]
	];

	function options( object ) {
		return Object.keys( object || {} ).map( function( value ) { return { value: value, label: object[ value ] }; } );
	}
	function pairOptions( pairs ) {
		return pairs.map( function( pair ) { return { value: pair[0], label: pair[1] }; } );
	}

	function edit( props ) {
		var settings = Object.assign( {}, defaults, props.attributes.settings || {} );
		var filters = props.attributes.dynamicFilters || [];
		function update( key, value ) {
			var changed = Object.assign( {}, settings ); changed[ key ] = value;
			props.setAttributes( { settings: changed } );
		}
		function select( key, label, choices, multiple ) {
			return el( SelectControl, { label: label, value: settings[ key ], options: options( choices ), multiple: !! multiple, onChange: function( value ) { update( key, value ); } } );
		}
		function text( key, label, type, help ) {
			return el( TextControl, { label: label, type: type || 'text', help: help || '', value: settings[ key ], onChange: function( value ) { update( key, 'number' === type ? Number( value ) : value ); } } );
		}
		function toggle( key, label, enabled, disabled ) {
			return el( ToggleControl, { label: label, checked: enabled === settings[ key ], onChange: function( checked ) { update( key, checked ? enabled : disabled ); } } );
		}
		function updateFilter( index, key, value ) {
			var changed = filters.map( function( filter ) { return Object.assign( {}, filter ); } );
			changed[ index ][ key ] = value; props.setAttributes( { dynamicFilters: changed } );
		}

		var queryControls = [
			select( 'post_type', __( 'Post types', 'easy-property-listings' ), config.postTypes, true ),
			select( 'status', __( 'Statuses', 'easy-property-listings' ), config.listingStatuses, true ),
			select( 'commercial_listing_type', __( 'Commercial listing type', 'easy-property-listings' ), config.commercialTypes, true ),
			text( 'limit', __( 'Listings per page', 'easy-property-listings' ), 'number' ),
			text( 'offset', __( 'Offset', 'easy-property-listings' ), 'number', __( 'Using an offset disables pagination.', 'easy-property-listings' ) ),
			text( 'post__in', __( 'Include listing IDs', 'easy-property-listings' ), 'text', __( 'Comma-separated post IDs.', 'easy-property-listings' ) ),
			text( 'post__not_in', __( 'Exclude listing IDs', 'easy-property-listings' ), 'text', __( 'Comma-separated post IDs.', 'easy-property-listings' ) )
		];
		var filterControls = [
			select( 'location_id', config.locationLabel || __( 'Locations', 'easy-property-listings' ), config.locationTerms, true ),
			select( 'feature_id', __( 'Features', 'easy-property-listings' ), config.featureTerms, true ),
			text( 'location', __( 'Location slugs', 'easy-property-listings' ) ),
			text( 'feature', __( 'Feature slugs', 'easy-property-listings' ) ),
			select( 'author', __( 'WordPress authors', 'easy-property-listings' ), config.authors, true ),
			text( 'agent', __( 'Listing agent usernames', 'easy-property-listings' ) ),
			toggle( 'featured', __( 'Featured only', 'easy-property-listings' ), 'yes', '' ),
			toggle( 'open_house', __( 'Open for inspection only', 'easy-property-listings' ), 'yes', '' ),
			toggle( 'auction', __( 'Auction only', 'easy-property-listings' ), 'yes', '' )
		];
		var orderControls = [
			select( 'sortby', __( 'Order by', 'easy-property-listings' ), { date: __( 'Date', 'easy-property-listings' ), price: __( 'Price', 'easy-property-listings' ), rand: __( 'Random', 'easy-property-listings' ), status: __( 'Status', 'easy-property-listings' ) } ),
			select( 'sort_order', __( 'Direction', 'easy-property-listings' ), { DESC: __( 'Descending', 'easy-property-listings' ), ASC: __( 'Ascending', 'easy-property-listings' ) } ),
			text( 'orderby_clause', __( 'Advanced orderby clauses', 'easy-property-listings' ), 'text', __( 'Comma-separated clause|direction pairs.', 'easy-property-listings' ) )
		];
		var outputControls = [
			select( 'design_source', __( 'Design source', 'easy-property-listings' ), { pattern: __( 'Gutenberg pattern', 'easy-property-listings' ), epl: __( 'Legacy EPL template', 'easy-property-listings' ) } ),
			'pattern' === settings.design_source
				? select( 'pattern', __( 'Listing pattern', 'easy-property-listings' ), config.listingPatterns || {} )
				: select( 'template', __( 'EPL template', 'easy-property-listings' ), { 'default': __( 'Default', 'easy-property-listings' ), card: __( 'Card', 'easy-property-listings' ), slim: __( 'Slim', 'easy-property-listings' ), table: __( 'Table', 'easy-property-listings' ), 'table-open': __( 'Table with inspection', 'easy-property-listings' ) } ),
			'pattern' === settings.design_source
				? select( 'columns', __( 'Columns', 'easy-property-listings' ), { pattern: __( 'Use pattern setting', 'easy-property-listings' ), 1: __( '1 column', 'easy-property-listings' ), 2: __( '2 columns', 'easy-property-listings' ), 3: __( '3 columns', 'easy-property-listings' ), 4: __( '4 columns', 'easy-property-listings' ), 5: __( '5 columns', 'easy-property-listings' ), 6: __( '6 columns', 'easy-property-listings' ) } )
				: null,
			toggle( 'tools_top', __( 'Archive tools above', 'easy-property-listings' ), 'on', 'off' ),
			toggle( 'tools_bottom', __( 'Archive tools below', 'easy-property-listings' ), 'on', 'off' ),
			toggle( 'pagination', __( 'Pagination', 'easy-property-listings' ), 'on', 'off' )
		];
		var dynamicControls = filters.map( function( filter, index ) {
			var compare = filter.compare || 'equal';
			return el( 'div', { key: index, className: 'epl-block-filter-control' },
				el( TextControl, { label: __( 'Meta key', 'easy-property-listings' ), value: filter.metaKey || '', onChange: function( value ) { updateFilter( index, 'metaKey', value ); } } ),
				el( SelectControl, { label: __( 'Compare', 'easy-property-listings' ), value: compare, options: pairOptions( comparisons ), onChange: function( value ) { updateFilter( index, 'compare', value ); } } ),
				[ 'exists', 'not_exists' ].indexOf( compare ) === -1 ? el( TextControl, { label: __( 'Value', 'easy-property-listings' ), help: __( 'Use commas for Between and In comparisons.', 'easy-property-listings' ), value: filter.value || '', onChange: function( value ) { updateFilter( index, 'value', value ); } } ) : null,
				el( Button, { isDestructive: true, variant: 'link', onClick: function() { props.setAttributes( { dynamicFilters: filters.filter( function( item, itemIndex ) { return itemIndex !== index; } ) } ); } }, __( 'Remove filter', 'easy-property-listings' ) )
			);
		} );
		dynamicControls.push( el( Button, { key: 'add', variant: 'secondary', onClick: function() { props.setAttributes( { dynamicFilters: filters.concat( [ { metaKey: '', compare: 'equal', value: '' } ] ) } ); } }, __( 'Add meta filter', 'easy-property-listings' ) ) );

		return el( Fragment, {},
			el( InspectorControls, {},
				el( PanelBody, { title: __( 'Query', 'easy-property-listings' ) }, queryControls ),
				el( PanelBody, { title: __( 'Filters', 'easy-property-listings' ), initialOpen: false }, filterControls ),
				el( PanelBody, { title: __( 'Dynamic meta filters', 'easy-property-listings' ), initialOpen: false }, dynamicControls ),
				el( PanelBody, { title: __( 'Ordering', 'easy-property-listings' ), initialOpen: false }, orderControls ),
				el( PanelBody, { title: __( 'Output', 'easy-property-listings' ), initialOpen: false }, outputControls )
			),
			el( 'div', useBlockProps(), el( ServerSideRender, { block: 'epl/listing-advanced', attributes: props.attributes, httpMethod: 'POST' } ) )
		);
	}

	wp.blocks.registerBlockType( 'epl/listing-advanced', { edit: edit, save: function() { return null; } } );
} )( window.wp, window.eplBlockData );
