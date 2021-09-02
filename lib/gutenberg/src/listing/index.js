/**
 * EPL Block listing
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
	epl_get_post_types
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
    'epl/listing-display',
    {
		title: 'Listing',
		description: 'Display EPL Listings',
		icon: 'admin-home',
		category: 'epl-blocks',
		example: {
			attributes: {
				example_show: 1,
			},
		},
		supports: {
			customClassName: false,
		},
        attributes: {
			post_type: {
				type: 'array',
				default: epl_get_setting('activate_post_types'),
			},
			status: {
				type: 'array',
				default: ['current', 'sold', 'leased'],
			},
			limit: {
				type: 'string',
				default: '10'
			},
			offset: {
				type: 'string',
				default: ''
			},
			author: {
				type: 'string',
				default: '',
			},
			agent: {
				type: 'string',
				default: '',
			},
			featured: {
				type: 'boolean',
				default: false,
			},
			template: {
				type: 'string',
				default: ''
			},
			location: {
				type: 'string',
				default: '0',
			},
			tools_top: {
				type: 'string',
				default: 'off',
			},
			tools_bottom: {
				type: 'string',
				default: 'off',
			},
			sortby: {
				type: 'string',
				default: ''
			},
			sort_order: {
				type: 'string',
				default: 'DESC'
			},
			pagination: {
				type: 'string',
				default: 'on'
			},
			instance_id: {
				type: 'string',
				default: '1'
			},
			wrapper_class: {
				type: 'string',
				default: ''
			}
		},
        edit: function( props ) {

			const { attributes: { post_type,status, limit, offset, author, agent, featured, template, location, tools_top, tools_bottom, sortby, sort_order, pagination, instance_id, wrapper_class   },
            	setAttributes } = props;

			let main_section = '';
			main_section = (
				<PanelBody
					title={__('Listing Settings', 'easy-property-listings')}
					initialOpen={true}
				>
					<SelectControl
						multiple
						key="post_type"
						label={__('Post Type', 'easy-property-listings')}
						value={post_type}
						options={epl_get_post_types()}
						onChange={ post_type => setAttributes({ post_type })}
					/>
					<SelectControl
						multiple
						key="status"
						label={__('Status', 'easy-property-listings')}
						value={status}
						options={[
							{
								label: __('Current', 'easy-property-listings'),
								value: 'current',
							},
							{
								label: __('Sold', 'easy-property-listings'),
								value: 'sold',
							},
							{
								label: __('Leased', 'easy-property-listings'),
								value: 'leased',
							},
						]}
						onChange={ status => setAttributes({ status })}
					/>
					<TextControl
						label={__('Limit', 'easy-property-listings')}
						help={__('max number of listings per page', 'easy-property-listings')}
						value={limit || ''}
						type={'number'}
						onChange={limit => setAttributes({ limit })}
					/>
					<TextControl
						label={__('Offset', 'easy-property-listings')}
						help={__('Offset posts. When used, pagination is disabled', 'easy-property-listings')}
						value={offset || ''}
						type={'number'}
						onChange={offset => setAttributes({ offset })}
					/>
					<TextControl
						label={__('Author', 'easy-property-listings')}
						help={__('Show listings of only specified author', 'easy-property-listings')}
						value={author || ''}
						type={'number'}
						onChange={author => setAttributes({ author })}
					/>
					<TextControl
						label={__('Agent', 'easy-property-listings')}
						help={__('Show listings of only specified agent', 'easy-property-listings')}
						value={agent || ''}
						onChange={agent => setAttributes({ agent })}
					/>
					<ToggleControl
						label={__('Featured', 'easy-property-listings')}
						checked={!!featured}
						onChange={featured => setAttributes({ featured })}
					/>
					<TextControl
						label={__('Template', 'easy-property-listings')}
						help={__('Template can be set to "slim" for home open style template', 'easy-property-listings')}
						value={template || ''}
						onChange={template => setAttributes({ template })}
					/>
					<TextControl
						label={__('Location', 'easy-property-listings')}
						help={__('Location slug. Should be a name like sorrento', 'easy-property-listings')}
						value={location || ''}
						onChange={location => setAttributes({ location })}
					/>
					<SelectControl
						key="tools_top"
						label={__('Tools Top', 'easy-property-listings')}
						value={tools_top}
						options={[
							{
								label: __('On', 'easy-property-listings'),
								value: 'on',
							},
							{
								label: __('Off', 'easy-property-listings'),
								value: 'off',
							}
						]}
						onChange={ tools_top => setAttributes({ tools_top })}
						help={__('Tools before the loop like pagination on or off', 'easy-property-listings')}
					/>
					<SelectControl
						key="tools_bottom"
						label={__('Tools Bottom', 'easy-property-listings')}
						value={tools_bottom}
						options={[
							{
								label: __('On', 'easy-property-listings'),
								value: 'on',
							},
							{
								label: __('Off', 'easy-property-listings'),
								value: 'off',
							}
						]}
						onChange={ tools_bottom => setAttributes({ tools_bottom })}
						help={__('Tools before the loop like pagination on or off', 'easy-property-listings')}
					/>
					<TextControl
						label={__('Sort By', 'easy-property-listings')}
						help={__('Options: price, date, status, rand (for random) : Default date', 'easy-property-listings')}
						value={sortby || ''}
						onChange={sortby => setAttributes({ sortby })}
					/>
					<TextControl
						label={__('Sort Order', 'easy-property-listings')}
						help={__('ASC or desc', 'easy-property-listings')}
						value={sort_order || ''}
						onChange={sort_order => setAttributes({ sort_order })}
					/>
					<SelectControl
						key="pagination"
						label={__('Pagination', 'easy-property-listings')}
						value={pagination} 
						options={[
							{
								label: __('On', 'easy-property-listings'),
								value: 'on',
							},
							{
								label: __('Off', 'easy-property-listings'),
								value: 'off',
							}
						]}
						onChange={ pagination => setAttributes({ pagination })}
					/>
					<TextControl
						label={__('Instance ID', 'easy-property-listings')}
						help={__('set instance ID when using multiple shortcodes on the same page', 'easy-property-listings')}
						value={instance_id || ''}
						onChange={instance_id => setAttributes({ instance_id })}
					/>
					<TextControl
						label={__('Class', 'easy-property-listings')}
						help={__('Wrapper Class', 'easy-property-listings')}
						value={wrapper_class || ''}
						onChange={wrapper_class => setAttributes({ wrapper_class })}
					/>
				</PanelBody>
			);

			const inspectorControls = (
				<InspectorControls>
					{ main_section }
				</InspectorControls>
			);

			function do_serverside_render( attributes ) {
				return <ServerSideRender
					block="epl/listing-display"
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
