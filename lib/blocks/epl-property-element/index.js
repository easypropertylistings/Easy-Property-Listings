( function( wp, data ) {
	'use strict';

	var el = wp.element.createElement;
	var Fragment = wp.element.Fragment;
	var __ = wp.i18n.__;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var useBlockProps = wp.blockEditor.useBlockProps;
	var PanelBody = wp.components.PanelBody;
	var BaseControl = wp.components.BaseControl;
	var Button = wp.components.Button;
	var ColorIndicator = wp.components.ColorIndicator;
	var ColorPicker = wp.components.ColorPicker;
	var Dropdown = wp.components.Dropdown;
	var Flex = wp.components.Flex;
	var FlexItem = wp.components.FlexItem;
	var RangeControl = wp.components.RangeControl;
	var SelectControl = wp.components.SelectControl;
	var TextControl = wp.components.TextControl;
	var ToggleControl = wp.components.ToggleControl;
	var CheckboxControl = wp.components.CheckboxControl;
	var config = data || { postTypes: {}, taxonomies: {}, imageSizes: {} };
	var eplQueryDesignStates = {};
	var previewCache = {};
	var previewQueue = [];
	var activePreviewRequests = 0;
	var maxPreviewRequests = 2;

	function runPreviewQueue() {
		while ( activePreviewRequests < maxPreviewRequests && previewQueue.length ) {
			activePreviewRequests++;
			previewQueue.shift()().finally( function() {
				activePreviewRequests--;
				runPreviewQueue();
			} );
		}
	}

	function queuePreviewRequest( request ) {
		return new Promise( function( resolve, reject ) {
			previewQueue.push( function() { return request().then( resolve, reject ); } );
			runPreviewQueue();
		} );
	}

	/**
	 * ServerSideRender sends one request for every repeated Post Template block.
	 * Pattern previews can contain dozens of identical EPL elements, which can
	 * exhaust a local PHP-FPM pool. Share identical responses and cap concurrent
	 * requests while retaining an accurate server-rendered editor preview.
	 */
	function CachedPropertyPreview( props ) {
		var postId = wp.data.useSelect( function( select ) {
			var editor = select( 'core/editor' );
			return editor && editor.getCurrentPostId ? editor.getCurrentPostId() : null;
		}, [] );
		// The Site Editor returns a template identifier such as
		// "twentytwentyfive//single-commercial", while the core block-renderer
		// endpoint only accepts a numeric post_id. Omit template IDs so the PHP
		// renderer can use its configured EPL listing preview fallback.
		var numericPostId = ( 'number' === typeof postId && postId > 0 ) || ( 'string' === typeof postId && /^\d+$/.test( postId ) )
			? Number( postId )
			: 0;
		var key = String( postId || 0 ) + ':' + JSON.stringify( props.attributes || {} );
		var initial = previewCache[ key ] && previewCache[ key ].content
			? { status: 'success', content: previewCache[ key ].content }
			: { status: 'loading', content: '' };
		var state = wp.element.useState( initial );
		var response = state[0];
		var setResponse = state[1];

		wp.element.useEffect( function() {
			var mounted = true;
			var cached = previewCache[ key ];
			if ( cached && cached.content ) {
				setResponse( { status: 'success', content: cached.content } );
				return function() { mounted = false; };
			}

			if ( ! cached ) {
				var path = '/wp/v2/block-renderer/epl/property-element?context=edit';
				if ( numericPostId ) {
					path += '&post_id=' + encodeURIComponent( numericPostId );
				}
				cached = previewCache[ key ] = {
					promise: queuePreviewRequest( function() {
						return wp.apiFetch( {
							path: path,
							method: 'POST',
							body: JSON.stringify( { attributes: props.attributes || {} } ),
							headers: { 'Content-Type': 'application/json' }
						} );
					} )
				};
				cached.promise.then( function( result ) {
					cached.content = result && result.rendered ? result.rendered : '';
					cached.error = '';
				}, function( error ) {
					cached.error = error && error.message ? error.message : __( 'Preview unavailable.', 'easy-property-listings' );
				} );
			}

			cached.promise.then( function() {
				if ( ! mounted ) {
					return;
				}
				setResponse( cached.error
					? { status: 'error', content: cached.error }
					: { status: 'success', content: cached.content || '' }
				);
			}, function( error ) {
				if ( mounted ) {
					setResponse( {
						status: 'error',
						content: error && error.message ? error.message : __( 'Preview unavailable.', 'easy-property-listings' )
					} );
				}
			} );

			return function() { mounted = false; };
		}, [ key ] );

		if ( 'success' === response.status ) {
			return response.content
				? el( wp.element.RawHTML, {}, response.content )
				: el( 'span', { className: 'epl-block-placeholder' }, __( 'No preview content.', 'easy-property-listings' ) );
		}
		if ( 'error' === response.status ) {
			return el( 'span', { className: 'epl-block-placeholder' }, response.content );
		}
		return el( 'span', { className: 'epl-block-preview-loading', 'aria-hidden': true } );
	}

	function options( object ) {
		return Object.keys( object || {} ).map( function( value ) {
			return { value: value, label: object[ value ] };
		} );
	}

	function statusBlockStyle( isOverlay ) {
		var spacing = {
			padding: { top: '0.52em', right: '0.82em', bottom: '0.52em', left: '0.82em' }
		};
		if ( isOverlay ) {
			spacing.margin = { top: '16px', left: '0px' };
		}

		return {
			spacing: spacing,
			color: { text: '#ffffff' },
			typography: {
				fontSize: '1rem',
				fontStyle: 'normal',
				fontWeight: '500',
				lineHeight: '1'
			}
		};
	}

	var postTypeOptions = options( config.postTypes );
	var taxonomyOptions = options( config.taxonomies );
	var imageSizeOptions = options( config.imageSizes );
	var elements = [
		[ 'image', __( 'EPL Property Image', 'easy-property-listings' ), 'format-image' ],
		[ 'gallery', __( 'EPL Property Gallery', 'easy-property-listings' ), 'images-alt2' ],
		[ 'title', __( 'EPL Property Title', 'easy-property-listings' ), 'heading' ],
		[ 'address', __( 'EPL Property Address', 'easy-property-listings' ), 'location' ],
		[ 'price', __( 'EPL Property Price', 'easy-property-listings' ), 'money-alt' ],
		[ 'status', __( 'EPL Property Status', 'easy-property-listings' ), 'tag' ],
		[ 'icons', __( 'EPL Property Icons', 'easy-property-listings' ), 'admin-home' ],
		[ 'heading', __( 'EPL Property Heading', 'easy-property-listings' ), 'editor-textcolor' ],
		[ 'meta', __( 'EPL Property Meta', 'easy-property-listings' ), 'database' ],
		[ 'category', __( 'EPL Property Category', 'easy-property-listings' ), 'category' ],
		[ 'taxonomy', __( 'EPL Property Taxonomy', 'easy-property-listings' ), 'category' ],
		[ 'content', __( 'EPL Property Content', 'easy-property-listings' ), 'text-page' ],
		[ 'excerpt', __( 'EPL Property Excerpt', 'easy-property-listings' ), 'excerpt-view' ],
		[ 'inspections', __( 'EPL Inspection Times', 'easy-property-listings' ), 'calendar-alt' ],
		[ 'map', __( 'EPL Property Map', 'easy-property-listings' ), 'location-alt' ],
		[ 'video', __( 'EPL Property Video', 'easy-property-listings' ), 'video-alt3' ],
		[ 'features', __( 'EPL Property Features', 'easy-property-listings' ), 'yes-alt' ],
		[ 'buttons', __( 'EPL Property Buttons', 'easy-property-listings' ), 'button' ],
		[ 'agents', __( 'EPL Listing Agents', 'easy-property-listings' ), 'groups' ]
	];

	function previewTypeControl( attributes, setAttributes ) {
		if ( postTypeOptions.length < 2 ) {
			return null;
		}
		return el( SelectControl, {
			label: __( 'Editor preview listing type', 'easy-property-listings' ),
			value: attributes.previewPostType,
			options: postTypeOptions,
			onChange: function( value ) { setAttributes( { previewPostType: value } ); },
			help: __( 'Only affects the editor fallback. Frontend blocks use the current listing.', 'easy-property-listings' )
		} );
	}

	function colorControl( label, value, onChange ) {
		return el( BaseControl, {},
			el( Flex, { align: 'center', justify: 'space-between' },
				el( FlexItem, {}, label ),
				el( FlexItem, {},
					el( Dropdown, {
						popoverProps: { placement: 'left-start' },
						renderToggle: function( toggle ) {
							return el( Button, {
								'aria-expanded': toggle.isOpen,
								'aria-label': label,
								onClick: toggle.onToggle,
								size: 'compact',
								title: label,
								variant: 'tertiary'
							}, el( ColorIndicator, { colorValue: value || 'transparent' } ) );
						},
						renderContent: function() {
							return el( ColorPicker, {
								color: value || '',
								enableAlpha: false,
								onChange: onChange
							} );
						}
					} )
				)
			)
		);
	}

	function propertyEdit( props ) {
		var attributes = props.attributes;
		var controls = [ previewTypeControl( attributes, props.setAttributes ) ];
		var mediaContext = wp.data.useSelect( function( select ) {
			var editor = select( 'core/block-editor' );
			var parentClientId = editor.getBlockRootClientId( props.clientId );
			var parent = parentClientId ? editor.getBlock( parentClientId ) : null;
			var parentClassName = parent && parent.attributes ? parent.attributes.className || '' : '';
			var isPatternMedia = parent && parentClassName.split( /\s+/ ).indexOf( 'epl-pattern-card-media' ) !== -1;
			var statusBlock = isPatternMedia ? parent.innerBlocks.find( function( block ) {
				return 'epl/property-element' === block.name && 'status' === block.attributes.element;
			} ) : null;

			return {
				isPatternMedia: !! isPatternMedia,
				parentClientId: parentClientId,
				statusBlock: statusBlock,
				imageIndex: parent ? parent.innerBlocks.findIndex( function( block ) { return block.clientId === props.clientId; } ) : -1
			};
		}, [ props.clientId ] );

		if ( [ 'image', 'title' ].indexOf( attributes.element ) !== -1 ) {
			controls.push( el( ToggleControl, {
				label: __( 'Link to listing', 'easy-property-listings' ),
				checked: attributes.link,
				onChange: function( value ) { props.setAttributes( { link: value } ); }
			} ) );
		}
		if ( 'image' === attributes.element ) {
			controls.push( el( SelectControl, {
				label: __( 'Image size', 'easy-property-listings' ),
				value: attributes.imageSize,
				options: imageSizeOptions,
				onChange: function( value ) { props.setAttributes( { imageSize: value } ); }
			} ) );
			if ( mediaContext.isPatternMedia ) {
				controls.push( el( ToggleControl, {
					label: __( 'Show status label', 'easy-property-listings' ),
					checked: !! mediaContext.statusBlock,
					help: __( 'Adds or removes the editable EPL Property Status block layered over this image.', 'easy-property-listings' ),
					onChange: function( value ) {
						var editor = wp.data.dispatch( 'core/block-editor' );
						if ( value && ! mediaContext.statusBlock ) {
							editor.insertBlocks(
								wp.blocks.createBlock( 'epl/property-element', { element: 'status', style: statusBlockStyle( true ) } ),
								mediaContext.imageIndex + 1,
								mediaContext.parentClientId
							);
						} else if ( ! value && mediaContext.statusBlock ) {
							editor.removeBlock( mediaContext.statusBlock.clientId, false );
						}
					}
				} ) );
			} else {
				controls.push( el( ToggleControl, {
					label: __( 'Show default EPL image sticker', 'easy-property-listings' ),
					checked: attributes.showStickers,
					onChange: function( value ) { props.setAttributes( { showStickers: value } ); }
				} ) );
			}
			controls.push( el( ToggleControl, {
				label: __( 'Enable hover effects', 'easy-property-listings' ),
				checked: attributes.enableImageHoverEffects,
				help: __( 'Combine zoom and CSS filters. Neutral slider values leave an effect disabled.', 'easy-property-listings' ),
				onChange: function( value ) { props.setAttributes( { enableImageHoverEffects: value } ); }
			} ) );
			if ( attributes.enableImageHoverEffects ) {
				[
					[ 'imageHoverZoom', __( 'Zoom (%)', 'easy-property-listings' ), 100, 150, 1 ],
					[ 'imageHoverBlur', __( 'Blur (px)', 'easy-property-listings' ), 0, 20, 0.1 ],
					[ 'imageHoverBrightness', __( 'Brightness (%)', 'easy-property-listings' ), 0, 200, 1 ],
					[ 'imageHoverContrast', __( 'Contrast (%)', 'easy-property-listings' ), 0, 200, 1 ],
					[ 'imageHoverSaturation', __( 'Saturation (%)', 'easy-property-listings' ), 0, 300, 1 ],
					[ 'imageHoverGrayscale', __( 'Grayscale (%)', 'easy-property-listings' ), 0, 100, 1 ],
					[ 'imageHoverOpacity', __( 'Opacity (%)', 'easy-property-listings' ), 0, 100, 1 ],
					[ 'imageHoverDuration', __( 'Transition duration (seconds)', 'easy-property-listings' ), 0, 3, 0.05 ]
				].forEach( function( field ) {
					controls.push( el( RangeControl, {
						label: field[1],
						value: attributes[ field[0] ],
						min: field[2],
						max: field[3],
						step: field[4],
						onChange: function( value ) {
							var change = {};
							change[ field[0] ] = value;
							props.setAttributes( change );
						}
					} ) );
				} );
			}
		}
		if ( 'meta' === attributes.element ) {
			[
				[ 'metaKey', __( 'Meta key', 'easy-property-listings' ) ],
				[ 'metaIcon', __( 'Dashicon name', 'easy-property-listings' ) ],
				[ 'prefix', __( 'Prefix', 'easy-property-listings' ) ],
				[ 'suffix', __( 'Suffix', 'easy-property-listings' ) ]
			].forEach( function( field ) {
				controls.push( el( TextControl, {
					label: field[1],
					value: attributes[ field[0] ],
					onChange: function( value ) { var change = {}; change[ field[0] ] = value; props.setAttributes( change ); }
				} ) );
			} );
			controls.push( colorControl(
				__( 'Icon color', 'easy-property-listings' ),
				attributes.metaIconColor,
				function( value ) { props.setAttributes( { metaIconColor: value } ); }
			) );
			controls.push( el( TextControl, {
				label: __( 'Icon size (px)', 'easy-property-listings' ),
				type: 'number',
				min: 8,
				max: 200,
				value: attributes.metaIconSize,
				onChange: function( value ) { props.setAttributes( { metaIconSize: Number( value ) } ); }
			} ) );
		}
		if ( 'taxonomy' === attributes.element ) {
			controls.push( el( SelectControl, {
				label: __( 'Taxonomy', 'easy-property-listings' ),
				value: attributes.taxonomy,
				options: taxonomyOptions,
				onChange: function( value ) { props.setAttributes( { taxonomy: value } ); }
			} ) );
			controls.push( el( TextControl, {
				label: __( 'Separator', 'easy-property-listings' ),
				value: attributes.separator,
				onChange: function( value ) { props.setAttributes( { separator: value } ); }
			} ) );
		}
		if ( 'category' === attributes.element ) {
			controls.push( el( SelectControl, {
				label: __( 'HTML tag', 'easy-property-listings' ),
				value: attributes.categoryTag,
				options: [
					{ value: 'div', label: 'div' },
					{ value: 'span', label: 'span' },
					{ value: 'p', label: 'p' }
				],
				onChange: function( value ) { props.setAttributes( { categoryTag: value } ); }
			} ) );
			controls.push( el( TextControl, {
				label: __( 'CSS class', 'easy-property-listings' ),
				value: attributes.categoryClass,
				onChange: function( value ) { props.setAttributes( { categoryClass: value } ); }
			} ) );
		}
		if ( 'status' === attributes.element ) {
			[
				[ 'saleLabel', __( 'Current sale label', 'easy-property-listings' ) ],
				[ 'rentalLabel', __( 'Current rental label', 'easy-property-listings' ) ],
				[ 'commercialSaleLabel', __( 'Commercial sale label', 'easy-property-listings' ) ],
				[ 'commercialLeaseLabel', __( 'Commercial lease label', 'easy-property-listings' ) ],
				[ 'commercialBothLabel', __( 'Commercial sale/lease label', 'easy-property-listings' ) ],
				[ 'underOfferLabel', __( 'Under offer label', 'easy-property-listings' ) ],
				[ 'soldLabel', __( 'Sold label', 'easy-property-listings' ) ],
				[ 'leasedLabel', __( 'Leased label', 'easy-property-listings' ) ],
				[ 'withdrawnLabel', __( 'Withdrawn label', 'easy-property-listings' ) ],
				[ 'offmarketLabel', __( 'Off market label', 'easy-property-listings' ) ],
				[ 'deletedLabel', __( 'Deleted label', 'easy-property-listings' ) ]
			].forEach( function( field ) {
				controls.push( el( TextControl, {
					label: field[1],
					value: attributes[ field[0] ],
					onChange: function( value ) { var change = {}; change[ field[0] ] = value; props.setAttributes( change ); }
				} ) );
			} );
			[
				[ 'current-sale', __( 'Sale background', 'easy-property-listings' ) ],
				[ 'current-rental', __( 'Rental background', 'easy-property-listings' ) ],
				[ 'current-commercial-sale', __( 'Commercial sale background', 'easy-property-listings' ) ],
				[ 'current-commercial-lease', __( 'Commercial lease background', 'easy-property-listings' ) ],
				[ 'current-commercial-both', __( 'Commercial sale/lease background', 'easy-property-listings' ) ],
				[ 'under-offer', __( 'Under offer background', 'easy-property-listings' ) ],
				[ 'sold', __( 'Sold background', 'easy-property-listings' ) ],
				[ 'leased', __( 'Leased background', 'easy-property-listings' ) ],
				[ 'withdrawn', __( 'Withdrawn background', 'easy-property-listings' ) ],
				[ 'offmarket', __( 'Off market background', 'easy-property-listings' ) ],
				[ 'deleted', __( 'Deleted background', 'easy-property-listings' ) ]
			].forEach( function( field ) {
				controls.push( colorControl(
					field[1],
					attributes.statusColors[ field[0] ] || '',
					function( value ) {
						var colors = Object.assign( {}, attributes.statusColors || {} );
						colors[ field[0] ] = value;
						props.setAttributes( { statusColors: colors } );
					}
				) );
			} );
		}

		return el( Fragment, {},
			el( InspectorControls, {}, el( PanelBody, { title: __( 'EPL element settings', 'easy-property-listings' ) }, controls ) ),
			el( 'div', useBlockProps( { className: 'epl-property-element-editor epl-property-element-editor-' + attributes.element } ), el( CachedPropertyPreview, { attributes: attributes } ) )
		);
	}

	wp.blocks.registerBlockType( 'epl/property-element', { edit: propertyEdit, save: function() { return null; } } );
	elements.forEach( function( item, index ) {
		var variationAttributes = { element: item[0] };
		if ( 'status' === item[0] ) {
			variationAttributes.style = statusBlockStyle( false );
		}
		wp.blocks.registerBlockVariation( 'epl/property-element', {
			name: item[0], title: item[1], icon: item[2], attributes: variationAttributes,
			isActive: [ 'element' ], isDefault: 0 === index, scope: [ 'inserter', 'transform' ]
		} );
	} );

	// Keep an internal variation so an active EPL Query only offers EPL card
	// designs. It is intentionally hidden from the inserter: users enter through
	// the native Query Loop and choose an EPL List or Grid pattern there.
	wp.blocks.registerBlockVariation( 'core/query', {
		name: 'epl/listings-query',
		title: __( 'EPL Listings', 'easy-property-listings' ),
		description: __( 'A WordPress Query Loop configured for EPL listings.', 'easy-property-listings' ),
		attributes: { namespace: 'epl/listings-query' },
		isActive: [ 'namespace' ],
		scope: []
	} );

	function queryDesignStatePersistence( props ) {
		var state = wp.data.useSelect( function( select ) {
			var editor = select( 'core/block-editor' );
			var parentClientId = editor.getBlockRootClientId( props.clientId );
			var order = editor.getBlockOrder( parentClientId );

			return {
				blocks: editor.getBlocks( props.clientId ),
				index: order.indexOf( props.clientId ),
				parentClientId: parentClientId
			};
		}, [ props.clientId ] );
		var blockEditor = wp.data.useDispatch( 'core/block-editor' );

		wp.element.useEffect( function() {
			var stateKey = state.parentClientId + ':' + state.index;
			var previous = eplQueryDesignStates[ stateKey ];
			var editor = wp.data.select( 'core/block-editor' );
			var isDesignReplacement = previous &&
				previous.clientId !== props.clientId &&
				! editor.getBlock( previous.clientId );

			if ( isDesignReplacement ) {
				var newTemplate = state.blocks.find( function( block ) {
					return 'core/post-template' === block.name;
				} );
				var oldTemplate = previous.blocks.find( function( block ) {
					return 'core/post-template' === block.name;
				} );

				if ( newTemplate && oldTemplate ) {
					var newCardBlocks = newTemplate.innerBlocks.map( function( block ) {
						return wp.blocks.cloneBlock( block );
					} );
					var restoredBlocks = previous.blocks.map( function( block ) {
						return 'core/post-template' === block.name
							? wp.blocks.cloneBlock( block, {}, newCardBlocks )
							: wp.blocks.cloneBlock( block );
					} );
					var restoredAttributes = Object.assign( {}, previous.attributes, { metadata: {} } );

					props.setAttributes( restoredAttributes );
					blockEditor.replaceInnerBlocks( props.clientId, restoredBlocks, false );
					eplQueryDesignStates[ stateKey ] = {
						attributes: restoredAttributes,
						blocks: restoredBlocks,
						clientId: props.clientId
					};
					return;
				}
			}

			eplQueryDesignStates[ stateKey ] = {
				attributes: Object.assign( {}, props.attributes ),
				blocks: state.blocks,
				clientId: props.clientId
			};
		}, [ props.attributes, props.clientId, state.blocks, state.index, state.parentClientId ] );

		return null;
	}

	function queryPaginationControl( props ) {
		var blocks = wp.data.useSelect( function( select ) {
			return select( 'core/block-editor' ).getBlocks( props.clientId );
		}, [ props.clientId ] );
		var blockEditor = wp.data.useDispatch( 'core/block-editor' );
		var pagination = blocks.find( function( block ) {
			return 'core/query-pagination' === block.name;
		} );

		function togglePagination( enabled ) {
			if ( enabled && ! pagination ) {
				var noResultsIndex = blocks.findIndex( function( block ) {
					return 'core/query-no-results' === block.name;
				} );
				var insertionIndex = -1 === noResultsIndex ? blocks.length : noResultsIndex;
				var paginationBlock = wp.blocks.createBlock(
					'core/query-pagination',
					{ layout: { type: 'flex', justifyContent: 'space-between' } },
					[
						wp.blocks.createBlock( 'core/query-pagination-previous' ),
						wp.blocks.createBlock( 'core/query-pagination-numbers' ),
						wp.blocks.createBlock( 'core/query-pagination-next' )
					]
				);
				blockEditor.insertBlocks( paginationBlock, insertionIndex, props.clientId );
			} else if ( ! enabled && pagination ) {
				blockEditor.removeBlock( pagination.clientId, false );
			}
		}

		return el( ToggleControl, {
			label: __( 'Show pagination', 'easy-property-listings' ),
			checked: !! pagination,
			help: __( 'Pagination appears on the frontend when the query contains more than one page of listings.', 'easy-property-listings' ),
			onChange: togglePagination
		} );
	}

	function queryPatternMetadataCleanup( props ) {
		var metadata = props.attributes.metadata || {};
		var eplPatternName = 0 === ( metadata.patternName || '' ).indexOf( 'epl/' ) ? metadata.patternName : '';
		var eplPatternLabel = 0 === ( metadata.name || '' ).indexOf( 'EPL Listings' ) ? metadata.name : '';

		wp.element.useEffect( function() {
			if ( ! eplPatternName && ! eplPatternLabel ) {
				return;
			}
			var cleanMetadata = Object.assign( {}, metadata );
			delete cleanMetadata.patternName;
			delete cleanMetadata.categories;
			if ( eplPatternLabel ) {
				delete cleanMetadata.name;
			}
			props.setAttributes( { metadata: cleanMetadata } );
		}, [ eplPatternName, eplPatternLabel ] );

		return null;
	}

	wp.hooks.addFilter( 'editor.BlockEdit', 'epl/listings-query-controls', function( BlockEdit ) {
		return function( props ) {
			if ( 'core/query' !== props.name || 'epl/listings-query' !== props.attributes.namespace ) {
				return el( BlockEdit, props );
			}
			var query = props.attributes.query || {};
			var selected = query.eplPostTypes || [];
			function update( changes ) { props.setAttributes( { query: Object.assign( {}, query, changes ) } ); }
			var typeControls = postTypeOptions.map( function( option ) {
				return el( CheckboxControl, {
					key: option.value, label: option.label, checked: selected.indexOf( option.value ) !== -1,
					onChange: function( checked ) {
						update( { eplPostTypes: checked ? selected.concat( [ option.value ] ) : selected.filter( function( value ) { return value !== option.value; } ) } );
					}
				} );
			} );
			return el( Fragment, {},
				el( queryDesignStatePersistence, { attributes: props.attributes, clientId: props.clientId, setAttributes: props.setAttributes } ),
				el( queryPatternMetadataCleanup, { attributes: props.attributes, setAttributes: props.setAttributes } ),
				el( BlockEdit, props ), el( InspectorControls, {},
				el( PanelBody, { title: __( 'EPL Listings Query', 'easy-property-listings' ) },
					query.inherit ? null : el( Fragment, {},
						el( 'strong', {}, __( 'Custom query listing types', 'easy-property-listings' ) ),
						typeControls
					),
					el( SelectControl, {
						label: __( 'EPL order by', 'easy-property-listings' ), value: query.eplOrderBy || '',
						options: [
							{ value: '', label: __( 'Use native Query setting', 'easy-property-listings' ) },
							{ value: 'price', label: __( 'Price (property_price_global)', 'easy-property-listings' ) }
						],
						onChange: function( value ) { update( { eplOrderBy: value } ); }
					} ),
					el( queryPaginationControl, { clientId: props.clientId } )
				)
			) );
		};
	} );
} )( window.wp, window.eplBlockData );
