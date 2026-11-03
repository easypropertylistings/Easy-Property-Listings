( function( wp, data ) {
	'use strict';

	var el = wp.element.createElement;
	var Fragment = wp.element.Fragment;
	var __ = wp.i18n.__;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var useBlockProps = wp.blockEditor.useBlockProps;
	var PanelBody = wp.components.PanelBody;
	var SelectControl = wp.components.SelectControl;
	var TextControl = wp.components.TextControl;
	var ToggleControl = wp.components.ToggleControl;
	var ServerSideRender = wp.serverSideRender;
	var config = data || { postTypes: {} };
	var postTypeOptions = Object.keys( config.postTypes || {} ).map( function( value ) {
		return { value: value, label: config.postTypes[ value ] };
	} );
	var elements = [
		[ 'archive-title', __( 'EPL Archive Title', 'easy-property-listings' ), 'heading' ],
		[ 'loop-start', __( 'EPL Property Loop Start', 'easy-property-listings' ), 'controls-play' ],
		[ 'loop-end', __( 'EPL Property Loop End', 'easy-property-listings' ), 'controls-skipforward' ],
		[ 'view-switch', __( 'EPL View Switch', 'easy-property-listings' ), 'screenoptions' ],
		[ 'sorting-tool', __( 'EPL Sorting Tool', 'easy-property-listings' ), 'sort' ],
		[ 'listing', __( 'EPL Default Listing', 'easy-property-listings' ), 'admin-home' ],
		[ 'pagination', __( 'EPL Pagination', 'easy-property-listings' ), 'editor-ol' ],
		[ 'not-found', __( 'EPL Listings Not Found', 'easy-property-listings' ), 'warning' ]
	];

	function archivePreview( attributes ) {
		var element = attributes.element;
		if ( 'archive-title' === element ) {
			return el( 'strong', {}, config.postTypes[ attributes.previewPostType ] || __( 'Listings', 'easy-property-listings' ) );
		}
		if ( 'loop-start' === element ) {
			return el( 'div', { className: 'epl-block-toolbar-preview' },
				attributes.showViewSwitch ? el( 'span', { 'aria-hidden': true }, '▤  ▦' ) : null,
				attributes.showSorting ? el( 'select', { disabled: true, 'aria-label': __( 'Sorting preview', 'easy-property-listings' ) }, el( 'option', {}, __( 'Sort', 'easy-property-listings' ) ) ) : null
			);
		}
		if ( 'view-switch' === element ) {
			return el( 'span', { 'aria-hidden': true }, '▤  ▦' );
		}
		if ( 'sorting-tool' === element ) {
			return el( 'select', { disabled: true, 'aria-label': __( 'Sorting preview', 'easy-property-listings' ) }, el( 'option', {}, __( 'Sort', 'easy-property-listings' ) ) );
		}
		if ( 'pagination' === element ) {
			return el( 'span', {}, __( 'Previous', 'easy-property-listings' ), '  1  2  3  ', __( 'Next', 'easy-property-listings' ) );
		}
		if ( 'not-found' === element ) {
			return el( 'span', {}, __( 'No listings found.', 'easy-property-listings' ) );
		}
		return el( ServerSideRender, { block: 'epl/archive-element', attributes: attributes, httpMethod: 'POST' } );
	}

	function archiveEdit( props ) {
		var controls = [];
		var blockProps = useBlockProps( {
			className: 'epl-archive-element-editor epl-archive-element-editor-' + props.attributes.element
		} );
		if ( [ 'listing', 'archive-title' ].indexOf( props.attributes.element ) !== -1 && postTypeOptions.length > 1 ) {
			controls.push( el( SelectControl, {
				label: __( 'Editor preview listing type', 'easy-property-listings' ),
				value: props.attributes.previewPostType,
				options: postTypeOptions,
				onChange: function( value ) { props.setAttributes( { previewPostType: value } ); }
			} ) );
		}
		if ( 'loop-start' === props.attributes.element ) {
			controls.push( el( ToggleControl, {
				label: __( 'Show list/grid view switch', 'easy-property-listings' ),
				checked: props.attributes.showViewSwitch,
				onChange: function( value ) { props.setAttributes( { showViewSwitch: value } ); }
			} ) );
			controls.push( el( ToggleControl, {
				label: __( 'Show sorting dropdown', 'easy-property-listings' ),
				checked: props.attributes.showSorting,
				onChange: function( value ) { props.setAttributes( { showSorting: value } ); }
			} ) );
			controls.push( el( ToggleControl, {
				label: __( 'Show extension-added toolbar items', 'easy-property-listings' ),
				checked: props.attributes.showAdditionalTools,
				onChange: function( value ) { props.setAttributes( { showAdditionalTools: value } ); }
			} ) );
		}
		if ( [ 'loop-start', 'sorting-tool' ].indexOf( props.attributes.element ) !== -1 ) {
			controls.push( el( TextControl, {
				label: __( 'Sorting instance ID', 'easy-property-listings' ),
				value: props.attributes.instanceId,
				onChange: function( value ) { props.setAttributes( { instanceId: value } ); }
			} ) );
		}
		if ( 'pagination' === props.attributes.element ) {
			controls.push( el( 'p', {}, __( 'Pagination is shown on the frontend only when the containing query has more than one page of listings.', 'easy-property-listings' ) ) );
		}

		return el( Fragment, {},
			controls.length ? el( InspectorControls, {}, el( PanelBody, { title: __( 'EPL archive settings', 'easy-property-listings' ) }, controls ) ) : null,
			'loop-end' === props.attributes.element
				? el( 'div', Object.assign( {}, blockProps, { title: __( 'EPL Property Loop End', 'easy-property-listings' ) } ) )
				: el( 'div', blockProps, archivePreview( props.attributes ) )
		);
	}

	wp.blocks.registerBlockType( 'epl/archive-element', { edit: archiveEdit, save: function() { return null; } } );
	elements.forEach( function( item, index ) {
		wp.blocks.registerBlockVariation( 'epl/archive-element', {
			name: item[0],
			title: item[1],
			icon: item[2],
			attributes: { element: item[0] },
			isActive: [ 'element' ],
			isDefault: 1 === index,
			scope: [ 'inserter', 'transform' ]
		} );
	} );
} )( window.wp, window.eplBlockData );
