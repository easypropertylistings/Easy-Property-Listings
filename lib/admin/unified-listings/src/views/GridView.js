/**
 * EPL Unified Listings - Grid View Component
 *
 * @package EPL
 * @since 4.0.0
 */

import { __ } from '@wordpress/i18n';
import { useMemo } from '@wordpress/element';
import { ToggleControl, Button, Spinner, Icon } from '@wordpress/components';
import { edit, external, starFilled } from '@wordpress/icons';
import { useStore } from '../store';

// Decode HTML entities
const decodeHtmlEntities = ( str ) => {
	if ( ! str ) return '';
	const textarea = document.createElement( 'textarea' );
	textarea.innerHTML = str;
	return textarea.value;
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

// Property Card Component
function PropertyCard( {
	listing,
	isSelected,
	onSelect,
	statusLabels,
	labels,
	gridSettings = {},
} ) {
	const {
		showPrice = true,
		showStatus = true,
		showBeds = true,
		showBaths = true,
	} = gridSettings;
	return (
		<div
			className={ `epl-property-card ${
				isSelected ? 'is-selected' : ''
			}` }
		>
			<div className="epl-property-card__image">
				{ listing.thumbnail ? (
					<img src={ listing.thumbnail } alt={ listing.title } />
				) : (
					<div className="epl-property-card__no-image">
						<svg
							width="48"
							height="48"
							viewBox="0 0 24 24"
							fill="none"
							stroke="currentColor"
							strokeWidth="1"
						>
							<rect
								x="3"
								y="3"
								width="18"
								height="18"
								rx="2"
								ry="2"
							/>
							<circle cx="8.5" cy="8.5" r="1.5" />
							<polyline points="21 15 16 10 5 21" />
						</svg>
					</div>
				) }

				<div className="epl-property-card__checkbox-wrapper">
					<ToggleControl
						checked={ isSelected }
						onChange={ ( checked ) =>
							onSelect( listing.id, checked )
						}
						label={ __( 'Select', 'easy-property-listings' ) }
						hideLabelFromVision
					/>
				</div>

				{ showStatus && (
					<div className="epl-property-card__status-wrapper">
						<span
							className={ `epl-status-badge epl-status-badge--${ getStatusClass(
								listing.propertyStatus
							) }` }
						>
							{ statusLabels[ listing.propertyStatus ] ||
								listing.propertyStatus }
						</span>
					</div>
				) }

				{ listing.featured && (
					<div className="epl-property-card__featured">
						<Icon icon={ starFilled } size={ 24 } />
					</div>
				) }
			</div>

			<div className="epl-property-card__body">
				<span className="epl-property-card__type">
					{ listing.postTypeLabel }
				</span>
				<h3 className="epl-property-card__title">
					<a href={ listing.editLink }>
						{ decodeHtmlEntities(
							listing.heading || listing.title
						) }
					</a>
				</h3>
				<p className="epl-property-card__address">
					{ listing.address || '—' }
				</p>
				<div className="epl-property-card__meta">
					{ showPrice && (
						<span className="epl-property-card__price">
							{ decodeHtmlEntities( listing.priceDisplay ) ||
								'—' }
						</span>
					) }
					{ ( showBeds || showBaths ) && (
						<span className="epl-property-card__stats">
							{ showBeds && listing.bedrooms && (
								<span>{ listing.bedrooms } bed</span>
							) }
							{ showBaths && listing.bathrooms && (
								<span> • { listing.bathrooms } bath</span>
							) }
						</span>
					) }
				</div>
			</div>

			<div className="epl-property-card__actions">
				<Button href={ listing.editLink } isSmall variant="secondary">
					{ labels.edit || __( 'Edit', 'easy-property-listings' ) }
				</Button>
				<Button
					href={ listing.viewLink }
					target="_blank"
					isSmall
					variant="secondary"
				>
					{ labels.view || __( 'View', 'easy-property-listings' ) }
				</Button>
			</div>
		</div>
	);
}

export default function GridView( {
	listings,
	loading,
	selectedIds,
	onSelect,
	gridSettings = {},
} ) {
	const { config } = useStore();
	const labels = config?.labels || {};
	const statusLabels = config?.statusLabels || {};

	// Get cards per row from settings
	const cardsPerRow = gridSettings?.cardsPerRow || 4;

	// Grid container style with CSS variable - use string key for custom properties
	const gridStyle = useMemo(
		() => ( {
			'--cards-per-row': String( cardsPerRow ),
		} ),
		[ cardsPerRow ]
	);

	const handleSelect = ( id, checked ) => {
		if ( checked ) {
			onSelect( [ ...selectedIds, id ] );
		} else {
			onSelect( selectedIds.filter( ( i ) => i !== id ) );
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
		<div className="epl-grid-view">
			<div className="epl-grid-container" style={ gridStyle }>
				{ listings.map( ( listing ) => (
					<PropertyCard
						key={ listing.id }
						listing={ listing }
						isSelected={ selectedIds.includes( listing.id ) }
						onSelect={ handleSelect }
						statusLabels={ statusLabels }
						labels={ labels }
						gridSettings={ gridSettings }
					/>
				) ) }
			</div>
		</div>
	);
}
