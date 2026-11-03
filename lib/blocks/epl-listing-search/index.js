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
	var TextareaControl = wp.components.TextareaControl;
	var ToggleControl = wp.components.ToggleControl;
	var ServerSideRender = wp.serverSideRender;
	var fields = ( data && data.searchFields ) || [];

	function selectOptions( choices ) {
		return Object.keys( choices || {} ).map( function( value ) {
			return { value: value, label: choices[ value ] };
		} );
	}

	function edit( props ) {
		var settings = props.attributes.settings || {};
		function value( field ) {
			return Object.prototype.hasOwnProperty.call( settings, field.key ) ? settings[ field.key ] : field.default;
		}
		function update( key, next ) {
			var changed = Object.assign( {}, settings );
			changed[ key ] = next;
			props.setAttributes( { settings: changed } );
		}

		var controls = fields.filter( function( field ) { return 'hidden' !== field.type; } ).map( function( field ) {
			var common = { key: field.key, label: field.label || field.key, help: field.help || '' };
			if ( 'checkbox' === field.type ) {
				return el( ToggleControl, Object.assign( common, {
					checked: 'on' === value( field ),
					onChange: function( checked ) { update( field.key, checked ? 'on' : 'off' ); }
				} ) );
			}
			if ( 'select' === field.type ) {
				return el( SelectControl, Object.assign( common, {
					value: value( field ),
					multiple: !! field.multiple,
					options: selectOptions( field.options ),
					onChange: function( next ) { update( field.key, next ); }
				} ) );
			}
			if ( 'textarea' === field.type ) {
				return el( TextareaControl, Object.assign( common, {
					value: value( field ),
					onChange: function( next ) { update( field.key, next ); }
				} ) );
			}
			return el( TextControl, Object.assign( common, {
				type: 'number' === field.type ? 'number' : 'text',
				value: value( field ),
				onChange: function( next ) { update( field.key, next ); }
			} ) );
		} );

		return el( Fragment, {},
			el( InspectorControls, {}, el( PanelBody, { title: __( 'Search form options', 'easy-property-listings' ) }, controls ) ),
			el( 'div', useBlockProps(), el( ServerSideRender, { block: 'epl/listing-search', attributes: props.attributes, httpMethod: 'POST' } ) )
		);
	}

	wp.blocks.registerBlockType( 'epl/listing-search', { edit: edit, save: function() { return null; } } );
} )( window.wp, window.eplBlockData );
