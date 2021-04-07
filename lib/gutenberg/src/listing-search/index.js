/**
 * EPL Block listing Search
 *
 * @since 3.5.0
 * @package EPL
 */

/**
 * EPL block functions
 */
 import {
	epl_get_setting,
	epl_get_post_edit_meta,
	epl_get_post_types,
	epl_get_search_attributes
} from '../gutenberg-blocks.js';

/**
 * Internal block libraries
 */
const { __, _x, sprintf } = wp.i18n;
const {
	registerBlockType,
} = wp.blocks;

const {
    InspectorControls,
} = wp.editor;

const {
	ServerSideRender,
	PanelBody,
	RangeControl,
	SelectControl,
	ToggleControl,
	TextControl
} = wp.components;

registerBlockType(
    'epl/listing-search',
    {
		title: 'Listing Search',
		description: 'Search EPL Listings',
		icon: 'search',
		category: 'epl-blocks',
		example: {
			attributes: {
				example_show: 1,
			},
		},
		supports: {
			customClassName: false,
		},
        attributes: epl_get_search_attributes(),
        edit: function( props ) {

			let fields = epl_get_search_attributes();
			const prop_atts = props.attributes;
			let main_section = '';
			
			main_section = (
				<PanelBody
					title={__('Listing Search', 'easy-property-listings')}
					initialOpen={true}
				>
				{Object.keys(fields).map( function(key, i ) {
					
					let currentValue = prop_atts[ key ];
					let currentKey = fields[key];
					let options = fields[key]['opts'];

					if( (options == null || options.length === 0) ) {
						options = [
							
							{
								label: __('Off', 'easy-property-listings'),
								value: 'off',
							},
							{
								label: __('On', 'easy-property-listings'),
								value: 'on',
							}
						];
					}

					if( 'text' == fields[key]['render_type']  ) {
						
						return (
							<TextControl
								label={fields[key]['label']}
								value={currentValue}
								onChange={ function(e) {
									var attrs = {};  	
									attrs[key] = e;  	
									props.setAttributes(attrs);
								}}
							/>
						);
					} else if( 'select' == fields[key]['render_type'] ) {
						return (
							<SelectControl
								key={fields[key]}
								label={fields[key]['label']}
								value={currentValue|| ''}
								options={options}
								onChange={ function(e) {
									var attrs = {};  	
									attrs[key] = e;  	
									props.setAttributes(attrs);
								}}
							/>
						);

					} else if( 'select_multiple' == fields[key]['render_type']  ) {
						return (
							<SelectControl
								multiple
								key={fields[key]}
								label={fields[key]['label']}
								value={currentValue}
								options={options}
								onChange={ function(e) {
									var attrs = {};  	
									attrs[key] = e;  	
									props.setAttributes(attrs);
								}}
							/>
						);
					} else {
						// undefined types
					}
				})}
				</PanelBody>
			);

			const inspectorControls = (
				<InspectorControls>
					{ main_section }
				</InspectorControls>
			);

			function do_serverside_render( attributes ) {
				return <ServerSideRender
					block="epl/listing-search"
					attributes={ attributes }
				/>
			}

			return [
				inspectorControls,
				do_serverside_render( props.attributes )
			];
        },

        save: props => {
		}
	},
);
