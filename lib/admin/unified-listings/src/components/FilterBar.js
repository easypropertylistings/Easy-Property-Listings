/**
 * EPL Unified Listings - FilterBar Component
 *
 * @package EPL
 * @since 4.0.0
 */

import { __ } from '@wordpress/i18n';
import {
	SearchControl,
	Button,
	FormTokenField,
	Icon,
} from '@wordpress/components';
import { chevronDown } from '@wordpress/icons';
import { useStore } from '../store';
import { useEffect } from '@wordpress/element';
import AdvancedFilters from './AdvancedFilters';

export default function FilterBar( { filters, onFilterChange } ) {
	const { config } = useStore();

	// Default fallbacks to prevent "No items found" if config fails to load
	const statusLabels =
		Object.keys( config?.statusLabels || {} ).length > 0
			? config.statusLabels
			: {
					current: 'Current',
					sold: 'Sold',
					leased: 'Leased',
					withdrawn: 'Withdrawn',
					offmarket: 'Off Market',
					deleted: 'Deleted',
			  };

	const postTypes =
		config?.postTypes && config.postTypes.length > 0
			? config.postTypes
			: [
					'property',
					'rental',
					'land',
					'commercial',
					'com_rental',
					'business',
					'rural',
			  ];

	const postTypeLabels =
		Object.keys( config?.postTypeLabels || {} ).length > 0
			? config.postTypeLabels
			: {
					property: 'Property',
					rental: 'Rental',
					land: 'Land',
					commercial: 'Commercial',
					com_rental: 'Commercial Rental',
					business: 'Business',
					rural: 'Rural',
			  };

	// Authors might be empty if no users found, but we must show the filter
	const authors = config?.authors || [];

	const labels = config?.labels || {};

	// Helper to map values to labels and vice versa for the tokens
	const getLabelFromValue = ( value, source ) => {
		if ( source === 'status' ) return statusLabels[ value ] || value;
		if ( source === 'type' ) return postTypeLabels[ value ] || value;
		if ( source === 'author' ) {
			const author = authors.find(
				( a ) => String( a.id ) === String( value )
			);
			return author ? author.name : value;
		}
		return value;
	};

	const getValueFromLabel = ( label, source ) => {
		if ( source === 'status' ) {
			return (
				Object.keys( statusLabels ).find(
					( key ) => statusLabels[ key ] === label
				) || label
			);
		}
		if ( source === 'type' ) {
			return (
				postTypes.find(
					( type ) => ( postTypeLabels[ type ] || type ) === label
				) || label
			);
		}
		if ( source === 'author' ) {
			const author = authors.find( ( a ) => a.name === label );
			return author ? String( author.id ) : label;
		}
		return label;
	};

	// Suggestions (Labels)
	const statusSuggestions = Object.values( statusLabels );
	const typeSuggestions = postTypes.map(
		( type ) => postTypeLabels[ type ] || type
	);
	const authorSuggestions = authors.map( ( author ) => author.name );

	// Current Values (Labels)
	const statusValues = ( filters.status || [] ).map( ( val ) =>
		getLabelFromValue( val, 'status' )
	);
	const typeValues = ( filters.postType || [] ).map( ( val ) =>
		getLabelFromValue( val, 'type' )
	);
	const authorValues = ( filters.author || [] ).map( ( val ) =>
		getLabelFromValue( val, 'author' )
	);

	// Handlers
	const handleStatusChange = ( tokens ) => {
		const values = tokens.map( ( token ) =>
			getValueFromLabel( token, 'status' )
		);
		onFilterChange( { status: values } );
	};

	const handleTypeChange = ( tokens ) => {
		const values = tokens.map( ( token ) =>
			getValueFromLabel( token, 'type' )
		);
		onFilterChange( { postType: values } );
	};

	const handleAuthorChange = ( tokens ) => {
		const values = tokens.map( ( token ) =>
			getValueFromLabel( token, 'author' )
		);
		onFilterChange( { author: values } );
	};

	const hasActiveFilters =
		filters.search ||
		( filters.status && filters.status.length > 0 ) ||
		( filters.postType && filters.postType.length > 0 ) ||
		( filters.author && filters.author.length > 0 );

	return (
		<div className="epl-filters-group">
			<div className="epl-search-input">
				<SearchControl
					value={ filters.search || '' }
					onChange={ ( value ) =>
						onFilterChange( { search: value } )
					}
					placeholder={
						labels.search ||
						__( 'Search listings...', 'easy-property-listings' )
					}
				/>
			</div>

			<div className="epl-filter-token epl-filter-token--has-icon">
				<FormTokenField
					value={ statusValues }
					suggestions={ statusSuggestions }
					onChange={ handleStatusChange }
					placeholder={
						labels.allStatuses ||
						__( 'Status', 'easy-property-listings' )
					}
					label=""
					__nextHasNoMarginBottom
					isBorderless={ false }
					expandOnFocus={ true }
					__experimentalExpandOnFocus={ true }
				/>
				<div className="epl-filter-token__icon">
					<Icon icon={ chevronDown } size={ 18 } />
				</div>
			</div>

			<div className="epl-filter-token epl-filter-token--has-icon">
				<FormTokenField
					value={ typeValues }
					suggestions={ typeSuggestions }
					onChange={ handleTypeChange }
					placeholder={
						labels.allTypes ||
						__( 'Type', 'easy-property-listings' )
					}
					label=""
					__nextHasNoMarginBottom
					isBorderless={ false }
					expandOnFocus={ true }
					__experimentalExpandOnFocus={ true }
				/>
				<div className="epl-filter-token__icon">
					<Icon icon={ chevronDown } size={ 18 } />
				</div>
			</div>

			<div className="epl-filter-token epl-filter-token--has-icon">
				<FormTokenField
					value={ authorValues }
					suggestions={ authorSuggestions }
					onChange={ handleAuthorChange }
					placeholder={
						labels.allAuthors ||
						__( 'Author', 'easy-property-listings' )
					}
					label=""
					__nextHasNoMarginBottom
					isBorderless={ false }
					expandOnFocus={ true }
					__experimentalExpandOnFocus={ true }
				/>
				<div className="epl-filter-token__icon">
					<Icon icon={ chevronDown } size={ 18 } />
				</div>
			</div>

			<AdvancedFilters
				filters={ filters }
				onFilterChange={ onFilterChange }
			/>

			{ hasActiveFilters && (
				<Button
					variant="link"
					className="epl-clear-filters"
					onClick={ () =>
						onFilterChange( {
							search: '',
							status: [],
							postType: [],
							author: [],
						} )
					}
				>
					{ __( 'Clear', 'easy-property-listings' ) }
				</Button>
			) }
		</div>
	);
}
