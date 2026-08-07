/**
 * EPL Unified Listings - Compact View Component
 *
 * @package EPL
 * @since 4.0.0
 */

import { __ } from '@wordpress/i18n';
import { useMemo } from '@wordpress/element';
import { CheckboxControl, Button, Spinner, Icon } from '@wordpress/components';
import { edit, starFilled, chevronUp, chevronDown } from '@wordpress/icons';
import { useStore } from '../store';

// Decode HTML entities
const decodeHtmlEntities = ( str ) => {
	if ( ! str ) return '';
	const textarea = document.createElement( 'textarea' );
	textarea.innerHTML = str;
	return textarea.value;
};

// Format date
const formatDate = ( dateString ) => {
	if ( ! dateString ) return '—';
	const date = new Date( dateString );
	return date.toLocaleDateString( 'en-US', {
		month: 'short',
		day: 'numeric',
		year: 'numeric',
	} );
};

// Get status class
const getStatusClass = ( status ) => {
	const statusMap = {
		current: 'current',
		sold: 'sold',
		leased: 'leased',
		withdrawn: 'withdrawn',
		offmarket: 'offmarket',
		deleted: 'deleted',
	};
	return statusMap[ status ] || 'current';
};

// Available field definitions for compact view
const allFields = [
	{
		key: 'address',
		label: __( 'Address / Title', 'easy-property-listings' ),
		sortable: true,
		sortKey: 'property_address_display',
	},
	{
		key: 'priceDisplay',
		label: __( 'Price', 'easy-property-listings' ),
		sortable: true,
		sortKey: 'property_price',
	},
	{
		key: 'propertyStatus',
		label: __( 'Status', 'easy-property-listings' ),
		sortable: true,
		sortKey: 'property_status',
	},
	{
		key: 'modified',
		label: __( 'Modified', 'easy-property-listings' ),
		sortable: true,
		sortKey: 'modified',
	},
	{
		key: 'date',
		label: __( 'Date', 'easy-property-listings' ),
		sortable: true,
		sortKey: 'date',
	},
	{
		key: 'bedrooms',
		label: __( 'Beds', 'easy-property-listings' ),
		sortable: true,
		sortKey: 'property_bedrooms',
	},
	{
		key: 'bathrooms',
		label: __( 'Baths', 'easy-property-listings' ),
		sortable: true,
		sortKey: 'property_bathrooms',
	},
	{
		key: 'postType',
		label: __( 'Type', 'easy-property-listings' ),
		sortable: true,
		sortKey: 'post_type',
	},
];

export default function CompactView( {
	listings,
	loading,
	selectedIds,
	onSelect,
	orderby,
	order,
	onSort,
	visibleFields = [],
} ) {
	const { config } = useStore();
	const labels = config?.labels || {};
	const statusLabels = config?.statusLabels || {};

	// Filter fields based on visible fields setting
	const fields = useMemo( () => {
		let keys = visibleFields;
		if ( ! keys || keys.length === 0 ) {
			keys = [ 'address', 'priceDisplay', 'propertyStatus' ];
		}

		// Map keys to field definitions to preserve order
		return keys
			.map( ( key ) => allFields.find( ( field ) => field.key === key ) )
			.filter( Boolean );
	}, [ visibleFields ] );

	// Handle select all
	const allSelected =
		listings.length > 0 &&
		listings.every( ( l ) => selectedIds.includes( l.id ) );
	const someSelected = selectedIds.length > 0 && ! allSelected;

	const handleSelectAll = ( checked ) => {
		if ( checked ) {
			onSelect( listings.map( ( l ) => l.id ) );
		} else {
			onSelect( [] );
		}
	};

	const handleSelectRow = ( id, checked ) => {
		if ( checked ) {
			onSelect( [ ...selectedIds, id ] );
		} else {
			onSelect( selectedIds.filter( ( i ) => i !== id ) );
		}
	};

	// Handle sort
	const handleSort = ( key ) => {
		const newOrder = orderby === key && order === 'desc' ? 'asc' : 'desc';
		onSort( key, newOrder );
	};

	// Column header with sort
	const SortableHeader = ( { sortKey, children } ) => (
		<span
			className={ `epl-compact-col--sortable ${
				orderby === sortKey ? 'is-sorted' : ''
			}` }
			onClick={ () => handleSort( sortKey ) }
			style={ { cursor: 'pointer' } }
		>
			{ children }
			{ orderby === sortKey && (
				<Icon
					icon={ order === 'asc' ? chevronUp : chevronDown }
					size={ 14 }
				/>
			) }
		</span>
	);

	// Render cell content based on field key
	const renderCellContent = ( listing, field ) => {
		switch ( field.key ) {
			case 'address':
				return (
					<>
						{ listing.featured && (
							<Icon
								icon={ starFilled }
								size={ 14 }
								className="epl-featured-star"
							/>
						) }
						<a href={ listing.editLink }>
							{ listing.address ||
								decodeHtmlEntities(
									listing.heading || listing.title
								) }
						</a>
					</>
				);

			case 'priceDisplay':
				return decodeHtmlEntities( listing.priceDisplay ) || '—';

			case 'propertyStatus':
				return (
					<span
						className={ `epl-status-badge epl-status-badge--${ getStatusClass(
							listing.propertyStatus
						) }` }
					>
						{ statusLabels[ listing.propertyStatus ] ||
							listing.propertyStatus }
					</span>
				);

			case 'modified':
				return formatDate( listing.modified );

			case 'date':
				return formatDate( listing.date );

			case 'bedrooms':
				return (
					<span className="epl-bedrooms">
						{ listing.bedrooms || '—' }
					</span>
				);

			case 'bathrooms':
				return (
					<span className="epl-bathrooms">
						{ listing.bathrooms || '—' }
					</span>
				);

			case 'postType':
				return (
					<span className="epl-post-type">
						{ listing.postTypeLabel }
					</span>
				);

			default:
				return '—';
		}
	};

	// Loading state
	if ( loading ) {
		return (
			<div className="epl-loading-state">
				<Spinner />
				<span>
					{ labels.loading ||
						__( 'Loading...', 'easy-property-listings' ) }
				</span>
			</div>
		);
	}

	// Empty state
	if ( ! listings.length ) {
		return (
			<div className="epl-empty-state">
				<svg
					className="epl-empty-state__icon"
					viewBox="0 0 24 24"
					fill="none"
					stroke="currentColor"
					strokeWidth="1.5"
				>
					<path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v14a2 2 0 01-2 2z" />
					<path d="M9 9h6M9 13h6M9 17h4" />
				</svg>
				<h3 className="epl-empty-state__title">
					{ labels.noListings ||
						__( 'No listings found', 'easy-property-listings' ) }
				</h3>
				<p className="epl-empty-state__text">
					{ __(
						'Try adjusting your filters or search terms',
						'easy-property-listings'
					) }
				</p>
			</div>
		);
	}

	return (
		<div className="epl-compact-view">
			<div className="epl-compact-header">
				<div className="epl-compact-col epl-compact-col--checkbox">
					<CheckboxControl
						checked={ allSelected }
						indeterminate={ someSelected }
						onChange={ handleSelectAll }
						aria-label={ __(
							'Select all',
							'easy-property-listings'
						) }
					/>
				</div>
				{ fields.map( ( field ) => (
					<div
						key={ field.key }
						className={ `epl-compact-col epl-compact-col--${ field.key }` }
					>
						{ field.sortable ? (
							<SortableHeader
								sortKey={ field.sortKey || field.key }
							>
								{ field.label }
							</SortableHeader>
						) : (
							field.label
						) }
					</div>
				) ) }
				<div className="epl-compact-col epl-compact-col--actions" />
			</div>
			<div className="epl-compact-body">
				{ listings.map( ( listing ) => (
					<div
						key={ listing.id }
						className={ `epl-compact-row ${
							selectedIds.includes( listing.id )
								? 'is-selected'
								: ''
						}` }
					>
						<div className="epl-compact-col epl-compact-col--checkbox">
							<CheckboxControl
								checked={ selectedIds.includes( listing.id ) }
								onChange={ ( checked ) =>
									handleSelectRow( listing.id, checked )
								}
							/>
						</div>
						{ fields.map( ( field ) => (
							<div
								key={ field.key }
								className={ `epl-compact-col epl-compact-col--${ field.key }` }
							>
								{ renderCellContent( listing, field ) }
							</div>
						) ) }
						<div className="epl-compact-col epl-compact-col--actions">
							<Button
								icon={ edit }
								href={ listing.editLink }
								label={
									labels.edit ||
									__( 'Edit', 'easy-property-listings' )
								}
								size="small"
							/>
						</div>
					</div>
				) ) }
			</div>
		</div>
	);
}
