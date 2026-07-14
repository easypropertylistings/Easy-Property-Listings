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
		[ 'card', __( 'EPL Agent Card', 'easy-property-listings' ), 'id' ],
		[ 'name', __( 'EPL Agent Name', 'easy-property-listings' ), 'admin-users' ],
		[ 'photo', __( 'EPL Agent Photo', 'easy-property-listings' ), 'format-image' ],
		[ 'position', __( 'EPL Agent Position', 'easy-property-listings' ), 'businessperson' ],
		[ 'bio', __( 'EPL Agent Biography', 'easy-property-listings' ), 'editor-paragraph' ],
		[ 'contact', __( 'EPL Agent Contact', 'easy-property-listings' ), 'phone' ],
		[ 'social', __( 'EPL Agent Social Links', 'easy-property-listings' ), 'share' ],
		[ 'listings', __( 'EPL Agent Listings', 'easy-property-listings' ), 'screenoptions' ]
	];

	function agentEdit( props ) {
		var attributes = props.attributes;
		var controls = [];

		if ( postTypeOptions.length > 1 ) {
			controls.push( el( SelectControl, {
				label: __( 'Editor preview listing type', 'easy-property-listings' ),
				value: attributes.previewPostType,
				options: postTypeOptions,
				onChange: function( value ) { props.setAttributes( { previewPostType: value } ); }
			} ) );
		}
		controls.push( el( SelectControl, {
			label: __( 'Listing agent', 'easy-property-listings' ),
			value: attributes.agentIndex,
			options: [ 1, 2, 3, 4 ].map( function( value ) { return { value: value, label: String( value ) }; } ),
			onChange: function( value ) { props.setAttributes( { agentIndex: Number( value ) } ); }
		} ) );

		if ( [ 'name', 'photo' ].indexOf( attributes.element ) !== -1 ) {
			controls.push( el( ToggleControl, {
				label: __( 'Link to agent profile', 'easy-property-listings' ),
				checked: attributes.link,
				onChange: function( value ) { props.setAttributes( { link: value } ); }
			} ) );
		}
		if ( 'photo' === attributes.element ) {
			controls.push( el( TextControl, {
				label: __( 'Image size', 'easy-property-listings' ),
				value: attributes.imageSize,
				onChange: function( value ) { props.setAttributes( { imageSize: value } ); }
			} ) );
		}
		if ( 'listings' === attributes.element ) {
			controls.push( el( SelectControl, {
				label: __( 'Listings layout', 'easy-property-listings' ),
				value: attributes.listingsMode,
				options: [
					{ value: '', label: __( 'Extension default', 'easy-property-listings' ) },
					{ value: 'list', label: __( 'List', 'easy-property-listings' ) },
					{ value: 'tabbed', label: __( 'Tabbed', 'easy-property-listings' ) }
				],
				onChange: function( value ) { props.setAttributes( { listingsMode: value } ); }
			} ) );
			if ( 'tabbed' === attributes.listingsMode ) {
				controls.push( el( SelectControl, {
					label: __( 'Tab orientation', 'easy-property-listings' ),
					value: attributes.tabStyle,
					options: [
						{ value: 'horizontal', label: __( 'Horizontal', 'easy-property-listings' ) },
						{ value: 'vertical', label: __( 'Vertical', 'easy-property-listings' ) }
					],
					onChange: function( value ) { props.setAttributes( { tabStyle: value } ); }
				} ) );
			}
		}

		return el( Fragment, {},
			el( InspectorControls, {}, el( PanelBody, { title: __( 'Agent settings', 'easy-property-listings' ) }, controls ) ),
			el( 'div', useBlockProps(), el( ServerSideRender, { block: 'epl/agent-element', attributes: attributes, httpMethod: 'POST' } ) )
		);
	}

	wp.blocks.registerBlockType( 'epl/agent-element', { edit: agentEdit, save: function() { return null; } } );
	elements.forEach( function( item, index ) {
		wp.blocks.registerBlockVariation( 'epl/agent-element', {
			name: item[0], title: item[1], icon: item[2], attributes: { element: item[0] },
			isActive: [ 'element' ], isDefault: 0 === index, scope: [ 'inserter', 'transform' ]
		} );
	} );
} )( window.wp, window.eplBlockData );
